<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
	<link type="text/css" rel="stylesheet" href="/admin/css/page.css" />
	<link type="text/css" rel="stylesheet" href="/admin/css/btn.css" />
	@include("admin.public.js")
<style>
a{ text-decoration:none;}
a:link{ color:#fff;}
a:hover{ color:#fff;}
a:visited{ color:#fff;}
.tc {

	background:#fff;
    height:450px;
    width:745px;
    -moz-border-radius:20px;
    -webkit-border-radius:20px;
    border-radius:15px;}
.top{ float:left; width:100%; height:auto;}
.form-group{ float:left; padding:30px; width:20%; height:10px;}
.main-top{  float:left;
	width:100px;
	height:30px;}


.form-control {
	
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
    color: #555;
    display: block;
    font-size: 14px;
    height: 20px;
    line-height: 1.42857;
    padding: 5px 12px;
    transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;
	overflow:hidden;
    
}


.form-control:focus {
    border-color: #66afe9;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(102, 175, 233, 0.6);
    outline: 0 none;
}

.table-striped{ float:left; width:705px; margin:0px 0 0 20px; }
.table-striped table{border-collapse:collapse;  font-size:14px;  color:#333; border:1px solid #fff; width:705px;text-align:center; }
.table-striped  th{  border-bottom:2px solid #ddd; border-right:1px solid #fff; padding:8px;  }
.table-striped tr td{ border-bottom:1px solid #ddd; border-right:1px solid #fff;  padding:11px; }

.bg2{background:#00cc33; }

.main-bot{ float:left; clear:both;  width:705px; height:auto; margin:18px 0 0 20px;}

.main-left{  float:left;
	width:100px;
	height:30px;
	margin:4px 0 0 10px;}
.main-left a:link{ color:#fff;}
.main-left a:hover{ color:#fff;}


</style>
</head>

<body style=" margin:0; padding:0; font-family:'Microsoft YaHei';">
<div class="tc">
<div class="top">
<form role="form">
 <input type="hidden" name="page" value="1" />
<div class="form-group">
	<input type="text" class="form-control" name="name" placeholder="用户名称" value="{{ Request::input("name") }}">
</div>
<div class="form-group main-top">
	<a class="common-button dblock combg1 linkSubmit" >搜索</a>
</div>
</form>



</div>
<div class="table-striped">
<table border="1" >
	<thead>
		<tr>
			<th style="width:10px;">选择</th>
			<th style="width:30%">姓名</th>
			<th style="width:30%">QQ号</th>
            <th style="width:15%;">操作</th>
		</tr>
	</thead>
	<tbody>
		@foreach($list as $user)
		<tr onclick="$(this).find('input').prop('checked',true);">
			<td>
			<input name="uid" value="{{ $user->uid }}" type="radio" id="square-checkbox-1" class="square-checkbox-1" checked="checked"></td>
			<td>{{ $user->name }}</td>
			<td>{{ $user->qq }}</td>
            <td>
			<a class="common-button combg2" onclick='selectSeoer({!! $user->toJson() !!});'>选择</a>
			</td>
		</tr>
		@endforeach
	</tbody>
</table>
</div>
<div class="main-bot">
{{-- <div class="main-left">
<a class="common-button fleft dblock combg1" onclick="selectSeoer();">确认</a>
</div> --}}
<div class="pagenav">
    {{ $list->appends(Request::input())->links() }}
</div>
</div>
</div>
<script>
function selectSeoer(user){
	parent.selectUserCallback(user);
	parent.layer.closeAll();
}
</script>
</body>