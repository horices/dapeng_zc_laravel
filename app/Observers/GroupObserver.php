<?php

namespace App\Observers;

use App\Models\GroupModel;
class GroupObserver{
    function saved(GroupModel $groupModel){
    }

    /**
     * @param GroupModel $groupModel
     */
    function saving(GroupModel $groupModel){
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