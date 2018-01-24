<?php
namespace App\Http\Controllers\Admin;


class GroupController extends BaseController
{
    /**
     * 群列表页
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function getList(){
        return view("admin.group.list");
    }
}

