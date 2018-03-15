<?php
namespace App\Http\Controllers\Admin;


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
            'leftNav' => "admin.user.list"
        ]);
    }
    function getAdd(UserModel $user){
        return view("admin.user.add",[
            'user'=>$user,
            'leftNav' => "admin.user.list"
        ]);
    }
    /**
     * 添加或删除记录
     */
    function postSave(Request $request){
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
}

