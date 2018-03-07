<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link type="text/css" rel="stylesheet" href="/admin/css/page.css" />
<link type="text/css" rel="stylesheet" href="/admin/css/btn.css" />
@include("admin.public.js")
<style>
.tc {

	background:#fff;
    height:450px;
    width:745px;
    -moz-border-radius:20px;
    -webkit-border-radius:20px;
    border-radius:15px;}
.top{ float:left; width:100%; height:auto;}
.form-group{ float:left; padding:30px 10px; width:auto; height:auto;}
.main-top{  float:left;
margin-left:0px;
	width:auto;
	height:auto;}


.form-control {
	
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
    color: #555;
    display: block;
    font-size: 14px;
    height: auto;
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
.table-striped table{border-collapse:collapse; font-size:14px;  color:#333; border:1px solid #fff; width:705px; text-align:center;}
.table-striped  th{  border-bottom:2px solid #ddd; border-right:1px solid #fff; padding:8px; }
.table-striped tr td{ border-bottom:1px solid #ddd; border-right:1px solid #fff;  padding:11px;  }

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

<body style=" margin:0; padding:0;  font-family:Microsoft YaHei;">
<div class="tc">
<div class="top">
<form role="form">
<input type="hidden" name="p" value="1" />
<div class="form-group">
<input type="text" class="form-control" name="group" 
 placeholder="群号" value="{{ Request::input('group') }}">
</div>
<div class="form-group">
	<input type="text" class="form-control" name="group_name"
		   placeholder="群昵称" value="{{ Request::input('group_name') }}">
</div>
<div class="form-group">
	<input type="text" class="form-control" name="adviser_name"
		   placeholder="课程顾问名称" value="{{ Request::input('adviser_name') }}">
</div>
<div class="form-group main-top">
<a class="common-button dblock combg1 linkSubmit" data="{p:1}"  style="padding:2px 8px;text-decoration:none;">搜索</a>
</div>
</form>



</div>
<div class="table-striped">
<table border="1" >
	<thead>
		<tr>
			{{--<th style="width:5%">选择</th>--}}
			<th style="width:25%">群号</th>
			<th style="width:25%">群昵称</th>
			<th style="width:10%">开启状态</th>
			<th style="width:20%">负责人</th>
            <th style="width:15%; text-align:center;">操作</th>
		</tr>
	</thead>
	<tbody>
	@foreach($list as $group)
		<tr>
			{{--<td>
				<input tabindex="9" name="qq_group" value="{$l.qq_group}" type="radio" id="square-checkbox-1" class="square-checkbox-1" checked="checked">
			</td>--}}
			<td>{{ $group->qq_group }}</td>
			<td>{{ $group->group_name }}</td>
			<td>{{ $group->is_open_text }}</td>
			<td>{{ $group->user->name }}</td>
			<td>
				<a class="common-button combg2" onclick="selectQQGroup({{ $group->toJson() }});">选择</a>
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
</div>
<div class="main-bot">
{{--<div class="main-left">
<a class="common-button fleft dblock combg1" onclick="selectQQGroup();">确认</a>
</div>--}}
<div class="pagenav">
  <ul>
    {{ $list->appends(Request::input())->links() }}
  </ul>
</div>
</div>
</div>
<script>
function selectQQGroup(qq_group){
	parent.selectGroupCallback(qq_group);
	parent.CustomDialog.closeDialog();
}
</script>
</body>