<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>大鹏教育-高品质的设计师在线教育</title>
<link href="/admin/css/bootstrap.min.css" rel="stylesheet">
<link href="/admin/css/font-awesome.min.css" rel="stylesheet">
<link href="/admin/css/plugins/toastr/toastr.min.css" rel="stylesheet">
<link href="/admin/css/animate.css" rel="stylesheet">
<link href="/admin/css/member.css" rel="stylesheet">
<link href="/admin/css/page.css" rel="stylesheet">
<link href="/admin/css/btn.css" rel="stylesheet">
<link href="/admin/css/status.css" rel="stylesheet">
<link href="/admin/css/style.css" rel="stylesheet">
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
var currentUploadObj={};
function openUpload(obj){
	var url = "{{ route('admin.upload') }}?1";
	obj.attr("href","javascript:;");
	if(obj.attr("uploadTarget")){
		url+='&target='+encodeURIComponent(obj.attr("uploadTarget"));
	}
	if(obj.attr("callback")){
		url+='&callback='+encodeURIComponent(obj.attr("callback"));
	}
	if(obj.attr('data')){
		json = eval('('+(obj.attr('data'))+')');
		for(var i in json){
			str = "json."+i;
			url+='&'+i+"="+eval(str);
		}
	}
	currentUploadObj = obj;
	var title=obj.attr('title')?obj.attr('title'):'图片上传';
	layer.open({
		title:title,
		type:2,
		area:['400px','80px'],
		content:url
	});
}
$(function(){
    $(".select_seoer").click(function(){
        layer.open({
            type:2,
            title:"选择推广专员",
            area:['750px','500px'],
            content:"{{ route("admin.public.select_seoer") }}"
        });
    });
    $(".select_adviser").click(function(){
        layer.open({
            type:2,
            title:"选择课程顾问",
            area:['750px','500px'],
            content:"{{ route("admin.public.select_adviser") }}"
        });
    });
    $(".select_group").click(function(){
        $(this).attr("group_type") || $(this).attr("group_type","");
        var url = Utils.createUrl({'type':$(this).attr("group_type")},"{{ route("admin.public.select_group") }}");
        layer.open({
            type:2,
            title:"选择群号",
            area:['750px','500px'],
            content:url
        });
    });
    $(".select_date").each(function(){
        var _this = $(this);
        laydate.render({
            elem: _this[0],
            type:'datetime',
            done: function(value, date, endDate){
                Utils.getfn(_this.attr('callback'))(_this,value);
            }
            //theme:'grid',
        });
    });
    $(".dropdown-toggle").click(function(){
        $(this).next().toggle();
    });
});

</script>
@section("script")
@show
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
                    <li class="dropdown hidden-lt-ie8">
                    	<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" style="padding-left:0px;padding-right:0px;">{{Session::get("userInfo.name")}} ({{Session::get("userInfo.grade_text")}})<i class="glyphicon glyphicon-user"></i><span class="new-message-count"></span> </a>
                        <ul class="dp-dropdown-menu dropdown-menu">
                            <div class="border-top"></div>
                            <li><a href="{{ route("admin.public.account") }}">个人中心</a></li>
                            <li class="divider"></li>
                            <li><a href="{{ route("admin.auth.logout") }}" class="">退出登陆</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ \App\Utils\Util::getWebSiteConfig("PC_URL") }}/signin" class="" target="_blank">橱窗管理系统登录</a></li>
                </ul>
            </div>
            <!--/.navbar-collapse -->
        </div>
    </div>
</div>
<div id="content-container" class="container main_container">
    <div class="row row-2-10">
        <div class="col-md-2 nav-left">
        @include("admin.public.nav")
        </div>
        <div class="col-md-10 dp-member-content">
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