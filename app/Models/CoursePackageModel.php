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
        }
        return $return;
    }
    /**
     * 获取附加的套餐信息 字符串类型
     * @return mixed
     */
    public function getCourseAttachTextAttribute(){
        $return = "";
        if($this->course_attach){
            $data = json_decode($this->course_attach,1);
            foreach ($data as $key => $val){
                if(isset($val['title']) && $val['title']){
                    $return .= $val['title']."\n";
                }
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
     * 关联活动表
     */
    function rebate(){
        return $this->hasMany(RebateActivityModel::class,"package_id","id");
    }
    /**
     * 新增数据
     * @param $data
     * @return mixed
     */
    static function addData($post){
        $validator = Validator::make($post,[
            'school_id'             =>  'required',
            'attach_length'         =>  'required',
            'title'                 =>  'required',
            'price'                 =>  'required|numeric',
            'attach_title'          =>  'required',
            'attach_price'          =>  'required',
        ],[
            'school_id.required'        =>  '请选择所属学院！',
            'attach_length.required'    =>  '请先添加课程套餐！',
            'title.required'            =>  '请输入套餐名称！',
            'price.required'            =>  '请输入套餐价格！',
            'price.numeric'             =>  '请输入正确的套餐价格！',
            'attach_title.required'     =>  '请填写附加课程！',
            'attach_price.required'     =>  '请输入正确的附加课程金额！',
        ]);
        //执行验证
        $validator->validate();
        //验证添加的附加课程是否满足格式
        for($i=0;$i<$post['attach_length'];$i++){
            if(!isset($post['attach_title'][$i])){
                throw new UserValidateException("附加课程必须填写标题!");
            }
            if(!isset($post['attach_price'][$i])){
                throw new UserValidateException("附加课程必须填写价格!");
            }
        }
        $data = [];
        foreach ($post['attach_title'] as $key=>$val){
            $data[$key] = [
                'title' =>  $val,
                'price' =>  $post['attach_price'][$key]
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
        //$post['attach_title']=array_filter($post['attach_title']);

        $validator = Validator::make($post,[
            'school_id'             =>  'sometimes|required',
            'attach_length'         =>  'sometimes|required',
            'title'                 =>  'sometimes|required',
            'price'                 =>  'sometimes|required|numeric',
        ],[
            'school_id.required'        =>  '请选择所属学院！',
            'attach_length.required'    =>  '请先添加课程套餐！',
            'title.required'            =>  '请输入套餐名称！',
            'price.required'            =>  '请输入套餐价格！',
            'price.numeric'             =>  '请输入正确的套餐价格！',
        ]);
        //执行验证
        $validator->validate();
        //验证添加的附加课程是否满足格式
        for($i=0;$i<$post['attach_length'];$i++){
            if(!isset($post['attach_title'][$i])){
                throw new UserValidateException("附加课程必须填写标题!");
            }
            if(!isset($post['attach_price'][$i])){
                throw new UserValidateException("附加课程必须填写价格!");
            }
        }
        //附加信息整理json
        $data = [];
        foreach ($post['attach_title'] as $key=>$val){
            $data[$key] = [
                'title' =>  $val,
                'price' =>  $post['attach_price'][$key]
            ];
        }
        unset($post['attach_title'],$post['attach_price']);
        $post['course_attach'] = json_encode($data,JSON_UNESCAPED_UNICODE);
        $detail = self::find($post['id']);
        if(!$detail){
            throw new UserValidateException("未找到套餐信息！");
        }
        //$query = self::query();
        return $detail->update($post);
    }



}