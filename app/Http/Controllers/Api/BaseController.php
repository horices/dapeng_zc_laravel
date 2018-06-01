<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DapengApiException;
use App\Exceptions\UserValidateException;
use App\Utils\Util;
use Curl\Curl;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BaseController extends Controller
{
    protected $apiKey = "8934031001776A04444F72154425DDBC";
    protected $forwardData = [];
    /*protected static $url;*/
    function __construct(Request $request){
        Log::info("请求接口:".url()->full());
        Log::info("请求数据:");
        Log::info($request->input());
        $validate = $this->validateApi();
        if($validate === false){
            throw new DapengApiException("接口验证错误！");
        }
    }

    function forward($url='',$data=[],$method=''){
        if(!$url){
            //获取美术学院数据
            $baseUrl = URL::route(Route::currentRouteName(),[],false);
            $host = Util::getWebSiteConfig('ZC_URL.'.Util::SCHOOL_NAME_MS.".".Util::getCurrentBranch(),false);
            $url = $host.$baseUrl;
        }
        if(!$data){
            request()->merge($this->getPostData(request()->except('sign')));
            $data = request()->all();
        }
        $method = $method ? $method : Str::lower(request()->getMethod());
        $curl = app(Curl::class);
        $response = $curl->$method($url,$data)->response;
        $curlData = Util::jsonDecode($response);
        if(!$curlData || $curlData['code'] == Util::FAIL){
            throw new UserValidateException("接口转发返回失败".$response);
        }
        $this->forwardData = $curlData['data'];
    }

    function getForwardData(){
        return $this->forwardData;
    }


    /**
     * 接口签名验证
     * @return bool
     */
    function validateApi(){
        $data = request()->input();
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
        $validateData = $this->getPostData($data);
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
    protected function makeSign($data = null){
        ksort($data);
        $str = '';
        foreach($data as $k=>$v){
            if($v !== ''){
                $str .= $k.$v;
            }
        }
        return md5($this->apiKey.$str.$this->apiKey);
    }

    /**
     * 生成 POST数据
     * @param string $data
     */
    protected function getPostData($data = null){
        //$data['timestamp'] = ceil(microtime(true)*1000);
        $data['sign'] = strtoupper($this->makeSign($data));
        return $data;
    }
}
