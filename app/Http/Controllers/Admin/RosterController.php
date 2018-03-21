<?php
namespace App\Http\Controllers\Admin;


use App\Exceptions\UserValidateException;
use App\Http\Requests\RosterAdd;
use App\Models\RosterModel;
use App\Models\UserModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;

class RosterController extends BaseController
{
    function getAdd(Request $request){
        return view("admin.roster.add");
    }

    function getUserAdd(Request $request){
        //dd($request->route());
        //return Route::dispatchToRoute($request);
//        return Route::respondWithRoute("admin.roster.add");
        return view("admin.roster.seoer_add");
    }
    function postUserAdd(Request $request){
        $request->merge(['test'=>1]);
        $userInfo = $this->getUserInfo();
        $request->merge(['seoer_id'=>$userInfo->uid,'roster_type'=>1]);

        if($request->post("validate") == 1){
            if(!RosterModel::validateRosterData($request->all())){
                throw new UserValidateException("非法操作");
            }
            return Util::ajaxReturn([
                'code'  => Util::SUCCESS,
                'msg'   =>  '该量可以被提交',
                'data'  => ''
            ]);
        }
        //验证数据
        return Route::respondWithRoute("admin.roster.add.post");
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
        if($roster = RosterModel::addRoster($data)){
            $returnData['code'] = Util::SUCCESS;
            $returnData['msg'] = "添加成功";
            $returnData['data'] = $roster;
        }else{
            $returnData['code'] = Util::FAIL;
            $returnData['msg'] = "添加失败";
        }
        return response()->json($returnData);
    }

    /**
     * 获取全部列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    function getList(Request $request){
        //若没有选择时间，则默认选择当天时间
        if(!$request->has("startdate")){
            $request->merge(['startdate'=>date('Y-m-d 00:00:00')]);
        }
        //查询所有列表
        $query = RosterModel::query()->with(['group',"group_event_log"=>function($query){
            $query->select("roster_id","group_status",DB::raw("max(addtime) as addtime"))->where("group_status","=",2)->groupBy(["roster_id","group_status"])->orderBy("id","desc");
        }]);
        $field_k = Input::get("field_k");
        $field_v = Input::get("field_v");
        $type = Input::get("type");
        $isReg = Input::get("is_reg");
        $courseType = Input::get("course_type");
        $groupStatus = Input::get("group_status");
        $flag = Input::get("flag");
        $startDate = Input::get("startdate");
        $endDate = Input::get("enddate");
        $seoerId = Input::get("seoer_id");
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
        if($seoerId !==  null){
            $query->where('inviter_id',$seoerId);
        }
        $query->where($where);
        if(Input::get('export') == 1){
            return $this->exportRosterList($query);
        }
        $userInfo = $this->getUserInfo();
        $statistics = $this->getStatistics([''],function($query) use($userInfo) {
            $query->where("inviter_id", $userInfo->uid);
        });
        $query->orderBy("id","desc");
        $list = $query->paginate();
        return view("admin.roster.list",[
            'list' => $list,
            "statistics"    => $statistics['statistics']
        ]);
    }

    /**
     * 获取指定用户的量列表
     */
    function getUserList(Request $request){
        $userInfo = $this->getUserInfo();
        $user = UserModel::seoer()->find($userInfo['uid']);
        if($user){
            $request->merge(['seoer_id'=>$userInfo->uid]);
        }else{
            $user = UserModel::adviser()->find($userInfo['uid']);
            if(!$user){
                throw new UserValidateException("您还没有权限访问该操作");
            }
            $request->merge(['last_adviser_id'=>$userInfo->uid]);
        }
        return Route::respondWithRoute("admin.roster.list");
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

