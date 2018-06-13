<div class="panel panel-default dp-cxx-menu dp-beijing-color">
	<div class="panel-body" style="padding-left: 0px; padding-right: 0px;">
		<div class="list-group-block">
			<div class="list-group-panel">
				<ul class="list-group dp-side-menu-2" id="subnav">
					@foreach ($navList as $nav)
					<li><a class="list-group-item" flag="{{$nav['flag']}}" target="{{$nav['target'] or '_self'}}" href="{{ route(collect($nav['route'])->first(),collect($nav['route'])->get(1,[]))}}">{{$nav['text']}}</a></li>
					@endforeach
				</ul>
			</div>
		</div>
	</div>
</div>
<script>
$(function(){
	$("#subnav li").removeClass("cur").find("a[flag='{{ $leftNav ?? Route::currentRouteName() }}']").addClass("cur");
});
</script>
<style>
	@media (min-width: 992px){
		.nav-left{padding-left: 0px; width: 10%}
		.nav-left a{padding-left: 8px;}
	}
</style>
