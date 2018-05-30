@extends("admin.public.layout")
@section("script")
    <style>
        [v-cloak] { display: none }
    </style>
    <script>
        //选择主套餐课程
        function selectPackage(index){
            vm.currentPos = index;
            if(vm[vm.currentPos].data.readonly == true){
                return false;
            }
            vm.list = vm[vm.currentPos].data.packageList;
            layer.open({
                type:1,
                title:"请选择主套餐课程",
                area:['700px','550px'],
                content:$(".package")
            });
            //默认选中
            $("input[default]").prop("checked","checked");
            //$(".select_package").show();
        }

        /**
         * 选择主套餐后回调
         * @param obj
         */
        function selectPackageCallback(obj){
            if(vm[vm.currentPos].data.readonly == true){
                return false;
            }
            vm[vm.currentPos].data.selectedPackage = [];
            $(obj).parent().find("input[type='radio']:checked").each(function () {
                vm[vm.currentPos].data.selectedPackage.push($(this).val());
            });
            layer.closeAll();
        }
        //选择附加课程
        function selectPackageAttach(index){
            vm.currentPos = index;
            if(vm[vm.currentPos].data.readonly == true){
                return false;
            }
            vm.list = vm[vm.currentPos].data.packageAttach;
            vm.$nextTick(function(){
                layer.open({
                    type:1,
                    title:"请选择附加课程",
                    area:['700px','550px'],
                    content:$(".tc_dialog").find(".packageAttach").prop("outerHTML")
                })
                vm.list = '';
                //默认选中
                $("input[default]").prop("checked","checked");
            })
        }
        /**
         * 选择附加套餐后回调
         * @param obj
         */
        function selectPackageAttachCallback(obj){
            if(vm[vm.currentPos].data.readonly == true){
                return false;
            }
            vm[vm.currentPos].data.selectedPackageAttach = [];
            $(obj).parent().find("input[type='checkbox']:checked").each(function () {
                vm[vm.currentPos].data.selectedPackageAttach.push($(this).val());
            });
            layer.closeAll();
        }
        //选择赠送课程
        function selectPackageCourse(index){
            vm.currentPos = index;
            if(vm[vm.currentPos].data.readonly == true){
                return false;
            }
            vm.list = vm[vm.currentPos].data.packageCourse;
            vm.$nextTick(function () {
                layer.open({
                    type:1,
                    title:"请选择赠送课程",
                    area:['700px','550px'],
                    content:$(".tc_dialog").find(".packageCourse").prop("outerHTML")
                })
                vm.list = '';
                //默认选中
                $("input[default]").prop("checked","checked");
            })

        }
        /**
         * 选择赠送课程后回调
         * @param obj
         */
        function selectPackageCourseCallback(obj){
            if(vm[vm.currentPos].data.readonly == true){
                return false;
            }
            vm[vm.currentPos].data.selectedPackageCourse = [];
            $(obj).parent().find("input[type='checkbox']:checked").each(function () {
                vm[vm.currentPos].data.selectedPackageCourse.push($(this).val());
            });
            layer.closeAll();
        }
        //添加支付信息
        function addPayInfo(index){
            vm.currentPos = index;
            vm.$nextTick(function(){
                layer.open({
                    type:1,
                    title:"添加支付信息",
                    shadeClose:true,
                    area:['500px','300px'],
                    content:$(".tc_dialog").find(".tc_pay").prop("outerHTML")
                })
                vm.list = '';
                $(".layui-layer .datetime").each(function(){
                    var _this = $(this);
                    laydate.render({
                        elem: _this[0], //指定元素
                        type:'datetime'
                    });
                });

            })

        }

        /**
         * 添加支付记录后回调
         */
        function addPayInfoCallback(obj){
            var form = $(obj).parents("form");
            var json = {};
            json.pay_type = form.find("select[name='pay_type']").val();
            json.amount = form.find("input[name='amount']").val();
            json.pay_time = form.find("input[name='pay_time']").val();
            json.readonly = false;
            if(!json.pay_type){
                layer.msg("请选择支付类型",{icon:2});
                return ;
            }
            if(!json.amount || parseFloat(json.amount) <= 0){
                layer.msg("请输入合法的支付金额",{icon:2});
                return ;
            }
            if(!json.pay_time){
                layer.msg("请选择支付时间",{icon:2});
                return ;
            }
            vm[vm.currentPos].data.payList.push(json);
            layer.closeAll();
        }


        /**
         * 至少提交一笔支付记录
         */
        function checkPayList(){
            if(vm.first.data.payList.length +vm.second.data.payList.length == 0){
                layer.msg("至少提交一笔支付记录",{icon:2});
                return false;
            }
        }
        var vm;
        $(function(){
            vm = new Vue({
                el:".sub_main",
                data:{
                    packageList:{!! collect($packageList)->toJson() !!},//所有的套餐列表
                    userRole:"{{ $userRole }}",  //用户身份
                    payType:{!! collect($payTypeList)->toJson() !!},
                    enroll:{!! collect($enroll)->toJson() !!},
                    first:{
                        'name': '',
                        'data':{
                            readonly:false,
                            registration_id:'',  //报名ID
                            packageList:[], //主套餐列表
                            packageAttach:[], //附加课程
                            packageCourse:[], //赠送课程
                            packageRebate:[], //所有的优惠活动
                            selectedPackage:[], //已选中主套餐索引
                            selectedPackageAttach:[],//已选中附加套餐索引
                            selectedPackageCourse:[], //已选中赠送课程索引
                            selectedPackageRebate:'', //选中的优惠活动
                            rebatePrice:0, //填写的优惠金额
                            payList:[], //支付列表
                        }
                    },//第一份报名,默认为美术学院
                    second:{
                        'name': '',
                        'data':{
                            readonly:false,
                            registration_id:'',  //报名ID
                            packageList:[], //主套餐列表
                            packageAttach:[], //附加课程
                            packageCourse:[], //赠送课程
                            packageRebate:[], //所有的优惠活动
                            selectedPackage:[], //已选中主套餐索引
                            selectedPackageAttach:[],//已选中附加套餐索引
                            selectedPackageCourse:[], //已选中赠送课程索引
                            selectedPackageRebate:'', //选中的优惠活动
                            rebatePrice:0, //填写的优惠金额
                            payList:[], //支付列表
                        },
                    },
                    list:[],//弹窗列表
                    currentPos:'first',
                    searchKey:'', //搜索关键字
                    //第二份报名默认为设计
                },
                computed:{
                    //总金额
                    firstTotalPrice:function(){
                        var total = 0;
                        if(this.first.data.selectedPackage){
                            for(var i=0;i<this.first.data.selectedPackage.length;i++){
                                total += parseFloat(this.first.data.packageList[this.first.data.selectedPackage[i]].price);
                            }
                        }
                        if(this.first.data.selectedPackageAttach){
                            for(var i=0;i<this.first.data.selectedPackageAttach.length;i++){
                                total += parseFloat(this.first.data.packageAttach[this.first.data.selectedPackageAttach[i]].price);
                            }
                        }
                        if(this.first.data.rebatePrice){
                            total -= this.first.data.rebatePrice;
                        }
                        return total;
                    },
                    //附加套餐总金额
                    firstPackageAttachPrice:function(){
                        var total = 0;
                        if(this.first.data.selectedPackageAttach){
                            for(var i=0;i<this.first.data.selectedPackageAttach.length;i++){
                                total += parseFloat(this.first.data.packageAttach[this.first.data.selectedPackageAttach[i]].price);
                            }
                        }
                        return total;
                    },
                    secondTotalPrice:function(){
                        var total = 0;
                        if(this.second.data.selectedPackage){
                            for(var i=0;i<this.second.data.selectedPackage.length;i++){
                                total += parseFloat(this.second.data.packageList[this.second.data.selectedPackage[i]].price);
                            }
                        }
                        if(this.second.data.selectedPackageAttach){
                            for(var i=0;i<this.second.data.selectedPackageAttach.length;i++){
                                total += parseFloat(this.second.data.packageAttach[this.second.data.selectedPackageAttach[i]].price);
                            }
                        }
                        if(this.second.data.rebatePrice){
                            total -= this.second.data.rebatePrice;
                        }
                        return total;
                    },
                    //附加套餐总金额
                    secondPackageAttachPrice:function(){
                        var total = 0;
                        if(this.second.data.selectedPackageAttach){
                            for(var i=0;i<this.second.data.selectedPackageAttach.length;i++){
                                total += parseFloat(this.second.data.packageAttach[this.second.data.selectedPackageAttach[i]].price);
                            }
                        }
                        return total;
                    },
                },
                methods:{
                    //默认选中
                    defaultChecked:function(value,key){
                        var flag = false;
                        for(var i=0;i<vm[vm.currentPos].data[key].length;i++){
                            if(value == vm[vm.currentPos].data[key][i]){
                                flag = true;
                            }
                        }
                        return flag?true:false;
                    },
                    //删除支付记录
                    removePayList:function(key,index){
                        console.log(this[key].data.payList[index]);
                        this[key].data.payList.splice(index,1);
                    },
                    addSecond:function(){
                        if(this.first.name="SJ"){
                            this.second.name="MS";
                        }else {
                            this.second.name="SJ";
                        }

                    },
                    removeSecond:function(){
                        this.second.name="";
                    },
                    objtostr:function(obj){
                        return JSON.stringify(obj);
                    },
                    searchList:function(){
                        var filterKey = this.searchKey && this.searchKey.toLowerCase();
                        var data = this.list;
                        if (filterKey) {
                            data = data.filter(function (row) {
                                return Object.keys(row).some(function (key) {
                                    return String(row[key]).toLowerCase().indexOf(filterKey) > -1
                                })
                            })
                        }
                        this.list = data;
                        //return data
                    }
                },
                watch:{
                    //变更学院后，变更套餐信息
                    "first.name":function(newName,oldName){
                        if(newName == this.second.name){
                            this.first.name = oldName;
                            return ;
                        }
                        if(newName)
                            this.first.data.packageList = this.packageList[newName];
                    },
                    "second.name":function(newName,oldName){
                        if(newName == this.first.name){
                            this.second.name = oldName;
                            return ;
                        }
                        if(newName)
                            this.second.data.packageList = this.packageList[newName];
                    },
                    //选择主套餐后，变更主套餐信息
                    "first.data.selectedPackage":function(newVal,oldVal){
                        for(var i=0;i<newVal.length;i++){
                            var packageInfo = this.first.data.packageList[newVal[i]];
                            this.first.data.packageAttach = packageInfo.course_attach_data.attach;
                            this.first.data.packageCourse = packageInfo.course_attach_data.give;
                            this.first.data.packageRebate = packageInfo.course_attach_data.rebate;
                        }
                    },
                    "first.data.rebatePrice":function(newVal,oldVal){
                        if(this.first.data.selectedPackageRebate === ''){
                            //没有选中优惠活动则跳过
                            return ;
                        }
                        //获取当前优惠最大数字
                        newVal = parseFloat(newVal);
                        if(newVal < 1 || newVal > parseFloat(this.first.data.packageRebate[0].price)){
                            layer.msg("数字不合法,只能在1-"+this.first.data.packageRebate[0].price+'之间');
                            this.first.data.rebatePrice = oldVal;
                        }
                    },
                    "first.data.selectedPackageRebate":function(newVal,oldVal){
                        if(newVal === ''){
                            //没有选中优惠
                            this.first.data.rebatePrice = 0;
                        }else{
                            this.first.data.rebatePrice = this.first.data.packageRebate[newVal].price;
                        }
                    },


                    //选择主套餐后，变更主套餐信息
                    "second.data.selectedPackage":function(newVal,oldVal){
                        for(var i=0;i<newVal.length;i++){
                            var packageInfo = this.second.data.packageList[newVal[i]];
                            this.second.data.packageAttach = packageInfo.course_attach_data.attach;
                            this.second.data.packageCourse = packageInfo.course_attach_data.give;
                            this.second.data.packageRebate = packageInfo.course_attach_data.rebate;
                        }
                    },
                    "second.data.rebatePrice":function(newVal,oldVal){
                        if(this.second.data.selectedPackageRebate === ''){
                            //没有选中优惠活动则跳过
                            return ;
                        }
                        //获取当前优惠最大数字
                        newVal = parseFloat(newVal);
                        if(newVal < 1 || newVal > parseFloat(this.second.data.packageRebate[0].price)){
                            layer.msg("数字不合法,只能在1-"+this.second.data.packageRebate[0].price+'之间');
                            this.second.data.rebatePrice = oldVal;
                        }
                    },
                    "second.data.selectedPackageRebate":function(newVal,oldVal){
                        if(newVal === ''){
                            //没有选中优惠
                            this.second.data.rebatePrice = 0;
                        }else{
                            this.second.data.rebatePrice = this.second.data.packageRebate[newVal].price;
                        }
                    },


                },
                mounted:function () {
                    if(JSON.stringify(this.enroll) == '{}'){
                        this.first.name="SJ";
                    }else{
                        var t = 0;
                        for(var schoolName in this.enroll.registrations){
                            var package_attach_content =  this.enroll.registrations[schoolName].package_attach_content;
                            var packageInfo = package_attach_content.package_info;
                            t++;
                            if(t == 1){
                                this.first.name = schoolName;
                                //课程顾问只有查看权限
                                if(this.userRole == "adviser"){
                                    this.first.data.readonly = true;
                                }
                                this.first.data.registration_id = this.enroll.registrations[this.first.name].id;
                                //this.first.data.packageList[packageInfo.id] = packageInfo;
                                this.first.data.selectedPackage.push(packageInfo.id);
                                package_attach_content.package_attach_id && (this.first.data.selectedPackageAttach = package_attach_content.package_attach_id.split(','));
                                package_attach_content.package_course_id && (this.first.data.selectedPackageCourse = package_attach_content.package_course_id.split(','));
                                this.first.data.selectedPackageRebate = package_attach_content.package_rebate_id;
                                this.first.data.payList = this.enroll.registrations[this.first.name].payList || [];
                                this.$nextTick(function(){
                                    this.first.data.rebatePrice = parseFloat(this.enroll.registrations[this.first.name].rebate);
                                })
                                //添加附加套餐
                            }else if (t == 2){
                                this.second.name = schoolName;
                                //课程顾问只有查看权限
                                if(this.userRole == "adviser"){
                                    this.second.data.readonly = true;
                                }
                                this.second.data.registration_id = this.enroll.registrations[this.second.name].id;
                                //this.second.data.packageList[packageInfo.id] = packageInfo;
                                this.second.data.selectedPackage.push(packageInfo.id);
                                package_attach_content.package_attach_id && (this.second.data.selectedPackageAttach = package_attach_content.package_attach_id.split(','));
                                package_attach_content.package_course_id && (this.second.data.selectedPackageCourse = package_attach_content.package_course_id.split(','));
                                this.second.data.selectedPackageRebate = package_attach_content.package_rebate_id;
                                this.second.data.payList = this.enroll.registrations[this.second.name].payList;
                                this.$nextTick(function(){
                                    this.second.data.rebatePrice = parseFloat(this.enroll.registrations[this.second.name].rebate);
                                })
                            }
                        }
                        //即没有添加美术学院，也没有添加设计学院
                        if(t == 0){
                            this.first.name = 'SJ';
                        }
                    }
                    AjaxAction.bindAjax();
                    //重新绑定
                    /*$(".ajaxSubmit, .submit:not('.notajax')").each(function(){
                        bindAjaxSubmitAction($(this));
                    });*/
                },
            });
        });
    </script>
@endsection
@section("right_content")

        <!--<div class="main_left pull-left">
            <div class="row dp-member-title-2 " style="margin-left: -3px; ">
                <h4 class="col-md-4 " style="padding-left: 0px; font-weight: 700; "> 报名信息： </h4>
            </div>
            <div class="div_input_one ">
                <label for="input01 " class="col-md-2 control-label "> 学员手机： </label>
                <input type="text " id="mobile " name="enroll[mobile]" value=" " maxlength="11 " class="form-control pull-left " />
                <a data="{mobile:$( '#mobile').val(),client_submit: 'PC'} " href="javascript:; " callback="hasRegistration " class="common-button dblock fleft combg2 ml5 ajaxLink " linkurl="/Member/Adviser/hasRegistration.html " processing="false " isclicked="0 ">下一步</a>
            </div>
        </div>-->
     <div class="sub_main">
     <form class="dp-member-content main_container">
      <div class="sub_main_one ">
       <div class="main_left pull-left">
        <div class="row dp-member-title-2 " style="margin-left: -3px; ">
         <h4 class="col-md-4 " style="padding-left: 0px; font-weight: 700; "> 报名信息： </h4>
        </div>
        <div class="div_input_one ">
         <label for="input01 " class="col-md-3 control-label "> 学员手机： </label>
         <input type="text " id="mobile " name="enroll[mobile]" maxlength="11" class="form-control pull-left " v-model="enroll.mobile" readonly/>
         <!--<a data="{mobile:$( '#mobile').val(),client_submit: 'PC'} " href="javascript:; " callback="hasRegistration " class="common-button dblock fleft combg2 ml5 ajaxLink " linkurl="/Member/Adviser/hasRegistration.html " processing="false " isclicked="0 ">下一步</a>-->
        </div>
        <div id="show-input " style="display: block; ">
         <div class="div_input_one ">
          <label for="input01 " class="col-md-3 control-label "> 学员QQ： </label>
          <input type="text " name="enroll[qq]" class="form-control" v-model="enroll.qq" :readonly="first.data.readonly == true"/>
         </div>
         <div class="div_input_one ">
          <label for="input01 " class="col-md-3 control-label "> 学员微信： </label>
          <input type="text " name="enroll[wx]" class="form-control" v-model="enroll.wx" :readonly="first.data.readonly == true"/>
         </div>
         <div class="div_input_one ">
          <label for="input01 " class="col-md-3 control-label "> 学员姓名： </label>
          <input type="text " name="enroll[name]" class="form-control" v-model="enroll.name" :readonly="first.data.readonly == true"/>
         </div>
        <div class="div_input_one " v-if="enroll.pay_adviser_name">
            <label for="input01 " class="col-md-3 control-label ">支付课程顾问： </label>
            <input type="text " class="form-control" v-model="enroll.pay_adviser_name" :readonly="true"/>
        </div>
        <div class="div_input_one " v-if="enroll.adviser_name">
            <label for="input01 " class="col-md-3 control-label ">报名课程顾问： </label>
            <input type="text " class="form-control" v-model="enroll.adviser_name" :readonly="true"/>
        </div>
        <div id="show-input " style="display: block; ">
         <div class="div_input_one ">
          <label for="input01 " class="col-md-3 control-label "> 学院名称： </label>
          <select class="form-control" v-model="first.name" :disabled="first.data.readonly == true">
           <option value="MS">美术学院</option>
           <option value="SJ">设计学院</option>
          </select>
         </div>
         <div class="div_input_one fj_tc">
          <label for="input01 " class="col-md-3 control-label "> 报名套餐： </label>
          <p class="tc_list pull-left" onclick="selectPackage('first');" v-cloak>选择主套餐课程</p>
         <div class="fj_list" v-show="first.data.selectedPackage.length >0 " v-cloak>
             <h3>已选主套餐：</h3>
             <p class="tc_list" v-for="item in first.data.selectedPackage">@{{ first.data.packageList[item].title }}</p>
         </div>
         </div>
         <div class="div_input_one fj_tc">
          <label for="input01 " class="col-md-3 control-label " > 附加课程： </label>
          <p class="tc_list pull-left" onclick="selectPackageAttach('first');" v-cloak>选择附加课程</p>
          <div class="fj_list" v-show="first.data.selectedPackageAttach.length >0 " v-cloak>
          	<h3>已选附加课程：</h3>
          	<p v-for="item in first.data.selectedPackageAttach">@{{ first.data.packageAttach[item].title }}（@{{ first.data.packageAttach[item].price }}元）</p>
          </div>
         </div>
         <div class="div_input_one fj_tc">
          <label for="input01" class="col-md-3 control-label "> 赠送课程： </label>
         <p class="tc_list pull-left" onclick="selectPackageCourse('first');" v-cloak>选择赠送课程</p>
         <div class="fj_list" v-show="first.data.selectedPackageCourse.length >0 " v-cloak>
         	<h3 class="pull-left">已选赠送课程：</h3>
             <span v-for="(item,index) in first.data.selectedPackageCourse">@{{ first.data.packageCourse[item].title }}<a v-if="(index+1) < first.data.selectedPackageCourse.length">、</a></span>
         </div>
         </div>
         <div class="div_input_one">
          <label for="input01" class="col-md-3 control-label "> 优惠活动： </label>
          <select class="form-control fleft pull-left" v-model="first.data.selectedPackageRebate" :disabled="first.data.readonly == true">
           <option value="" selected>无优惠</option>
           <option v-for="(item,index) in first.data.packageRebate" :value="index">@{{ item.title }}</option>
          </select>
          <input type="text" v-model="first.data.rebatePrice" class="form-control rebate_price  pull-left " :readonly="first.data.selectedPackageRebate === '' || first.data.readonly == true " max="500" min="1" value="0" />
         </div>
         <div class="div_input_one ">
          <label for="input01" class="col-md-4 control-label text-left"> 套餐总金额： </label>
          <p class="pull-left num_price" v-cloak>@{{firstTotalPrice}}</p>

         </div>
         <div class="pay_mess">
          <div class="pay_mess_main fj_tc">
           <label for="input01 " class="col-md-3 control-label "> 添加支付： </label>
           <p class="tc_list pull-left" onclick="addPayInfo('first');">添加支付记录</p>
          </div>
          <div class="pay_input " v-show="first.data.payList.length > 0" v-cloak>
           <div class="pay_input zf_title col-md-4">
             支付记录：
           </div>
           <div class="jl_list pull-left">
            <div class="jl_list_sun" v-for="(item,index) in first.data.payList">
             <p class="pull-left">方式:@{{ payType[item.pay_type]}} | 金额:@{{ item.amount }}元 | 时间:@{{ item.pay_time }}</p>
             <span class="glyphicon glyphicon-minus" @click="removePayList('first',index)" v-if="item.readonly != true"></span>
            </div>
           </div>
          </div>
         </div>
        </div>
        </div>
       </div>

       <div class="main_right pull-left" v-cloak>
           <div id="show-input " class="add_menu" style="display: block; " v-if="second.name  != ''">
               <div class="div_input_one ">
                   <label for="input01 " class="col-md-3 control-label "> 学院名称： </label>
                   <select class="form-control" v-model="second.name" :disabled="second.data.readonly == true">
                       <option value="MS">美术学院</option>
                       <option value="SJ">设计学院</option>
                   </select>
               </div>
               <div class="div_input_one fj_tc">
                   <label for="input01 " class="col-md-3 control-label "> 报名套餐： </label>
                   <p class="tc_list pull-left" onclick="selectPackage('second');" v-cloak>选择主套餐课程</p>
                   <div class="fj_list" v-show="second.data.selectedPackage.length >0 " v-cloak>
                       <h3>已选主套餐：</h3>
                       <p class="tc_list" v-for="item in second.data.selectedPackage">@{{ second.data.packageList[item].title }}</p>
                   </div>
               </div>
               <div class="div_input_one fj_tc">
                   <label for="input01 " class="col-md-3 control-label " > 附加套餐： </label>
                   <p class="tc_list pull-left" onclick="selectPackageAttach('second');" v-cloak>选择附加套餐课程</p>
                   <div class="fj_list" v-show="second.data.selectedPackageAttach.length >0 " v-cloak>
                       <h3>已选附加课程：</h3>
                       <p v-for="item in second.data.selectedPackageAttach">@{{ second.data.packageAttach[item].title }}（@{{ second.data.packageAttach[item].price }}元）</p>
                   </div>
               </div>
               <div class="div_input_one fj_tc">
                   <label for="input01" class="col-md-3 control-label "> 赠送课程： </label>
                   <p class="tc_list pull-left" onclick="selectPackageCourse('second');" v-cloak>选择赠送课程</p>
                   <div class="fj_list" v-show="second.data.selectedPackageCourse.length >0 " v-cloak>
                       <h3 class="pull-left">已选赠送课程：</h3>
                       <span v-for="(item,index) in second.data.selectedPackageCourse">@{{ second.data.packageCourse[item].title }}<a v-if="((index+1) < second.data.selectedPackageCourse.length)">、</a></span>
                   </div>
               </div>
               <div class="div_input_one">
                   <label for="input01" class="col-md-3 control-label "> 优惠活动： </label>
                   <select class="form-control fleft pull-left" v-model="second.data.selectedPackageRebate" :disabled="second.data.readonly == true">
                       <option value="" selected>无优惠</option>
                       <option v-for="(item,index) in second.data.packageRebate" :value="index">@{{ item.title }}</option>
                   </select>
                   <input type="text" v-model="second.data.rebatePrice" class="form-control rebate_price  pull-left " :readonly="second.data.selectedPackageRebate === '' || second.data.readonly == true" max="500" min="1" value="0" />
               </div>
               <div class="div_input_one ">
                   <label for="input01" class="col-md-4 control-label "> 套餐总金额： </label>
                   <p class="pull-left num_price" v-cloak>@{{secondTotalPrice}}</p>

               </div>
               <div class="pay_mess">
                   <div class="pay_mess_main fj_tc">
                       <label for="input01 " class="col-md-3 control-label "> 添加支付： </label>
                       <p class="tc_list pull-left" onclick="addPayInfo('second');">添加支付记录</p>
                   </div>
                   <div class="pay_input " v-show="second.data.payList.length > 0" v-cloak>
                       <div class="pay_input zf_title col-md-3">
                           支付记录：
                       </div>
                       <div class="jl_list pull-left">
                           <div class="jl_list_sun" v-for="(item,index) in second.data.payList">
                               <p class="pull-left">方式:@{{ payType[item.pay_type]}} | 金额:@{{ item.amount }}元 | 时间:@{{ item.pay_time }}</p>
                               <span class="glyphicon glyphicon-minus" @click="removePayList('second',index)" v-if="item.readonly != true"></span>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
        <div class="add_tc" v-show="second.name == ''" @click="addSecond">
         <span>+</span>
         <p>继续添加报名套餐信息</p>
        </div>
        <div class="add_tc" v-show="second.name != '' && second.data.readonly == false" @click="removeSecond">
         <span>-</span>
         <p>继续删除报名套餐信息</p>
        </div>
        <div class="div_input_one ">
         <label for="input01 " class="col-md-3 control-label "> 是否导学： </label>
         <div class="in_radio pull-left">
          <input type="radio" name="is_guide" value="1" v-model="enroll.is_guide" :disabled="first.data.readonly == true" />是
          <input type="radio" name="is_guide" value="0" v-model="enroll.is_guide" :disabled="first.data.readonly == true" /> 否
         </div>
        </div>
        <div class="sub_main_two ">
         <div class="div_input_one text-left">
         <input type="hidden" name="back_url" value="{{ Request::get('back_url') }}" />
         <input type="hidden" name="enroll[id]" :value="enroll.id" v-if="enroll.id" />
         <input type="hidden" name="enroll[is_guide]" :value="enroll.is_guide" />
         <input type="hidden" :name="'registration['+first.name+'][client_submit]'" value="PC" />

         <input type="hidden" :name="'registration['+first.name+'][id]'" v-if="first.data.registration_id" :value="first.data.registration_id" />
         <input type="hidden" :name="'registration['+first.name+'][school_id]'" v-model="first.name" />
         <input type="hidden" :name="'registration['+first.name+'][package_id]'" v-model="first.data.selectedPackage" />
         <input type="hidden" :name="'registration['+first.name+'][package_all_title]'" v-for="index in first.data.selectedPackage" :value="first.data.packageList[index].title" />
         <input type="hidden" :name="'registration['+first.name+'][rebate]'" v-model="first.data.rebatePrice" />
         <input type="hidden" :name="'registration['+first.name+'][package_total_price]'" v-model="firstTotalPrice" />
         <input type="hidden" :name="'registration['+first.name+'][course_attach_all_price]'" v-model="firstPackageAttachPrice" />
         <input type="hidden" :name="'registration['+first.name+'][package_attach_content][package_attach_id]'" v-model="first.data.selectedPackageAttach" />
         <input type="hidden" :name="'registration['+first.name+'][package_attach_content][package_course_id]'" v-model="first.data.selectedPackageCourse" />
         <input type="hidden" :name="'registration['+first.name+'][package_attach_content][package_rebate_id]'" v-model="first.data.selectedPackageRebate" />
         <input type="hidden" :name="'registration['+first.name+'][package_attach_content][package_info]'" v-if="first.data.selectedPackage" :value="objtostr(first.data.packageList[first.data.selectedPackage])" />
         <input type="hidden" :name="'registration['+first.name+'][package_attach_content][package_attach][]'" v-for="attach in first.data.selectedPackageAttach" :value="objtostr(first.data.packageAttach[attach])" />
         <input type="hidden" :name="'registration['+first.name+'][package_attach_content][package_course][]'" v-for="course in first.data.selectedPackageCourse" :value="objtostr(first.data.packageCourse[course])" />
         <input type="hidden" :name="'registration['+first.name+'][package_attach_content][package_rebate]'" v-if="first.data.selectedPackageRebate !== '' " :value="objtostr(first.data.packageRebate[first.data.selectedPackageRebate])" />
         <input type="hidden" :name="'registration['+first.name+'][pay_list][]'" v-for="pay in first.data.payList" :value="objtostr(pay)" />

         <div v-if="second.name != ''">
         <input type="hidden" :name="'registration['+second.name+'][client_submit]'" value="PC" />
         <input type="hidden" :name="'registration['+second.name+'][id]'" v-if="second.data.registration_id" :value="second.data.registration_id"  />
         <input type="hidden" :name="'registration['+second.name+'][school_id]'" v-model="second.name" />
         <input type="hidden" :name="'registration['+second.name+'][package_id]'" v-model="second.data.selectedPackage" />
         <input type="hidden" :name="'registration['+second.name+'][package_all_title]'" v-for="index in second.data.selectedPackage" :value="second.data.packageList[index].title" />
         <input type="hidden" :name="'registration['+second.name+'][rebate]'" v-model="second.data.rebatePrice" />
         <input type="hidden" :name="'registration['+second.name+'][package_total_price]'" v-model="secondTotalPrice" />
         <input type="hidden" :name="'registration['+second.name+'][course_attach_all_price]'" v-model="secondPackageAttachPrice" />
         <input type="hidden" :name="'registration['+second.name+'][package_attach_content][package_attach_id]'" v-model="second.data.selectedPackageAttach" />
         <input type="hidden" :name="'registration['+second.name+'][package_attach_content][package_course_id]'" v-model="second.data.selectedPackageCourse" />
         <input type="hidden" :name="'registration['+second.name+'][package_attach_content][package_rebate_id]'" v-model="second.data.selectedPackageRebate" />
         <input type="hidden" :name="'registration['+second.name+'][package_attach_content][package_info]'" v-if="second.data.selectedPackage" :value="objtostr(second.data.packageList[second.data.selectedPackage])" />
         <input type="hidden" :name="'registration['+second.name+'][package_attach_content][package_attach][]'" v-for="attach in second.data.selectedPackageAttach" :value="objtostr(second.data.packageAttach[attach])" />
         <input type="hidden" :name="'registration['+second.name+'][package_attach_content][package_course][]'" v-for="course in second.data.selectedPackageCourse" :value="objtostr(second.data.packageCourse[course])" />
         <input type="hidden" :name="'registration['+second.name+'][package_attach_content][package_rebate]'" v-if="second.data.selectedPackageRebate !== '' " :value="objtostr(second.data.packageRebate[second.data.selectedPackageRebate])" />
         <input type="hidden" :name="'registration['+second.name+'][pay_list][]'" v-for="pay in second.data.payList" :value="objtostr(pay)" />
         </div>
      <button type="button" class="btn btn-primary ajaxSubmit" url="{{ route("admin.registration.add-registration") }}" beforeAction="checkPayList" showloading="true">确认提交</button>
         </div>
        </div>
       </div>
      </div>
     </form>
     {{-- 弹窗开始 --}}
     <div class="body_zz"></div>
     <!--支付信息弹窗-->
         <div class="tc_pay" style="display: none;">
             <form>
                 <div class="tc_pay_l pull-left">
                     <!--         <div class="row dp-member-title-2 ">
                               <h4 class="col-md-4 "> 支付信息： </h4>
                              </div> -->
                     <div class="div_input_one ">
                         <div class="pay_input pull-left zf_title col-md-2">
                             支付方式：
                         </div>
                         <div class="pay_input">
                             <select id="pay-type " name="pay_type" class="form-control user-pay-input">
                                 <option :value="index" v-for="(item,index) in payType">@{{ item }}</option>
                             </select>
                         </div>
                         <div class="pay_input">
                             <div class="pay_input pull-left zf_title col-md-2">
                                 支付金额：
                             </div>
                             <input type="text" id="pay-amount" name="amount" placeholder="金额" value="" maxlength="8" class="form-control" />
                         </div>
                         <div class="pay_input ">
                             <div class="pay_input pull-left zf_title col-md-2">
                                 支付日期：
                             </div>
                             <div class="input-group form_datetime " >
                                 <input class="form-control form_width_3 datetime" name="pay_time" size="16" type="text" value="" />
                                 <!--<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>-->
                                 <!--<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>-->
                             </div>
                             <input type="hidden" id="dtp_input1" value="" />
                             <br />
                         </div>
                         <!--<div class="pay_input">
                          <div class="pay_input zf_title col-md-2">
                            支付记录：
                          </div>
                          <p class="pull-left">方式:花呗 | 金额:100元 | 时间:2018-05-15 00:00:43</p>
                         </div> -->
                     </div>
                 </div>
                 <div class="pay_add pull-right" onclick="addPayInfoCallback(this)">
                     <div title="添加该支付方式 " class="add-pay-type text-center">
                         <span>+</span>
                         <p>点击添加</p>
                     </div>
                 </div>
             </form>
         </div>


         <!--选择主套餐弹窗-->
         <div class="select_tc package">
             <!--   <div class="se_top">
                 <h3 class="pull-left">选择附加套餐</h3>
                 <span class="glyphicon glyphicon-remove-circle pull-right"></span>
                </div> -->
             <form class="se_form">
                 <input type="hidden" name="schoolName" value="" />
                 <input type="search" placeholder="课程名称" class="form-control pull-left" v-model='searchKey' @change="searchList" />
                 <button type="button" class="btn btn-primary">搜索</button>
                 <div class="se_table">
                     <table>
                         <tbody>
                         <tr>
                             <td>勾选</td>
                             <td>课程名称</td>
                             <td>课程价格</td>
                         </tr>
                         <tr v-for="(item,index) in list">
                             <td>
                                 <div class="check_box">
                                     <input type="radio" :value="index" name="course" :id="'check'+index" :checked="defaultChecked(index,'selectedPackage')" />
                                     <label :for="'check'+index"></label>
                                 </div>
                             </td>
                             <td>@{{ item.title }}</td>
                             <td>@{{ item.price }}</td>
                         </tr>
                         </tbody>
                     </table>
                 </div>
                 <button type="button" class="btn btn-success pull-right btn-qz" onclick="selectPackageCallback(this)">确认</button>
             </form>
         </div>

         <!--选择附加套餐弹窗-->
         <div class="select_tc packageAttach">
             <!--   <div class="se_top">
                 <h3 class="pull-left">选择附加套餐</h3>
                 <span class="glyphicon glyphicon-remove-circle pull-right"></span>
                </div> -->
             <form class="se_form">
                 <input type="hidden" name="schoolName" value="" />
                 <!--<input type="search" placeholder="课程名称" class=" form-control pull-left" />
                 <button type="button" class="btn btn-primary">搜索</button>-->
                 <div class="se_table">
                     <table>
                         <tbody>
                         <tr>
                             <td>勾选</td>
                             <td>课程名称</td>
                             <td>课程价格</td>
                         </tr>
                         <tr v-for="(item,index) in list">
                             <td>
                                 <div class="check_box">
                                     <input type="checkbox" :value="index" name="course[]" :id="'check'+index" :checked="defaultChecked(index,'selectedPackageAttach')" />
                                     <label :for="'check'+index"></label>
                                 </div>
                             </td>
                             <td>@{{ item.title }}</td>
                             <td>@{{ item.price }}</td>
                         </tr>
                         </tbody>
                     </table>
                 </div>
                 <button type="button" class="btn btn-success pull-right btn-qz" onclick="selectPackageAttachCallback(this)">确认</button>
             </form>
         </div>
         <!--选择赠送课程弹窗-->
         <div class="select_tc packageCourse">
             <!--   <div class="se_top">
                 <h3 class="pull-left">选择附加套餐</h3>
                 <span class="glyphicon glyphicon-remove-circle pull-right"></span>
                </div> -->
             <form class="se_form">
                 <input type="hidden" name="schoolName" value="" />
                 <!--<input type="search" placeholder="课程名称" class=" form-control pull-left" />
                 <button type="button" class="btn btn-primary">搜索</button>-->
                 <div class="se_table">
                     <table>
                         <tbody>
                         <tr>
                             <td>勾选</td>
                             <td>课程名称</td>
                             <!--<td>课程价格</td>-->
                         </tr>
                         <tr v-for="(item,index) in list">
                             <td>
                                 <div class="check_box">
                                     <input type="checkbox" :value="index" name="course[]" :id="'check'+index" :checked="defaultChecked(index,'selectedPackageCourse')" />
                                     <label :for="'check'+index"></label>
                                 </div>
                             </td>
                             <td>@{{ item.title }}</td>
                             <!--<td>@{{ item.price }}</td>-->
                         </tr>
                         </tbody>
                     </table>
                 </div>
                 <button type="button" class="btn btn-success pull-right btn-qz" onclick="selectPackageCourseCallback(this)">确认</button>
             </form>
         </div>
     </div>
     {{-- 弹窗结束 --}}
@endsection