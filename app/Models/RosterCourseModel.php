<?php

namespace App\Models;

class RosterCourseModel extends BaseModel
{
    protected $table = "roster_course";

    protected function getCourseTypeTextAttribute(){
        return app('status')->getCourseType($this->course_type);
    }
    protected function getAddtimeTextAttribute(){
        return date('Y-m-d H:i:s',$this->addtime);
    }
}
