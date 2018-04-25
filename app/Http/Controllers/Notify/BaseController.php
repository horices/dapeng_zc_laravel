<?php

namespace App\Http\Controllers\Notify;

use App\Http\Controllers\BaseController as Controller;
use Curl\Curl;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class BaseController extends Controller
{
    function __construct(Curl $curl)
    {
        /**
         * 当前学院没设计学院时，需要对该通知进行转发
         */
        /*dd(route(Route::currentRouteName(),['a'=>1],false));
        dd(Request::fullUrl());
        dd($curl->post()->response);*/

    }

    /**
     * 生成签名
     */
    function makeSign(){
        dd(route('','',"http://www.baidu.com"));
    }

}
