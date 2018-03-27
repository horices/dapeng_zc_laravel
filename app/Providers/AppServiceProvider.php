<?php

namespace App\Providers;

use App\Models\EventGroupLogModel;
use App\Models\GroupModel;
use App\Models\RosterCourseLogModel;
use App\Observers\EventGroupLogObserver;
use App\Observers\GroupObserver;
use App\Observers\RosterCourseLogObserver;
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
    public function boot()
    {
        //列举所有的常量到模板中
        //是否注册
        View::share("registerStatus",app('status')->getRegisterStatus());
        //开通课程状态
        View::share("courseType",app('status')->getCourseType());
        //提交量的类型
        View::share("rosterType",app('status')->getRosterType());
        //群状态
        View::share("groupStatus",app('status')->getGroupStatus());
        //用户级别
        View::share("userGradeList",app('status')->getUserGradeList());
        //私聊深度
        View::share("rosterDeepLevel",app('status')->getRosterDeepLevel());
        //用户意向
        View::share("rosterIntention",app('status')->getRosterIntention());
        //导航菜单
        View::share("navList",app('status')->getLeftNavList());
        //获取支付方式列表
        View::share("payTypeList",app('status')->getPayTypeList());

        //监听用户用事件 
        UserModel::observe(UserObserver::class);
        //监听群事件
        GroupModel::observe(GroupObserver::class);
        //监听开课事件
        RosterCourseLogModel::observe(RosterCourseLogObserver::class);
        //监听群日志事件
        EventGroupLogModel::observe(EventGroupLogObserver::class);
        //记录SQL日志
        DB::listen(function($query){
            $sql = $query->sql;
            foreach ($query->bindings as $v){
                $sql = Str::replaceFirst('?',array_shift($query->bindings),$sql);
            }
            $sql = "耗时: ".$query->time ." ".$sql;
            Log::info("\r\n\r\n===================================================================\r\n");
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
        $this->app->singleton("status",function(){
            return new \App\Http\Controllers\Admin\BaseController();
        });
    }
}
