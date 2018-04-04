<?php

namespace App\Observers;

use App\Exceptions\UserValidateException;
use App\Models\GroupModel;
use App\Models\UserModel;

class GroupObserver{
    function saved(GroupModel $groupModel){
    }

    /**
     * @param GroupModel $groupModel
     */
    function saving(GroupModel $groupModel){
        /**
         * 如果用户状态为已关闭，则不允许开启本群
         */
        $user = UserModel::find($groupModel->leader_id);
        if($user->status == 0){
            throw new UserValidateException("该用户已被暂停，请先开启该账号");
        }
        /**
         * 同一时间只允许同时存在一个群开启
         */
        if($groupModel->is_open == 1){
            $query = GroupModel::query();
            $query->where([
                'leader_id' => $groupModel->leader_id
            ])->update(['is_open'=>0]);
        }
    }
}