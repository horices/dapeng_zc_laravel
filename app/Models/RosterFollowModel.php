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

    /**
     * 获取私聊次数(每个记录算一次)
     */
    protected function getTimesAttribute(){
        if($this->roster_id !== null)
            $times = $this->where("roster_id",$this->roster_id)->count();
        return $times ?? 0;
    }

    /**
     * 量的详情
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function roster(){
        return $this->belongsTo(RosterModel::class);
    }

    /**
     * 创建者信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function creator(){
        return $this->belongsTo(UserModel::class,'create_id','uid')->withDefault();
    }

    /**
     * 获取私聊次数
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function followCount(){
        return $this->hasMany(self::class,'roster_id','roster_id');
    }
}
