@extends("admin.public.layout")
@section("right_content")
<script>
    function changeGrade(grade){
        $(".grade").hide();
        $(".grade"+grade).show();
    }
    $(function(){
        $("select[name='grade']").change(function(){
            changeGrade($(this).val());
        });
        changeGrade({{ $user->grade }});
    });
</script>
<div class="row dp-member-title-2">
    <h4 class="col-md-4" style="padding-left:0">
        @if($user->uid)编辑@else添加@endif用户
    </h4>
</div>
<div class="row dp-member-body-2">
    <form role="form" id="regForm" class="form-horizontal" action="{{ route('admin.user.save')}}" method="post">
    <input type="hidden" name="_token" value="{{csrf_token()}}"/>
    @if($user->uid)
    <input type="hidden" name="uid" value="{{$user->uid}}" />
    @endif
        <fieldset>
        	@if($user->uid)
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">ID</label>
                <div class="col-md-8 controls">
                    <input type="text" id="name" name="name" readOnly="true" class="form-control" style="width:200px" placeholder="" value="{{$user->uid}}">
                    <p class="help-block"></p>
                </div>
            </div>
            @endif
			<div class="form-group">
                <label class="col-md-2 control-label" for="input01">权限</label>
                <div class="col-md-8 controls">
                	<select name="grade">
                		@foreach($userGradeList as $k=>$v)
							<option value="{{$k}}" @if($user->uid && $k == $user->grade) selected @endif >{{$v}}</option>                			
                		@endforeach
                	</select>
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">姓名</label>
                <div class="col-md-8 controls">
	                <input type="text" name="name"  class="form-control" style="width:200px" placeholder="" value="{{$user->name}}">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">工号</label>
                <div class="col-md-8 controls">
                    <input type="text" name="staff_no"  class="form-control" style="width:200px" placeholder="" value="{{$user->staff_no}}">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">展翅系统账号</label>
                <div class="col-md-8 controls">
                    <input type="text" name="mobile" class="form-control" style="width:200px" maxlength='11' placeholder="" value="{{$user->mobile}}">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">展翅系统密码</label>
                <div class="col-md-8 controls">
                    <input type="text" name="mobile" class="form-control" style="width:200px" maxlength='11' placeholder="" value="{{$user->mobile}}">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group grade grade9 grade10">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">主站账号</label>
                <div class="col-md-8 controls">
                    <input type="text" name="dapeng_user_mobile" class="form-control" style="width:200px" maxlength='11' placeholder="" value="{{$user->dapeng_user_mobile}}">
                    <p class="help-block"></p>
                </div>
            </div>
            
            
			<div class="form-group grade grade9 grade10">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">默认分配QQ数量</label>
                <div class="col-md-8 controls">
                    <input type="text" name="per_max_num_qq" class="form-control" style="width:200px" maxlength='11' placeholder="" value="{{$user->per_max_num_qq}}">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group grade grade9 grade10">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">默认分配微信数量</label>
                <div class="col-md-8 controls">
                    <input type="text" name="per_max_num_wx" class="form-control" style="width:200px" maxlength='11' placeholder="" value="{{$user->per_max_num_wx}}">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"></label>
                <div class="col-md-8 controls">
                    <button class="btn btn-primary ajaxSubmit" type="button" >确认保存</button>
                    <button class="btn btn-white" type="reset">重置</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>
@endsection