<?php
use Illuminate\Support\Facades\Route;

Route::get("User/getInfo","RosterController@getInfo")->name("api.roster.info.get");
Route::post("User/addQQ","RosterController@add")->name("api.roster.add");
Route::post("User/setInfo","RosterController@setInfo")->name("api.roster.info.set");
Route::post("User/checkRosterStatus","RosterController@checkRosterStatus")->name("api.roster.status.check");
Route::post("user/login","UserController@postLogin")->name("api.user.login");