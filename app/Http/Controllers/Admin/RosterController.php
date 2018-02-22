<?php
namespace App\Http\Controllers\Admin;


use App\Models\RosterModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class RosterController extends BaseController
{
    function getList(){
        $field_k = Input::get("field_k");
        $field_v = Input::get("field_v");
        //查询所有列表
        $query = RosterModel::query()->with(['group_info',"group_event_log"=>function($query){
            $query->select("roster_id","group_status",DB::raw("max(addtime) as addtime"))->where("group_status","=",2)->groupBy(["roster_id","group_status"])->orderBy("id","desc");
        }])->orderBy("id","desc")->whereIn("id",['2144421','2013210','2045020']);
        if($field_k == "account" && $field_v !== null){
            $query->where("qq","=",$field_v)->orWhere('wx','=',$field_v);
        }
        $list = $query->paginate(20);
        return view("admin.roster.list",[
            'list' => $list
        ]);
    }
}

