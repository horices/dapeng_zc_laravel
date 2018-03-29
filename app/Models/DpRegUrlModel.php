<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29 0029
 * Time: 18:02
 */

namespace App\Models;


class DpRegUrlModel extends BaseModel {
    protected $table = "dp_reg_url";
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
}