@extends('frontend.website.layout.app')
@push('head')
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@foreach(app('globalStaticPage') as $page)
@if ($page->slug == 'find-dealership')
@if ($page->description)
@section('meta_description',$page->description)
@else
@section('meta_description', app('globalSeo')['description'])
@endif
@if ($page->keyword)
@section('meta_keyword', $page->keyword)
@else
@section('meta_keyword', app('globalSeo')['keyword'])
@endif
@section('title')
    Dealership | Best Used Cars for Sale - bestdreamcar.com®
@endsection
@endif
@endforeach

@section('gtm')
{!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])

@section('og_title')
    Dealership | Best Used Cars for Sale - bestdreamcar.com®
@endsection
@section('og_description', app('globalSeo')['og_description'])
@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])

@section('twitter_title')
    Dealership | Best Used Cars for Sale - bestdreamcar.com®
@endsection
@section('twitter_description', app('globalSeo')['twitter_description'])
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])
@push('css')
    <style>
    .showInventoryBtn
    {
        border: 1px solid rgb(4, 100, 100);
        padding: 5px;
        text-transform: uppercase;
        color: rgb(8, 57, 63);
        border-radius:3px;
        font-weight: 500;

    }
    .showInventoryBtn:hover
    {
        background-color: rgb(79, 0, 131);
        transition: .5s ease all;
        color: white;
    }

    .dealer-result-name
    {

        font-weight: 700;
        color: rgb(31, 17, 17);
        text-transform: uppercase;
    }
    .dealer-result-location
    {


        color: rgb(31, 17, 17);
        text-transform: uppercase;
        font-size: 14px
    }
    </style>
@endpush
@section('content')

	<!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3 sptb-2" data-image-src="{{asset('frontend/assets')}}/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white ">
                        <h1 class="">Find Dealership</h1>
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Dealership</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

	<!--Statistics-->
    <section id="main_container"></section>
@endsection

@push('js')
@include('frontend.website.layout.js.dealer_view_js')
@endpush
