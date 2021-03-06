<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/27 0027
 * Time: 16:02
 */

namespace App\Http\Controllers\Admin\Pay;


use App\Exceptions\UserValidateException;
use App\Http\Controllers\Admin\BaseController;
use App\Models\CoursePackageModel;
use App\Models\RebateActivityModel;
use App\Utils\Util;
use Illuminate\Http\Request;

class RebateController extends BaseController {
    /**
     * 优惠列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getList(Request $request){
        if(!$request->has("package_id")){
            throw new UserValidateException("请先选择套餐！");
        }
        //先获取套餐的信息
        $packageDetail = CoursePackageModel::find($request->get("package_id"));
        $RebateActivityModel = RebateActivityModel::query()->where("status",'USE');
        $RebateActivityModel->where('package_id',$request->get('package_id'));
        $title = $request->get("title");
        if(!empty($title)){
            $RebateActivityModel->where('title','like','%'.$title.'%');
        }
        $type = $request->get("type");
        if($type != ''){
            $RebateActivityModel->where('type',$type);
        }
        $list = $RebateActivityModel->orderBy('id','desc')->paginate(15);

        return view("admin.pay.rebate.list",[
            'package'       =>  $packageDetail,
            'list'          =>  $list,
            'adminInfo'     =>  $this->getUserInfo(),
            'leftNav'           => "admin.pay.rebate"
        ]);
    }

    /**
     * 新增优惠
     * @param RebateActivityModel $rebate
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getAdd(RebateActivityModel $rebate,Request $request){
        session(["backUrl"=>route("admin.pay.rebate.list",['package_id'=>$request->get('package_id')])]);
        return view("admin.pay.rebate.detial",[
            'r'                 =>  $rebate,
            'course_give'       =>  collect($rebate->course_give_data)->toJson(JSON_UNESCAPED_UNICODE),
            'leftNav'           => "admin.pay.rebate"
        ]);
    }

    /**
     * 编辑优惠
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getEdit(Request $request){
        session(["backUrl"=>url()->previous()]);
        $rebate = RebateActivityModel::find($request->get("id"));
        return view("admin.pay.rebate.detial",[
            'r'                 =>  $rebate,
            'course_give'       =>  collect($rebate->course_give_data)->toJson(JSON_UNESCAPED_UNICODE),
            'leftNav'           => "admin.pay.rebate"
        ]);
    }

    /**
     * 跟新或新增优惠
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function postSave(Request $request){
        //修改数据
        if ($request->has('id') && $request->input('id')) {
            $eff = RebateActivityModel::updateData($request->input());
            if($eff){
                return response()->json(['code'=>Util::SUCCESS,'msg'=>'修改成功！','url'=>session()->get("backUrl")]);
            }
        }else{
            $eff = RebateActivityModel::addData($request->input());
            if($eff){
                return response()->json(['code'=>Util::SUCCESS,'msg'=>'新增成功！','url'=>session()->get("backUrl")]);
            }
        }
    }

    function postDelete(Request $request){
        if ($request->has('id') && $request->input('id')) {
            $id = $request->input('id');
            $detail = RebateActivityModel::find($id);
            if(!$detail){
                throw new UserValidateException("未找到要删除的套餐！");
            }
            if($detail->delete()){
                return response()->json(['code'=>Util::SUCCESS,'msg'=>'删除成功！']);
            }else{
                throw new UserValidateException("删除失败！");
            }
        }
    }

}