<?php

namespace App\Jobs;

use App\Models\RosterModel;
use App\Utils\Util;
use Curl\Curl;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendOpenCourseNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $roster = null;  //当前量的信息
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(RosterModel $roster)
    {
        $this->roster = $roster;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $curl = app("curl");
        $data['qq'] = $this->roster->qq;
        $url = Util::getWebSiteConfig("ZC_URL.".Util::SCHOOL_NAME_SJ.".".Util::MASTER,false).route("notify.dapeng.course.open",[],false);
        //$url = route("notify.dapeng.course.open",[],true);
        $data['sign'] = md5($url."|".$data['qq']."|dapeng");
        $data['course_type'] = 'trial';
        $data['type'] = 'trial';
        $data['course_id'] = '00000001';
        $data['course_name'] = '大鹏所有试学课';
        $data['operator_id'] = '';
        $data['operator_name'] = '';
        $data['operator_ip'] = '';
        SendNotification::dispatch($url,$data);
    }
}
