<?php

namespace App\Http\Controllers\Admin\Roster;

use App\Exceptions\UserValidateException;
use App\Http\Controllers\Admin\BaseController;
use App\Http\Requests\RosterFollowRequest;
use App\Models\RosterFollowModel;
use App\Models\RosterModel;
use App\Models\UserModel;
use App\Utils\Util;
use Faker\Provider\bn_BD\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class FollowController extends BaseController
{

    function getIndex(){
        $searchType = Request::get("search_type");
        $keywords = Request::get("keywords");
        //查询所有用户
        $query = UserModel::adviser()->status()->with(['lastRosterFollowOne'])->withCount(['rosterFollow'=>function($query){
            $startDate = Request::get("startdate");
            $endDate = Request::get("enddate");
            if($startDate){
                $query->where("create_time",">=",strtotime($startDate));
            }
            if($endDate){
                $query->where("create_time","<",strtotime($endDate));
            }
            $query->select(DB::raw(" count(DISTINCT roster_id,from_unixtime(create_time,'%Y%m%d'))"));
        }]);
        if($searchType && $keywords !== null){
            $query->where($searchType,$keywords);
        }
        $list = $query->paginate();
        return view("admin.roster.follow.index",[
            'list'  =>  $list
        ]);
    }
    //获取单个课程顾问的关单记录
    function getList(){
        $query = RosterFollowModel::query();
        $query->with(["roster.group",'creator']);
        $userId = Request::get("user_id");
        $rosterNo = Request::get("roster_no");
        $deepLevel = Request::get("deep_level");
        $intention = Request::get("intention");
        $startDate = Request::get("startdate");
        $endDate = Request::get("enddate");
        $order = Request::get("order");
        $direction = Request::get("direction");
        if($userId){
            $where['adviser_id'] = $userId;
        }
        if($deepLevel !== null){
            $where['deep_level'] = $deepLevel;
        }
        if ($intention){
            $where['intention'] = $intention;
        }
        if($rosterNo !== null){
            $where['qq'] = $rosterNo;
        }
        if($startDate !== null){
            $query->where("create_time",">=",strtotime($startDate));
        }
        if($endDate !== null){
            $query->where("create_time","<",strtotime($endDate));
        }
        if($order !== null && $direction !== null){
            if($direction){
                $direction = 'desc';
            }else{
                $direction = 'asc';
            }
            $query->orderBy($order,$direction);
        }
        $query->where($where);
        //获取单个用户的销售统计
        $list = $query->select(DB::raw('*,count(*) as count'))->groupBy("roster_id")->paginate();
        return view("admin.roster.follow.list",[
            'leftNav'   =>  Request::get("leftNav","admin.roster.follow.index"),
            'list'  =>  $list
        ]);
    }

    /**
     * 用户访问个人销售数据
     * @return mixed
     */
    function getUserList(){
        $userInfo = $this->getUserInfo();
        Request::merge(['user_id'=>$userInfo->uid,'leftNav'=>"admin.roster.follow.list.user"]);
        return Route::respondWithRoute("admin.roster.follow.list");
    }

    /**
     * 新增关单信息
     * @param $roster_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getAdd(){
        $rosterId = Request::get("roster_id");
        $roster = RosterModel::find($rosterId);
        return view("admin.roster.follow.add",[
            'leftNav'   => "admin.roster.list",
            'roster'    =>  $roster
        ]);
    }

    /**
     * 保存用户的关单信息
     * @param $roster_id
     */
    function postSave(RosterFollowRequest $request){
        $userInfo  = $this->getUserInfo();
        $followId = $request->get("follow_id");
        $roster = RosterModel::find($request->input("roster_id"));
        Request::merge(['create_id'=>$userInfo->uid,'create_time'=>time(),'qq'=>$roster->qq,'adviser_id'=>$roster->last_adviser_id,'adviser_name'=>$roster->last_adviser_name]);
        if(!$followId){
            $data = Request::all();
            $rosterFollow = RosterFollowModel::create($data);
            if(!$rosterFollow){
                throw new UserValidateException("添加用户关单失败");
            }
        }else{
            $rosterFollow = RosterFollowModel::find($followId);
            $rosterFollow->fill(Request::all());
            if($rosterFollow->save() === false){
                throw new UserValidateException("保存用户关单失败");
            }
        }
        return Util::ajaxReturn(Util::SUCCESS,"保存成功");
    }
}
