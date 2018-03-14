@extends("admin.public.layout")

@section("right_content")
    <div class="row search-row" style="padding:9px 0 15px 15px;">
        <form class="form-inline" style="width:920px; height:auto;" method="get">
            <div class="" style="float:left; height:30px;">
                <select name="searchType" class="form-qq" style="width:80px; padding:3px;">
                    <option value="name">姓名</option>
                    <option value="qq">QQ号</option>
                    <option value="mobile">手机号</option>
                </select>
                <input type="text" name="keywords" class="form-control"
                       style="height:30px; margin-bottom:3px; width:120px;" id="name" placeholder=""
                       value="">
            </div>
            <div class="form-tm" style="float:left; margin-left:10px;">
                <label>
                    <select name="dateType" style="padding:3px;">
                        <option value="create_time">私聊时间</option>
                    </select>&nbsp;</label>
                <input type="text" name="startdate" class="form-control datetime" id="startdate" style="height:30px;"
                       placeholder="开始时间" value="">
                <label>&nbsp;至&nbsp;</label>
                <input type="text" name="enddate" class="form-control datetime" id="enddate" style="height:30px;"
                       placeholder="结束时间" value="">
            </div>
            {{--<div class="fleft" style="height:35px;overflow:hidden;line-height:35px;"><a
                        href="{:U('',mergeArray($_GET,['startdate'=>date('Y-m-d 00:00'),'enddate'=>date('Y-m-d 23:59')]))}">今日</a>
            </div>--}}
            <div class="form-but" style="float:left;  color:#fff;  width:122px; margin-top:3px;  "><span class="but-ss" style=" height:auto;text-align:center; line-height:25px; "><a class="common-button combg1 linkSubmit" href="{:U()}" style="height:30px;line-height:20px;">搜索</a></span></div>
            <div class="form-but" style=" color:#fff;  width:122px; margin-top:3px;  "><span class="but-ss fleft" style=" height:auto;text-align:center; line-height:25px; "><!--  <a class="common-button combg4 linkSubmit" href="{:U('exportAdviserStatistics')}" style="height:30px;line-height:20px;">导出</a> --></span></div>
    </div>
    </form>
    <div id="w0" class="grid-view">
        <table class="table">
            <thead class="thead" style=" font-size:14px; ">
            {{--<tr>
                <th colspan="5">总计:</th>
            </tr>--}}
            <tr>
                <th>ID</th>
                <th>课程顾问</th>
                <th>私聊量</th>
                <th>最近私聊时间</th>
                <th>销售统计</th>
            </tr>
            </thead>
            <tbody class="tbody" style="color:#333;">
            @foreach($list as $followInfo)
                <tr class="">
                    <td>{{ $followInfo->uid }}</td>
                    <td>{{ $followInfo->name }}</td>
                    <td>{{ $followInfo->num }}</td>
                    <td>

                    </td>
                    <td>
                        <a href="{{ route("admin.roster.follow.list",['user_id'=>$followInfo->uid]) }}">点击查看</a>
                    </td>
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
    </div>
    </div>
    </div>
    </div>
@endsection