@extends("admin.public.layout")
@section("right_content")
<link rel="stylesheet" href="/js/webuploader/webuploader.css" />
<style>
.act_list{ position:relative; background:#f00; zoom:1;}
.act_list .sel{ margin:0; padding:0; width:80px; height:22px; line-height:22px; overflow:hidden; position:absolute; border:1px transparent solid; left:0; top:0;}
.act_list .sel li a{ display:block; width:100%; height:22px; line-height:22px; margin:0; padding:0 0 0 10px; outline:0; text-decoration:none;}
.act_list .sel_on{ height:auto; border:1px #C4C4C4 solid; background:#fff; z-index:10; box-shadow:0px 0px 6px #ccc; border-radius:3px;}
.act_list .sel_on li a:hover{ background:#71A406; color:#fff; text-decoration:none;}
.table th, td{word-break:break-all}
.table{table-layout:fixed;}

.gray{ color:#aaa;}
.form-group a:hover{ color:#fff;}
.group_00{}/*默认色*/
.group_01{color:#3bbbd9;}/*蓝色*/
.group_02{color:#00cc33;}/*绿色*/
.group_03{color:#ff1e00;}/*黄色*/
.group_04{color:#ff7f00;}/*红色*/
@media (min-width: 992px){
    .col-md-10{width: 85%}
}
.flag_icon::before{
    position:relative;
    display:block;
    position:absolute;
    left:10px;
}
.flag_icon_1::before{
    content:url(/admin/images/flag_icon_new.gif);

}
.flag_icon_2::before{
    content:url(/admin/images/flag_icon_active.gif);
}

.link_1, .link_1:hover{ color:#0c3; text-decoration:none;}
.link_2, .link_2:hover{ background:#0c3; color:#fff; text-decoration:none; display:inline-block; padding:0 3px; border-radius:3px;}

.webuploader-pick{padding: 5px 15px !important;margin-top: 10px !important;display: inline !important;}
</style>
<script>
    $(function () {
        $(".grade").hide();
        $(".grade{{$userInfo->grade}}").show();
    });
    function openCourseLog(roster){
        AjaxAction.ajaxLinkAction("<a url='{{ route("admin.roster.course.list") }}' method='get' showloading='true' data='{roster_id:"+roster.id+"}'></a>",function(data){
            $("#courseList").empty().html(data);
            console.log($(data).find("li").size());
            if($(data).find("li").size()){
                layer.open({
                    title:roster.roster_no+" 开通课程记录",
                    type:1,
                    shadeClose:true,
                    area:['400px','200px'],
                    skin: 'layui-layer-rim',
                    content:$("#courseList")
                });
            }
        })
    }
    function openGroupLog(roster){
        AjaxAction.ajaxLinkAction("<a url='{{ route("admin.roster.group.log.list") }}' method='get' showloading='true' data='{roster_id:"+roster.id+"}'></a>",function(data){
            $("#courseList").empty().html(data);
            if($(data).find("li").size()>1){
                layer.open({
                    title:roster.roster_no+" 群状态变更记录",
                    type:1,
                    shadeClose:true,
                    area:['400px','200px'],
                    skin: 'layui-layer-rim',
                    content:$("#courseList")
                });
            }
        })
    }

    $(function () {
        var uploader = WebUploader.create({
            auto: true,
            // swf文件路径
            swf: '/Public/js/webupload/Uploader.swf',
            //默认值：'file'文件上传域的name
            fileVal: 'download',
            // 文件接收服务端。
            server: "{{route('admin.roster.index.upload-excel')}}",
            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#uploader',
            // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
            resize: false,
            //文件重复上传
            duplicate: true,
            formData:{
                _token:$('meta[name="csrf-token"]').attr('content')
            }
        });

        //上传成功回调 如果code == 2 则跳转到导出地址
        uploader.on('uploadSuccess', function (file, response) {
            layer.close();
            AjaxAction.ajaxReturn(response);
        });
        //上传进度
        uploader.on('uploadStart', function (file) {
            layer.msg('正在导入数据，请稍候。。', {
                icon: 16
                , shade: 0.1,
                time: 99999999
            });
        });
    })

</script>
            
            
  <div class="row search-row" style="padding:9px 0 15px 15px;">
        <form class="form-inline" role="form">
                <input type="hidden" name="seoer_id" value="{{ Request::input("seoer_id") }}" />
            <input type="hidden" name="adviser_id" value="{{ Request::input("adviser_id") }}" />
            <input type="hidden" name="show_statistics" value="{{ Request::input("show_statistics") }}" />
            <div class="form-group">
                <select name="search_type" class="form-control">
                    <option value="roster_no" selected>QQ/微信</option>
                    {{--<option value="group_name" @if(Request::input('field_k') == 'group_name') selected @endif>班级代号</option>--}}
                </select>
                <input type="text" name="keywords" class="form-control" placeholder="" value="{{ Request::input("keywords") }}">
            </div>
            <div class="form-group">
                <label class="control-label">来源类型</label>
                <select name="roster_type" class="form-control">
                    <option value="">不限</option>
                    @foreach($rosterType  as $k=>$v)
                    <option value="{{ $k }}"  @if(Request::input("roster_type") == $k) selected @endif>{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="control-label">是否注册</label>
                <select name="is_reg" class="form-control">
                    <option value="">全部</option>
                    @foreach($registerStatus  as $k=>$v)
                        <option value="{{ $k }}"  @if(Request::input("is_reg") === strval($k)) selected @endif>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">开通课程</label>
                <select name="course_type" class="form-control">
                    <option value="">全部</option>
                    @foreach($courseType as $k=>$v)
                    <option value="{{ $k }}" @if(Request::input('course_type') === strval($k)) selected @endif>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">进群状态</label>
                <select name="group_status" class="form-control">
                    <option value="">全部</option>
                    @foreach($groupStatus as $k=>$v)
                    <option value="{{ $k }}" @if(Request::input('group_status') === strval($k)) selected @endif>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">新活状态</label>
                <select name="flag" class="form-control">
                    <option value="" >请选择</option>
                    <option value="1" @if(\Illuminate\Support\Facades\Input::get("flag") == 1) selected @endif>新量</option>
                    <option value="2" @if(\Illuminate\Support\Facades\Input::get("flag") == 2) selected @endif>活量</option>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">提交时间</label>
                <input type="text" name="startdate" class="form-control select_date" style="width:140px;" value="{{ Request::input("startdate") }}" /> 至
                <input type="text" name="enddate" class="form-control select_date" style="width:140px;" value="{{ Request::input("enddate") }}" />
            </div>
{{--            <div class="form-group">
                <input type="text" name="seoer_name" class="form-control" style="width:140px;" value="{{ Request::input("seoer_name") }}" placeholder="推广专员名称" />
                <input type="text"  name="adviser_name" class="form-control" style="width:140px;" value="{{ Request::input("adviser_name") }}" placeholder="课程顾问名称" />
            </div>--}}
            <div class="form-group">
                <a href="" class="common-button combg2 linkSubmit">搜索</a>
                <a class="common-button combg4 linkSubmit grade grade4 grade5" showLoading="true" data="{export:1,test:2}" href="{{ route("admin.roster.list",['export'=>1]) }}" >
                    导出
                </a>
                <a id="uploader" class="grade grade4 grade5">
                    导入
                </a>


            </div>
        </form>
    </div>
    <style>
        .table th,td{text-align: center}
    </style>
    <div id="w0" class="grid-view">
        @if(Request::get("show_statistics"))
        <table class="table">
            <thead class="thead" style=" font-size:14px; ">
            <tr>
                <th>数据总量</th>
                <th>进群总量/比例</th>
                <th>退群总量/比例</th>
                <th>被踢总量/比例</th>
                <th>注册总量/比例</th>
                <th>试学总量/比例</th>
                <th>正课总量/比例</th>
            </tr>
            <tr>
                <th>{{ $statistics['user_total'] ?? 0 }}</th>
                <th><span style="color:#3bbbd9">{{ $statistics['user_total_join_group'] ?? 0 }}</span><span style="color:#ccc;">/</span><span style="color:#ff7f00">{{ $statistics['user_total_join_group_percent'] ?? '- -' }}</span></th>
                <th><span style="color:#3bbbd9">{{ $statistics['user_total_group_num_3'] ?? 0 }}</span><span style="color:#ccc;">/</span><span style="color:#ff7f00">{{ $statistics['user_total_quit_group_percent'] ?? '- -' }}</span></th>
                <th><span style="color:#3bbbd9">{{ $statistics['user_total_group_num_5'] ?? 0 }}</span><span style="color:#ccc;">/</span><span style="color:#ff7f00">{{ $statistics['user_total_kick_group_percent'] ?? '- -' }}</span></th>
                <th><span style="color:#3bbbd9">{{ $statistics['user_total_reg_num_1'] ?? 0 }}</span><span style="color:#ccc;">/</span><span style="color:#ff7f00">{{ $statistics['user_total_reg_percent'] ?? '- -' }}</span></td>
                <th><span style="color:#3bbbd9">{{ $statistics['user_total_course_num_1'] ?? 0 }}</span><span style="color:#ccc;">/</span><span style="color:#ff7f00">{{ $statistics['user_total_trial_course_percent'] ?? '- -' }}</span></th>
                <th><span style="color:#3bbbd9">{{ $statistics['user_total_course_num_2'] ?? 0 }}</span><span style="color:#ccc;">/</span><span style="color:#ff7f00">{{ $statistics['user_total_formal_course_percent'] ?? '- -' }}</span></th>
            </tr>
            </thead>
        </table>
        @endif
        <table class="table">
            <thead>
                <tr>
                    {{--<th width="50">序号</th>--}}
                    <th width="50">类型</th>
                    <th width="100">账号</th>
                    <th width="80">班级代号</th>
                    <th width="95">群号/微信号</th>
                    <th width="80">推广专员</th>
                    <th width="80">课程顾问</th>
                    <th width="80">提交时间</th>
                    <th width="80">是否注册</th>
                    <th width="80">开通课程</th>
                    <th width="80">进群状态</th>
                    <th width="80">进群时间</th>
                    <th width="80" class="grade grade4 grade5">销售数据</th>
                    <th width="90" class="grade grade4 grade5 grade9 grade10">操作</th>

                    <!-- <th style="padding-left:19px" width="80">操作</th> -->
                </tr>
            </thead>

            <tbody>
                @foreach($list as $roster)
                <tr title="{{ $roster->qq_nickname }}" style="@if($roster->is_old == 1) opacity:0.5; @endif">
                    {{--<td class="flag_icon flag_icon_{{ $roster->flag_type }}">{{ $roster->id }}</td>--}}
                    <td class="flag_icon flag_icon_{{ $roster->flag }}">{{ $roster->roster_type_text }}</td>
                    <td>{{ $roster->roster_no }}</td>
                    <td>{{ $roster->group->group_name }}</td>
                    <td>{{ $roster->group->qq_group }}</td>
                    <td>{{ $roster->inviter_name }}</td>
                    <td>{{ $roster->last_adviser_name }}</td>
                    <td>{!! $roster->addtime_text !!}</td>
                    <td class="register_status_{{ $roster->is_reg }}">{{ $roster->is_reg_text }}</td>
                    <td title="{{ $roster->course_name }}" @if($roster->course_type) onclick="openCourseLog({{ $roster->toJson() }});" @endif style="cursor:pointer;" class="open_course_status_{{ $roster->course_type }}">
                        {{ $roster->course_type_text }}</td>
                    <td>
                        @if($roster->roster_type == 1)
                        <span class="group_status_{{ $roster->group_status }}" @if($roster->group_status) onclick="openGroupLog({{ $roster->toJson() }})" @endif style="cursor:pointer;">{{ $roster->group_status_text }}</span>
                        @elseif($roster->roster_type == 2)
                            @if($roster->group_status == 0)
                                <span class="group_status_{{ $roster->group_status }} @if($userInfo->grade != 9 && $userInfo->grade != 10) ajaxLink @endif" url="{{ route('admin.roster.change-group-status') }}" data="{roster_id:'{{ $roster->id }}',group_status:1}" warning="您确定要将{{ $roster->roster_no }}添加状态更改为等待添加吗" style="cursor:pointer;">无</span>
                            @elseif($roster->group_status == 1)
                                <span class="group_status_{{ $roster->group_status }} @if($userInfo->grade != 11 && $userInfo->grade != 12) ajaxLink @endif" url="{{ route('admin.roster.change-group-status') }}" data="{roster_id:'{{ $roster->id }}',group_status:2}" warning="您确定要将{{ $roster->roster_no }}添加状态更改为已添加嘛" style="cursor:pointer;">等待添加</span>
                            @else
                                <span class="group_status_{{ $roster->group_status }}" style="cursor:pointer;">已添加</span>
                            @endif
                        @endif
                    </td>
                    <td @if($roster->group_status) onclick="openGroupLog({{ $roster->toJson() }})" @endif style="cursor:pointer;">
                    {!! $roster->group_event_log->count() ? $roster->group_event_log->first()->addtime_text : '无' !!}
                    </td>
                    <td class="grade grade4 grade5">
                    @if( $roster->is_old == 0)
                    <a class="link_3" href="{{ route("admin.roster.follow.add",['roster_id'=>$roster->id]) }}">点击添加</a>
                    @endif
                    </td>
                    <td class="grade grade4 grade5 grade9 grade10">
                        @if($roster->dapeng_user_mobile)
                        <a class="ajaxLink" method="post" showLoading="1" data="{id:{{$roster->id}}}" url="{{route('admin.roster.index.open-course')}}">开通</a>
                        @else
                        <a href="javascript:;" onclick="alertOpenCourse('{{ $roster->id }}')">开通</a>
                        @endif
                        <a class="ajaxLink" data="{url:'{{$roster->reg_url_prama}}'}" wx="{{$roster->wx}}" qq="{{$roster->qq}}" url="{{route('admin.roster.index.set-reg-url')}}" callback="registerUrl" >链接</a>
                    </td>
                </tr>
                @endforeach
                @if(!$list->count())
                    <tr>
                        <th colspan="14">暂无信息</th>
                    </tr>
                @endif
            </tbody>
        </table>
</div>
<div class="pagenav"> <ul>{{ $list->appends(\Illuminate\Support\Facades\Request::input())->links() }} </ul></div>
<div id="courseList" style="display:none;"></div>
<style>
    #open-course{width: 300px; height: 100px; padding-top: 30px;display: none;padding-left: 10px;}
    #open-course input{width: 200px; float: left}
    .combg1{float: left}
</style>
<!--弹窗 开通课程 -->
<div id="open-course" class="form-group">
    <form method="get">
        <input type="hidden" name="id" value="" />
        <input type="text" name="phone" class="form-control" placeholder="请输入开课学员手机号" value="" />
        &nbsp;<a class="common-button combg1 ajaxSubmit" showLoading="1" method="post" url="{{route('admin.roster.index.open-course')}}">提交</a>
    </form>
</div>
<script src="/js/webuploader/webuploader.js"></script>
<script>

    //开通课程弹窗
    function alertOpenCourse(user_roster_id){
        $("#open-course").find("input[name='id']").val(user_roster_id);
        //$("input[name='user_roster_id']").val(user_roster_id);
        layer.open({
            type: 1,
            title: "开通课程",
            closeBtn: 1,
            shadeClose: true,
            content: $("#open-course")
        });
    }
    function registerUrl(json,obj) {
        var wx  =  $(obj).attr("wx");
        var qq  =  $(obj).attr("qq");
        var str = wx ? "微信号<b>"+wx+"</b>的专属注册链接" : "QQ号<b>"+qq+"</b>的专属注册链接";
        //页面层-自定义
        layer.open({
            type        : 1,
            area        : ['30%','150px'],
            title       : str,
            closeBtn    : 1,
            shadeClose  : true,
            content     : "<input type='text' value='"+json.data.reg_url+"' style='height: 50px;margin:5px 0px 0px 10px;'/>"
        });
    }
</script>
@endsection