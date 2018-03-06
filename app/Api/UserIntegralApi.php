<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/5 0005
 * Time: 14:39
 */

namespace App\Api;


class UserIntegralApi extends BaseApi {
    protected static $url = [
        'integralRecordDetails'       =>  '/api/extension/integralAccess', //2.获取用户积分记录列表
        'getTotalScore'               =>  '/api/extension/getTotalScore',   //1.获得用户总积分
        'operatingUserPoints'         =>  '/api/extension/operatingUserPoints',   //操作用户积分
        'getWay'                      =>    '/api/extension/integralAccess', //38.获取用户积分获得方式列表
        'getIntergalTask'             =>    '/api/extension/getIntegralTask', //44 获取用户积分获得任务列表
    ];

    /**
     * 1.获得用户总积分
     */
    public static function getTotalScoreByUserId($userId){
        $data['userId'] = $userId;
        $data = self::api(self::$url['getTotalScore'],$data);
        return $data;
    }
}