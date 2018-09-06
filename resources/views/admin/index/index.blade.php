@extends("admin.public.layout")
@section("content")
<div id="content-container" class="container">
    <div class="row row-2-10">
    <include file="Public:nav" />
        <div class="col-md-10 dp-member-content" style="padding:30px 30px;">
            
            <style>
            .info-list{}
			.info-list dt{ background:url({{ env("IMG_BASE_URL") }}/admin/images//ico-1.gif) no-repeat 0 4px; padding-left:26px; line-height:30px; font-size:16px;}
			.info-list dd{ color:#747474; line-height:24px; padding:6px 0 0 26px;}
            </style>
            
            <dl class="info-list">
            	<dt>如何加入大鹏教育展翅系统</dt>
                <dd>由于缺少经验、资历，刚踏入社会不久的年轻人收入水平低，是再正常不过的事情。但“年轻人变得越来越穷”的说法听起来却让人觉得匪夷所思——如今的年轻人大多拥有了更高的学历，掌握了更丰富的资源，有了更多元化的选择，怎么还会越来越穷呢？然而，这的确不是空穴来风，“青年贫困”已经成为全球多个发达国家和地区头疼的问题。<p style="text-align:right"><a class="btn btn-primary" href="#">查看详情</a></p></dd>
            </dl>
            
            <dl class="info-list">
            	<dt>如何加入大鹏教育展翅系统</dt>
                <dd>由于缺少经验、资历，刚踏入社会不久的年轻人收入水平低，是再正常不过的事情。但“年轻人变得越来越穷”的说法听起来却让人觉得匪夷所思——如今的年轻人大多拥有了更高的学历，掌握了更丰富的资源，有了更多元化的选择，怎么还会越来越穷呢？然而，这的确不是空穴来风，“青年贫困”已经成为全球多个发达国家和地区头疼的问题。<p style="text-align:right"><a class="btn btn-primary" href="#">查看详情</a></p></dd>
            </dl>
            
            <dl class="info-list">
            	<dt>如何加入大鹏教育展翅系统</dt>
                <dd>由于缺少经验、资历，刚踏入社会不久的年轻人收入水平低，是再正常不过的事情。但“年轻人变得越来越穷”的说法听起来却让人觉得匪夷所思——如今的年轻人大多拥有了更高的学历，掌握了更丰富的资源，有了更多元化的选择，怎么还会越来越穷呢？然而，这的确不是空穴来风，“青年贫困”已经成为全球多个发达国家和地区头疼的问题。<p style="text-align:right"><a class="btn btn-primary" href="#">查看详情</a></p></dd>
            </dl>
            
            <dl class="info-list">
            	<dt>如何加入大鹏教育展翅系统</dt>
                <dd>由于缺少经验、资历，刚踏入社会不久的年轻人收入水平低，是再正常不过的事情。但“年轻人变得越来越穷”的说法听起来却让人觉得匪夷所思——如今的年轻人大多拥有了更高的学历，掌握了更丰富的资源，有了更多元化的选择，怎么还会越来越穷呢？然而，这的确不是空穴来风，“青年贫困”已经成为全球多个发达国家和地区头疼的问题。<p style="text-align:right"><a class="btn btn-primary" href="#">查看详情</a></p></dd>
            </dl>
            

        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">查看详情</h4>
            </div>
            <div class="modal-body"> ... </div>
        </div>
    </div>
</div>
@endsection