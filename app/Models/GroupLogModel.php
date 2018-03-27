<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupLogModel extends BaseModel
{
    protected $table = "event_group_log";

    protected function getGroupStatusTextAttribute(){
        return app('status')->getGroupStatus($this->group_status);
    }
    protected function getAddtimeTextAttribute(){
        return date("Y-m-d H:i:s",$this->addtime);
    }
}
