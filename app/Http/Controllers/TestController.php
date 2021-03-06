<?php

namespace App\Http\Controllers;

use App\Models\CoursePackageModel;
use App\Models\RebateActivityModel;
use App\Models\UserEnrollModel;
use App\Models\UserPayLogModel;
use App\Models\UserRegistrationModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends BaseController
{
    //
    function index(){
        return "hello World";
    }

    function test(){
        //return view("test.test");
        //$this->setPackage();
        //$this->setRebate();
        $this->setRegistrationAttach();
        //$this->setEnroll();
    }

    /**
     * 整理套餐相关数据
     */
    function setPackage(){
        $CoursePackageModel = CoursePackageModel::query();
        $packageAttachList = $CoursePackageModel->where([
            ["type","=","1"],
        ])->get();
        //附加课程json组合
        $packageData = [];
        foreach ($packageAttachList as $key=>$val){
            $packageData[$key]['title'] = $val['title'];
            $packageData[$key]['price'] = floatval($val['price']);
        }
        $packageAttachJson = json_encode($packageData,JSON_UNESCAPED_UNICODE);
        $CoursePackageModel = CoursePackageModel::query();
        $CoursePackageModel->where([
            ["type","=","0"],
        ])->update(['course_attach'=>$packageAttachJson]);
    }

    /**
     * 设计优惠相关数据
     */
    function setRebate(){
        $CoursePackageModel = CoursePackageModel::query();
        $packageList = $CoursePackageModel->where([
            ['type','=','0'],
        ])->get();
        //赠送课程
        $give = [
            0=>['id'=>0,'text'=>'无','checked'=>false],
            1=>['id'=>1,'text'=>'英语口语','checked'=>false],
            2=>['id'=>2,'text'=>'AE','checked'=>false],
            3=>['id'=>'3','text'=>'转手绘','checked'=>false],
            4=>['id'=>'4','text'=>'H5','checked'=>false],
            5=>['id'=>'5','text'=>'JAVA','checked'=>false],
            6=>['id'=>'6','text'=>'手绘','checked'=>false],
            7=>['id'=>'7','text'=>'素描','checked'=>false],
            8=>['id'=>'8','text'=>'色彩','checked'=>false],
            9=>['id'=>'9','text'=>'广告','checked'=>false],
            10=>['id'=>'10','text'=>'摄影','checked'=>false],
            11=>['id'=>'11','text'=>'美妆','checked'=>false],
            12=>['id'=>'12','text'=>'摄影实战班','checked'=>false],
            13=>['id'=>'13','text'=>'C4D','checked'=>false],
            14=>['id'=>'14','text'=>'视频摄制','checked'=>false],
        ];
        $giveList = collect($give)->pluck('text');
        //优惠
        $rebateActivityList = RebateActivityModel::all();
        foreach ($packageList as $key=>$val){
            foreach ($rebateActivityList as $i=>$l){
                $l->replicate();
                $l->package_id = $val->id;
                $data = $l->toArray();
                $data['course_give'] = json_encode($giveList,JSON_UNESCAPED_UNICODE);
                unset($data['id']);
                $l->create($data);
            }
        }
    }

    function setRegistrationAttach(){
        set_time_limit (0);
        //package_attach_content
        $data = [];
        //套餐
        $attachList = CoursePackageModel::where('type','1')->get();
        //优惠
        $rebateList = RebateActivityModel::all();
        //报名
        //$userList = UserRegistrationModel::all();
        $i = 0;
        UserRegistrationModel::where("id",">",21187)->select("package_id","give_id","id","package_attach_id","rebate_id")->chunk(3000, function ($list,$page) use(&$attachList,&$rebateList,&$i){
                foreach ($list as $key=>$val){
                    echo $i++."\n<br/>";
                    $PackageModel = CoursePackageModel::with("rebate")->find($val['package_id']);
                    if(!$PackageModel){
                        continue;
                    }
                    $data['package_info'] = $PackageModel->toArray();
                    unset($data['package_info']['course_attach']);
                    //设置package_attach_id 附加套餐
                    $data['package_attach_id'] = "";
                    if($val->package_attach_id){
                        foreach ($attachList as $k=>$v){
                            if($val->package_attach_id == $v->id){
                                $data['package_attach_id'] = $k;
                            }
                        }
                    }

                    //设置rebate_id 优惠活动
                    $data['package_rebate_id'] = "";
                    if($val->rebate_id){
                        foreach ($rebateList as $k=>$v){
                            if($val->rebate_id == $v->id){
                                $data['package_rebate_id'] = $k;
                            }
                        }
                    }

                    //设置package_course_id 赠送课程
                    $data['package_course_id'] = $val->give_id;
                    UserRegistrationModel::query()->where("id",$val->id)->update([
                        'package_attach_content'    =>  json_encode($data,JSON_UNESCAPED_UNICODE)
                    ]);
                }
                if($page == 1){
                    exit;
                }
        });

    }

    /**
     * 设置登记表的信息
     */
    function setEnroll(){
        set_time_limit(0);
        $i = 0;
        UserRegistrationModel::select("id","name","adviser_id","adviser_name","mobile","qq")->chunk(1000, function (&$list,$page) use(&$i){
            if($page == 1){
                foreach ($list as $key=>$val){
                    echo $i++."\n<br/>";
                    $enroll = [
                        'name'              =>  $val->name,
                        'adviser_id'        =>  $val->adviser_id,
                        'adviser_name'      =>  $val->adviser_name,
                        'mobile'            =>  $val->mobile,
                        'qq'                =>  $val->qq,
                        'wx'                =>  '',
                        'is_guide'          =>  0
                    ];
                    $lastData = UserEnrollModel::query()->create($enroll);
                    UserRegistrationModel::query()->where('id',$val->id)->update([
                        'enroll_id' =>  $lastData->id,
                        'school_id' =>  'SJ'
                    ]);
                }
            }else{
                return false;
            }
        });
    }


    function setUserPayLog(){
        DB::update('update user_pay_log upl JOIN user_registration ur on upl.registration_id = ur.id set upl.enroll_id = ur.enroll_id');
    }

}
