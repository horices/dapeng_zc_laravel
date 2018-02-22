<?php

namespace App\Models;


use App\Http\Controllers\BaseController;

class RosterModel extends BaseModel
{
    protected $table = "user_roster";

    function getRosterNoAttribute(){
        return $this->type == 1 ? $this->qq: $this->wx;
    }
    function getIsRegTextAttribute(){
        return app("status")->getRegisterStatus()[$this->is_reg];
    }
    function getRosterTypeTextAttribute(){
        return app("status")->getRosterType()[$this->type];
    }
    function getCourseTypeTextAttribute(){
        return app("status")->getCourseType()[$this->course_type];
    }
    function getGroupStatusTextAttribute(){
        return app("status")->getGroupStatus()[$this->group_status];
    }
    function getAddtimeTextAttribute($v){
        return date('m-d',$this->addtime)."<br />".date('H:i',$this->addtime);
    }

    /**
     * 群信息
     */
    function group_info(){
        return $this->hasOne(GroupModel::class,'id','qq_group_id');
    }
    /**
     * 群日志
     */
    function group_event_log(){
        return $this->hasMany(EventGroupLogModel::class,'roster_id','id');
    }
    //最后一次群时间
    function last_group_time(){
        //return $this->hasOne(EventGroupLogModel::class,"roster_id")->orderBy("id","desc")->limit(1);
        return $this->belongsTo(EventGroupLogModel::class,'id','roster_id');//->orderBy("id","desc")->limit(1);
    }
}
