<?php

namespace App\Providers;

use App\Models\EventGroupLogModel;
use App\Models\GroupModel;
use App\Models\RebateActivityModel;
use App\Models\RosterCourseLogModel;
use App\Models\RosterModel;
use App\Models\UserEnrollModel;
use App\Models\UserPayLogModel;
use App\Models\UserPayModel;
use App\Models\UserRegistrationModel;
use App\Observers\EventGroupLogObserver;
use App\Observers\GroupObserver;
use App\Observers\RebateObserver;
use App\Observers\RosterCourseLogObserver;
use App\Observers\RosterObserver;
use App\Observers\UserEnrollObserver;
use App\Observers\UserPayLogObserver;
use App\Observers\UserPayObserver;
use App\Observers\UserRegistrationObserver;
use Curl\Curl;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
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
        //监听新量事件
        RosterModel::observe(RosterObserver::class);
        //监听群事件
        GroupModel::observe(GroupObserver::class);
        //监听开课事件
        RosterCourseLogModel::observe(RosterCourseLogObserver::class);
        //监听群日志事件
        EventGroupLogModel::observe(EventGroupLogObserver::class);
        //观察器 优惠活动表
        RebateActivityModel::observe(RebateObserver::class);
        //观察器 支付报名
        UserRegistrationModel::observe(UserRegistrationObserver::class);
        //一级支付记录
        UserPayModel::observe(UserPayObserver::class);
        //二级支付记录
        UserPayLogModel::observe(UserPayLogObserver::class);
        //主报名支付
        UserEnrollModel::observe(UserEnrollObserver::class);
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
        //处理列队前
        Queue::before(function (JobProcessing $event) {
            Log::info("开始进入列队:".$event->connectionName);
        });
        //处理列队后
        Queue::after(function (JobProcessed $event) {
            Log::info("列队处理完成:".$event->connectionName);
        });
        //队列任务失败日志
        Queue::failing(function (JobFailed $event) {
            Log::error($event->connectionName."队列任务失败:".$event->exception->getMessage());
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
        $this->app->singleton("curl",function(){
            return new Curl();
        });
    }
}
