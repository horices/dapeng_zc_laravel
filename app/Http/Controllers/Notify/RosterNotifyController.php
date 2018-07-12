<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 15:12
 */

namespace App\Http\Controllers\Notify;


use App\Exceptions\UserNotifyException;
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
    }
    /**
     * 创建新量时，自动将本学量的量，置为灰色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function created(Request $request){
        if($request->input("addtimes") > 1){
            //将本学院的该量，置为灰色
            RosterModel::where(app("status")->getRosterTypeColumn($request->input("roster_type")),$request->input("roster_no"))->update([
                'flag'  =>  0,
                'is_old'    => 1
            ]);
        }
        return Util::ajaxReturn(Util::SUCCESS,"通知成功");
    }
}