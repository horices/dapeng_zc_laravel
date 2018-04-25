<?php

namespace App\Http\Controllers\Admin\Roster;

use App\Http\Controllers\Admin\BaseController;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;

class StatisticsController extends BaseController
{
    /**
     * 获取推广专员统计
     */
    function getSeoerStatistics(){
        $user = UserModel::query();
        $searchType = Input::get("searchType");
        $keywords = Input::get("keywords");
        $seoerGrade = Input::get("seoer_grade");
        if($searchType && $keywords !== null){
            $user->where($searchType,$keywords);
        }
        $user->where('status',1);

        if($seoerGrade){
            $user->where('grade',$seoerGrade);
        }else{
            $user->seoer();
        }
        $uids = $user->get()->pluck('uid')->toArray();
        if(Input::get('export') == 1){
            $temp = $this->getStatistics(["inviter_id"],function($query) use ($uids,$seoerGrade){
                $query->whereIn('inviter_id',$uids);
            });
            //查询所有的推广专员,并补全每个专员的统计信息
            $users = $user->select("uid","name")->get()->keyBy('uid')->transform(function($v,$k) use($temp){
                return collect($v)->merge($temp['user_statistics'][$k] ?? []);
            });
            return $this->exportStatisticsList($users);
        }

        $list = $user->paginate();
        $statistics = $this->getStatistics(["inviter_id"],function ($tquery) use ($uids){
            $tquery->whereIn('inviter_id',$uids);
        });
        return view("admin.roster.statistics.statistics",[
            'leftNav'   => "admin.roster.statistics.seoer",
            'list'  => $list,
            'user_id_str'  =>  'seoer_id',
            'statistics'    => $statistics['statistics'],
            'user_statistics'    => $statistics['user_statistics'],
            'url_data'  =>  ['leftNav'=>"admin.roster.statistics.seoer"]
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
                //$query->whereIn('last_adviser_id',$uids);
            });
            //查询所有的推广专员,并补全每个专员的统计信息
            $users = $user->select("uid","name")->get()->keyBy('uid')->transform(function($v,$k) use($temp){
                return collect($v)->merge($temp['user_statistics'][$k] ?? []);
            });
            return $this->exportStatisticsList($users);
        }
        $list = $user->paginate();
        $statistics = $this->getStatistics(["last_adviser_id"],function ($tquery) use ($list){
            //$tquery->whereIn("last_adviser_id",$list->pluck('uid'));
        });
        return view("admin.roster.statistics.statistics",[
            'leftNav'   => "admin.roster.statistics.adviser",
            'list'  => $list,
            'user_id_str'   => "adviser_id",
            'statistics'    => $statistics['statistics'],
            'user_statistics'    => $statistics['user_statistics'],
            'url_data'  =>  ['leftNav'=>"admin.roster.statistics.adviser"]
        ]);
    }

    function getIntelligentStatistics(Request $request){
        $request->merge(['seoer_grade'=>11]);
        return Route::respondWithRoute("admin.roster.statistics.seoer");
    }

}
