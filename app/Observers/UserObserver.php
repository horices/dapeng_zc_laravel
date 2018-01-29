<?php

namespace App\Observers;

use App\Models\UserModel;
class UserObserver{
    function saved(UserModel $userModel){
    }
    function saving(UserModel $userModel){
    }
}