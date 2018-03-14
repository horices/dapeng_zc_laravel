<?php
namespace App\Http\Controllers\Admin;


use App\Models\GroupModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;

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
        })->with("user");
        if($type){
            $groupModel->where("type","=",$type);
        }
        if($fieldK && $fieldK != 'adviser_name' &&  $fieldV !== null){
            $groupModel->where($fieldK,"=",$fieldV);
        }
        if($isOpen !== null){
            $groupModel->where("is_open","=",$isOpen);
        }
        $list = $groupModel->paginate();
        return view("admin.group.list",[
            'list' => $list,
        ]);
    }
    function getAdd(GroupModel $group){
        return view("admin.group.add",[
            'group' => $group,
            'leftNav' => "admin.group.list"
        ]);
    }
    
    /**
     * 修改群
     * @param unknown $id
     */
    function getEdit($id){
        //查询群信息
        $group = GroupModel::find($id);
        return view("admin.group.add",[
            'group' => $group,
            'leftNav' => "admin.group.list"
        ]);
    }
    
    function postSave(Request $request){
        if($request->input("id")){
            $group = GroupModel::find($request->input("id"));
            if($group->update($request->input())){
                $returnData['code'] = Util::SUCCESS ;
                $returnData['msg'] = "修改成功";
            }else{
                $returnData['code'] = Util::FAIL ;
                $returnData['msg'] = "修改失败".$group->errors;
            }
        } else {
            if(GroupModel::create($request->input())){
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

