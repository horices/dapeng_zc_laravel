@extends("admin.public.layout")
@section("style")
    <style>
        .act_list {
            position: relative;
            background: #f00;
            zoom: 1;
        }

        .act_list .sel {
            margin: 0;
            padding: 0;
            width: 80px;
            height: 22px;
            line-height: 22px;
            overflow: hidden;
            position: absolute;
            border: 1px transparent solid;
            left: 0;
            top: 0;
        }

        .act_list .sel li a {
            display: block;
            width: 100%;
            height: 22px;
            line-height: 22px;
            margin: 0;
            padding: 0 0 0 10px;
            outline: 0;
            text-decoration: none;
        }

        .act_list .sel_on {
            height: auto;
            border: 1px #C4C4C4 solid;
            background: #fff;
            z-index: 10;
            box-shadow: 0px 0px 6px #ccc;
            border-radius: 3px;
        }

        .act_list .sel_on li a:hover {
            background: #71A406;
            color: #fff;
            text-decoration: none;
        }

        .combg1 {
            float: left;
            padding: 5px 25px 6px;
            font-size: 14px;
            margin: 2px 0 0 10px;
            background: #0591b2;
        }

        .combg2 {
            float: left;
            padding: 5px 25px;
            font-size: 14px;
            background: #00cc33;
        }

        .linkSubmit a {
            color: #fff;
            text-decoration: none;
        }

        .gray {
            color: #aaa;
        }
    </style>
@endsection
@section("right_content")
    <div class="row search-row" style="padding:9px 0 15px 15px;">
        <form class="form-inline" style="width:1100px; height:auto;" method="get">
            <div class="" style="float:left; height:30px;">
                <select name="searchType" class="form-control" style="width:80px; padding:3px;">
                    <option value="name">姓名</option>
                    <option value="qq">QQ号</option>
                    <option value="mobile">手机号</option>
                </select>
                <input type="text" name="keywords" class="form-control"
                       style="height:30px; margin-bottom:3px; width:120px;" id="name" placeholder=""
                       value="{{ Request::input('keywords') }}">
            </div>

            <div class="" style="float:left; height:30px;">
                <select name="roster_type" class="form-control" style="width:90px;">
                    <option value="">来源</option>
                    @foreach($rosterType as $k=>$v)
                        <option value="{{ $k }}"
                                @if($k == Request::input("roster_type")) selected @endif >{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div style="float:left; margin-left:10px;">
                <select name="dateType" class="form-control" style="padding:3px;">
                    <option value="addtime">提交时间</option>
                    {{--   <option value="ur.reg_time">注册时间</option>
                       <option value="ur.trial_time">开通试学时间</option>
                       <option value="ur.formal_time">开通正课时间</option>--}}
                </select>

                <input type="text" name="startdate" class="form-control select_date" id="startdate" style="height:30px;"
                       placeholder="开始时间" value="{{ Request::input('startdate') }}">
                <label>&nbsp;至&nbsp;</label>
                <input type="text" name="enddate" class="form-control select_date" id="enddate" style="height:30px;"
                       placeholder="结束时间" value="{{ Request::input('enddate') }}">
            </div>
            <div class="form-control" style="">
    <span class="but-ss">
    <a class="common-button combg1 linkSubmit">搜索</a>
    </span>
            </div>
            <div class="form-control" style="">
<span class="but-ss fleft" style="line-height:25px; ">
<a class="common-button combg1 linkSubmit" data="{export:1}" style="">
    导出
</a>
</span>
            </div>

    </div>

    </form>
    <div id="w0" class="grid-view">
        <table class="table">
            <thead class="thead" style=" font-size:14px; ">
            <tr>
                <th colspan="2">总计</th>
                <th>{{ $statistics['user_total'] }}</th>
                <th><span style="color:#3bbbd9">{{ $statistics['user_total_join_group'] }}</span><span
                            style="color:#ccc;">/</span><span
                            style="color:#ff7f00">{{ $statistics['user_total_join_group_percent'] ?? '- -' }}</span>
                </th>
                <th><span style="color:#3bbbd9">{{ $statistics['user_total_group_num_3'] }}</span><span
                            style="color:#ccc;">/</span><span
                            style="color:#ff7f00">{{ $statistics['user_total_quit_group_percent'] ?? '- -' }}</span>
                </th>
                <th><span style="color:#3bbbd9">{{ $statistics['user_total_group_num_5'] }}</span><span
                            style="color:#ccc;">/</span><span
                            style="color:#ff7f00">{{ $statistics['user_total_kick_group_percent'] ?? '- -' }}</span>
                </th>
                <th><span style="color:#3bbbd9">{{ $statistics['user_total_reg_num_1'] }}</span><span
                            style="color:#ccc;">/</span><span
                            style="color:#ff7f00">{{ $statistics['user_total_reg_percent'] ?? '- -' }}</span>
                </td>
                <th><span style="color:#3bbbd9">{{ $statistics['user_total_course_num_1'] }}</span><span
                            style="color:#ccc;">/</span><span
                            style="color:#ff7f00">{{ $statistics['user_total_trial_course_percent'] ?? '- -' }}</span>
                </th>
                <th><span style="color:#3bbbd9">{{ $statistics['user_total_course_num_2'] }}</span><span
                            style="color:#ccc;">/</span><span
                            style="color:#ff7f00">{{ $statistics['user_total_formal_course_percent'] ?? '- -' }}</span>
                </th>
                <th></th>
            </tr>
            <tr>
                <th>ID</th>
                <th>姓名</th>
                <th>数据量</th>
                <th>进群量/比例</th>
                <th>退群量/比例</th>
                <th>被踢量/比例</th>
                <th>注册量/比例</th>
                <th>试学量/比例</th>
                <th>正课量/比例</th>
                <th>详情</th>
            </tr>
            </thead>
            <tbody class="tbody" style="color:#333;">
            @foreach($list as $user)
                <tr class="">
                    <td>{{ $user->uid }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user_statistics[$user->uid]['user_total'] ?? 0 }}</td>
                    <td>
                        <span style="color:#3bbbd9">{{ $user_statistics[$user->uid]['user_total_join_group'] ?? 0 }}</span><span
                                style="color:#ccc;">/</span><span
                                style="color:#ff7f00">{{ $user_statistics[$user->uid]['user_total_join_group_percent'] ?? '- -' }}</span>
                    </td>
                    <td>
                        <span style="color:#3bbbd9">{{ $user_statistics[$user->uid]['user_total_group_num_3'] ?? 0 }}</span><span
                                style="color:#ccc;">/</span><span
                                style="color:#ff7f00">{{ $user_statistics[$user->uid]['user_total_quit_group_percent'] ?? '- -' }}</span>
                    </td>
                    <td>
                        <span style="color:#3bbbd9">{{ $user_statistics[$user->uid]['user_total_group_num_5'] ?? 0 }}</span><span
                                style="color:#ccc;">/</span><span
                                style="color:#ff7f00">{{ $user_statistics[$user->uid]['user_total_kick_group_percent'] ?? '- -' }}</span>
                    </td>
                    <td>
                        <span style="color:#3bbbd9">{{ $user_statistics[$user->uid]['user_total_reg_num_1'] ?? 0 }}</span><span
                                style="color:#ccc;">/</span><span
                                style="color:#ff7f00">{{ $user_statistics[$user->uid]['user_total_reg_percent'] ?? '- -' }}</span>
                    </td>
                    <td>
                        <span style="color:#3bbbd9">{{ $user_statistics[$user->uid]['user_total_course_num_1'] ?? 0 }}</span><span
                                style="color:#ccc;">/</span><span
                                style="color:#ff7f00">{{ $user_statistics[$user->uid]['user_total_trial_course_percent'] ?? '- -' }}</span>
                    </td>
                    <td>
                        <span style="color:#3bbbd9">{{ $user_statistics[$user->uid]['user_total_course_num_2'] ?? 0 }}</span><span
                                style="color:#ccc;">/</span><span
                                style="color:#ff7f00">{{ $user_statistics[$user->uid]['user_total_formal_course_percent'] ?? '- -' }}</span>
                    </td>
                    <!--                                     <td><a href="{:U('my_data?subnavAction=adviser_statistics&adviser_id='.$l['uid'],$_GET)}">查看</a></td> -->
                    <!--                                     <td><a href="{:U('data_all?subnavAction=adviser_statistics&adviser_name='.$l['name'],$_GET)}">查看</a></td> -->
                    <td><a href="{{ route("admin.roster.list",\Illuminate\Support\Facades\Input::merge([$user_id_str=>$user->uid,'show_statistics'=>1])->all()) }}">查看</a></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="10">
                    <div class="pagenav">
                        <ul>{{ $list->appends(\Illuminate\Support\Facades\Request::all())->links() }}</ul>
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
@endsection