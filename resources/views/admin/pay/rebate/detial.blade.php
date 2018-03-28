@extends("admin.public.layout")
@section("right_content")
<div class="row dp-member-title-2">
    <div class="btn-back">
        <a href="{{route('admin.pay.rebate.list')}}"><<返回</a>
    </div>
    <h4 class="col-md-4" style="padding-left:0">@if(Request::input('id'))修改@else新增@endif优惠活动</h4>
</div>
<div class="row dp-member-body-2">

    <form class="form-horizontal">
        @if(Request::input('id'))
            <input type="hidden" name="id" value="{{$r->id}}" />
        @endif
        <fieldset>
            <div class="form-group">
                <label class="col-md-2 control-label" for="input01">活动标题：</label>
                <div class="col-md-8 controls">
                    <input type="text" name="title" class="form-control" style="width:200px" value="{{$r->title}}">
                    <p class="help-block"></p>
                </div>
            </div>

            <div class="form-group">
                <!-- Text input-->
                <label class="col-md-2 control-label" for="input01">金额：</label>
                <div class="col-md-8 controls">
                    <input type="number" name="price" value="{{$r->price}}" class="form-control" style="width:200px">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"></label>
                <div class="col-md-8 controls">
                    <button class="btn btn-primary ajaxSubmit" url="{{route('admin.pay.rebate.save')}}" type="button">确认保存</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>
@endsection("right_content")