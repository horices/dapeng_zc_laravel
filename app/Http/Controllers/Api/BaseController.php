<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DapengApiException;
use App\Utils\Util;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
    protected static $apiKey = "8934031001776A04444F72154425DDBC";
    /*protected static $url;*/
    function __construct(){
        Log::info(Request()->input());
        $validate = $this->validateApi();
        if($validate === false){
            throw new DapengApiException("接口验证错误！");
        }
        Log::info("调用通知接口:".url()->full());
    }


    /**
     * 接口签名验证
     * @return bool
     */
    function validateApi(){
        $data = Request()->input();
        //return Util::ajaxReturn(1,$data);
//        $data = [
//            'type'      =>  'id',
//            'keyword'   =>  129,
//            'timestamp' =>  1494472570116,
//        ];
//        $validateData = self::getPostData($data);

        $sign       = isset($data['sign']) ? $data['sign'] : "";
        $timestamp       = isset($data['timestamp']) ? $data['timestamp'] : "";
        unset($data['sign']);
        $validateData = self::getPostData($data);
        if(empty($sign) || empty($timestamp)){
            return false;
        }
        //return Util::ajaxReturn(1,$validateData);
        if(($validateData['sign'] != $sign) || ($validateData['timestamp'] != $timestamp)){
            return false;
        }
        return true;
    }



    /**
     * 生成签名
     * @param string $data
     */
    private static function makeSign($data = null){
        ksort($data);
        $str = '';
        foreach($data as $k=>$v){
            if($v !== ''){
                $str .= $k.$v;
            }
        }
        return md5(self::$apiKey.$str.self::$apiKey);
    }

    /**
     * 生成 POST数据
     * @param string $data
     */
    private static function getPostData($data = null){
        //$data['timestamp'] = ceil(microtime(true)*1000);
        $data['sign'] = strtoupper(self::makeSign($data));
        return $data;
    }
}
