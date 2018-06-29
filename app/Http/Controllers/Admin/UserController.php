<?php
namespace App\Http\Controllers\Admin;


use App\Utils\Api\DapengUserApi;
use App\Exceptions\DapengApiException;
use App\Exceptions\UserValidateException;
use App\Http\Requests\UserRequest;
use App\Models\UserModel;
use DebugBar\DebugBar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Utils\Util;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    function getList(){
        $field_k = Input::get("field_k");
        $field_v = Input::get("field_v");
        $status = Input::get("status");
        $grade = Input::get("grade");
        //允许查询所有的人员
        $query = UserModel::withoutGlobalScope('status');
        if($field_v !== null){
            $query->where($field_k,"=",$field_v);
        }
        if($status !== null){
            $query->where("status",$status);
        }
        if($grade !== null)
            $query->where("grade",$grade);
        if(Input::get('export') == 1){
            return $this->exportUserList($query);
        }

        //获取最新20条记录
        $list = $query->orderBy("uid","desc")->paginate();
        return view("admin.user.list",[
            'list'  => $list
        ]);
    }
    
    function getEdit($id){
        $user = UserModel::withoutGlobalScope('status')->where("uid",$id)->first();
        return view("admin.user.add",[
            'user'  =>  $user,
            'userGradeList'=>collect($this->getUserGradeList())->filter(function($v,$k){
                return $k>5;
            }),
            'leftNav' => "admin.user.list"
        ]);
    }
    function getAdd(UserModel $user){
        return view("admin.user.add",[
            'user'=>$user,
            'userGradeList'=>collect($this->getUserGradeList())->filter(function($v,$k){
                return $k>5;
            }),
            'leftNav' => "admin.user.list"
        ]);
    }
    /**
     * 添加或删除记录
     */
    function postSave(UserRequest $request){
        $input = $request->all();
        if($request->input("uid")){
            $user = UserModel::withoutGlobalScope('status')->find($request->input("uid"));
            if(!$request->get("password")) {
                $input = $request->except("password");
            }else{
                $input['password'] = md5($request->get("password"));
            }
            $user->fill($input);
            if($user->save()){
                $returnData['code'] = Util::SUCCESS ;
                $returnData['msg'] = "修改成功";
                $routeUrl = $request->get('back_url');
                if($routeUrl)
                    $returnData['url'] = $routeUrl;
            }else{
                $returnData['code'] = Util::FAIL ;
                $returnData['msg'] = "修改失败".$user->errors;
            }
        } else{
            if(!collect($input)->get('password')){
                $input['password'] = '123456';
            }
            $input['password'] = md5($input['password']);
            if(UserModel::create($input)){
                $returnData['code'] = Util::SUCCESS;
                $returnData['msg'] = "添加成功";
                $returnData['url'] = route("admin.user.list");
            }else{
                $returnData['code'] = Util::FAIL ;
                $returnData['msg'] = "添加失败";
            }
        }
        return response()->json($returnData);
    }

    function exportUserList($query){
        $data['filename'] = "用户列表".date('YmdHis');
        $data['title']  =   [
            'staff_no'    =>  '工号',
            'name'      =>  '姓名',
            'mobile'  =>  '手机号',
            'dapeng_user_mobile'  =>  '主站手机号',
            'status_text'   =>  '状态',
            'grade_text'          =>  '用户级别'

        ];
        //$data['data'] = $query->take(5000)->get();
        return $this->export($data,$query);
    }

    /**
     * 分量管理列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getQuantityList(Request $request){
//        \Debugbar::enable();
        $field_k = $request->get('field_k');
        $field_v = $request->get('field_v');
        $status = $request->get("status");
        $qyery = UserModel::withCount(['groups as groups_qq_count'=>function($group){
            $group->where('type',1);
        },'groups as groups_wx_count'=>function($group){
            $group->where('type',2);
        }])->whereIn('grade',[9,10]);
        if($status !== null){
            $qyery->where('status',$status);
        }
        if($field_k && $field_v){
            $qyery->where($field_k,'like',$field_v.'%');
        }
        $list = $qyery->paginate();
        return view("admin.user.quantity-list",[
            'list' => $list,
            'userInfo'  => $this->getUserInfo(),
            'leftNav'   => 'admin.user.quantity-list'
        ]);
    }

    /**
     * 修改顾问分量
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getQuantityEdit(Request $request){
        $info = UserModel::find($request->get('uid'));
        return view("admin.user.quantity-detail",[
           'r'          => $info,
            'leftNav'   =>  'admin.user.quantity-list'
        ]);
    }

    /**
     * 课程顾问开课
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws DapengApiException
     * @throws UserValidateException
     */
    function postOpenCourseHead(Request $request){
        $uid = $request->get("uid");
        $user = UserModel::find($uid);
        if($user->status == 0){
            throw new UserValidateException("该账号已暂停！");
        }
        if(!$user->dapeng_user_mobile){
            throw new UserValidateException("请先绑定主站账号！");
        }
        //查询主站的用户信息
        $dapengMap = [
            'type'      =>  'MOBILE',
            'keyword'   =>  $user->dapeng_user_mobile
        ];
        $dapengUserInfo = DapengUserApi::getInfo($dapengMap);
        if($dapengUserInfo['code'] == Util::FAIL){
            throw new UserValidateException("主站找不到该课程顾问！");
        }

//        if(!$dapengUserInfo['data']['user']['qqAccount']){
//            throw new UserValidateException("请先去主站补全QQ号！");
//        }
        $data = [
            'advisorid'                =>  $dapengUserInfo['data']['user']['userId'],
        ];
        $openCourseInfo = DapengUserApi::openCourseHead($data);
        if($openCourseInfo['code'] == Util::FAIL){
            throw new DapengApiException($openCourseInfo['msg']);
        }
        //更新课程顾问开课状态
        $user->is_open_course = 1;
        $user->save();
        return response()->json(['code'=>Util::SUCCESS,'msg'=>'开课成功！']);
    }
}

