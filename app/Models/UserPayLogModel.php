<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/13 0013
 * Time: 17:09
 */

namespace App\Models;


use App\Utils\Util;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserPayLogModel extends BaseModel {
    protected $table = "user_pay_log";
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    //支付方式
    public $payType = [
        'ALIPAY'    =>  '支付宝',
        'WEIXIN'    =>  '微信',
        'HUABEI'    =>  '花呗',
        'HUABEIFQ'  =>  '花呗分期',
        'MAYIFQ'    =>  '蚂蚁分期',
        'BANKZZ'    =>  '银行转账',
    ];

    /**
     * 获取支付方式
     * @return mixed
     */
    public function getPayTypeTextAttribute(){
        return app("status")->getPayTypeList($this->pay_type);
    }

    /**
     * 新增数据
     * @param $data
     * @return mixed
     */
    function addData($data){
        $userInfo = $this->getUserInfo();
        Util::setDefault($data['adviser_id'],$userInfo['uid']);
        Util::setDefault($data['adviser_name'],$userInfo['name']);
        $data = $this->getColumns($data);
        $validator = Validator::make($data,[
            'registration_id'=> 'required|exists:user_registration,id',
            'mobile'        =>  'required|regex:/\d{11}/',
            'pay_time'      =>  'required|numeric',
            'pay_type'      =>  [
                'required',
                Rule::in(array_keys($this->payType)),
                ],
            'amount'        =>  'required|numeric'
        ],[
            'registration_id.required'  =>  '未找到报名记录！',
            'registration_id.exists'    =>  '未找到正确的报名记录！',
            'mobile.required'           =>  '请填写手机号！',
            'pay_time.required'         =>  '填写支付时间！',
            'pay_time.numeric'          =>  '填写正确的支付时间！',
            'pay_type.required'         =>  '请选择支付类型！',
            'pay_type.in'               =>  '请选择正确的支付类型！',
            'amount.required'           =>  '支付金额不能为空！',
            'amount.numeric'            =>  '支付金额格式错误！',
        ]);
        $validator->validate();
        return self::create($data);
    }

    function updateData($data){
        $validator = Validator::make($data,[
            'id'            =>  'required|exists:user_pay_log,id',
            'mobile'        =>  'required|regex:/\d{11}/',
            'pay_time'      =>  'required|numeric',
            'pay_type'      =>  'required|in_array:'.$this->payType,
            'amount'        =>  'required|numeric'
        ],[
            'id.required'               =>  '未找到更新的支付记录！',
            'id.exists'                 =>  '未找到更新的支付记录！',
            'mobile.required'           =>  '请填写手机号！',
            'pay_time.required'         =>  '填写支付时间！',
            'pay_time.numeric'          =>  '填写正确的支付时间！',
            'pay_type.required'         =>  '请选择支付类型！',
            'pay_type.in_array'         =>  '请选择正确的支付类型！',
            'amount.required'           =>  '支付金额不能为空！',
            'amount.numeric'            =>  '支付金额格式错误！',
        ]);
        $validator->validate();
        $res = self::find($data['id']);
        return $res->save($data['id']);
    }

}