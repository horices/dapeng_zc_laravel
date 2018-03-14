<?php

namespace App\Http\Controllers\Admin\Roster;

use App\Http\Controllers\Admin\BaseController;
use App\Models\RosterModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class StatisticsController extends BaseController
{
    /**
     * 获取推广专员统计
     */
    function getSeoerStatistics(){
        $roster = RosterModel::query();
        $user = UserModel::query();
        $searchType = Input::get("searchType");
        $keywords = Input::get("keywords");
        $rosterType = Input::get("roster_type");
        $startDate = Input::get("startDate",date('Y-m-d'));
        $endDate = Input::get("endDate");
        if($searchType && $keywords !== null){
            $user->where($searchType,$keywords);
        }
        $list = $user->where('status',1)->seoer()->paginate();
        if($rosterType){
            $roster->where("type",$rosterType);
        }
        if($startDate){
            $roster->where('addtime','>=',strtotime($startDate));
        }
        if($endDate){
            $roster->where('addtime','<',strtotime($endDate));
        }
        $result = $roster->select([
            'inviter_id','last_adviser_id','is_reg','course_type','group_status',DB::raw("count(*) as num")
        ])->groupBy(['inviter_id','is_reg','course_type','group_status'])
            //->where('addtime','>',1513339653)
            ->whereIn('inviter_id',explode(',',collect($list->toArray()['data'])->implode('uid',',')))->get();
        $statistics = $user_statistics = [];
        $statistics['user_total'] = 0; //所有数据总量
        $statistics['user_total_reg_num_0'] = 0;   //未注册总人数
        $statistics['user_total_reg_num_1'] = 0;   //已注册总人数
        $statistics['user_total_group_num_0'] = 0; //未申请总人数
        $statistics['user_total_group_num_1'] = 0; //等待通过总人数
        $statistics['user_total_group_num_2'] = 0; //已经进群总人数
        $statistics['user_total_group_num_3'] = 0; //已经退群总人数
        $statistics['user_total_group_num_4'] = 0; //已拒绝总人数
        $statistics['user_total_group_num_5'] = 0; //已被踢总人数
        $statistics['user_total_course_num_0'] = 0;    //未开通课程总人数
        $statistics['user_total_course_num_1'] = 0;    //开通试学课总人数
        $statistics['user_total_course_num_2'] = 0;    //开通正式课总人数
        $result->groupBy("inviter_id")->each(function($collect,$seoer_id) use (&$statistics,&$user_statistics){
            $temp['user_total'] = 0; //所有数据总量
            $temp['user_total_reg_num_0'] = 0;   //未注册总人数
            $temp['user_total_reg_num_1'] = 0;   //已注册总人数
            $temp['user_total_group_num_0'] = 0; //未申请总人数
            $temp['user_total_group_num_1'] = 0; //等待通过总人数
            $temp['user_total_group_num_2'] = 0; //已经进群总人数
            $temp['user_total_group_num_3'] = 0; //已经退群总人数
            $temp['user_total_group_num_4'] = 0; //已拒绝总人数
            $temp['user_total_group_num_5'] = 0; //已被踢总人数
            $temp['user_total_course_num_0'] = 0;    //未开通课程总人数
            $temp['user_total_course_num_1'] = 0;    //开通试学课总人数
            $temp['user_total_course_num_2'] = 0;    //开通正式课总人数
            //提交总人数

            $collect->each(function ($user) use (&$statistics,&$temp){
                $temp['user_total'] += $user->num ;
                $statistics['user_total'] += $user->num;
                //注册量
                $temp['user_total_reg_num_'.$user->is_reg]+=$user->num;
                $statistics['user_total_reg_num_'.$user->is_reg] += $user->num;
                //进群量
                $temp['user_total_group_num_'.$user->group_status] += $user->num;
                $statistics['user_total_group_num_'.$user->group_status] += $user->num;
                //开课量
                $temp['user_total_course_num_'.$user->course_type] += $user->num;
                $statistics['user_total_course_num_'.$user->course_type] += $user->num;
            });
            //进群量的在群量+退群量+被踢量
            $temp['user_total_join_group'] = $temp['user_total_group_num_2'] + $temp['user_total_group_num_3'] +$temp['user_total_group_num_5'];
            //进群比例
            if($temp['user_total'])
                $temp['user_total_join_group_percent'] = (round($temp['user_total_join_group']/$temp['user_total'],4)*100)."%";
            if($temp['user_total_join_group']){
                //退群比例
                $temp['user_total_quit_group_percent'] = (round($temp['user_total_group_num_3']/$temp['user_total_join_group'],4)*100)."%";
                //被踢比例
                $temp['user_total_kick_group_percent'] = (round($temp['user_total_group_num_5']/$temp['user_total_join_group'],4)*100)."%";
                //注册比例
                $temp['user_total_reg_percent'] = (round($temp['user_total_reg_num_1']/$temp['user_total_join_group'],4)*100)."%";
                //试学比例
                $temp['user_total_trial_course_percent'] = (round($temp['user_total_course_num_1']/$temp['user_total_join_group'],4)*100)."%";
                //正课比例
                $temp['user_total_formal_course_percent'] = (round($temp['user_total_course_num_2']/$temp['user_total_join_group'],4)*100)."%";
            }
            $user_statistics[$seoer_id] = $temp;
        });
        //进群量的在群量+退群量+被踢量
        //进群量的在群量+退群量+被踢量
        $statistics['user_total_join_group'] = $statistics['user_total_group_num_2'] + $statistics['user_total_group_num_3'] +$statistics['user_total_group_num_5'];
        //进群比例
        if($statistics['user_total'])
            $statistics['user_total_join_group_percent'] = (round($statistics['user_total_join_group']/$statistics['user_total'],4)*100)."%";
        if($statistics['user_total_join_group']){
            //退群比例
            $statistics['user_total_quit_group_percent'] = (round($statistics['user_total_group_num_3']/$statistics['user_total_join_group'],4)*100)."%";
            //被踢比例
            $statistics['user_total_kick_group_percent'] = (round($statistics['user_total_group_num_5']/$statistics['user_total_join_group'],4)*100)."%";
            //注册比例
            $statistics['user_total_reg_percent'] = (round($statistics['user_total_reg_num_1']/$statistics['user_total_join_group'],4)*100)."%";
            //试学比例
            $statistics['user_total_trial_course_percent'] = (round($statistics['user_total_course_num_1']/$statistics['user_total_join_group'],4)*100)."%";
            //正课比例
            $statistics['user_total_formal_course_percent'] = (round($statistics['user_total_course_num_2']/$statistics['user_total_join_group'],4)*100)."%";
        }
        return view("admin.roster.statistics.statistics",[
            'leftNav'   => "admin.roster.statistics.seoer_statistics",
            'list'  => $list,
            'statistics'    => $statistics,
            'user_statistics'    => $user_statistics
        ]);
    }
}
