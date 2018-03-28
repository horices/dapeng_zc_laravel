<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/28 0028
 * Time: 16:20
 */

namespace App\Observers;


use App\Models\UserPayLogModel;
use App\Utils\Util;

class UserPayLogObservers {
    function creating(UserPayLogModel $userPayLog){
        $userInfo = Util::getUserInfo();
        $userPayLog->uid = $userInfo['uid'];
        $userPayLog->adviser_id = $userInfo['uid'];
        $userPayLog->adviser_name = $userInfo['name'];
        $userPayLog->adviser_qq = $userInfo['qq'];
    }
}