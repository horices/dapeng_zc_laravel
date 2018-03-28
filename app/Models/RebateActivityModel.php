<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6 0006
 * Time: 17:08
 */

namespace App\Models;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RebateActivityModel extends BaseModel {
    protected $table = "rebate_activity";
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    /**
     * 新增数据
     * @param $data
     * @return mixed
     */
    public static function addData($data){
        $validator = Validator::make($data,[
            'title'     =>  'required',
            'price'     =>  'required|numeric'
        ],[
            'title.required'    =>  '标题不能为空！',
            'price.required'    =>  '价格不能为空！',
            'price.numeric'     =>  '请输入正确的价格！',
        ]);
        $validator->validate();
        return self::create($data);
    }

    /**
     * 修改数据
     * @param $data
     * @return mixed
     */
    static function updateData($data){
        $validator = Validator::make($data,[
            'id'        =>  'sometimes|numeric|exists:rebate_activity,id',
            'title'     =>  'required',
            'price'     =>  'required|numeric'
        ],[
            'id.numeric'        =>  '请选择要修改的优惠！',
            'id.exists'         =>  '请选择要修改的优惠！',
            'title.required'    =>  '标题不能为空！',
            'price.required'    =>  '价格不能为空！',
            'price.numeric'     =>  '请输入正确的价格！',
        ]);
        //执行验证
        $validator->validate();
        $detail = self::find($data['id']);
        return DB::transaction(function () use($data,$detail){
            $detail->status = 'MOD';
            $detail->save();
            $data['status'] = "USE";
            $data['rebate_id'] = $detail->rebate_id;
            unset($data['id']);
            return self::create($data);
        });
    }


}