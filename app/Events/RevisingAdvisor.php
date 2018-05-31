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
use Illuminate\Support\Facades\Validator;

class RevisingAdvisor
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $qqList = [];  //要转移的QQ号
    public $groupId = null; //群ID号
    public $newAdviser = null;  //新的课程顾问
    public $oldAdviserId = null;  //老的课程顾问ID
    /**
     * Create a new event instance.
     *
     * @param array $data 要修改的数据
     *  groupId    : 群ID
     *  newAdviserId  ：新课程顾问ID
     *  oldAdviserId  : 原课程顾问ID
     * @return void
     */
    public function __construct(array $data)
    {
        Validator::make($data,[
            "groupId"  =>  "required",
            "newAdviserId"  =>  "required",
            "oldAdviserId"  =>  "required"
        ],[
            "group_id.required" =>  "群ID号为必填项",
            "newAdviserId.required" =>  "未找到新的课程顾问ID",
            "oldAdviserId.required" =>  "未找到原课程顾问ID"
        ])->validate();
        $data = collect($data);
        $this->oldAdviserId = $data->get("oldAdviserId");
        $this->groupId = $data->get("groupId");
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
