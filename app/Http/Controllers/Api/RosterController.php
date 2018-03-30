<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RosterModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RosterController extends BaseController
{
    //
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
}
