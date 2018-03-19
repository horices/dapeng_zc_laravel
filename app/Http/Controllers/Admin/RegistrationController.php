<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25 0025
 * Time: 10:01
 */

namespace App\Http\Controllers\Admin;


use App\Api\DapengUserApi;
use App\Exceptions\UserValidateException;
use App\Http\Requests\RegistrationForm;
use App\Models\CoursePackageModel;
use App\Models\RebateActivityModel;
use App\Models\UserHeadMasterModel;
use App\Models\UserPayLogModel;
use App\Models\UserPayModel;
use App\Models\UserRegistrationModel;
use App\Utils\Util;
use Faker\Provider\bn_BD\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Schema;

class RegistrationController extends BaseController{
    function getAdd(){
        //获取附加课程套餐列表
        $CoursePackage = new CoursePackageModel();
        //$packageAttachList = $CoursePackage->where(['type'=>1,'status'=>'USE'])->select();
        $packageAttachList = CoursePackageModel::where([
            ['type','=',1],
            ['status','=','USE'],
        ])->get();
        //优惠活动列表
        $rebateList = RebateActivityModel::where('status','=','USE')->get();

        //分期支付方式列表
        $UserRegistration = new UserRegistrationModel();
        $fqTypeList = $UserRegistration->fqType;
        return view("admin.registration.add",[
            'packageAttachList'=>  json_encode($packageAttachList,JSON_UNESCAPED_UNICODE),
            'giveList'      =>  json_encode(array_reverse($CoursePackage->give),JSON_UNESCAPED_UNICODE),//赠送课程
            'fqTypeList'    =>  json_encode($fqTypeList,JSON_UNESCAPED_UNICODE),
            'rebateList'    =>  json_encode($rebateList,JSON_UNESCAPED_UNICODE)
        ]);
    }
    /**
     * @note 检查学员是否已经报名
     */
    function postHasRegistration(RegistrationForm $request){
        $post = $request->post();
        if(!isset($post['mobile']) || !is_numeric($post['mobile']) || !Util::checkMobileFormat($post['mobile'])){
            return response()->json(['code'=>Util::FAIL,"msg"=>"开课手机号有误请检查!"]);
        }
        //获取当前登录者信息
        $adminInfo = $this->getUserInfo();
        //初始化课程顾问ID
        $adviserId = 0;

        //如果是手机端提交则需要检查课程顾问
        if($post['client_submit'] == "WAP"){
            if(!key_exists("adviser_mobile",$post) || !$post['adviser_mobile'] || !Util::checkMobileFormat($post['adviser_mobile'])){
                throw new UserValidateException("课程顾问手机号有误请检查");
                //return response()->json(['code'=>Util::FAIL,"msg"=>"课程顾问手机号有误请检查!"]);
            }
            //查询和判断课程顾问
            $hasAdviser = UserHeadMasterModel::where(['mobile','=',$post['adviser_mobile']])->first();
            if(!$hasAdviser){
                return response()->json(['code'=>Util::FAIL,"msg"=>"课程顾问不存在!"]);
            }
            $adviserId = $hasAdviser['uid'];
        }elseif ($post['client_submit'] == "PC"){
            $adviserId = $adminInfo['uid'];
        }else{
            return response()->json(['code'=>Util::FAIL,"msg"=>"数据来源有误!"]);
        }
        //根据手机号查询用户是否已在主站注册
        $dpData = [
            'type'      =>  'MOBILE',
            'keyword'   =>  $post['mobile'],
        ];

        $hasDapengUser = DapengUserApi::getInfo($dpData);

        if($hasDapengUser['code'] == Util::FAIL){
            return response()->json(['code'=>Util::FAIL,"msg"=>"该开课手机号未注册!"]);
        }
        $hasReg = UserRegistrationModel::with(["rebateActivity"])->where("mobile",$post['mobile'])->orderBy("id","desc")->first();
        if($hasReg){
            return response()->json(['code'=>Util::SUCCESS,"msg"=>"学员已报名!",'data'=>$hasReg]);
        }else{
            return response()->json(['code'=>Util::SUCCESS,"msg"=>"该学员未报名,请填写报名信息!",'data'=>'']);
        }
    }

    /**
     * @note 添加用户的支付信息和报名信息
     */
    function postAddRegistration(RegistrationForm $registration){
        $post = $registration->post();
        $UserRegistration = new UserRegistrationModel();
        $UserHeadMaster = new UserHeadMasterModel();
        //获取当前用户信息
        $adminInfo = $this->getUserInfo($registration);
        $adviserId = 0;
        $tmpMap = [];
        if($post['client_submit'] == "WAP"){
            if(!key_exists("adviser_mobile",$post) || !$post['adviser_mobile']){
                return response()->json(['code'=>Util::FAIL,"msg"=>"请填写课程顾问手机号!"]);
            }
            $tmpMap = ['mobile','=',$post['adviser_mobile']];
        }else if($post['client_submit'] == "PC"){
            $tmpMap = ['uid','=',$adminInfo['uid']];
            //$adviserId = $this->getAdviserId();
        }else{
            return response()->json(['code'=>Util::FAIL,"msg"=>"信息来源错误!"]);
        }


        //判断手机号是否在主站注册过
        $dpData = [
            'type'      =>  'MOBILE',
            'keyword'   =>  $post['mobile'],
        ];
        $hasDapengUser = DapengUserApi::getInfo($dpData);
        if($hasDapengUser['code'] == Util::FAIL){
            return response()->json(['code'=>Util::FAIL,'msg'=>'该开课手机号未注册!']);
        }
        //$post['is_open']    = 1;  //默认报名课程开启

        //查询和判断课程顾问
        $hasAdviser = $UserHeadMaster::where([$tmpMap])->first();
        if(!$hasAdviser){
            return response()->json(['code'=>Util::FAIL,'msg'=>'课程顾问不存在!']);
        }
        $adviserId = $hasAdviser['uid'];
        //获取当前课程顾问信息
        $post['adviser_id'] = $adviserId;
        $post['adviser_name'] = $hasAdviser['name'];
        $post['adviser_qq'] = $hasAdviser['qq'] ?: '';
        //$post['is_open']    = 1;  //默认报名课程开启
        //该学员总的支付金额
        if(!isset($post['pay_type_list']) || !isset($post['amount_list']) || empty($post['pay_type_list']) || empty($post['amount_list'])){
            return response()->json(['code'=>Util::FAIL,'msg'=>'必须添加支付信息!']);
        }
        $allAmount= array_sum($post['amount_list']);
        if($allAmount<=0){
            return response()->json(['code'=>Util::FAIL,'msg'=>'请填写正确的支付金额！']);
        }
        $post['amount_submitted'] = $allAmount;
//        if($post['amount_submitted'] > $post['package_total_price'])
//            $this->returnAjaxJson(FAIL,'已提交金额不能大于总金额！');
        //$post['amount_submitted'] = $post['amount_submitted']+$post['amount'];
        //$post['amount_submitted'] = $post['amount'];
        $CoursePackage = new CoursePackageModel();
        //开启事务
        DB::beginTransaction();

        //必须选择赠送课程
        if(!isset($post['give_id']) || $post['give_id'] == ''){
            return response()->json(['code'=>Util::FAIL,'msg'=>'请选择赠送课程！']);
        }
        //插入报名记录
        $resReg = $UserRegistration->addData($post);
        if(!$resReg){
            DB::rollBack();
        }
        Util::setDefault($regId,$resReg['id']);
        //添加用户支付信息
        $UserPay = new UserPayModel();
        $post['registration_id'] = $resReg['id']; //关联报名课程记录ID
        $resUserPay = $UserPay->addData($post);
        if(!$resUserPay){
            DB::rollBack();
        }
        //循环添加多个支付方式记录
        $UserPayLog = new UserPayLogModel();
        $post['pay_id'] = $resUserPay['id'];

        foreach ($post['pay_type_list'] as $key=>$val){
            $post['amount'] = $post['amount_list'][$key];
            $post['pay_time'] = strtotime($post['pay_time_list'][$key]);
            $post['pay_type'] = $val;
            $resUserPayLog = $UserPayLog->addData($post);
            if(!$resUserPayLog){
                DB::rollBack();
            }
        }
        //重置套餐全名
        $eff = $UserRegistration->setPackageAllTitle($regId);
        if(!$eff){
            DB::rollBack();
            return response()->json(['code'=>Util::FAIL,'msg'=>'重置套餐全名失败！']);
        }
        //更新报名信息的最后一次提交支付记录时间
        $UserRegistration->setLastPayTime($regId);
        DB::commit();
        return response()->json(['code'=>Util::SUCCESS,'msg'=>'信息提交成功！']);
    }

    /**
     * @note 更新报名记录（添加支付记录）
     */
    function postUpdateRegistration(Request $request){
        $post = $request->post();
        $UserRegistration = new UserRegistrationModel();
        $UserHeadMaster = new UserHeadMasterModel();
        $adviserId = 0;
        $tmpMap = [];

        if($post['client_submit'] == "WAP"){
            if(!key_exists("adviser_mobile",$post) || !$post['adviser_mobile']){
                return response()->json(['code'=>Util::FAIL,'msg'=>'请填写课程顾问手机号!']);
            }
            $tmpMap = ['mobile','=',$post['adviser_mobile']];
        }else if($post['client_submit'] == "PC"){
            $tmpMap = ['uid','=',$this->getUserInfo($request)['uid']];
        }else{
            return response()->json(['code'=>Util::FAIL,'msg'=>'信息来源错误!']);
        }

        //查询和判断课程顾问
        $hasAdviser = $UserHeadMaster::where([$tmpMap])->first();

        if(!$hasAdviser){
            return response()->json(['code'=>Util::FAIL,'msg'=>'课程顾问不存在!']);
        }
        $adviserId = $hasAdviser['uid'];
        //补充课程顾问信息
        $post['adviser_id'] = $adviserId;
        $post['adviser_name'] = $hasAdviser['name'];
        $post['adviser_qq'] = $hasAdviser['qq'] ?: '';
        //检查报名信息
//        $hasReg = $UserRegistration->where(['adviser_id'=>$adviserId,'mobile'=>$post['mobile']])->find();
//        if(!$hasReg){
//            $this->returnAjaxJson(FAIL,'该学员与课程顾问信息不一致！');
//        }
        DB::beginTransaction();
        if(empty($post['pay_type_list']) || empty($post['amount_list'])){
            return response()->json(['code'=>Util::FAIL,'msg'=>'必须添加支付信息!']);
        }
        //更新报名已提交金额
        $allAmount = array_sum($post['amount_list']);
        if($allAmount<=0){
            return response()->json(['code'=>Util::FAIL,'msg'=>'请填写正确的支付金额!']);
        }
        $post['amount_submitted'] = $allAmount;

//        if($post['amount_submitted'] > $post['package_total_price'])
//            $this->returnAjaxJson(FAIL,'已提交金额不能大于总金额！');

        //添加用户支付信息
        $UserPay = new UserPayModel();

        $res = $UserPay->addData($post);
        if(!$res){
            DB::rollBack();
        }
        //循环添加多个支付方式记录
        $UserPayLog = new UserPayLogModel();
        $post['pay_id'] = $res['id'];
        $resUserPayLog = "";
        foreach ($post['pay_type_list'] as $key=>$val){
            $post['amount'] = $post['amount_list'][$key];
            $post['pay_time'] = strtotime($post['pay_time_list'][$key]);
            $post['pay_type'] = $val;
            $resUserPayLog = $UserPayLog->addData($post);
            if(!$res){
                DB::rollBack();
            }
        }

        //$post['amount_submitted'] = $post['amount_submitted']+$post['amount'];
        $regData = [
            'remark'            =>  $post['remark'],
            'last_pay_time'     =>  $resUserPayLog['create_time'],
        ];
        $eff = $UserRegistration->where('id','=',$post['registration_id'])
            ->increment('amount_submitted',$allAmount,$regData);
        if(!$eff){
            DB::rollBack();
        }
        DB::commit();
        return response()->json(['code'=>Util::SUCCESS,'msg'=>'提交成功!']);
    }

    /**
     * @note 异步获取课程套餐列表
     */
    function postPackageList(){
        $CoursePackage = new CoursePackageModel();
        $map = ['type'=>0,'status'=>'USE'];
        $title = Input::get("title",'trim');
        if(!$title){
            return response()->json(['code'=>Util::FAIL,'msg'=>'关键字为空!']);
        }
        $list = $CoursePackage::where([
            ['title','like',"%".$title."%"],
            ['type','=',0],
            ['status','=','USE']
        ])->get();
        return response()->json(['code'=>Util::SUCCESS,'msg'=>'搜索完成!','data'=>$list]);
    }

    /**
     * 获取用户统计列表
     */
    function getUserList(Request $request){
        $UserRegistration = new UserRegistrationModel();
        $query = $UserRegistration::query()->where('is_active',1);
        //根据学员姓名检索
        $name = $request->get("name");
        if(!empty($name)){
            $query->where("name","like",'%'.$name.'%');
        }
        //根据课程顾问检索
        $adviserName = $request->get("adviserName");
        if(!empty($adviserName)){
            $query->where("adviser_name","like",'%'.$adviserName.'%');
        }
        //根据导学状态检索
        $isOpen = $request->get("is_open");
        if($isOpen != ''){
            $query->where("is_open",$isOpen);
        }
        //根据开课手机号检索
        $mobile = $request->get("mobile");
        if($mobile){
            $query->where("mobile","like",'%'.$mobile.'%');
        }
        //根据时间来检索
        $startDate = urldecode($request->get("startDate"));
        if(!empty($startDate)){
            $query->where("create_time",">=",$startDate);
        }
        //根据课程顾问ID来筛选所属学员的统计信息
//        $adviserId = I("get.adviserId",0,"intval");
//        if($adviserId){
//            $map['adviser_id'] = $adviserId;
//        }
        $endDate = urldecode($request->get("endDate"));
        if(!empty($endDate)){
            $query->where("create_time","<=",$endDate);
        }

        $list = $query->orderBy("last_pay_time","desc")->paginate(15);



        $RebateActivity = new RebateActivityModel();
        foreach ($list as $key=>$val){
            $list[$key]['idk'] = $key+1;
            //套餐总金额
            //$list[$key]['package_total_price'] = $CoursePackage->where(['id'=>$val['package_id']])->getField('price');
//            if($val['package_attach_id']){
//                $list[$key]['package_total_price']+= $CoursePackage->where(['id'=>$val['package_attach_id']])->getField('price');
//            }
            //分期方式
            //$list[$key]['fq_type_str'] = $UserRegistration->fqType[$val['fq_type']] ?: '无分期';
            //开课状态
            //$list[$key]['is_open_str'] = $UserRegistration->isOpenArr[$val['is_open']];
            //优惠金额
            //$list[$key]['rebate'] = $RebateActivity->where(['id'=>$val['rebate_id']])->getField("price");
            //最后的支付记录时间
            //$list[$key]['last_pay_time'] = $val['last_pay_time'] ? date("Y-m-d H:i:s",$val['last_pay_time']) : '无';
        }
        $_GET['subnavAction'] = "userPayList";
        return view("admin.registration.user-list",[
            'list'          =>  $list,
            'adminInfo'     =>  $this->getUserInfo($request)
        ]);

    }


}