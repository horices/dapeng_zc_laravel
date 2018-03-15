<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/13 0013
 * Time: 16:37
 */

namespace App\Models;


use App\Utils\Util;
use Illuminate\Support\Facades\Validator;

class UserPayModel extends BaseModel{
    protected $table = "user_pay";
    const CREATED_AT = 'creation_time';
    const UPDATED_AT = 'update_time';
    protected $fillable = ["id", "uid", "amount","registration_id","package_id","mobile","qq","rebate","remark", "create_time", "update_time", "adviser_id", "adviser_name", "adviser_qq", "name"];
    /**
     * 新增数据
     * @param $data
     * @return mixed
     */
    function addData($data){
        $userInfo = $this->getUserInfo();
        Util::setDefault($data['adviser_id'],$userInfo['uid']);
        Util::setDefault($data['adviser_name'],$userInfo['name']);
        $validator = Validator::make($data,[
            'mobile'    =>  'regex:/\d{11}/',
            'amount'    =>  'sometime|numeric'
        ],[
            'mobile.regex'      =>  '手机格式错误！',
            'amount.numeric'    =>  '支付金额有误！',
        ]);
        $validator->validate();
        $query = self::query();
        return $query->create($data);
    }

    /**
     *  更新数据
     * @param $data
     * @return mixed
     */
    function updateData($data){
        $validator = Validator::make($data,[
            'id'        =>  'required|exists:user_pay,id',
            'mobile'    =>  'sometimes|regex:/\d{11}/',
            'amount'    =>  'sometimes|required|numeric'
        ],[
            'id.required'       =>  '未找到需要更新的记录！',
            'id.exists'         =>  '未找到需要更新的记录！',
            'mobile.regex'      =>  '手机格式错误！',
            'amount.required'   =>  '支付金额有误！',
            'amount.numeric'    =>  '支付金额有误！',
        ]);
        $validator->validate();
        $res = self::find($data['id']);
        return $res->save($data['id']);
    }
}