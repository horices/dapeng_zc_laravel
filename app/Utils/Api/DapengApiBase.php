<?php
namespace App\Utils\Api;

use App\Utils\Util;
use Curl\Curl;
use Illuminate\Support\Facades\Log;

class DapengApiBase extends BaseApi {
    protected static $page = 1;
    protected static $pagesize = 10;

    protected static function getApiKey()
    {
        return '8934031001776A04444F72154425DDBC';
    }

    /**
     * 获取API信息
     * @param unknown $url
     * @param unknown $data
     * @param string $method
     */
    public static function api($url,$data = [],$method = "post"){
        if(strpos($url, "http") !== 0){
            $url = Util::getDapengHost().$url;
        }
        $returnData = static :: sendCurl($url,$data,$method);
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
