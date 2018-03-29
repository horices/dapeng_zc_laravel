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
        $type = $request->get("type");
        if($type != ''){
            $CoursePackageModel->where('type',$type);
        }
        $list = $CoursePackageModel->orderBy('id','desc')->paginate(15);
        return view("admin.pay.package.list",[
            'list'          =>  $list,
            'adminInfo'     =>  $this->getUserInfo(),
            'leftNav'           => "admin.pay.package"
        ]);
    }

    function getAdd(CoursePackageModel $package){
        return view("admin.pay.package.detail",[
            'r' =>  $package,
            'leftNav'           => "admin.pay.package"
        ]);
    }

    /**
     * 获取套餐详情 增/改
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getEdit(Request $request){
        $packageId = $request->get("package_id");
        $detail = CoursePackageModel::where("package_id",$packageId)->orderBy("id","desc")->first();
        return view("admin.pay.package.detail",[
            'r'                 =>  $detail,
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
        //修改数据
        if ($request->has('id') && $request->input('id')) {
            $eff = CoursePackageModel::updateData($request->input());
            if($eff){
                return response()->json(['code'=>Util::SUCCESS,'msg'=>'修改成功！']);
            }

        }else{
            $eff = CoursePackageModel::addData($request->input());
            if($eff){
                return response()->json(['code'=>Util::SUCCESS,'msg'=>'新增成功！']);
            }
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
            $detail->status = "DEL";
            $eff = $detail->save();
            if($eff){
                return response()->json(['code'=>Util::SUCCESS,'msg'=>'删除成功！']);
            }else{
                throw new UserValidateException("删除失败！");
            }
        }
    }

}