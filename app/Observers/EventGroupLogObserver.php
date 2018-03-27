<?php

namespace App\Observers;

use App\Exceptions\UserValidateException;
use App\Models\EventGroupLogModel;
use App\Models\RosterModel;
use Illuminate\Support\Facades\Log;

class EventGroupLogObserver{
    function saved(EventGroupLogModel $log){
        $roster = RosterModel::find($log->roster_id);
        $roster->group_status = $log->group_status;
        $roster->group_status_time = $log->addtime;
        if($roster->save() === false){
            Log::error("更新用户状态失败");
            throw new UserValidateException("更新用户状态失败");
        }
    }
}