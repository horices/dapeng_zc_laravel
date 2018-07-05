@extends("admin.public.layout")
@section("right_content")
    <script>
        var isValidate = false;
        var validateMsg = "等待验证数据...";
        $(function(){
            $("input[name='roster_type']").click(function () {
                $(".select_group").attr("group_type",$(this).val());
            });
            $("input[name='roster_type'][value='1']").click();
        });
        function selectUserCallback(user){
            $("input[name='seoer_id']").val(user.uid);
            $("input[name='seoer_name']").val(user.name);
        }
        function selectGroupCallback(group){
            $("input[name='group']").val(group.qq_group);
            $("input[name='qq_group_id']").val(group.id);
        }
        function validateQQ(obj) {
            if($("input[name='roster_type']:checked").val() != 1)
                return ;
            obj.value = obj.value.replace(/\D/ig, "");
        }
        function checkRosterStatus() {
            var value = $("input[name='roster_no']").val();
            var roster_type = $("input[name='roster_type']:checked").val();
            AjaxAction.ajaxLinkAction("<a url='{{ route("admin.roster.check-roster-status") }}' data=\"{roster_no:'"+value+"',roster_type:'"+roster_type+"'}\" showloading='true'></a>",function(data){
                if(!data.code){
                    CustomDialog.failDialog(data.msg);
                    validateMsg = data.msg;
                }else{
                    CustomDialog.successDialog(data.msg);
                    isValidate = true;
                }
            })
        }
        function checkRosterValidated(obj){
            if(!isValidate){
                    CustomDialog.failDialog(validateMsg);
                return false;
            }
        }
    </script>
        <form method="post">
            <input type="hidden" name="is_admin_add" value="1" />
            <input type="hidden" name="seoer_id" value="" />
            <input type="hidden" name="qq_group_id" value="" />
            {{ csrf_field() }}
            <div class="form-group ">
                <label for="">新量类型:</label>
                <div class="row">
                    <div class="col-lg-2">
                    @foreach($rosterType as $k => $type)
                        <label class="radio-inline">
                            <input type="radio" class="" name="roster_type" value="{{ $k }}">{{ $type }}
                        </label>

                    @endforeach
                    </div>
                </div>
            </div>
            <div class="form-group ">
                <label for="">新量号码:</label>
                <div class="row">
                    <div class="col-lg-3">
                        <input type="text" class="form-control " name="roster_no" placeholder="填写新量号码" onkeyup="validateQQ(this);" onblur="checkRosterStatus(this.value)">
                    </div>
                </div>
            </div>
            <div class="form-group ">
                <label for="">填写推广专员:</label>
                <div class="row">
                    <div class="col-lg-3">
                        <input type="text" class="form-control" name="seoer_name" placeholder="请选择推广专员" readonly>
                    </div>
                    <button type="button" class="btn btn-info select_seoer">选择推广专员</button>
                </div>

            </div>
            <div class="form-group ">
                <label for="">填写群号:</label>
                <div class="row">
                    <div class="col-lg-3">
                        <input type="text" class="form-control" name="group" placeholder="不选择时，进入自动分配" readonly>
                    </div>
                    <button type="button" class="btn btn-info select_group" group_type="1">选择群号码</button>
                </div>
            </div>

            <div class="form-group ">
                <button class="btn btn-info ajaxSubmit" showloading="true" type="button" beforeAction="checkRosterValidated">提交</button>
            </div>
          <style>
                .success-notice{ background:#E0FFE4; border:1px #76E77F solid; width:415px; border-radius:8px; padding:0px 20px; color:#2B6330; margin:30px 0 0 15px}
                .success-notice p{ margin:18px 0;}
                .success-notice .s2 em{ font:700 18px Tahoma; margin-right:15px;}

                .error-notice{ background:#FDE6E7; border:1px #F9B2B2 solid; width:415px; border-radius:8px; padding:0px 20px; color:#2B6330; margin:30px 0 0 15px}
                .error-notice p{ margin:18px 0;}
                .error-notice .s2 em{ font:700 18px Tahoma; margin-right:15px;}
                </style>

                <div id="success" class="success-notice hidden">
                    <p class="s1">QQ号码已成功提交！</p>
                    <p class="s2">请加QQ群 <em id="qq_group">7855382</em>  <!--button class="btn btn-primary" type="button" id="clipboarder" onClick="copy(this)">点击复制QQ群号</button--></p>
                    <p class="s3">请告知该QQ号码加入QQ群，完成流量提交！ </p>
                    <p class="s4" style="text-align:right"><button class="btn btn-primary" type="button" onClick="location.href='/Member/Index/my_data_12'">确认</button></p>
                </div>
                <empty name="qqGroup">

                </empty>
            </fieldset>
        </form>
@endsection