<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6 0006
 * Time: 17:07
 */

namespace App\Models;


class CoursePackageModel extends BaseModel {
    //赠送课程
    public $give = [
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
}