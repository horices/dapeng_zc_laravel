<?php

namespace App\Models;

use App\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @property-read string $type_text [QQ,微信]
 * @property-read UserModel $user   群绑定的用户信息
 * @author Administrator
 *
 */
class GroupModel extends BaseModel
{
    protected $table = "user_qqgroup";
    protected $casts = [
        'qrc_url' => "string",
        'qrc_link' => "string",
    ];
    //禁止批量赋值的字段
    protected $fillable = [
        "type","group_name","qq_group","qrc_url","qrc_link","leader_id","is_open","mark"
    ];

    protected $appends = [
        'is_open_text',
        'addtime_export_text',
        'type_text'
    ];
    protected function getIsOpenTextAttribute(){
        return $this->is_open == 1 ?'正常':'关闭';
    }
    protected function getQrcUrlAttribute($v){
        if(Str::startsWith($v,"./Uploads")){
            $v = Str::lower(Str::substr($v,1));
        }
        return $v;
    }
    protected function getTypeTextAttribute(){
        if($this->group_status !== null){
            return app("status")->getRosterType()[$this->type];
        }
    }
    protected function getAddtimeExportTextAttribute(){
        return date('Y-m-d H:i:s',$this->add_time);
    }
    function user(){
        return $this->belongsTo(UserModel::class,'leader_id','uid')->withDefault();
    }

    /**
     * 获取没有关闭的群
     */
    public function scopeOpened($query){
        return $query->where("is_open",1);
    }
    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope('status',function (Builder $builder){
            $builder->where("status",1);
        });
    }
}
