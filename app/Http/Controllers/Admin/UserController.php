<?php
namespace App\Http\Controllers\Admin;


use App\Api\DapengUserApi;
use App\Exceptions\DapengApiException;
use App\Exceptions\UserValidateException;
use App\Http\Requests\UserRequest;
use App\Models\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Utils\Util;

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

        if($request->input("uid")){
            $user = UserModel::withoutGlobalScope('status')->find($request->input("uid"));
            $user->fill($request->input());
            if($user->save()){
                $returnData['code'] = Util::SUCCESS ;
                $returnData['msg'] = "修改成功";
                $returnData['url'] = route("admin.user.list");
            }else{
                $returnData['code'] = Util::FAIL ;
                $returnData['msg'] = "修改失败".$user->errors;
            }
        } else {
            if(UserModel::create($request->input())){
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
        $data['data'] = $query->take(5000)->get();
        return $this->export($data,'xls');
    }

    /**
     * 分量管理列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getQuantityList(Request $request){
        $field_k = $request->get('field_k');
        $field_v = $request->get('field_v');
        $status = $request->get("status");
        $qyery = UserModel::whereIn('grade',[9,10]);
        if($status !== ""){
            $qyery->where('status',$status);
        }
        if($field_k && $field_v){
            $qyery->where($field_k,'like',$field_v.'%');
        }
        $list = $qyery->paginate();
        return view("admin.user.quantity-list",[
            'list' => $list,
            'userInfo'  => $this->getUserInfo(),
            'leftNav'   => \Illuminate\Support\Facades\Request::get("leftNav")
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
        $userData = UserModel::find($uid);
        if($userData->status == 0){
            throw new UserValidateException("该账号已暂停！");
        }
        if(!$userData->dapeng_user_mobile){
            throw new UserValidateException("请先绑定主站账号！");
        }
        //查询主站的用户信息
        $dapengMap = [
            'type'      =>  'MOBILE',
            'keyword'   =>  $userData->dapeng_user_mobile
        ];
        $dapengUserInfo = DapengUserApi::getInfo($dapengMap);
        if($dapengUserInfo['code'] == Util::FAIL){
            throw new UserValidateException("主站找不到该课程顾问！");
        }

        if(!$dapengUserInfo['data']['user']['qqAccount']){
            throw new UserValidateException("请先去主站补全QQ号！");
        }
        $data = [
            'wingsId'           =>  $userData->uid,
            'advisorMobile'     =>  $userData->dapeng_user_mobile,
            'studentMobile'     =>  $userData->dapeng_user_mobile,
            'schoolId'          =>  Util::getSchoolName(),
            'qq'                =>  $dapengUserInfo['data']['user']['qqAccount'],
            'wx'                =>  ''
        ];
        $openCourseInfo = DapengUserApi::openCourse($data);
        if($openCourseInfo['code'] == Util::FAIL){
            throw new DapengApiException($openCourseInfo['msg']);
        }
        return response()->json(['code'=>Util::SUCCESS,'msg'=>'开课成功！']);
    }
}

