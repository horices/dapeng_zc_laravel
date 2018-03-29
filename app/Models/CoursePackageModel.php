<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6 0006
 * Time: 17:07
 */

namespace App\Models;


use App\Utils\Util;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoursePackageModel extends BaseModel {
    protected $table = "course_package";
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
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
    ];

    /**
     * 获取套餐标题
     * @param $value
     * @return string]
     */
    public function getTitleAttribute($value){
        if($this->status == "DEL")
            return $value."(已删)";
        else
            return $value;
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
    static function addData($data){
        $validator = Validator::make($data,[
            'title'     =>  'required',
            'price'     =>  'required|numeric',
        ],[
            'title.required'    =>  '请输入套餐标题！',
            'price.required'    =>  '请输入套餐金额！',
            'price.numeric'     =>  '请输入正确的金额！'
        ]);
        //执行验证
        $validator->validate();

    }

    /**
     * 修改数据
     * @param $data
     * @return mixed
     */
    static function updateData($data){
        $validator = Validator::make($data,[
            'id'        =>  'sometimes|numeric|exists:course_package,id',
            'title'     =>  'required',
            'price'     =>  'required|numeric',
        ],[
            'id.numeric'        =>  '请选择要修改的套餐！',
            'id.exists'         =>  '请选择要修改的套餐！',
            'title.required'    =>  '请输入套餐标题！',
            'price.required'    =>  '请输入套餐金额！',
            'price.numeric'     =>  '请输入正确的金额！'
        ]);
        //执行验证
        $validator->validate();
        $data['uid'] = Util::getUserInfo()['uid'];
        $detail = self::find($data['id']);
        return DB::transaction(function () use($data,$detail){
            $detail->status = 'MOD';
            $detail->save();
            $data['status'] = "USE";
            $data['package_id'] = $detail->package_id;
            unset($data['id']);
            return self::create($data);
        });
    }



}