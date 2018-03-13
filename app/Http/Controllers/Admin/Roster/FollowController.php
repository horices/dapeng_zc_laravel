<?php

namespace App\Http\Controllers\Admin\Roster;

use App\Http\Controllers\Admin\BaseController;
use App\Models\RosterModel;
use App\Models\UserModel;

class FollowController extends BaseController
{

    function getList(){
        //查询所有用户
        $query = UserModel::adviser();
        $list = $query->paginate();
        return view("admin.roster.follow.list",[
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
