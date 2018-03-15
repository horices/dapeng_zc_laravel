<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25 0025
 * Time: 11:25
 */

namespace App\Models;


use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserRegistrationModel extends BaseModel{
    protected $table = "user_registration";
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    //报名分期付款方式
    public $fqType = [
        'CASH'      =>  '现金分期',
        'HUABEI'    =>  '花呗分期',
        'MYFQ'      =>  '蚂蚁分期',
    ];

    /**
     * 新增数据
     * @param $data
     * @return mixed
     */
    function addData($data){
        $validator = Validator::make($data, [
            'mobile'            =>'required|regex:/\d{11}/|unique:user_registration',
            'name'              => 'required|unique:user_registration|max:255',
            'qq'                => 'required|regex:/\d{5,11}/|unique:user_registration',
            'package_id'        => 'exists:course_package,id',
            'package_attach_id' => 'exists:course_package,id',
            'rebate_id'         => 'exists:rebate_activity,id',
            'amount_submitted'  =>  'required|numeric'
        ],[
            'mobile.required'   =>  '请输入正确的学员手机号！',
            'mobile.unique'     =>  '该学员手机号已存在！',
            'name.required'     =>  '请输入学员姓名！',
            'qq.required'       =>  '请输入学员QQ号！',
            'qq.regex'          =>  '学员QQ号格式错误！',
            'qq.unique'         =>  '学员QQ号已存在！',
            'package_id.exists' =>  '请选择正确的课程主套餐！',
            'package_attach_id.exists' =>  '请选择正确的课程副套餐！',
            'rebate_id.exists'  =>  '请选择正确的优惠活动！',
            'amount_submitted,required'=>   '已提交金额有误！',
            'amount_submitted,numeric'=>   '已提交金额有误！',
        ]);
        //执行验证
        $validator->validate();
        return self::create($data);
    }

    /**
     * 更新数据
     * @param $data
     * @return bool
     */
    function updateData($data){
        $validator = Validator::make($data, [
            'id'                =>  'required|numeric|exists:user_registration,id',
            'mobile'            => [
                'sometimes',
                'required',
                'regex:/\d{11}/',
                Rule::unique('user_registration')->ignore($data['id']),
                ],
            'name'              => 'sometimes|required|max:255',
            'qq'                => [
                'sometimes',
                'required',
                'regex:/\d{5,11}/',
                Rule::unique('user_registration')->ignore($data['id']),
                ],
            'package_id'        => 'sometimes|exists:course_package,id',
            'package_attach_id' => 'sometimes|exists:course_package,id',
            'rebate_id'         => 'sometimes|exists:rebate_activity,id',
            'amount_submitted'  =>  'sometimes|required|numeric'
        ],[
            'id.required'       =>  '更新的支付记录错误！',
            'id.numeric'        =>  '更新的支付记录错误！',
            'id.exists'         =>  '未找到要修改的支付记录',
            'mobile.required'   =>  '请输入正确的学员手机号！',
            'mobile.unique'     =>  '该学员手机号已存在！',
            'name.required'     =>  '请输入学员姓名！',
            'name.max'          =>  '学员姓名格式错误！',
            'qq.required'       =>  '请输入学员QQ号！',
            'qq.regex'          =>  '学员QQ号格式错误！',
            'qq.unique'         =>  '学员QQ号已存在！',
            'package_id.exists' =>  '请选择正确的课程主套餐！',
            'package_attach_id.exists' =>  '请选择正确的课程副套餐！',
            'rebate_id.exists'  =>  '请选择正确的优惠活动！',
            'amount_submitted,required'=>   '已提交金额有误！',
            'amount_submitted,numeric'=>   '已提交金额有误！',
        ]);
        $validator->validate();
        $res = self::find($data['id']);
        return $res->save($data['id']);
    }

}