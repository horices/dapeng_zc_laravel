<?php

namespace App\Observers;

use App\Models\UserModel;
class UserObserver{
    function saved(UserModel $userModel){
        echo "收到 saved 事件";
        exit();
    }
    function saving(UserModel $userModel){
        print_R($userModel->dapeng_user_mobile);
        return false;
    }
}