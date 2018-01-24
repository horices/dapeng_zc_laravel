//在初始化方法中添加默认绑定事件
AjaxAction.options.event.init = "loadInit";
AjaxAction.options.event.uninit = "unloadInit";
AjaxAction.options.event.beforeAjaxLoadAction = function(obj,url){
}
AjaxAction.options.event.afterAjaxLoadAction = function(obj,url){
}
AjaxAction.options.event.ajaxBefore = function(){
	NProgress.start();
};
AjaxAction.options.event.ajaxSuccess = function(){
	NProgress.done();
};
$(function(){
	AjaxAction.init();
})
//初始化绑定事件