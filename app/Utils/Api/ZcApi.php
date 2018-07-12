<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/5 0005
 * Time: 14:39
 */

namespace App\Utils\Api;



use App\Exceptions\UserValidateException;
use App\Utils\Util;
use Illuminate\Support\Facades\Log;

class ZcApi extends BaseApi {

    protected static $url = [
        'checkRosterStatus' =>  "/Api/User/checkRosterStatus",
    ];
    protected static function getApiKey()
    {
        return "8934031001776A04444F72154425DDBC";
    }

    /**
     * @param string $schoolName 学院名称
     * @param array $data 需要验证的量的信息
     *      keys:  roster_type:[1:qq,2:微信]
     *             roster_no: 量的号码
     */
    public static function validateRoster($schoolName,array $data){
        //获取学院的地址
        $url = Util::getWebSiteConfig("ZC_URL.".$schoolName).static::$url['checkRosterStatus'];
        $data = static :: api($url,$data,"post");
        return $data;
    }
    public static function api($url, $data = [], $method = "post")
    {
        $origin = static :: sendCurl($url,$data,$method);
        $curlData = Util::jsonDecode($origin,true);
        if(!$curlData){
            Log::error("接口返回数据：".$origin);
            throw new UserException("接口出现未知错误");
        }
        if($curlData['code'] == Util::FAIL){
            throw new UserValidateException($curlData['msg']);
        }
        return $curlData;
    }


}