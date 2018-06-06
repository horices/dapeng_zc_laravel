@extends("admin.public.layout")
@section("right_content")
    <script type="text/javascript">
        $(function(){
            //列表点击变色
            $(".listCurrent").click(function () {
                $(this).css('background-color','darkgrey').siblings().css('background-color','')
            })
        });

        /**
         * 查看该QQ的开课记录
         */
        function openCourseList(obj){
            var obj = $(obj);
            var qq = obj.attr("qq");
            $.post("{:U('getQQCourseList')}",{roster_id:obj.attr("roster_id")},function(data){
                $("#courseList").empty().html(data);
                console.log($(data).find("li").size());
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
    </script>
                <style>
                    .link_1, .link_1:hover{ color:#0c3; text-decoration:none;}
                    .link_2, .link_2:hover{ background:#0c3; color:#fff; text-decoration:none; display:inline-block; padding:0 3px; border-radius:3px;}
                </style>


    <div class="row search-row" style="padding:9px 0 0px 15px;">
        <div class="row dp-member-title-2">
            <h4 class="col-md-4">支付记录：</h4>
        </div>
        <form class="form-inline" role="form">
            <div class="form-group">
                <a class="common-button combg4" href="{{route('admin.registration.list.user')}}">切换到用户统计</a>
            </div>

            <div class="form-group">
                @if($adminInfo['grade']<9)
                <input type="text" name="adviserName" class="form-control" placeholder="顾问姓名" value="{{Request::input('adviserName')}}" style="width: 110px;"/>
                @endif
                <input type="text" name="name" class="form-control" placeholder="学员姓名" value="{{Request::input('name')}}" style="width: 110px;"/>
                <input type="text" name="mobile" class="form-control" placeholder="开课手机号" value="{{Request::input('mobile')}}" style="width: 100px;"/>
                <!--<select name="is_open" class="form-control">
                    <option value="">是否导学</option>
                    <option <present name="_GET[is_open]"><eq name="_GET[is_open]" value="1">selected</eq></present> value="1">是</option>
                    <option <present name="_GET[is_open]"><eq name="_GET[is_open]" value="0">selected</eq></present> value="0">否</option>
                </select>-->
            </div>

            <div class="form-group">
                <input type="text" id="startdate" name="startDate" class="form-control select_date" style="width:165px;" value="{{Request::input('startDate')}}" placeholder="开始时间" /> 至
                <input type="text" id="enddate" name="endDate" class="form-control select_date" style="width:165px;" value="{{Request::input('endDate')}}" placeholder="截至时间" />
            </div>
            <div class="form-group">
                <a class="common-button combg1 linkSubmit" href="{{\Illuminate\Support\Facades\URL::current()}}">搜索</a>
                @if($adminInfo->grade <= 5)
                    <a class="common-button combg2 linkSubmit" data="{'export':'1'}" showloading="true">导出</a>
                @endif
            </div>
        </form>
    </div>
    <div class="row search-row" style="padding: 0px 0px 10px 18px;">
        本月统计：{{floatval($allSubmitAmount)}}
    </div>

    <div id="w0" class="grid-view">
        <table class="table">
            <thead>
            <tr>
                <th width="50">序号</th>
                @if($adminInfo['grade'] <= 5)<th width="80">顾问</th>@endif
                <th width="80">学员</th>
                <th>开课手机</th>
                <th>QQ/微信</th>
                <th width="120">套餐名称</th>
                <th>应交总金额</th>
                <th>已交总金额</th>
                <th>设计学院</th>
                <th>美术学院</th>
                <th width="80">支付方式</th>
                <th>支付时间</th>
                <th>提交时间</th>
                <th width="80">操作</th>
            </tr>
            </thead>
            <tbody>
            @if (count($list) > 0)
                @foreach ($list as $k=>$v)
                    <tr class="listCurrent">
                        <td>{{ $loop->index + 1 }}</td>
                        @if($adminInfo['grade'] <= 5)<td>{{$v->adviser_name}}</td>@endif
                        <td>{{$v->name}}</td>
                        <td>{{$v->mobile}}</td>
                        <td>{{$v->account}}</td>
                        <td>
                            @foreach($v->userRegistration as $l)
                                {{$l->package_all_title}}<br/>
                            @endforeach
                        </td>
                        <td>{{$v->total_should_price}}</td>
                        <td>{{$v->total_submitted_price}}</td>
                        <td>{{$v->submitted_price['SJ']}}</td>
                        <td>{{$v->submitted_price['MS']}}</td>
                        <td>{{$v->last_pay_type_text}} </td>
                        <td>{{$v->last_pay_time_text}}</td>
                        <td>{{$v->create_time}}</td>
                        <td style="text-align: center">
<a href="{{route("admin.registration.add",['mobile'=>$v->mobile,'back_url'=>\Illuminate\Support\Facades\URL::full()])}}">查看</a>
                                @if($adminInfo['grade'] <= 5)
                                    |<a url="" class="ajaxLink" warning="确认删除？">删除</a>
                                @endif
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="13" ><div class="pagenav"> <ul>{{$list->appends(Request::input())->links()}} </ul></div></td>
                </tr>
            @else
                <tr>
                    <td colspan="13">暂无信息</td>
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