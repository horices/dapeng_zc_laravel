<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserException;
use App\Exceptions\UserValidateException;
use App\Http\Controllers\Controller;
use App\Models\RosterModel;
use App\Models\UserModel;
use App\Utils\Util;
use Curl\Curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RosterController extends BaseController
{
    /**
     * 获取用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function getInfo(Request $request,Curl $curl){
        Validator::make($request->all(),[
            'schoolId'  =>  "nullable|in:SJ,MS",
            'type'  =>  "required|in:id,dapeng_user_id,qq,mobile,name",
            'keyword'   =>  'required'
        ],[
            'type.required' =>  "请输入要查询的类型",
            'type.in'   =>  "请选择正确的查询类型",
            'schoolId.in' =>  "请选择正确的学院",
            'keyword.required'   =>  "请输入查询的关键字"
        ])->validate();
        $result = [];
        $result['sj'] = new \stdClass();
        $result['ms'] = new \stdClass();;
        //没有传入学院ID，或传入当前学院，表示需要查询当前学院的信息
        if(!$request->get("schoolId") || Util::getSchoolName() == $request->get("schoolId")){
            $roster = RosterModel::with('group','adviser')->where(Input::get("type"),Input::get("keyword"))->orderBy("id","desc")->first();
            if($roster){
                $roster = $roster->toArray();
                $roster['origin'] = Str::lower(Util::getSchoolName());
                $roster['adviser_qq'] = $roster['adviser']['qq'];
                $roster['adviser_name'] = $roster['adviser']['name'];
                $roster['adviser_mobile'] = $roster['adviser']['mobile'];
                $roster['qq_group_url'] = $roster['group']['qrc_link'];
                $roster['qq_group_qrc'] = $roster['group']['qrc_url'];
                $result[Str::lower(Util::getSchoolName())] = $roster;
            }
        }
        //如果当前是设计学院，且需要查询美术学院，向美术学院发送通知
        if(Util::getSchoolName() == Util::SCHOOL_NAME_SJ && $request->get("schoolId") != 'SJ'){
            //获取美术学院数据
            $baseUrl = URL::route(Route::currentRouteName(),[],false);
            $host = Util::getWebSiteConfig('ZC_URL.'.Util::SCHOOL_NAME_MS.".".Util::getCurrentBranch(),false);
            $request->merge(['sign'=>$this->makeSign(['url'=>$host.$baseUrl])]);
            $response = $curl->post($host.$baseUrl,$request->all())->response;
            $curlData = Util::jsonDecode($response);
            if(!$curlData){
                throw new UserValidateException("获取美术学院信息返回失败".$response);
            }
            //美术学院获取失败时，直接返回
            if($curlData['code'] == Util::FAIL){
                return Util::ajaxReturn(Util::FAIL,$curlData['msg'],$curlData['data']);
            }
            $result = collect($result)->merge($curlData);
        }
        return Util::ajaxReturn(Util::SUCCESS,"",$result);
    }


    /**
     * M站注册调用该接口，设置用户的主站信息 dapeng_user_id,dapeng_user_mobile,dapeng_reg_time
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function setInfo(Request $request){
        $rosterId = $request->get("roster_id");
        $roster = RosterModel::find($rosterId);
        $roster->fild($request->all());
        if($roster->save() === false){
            Log::error("保存roster信息失败");
        }
        return Util::ajaxReturn(Util::SUCCESS,"success","修改成功");
    }

    /**
     * 判断一个量否可以被提交
     * @params array $data 需要验证的数据
     *               keys:  roster_type:[1:qq,2:微信]
     *                      roster_no: 量的号码
     */
    function checkRosterStatus(Request $request){
        $data['roster_type'] = $request->get("roster_type");
        $data['roster_no'] = $request->get("roster_no");
        $code = Util::SUCCESS;
        $msg = "可以正常添加";
        //验证时，需要验证seoer身份，这里随意补全一个推广专员的身份
        $seoer = UserModel::seoer()->first();
        $data['seoer_id'] = $seoer->uid;
        try{
            // 对验证进行异常捕获，有异常表示数据错误，不能正常提交
            RosterModel::validateRosterData($data);
        }catch (UserException $e){
            $code = Util::FAIL;
            $msg = $e->getMessage();
        }catch (ValidationException $e){
            $code = Util::FAIL;
            $msg = collect($e->errors())->first()[0];
        }catch (\Exception $e){
            $code = Util::FAIL;
            $msg = "未知错误:".$e->getMessage();
        }
        return Util::ajaxReturn($code,$msg);
    }
}
