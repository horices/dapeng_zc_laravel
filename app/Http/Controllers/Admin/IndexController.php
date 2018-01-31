<?php
namespace App\Http\Controllers\Admin;


class IndexController extends BaseController
{
    function getIndex(){
        return view("admin.index.index");
    }
    
    /**
     * 打开上传页面
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function getUpload(){
        return view("admin.public.upload");
    }
    
    /**
     * 上传图片
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function postUpload(){
        print_R($_FILES);
        exit();
    }
}

