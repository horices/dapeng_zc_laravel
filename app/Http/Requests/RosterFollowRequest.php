<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RosterFollowRequest extends FormRequest
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
            'roster_id'=>"exists:user_roster,id",
            //'picurl'    =>  "required",
            "deep_level"=> "required|in:".implode(',',array_keys(app('status')->getRosterDeepLevel())),
            'intention' =>  "required|in:".implode(',',array_keys(app('status')->getRosterIntention()))
        ];
    }
    public function messages()
    {
        return [
            'roster_id.required' =>  "缺少roster_id字段信息",
            'roster_id.exists'  =>  '该量信息存在错误',
            'picurl.required'   =>  '请上传聊天图片',
            'deep_level.required'        =>  '请选择私聊深度',
            'deep_level.in'     =>  '私聊深度不正确',
            'intention.required'    =>  '请选择报名意向',
            'intention.in'      =>  '报名意向不正确'
        ];
    }
}
