<?php
namespace App\Http\Controllers\Admin;

use App\Http\Requests\ConfirmPasswordRequest;
use App\Models\RosterModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use App\Utils\Util;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


class IndexController extends BaseController
{
    function getIndex(){
        $grade = $this->getUserInfo()['grade'];
        $permission = $this->getPermission($grade);
        return redirect(route($permission['default_route']));
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
        return view("admin.index.accounts",[
            'userInfo' =>   $this->getUserInfo()
        ]);
    }

    /**
     * 修改用户密码
     */
    function postAccount(ConfirmPasswordRequest $request){
        if($request->post("password")){
            //修改用户密码
            $userInfo = $this->getUserInfo();
            $userInfo->password = md5($request->post("password"));
            if(!$userInfo->save()){
                Log::error("更新用户密码失败",['data'=>$request->all()]);
            }
        }
        $return['code'] = Util::SUCCESS;
        $return['msg'] = "保存成功";
        return response()->json($return);
    }
}

