<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6 0006
 * Time: 17:07
 */

namespace App\Models;


use App\Exceptions\UserValidateException;
use App\Utils\Util;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoursePackageModel extends BaseModel {
    protected $table = "course_package";
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $appends = [
        'school_text',
        'course_attach_data'
    ];

    public function getCourseAttachAttribute($value){
        if(!$value){
            $data = [
                'attach'    =>  [],
                'give'      =>  [],
                'rebate'    =>  []
            ];
            $value = collect($data)->toJson();
        }
        return $value;
    }

    /**
     * 获取学院标题
     * @return \Illuminate\Config\Repository|mixed
     */
    public function getSchoolTextAttribute(){
        $this->school_id = $this->school_id ? $this->school_id : 'SJ';
        return Util::getSchoolNameText($this->school_id);
    }

    /**
     * 获取附加的套餐信息
     * @return mixed
     */
    public function getCourseAttachDataAttribute(){
        $return = [];
        if($this->course_attach){
            $return = json_decode($this->course_attach,1);
            if(isset($return['rebate']) && $return['rebate']){
                $temp = $return['rebate'];
                $return['rebate'] = [];
                $return['rebate'][] = $temp;
            }
        }
        return $return;
    }

    /**
     * 获取类型文字
     * @return string
     */
    public function getTypeTextAttribute(){
        return $this->type == 1 ? "副套餐" : "主套餐";
    }

    /**
     * 新增数据
     * @param $data
     * @return mixed
     */
    static function addData($post){
        $validator = Validator::make($post,[
            'attach_title'          =>  'required',
            'attach_price'          =>  'required',
            'give_title'            =>  'required',
            'rebate_price'          =>   'required_with:rebate_title',
            'rebate_start_date'     =>   'required_with:rebate_title',
            'rebate_end_date'       =>   'required_with:rebate_title',
        ],[
            'attach_title.required'    =>  '请填写附加课程！',
            'give_title.required'      =>  '请填写赠送课程！',
            'attach_price.required'    =>  '请输入正确的附加课程金额！',
            'rebate_price.required_with'=>  '请填写优惠金额！',
            'rebate_start_date.required_with'=>  '请填写优惠开启时间！',
            'rebate_end_date.required_with'=>  '请填写优惠结束时间！',
        ]);
        //执行验证
        $validator->validate();

        $data = [];
        foreach ($post['attach_title'] as $key=>$val){
            $data['attach'][$key] = [
                'title' =>  $val,
                'price' =>  $post['attach_price'][$key]
            ];
        }
        if(isset($post['give_title'])){
            foreach ($post['give_title'] as $key=>$val){
                $data['give'][$key] = ['title'=>$val];
            }
        }

        //判断优惠活动
        if($post['rebate_title']){
            $data['rebate'] = [
                'title'         =>  $post['rebate_title'],
                'price'         =>  $post['rebate_price'],
                'start_date'    =>  $post['rebate_start_date'],
                'end_date'      =>  $post['rebate_end_date'],
            ];
        }
        unset($post['attach_title'],$post['attach_price']);
        $post['course_attach'] = json_encode($data,JSON_UNESCAPED_UNICODE);
        return self::create($post);
    }

    /**
     * 修改数据
     * @param $data
     * @return mixed
     */
    static function updateData($post){
        $validator = Validator::make($post,[
            'attach_title'          =>  'sometimes|required',
            'attach_price'          =>  'sometimes|required',
            'give_title'            =>  'sometimes|required',
            'rebate_price'          =>   'sometimes|required_with:rebate_title',
            'rebate_start_date'     =>   'sometimes|required_with:rebate_title',
            'rebate_end_date'       =>   'sometimes|required_with:rebate_title',
        ],[
            'attach_title.required'    =>  '请填写附加课程！',
            'give_title.required'      =>  '请填写赠送课程！',
            'attach_price.required'    =>  '请输入正确的附加课程金额！',
            'rebate_price.required_with'=>  '请填写优惠金额！',
            'rebate_start_date.required_with'=>  '请填写优惠开启时间！',
            'rebate_end_date.required_with'=>  '请填写优惠结束时间！',
        ]);
        //执行验证
        $validator->validate();
        //附加信息整理json
        $data = [];
        foreach ($post['attach_title'] as $key=>$val){
            $data['attach'][$key] = [
                'title' =>  $val,
                'price' =>  $post['attach_price'][$key]
            ];
        }
        if(isset($post['give_title'])){
            foreach ($post['give_title'] as $key=>$val){
                $data['give'][$key] = ['title'=>$val];
            }
        }

        //判断优惠活动
        if($post['rebate_title']){
            $data['rebate'] = [
                'title'         =>  $post['rebate_title'],
                'price'         =>  $post['rebate_price'],
                'start_date'    =>  $post['rebate_start_date'],
                'end_date'      =>  $post['rebate_end_date'],
            ];
        }
        unset($post['attach_title'],$post['attach_price']);
        $post['course_attach'] = json_encode($data,JSON_UNESCAPED_UNICODE);

        $post['uid'] = Util::getUserInfo()['uid'];
        $detail = self::find($post['id']);
        if(!$detail){
            throw new UserValidateException("未找到套餐信息！");
        }
        //$query = self::query();
        return $detail->update($post);
    }



}