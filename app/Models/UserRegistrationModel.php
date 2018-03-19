<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25 0025
 * Time: 11:25
 */

namespace App\Models;


use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserRegistrationModel extends BaseModel{
    protected $table = "user_registration";
    public $timestamps = true;
    protected $dateFormat = 'U';
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $appends = [
        "is_belong"
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
    public function getFqTypeAttribute($value){
        if(in_array($value,$this->fqType))
            return $this->fqType[$value];
        else
            return "无分期";
    }
    //获取开课状态
    public function getIsOpenAttribute($value){
        return $this->isOpenArr[$value];
    }
    //获取套餐总价格
    public function getPackageTotalPriceAttribute(){
        return $this->coursePackage->price+$this->coursePackageAttach->price;
    }

    /**
     * 获取isBelong
     * @return int
     */
    public function getIsBelongAttribute(){
        return 1;
    }

    /**
     * 获得关联的主套餐课程。
     */
    public function coursePackage()
    {
        return $this->belongsTo(CoursePackageModel::class,'package_id','id');
    }
    /**
     * 获得关联的副套餐课程。
     */
    public function coursePackageAttach()
    {
        return $this->belongsTo(CoursePackageModel::class,'package_attach_id','id');
    }

    /**
     * 获取优惠活动信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rebateActivity(){
        return $this->belongsTo(RebateActivityModel::class,'rebate_id','id');
    }




    /**
     * 新增数据
     * @param $data
     * @return mixed
     */
    function addData($data){
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
            'amount_submitted,required'=>   '已提交金额有误！',
            'amount_submitted,numeric'=>   '已提交金额有误！',
        ]);
        $data = $this->getColumns($data);
        //执行验证
        $validator->validate();
        return self::create($data);
    }

    /**
     * 更新数据
     * @param $data
     * @return bool
     */
    function updateData($data){
        $validator = Validator::make($data, [
            'id'                =>  'sometimes|numeric|exists:user_registration,id',
            'mobile'            => [
                'sometimes',
                'required',
                'regex:/\d{11}/',
                Rule::unique('user_registration')->ignore($data['id']),
                ],
            'name'              => 'sometimes|required|max:255',
            'qq'                => [
                'sometimes',
                'required',
                'regex:/\d{5,11}/',
                Rule::unique('user_registration')->ignore($data['id']),
                ],
            'package_id'        => 'sometimes|exists:course_package,id',
            'package_attach_id' => 'sometimes|exists:course_package,id',
            'rebate_id'         => 'sometimes|exists:rebate_activity,id',
            'amount_submitted'  =>  'sometimes|required|numeric'
        ],[
            'id.required'       =>  '更新的支付记录错误！',
            'id.numeric'        =>  '更新的支付记录错误！',
            'id.exists'         =>  '未找到要修改的支付记录',
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
        $data = $this->getColumns($data);
        //重置优惠活动价格
        if(isset($data['rebate_id']) && $data['rebate_id']){
            $RebateActivity = new RebateActivityModel();
            $rebateData = RebateActivityModel::where(['id'=>$data['rebate_id']])
                ->first();
            if($rebateData && isset($rebateData['price'])){
                $data['rebate'] = $rebateData['price'];
            }
        }
        $res = self::find($data['id']);
        return $res->save();
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
        return $this->updateData($data);
//        self::where('id',$id)->update([
//            'package_all_title'     =>  $data['package_all_title'],
//            'package_total_price'   =>  $data['package_total_price']
//        ]);
    }

    /**
     * @note 设置报名信息 最后一次的提交支付记录的时间
     * @param $registration_id
     */
    function setLastPayTime($id){
        $UserPayLog = new UserPayLogModel();
        $lastPayTime = $UserPayLog::where('registration_id',$id)
            ->orderBy("id","desc")
            ->value("create_time");
        self::where('id',$id)->update(['last_pay_time'=>$lastPayTime]);
    }

}