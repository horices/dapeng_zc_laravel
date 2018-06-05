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

class RebateObserver extends BaseObserver {

    function creating(RebateActivityModel $rebateModel){
        $rebateModel->create_uid = Util::getUserInfo()['uid'];
        $rebateModel->update_uid = Util::getUserInfo()['uid'];
    }

    function created(RebateActivityModel $rebateModel){

    }

    function updating(RebateActivityModel $rebateModel){
        $rebateModel->update_uid = Util::getUserInfo()['uid'];
    }

}