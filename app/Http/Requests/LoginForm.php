<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginForm extends FormRequest
{
    /**
     * {@inheritDoc}
     * @see \Illuminate\Foundation\Http\FormRequest::messages()
     */
    public function messages()
    {
        return [
            'username.required'=>'请输入用户名',
            'password.required'=>'请输入密码'
        ];
    }

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
            'username'=>'required',
            'password'  => 'required'
        ];
    }
}
