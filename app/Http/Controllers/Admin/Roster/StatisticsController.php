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
            'leftNav'   => "admin.roster.statistics.seoer",
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
            'leftNav'   => "admin.roster.statistics.adviser",
            'list'  => $list,
            'statistics'    => $statistics['statistics'],
            'user_statistics'    => $statistics['user_statistics']
        ]);
    }
}
