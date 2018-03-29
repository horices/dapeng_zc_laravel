<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/28 0028
 * Time: 15:55
 */

namespace App\Observers;


use App\Models\UserPayModel;
use App\Models\UserRegistrationModel;
use App\Utils\Util;

class UserRegistrationObservers{

    function creating(UserRegistrationModel $registrationModel){
        $userInfo = Util::getUserInfo();
        $registrationModel->uid = $userInfo['uid'];
        $registrationModel->adviser_id = $userInfo['uid'];
        $registrationModel->adviser_name = $userInfo['name'];
        $registrationModel->adviser_qq = $userInfo['qq'];
    }

    function created(UserRegistrationModel $registrationModel){
        $userPayArr = $registrationModel->toArray();
        $registrationArr['registration_id'] = $userPayArr['id'];
        $registrationArr['amount'] = $userPayArr['amount_submitted'];
        unset($userPayArr['id']);
        $userPayModel = UserPayModel::query();
        $userPayModel->create($userPayArr);
    }

//    function updating(UserRegistrationModel $registrationModel){
//
//    }

}