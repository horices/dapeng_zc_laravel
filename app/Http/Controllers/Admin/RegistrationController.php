<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25 0025
 * Time: 10:01
 */

namespace App\Http\Controllers\Admin;


use App\Api\DapengUserApi;
use App\Exceptions\DapengApiException;
use App\Exceptions\UserValidateException;
use App\Http\Requests\RegistrationForm;
use App\Models\CoursePackageModel;
use App\Models\RebateActivityModel;
use App\Models\UserHeadMasterModel;
use App\Models\UserPayLogModel;
use App\Models\UserPayModel;
use App\Models\UserRegistrationModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;


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
    function postAddRegistration(RegistrationForm $registration,UserRegistrationModel $UserRegistration,UserPayModel $UserPayModel,UserPayLogModel $UserPayLogModel){
        $post = $registration->post();
        //补全字段数据
        $UserRegistration->completeData($post);
        //插入报名记录
        $UserRegistration->addData($post,$UserPayModel,$UserPayLogModel);
        return response()->json(['code'=>Util::SUCCESS,'msg'=>'信息提交成功！']);
    }

    /**
     * @note 更新报名记录（添加支付记录）
     */
    function postUpdateRegistration(RegistrationForm $request,UserRegistrationModel $UserRegistration,UserPayModel $UserPayModel,UserPayLogModel $UserPayLogModel){
        $data = $request->post();
        //补全字段
        $data = $UserRegistration->completeData($data);
        //写入数据
        $UserRegistration->updateData($data,$UserPayModel,$UserPayLogModel);
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
        $query = UserRegistrationModel::query()->where('is_active',1);
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
            $query->where("create_time",">=",strtotime($startDate));
        }
        //根据课程顾问ID来筛选所属学员的统计信息
//        $adviserId = I("get.adviserId",0,"intval");
//        if($adviserId){
//            $map['adviser_id'] = $adviserId;
//        }
        $endDate = urldecode($request->get("endDate"));
        if(!empty($endDate)){
            $query->where("create_time","<=",strtotime($endDate));
        }

        $list = $query->orderBy("last_pay_time","desc")->paginate(15);
        foreach ($list as $key=>$val){
            $list[$key]['idk'] = $key+1;
        }
        $_GET['subnavAction'] = "userPayList";
        return view("admin.registration.list-user",[
            'list'          =>  $list,
            'adminInfo'     =>  $this->getUserInfo()
        ]);
    }

    function getPayList(Request $request){
        $UserPayLog = new UserPayLogModel();
        $map = [];

        //根据课程顾问ID来筛选所属学员的支付记录信息
        $adviserId = $request->get("adviserId");
        if($adviserId){
            $map['upl.adviser_id'] = $adviserId;
            $mapA['adviser_id'] = $adviserId;
        }
        //月统计条总金额
        $allSubmitAmount = UserPayLogModel::whereBetween('create_time',[strtotime(date('Y-m-1 00:00:00')),time()])->sum("amount");

        $userPayLogModel = UserPayLogModel::query();
        //根据学员姓名检索
        $name = $request->get("name");
        if(!empty($name)){
            $userPayLogModel->where("name","like",'%'.$name.'%');
        }
        //根据课程顾问姓名检索
        $adviserName = $request->get("adviserName");
        $userPayLogModel->whereHas('userHeadmaster' , function($query) use ($adviserName){
            if(!empty($adviserName)){
                $query->where("name","like",'%'.$adviserName.'%');
            }
        })->with("userHeadmaster");


        //根据导学状态检索
        $isOpen = $request->get("is_open");
        $userPayLogModel->whereHas('userRegistration',function ($query) use ($isOpen){
            if($isOpen != ''){
                $query->where("is_open",$isOpen);
            }
        })->with('userRegistration');


        //根据时间来检索
        $startDate = $request->get("startDate");
        if(!empty($startDate)){
            $userPayLogModel->where("create_time",">=",strtotime($startDate));
        }

        $endDate = $request->get("endDate");
        if(!empty($endDate)){
            $userPayLogModel->where("create_time","<=",strtotime($endDate)+1);
        }
        $list = $userPayLogModel->orderBy("id","desc")->paginate(15);

        //总记录条数
        return view("admin.registration.list-pay",[
            'allSubmitAmount'   =>  $allSubmitAmount,
            'list'              =>  $list,
            'adminInfo'         =>  $this->getUserInfo()
        ]);
    }

    function getListDetail(Request $request){
        $logId = $request->get("pay_log_id");

        $UserPayLog = new UserPayLogModel();
        $detail = UserPayLogModel::find($logId);

//        $detail['pay_type_str'] = $UserPayLog->payType[$detail['pay_type']];
//        $detail['pay_time'] = date("Y-m-d H:i:s",$detail['pay_time']);

        //报名信息
//        $UserRegistration = new UserRegistrationModel();
//        $regData = $UserRegistration->where(['id'=>$detail['registration_id']])->field(true)->find();
//        $detail['package_all_title'] = $regData['package_all_title'];
//        $CoursePackage = new CoursePackageModel();
//        $tmpMap = [
//            'id'    =>  ['in',[$regData['package_id'],$regData['package_attach_id']]]
//        ];
//        $detail['package_total_price'] = $CoursePackage->where($tmpMap)->sum("price");
        //套餐信息
//        $CoursePackage = new CoursePackageModel();
//        $packageData = $CoursePackage->where(['id'=>$regData['package_id']])->find();
//        $detail['package_title'] = $packageData['title'];
//        $detail['package_price'] = $packageData['price'];
//        if($packageData['status'] == 'DEL'){
//            $detail['package_title'] = $detail['package_title']."(已删)";
//        }
        //附加套餐列表

        $packageAttachList = CoursePackageModel::where([
            ['type','=',1],
            ['status','=','USE']
        ])->orWhere('id',$detail->userHeadmaster->package_attach_id)->get();

        //活动信息
//        $RebateActivity = new RebateActivityModel();
//        //$rebateData = $RebateActivity->where(['id'=>$regData['rebate_id']])->find();
//        $detail['rebate_title'] = $detail['rebate_title'] ?: '';
//        $detail['rebate_price'] = $detail['rebate_price'] ?: 0;
        //获取赠送课程列表
        $this->assign('giveList',json_encode(array_reverse($CoursePackage->give),JSON_UNESCAPED_UNICODE));

        $detail = json_encode($detail,JSON_UNESCAPED_UNICODE);
        $this->assign('r',$detail);
        //获取支付方式列表
        $this->assign("pay_type_list",json_encode($UserPayLog->payType,JSON_UNESCAPED_UNICODE));
        //获取优惠活动列表
        $rebateMap = [
            'status'    =>  'USE',
            '_logic'    =>  'OR',
            'id'        =>  $regData['rebate_id']
        ];
        $rebateList = $RebateActivity->where($rebateMap)->select();
        $this->assign("rebateList",json_encode($rebateList,JSON_UNESCAPED_UNICODE));
        //分期方式列表
        $fqTypeList = $UserRegistration->fqType;
        $this->assign("fqTypeList",json_encode($fqTypeList,JSON_UNESCAPED_UNICODE));

        $_GET['subnavAction'] = "userPayList";
        return view("admin.registration.list-pay",[
            'packageAttachList' =>  json_encode($packageAttachList,JSON_UNESCAPED_UNICODE),

        ]);
    }

}