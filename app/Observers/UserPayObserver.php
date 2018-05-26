<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/28 0028
 * Time: 15:54
 */

namespace App\Observers;


use App\Models\UserPayModel;
use App\Utils\Util;

class UserPayObserver{
    function creating(UserPayModel $userPayModel){
        $userInfo = Util::getUserInfo();
        $userPayModel->uid = $userInfo['uid'];
        $userPayModel->adviser_id = $userInfo['uid'];
        $userPayModel->adviser_name = $userInfo['name'];
        $userPayModel->adviser_qq = $userInfo['qq'];
    }
}