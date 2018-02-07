<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\BaseController;
use App\Models\UserModel;
use App\Observers\UserObserver;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(BaseController $controller)
    {
        //列举所有的常量到模板中
        //用户级别
        View::share("rosterType",$controller->getRosterType());
        //用户级别
        View::share("userGradeList",$controller->getUserGradeList());
        //导航菜单
        View::share("navList",$controller->getLeftNavList());
        //监听用户用事件 
        UserModel::observe(UserObserver::class);

        //记录SQL日志
        DB::listen(function($query){
            $sql = $query->sql;
            foreach ($query->bindings as $v){
                $sql = Str::replaceFirst('?',array_shift($query->bindings),$sql);
            }
            $sql = "耗时: ".$query->time ." ".$sql;
            Log::info($sql);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(BaseController::class,function(){
            return new \App\Http\Controllers\Admin\BaseController();
        });
    }
}
