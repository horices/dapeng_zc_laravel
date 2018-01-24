<?php
namespace App\Http\Controllers;

use App\Events\Event;
use app\Http\Controllers\BaseController;

class IndexController extends BaseController
{
    function test(){
        resolve($name);
        event(new Event('123'));
    }
}

