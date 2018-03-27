@extends("admin.public.layout")
@section("right_content")
<script src="/js/clipboard.min.js"></script>
<script>
    /**
     * 提交新量检查
     * @param jsonData
     * @param obj
     */
    function checkRosterNoCallback(jsonData,obj) {
        $(".showmsg").hide();
        $(".showmsg"+jsonData.code).show();
        if(jsonData.code == {{ \App\Utils\Util::FAIL }}){
            CustomDialog.failDialog(jsonData.msg);
        }
        return ;
    }

    /**
     * 添加新量后的回调
     * @param jsonData
     * @param obj
     */
    function addRosterCallback(jsonData,obj){
        $(".showmsg").hide();
        $("#success").show();
        $("#qq_group").text(jsonData.data.qq_group);
        new ClipboardJS('#clipboarder');
    }

    /**
     * 重置表单
     */
    function resetForm(){
        $(".showmsg").hide();
        document.forms[0].reset();
    }
</script>
<style>
    .info-list{}
    .info-list dt{ background:url(/admin/images/ico-1.gif) no-repeat 0 4px; padding-left:26px; line-height:30px; font-size:16px;}
    .info-list dd{ color:#747474; line-height:24px; padding:6px 0 0 26px;}
    </style>

    <dl class="info-list">
        <dt>数据提交规则</dt>
        <dd>1. 提交的QQ必须是真实有效，必须进过推广专员初审之后才可提交；<br>2. 推广专员提交的QQ号码的数据状态，与网站数据同步，推广专员可查看流量的进群及开通课程情况；</dd></dd>
    </dl>

    <dl class="info-list" style="margin-top:60px;">
        <dt>填写需要提交的QQ号</dt>
    </dl>

<style>
    .success-notice{ background:#E0FFE4; border:1px #76E77F solid; width:415px; border-radius:8px; padding:0px 20px; color:#2B6330; margin:30px 0 0 15px}
    .success-notice p{ margin:18px 0;}
    .success-notice .s2 em{ font:700 18px Tahoma; margin-right:15px;}

    .error-notice{ background:#FDE6E7; border:1px #F9B2B2 solid; width:415px; border-radius:8px; padding:0px 20px; color:#2B6330; margin:30px 0 0 15px}
    .error-notice p{ margin:18px 0;}
    .error-notice .s2 em{ font:700 18px Tahoma; margin-right:15px;}
</style>

<form role="form" class="form-inline">
    <input type="hidden" name="roster_type" value="{{ \Illuminate\Support\Facades\Request::input('roster_type') }}" />
    <fieldset>

        <div class="form-group">

            <div class="col-md-12">
                <input type="text" id="qq" name="roster_no" class="form-control" maxlength="12" style="width:300px" placeholder="" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"> <button class="btn btn-primary ajaxSubmit" data="{validate:1}" callback="checkRosterNoCallback" > 提交检查</button>
            </div>
        </div>
        <div id="addQQ" class="showmsg success-notice showmsg1" style="display:none;">
            <p class="s1">QQ号码可以提交</p>
            <p class="s3"></p>
            <p class="s4" style="text-align:right">
                <button class="btn btn-primary ajaxSubmit" callback="addRosterCallback">确认提交</button>
            </p>
        </div>

        <div id="unaddQQ" class="showmsg error-notice showmsg0" style="display:none">
            <p class="s1">QQ号码不可以提交</p>
            <p class="s3"></p>
            <p class="s2" style="color:#797575;">您可以重新提交新的QQ号码！</p>
            <p class="s4" style="text-align:right">
                <button type="button" class="btn btn-primary" onClick="resetForm();">取消</button>
            </p>
        </div>

        <div id="success" class="success-notice showmsg" style="display:none">
            <p class="s1">QQ号码已成功提交！</p>
            <p class="s2">请加QQ群 <em id="qq_group"></em>  <input type="button" class="btn btn-primary" value="点击复制QQ群号" data-clipboard-target="#qq_group" id="clipboarder"/></p>
            <p class="s3">请告知该QQ号码加入QQ群，完成流量提交！ </p>
            <p class="s4" style="text-align:right">
                <button type="button" class="btn btn-primary" onClick="resetForm();">确认</button>
            </p>
        </div>

        {{--<div id="error" class="error-notice showmsg" style="display:none">
            <p class="s1" id="err_msg" style="color:#944F4F;">提交的QQ号码重复！请重新提交！</p>
            <p class="s2" style="color:#797575;">您可以重新提交新的QQ号码！</p>
            <p class="s3" style="text-align:right"><button class="btn btn-primary" onClick="location.reload();return false;">确认</button></p>
        </div>--}}
    </fieldset>
</form>
            
@endsection