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
                    this.course_attach.push({'title':attachTitle,'price':attachPrice});
                    $(".course_attach_title").val("");
                    $(".course_attach_price").val("");
                },
                //删除附加课程
                minus_course_attach:function (index) {
                    this.course_attach.splice(index,1);
                },
                setAttrNameArr:function (str,val) {
                    return str+"["+val+"]";
                }
            },
            computed:{
                attachLength:function () {
                    return this.course_attach.length;
                }
            }
        });
    }
</script>
<div class="row dp-member-title-2">
    <div class="btn-back">
        <a href="{{route('admin.pay.package.list')}}">&lt;&lt;返回</a>
    </div>
    <h4 class="col-md-4" style="padding-left:0"> 课程套餐</h4>
</div>
<div id="content-vue" class="row dp-member-body-2">
    <form class="form-horizontal main_container add_tc_form" onsubmit="return beforeOnSubmitAction(this);">
        <fieldset>
            <div class="div_input_one">
                <label for="input01 " class="col-md-2 control-label "> 学院名称： </label>
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

                    <div class="add_tc pull-left" v-for="(l,index) in course_attach" v-if="course_attach">
                        <input type="text" name="attach_title[]" placeholder="请输入附加课程名称" class="form-control pull-left" v-model="l.title" />
                        <input type="number" name="attach_price[]" placeholder="请输入金额" class="form-control pull-left" v-model="l.price" />
                        <span class="pull-left btn_add" @click="minus_course_attach(index)">-</span>
                        <p class="pull-left">点击删除附加课程</p>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" v-model="r.id" />
            <input type="hidden" :name="setAttrNameArr('attach_title',index)" :value="l.title" v-for="(l,index) in course_attach "/>
            <input type="hidden" :name="setAttrNameArr('attach_price',index)" :value="l.price" v-for="(l,index) in course_attach "/>
            <input type="hidden" name="attach_length" :value="attachLength"/>
            <button class="btn btn-primary ajaxSubmit" url="{{route('admin.pay.package.save')}}" type="button">确认提交</button>
        </fieldset>
    </form>
</div>
@endsection