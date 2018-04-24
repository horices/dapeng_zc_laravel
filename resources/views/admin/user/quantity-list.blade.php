@extends("admin.public.layout")
@section("right_content")
<div class="row search-row" style="padding:9px 0 15px 15px;">
    <form class="form-inline" role="form" action="{:U()}">
        <div class="form-group">
            <select name="field_k" class="form-control">
                <option value="">请选择</option>
                <option value="qq" selected>QQ号</option>
                <option value="name">姓名</option>
                <option value="mobile">手机号</option>
                <option value="dapeng_user_mobile">主站手机号</option>
            </select>
            <input type="text" name="field_v" class="form-control" id="name" placeholder="" value="{$_GET.field_v}">
            <if condition="$_GET['field_k']"><script>$('select[name=field_k]').val('{$_GET.field_k}');</script></if>
        </div>
        <div class="form-group">
            <label class="control-label">数据状态</label>
            <select name="status" class="form-control">
                <option value="">请选择</option>
                <option value="1">正常</option>
                <option value="0">已暂停</option>
            </select>
            <if condition="is_numeric($_GET['status'])"><script>$('select[name=status]').val('{$_GET.status}');</script></if>
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
            <th style="padding-left:19px" width="80">操作</th>
        </tr>
        </thead>

        <tbody>
        <notempty name="list">
            <volist name="list" id="l">
                <tr class="{:$l['status']==0 ? 'gray' : ''}">
                    <td>{$l.code}</td>
                    <td>{$l.name}</td>
                    <!--<td>{$l['qq'] ?: '--'}</td>  -->
                    <td>{$l['mobile']}</td>
                    <td>{$l['groupCountQQ'] ?: 0}</td>
                    <td>{$l.per_max_num_qq}</td>
                    <td>{$l['groupCountWx'] ?: 0}</td>
                    <td>{$l.per_max_num_wx}</td>
                    <td>{:$l['status']==1 ? '正常' : '已暂停'}</td>
                    <td>
                        <!--<eq name="l.status" value="1">
                        <a href="{:U('changeStatus')}" class="ajaxSubmit" data="{uid:{$l.uid},status:0,model:'UserHeadMaster'}">暂停帐号</a>
                        <else />
                        <a href="{:U('changeStatus')}" class="ajaxSubmit" data="{uid:{$l.uid},status:1,model:'UserHeadMaster'}">启用帐号</a>
                        </eq>  -->
                        <a href="{:U('edit_adviser?uid='.$l['uid'])}">编辑</a>
                    </td>
                </tr>
            </volist>
            <tr>
                <td colspan="8"><div class="pagenav"><ul>{$pageNav}</ul></div></td>
            </tr>
            <else />
            <tr>
                <td colspan="8">暂无信息</td>
            </tr>
        </notempty>
        </tbody>
    </table>
</div>
@endsection