<?php
namespace App\Utils\Api;

use App\Utils\Util;
use Curl\Curl;
use Illuminate\Support\Facades\Log;

class DapengApiBase {
    private static $apiKey = '8934031001776A04444F72154425DDBC';
    protected static $page = 1;
    protected static $pagesize = 10;
    /**
     * 生成签名
     * @param string $data
     */
    private static function makeSign($data = null){
        ksort($data);
        $str = '';
        foreach($data as $k=>$v){
            if($v !== '' && $v !== NULL){
                $str .= $k.$v;
            }
        }
        return md5(self::$apiKey.$str.self::$apiKey);
    }
    /**
     * 生成 POST数据
     * @param string $data
     */
    public static function getPostData(array $data = null){
        Util::setDefault($data['timestamp'], intval(ceil(microtime(true)*1000)));
        $data['sign'] = strtoupper(self::makeSign($data));
        return $data;
    }
    /**
     * 获取API信息
     * @param unknown $url
     * @param unknown $data
     * @param string $method
     */
    public static function api($url,$data = [],$method = "post"){
//        $monolog = Log::getMonolog();
//        $monolog->popHandler();
//        Log::useDailyFiles('logData/error.log');
        if(strpos($url, "http") !== 0){
            $url = Util::getDapengHost().$url;
        }
        $Curl = new Curl();
        //设置连接超时为两秒
        $Curl->setOpt(CURLOPT_CONNECTTIMEOUT_MS, 2000);
        Log::info("\n\n==============================================================");
        Log::info("请求接口");
        Log::info("提交地址：".$url);
        Log::info("提交数据:");
        Log::info(self::getPostData($data));
        $returnData = $Curl->$method($url,self::getPostData($data))->response;
        Log::info("返回数据:".$returnData);
        Log::info("==============================================================\n\n");
        $result = json_decode($returnData,true);
        if($result['status'] == "success"){
            $result['code'] = Util::SUCCESS;
            $result['msg'] = $result['errorMsg'];
            $result['data'] = !is_array($result['result'])?[]:$result['result'];
        }elseif ($result['status'] == "warning"){
            $result['code'] = Util::WARNING;
            $result['msg'] = $result['result'];
            $result['data'] = !is_array($result['result'])?[]:$result['result'];
        }elseif($result['status'] == 'failure'){
            $result['code'] = Util::FAIL;
            if(is_array($result['result'])){
                $result['msg'] = $result['result']['errMsg'];
            }else{
                $result['msg'] = $result['result'];
            }
            $result['data'] = [];
        }
        if(isset($result['result'])){
//             $result['data']= $result['result'];
            unset($result['status']);
            unset($result['errorMsg']);
            unset($result['result']);
        }
        return $result;
    }
}
