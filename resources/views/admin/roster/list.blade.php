@extends("admin.public.layout")
@section("right_content")
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
</style>

<style>
.link_1, .link_1:hover{ color:#0c3; text-decoration:none;}
.link_2, .link_2:hover{ background:#0c3; color:#fff; text-decoration:none; display:inline-block; padding:0 3px; border-radius:3px;}
</style>
            
            
  <div class="row search-row" style="padding:9px 0 15px 15px;">
        <form class="form-inline" role="form">
            <div class="form-group">
                <select name="field_k" class="form-control">
                    <option value="account" selected>QQ/微信</option>
                    <option value="group_name" @if(Request::input('field_k') == 'group_name') selected @endif>班级代号</option>
                </select>
                <input type="text" name="field_v" class="form-control" id="name" placeholder="" value="{{ Request::input("field_v") }}">
            </div>
            <div class="form-group">
                <label class="control-label">来源类型</label>
                <select name="type" class="form-control">
                    <option value="">不限</option>
                    @foreach($rosterType  as $k=>$v)
                    <option value="{{ $k }}"  @if(Request::input("type") == $k) selected @endif>{{ $v }}</option>
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
                <a class="common-button combg4 linkSubmit" href="">
                    导出
                </a>

                <!--<a href="{:U('exportResult')}"  class="common-button combg4 ajaxSubmit">
                    导出
                </a>-->


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
                    <th width="80">销售数据</th>
                    <th width="90">操作</th>

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
                    <td title="{{ $roster->course_name }}" onclick="openCourseList(this);" roster_id="{{ $roster->id }}" qq="{{ $roster->roster_no }}" style="cursor:pointer;" class="open_course_status_{{ $roster->course_type }}">
                        {{ $roster->course_type_text }}</td>
                    <td>
                        <span class="group_status_{{ $roster->group_status }}">{{ $roster->group_status_text }}</span>
                    </td>
                    <td onclick="openGroupLog(this);" roster_id="{$v.id}" qq="{{ $roster->roster_no }}" type="{{ $roster->roster_type }}" style="cursor:pointer;">
                    {!! $roster->group_event_log->count() ? $roster->group_event_log->first()->addtime_text : '无' !!}
                    </td>
                    <td>
                    @if( $roster->is_old == 0)
                    <a class="link_3" href="{{ route("admin.roster.follow.add",['roster_id'=>$roster->id]) }}" data="{roster_id:{{ $roster->id }}">点击添加</a>
                    @endif
                    </td>
                    <td>
                        @if($roster->dapeng_user_mobile)
                        <a class="ajaxLink" method="get" callback="reFun" data="{'phone':}" showLoading="1" href="{:U('Index/openCourse',['user_roster_id'=>$v[id]])}">开通</a>
                        @else
                        <a href="javascript:;" onclick="alertOpenCourse('{{ $roster->id }}')">开通</a>
                        @endif
                        <a href="javascript:;" url="{$v[userRegUrl]}" wx="{$v[wx]}" qq="{$v[qq]}" class="link_4" >链接</a>

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
<div class="pagenav"> <ul>{{ $list->links() }} </ul></div>
<style>
    #open-course{width: 300px; height: 100px; padding-top: 30px;display: none;padding-left: 10px;}
    #open-course input{width: 200px; float: left}
    .combg1{float: left}
</style>
<!--弹窗 开通课程 -->
<div id="open-course" class="form-group">
    <form method="get">
        <input type="hidden" name="user_roster_id" value="" />
        <input type="text" name="phone" class="form-control" placeholder="请输入开课学员手机号" value="" />
        &nbsp;<a class="common-button combg1 ajaxSubmit" showLoading="1" method="get" callback="reFun" href="{:U('openCourse')}">提交</a>
    </form>
</div>
@endsection