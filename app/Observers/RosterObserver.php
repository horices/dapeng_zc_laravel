<?php

namespace App\Observers;


use App\Models\RosterModel;
use App\Utils\Util;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RosterObserver extends  BaseObserver {
    function creating(RosterModel $roster){
        $roster->last_adviser_id = $roster->adviser_id;
        $roster->last_adviser_name = $roster->adviser_name;
    }
    function saving(RosterModel $rosterModel){
    }
    function saved(RosterModel $rosterModel){
        if(!$rosterModel->qq){
            //提交微信号时，自动生成相应的QQ号
            $rosterModel->qq = "wx".Str::lower(Util::getSchoolName()).$rosterModel->id;
            if($rosterModel->save() === false){
                Log::error("保存生成的QQ号失败");
            }
            return false;
        }
    }
}