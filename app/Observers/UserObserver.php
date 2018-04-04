<?php

namespace App\Observers;

use App\Exceptions\UserValidateException;
use App\Models\GroupModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Log;

class UserObserver{
    function saved(UserModel $userModel){

    }
    function saving(UserModel $userModel){
        if($userModel->status == 0){
            //关闭用户时，需要把该用户关联的群全部关闭
            if(GroupModel::where("leader_id",$userModel->uid)->update(['is_open'=>0]) === false){
                Log::error("暂停用时，关闭群状态失败");
                throw new UserValidateException("关闭用户关联群失败");
            }
        }
    }
}