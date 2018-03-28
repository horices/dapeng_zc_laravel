@extends("admin.public.layout")
@section("right_content")
<div class="row search-row" style="padding:9px 0 15px 15px;">
    <form class="form-inline" role="form">
        <div class="form-group">
            <a class="common-button combg4" href="{{route('admin.pay.rebate.add')}}">新增活动</a>
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
            <th>标题</th>
            <th>金额</th>
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
                    <td>{{$v->price}}</td>
                    <td>{{$v->create_time}}</td>
                    <td>
                        <a href="{{route('admin.pay.rebate.edit',['id'=>$v->id])}}">修改</a>|
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