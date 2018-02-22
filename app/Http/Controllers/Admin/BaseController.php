<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController as Controller;
use App\Models\UserModel;
use Illuminate\Support\Facades\Input;

class BaseController extends Controller{

    /**
     * 检索所有的推广专员
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getSelectSeoer(){
        //查询所有的推广专员
        $query = UserModel::query();
        $query->where("grade","=","12");
        $name = Input::get("name");
        if($name){
            $query->where("name","like","%".$name."%");
        }
        $list = $query->paginate(5);
        return view("admin.public.select_seoer",[
            'list' => $list
        ]);
    }
    /**
     * 检索所有的课程顾问
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getSelectAdviser(){
        //查询所有的推广专员
        $query = UserModel::query();
        $query->whereIn("grade",[10]);
        $name = Input::get("name");
        if($name){
            $query->where("name","like","%".$name."%");
        }
        $list = $query->paginate(5);
        return view("admin.public.select_seoer",[
            'list' => $list
        ]);
    }
}