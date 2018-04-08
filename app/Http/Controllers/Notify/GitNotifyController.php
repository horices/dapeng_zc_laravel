<?php

namespace App\Http\Controllers\Notify;

use App\Http\Controllers\Controller;
use App\Utils\Util;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class GitNotifyController extends BaseController
{
    //要更新的文件夹
    protected $folder = [
        //Util::MASTER    =>
    ];
    protected $branch = [
        Util::MASTER    =>  "master",
        Util::TEST  =>  "test",
        Util::DEV   =>  "dev",
    ];
    //远程用户
    protected $user = [
        Util::MASTER => 'horice',
        Util::TEST => 'horice',
        Util::DEV => 'horice',
    ];
    function getRemoteUser(){
        $host = collect(Util::getWebSiteConfig('HOST_ALL',false))->get(Request::header('HTTP_HOST'),Util::TEST);
        return $this->user[$host];
    }
    function getBranchName(){
        $host = collect(Util::getWebSiteConfig('HOST_ALL',false))->get(Request::header('HTTP_HOST'),Util::TEST);
        return $this->branch[$host];
    }
    /**
     * coding webHook
     */
    function coding(){
        $hosts = Util::getWebSiteConfig('HOST_ALL',false);
        //推送时，自动更新
        if(Request::header("x-coding-event") == "push"){
            if(Str::endsWith(Request::input("ref"),$this->getBranchName())){
                $this->deploy();
            }
        }
        //提交合并请求时，自动更新
        if(Request::header("x-coding-event") == "merge request"){
            if($this->getBranchName() == Request::input("mergeRequest.base.ref")){
                $this->deploy();
            }
        }
    }

    /**
     * 自动更新
     */
    function deploy (){
        $info = shell_exec("sudo -uhorice ".base_path()."/../website-auto-update.sh ".base_path());
        Log::info($info);
    }

}
