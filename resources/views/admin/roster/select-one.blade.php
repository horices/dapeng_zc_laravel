@extends("admin.public.layout")
@section("right_content")
    <link rel="stylesheet" href="/js/webuploader/webuploader.css" />
    <style>
        .grade {
            display: none;
        }
        .group_status_underline{
            text-decoration: underline;
            cursor:pointer;
        }
        .webuploader-pick{padding: 5px 15px !important;margin-top: 10px !important;display: inline !important;}
    </style>

    <script>
        $(function () {
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
                swf: '/js/webupload/Uploader.swf',
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
            <div class="form-group">
                <select name="search_type" class="form-control">
                    <option value="roster_no" selected>QQ/微信</option>
                    {{--<option value="group_name" @if(Request::input('field_k') == 'group_name') selected @endif>班级代号</option>--}}
                </select>
                <input type="text" name="keywords" class="form-control" placeholder="" value="{{ Request::input("keywords") }}">
            </div>
            <div class="form-group">
                <a href="" class="common-button combg2 linkSubmit search">搜索</a>
            </div>
        </form>
    </div>
    <style>
        .table th,td{text-align: center}
    </style>
    <div id="w0" class="grid-view">
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
            @if($roster)
                <tr title="{{ $roster->qq_nickname }}" style="@if($roster->is_old == 1) opacity:0.5; @endif">
                    {{--<td class="flag_icon flag_icon_{{ $roster->flag_type }}">{{ $roster->id }}</td>--}}
                    <td class="flag_icon flag_icon_{{ $roster->flag }}">{{ $roster->roster_type_text }}</td>
                    <td>{{ $roster->roster_no }}</td>
                    <td>{{ $roster->group->group_name }}</td>
                    <td>{{ $roster->group->qq_group }}</td>
                    <td>{{ $roster->inviter_name }}</td>
                    <td>{{ $roster->adviser->name }}</td>
                    <td>{!! $roster->addtime_text !!}</td>
                    <td class="register_status_{{ $roster->is_reg }}">{{ $roster->is_reg_text }}</td>
                    <td title="{{ $roster->course_name }}" @if($roster->course_type) onclick="openCourseLog({{ $roster->toJson() }});" @endif style="cursor:pointer;" class="open_course_status_{{ $roster->course_type }}">
                        {{ $roster->course_type_text }}</td>
                    <td>
                        @if($roster->group_status == 0)
                            <span class="group_status_{{ $roster->group_status }} @if($userInfo->grade != 11 && $userInfo->grade != 12 && $roster->roster_type == 2) ajaxLink group_status_underline @endif group_status_type_{{ $roster->roster_type }}" url="{{ route('admin.roster.change-group-status') }}" data="{roster_id:'{{ $roster->id }}',group_status:2}" warning="您确定要将{{ $roster->roster_no }}添加状态更改为已添加吗">无</span>
                        @else
                            <span class="group_status_{{ $roster->group_status }} group_status_type_{{ $roster->roster_type }}">{{ $roster->group_status_text }}</span>
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
                            <a class="@if($roster->is_old != 1) ajaxLink @endif" method="post" showLoading="1" data="{id:{{$roster->id}}}" url="{{route('admin.roster.index.open-course')}}" @if($roster->roster_type == 2 && $roster->group_status != 2) style='display:none;' @endif>开通</a>
                            @if($userInfo->grade <= 5 )
                                <a class="@if($roster->is_old != 1) ajaxLink @endif" method="post" showLoading="1" data="{roster_id:{{$roster->id}}}" url="{{route('admin.roster.unbind')}}">解绑</a>
                            @endif
                        @else
                            <a href="javascript:;" account="{{$roster->account}}" roster-type="{{ $roster->roster_type_text }}" @if($roster->is_old != 1) onclick="alertOpenCourse('{{ $roster->id }}',this)" @endif @if($roster->roster_type == 2 && $roster->group_status != 2) style='display:none;' @endif>开通</a>
                        @endif
                        <a class="@if($roster->is_old != 1) ajaxLink @endif" data="{url:'{{$roster->reg_url_prama}}'}" wx="{{$roster->wx}}" qq="{{$roster->qq}}" url="{{route('admin.roster.index.set-reg-url')}}" callback="registerUrl" @if($roster->roster_type == 2 && $roster->group_status != 2) style='display:none;' @endif>链接</a>
                    </td>
                </tr>
            @else
                <tr>
                    <th colspan="14">暂无信息</th>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
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