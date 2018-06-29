@extends("admin.public.layout_base")
@section("content")
    <div class="row row-2-10">
        <div class="col-md-2 nav-left">
            @include("admin.public.nav")
        </div>
        <div class="col-md-10 dp-member-content">
            @section("right_content")
            @show
        </div>
    </div>
@endsection