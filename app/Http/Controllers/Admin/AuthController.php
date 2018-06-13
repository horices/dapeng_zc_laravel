<?php 
namespace App\Http\Controllers\Admin;

use App\Exceptions\UserValidateException;
use App\Http\Requests\LoginForm;
use App\Http\Requests\RegRequest;
use App\Http\Requests\SendSMSRequest;
use App\Models\UserHeadMasterModel;
use App\Models\UserModel;
use App\Utils\Util;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

class AuthController extends  BaseController{
    /**
     * 登陆
     */
    function getLogin(){
        if($userInfo = Cookie::get("userInfo")){
            return redirect()->route("admin.index.index");
        }
        return view("admin.auth.login");
    }
    function postLogin(LoginForm $request){
        $user = UserModel::checkLogin($request->input("username"), $request->input("password"));
        $this->login($user,$request->get("remember_me"));
        //返回登陆成功的信息
        return response()->json(['code'=>Util::SUCCESS,"msg"=>"登陆成功","url"=>route("admin.index.index")]);
        //return response()->json(['code'=>Util::SUCCESS,"msg"=>"登陆成功"]);
    }

    /**
     * 注册
     */
    function postReg(RegRequest $request){
        throw new UserValidateException("此功能已关闭！");
        $request->merge([
            'grade' =>  12,
            'addtime'   => time(),
            'password'=>md5($request->get("password"))
        ]);
        $user = UserHeadMasterModel::create($request->all());
        return Util::ajaxReturn(Util::SUCCESS,"注册成功");
    }

    function postSendSms(SendSMSRequest $request){
        Util::sendSms($request->get("mobile"));
        return Util::ajaxReturn(Util::SUCCESS,"发送成功");
    }
    /**
     * 将指定的用户登入到系统中
     * @param array $userInfo
     * @param tinyint $rememberMe 是否需要存储
     */
    function login(UserModel $userInfo,$rememberMe = 0){
        if(!$userInfo || !$userInfo->uid){
            throw new AuthenticationException("您还不入登入系统中");
        }
        //记住用户名,30天
        if($rememberMe){
            //保存存用户对象时，会不能正常保存，所以需要转成数组，登陆时，fill 到对象中
            Cookie::queue("userInfo",$userInfo->toArray(),60*24*30);
        }
        session(['userToken'=>$userInfo->uid]);
        session(['userInfo'=>$userInfo]);
    }

    /**
     * 如果可以的话，进行自动登陆
     * @return bool|string
     */
    function autoLogin(){
        //检查COOKIE是否有值，有值则自动登陆
        $userInfo  = Cookie::get("userInfo");
        if($userInfo){
            //自动登陆
            $userInfo = app(UserModel::class)->fill($userInfo);
            app(AuthController::class)->login($userInfo);
            return $userInfo;
        }
        return false;
    }
    function getLogout(Request $request){
        if($request->session()->has("userInfo")){
            $request->session()->flush();
        }
        Cookie::queue(Cookie::forget("userInfo"));
        return response()->redirectToRoute("admin.index.index");
    }
}
?>