<?php

namespace App\Models;


class RosterFollowModel extends BaseModel
{
    protected $table = "user_roster_info";

    protected function getDeepLevelTextAttribute(){
        return app('status')->getRosterDeepLevel($this->deep_level);
    }
    protected function getIntentionTextAttribute(){
        return app('status')->getRosterIntention($this->intention);
    }
    protected function getCreateTimeTextAttribute(){
        return date('Y-m-d H:i',$this->create_time);
    }

    function roster(){
        return $this->belongsTo(RosterModel::class);
    }
    function creator(){
        return $this->belongsTo(UserModel::class,'create_id','uid');
    }
}
