<?php
namespace App\Http\Controllers\Admin;


class IndexController extends BaseController
{
    function getIndex(){
        return view("admin.index.index");
    }
}

