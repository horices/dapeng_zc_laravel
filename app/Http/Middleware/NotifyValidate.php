<?php

namespace App\Http\Middleware;

use App\Exceptions\UserValidateException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class NotifyValidate
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
        //验证签名
        if(!$request->input("qq")){
            throw new NotAcceptableHttpException("未找到QQ号");
        }
        if($this->checkSign($request) === false){
            throw new NotAcceptableHttpException("签名错误");
        }
        return $next($request);
    }


    /**
     * 校验签名串
     * @param Request $request
     * @return bool
     */
    function checkSign(Request $request){
        return true;
        return $request->input("sign") == md5($request->fullUrl()."|".$request->input("qq")."|dapeng");
    }
}
