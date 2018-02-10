<?php

namespace App\Models;


class EventGroupLogModel extends BaseModel
{
    protected $table = "event_group_log";

    protected function getAddtimeTextAttribute(){
        return date('Y-m-d H:i:s',$this->addtime);
    }

    protected function roster(){
        return $this->belongsTo(RosterModel::class,'roster_id','id');
    }
}
