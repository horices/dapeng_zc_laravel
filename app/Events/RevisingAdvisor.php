<?php

namespace App\Events;

use App\Exceptions\UserValidateException;
use App\Models\RosterModel;
use App\Models\UserModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RevisingAdvisor
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $qqList = [];  //要转移的QQ号
    public $newAdviser = null;  //新的课程顾问
    /**
     * Create a new event instance.
     *
     * @param array $data 要修改的数据
     *  group_id    : 群ID
     *  newAdviserId  ：新课程顾问ID
     * @return void
     */
    public function __construct(array $data)
    {
        $data = collect($data);
        $this->newAdviser = UserModel::find($data->get('newAdviserId'));
        if(!$this->newAdviser->dapeng_user_id){
            throw new UserValidateException("该课程顾问未绑定主站帐号");
        }
        $qqList = [];
        if($groupId = $data->get("groupId")){
            //查询该群下的所有QQ号
            $qqList = RosterModel::where('qq_group_id',$groupId)->pluck("qq");
        }
        $this->qqList = $qqList;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
