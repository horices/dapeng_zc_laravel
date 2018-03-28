<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Session;

class SMSCode implements Rule
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
        if($value != Session::get("sms_code")){
            $this->msg = "短信验证码错误";
            return false;
        }
        if(Session::get("sms_code_expire_time")<time()){
            $this->msg = "短信验证码已过期";
            return false;
        }
        return ;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->msg;
    }
}
