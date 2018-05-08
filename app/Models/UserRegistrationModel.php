<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25 0025
 * Time: 11:25
 */

namespace App\Models;


use App\Exceptions\DapengApiException;
use App\Exceptions\UserValidateException;
use App\Utils\Api\DapengUserApi;
use App\Utils\Util;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserRegistrationModel extends BaseModel{
    protected $table = "user_registration";
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    protected $dates = [
        //'last_pay_time'
    ];
    protected $appends = [
        "is_belong","is_open_text","fq_type_text","sub_price","last_pay_time_text","is_bright"
    ];
    //报名分期付款方式
    public $fqType = [
        'CASH'      =>  '现金分期',
        'HUABEI'    =>  '花呗分期',
        'MYFQ'      =>  '蚂蚁分期',
    ];

    //开课状态数组
    public $isOpenArr = ['未开课','部分开课','全部开课'];

    //获取分期类型
    public function getFqTypeTextAttribute(){
        if(in_array($this->fq_type,array_keys($this->fqType)))
            return $this->fqType[$this->fq_type];
        else
            return "无分期";
    }
    //获取开课状态
    public function getIsOpenTextAttribute(){
        return $this->isOpenArr[$this->is_open];
    }
    //获取套餐总价格
    public function getPackageTotalPriceAttribute(){
        return floatval($this->coursePackage->price) + floatval($this->coursePackageAttach->price);
    }
    /**
     * 获取isBelong
     * @return int
     */
    public function getIsBelongAttribute(){
        return 1;
    }
    /**
     * 获取应交金额
     * @return mixed
     */
    public function getSubPriceAttribute(){
        return $this->package_total_price - $this->rebate;
    }

    /**
     * 获取提交时间文字
     * @return false|string
     */
    public function getLastPayTimeTextAttribute(){
        //$pay_time = UserPayLogModel::where("registration_id",$this->id)->orderBy("id","desc")->value("pay_time");
        return date("Y-m-d H:i:s",$this->last_pay_time);
    }

    /**
     * 是否记录高亮显示
     * @return int
     */
    public function getIsBrightAttribute(){
        return $this->create_time > date("Y-m")."-16 00:00:00" ? 1 : 0;
    }

    /**
     * 获取支付记录模型
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userPayLog(){
        return $this->hasMany(UserPayLogModel::class,'registration_id','id');
    }

    /**
     * 获得关联的主套餐课程。
     */
    public function coursePackage()
    {
        return $this->belongsTo(CoursePackageModel::class,'package_id','id')->withDefault();
    }
    /**
     * 获得关联的副套餐课程。
     */
    public function coursePackageAttach()
    {
        return $this->belongsTo(CoursePackageModel::class,'package_attach_id','id')->withDefault();
    }

    /**
     * 获取优惠活动信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rebateActivity(){
        return $this->belongsTo(RebateActivityModel::class,'rebate_id','id')->withDefault();
    }

    /**
     * 获取管理员记录买模型信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userHeadmaster(){
        return $this->belongsTo(UserModel::class,'adviser_id','uid')->withDefault();
    }

    /**
     * 更新数据库验证
     * @param $data
     * @return mixed
     */
    static function updateValidate($data){
        $validator = Validator::make($data, [
            'registration_id'      =>  'sometimes|numeric|exists:user_registration,id',
            'mobile'            => [
                'sometimes',
                'required',
                'regex:/\d{11}/',
                Rule::unique('user_registration')->ignore($data['registration_id']),
            ],
            'name'              => 'sometimes|max:255',
            'qq'                => [
                'sometimes',
                'required',
                'regex:/\d{5,11}/',
                Rule::unique('user_registration')->ignore($data['registration_id']),
            ],
            'package_id'        => 'sometimes|exists:course_package,id',
            'package_attach_id' => 'sometimes|exists:course_package,id',
            'rebate_id'         => 'sometimes|exists:rebate_activity,id',
            'amount_submitted'  =>  'sometimes|required|numeric'
        ],[
            'registration_id.required'       =>  '更新的支付记录错误！',
            'registration_id.numeric'        =>  '更新的支付记录错误！',
            'registration_id.exists'         =>  '未找到要修改的支付记录',
            'mobile.required'   =>  '请输入正确的学员手机号！',
            'mobile.unique'     =>  '该学员手机号已存在！',
            'name.required'     =>  '请输入学员姓名！',
            'name.max'          =>  '学员姓名格式错误！',
            'qq.required'       =>  '请输入学员QQ号！',
            'qq.regex'          =>  '学员QQ号格式错误！',
            'qq.unique'         =>  '学员QQ号已存在！',
            'package_id.exists' =>  '请选择正确的课程主套餐！',
            'package_attach_id.exists' =>  '请选择正确的课程副套餐！',
            'rebate_id.exists'  =>  '请选择正确的优惠活动！',
            'amount_submitted,required'=>   '已提交金额有误！',
            'amount_submitted,numeric'=>   '已提交金额有误！',
        ]);
        $validator->validate();
    }

    /**
     * 新增报名时补全入库字段信息
     * @param array $data
     *          int mobile:开课手机号
     *          int uid:顾问ID,
     *          array pay_type_list:支付方式列表[ALIPAY:支付宝余额支付, HUABEI:花呗,HUABEIFQ:花呗分期, WEIXIN:微信支付, MAYIFQ:蚂蚁分期, BANKZZ:银行转账]
     *          array amount_list:支付记录的金额
     *          int give_id:赠送课程，逗号隔开的ID集合
     *
     * @return array
     * @throws DapengApiException
     * @throws UserValidateException
     */
    function completeData(array $data){
        $tmpMap = [];
        if($data['client_submit'] == "WAP"){
            $tmpMap = ['mobile','=',$data['adviser_mobile']];
        }else if($data['client_submit'] == "PC"){
            $adminInfo = $this->getUserInfo();
            $tmpMap = ['uid','=',$adminInfo['uid']];
        }

        //判断手机号是否在主站注册过
        $dpData = [
            'type'      =>  'MOBILE',
            'keyword'   =>  $data['mobile'],
        ];
        $hasDapengUser = DapengUserApi::getInfo($dpData);
        if($hasDapengUser['code'] == Util::FAIL){
            throw new DapengApiException("该开课手机号未注册！");
        }
        $hasAdviser = UserHeadMasterModel::where([$tmpMap])->first();
        if(!$hasAdviser){
            throw new UserValidateException("课程顾问不存在！");
        }

        //检查报名信息 所属课程顾问
//        if($data['mobile'] != $hasAdviser->mobile){
//            throw new UserValidateException("该学员与课程顾问信息不一致！");
//        }


        $data['adviser_id'] = $hasAdviser->uid;
        $data['adviser_name'] = $hasAdviser->name;
        $data['adviser_qq'] = $hasAdviser->qq;
        //验证支付金额信息
        $validator = Validator::make($data,[
            'pay_type_list'     =>  'required|array',
            'pay_type_list.*'   =>  [Rule::in(array_keys(app("status")->getPayTypeList()))],
            'amount_list'       =>  'required|array',
            'amount_list.*'     =>  'required|numeric',
            'give_id'           =>  'required',

        ],[
            'pay_type_list.required'    =>  '请选择支付方式！',
            'pay_type_list.array'       =>  '请选择支付方式！',
            'amount_list.required'       =>  '请输入支付金额！',
            'amount_list.array'         =>  '请输入支付金额！',
            'pay_type_list.*.in'         =>  '请选择正确的支付方式！',
            'amount_list.*.numeric'      =>  '请输入正确的支付金额！',
            'give_id.required'          =>  '请选择要赠送的课程！',
        ]);
        //执行验证
        $validator->validate();
//        if($post['amount_submitted'] > $post['package_total_price'])
//        $this->returnAjaxJson(FAIL,'已提交金额不能大于总金额！');
//        $post['amount_submitted'] = $post['amount_submitted']+$post['amount'];
//        $post['amount_submitted'] = $post['amount'];
        //提交的总金额
        $allAmount= array_sum($data['amount_list']);
        if($allAmount<=0){
            throw new UserValidateException("请填写正确的支付金额！");
        }
        $data['amount_submitted'] = $allAmount;
        return $data;

    }


    /**
     * 新增数据
     * @param $data
     * @return mixed
     */
    function addData($data,$UserPayModel,$UserPayLogModel){
        $validator = Validator::make($data, [
            'mobile'            =>'required|regex:/\d{11}/|unique:user_registration',
            'name'              => 'required|unique:user_registration|max:255',
            'qq'                => 'required|regex:/\d{5,11}/|unique:user_registration',
            'package_id'        => 'exists:course_package,id',
            'package_attach_id' => 'exists:course_package,id',
            'rebate_id'         => 'exists:rebate_activity,id',
            'amount_submitted'  =>  'required|numeric'
        ],[
            'mobile.required'   =>  '请输入正确的学员手机号！',
            'mobile.unique'     =>  '该学员手机号已存在！',
            'name.required'     =>  '请输入学员姓名！',
            'qq.required'       =>  '请输入学员QQ号！',
            'qq.regex'          =>  '学员QQ号格式错误！',
            'qq.unique'         =>  '学员QQ号已存在！',
            'package_id.exists' =>  '请选择正确的课程主套餐！',
            'package_attach_id.exists' =>  '请选择正确的课程副套餐！',
            'rebate_id.exists'  =>  '请选择正确的优惠活动！',
            'amount_submitted.required'=>   '已提交金额有误！',
            'amount_submitted.numeric'=>   '已提交金额有误！',
        ]);
        //执行验证
        $validator->validate();
        //$data = $this->getColumns($data);
        //开启事务
        return DB::transaction(function () use($UserPayModel,$UserPayLogModel,$data){
            $resReg = self::create($data);
            //添加用户支付信息
            $data['registration_id'] = $resReg['id']; //关联报名课程记录ID
            unset($data['id']);
            $resUserPay = $UserPayModel->addData($data);
            //循环添加多个支付方式记录
            $data['pay_id'] = $resUserPay->id;
            foreach ($data['pay_type_list'] as $key=>$val){
                $data['amount'] = $data['amount_list'][$key];
                $data['pay_time'] = strtotime($data['pay_time_list'][$key]);
                $data['pay_type'] = $val;
                $UserPayLogModel->addData($data);
            }
            //重置套餐全名
            $eff = $this->setPackageAllTitle($resReg['id']);
            if(!$eff){
                throw new UserValidateException("重置套餐全名失败！");
            }
            //更新报名信息的最后一次提交支付记录时间
            //$this->setLastPayTime($resReg['id']);
        });
    }

    /**
     * 更新数据
     * @param $data
     * @return bool
     */
    function updateData($data,$UserPayModel,$UserPayLogModel){
        $registrationId = $data['registration_id'];
        $validator = Validator::make($data, [
            'registration_id'      =>  'sometimes|numeric|exists:user_registration,id',
            'mobile'            => [
                'sometimes',
                'required',
                'regex:/\d{11}/',
                Rule::unique('user_registration')->ignore($data['registration_id']),
                ],
            'name'              => 'sometimes|max:255',
            'qq'                => [
                'sometimes',
                'required',
                'regex:/\d{5,11}/',
                Rule::unique('user_registration')->ignore($data['registration_id']),
                ],
            'package_id'        => 'sometimes|exists:course_package,id',
            'package_attach_id' => 'sometimes|exists:course_package,id',
            'rebate_id'         => 'sometimes|exists:rebate_activity,id',
            'amount_submitted'  =>  'sometimes|required|numeric'
        ],[
            'registration_id.required'       =>  '更新的支付记录错误！',
            'registration_id.numeric'        =>  '更新的支付记录错误！',
            'registration_id.exists'         =>  '未找到要修改的支付记录',
            'mobile.required'   =>  '请输入正确的学员手机号！',
            'mobile.unique'     =>  '该学员手机号已存在！',
            'name.required'     =>  '请输入学员姓名！',
            'name.max'          =>  '学员姓名格式错误！',
            'qq.required'       =>  '请输入学员QQ号！',
            'qq.regex'          =>  '学员QQ号格式错误！',
            'qq.unique'         =>  '学员QQ号已存在！',
            'package_id.exists' =>  '请选择正确的课程主套餐！',
            'package_attach_id.exists' =>  '请选择正确的课程副套餐！',
            'rebate_id.exists'  =>  '请选择正确的优惠活动！',
            'amount_submitted,required'=>   '已提交金额有误！',
            'amount_submitted,numeric'=>   '已提交金额有误！',
        ]);
        $validator->validate();

        //开启事务
        $eff = DB::transaction(function () use($UserPayModel,$UserPayLogModel,$data){
            //添加用户支付信息
            $resUserPay = $UserPayModel->addData($data);
            //循环添加多个支付方式记录
            $data['pay_id'] = $resUserPay['id'];
            $resUserPayLog = "";
            foreach ($data['pay_type_list'] as $key=>$val){
                $data['amount'] = $data['amount_list'][$key];
                $data['pay_time'] = strtotime($data['pay_time_list'][$key]);
                $data['pay_type'] = $val;
                $resUserPayLog = $UserPayLogModel->addData($data);
            }
            $data['last_pay_time'] = $resUserPayLog['create_time'];
            //重置套餐全名
            return $this->setPackageAllTitle($data['registration_id']);
            //重置优惠活动价格
//            if(isset($data['rebate_id']) && $data['rebate_id']){
//                $rebateData = RebateActivityModel::where(['id'=>$data['rebate_id']])
//                    ->first();
//                if($rebateData && isset($rebateData['price'])){
//                    $data['rebate'] = $rebateData['price'];
//                }
//            }
        });
        $data = $this->getColumns($data);
        $data['amount_submitted'] = DB::raw('amount_submitted+'.$data['amount_submitted']);
        return self::where('id',$registrationId)->update($data);
    }


    /**
     * 重新设置套餐总金额
     * @param UserRegistrationModel $userRegistrationModel
     */
    function setPackageAll(UserRegistrationModel $userRegistrationModel){
        //主套餐价格
        $package = CoursePackageModel::find($userRegistrationModel->package_id);
        $packageAttach = CoursePackageModel::find($userRegistrationModel->package_attach_id);
        $this->attributes['package_total_price'] = $package->price+$packageAttach->price;
        //套餐全名
        $packageAllTitle = $package->title."+".$packageAttach->title;
        $giveTitle = "";
        $giveArr = explode(',',$userRegistrationModel->give_id);
        foreach ($giveArr as $key=>$val){
            $giveTitle .= CoursePackageModel::$giveList[$val]['text']."+";
        }
        $packageAllTitle .= trim("+赠送".$giveTitle);
        $this->attributes['package_all_title'] = $packageAllTitle;
    }

    /**
     * 设置开课报名学员的 套餐全名和重新套餐应付计算金额
     * @param $id
     */
    function setPackageAllTitle($id){
        $regData = self::where('id',$id)->first();

        $data['package_all_title'] = '';
        $CoursePackage = new CoursePackageModel();
        //主套餐
        $packageData = $CoursePackage::where([
                ['id','=',$regData['package_id']],
                ['type','=',0]
            ])->first();

        $data['package_all_title'] .= $packageData['title'];
        //附加套餐
        $packageAttachData = $CoursePackage::where([
            ['id','=',$regData['package_attach_id']],
            ['type','=',1]
        ])->first();
        if($packageAttachData)
            $data['package_all_title'] .= "+".$packageAttachData['title'];

        //赠送课程
        if($regData['give_id']){
            $giveTitle = "";
            $giveArr = explode(',',$regData['give_id']);
            foreach ($giveArr as $key=>$val){
                $giveTitle .= $CoursePackage->give[$val]['text']."+";
            }

            $data['package_all_title'] .= "+赠送".$giveTitle;
            $data['package_all_title'] = trim($data['package_all_title'],"+");
        }

        //套餐总金额
        $data['package_total_price'] = $packageData['price']+$packageAttachData['price'];
        $data['id'] = $id;
        //return $this->updateData($data);
        return self::where('id',$id)->update([
            'package_all_title'     =>  $data['package_all_title'],
            'package_total_price'   =>  $data['package_total_price']
        ]);
    }

    /**
     * @note 设置报名信息 最后一次的提交支付记录的时间
     * @param $registration_id
     */
    function setLastPayTime($id){
        $lastPayTime = UserPayLogModel::where('registration_id',$id)
            ->orderBy("id","desc")
            ->value("create_time");
        return self::where('id',$id)->update(['last_pay_time'=>$lastPayTime]);
    }

}