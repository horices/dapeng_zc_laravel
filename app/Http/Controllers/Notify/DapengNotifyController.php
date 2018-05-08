<?php

namespace App\Http\Controllers\Notify;
use App\Models\RosterCourseLogModel;
use App\Models\RosterCourseModel;
use App\Models\RosterModel;
use App\Utils\Util;
use Curl\Curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;


/**
 * 大鹏主站系统通知
 * Class DapengNotifyController
 * @package App\Http\Controllers\Notify
 */
class DapengNotifyController extends BaseController
{
    /**
     * 初始化实例对象,转发请求,因为每个通知的签名验证方式不统一，所以需要单独进行转发
     * DapengNotifyController constructor.
     * @param Curl $curl
     */
    function __construct(Curl $curl)
    {
        parent::__construct($curl);
    }

    /**
     * 注册通知
     */
    function reg(){
        $qq = Input::get("qq");
        $roster = RosterModel::where('qq',$qq)->orderBy("addtime","desc")->first();
        $roster->is_reg = 1;
        $roster->dapeng_reg_time = ceil(Input::get("dapeng_reg_time",time()*1000)/1000);
        if(!$roster->save()){
            Log::error("更新用户注册状态失败");
        }
        return Util::ajaxReturn(Util::SUCCESS,"success");
    }
    /**
     * 开课通知
     */
    function openCourse(Request $request,RosterCourseLogModel $courseLog){
        //查询这个QQ号的情况
        $roster = RosterModel::where("qq",Input::get("qq"))->orderBy("id","desc")->first();
        $request->merge([
            'action'=>1,
            'roster_id' => $roster->id,
            'addtime'   =>  time(),
            'user_type' => $roster->type,
            'course_type'=>app('status')->getCourseTypeColumnValue(Input::get('type'))
        ]);
        //修改注册状态和时间
        if(!$roster->dapeng_reg_time){
            $roster->is_reg = 1;
            $roster->dapeng_reg_time = ceil($request->get("dapeng_reg_time",time()*1000)/1000);
            $roster->save();
        }

        if(!RosterCourseLogModel::create(Input::get())){
            Log::error("开课通知处理失败");
        }
        return Util::ajaxReturn(Util::SUCCESS,"success");
    }
    /**
     * 关闭课程通知
     */
    function closeCourse(Request $request,RosterCourseLogModel $courseLog){
        //查询这个QQ号的情况
        $roster = RosterModel::where("qq",Input::get("qq"))->orderBy("id","desc")->first();
        $request->merge([
            'action'=>2,
            'roster_id' => $roster->id,
            'addtime'   =>  time(),
            'user_type' => $roster->type,
            'course_type'=>app('status')->getCourseTypeColumnValue(Input::get('type'))
        ]);
        if(!RosterCourseLogModel::create(Input::get())){
            Log::error("开课通知处理失败");
        }
        return Util::ajaxReturn(Util::SUCCESS,"success");
    }
}
