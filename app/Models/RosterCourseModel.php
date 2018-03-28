<?php

namespace App\Models;

class RosterCourseModel extends BaseModel
{
    protected $table = "roster_course";

    protected function getCourseTypeTextAttribute(){
        if($this->course_type !== null) {
            return app('status')->getCourseType($this->course_type);
        }
    }
    protected function getAddtimeTextAttribute(){
        if($this->addtime !== null) {
            return date('Y-m-d H:i:s', $this->addtime);
        }
    }
}
