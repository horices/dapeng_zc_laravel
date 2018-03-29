<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/5 0005
 * Time: 10:33
 */

namespace App\Api;


use App\Utils\DapengApiBase;
use App\Utils\Util;

class DapengUserApi extends BaseApi {
    protected static $url = [
        'register'                  => "/api/register",    //注册
        'login'                     => "/api/login",           //登陆
        'setSignIn'                 =>  '/api/extension/addAttendanceRecord', //用户签到
        'getInfo'               => "/api/user-message",    //获取用户信息
        'getUserList'               => "/api/users",           //获得用户列表
        'setUserInfo'               => "/api/secure/user/basic",    //修改用户信息
        'getSignDays'               =>  '/api/extension/signDays',        //获取用户签到天数
        'getStudyDays'              =>  '/api/extension/studyTtime',    //41.获取用户学习天数
        'savePassword'              =>  '/user/reset_pwd',      //用户修改密码
        'changePassword'            =>  '/api/user/change_pwd', //修改密码（不需要验证手机号码)
        'saveInfo'                  =>  '/api/secure/user/basic', //修改个人信息
        'getTiddingStatistics'      =>  '/api/extension/tidings_statistics',//获取用户信息统计
        'getJobCommentList'         =>  '/api/extension/jobCommentList', //获取指定作业的评论列表
        'getRaffle'                 =>  '/api/extension/sysAutoTakeRed',    //自动抽取 红包
        'getLottery'                =>  '/api/extension/drawTheLottery',    //自动抽奖
        'getSecurity'               =>  '/api/extension/userSecurity',   //获取用户帐号安全等级
        'updateStudyTime'           =>  "/api/extension/updateStudyTtime",   //接口编号：42-1 更新用户的最后学习时间
    ];
    /**
     * 获取用户信息
     * @param $data
     * @return mixed
     */
    public static function getInfo($data)
    {
        $data =self::api(self::$url['getInfo'], $data, "get");
        if($data['code'] == Util::FAIL){
            return $data;
        }
        if($data['data']){
            Util::getImgUrl($data['data']['user']['avatar']);
            if($data['data']['user']['gender'] == 'S'){
                $data['data']['user']['gender'] = "保密";
            }elseif( $data['data']['user']['gender'] == "M"){
                $data['data']['user']['gender'] = "男";
            }elseif( $data['data']['user']['gender'] == "F"){
                $data['data']['user']['gender'] = "女";
            }
            //$data['data']['user']['gender'] = "保密";
        }
        //获取用户身份 接口1
        $info = UserIntegralApi::getTotalScoreByUserId($data['data']['user']['userId']);
        $data['data']['user'] = collect([$data['data']['user'],$info['data']])->collapse();
        //$data= ArrayHelper::merge($data,$info);
        $typeArr = [
            'VIP'   =>  '/images/per_bg (12).png',
            'ZCXY'  =>  '/images/zhucexueyuan.png',
            'SXXY'  =>  '/images/shixuexueyuan.png',
        ];
        $typeGuestArr = [
            'VIP'   =>  '/images/stu.png',
            'ZCXY'  =>  '/images/zhu.png',
            'SXXY'  =>  '/images/shi.png',
        ];
        if(!isset($data['data']['user']['type'])){
            $data['data']['user']['type'] = 'ZCXY';
        }

        $data['data']['user']['identityImg'] = $typeArr[$data['data']['user']['type']];
        $data['data']['user']['identityGuestImg'] = $typeGuestArr[$data['data']['user']['type']];
        return $data;
    }
}