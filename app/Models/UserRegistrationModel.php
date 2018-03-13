<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25 0025
 * Time: 11:25
 */

namespace App\Models;


use Illuminate\Support\Facades\Validator;

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

    function addData($data){
        $validator = Validator::make($data, [
            'mobile'=>'required|regex:/\d{11}/|unique:user_registration',
            'name' => 'required|unique:posts|max:255',
            'qq' => 'required',
        ],[
            'mobile.required' =>  '请输入正确的学员手机号！',
            'mobile.unique'   =>  '该学员手机号已存在！',
            'name.required'   =>  '请输入学员姓名！',
            'qq.required'     =>  '请输入学员QQ号！',
        ]);
    }
}