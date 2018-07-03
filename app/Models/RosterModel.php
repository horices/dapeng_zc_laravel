<?php

namespace App\Models;


use App\Exceptions\UserValidateException;
use App\Http\Controllers\BaseController;
use App\Utils\Api\ZcApi;
use App\Utils\Util;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RosterModel extends BaseModel
{
    protected $table = "user_roster";
    protected $appends = [
        'is_reg_text',
        'roster_no',
        'roster_type',
        'roster_type_text',
        'course_type_text',
        'group_status_text',
        'addtime_export_text',
        'reg_url_prama'
    ];

    function getRosterNoAttribute(){
        return $this->type == 1 ? $this->qq: $this->wx;
    }
    function getIsRegTextAttribute(){
        if($this->is_reg !== null){
            return app("status")->getRegisterStatus()[$this->is_reg];
        }
    }
    function getRosterTypeAttribute(){
        if($this->type !== null) {
            return $this->type;
        }
    }
    function getRosterTypeTextAttribute(){
        if($this->roster_type !== null) {
            return app("status")->getRosterType()[$this->roster_type];
        }
    }
    function getCourseTypeTextAttribute(){
        if($this->course_type !== null) {
            return app("status")->getCourseType()[$this->course_type];
        }
    }
    function getGroupStatusTextAttribute(){
        if($this->group_status !== null) {
            $status = app("status")->getGroupStatus();
            if($this->roster_type == 2){
                $status[1] = '等待添加';
                $status[2] = "已添加";
            }
            return $status[$this->group_status];
        }
    }
    function getAddtimeExportTextAttribute($v){
        if($this->addtime !== null) {
            return date('Y-m-d H:i:s', $this->addtime);
        }
    }
    function getAddtimeTextAttribute($v){
        if($this->addtime !== null) {
            return date('m-d', $this->addtime) . "<br />" . date('H:i', $this->addtime);
        }
    }
    function getDapengRegTimeExportTextAttribute(){
        if($this->dapeng_reg_time){
            return date('Y-m-d H:i:s',$this->dapeng_reg_time);
        }
    }

    /**
     * 获取专属注册链接的地址参数
     * @return string
     */
    function getRegUrlPramaAttribute(){
        $qqCrypt = Util::think_encrypt($this->qq);
        $stamp = time();
        $data = [$qqCrypt,$this->group->group_name,time()];
        $str = http_build_query($data);
        $signData = md5('8934031001776A04444F72154425DDBC'.$str.'8934031001776A04444F72154425DDBC');
        return Util::getWapHost()."/login/register-zc?qqCrypt=".$qqCrypt."&className=".$this->group->group_name."&rosterId=".$this->id."&stamp=".$stamp."&sign=".$signData."&schoolId=".strtolower(Util::getSchoolName())."&rosterId=".$this->id;
        //return Util::getShorturl(Util::getWapHost()."/login/register-zc?qqCrypt=".$qqCrypt."&className=".$this->group->group_name."&rosterId=".$this->id."&stamp=".$stamp."&sign=".$signData."&schoolId=".strtolower(Util::getSchoolName()));
    }

    /**
     * 根据类型返回账号
     * @return mixed
     */
    function getAccountAttribute(){
        return ($this->type == 1) ? $this->qq : $this->wx;
    }

    /**
     * 群信息
     */
    function group(){
        return $this->belongsTo(GroupModel::class,'qq_group_id')->withDefault();
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
        return $this->belongsTo(UserModel::class,'last_adviser_id','uid')->withDefault();
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
     * 验证数据 不能调用接口，防止进入死循环
     * @param array $data
     * @param boolean $multiSchool 是否进行多学院认证,默认为否
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function validateRosterData(array $data,$multiSchool = false){
        /**
         * @var $validator \Illuminate\Validation\Validator;
         */
        Validator::make($data,[
            'roster_type'   =>  'required|in:1,2',
        ],[
            'roster_type.required'  =>  '请选择正确的提交类型',
            'roster_type.in'    => "类型只能为1:QQ或2:微信",
        ])->validate();

        $columnText = app("status")->getRosterType()[$data['roster_type']];
        $validator = Validator::make($data,[
            'roster_no' =>  'required',
            'roster_type'   =>  'required|in:1,2',
            //'qq_group_id'   =>  'required|exists:user_qqgroup,id',
            'qq_group_id'   =>  'sometimes|required_if:from_type,6|exists:user_qqgroup,id',
            'seoer_id'   =>  'sometimes|required|exists:user_headmaster,uid'
        ],[
            'roster_no.required' =>  '请输入'.$columnText.'号码',
            'roster_no.unique'  =>  '该'.$columnText.'号码已存在',
            'roster_no.digits_between'  =>  'QQ号码必须为全数字，且长度在5-10位',
            'roster_no.regex'  =>  '该微信号不符合规则',
            'roster_type.required'  =>  '请选择正确的提交类型',
            'roster_type.in'    => "类型只能为1或2",
            'qq_group_id.required_if'   =>  '请选择'.$columnText.'群',
            'qq_group_id.exists'   =>   $columnText.'群不存在',
            'seoer_id.required'  =>  '请选择推广专员',
            'seoer_id.exists'   => '推广专员不存在'
        ]);
        $createData = [];   //重置要添加的数据
        /**
         * QQ提交时，需要全数字，并且长度为5-12
         * 微信提交时，
         */
        $validator->sometimes("roster_no","digits_between:5,10",function($input){
            return $input->roster_type == 1;
        });
        $validator->sometimes("roster_no",[
            "regex:/^[a-zA-Z][\w-]{5,19}$/isU"
        ],function($input){
            return $input->roster_type == 2;
        });
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
                    if(RosterModel::where($column,$input->roster_no)->update(['flag'=>0,'is_old'=>1]) === false){
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

        if($multiSchool){
            unset($temp);
            $temp['roster_type'] = $data['roster_type'];
            $temp['roster_no'] = $data['roster_no'];
            //验证其它学院，是否正常
            if(Util::getSchoolName() == Util::SCHOOL_NAME_SJ){
                //验证后，如果不能提交会有异常抛出，不需要处理成功时的情况
                ZcApi::validateRoster(Util::SCHOOL_NAME_MS,$temp);
                ZcApi::validateRoster(Util::SCHOOL_NAME_IT,$temp);
            }
            if(Util::getSchoolName() == Util::SCHOOL_NAME_MS){
                //验证后，如果不能提交会有异常抛出，不需要处理成功时的情况
                ZcApi::validateRoster(Util::SCHOOL_NAME_SJ,$temp);
                ZcApi::validateRoster(Util::SCHOOL_NAME_IT,$temp);
            }
            if(Util::getSchoolName() == Util::SCHOOL_NAME_IT){
                //验证后，如果不能提交会有异常抛出，不需要处理成功时的情况
                ZcApi::validateRoster(Util::SCHOOL_NAME_SJ,$temp);
                ZcApi::validateRoster(Util::SCHOOL_NAME_MS,$temp);
            }
        }
        return array_merge($data,$createData);
    }
    /**
     * 添加一个新量，自带验证功能
     * @param $data array 要添加的数据
     *      keys:
     *              roster_no:新量号码，QQ或微信号
     *              roster_type:新量类型，[1:QQ号，２：微信号]
     *              seoer_id:推广专员ID
     *              qq_group_id：群ID号，不传该值时，系统会自带分配一个群
     *              is_admin_add:是否是管理员添加，[1是，0否]
     *              from_type:来源类型,[1:正常提交 2:大鹏PC站 3:大鹏WAP站 4:Android 5:IOS 6.批量导入]
     * @param $multiSchool 是否开启多学院验证，默认为false;
     */
    public static function addRoster(array $data,$multiSchool = false){

        //验证数据是否存在问题，并补全部分信息
        $data = self::validateRosterData($data,$multiSchool);
        $column = app('status')->getRosterTypeColumn($data['roster_type']);
        //验证成功后，获取QQ群信息
        if(!isset($data['qq_group_id'])){
            $groupInfo = app('status')->getNextGroupInfo($data['roster_type']);
            if(!$groupInfo){
                throw new UserValidateException("未找到可用的群信息");
            }
            $data['qq_group_id'] = $groupInfo['id'];
        }
        //补全推广专员信息
        if(!isset($data['seoer_name'])){
            $seoer = UserModel::query()->find($data['seoer_id']);
            if(!$seoer)
                throw new UserValidateException("本操作只能由推广专员进行操作");
            $data['seoer_name'] = $seoer->name;
        }
        $group = GroupModel::find($data['qq_group_id']);
        $createData[$column] = $data['roster_no'];
        $createData['type'] = $data['roster_type'];
        $createData['qq_group_id'] = $data['qq_group_id'];
        $createData['qq_group'] = $group->qq_group;
        $createData['inviter_id'] = $data['seoer_id'];
        $createData['inviter_name'] = $data['seoer_name'];
        $createData['adviser_id'] =  $group->leader_id;
        $createData['adviser_name'] = $group->user->name;
        $createData['flag'] = $data['flag'] ?? 0;
        $createData['addtimes'] = $data['addtimes'] ?? 1;
        $createData['addtime'] = time();
        $createData['is_reg'] = 0;
        $createData['group_status'] = 0;
        $createData['course_type'] = 0;
        $createData['is_admin_add'] = $data['is_admin_add'] ?? 0;
        $createData['from_type'] = $data['from_type'] ?? 1;
        $query = static::query();
        return $query->create($createData);
    }
}
