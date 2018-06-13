<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<title>用户登录 大鹏教育-高品质的设计师在线教育</title>
@include("admin.public.js")
<style>
@charset "utf-8";

* { -webkit-tap-highlight-color: rgba(0, 0, 0, 0); }
blockquote, body, button, dd, dl, dt, fieldset, figure, form, h1, h2, h3, h4, h5, h6, input, keygen, legend, menu, ol, p, pre, select, td, textarea, th, ul { margin: 0; padding: 0; }
html { -webkit-text-size-adjust: 100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; }
*, *:after, *:before { -webkit-box-sizing: inherit; -moz-box-sizing: inherit; box-sizing: inherit; }
body { line-height: 1.4; }
h1, h2, h3, h4, h5, h6 { font-size: 100%; }
ol, ul { list-style: none; }
table { border-collapse: collapse; border-spacing: 0; }
button, input, select, textarea { color: inherit; font-family: inherit; font-size: 100%; font-weight: inherit; }
label { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; cursor: pointer; }
abbr, fieldset, img { border: 0; }
abbr[title] { border-bottom: 1px dotted; }
address, em, i, optgroup { font-style: normal; }
img { vertical-align: middle; }
q:before, q:after { content: ''; }
sup { vertical-align: text-top; }
sub { vertical-align: text-bottom; }
small { font-size: 85.7%; }
code, kbd, pre, samp { font-family: Monaco, Menlo, Consolas, "Courier New", monospace; font-size: 1em; }
pre { white-space: pre-wrap; }
/* HTML 5 */
article, aside, details, figcaption, figure, footer, header, hgroup, menu, main, nav, section, summary { display: block; }
body { color: #999999; font: normal 12px/1.4 Verdana, "Lantinghei SC", "Hiragino Sans GB", 'Microsoft YaHei', Helvetica, arial, simsun, sans-serif; }
input, button { outline: 0; }
table { max-width: 100%; }
a { color: #35b558; text-decoration: none; }
a:hover { color: #51cc73; text-decoration: underline; }
.user-select-none { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
.symbol { /* 符号字体 */
font-family: simsun !important; }
.clear { clear: both !important; }
.float-left { float: left !important; }
.float-right { float: right !important; }
.float-none { float: none !important; }
.block { display: block !important; }
.hide { display: none !important; }
.hidden { visibility: hidden !important; }
.disabled:hover, .disabled:hover a { cursor: default !important; cursor: not-allowed !important; }
.full-block { display: block; width: 100% !important; }
/* color */
.passport-light-color { /* 浅色 */
color: #999999 !important; }
.passport-tip-color { /* 提示色 */
color: #fd6505 !important; }
.passport-warn-color { /* 警告色 */
color: #ff0000 !important; }
.passport-success-color { /* 成功色 */
color: #339734 !important; }
/* text align */
.text-left { text-align: left !important; }
.text-right { text-align: right !important; }
.text-center { text-align: center !important; }
.text-nowrap { white-space: nowrap !important; }
/* mask */
.mask { position: fixed; top: 0; left: 0; z-index: 900; width: 100%; height: 100%; background: #000000; opacity: 0.4; }
/* position */
.static { position: static !important; }
.absolute { position: absolute !important; }
.fixed { position: fixed !important; }
/* control base */
input, button, .passport-btn, select, textarea { -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; }
input, button, .passport-btn, select, label { vertical-align: middle; }
button, input[type="button"], input[type="submit"], input[type="reset"] { line-height: normal !important; cursor: pointer; }
.passport-placeholder { color: #999999 !important; }
.passport-empty-input, .passport-empty-input:focus, .passport-empty-input:hover { background: #ffffff !important; border: 1px solid #ff6711 !important; -webkit-box-shadow: none !important; box-shadow: none !important; }
.passport-error-input, .passport-error-input:focus, .passport-error-input:hover { background: #ffffff !important; border: 1px solid #ff6711 !important; -webkit-box-shadow: none !important; box-shadow: none !important; }
.passport-empty-txt { color: #ff0000 !important; }
.passport-error-txt { color: #ff0000 !important; }

.passport-success-txt { color: #35b558 !important; }
/* note */
.passport-note { color: #666666; vertical-align: middle; }
span.passport-note { padding-left: 5px; }
div.passport-note { padding: 5px 0 0; line-height: 1.8; }
.passport-note strong { padding: 0 5px; color: #ff0000; }
.passport-note b { padding: 0 5px; color: #333333; }
/* checkbox & radio */
input[type="checkbox"], input[type="radio"] { width: 13px; height: 13px; margin: 0 5px 0 2px; cursor: pointer; }
input[type="checkbox"][disabled], input[type="radio"][disabled], input[type="checkbox"][disabled]:hover, input[type="radio"][disabled]:hover, input[type="checkbox"][disabled]:focus, input[type="radio"][disabled]:focus, input[type="checkbox"][disabled]:active, input[type="radio"][disabled]:active { cursor: default !important; cursor: not-allowed !important; }
/* button */
.passport-btn { height: 26px; padding: 0 15px; font-size: 12px; border: 1px solid #eaeaea; -webkit-transition: background-color 0.8s, border-color 0.8s; -moz-transition: background-color 0.8s, border-color 0.8s; transition: background-color 0.8s, border-color 0.8s; background: #eaeaea; border-color: #eaeaea; color: #666666; /* 以下样式针对非 input、button 的其他标签 */
display: inline-block; line-height: 24px; text-align: center; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; cursor: pointer; }
.passport-btn .icon { margin-right: 5px; }
.passport-btn:hover { background: #eeeeee; color: #999999; }
.passport-btn:active { background: #eeeeee; color: #999999; }
.passport-btn:hover { text-decoration: none; }
.passport-btn:not(button) .icon { position: relative; top: -1px; }
.passport-btn.disabled, .passport-btn[disabled], .passport-btn.disabled:hover, .passport-btn[disabled]:hover, .passport-btn.disabled:focus, .passport-btn[disabled]:focus, .passport-btn.disabled:active, .passport-btn[disabled]:active { -webkit-transition: none; -moz-transition: none; transition: none; background: #eeeeee !important; border: 1px solid #e7e7e7 !important; color: #999999 !important; cursor: default !important; cursor: not-allowed !important; }
/* button theme */
.passport-btn-def { background: #35b558; border-color: #35b558; color: #ffffff; }
.passport-btn-def:hover { background: #51cc73; border-color: #51cc73; color: #ffffff; }
.passport-btn-def:active { background: #51cc73; border-color: #51cc73; color: #ffffff; }
.passport-btn-primary { background: #2d85ca; border-color: #2d85ca; color: #ffffff; }
.passport-btn-primary:hover { background: #529dd8; border-color: #529dd8; color: #ffffff; }
.passport-btn-primary:active { background: #529dd8; border-color: #529dd8; color: #ffffff; }
/* button height */
.passport-btn.xl { /* extra large */
height: 40px; padding: 0 30px; font-size: 18px; line-height: 38px; }
/* button width */
.passport-btn.w-full { width: 100%; padding: 0 !important; }
/* button padding */
/* button file */
.passport-txt[disabled], select[disabled], textarea[disabled], .passport-txt[disabled]:hover, select[disabled]:hover, textarea[disabled]:hover, .passport-txt[disabled]:focus, select[disabled]:focus, textarea[disabled]:focus, .passport-txt[disabled]:active, select[disabled]:active, textarea[disabled]:active { background: #f8f8f8 !important; border-color: #f1f1f1 !important; -webkit-box-shadow: none !important; box-shadow: none !important; cursor: default !important; cursor: not-allowed !important; }
/* input text */
.passport-txt { width: 100px; height: 26px; padding: 5px 5px; font-size: 12px; line-height: 16px; border: 1px solid #e4e4e4; border-color: #e4e4e4; -webkit-transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out; -moz-transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out; transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out; }
.passport-txt[readonly="readonly"], .passport-txt[readonly="readonly"]:hover, .passport-txt[readonly="readonly"]:focus { border-color: #e4e4e4 !important; -webkit-box-shadow: none; box-shadow: none; }
.passport-txt:hover { border-color: #cccccc; -webkit-box-shadow: 0 0 5px #ffffff; box-shadow: 0 0 5px #ffffff; }
.passport-txt:focus { border-color: #35b558; -webkit-box-shadow: 0 0 5px #51cc73; box-shadow: 0 0 5px #51cc73; }
/* text radius */
.passport-txt.txt-radius { -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; }
/* text height */
.passport-txt.xl { height: 40px; padding: 11px 11px; font-size: 14px; line-height: 18px; }
/* text width */
.passport-txt.w-lg { width: 170px; }
.passport-txt.w-full { width: 100%; }
/* tel text */
/* tel height*/
/* txt section */
/* file */
/* select */
/* select height */
/* select width */
/* textarea */
/* icon */
.passport-icon { display: inline-block; vertical-align: middle; background-repeat: no-repeat; }
/* font icon */
.f-icon { display: inline-block; font: normal normal normal 14px/1 FontAwesome; font-size: inherit; text-rendering: auto; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; transform: translate(0, 0); }
/* 18x18 */
.icon-refresh { width: 18px; height: 18px; background-position: 0 -38px; }
/* 32x32 */
.icon-phone, .icon-letter { width: 32px; height: 32px; background-position: -49px -58px; }
.icon-letter { background-position: -83px -58px; }
/* 38x38 */
/*.icon-tencent,
.icon-weibo,
.icon-weixin,
.icon-eoe {
  width: 38px;
  height: 38px;
  background-position: 0 0;
}*/
.icon-tencent, .icon-weibo, .icon-weixin, .icon-eoe { width: 55px; height: 55px; background-position: 0 -65px; }
.passport-third a:hover .icon-tencent { background-position: 0 0; }
.passport-third .icon-weibo { background-position: -81px -65px; }
.passport-third a:hover .icon-weibo { background-position: -81px 0px; }
.passport-third .icon-weixin { background-position: -162px -65px; }
.passport-third a:hover .icon-weixin { background-position: -162px 0px; }
.passport-third .icon-eoe { background-position: -243px -65px; }
.passport-third a:hover .icon-eoe { background-position: -243px 0; }
/* 48x48 */
.icon-success { width: 48px; height: 48px; background-position: 0 -58px; }
.passport-tab > .tabs ul:after { display: table; clear: both; content: " "; }
.passport-tab > .tabs li { float: left; cursor: pointer; }
.passport-tab > .tabbed > .tab-group { display: none; }
.passport-popup { position: fixed; top: 0; left: 0; z-index: 990; padding: 10px; background: rgba(0, 0, 0, 0.2); }
.passport-popup:after { display: table; clear: both; content: " "; }
.passport-popup .close { position: absolute; top: 10px; right: 10px; display: block; width: 20px; height: 20px; border-radius: 50%; cursor: pointer; background: url("/admin/images/popup_def_close.png") no-repeat center center / 14px 14px; }
.passport-popup > .layout-inner { position: relative; float: left; width: 100%; height: 100%; background: #fff; }
.passport-popup > .layout-inner > .hd { height: 40px; padding: 0 30px 0 8px; background: #f3f3f3; line-height: 40px; cursor: default; }
.passport-popup > .layout-inner > .hd h2 { color: #666; font-size: 16px; font-weight: normal; }
.passport-popup .btn-wrap { padding: 10px 0; text-align: center; }
.passport-popup.passport-popup-primary .hd { background: #f3f3f3; }
.passport-popup.passport-popup-info .hd { background: none; }
.passport-header { margin-bottom: 30px; }
#logo { width: 150px; height: 37px; margin-right: auto; margin-left: auto; }
#logo img { width: 100%; height: 100%; }
#warning { position: fixed; top: -40px; left: 50%; z-index: 60000; opacity: 0; padding: 4px 20px; border-bottom-right-radius: 3px; border-bottom-left-radius: 3px; font-size: 16px; font-weight: 400; color: #fff; text-align: center; }
.waring-success { background: #35b558; }
.waring-failure { background: #ff5c00; }
.waring-sub { background: #2d85ca; }
.passport-step { padding: 8px 6px; }
.passport-step li { position: relative; top: 0; left: 0; float: left; width: 100px; margin-left: 90px; padding-top: 35px; text-align: center; }
.passport-step li.first { margin-left: 0; }
.passport-step li .disc { position: absolute; top: 0; left: 50%; z-index: 20; display: block; width: 30px; height: 30px; margin-left: -15px; background-color: #b9b9b9; -webkit-border-radius: 50%; -moz-border-radius: 50%; border-radius: 50%; color: #fff; font: 16px/30px arial; cursor: default; }
.passport-step li .strip { position: absolute; top: 13px; left: -120px; z-index: 10; display: block; width: 150px; height: 3px; background-color: #b9b9b9; }
.passport-step p { color: #666; font-size: 14px; line-height: 18px; text-align: center; white-space: nowrap; }
.passport-step .now .disc, .passport-step .now .strip, .passport-step .past .disc, .passport-step .past .strip { background-color: #35b558; }
.passport-safely { margin-top: 10px; }
.passport-safely li { float: left; width: 60px; height: 14px; margin-right: 4px; background-color: #ddd; color: #fff; font-size: 12px; line-height: 14px; text-align: center; cursor: default; }
.passport-safely.safely-danger .danger { background-color: #da4619; }
.passport-safely.safely-general .danger, .passport-safely.safely-general .general { background-color: #ff5c00; }
.passport-safely.safely-safe li { background-color: #35b558; }
.clearfix:after, .passport-step:after, .passport-safely:after, .passport-sign:after, .passport-form .form-imgcode .form-cont:after { display: table; clear: both; content: " "; }
.passport-icon { background-image: url("/admin/images/fouricon.png"); background-size: 298px 120px; }
body { background: #f5f5f5 url("/admin/images/rebc.gif"); }
.wrap { margin-right: auto; margin-left: auto; }
.wrap:after { display: table; clear: both; content: " "; }
.passport-wrapper { width: 426px; margin-right: auto; margin-left: auto; padding-top: 100px; padding-bottom: 20px; }
.passport-sign { /*height: 400px;*/ padding: 40px; box-shadow: 0 0 6px #ccc; background-color: #fff; }
.passport-sign .main-form, .passport-sign .aside { float: left; padding: 0 25px; }
.passport-sign .main-form { width: 360px; padding-right: 40px; }
.passport-sign .main-form h2 { color: #333; margin-bottom: 10px; font-size: 20px; font-weight: normal; line-height: 20px; }
.passport-sign .main-form p { margin-bottom: 20px; }
.passport-sign .main-form.main-form-sign { /*min-height: 350px;*/}
.passport-sign .aside { width: 340px; margin-left: 40px; }
.passport-sign .passport-goto { margin-bottom: 10px; padding-top: 50px; color: #000; font-size: 16px; }
.passport-sign .passport-goto.mg-b100 { margin-bottom: 100px; }
.passport-sign .sendgift { margin-bottom: 50px; }
.passport-sign .passport-third .hd { height: 16px; font-size: 12px; text-align: center; }
.passport-sign .passport-third .hd .layout-inner { margin-bottom: 7px; border-bottom: 2px solid #f2f2f2; }
.passport-sign .passport-third .hd h3 { width: 106px; margin: 0 auto -8px; background-color: #fff; font-weight: normal; line-height: 16px; }
.passport-sign .passport-third .links { width: 286px; margin: 0 auto; padding-top: 20px; }
.passport-sign .passport-third .links a { float: left; display: block; margin: 0 8px; }
.passport-sign .passport-ad { width: 240px; height: 60px; margin-top: 80px; }
.passport-sign .passport-tab .tabs { margin-bottom: 15px; }
.passport-sign .passport-tab .tabs ul { border-bottom: 2px solid #e4e4e4; }
.passport-sign .passport-tab .tabs li { float: left; width: 50%; height: 32px; margin-bottom: -2px; text-align: center; font-size: 16px; }
.passport-sign .passport-tab .tabs li.active { color: #35b558; border-bottom: 2px solid #35b558; }
.passport-form .form-item { margin-bottom: 12px; }
.passport-form .form-cont { position: relative; line-height: 26px; }
.passport-form .js-placeholder { position: absolute; top: 0; left: 0; right: 0; z-index: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; height: 40px; padding: 11px; font-size: 12px; line-height: 18px; text-align: left; }
.passport-form .passport-txt { position: relative; z-index: 10; color: #666; font-size: 12px; }
.passport-form .passport-txt.placeholder { background-color: rgba(0, 0, 0, 0); }
.passport-form .passport-note { position: absolute; top: 0; right: 0; bottom: 0; z-index: 20; padding: 0; background-color: rgba(0, 0, 0, 0); color: #fff; line-height: 26px; }
.passport-form .passport-note span { display: block; height: 26px; margin: 7px 7px 0 0; padding: 0 5px; background-color: #ff5c00; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; }
.passport-form .form-imgcode .layout-inner { position: relative; float: left; width: 170px; }
.passport-form .form-imgcode .imgcode { float: right; }
.passport-form .form-imgcode .imgcode img { width: 100px; height: 40px; cursor: pointer; }
.passport-form .form-imgcode .imgcode .passport-icon { cursor: pointer; display: none; }
.passport-form .form-mcode .btn-getcode { position: absolute; top: 0; right: 0; bottom: 0; z-index: 20; }
.passport-form .form-mcode .btn-getcode .passport-btn { text-overflow: initial; width: 90px; height: 26px; margin: 7px 7px 0 0; padding: 0; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; }
.passport-form .form-mcode .passport-note { right: 97px; }
.passport-form .passport-sms { position: absolute; top: 100%; left: 0; }
.passport-form .passport-sms a { color: #2d85ca; }
.passport-form .passport-sms a:hover { color: #529dd8; }
.passport-form .passport-sms a.disabled, .passport-form .passport-sms a.disabled:hover { color: #999; }
.passport-form-sign .form-treaty .passport-note span { margin: 0; }
.passport-form-sign .form-treaty .form-cont { line-height: 20px; }
.passport-form-sign .form-treaty .passport-note { top: -3px; line-height: 26px; }
.passport-form-sign .treaty { color: #999; }
.passport-form-sign .treaty:hover { color: #35b558; }
.passport-form-sign .form-sevenday label { float: left; }
.passport-form-sign .form-sevenday .forget-link { float: right; color: #999; }
.passport-form-sign .form-sevenday .forget-link:hover { color: #35b558; }
.passport-forget { padding: 25px 0; background-color: #fff; min-height: 400px; }
.passport-forget .passport-into { padding-top: 95px; }
.passport-forget .passport-into .passport-btn { display: block; width: 250px; height: 54px; margin: 0 auto 50px; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; font-size: 16px; text-align: left; line-height: 52px; }
.passport-forget .passport-into .passport-btn .passport-icon { margin-right: 25px; }
.passport-forget .passport-step { margin: 40px auto 20px; width: 492px; }
.passport-forget .passport-form { width: 300px; margin-right: auto; margin-left: auto; }
.passport-forget .passport-form .form-submit { margin-top: 30px; }
.passport-forget .passport-form .form-item { margin-bottom: 18px; }
.passport-form-email .form-text, .passport-form-phone .form-text, .passport-form-email .form-code-text, .passport-form-phone .form-code-text { width: 500px; margin: 0 -100px; text-align: center; }
.passport-form-email .form-text p, .passport-form-phone .form-text p, .passport-form-email .form-code-text p, .passport-form-phone .form-code-text p { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 20px; }
.passport-form-email .form-text big, .passport-form-phone .form-text big, .passport-form-email .form-code-text big, .passport-form-phone .form-code-text big { font-size: 14px; }
.passport-form-email .form-email, .passport-form-phone .form-email { font-size: 14px; }
.passport-form-email .form-email p, .passport-form-phone .form-email p { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.passport-form-email .form-email em, .passport-form-phone .form-email em { color: #35b558; }
.passport-form-phone .form-code-text { position: relative; width: 100%; margin: 0; text-align: left; }
.passport-form-phone .form-code-text .passport-icon { position: absolute; right: 0; left: 0; margin-left: -60px; }
.passport-form-phone .passport-voice { color: #2d85ca; }
.passport-form-phone .passport-voice:hover { color: #529dd8; }
.mb-25 { margin-bottom: 25px !important; }
@media screen and (max-width: 600px) {
	.passport-wrapper { width: 90%; padding-top: 50px; }
	.passport-wrapper .passport-sign { height: auto; }
	.passport-wrapper .passport-sign .main-form { border-right: 0; width: 100%; height: auto; min-height: auto; }
	.passport-wrapper .aside { clear: both; width: 100%; }
	.passport-wrapper .passport-goto { margin-bottom: 20px; padding-top: 20px; }
}
@media screen and (max-width: 400px) {
	.passport-wrapper .passport-sign .imgcode { clear: both; float: left; margin-top: 4px; }
}
</style>

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
        $(".verifyCode").click();
    }
</script>
</head>

<body>
<div class="passport-wrapper">
    <header id="header" class="passport-header">
        <div id="logo">
            <a><img src="http://www.dapengedu.com/resources/dapeng/img/logo.png"/></a>
        </div>
    </header>

    <div id="container">
        <div class="passport-sign">
            <div class="main-form main-form-sign">
                <div class="passport-tab" id="login-tabs">
                    <div class="tabs">
                        <ul>
                            <li class="active" onClick="$(this).addClass('active').siblings().removeClass('active'); $('#step_2').hide(); $('#step_1').show();">登录</li>
                            {{--<li onClick="$(this).addClass('active').siblings().removeClass('active'); $('#step_1').hide(); $('#step_2').show();">注册</li>--}}
                        </ul>
                    </div>
                    <div class="tabbed">
                        <!-- tab-group -->
                        <div class="tab-group" style="display:block;" id="step_1">
                            <!-- form -->
                            <form class="passport-form passport-form-sign" action="{{ route('admin.auth.login')}}" id="loginForm">
                            {{ csrf_field() }}
                                <div class="form-item">
                                    <div class="form-cont">
                                        <input type="text" id="username" name="username" class="passport-txt xl w-full" tabindex="1" placeholder="手机号" />
                                    </div>
                                </div>
                                <div class="form-item">
                                    <div class="form-cont">
                                        <input type="password" id="password" name="password" class="passport-txt xl w-full" tabindex="2" placeholder="输入6~32位密码" />
                                    </div>
                                </div>
                                <div class="form-item form-imgcode mb-25">
                                    <div class="form-cont">
                                        <div class="layout-inner">
                                            <input type="text" id="verify" name="verify" class="passport-txt xl w-lg" tabindex="3" maxlength="5" placeholder="验证码" autocomplete="off" />
                                        </div>
                                        <div class="imgcode">
                                            <img src="{{ captcha_src() }}" alt='验证码' class="verifyCode" onClick="$('#verify').val(''); this.src='{{ captcha_src() }}?'+Math.random(100,999)"> <i class="passport-icon icon-refresh refreshCode"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-item form-sevenday">
                                    <div class="form-cont clearfix">
                                        <label>
                                            <input type="checkbox" name="remember_me" value="1" class="passport-sevenday" checked />记住用户名
                                        </label>
                                        <!--a class="forget-link">忘记密码</a-->
                                    </div>
                                </div>
                                <div class="form-item">
                                    <div class="form-cont">
                                        <button type="button" class="ajaxSubmit passport-btn passport-btn-def xl w-full" tabindex="4" showloading="true" callback="loginCallback">登录</button>
                                    </div>
                                </div>
                            </form>
                            <!-- form end -->
                        </div>
                        <!-- tab-group end --> 
                        
                        <style>
                        #step_2 .form-item{ margin:15px 0;}
                        </style>
                        
                        <!-- tab-group -->
                        <div class="tab-group" id="step_2" style="display:none;">
                        	<form class="passport-form passport-form-sign" action="{{ route("admin.auth.reg.post") }}" method="post">
                                {{ csrf_field() }}
                                <div class="form-item">
                                    <div class="form-cont">
                                        <input type="text" name="name" class="passport-txt xl w-full" tabindex="1" placeholder="姓名" />
                                    </div>
                                </div>
                                <div class="form-item">
                                    <div class="form-cont">
                                        <input type="text" name="mobile" maxlength="11" minlength="11" class="passport-txt xl w-full" tabindex="1" placeholder="手机" />
                                    </div>
                                </div>
                                <div class="form-item">
                                    <div class="form-cont">
                                        <div class="layout-inner">
                                            <input type="text" name="smscode" class="passport-txt xl w-lg" tabindex="3" maxlength="4" placeholder="验证码" autocomplete="off" />
                                            <button class="btn btn-primary" type="button" onClick="sendSms(this,$('input[name=mobile]').val());" beforeAction="checkSmsTime" style="width:119px; height:40px;">获取验证码</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-item">
                                    <div class="form-cont">
                                        <input type="password" name="password" class="passport-txt xl w-full" tabindex="1" placeholder="密码（必须由字母和数字组成，不少于6位）" />
                                    </div>
                                </div>
                                
                                <div class="form-item">
                                    <div class="form-cont">
                                        <button type="button" class="passport-btn passport-btn-def xl w-full ajaxSubmit" tabindex="4">注册</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- tab-group end -->
                    </div>
                </div>
            </div>
            <div class="aside">
				<!--
                <div class="passport-goto">
                    没有账号? <a onclick="$('.tabs li:eq(1)').trigger('click')">新用户注册</a>
                </div>
                <div class="sendgift">
                    大鹏教育-高品质的设计师在线教育
                </div>
                <div class="passport-third">
                    <header class="hd">
                        <div class="layout-inner">
                            <h3>合作方帐号登录</h3>
                        </div>
                    </header>
                    <div class="links">
                        <a><i class="passport-icon icon-tencent"></i></a> 
                        <a><i class="passport-icon icon-weibo"></i></a> 
                        <a><i class="passport-icon icon-weixin"></i></a> 
                        <a><i class="passport-icon icon-eoe"></i></a>
                    </div>
                </div>
				-->
            </div>
        </div>
    </div>
</div>
</body>
</html>