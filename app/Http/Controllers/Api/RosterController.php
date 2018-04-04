<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RosterModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RosterController extends BaseController
{
    /**
     * 获取用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function getInfo(Request $request){
        Validator::make($request->all(),[
            'schoolId'  =>  "nullable|in:SJ,MS",
            'type'  =>  "required|in:id,dapeng_user_id,qq,mobile,name",
            'keyword'   =>  'required'
        ],[
            'type.required' =>  "请输入要查询的类型",
            'type.in'   =>  "请选择正确的查询类型",
            'schoolId.in' =>  "请选择正确的学院",
            'keyword.required'   =>  "请输入查询的关键字"
        ])->validate();
        $roster = RosterModel::with('group','adviser')->where(Input::get("type"),Input::get("keyword"))->orderBy("id","desc")->first();
        $roster = $roster->toArray();
        $roster['origin'] = Str::lower(Util::getSchoolName());
        $roster['adviser_qq'] = $roster['adviser']['qq'];
        $roster['adviser_name'] = $roster['adviser']['name'];
        $roster['adviser_mobile'] = $roster['adviser']['mobile'];
        $roster['qq_group_url'] = $roster['group']['qrc_link'];
        $roster['qq_group_qrc'] = $roster['group']['qrc_url'];
        return Util::ajaxReturn(Util::SUCCESS,"",$roster);
    }


    /**
     * M站注册调用该接口，设置用户的主站信息 dapeng_user_id,dapeng_user_mobile,dapeng_reg_time
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function setInfo(Request $request){
        $rosterId = $request->get("roster_id");
        $roster = RosterModel::find($rosterId);
        $roster->fild($request->all());
        if($roster->save() === false){
            Log::error("保存roster信息失败");
        }
        return Util::ajaxReturn(Util::SUCCESS,"success","修改成功");
    }
}
