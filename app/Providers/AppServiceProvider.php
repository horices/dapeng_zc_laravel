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
        View::share("userGradeList",$controller->getUserGradeList());
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
