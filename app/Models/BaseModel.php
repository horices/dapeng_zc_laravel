<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $primaryKey = "id";
    public $timestamps = false;
    
    //禁止批量赋值的字段
    protected $guarded = [
        
    ];
}
