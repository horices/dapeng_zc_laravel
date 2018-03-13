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
        //查询所有列表
        $query = RosterModel::query()->with(['group_info',"group_event_log"=>function($query){
            $query->select("roster_id","group_status",DB::raw("max(addtime) as addtime"))->where("group_status","=",2)->groupBy(["roster_id","group_status"])->orderBy("id","desc");
        }])->orderBy("id","desc");
        $field_k = Input::get("field_k");
        $field_v = Input::get("field_v");
        $type = Input::get("type");
        $isReg = Input::get("is_reg");
        $courseType = Input::get("course_type");
        $groupStatus = Input::get("group_status");
        $flag = Input::get("flag");
        $startDate = Input::get("startdate",date('Y-m-d '));
        $endDate = Input::get("enddate");
        $where = [];
        if($field_k && $field_v !== null){
            if($field_k == "account"){
                $query->where("qq","=",$field_v)->orWhere('wx','=',$field_v);
            }else{
                $where[$field_k] = $field_v;
            }
        }
        if($type !== null){
            $where['type'] = $type;
        }
        if($isReg !== null){
            $where['is_reg'] = $isReg;
        }
        if($courseType !== null){
            $where['course_type'] = $courseType;
        }
        if($groupStatus !== null){
            $where['group_status'] = $groupStatus;
        }
        if($flag !== null){
            $where['flag'] = $flag;
        }
        if($startDate !== null){
            $query->whereRaw("addtime >= ".strtotime($startDate));
        }
        if($endDate !== null){
            $query->whereRaw("addtime <= ".strtotime($endDate));
        }
        $query->where($where);
        $list = $query->paginate(20);
        return view("admin.roster.list",[
            'list' => $list
        ]);
    }
}

