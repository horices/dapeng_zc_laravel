<div class="panel panel-default dp-cxx-menu dp-beijing-color">
	<div class="panel-body" style="padding-left: 0px; padding-right: 0px;">
		<div class="list-group-block">
			<div class="list-group-panel">
				<ul class="list-group dp-side-menu-2" id="subnav">
					@foreach ($navList as $nav)
					<li><a class="list-group-item" flag="{{$nav['flag']}}" href="{{$nav['url']}}">{{$nav['text']}}</a></li>
					@endforeach
				</ul>
			</div>
		</div>
	</div>
</div>
<style>
	@media (min-width: 992px){
		.nav-left{padding-left: 0px; width: 15%}
		.nav-left a{padding-left: 8px;}
	}
</style>
