<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController as Controller;
use App\Models\GroupModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class BaseController extends Controller{

    /**
     * 检索所有的推广专员
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getSelectSeoer(){
        //查询所有的推广专员
        $query = UserModel::seoer();
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
        $query = UserModel::adviser();
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
     * 选择QQ群
     */
    function getSelectGroup(){
        $group = Input::get("group");
        $groupName = Input::get("group_name");
        $adviserName = Input::get("adviser_name");
        $query = GroupModel::with("user")->whereHas('user' ,function($query) use ($adviserName){
            if($adviserName){
                $query->where("name","like","%".$adviserName."%");
            }
        });
        if($group){
            $query->where("qq_group",$group);
        }
        if($groupName){
            $query->where("group_name",$groupName);
        }
        $list = $query->paginate(5);
        return view("admin.public.select_group",[
            'list'  => $list
        ]);
    }
}