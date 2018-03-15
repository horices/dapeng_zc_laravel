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

    /**
     * 导出数据，到指定的文件
     * @param array $data
     *      filename :  导出的文件名
     *      title :     字段名,支持点 多级选择
     *      data    : 数据
     */
    function export(array &$data,$exportType = 'xls'){
        //导出最长为五分钟
        set_time_limit(60*2);
        ini_set("memory_limit","100M");
        //将数组全部转化为 Collect
        $data = collect($data);
        $data->transform(function($v,$k){
            if(is_array($v)){
                $v = collect($v);
            }
            return $v;
        });
        //重新整理数组,取出多级数据
        $data->get("data")->transform(function($v) use ($data){
            //判断字段中是否存在点的语法,进行多级获取
            $data->get("title")->keys()->each(function($column) use (&$v){
                if(strpos($column,'.') !== false){
                    collect(explode('.',$column))->each(function($key) use (&$v,$column){
                        if($v instanceof Model){
                            if(!$v->$column) $v->$column = $v;
                            $v->$column = $v->$column->$key;
                        }elseif($v instanceof Collection){
                            $v->put($column,$v->get($key));
                        }else{
                            $v[$column] = $v[$key];
                        }
                    });
                }
            });
            //重新排序
            $temp = [];
            $data->get("title")->keys()->each(function($key) use (&$temp, $v){
                $temp[] =  collect($v)->get($key);
            });
            return $temp;
            //return collect($v->toArray())->only($data->get("title")->keys());
        });

        //添加标头
        $data->get("data")->prepend($data->get("title"));
        Excel::create($data->get("filename"), function($excel) use ($data) {
            /**
             * @var $excel LaravelExcelWriter
             */
            $excel->sheet('Sheetname', function($sheet) use ($data) {
                /**
                 * @var $sheet  LaravelExcelWorksheet
                 */
                $sheet->fromArray($data->get("data")->toArray(),'','','',false);
            });
        })->export($exportType);
    }
    function getList($export = 0){
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
        if($export == 1){
            return $this->exportRosterList($query);
        }
        $list = $query->paginate();
        return view("admin.roster.list",[
            'list' => $list
        ]);
    }

    function exportRosterList($query){
        //对数据进行导出，不进行展现
        $data['filename'] = "所有数据导出";
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

