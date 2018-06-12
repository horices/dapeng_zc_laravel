<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/28 0028
 * Time: 15:55
 */

namespace App\Observers;


use App\Models\UserRegistrationModel;
use App\Utils\Util;
use Illuminate\Support\Facades\Validator;

class UserRegistrationObserver extends BaseObserver {

    function creating(UserRegistrationModel $registrationModel){
        $userInfo = Util::getUserInfo();
        $registrationModel->uid = $userInfo['uid'];
        $registrationModel->adviser_id = $userInfo['uid'];
        $registrationModel->adviser_name = $userInfo['name'];
        $registrationModel->adviser_qq = $userInfo['qq'];

    }
    function updating(UserRegistrationModel $registrationModel){
        //重新设置套餐总价格
        //$registrationModel->setPackageAll($registrationModel);
    }

    function saving(UserRegistrationModel $registration){
        Validator::make($registration->toArray(),[
            //'school_id'  =>  "required",
            //"enroll_id"    =>  "required",
        ],[
            //"school_id.required" =>  "未找到学院",
            //"enroll_id.required"    =>  "主报名信息错误",
        ])->validate();
    }
}