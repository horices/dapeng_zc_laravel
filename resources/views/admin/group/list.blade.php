@extends("admin.public.layout")
@section("right_content")
       <div class="row search-row" style="padding:9px 0 15px 15px;">
           <form class="form-inline" role="form">

               <div class="form-group">
                   <label class="control-label">来源类型</label>
                   <select name="type" class="form-control">
                       <option value="">请选择</option>
                       <option value="1" <eq name="_GET[type]" value="1">selected</eq> >QQ</option>
                       <option value="2" <eq name="_GET[type]" value="2">selected</eq>>微信</option>
                   </select>
                   <if condition="is_numeric($_GET['is_open'])"><script>$('select[name=is_open]').val('{$_GET.is_open}');</script></if>
               </div>

           	<div class="form-group">
               	<select name="field_k" class="form-control" style="width:160px">
                   	<option value="">请选择</option>
                       <option value="g.group_name" selected>班级代号</option>
                       <option value="g.qq_group">群号/微信号</option>
		<option value="h.name">课程顾问姓名</option>
                   </select>
               	<input type="text" name="field_v" class="form-control" id="name" placeholder="" value="{$_GET.field_v}">
                   <if condition="$_GET['field_k']"><script>$('select[name=field_k]').val('{$_GET.field_k}');</script></if>
               </div>
               <div class="form-group">
               	<label class="control-label">开启状态</label>
               	<select name="is_open" class="form-control">
                   	<option value="">请选择</option>
                       <option value="1">开启</option>
                       <option value="0">关闭</option>
                   </select>
                   <if condition="is_numeric($_GET['is_open'])"><script>$('select[name=is_open]').val('{$_GET.is_open}');</script></if>
               </div>
               
               <div class="form-group">
           		<div  class="form-but linkSubmit" href="{:U()}"><a class="common-button dblock combg1">搜索</a></div>
               </div>
               <div class="form-group">
           		<div  class="form-but"><a class="common-button dblock combg2 ajaxLink"  href="{:U('Index/closeAllGroup')}" warning="确认关闭所有群么，关闭后无法提交数据!">一键关闭所有群</a></div>
               </div>
               <div class="form-group">
           		<a class="common-button combg5 linkSubmit" href="{:U('Index/exportQQGroup')}">导出</a>
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
               	<if condition="$list">
                       <foreach name="list" item="v">
                           <tr>
                               <td><eq name="v.type" value="1">QQ<else/>微信</eq></td>
                               <td>{$v.group_name}</td>
                               <td>{$v.qq_group}</td>
                               <td>{$v.name}<notempty name="v.qq">({$v.qq})</notempty></td>
                               <td>
                               <eq name="v.is_open" value="1">
                               <a href="{:U('changeStatus')}" data="{id:{$v.id},is_open:0,model:'UserQQGroup'}" class="common-button combg1 ajaxLink" style="margin:0px;padding:4px 8px;" warning="确认要关闭该群组么,关闭后系统不再对该群组进行分量？">开启</a>
                               <else />
                               <a href="{:U('changeStatus')}" data="{id:{$v.id},is_open:1,model:'UserQQGroup'}" class="common-button combg3 ajaxLink" style="margin:0px;padding:4px 8px;" warning="确认要开启该群组么,开启后会自动关闭该课程顾问关联的其它群？">关闭</a>
                               </eq>
                               </td>
                               <!-- <td>
                               <switch name="v.status">
                               <case value="1">
                               <a href="{:U('changeStatus')}" data="{id:{$v.id},status:2,model:'UserQQGroup'}" class="common-button combg1 ajaxLink" style="margin:0px;padding:4px 8px; font-size:auto;" warning="已满后该群不再分量，是否确认?">正常</a>
                               </case>
                               <case value="2">
                               <a href="{:U('changeStatus')}" data="{id:{$v.id},status:1,model:'UserQQGroup'}" class="common-button combg3 ajaxLink" style="margin:0px;padding:4px 8px; font-size:auto;" warning="修改后该群正常进量，是否确认?">已满</a>
                               </case>
                               <default />
                               <a href="{:U('changeStatus')}" data="{id:{$v.id},status:1,model:'UserQQGroup'}" class="common-button combg2 ajaxLink" style="margin:0px;padding:4px 8px; font-size:auto;" warning="修改后该群正常进量，是否确认?">注销</a>
                               </switch>
                               </td> -->
                               <td>
                               <gt name="v.leader_id" value='0'>
                               <a href="{:U('changeStatus')}" data="{id:{$v.id},leader_id:'',model:'UserQQGroup'}" class="ajaxLink" warning="确认要解除绑定么？" >取消绑定</a>
                               </gt>
                               <!-- <a href="{:U('del')}" data="{id:{$v.id},model:'UserQQGroup'}" class="ajaxLink" warning="确认要删除该群么，删除后不可恢复!">删除该群</a> -->
                              <a href="{:U('editQQGroup?id='.$v['id'])}">修改</a>
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