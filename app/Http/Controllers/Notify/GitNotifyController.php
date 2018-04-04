<?php

namespace App\Http\Controllers\Notify;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GitNotifyController extends BaseController
{
    /**
     * coding webHook
     */
    function coding(Request $request){
        dd($request->headers);
    }

}
