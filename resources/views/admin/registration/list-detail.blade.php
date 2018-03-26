@extends("admin.public.layout")
@section("right_content")
    <style>
        .form-control{width: 200px; font-size: 6px;}
        .course-package,.rebate-activity{width: 370px;border: 1px solid #ccc;margin-left: 120px;display: none;max-height: 100px;overflow-y:scroll;}
        .course-package span,.rebate-activity span{width: 370px;height:28px; display: inline-block; line-height: 28px;cursor: pointer; padding-left: 4px;}
        .course-package span:hover,.rebate-activity span:hover{background-color: #ccc}
        /*添加支付按钮*/
        .add-pay-type{width: 40px; line-height: 30px;font-size: 20px; cursor: pointer;}
        .help-block{color: red; float: left;width: 50px;}
        .col-md-8{width: auto}
        .input-two{width: 40px; float: left;margin-left: 6px;cursor: pointer;font-size: 14px;color: red}
        [v-cloak] {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="/js/datetimepicker/jquery.datetimepicker.css">
<script>
        var isAjax = 0;
        function loadInit() {
            vm = new Vue({
                el:"#content-main",
                data:{
                    userPayInfo	:   {!! $r !!},
                    noMod		:	true,
                    modData		:	{
                        field:'',
                    },
                    payTypeList:{!! $payTypeList !!},
                    //套餐列表
                    packageList:[],
                    //附加套餐列表
                    packageAttachList:{!! $packageAttachList !!},
                    //优惠活动列表
                    rebateList:{!! $rebateList !!},
                    //赠送课程列表
                    giveList:{!! $giveList !!},
                    //分期方式列表
                    fqTypeList:{!! $fqTypeList !!},
                    //权限grade
                    adminInfo:{!! $adminInfo !!}
                },
                mounted:function () {
                    var _this = this;
                    _this.$nextTick(function(){
                        //数据员一下的权限没有修改功能
                        if(_this.grade > 5){
                            $(".help-block").remove();
                        }
                    });
                },
                methods:{
                    searchPackage:function () { //搜索相关套餐
                        var _this = this;
                        if(isAjax == 1){
                            return ;
                        }
                        isAjax = 1;
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url         :   "{{route('admin.registration.post-package-list')}}",
                            dataType    :   'json',
                            method      :   'post',
                            data        :   {'title':_this.userPayInfo.user_registration.course_package.title},
                            success     :   function (data) {
                                if(data.data.length > 0){
                                    $(".course-package").show();
                                }else{
                                    $(".course-package").hide();
                                }
                                vm.userPayInfo.package_total_price = 0;
                                _this.packageList = data.data;
                                isAjax = 0;
                            }
                        })
                    },
                    setPackAttach:function () {
                        var attachId;
                        var _this = this;
                        if(_this.userPayInfo.package_attach_id == 0){
                            _this.userPayInfo.package_total_price = _this.userPayInfo.package_price;
                        }
                        //用each获取每个元素
                        $.each(this.packageAttachList, function(i, val){
                            if(_this.userPayInfo.package_attach_id == val.id){
                                _this.userPayInfo.package_attach_title = val.title;
                                _this.userPayInfo.package_attach_price = val.price;
                                _this.userPayInfo.package_total_price = parseInt(_this.userPayInfo.package_price)+parseInt(_this.userPayInfo.package_attach_price);
                            }
                        });
                        setPackageTotal(); //计算最终套餐价格
                    },
                    setRebate:function () {
                        var _this = this;
                        //用each获取每个元素
                        $.each(this.rebateList, function(i, val){
                            if(_this.userPayInfo.rebate_id == val.id){
                                _this.userPayInfo.rebate_title = val.title;
                                _this.userPayInfo.rebate_price = val.price;
                            }
                        });
                    },
                    searchRebate:function () {
                        var _this = this;
                        if(isAjax == 1){
                            return ;
                        }
                        isAjax = 1;
                        $.ajax({
                            url         :   "{:U('Adviser/getRebateList')}",
                            dataType    :   'json',
                            method      :   'post',
                            data        :   {'title':_this.userPayInfo.rebate_title},
                            success     :   function (data) {
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
                                _this.userPayInfo.give_id = 0;
                                $(this).attr("disabled",'true');
                            });
                            return ;
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
                }
            });
        }

        $(".input-two").click(function () {
            var _this = this;
            obj = $(this).parent(".form-group").find(".form-control");
//            if(!confirm("确认修改？")){
//                return false;
//            }
            $.ajax({
                url:"{:U('modPayInfo')}",
                dataType:'json',
                method:"post",
                data:{
                    field		:	$(obj).attr("name"),
                    val			:	$(obj).val(),
                    pay_id		:	vm.userPayInfo.pay_id,
                    package_id	:	vm.userPayInfo.package_id,
                    registration_id:vm.userPayInfo.registration_id,
                    id			:	vm.userPayInfo.id,
                },
                success:function (jsonData) {
                    if(jsonData.code == 1){
                        layer.msg(jsonData.msg,{icon:1,time:2000});
                    }else{
                        layer.msg(jsonData.msg,{icon:2,time:2000});
                    }
                }
            })
        })
        /**
         *  选择课程套餐
         * @param obj
         */
        function setPackName(obj) {
//            $("#package-title").val($(obj).text());
            vm.userPayInfo.user_registration.course_package.title = $(obj).find('a').text();
            $(".course-package").hide();
            vm.userPayInfo.user_registration.course_package.price = $(obj).attr("price");
            //套餐ID
            vm.userPayInfo.user_registration.course_package.id = $(obj).attr("package-id");
            vm.userPayInfo.user_registration.package_total_price = parseInt(vm.userPayInfo.package_price)+parseInt(vm.userPayInfo.package_attach_price);
            setPackageTotal(); //计算最终套餐价格
        }
        /**
         * 计算套餐最终价格
         */
        function setPackageTotal() {
            vm.userPayInfo.total_price = parseInt(vm.userPayInfo.package_price)+parseInt(vm.userPayInfo.package_attach_price);
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
<div class="row dp-member-title-2">
    <h4 class="col-md-4" style="padding-left:0">学员支付详情</h4>
</div>
<div id="content-main" class="row dp-member-body-2">

    <form id="regForm" class="form-horizontal">
            <input type="hidden" name="id" :value="userPayInfo.id" />
        <fieldset>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">学员手机</label>
                <div class="col-md-8 controls">
                    <input type="text" name="mobile" value="" class="form-control fleft" maxlength="11" v-model="userPayInfo.mobile"  />
                </div>
                <p class="help-block input-two" >修改</p>
            </div>
            <!--提交用户支付信息 开通课程信息-->

            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">学员QQ：</label>
                <div class="col-md-8 controls">
                    <input type="text" name="qq" class="form-control" v-model="userPayInfo.qq"   />
                </div>
                <p class="help-block input-two" >修改</p>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">学员姓名：</label>
                <div class="col-md-8 controls">
                    <input type="text" name="name" class="form-control" v-model="userPayInfo.name"   />
                </div>
                <p class="help-block input-two" >修改</p>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">支付课程顾问：</label>
                <div class="col-md-8 controls">
                    <input type="text" name="adviser_name" class="form-control" v-model="userPayInfo.adviser_name" disabled />
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">报名课程顾问：</label>
                <div class="col-md-8 controls">
                    <input type="text" name="adviser_name" class="form-control" v-model="userPayInfo.adviser_name_reg" disabled />
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">学员报名套餐：</label>
                <div class="col-md-8 controls">
                    <input id="package-title" type="text" name="package_title" class="form-control fleft" v-model="userPayInfo.user_registration.course_package.title" @keyup="searchPackage" style="width: 400px;"   />
                </div>
                <p class="help-block ajaxLink" data="{field:'package_id',val:$('#package_id').val()}" href="{:U('modField',['pay_log_id'=>$_GET['pay_log_id']])}" >修改</p>
            </div>
            <div class="course-package" style="display: none;">
                <span onclick="setPackName(this)" v-for="(l,index) in packageList" :price="l.price" :package-id="l.id"><a>@{{l.title}}</a> - (金额@{{l.price}}元)</span>
                <input id="package_id" type="hidden" name="package_id" v-model="userPayInfo.package_id"  />
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">
                    附加套餐：
                </label>
                <div class="col-md-8 controls">
                    <select id="package_attach_id" class="col-md-8 form-control fleft" v-model="userPayInfo.user_registration.course_package_attach.id" name="package_attach_id" @change="setPackAttach"  >
                    <option value="0">选择附加套餐</option>
                    <option v-for="(l,index) in packageAttachList" :value="l.id" selected="selected"><a>@{{l.title}}</a></option>
                    </select>
                </div>
                <p class="help-block ajaxLink" data="{field:'package_attach_id',val:$('#package_attach_id').val()}" href="{:U('modField',['pay_log_id'=>$_GET['pay_log_id']])}" >修改</p>

                <input type="hidden" name="package_attach_title" v-model="userPayInfo.package_attach_title" />
                <!--<input type="hidden" name="package_attach_id" v-model="userPayInfo.package_attach_id" />-->
                <input type="hidden" name="package_attach_price" v-model="userPayInfo.package_attach_price" />
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">赠送课程：</label>
                <div class="col-md-8 controls">
                    <template v-for="(l,index) in giveList">
                        <input type="checkbox" class="give_select_id" name="give_id[]" :value="l.id" v-model="l.checked"  @click="giveSelect(index)"  />@{{l.text}}&nbsp;
                    </template>
                    <input name="give_id" id="give_id" type="hidden" v-model="userPayInfo.give_id"/>
                </div>
                <p class="help-block ajaxLink" data="{field:'give_id',val:$('#give_id').val()}" href="{:U('modField',['pay_log_id'=>$_GET['pay_log_id']])}" >修改</p>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">收款方式：</label>
                <div class="col-md-8 controls">
                    <select class="form-control" name="pay_type"  >
                        <option value="" v-for="(l,index) in payTypeList" :value="index" :selected="index == userPayInfo.pay_type" >@{{l}}</option>
                    </select>

                </div>
                <p class="help-block input-two" >修改</p>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">收款金额：</label>
                <div class="col-md-8 controls">
                    <input id="package-price" type="text" name="amount" class="form-control" v-model="userPayInfo.amount"   />
                </div>
                <p class="help-block input-two" >修改</p>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">套餐总金额：</label>
                <div class="col-md-8 controls">
                    <input type="text" name="package_total_price" class="form-control" v-model="userPayInfo.user_registration.package_total_price" disabled />
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">分期方式：</label>
                <div class="col-md-8 controls">
                    <template v-for="(l,index) in fqTypeList">
                        <input type="radio" name="fq_type" :value="index" v-model="userPayInfo.user_registration.fq_type"/>@{{l}}&nbsp;
                    </template>
                    <input type="radio" name="fq_type" value="" v-model="userPayInfo.fq_type" />无
                    <input id="fq_type" type="hidden" v-model="userPayInfo.fq_type"/>
                </div>
                <p class="help-block ajaxLink" data="{field:'fq_type',val:$('#fq_type').val()}" href="{:U('modField',['pay_log_id'=>$_GET['pay_log_id']])}" >修改</p>
            </div>
            <!--<div class="div_input_one">-->
            <!--<label class="col-md-2 control-label" for="input01">-->
            <!--优惠活动：-->
            <!--</label>-->
        <!--<input id="rebate-title" type="text" name="rebate_title" class="form-control fleft" v-model="userPayInfo.rebate_title" style="width: 300px;" :readonly="hasUser" @keyup="searchRebate" />-->
            <!--</div>-->

            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">
                    优惠活动：
                </label>
                <div class="col-md-8 controls">
                    <select id="rebate_id" class="form-control fleft" v-model="userPayInfo.user_registration.rebate_id" name="rebate_id"  @change="setRebate"  >
                    <option value="0">选择优惠活动</option>
                    <option v-for="(l,index) in rebateList" :value="l.id" :selected="l.id == userPayInfo.user_registration.rebate_id">@{{l.title}}<template v-if="l.status == 'DEL'">(已删)</template></option>
                    </select>
                    <input type="hidden" name="rebate_title" v-model="userPayInfo.rebate_title" />
                    <input type="hidden" name="rebate_price" v-model="userPayInfo.rebate_price" />
                </div>
                <p class="help-block ajaxLink" data="{field:'rebate_id',val:$('#rebate_id').val()}" href="{:U('modField',['pay_log_id'=>$_GET['pay_log_id']])}" >修改</p>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">优惠金额：</label>
                <div class="col-md-8 controls">
                    <input type="text" name="rebate_price" class="form-control" v-model="userPayInfo.rebate_price" disabled />
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">收款时间：</label>
                <div class="col-md-8 controls">
                    <input type="text" name="pay_time" class="form-control datetime" v-model="userPayInfo.pay_time"   />
                </div>
                <p class="help-block input-two" >修改</p>
            </div>


            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">已交总金额：</label>
                <div class="col-md-8 controls">
                    <input type="text" name="amount_submitted" class="form-control" v-model="userPayInfo.amount_submitted" readonly   />
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">服务期：</label>
                <div class="col-md-8 controls">
                    <input type="radio" name="server_date" value="0" v-model="userPayInfo.server_date" :checked="userPayInfo.server_date == 0"   />无&nbsp;&nbsp;
                    <input type="radio" name="server_date" value="1" v-model="userPayInfo.server_date" :checked="userPayInfo.server_date == 1"   />1个月&nbsp;&nbsp;
                    <input type="radio" name="server_date" value="2" v-model="userPayInfo.server_date" :checked="userPayInfo.server_date == 2"   />2个月
                    <input id="server_date" type="hidden" v-model="userPayInfo.server_date"/>
                </div>
                <p class="help-block ajaxLink" data="{field:'server_date',val:$('#server_date').val()}" href="{:U('modField',['pay_log_id'=>$_GET['pay_log_id']])}" >修改</p>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">开课状态：</label>
                <div class="col-md-8 controls">
                    <input type="radio" name="is_open" value="0" v-model="userPayInfo.is_open" :checked="userPayInfo.is_open == 0"   />未开课&nbsp;&nbsp;
                    <input type="radio" name="is_open" value="1" v-model="userPayInfo.is_open" :checked="userPayInfo.is_open == 1"   />部分开课&nbsp;&nbsp;
                    <input type="radio" name="is_open" value="2" v-model="userPayInfo.is_open" :checked="userPayInfo.is_open == 2"   />全部开课
                    <input id="is_open" type="hidden" v-model="userPayInfo.is_open"/>
                </div>
                <p class="help-block ajaxLink" data="{field:'is_open',val:$('#is_open').val()}" href="{:U('modField',['pay_log_id'=>$_GET['pay_log_id']])}" >修改</p>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">备注：</label>
                <div class="col-md-8 controls">
                    <textarea id="remark" name="remark" class="form-control" style="width:400px; height:120px;" v-model="userPayInfo.remark"   ></textarea>
                </div>
                <p class="help-block input-two" data="{field:'remark',val:$('#remark').val()}" href="{:U('modField',['pay_log_id'=>$_GET['pay_log_id']])}" >修改</p>
            </div>
        </fieldset>
    </form>
</div>
@endsection