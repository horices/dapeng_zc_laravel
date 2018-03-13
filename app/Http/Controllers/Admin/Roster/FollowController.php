<?php

namespace App\Http\Controllers\Admin\Roster;

use App\Http\Controllers\Admin\BaseController;
use App\Models\RosterModel;

class FollowController extends BaseController
{

    function getAdd($roster_id){

        $roster = RosterModel::find($roster_id);
        return view("admin.roster.follow.add",[
            'leftNav'   => "admin.roster.list",
            'roster'    =>  $roster
        ]);
    }
}
