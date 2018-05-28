@extends("admin.public.layout")
@section("script")
 <script>
function hasRegistration(data){
    if(data.code < 0){
        layer.msg(data.msg,{icon:2});
        return ;
    }
    if(data.code == 0){
        layer.msg(data.msg,{icon:0});
        return ;
    }
    if(data.code >0 ){
        layer.msg(data.msg,{icon:1});
    }
    $("input[name='mobile']").val($("#mobile").val());
    $("#suForm")[0].submit();
}
</script>
@endsection
@section("right_content")
  <div id="content-container" class="container ">
   <div class="row row-2-10 ">
    <form class="col-md-10" style="padding: 30px; ">
        <div class="main_left pull-left" style="border:none;">
            <div class="row dp-member-title-2 " style="margin-left: -3px; ">
                <h4 class="col-md-4 " style="padding-left: 0px; font-weight: 700; "> 报名信息： </h4>
            </div>
            <div class="div_input_one ">
                <label for="input01 " class="col-md-3 control-label "> 学员手机： </label>
                <input type="text " id="mobile" name="enroll[mobile]" value="" maxlength="11 " class="form-control pull-left " />
                <a data="{mobile:$( '#mobile').val(),client_submit: 'PC'} " url="{{ route('admin.registration.has-registration') }}" callback="hasRegistration" class="common-button dblock fleft combg2 ml5 ajaxSubmit" >下一步</a>
            </div>
        </div>
    </form>
    <form action="" method="get" id="suForm">
     <input type="hidden" name="mobile" value="" />
    </form>
   </div>

  </div>
@endsection