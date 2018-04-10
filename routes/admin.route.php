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
Route::post("user/open-course-head","UserController@postOpenCourseHead")->name("admin.user.open-course-head");

//学员量管理
Route::get("roster/list","Roster\IndexController@getList")->name("admin.roster.list");
//开课记录
Route::get("roster/course/list","Roster\IndexController@getCourseList")->name("admin.roster.course.list");
//进群记录
Route::get("roster/group/list","Roster\IndexController@getGroupLogList")->name("admin.roster.group.log.list");
//量的其它操作
Route::post("roster/change-group-status","Roster\IndexController@changeGroupStatus")->name("admin.roster.change-group-status");
Route::get("roster/add","Roster\IndexController@getAdd")->name("admin.roster.add");
Route::post("roster/add","Roster\IndexController@postAdd")->name("admin.roster.add.post");
Route::get("roster/user/add","Roster\IndexController@getUserAdd")->name("admin.roster.user.add");
Route::get("roster/user/addwx","Roster\IndexController@getUserAddWx")->name("admin.roster.user.addwx");
Route::post("roster/user/add","Roster\IndexController@postUserAdd")->name("admin.roster.user.add.post");
Route::get("roster/statistics/seoer","Roster\StatisticsController@getSeoerStatistics")->name("admin.roster.statistics.seoer");
Route::get("roster/statistics/adviser","Roster\StatisticsController@getAdviserStatistics")->name("admin.roster.statistics.adviser");
Route::get("roster/list/user","Roster\IndexController@getUserList")->name("admin.roster.list.user");
Route::get("roster/list/one","Roster\IndexController@getSelectOne")->name("admin.roster.list.one");

//关单信息管理
Route::get("roster/follow/index","Roster\FollowController@getIndex")->name("admin.roster.follow.index");
Route::get("roster/follow/list/{user_id}","Roster\FollowController@getList")->name("admin.roster.follow.list");
Route::get("roster/follow/user/list","Roster\FollowController@getUserList")->name("admin.roster.follow.list.user");
Route::get("roster/follow/add/{roster_id}","Roster\FollowController@getAdd")->name("admin.roster.follow.add");
Route::get("roster/follow/add/{roster_id}/{follow_id}","Roster\FollowController@getEdit")->name("admin.roster.follow.edit");
Route::post("roster/follow/save","Roster\FollowController@postSave")->name("admin.roster.follow.save.post");
//形成专属注册链接
Route::post("roster/index/set-reg-url","Roster\IndexController@postSetRegUrl")->name("admin.roster.index.set-reg-url");
//开通课程
Route::post("roster/index/open-course","Roster\IndexController@postOpenCourse")->name("admin.roster.index.open-course");
Route::post("roster/index/upload-excel","Roster\IndexController@postUploadExcel")->name("admin.roster.index.upload-excel");
Route::get("roster/index/export-error-user","Roster\IndexController@getExportErrorUser")->name("admin.roster.index.export-error-user");

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
Route::post("registration/post-package-list","RegistrationController@postPackageList")->name("admin.registration.post-package-list");
//支付用户统计列表
Route::get("registration/list-user","RegistrationController@getUserList")->name("admin.registration.list.user");
//支付记录列表
Route::get("registration/list-pay","RegistrationController@getPayList")->name("admin.registration.list.pay");
//支付记录详情
Route::get("registration/list-detail","RegistrationController@getListDetail")->name("admin.registration.list.detail");
Route::post("registration/mod-field","RegistrationController@postModField")->name("admin.registration.mod-field");
Route::post("registration/mod-log-field","RegistrationController@postModLogField")->name("admin.registration.mod-log-field");;
Route::post("registration/delete","RegistrationController@postDelete")->name("admin.registration.delete");

/**
 * 课程套餐
 */
//课程套餐列表
Route::get("pay/package/list","Pay\PackageController@getList")->name("admin.pay.package.list");
//修改课程套餐
Route::get("pay/package/add","Pay\PackageController@getAdd")->name("admin.pay.package.add");
//修改课程套餐
Route::get("pay/package/edit","Pay\PackageController@getEdit")->name("admin.pay.package.edit");
Route::post("pay/package/save","Pay\PackageController@postSave")->name("admin.pay.package.save");
Route::post("pay/package/delete","Pay\PackageController@postDelete")->name("admin.pay.package.delete");

/**
 * 课程优惠活动
 */
Route::get("pay/rebate/list","Pay\RebateController@getList")->name("admin.pay.rebate.list");
Route::get("pay/rebate/add","Pay\RebateController@getAdd")->name("admin.pay.rebate.add");
Route::get("pay/rebate/edit","Pay\RebateController@getEdit")->name("admin.pay.rebate.edit");
Route::post("pay/rebate/save","Pay\RebateController@postSave")->name("admin.pay.rebate.save");
Route::post("pay/rebate/delete","Pay\RebateController@postDelete")->name("admin.pay.rebate.delete");
