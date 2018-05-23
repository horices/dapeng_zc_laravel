@extends("admin.public.layout")
@section("right_content")

    <div class="row dp-member-title-2">
        <h4 class="col-md-4" style="padding-left:0">
            课程顾问<notempty name="r.uid">分量编辑<else />添加</notempty>
        </h4>
    </div>

    <div class="row dp-member-body-2">

        <form role="form" id="regForm" class="form-horizontal" method="post">
            <input type="hidden" name="back_url" value="{{ \Illuminate\Support\Facades\URL::previous() }}" />
            <fieldset>
                <div class="form-group">
                    <label class="col-md-2 control-label" for="input01">ID</label>
                    <div class="col-md-8 controls">
                        <input type="text" name="uid" readOnly="true" class="form-control" style="width:200px" placeholder="" value="{{$r->uid}}">
                        <p class="help-block"></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label" for="input01">工号</label>
                    <div class="col-md-8 controls">
                        <input type="text" id="staff_no" name="staff_no" readOnly="true"  class="form-control" style="width:200px" placeholder="" value="{{$r->staff_no}}">
                        <p class="help-block"></p>
                    </div>
                </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label" for="input01">在职状态</label>
                        <div class="col-md-8 ">
                            <label class="btn btn-primary">
                                {{$r->incumbency_text}}
                            </label>

                        </div>
                    </div>

                <div class="form-group">
                    <label class="col-md-2 control-label" for="input01">姓名</label>
                    <div class="col-md-8 controls">
                            <input type="text" id="name" name="name" readOnly="true"  class="form-control" style="width:200px" value="{{$r->name}}">
                        <p class="help-block"></p>
                    </div>
                </div>


                <div class="form-group">
                    <!-- Text input-->
                    <label class="col-md-2 control-label" for="input01">展翅系统账号</label>
                    <div class="col-md-8 controls">
                        <input type="text" name="mobile" readOnly="true" class="form-control" style="width:200px" maxlength='11' placeholder="" value="{{$r->mobile}}">
                        <p class="help-block"></p>
                    </div>
                </div>

                <div class="form-group">
                    <!-- Text input-->
                    <label class="col-md-2 control-label" for="input01">主站账号</label>
                    <div class="col-md-8 controls">

                        <input type="text" name="dapeng_user_mobile" readOnly="true" class="form-control" style="width:200px" maxlength='11' value="{{$r->dapeng_user_mobile}}">
                        <p class="help-block"></p>
                    </div>
                </div>

                {{--<div class="form-group">
                    <!-- Text input-->
                    <label class="col-md-2 control-label" for="input01">自定义密码</label>
                    <div class="col-md-8 controls">
                        <input type="text" name="password" class="form-control"  style="width:200px" maxlength='11' placeholder="默认123456，不修改请留空" value="">
                        <p class="help-block"></p>
                    </div>
                </div>--}}


                <div class="form-group">
                    <!-- Text input-->
                    <label class="col-md-2 control-label" for="input01">默认分配QQ数量</label>
                    <div class="col-md-8 controls">
                        <input type="text" name="per_max_num_qq" class="form-control" style="width:200px" maxlength='11' placeholder="" value="{{$r->per_max_num_qq}}">
                        <p class="help-block"></p>
                    </div>
                </div>

                <div class="form-group">
                    <!-- Text input-->
                    <label class="col-md-2 control-label" for="input01">默认分配微信数量</label>
                    <div class="col-md-8 controls">
                        <input type="text" name="per_max_num_wx" class="form-control" style="width:200px" maxlength='11' placeholder="" value="{{$r->per_max_num_qq}}">
                        <p class="help-block"></p>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-2 control-label"></label>
                    <div class="col-md-8 controls">
                        <button class="btn btn-primary ajaxSubmit" url="{{route('admin.user.save',['route_url'=>'admin.user.quantity-list'])}}" type="button" >确认保存</button>
                        <button class="btn btn-white" type="reset">重置</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
@endsection