<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 15:12
 */

namespace App\Http\Controllers\Notify;


use App\Exceptions\UserNotifyException;
use App\Jobs\SendNotification;
use App\Models\RosterModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class RosterNotifyController extends BaseController
{

    /**
     * 初始化实例对象,转发请求,因为每个通知的签名验证方式不统一，所以需要单独进行转发
     * DapengNotifyController constructor.
     */
    function __construct(Request $request)
    {
        parent::__construct();
        //校验签名是否正确
        if(Util::makeSign($request->except("sign")) != $request->input("sign")){
            throw new UserNotifyException("签名错误");
        }
        //转发请求
        $baseUrl = URL::route(Route::currentRouteName(),[],false);
        //设计学院正式站
        if(Util::getSchoolName() == Util::SCHOOL_NAME_SJ && Util::getCurrentBranch() == Util::MASTER){
            //通知美术学院正式站
            $host = Util::getWebSiteConfig('ZC_URL.'.Util::SCHOOL_NAME_MS.".".Util::MASTER,false);
            SendNotification::dispatch($host.$baseUrl,$request->all());
            //通知IT学院正式站
            $host = Util::getWebSiteConfig('ZC_URL.'.Util::SCHOOL_NAME_IT.".".Util::MASTER,false);
            SendNotification::dispatch($host.$baseUrl,$request->all());
        }
        if(Util::getSchoolName() == Util::SCHOOL_NAME_SJ && Util::getCurrentBranch() == Util::DEV){
            //通知美术学院测试站
            $host = Util::getWebSiteConfig('ZC_URL.'.Util::SCHOOL_NAME_MS.".".Util::DEV,false);
            SendNotification::dispatch($host.$baseUrl,$request->all());
            //通知IT学院测试站
            $host = Util::getWebSiteConfig('ZC_URL.'.Util::SCHOOL_NAME_IT.".".Util::DEV,false);
            SendNotification::dispatch($host.$baseUrl,$request->all());
        }
    }
    /**
     * 创建新量时，自动将本学院的量，置为灰色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function created(Request $request){
        if($request->input("addtimes") > 1){
            \Illuminate\Support\Facades\Log::info("置灰操作：");
            //将本学院的该量，置为灰色
            RosterModel::where(app("status")->getRosterTypeColumn($request->input("roster_type")),$request->input("roster_no"))->update([
                'flag'  =>  0,
                'is_old'    => 1
            ]);
        }
        return Util::ajaxReturn(Util::SUCCESS,"通知成功");
    }
}