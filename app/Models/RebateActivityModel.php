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
    protected $appends = [
        'course_give_data','start_time_text','end_time_text'
    ];

    /**
     * 获取赠送课程列表 数组形式
     * @return array|mixed
     */
    public function getCourseGiveDataAttribute(){
        $return = [];
        if($this->course_give){
            $return = json_decode($this->course_give,1);
        }
        return $return;
    }

    /**
     * 获取日期格式的优惠开启时间
     * @return false|string
     */
    public function getStartTimeTextAttribute(){
        return $this->start_time ? date('Y-m-d H:i:s',$this->start_time) : '';
    }

    /**
     * 获取日期格式的优惠结束时间
     * @return false|string
     */
    public function getEndTimeTextAttribute(){
        return $this->end_time ? date('Y-m-d H:i:s',$this->end_time) : '';
    }

    /**
     * 修改器 优惠开启时间
     * @param $value
     * @return false|int
     */
    public function setStartTimeAttribute($value){
        $this->attributes['start_time'] = strtotime($value);
    }

    /**
     * 修改器 优惠结束时间
     * @param $value
     * @return false|int
     */
    public function setEndTimeAttribute($value){
        $this->attributes['end_time'] = strtotime($value);
    }

    /**
     * 新增数据
     * @param $data
     * @return mixed
     */
    public static function addData($post){
        $validator = Validator::make($post,[
            'title'             =>  'required',
            'price_max'             =>  'required|numeric',
            'start_time'        =>  'required',
            'end_time'          =>  'required',
            'package_id'        =>  'required|exists:course_package,id',
        ],[
            'title.required'    =>  '标题不能为空！',
            'price_max.required'    =>  '价格不能为空！',
            'price_max.numeric'     =>  '请输入正确的价格！',
            'start_time.required' =>  '请输入优惠开启时间！',
            'end_time.required' =>  '请输入优惠截止时间！',
            'package_id.required'=>'请先选择套餐！',
            'package_id.exists' =>'请先选择套餐！',
        ]);
        $validator->validate();
        $post['course_give'] = collect($post['give_title'])->toJson(JSON_UNESCAPED_UNICODE);
        return self::create($post);
    }

    /**
     * 修改数据
     * @param $data
     * @return mixed
     */
    static function updateData($post){
        $validator = Validator::make($post,[
            'id'        =>  'sometimes|numeric|exists:rebate_activity,id',
            'title'     =>  'sometimes|required',
            'price_max'     =>  'sometimes|required|numeric',
            'start_time'        =>  'sometimes|required',
            'end_time'          =>  'sometimes|required',
            'package_id'=>  'sometimes|required|exists:course_package,id',
        ],[
            'id.numeric'        =>  '请选择要修改的优惠！',
            'id.exists'         =>  '请选择要修改的优惠！',
            'title.required'    =>  '标题不能为空！',
            'price_max.required'    =>  '价格不能为空！',
            'price_max.numeric'     =>  '请输入正确的价格！',
            'start_time.required' =>  '请输入优惠开启时间！',
            'end_time.required' =>  '请输入优惠截止时间！',
            'package_id.required'=>'请先选择套餐！',
            'package_id.exists' =>'请先选择套餐！',
        ]);
        //执行验证
        $validator->validate();
        $post['course_give'] = collect($post['give_title'])->toJson(JSON_UNESCAPED_UNICODE);
        $detail = self::find($post['id']);
        $detail->fill($post);
        //dd($detail);
        return $detail->save();
    }


}