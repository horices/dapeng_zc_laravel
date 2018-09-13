<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserException;
use App\Exceptions\UserValidateException;
use App\Http\Controllers\Controller;
use App\Jobs\SendCreatedRosterNotification;
use App\Models\RosterModel;
use App\Models\UserModel;
use App\Utils\Util;
use Curl\Curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    function getInfo(Request $request){
        Validator::make($request->all(),[
            'schoolId'  =>  "nullable|in:SJ,MS,IT",
            'type'  =>  "required|in:id,dapeng_user_id,qq,mobile,name,wx",
            'keyword'   =>  'required'
        ],[
            'type.required' =>  "请输入要查询的类型",
            'type.in'   =>  "请选择正确的查询类型",
            'schoolId.in' =>  "请选择正确的学院",
            'keyword.required'   =>  "请输入查询的关键字"
        ])->validate();
        $curl = app("curl");
        $result = [];
        $result['sj'] = new \stdClass();
        $result['ms'] = new \stdClass();
        $result['it'] = new \stdClass();
        //没有传入学院ID，或传入当前学院，表示需要查询当前学院的信息
        if(!$request->get("schoolId") || Util::getSchoolName() == $request->get("schoolId")){
            $roster = RosterModel::with('group','adviser')->where(Input::get("type"),Input::get("keyword"))->orderBy("id","desc")->first();
            if($roster){
                $roster['origin'] = Str::lower(Util::getSchoolName());
                $roster['adviser_qq'] = $roster->adviser->qq;
                $roster['adviser_name'] = $roster->adviser->name;
                $roster['adviser_mobile'] = $roster->adviser->mobile;
                $roster['qq_group_url'] = $roster->group->qrc_link;
                $roster['qq_group_qrc'] = $request->getSchemeAndHttpHost().$roster->group->qrc_url;
                $result[Str::lower(Util::getSchoolName())] = $roster->toArray();
            }
        }
        //如果当前是设计学院，且需要查询其它学院，向其它学院发送通知
        if(Util::getSchoolName() == Util::SCHOOL_NAME_SJ && $request->get("schoolId") != 'SJ'){
            //判断是否需要查询美术学院的数据
            if(!$request->get("schoolId")  || $request->get("schoolId") == Util::SCHOOL_NAME_MS){
                //获取美术学院数据
                $baseUrl = URL::route(Route::currentRouteName(),[],false);
                $host = Util::getWebSiteConfig('ZC_URL.'.Util::SCHOOL_NAME_MS.".".Util::getCurrentBranch(),false);
                $request->merge($this->getPostData($request->except('sign')));
                $response = $curl->get($host.$baseUrl."?".http_build_query($request->all()))->response;
                $curlData = Util::jsonDecode($response);
                if(!$curlData){
                    throw new UserValidateException("获取美术学院信息返回失败".$response);
                }
                //美术学院获取失败时，直接返回
                if($curlData['code'] == Util::FAIL){
                    return Util::ajaxReturn(Util::FAIL,$curlData['msg']);
                }
                $result = collect($result)->merge(collect($curlData['data'])->filter());
            }

            //判断是否需要查询IT学院的数据
            if(!$request->get("schoolId")  || $request->get("schoolId") == Util::SCHOOL_NAME_IT){
                //获取美术学院数据
                $baseUrl = URL::route(Route::currentRouteName(),[],false);
                $host = Util::getWebSiteConfig('ZC_URL.'.Util::SCHOOL_NAME_IT.".".Util::getCurrentBranch(),false);
                $request->merge($this->getPostData($request->except('sign')));
                $response = $curl->get($host.$baseUrl."?".http_build_query($request->all()))->response;
                $curlData = Util::jsonDecode($response);
                if(!$curlData){
                    throw new UserValidateException("获取美术学院信息返回失败".$response);
                }
                //美术学院获取失败时，直接返回
                if($curlData['code'] == Util::FAIL){
                    return Util::ajaxReturn(Util::FAIL,$curlData['msg']);
                }
                $result = collect($result)->merge(collect($curlData['data'])->filter());
            }
        }
        return Util::ajaxReturn(Util::SUCCESS,"",$result);
    }


    /**
     * M站注册调用该接口，设置用户的主站信息 dapeng_user_id,dapeng_user_mobile,dapeng_reg_time
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function setInfo(Request $request){
        Validator::make($request->all(),[
            'schoolId'  =>  "required|in:SJ,MS,IT",
            'type'  =>  "required|in:id,dapeng_user_id,qq,mobile,name",
            'keyword'   =>  'required'
        ],[
            'type.required' =>  "请输入要查询的类型",
            'type.in'   =>  "请选择正确的查询类型",
            'schoolId.in' =>  "请选择正确的学院",
            'keyword.required'   =>  "请输入查询的关键字"
        ])->validate();
        $curl = app("curl");
        //如果当前是设计学院，且需要查询美术学院，向美术学院发送通知
        //如果当前是设计学院，且需要查询美术学院，向美术学院发送通知
        if(Util::getSchoolName() == Util::SCHOOL_NAME_SJ && $request->get("schoolId") != 'SJ'){
            //获取美术学院数据
            $baseUrl = URL::route(Route::currentRouteName(),[],false);
            $host = Util::getWebSiteConfig('ZC_URL.'.$request->get("schoolId").".".Util::getCurrentBranch(),false);
            $request->merge($this->getPostData($request->except('sign')));
            $response = $curl->post($host.$baseUrl,$request->all())->response;
            $curlData = Util::jsonDecode($response);
            if(!$curlData){
                throw new UserValidateException("获取".$request->get("schoolId")."学院信息返回失败".$response);
            }
            //通知其它学院时，直接返回，不再修改设计学院
            return $curlData;
            //美术学院获取失败时，直接返回
            /*if($curlData['code'] == Util::FAIL){
                return Util::ajaxReturn(Util::FAIL,$curlData['msg']);
            }*/
        }
        //$rosterId = $request->get("roster_id");
        $roster = RosterModel::where(Input::get("type"),Input::get("keyword"))->first();
        if(!$roster){
            Log::error("主站已经注册成功，但展翅未改dapeng_user_mobile：".Input::get("keyword"));
            throw new UserValidateException("未找到专属链接的用户！");
        }
        $roster->fill($request->only(['dapeng_user_mobile','is_reg','dapeng_user_id','schoolId']));
        if($roster->save() === false){
            Log::error("保存roster信息失败");
            throw new UserValidateException("修改信息失败！");
        }
        return Util::ajaxReturn(Util::SUCCESS,"success","修改成功");
    }

    /**
     * 判断一个量当前学院否可以被提交
     * @params array $data 需要验证的数据
     *               keys:  roster_type:[1:qq,2:微信]
     *                      roster_no: 量的号码
     */
    function checkRosterStatus(Request $request){
        $data = RosterModel::validateRosterData($request->all(),false);
        return Util::ajaxReturn(Util::SUCCESS,"可以正常添加",$data);
    }


    /**
     * 添加一个新的QQ号
     */
    function add(Request $request){
        $data = $request->all();
        //防止并发时，重复提交量的问题
        $resource = fopen("roster.lock","w+");
        flock($resource,LOCK_EX);
        DB::beginTransaction();
        if($roster = RosterModel::addRoster(collect($data)->toArray(),true)){
            DB::commit();
            //发送添加成功的通知(此通知需要同步发送,先将其它学院置为灰色,通知后，当前量被重置为老量,且没有新活标识)
            SendCreatedRosterNotification::dispatch($roster->toArray());
            $roster->load("group");
            $returnData['code'] = Util::SUCCESS;
            $returnData['msg'] = "添加成功";
            $returnData['data'] = $roster;
        }else{
            DB::rollback();
            $returnData['code'] = Util::FAIL;
            $returnData['msg'] = "添加失败";
        }
        flock($resource,LOCK_UN);
        fclose($resource);
        return response()->json($returnData);
    }
}
