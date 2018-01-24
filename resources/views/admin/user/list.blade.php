@extends("admin.public.layout")
@section("right_content")
<div class="row search-row" style="padding:9px 0 15px 15px;">
    <form class="form-inline" role="form" action="{:U()}">
        <input type="hidden" name="grade" value="{$_GET['grade']}" />
        <div class="form-group">
            <select name="field_k" class="form-control">
                <option value="">请选择</option>
                <option value="qq" selected>QQ号</option>
                <option value="name">姓名</option>
                <option value="mobile">手机号</option>
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
            <label class="control-label">用户等级</label>
            <select name="field_g" class="form-control">
                <option value="">请选择</option>
                <option value="4">管理员</option>
                <option value="5">数据员</option>
                <option value="9">课程顾问战队长</option>
                <option value="10">课程顾问</option>
                <option value="12">推广专员</option>
                <option value="11">智能推广</option>
            </select>
            <if condition="is_numeric($_GET['field_g'])"><script>$('select[name=field_g]').val('{$_GET.field_g}');</script></if>
        </div>
        
        <div class="form-group">
            <a href="{:U('')}" class="common-button combg2 linkSubmit">搜索</a>
        </div>

        <div class="form-group"  style=" color:#fff;  width:122px; margin-top:3px;  ">
            <span class="but-ss fleft" style=" height:auto;text-align:center; line-height:25px; ">
                <a class="common-button combg4 linkSubmit" href="{:U('exportUserStatistics')}" style="height:30px;line-height:20px;">导出</a>
            </span>
        </div>
    </form>
</div>

<div id="w0" class="grid-view">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>姓名</th>
                <th>QQ号</th>
                <th>手机号</th>

                <!-- <th>未通过</th>
                <th>待验证</th>
                <th>通过</th> -->
                
                <th>状态</th>
                <th>等级</th>
                <th style="padding-left:19px" width="80">操作</th>
            </tr>
        </thead>
        
        <tbody>
            <if condition="$seoer">
                <foreach name="seoer" item="v">
                    <tr class="{:$v['status']==0 ? 'gray' : ''}">
                        <td>{$v.code}</td>
                        <td>{$v.name}</td>
                        <td>{$v['qq'] ?: '--'}</td>
                        <td>{$v['mobile']}</td>
                        <!-- <td>{$v[-1] ?: 0}</td>
                        <td>{$v[1] ?: 0}</td>
                        <td>{$v[2] ?: 0}</td> -->
                        <td>{:$v['status']==1 ? '正常' : '已暂停'}</td>
                        <td>
                        <if condition="$v['grade'] eq 4">管理员
                        <elseif condition="$v['grade'] eq 5"/>数据员
                        <elseif condition="$v['grade'] eq 9"/>课程顾问战队长
                        <elseif condition="$v['grade'] eq 10"/>课程顾问
                        <elseif condition="$v['grade'] eq 12"/>推广专员
                        <elseif condition="$v['grade'] eq 11"/>智能推广
                        </if>
                        </td>
                        <td>
                        <eq name="v.status" value="1">
                        <a href="{:U('changeStatus')}" class="ajaxSubmit" data="{uid:{$v.uid},status:0,model:'UserHeadMaster'}">暂停帐号</a>
                        <else />
                        <a href="{:U('changeStatus')}" class="ajaxSubmit" data="{uid:{$v.uid},status:1,model:'UserHeadMaster'}">启用帐号</a>
                        </eq>
                         <br /> 
                         <if condition="$v['grade'] eq 10" >
                         <a href="{:U('edit_adviser?uid='.$v['uid'])}">修改帐号</a>
                         <elseif condition="$v['grade'] eq 12"/>
                         <a href="{:U('edit_seoer?uid='.$v['uid'])}">修改帐号</a>
                         </if> 

                         </td>
                    </tr>   
                </foreach>
         
                <tr>
                    <td colspan="8"><div class="pagenav"><ul>{$pageNav}</ul></div></td>
                </tr>
            <else />
                <tr>
                    <td colspan="8"></td>
                </tr>
            </if>
        </tbody>
    </table>
</div>

@endsection