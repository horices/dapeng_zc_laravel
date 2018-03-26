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
	@foreach($rosterCourse as $k=>$course)
	<li><div class="course_name">{{ $k+1 }}. {{ $course->course_name }}({{ $course->course_type_text }})</div>    <div class="course_addtime">{{ $course->addtime_text }}</div></li>
	@endforeach
</ul>