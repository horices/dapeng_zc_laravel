<?php

namespace App\Listeners;

use App\Models\RosterModel;
use App\Utils\Api\DapengUserApi;
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
        if(!$event->qqList->count()){
            return ;
        }
        $data = DapengUserApi::revisingAdvisor([
            'qqList'=>collect($event->qqList)->implode(','),
            'schoolId'  => Util::getSchoolId(),
            'newAdviserId'  => $event->newAdviser->dapeng_user_id
            ]);
        if($data['code'] == Util::FAIL){
            throw new UserValidateException($data['msg']);
        }
        //更换 last_adviser_id
        if(RosterModel::where([
            "adviser_id"=>$event->oldAdviserId,
            "qq_group_id"   => $event->groupId
        ])->update([
            'last_adviser_id'   =>  $event->newAdviser->uid,
            'last_adviser_name'   =>  $event->newAdviser->name,
        ]) === false){
            throw new UserValidateException("更新用户 last_adviser_id失败");
        }
    }
}
