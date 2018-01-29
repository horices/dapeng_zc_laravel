<?php


use Illuminate\Support\Facades\Route;
use function Composer\Autoload\includeFile;
use App\Http\Middleware\BackendAuth;

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
Route::group(['prefix'=>'admin','namespace'=>"Admin",'middleware'=>[BackendAuth::class]], function(){
    //include("admin.route.php");
    Route::get("index/index","IndexController@getIndex")->name("admin.index.index");
    Route::get("group/list","GroupController@getList")->name("admin.group.list");
    Route::get("index/index","IndexController@getIndex")->name("admin.index.index");
    //用户管理
    Route::get("user/list","UserController@getList")->name("admin.user.list");
    Route::get("user/add","UserController@getAdd")->name("admin.user.add");
    Route::get("user/{uid}","UserController@getEdit")->name("admin.user.edit");
    Route::post("user/save","UserController@postSave")->name("admin.user.save");
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
