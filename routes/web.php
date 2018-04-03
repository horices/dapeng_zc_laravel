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
Route::group(['prefix'=>'admin','namespace'=>"Admin"], function(){
    //不需要登陆验证过滤的地址
    Route::get("auth/login","AuthController@getLogin")->name("admin.auth.login");
    Route::post("auth/login","AuthController@postLogin")->name("admin.auth.login.post");
    Route::post("auth/reg","AuthController@postReg")->name("admin.auth.reg.post");
    Route::post("auth/sendsms","AuthController@postSendSms")->name("admin.auth.send.sms");
});
Route::group(['prefix'=>'admin','namespace'=>"Admin",'middleware'=>[BackendAuth::class,\App\Http\Middleware\LowerUrl::class]], function(){
    include("admin.route.php");
});
Route::group(['prefix'=>'App','namespace'=>"Notify"], function(){
    Route::post("Index/index.html","DapengNotifyController@reg")->middleware(\App\Http\Middleware\NotifyValidate::class);
    Route::post("Index/openCourse.html","DapengNotifyController@openCourse")->middleware(\App\Http\Middleware\NotifyValidate::class);
    Route::post("Index/closeCourse.html","DapengNotifyController@closeCourse")->middleware(\App\Http\Middleware\NotifyValidate::class);
    Route::post("QQGroupRebotEvent/index.html","QQGroupEventNotifyController@index");
});

Route::group(['prefix'=>'Api','namespace'=>"Api"], function(){
    Route::get("User/getInfo","RosterController@getInfo");
    Route::post("User/addQQ","RosterController@add");
    Route::post("User/setInfo","RosterController@setInfo");
});
//Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
