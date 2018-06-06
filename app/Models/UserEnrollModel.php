<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/24 0024
 * Time: 17:28
 */

namespace App\Models;


use Illuminate\Support\Facades\Validator;

class UserEnrollModel extends BaseModel {
    protected $table = "user_enroll";

    /**
     * 获取已提交的总金额
     * @return mixed
     */
    public function getTotalSubmittedPriceAttribute(){
        return collect($this->userRegistration)->sum("amount_submitted");
    }

    /**
     * 获取应交总金额
     * @return mixed
     */
    public function getTotalShouldPriceAttribute(){
        return collect($this->userRegistration)->sum("package_total_price");
    }

    /**
     * 获取每个学院对应的已交金额
     * @return static
     */
    public function getSubmittedPriceAttribute(){
        return collect($this->userRegistration)->pluck('amount_submitted','school_id')->all();
    }

    public function getPayTextAttribute(){

    }

    /**
     * 获取报名表模型
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userRegistration(){
        return $this->hasMany(UserRegistrationModel::class,'enroll_id','id');
    }
}