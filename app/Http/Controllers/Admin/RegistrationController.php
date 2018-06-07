<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25 0025
 * Time: 10:01
 */

namespace App\Http\Controllers\Admin;


use App\Exceptions\UserException;
use App\Exceptions\UserValidateException;
use App\Http\Requests\RegistrationForm;
use App\Models\CoursePackageModel;
use App\Models\RebateActivityModel;
use App\Models\UserEnrollModel;
use App\Models\UserPayLogModel;
use App\Models\UserPayModel;
use App\Models\UserRegistrationModel;
use App\Utils\Api\DapengUserApi;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;


class RegistrationController extends BaseController{

    /*function getAdd(){
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
            'giveList'      =>  json_encode(array_reverse(CoursePackageModel::$giveList),JSON_UNESCAPED_UNICODE),//赠送课程
            'fqTypeList'    =>  json_encode($fqTypeList,JSON_UNESCAPED_UNICODE),
            'rebateList'    =>  json_encode($rebateList,JSON_UNESCAPED_UNICODE),
            'leftNav'           => "admin.registration.add"
        ]);
    }*/

    /**
     * 可修改权限
     * @param UserEnrollModel $enroll
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getAdd(UserEnrollModel $enroll,Request $request){
        $userInfo = $this->getUserInfo();
        $userRole = $userInfo->grade <=5 ? "":"adviser";
        if(!$request->get("mobile")){
            return view("admin.registration.add-first",[
                $request->get("leftNav","admin.registration.add"),
            ]);
        }
        $mobile = $request->get("mobile");
        //不存在模型则创建一个，保证前端VUE不会出现问题
        $enroll = UserEnrollModel::firstOrNew(['mobile'=>$mobile],['is_guide'=>1]);
        if($enroll->id){
            $enroll = $enroll->toArray();
            UserRegistrationModel::where("enroll_id",$enroll['id'])->get()->each(function($registration) use (&$enroll,$userRole){
                //查询当前报名下的支付信息
                $payList = UserPayLogModel::where("registration_id",$registration->id)->select("id","pay_type","amount","pay_time","adviser_name")->orderBy("id","asc")->get()->toArray();
                $payList = collect($payList)->transform(function($v) use (&$enroll,$userRole){
                    $v['pay_time'] = $v['pay_time_text'];
                    $v['readonly'] = false;
                    if($userRole == "adviser"){
                        $v['readonly'] = true;
                    }
                    $enroll['pay_adviser_name'] = $v['adviser_name'];
                    return $v;
                });
                $registration['payList'] = $payList;
                $enroll['registrations'][$registration->school_id] = $registration;
            });
        }

        $packageList = [];
        //查询所有的套餐列表
        CoursePackageModel::with("rebate")->where("course_attach","!=",'')->get()->mapWithKeys(function($v){
            return [$v['id']=>$v];
        })->each(function($v) use (&$packageList){
            $packageList[$v['school_id']][$v['id']] = $v;
        });
        $userInfo = $this->getUserInfo();
        if($userInfo->grade == 9 || $userInfo->grade == 10){
            $request->merge(['userRole' =>  'adviser','leftNav'=>"admin.registration.add.user"]);
        }
        $request->merge(['back_url'=>$request->get("back_url",route("admin.registration.list.pay"))]);
        return view("admin.registration.add",[
            'enroll'    =>  $enroll,
            "leftNav"   =>  $request->get("leftNav","admin.registration.add"),
            "userRole"  =>  $userRole,
            'packageList'   =>   $packageList
        ]);
    }

    /**
     * 用户添加或修改
     * @return mixed
     */
    function getUserAdd(Request $request){
        $request->merge(['userRole' =>  'adviser','leftNav'=>"admin.registration.add.user","editRoute"=>"admin.registration.add.user"]);
        return Route::respondWithRoute("admin.registration.add");
    }
    /**
     * 添加或修改报名信息
     */
    function postSaveRegistration(Request $request){
        DB::transaction(function () use($request){
            $jsonDecode = function (&$v){
                $t = Util::jsonDecode($v);
                if($t !== null){
                    $v = $t;
                }
            };
            $enrollData = $request->get("enroll");
            if($enrollData['qq'] && $enrollData['wx']){
                throw new UserValidateException("QQ和微信只能填一项");
            }
            $validator = Validator::make($enrollData,[
                'name'  =>  "required",
                "mobile"    =>  ["required","unique:user_enroll,mobile,".collect($enrollData)->get('id')],
                "is_guide"  =>  'required',
            ],[
                "name.required" =>  "姓名必须填写",
                "mobile.required"        =>  "请输入学员手机号",
                "mobile.unique" =>  "该手机号已经存在",
                "qq.required"   =>  "QQ号和微信号至少填一项",
                "qq.regex"   =>  "请输入正确的QQ号",
                "wx.required"   =>  "QQ号和微信号至少填一项",
                "wx.regex"   =>  "请输入正确的微信号",
                "is_guide.required"     =>  "请选择是否导学"
            ]);
            $validator->sometimes("qq",["required","regex:/^\d{5,10}$/"],function($input){
                return $input->wx == '';
            });
            $validator->sometimes("wx",["required","regex:/^[\-a-zA-Z]{1}[-_a-zA-Z0-9]{5,19}+$/"],function($input){
                return $input->qq == '';
            });
            $validator->validate();
            //添加新的主报名信息
            if(!isset($enrollData['id'])){
                $enroll = UserEnrollModel::create($request->get("enroll"));
            }else{
                $enroll = UserEnrollModel::find($enrollData['id']);
                $enroll->fill($request->get("enroll"));
                if($enroll->save() === false){
                    throw new UserException("更新主站失败");
                }
            }

            //查询该主报名信息的所有学院报名信息
            $registrationIds = UserRegistrationModel::where("enroll_id","=",$enroll->id)->pluck("id");
            //添加副报名信息
            foreach ($request->get("registration") as $k=>$registrationData) {
                if(!is_string($k)){
                    continue;
                }
                $payList = [];
                if(isset($registrationData['pay_list'])){
                    $payList = $registrationData['pay_list'];
                    unset($registrationData['pay_list']);
                }
                //添加报名信息
                $registrationData['enroll_id'] = $enroll->id;
                $registrationData['name'] = $enroll->name;
                $registrationData['adviser_id'] = $enroll->adviser_id;
                $registrationData['adviser_name'] = $enroll->adviser_name;
                $registrationData['mobile'] = $enroll->mobile;
                $registrationData['qq'] = $enroll->qq;
                $registrationData['wx'] = $enroll->wx;
                Validator::make($registrationData,[
                    'package_id'    =>  "required"
                ],[
                    "package_id.required"   =>  "请选择主套餐"
                ])->validate();
                //array_walk_recursive($registrationData,$jsonDecode);
                array_walk_recursive($registrationData,$jsonDecode);
                $registrationData['package_price'] = $registrationData['package_attach_content']['package_info']["price"];
                if(!isset($registrationData['id'])){
                    $registration = UserRegistrationModel::create($registrationData);
                }else{
                    //如果存在该ID，则该ID不需要被移除
                    $registrationIds = collect($registrationIds)->diff($registrationData['id']);
                    $registration = UserRegistrationModel::find($registrationData['id']);
                    $registration->fill($registrationData);
                    if($registration->save() === false){
                        throw new UserException("更新副报名信息失败");
                    }
                }
                //查询该学院报名下的所有支付信息
                $payIds = UserPayLogModel::where("registration_id",$registration->id)->pluck("id");
                //开始更新支付信息
                foreach ($payList as $v){
                    $v = Util::jsonDecode($v);
                    $tempdata['registration_id'] = $registration->id;
                    $tempdata['qq'] = $enroll->qq;
                    $tempdata['wx'] = $enroll->wx;
                    //支付记录暂时不允许修改，
                    $tempdata['amount'] = $v['amount'];
                    $tempdata['pay_type'] = $v['pay_type'];
                    $tempdata['pay_time'] = strtotime($v['pay_time']);
                    $tempdata['mobile'] = $enroll->mobile;
                    $tempdata['name'] = $enroll->name;
                    $tempdata['enroll_id'] = $enroll->id;
                    if(!isset($v['id'])){
                        UserPayLogModel::create($tempdata);
                    }else{
                        $payIds = collect($payIds)->diff([$v['id']]);
                        //支付记录不允许修改
                        /*$payLog = UserPayLogModel::find($v['id']);
                        $payLog->fill($tempdata);
                        if( $payLog->save() === false ){
                            throw new UserException("更新支付记录失败");
                        }*/
                    }
                }
                //删除已经删除的支付记录
                $payIds = $payIds->toArray();
                if($payIds && UserPayLogModel::destroy($payIds) === false){
                    throw new UserException("删除支付记录失败");
                }
                //更新最后支付时间
                $lastPay = UserPayLogModel::where("registration_id",$registration->id)->orderBy("create_time","desc")->first();
                $registration->last_pay_time = $lastPay->create_time->timestamp;
                //用户已经提交的所有金额
                $registration->amount_submitted = UserPayLogModel::where("registration_id",$registration->id)->sum("amount");
                if($registration->save() === false){
                    throw new UserValidateException("更新最后支付时间及支付金额失败");
                }
            }
            //删除已经删除的报名支付
            if($registrationIds && UserRegistrationModel::destroy($registrationIds->toArray()) === false){
                throw new UserException("删除报名记录失败");
            }
        });
        return [
            'code'  =>  Util::SUCCESS,
            'msg'   =>  "保存成功",
            "url"   =>  $request->get("back_url"),
            "data"  =>  ""
        ];

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
        //$hasReg = UserRegistrationModel::with(["rebateActivity"])->where("mobile",$post['mobile'])->orderBy("id","desc")->first();
        $hasReg = UserEnrollModel::where("mobile",$post['mobile'])->count();
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
        $eff = $UserRegistration->addData($post,$UserPayModel,$UserPayLogModel);
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
        $title = Input::get("title",'trim');
        if(!$title){
            throw new UserValidateException("关键字不能为空！");
        }
        $list = CoursePackageModel::where([
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
        $query = UserRegistrationModel::with(["userHeadmaster"])->where('is_active',1);
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
        $adminInfo = $this->getUserInfo();
        if($adminInfo['grade'] >=9 ){
            $query->where("adviser_id",$adminInfo['uid']);
        }
//        $adviserId = I("get.adviserId",0,"intval");
//        if($adviserId){
//            $map['adviser_id'] = $adviserId;
//        }
        $endDate = urldecode($request->get("endDate"));
        if(!empty($endDate)){
            $query->where("create_time","<=",strtotime($endDate));
        }

        $query->orderBy("last_pay_time","desc");
        if($request->get('export') == 1){
            return $this->exportListUser($query);
        }
        $list = $query->paginate(15);
        foreach ($list as $key=>$val){
            $list[$key]['idk'] = $key+1;
        }
        return view("admin.registration.list-user",[
            'list'          =>  $list,
            'adminInfo'     =>  $this->getUserInfo(),
            'leftNav'           => "admin.registration.list"
        ]);
    }

    /**
     * 导出用户统计列表
     * @param $query
     */
    function exportListUser($query){
        $exportData['filename'] = "用户统计".date("Y-m-d_H:i:s");
        $exportData['title'] = [
            //'id'                       =>  '序号',
            'adviser_name'           =>  '课程顾问',
            'name'                      =>  '学员姓名',
            'mobile'                    =>  '开课手机',
            'qq'                        =>  'QQ号',
            'wx'                        =>  '微信',
            'school_text'               =>  '学院名称',
            'package_all_title'         =>  '报名套餐',
            'attach_course_text'        =>  '附加课程',
            'package_all_price'         =>  '套餐总金额',
            'rebate'                    =>  '优惠金额',
            'package_total_price'       =>  '应交总金额',
            'amount_submitted'          =>  '已收金额',
            'is_open_text'              =>  '开课状态',
            'last_pay_time_text'        =>  '提交时间',
            'guide_text'                =>  '是否导学',
        ];
        $exportData['data'] = $query->take(3000)->get();
        return $this->export($exportData);
    }

    /**
     * 获取支付记录列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getPayList(Request $request){
        $UserEnrollModel = UserEnrollModel::with(["userRegistration","userPayLog"=>function($payLog){
            $payLog->orderBy("id","desc");
        }]);
        //根据学员姓名检索
        $name = $request->get("name");
        if(!empty($name)){
            $UserEnrollModel->where("name","like",'%'.$name.'%');
        }
        //根据开课手机号检索
        $mobile = $request->get("mobile");
        if($mobile){
            $UserEnrollModel->where("mobile","like",'%'.$mobile.'%');
        }
        //根据课程顾问ID来筛选所属学员的统计信息
        $adminInfo = $this->getUserInfo();
        if($adminInfo['grade'] >=9 ){
            $UserEnrollModel->where("adviser_id",$adminInfo['uid']);
        }
        //根据课程顾问姓名检索
        $adviserName = $request->get("adviserName");

        if(!empty($adviserName)){
            $UserEnrollModel->where("name","like",'%'.$adviserName.'%');
        }



        //根据导学状态检索
//        $isOpen = $request->get("is_open");
//        $UserEnrollModel->whereHas('userRegistration',function ($query) use ($isOpen){
//            if($isOpen != ''){
//                $query->where("is_open",$isOpen);
//            }
//        })->with('userRegistration');

        //根据时间来检索
        $startDate = $request->get("startDate");
        if(!empty($startDate)){
            $UserEnrollModel->where("create_time",">=",strtotime($startDate));
        }

        $endDate = $request->get("endDate");
        if(!empty($endDate)){
            $UserEnrollModel->where("create_time","<=",strtotime($endDate)+1);
        }

        $UserEnrollModel->orderBy("id","desc");
        //导出
        if($request->input("export") == 1){
            return $this->exportPayList($UserEnrollModel);
        }

        $list = $UserEnrollModel->paginate(15);
        //月统计条总金额
        $userPayLogModel = UserPayLogModel::query();
        $allSubmitAmount = $userPayLogModel->whereBetween('create_time',[strtotime(date('Y-m-1 00:00:00')),time()])->sum("amount");
        //总记录条数
        return view("admin.registration.list-pay",[
            'allSubmitAmount'   =>  $allSubmitAmount,
            'list'              =>  $list,
            'adminInfo'         =>  $adminInfo,
            'leftNav'           => "admin.registration.list"
        ]);
    }

    /**
     * 导出支付列表
     * @param $model
     */
    function exportPayList($model){
        $exportData['filename'] = "用户支付记录导出".date("Y-m-d_H:i:s");
        $exportData['title'] = [
            //'id'                       =>  '序号',
            'last_pay_time_text'     =>   '收款日期',
            'adviser_name'           =>  '课程顾问',
            'name'                      =>  '学员姓名',
            'mobile'                    =>  '开课手机',
//            'total_submitted_price'       =>  '支付总金额',
            'last_pay_type_text'          =>  '付款方式',
            'package_all_price'           =>  "套餐总金额",    //主套餐+附加课程
            'total_should_price'          =>  '应交总金额',    //套餐总金额-优惠
            'total_rebate_price'         =>  '优惠金额',
            'qq'                        =>  'QQ号',
            'wx'                        =>  "微信号",
            'submitted_price.SJ'  =>  '设计学院',
            'submitted_price.MS'  =>  '美术学院',
            'package_title_text'         =>  '报名套餐',
        ];
        $exportData['data'] = $model->take(3000)->get();
        return $this->export($exportData);
    }
    /**
     * 获取支付记录的详情信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getListDetail(Request $request){
        $logId = $request->get("payLogId");
        $detail = UserPayLogModel::with("userRegistration.rebateActivity")->find($logId);
        //附加套餐列表
        $packageAttachList = CoursePackageModel::where([
            ['type','=',1],
            ['status','=','USE']
        ])->orWhere('id',$detail->userRegistration->package_attach_id)->get()->toJson();
        //处理已经赠送课程
        $giveList = array_reverse(CoursePackageModel::$giveList);
        foreach ($giveList as $key=>$val){
            $giveIds = explode(',',$detail->userRegistration->give_id);
            if(in_array($val['id'],$giveIds)){
                $giveList[$key]['checked'] = true;
            }
        }
        //dd($giveList);
        //获取优惠活动列表
        $rebateList = RebateActivityModel::where([
            ['status','=','USE'],
        ])->orWhere('id',$detail->userRegistration->rebate_id)->get()->toJson();
        return view("admin.registration.list-detail",[
            'r'                 =>  $detail,
            'packageAttachList' =>  $packageAttachList,
            //获取赠送课程列表
            'giveList'          =>  collect($giveList)->toJson(),
            //支付方式列表
            'payTypeList'       =>  collect(app("status")->getPayTypeList())->toJson(),
            //优惠活动
            'rebateList'        =>  $rebateList,
            //分期方式列表
            'fqTypeList'        =>  collect(app("status")->getFqType())->toJson(),
            'adminInfo'         =>  collect($this->getUserInfo())->toJson(),
            'leftNav'           => "admin.registration.list"
        ]);
    }

    /**
     * 删除支付记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UserValidateException
     */
    function postDelete(Request $request){
        if(!$request->get("id")){
            throw new UserValidateException("未找到支付记录！");
        }
        $id = $request->get("id");
        $UserPayLog = UserPayLogModel::find($id);
        if(!$UserPayLog){
            throw new UserValidateException("未找到该支付记录!");
        }
        //判断并删除没有支付记录的报名信息和顶级支付记录
        $eff = DB::transaction(function ()use($UserPayLog) {
            return $UserPayLog->delete();
        });
        if($eff){
            return response()->json(['code'=>Util::SUCCESS,'msg'=>'删除成功！']);
        }else{
            throw new UserValidateException("删除失败！");
        }
    }

    /**
     * 修改字段值
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UserValidateException
     */
    function postModField(Request $request){
        if(!$request->get('id') || !$request->has('val') || !$request->get('field')){
            throw new UserValidateException("修改数据错误!");
        }
        $post = $request->input();
        $UserRegData = UserRegistrationModel::find($post['id']);
        if(!$UserRegData){
            throw new UserValidateException("未找到该用户报名记录!");
        }
        $field = $post['field'];
        $UserRegData->$field = $post['val'];
        $data = $UserRegData->toArray();
        $data['registration_id'] = $data['id'];
        UserRegistrationModel::updateValidate($data);
        $eff = $UserRegData->save();
        if($eff){
            return response()->json(['code'=>Util::SUCCESS,'msg'=>'修改成功！']);
        }else{
            throw new UserValidateException("修改失败!");
        }
    }

    /**
     * 修改支付记录字段
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UserValidateException
     */
    function postModLogField(Request $request){
        $logModel = UserPayLogModel::find($request->get('id'));
        if(!$logModel){
            throw new UserValidateException("未找到要修改的支付记录！");
        }
        $field = $request->get("field");
        $logModel->$field = $request->get("val");
        $data = $logModel->toArray();
        $logModel->updateValidate($data);
        $eff = $logModel->save();
        if($eff){
            return Util::ajaxReturn(Util::SUCCESS,'修改成功！');
        }else{
            throw new UserValidateException("修改失败");
        }
    }

    /**
     * 支付记录软删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UserValidateException
     */
    function getDeletePay(Request $request){
        $id = $request->get('id');
        $detail = UserEnrollModel::find($id);
        if(!$detail){
            throw new UserValidateException('未找到用户信息！');
        }
        $eff = $detail->delete();
        if(!$eff){
            throw new UserValidateException('删除失败！');
        }
        return Util::ajaxReturn(Util::SUCCESS,'删除成功！');
    }

}