<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/28 0028
 * Time: 11:08
 */

namespace App\Observers;


use App\Models\RebateActivityModel;
use App\Utils\Util;

class RebateObserver{

    function creating(RebateActivityModel $rebateModel){
        $rebateModel->uid = Util::getUserInfo()['uid'];
    }

    function created(RebateActivityModel $rebateModel){
        //更新package_id
        if(!$rebateModel->rebate_id){
            $rebateModel->rebate_id = $rebateModel->id;
            $rebateModel::query();
            $rebateModel->update(['rebate_id'=>$rebateModel->id]);
        }
    }

    function updating(RebateActivityModel $rebateModel){
        $rebateModel->uid = Util::getUserInfo()['uid'];
    }

}