<?php


use App\Http\Middleware\BackendAuth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/',function(){
    return redirect("/admin/index/index");
});
//后台页面不需要登陆即可访问
Route::group(['prefix'=>'admin','namespace'=>"Admin"], function(){
    //不需要登陆验证过滤的地址
    Route::get("auth/login","AuthController@getLogin")->name("admin.auth.login");
    Route::post("auth/login","AuthController@postLogin")->name("admin.auth.login.post");
    Route::post("auth/reg","AuthController@postReg")->name("admin.auth.reg.post");
    Route::post("auth/sendsms","AuthController@postSendSms")->name("admin.auth.send.sms");
});
//管理员后台,需要验证登陆
Route::group(['prefix'=>'admin','namespace'=>"Admin",'middleware'=>[BackendAuth::class,\App\Http\Middleware\LowerUrl::class]], function(){
    include("admin.route.php");
});
//通知接口地址(大鹏主站开课通知,QQ群机器人通知)
Route::group(['prefix'=>'App','namespace'=>"Notify"], function(){
    Route::post("Index/index.html","DapengNotifyController@reg")->middleware(\App\Http\Middleware\NotifyValidate::class)->name("notify.dapeng.reg");
    Route::post("Index/openCourse.html","DapengNotifyController@openCourse")->middleware(\App\Http\Middleware\NotifyValidate::class)->name("notify.dapeng.course.open");
    Route::post("Index/openCourseMulti.html","DapengNotifyController@openCourseMulti")->middleware(\App\Http\Middleware\NotifyValidate::class)->name("notify.dapeng.course.open.multi");
    Route::post("Index/closeCourse.html","DapengNotifyController@closeCourse")->middleware(\App\Http\Middleware\NotifyValidate::class)->name("notify.dapeng.course.close");
    Route::post("QQGroupRebotEvent/index.html","QQGroupEventNotifyController@index")->name("notify.dapeng.index");;
    Route::post("git/coding","GitNotifyController@coding")->name("notify.git.coding");
});
//向外提供接口的地址(外部添加新量和修改部分信息)
Route::group(['prefix'=>'Api','namespace'=>"Api"], function(){
    Route::get("User/getInfo","RosterController@getInfo")->name("api.roster.info.get");
    Route::post("User/addQQ","RosterController@add")->name("api.roster.add");
    Route::post("User/setInfo","RosterController@setInfo")->name("api.roster.info.set");
    Route::post("User/checkRosterStatus","RosterController@checkRosterStatus")->name("api.roster.status.check");
});
//Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
