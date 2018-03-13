<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/13 0013
 * Time: 16:37
 */

namespace App\Models;


class UserPayModel extends BaseModel{
    protected $table = "user_pay";
    const CREATED_AT = 'creation_time';
    const UPDATED_AT = 'update_time';
}