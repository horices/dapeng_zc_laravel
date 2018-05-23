@extends("admin.public.layout")
@section("right_content")
<div class="row search-row" style="padding:9px 0 15px 15px;">
	<div class="row">
        <a href="{{ route('admin.user.add') }}" class="btn btn-info">添加新成员</a>
    </div>
    <div class="clearfix" style="margin-bottom: 10px;"></div><!-- 清除浮动 -->
    <div class="row">
    <form class="form-inline" role="form" action="">
    	<input type="hidden" name="page" value="1" />
        <div class="form-group">
            <select name="field_k" class="form-control">
                <option value="name" {{ Request::input('field_k') == 'name'?'selected':'' }}>姓名</option>
                <option value="mobile" {{ Request::input('field_k') == 'mobile'?'selected':'' }}>展翅手机号</option>
                <option value="dapeng_user_mobile" {{ Request::input('field_k') == 'dapeng_user_mobile'?'selected':'' }}>主站手机号</option>
            </select>
            <input type="text" name="field_v" class="form-control" id="name" placeholder="" value="{{Request::input('field_v')}}">
        </div>
        <div class="form-group">
            <label class="control-label">用户状态</label>
            <select name="status" class="form-control">
                <option value="">请选择</option>
                <option value="1" {{ Request::input('status') == 1?'selected':'' }}>正常</option>
                <option value="0" {{ Request::input('status') === '0'?'selected':'' }}>已暂停</option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label">身份</label>
            <select name="grade" class="form-control">
                <option value="">请选择</option>
                @foreach($userGradeList as $key=> $grade)
                <option value="{{$key}}" {{ Request::input("grade") == $key?'selected':'' }}>{{$grade}}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <a class="common-button combg2 linkSubmit">搜索</a>
        </div>

        <div class="form-group"  style=" color:#fff;  width:122px; margin-top:3px;  ">
            <span class="but-ss fleft" style=" height:auto;text-align:center; line-height:25px; ">
                <a class="common-button combg4 linkSubmit" data="{export:1}" style="height:30px;line-height:20px;">导出</a>
            </span>
        </div>
    </form>
    </div>
</div>

<div id="w0" class="grid-view">
    <table class="table">
        <thead>
            <tr>
                <th>工号</th>
                <th>姓名</th>
                <th>展翅系统账号</th>
                <th>主站手机号</th>
                <th>状态</th>
                <th>在职状态</th>
                <th>用户身份</th>
                <th style="padding-left:19px" width="80">操作</th>
            </tr>
        </thead>
        
        <tbody>
        	@foreach($list as $user)
            <tr for="user in userList.data" class="{:$v['status']==0 ? 'gray' : ''}">
                <td>{{$user->staff_no}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->mobile}}</td>
                <td>{{$user->dapeng_user_mobile ? $user->dapeng_user_mobile : '---------------'}}</td>
                <td>
                    @if($user->status == 1)
                    <span class="common-button combg1 ajaxLink" url="{{ route("admin.user.save") }}" showloading="true" data="{uid:{{ $user->uid }},status:0}" warning="确认要暂停该账号么，暂停后，群自动关闭">{{$user->status_text}}</span>
                    @else
                    <span class="common-button combg3 ajaxLink" url="{{ route("admin.user.save") }}" showloading="true" data="{uid:{{ $user->uid }},status:1}" warning="确认要启用该账号么">{{$user->status_text}}</span>
                    @endif
                </td>
                <td>{{$user->incumbency_text}}</td>
                <td>{{$user->grade_text}}</td>
                <td>
                    @if($user->grade > \App\Utils\Util::getUserInfo()['grade'])
                    <a href="{{ route('admin.user.edit',['id'=>$user->uid])}}">修改帐号</a>
                    @endif
                </td>

                <td>
                    @if(in_array($user->grade,['10','9']))
                        <a class="ajaxSubmit" data="{uid:{{$user->uid}}}" showloading="true" url="{{route('admin.user.open-course-head')}}">开课</a>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="8"><div class="pagenav"><ul>{{$list->appends(Request::input())->links()}}</ul></div></td>
            </tr>
        </tbody>
    </table>
</div>

@endsection