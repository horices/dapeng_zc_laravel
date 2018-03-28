<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendSMSRequest extends FormRequest
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
            //
            'mobile'    =>  [
                'required',
                'regex:/1\d{10}/'
            ]
        ];
    }

    public function messages()
    {
        return [
            'mobile.required'   =>  "请输入手机号",
            'mobile.regex'  =>  "手机号不正确"
        ];
    }
}
