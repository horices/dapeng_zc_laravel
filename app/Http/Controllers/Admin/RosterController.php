<?php
namespace App\Http\Controllers\Admin;


use App\Models\EventGroupLogModel;
use App\Models\RosterModel;
use App\Models\UserModel;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Utils\Util;

class RosterController extends BaseController
{
    function getList(){
        //查询所有列表
        $query = RosterModel::query()->with(['group',"group_event_log"])->orderBy("id","desc");
        $list = $query->paginate(20);
        return view("admin.roster.list",[
            'list' => $list
        ]);
    }
}

