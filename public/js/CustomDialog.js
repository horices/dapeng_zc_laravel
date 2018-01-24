/**
 * 自定义切换弹窗
 */
(function(){
	function CustomDialog(options){
		var defaults = {
			title:false,
			type:0,
			time:1500,
			content:false,
			shade:0.3
		};
		/**
		 * 普通提示弹窗
		 */
		this.dialog = function(options){
			var settings = $.extend({},defaults,options);
			return layer.msg('',settings);
		}
		/**
		 * 弹出页面层
		 */
		this.divDialog = function(options){
			if(typeof options == "string"){
				temp = {};
				temp.content = options;
				options = temp;
			}
			options.type = 1;
			options.style="background-color:transparent";
			return layer.open(options);
		}
		/**
		 * 成功信息
		 */
		this.successDialog = function(msg){
			var options = {
				//title:"操作成功",
				content:msg || "操作成功",
				icon:1
			};
			return this.dialog(options);
		}
		/**
		 * 失败信息
		 */
		this.failDialog = function(msg){
			var options = {
					//title:"操作失败",
					content:msg || "操作失败",
					icon:2
				};
			return this.dialog(options);
		}
		/**
		 * 警告信息
		 */
		this.warnDialog = function(msg){
			var options = {
					//title:"请谨慎操作",
					content:msg || "请谨慎操作",
					icon:1
				};
			return this.dialog(options);
		}
		/**
		 * 确认信息
		 */
		this.confirmDialog = function(msg){
			var confirmResult = '';
			layer.open({
				title:"确实进行操作么",
				btn:['确认','取消'],
				skin:null,
				yes:function(){
					confirmResult = true;
				},
				no:function(){
					confirmResult = false;
				}
			});
			return confirmResult === true;
			//return this.dialog(options);
		}
		/**
		 * 正在加载对话框
		 */
		this.loadingDialog = function(msg){
			return layer.load(1);
			//return this.dialog(options);
		}
		/**
		 * 关闭对话窗
		 */
		this.closeDialog = function(index){
			if(typeof index != "undefined"){
				layer.close(index);
			}else{
				layer.closeAll();
			}
		}
	}
	window.CustomDialog = new CustomDialog();
})();