<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25 0025
 * Time: 11:25
 */

namespace App\Models;


class UserRegistrationModel extends BaseModel{
    protected $table = "user_registration";
    const CREATED_AT = 'creation_time';
    const UPDATED_AT = 'update_time';
    //报名分期付款方式
    public $fqType = [
        'CASH'      =>  '现金分期',
        'HUABEI'    =>  '花呗分期',
        'MYFQ'      =>  '蚂蚁分期',
    ];

    function add(){

    }
}