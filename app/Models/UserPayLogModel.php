<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/13 0013
 * Time: 17:09
 */

namespace App\Models;


class UserPayLogModel extends BaseModel {
    protected $table = "user_pay_log";
    const CREATED_AT = 'creation_time';
    const UPDATED_AT = 'update_time';
}