<?php

namespace App\Models;


class EventGroupLogModel extends BaseModel
{
    protected $table = "event_group_log";

    protected function getGroupStatusTextAttribute(){
        return app('status')->getGroupStatus($this->group_status);
    }
    protected function getAddtimeTextAttribute(){
        return date('m-d',$this->addtime)."<br />".date('H:i',$this->addtime);
    }
    protected function getAddtimeFullTextAttribute(){
        return date('Y-m-d H:i:s',$this->addtime);
    }
    protected function roster(){
        return $this->belongsTo(RosterModel::class,'roster_id','id');
    }
}
