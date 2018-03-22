<?php

namespace App\Http\Controllers\Notify;
use App\Exceptions\UserValidateException;
use App\Models\RosterCourseLogModel;
use App\Models\RosterModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;


/**
 * 大鹏主站系统通知
 * Class DapengNotifyController
 * @package App\Http\Controllers\Notify
 */
class DapengNotifyController extends BaseController
{
    /**
     * 注册通知
     */
    function reg(){
        $qq = Input::get("qq");
        $roster = RosterModel::where('qq',$qq)->orderBy("addtime","desc")->first();
        $roster->is_reg = 1;
        $roster->dapeng_reg_time = Input::get("dapeng_reg_time",time());
        if(!$roster->save()){
            Log::error("更新用户注册状态失败");
        }
        return Util::ajaxReturn(Util::SUCCESS,"success");
    }
    /**
     * 开课通知
     */
    function openCourse(){
        $qq = Input::get("qq");
        $roster = RosterModel::where('qq',$qq)->orderBy("addtime","desc");
        RosterCourseLogModel::create(Input::get());
    }
}
