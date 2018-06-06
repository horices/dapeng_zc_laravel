<?php

namespace App\Observers;

use App\Exceptions\UserValidateException;
use App\Models\UserEnrollModel;
use App\Utils\Util;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserEnrollObserver extends BaseObserver {
    function creating(UserEnrollModel $enroll){
        $userInfo = Util::getUserInfo();
        $enroll->adviser_id = $userInfo->uid;
        $enroll->adviser_name = $userInfo->name;
    }
    function saving(UserEnrollModel $enroll){
        if($enroll->qq && $enroll->wx){
            throw new UserValidateException("QQ号和微信号只能输入一项");
        }

    }


}