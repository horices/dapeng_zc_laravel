<?php
namespace App\Http\Controllers\Admin;


use App\Events\RevisingAdvisor;
use App\Models\GroupModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

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
        $groupModel = GroupModel::query()->with("user");
        if($fieldV !== null){
            if($fieldK == "adviser_name"){
                $groupModel->whereHas('user' , function($query) use ($fieldK,$fieldV){
                    $query->where("name","like","%".$fieldV."%");
                });
            }else{
                $groupModel->where($fieldK,$fieldV);
            }
        }
        if($type){
            $groupModel->where("type","=",$type);
        }
        if($fieldK && $fieldK != 'adviser_name' &&  $fieldV !== null){
            $groupModel->where($fieldK,"=",$fieldV);
        }
        if($isOpen !== null){
            $groupModel->where("is_open","=",$isOpen);
        }
        if(Input::get('export') == 1){
            return $this->exportGroupList($groupModel);
        }
        $list = $groupModel->orderBy("id","desc")->paginate();
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
        $rules = [
            'qq_group'  =>  'sometimes|required|unique:user_qqgroup,qq_group',
            'group_name'    =>  "nullable|unique:user_qqgroup,group_name",
            'leader_id' =>  'sometimes|required|exists:user_headmaster,uid'
        ];
        $messages = [
            'qq_group.required' =>  '群号码为必填项',
            'qq_group.unique'   =>  '该群号码已存在',
            'group_name.unique' =>  '班级代号已经存在',
            'leader_id.required'    =>  "请选择课程顾问",
            "leader_id.exists"  =>  "请选择正确的课程顾问"
        ];
        if($request->input("id")){
            $group = GroupModel::find($request->input("id"));
            $rules['qq_group'] = $rules['qq_group'].",".$group->id;
            $rules['group_name'] = $rules['group_name'].",".$group->id;
            Validator::make($request->all(),$rules,$messages)->validate();
            if($group->update($request->input())){
                $returnData['code'] = Util::SUCCESS ;
                $returnData['msg'] = "修改成功";
            }else{
                $returnData['code'] = Util::FAIL ;
                $returnData['msg'] = "修改失败".$group->errors;
            }
        } else {
            Validator::make($request->all(),$rules,$messages)->validate();
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

    function exportGroupList($query){
        $data['filename'] = "群信息".date('YmdHis');
        $data['title']  =   [
            'group_name'    =>  '群代号',
            'qq_group'      =>  '群号',
            'is_open_text'  =>  '开启状态',
            'user.name'  =>  '课程顾问',
            'addtime_export_text'   =>  '创建时间',
            'type_text'          =>  '群类型'

        ];
        $data['data'] = $query->select(DB::raw("*,concat('\'',group_name) as group_name"))->get();
        return $this->export($data,$query,'csv');
    }

    /**
     * 一键关闭所有群
     * @return \Illuminate\Http\JsonResponse
     */
    function postCloseAllGroup(){
        $eff = GroupModel::where("id",">","0")->update(['is_open'=>0]);
        if($eff){
            $returnData['code'] = Util::SUCCESS;
            $returnData['msg'] = "已经成功关闭所有群!";
        }else{
            $returnData['code'] = Util::FAIL;
            $returnData['msg'] = "关闭失败！";
        }
        return Util::ajaxReturn($returnData);
    }
}

