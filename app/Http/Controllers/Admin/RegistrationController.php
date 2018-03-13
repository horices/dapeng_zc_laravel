<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25 0025
 * Time: 10:01
 */

namespace App\Http\Controllers\Admin;


use App\Api\DapengUserApi;
use App\Models\CoursePackageModel;
use App\Models\RebateActivityModel;
use App\Models\UserHeadMasterModel;
use App\Models\UserRegistrationModel;
use App\Utils\Util;
use Faker\Provider\bn_BD\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends BaseController{
    function getAdd(){
        //获取附加课程套餐列表
        $CoursePackage = new CoursePackageModel();
        //$packageAttachList = $CoursePackage->where(['type'=>1,'status'=>'USE'])->select();
        $packageAttachList = $CoursePackage::where([
            ['type','=',1],
            ['status','=','USE'],
        ])->get();
        //优惠活动列表
        $RebateActivity = new RebateActivityModel();
        $rebateList = $RebateActivity::where('status','=','USE')->get();

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
    function postHasRegistration(Request $request){
        $post = $request->post();
        $UserRegistration = new UserRegistrationModel();
        if(!isset($post['mobile']) || !is_numeric($post['mobile']) || !Util::checkMobileFormat($post['mobile'])){
            return response()->json(['code'=>Util::FAIL,"msg"=>"开课手机号有误请检查!"]);
        }
        //获取当前登录者信息
        $adminInfo = $this->getUserInfo($request);
        //初始化课程顾问ID
        $adviserId = 0;

        //如果是手机端提交则需要检查课程顾问
        if($post['client_submit'] == "WAP"){
            if(!key_exists("adviser_mobile",$post) || !$post['adviser_mobile'] || !Util::checkMobileFormat($post['adviser_mobile'])){
                return response()->json(['code'=>Util::FAIL,"msg"=>"课程顾问手机号有误请检查!"]);
            }
            //查询和判断课程顾问
            $UserHeadMaster = new UserHeadMasterModel();
            $hasAdviser = $UserHeadMaster::where(['mobile','=',$post['adviser_mobile']])->first();
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

        $map = [['mobile','=',$post['mobile']]];
        $hasReg = $UserRegistration::where($map)->orderBy("id","desc")->first();
        if($hasReg){
            $hasReg = $hasReg->toArray();
            $hasReg['isBelong'] = 0;
            //获取课程套餐信息
            $coursePackage = new CoursePackageModel();
            $packageInfo = $coursePackage::where('id','=',$hasReg['package_id'])->first()->toArray();

            $hasReg['package_title'] = $packageInfo['title'];
            if($packageInfo['status'] == 'DEL'){
                $hasReg['package_title'] = $hasReg['package_title']."(已删)";
            }
            $hasReg['package_price'] = $packageInfo['price'];
            //获取附加套餐信息
            $packageAttach = $coursePackage::where('id','=',$hasReg['package_attach_id'])
                ->first()->toArray();
            $hasReg['package_attach_title'] = $packageAttach['title'];
            $hasReg['package_attach_price'] = $packageAttach['price'];
            $hasReg['package_total_price'] = $packageAttach['price']+$packageInfo['price'];
            //当前附加套餐信息
            $hasReg['package_attach_current_data'] = $packageAttach;
            //获取惠活动信息
            $RebateActivity = new RebateActivityModel();
            $rebateData = $RebateActivity::where('id','=',$hasReg['rebate_id'])
                ->first()->toArray();
            $hasReg['rebate_price'] = $hasReg['rebate'];
            $hasReg['rebate_title'] = $rebateData['title'];
            //检查该学员是否属于当前课程顾问
//            if($hasReg['adviser_id'] == $adviserId)
//                $hasReg['isBelong'] = 1;
            $hasReg['isBelong'] = 1;
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
        $adminInfo = $this->getUserInfo();
        $adviserId = 0;
        $tmpMap = [];
        if($post['client_submit'] == "WAP"){
            if(!key_exists("adviser_mobile",$post) || !$post['adviser_mobile']){
                return response()->json(['code'=>Util::FAIL,"msg"=>"请填写课程顾问手机号!"]);
            }
            $tmpMap = ['mobile'=>$post['adviser_mobile']];
        }else if($post['client_submit'] == "PC"){
            $tmpMap = ['uid'=>$adminInfo['uid']];
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
            $this->returnAjaxJson(FAIL,'必须添加支付信息！');
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
        //先添加课程套餐

        //添加报名信息
        Util::setDefault($regId,0);
        //必须选择赠送课程
        if(!isset($post['give_id']) || $post['give_id'] == ''){
            return response()->json(['code'=>Util::FAIL,'msg'=>'请选择赠送课程！']);
        }
        if($UserRegistration->create($post) === false || ($regId = $UserRegistration->add()) === false){
            DB::rollBack();
            $this->returnAjaxJson(FAIL,$UserRegistration->getError());
        }
        //添加用户支付信息
        $UserPay = new UserPayModel();
        $post['registration_id'] = $regId; //关联报名课程记录ID
        if($UserPay->create($post) === false || ($payId = $UserPay->add()) === false){
            DB::rollBack();
            $this->returnAjaxJson(FAIL,$UserPay->getError());
        }
        //循环添加多个支付方式记录
        $UserPayLog = new UserPayLogModel();
        $post['pay_id'] = $payId;

        foreach ($post['pay_type_list'] as $key=>$val){
            $post['amount'] = $post['amount_list'][$key];
            $post['pay_time'] = strtotime($post['pay_time_list'][$key]);
            $post['pay_type'] = $val;
            if($UserPayLog->create($post) === false || $UserPayLog->add() === false){
                DB::rollBack();
                $this->returnAjaxJson(FAIL,$UserPayLog->getError());
            }
        }
        $UserRegistration->setPackageAllTitle($regId);
        //更新报名信息的最后一次提交支付记录时间
        $UserRegistration->setLastPayTime($regId);
        DB::commit();
        $this->returnAjaxJson(SUCCESS,'信息提交成功！');
    }

    /**
     * @note 更新报名记录（添加支付记录）
     */
    function postUpdateRegistration(Request $request){
        $post = $request->post();
        $UserRegistration = new UserRegistrationModel();
        dd($UserRegistration->add());
        exit;
        //$UserRegistration = new UserRegistrationModel();
        $UserHeadMaster = new UserHeadMasterModel();
        $adviserId = 0;
        $tmpMap = [];
        if($post['client_submit'] == "WAP"){
            if(!key_exists("adviser_mobile",$post) || !$post['adviser_mobile']){
                $this->returnAjaxJson(FAIL,'请填写课程顾问手机号');
            }
            $tmpMap = ['mobile'=>$post['adviser_mobile']];
        }else if($post['client_submit'] == "PC"){
            $tmpMap = ['uid'=>$this->getAdviserId()];
        }else{
            $this->returnAjaxJson(FAIL,'信息来源错误！');
        }

        //查询和判断课程顾问
        $hasAdviser = $UserHeadMaster->where($tmpMap)->field(true)->find();

        if(!$hasAdviser){
            $this->returnAjaxJson(FAIL,'课程顾问不存在！');
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
        M()->startTrans();
        if(empty($post['pay_type_list']) || empty($post['amount_list'])){
            $this->returnAjaxJson(FAIL,'必须添加支付信息！');
        }
        //更新报名已提交金额
        $allAmount = array_sum($post['amount_list']);
        if($allAmount<=0){
            $this->returnAjaxJson(FAIL,'请填写正确的支付金额！');
        }
        $post['amount_submitted'] = $allAmount;

//        if($post['amount_submitted'] > $post['package_total_price'])
//            $this->returnAjaxJson(FAIL,'已提交金额不能大于总金额！');

        //添加用户支付信息
        $UserPay = new UserPayModel();
        if($UserPay->create($post) === false || ($payId = $UserPay->add()) === false){
            M()->rollback();
            $this->returnAjaxJson(FAIL,$UserPay->getError());
        }
        //循环添加多个支付方式记录
        $UserPayLog = new UserPayLogModel();
        $post['pay_id'] = $payId;

        foreach ($post['pay_type_list'] as $key=>$val){
            $post['amount'] = $post['amount_list'][$key];
            $post['pay_time'] = strtotime($post['pay_time_list'][$key]);
            $post['pay_type'] = $val;
            if($UserPayLog->create($post) === false || $UserPayLog->add() === false){
                M()->rollback();
                $this->returnAjaxJson(FAIL,$UserPayLog->getError());
            }
        }

        //$post['amount_submitted'] = $post['amount_submitted']+$post['amount'];
        $UserResitration = new UserRegistrationModel();
        $regData = [
            'remark'            =>  $post['remark'],
            'amount_submitted'  =>  ['exp','amount_submitted+'.$allAmount]
        ];
        $eff = $UserResitration->where(['id'=>$post['registration_id']])
            ->save($regData);
        if(!$eff){
            M()->rollback();
            $this->returnAjaxJson(FAIL,$UserResitration->getError());
        }
        //更新报名信息的最后一次提交支付记录时间
        $UserResitration->setLastPayTime($post['registration_id']);
        M()->commit();
        $this->returnAjaxJson(SUCCESS,'提交成功！');
    }

    function addSubmit(Request $request){
        $post = $request->post();
        $UserRegistration = new UserRegistrationModel();
        $UserHeadMaster = new UserHeadMasterModel();
        $adviserId = 0;
        $tmpMap = [];
        if($post['client_submit'] == "WAP"){
            if(!key_exists("adviser_mobile",$post) || !$post['adviser_mobile']){
                return Util::ajaxReturn(Util::FAIL,'请填写课程顾问手机号');
            }
            $tmpMap = ['mobile'=>$post['adviser_mobile']];
        }else if($post['client_submit'] == "PC"){
            $tmpMap = ['uid'=>$this->getAdviserId()];
            //$adviserId = $this->getAdviserId();
        }else{
            return Util::ajaxReturn(Util::FAIL,'信息来源错误!');
        }


        //判断手机号是否在主站注册过
        $dpData = [
            'type'      =>  'MOBILE',
            'keyword'   =>  $post['mobile'],
        ];
        $hasDapengUser = DapengUserApi::getInfo($dpData);
        if($hasDapengUser['code'] == FAIL){
            $this->returnAjaxJson(FAIL,'该开课手机号未注册！');
        }
        //$post['is_open']    = 1;  //默认报名课程开启

        //查询和判断课程顾问
        $hasAdviser = $UserHeadMaster->where($tmpMap)->field(true)->find();
        if(!$hasAdviser){
            $this->returnAjaxJson(FAIL,'课程顾问不存在！');
        }
        $adviserId = $hasAdviser['uid'];
        //获取当前课程顾问信息
        $post['adviser_id'] = $adviserId;
        $post['adviser_name'] = $hasAdviser['name'];
        $post['adviser_qq'] = $hasAdviser['qq'] ?: '';
        //$post['is_open']    = 1;  //默认报名课程开启
        //该学员总的支付金额
        if(!isset($post['pay_type_list']) || !isset($post['amount_list']) || empty($post['pay_type_list']) || empty($post['amount_list'])){
            $this->returnAjaxJson(FAIL,'必须添加支付信息！');
        }
        $allAmount= array_sum($post['amount_list']);
        if($allAmount<=0){
            $this->returnAjaxJson(FAIL,'请填写正确的支付金额！');
        }
        $post['amount_submitted'] = $allAmount;
//        if($post['amount_submitted'] > $post['package_total_price'])
//            $this->returnAjaxJson(FAIL,'已提交金额不能大于总金额！');
        //$post['amount_submitted'] = $post['amount_submitted']+$post['amount'];
        //$post['amount_submitted'] = $post['amount'];
        $CoursePackage = new CoursePackageModel();
        M()->startTrans(); //开启事务
        //先添加课程套餐

        //添加报名信息
        setDefault($regId,0);
        //必须选择赠送课程
        if(!isset($post['give_id']) || $post['give_id'] == ''){
            $this->returnAjaxJson(FAIL,'请选择赠送课程！');
        }

        if($UserRegistration->create($post) === false || ($regId = $UserRegistration->add()) === false){
            M()->rollback();
            $this->returnAjaxJson(FAIL,$UserRegistration->getError());
        }
        //添加用户支付信息
        $UserPay = new UserPayModel();
        $post['registration_id'] = $regId; //关联报名课程记录ID
        if($UserPay->create($post) === false || ($payId = $UserPay->add()) === false){
            M()->rollback();
            $this->returnAjaxJson(FAIL,$UserPay->getError());
        }
        //循环添加多个支付方式记录
        $UserPayLog = new UserPayLogModel();
        $post['pay_id'] = $payId;

        foreach ($post['pay_type_list'] as $key=>$val){
            $post['amount'] = $post['amount_list'][$key];
            $post['pay_time'] = strtotime($post['pay_time_list'][$key]);
            $post['pay_type'] = $val;
            if($UserPayLog->create($post) === false || $UserPayLog->add() === false){
                M()->rollback();
                $this->returnAjaxJson(FAIL,$UserPayLog->getError());
            }
        }
        $UserRegistration->setPackageAllTitle($regId);
        //更新报名信息的最后一次提交支付记录时间
        $UserRegistration->setLastPayTime($regId);
        M()->commit();
        $this->returnAjaxJson(SUCCESS,'信息提交成功！');
        return view("admin.registration.add");
    }

}