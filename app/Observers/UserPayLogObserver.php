<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/28 0028
 * Time: 16:20
 */

namespace App\Observers;


use App\Models\UserPayLogModel;
use App\Models\UserPayModel;
use App\Models\UserRegistrationModel;
use App\Utils\Util;

class UserPayLogObserver {
    function creating(UserPayLogModel $userPayLog){
        //添加时，自动补全课程顾问信息
        $userInfo = Util::getUserInfo();
        $userPayLog->uid = $userInfo['uid'];
        $userPayLog->adviser_id = $userInfo['uid'];
        $userPayLog->adviser_name = $userInfo['name'];
        $userPayLog->adviser_qq = $userInfo['qq'];
    }

    function created(UserPayLogModel $userPayLog){
        //添加完成后，更新最后支付时间
        //$query = UserRegistrationModel::find($userPayLog->registration_id);
        //$query->last_pay_time = $userPayLog->create_time->timestamp;
        //$query->save();
    }

    function deleted(UserPayLogModel $userPayLog){

    }
}