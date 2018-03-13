@extends("admin.public.layout")
@section("right_content")
<div class="row dp-member-title-2">
    <h4 class="col-md-4" style="padding-left:0"><notempty name="r.uid">修改<else />添加</notempty>关单信息</h4>
</div>
<div class="row dp-member-body-2">
    <form role="form" id="regForm" class="form-horizontal" method="post">
    <input type="hidden" name="roster_id" value="{$r.id}" />
    <input type="hidden" name="grade" value="{$Think.get.grade}" />
        <fieldset>
            <div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">{{ $roster->roster_type_text }}号</label>
                <div class="col-md-8 controls">
                    <input type="text" name="qq" class="form-control" style="width:200px" maxlength='10' placeholder="" value="{{ $roster->roster_no }}" disabled="disabled">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">群号</label>
                <div class="col-md-8 controls">
                    <input type="text" name="" class="form-control" style="width:200px" placeholder="" value="{{ $roster->qq_group }}" disabled="disabled">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">群昵称</label>
                <div class="col-md-8 controls">
                    <input type="text" name="" class="form-control" style="width:200px" placeholder="" value="{{ $roster->group_info->group_name }}" disabled="disabled">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">课程顾问</label>
                <div class="col-md-8 controls">
                    <input type="text" name="" class="form-control" style="width:200px" placeholder="" value="{{ $roster->adviser->name }}" disabled="disabled">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">聊天截图</label>
                <div class="col-md-8 controls">
                    <input type="text" name="picurl" id="picurl" class="form-control" style="width:200px;display:inline;" maxlength='10' placeholder="请输入图片地址" value="{{ $roster->picurl }}" >
                    <a class="common-button combg2 ml5 ajaxUpload" uploadTarget="#picurl" style="height:34px;">上传图片</a>
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">私聊深度</label>
                <div class="col-md-8 controls">
                    <select name="deep_level" class="controls col-xs-2">
                        @foreach($rosterDeepLevel as $key =>$level)
                        <option value="{{ $key }}" class="option_group">{{ $level }}</option>
                        @endforeach
                    </select>
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">报名意向</label>
                <div class="col-md-8 controls">
                    <select name="intention" class="controls col-xs-2">
                        @foreach($rosterIntention as $key => $intention)
                        <option value="{{ $key }}" class="option_group">{{ $intention }}</option>
                        @endforeach
                    </select>
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">备注</label>
                <div class="col-md-8 controls">
                    <textarea name="comment" class="form-control" style="width:400px; height:120px;">{{ $roster->comment }}</textarea>
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