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
            'adminInfo'     =>  $this->getUserInfo()
        ]);

    }


}