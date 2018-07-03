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
use Illuminate\Support\Facades\Validator;
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
        //当前学院为设计学院，且要查询的学院不仅有设计学院
        if(Util::getSchoolName() == Util::SCHOOL_NAME_SJ && $request->get("schoolId") != Util::SCHOOL_NAME_SJ){
            //$this->forward();
        }
    }

    /**
     * 对请求数据进行转发
     * @param string $url
     * @param array $data
     * @param string $method
     * @throws UserValidateException
     */
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
        $curl = app("curl");
        Log::info("开始转发请求:".$url);
        Log::info("请求数据:");
        Log::info($data);
        $response = $curl->$method($url,$data)->response;
        Log::info("返回数据:".$response);
        $curlData = Util::jsonDecode($response);
        if(!$curlData || $curlData['code'] == Util::FAIL){
            throw new UserValidateException($curlData['msg']);
        }
        $this->forwardData = $curlData['data'];
        return $curlData;
    }

    /**
     * 获得转发的结果
     * @return array
     */
    function getForwardData(){
        return $this->forwardData;
    }


    /**
     * 接口签名验证
     * @return bool
     */
    function validateApi(){
        $sign = request()->get("sign");
        $data = request()->except('sign');
        Validator::make(request()->all(),[
            'timestamp' =>  'required',
            'sign'      =>  'required'
        ],[
            'timestamp.required'    =>  "缺少必要参数",
            'sign.required' =>  "缺少必要参数"
        ])->validate();
        $validateData = $this->getPostData($data);
        if(($validateData['sign'] != $sign)){
            Log::error("签名不一致:".$validateData['sign']."==".$sign);
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
