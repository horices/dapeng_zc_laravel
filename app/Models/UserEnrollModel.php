<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/24 0024
 * Time: 17:28
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;

class UserEnrollModel extends BaseModel {
    use SoftDeletes;
    protected $table = "user_enroll";
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    const DELETED_AT  = 'delete_time';
    protected $dates = ['delete_time'];
    protected $appends = [
        'account','package_all_price','total_submitted_price','submitted_price','total_should_price',
        'should_price','total_rebate_price','package_title_text','last_pay_type_text','last_pay_time_text'
    ];

    /**
     * 获取用户qq/wx
     * @return mixed
     */
    public function getAccountAttribute(){
        return $this->qq ? $this->qq : $this->wx;
    }

    /**
     * 获取所有报名表的套餐总金额
     * @return mixed
     */
    public function getPackageAllPriceAttribute(){
        return collect($this->userRegistration)->sum("package_price")+collect($this->userRegistration)->sum("course_attach_all_price");
    }

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
     * 获取总的优惠金额
     * @return mixed
     */
    public function getTotalRebatePriceAttribute(){
        return collect($this->userRegistration)->sum('rebate');
    }

    /**
     * 获取每个学院对应的已交金额
     * @return static
     */
    public function getSubmittedPriceAttribute(){
        return collect($this->userRegistration)->pluck('amount_submitted','school_id')->all();
    }

    /**
     * 获取多个套餐，换行（导出专用）
     * @return string
     */
    public function getPackageTitleTextAttribute(){
        $value = "";
        foreach ($this->userRegistration as $v){
            $value .= $v['package_total_title']."<br/>";
        }
        return $value;
    }

    /**
     * 获取最后一次支付类型
     * @return string
     */
    public function getLastPayTypeTextAttribute(){
        $value = "";
        if($this->userPayLog->count()>0){
            $value = $this->userPayLog->first()->pay_type_text;
        }
        return $value;
    }
    /**
     * 获取最后一次支付时间
     * @return string
     */
    public function getLastPayTimeTextAttribute(){
        $value = "";
        if($this->userPayLog->count()>0){
            $value = $this->userPayLog->first()->pay_time_text;
        }
        return $value;
    }

    /**
     * 获取报名表模型
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userRegistration(){
        return $this->hasMany(UserRegistrationModel::class,'enroll_id','id');
    }

    /**
     * 获取支付记录表模型
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userPayLog(){
        return $this->hasMany(UserPayLogModel::class,'enroll_id','id');
    }
}