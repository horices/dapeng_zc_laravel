<?php

namespace App\Http\Controllers\Api;

use App\Models\UserHeadMasterModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class QQRobotController extends BaseController
{

    /**
     * 是否允许指定的用户登陆到 cleverQQ 插件
     * @param Request $request
     */
    function checkPermission(Request $request){
        Validator::make($request->all(),[
            'uid'   =>  "required"
        ],[
            'uid.required'  =>  "请输入用户ID"
        ])->validate();
        if(UserHeadMasterModel::whalere("robot_allow_login",1)->where("uid",$request->get("uid"))->count()> 0){
            return Util::ajaxReturn(Util::SUCCESS,"登陆成功");
        }
        return Util::ajaxReturn(Util::FAIL,"该用户不允许登陆使用");
    }
}
