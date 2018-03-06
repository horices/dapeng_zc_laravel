@extends("admin.public.layout")
<script>
    function loadInit() {
        vm = new Vue({
            el: '#content-container',
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
                rebateList:{{$rebateList}},
                //附加套餐列表
                packageAttachList:{{$packageAttachList}},
                giveList:{{$giveList}},   //赠送课程
                //分期方式列表
                fqTypeList:{{$fqTypeList}},
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
</script>
@section("right_content")
        55
@endsection