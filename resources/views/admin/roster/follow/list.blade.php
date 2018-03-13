@extends("admin.public.layout")
@section("right_content")
<div class="row search-row" style="padding:9px 0 15px 15px;">
    <form class="form-inline" style="width:920px; height:auto;" method="get">
        <div class="form-tm" style="float:left; margin-left:10px;">
            <input type="text" name="qq" class="form-control" style="height:30px;width:120px;" placeholder="搜索QQ号"
                   value="{$Think.get.qq}">
            <label>
                <select name="deep_level" style="padding:3px;">
                    <option value="">私聊深度</option>
                    @foreach($rosterDeepLevel as $key =>$level)
                        <option value="{{ $key }}" class="option_group" @if(Request::input('deep_level') == $key) selected @endif>{{ $level }}</option>
                    @endforeach
                </select>&nbsp;
            </label>
            <label>
                <select name="intention" style="padding:3px;">
                    <option value="">报名意向</option>
                    @foreach($rosterIntention as $key => $intention)
                        <option value="{{ $key }}" class="option_group" @if(Request::input('intention') == $key) selected @endif>{{ $intention }}</option>
                    @endforeach
                </select>&nbsp;
            </label>

            <label>
                <select name="dateType" style="padding:3px;">
                    <option value="create_time">私聊时间</option>
                </select>&nbsp;</label>
            <input type="text" name="startdate" class="form-control datetime" id="startdate" style="height:30px;"
                   placeholder="开始时间" value="{$Think.get.startdate}">
            <label>&nbsp;至&nbsp;</label>
            <input type="text" name="enddate" class="form-control datetime" id="enddate" style="height:30px;"
                   placeholder="结束时间" value="{$Think.get.enddate}">
        </div>
        <div class="fleft" style="height:35px;overflow:hidden;line-height:35px;"><a
                    href="{:U('',mergeArray($_GET,['startdate'=>date('Y-m-d 00:00'),'enddate'=>date('Y-m-d 23:59')]))}">今日</a>
        </div>
        <div class="form-but" style="float:left;  color:#fff;  width:122px; margin-top:3px;  ">
<span class="but-ss" style=" height:auto;text-align:center; line-height:25px; ">
<a class="common-button combg1 linkSubmit" href="{:U('',mergeArray($_GET,['p'=>1,'startdate'=>'','enddate'=>'']))}"
   data="" style="height:30px;line-height:20px;">搜索</a></span></div>
        <div class="form-but" style=" color:#fff;  width:122px; margin-top:3px;  ">
<span class="but-ss fleft" style=" height:auto;text-align:center; line-height:25px; ">
<!-- <a class="common-button combg4 linkSubmit" href="{:U('exportAdviserStatistics')}" style="height:30px;line-height:20px;">导出</a> --></span>
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
            <th style="width:110px;">QQ号</th>
            <th style="width:60px;">群昵称</th>
            <th style="width:85px;">课程顾问</th>
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
                <td>{{ $followInfo->id }}</td>
                <td>{{ $followInfo->qq }}</td>
                <td>{{ $followInfo->roster->group->group_name}}</td>
                <td>{{ $followInfo->adviser_name }}</td>
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
            <td colspan="10">
                <div class="pagenav">
                    <ul>{{ $list->appends(Request::input())->links() }}</ul>
                </div>
            </td>
        </tr>
        @if(!$list->count())
            <tr>
                <td colspan="8">暂无信息</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
@endsection