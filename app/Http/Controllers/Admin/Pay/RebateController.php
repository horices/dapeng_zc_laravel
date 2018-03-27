<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/27 0027
 * Time: 16:02
 */

namespace App\Http\Controllers\Admin\Pay;


use App\Http\Controllers\Admin\BaseController;
use App\Models\RebateActivityModel;
use Illuminate\Http\Request;

class RebateController extends BaseController {
    /**
     * 优惠列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getList(Request $request){
        $RebateActivityModel = RebateActivityModel::query()->where("status",'USE');
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
            'list'          =>  $list,
            'adminInfo'     =>  $this->getUserInfo(),
            'leftNav'           => "admin.pay-package"
        ]);
    }
}