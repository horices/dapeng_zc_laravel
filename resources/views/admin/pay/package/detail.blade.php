@extends("admin.public.layout")
@section("right_content")
<script>
    function loadInit() {
        vm = new Vue({
            el:"#content-vue",
            data:{
                r               :   {!! $r !!},
                course_attach   :   {!! $course_attach !!},
            },
            mounted:function () {
                var _this = this;
                _this.$nextTick(function () {

                })
            },
            methods:{
                //新增附加课程
                add_course_attach:function () {
                    var attachTitle = $(".course_attach_title").val();
                    if(!attachTitle){
                        layer.msg('请填写附加课程标题！',{icon:0,time:2000});
                        return ;
                    }
                    var attachPrice = $(".course_attach_price").val();
                    if(!attachPrice){
                        layer.msg('请填写附加课程价格！',{icon:0,time:2000});
                        return ;
                    }
                    this.course_attach.attach.push({'title':attachTitle,'price':attachPrice});
                    $(".course_attach_title").val("");
                    $(".course_attach_price").val("");
                },
                //删除附加课程
                minus_course_attach:function (index) {
                    this.course_attach.attach.splice(index,1);
                },
                //新增赠送课程
                add_course_give:function () {
                    var giveTitle = $(".course_give_title").val();
                    if(!giveTitle){
                        layer.msg('请填写赠送课程标题！',{icon:0,time:2000});
                        return ;
                    }
                    this.course_attach.give.push({'title':giveTitle});
                    $(".course_give_title").val("");
                },
                //删除赠送课程
                minus_course_give:function (index) {
                    this.course_attach.give.splice(index,1);
                }
            }
        });
    }
</script>
<div class="row dp-member-title-2">
    <div class="btn-back">
        <a href="{{route('admin.pay.package.list')}}">&lt;&lt;返回</a>
    </div>
    <h4 class="col-md-4" style="padding-left:0"> 新增课程套餐</h4>
</div>
<div id="content-vue" class="row dp-member-body-2">
    <form class="form-horizontal main_container add_tc_form" onsubmit="return beforeOnSubmitAction(this);">
        <fieldset>
            <div class="div_input_one">
                <label for="input01 " class="col-md-2 control-label "> 学员名称： </label>
                <select class="form-control" name="school_id" v-model="r.school_id">
                    <option value="SJ">设计学院</option>
                    <option value="MS">美术学院</option>
                </select>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">套餐名称：</label>
                <div class="col-md-8 controls">
                    <input type="text" name="title" class="form-control" v-model="r.title" />
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">套餐金额：</label>
                <div class="col-md-8 controls">
                    <input type="number" name="price" v-model="r.price" class="form-control" />
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group tc_fj">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">附加课程：</label>
                <div class="col-md-8 controls">
                    <input type="text" class="form-control pull-left course_attach_title" placeholder="请输入附加课程名称" />
                    <input type="number" value="" class="form-control pull-left course_attach_price" placeholder="请输入金额" />
                    <span class="pull-left btn_add" style="cursor:pointer" @click="add_course_attach">+</span>
                    <p class="pull-left">点击添加附加课程</p>

                    <div class="add_tc pull-left" v-for="(l,index) in course_attach.attach">
                        <input type="text" name="attach_title[]" placeholder="请输入附加课程名称" class="form-control pull-left" v-model="l.title" />
                        <input type="number" name="attach_price[]" placeholder="请输入金额" class="form-control  pull-left" v-model="l.price" />
                        <span class="pull-left btn_add" @click="minus_course_attach(index)">-</span>
                        <p class="pull-left">点击删除附加课程</p>
                    </div>
                </div>
            </div>

            <div class="form-group tc_fj sent_course">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">赠送课程：</label>
                <div class="col-md-8 controls">
                    <input type="text"  class="form-control pull-left course_give_title" placeholder="请输入赠送课程名称" />
                    <span class="pull-left btn_add" style="cursor:pointer" @click="add_course_give">+</span>
                    <p class="pull-left">点击添加赠送课程</p>

                    <div class="add_tc pull-left" v-for="(l,index) in course_attach.give">
                        <input type="text" name="give_title[]" v-model="l.title" class="form-control pull-left" />
                        <span class="pull-left btn_add" style="cursor:pointer" @click="minus_course_give(index)">-</span>
                        <p class="pull-left" style="cursor:pointer">点击删除赠送课程</p>
                    </div>

                </div>
            </div>

            <div class="form-group tc_fj ">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">优惠活动：</label>
                <div class="col-md-8 controls">
                    <input type="text" name="rebate_title" :value="course_attach.rebate.title" placeholder="请输入活动名称" class="form-control pull-left">
                    <input type="number" name="rebate_price" :value="course_attach.rebate.price" placeholder="金额" class="form-control pull-left">
                </div>
            </div>

            <div class="form-group tc_fj ">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">活动时间：</label>
                <div class="input-group form_datetime pull-left">
                    <input class="form-control form_width_3 select_date" name="rebate_start_date" type="text" :value="course_attach.rebate.start_date" />
                </div>
                <div class="input-group form_datetime" >
                    <input class="form-control form_width_3 select_date" name="rebate_end_date" type="text" :value="course_attach.rebate.end_date" />
                </div>
            </div>
            <input type="hidden" name="id" v-model="r.id" />
            <button class="btn btn-primary ajaxSubmit" url="{{route('admin.pay.package.save')}}" type="button">确认提交</button>
        </fieldset>
    </form>
</div>
@endsection