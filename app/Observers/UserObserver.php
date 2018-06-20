<?php

namespace App\Observers;

use App\Exceptions\UserValidateException;
use App\Models\GroupModel;
use App\Models\UserModel;
use App\Utils\Api\DapengUserApi;
use App\Utils\Util;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserObserver extends  BaseObserver {
    function saved(UserModel $userModel){

    }
    function saving(UserModel $userModel){

        //Validator::make($userModel->toArray(),[],[]);
        //如果用户的主站手机号被改变，则需要进行验证
        if($userModel->dapeng_user_mobile && $userModel->dapeng_user_mobile != $userModel->getOriginal("dapeng_user_mobile")){
            //判断当前主站账号是否已经绑定
            if($temp = $userModel->where("dapeng_user_mobile",$userModel->dapeng_user_mobile)->first()){
                throw new UserValidateException($temp->name." 已绑定该主站账号");
            }
            $return = DapengUserApi::getInfo(['type'=>'MOBILE','keyword'=>$userModel->dapeng_user_mobile]);
            if($return['code'] != Util::SUCCESS){
                throw new UserValidateException("获取主站用户信息失败");
            }
            $dapengUserInfo = $return['data'];
            if(!collect($dapengUserInfo['roleList'])->contains("consultant")){
                throw new UserValidateException("该用户没有课程顾问权限");
            }
            $userModel->dapeng_user_id = $dapengUserInfo['user']['userId'];
        }
        if($userModel->status == 0){
            //关闭用户时，需要把该用户关联的群全部关闭
            if(GroupModel::where("leader_id",$userModel->uid)->update(['is_open'=>0]) === false){
                Log::error("暂停用时，关闭群状态失败");
                throw new UserValidateException("关闭用户关联群失败");
            }
        }
    }

    //新增前
    function creating(UserModel $userModel){
    }

    //更新前
    function updating(UserModel $userModel){

    }
}