<?php

namespace App\Models;

use App\Http\Controllers\BaseController;
use Illuminate\Notifications\Notifiable;

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
    
    protected function getIsOpenTextAttribute(){
        return $this->is_open == 1 ?'正常':'关闭';
    }
    protected function getTypeTextAttribute(){
        return app(BaseController::class)->getRosterType()[$this->type];
    }
    
    function user(){
        return $this->belongsTo(UserModel::class,'leader_id','uid')->withDefault();
    }
}
