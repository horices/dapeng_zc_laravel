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
        $query = UserModel::query();
        if($field_v !== null){
            $query->where($field_k,"=",$field_v);
        }
        if($status !== null){
            $query->where("status",$status);
        }
        if($grade !== null)
            $query->where("grade",$grade);
        //获取最新20条记录
        $list = $query->orderBy("uid","desc")->paginate(20);
        $listJson = $list->toJson();
        return view("admin.user.list",compact("list",'listJson'));
    }
    
    function getEdit($id){
        $user = UserModel::where("uid","=",$id)->first();
        return view("admin.user.add",compact('user'));
    }
    function getAdd(UserModel $user){
        return view("admin.user.add",[
            'user'=>$user
        ]);
    }
    /**
     * 添加或删除记录
     */
    function postSave(Request $request){
        if($request->input("uid")){
            $user = UserModel::find($request->input("uid"));
            if($user->update($request->input())){
                $returnData['code'] = Util::SUCCESS ;
                $returnData['msg'] = "修改成功";
            }else{
                $returnData['code'] = Util::FAIL ;
                $returnData['msg'] = "修改失败".$user->errors;
            }
        } else {
            if(UserModel::create($request->input())){
                $returnData['code'] = Util::SUCCESS;
                $returnData['msg'] = "添加成功";
            }else{
                $returnData['code'] = Util::FAIL ;
                $returnData['msg'] = "添加失败";
            }
        }
        return response()->json($returnData);
    }
}

