@extends("admin.public.layout")
@section("right_content")
<div class="row search-row" style="padding:9px 0 15px 15px;">
    <div class="row dp-member-title-2">
        <div class="btn-back">
            <a href="{{route('admin.pay.package.list')}}">&lt;&lt;返回</a>
        </div>
        <h5 class="col-md-2" style="padding-left:0"> 学院名称：{{$package->school_text}}</h5>
        <h5 class="col-md-4" style="padding-left:0">套餐名称：{{$package->title}}</h5>
    </div>
    <form class="form-inline" role="form">
        <input type="hidden" name="package_id" value="{{Request::get('package_id')}}" />
        <div class="form-group">
            <a class="common-button combg4" href="{{route('admin.pay.rebate.add',['package_id'=>Request::input('package_id')])}}">新增活动</a>
        </div>
        <div class="form-group">
            <input type="text" name="title" class="form-control" placeholder="活动标题" value="{{Request::input('title')}}" style="width: 110px;"/>
            <a class="common-button combg1 linkSubmit">搜索</a>
        </div>
    </form>
</div>

<div id="w0" class="grid-view">
    <table class="table">
        <thead>
        <tr>
            <th>序号</th>
            <th>优惠活动</th>
            <th>赠送课程</th>
            <th>优惠金额</th>
            <th>活动时间</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @if(count($list)>0)
            @foreach($list as $k=>$v)
                <tr>
                    <td>{{$v->id+1}}</td>
                    <td>{{$v->title}}</td>
                    <td>
                        @if($v->course_give_data)
                            @foreach($v->course_give_data as $l)
                                {{$l or ''}}<br/>
                            @endforeach
                        @endif
                    </td>
                    <td>{{$v->price_max}}</td>
                    <td>{{$v->start_time_text}}-{{$v->end_time_text}}</td>
                    <td>{{$v->create_time}}</td>
                    <td>
                        <a href="{{route('admin.pay.rebate.edit',['id'=>$v->id,'package_id'=>Request::get('package_id')])}}">编辑</a>|
                        <a url="{{route('admin.pay.rebate.delete',['id'=>$v->id])}}" warning="确认删除？" class="ajaxLink">删除</a>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="12" ><div class="pagenav"> <ul>{{ $list->appends(Request::input())->links() }}</ul></div></td>
            </tr>
            @else
            <tr>
                <td colspan="12">暂无信息</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
@endsection("right_content")