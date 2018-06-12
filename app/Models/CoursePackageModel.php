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
    //赠送课程
    public static $giveList = [
        0=>['id'=>0,'text'=>'无','checked'=>false],
        1=>['id'=>1,'text'=>'英语口语','checked'=>false],
        2=>['id'=>2,'text'=>'AE','checked'=>false],
        3=>['id'=>'3','text'=>'转手绘','checked'=>false],
        4=>['id'=>'4','text'=>'H5','checked'=>false],
        5=>['id'=>'5','text'=>'JAVA','checked'=>false],
        6=>['id'=>'6','text'=>'手绘','checked'=>false],
        7=>['id'=>'7','text'=>'素描','checked'=>false],
        8=>['id'=>'8','text'=>'色彩','checked'=>false],
        9=>['id'=>'9','text'=>'广告','checked'=>false],
        10=>['id'=>'10','text'=>'摄影','checked'=>false],
        11=>['id'=>'11','text'=>'美妆','checked'=>false],
        12=>['id'=>'12','text'=>'摄影实战班','checked'=>false],
        13=>['id'=>'13','text'=>'C4D','checked'=>false],
        14=>['id'=>'16','text'=>'视频摄制','checked'=>false],
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
        if($this->course_attach){
            return json_decode($this->course_attach,1);
        }
        return [];
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
            'title'          =>   'required',
            'price'          =>   'required',
        ],[
            'title.required'=>  '请填写套餐名称！',
            'price.required'=>  '请填写套餐价格！',
        ]);
        //执行验证
        $validator->validate();
        return self::create($post);
    }

    /**
     * 修改数据
     * @param $data
     * @return mixed
     */
    static function updateData($post){
        $validator = Validator::make($post,[
            'title'          =>   'sometimes|required',
            'price'          =>   'sometimes|required',
        ],[
            'title.required'=>  '请填写套餐名称！',
            'price.required'=>  '请填写套餐价格！',
        ]);
        //执行验证
        $validator->validate();
        $detail = self::find($post['id']);
        if(!$detail){
            throw new UserValidateException("未找到套餐信息！");
        }
        $detail->status = "MOD";
        $detail->save();
        $query = self::query();
        $detail->status = "USE";
        $query->create();
        return $detail->update($post);
    }



}