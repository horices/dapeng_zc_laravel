<?php
use Illuminate\Support\Facades\Route;

Route::get("index/index","IndexController@getIndex")->name("admin.index.index");

//上传
Route::get("upload","IndexController@getUpload")->name("admin.upload");
Route::post("upload","IndexController@postUpload")->name("admin.upload.post");

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
Route::get("roster/list","RosterController@getList")->name("admin.roster.list");
Route::get("roster/add","RosterController@getAdd")->name("admin.roster.add");
Route::post("roster/add","RosterController@postAdd")->name("admin.roster.add.post");
Route::get("roster/user/add","RosterController@getUserAdd")->name("admin.roster.user.add");
Route::post("roster/user/add","RosterController@postUserAdd")->name("admin.roster.user.add.post");
Route::get("roster/statistics/seoer","Roster\StatisticsController@getSeoerStatistics")->name("admin.roster.statistics.seoer");
Route::get("roster/statistics/adviser","Roster\StatisticsController@getAdviserStatistics")->name("admin.roster.statistics.adviser");
Route::get("roster/list/user","RosterController@getUserList")->name("admin.roster.list.user");

//关单信息管理
Route::get("roster/follow/index","Roster\FollowController@getIndex")->name("admin.roster.follow.index");
Route::get("roster/follow/list/{user_id}","Roster\FollowController@getList")->name("admin.roster.follow.list");
Route::get("roster/follow/add/{roster_id}","Roster\FollowController@getAdd")->name("admin.roster.follow.add");
Route::get("roster/follow/add/{roster_id}/{follow_id}","Roster\FollowController@getEdit")->name("admin.roster.follow.edit");
Route::post("roster/follow/save/{roster_id}/{follow_id?}","Roster\FollowController@postSave")->name("admin.roster.follow.edit.post");


//公共操作
Route::get("index/select_seoer","BaseController@getSelectSeoer")->name("admin.public.select_seoer");
Route::get("index/select_adviser","BaseController@getSelectAdviser")->name("admin.public.select_adviser");
Route::get("index/select_group","BaseController@getSelectGroup")->name("admin.public.select_group");
Route::get("auth/logout","AuthController@getLogout")->name("admin.auth.logout");
Route::get("index/account","IndexController@getAccount")->name("admin.public.account");
Route::post("index/account","IndexController@postAccount");
/*支付系统*/
//支付统计
Route::get("registration/add","RegistrationController@getAdd")->name("admin.registration.add");
//验证 支付 是否已存在 并且获取信息
Route::post("registration/has-registration","RegistrationController@postHasRegistration")->name("admin.registration.has-registration");
//写入支付记录
Route::post("registration/add-registration","RegistrationController@postAddRegistration")->name("admin.registration.add-registration");
//更新 报名
Route::post("registration/update-registration","RegistrationController@postUpdateRegistration")->name("admin.registration.update-registration");
//异步获课程取套餐列表
Route::post("registration/get-package-list","RegistrationController@postPackageList")->name("admin.registration.get-package-list");
//支付用户统计列表
Route::get("registration/user-list","RegistrationController@getUserList")->name("admin.registration.user-list");