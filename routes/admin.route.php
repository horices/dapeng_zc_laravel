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
Route::get("group/save","GroupController@postSave")->name("admin.group.save");
//用户管理
Route::get("user/list","UserController@getList")->name("admin.user.list");
Route::get("user/add","UserController@getAdd")->name("admin.user.add");
Route::get("user/{uid}","UserController@getEdit")->name("admin.user.edit");
Route::post("user/save","UserController@postSave")->name("admin.user.save");