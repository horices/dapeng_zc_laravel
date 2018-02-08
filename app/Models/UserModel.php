<?php

namespace App\Models;

use Illuminate\Auth\AuthenticationException;
use App\Http\Controllers\BaseController;

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
    protected function getAddtimeAttribute($v){
        return date('Y-m-d H:i:s',$v);
    }
    protected function getGradeAttribute($v){
        return $v;
    }
    protected function getGradeTextAttribute(){
        return app(BaseController::class)->getUserGradeList()[$this->grade];
    }
    protected function getStatusTextAttribute($v){
        return $this->status == 1 ? '正常':'暂停';
    }
    /**
     * 检测用户名密码是否正确
     */
    protected function checkLogin($username , $password){
        //throw new AuthenticationException("帐号密码错误");
        return $this->where("mobile","=",$username)->where("password","=",md5($password))->firstOrFail();
    }
    
    protected function groups(){
        return $this->hasMany(GroupModel::class,'leader_id','uid');
    }
}
