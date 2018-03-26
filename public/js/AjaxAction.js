/**
 * require jquery.js CustomDialog.js Utils.js
 */
/**
 * ajax 请求配置
 * @returns
 */
(function(){
	var defaults = {
			event:{
				//添加初始化方法
				init:function(){
				},
				uninit:function(){
				},
				ajaxBefore:function(){
					NProgress.start();
				},
				ajaxSuccess:function(){
					NProgress.done();
				},
				ajaxAfter:function(){
				}
			}
	};
	function AjaxAction(options){
		var _this = this;
		_this.options = $.extend({},defaults,options);
		/**
		 * 绑定所有的ajax操作
		 */
		_this.init = function(){
			//保存备份的 html
			_this.html = $(".ajaxLoadKeepHtml").prop("outerHTML");
			//初始化函数
			//console.log(_this.options.event.init);
			//console.log(_this.getfn(_this.options.event.init));
			_this.getfn(_this.options.event.init)();
			_this.bindAjax();
		}
		/**
		 * 绑定操作
		 */
		_this.bindAjax = function(){
			//把所有的ajaxLink的链接转为ajax提交
		    $(".ajaxLink").each(function(){
		    	_this.bindAjaxLinkAction($(this));
		    });
		    //绑定异步加载事件
		    $(".ajaxLoad").each(function(){
		    	_this.bindAjaxLoadAction($(this));
		    });
		    //绑定异步提交事件
		    $(".ajaxSubmit").each(function(){
		    	_this.bindAjaxSubmitAction($(this));
		    });
		    //绑定提交事件
		    $(".linkSubmit").each(function(){
		    	_this.bindLinkSubmitAction($(this));
		    });
		    //绑定重置事件
		    $(".resetForm").each(function(){
		       $(this).attr("href","javascript:;");
		    });
		    $(".resetForm").click(function(){
		        $(this).parents("form")[0].reset();
		    });
		    $(".ajaxUpload").each(function(){
				$(this).attr("href","javascript:;");
			});
			$(".ajaxUpload").unbind("click").click(function(){
				openUpload($(this));
				return false;
			})
		    $(".back").each(function(){
		    	$(this).attr("href","javascript:;");
		    	$(this).unbind("click").click(function(){
		    		if(Utils.getQueryString('dapengWebViewFirstPage') == "1"){
		    			if(window['dsBridge']){
		    				dsBridge.call("exitDapengWebViewFirstPage");
		    			}
		    		}else{
		    			history.back();
		    		}
		    	});
		    })
		    window.onscroll = null;
		    $(".slideAjax").each(function(){
		    	_this.bindSlideAjax($(this));
		    })
		}
		/**
		 * 发送AJAX请求
		 * @param obj 请求对象
		 * @param successCallback function||String	请求成功后的回调方法，只允许回调匿名方法或本类方法
		 */
		_this.ajaxAction = function(obj,successCallback){
			obj.attr("processing","true");
			var obj = $(obj);
			var data = $.extend({},(new Function("return " + obj.attr("addData")))(),(new Function("return " + obj.attr("data")))());
			var options = {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
				url:obj.attr('url') || location.pathname,
		        type:obj.attr('method') || "post",
		        data:data,
		        success:function(data){
                    _this.resetFlag(obj);
		        	if(data.replaceState && data.url){
		        		var stateObj = {
		        				title:"测试",
		        				url:data.url
		        		}
		        		history.replaceState(stateObj,"测试",data.url);
		        	}
		        	if(_this.getfn(_this.options.event.ajaxSuccess)() === false){
		        		return false;
		        	}
		        	if(successCallback){
		        		if(typeof successCallback == "function"){
			        		successCallback(data,obj);
			        	}else{
			        		_this.getfn(successCallback,_this)(data,obj);
			        	}
		        	}else{
		        		_this.ajaxReturn(data,obj);
		        	}
		        },
		        error:function(data){
		        	if(data.status == 302){
		        		_this.ajaxLoadAction("<a url='"+data.getResponseHeader('X-Redirect')+"'></a>");
		        	}else{
		        		_this.resetFlag(obj);
		    		    CustomDialog.failDialog("请求失败");
		        	}
		        }
			}
			if(obj.attr("dataType")){
				options.dataType = obj.attr("dataType");
			}
			_this.ajax(options);
		},
		_this.ajax = function(options,callback){
			$.ajax(options);
		},
        /**
		 * 重置标识
         * @param obj
         */
		_this.resetFlag = function(obj){
            if(obj && obj.attr){
                if(obj.attr("interval")){
                    setTimeout(function(){
                        obj.attr("processing","false");
                    },parseInt(obj.attr("interval")));
                }else{
                    obj.attr("processing","false"); //取消正在处理数据标识
                }
            }
            if(obj.attr("keepDialog") != 'true'){
                CustomDialog.closeDialog();
            }
		}
		/**
		 * ajax 默认回调
		 */
		_this.ajaxReturn = function (data,obj){
		    //设置默认回调方法后不再执行默认回调
		    if(obj && obj.attr && obj.attr("callback")){
		    		var callback = obj.attr("callback");
		    		_this.getfn(callback)(data,obj);
		            return ;
		    }
		    if(!data){
		    	CustomDialog.failDialog("请求超时");
		    	return ;
		    }
		    if(typeof data == "string"){
		    		CustomDialog.failDialog(data);
		            return ;
		    }
			var time = 0;
			var url = data.url;
			if(data.code){
				CustomDialog.successDialog(data.msg);
			}else{
				CustomDialog.failDialog(data.msg);
			}
			time = 1500;
			if(data.location === false || data.code == 0){
				return ;
			}
			setTimeout(function(){
				switch(url){
					case "back":
						history.back();
						return ;
					break;
				}
				if(url){
		        	if(location == 'ajax'){
		        		_this.ajaxLoadAction("<a url='"+url+"' storage='false'></a>");
		        	}else{
		        		top.location.href = url;
		        	}
		        }else{
		        	if(location == 'ajax'){
		        		_this.ajaxLoadAction("<a url='"+location.href+"' storage='false'></a>");
		        	}else{
		        		top.location.reload();
		        	}
		        }
			},time);
		}
		/**
		 * 绑定异步链接
		 * @param {type} obj
		 * @returns {undefined}
		 */
		_this.bindAjaxLinkAction = function (obj){
		    $(obj).unbind('click').bind('click',function(){
		    	_this.ajaxLinkAction(obj);
		        return false;
		    });
		}
		/**
		 * 异步提交链接动作
		 * @param {type} obj
		 * @returns {Boolean}
		 */
		_this.ajaxLinkAction = function (obj,successCallback){
		    var obj = $(obj);
		    var data = '';
		    if(_this.beforeBaseAjaxAction(obj) === false){
		    	return false;
		    }
		    var options = {
		    		yes:function(){
                        if(obj.attr("showloading")){
                            CustomDialog.loadingDialog();
                        }
		    			if(_this.getfn(_this.options.event.ajaxBefore)(obj) === false){
		    				return false;
		    			}
		    		    _this.ajaxAction(obj,successCallback);
		    		}
		    };
		    if(obj.attr("warning")){
		    	options.content = obj.attr("warning")
		    	CustomDialog.confirmDialog(options);
		    }else{
		    	options.yes();
		    }
		}
		/**
		 * 绑定链接提交表单事件
		 * @param {type} obj
		 * @returns {undefined}
		 */
		_this.bindLinkSubmitAction = function (obj){
		    var href=obj.attr("href");
		    var form = obj.parents("form");
		    var action = form.attr("action");
		    if(href && href !=='#' && href !== ':;' && href !== 'javascript:;' && href !== 'javascript:void();' && href !== 'void();' && href !== 'void' && href !== "javascript:void(0)"){
		        action = href;
		     }
		     obj.attr("href","javascript:;");
		     obj.unbind('click').bind('click',function(){
		        form.attr("action",action);
		        if(_this.getfn(obj.attr("beforeAction"))(obj) === false){
		        	return false;
		        }
		        var options = {
		        	yes:function(){
		        		var exparams = Utils.strtoobj(obj.attr('data'));
		        		var node = [];
		        		var input = '';
                        for(var k in exparams){
                        	input = $("<input name='"+k+"' type='hidden' value='" +exparams[k]+ "' />");
                        	node.push(input);
                        	form.prepend(input);
						}
		        		form[0].submit();
                        if(obj.attr("showloading")){
                            CustomDialog.loadingDialog();
                        }
                        for(var i = 0 ;i<node.length;i++){
                            node[i].remove();
						}
		        	}
		        };
		        if(obj.attr("warning")){
		        	options.content = obj.attr("warning");
		        	CustomDialog.confirmDialog(options);
		        }else{
		        	options.yes();
		        }
		     });
		}
		/**
		 * 绑定异步提交事件
		 * @param {type} obj
		 * @returns {undefined}
		 */
		_this.bindAjaxSubmitAction = function (obj){
		    $(obj).unbind('click').bind('click',function(){
		    	_this.ajaxSubmitAction(obj);
		        return false;
		    });
		}
		/**
		 * 异步提交表单
		 * @param {type} obj
		 * @returns {undefined}
		 */
		_this.ajaxSubmitAction = function (obj){
		    var obj = $(obj);
		    var form,data = {},formData = {};
		    if(_this.beforeBaseAjaxAction(obj) === false){
		    	return false;
		    }
		    obj.attr("processing","true");
		    if(obj.attr("formTarget")){
		            form = $(obj.attr("formTarget"));
		    }else{
		            form = obj.parents("form").eq(0);
		    }
		    form.serializeArray().map(function(x){formData[x.name] = x.value;});
		    obj.attr("url") || obj.attr("url",form.attr("action"));
		    obj.attr("addData",JSON.stringify(formData));
		    var options = {
		    		yes:function(){
		    			if(obj.attr("showloading")){
		    				CustomDialog.loadingDialog();
		    		    }
		    		    if(_this.getfn(_this.options.event.ajaxBefore)(obj) === false){
		    				return false;
		    			}
		    		    _this.ajaxAction(obj);
		    		}
		    };
		    if(obj.attr("warning")){
		    	options.content = obj.attr("warning");
		    	CustomDialog.confirmDialog(options);
		    }else{
		    	options.yes();
		    }
		}
		/**
		 * 异步获取内容链接
		 * @param {type} obj
		 * @returns {undefined}
		 */
		_this.bindAjaxLoadAction = function (obj){
		    obj.attr("href","javascript:;");
		    //$(obj).attr("dataTarget") || $(obj).attr("dataTarget","#admin_content");
		    $(obj).attr("storage") || $(obj).attr("storage","true");
		    $(obj).attr("method") || $(obj).attr("method","get");
		    $(obj).unbind('click').bind('click',function(){
		    	_this.ajaxLoadAction(obj);
		        return false;
		    });
		}
		/**
		 * 异步加载
		 * @param {objetc} obj
		 * @param {int} notstorage
		 * @returns {undefined}
		 */
		_this.ajaxLoadAction = function (obj,notstorage){
			var obj = $(obj);
			//$(obj).attr("dataTarget") || $(obj).attr("dataTarget","body");
		    $(obj).attr("storage") || $(obj).attr("storage","true");
		    $(obj).attr("method") || $(obj).attr("method","get");
		    if(_this.beforeBaseAjaxAction(obj) === false){
		    	return false;
		    }

		    var options = {
		    		yes:function(){
		    			if(_this.getfn(_this.options.event.ajaxBefore)(obj) === false){
							return false;
						}
					    obj.attr("showloading") || obj.attr("showloading",1);
					    //执行跳转前回调
					    _this.exfn(_this.options.event.uninit)();
					    //统一加载前回调
					    _this.exfn(_this.options.event.beforeAjaxLoadAction)();
					    _this.ajaxAction(obj,function(data){
					    	if(obj.attr("storage") == "true" && typeof data == "string"){
					    		var url = obj.attr("url");
					    		if(url.indexOf('?') == -1){
					    			url+="?";
					    		}
					    		url+="&";
					    		if(obj.attr("addData")){
					    			url+=Utils.parseParam(Utils.strtoobj(obj.attr("addData")));
					    			url+="&";
					    		}
					    		if(obj.attr("data")){
					    			url+=Utils.parseParam(Utils.strtoobj(obj.attr("data")));
					    		}
					    		//还原地址栏 支除最后一位的?&
					    		if(url.charAt(url.length-1) == '&'){
					    			url = url.substr(0,url.length-1);
					    		}
					    		if(url.charAt(url.length-1) == '?'){
					    			url = url.substr(0,url.length-1);
					    		}
					    		var stateObj = {
					    				title:"测试",
					    				url:url
					    		}
					    		history.pushState(stateObj,"测试",url);
					    		if(obj.title){
					    			document.title=obj.title
					    		}
					    		//统一加载后回调
					    		_this.exfn(_this.options.event.afterAjaxLoadAction)(obj,url);
					    	}
					    	if(obj.attr("callback")){
					    		return _this.getfn(obj.attr("callback"))(data,obj);
					    	}else{
					    		return _this.getfn("defaultAjaxLoadCallback",_this)(data,obj);
					    	}
					    });
		    		}
		    }
		    if(obj.attr("warning")){
		    	options.content = obj.attr("warning");
		    	CustomDialog.confirmDialog(options);
		    }else{
		    	options.yes();
		    }
		}
		/**
		 * 异步装载页面默认回调
		 */
		_this.defaultAjaxLoadCallback = function (data,obj){
			if(obj && obj.attr){
				setTimeout(function(){
					obj.attr("processing","false"); //取消正在处理数据标识
				},1000);
		    }
			if(typeof data == "object"){
				if(data.script){
					//允许执行JS代码
					eval(data.script);
				}
				if(data.msg){
					if(data.code == 1){
						CustomDialog.successDialog(data.msg);
					}else{
						CustomDialog.failDialog(data.msg);
					}
				}
				if(data.url){
					setTimeout(function(){
						//在当前目标中创建一个新的DIV
						AjaxAction.ajaxLoadAction($("<a url='"+data.url+"'></a>"));
					},2000);
				}
				return ;
			}else{
				//返回字符串时，填充到指定区域
				var target = $(obj).attr("dataTarget") || "body";
				//清空之前
				var fn = _this.options.event.init;
				var unloadfn = _this.options.event.uninit;
				$(window).unbind("scroll");
				window[fn] = null;
				window[unloadfn] = null;
				_this.options.event.init = fn;
				_this.options.event.uninit = unloadfn;
				//重新加载代码 初始化
				$(target).html(data);
				$(target).append(_this.html);
				_this.init();//重新初始化ajax事件
				return ;
			}
		}
		/**
		 * 执行指定的方法
		 */
		_this.exfn = function(fnname,scope){
			//如果是方法 ，则直接返回该方法
			return _this.getfn(fnname,scope);
		}
		/**
		 * 获得方法
		 */
		_this.getfn = function(fnname,scope){
			return Utils.getfn(fnname,scope);
		}
		/**
		 * 异步提交前操作
		 * @param obj
		 */
		_this.beforeBaseAjaxAction = function (obj){
			$(obj).attr("interval") || $(obj).attr("interval","1500");
			if(obj.attr("processing") == "true"){
				CustomDialog.failDialog("正在处理数据，请稍候。。。");
				return false;
			}
			if(obj && obj.attr && obj.attr("beforeAction")){
				if(_this.getfn(obj.attr("beforeAction"))(obj) === false){
					return false;
				}
		        //var fn = eval(obj.attr("beforeAction"));
		        //if(fn(obj)===false){return false;}
			}
		}
		/**
		 * 绑定上拉加载
		 */
		_this.bindSlideAjax = function(obj){
			var obj = $(obj);
			var scrollHeight,scrollTop,clientHeight;
			obj.attr("page") || obj.attr("page",1);
			obj.attr("pagesize") || obj.attr("pagesize",10);
			obj.attr("storage") || obj.attr("storage","false");
			obj.attr("direction") || obj.attr("direction","down");

			function scrollCallback(){
				alert("scrollCallback");
			}
			if(obj.attr("el")){
				//触发指定元素的滚动条
				var o;
				if(obj.attr("el") == "this"){
					o = obj;
				}else{
					o = $(obj.attr("el"));
				}
				o.scroll(function(){
					clientHeight = o.height();
					scrollHeight = o[0].scrollHeight;
					scrollTop = o.scrollTop();
					if(scrollHeight<clientHeight+scrollTop+100){
						_this.slideAjaxAction(obj);
					}
				});
			}else{
				//触发window的滚动条
				$(window).scroll(function(){
					clientHeight = $(window).height();
					scrollHeight = $(document).height();
					scrollTop = $(document).scrollTop();
					if(scrollHeight<clientHeight+scrollTop+100){
						_this.slideAjaxAction(obj);
					}
				});
			}
		}
		/**
		 * 异步加载数据，
		 */
		_this.slideAjaxAction = function(obj){
			obj = $(obj);
			if(obj.length >1){
				obj.each(function(){
					_this.slideAjaxAction($(this));
				});
				return ;
			}
			if(typeof obj !== "object"){
				console.error("slideAjaxAction 非法参数");
				return ;
			}
			if(obj.attr("allowAjaxLoad") == "false"){
				return ;
			}
			if(obj.attr("empty") == "true"){
				return ;
			}
			if(obj.attr("slideLoading") == "1"){
				return ;
			}
			obj.attr("slideLoading",1);
			obj.attr("page") || obj.attr("page",1);
			obj.attr("pagesize") || obj.attr("pagesize",10);
			var json = {
					page:obj.attr("page") || 1,
					pagesize:obj.attr("pagesize") || 10
			}
			obj.attr("addData",JSON.stringify(json));
			$(obj).attr("interval") || $(obj).attr("interval","0");
			_this.ajaxLinkAction(obj,function(json,obj){
				obj.attr("slideLoading",0);
				obj.attr("page",parseInt(obj.attr("page"))+1);
				_this.ajaxReturn(json,obj);
			});
		}
	}
	window.AjaxAction = new AjaxAction;
})();
/**
 * 修改后退，用 AJAX 方式获取最新数据
 */
window.onpopstate = function(event) {
	//先执行销毁方法
	AjaxAction.exfn(AjaxAction.options.event.uninit)();
	AjaxAction.ajaxLoadAction($("<a url='"+location.href+"' storage='false'></a>"));
	return true;
};
