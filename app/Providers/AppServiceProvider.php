<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\BaseController;
use App\Models\UserModel;
use App\Observers\UserObserver;

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
