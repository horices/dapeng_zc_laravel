<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/5 0005
 * Time: 14:39
 */

namespace App\Utils\Api;



use App\Utils\Util;
use Curl\Curl;
use Illuminate\Support\Facades\Log;

abstract class BaseApi {
    abstract protected static function getApiKey();
    //发送API方法
    abstract public static function api($url,$data = [],$method = "post");

    //发送API方法
    public static function sendCurl($url,$data = [],$method = "post"){
        $curl = app("curl");
        //设置连接超时为两秒
        $curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 2000);
        Log::info("\n\n==============================================================");
        Log::info("请求接口");
        Log::info("提交地址：".$url);
        Log::info("提交数据:");
        Log::info(static::getPostData($data));
        $returnData = $curl->$method($url,self::getPostData($data))->response;
        Log::info("返回数据:");
        Log::info($returnData);
        return $returnData;
    }
    /**
     * 生成签名
     * @param string $data
     */
    protected static function makeSign($data = null){
        ksort($data);
        $str = '';
        foreach($data as $k=>$v){
            if($v !== '' && $v !== NULL){
                $str .= $k.$v;
            }
        }
        return md5(static::getApiKey().$str.static::getApiKey());
    }
    /**
     * 生成 POST数据
     * @param string $data
     */
    public static function getPostData(array $data = null){
        Util::setDefault($data['timestamp'], strval(ceil(microtime(true)*1000)));
        $data['sign'] = strtoupper(static::makeSign($data));
        return $data;
    }
}