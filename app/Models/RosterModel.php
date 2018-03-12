<?php

namespace App\Models;


use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RosterModel extends BaseModel
{
    protected $table = "user_roster";

    function getRosterNoAttribute(){
        return $this->type == 1 ? $this->qq: $this->wx;
    }
    function getIsRegTextAttribute(){
        return app("status")->getRegisterStatus()[$this->is_reg];
    }
    function getRosterTypeTextAttribute(){
        return app("status")->getRosterType()[$this->type];
    }
    function getCourseTypeTextAttribute(){
        return app("status")->getCourseType()[$this->course_type];
    }
    function getGroupStatusTextAttribute(){
        return app("status")->getGroupStatus()[$this->group_status];
    }
    function getAddtimeTextAttribute($v){
        return date('m-d',$this->addtime)."<br />".date('H:i',$this->addtime);
    }

    /**
     * 群信息
     */
    function group_info(){
        return $this->hasOne(GroupModel::class,'id','qq_group_id');
    }

    /**
     * 推广专员信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function seoer(){
        return $this->belongsTo(UserModel::class,'inviter_id','uid');
    }

    /**
     * 课程顾问信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function adviser(){
        return $this->belongsTo(UserModel::class,'last_adviser_id','uid');
    }
    /**
     * 群日志
     */
    function group_event_log(){
        return $this->hasMany(EventGroupLogModel::class,'roster_id','id');
    }
    //最后一次群时间
    function last_group_time(){
        //return $this->hasOne(EventGroupLogModel::class,"roster_id")->orderBy("id","desc")->limit(1);
        return $this->belongsTo(EventGroupLogModel::class,'id','roster_id');//->orderBy("id","desc")->limit(1);
    }

    /**
     * 添加一个新量，自带验证功能
     * @param $data array 要添加的数据
     *      keys:
     *              roster_no:新量号码，QQ或微信号
     *              roster_type:新量类型，[1:QQ号，２：微信号]
     *              seoer_id:推广专员ID
     *              qq_group_id：群ID号，不传该值时，系统会自带分配一个群
     */
    public static function addRoster(array $data){
        /**
         * @var $validator \Illuminate\Validation\Validator;
         */
        $validator = Validator::make($data,[
            'roster_no' =>  'required',
            'roster_type'   =>  'required|in:1,2',
            //'qq_group_id'   =>  'required|exists:user_qqgroup,id',
            'seoer_id'   =>  'required|exists:user_headmaster,uid'
        ],[
            'roster_no.required' =>  '请输入号码',
            'roster_no.unique'  =>  '该号码已存在',
            'roster_type.required'  =>  '请选择正确的提交类型',
            'roster_type.in'    => "类型只能为1或2",
            'qq_group_id.required'   =>  '请选择QQ群',
            'qq_group_id.exists'   =>   'QQ群不存在',
            'seoer_id.required'  =>  '请选择推广专员',
            'seoer_id.exists'   => '推广专员不存在'
        ]);
        $createData = [];   //重置要添加的数据
        $column = app('status')->getRosterTypeColumn($data['roster_type']);
        //该量已经存在，判断该量是否允许被添加,添加验证规则，返回 false 表示允许添加  返回 true 表示进需要行验证，不能进行添加
        $validator->sometimes('roster_no','unique:user_roster,'.$column,function($input) use($column , &$roster , &$createData){
            $roster = RosterModel::where($column,$input->roster_no)->orderBy('addtime','desc')->first();
            /**
             * 默认需要验证(不能进行重复添加)
             * 不需要验证的情况如下:
             *  1.三无状态(未进群，未注册，未开通),今天之内不可提交
             *  2.未注册，未开通,已被踢或已退群时时，允许立马被重新添加
             *  3.其它所有情况不允许提交，但是添加活量标识
             */
            //如果未找到该量的信息，则不需要验证,可直接添加
            if(!$roster){
                return false;
            }
            $flag = 0 ;
            if($roster->course_type == 0 && $roster->is_reg == 0 ){
                if($roster->group_status == 0  && date('Ymd') >  date('Ymd',$roster->addtime) ){
                    //第二天后可以重新提交
                    $flag = 1;
                }
                if($roster->group_status >= 3 ){
                    //已被踢或已退群，可以重新提交
                    $flag = 1;
                }
                if($flag == 1){
                    //需要对该QQ号取消所有的新量标识
                    if(RosterModel::where($column,$input->roster_no)->update(['flag'=>0]) === false){
                        Log::error("取消活量标识失败");
                    }
                    $createData['flag'] = $flag;    //标识为活量
                    $createData['addtimes'] = $roster->addtimes + 1;
                    //允许添加该量
                    return false;
                }
            }
            /**
             * 已进群，已注册或已开通的课程，添加为活量
             */
            if($roster->group_status == 2 || $roster->is_reg || $roster->course_type){
                $roster->flag = 2;  //标记为活量
                $roster->last_addtime = time(); //更新为活量的时间
                if($roster->save() == false){
                    Log::error("标识活量失败",$roster->toArray());
                }
            }
            return true;
        });
        //调用系统验证，验证失败时，抛出一个异常
        $validator->validate();
        //验证成功后，获取QQ群信息
        if(!$data['qq_group_id']){
            $groupInfo = app('status')->getNextGroupInfo($data['roster_type']);
            $data['qq_group_id'] = $groupInfo['id'];
        }
        $group = GroupModel::find($data['qq_group_id']);
        $createData[$column] = $data['roster_no'];
        $createData['type'] = $data['roster_type'];
        $createData['qq_group_id'] = $data['qq_group_id'];
        $createData['qq_group'] = $group->qq_group;
        $createData['inviter_id'] = $data['seoer_id'];
        $createData['inviter_name'] = $data['seoer_name'];
        $createData['adviser_id'] = $createData['last_adviser_id'] = $group->leader_id;
        $createData['adviser_name'] = $createData['last_adviser_name'] = $group->user->name;
        $createData['addtime'] = time();
        $query = static::query();
        return $query->create($createData);
    }
}
