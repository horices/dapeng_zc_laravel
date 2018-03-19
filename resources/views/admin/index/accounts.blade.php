@extends("admin.public.layout")
@section("right_content")
    <style>
        #tab {
            height: 30px;
            margin-bottom: 60px;
        }

        #tab li {
            float: left;
            margin-right: 50px;
            font: 18px/30px Tahoma;
            cursor: pointer;
        }

        #tab li.cur {
            border-bottom: 3px #0c3 solid;
            color: #0c3;
        }

        #step_1 {
            margin: 0 0 0 40px;
        }

        #step_1 .s1 {
            height: 24px;
        }

        #step_1 .s1 em {
            display: inline-block;
            width: 90px;
            text-align: left;
            padding-right: 12px;
            font: 700 14px Tahoma;
        }

        #step_1 .btn {
            display: inline-block;
            border: 1px #0c3 solid;
            padding: 3px 6px;
            color: #0c3;
            margin-left: 30px;
        }

        #step_2 {
            margin: 0 0 0 40px;
        }

        #step_2 .s2 {
        }

        #step_2 .s2 em {
            display: inline-block;
            width: 120px;
            text-align: right;
            padding-right: 12px;
            font: 700 14px Tahoma;
        }

        #step_2 {
        }
    </style>
    <ul id="tab">
        <li class="cur"
            onClick="$(this).addClass('cur').siblings().removeClass('cur'); $('#step_2').hide(); $('#step_1').show();">
            个人资料
        </li>
        <li onClick="$(this).addClass('cur').siblings().removeClass('cur'); $('#step_1').hide(); $('#step_2').show();">
            安全中心
        </li>
    </ul>

    <div id="step_1" style="display:">
        <p class="s1"><em>ID：</em>{{ $userInfo->uid }}</p>
        <p class="s1"><em>姓名：</em>{{ $userInfo->name }}</p>
        <p class="s1"><em>QQ号码：</em>{{ $userInfo->qq }}</p>
        <p class="s1"><em>手机号码：</em>{{ $userInfo->mobile }} <a class="btn" style="display:none"
                                                                        onClick="$('#tab li').eq(1).trigger('click');">修改手机号码</a>
        </p>
    </div>

    <div id="step_2" style="display:none">
        <form role="form" id="regForm" class="form-inline">
            <p class="s2"><em>手机号：</em><input type="text" id="mobile" name="mobile" class="form-control" maxlength="20"
                                              style="width:300px" value="{:session('member_auth.mobile')}"
                                              placeholder="请输入新手机号"></p>
            <p class="s2"><em>验证码：</em><input type="text" id="dp-code" name="up-code" class="form-control" maxlength="4"
                                              style="width:100px" placeholder="">&nbsp;&nbsp;<button
                        class="btn btn-primary" id="sendSms" type="button">获取验证码
                </button>
            </p>
            <!--<p class="s2"><em>QQ号：</em><input type="text" id="qq" name="qq" class="form-control" maxlength="20" style="width:300px" value="{:session('member_auth.qq')}" placeholder="请输入新qq"></p>-->
            <p class="s2"><em>输入新密码：</em><input type="password" name="password" class="form-control" maxlength="16"
                                                style="width:300px" placeholder="不修改密码请留空,6-16位"></p>
            <p class="s2"><em>确认新密码：</em><input type="password" name="repassword" class="form-control" maxlength="16"
                                                style="width:300px" placeholder="不修改密码请留空,6-16位"></p>
            <p class="s2" style="padding-left:100px; padding-top:12px;">
                <button class="btn btn-primary ajaxSubmit" type="button" style="width:230px;">确认修改</button>
            </p>
        </form>
    </div>
@endsection