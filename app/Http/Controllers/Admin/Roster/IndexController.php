<?php
namespace App\Http\Controllers\Admin\Roster;


use App\Api\DapengUserApi;
use App\Exceptions\DapengApiException;
use App\Exceptions\UserValidateException;
use App\Http\Controllers\Admin\BaseController;
use App\Http\Requests\RosterAdd;
use App\Models\DpRegUrlModel;
use App\Models\EventGroupLogModel;
use App\Models\GroupLogModel;
use App\Models\GroupModel;
use App\Models\RosterCourseModel;
use App\Models\RosterModel;
use App\Models\UserHeadMasterModel;
use App\Models\UserModel;
use App\Rules\DapengUserHas;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Validator;

class IndexController extends BaseController
{
    function getAdd(Request $request){
        return view("admin.roster.add");
    }

    function getUserAdd(Request $request){
        //dd($request->route());
        //return Route::dispatchToRoute($request);
//        return Route::respondWithRoute("admin.roster.add");
        $request->merge(['roster_type'=>1]);
        return view("admin.roster.seoer-add");
    }
    function getUserAddWx(Request $request){
        $request->merge(['roster_type'=>2]);
        return view("admin.roster.seoer-add-wx");
    }
    function postUserAdd(Request $request){
        $request->merge(['test'=>1]);
        $userInfo = $this->getUserInfo();
        $request->merge(['seoer_id'=>$userInfo->uid,'roster_type'=>$request->get("roster_type")]);
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
            $roster->load("group");
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
        $adviserId = Input::get("adviser_id");
        $showStatistics = Input::get("show_statistics");
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
            $statisticsWhere['inviter_id'] = $seoerId;
        }
        $statistics = [];
        $statistics['statistics'] = '';
        if($adviserId !== null){
            $query->where('last_adviser_id',$adviserId);
            $statistics = $this->getStatistics(['last_adviser_id'],function($query) use($adviserId) {
                $query->where("last_adviser_id", $adviserId);
            });
        }
        $query->where($where);
        if(Input::get('export') == 1){
            return $this->exportRosterList($query);
        }
        if(!$statistics['statistics'] && $showStatistics){
            $statistics = $this->getStatistics(['']);
        }
        $query->orderBy("id","desc");
        $list = $query->paginate();

        return view("admin.roster.list",[
            'list' => $list,
            'userInfo'  => $this->getUserInfo(),
            "statistics"    => $statistics['statistics']
        ]);
    }

    /**
     * 获取开课记录
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getCourseList(Request $request){
        $rosterCourse = RosterCourseModel::where(['roster_id'=>$request->get("roster_id")])->orderBy('id','desc')->get();
        return view("admin.roster.course-list",[
            'rosterCourse'  =>  $rosterCourse
        ]);
    }

    /**
     * 查询量的群状态变更记录
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getGroupLogList(Request $request){
        $rosterId = $request->get("roster_id");
        $groupLogList = EventGroupLogModel::where("roster_id",$rosterId)->orderBy("id","desc")->get();
        return view("admin.roster.group-log-list",[
            'groupLogList'=>$groupLogList
        ]);
    }

    /**
     * 修改量的进群状态
     * 目前只有微信量未进群时，可以修改此状态
     */
    function changeGroupStatus(Request $request){
        $rosterId = $request->get("roster_id");
        $groupStatus = $request->get("group_status");
        if(!$rosterId)
            throw new UserValidateException("缺少必要信息");
        $roster = RosterModel::find($rosterId);
        if(!$roster){
            throw new UserValidateException("出现未知错误");
        }
        if($roster->roster_type == 2){
            $roster->group_status = $groupStatus;
            //添加用户的进群记录
            $userInfo = $this->getUserInfo();
            $data['roster_id'] = $roster->id;
            $data['qq'] = $roster->roster_no;
            $data['group_status'] = $groupStatus;
            $data['addtime'] = time();
            $data['operator'] = $userInfo->uid;
            $data['operator_name'] = $userInfo->name;
            EventGroupLogModel::create($data);
        }
        return Util::ajaxReturn(Util::SUCCESS,"修改成功",[]);
    }
    /**
     * 获取指定用户的量列表
     */
    function getUserList(Request $request){
        $userInfo = $this->getUserInfo();
        //默认显示数据统计
        $request->merge(["show_statistics"=>1]);
        $user = UserModel::seoer()->find($userInfo['uid']);
        if($user){
            $request->merge(['seoer_id'=>$userInfo->uid]);
        }else{
            $user = UserModel::adviser()->find($userInfo['uid']);
            if(!$user){
                throw new UserValidateException("您还没有权限访问该操作");
            }
            $request->merge(['adviser_id'=>$userInfo->uid]);
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

    function postOpenCourse(Request $request){
        $validator = Validator::make($request,[
            'id'        =>  'sometimes|required|exists:user_roster,id',
            'phone'     =>  ['required_without:dapeng_user_id','regex:/\d{11}/',new DapengUserHas()],
        ],[
            'id.required'       =>  '请选择要开课的用户！',
            'id.exists'         =>  '未找到要开课的用户！',
            'phone.required'    =>  '请输入要开课用户的手机号！',
            'phone.regex'       =>  '请输入正确格式的手机号！'
        ]);
        $validator->validate();
        //当前用户信息
        $rosterData = RosterModel::find($request->get("id"));
        //所属课程顾问信息
        $userData = UserModel::find($rosterData->last_adviser_id);
        //用户主站手机号
        $studentMobile = $request->has("phone") ? $request->get("phone") : $rosterData->dapeng_user_mobile;
        //获取主站信息
        $dapengUserInfo = DapengUserApi::getInfo(['type'=>'MOBILE','keyword'=>$studentMobile]);
        if($dapengUserInfo['code'] == Util::FAIL){
            throw new DapengApiException("主站未找到该用户！");
        }
        $data = [
            'wingsId'           =>  $userData->uid,
            'advisorMobile'     =>  $userData->dapeng_user_mobile,
            'studentMobile'     =>  $studentMobile,
            'qq'                =>  $userData->qq,
            'wx'                =>  $userData->wx,
            'schoolId'          =>  'SJ'
        ];
        $res = DapengUserApi::openCourse($data); //接口59
        if($res['code'] == Util::FAIL){
            throw new DapengApiException($res['msg']);
        }
        $rosterData->dapeng_user_mobile = $studentMobile;
        $rosterData->dapeng_user_id  = $dapengUserInfo['data']['user']['userId'];
        $rosterData->save();
        return response()->json(['code'=>Util::SUCCESS,'msg'=>'开课成功！']);
    }
}

