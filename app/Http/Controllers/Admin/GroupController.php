<?php
namespace App\Http\Controllers\Admin;


use App\Models\GroupModel;
use Illuminate\Support\Facades\Input;

class GroupController extends BaseController
{
    /**
     * 群列表页
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function getList(){
        $type = Input::get("type");
        $fieldK = Input::get("field_k");
        $fieldV = Input::get("field_v");
        $isOpen = Input::get("is_open"); 
        $groupModel = GroupModel::whereHas('user' , function($query) use ($fieldK,$fieldV){
            if($fieldK == "adviser_name" && $fieldV !== null){
                $query->where("name","like","%".$fieldV."%");
            }
        });
        if($type){
            $groupModel->where("type","=",$type);
        }
        if($fieldK && $fieldK != 'adviser_name' &&  $fieldV !== null){
            $groupModel->where($fieldK,"=",$fieldV);
        }
        if($isOpen !== null){
            $groupModel->where("is_open","=",$isOpen);
        }
        $list = $groupModel->paginate(20);
        return view("admin.group.list",compact("list"));
    }
    
    /**
     * 修改群
     * @param unknown $id
     */
    function getEdit($id){
        //查询群信息
        $group = GroupModel::find($id);
        return view("admin.group.add",compact("group"));
    }
}

