@extends("admin.public.layout")
@section("right_content")
<div class="row search-row" style="padding:9px 0 15px 15px;">
    <form class="form-inline" role="form">
        <div class="form-group">
            <a class="common-button combg4" href="{{route('admin.pay.package.add')}}">新增套餐</a>
        </div>
        <div class="form-group">
            <select name="school_id" class="form-control">
                <option value="">学院名称</option>
                <option value="SJ" @if(Request::input('school_id') == 'SJ') selected @endif>设计学院</option>
                <option value="MS" @if(Request::input('school_id') == 'MS') selected @endif>美术学院</option>
            </select>
            <input type="text" name="title" class="form-control" placeholder="套餐标题" value="{{Request::input('title')}}" style="width: 110px;"/>
            <a class="common-button combg1 linkSubmit">搜索</a>
        </div>
    </form>
</div>

<div id="w0" class="grid-view">
    <table class="table">
        <thead>
        <tr>
            <th>序号</th>
            <th>学院名称</th>
            <th>套餐名称</th>
            <th>附加课程</th>
            <th>套餐金额</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
            @if(count($list)>0)
                @foreach($list as $k=>$v)
                <tr style="<eq name='v.is_old' value='1'>opacity:0.5;</eq>">
                    <td>{{$k+1}}</td>
                    <td>{{$v->school_text}}</td>
                    <td>{{$v->title}}</td>
                    <td>
                        @if(isset($v->course_attach_data))
                            @foreach($v->course_attach_data as $l)
                                {{$l['title'] or ''}}<br/>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        {{$v->price}}
                    </td>
                    <td>{{$v->create_time}}</td>
                    <td>
                        <a href="{{route('admin.pay.rebate.list',['package_id'=>$v->id])}}">活动详情</a>|
                        <a href="{{route('admin.pay.package.edit',['id'=>$v->id])}}">编辑</a>|
                        <a url="{{route('admin.pay.package.delete')}}" warning = '确认删除？' data="{id:{{$v->id}}}" class="ajaxLink">删除</a>
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
