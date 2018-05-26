<?php

namespace App\Observers;

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
        Validator::make($enroll->toArray(),[
            'name'  =>  "required",
            "mobile"    =>  ["required","unique:user_enroll,mobile,".$enroll->id],
            "is_guide"  =>  'required',
        ],[
            "name.required" =>  "姓名必须填写",
            "mobile.required"        =>  "请输入学员手机号",
            "mobile.unique" =>  "该手机号已经存在",
            "is_guide.required"     =>  "请选择是否导学"
        ])->validate();
    }


}