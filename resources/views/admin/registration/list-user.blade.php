@extends("admin.public.layout")
@section("right_content")

<style>
    .container{width: 1340px}
    .act_list{ position:relative; background:#f00; zoom:1;}
    .act_list .sel{ margin:0; padding:0; width:80px; height:22px; line-height:22px; overflow:hidden; position:absolute; border:1px transparent solid; left:0; top:0;}
    .act_list .sel li a{ display:block; width:100%; height:22px; line-height:22px; margin:0; padding:0 0 0 10px; outline:0; text-decoration:none;}
    .act_list .sel_on{ height:auto; border:1px #C4C4C4 solid; background:#fff; z-index:10; box-shadow:0px 0px 6px #ccc; border-radius:3px;}
    .act_list .sel_on li a:hover{ background:#71A406; color:#fff; text-decoration:none;}

    .gray{ color:#aaa;}
    .form-group a:hover{ color:#fff;}
    .group_00{}/*默认色*/
    .group_01{color:#3bbbd9;}/*蓝色*/
    .group_02{color:#00cc33;}/*绿色*/
    .group_03{color:#ff1e00;}/*黄色*/
    .group_04{color:#ff7f00;}/*红色*/
    .td_clip{overflow:hidden; white-space: nowrap;-moz-text-overflow: ellipsis;text-overflow: ellipsis;width: 100px;}
    /*开课容器*/
    #openCourse{padding: 20px 0px 0px 10px;}
    #openCourse .common-button{margin-left: 20px;}
    /*设置开课状态*/
    .set-open{position:absolute;display: none;background-color: #f3f3f3; height: 70px; width: 75px;}
    .set-open dl dd{border: 1px solid #333333;border-top: none;text-align: center}
    .set-open dl dd:nth-child(1){border-top: 1px solid #333333;}
</style>

    <script type="text/javascript">
        $(function(){
            var currentGroupStatus = "{$Think.get.group_status|default=''}";
            var courseType = "{$Think.get.course_type}";
            var isReg = "{$Think.get.is_reg}";
            $("select[name='group_status'] option[value='"+currentGroupStatus+"']").prop("selected","selected");
            $("select[name='course_type'] option[value='"+courseType+"']").prop("selected","selected");
            $("select[name='is_reg'] option[value='"+isReg+"']").prop("selected","selected");
            $("select[name='dateType'] option[value='{$Think.get.dateType}']").prop("selected",true);
            //列表点击变色
            $(".listCurrent").click(function () {
                $(this).css('background-color','darkgrey').siblings().css('background-color','')
            })
        });

        /**
         * 查看该QQ的开课记录
         */
        function openCourseList(obj){
            $("#actRegId").val($(obj).attr('regId'));
            $(":radio[name='val']:eq("+$(obj).attr('isOpen')+")").prop("checked",'checked');
            console.log($(obj).attr('regId'));
            console.log($(obj).attr('isOpen'));
            layer.open({
                title:" 开课操作",
                type:1,
                shadeClose:true,
                area:['400px','120px'],
                skin: 'layui-layer-rim',
                content:$("#openCourse")
            });
        }
        //设置开课状态
        function setIsOpen(obj) {
            $(obj).next("div").toggle();
        }
    </script>



                <style>
                    .link_1, .link_1:hover{ color:#0c3; text-decoration:none;}
                    .link_2, .link_2:hover{ background:#0c3; color:#fff; text-decoration:none; display:inline-block; padding:0 3px; border-radius:3px;}
                </style>


                <div class="row search-row" style="padding:9px 0 15px 15px;">
                    <form class="form-inline" role="form">
                        <div class="form-group">
                            <a class="common-button combg4" href="{{route('admin.registration.list.pay')}}">切换到支付记录</a>
                        </div>
                        <div class="form-group">
                            <input type="text" name="adviserName" class="form-control" placeholder="顾问姓名" value="{{Request::input('adviserName')}}" style="width: 110px;"/>
                            <input type="text" name="name" class="form-control" placeholder="学员姓名" value="{{Request::input('name')}}" style="width: 110px;"/>
                            <input type="text" name="mobile" class="form-control" placeholder="开课手机号" value="{{Request::input('mobile')}}" style="width: 110px;"/>
                            <!--<select name="is_open" class="form-control">
                                <option value="">是否导学</option>
                                <option <present name="_GET[is_open]"><eq name="_GET[is_open]" value="1">selected</eq></present> value="1">是</option>
                                <option <present name="_GET[is_open]"><eq name="_GET[is_open]" value="0">selected</eq></present> value="0">否</option>
                            </select>-->
                        </div>

                        <div class="form-group">
                            <input type="text" id="startdate" name="startDate" class="form-control datetime" style="width:165px;" value="{{Request::input('startDate')}}" placeholder="开始时间" /> 至
                            <input type="text" id="enddate" name="endDate" class="form-control datetime" style="width:165px;" value="{{Request::input('endDate')}}" placeholder="截至时间" />
                        </div>
                        <div class="form-group">
                            <a class="common-button combg1 linkSubmit" href="{{\Illuminate\Support\Facades\URL::current()}}">搜索</a>
                            <elt name="_SESSION['dapeng']['member_auth']['grade']" value="5">
                                <a class="common-button combg2 linkSubmit" href="{:U('exportUserList')}">导出</a>
                            </elt>
                        </div>
                    </form>
                </div>

                <div id="w0" class="grid-view">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>顾问</th>
                            <th>学员</th>
                            <th>开课手机</th>
                            <th>QQ号</th>
                            <th width="130">课程套餐</th>
                            <th>分期</th>
                            <th>开课</th>
                            <th>总金额</th>
                            <th>优惠</th>
                            <th>应交</th>
                            <th>已交</th>
                            <th width="100">提交时间</th>
                            <elt name="_SESSION['dapeng']['member_auth']['grade']" value="5">
                                <th width="60">操作</th>
                            </elt>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($list) > 0)
                            @foreach ($list as $v)
                                <tr class="listCurrent">
                                    <td>{{$v->id}}</td>
                                    <td>{{$v->adviser_name}}</td>
                                    <td>{{$v->name}}</td>
                                    <td>{{$v->mobile}}</td>
                                    <td>{{$v->qq}}</td>
                                    <td>
                                        {{$v->package_all_title}}
                                    </td>
                                    <td>{{$v->fq_type_text}}</td>
                                    <td>
                                        {{$v->is_open_text}}
                                    </td>
                                    <td>{{$v->package_total_price}}</td>
                                    <td>{{floatval($v->rebate)}}</td>
                                    <td>{{$v->package_total_price-$v->rebate}}</td>
                                    <td>{{$v->amount_submitted}}</td>
                                    <td>{{date("Y-m-d H:i:s",$v->last_pay_time)}}</td>
                                        @if($adminInfo['grade'] <= 5)
                                        <td>
                                            <a class="set-is-open-a" onclick="setIsOpen(this)">开课</a>
                                            <div class="set-open">
                                                <dl>
                                                    <dd>
                                                        <a class="ajaxLink" href="{:U('modRegistrationField')}" data="{id:{$v.id},val:'0',field:'is_open'}" warning="开课状态是否确认？">
                                                            未开课
                                                        </a>
                                                    </dd>
                                                    <dd>
                                                        <a class="ajaxLink" href="{:U('modRegistrationField')}" data="{id:{$v.id},val:'1',field:'is_open'}" warning="开课状态是否确认？">
                                                            部分开课
                                                        </a>
                                                    </dd>
                                                    <dd>
                                                        <a class="ajaxLink" href="{:U('modRegistrationField')}" data="{id:{$v.id},val:'2',field:'is_open'}" warning="开课状态是否确认？">
                                                            全部开课
                                                        </a>
                                                    </dd>
                                                </dl>
                                            </div>
                                        </td>
                                    @endif
                                    <!--<td>{:date('Y-m-d H:i:s',$v['create_time'])}</td>-->
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="14" ><div class="pagenav"> <ul>{{ $list->links() }} </ul></div></td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="14">暂无信息</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
    <script>
        function showDetail(obj) {
            var id = $(obj).attr("pay-log-id");
            layer.open({
                type:2,
                title:"查看用户支付信息",
                area:['520px','490px'],
                content: "{:U('Index/userPayDetail')}?pay_log_id="+id
            });

        }
    </script>
@endsection