<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name'  =>  "sometimes|required",
            'mobile'    =>  "sometimes|required",
            'grade' =>  'sometimes|required',
            'dapeng_user_mobile'   =>   "sometimes|required_if:grade,9,10",
        ];
    }

    public function messages()
    {
        return [
            "name.required"  =>  "姓名为必填项",
            'grade.required'    =>  '请选择用户级别',
            "dapeng_user_mobile.required_if"    =>  "请输入主站帐号",
            "mobile.required"  =>  "展翅系统账号为必填",
        ];
    }
    public function getValidatorInstance()
    {
        return parent::getValidatorInstance(); // TODO: Change the autogenerated stub
    }
}
