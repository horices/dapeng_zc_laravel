<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/27 0027
 * Time: 10:18
 */

namespace App\Http\Controllers\Admin\Pay;


use App\Exceptions\UserValidateException;
use App\Http\Controllers\Admin\BaseController;
use App\Models\CoursePackageModel;
use App\Utils\Util;
use Illuminate\Http\Request;

class PackageController extends BaseController {
    /**
     * 获取课程套餐列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getList(Request $request){
        $CoursePackageModel = CoursePackageModel::query()->where("status",'USE');
        $title = $request->get("title");
        if(!empty($title)){
            $CoursePackageModel->where('title','like','%'.$title.'%');
        }
        $schoolId= $request->get("school_id");
        if($schoolId != ''){
            $CoursePackageModel->where('school_id',$schoolId);
        }
        $list = $CoursePackageModel->orderBy('id','desc')->paginate(15);
        return view("admin.pay.package.list",[
            'list'          =>  $list,
            'adminInfo'     =>  $this->getUserInfo(),
            'leftNav'           => "admin.pay.package"
        ]);
    }

    function getAdd(CoursePackageModel $package){
        session(["backUrl"=>route("admin.pay.package.list")]);
        $package->school_id = Util::getSchoolName();
        return view("admin.pay.package.detail",[
            'r'              =>  $package,
            'course_attach'  =>  collect($package->course_attach_data)->toJson(),
            'leftNav'        => "admin.pay.package"
        ]);
    }

    /**
     * 获取套餐详情 增/改
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getEdit(Request $request){
        session(["backUrl"=>url()->previous()]);
        $packageId = $request->get("id");
        $detail = CoursePackageModel::where("id",$packageId)->orderBy("id","desc")->first();
        //dd(collect($detail->course_attach)->toArray());
        return view("admin.pay.package.detail",[
            'r'                 =>  $detail->toJson(),
            'course_attach'     =>  $detail->course_attach,
            'leftNav'           => "admin.pay.package"
        ]);
    }

    /**
     * 执行新增或修改
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UserValidateException
     */
    function postSave(Request $request){
        $post= $request->post();
        foreach ($post as $key=>$val){
            if(is_array($val)){
                $post[$key] = array_filter($val);
            }
        }
        $eff = 0;
        if(isset($post['id']) && $post['id']){
            $eff = CoursePackageModel::updateData($post);
        }else{
            $eff = CoursePackageModel::addData($post);
        }
        if($eff){
            $msg = isset($post['id']) && $post['id'] ? '修改成功！' : '新增成功！';
            return response()->json(['code'=>Util::SUCCESS,'msg'=>$msg,'url'=>session()->get("backUrl")]);
        }else{
            $msg = isset($post['id']) && $post['id'] ? '修改失败！' : '新增失败！';
            throw new UserValidateException($msg);
        }
    }

    /**
     * 软删套餐
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UserValidateException
     */
    function postDelete(Request $request){
        if ($request->has('id') && $request->input('id')) {
            $id = $request->input('id');
            $detail = CoursePackageModel::find($id);
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