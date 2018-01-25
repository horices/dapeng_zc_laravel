<?php
namespace App\Http\Controllers\Admin;


use App\Models\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

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
}

