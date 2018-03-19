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
        $user = UserModel::query();
        $searchType = Input::get("searchType");
        $keywords = Input::get("keywords");
        if($searchType && $keywords !== null){
            $user->where($searchType,$keywords);
        }
        $user->where('status',1)->seoer();
        if(Input::get('export') == 1){
            $uids = $user->get()->pluck('uid')->toArray();
            $temp = $this->getStatistics(["inviter_id"],function($query) use ($uids){
                $query->whereIn('inviter_id',$uids);
            });
            //查询所有的推广专员,并补全每个专员的统计信息
            $users = $user->select("uid","name")->get()->keyBy('uid')->transform(function($v,$k) use($temp){
                return collect($v)->merge($temp['user_statistics'][$k] ?? []);
            });
            return $this->exportStatisticsList($users);
        }
        $list = $user->paginate();
        $statistics = $this->getStatistics(["inviter_id"],function ($tquery) use ($list){
           $tquery->whereIn("inviter_id",$list->pluck('uid'));
        });
        return view("admin.roster.statistics.statistics",[
            'leftNav'   => "admin.roster.statistics.seoer_statistics",
            'list'  => $list,
            'statistics'    => $statistics['statistics'],
            'user_statistics'    => $statistics['user_statistics']
        ]);
    }
    /**
     * 获取课程顾问统计
     */
    function getAdviserStatistics(){
        $user = UserModel::query();
        $searchType = Input::get("searchType");
        $keywords = Input::get("keywords");
        if($searchType && $keywords !== null){
            $user->where($searchType,$keywords);
        }
        $user->where('status',1)->adviser();
        if(Input::get('export') == 1){
            $uids = $user->get()->pluck('uid')->toArray();
            $temp = $this->getStatistics(["last_adviser_id"],function($query) use ($uids){
                $query->whereIn('last_adviser_id',$uids);
            });
            //查询所有的推广专员,并补全每个专员的统计信息
            $users = $user->select("uid","name")->get()->keyBy('uid')->transform(function($v,$k) use($temp){
                return collect($v)->merge($temp['user_statistics'][$k] ?? []);
            });
            return $this->exportStatisticsList($users);
        }
        $list = $user->paginate();
        $statistics = $this->getStatistics(["inviter_id"],function ($tquery) use ($list){
            $tquery->whereIn("last_adviser_id",$list->pluck('uid'));
        });
        return view("admin.roster.statistics.statistics",[
            'leftNav'   => "admin.roster.statistics.adviser_statistics",
            'list'  => $list,
            'statistics'    => $statistics['statistics'],
            'user_statistics'    => $statistics['user_statistics']
        ]);
    }

    /**
     * 通用处理统计
     * @param array $column
     * @param \Closure|null $rosterWhere
     * @return mixed
     */
    protected function getStatistics(array $column,\Closure $rosterWhere = null){
        $roster = RosterModel::query();
        $user = UserModel::query();
        $searchType = Input::get("searchType");
        $keywords = Input::get("keywords");
        $rosterType = Input::get("roster_type");
        $startDate = Input::get("startdate");
        $endDate = Input::get("enddate");
        $field = collect($column)->merge(['is_reg','course_type','group_status'])->filter();
        if($searchType && $keywords !== null){
            $user->where($searchType,$keywords);
        }
        if($rosterType){
            $roster->where("type",$rosterType);
        }
        if($startDate){
            $roster->where('addtime','>=',strtotime($startDate));
        }
        if($endDate){
            $roster->where('addtime','<',strtotime($endDate));
        }
        $query = $roster->select($field->merge([DB::raw("count(*) as num")])->toArray())->groupBy($field->toArray());
        if($rosterWhere){
            $query->where($rosterWhere);
        }
        $result = $query->get();
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
        $result->groupBy($column)->each(function($collect,$user_id) use (&$statistics,&$user_statistics){
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
            $user_statistics[$user_id] = $temp;
        });
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
        $data['user_statistics'] = $user_statistics;
        $data['statistics'] = $statistics;
        return $data;
    }

    function exportStatisticsList(&$list){
        $data['filename'] = '效率统计';
        $data['title'] = [
            'name'  =>  '姓名',
            'user_total'    =>  '数据量',
            'user_total_join_group' =>  '进群量',
            'user_total_join_group_percent' =>  '进群比例',
            'user_total_group_num_3'    =>  '退群量',
            'user_total_quit_group_percent' =>  '退群比例',
            'user_total_group_num_5'    =>  '被踢量',
            'user_total_kick_group_percent' =>  '被踢比例',
            'user_total_reg_num_1'  =>  '注册量',
            'user_total_reg_percent'    =>  '注册比例',
            'user_total_course_num_1'   =>  '试学量',
            'user_total_trial_course_percent'   =>  '试学比例',
            'user_total_course_num_2'   =>  '正课量',
            'user_total_formal_course_percent'  =>  '正课比例'
        ];
        $data['data'] = $list;
        return $this->export($data);
    }
}
