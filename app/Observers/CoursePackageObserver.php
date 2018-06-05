<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/4 0004
 * Time: 16:53
 */

namespace App\Observers;


use App\Models\CoursePackageModel;
use App\Utils\Util;
use Illuminate\Support\Facades\Validator;

class CoursePackageObserver extends BaseObserver {
    function saving(CoursePackageModel $CoursePackage){
        $userInfo = Util::getUserInfo();
        $CoursePackage->uid = $userInfo['uid'];
    }


}