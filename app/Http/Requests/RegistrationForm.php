<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9 0009
 * Time: 9:09
 */

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class RegistrationForm extends FormRequest{
    /**
     * {@inheritDoc}
     * @see \Illuminate\Foundation\Http\FormRequest::messages()
     */
    public function messages()
    {
        return [
            'mobile.required'       =>  "请输入正确格式的手机号！",
            'mobile.numeric'       =>  "请输入正确格式的手机号！",
            'mobile.regex'          =>  "请输入正确格式的手机号！",
            'adviser_mobile.required'=> "请输入课程顾问手机号！",
            'adviser_mobile.regex'   => "课程顾问手机号格式错误！",
            'adviser_mobile.exists'  => "课程顾问手机号不存在！",
            'client_submit.required' => "数据来源有误！",
            'client_submit.in' => "数据来源有误！",
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
            'mobile'            =>  'required|numeric|regex:/^(1[0-9][0-9])\\d{8}$/',
            'client_submit'     =>  'required|in:WAP,PC'
        ];
    }

    /**
     * 重新方法 使用sometimes
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance(){
        $validator = parent::getValidatorInstance();
        $validator->sometimes('adviser_mobile', 'required|numeric|regex:/^(1[0-9][0-9])\\d{8}$/|exists:user_headmaster,mobile', function($input) {
            return $input->client_submit == "WAP";
        });

        return $validator;
    }
}