<?php

namespace App\Http\Controllers\Notify;

use App\Http\Controllers\BaseController as Controller;
use Curl\Curl;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class BaseController extends Controller
{
    function __construct(Curl $curl)
    {
        /**
         * 当前学院没设计学院时，需要对该通知进行转发
         */
        //记录通知
        //记录请求的所有数据
        Log::info("请求地址:".url()->full());
        Log::info("请求参数:");
        Log::info(Request::all());

    }
}
