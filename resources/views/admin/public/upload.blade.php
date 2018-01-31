<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
</head>
<body>
<form name="" method="post" action="" enctype="multipart/form-data" >
{{ csrf_field() }}
@foreach(Request::input() as $k=>$v)
    @if($v)
    	<input type="hidden" name="{{$k}}" value="{{$v}}" />
	@endif
@endforeach
<input type="file" name="upload" style="height:25px; background-color:#EBEBEB; border:1px solid black;" onMouseOver ="this.style.backgroundColor='#F0F0F0'" onMouseOut ="this.style.backgroundColor='#FAFAFA'" >
<input type="submit" name="Submit" value="上传文件" style="height:25px; background-color:#EBEBEB; border:1 solid black;" onMouseOver ="this.style.backgroundColor='#F0F0F0'" onMouseOut ="this.style.backgroundColor='#FAFAFA'" >
<!-- <br />
(默认回调父框架JS中的uploadCallback方法,修改请带 callback=方法名) -->
</form>
</body>
</html>