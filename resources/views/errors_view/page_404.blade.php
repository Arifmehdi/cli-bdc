@extends('frontend.website.layout.app')
@push('css')
<style>
    /*======================
    404 page
=======================*/


    .page_404 {
        padding: 40px 0;
        background: #fff;
        font-family: 'Arvo', serif;
        margin-top: 50px
    }

    .page_404 img {
        width: 100%;
    }

    .four_zero_four_bg {

        background-image: url({{asset('/frontend/error_404.gif')}});
        height: 400px;
        background-position: center;
    }


    .four_zero_four_bg h1 {
        font-size: 80px;
        margin-top: 100px
    }

    .four_zero_four_bg h3 {
        font-size: 80px;
    }

    .link_404 {
        color: #F1F5FD !important;
        padding: 10px 20px;
        background: #39ac31;
        margin: 20px 0;
        display: inline-block;
    }

    .contant_box_404 {
        margin-top: -50px;
    }
</style>
@endpush
@section('content')

<div class="container mt-4 mb-5">
    <section class="page_404">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 ">
                    <div style="margin: 0 auto" class="col-sm-10 col-sm-offset-1  text-center">
                        <div class="four_zero_four_bg">
                            <h1 class="text-center ">404</h1>


                        </div>

                        <div class="contant_box_404">
                            <h3 class="h2">
                                Look like you're lost
                            </h3>

                            <p>The page you are looking for not avaible!</p>

                            <a href="{{ route('home')}}" class="link_404">Go to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
