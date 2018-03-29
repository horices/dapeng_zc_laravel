<?php

namespace App\Listeners;

use App\Api\DapengUserApi;
use App\Events\RevisingAdvisor;
use App\Exceptions\UserValidateException;
use App\Utils\Util;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RevisingAdvisorListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RevisingAdvisor  $event
     * @return void
     */
    public function handle(RevisingAdvisor $event)
    {
        //如果没有QQ号需要转移，则跳过
        if(!$event->qqList){
            return ;
        }
        $data = DapengUserApi::revisingAdvisor([
            'qqList'=>collect($event->qqList)->implode(','),
            'schoolId'  => 'SJ',
            'newAdviserId'  => $event->newAdviser->dapeng_user_id
            ]);
        if($data['code'] == Util::FAIL){

            throw new UserValidateException($data['msg']);
        }
    }
}
