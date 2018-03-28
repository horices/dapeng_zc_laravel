<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Requests\LoginForm;
use App\Http\Requests\SendSMSRequest;
use App\Models\UserHeadMasterModel;
use App\Models\UserModel;
use Illuminate\Auth\AuthenticationException;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Mews\Captcha\Facades\Captcha;

class AuthController extends  BaseController{
    /**
     * 登陆
     */
    function getLogin(){
        return view("admin.auth.login");
    }
    function postLogin(LoginForm $request){

        $user = UserModel::checkLogin($request->input("username"), $request->input("password"));
        $this->login($user);
        //返回登陆成功的信息
        return response()->json(['code'=>Util::SUCCESS,"msg"=>"登陆成功","url"=>url("/admin/index/index")]);
    }

    /**
     * 注册
     */
    function postReg(Request $request){
        $user = UserHeadMasterModel::create($request->all());
    }

    function postSendSms(SendSMSRequest $request){
        Util::sendSms($request->get("mobile"));
        return Util::ajaxReturn(Util::SUCCESS,"发送成功");
    }
    /**
     * 将指定的用户登入到系统中
     * @param array $userInfo
     */
    function login(UserModel $userInfo){
        if(!$userInfo || !$userInfo->uid){
            throw new AuthenticationException("您还不入登入系统中");
        }
        session(['userToken'=>$userInfo->uid]);
        session(['userInfo'=>$userInfo]);
    }

    function getLogout(Request $request){
        if($request->session()->has("userInfo")){
            $request->session()->flush();
        }
        return response()->redirectToRoute("admin.index.index");
    }
}
?>