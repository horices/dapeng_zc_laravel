<?php

namespace App\Models;

use App\Utils\Api\DapengUserApi;
use App\Exceptions\UserValidateException;
use App\Http\Controllers\BaseController;
use App\Utils\Util;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**

 * @method static $this->checkLogin($username , $password)
 * @property-read string $grade_text 级别文字描述
 * @property-read string $status_text 状态文字描述[正常，注销]
 * @method static checkLogin($username , $password)
 * @author Administrator
 *
 */
class UserModel extends BaseModel
{
    protected $table = "user_headmaster";
    protected $primaryKey = "uid";
    /* protected $fillable = [
        'name','mobile','dapeng_user_mobile','per_max_num_qq','per_max_num_wx'
    ]; */
    //该字段不显示
    protected $hidden = [
        'password','code','email','qq','login','coins','last_login_time','last_login_ip','pid','path','grade_up_time','up_ing','mark'
    ];

    protected $appends = [
        'status_text',
        'grade_text',       //级别描述
        'incumbency_text',  //在职状态描述
    ];
    protected function getPerMaxNumWxAttribute($v){
        return $v ?? 1;
    }
    protected function setPerMaxNumWxAttribute($v){
        $this->attributes['per_max_num_wx'] = $v ?? 1;
    }
    protected function getPerMaxNumQqAttribute($v){
        return $v ?? 1;
    }
    protected function setPerMaxNumQqAttribute($v){
        $this->attributes['per_max_num_qq'] =  $v ?? 1;
    }
    protected function getAddtimeTextAttribute($v){
        return date('Y-m-d H:i:s',$v);
    }
    protected function getGradeTextAttribute(){
        if($this->grade >0 ){
            return app(BaseController::class)->getUserGradeList()[$this->grade];
        }
    }
    protected function getStatusTextAttribute($v){
        return $this->status == 1 ? '正常':'暂停';
    }
    /**
     * 获取用户在职状态
     * @return string
     */
    function getIncumbencyTextAttribute(){
        return $this->is_incumbency ? '在职' : '离职';
    }


    /**
     * 检测用户名密码是否正确
     */
    protected function checkLogin($username , $password){
        //throw new AuthenticationException("帐号密码错误");
        $userInfo = $this->where("mobile","=",$username)->where("password","=",md5($password))->first();
        if(!$userInfo){
            throw new UserValidateException("帐号密码错误");
        }
        if(!$userInfo->status){
            throw new UserValidateException("该账号已经被暂停。请联系管理员");
        }
        return $userInfo;
    }

    /**
     * 用户管理群信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function groups(){
        return $this->hasMany(GroupModel::class,'leader_id','uid');
    }

    /**
     * 销售记录
     */
    function rosterFollow(){
        return $this->hasMany(RosterFollowModel::class,'adviser_id');
    }
    function lastRosterFollowOne(){
        return $this->belongsTo(RosterFollowModel::class,"uid","adviser_id")->orderBy("id","desc")->withDefault();
    }
    /**
     * 查询课程顾问
     * @param $query
     * @return mixed
     */
    public function scopeAdviser($query){
        return $query->whereIn('grade',[9,10]);
    }

    /**
     * 查询推广专员
     * @param $query
     * @return mixed
     */
    public function scopeSeoer($query){
        return $query->whereIn("grade",[11,12]);
    }
    public function scopeStatus($query){
        return $query->where("status",1);
    }
    protected static function boot()
    {
        parent::boot();
        /**
         * 默认只查询状态为正常的用户
         */
        self::addGlobalScope('status',function (Builder $builder){
            //$builder->where("status",1);
        });
    }
}
