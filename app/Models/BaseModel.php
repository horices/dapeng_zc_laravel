<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class BaseModel extends Model
{
    protected $primaryKey = "id";
    protected $perPage = 15;    //默认分页数
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

    /**
     * 获取当前登陆的用户信息
     * @param Request $request
     * @return mixed|\ArrayAccess[]|array[]|\ArrayAccess|array|Closure
     */
    public function getUserInfo(){
        return session()->get("userInfo");
    }

    /**
     * 获取当前model下 表包含的字段 键值对
     * @param array $data
     * @return array
     */
    public function getColumns(array $data){
        $columns = Schema::getColumnListing($this->table);
        $fields = [];
        foreach ($columns as $key=>$val){
            if(isset($data[$val])){
                $fields[$val] = $data[$val];
            }
        }
        return $fields;
    }

    /**
     * 重写父级自动填充，自动过滤非本表自段
     * @param array $attributes
     * @return $this
     */
    function fill(array $attributes)
    {
        $columns = Cache::remember($this->getTable()."columns",1,function(){
            return Schema::getColumnListing($this->getTable());
        });
        return parent::fill(collect($attributes)->intersectByKeys(collect($columns)->flip())->toArray());
    }


}
