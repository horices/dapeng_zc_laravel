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
Route::post("group/change_open_status","GroupController@postSave")->name("admin.group.change_open_status");

//用户管理
Route::get("user/list","UserController@getList")->name("admin.user.list");
Route::get("user/add","UserController@getAdd")->name("admin.user.add");
Route::get("user/{uid}","UserController@getEdit")->name("admin.user.edit");
Route::post("user/save","UserController@postSave")->name("admin.user.save");

//学员量管理
Route::get("roster/list/{export?}","RosterController@getList")->name("admin.roster.list");
Route::get("roster/add","RosterController@getAdd")->name("admin.roster.add");
Route::post("roster/add","RosterController@postAdd");

//关单信息管理
Route::get("roster/follow/index","Roster\FollowController@getIndex")->name("admin.roster.follow.index");
Route::get("roster/follow/list/{user_id}","Roster\FollowController@getList")->name("admin.roster.follow.list");
Route::get("roster/follow/add/{roster_id}","Roster\FollowController@getAdd")->name("admin.roster.follow.add");
Route::get("roster/follow/add/{roster_id}/{follow_id}","Roster\FollowController@getEdit")->name("admin.roster.follow.edit");
Route::post("roster/follow/save/{roster_id}/{follow_id?}","Roster\FollowController@postSave");


//公共操作
Route::get("index/select_seoer","BaseController@getSelectSeoer")->name("admin.public.select_seoer");
Route::get("index/select_adviser","BaseController@getSelectAdviser")->name("admin.public.select_adviser");
Route::get("index/select_group","BaseController@getSelectGroup")->name("admin.public.select_group");
Route::get("roster/statistics/seoer_statistics","Roster\StatisticsController@getSeoerStatistics")->name("admin.public.seoer_statistics");
Route::get("roster/statistics/adviser_statistics","Roster\StatisticsController@getAdviserStatistics")->name("admin.public.adviser_statistics");

/*支付系统*/
//支付统计
Route::get("registration/add","RegistrationController@getAdd");
//验证 支付 是否已存在 并且获取信息
Route::post("registration/has-registration","RegistrationController@postHasRegistration");
//写入支付记录
Route::post("registration/add-registration","RegistrationController@postAddRegistration");
//更新 报名
Route::post("registration/update-registration","RegistrationController@postUpdateRegistration");
//异步获课程取套餐列表
Route::post("registration/get-package-list","RegistrationController@postPackageList");
//支付用户统计列表
Route::get("registration/user-list","RegistrationController@getUserList");