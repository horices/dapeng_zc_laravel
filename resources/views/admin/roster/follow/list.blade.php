@extends("admin.public.layout")
@section("right_content")
<div class="row search-row" style="padding:9px 0 15px 15px;">

    <form class="form-inline" role="form">
        <input type="hidden" name="user_id" value="{{ Request::get("user_id") }}" />
        <div class="form-group">
            <input type="text" name="roster_no" class="form-control" style="width: 120px;" placeholder="搜索量号码" value="{{ Request::input("roster_no") }}">
        </div>
        <div class="form-group">
            <label class="control-label">私聊深度</label>
            <select name="deep_level" style="padding:3px;" class="form-control">
                <option value="">私聊深度</option>
                @foreach($rosterDeepLevel as $key =>$level)
                    <option value="{{ $key }}" class="option_group" @if(Request::input('deep_level') == $key) selected @endif>{{ $level }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>报名意向</label>
            <select name="intention" style="padding:3px;" class="form-control">
                <option value="">报名意向</option>
                @foreach($rosterIntention as $key => $intention)
                    <option value="{{ $key }}" class="option_group" @if(Request::input('intention') == $key) selected @endif>{{ $intention }}</option>
                @endforeach
            </select>&nbsp;
        </div>
        <div class="form-group">
            <label>私聊时间：</label>
            <input type="text" name="startdate" class="form-control select_date" style="width:140px;" value="{{ Request::input("startdate") }}" callback="selectDateCallback" /> 至
            <input type="text" name="enddate" class="form-control select_date" style="width:140px;" value="{{ Request::input("enddate") }}" callback="selectDateCallback" />
        </div>

        {{--            <div class="form-group">
                        <input type="text" name="seoer_name" class="form-control" style="width:140px;" value="{{ Request::input("seoer_name") }}" placeholder="推广专员名称" />
                        <input type="text"  name="adviser_name" class="form-control" style="width:140px;" value="{{ Request::input("adviser_name") }}" placeholder="课程顾问名称" />
                    </div>--}}
        <div class="form-group">
            <a href="" class="common-button combg2 linkSubmit" data="{page:1}">搜索</a>
        </div>
    </form>
</div>

<div class="grid-view">
    <table class="table">
        <thead class="thead" style=" font-size:14px; ">
        <!-- <tr>
            <th colspan="8">总计:1560</th>
        </tr> -->
        <tr>
            <th style="width:50px;">ID</th>
            <th style="width:110px;">类型</th>
            <th style="width:110px;">号码</th>
            <th style="width:60px;">群昵称</th>
            <th style="width:60px;">群号</th>
            <th style="width:85px;">课程顾问</th>
            <th style="width:85px;" class="linkSubmit" formTarget="#searchForm" data="{order:'count',direction:{{ (Request::get('direction')+1)%2 }} }">私聊次数</th>
            <th style="width:75px;">私聊深度</th>
            <th style="width:75px;">报名意向</th>
            <th>备注</th>
            <th style="width:145px;">私聊时间</th>
            <th style="width:60px;">添加人</th>
            <!--  <th>详情</th> -->
        </tr>
        </thead>
        <tbody class="tbody" style="color:#333;">
        @foreach($list as $followInfo)
            <tr class="">
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $followInfo->roster->roster_type_text }}</td>
                <td>{{ $followInfo->roster->roster_no }}</td>
                <td>{{ $followInfo->roster->group->group_name}}</td>
                <td>{{ $followInfo->roster->group->qq_group }}</td>
                <td>{{ $followInfo->adviser_name }}</td>
                <td>{{ $followInfo->count }}</td>
                <td class="showTip" src="{:U('getChart?type=deep_level&qq='.$l['qq'])}">
                    {{ $followInfo->deep_level_text }}
                </td>
                <td class="showTip" src="{:U('getChart?type=intention&qq='.$l['qq'])}">
                    {{ $followInfo->intention_text }}
                </td>
                <td>{{ $followInfo->comment }}</td>
                <td>{{ $followInfo->create_time_text }}</td>
                <td>{{ $followInfo->creator->name }}</td>
                <!-- <td><a href="{:U('my_data?subnavAction=adviser_statistics&adviser_id='.$l['uid'],mergeArray($_GET,array('p'=>1)))}">查看</a></td> -->
            </tr>
        @endforeach
        <tr>
            <td colspan="12">
                <div class="pagenav">
                    <ul>{{ $list->appends(Request::input())->links() }}</ul>
                </div>
            </td>
        </tr>
        @if(!$list->count())
            <tr>
                <td colspan="12">暂无信息</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
@endsection