<?php
use Illuminate\Support\Facades\Route;

Route::get("index/index","IndexController@getIndex")->name("admin.index.index");

//上传
Route::get("upload","IndexController@getUpload")->name("admin.upload");
Route::post("upload","IndexController@postUpload");

//群管理
Route::get("group/list","GroupController@getList")->name("admin.group.list");
Route::get("group/add","GroupController@getAdd")->name("admin.group.add");
Route::get("group/{group_id}","GroupController@getEdit")->name("admin.group.edit");
Route::post("group/save","GroupController@postSave")->name("admin.group.save");
//用户管理
Route::get("user/list","UserController@getList")->name("admin.user.list");
Route::get("user/add","UserController@getAdd")->name("admin.user.add");
Route::get("user/{uid}","UserController@getEdit")->name("admin.user.edit");
Route::post("user/save","UserController@postSave")->name("admin.user.save");

//学员量管理
Route::get("roster/list","RosterController@getList")->name("admin.roster.list");
Route::get("roster/add","RosterController@getAdd")->name("admin.roster.add");

//公共操作
Route::get("index/select_seoer","BaseController@getSelectSeoer")->name("admin.public.select_seoer");
Route::get("index/select_adviser","BaseController@getSelectAdviser")->name("admin.public.select_adviser");