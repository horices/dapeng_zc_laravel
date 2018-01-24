<?php
namespace App\Http\Controllers\Admin;


class UserController extends BaseController
{
    function getList(){
        return view("admin.user.list");
    }
}

