@extends("admin.public.layout")
@section("right_content")
<div class="row search-row" style="padding:9px 0 15px 15px;">
<div class="">
<a href="{{ route('admin.group.add') }}" class="btn btn-info">添加新群</a>
</div>
<div class="clearfix" style="margin-bottom: 10px;"></div><!-- 清除浮动 -->
   <form class="form-inline" role="form">
       <div class="form-group">
           <label class="control-label">来源类型</label>
           <select name="type" class="form-control">
               <option value="">请选择</option>
               @foreach($rosterType as $k=>$v)
               <option value="{{$k}}" @if(Request::input('type') == $k) selected @endif >{{$v}}</option>
               @endforeach
           </select>
       </div>
    <div class="form-group">
        <select name="field_k" class="form-control" style="width:160px">
               <option value="group_name" @if(Request::input('field_k') == 'group_name') selected @endif>班级代号</option>
               <option value="qq_group" @if(Request::input('field_k') == 'qq_group') selected @endif>群号/微信号</option>
            <option value="adviser_name" @if(Request::input('field_k') == 'adviser_name') selected @endif>课程顾问姓名</option>
           </select>
        <input type="text" name="field_v" class="form-control" id="name" placeholder="" value="{{ Request::input("field_v")}}">
       </div>
       <div class="form-group">
        <label class="control-label">开启状态</label>
        <select name="is_open" class="form-control">
            <option value="">请选择</option>
               <option value="1" @if(Request::input('is_open') === '1') selected @endif>开启</option>
               <option value="0" @if(Request::input('is_open') === '0') selected @endif>关闭</option>
           </select>
       </div>
       <div class="form-group">
        <div  class="form-but linkSubmit"><a class="common-button dblock combg1" showloading="true">搜索</a></div>
       </div>
       <div class="form-group">
        <div  class="form-but"><a class="common-button dblock combg2 ajaxLink"  url="{{route('admin.group.close-all-group')}}" warning="确认关闭所有群么，关闭后无法提交数据!">一键关闭所有群</a></div>
       </div>
       <div class="form-group">
        <a class="common-button combg5 linkSubmit" data="{export:1}" showloading="true">导出</a>
       </div>
   </form>
</div>

<div id="w0" class="grid-view">
   <table class="table">
       <thead>
           <tr>
               <th>类型</th>
               <th>班级代号</th>
               <th>群号/微信号</th>
               <th>课程顾问名称</th>
               <th>开启状态</th>
               <!-- <th>人员状态</th> -->
               <th style="padding-left:19px" width="180">操作</th>
           </tr>
       </thead>
       <tbody>
            @foreach ($list as $group)
               <tr>
                   <td>{{ $group->type_text }}</td>
                   <td>{{ $group->group_name }}</td>
                   <td>{{ $group->qq_group }}</td>
                   <td>{{ $group->user->name }}</td>
                   <td>
                   @if($group->is_open)
                   <a class="common-button combg1 ajaxLink" style="margin:0px;padding:4px 8px;" url="{{ route('admin.group.save') }}" data="{id:{{ $group->id }},is_open:0}" warning="确认要关闭该群么">{{$group->is_open_text}}</a>
                   @else
                   <a class="common-button combg3 ajaxLink" style="margin:0px;padding:4px 8px;" url="{{ route('admin.group.save') }}" data="{id:{{ $group->id }},is_open:1}" warning="确认要开启该群组,开启后会自动关闭该课程顾问关联的其它群">{{$group->is_open_text}}</a>
                   @endif
                   </td>
                   <td>
                  <a href="{{ route("admin.group.edit",['id'=>$group->id]) }}">修改</a>
                   </td>
               </tr>
               @endforeach
               <tr>
                   <td colspan="8"><div class="pagenav"><ul>{{ $list->appends(Request::input())->links() }}</ul></div></td>
               </tr>
               <tr>
                   <td colspan="8"></td>
               </tr>
       </tbody>
   </table>
</div>
@endsection