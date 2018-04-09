<extend name="Public/base" />

<block name="style">
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
</style>
</block>

<block name="script">
<script type="text/javascript">
$(function(){
	var currentGroupStatus = "{$Think.get.group_status|default=''}";
	var courseType = "{$Think.get.course_type}";
	var isReg = "{$Think.get.is_reg}";
	$("select[name='group_status'] option[value='"+currentGroupStatus+"']").prop("selected","selected");
	$("select[name='course_type'] option[value='"+courseType+"']").prop("selected","selected");
	$("select[name='is_reg'] option[value='"+isReg+"']").prop("selected","selected");
});

/**
 * 查看该QQ的开课记录
 */
function openCourseList(obj){
	var obj = $(obj);
	var qq = obj.attr("qq");
	$.post("{:U('getQQCourseList')}",{roster_id:obj.attr("roster_id")},function(data){
		$("#courseList").empty().html(data);
		if($(data).find("li").size()){
			layer.open({
				title:qq+" 开通课程记录",
				type:1,
				shadeClose:true,
				area:['400px','200px'],
				skin: 'layui-layer-rim',
				content:$("#courseList")
			});
		}
	});
}
/**
 * 查看该QQ的进群记录
 */
function openGroupLog(obj){
	var obj = $(obj);
	var qq = obj.attr("qq");
	$.post("{:U('getRosterGroupLogList')}",{roster_id:obj.attr("roster_id"),roster_type:obj.attr("type")},function(data){
		$("#courseList").empty().html(data);
		if($(data).find("li").size()>1){
			layer.open({
				title:qq+" 群记录",
				type:1,
				shadeClose:true,
				area:['400px','200px'],
				skin: 'layui-layer-rim',
				content:$("#courseList")
			});
		}
	});
}
</script>
</block>

<block name="content">
<div id="content-container" class="container">
    <div class="row row-2-10">
    <include file="Public:nav" />
        <div class="col-md-10 dp-member-content" style="padding:30px 30px;">

			<style>
            .link_1, .link_1:hover{ color:#0c3; text-decoration:none;}
			.link_2, .link_2:hover{ background:#0c3; color:#fff; text-decoration:none; display:inline-block; padding:0 3px; border-radius:3px;}
            </style>
            
            
          <div class="row search-row" style="padding:9px 0 15px 15px;">
                <form class="form-inline" role="form">
                	<input type="hidden" name="subnavAction" value="{$Think.get.subnavAction}" />
                    	<input type="hidden" name="adviser_id" value="{$Think.get.adviser_id}" />
                        <input type="hidden" name="grade" value="{$Think.get.grade}" />
                	<div class="form-group">
                    	<select name="field_k" class="form-control">
                        	<!--<option value="">请选择</option>-->
                            <option value="account" selected>QQ/微信</option>
                        </select>
                    	<input type="text" name="field_v" class="form-control" id="name" placeholder="" value="{$_GET.field_v}">
                    </div>
                    
                    <div class="form-group">
                		<a href="{:U('')}" class="common-button combg2 linkSubmit">搜索</a>
                		<!-- <a class="common-button combg4 linkSubmit" href="{:U('exportResult')}">
                            导出
                        </a> -->


                    </div>
                </form>
            </div>
            <style>
                .table th,td{text-align: center}
            </style>
            <if condition="$list">   
            <div id="w0" class="grid-view">
                <table class="table">
                    <thead>
                        <tr>
                        	<th width="50">序号</th>
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
                            <th width="90">操作</th>

                            <!-- <th style="padding-left:19px" width="80">操作</th> -->
                        </tr>
                    </thead>
                    
                    <tbody>
                            <foreach name="list" item="v">
                                <tr title="{$v.qq_nickname}" style="<eq name='v.is_old' value='1'>opacity:0.5;</eq>">
                                <td class="flag_icon flag_icon_{$v.flag_type}">{$key+1}
                                </td>
                                    <td><eq name="v.type" value="1">QQ<else/>微信</eq></td>
                                    <td>
                                        <eq name="v.type" value="1">{$v.qq}<else/>{$v.wx}</eq>
                                    </td>
                                    <td>{$v.group_name}</td>
									<td>{$v.qq_group}</td>
									<td>{$v.seoer_name}</td>
									<td>{$v.adviser_name}</td>
                                    <td>{:date('m-d', $v['addtime'])}<br>{:date('H:i', $v['addtime'])}</td>
                                    <td><eq name="v.is_reg" value="1"><span style="color:#00cc33;">已注册</span><else />未注册</eq></td>
                                    <td title="{$v.course_name}" onclick="openCourseList(this);" roster_id="{$v.id}" qq="{$v.account}" style="cursor:pointer;"><switch name="v.course_type"><case value="1"><span style="color:#3bbbd9;">试学课</span></case><case value="2"><span style="color:#00cc33;">正式课</span></case><default /><span style="color:#ff1e00;">未开通</span></switch></td>
                                    <td>
                                    <eq name="v.type" value="1">
                                    	<span  class="group_0{$v.group_status}">{$groupStatusQQ[$v['group_status']]}</span>
                                    <else />
                                    <if condition="$v[group_status] eq 0" >
                                    <span class="group_0{$v.group_status} ajaxLink" style="text-decoration:underline;cursor:pointer;"   href="{:U('setWxStatusTG')}" data="{roster_id:{$v.id},qq:'{$v.account}',group:'{$v.qq_group}'}" warning="您确定要将{$v.wx}添加状态更改为等待添加吗？"  roster_type="{$v.type}" roster_group_status="{$v.group_status}" roster_id="{$v.id}" style="cursor:pointer;" >{$groupStatusWx[$v['group_status']]}</span>
                                    
                                    <elseif condition="$v.group_status eq 1"/>
                                    <span class="group_0{$v.group_status} ajaxLink" style="text-decoration:underline;cursor:pointer;"   href="{:U('setWxStatusGW')}" data="{roster_id:{$v.id},qq:'{$v.account}',group:'{$v.qq_group}'}" warning="您确定要将{$v.wx}添加状态更改为已添加吗？"  roster_type="{$v.type}" roster_group_status="{$v.group_status}" roster_id="{$v.id}" style="cursor:pointer;"  >{$groupStatusWx[$v['group_status']]}</span>
                                    <else />
                                    		<span class="group_0{$v.group_status}">{$groupStatusWx[$v['group_status']]}</span>
                                    </if>
                                    	<!--  <if condition="$v[group_status] ELT 1" >
                                    		<span class="group_0{$v.group_status}" style="text-decoration:underline">{$groupStatusWx[$v['group_status']]}</span>
                                    	<else />
                                    		<span class="group_0{$v.group_status}">{$groupStatusWx[$v['group_status']]}</span>
                                    	</if>-->
                                    </eq>
                                    </td>
                                    <td onclick="openGroupLog(this);" roster_id="{$v.id}" qq="{$v.account}" type="{$v.type}" style="cursor:pointer;">
                                    <if condition="$v['entertime']" >{:date('m-d', $v['entertime'])}<br>{:date('H:i', $v['entertime'])}
                                    <else />
                                    </if>
                                    </td>
                                    <td>
                                        <empty name="v.dapeng_user_mobile">
                                            <a href="javascript:;" onclick="alertOpenCourse('{$v.id}')">
                                                开通
                                            </a>
                                            <else/>
                                            <a class="ajaxLink" method="get" callback="reFun" data="{'phone':<?php echo $v[dapeng_user_mobile] ?>}" showLoading="1" href="{:U('Index/openCourse',['user_roster_id'=>$v[id]])}">开通</a>
                                        </empty>
                                        <a href="javascript:;" url="{$v[userRegUrl]}" wx="{$v[wx]}" qq="{$v[qq]}" class="link_4" >链接</a>

                                    </td>
                                </tr>	
                            </foreach>
                     
                            <tr>
                      
                                <td colspan="14" ></td>
                            </tr>
                    </tbody>
                </table>
        </div>
        <div class="pagenav"> <ul>{$pageNav} </ul></div>
        <else />
            <tr>
            <notempty name="Think.get.field_v"><th colspan="14" >无账号信息 请重新搜索</th></notempty>
                                
                            </tr>
        </if>
    </div>
</div>
</div>

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

    <script>
        function reFun(json,obj){
            if(json.code == 1){
                openSuccessDialog("开通成功！");
                setTimeout(function () {
                    window.location.reload();
                },1500);
            }else{
                openFailDialog(json.msg);
            }
        }

        //开通课程弹窗
        function alertOpenCourse(user_roster_id){
            $("input[name='user_roster_id']").val(user_roster_id);
            layer.open({
                type: 1,
                title: "开通课程",
                closeBtn: 1,
                shadeClose: true,
                content: $("#open-course")
            });
        }

        function addGroup(json,obj){
            if(json.code == 1){
                openSuccessDialog(json.msg);
                $('#addgroup').html('已加').css('color','#909090').attr('href', 'javascript:;').unbind();
            }else{
                openFailDialog(json.msg);
            }


        }
    </script>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">查看详情</h4>
            </div>
            <div class="modal-body"> ... </div>
        </div>
    </div>
</div>
<div style="display:none;" id="courseList">
</div>
    <script>
        $(".link_4").click(function(){
            var url = $(this).attr("url");
            var wx  =  $(this).attr("wx");
            var qq  =  $(this).attr("qq");
            var str = wx ? "微信号<b>"+wx+"</b>的专属注册链接" : "QQ号<b>"+qq+"</b>的专属注册链接";
            //url_con = "";
            $.ajax({
                url         :   "{:U('setRegUrl')}",
                data        :   url,
                method      :   "post",
                dataType    :   "json",
                success     :   function(data){
                    //页面层-自定义
                    layer.open({
                        type        : 1,
                        area        : ['30%','150px'],
                        title       : str,
                        closeBtn    : 1,
                        shadeClose  : true,
                        content     : "<input type='text' value='"+data.data.url+"' style='height: 50px;margin:5px 0px 0px 10px;'/>"
                    });
                }
            });
        });
    </script>
</block>