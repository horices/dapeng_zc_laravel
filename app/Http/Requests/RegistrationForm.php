<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9 0009
 * Time: 9:09
 */

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class RegistrationForm extends FormRequest{
    /**
     * {@inheritDoc}
     * @see \Illuminate\Foundation\Http\FormRequest::messages()
     */
    public function messages()
    {
        return [

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

        ];
    }
}