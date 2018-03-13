@extends("admin.public.layout")
@section("right_content")
    <script>
        function loadInit() {
            vm = new Vue({
                el: '#container',
                data: {
                    payData : {
                        type:'',
                        txt:'',
                        amount:'',
                        time:'',
                        packageTitle:'', //套餐标题
                        packageTotalPrice:'', //套餐总金额
                        packageId:'', //套餐ID
                    },
                    //课程套餐列表
                    packageList:[],
                    //优惠活动列表
                    rebateList:{!! $rebateList !!},
                    //附加套餐列表
                    packageAttachList:{!!$packageAttachList!!},
                    giveList:{!!$giveList!!},   //赠送课程
                    //分期方式列表
                    fqTypeList:{!!$fqTypeList!!},
                    //支付方式列表
                    payDataList: [],
                    //用户支付的相关信息
                    userPayInfo:{
                        package_id:'', //套餐ID
                        package_title:'', //主套餐标题
                        package_tmp_title:'',
                        package_price:0, //主套餐价格
                        package_attach_select:'',//附加套餐对象
                        package_attach_id:0, //附加套餐ID
                        package_attach_title:'', //附加套餐
                        package_attach_price:0,//附加套餐价格
                        give_title:'',    //赠送课程标题
                        give_id:'',   //赠送课程主键
                        package_total_price:0, //套餐总金额
                        amount_submitted:0,
                        rebate_title:'',  //活动标题
                        rebate_tmp_title:'',//临时活动标题
                        rebate_price:'',  //活动优惠价格,
                        rebate_id:0,      //优惠活动ID
                        fq_type:'',       //分期方式
                        server_date:0,  //赠送服务期
                        is_open:0,      //是否已经开课导学
                    },
                    //用户是否已经存在
                    hasUser:false
                },
                ready:function () {
                    var _this = this;
                    _this.$nextTick(function () {})
                },
                methods: {
                    checkCourseIdInStr:function() {
                        return false;
                    },
                    setPackAttach:function () {
                        var attachId;
                        var _this = this;
                        //用each获取每个元素
                        $.each(this.packageAttachList, function(i, val){
                            if(_this.userPayInfo.package_attach_id == val.id){
                                _this.userPayInfo.package_attach_title = val.title;
                                _this.userPayInfo.package_attach_price = val.price;
                            }
                        });
                        if(_this.userPayInfo.package_attach_id == 0){
                            _this.userPayInfo.package_total_price = _this.userPayInfo.package_price;
                        }else{
                            _this.userPayInfo.package_total_price = parseFloat(_this.userPayInfo.package_price)+parseFloat(_this.userPayInfo.package_attach_price);
                        }

                        setPackageTotal(); //计算最终套餐价格
                    },
                    setRebate:function () {
                        var _this = this;
                        if(_this.userPayInfo.rebate_id == 0){
                            _this.userPayInfo.rebate_title = '';
                            _this.userPayInfo.rebate_price = 0;
                        }else{
                            //用each获取每个元素
                            $.each(this.rebateList, function(i, val){
                                if(_this.userPayInfo.rebate_id == val.id){
                                    _this.userPayInfo.rebate_title = val.title;
                                    _this.userPayInfo.rebate_price = val.price;
                                }
                            });
                        }

                    },
                    addPayType:function(){
                        this.payData.txt = $("#pay-type").find("option:selected").text();
                        this.payData.time = $(".datetime").val();
                        if(!this.payData.type){
                            layer.msg("请先选择支付方式！",{icon:2,time:2000});
                            return false;
                        }
                        if(!this.payData.amount){
                            layer.msg("请填写支付金额！",{icon:2,time:2000});
                            return false;
                        }

                        if(isNaN(this.payData.amount) || parseFloat(this.payData.amount)>9999){
                            layer.msg("请填写正确的支付金额！",{icon:2,time:2000});
                            return false;
                        }
                        if(!this.payData.time){
                            layer.msg("支付日期不能为空！",{icon:2,time:2000});
                            return false;
                        }
                        var dom = {type:this.payData.type,txt:this.payData.txt,amount:this.payData.amount,time:this.payData.time};
                        this.payDataList = this.payDataList.concat(dom);
                        this.userPayInfo.amount_submitted = parseFloat(this.userPayInfo.amount_submitted)+parseFloat(this.payData.amount);
                        //初始化 支付方式选择
                        this.payData.type = '';
                        this.payData.txt = '';
                        this.payData.amount = '';
                        this.payData.time = '';

                    },
                    minusPayType:function (index) {
                        this.userPayInfo.amount_submitted = accSub(parseFloat(this.userPayInfo.amount_submitted),parseFloat(this.payDataList[index].amount));
                        //this.userPayInfo.amount_submitted = parseFloat(this.userPayInfo.amount_submitted)-parseFloat(this.payDataList[index].amount);
                        this.payDataList.splice(index, 1);

                    },
                    //获取当前附加套餐
                    getAttach:function (id) {
                        var val = this.packageAttachList.indexOf(id);
                        console.log(val);
                    },
                    //控制赠送课程 只要选择了否，则前面的
                    giveSelect:function () {
                        var _this = this;
                        var glen = $(".give_select_id").length-1;
                        if($($(".give_select_id")[glen]).prop('checked')){
                            $(".give_select_id").each(function (e) {
                                if(e == glen){
                                    return ;
                                }
                                _this.giveList[e].checked = false;
                                //$(this).attr("checked",false);
                                $(this).attr("disabled",'true');
                            })
                        }else{
                            $(".give_select_id").attr("disabled",false);
                        }
                        var k = 0;
                        var giveArr = [];
                        $("input[name='give_id[]']:checked").each(function (i,el) {
                            giveArr[k] = $(el).val();
                            k++;
                        });
                        _this.userPayInfo.give_id = giveArr.toString();
                    },
                    searchPackage:function () { //搜索相关套餐
                        var _this = this;
                        if(isAjax == 1){
                            return ;
                        }
                        isAjax = 1;
                        $.ajax({
                            url         :   "{:U('getPackageList')}",
                            dataType    :   'json',
                            method      :   'post',
                            data        :   {'title':_this.userPayInfo.package_tmp_title},
                            success     :   function (data) {
                                if(data.data.length > 0){
                                    $(".course-package").show();
                                }else{
                                    $(".course-package").hide();
                                }
                                //vm.userPayInfo.package_total_price = "";
                                _this.packageList = data.data;
                                isAjax = 0;
                            }
                        })
                    },
                    searchRebate:function () {
                        var _this = this;
                        if(isAjax == 1){
                            return ;
                        }
                        isAjax = 1;
                        $.ajax({
                            url         :   "{:U('getRebateList')}",
                            dataType    :   'json',
                            method      :   'post',
                            data        :   {'title':_this.userPayInfo.rebate_tmp_title},
                            success     :   function (data) {
                                //$(".rebate-activity").show();
                                if(data.data.length > 0){
                                    $(".rebate-activity").show();
                                }else{
                                    $(".rebate-activity").hide();
                                }
                                //vm.userPayInfo.package_total_price = "";
                                _this.rebateList = data.data;
                                isAjax = 0;
                            }
                        })
                    },
                    removeRebate:function () {
                        vm.userPayInfo.rebate_id = 0;
                        vm.userPayInfo.rebate_price = 0;
                        vm.userPayInfo.rebate_title = '';
                        $(".rebate-activity").hide();
                    }
                }
            });
        }
        //判断用户是否已经报名
        function hasRegistration(jsonData,obj) {
            if(jsonData.code == 0){
                layer.msg(jsonData.msg,{icon:0,time:2000});
                return ;
            }
            if(jsonData.code == 1 && jsonData.data){
                //获取用户支付的相关信息
                vm.userPayInfo = jsonData.data;
                vm.userPayInfo.package_tmp_title = jsonData.data.package_title;
                vm.userPayInfo.rebate_tmp_title = jsonData.data.rebate_title;
                if(jsonData.data.package_attach_current_data){
                    vm.packageAttachList.push(jsonData.data.package_attach_current_data);
                }
                vm.hasUser = 1;
                //修改异步提交地址
                //$(".ajaxSubmit").attr("href","{:U('updateRegistration')}");
                $(".ajaxSubmit").attr("url","{{url('registration/update-registration')}}");
                //判断是否属于当前课程顾问，非当前课程顾问的学员不能提交支付信息
                if(jsonData.data.isBelong == 0){
                    $(".ajaxSubmit").attr("disabled",true);
                    $(".ajaxSubmit").text("只有该学员的课程顾问才能提交");
                }
                layer.msg(jsonData.msg,{icon:1,time:2000});
            }else{
                layer.msg(jsonData.msg,{icon:0,time:2000});
            }
            //设置已选赠送课程
            $.each(vm.giveList,function (index,el) {
                if(checkCourseIdInStr(vm.userPayInfo.give_id,$(el)[0].id)){
                    vm.giveList[index].checked = true;
                    $(el)[0].checked = true;
                }
            });
            $(".main_left").css({'border-right':'1px solid #666'});
            $(".sub_main_two").show();
            $("#show-input").show();
            $(".main_right").show();
            $("#mobile").attr("readonly",true);
            return ;
        }
        /**
         *  判断赠送课程是否包含
         * @param str
         * @param id
         * @returns {boolean}
         */
        function checkCourseIdInStr(str,id) {
            id = parseInt(id);
            var arr = str.split(",");
            $.each(arr,function (index) {
                arr[index] = parseInt(arr[index]);
            });
            if($.inArray(id, arr) > -1){
                return true;
            }else{
                return false;
            }
        }
    </script>
    <style>
        .container{width: 1270px;}
        .form-control{width: 200px;}
        .course-package,.rebate-activity{width: 370px;border: 1px solid #ccc;margin-left: 120px;display: none;max-height: 100px;overflow-y:scroll;}
        .course-package span,.rebate-activity span{width: 370px;height:28px; display: inline-block; line-height: 28px;cursor: pointer; padding-left: 4px;}
        .course-package span:hover,.rebate-activity span:hover{background-color: #ccc}
        /*添加支付按钮*/
        .add-pay-type{width: 40px; line-height: 30px;font-size: 20px; cursor: pointer;}
        .help-block{color: red; float: left;width:50px;}
        [v-cloak] {
            display: none;
        }
        /*新结构*/
        .sub_main{width: 100%; min-height:960px;}
        .sub_main .main_left{width: 47%; height: 100%; float: left}
        .sub_main .main_right{width: 53%; height: 100%; float: left;padding-left: 25px;}
        .control-label{width: 120px;float: left;text-align: center;line-height: 34px;}
        .div_input_one{height: 40px;width: 100%;margin-top: 10px; line-height: 34px;}
        .sub_main_one{min-height: 670px;}
        .sub_main_two{height: 100px;border-top: 1px solid #ccc;height: 30%;display: none}
        .pay_input{width: 100px; float: left; margin-left: 4px;}
        #show-input,.main_right{display: none}
    </style>
    <!--<div class="row dp-member-title-2">-->
    <!--<h4 class="col-md-4" style="padding-left:0">学员支付/开课录入</h4>-->
    <!--</div>-->
    <form id="container">
        <div class="sub_main">
            <div class="sub_main_one">
                <div class="main_left">
                    <div class="row dp-member-title-2" style="margin-left: -3px;">
                        <h4 class="col-md-4" style="padding-left:0;font-weight:700;">
                            报名信息：
                        </h4>
                    </div>
                    <div class="div_input_one">
                        <label class="col-md-2 control-label" for="input01">
                            学员手机：
                        </label>
                        <input type="text" id="mobile" name="mobile" value="" class="form-control fleft" maxlength="11" :readonly="hasUser"/>
                        <a class="common-button dblock fleft combg2 ml5 ajaxLink" data="{mobile:$('#mobile').val(),client_submit:'PC'}" url="{{url('admin/registration/has-registration')}}" callback="hasRegistration">下一步</a>
                    </div>
                    <div id="show-input">
                        <div class="div_input_one">
                            <label class="col-md-2 control-label" for="input01">
                                学员QQ：
                            </label>
                            <input type="text" name="qq" class="form-control" v-model="userPayInfo.qq" :readonly="hasUser" />
                        </div>

                        <div class="div_input_one">
                            <label class="col-md-2 control-label" for="input01">
                                学员姓名：
                            </label>
                            <input type="text" name="name" class="form-control" v-model="userPayInfo.name" :readonly="hasUser"/>
                        </div>

                        <div class="div_input_one">
                            <label class="col-md-2 control-label" for="input01">
                                开课套餐：
                            </label>
                            <input id="package-title" type="text" name="package_title" class="form-control fleft" style="width: 300px;" v-model="userPayInfo.package_tmp_title" :readonly="hasUser" @keyup="searchPackage" />
                            <!--<a class="common-button dblock fleft combg2 ml5 ajaxLink" data="{title:$('#package-title').val(),total_price:$('#package-price').val()}" href="{:U('addPackage')}" callback="addPackage" v-show="!packageList.length && payData.packageTitle">新加</a>-->
                        </div>
                        <div class="course-package" style="display: none;">
                            <span onclick="setPackName(this)" v-for="(l,index) in packageList" :price="l.price" :package-id="l.id"><a>@{{l.title}}</a> - (金额@{{l.price}}元)</span>
                            <input type="hidden" name="package_id" v-model="userPayInfo.package_id"/>
                        </div>
                        <div class="div_input_one">
                            <label class="col-md-2 control-label" for="input01">
                                已选主套餐：
                            </label>
                            @{{userPayInfo.package_title || '未选'}}
                        </div>

                        <div class="div_input_one">
                            <label class="col-md-2 control-label" for="input01">
                                附加套餐：
                            </label>
                            <select class="form-control fleft" v-model="userPayInfo.package_attach_id" name="package_attach_id" :disabled="hasUser" @change="setPackAttach">
                            <option value="0">选择附加套餐</option>
                            <option v-for="(l,index) in packageAttachList" :value="l.id" :selected="(l.id == userPayInfo.package_attach_id)"><a>@{{l.title}}<template v-if="l.status == 'DEL'">(已删)</template></a></option>
                            </select>
                            <input type="hidden" name="package_attach_title" v-model="userPayInfo.package_attach_title" />
                            <!--<input type="hidden" name="package_attach_id" v-model="userPayInfo.package_attach_id" />-->
                            <input type="hidden" name="package_attach_price" v-model="userPayInfo.package_attach_price" />
                        </div>


                        <div class="div_input_one" style="height: 55px;">
                            <label class="col-md-2 control-label" for="input01">
                                赠送：
                            </label>
                            <template v-for="(l,index) in giveList">
                                <input type="checkbox" class="give_select_id" name="give_id[]" :value="l.id" :disabled="hasUser" :checked="l.checked" v-model="l.checked"  @click="giveSelect(index)"/>@{{l.text}}
                            </template>
                            <input id="give_id" name="give_id" type="hidden" v-model="userPayInfo.give_id"/>
                        </div>


                        <div class="div_input_one">
                            <label class="col-md-2 control-label" for="input01">
                                套餐总金额：
                            </label>
                            <input id="package-price" type="text" name="package_total_price" class="form-control" v-model="userPayInfo.package_total_price" readonly/>
                        </div>

                        <div class="div_input_one">
                            <label class="col-md-2 control-label" for="input01">
                                优惠活动：
                            </label>
                            <select class="form-control fleft" v-model="userPayInfo.rebate_id" name="rebate_id" :disabled="hasUser" @change="setRebate">
                            <option value="0">选择优惠活动</option>
                            <option v-for="(l,index) in rebateList" :value="l.id" :selected="(l.id == userPayInfo.rebate_id)">@{{l.title}}</option>
                            </select>
                            <input type="hidden" name="rebate_title" v-model="userPayInfo.rebate_title" />
                            <input type="hidden" name="rebate_price" v-model="userPayInfo.rebate_price" />
                        </div>


                        <div class="div_input_one">
                            <label class="col-md-2 control-label" for="input01">
                                优惠金额：
                            </label>
                            <input type="text" name="rebate" class="form-control" v-model="userPayInfo.rebate_price" readonly />
                        </div>


                        <div class="div_input_one">
                            <label class="col-md-2 control-label" for="input01">
                                服务期：
                            </label>
                            <input type="radio" name="server_date" :checked="userPayInfo.server_date == 0" value="0" v-model="userPayInfo.server_date" :disabled="hasUser" checked />无
                            <input type="radio" name="server_date" :checked="userPayInfo.server_date == 1" value="1" v-model="userPayInfo.server_date" :disabled="hasUser" /> 1个月
                            <input type="radio" name="server_date" :checked="userPayInfo.server_date == 2" value="2" :disabled="hasUser" v-model="userPayInfo.server_date" />2个月
                        </div>

                        <div class="div_input_one" v-show="hasUser">
                            <label class="col-md-2 control-label" for="input01">
                                是否开课：
                            </label>
                            <input type="radio" name="is_open" :checked="userPayInfo.is_open == 0" value="0" v-model="userPayInfo.is_open" :disabled="hasUser" checked />未开课
                            <input type="radio" name="is_open" :checked="userPayInfo.is_open == 1" value="1" v-model="userPayInfo.is_open" :disabled="hasUser" /> 部分开课
                            <input type="radio" name="is_open" :checked="userPayInfo.is_open == 2" value="2" v-model="userPayInfo.is_open" :disabled="hasUser" /> 全部开课
                        </div>


                    </div>
                </div>
                <!--右侧容器-->
                <div class="main_right">
                    <div class="row dp-member-title-2">
                        <h4 class="col-md-4" style="padding-left:0;margin-left: -38px;font-weight:700;">
                            支付信息：
                        </h4>
                    </div>
                    <div class="div_input_one">
                        <label class="col-md-2 control-label" for="input01" style="width: 100px;padding-left: 0px;">
                            是否分期：
                        </label>
                        <input type="radio" name="fq_type" value="" :disabled="hasUser" v-model="userPayInfo.fq_type" />无分期
                        <template v-for="(l,index) in fqTypeList">
                            <input type="radio" name="fq_type" :value="index" :disabled="hasUser" v-model="userPayInfo.fq_type"  />@{{l}}&nbsp;
                        </template>

                    </div>
                    <div class="div_input_one" style="height: 400px;">
                        <div class="pay_input" style="width:75px;font-weight:700;">
                            支付方式：
                        </div>
                        <div class="pay_input" style="width: 110px;">

                            <select id="pay-type" class="form-control user-pay-input" v-model="payData.type" style="width: 110px;">
                                <option value="" selected>方式</option>
                                <option value="ALIPAY">支付宝</option>
                                <option value="WEIXIN">微信</option>
                                <option value="HUABEI">花呗</option>
                                <option value="HUABEIFQ">花呗分期</option>
                                <option value="MAYIFQ">蚂蚁分期</option>
                                <option value="BANKZZ">银行转账</option>
                            </select>
                        </div>

                        <!--支付金额-->
                        <div class="pay_input" style="width:82px;">
                            <input type="text" id="pay-amount" class="form-control" value="" placeholder="金额" style="width: 80px;" v-model="payData.amount" maxlength="8" onkeyup="this.value=this.value.replace(/[^0-9|\.]/,'')"/>
                        </div>
                        <div class="pay_input" style="width:165px;margin-right: 10px;">
                            <input type="text" class="form-control datetime" value="" placeholder="日期" v-model="payData.time" style="width: 165px;">
                        </div>
                        <div class="fa fa-plus add-pay-type" @click="addPayType" title="添加该支付方式"></div>
                    <!--<span class="help-block">*必填项</span>-->
                    <div class="form-group"  style="height: 30px;color: red; padding-left: 85px;">
                        注意:请在填写完支付信息后点击‘+’生成有效信息
                    </div>
                    <!--开始循环支付方式列表-->
                    <div class="form-group" v-for="(item,index) in payDataList" style="height: 30px;">
                        <label class="col-md-2 control-label" for="input01"></label>
                        <div class="col-md-8 controls" style="width:440px;margin-right:2px; padding-left: 6px;">
                            <div class="form-control user-pay-input" style="width:440px; background-color: #CCCCCC;font-weight: bold;" v-cloak>
                                方式:@{{item.txt}}&nbsp;&nbsp;|&nbsp;&nbsp;金额:@{{item.amount}}元&nbsp;&nbsp;|&nbsp;&nbsp;时间:@{{item.time}}
                            </div>

                            <input type="hidden" name="pay_type_list[]" :value="item.type" />
                            <input type="hidden" name="amount_list[]" :value="item.amount" />
                            <input type="hidden" name="pay_time_list[]" :value="item.time" />
                        </div>
                        <div class="col-md-8 controls fa fa-minus add-pay-type" @click="minusPayType(index)" title="删除该支付方式"></div>
                    </div>
                </div>
        </div>
                </div>

            <div class="sub_main_two">

                <div class="div_input_one" style="height: 150px;">
                    <label class="col-md-2 control-label" for="input01" style="width: 130px;">
                        开课交接备注：
                    </label>
                    <textarea name="remark" class="form-control" style="width:500px; height:120px;" v-model="userPayInfo.remark"></textarea>
                </div>
                <div class="div_input_one" style="margin: 0 auto; text-align: center;">
                    <input type="hidden" name="package_id" v-model="userPayInfo.package_id" :disabled="!hasUser" />
                    <input type="hidden" name="rebate_id" v-model="userPayInfo.rebate_id" :disabled="!hasUser" />
                    <input type="hidden" name="registration_id" v-model="userPayInfo.id" :disabled="!hasUser" />
                    <input type="hidden" name="client_submit" value="PC" />
                    <button class="btn btn-primary ajaxSubmit" type="button" url="{{url('add-registration')}}">确认提交</button>
                </div>
            </div>
        </div>
    </form>
@endsection