<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="stylesheet" type="text/css" href="/admin/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/admin/css/login.css">
@include("admin.public.js")
<title>登陆</title>
    <script>
        smsIntervel = "";
        function checkSmsTime(obj){
            var time  = parseInt($(obj).attr("time"));
            if(time > 0){
                CustomDialog.failDialog(time + "秒后重试");
                return false;
            }
        }
        function sendSms(obj,mobile){
            var time = parseInt($(obj).attr("time"));
            if(time > 0){
                CustomDialog.failDialog(time + "秒后重试");
                return false;
            }
            AjaxAction.ajaxLinkAction("<a url='{{ route('admin.auth.send.sms') }}' data=\"{mobile:'"+mobile+"'}\"></a>",function(data){
                if(data.code == {{ \App\Utils\Util::SUCCESS }}){
                    CustomDialog.successDialog("发送成功");
                    $(obj).attr("time",60);
                    //进行倒计时
                    smsIntervel = setInterval(function(){
                        var time = parseInt($(obj).attr("time"));
                        if(time >0){
                            $(obj).attr("time",time-1);
                            $(obj).text("剩于"+$(obj).attr("time")+" 秒")
                        }else{
                            $(obj).text("获取验证码");
                            clearInterval(smsIntervel);
                        }
                    },1000);
                }else{
                    CustomDialog.failDialog(data.msg);
                }
            })
        }

        function loginCallback(json,obj){
            AjaxAction.defaultReturn(json,obj);
            if(json.code == {{ \App\Utils\Util::FAIL }}){
                $(".verifyCode").click();
            }
a
        }
    </script>
</head>

<body style="background:url(/admin/img/rebc.gif) repeat">
<div class="page_inner">
<div class="top_logo" align="center">
	<img src="/admin/img/logo.png">
</div>
<div class="form_main">
	<div class="form_inner">
    	<h2 class="login_title" align="center">登录账号</h2>
    	<form role="form"  action="{{ route('admin.auth.login')}}">
            {{ csrf_field() }}
        <div  class="form-group">
    	<input type="text" placeholder="手机号" name="username"  class="form-control" >
        </div>
        <div  class="form-group">
    	<input type="password" placeholder="输入6~32位密码" name="password"   class="form-control" >
        </div>
        <div  class="form-group yz_group">
    	<input type="text" placeholder="验证码" id="verify" name="verify"  class="form-control pull-left" >
        <img src="{{ captcha_src() }}" alt='验证码' class="pull-right verifyCode" onclick="$('#verify').val(''); this.src='{{ captcha_src() }}?'+Math.random(100,999)">
        </div>
        <div  class="form-group">
        	<div class="radio_box">
                <input type="checkbox" class="pull-left" name="remember_me" value="1" checked>
                <label  class="pull-left">记住用户名</label>
            </div>
        </div>
        <div class="form-group">
        <button type="button" class="btn btn-success ajaxSubmit" showloading="true" callback="loginCallback">登录</button>
        </div>
    </form>
</div>
</div>
</body>
</html>
