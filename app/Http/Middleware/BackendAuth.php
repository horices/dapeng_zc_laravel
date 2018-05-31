<?php

namespace App\Http\Middleware;

use App\Exceptions\UserValidateException;
use App\Http\Controllers\Admin\AuthController;
use App\Models\UserModel;
use Closure;
use Illuminate\Support\Facades\View;

class BackendAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->session()->get("userToken")){
            if(!app(AuthController::class)->autoLogin()){
                return redirect(route("admin.auth.login"));
            }
        }
        $userInfo = app('status')->getUserInfo();
        if(app('status')->checkUserPermission(collect($request->route()->getAction())->get('as',''),$userInfo->grade) === false){
            throw new UserValidateException("您没有权限访问此页面");
        }
        //修改模板左侧菜单
        $leftNavList = app('status')->getLeftNavList();
        //只展示用户当前等级的导航
        $leftNavList = collect($leftNavList)->filter(function($v) use($userInfo){
            $grade = collect($v['grade'] ?? []);
            return $grade->contains($userInfo['grade']) || $grade->contains('*');
            //return in_array($userInfo['grade'],$v['grade'] ?? []) || in_array('*',$v['grade'] ?? []);
        })->toArray();
        View::share('navList',$leftNavList);
        return $next($request);
    }
}
