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
                <option value="mobile" {{ Request::input('field_k') == 'mobile'?'selected':'' }}>手机号</option>
            </select>
            <input type="text" name="field_v" class="form-control" id="name" placeholder="" value="{{Request::input('field_v')}}">
        </div>
        <div class="form-group">
            <label class="control-label">数据状态</label>
            <select name="status" class="form-control">
                <option value="">请选择</option>
                <option value="1" {{ Request::input('status') == 1?'selected':'' }}>正常</option>
                <option value="0" {{ Request::input('status') === '0'?'selected':'' }}>已暂停</option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label">用户等级</label>
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
                <a class="common-button combg4 linkSubmit" href="{:U('exportUserStatistics')}" style="height:30px;line-height:20px;">导出</a>
            </span>
        </div>
    </form>
    </div>
</div>

<div id="w0" class="grid-view">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>姓名</th>
                <th>手机号</th>
                <th>状态</th>
                <th>用户身份</th>
                <th style="padding-left:19px" width="80">操作</th>
            </tr>
        </thead>
        
        <tbody>
        	@foreach($list as $user)
            <tr for="user in userList.data" class="{:$v['status']==0 ? 'gray' : ''}">
                <td>{{$user->uid}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->mobile}}</td>
                <td>{{$user->status}}</td>
                <td>{{$user->grade_text}}</td>
                <td><a href="{{ route('admin.user.edit',['id'=>$user->uid])}}">修改帐号</a></td>
            </tr>
            @endforeach
                <tr>
                    <td colspan="8"><div class="pagenav"><ul>{{$list->appends(Request::input())->links()}}</ul></div></td>
                </tr>
        </tbody>
    </table>
</div>

@endsection