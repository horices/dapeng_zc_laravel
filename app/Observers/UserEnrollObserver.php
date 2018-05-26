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
        $validator = Validator::make($enroll->toArray(),[
            'name'  =>  "required",
            "mobile"    =>  ["required","unique:user_enroll,mobile,".$enroll->id],
            "is_guide"  =>  'required',
        ],[
            "name.required" =>  "姓名必须填写",
            "mobile.required"        =>  "请输入学员手机号",
            "qq.required"   =>  "QQ号和微信号至少填一项",
            "qq.regex"   =>  "请输入正确的QQ号",
            "wx.required"   =>  "QQ号和微信号至少填一项",
            "wx.regex"   =>  "请输入正确的微信号",
            "mobile.unique" =>  "该手机号已经存在",
            "is_guide.required"     =>  "请选择是否导学"
        ]);
        $validator->sometimes("qq",["required","regex:/^\d{5,10}$/"],function($input){
            return $input->wx == '';
        });
        $validator->sometimes("wx",["required","regex:/^\d{5,10}$/"],function($input){
            return $input->qq == '';
        });
        $validator->validate();
    }


}