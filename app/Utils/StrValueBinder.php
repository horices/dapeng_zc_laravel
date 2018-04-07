<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/7
 * Time: 17:36
 */

namespace App\Utils;


use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Cell_IValueBinder;
use PHPExcel_Cell_DefaultValueBinder;

/**
 * laravel-Excel 解决数字识别后带小数点的问题
 * Class StrValueBinder
 * @package App\Utils
 */
class StrValueBinder extends PHPExcel_Cell_DefaultValueBinder implements PHPExcel_Cell_IValueBinder{

    public function bindValue(PHPExcel_Cell $cell, $value = null)
    {

        if(is_numeric($value)){
            $cell->setValueExplicit($value,PHPExcel_Cell_DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value); // TODO: Change the autogenerated stub
    }

}