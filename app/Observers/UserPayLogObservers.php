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

class UserPayLogObservers {
    function creating(UserPayLogModel $userPayLog){
        $userInfo = Util::getUserInfo();
        $userPayLog->uid = $userInfo['uid'];
        $userPayLog->adviser_id = $userInfo['uid'];
        $userPayLog->adviser_name = $userInfo['name'];
        $userPayLog->adviser_qq = $userInfo['qq'];
    }

    function deleted(UserPayLogModel $userPayLog){
        $logPayCount = UserPayLogModel::where("pay_id",$userPayLog->pay_id)->count();
        //如果二级支付记录为空，则删除一级支付记录
        if($logPayCount == 0){
            UserPayModel::where("id",$userPayLog->pay_id)->delete();
            UserRegistrationModel::where("id",$userPayLog->registration_id)->delete();
        }else{
            UserPayModel::where("id",$userPayLog->pay_id)->decrement("amount",$userPayLog->amount);
            UserRegistrationModel::where("id",$userPayLog->registration_id)->decrement("amount_submitted",$userPayLog->amount);
        }
    }
}