@extends("admin.public.layout")
@section("right_content")
<script>
function uploadCallback(url,obj,json){
	$("input[name='qrc_link']").val(json.qr_link);
}
function selectUserCallback(user){
    if(!user)
        return ;
    $("input[name='leader_name']").val(user.name);
    $("input[name='leader_id']").val(user.uid);
    $("input[name='dapeng_user_mobile']").val(user.dapeng_user_mobile);
}
</script>
<div class="row dp-member-title-2">
    <h4 class="col-md-4" style="padding-left:0">@if($group->id)修改@else添加 @endif群信息</h4>
</div>
<style>
    .item-default{width: 30px; float: left;}
</style>
<div class="row dp-member-body-2">

    <form role="form" id="regForm" class="form-horizontal" action="{{ route('admin.group.save') }}">
    {{ csrf_field() }}
    @if($group->id)
    <input type="hidden" name="id" value="{{$group->id}}" />
    @endif
        <fieldset>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">
                    		群所属
                </label>
                <div class="col-md-8 controls">
                    <div class="radio">
                        <label>
                            <input class="select-qun" type="radio" name="type" checked value="1" />QQ群
                        </label>
                        <label>
                            <input class="select-qun" type="radio" name="type" @if($group->type == 2) checked @endif  value="2"  />微信群
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label nickname" for="input01">班级代号</label>
                <div class="col-md-8 controls">
                    <input type="text" name="group_name" class="form-control" style="width:200px" value="{{ $group->group_name}}" placeholder="">
                    <p class="help-block"></p>
                </div>
            </div>

			<div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label account" for="input01">群号码</label>
                <div class="col-md-8 controls">
                    <input type="text" name="qq_group" value="{{$group->qq_group}}" class="form-control" maxlength="20" style="width:200px" placeholder="">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">上传二维码</label>
                <div class="col-md-8 controls">
                    <input type="text" name="qrc_url" value="{{$group->qrc_url}}" class="form-control fleft" maxlength="11" style="width:200px;" placeholder="请上传二维码图片" id="qrc_url">
                <a class="common-button dblock fleft combg2 ml5 ajaxUpload" uploadTarget="#qrc_url" href="javascript:;">上传</a>
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label qr_link" for="input01">群链接地址</label>
                <div class="col-md-8 controls">
                    <input type="text" name="qrc_link" value="{{ $group->qrc_link}}" class="form-control" style="width:200px" placeholder="">
                    <p class="help-block">该链接为群二维码识别后的地址</p>
                </div>
            </div>
			<div class="form-group">
                <label class="col-md-2 control-label" for="input01">所属课程顾问</label>
                <div class="col-md-8 controls">
                    <input type="hidden" name="leader_id" value="{{ $group->leader_id}}" >
                    <input type="text" name="leader_name" value="{{ $group->user->name}}" class="form-control fleft" maxlength="11" style="width:200px;" readonly="true" placeholder="请选择课程顾问">
                <a class="common-button dblock fleft combg2 ml5 select_adviser" href="javascript:;" callback="selectUserCallback">选择</a>
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">主站手机号</label>
                <div class="col-md-8 controls">
                    <input type="text" name="dapeng_user_mobile" class="form-control fleft" style="width:200px" value="{{ $group->user->dapeng_user_mobile}}" readonly="true" placeholder="主站账号">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label fleft" for="input01">开启状态</label>
                  <div class="slideThree fleft ml15" on="开启" off="关闭">  
				      <input value="1" id="slideThree" name="is_open" type="checkbox">
				      <label for="slideThree"></label>
				    </div>
            </div>
            <!-- <div class="form-group">
                <label class="col-md-2 control-label fleft" for="input01">满员状态</label>
                  <div class="slideThree fleft ml15" on="正常" off="已满">  
				      <input value="1" id="slideThree1" <eq name="r.status" value="1">checked="checked"</eq> name="status" type="checkbox">
				      <label for="slideThree1"></label>
				    </div>
            </div> -->
            <div class="form-group">
                <label class="col-md-2 control-label">备注</label>
                <div class="col-md-8 controls">
                	<textarea name="mark" class="form-control" style="width:400px; height:120px;"></textarea>
                    <p class="help-block"></p>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-md-2 control-label"></label>
                <div class="col-md-8 controls">
                    <button class="btn btn-primary ajaxSubmit" type="button" showLoading="1">确认保存</button>
                    <button class="btn btn-white" type="reset">重置</button>
                </div>
            </div>
        </fieldset>
    </form>

</div>
@endsection