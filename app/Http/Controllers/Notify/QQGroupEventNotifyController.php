<?php

namespace App\Http\Controllers\Notify;

use App\Models\EventGroupLogModel;
use App\Models\RosterModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class QQGroupEventNotifyController extends BaseController
{
    private $_GROUP_STATUS = [
        "ClusterRequestJoin"    =>  1,
        "ClusterMemberJoin"     =>  2,
        "ClusterMemberExit"     =>  3,
        "ClusterMemberKick"     =>  5
    ];
    private $_EVENTS = [
        //"ClusterIM"             =>  "即时通讯",
        //"FriendStatusChange"    =>  "改变状态",
        "ClusterRequestJoin"    =>  "请求入群",
        "ClusterMemberJoin"     =>  "加入群",
        "ClusterMemberExit"     =>  "已退群",
        "ClusterMemberKick"     =>  "已被踢"
    ];
    /**
     *
     */
    function index(Request $request){
        $roster = RosterModel::where("qq",$request->get("Sender"))->orderBy("id","desc")->first();
        if(!$roster){
            throw new NotAcceptableHttpException("QQ号不存在");
        }
        $data['roster_id'] = $roster->id;
        $data['group_status'] = $this->_GROUP_STATUS[$request->get("Event")];
        $data['qq'] = $request->get("Sender");
        $data['qq_nickname'] = $request->get("SenderName");
        $data['group'] = $request->get("GroupId");
        $data['group_name'] = $request->get("GroupName");
        $data['operator'] = $request->get("Operator");
        $data['operator_name'] = $request->get("OperatorName");
        $data['robot_qq'] = $request->get("RobotQQ");
        $data['addtime']    =  time();
        if(EventGroupLogModel::create($data) == false){
            Log::error("添加记录失败");
        }
    }
}
