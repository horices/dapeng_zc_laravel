<style>
.qq_course_list{
	list-style-type:none;
	overflow:hidden;
	padding:0px;
}
.qq_course_list li{
	overflow:hidden;
	margin:5px 10px;
}
.course_name{
	float:left;
	ovflow:hidden;
}
.course_addtime{
	float:right;
	margin-left:10px;
	width:140px;
}
</style>
<ul class="qq_course_list">
	<li>
	  <div class="course_name">状态</div>  
	  <div class="course_addtime">时间</div>
	  </li>
	@foreach($groupLogList as $log)
	<li>
		<div class="course_name">{{ $log->group_status_text }}</div>
	  <div class="course_addtime">{{ $log->addtime_full_text }}</div>
	  </li>
	@endforeach
</ul>