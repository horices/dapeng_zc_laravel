(function(){
	function Utils(){
		//将字符串转为JS
		this.strtoobj=function(objstr){
			return ((new Function("return "+objstr))());
		}
		/**
		 * 获得方法
		 */
		this.getfn = function(fnname,scope){
			if(typeof fnname == "function"){
				return fnname;
			}else if ( typeof fnname == "string" ){
				if(scope){
					return scope[fnname];
				/*}else if(fnname){
					return (new Function("return "+fnname))();*/
				}else if(window[fnname]){
					return window[fnname];
				}else{
					return function(){};
				}
			}else{
				return function(){};
			}
		}
		//计算textarea字符数 属性count-toal总字数限制
		this.countWords = function (obj) {
			obj = $(obj);
			var totalShow = obj.attr("countTotal");
            $("."+obj.attr("msgTarget")).text(totalShow);

            obj.on("input propertychange", function () {
                var text1 = $(this).val();
                var total = totalShow;
                var len; //记录剩余字符串的长度
                //textarea控件不能用maxlength属性，就通过这样显示输入字符数了
                if (text1.length >= total) {
                    $(this).val(text1.substr(0, total));
                    len = 0;
                } else {
                    len = total - text1.length;
                }
                var show = "你还可以输入" + len + "个字";
                $("."+obj.attr("msgTarget")).text(show);
                //document.getElementById("pinglun").innerText = show;
            })

        }
        /*限制输入框字数 另一种方式*/
        this.countWordsOne = function (obj) {
            obj = $(obj);
            var totalShow = obj.attr("countTotal");
            obj.on("input propertychange", function() {
                var $this = $(this),
                    _val = $this.val(),
                    count = "";
                if(_val.length>=parseInt(totalShow)){
                    $("."+obj.attr("msgTarget")).addClass("red_color");
				}else{
                    $("."+obj.attr("msgTarget")).removeClass("red_color");
				}
                if (_val.length > parseInt(totalShow)) {
                    $this.val(_val.substring(0, parseInt(totalShow)));
                }
                count = $this.val().length;
                $("."+obj.attr("msgTarget")).text(count);
            });
        }
		/**
		 * 将JSON转为url 参数
		 */
		this.parseParam = function(param, key) {
		    var paramStr = "";
		    var _this = this;
		    if (param instanceof String || param instanceof Number || param instanceof Boolean) {
		        paramStr += "&" + key + "=" + encodeURIComponent(param);
		    } else {
		        $.each(param, function(i) {
		            var k = key == null ? i : key + (param instanceof Array ? "[" + i + "]" : "." + i);
		            paramStr += '&' + _this.parseParam(this, k);
		        });
		    }
		    return paramStr.substr(1);
		};
		//设置分享内容
		this.setShare = function (shareParams) {
            if(!window.mobShare){
                setTimeout(function () {
                    this.setShare(shareParams);
                },100);
                return ;
            }
            //设置分享内容
            mobShare.config( {
                debug: true, // 开启调试，将在浏览器的控制台输出调试信息
                appkey: '1e38dec26e4fe', // appkey
                params: {
                    url: shareParams.url, // 分享链接
                    title: shareParams.title, // 分享标题
                    description: shareParams.description, // 分享内容
                    pic: shareParams.pic, // 分享图片，使用逗号,隔开
                    //reason:'',//自定义评论内容，只应用与QQ,QZone与朋友网
                },
                /**
                 * 分享时触发的回调函数
                 * 分享是否成功，目前第三方平台并没有相关接口，因此无法知道分享结果
                 * 所以此函数只会提供分享时的相关信息
                 * @param {String} plat 平台名称
                 * @param {Object} params 实际分享的参数 { url: 链接, title: 标题, description: 内容, pic: 图片连接 }
                 */
                callback: function( plat, params ) {

                }
            } );
            // 实例一个新浪微博的分享对象
            var weibo = mobShare( 'weibo' );
            // 然后通过用户事件触发分享（浏览器限制原因，打开新窗口必需通过用户事件触发）
            // 分享到新浪微博
            $( '.share-weibo' ).click( function() {
                weibo.send();
            });
            //分享到qq空间
            var qzone = mobShare( 'qzone' );
            $(".share-qzone").click(function () {
                qzone.send();
            });
            //微信分享
            var weixin = mobShare( 'weixin' );
            $(".share-weixin").click(function () {
                CustomDialog.closeDialog();
                $(".share_fs").hide();
				if(this.isWxBrowser()){
					document.getElementById('mcover').style.display='block';
					$("#mcover").click(function () {
						$(this).hide();
					});
					$(".-mob-share-weixin-qrcode-content").hide();
					return false;
				}else{
					weixin.send();
					var qrc = $(".-mob-share-weixin-qrcode").attr("src");
					$(".-mob-share-weixin-qrcode-content").hide();
					CustomDialog.dialog({
						type: 1,
						anim: 'up',
						title:"微信分享二维码",
						skin:"",
						style:"border:1px solid;padding:4px",
						time:false,
						content:"<img src='"+qrc+"'>"
					});
				}
            });
        };
        //判断是否是微信浏览器
		this.isWxBrowser = function () {
            var ua = window.navigator.userAgent.toLowerCase();
            if(ua.match(/MicroMessenger/i) == 'micromessenger'){
                return true;
            }else{
                return false;
            }
        }
		//合并JS数组
		this.mergeArray = function(a,b){
			for(var i= 0;i<b.length;i++){
				a.push(b[i]);
			}
		}
		//判断是否为一个空对象
		this.isEmpty = function(a){
			return !Object.keys(a).length;
		}
		this.getQueryString = function (name)
		{
			var params = this.getAllParams();
			return params[name] || "";
		}
		/**
		 * 获取地址中所有的参数
		 */
		this.getAllParams = function(url){
			if(!url){
				url = window.location.href;
			}
			var hash = "";
			var paramsStr = "";
			var paramsArr = [];
			if(url.indexOf('#') >= 0){
				hash = url.substr(url.indexOf('#'));
				url = url.substr(0,url.indexOf('#'));
			}
			var paramsObj = {
					'hash#':hash
			};
			if(url.indexOf("?")>=0){
				paramsStr = url.substr(url.indexOf("?")+1);
				paramsArr = paramsStr.split('&');
				var temp ;
				for(var i=0;i<paramsArr.length;i++){
					temp = paramsArr[i].split('=');
					paramsObj[temp[0]] = temp[1] || "";
				}
			}
			return paramsObj;
		}
		/**
		 * 创建一个新的URL
		 */
		this.createUrl = function(params,url){
			if(!url){
				url = window.location.href;
			}
			var paramsObj = this.getAllParams();
			var baseUrl = url;
			if(url.indexOf('?')>0){
				baseUrl = url.substr(0,url.indexOf('?'));
			}
			baseUrl += "?";
			for(var x in params){
				paramsObj[x] = params[x];
			}
			//低版本androis 不支持
			//paramsObj = Object.assign(paramsObj,params || {});
			for(key in paramsObj){
				if(key && key != "hash#")
					baseUrl+="&"+key+"="+paramsObj[key];
			}
			baseUrl+=paramsObj['hash#'];
			return baseUrl;
		}
		/**
		 * 修改一个页面的地址
		 */
		this.changeUrl = function(obj,replace){
			if(replace == true){
				history.replaceState(obj,'',obj.url);
			}else{
				history.pushState(obj,'',obj.url);
			}
		}
	}
	window.Utils = new Utils;
})();