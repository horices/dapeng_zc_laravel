<?php

namespace App\Rules;

use App\Api\DapengUserApi;
use App\Utils\Util;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Session;

class DapengUserHas implements Rule
{
    public $msg = "";
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $arr = [
            'dapeng_user_id'        =>  'ID',
            'dapeng_user_mobile'    =>  'MOBILE',
            'dapeng_user_qq'        =>  'QQ',
            'dapeng_user_weixin'    =>  'WEIXIN',
            'dapeng_user_wxopenid'  =>  'WXOPENID'
        ];
        $res = DapengUserApi::getInfo(['type'=>$arr[$attribute],'keyword'=>$value]);
        if($res['code'] == Util::SUCCESS){
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "主站未找到该用户！";
    }
}
