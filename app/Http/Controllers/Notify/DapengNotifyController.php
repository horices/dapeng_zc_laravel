<?php

namespace App\Http\Controllers\Notify;
use App\Exceptions\UserNotifyException;
use App\Exceptions\UserValidateException;
use App\Models\RosterCourseLogModel;
use App\Models\RosterModel;
use App\Utils\Util;
use Curl\Curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;


/**
 * 大鹏主站系统通知
 * Class DapengNotifyController
 * @package App\Http\Controllers\Notify
 */
class DapengNotifyController extends BaseController
{
    /**
     * 初始化实例对象,转发请求,因为每个通知的签名验证方式不统一，所以需要单独进行转发
     * DapengNotifyController constructor.
     * @param Curl $curl
     */
    function __construct(Curl $curl,Request $request)
    {
        parent::__construct($curl);
        //校验签名是否正确
        if($this->checkSign($request->get("sign")) === false){
        //    throw new UserNotifyException("签名错误");
        }
        $baseUrl = URL::route(Route::currentRouteName(),[],false);
        //设计学院正式站
        if(Util::getSchoolName() == Util::SCHOOL_NAME_SJ && Util::getCurrentBranch() == Util::MASTER){

            //通知设计学院测试站
            $host = Util::getWebSiteConfig('ZC_URL.'.Util::SCHOOL_NAME_SJ.".".Util::DEV,false);
            $request->merge(['sign'=>$this->makeSign(['url'=>$host.$baseUrl])]);
            $curl->post($host.$baseUrl,$request->all())->response;
            //通知美术学院正式站
            $host = Util::getWebSiteConfig('ZC_URL.'.Util::SCHOOL_NAME_MS.".".Util::MASTER,false);
            $request->merge(['sign'=>$this->makeSign(['url'=>$host.$baseUrl])]);
            $curl->post($host.$baseUrl,$request->all())->response;
        }
        //美术学院正式站
        if(Util::getSchoolName() == Util::SCHOOL_NAME_MS && Util::getCurrentBranch() == Util::MASTER){
            //通知美术学院测试站
            $host = Util::getWebSiteConfig('ZC_URL.'.Util::SCHOOL_NAME_MS.".".Util::DEV,false);
            $request->merge(['sign'=>$this->makeSign(['url'=>$host.$baseUrl])]);
            $curl->post($host.$baseUrl,$request->all())->response;
        }
    }

    /**
     * 注册通知
     */
    function reg(){
        $qq = Input::get("qq");
        $roster = RosterModel::where('qq',$qq)->orderBy("addtime","desc")->first();
        $roster->is_reg = 1;
        $roster->dapeng_reg_time = ceil(Input::get("dapeng_reg_time",time()*1000)/1000);
        if(!$roster->save()){
            Log::error("更新用户注册状态失败");
        }
        return Util::ajaxReturn(Util::SUCCESS,"success");
    }
    /**
     * 开课通知
     */
    function openCourse(Request $request,RosterCourseLogModel $courseLog){
        //查询这个QQ号的情况
        $roster = RosterModel::where("qq",Input::get("qq"))->orderBy("id","desc")->first();
        $request->merge([
            'action'=>1,
            'roster_id' => $roster->id,
            'addtime'   =>  time(),
            'user_type' => $roster->type,
            'course_type'=>app('status')->getCourseTypeColumnValue(Input::get('type'))
        ]);
        //修改注册状态和时间
        if(!$roster->dapeng_reg_time){
            $roster->is_reg = 1;
            $roster->dapeng_reg_time = ceil($request->get("dapeng_reg_time",time()*1000)/1000);
            $roster->save();
        }
        if(!RosterCourseLogModel::create(Input::get())){
            Log::error("开课通知处理失败");
        }
        return Util::ajaxReturn(Util::SUCCESS,"success");
    }

    /**
     * 开课通知
     */
    function openCourseMulti(Request $request,RosterCourseLogModel $courseLog){
        //查询这个QQ号的情况
        $roster = RosterModel::where("qq",Input::get("qq"))->orderBy("id","desc")->first();
        //修改注册状态和时间
        if(!$roster->dapeng_reg_time){
            $roster->is_reg = 1;
            $roster->dapeng_reg_time = ceil($request->get("dapeng_reg_time",time()*1000)/1000);
            $roster->save();
        }
        $data = $request->only("dapeng_user_id","qq","operator_id","opeartor_name","operator_ip");
        $data['course_type'] = app('status')->getCourseTypeColumnValue(Input::get('course_type'));
        $data['roster_id'] = $roster->id;
        $data['addtime'] = time();
        $data['user_type'] = $roster->type;
        $data['action'] = 1;
        $courseIdMap = collect(explode(',',trim($request->get("course_id_map"),'[]')));
        $courseTitleMap = collect(explode(',',trim($request->get("course_title_map"),'[]')));
        foreach($courseIdMap as $k=>$v){
            $data['course_id'] = $v;
            $data['course_name'] = $courseTitleMap->get($k);
            RosterCourseLogModel::create($data);
        }
        return Util::ajaxReturn(Util::SUCCESS,"success");
    }
    /**
     * 关闭课程通知
     */
    function closeCourse(Request $request,RosterCourseLogModel $courseLog){
        //查询这个QQ号的情况
        $roster = RosterModel::where("qq",Input::get("qq"))->orderBy("id","desc")->first();
        $request->merge([
            'action'=>2,
            'roster_id' => $roster->id,
            'addtime'   =>  time(),
            'user_type' => $roster->type,
            'course_type'=>app('status')->getCourseTypeColumnValue(Input::get('type'))
        ]);
        if(!RosterCourseLogModel::create(Input::get())){
            Log::error("开课通知处理失败");
        }
        return Util::ajaxReturn(Util::SUCCESS,"success");
    }

    /**
     * 生成一个新的签名
     * @param array $data
     *                  qq: QQ号
     *                  url: 完整的URL地址
     * @return string|void
     */
    function makeSign(array $data = [])
    {
        $data = collect($data);
        $qq = $data->get("qq",\Illuminate\Support\Facades\Request::get("qq"));
        $url =  $data->get("url",URL::route(Route::currentRouteName()));
        if(!$qq){
            throw new UserNotifyException("未找到QQ号");
        }
        return md5($url.'|'.$qq.'|dapeng');
    }

    /**
     * 校验签名是否正确
     * @param $sign
     * @return bool
     * @throws UserNotifyException
     */
    function checkSign($sign){
        //确保地址栏中不能有其它参数
        return $sign == $this->makeSign(['url'=>URL::full()]) && $sign == $this->makeSign();
    }
}
