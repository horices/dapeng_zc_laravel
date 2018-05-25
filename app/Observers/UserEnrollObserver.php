<?php

namespace App\Observers;

use App\Exceptions\UserValidateException;
use App\Models\GroupModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Log;

class UserEnrollObserver{
    function saved(UserModel $userModel){

    }
    function saving(UserModel $userModel){
    }
}