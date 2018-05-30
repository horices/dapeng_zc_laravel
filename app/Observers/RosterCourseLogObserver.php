<?php

namespace App\Observers;

use App\Models\RosterCourseLogModel;
use App\Models\RosterCourseModel;
use App\Models\RosterModel;
use Illuminate\Support\Facades\Log;

class RosterCourseLogObserver{
    function saved(RosterCourseLogModel $rosterCourseLog){
        //添加新记录时，修改
        if($rosterCourseLog->action == 1){
            //开通课程
            $data = $rosterCourseLog->toArray();
            unset($data['id']);
            //判断当前课程该用户没有开通，则开通该课程
            if(!RosterCourseModel::where([
                'roster_id' =>  $rosterCourseLog->roster_id,
                'course_id' =>  $rosterCourseLog->course_id
            ])->count()){
                if(!RosterCourseModel::create($data)){
                    Log::error("更新用户课程失败",['roster_course_log'=>$rosterCourseLog]);
                }
            }

        }else{
            //关闭课程
            if(RosterCourseModel::where('qq',$rosterCourseLog->qq)->where("course_id",$rosterCourseLog->course_id)->delete() === false){
                Log::error("删除关联课程时失败");
            }
        }
        //查询当前量最后一次开通的课程
        $rosterCourse = RosterCourseModel::where("qq",$rosterCourseLog->qq)->orderBy("id","desc")->first();
        //查询当前量的最后一次开通的正式课
        $formalRosterCourse = RosterCourseModel::where("qq",$rosterCourseLog->qq)->where('course_type',2)->orderBy("id","desc")->first();
        if($formalRosterCourse){
            //如果用户开通过正式课，则以正式课为主
            $rosterCourse = $formalRosterCourse;
        }
        if(RosterModel::find($rosterCourseLog->roster_id)->fill([
            'course_id' => $rosterCourse->course_id ?? 0,
            'course_name' => $rosterCourse->course_name ?? '',
            'last_open_course_time' => $rosterCourse->addtime ?? 0,
            'course_type'=>$rosterCourse->course_type ??0
        ])->save() === false){
            Log::error("更新用户开课信息时，失败");
        }

    }
    function saving(RosterCourseLogModel $rosterCourseLog){
    }
}