@extends("admin.public.layout")
@section("right_content")
<div class="row search-row" style="padding:9px 0 15px 15px;">
    <form class="form-inline" role="form">
        <div class="form-group">
            <select name="field_k" class="form-control">
                <option value="">请选择</option>
                <option value="qq">QQ号</option>
                <option value="name">姓名</option>
                <option value="mobile">手机号</option>
                <option value="dapeng_user_mobile">主站手机号</option>
            </select>
            <input type="text" name="field_v" class="form-control" id="name" placeholder="" value="{{Request::input('field_v')}}">
        </div>
        <div class="form-group">
            <label class="control-label">数据状态</label>
            <select name="status" class="form-control">
                <option value="">请选择</option>
                <option value="1">正常</option>
                <option value="0">已暂停</option>
            </select>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-default">搜 索</button>
        </div>
    </form>
</div>

<div id="w0" class="grid-view">
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>姓名</th>
            <!--<th>QQ号</th>  -->
            <th>手机号</th>
            <th>负责的QQ群（个）</th>
            <th>QQ每轮分配数量</th>
            <th>负责的微信号（个）</th>
            <th>微信每轮分配数量</th>
            <th>状态</th>
            <th width="80">操作</th>
        </tr>
        </thead>

        <tbody>
        @if(count($list)>0)
            @foreach($list as $v)
                <tr>
                    <td>{{$v->code}}</td>
                    <td>{{$v->name}}</td>
                    <!--<td>{$l['qq'] ?: '--'}</td>  -->
                    <td>{{$v->mobile}}</td>
                    <td>{{$v->groups_qq_count or 0}}</td>
                    <td>{{$v->per_max_num_qq or 0}}</td>
                    <td>{{$v->groups_wx_count or 0}}</td>
                    <td>{{$v->per_max_num_wx or 0}}</td>
                    <td>{{$v->status_text}}</td>
                    <td>
                        <!--<eq name="l.status" value="1">
                        <a href="{:U('changeStatus')}" class="ajaxSubmit" data="{uid:{$l.uid},status:0,model:'UserHeadMaster'}">暂停帐号</a>
                        <else />
                        <a href="{:U('changeStatus')}" class="ajaxSubmit" data="{uid:{$l.uid},status:1,model:'UserHeadMaster'}">启用帐号</a>
                        </eq>  -->
                        <a href="{{route('admin.user.quantity-edit',['uid'=>$v->uid])}}">编辑</a>
                    </td>
                </tr>
            @endforeach
                <tr>
                    <td colspan="8"><div class="pagenav"><ul>{{$list->appends(Request::input())->links()}}</ul></div></td>
                </tr>
            @else
                <tr>
                    <td colspan="8">暂无信息</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection