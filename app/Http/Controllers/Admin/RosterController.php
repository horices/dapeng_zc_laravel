<?php
namespace App\Http\Controllers\Admin;


use App\Http\Requests\RosterAdd;
use App\Models\GroupModel;
use App\Models\RosterModel;
use App\Models\UserModel;
use App\Utils\Util;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

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
        $query = RosterModel::query()->with(['group',"group_event_log"=>function($query){
            $query->select("roster_id","group_status",DB::raw("max(addtime) as addtime"))->where("group_status","=",2)->groupBy(["roster_id","group_status"])->orderBy("id","desc");
        }])->orderBy("id","desc");
        $field_k = Input::get("field_k");
        $field_v = Input::get("field_v");
        $type = Input::get("type");
        $isReg = Input::get("is_reg");
        $courseType = Input::get("course_type");
        $groupStatus = Input::get("group_status");
        $flag = Input::get("flag");
        $startDate = Input::get("startdate");
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
        if(Input::get('export') == 1){
            return $this->exportRosterList($query);
        }
        $list = $query->paginate();
        return view("admin.roster.list",[
            'list' => $list
        ]);
    }

    function exportRosterList($query){
        //对数据进行导出，不进行展现
        $data['filename'] = "所有数据导出".date('YmdHis');
        $data['title'] = [
            'roster_no'    =>  '号码',
            'group.group_name'    =>  '班级代号',
            'group.qq_group'    =>  '群号',
            'roster_type_text'    =>  '类型',
            'inviter_name'    => '推广专员',
            'last_adviser_name' =>  '课程顾问',
            'addtime_export_text'   =>  '提交时间',
            'is_reg_text'   =>  "是否注册",
            "course_type_text"  =>  "课程类型",
            "group_status_text" =>  "进群状态",
        ];
        $data['data'] = $query->take(5000)->get();
        return $this->export($data);
    }
}

