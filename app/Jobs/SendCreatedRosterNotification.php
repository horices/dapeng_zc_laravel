<?php

namespace App\Jobs;

use App\Models\RosterModel;
use App\Utils\Util;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendCreatedRosterNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $roster;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $roster)
    {
        $this->roster = collect($roster);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //发送到设计学院
        $url = Util::getWebSiteConfig("ZC_URL.".Util::SCHOOL_NAME_SJ.".".Util::getCurrentBranch(),false).route("notify.zc.roster.created",[],false);
        //$url = "http://local.dp_zc_sj.com".route("notify.zc.roster.created",[],false);
        $data["roster_type"] = $this->roster->get("roster_type");
        $data['roster_no'] = $this->roster->get("roster_no");
        $data['addtimes'] = $this->roster->get("addtimes");
        $data['timestamp'] = time();
        $data['sign'] = Util::makeSign($data);
        //if($data['addtimes'] > 1){
            SendNotification::dispatch($url,$data);
        //}
    }
}
