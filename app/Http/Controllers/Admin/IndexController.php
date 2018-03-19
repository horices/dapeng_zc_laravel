<?php
namespace App\Http\Controllers\Admin;

use App\Models\RosterModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use App\Utils\Util;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;


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
    function postUpload(Request $request){
        $url = $request->file("upload")->store('upload');
        $url = "/".$url;
        $qrLink = Util::getQrContent(public_path().$url);
        $target = $request->post("target","upload");
        $target = $target{0} == '#'? substr($target, 1): $target;
        $callback = $request->post("callback","uploadCallback");
        return <<<JS
				<script>
				var target = parent.document.getElementById('{$target}');
				var json = {"url":"{$url}","qr_link":"{$qrLink}"};
				if(target){
					if(typeof(target) != "undefined"){
						if(typeof target.value == "undefined"){
							target.innerHTML='{$url}';
						}else{
							target.value = '{$url}';
						}
					}
				}
				if(typeof(parent.{$callback}) != "undefined")
					parent.{$callback}('{$url}',parent.currentUploadObj,json);
				parent.layer.closeAll();
				</script>
JS;
    }


    /**
     * 个人中心
     */
    function getAccount(){
        return view("admin.index.accounts");
    }
}

