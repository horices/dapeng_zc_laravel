<?php

namespace App\Models;

use App\Exceptions\UserValidateException;
use App\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Builder;

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
        'password'
    ];

    protected $appends = [
        'status_text',
        'grade_text',       //级别描述
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
        if($this->grade)
            return app(BaseController::class)->getUserGradeList()[$this->grade];
    }
    protected function getStatusTextAttribute($v){
        return $this->status == 1 ? '正常':'暂停';
    }
    protected function getStatisticsAttribute(){
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
        return $userInfo;
    }

    /**
     * 用户管理群信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    protected function groups(){
        return $this->hasMany(GroupModel::class,'leader_id','uid');
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
