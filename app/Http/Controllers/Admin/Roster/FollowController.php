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
        //查询所有用户
        $query = UserModel::adviser()->with(['lastRosterFollowOne','rosterFollow'=>function($query){
            $startDate = Request::get("startdate");
            $endDate = Request::get("enddate");
            if($startDate){
                $query->where("create_time",">=",strtotime($startDate));
            }
            if($endDate){
                $query->where("create_time","<",strtotime($endDate));
            }
        }]);
        $list = $query->paginate();
        return view("admin.roster.follow.index",[
            'list'  =>  $list
        ]);
    }
    //获取单个课程顾问的关单记录
    function getList(){
        $userId = Request::get("user_id");
        //获取单个用户的销售统计
        $query = RosterFollowModel::query();
        $query->with(["roster.group",'creator']);
        $query->where('adviser_id',$userId);
        $list = $query->paginate();
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
    function getAdd($roster_id){
        $roster = RosterModel::find($roster_id);
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
