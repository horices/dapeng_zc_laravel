@extends("admin.public.layout")
@section("right_content")
    <script>
        function loadInit() {
            vm = new Vue({
                el:"#content-vue",
                data:{
                    r               :   {!! $r !!},
                    course_give     :   {!! $course_give !!},
                },
                mounted:function () {
                    var _this = this;
                    _this.$nextTick(function () {

                    })
                },
                methods:{
                    //新增赠送课程
                    add_course_give:function () {
                        var giveTitle = $(".course_give_title").val();
                        if(!giveTitle){
                            layer.msg('请填写赠送课程标题！',{icon:0,time:2000});
                            return ;
                        }
                        this.course_give.push(giveTitle);
                        $(".course_give_title").val("");
                    },
                    //删除赠送课程
                    minus_course_give:function (index) {
                        this.course_give.splice(index,1);
                    },
                    setAttrNameArr:function (str,val) {
                        return str+"["+val+"]";
                    }
                },
                computed:{
                    giveLength:function () {
                        return this.course_give.length;
                    }
                }
            });
        }
        function setStartTime(obj,value) {
            vm.r.start_time_text = value;
        }

        function setEndTime(obj,value) {
            vm.r.end_time_text = value;
        }
    </script>
    <div class="row dp-member-title-2">
        <div class="btn-back">
            <a href="{{route('admin.pay.rebate.list',['package_id'=>Request::get('package_id')])}}">&lt;&lt;返回</a>
        </div>
        <h4 class="col-md-4" style="padding-left:0"> 优惠活动</h4>
    </div>
    <div id="content-vue" class="row dp-member-body-2">
        <form class="form-horizontal main_container add_tc_form" onsubmit="return beforeOnSubmitAction(this);">
            <fieldset>
                <div class="form-group">
                    <label class="col-md-2 control-label" for="input01">优惠活动：</label>
                    <div class="col-md-8 controls">
                        <input type="text" name="title" class="form-control" v-model="r.title" />
                        <p class="help-block"></p>
                    </div>
                </div>
                <div class="form-group">
                    <!-- Text input-->
                    <label class="col-md-2 control-label" for="input01">套餐金额：</label>
                    <div class="col-md-8 controls">
                        <input type="number" name="price_max" v-model="r.price_max" class="form-control" />
                        <p class="help-block"></p>
                    </div>
                </div>

                <div class="form-group tc_fj ">
                    <!-- Text input-->
                    <label class="col-md-2 control-label" for="input01">活动时间：</label>
                    <div class="input-group form_datetime pull-left">
                        <input class="form-control form_width_3 select_date" name="start_time" type="text" :value="r.start_time_text" callback="setStartTime" />
                    </div>
                    <div class="input-group form_datetime" >
                        <input class="form-control form_width_3 select_date" name="end_time" type="text" :value="r.end_time_text" callback="setEndTime" />
                    </div>
                </div>

                <div class="form-group tc_fj sent_course">
                    <!-- Text input-->
                    <label class="col-md-2 control-label" for="input01">赠送课程：</label>
                    <div class="col-md-8 controls">
                        <input type="text"  class="form-control pull-left course_give_title" placeholder="请输入赠送课程名称" />
                        <span class="pull-left btn_add" style="cursor:pointer" @click="add_course_give">+</span>
                        <p class="pull-left">点击添加赠送课程</p>

                        <div class="add_tc pull-left" v-for="(l,index) in course_give" v-if="course_give">
                            <input type="text" name="give_title[]" v-model="l" class="form-control pull-left" />
                            <span class="pull-left btn_add" style="cursor:pointer" @click="minus_course_give(index)">-</span>
                            <p class="pull-left" style="cursor:pointer">点击删除赠送课程</p>
                        </div>

                    </div>
                </div>


                <input type="hidden" name="id" v-model="r.id" />
                <input type="hidden" name="package_id" value="{{Request::get('package_id')}}" />
                <input type="hidden" name="give_length" :value="giveLength"/>
                <button class="btn btn-primary ajaxSubmit" url="{{route('admin.pay.rebate.save')}}" type="button">确认提交</button>
            </fieldset>
        </form>
    </div>
@endsection