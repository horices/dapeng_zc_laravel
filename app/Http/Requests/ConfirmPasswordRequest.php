<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmPasswordRequest extends FormRequest
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
            'password'  =>  'nullable|min:6|max:16|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'password.min'  =>  '最少为六位',
            'password.max'  =>  '最长为16位',
            'password.confirmed'    =>  '两次密码不一致'
        ];
    }
}
