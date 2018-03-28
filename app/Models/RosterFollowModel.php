<?php

namespace App\Models;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class RosterFollowModel extends BaseModel
{
    protected $table = "user_roster_info";

    protected function getDeepLevelTextAttribute(){
        if($this->deep_level !== null) {
            return app('status')->getRosterDeepLevel($this->deep_level);
        }
    }
    protected function getIntentionTextAttribute(){
        if($this->intention !== null) {
            return app('status')->getRosterIntention($this->intention);
        }
    }
    protected function getCreateTimeTextAttribute(){
        if($this->create_time !== null) {
            return date('Y-m-d H:i', $this->create_time);
        }
    }

    function roster(){
        return $this->belongsTo(RosterModel::class);
    }
    function creator(){
        return $this->belongsTo(UserModel::class,'create_id','uid');
    }
}
