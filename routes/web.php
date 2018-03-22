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
    Route::post("auth/login","AuthController@postLogin");
});
Route::group(['prefix'=>'admin','namespace'=>"Admin",'middleware'=>[BackendAuth::class,\App\Http\Middleware\LowerUrl::class]], function(){
    include("admin.route.php");
});
Route::group(['prefix'=>'App','namespace'=>"Notify",'middleware'=>[\App\Http\Middleware\NotifyValidate::class]], function(){
    Route::post("Index/index","DapengNotifyController@reg");
    Route::post("Index/openCourse","DapengNotifyController@openCourse");
    Route::post("Index/closeCourse","DapengNotifyController@closeCourse");

});
//Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
