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
            if(jsonData.code == {{ \App\Utils\Util::SUCCESS }}){
                $(".showmsg").hide();
                $("#success").show();
                $("#qrc_url").attr("src",jsonData.data.group.qrc_url);
                $("#qq_group").text(jsonData.data.qq_group);
                new ClipboardJS('#clipboarder');
            }else{
                CustomDialog.failDialog(jsonData.msg);
            }
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
				.info-list dt{ background:url(__IMG__/ico-1.gif) no-repeat 0 4px; padding-left:26px; line-height:30px; font-size:16px;}
				.info-list dd{ color:#747474; line-height:24px; padding:6px 0 0 26px;}
				</style>
				
				<dl class="info-list">
					<dt>数据提交规则</dt>
					<dd>1. 提交的微信号必须真实有效，必须进过推广专员初审之后才可提交；<br>2. 推广专员提交的微信号的数据状态，与网站数据同步，推广专员可查看提交流量的好友添加状态及开通课程情况；<br>3..提交的流量需要手动修改添加好友状态，从状态“无”修改为“等待添加”状态<br>

（等待添加的含义为推广提交的潜在用户量已经发送添加课程顾问为好友的申请）</dd></dd>
				</dl>
                
                <dl class="info-list" style="margin-top:60px;">
					<dt>输入需要填写的微信号（账号类型：仅为微信号）</dt>
				</dl>

            <style>
                .success-notice{ background:#E0FFE4; border:1px #76E77F solid; width:415px; border-radius:8px; padding:0px 20px; color:#2B6330; margin:30px 0 0 15px}
                .success-notice p{ margin:18px 0;}
                .success-notice .s2 em{ font:700 18px Tahoma; margin-right:15px;}

                .error-notice{ background:#FDE6E7; border:1px #F9B2B2 solid; width:415px; border-radius:8px; padding:0px 20px; color:#2B6330; margin:30px 0 0 15px}
                .error-notice p{ margin:18px 0;}
                .error-notice .s2 em{ font:700 18px Tahoma; margin-right:15px;}
            </style>

<form role="form" class="form-inline" action="{{ route("admin.roster.user.add.post") }}">
    <input type="hidden" name="roster_type" value="{{ \Illuminate\Support\Facades\Request::input('roster_type') }}" />
    <fieldset>

        <div class="form-group">

            <div class="col-md-12">
                <input type="text"  name="roster_no" class="form-control" maxlength="26" style="width:300px" placeholder="" > <button class="btn btn-primary ajaxSubmit" data="{validate:1}" callback="checkRosterNoCallback" showloading="true"  > 提交检查</button>
            </div>
        </div>
        <div id="addQQ" class="showmsg success-notice showmsg1" style="display:none">
            <p class="s1">微信号检测正常</p>
            <p class="s3"></p>
            <p class="s4" style="text-align:right">
                <button class="btn btn-primary ajaxSubmit" callback="addRosterCallback" showloading="true">确认提交</button>
            </p>
        </div>

        <div id="unaddQQ" class="showmsg error-notice showmsg0" style="display:none">
            <p class="s1">微信号不可以提交</p>
            <p class="s3"></p>
            <p class="s2" style="color:#797575;">您可以重新提交新的微信号！</p>
            <p class="s4" style="text-align:right">
                <button type="button" class="btn btn-primary" onClick="resetForm();">取消</button>
            </p>
        </div>

        <div id="success" class="success-notice showmsg" style="display:none">
            <p class="s1">微信号已成功提交！</p>
            <p class="s2">
                请加课程顾问微信号：
                <em id="qq_group"></em>
                <p>
                    <img src="" id="qrc_url" style="width: 200px; height: 200px;" />
                </p>
                <input type="button" class="btn btn-primary" value="点击复制微信号" data-clipboard-target="#qq_group" id="clipboarder"/>
            </p>
            <p class="s3">请告知该学员添加课程顾问为好友，完成流量提交！ </p>
            <p class="s4" style="text-align:right">
                <button type="button" class="btn btn-primary" onClick="resetForm();">确认</button>
            </p>
        </div>

        <div id="error" class="error-notice showmsg" style="display:none">
            <p class="s1" id="err_msg" style="color:#944F4F;">提交的微信号重复！请重新提交！</p>
            <p class="s2" style="color:#797575;">您可以重新提交新的微信号！</p>
            <p class="s3" style="text-align:right"><button class="btn btn-primary" type="button" onClick="resetForm();">确认</button></p>
        </div>
    </fieldset>
</form>
@endsection