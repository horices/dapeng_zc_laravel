<?php

namespace App\Http\Requests;

use App\Rules\SMSCode;
use Illuminate\Foundation\Http\FormRequest;

class RegRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'  =>  'required',
            'mobile'  =>  ['required','regex:/1\d{10}/','unique:user_headmaster,mobile'],
            'password'  =>  'required|between:6,16',
            'smscode'  =>  [
                'required',
                new SMSCode()
            ]
        ];
    }

    public function messages()
    {
        return [
            'name.required' =>  '请输入姓名',
            'mobile.required'   =>  '请输入手机号',
            'mobile.regex'  =>  "手机号格式不正确",
            'mobile.unique' =>  '该手机号已注册',
            'password.required' =>  '请输入密码',
            'password.between'  =>  '密码长度为6-16位',
            'smscode.required' =>  "请输入短信验证码",
        ];
    }
}
