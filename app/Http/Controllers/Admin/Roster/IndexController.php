<?php
namespace App\Http\Controllers\Admin\Roster;


use App\Jobs\SendCreatedRosterNotification;
use App\Jobs\SendOpenCourseNotification;
use App\Models\RosterCourseLogModel;
use App\Utils\Api\DapengUserApi;
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
use App\Utils\StrValueBinder;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class IndexController extends BaseController
{
    function getAdd(Request $request){
        return view("admin.roster.add");
    }

    function getUserAdd(Request $request){
        $request->merge(['roster_type'=>1]);
        return view("admin.roster.seoer-add");
    }
    function getUserAddWx(Request $request){
        $request->merge(['roster_type'=>2]);
        return view("admin.roster.seoer-add-wx");
    }
    function postUserAdd(Request $request){
        $request->merge(['is_admin_add'=>0]);
        $userInfo = $this->getUserInfo();
        $request->merge(['seoer_id'=>$userInfo->uid,'roster_type'=>$request->get("roster_type")]);
        if($request->post("validate") == 1){
            RosterModel::validateRosterData($request->all(),true);
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
        //防止并发时，重复提交量的问题
        $resource = fopen("roster.lock","w+");
        flock($resource,LOCK_EX);
        DB::beginTransaction();
        if($roster = RosterModel::addRoster(collect($data)->toArray(),true)){
            DB::commit();
            //发送添加成功的通知(此通知需要同步发送,先将其它学院置为灰色,通知后，当前量被重置为老量,且没有新活标识)
            SendCreatedRosterNotification::dispatch($roster->toArray());
            $roster->load("group");
            $returnData['code'] = Util::SUCCESS;
            $returnData['msg'] = "添加成功";
            $returnData['data'] = $roster;
        }else{
            DB::rollback();
            $returnData['code'] = Util::FAIL;
            $returnData['msg'] = "添加失败";
        }
        flock($resource,LOCK_UN);
        fclose($resource);
        return response()->json($returnData);
    }

    /**
     * 检查该量量是否允许被添加
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function postCheckRosterStatus(Request $request){
        RosterModel::validateRosterData($request->all(),true);
        return Util::ajaxReturn(['code'=>Util::SUCCESS,"msg"=>"可以正常添加"]);
    }
    /**
     * 解除绑定，可以重新输入手机号
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUnbind(Request $request){
        $roster = RosterModel::find($request->get("roster_id"));
        $roster->dapeng_user_mobile = '';
        $roster->dapeng_user_id = '';
        $roster->save();
        return Util::ajaxReturn(Util::SUCCESS,"解绑成功");
    }
    /**
     * 获取全部列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    function getList(Request $request){
        //若没有选择时间，则默认选择当天时间
//        if(!$request->get("startdate")){
//            $request->merge(['startdate'=>date('Y-m-d 00:00:00')]);
//        }
        $where = [];
        $statistics = [];
        $isLoadStatistics = false;  //是否已经加载统计，没有加载的话，加载全部统计
        if(!$request->has("startdate")){
            $request->merge(['startdate'=>date('Y-m-d 00:00:00')]);
        }
        //查询所有列表
        $query = RosterModel::query()->with(['group','adviser',"group_event_log"=>function($query){
            $query->select("roster_id","group_status",DB::raw("max(addtime) as addtime"))->where("group_status",">",0)->groupBy(["roster_id","group_status"])->orderBy("id","desc");
        }]);
        $seachType = Input::get("search_type");
        $keywords = Input::get("keywords");
        if($seachType && $keywords !== null){
            if($seachType == "roster_no"){
                $query->where(function($query) use ($keywords){
                    $query->where("qq",$keywords)->orWhere("wx",$keywords);
                });
                //搜索指定的QQ号或微信号时，自动清除其它条件，除推广专员，其它人搜索没有条件限制
                /*$request->replace([
                    'search_type'  =>   $seachType,
                    'keywords'  => $keywords,
                    'seoer_id'=> Input::get("seoer_id"),
                    'adviser_id'    => Input::get("adviser_id"),
                    'show_statistics'   =>  Input::get('show_statistics'),
                ]);*/
            }elseif ($seachType == "group_name"){
                $query->whereHas("group",function ($group) use($keywords) {
                    $group->where('group_name',$keywords);
                });
            }elseif ($seachType == "qq_group"){
                $query->whereHas("group",function ($group) use($keywords) {
                    $group->where('qq_group',$keywords);
                });
            }else{
                $where[$seachType] = $keywords;
            }
        }
        $type = Input::get("roster_type");
        $isReg = Input::get("is_reg");
        $courseType = Input::get("course_type");
        $groupStatus = Input::get("group_status");
        $flag = Input::get("flag");
        $startDate = Input::get("startdate");
        $endDate = Input::get("enddate");
        $dateType = Input::get("dateType",'addtime');
        $seoerId = Input::get("seoer_id");
        $adviserId = Input::get("adviser_id");
        $showStatistics = Input::get("show_statistics");
        $adviserName = Input::get("adviser_name");
        $seoerName = Input::get("seoer_name");
        //智能推广
        $seoerGrade = Input::get("seoer_grade");

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
            $query->whereRaw($dateType." >= ".strtotime($startDate));
        }
        if($endDate !== null){
            $query->whereRaw($dateType." <= ".strtotime($endDate));
        }
        if($seoerId !==  null){
            $query->where('inviter_id',$seoerId);
            $statistics = $this->getStatistics(['inviter_id'],function($query) use($seoerId) {
                $query->where("inviter_id", $seoerId);
            });
            $statistics = collect($statistics)->get($seoerId);
            $isLoadStatistics = true;
        }
        if($adviserId !== null){
            $query->where('adviser_id',$adviserId);
            $statistics = $this->getStatistics(['adviser_id'],function($query) use($adviserId) {
                $query->where("last_adviser_id", $adviserId);
            });
            $statistics = collect($statistics)->get($adviserId);
            $isLoadStatistics = true;
            //查看指定的课程时，三天内的活量优先显示
            //三天前的时间节点
            $query->orderBy(DB::raw("concat(flag,addtime) > 2".strtotime("-3 days")),"desc");
        }
        //课程顾问姓名搜索
        if($adviserName){
            $groupIds = GroupModel::whereHas("user",function($user) use ($adviserName){
                $user->where("name","like",$adviserName."%");
            })->pluck("id");
            $query->whereIn("qq_group_id",$groupIds);
            /*$query->whereHas("adviser",function ($query)use ($adviserName){
                $query->group()->user()->where([
                    ['name','like',$adviserName."%"]
                ]);
            });*/
        }

        //推广专员姓名搜索

        if($seoerName || $seoerGrade){
            $query->whereHas("seoer",function ($query) use ($seoerName,$seoerGrade){
                if(!$seoerGrade){
                    $query->seoer();
                }else{
                    $query->where('grade',$seoerGrade);
                }
                if($seoerName){
                    $query->where([
                        ['name','like',"{$seoerName}%"]
                    ]);
                }
            });
        }
        $query->where($where);
        if(Input::get('export') == 1){
            return $this->exportRosterList($query);
        }
        if(!$isLoadStatistics && $showStatistics)
            $statistics = $this->getStatistics();
        $query->orderBy("id","desc");
        $list = $query->paginate();
        return view("admin.roster.list",[
            'list' => $list,
            'userInfo'  => $this->getUserInfo(),
            "statistics"    => $statistics,
            'leftNav'   => \Illuminate\Support\Facades\Request::get("leftNav")
        ]);
    }

    /**
     * 生成新浪接口的短连接
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function postSetRegUrl(Request $request){
        $regUrl = Util::getShorturl($request->input('url'));
        return Util::ajaxReturn(Util::SUCCESS,'生成成功！',['reg_url'=>$regUrl]);
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
            'seoer_staff_no'    => '推广工号',
            'inviter_name'    => '推广专员',
            'adviser_staff_no' =>  '顾问工号',
            'last_adviser_name' =>  '课程顾问',
            'addtime_export_text'   =>  '提交时间',
            'is_reg_text'   =>  "是否注册",
            'dapeng_reg_time_export_text'  =>  '注册时间',
            "course_type_text"  =>  "课程类型",
            "group_status_text" =>  "进群状态",
            "group_status_1_last_time"    =>  "申请进群时间",
            "group_status_2_last_time"    =>  "进群时间",
            "group_status_full_text"     =>  "群状态变更时间"
        ];
        set_time_limit(0);
        ini_set("memory_limit","100M");
        //查询所有的用户
        $users = [];//UserModel::all()->keyBy("uid");
        //查询所有的群信息
        $groups = [];//GroupModel::all()->keyBy("id");
        $i = 1;
        $max = 30000;   //最大导出数量
        $fileName = $data['filename'].".csv";
        //设置好告诉浏览器要下载excel文件的headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'. $fileName .'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $fp = fopen('php://output', 'a');//打开output流
        mb_convert_variables('GBK', 'UTF-8', $data['title']);
        fputcsv($fp,$data['title']);
        $query->without(['group','adviser'])->orderBy("id","desc")->select(DB::raw('id,qq,wx,type,inviter_id,inviter_name,addtime,dapeng_reg_time,is_reg,course_type,group_status,qq_group_id'))->chunk(2000, function ($rows) use (&$data,&$i,$max,&$fp,&$users,&$groups) {
            foreach ($rows as $row) {
                $row['group'] = [];
                $row['adviser'] = [];
                if($row->qq_group_id){
                    if(!isset($groups[$row->qq_group_id])){
                        $groups[$row->qq_group_id] = GroupModel::find($row->qq_group_id);
                        if(!isset($users[$row->qq_group_id])){
                            $users[$row->qq_group_id] = UserModel::find($groups[$row->qq_group_id]->leader_id);
                        }
                    }
                    $row['group'] = $groups[$row->qq_group_id];
                    $row['adviser'] = $users[$row->qq_group_id];
                }
                if($row->inviter_id){
                    if($users[$row->inviter_id]  = UserModel::find($row->inviter_id)){
                        $row['seoer_staff_no'] = $users[$row->inviter_id]->staff_no."\t";
                    }
                }
                if($row['adviser']){
                    $row['adviser_staff_no'] = $users[$row->qq_group_id]->staff_no."\t";
                    $row['last_adviser_name'] =  $row['adviser']->name;
                }
                //存在群变更记录
                if($row->group_event_log->count()){
                    if($log = $row->group_event_log->first(function($groupEventLog){
                        return $groupEventLog->group_status == 1;
                    })){
                        $row['group_status_1_last_time'] = $log->addtime_full_text;
                    };
                    if($log = $row->group_event_log->first(function($groupEventLog){
                        return $groupEventLog->group_status == 2;
                    })){
                        $row['group_status_2_last_time'] = $log->addtime_full_text;
                    };
                    foreach($row->group_event_log as $log){
                        $row['group_status_full_text'] .= app('status')->getGroupStatus($log->group_status).":".$log->addtime_full_text."\r\n";
                    }
                }
                //$row['adviser'] = $users[$row->last_adviser_id];
                $rowOut = $this->parseExportTitle($data['title'],$row);
                mb_convert_variables('GBK', 'UTF-8', $rowOut);
                fputcsv($fp, $rowOut);
                //刷新输出缓冲到浏览器
                ob_flush();
                flush();//必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
                if($i++ >= $max){
                    return false;
                }
            }
        });
        fclose($fp);
        exit();
        //$data['data'] = $query->take(5000)->get();
        return $this->export($data,$query,'csv');
    }

    /**
     * 学员开课
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws DapengApiException
     * @throws UserValidateException
     */
    function postOpenCourse(Request $request){
        $data = collect($request)->toArray();
        $validator = Validator::make($data,[
            'id'        =>  'sometimes|required|exists:user_roster,id',
            //'phone'     =>  ['required_without:dapeng_user_id','regex:/\d{11}/',new DapengUserHas()],
        ],[
            'id.required'       =>  '请选择要开课的用户！',
            'id.exists'         =>  '未找到要开课的用户！',
            //'phone.required_without'    =>  '请输入要开课用户的手机号！',
            //'phone.regex'       =>  '请输入正确格式的手机号！'
        ]);
        $validator->validate();
        //当前用户信息
        $rosterData = RosterModel::find($data['id']);

        //用户开课的主站手机号
        //$studentMobile = "";
        if($rosterData->dapeng_user_mobile) {
            $studentMobile = $rosterData->dapeng_user_mobile;
        }else {
            if(!$request->phone){
                throw new UserValidateException("请填写开课用户的手机号！");
            }
            $studentMobile = $request->phone;
        }
        //所属群
        $groupData = GroupModel::find($rosterData->qq_group_id);
        //所属课程顾问信息
        $userData = UserModel::find($rosterData->group->user->uid);
        //获取主站信息
        $dapengUserInfo = DapengUserApi::getInfo(['type'=>'MOBILE','keyword'=>$studentMobile]);
        if($dapengUserInfo['code'] == Util::FAIL){
            throw new DapengApiException("主站未找到该用户！");
        }
        $data = [
            'advisorMobile'             =>  $userData->dapeng_user_mobile,
            'studentMobile'             =>  $studentMobile,
            'classCode'                 =>  $groupData->group_name,
            'qq'                        =>  $rosterData->qq,
            'wx'                        =>  $rosterData->wx,
            'affiliatedCollege'         =>  Util::getSchoolId()
        ];
        if(!$userData->dapeng_user_mobile){
            throw new UserValidateException("该课程顾问还未绑定主站账号,不能进行该操作");
        }
        $res = DapengUserApi::openCourse($data); //接口59
        if($res['code'] == Util::FAIL){
            throw new DapengApiException($res['msg']);
        }
        $rosterData->dapeng_user_mobile = $studentMobile;
        //$rosterData->dapeng_user_id  = $dapengUserInfo['data']['user']['userId'];
        $rosterData->save();
        unset($data);
        //添加开课日志
        $data['roster_id'] = $rosterData->id;
        $data['qq'] = $rosterData->qq;
        $data['action'] = 1;
        $data['course_type'] = 1;
        $data['course_id'] = '00000001';
        $data['course_name'] = '大鹏所有试学课';
        $data['addtime'] = time();
        $data['user_type'] = $rosterData->type;
        RosterCourseLogModel::create($data);
        //修改注册状态和时间
        if(!$rosterData->dapeng_reg_time){
            $rosterData->is_reg = 1;
            $rosterData->dapeng_reg_time = time();
            $rosterData->save();
        }
        //发送开课通知
        //SendOpenCourseNotification::dispatch($rosterData);
        return response()->json(['code'=>Util::SUCCESS,'msg'=>'开课成功！']);
    }

    /**
     * 使用excel批量导入量
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UserValidateException
     */
    function postUploadExcel(Request $request,StrValueBinder $binder){
        $path = $request->file('download')->store('upload/excel');
        $excelArr = Excel::selectSheetsByIndex(0)->setValueBinder($binder)->load($path)->all(['account','seoer','type','group'])->toArray();
        if(empty($excelArr)){
            throw new UserValidateException("表格数据不能为空！");
        }
        //发生错误的量
        $errorArr = [];
        $returnData = [];
        $isImport = 0;
        //导出excel信息
        $exportData = [];
        if(!empty($excelArr)){
            $returnData = [
                'code'  =>  Util::SUCCESS,
                'msg'   =>  '导入成功！'
            ];
            $i = 0;
            foreach ($excelArr as $key=>$val){
                if(empty($val['account'])){
                    continue;
                }
                $userData['is_admin_add'] = 1;
                $userData['roster_type'] = $val['type']; //量的类型 1qq 2微信
                $userData['roster_no'] = $val['account'];  //量账号
                //推广专员ID
                $userData['seoer_id'] = UserModel::where('mobile',$val['seoer'])->value("uid");
                //根据qq群账号获取群ID
                $userData['qq_group_id'] = GroupModel::where("qq_group",$val['group'])->value("id");
                //来源类型，批量导入
                $userData['from_type'] = 6;
                //查询量的主站信息
                //$type = ($userData['type'] == 1) ? "QQ" : "WEIXIN";
                //$userData = collect($userData,$this->getQQRegInfo($val['account'],$type))->collapse();
                try{
                    //dd($userData);
                    $eff = RosterModel::addRoster($userData);
                }catch (ValidationException $exception){
                    $isImport = 1;
                    $errorArr[$i]['account'] = $val['account'];  //量账号
                    $errorArr[$i]['seoer'] = $val['seoer'];    //推广专员账号
                    $errorArr[$i]['type'] = $val['type'];  //量的类型
                    $errorArr[$i]['group'] = $val['group'];  //群账号
                    $errorArr[$i]['error']   =  collect($exception->errors())->first()[0] ?: "未知";
                    $i++;
                }

            }
                unset($userData);
            }

            //如果有错误的量，需要导出
            if($isImport == 1){
                //返回提示
                $returnData['code'] = Util::WARNING;
                $returnData['msg'] = "部分量未导入！";
                $exportData['filename'] = date("Y-m-d H:i:s")."问题数据";
                $exportData['title'] = [
                    'account'=>"账号",
                    'seoer'=>"推广专员",
                    'type'=>"类型",
                    'group'=>"群账号",
                    'error'=>"错误原因",
                ];
                $exportData['data'] = $errorArr;
                //unset($_SESSION['error_user']);
                $request->session()->forget('error_user');
                session(['error_user'=>$exportData]);
                //$_SESSION['error_user'] = $exportData;
                $returnData['url'] = route('admin.roster.index.export-error-user');
                $returnData['location'] = true;
                unlink($path);
                //$this->exportCsv($exportData);
            }
            return response()->json($returnData);
    }

    /**
     * 执行导入 量
     * @param Request $request
     */
    function getExportErrorUser(Request $request){
        if($request->session()->has('error_user')){
            $exportData = $request->session()->get('error_user');
            $request->session()->forget('error_user');
            return $this->export($exportData);
            //$this->exportCsv($exportData);
        }else{
            echo "无导出数据！";
        }
    }

    /**
     * 查询指定的量
     */
    function getSelectOne(Request $request){
        $keywords = $request->input("keywords");
        $roster = "";
        //查询所有列表
        $query = RosterModel::query()->with(['group',"group_event_log"=>function($query){
            $query->select("roster_id","group_status",DB::raw("max(addtime) as addtime"))->where("group_status",">",0)->groupBy(["roster_id","group_status"])->orderBy("id","desc");
        }]);
        if($keywords){
            $query->where(function($query) use ($keywords){
                $query->where("qq",$keywords)->orWhere("wx",$keywords);
            });
            $roster = $query->orderBy("id","desc")->first();
        }
        //$request->merge(['startdate'=>null,'search_type'=>'roster_no','keywords'=>$keywords,'show_statistics'=>1,'form_ele'=>'.search_type,.search']);
        return view("admin.roster.select-one",[
            'roster'=>  $roster,
            'userInfo'  => $this->getUserInfo()
        ]);
        //return Route::respondWithRoute("admin.roster.list");
    }

    /**
     * 查询指定 智能推广的数据
     * @param Request $request
     * @return mixed
     */
    function getIntelligent(Request $request){
        $request->merge(['seoer_grade'=>11]);
        return Route::respondWithRoute("admin.roster.list");
    }
}

