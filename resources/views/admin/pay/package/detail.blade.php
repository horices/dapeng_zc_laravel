@extends("admin.public.layout")
@section("right_content")
<div class="col-md-10 dp-member-content" style="padding:30px 30px;">
    <div class="row dp-member-title-2">
        <div class="btn-back">
            <a href="{{route('admin.pay.package.list')}}"><<返回</a>
        </div>
        <h4 class="col-md-4" style="padding-left:0">课程套餐</h4>
    </div>
    <div class="row dp-member-body-2">
        <form class="form-horizontal">
            @if($r->id)
                <input type="hidden" name="id" value="{{$r->id}}" />
            @endif
            <fieldset>
                <div class="form-group">
                    <label class="col-md-2 control-label" for="input01">套餐名称：</label>
                    <div class="col-md-8 controls">
                        <input type="text" name="title" class="form-control" style="width:200px" value="{{$r->title}}">
                        <p class="help-block"></p>
                    </div>
                </div>

                <div class="form-group">
                    <!-- Text input-->
                    <label class="col-md-2 control-label" for="input01">价值金额：</label>
                    <div class="col-md-8 controls">
                        <input type="number" name="price" value="{{$r->price}}" class="form-control" style="width:200px">
                        <p class="help-block"></p>
                    </div>
                </div>
                <div class="form-group">
                    <!-- Text input-->
                    <label class="col-md-2 control-label" for="input01">套餐类型</label>
                    <div class="col-md-8 controls">
                        <input type="radio" name="type" value="0" @if($r->type == 0)  checked @endif />主套餐&nbsp;&nbsp;
                        <input type="radio" name="type" value="1"  @if($r->type == 1)  checked @endif />副套餐
                        <p class="help-block"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"></label>
                    <div class="col-md-8 controls">
                        <button class="btn btn-primary ajaxSubmit" url="{{route('admin.pay.package.save')}}" type="button">确认保存</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
@endsection