<?php

namespace App\Models;


class EventGroupLogModel extends BaseModel
{
    protected $table = "event_group_log";

    protected function getGroupStatusTextAttribute(){
        $status = app('status')->getGroupStatus();
        //如果当前为微信时，
        if($this->roster->type == 2){
            $status[1] = "等待添加";
            $status[2] = "已添加";
        }
        if($this->group_status !== null){
            return $status[$this->group_status];
        }
    }
    protected function getAddtimeTextAttribute(){
        if($this->addtime !== null) {
            return date('m-d', $this->addtime) . "<br />" . date('H:i', $this->addtime);
        }
    }
    protected function getAddtimeFullTextAttribute(){
        if($this->addtime !== null) {
            return date('Y-m-d H:i:s', $this->addtime);
        }
    }
    protected function roster(){
        return $this->belongsTo(RosterModel::class,'roster_id','id');
    }
}
