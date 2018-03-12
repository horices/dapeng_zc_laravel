<?php
namespace App\Http\Controllers\Admin;


use App\Http\Requests\RosterAdd;
use App\Models\GroupModel;
use App\Models\RosterModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RosterController extends BaseController
{
    function getAdd(){
        return view("admin.roster.add");
    }

    /**
     * 添加一个新量
     * @param Request $request
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAdd(Request $request,array $data = []){
        if(!$data){
            $data = $request->all();
        }
        if(RosterModel::addRoster($data)){
            $returnData['code'] = Util::SUCCESS;
            $returnData['msg'] = "添加成功";
        }else{
            $returnData['code'] = Util::FAIL;
            $returnData['msg'] = "添加失败";
        }
        return response()->json($returnData);
    }
    function getList(){
        $field_k = Input::get("field_k");
        $field_v = Input::get("field_v");
        //查询所有列表
        $query = RosterModel::query()->with(['group_info',"group_event_log"=>function($query){
            $query->select("roster_id","group_status",DB::raw("max(addtime) as addtime"))->where("group_status","=",2)->groupBy(["roster_id","group_status"])->orderBy("id","desc");
        }])->orderBy("id","desc");
        if($field_k == "account" && $field_v !== null){
            $query->where("qq","=",$field_v)->orWhere('wx','=',$field_v);
        }
        $list = $query->paginate(20);
        return view("admin.roster.list",[
            'list' => $list
        ]);
    }
}

