<?php

namespace App\Models;

use Illuminate\Auth\AuthenticationException;
use App\Http\Controllers\BaseController;

/**
 * @method static checkLogin($username , $password) 
 * @author Administrator
 *
 */
class UserModel extends BaseModel
{
    protected $table = "user_headmaster";
    protected function getAddtimeAttribute($v){
        return date('Y-m-d H:i:s',$v);
    }
    protected function getGradeAttribute($v){
        return BaseController::USER_GRADE[$v];
    }
    /**
     * 检测用户名密码是否正确
     */
    protected function checkLogin($username , $password){
        //throw new AuthenticationException("帐号密码错误");
        return $this->where("mobile","=",$username)->where("password","=",md5($password))->firstOrFail();
    }
}
