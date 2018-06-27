<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserValidateException;
use App\Http\Controllers\Controller;
use App\Models\UserModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class RosterController
 * @package App\Http\Controllers\Api
 */
class UserController extends BaseController
{
    /**
     * 获取用户信息
     * @return \App\Models\UserModel|void
     */
    function postLogin(Request $request){
        Validator::make($request->all(),[
            'mobile'    =>  'required|string|size:11',
            'password'  =>  'required'
        ],[
            'mobile.required'   =>  "请输入手机号",
            'mobile.size'       =>  "请输入11位的手机号",
            'password.required' =>  "请输入密码"
        ])->validate();

        $user = UserModel::where([
            'mobile'    => $request->get("mobile"),
            'password'  => md5($request->get("password"))
        ])->first();
        if(!$user){
            throw new UserValidateException("用户名密码错误");
        }
        return [
            'code' => Util::SUCCESS,
            'msg'   =>  "登陆成功",
            'data'  =>  $user
        ];
    }
}
