<?php

namespace App\Http\Controllers\Admin\Roster;

use App\Http\Controllers\Admin\BaseController;
use App\Models\RosterFollowModel;
use App\Models\RosterModel;
use App\Models\UserModel;

class FollowController extends BaseController
{

    function getIndex(){
        //查询所有用户
        $query = UserModel::adviser();
        $list = $query->paginate();
        return view("admin.roster.follow.index",[
            'list'  =>  $list
        ]);
    }
    //获取单个课程顾问的关单记录
    function getList($userId){
        //获取单个用户的销售统计
        $query = RosterFollowModel::query();
        $query->with(["roster.group",'creator']);
        $query->where('adviser_id',$userId);
        $list = $query->paginate();
        return view("admin.roster.follow.list",[
            'leftNav'   =>  "admin.roster.follow.index",
            'list'  =>  $list
        ]);
    }
    function getAdd($roster_id){
        $roster = RosterModel::find($roster_id);
        return view("admin.roster.follow.add",[
            'leftNav'   => "admin.roster.list",
            'roster'    =>  $roster
        ]);
    }
}
