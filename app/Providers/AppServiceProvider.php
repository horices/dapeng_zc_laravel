<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\BaseController;

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
        View::share("userGradeList",$controller->getUserGradeList());
        //导航菜单
        View::share("navList",$controller->getLeftNavList());
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
