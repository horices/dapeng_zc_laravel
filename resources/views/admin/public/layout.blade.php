<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>大鹏教育-高品质的设计师在线教育</title>
<link href="/admin/css/bootstrap.min.css" rel="stylesheet">
<link href="/admin/css/font-awesome.min.css" rel="stylesheet">
<link href="/admin/css/plugins/toastr/toastr.min.css" rel="stylesheet">
<link href="/admin/css/animate.css" rel="stylesheet">
<link href="/admin/css/member.css" rel="stylesheet">
<link href="/admin/css/page.css" rel="stylesheet">
<link href="/admin/css/btn.css" rel="stylesheet">
@include("admin.public.js")
<style>
a{ text-decoration:none;}
a:hover{text-decoration:none;}
.dp-member-body-2{ padding:15px 0 0 40px;}

.flag_icon::before{
	position:relative;
	display:block;
	position:absolute;
	left:10px;
}
.flag_icon_new::before{
	content:url(/admin/images/flag_icon_new.gif);
	
}
.flag_icon_active::before{
	content:url(/admin/images/flag_icon_active.gif);
}
.flag_icon_both::before{
	content:url(/admin/images/flag_icon_both.gif);
}
</style>
<script>
$(function(){
	$(".selectSeoer").click(function(){
	    if($(this).attr("isqq") == 1)
            var con = "{:U('Index/selectSeoer',['isqq'=>1])}";
        else
            var con = "{:U('Index/selectSeoer')}";
		layer.open({
			type:2,
			title:"选择推广专员",
			area:['745px','490px'],
			content: con
		});
	});
	$(".selectQQGroup").click(function(){
        if($(this).attr("isqq") == 1){
            con = "{:U('Index/selectQQGroup',['isqq'=>1])}";
            layertitle = "选择QQ群";
        }else{
            con = "{:U('Index/selectQQGroup')}";
            layertitle = "选择课程顾问微信号";
        }

		layer.open({
			type:2,
			title:layertitle,
			area:['745px','490px'],
			content: con
		});
	});
	$(".selectAdviser").click(function(){
		layer.open({
			type:2,
			title:"选择课程顾问",
			area:['745px','490px'],
			content: "{:U('Index/selectAdviser')}"
		});
	});
	$(".datetime").each(function(){
		var settings = {
			lang:'zh',
			format:'Y-m-d H:i:s',
			defaultTime:"00:00",
			//closeOnDateSelect:true,
			timepicker:true,
			step:1
	    };
		if($(this).attr("options")){
			var option = (new Function("return " + $(this).attr("options")))();
			$.extend(settings,option);
		}
		$(this).datetimepicker(settings);
	})
	$(".date").each(function(){
		var settings = {
			lang:'zh',
			format:'Y-m-d',
			closeOnDateSelect:true,
			timepicker:false,
	    };
		if($(this).attr("options")){
			var option = (new Function("return " + $(this).attr("options")))();
			$.extend(settings,option);
		}
		$(this).datetimepicker(settings);
	})
});

function selectSeoerCallback(seoerId,seoerName){
	$("input[name='inviter_id']").val(seoerId);	
	$("input[name='inviter_name']").val(seoerName);	
	layer.closeAll();
}
function selectQQGroupCallback(qq_group){
	$("input[name='qq_group']").val(qq_group);	
	layer.closeAll();
}
function selectAdviserCallback(adviserId,advisername,dapeng_user_mobile){
	$("input[name='leader_id']").val(adviserId);	
	$("input[name='leader_name']").val(advisername);
	$("input[name='dapeng_user_mobile']").val(dapeng_user_mobile);
	layer.closeAll();
}


//选中默认菜单 

$(function(){
	var action = "{$subnavAction}" || "{$Think.get.subnavAction|default=ACTION_NAME}";
	$("#subnav").find("a[flag='"+action+"']").addClass("cur");
	
	<eq name="isShowUpdateLog" value="1">
	layer.open({
		type:2,
		title:"{:date('Y-m-d')}更新日志",
		area:['745px','490px'],
		content: "{:U('showUpdateLog')}"
	});
	</eq>
});

</script>
</head>

<body class="" id="bodyColor" style="background-color:#f0f0f0;">
<div class="navbar dp-nav">
    <div class="container">
        <div class="container-gap">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                <a class="navbar-brand logo" href="#"> <img src="/admin/images/member_logo.png" alt="大鹏教育-高品质的设计师在线教育" /> </a>
            </div>
            <div class="navbar-collapse collapse dp-collapse">
                
                <ul class="nav navbar-nav navbar-right dp-navbar">
                	<!--
                    <li><a href="/Member/Index/index">推广中心</a></li>
                    <li style="color: #7E7E8C;font-size: 12px;">|</li>
                    <li><a href="/Member/Trade/index" style="padding-right:10px;">交易中心</a></li>
                    <li class="visible-lt-ie8"><a href="/Member/Order/index">工单管理</a></li>
                    -->
                    <li class="dropdown hidden-lt-ie8"> 
                    	<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="padding-left:0px;padding-right:0px;">{{Session::get("userInfo.name")}} ({{Session::get("userInfo.grade")}})<i class="glyphicon glyphicon-user"></i><span class="new-message-count"></span> </a>
                        <ul class="dp-dropdown-menu dropdown-menu">
                            <div class="border-top"></div>
                            <li><a href="{:U('accounts')}">个人中心</a></li>
                            <li class="divider"></li>
                            <li><a href="{:U('Portal/logout')}" class="">退出登陆</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!--/.navbar-collapse -->
        </div>
    </div>
</div>
<div id="content-container" class="container">
    <div class="row row-2-10">
        <div class="col-md-2 nav-left">
        @include("admin.public.nav")
        </div>
        <div class="col-md-10 dp-member-content" style="padding:30px 30px;">
        @section("right_content")
        @show
        </div>
    </div>
</div>
<div class="dp-footer">
    <div class="wrap">
        <div class="container">
            <ul class="nav navbar-nav">
                <li><i class="img1"></i><span>在线直播授课</span></li>
                <li><i class="img2"></i><span>全选在线教法</span></li>
                <li><i class="img3"></i><span>一线设计讲师</span></li>
                <li><i class="img4"></i><span>创新课程体系</span></li>
            </ul>
            <div class="pull-right nav-r">
                <p class="consult">客服咨询：400 668 1033</p>
                <p class="time">周一至周日9:00-18:00 /仅收市话费</p>
            </div>
        </div>
    </div>
    <div class="container main">
        <div class="row more">
            <div class="col-sm-2 float-l">
                <h3>我想学</h3>
                <ul>
                    <li> <a target="_blank" href="http://www.dapengjiaoyu.com/course/ih7eniy2">设计功能精通班</a> </li>
                    <li> <a target="_blank" href="http://www.dapengjiaoyu.com/course/ih7bjlfu">设计师高级精英班</a> </li>
                    <li> <a target="_blank" href="http://www.dapengjiaoyu.com/course/ih7c6ip8">设计师就业直通班</a> </li>
                </ul>
            </div>
            <div class="col-sm-2 float-l">
                <h3>我想了解</h3>
                <ul>
                    <li> <a target="_blank" href="http://www.dapengjiaoyu.com/course/explore/?type=OPEN">朋友说</a> </li>
                    <li> <a target="_blank" href="http://www.dapengjiaoyu.com/course/explore/?type=OPEN">大鹏炫技</a> </li>
                    <li> <a target="_blank" href="http://www.dapengjiaoyu.com/course/explore/?type=OPEN">精品课程</a> </li>
                </ul>
            </div>
            <div class="col-sm-2 float-l">
                <h3>获得证书</h3>
                <ul>
                    <li> <a target="_blank" href="http://www.dapengjiaoyu.com/page/certificate">ADOBE认证</a> </li>
                    <li> <a target="_blank" href="http://www.dapengjiaoyu.com/page/certificate">工信部证书</a> </li>
                    <li> <a target="_blank" href="http://www.dapengjiaoyu.com/page/certificate">CCID认证</a> </li>
                </ul>
                <!--底部导航 健康产品-->
            </div>
            <div class="col-sm-2 float-l">
                <h3>了解大鹏</h3>
                <ul>
                    <li> <a target="_blank" href="http://www.dapengjiaoyu.com/page/about">大鹏简介</a> </li>
                    <li> <a target="_blank" href="http://www.dapengjiaoyu.com/page/about">媒体报道</a> </li>
                    <li> <a href="/#praise">学员评价</a> </li>
                </ul>
            </div>
            <div class="col-sm-2 float-l">
                <h3>关注大鹏</h3>
                <ul>
                    <li> <a target="_blank" href="#">微信公众号</a> </li>
                    <li> <a target="_blank" href="#">微博#大鹏#</a> </li>
                    <li> <a target="_blank" href="#">优酷视频</a> </li>
                </ul>
            </div>
            <div class="qr-group">
                <div>
                    <i class="app-qr"></i><span class="">下载手机APP</span>
                </div>
                <div>
                    <i class="wx-qr"></i><span class="">关注微信公号</span>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="f-wrap p-wrap">
            <ul class="clearfix">
                <li><a href="/page/about#joinus"  target="_blank"  >加入我们</a></li>
                <li><a href="/page/about#contactus"  >联系我们</a></li>
                <li><a href="/page/help"  target="_blank"  >帮助中心</a></li>
            </ul>
            <div class="copyright">
                Copyright &copy; 2013-2015 大鹏教育　|　京ICP备14053618号
            </div>
        </div>
    </div>
</div>


</body>
</html>