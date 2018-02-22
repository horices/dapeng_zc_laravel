<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $primaryKey = "id";
    public $timestamps = false;

    //允许为NULL的字段
    protected $nullable = [

    ];
    //禁止批量赋值的字段
    protected $guarded = [

    ];

    //允许转为数组的字段,自动调用 getColumnAttribute 方法
    protected $appends = [];

    /**
     * 重写父类设置属性，防止通过中间件时，返回的NULL问题
     * @param string $key
     * @param mixed $value
     * @return $this|void
     */
    public function setAttribute($key, $value)
    {
        if(!in_array($key,$this->nullable)){
            $value = $value ?? '';
        }
        parent::setAttribute($key,$value);
    }




}
