@extends('frontend.website.layout.app')
@section('meta_description', "$content->seo_description")
@section('meta_keyword', "$content->seo_keyword")
@section('title',"$content->seo_title")

@section('gtm')
{!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])
@section('og_title', app('globalSeo')['og_title'])
@section('og_description', app('globalSeo')['og_description'])
@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])
@section('twitter_title', app('globalSeo')['twitter_title'])
@section('twitter_description', app('globalSeo')['twitter_description'])
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])

@section('content')
@php
use Illuminate\Support\Facades\Request;
@endphp
<style>
    .pagination .page-item.prev .page-link,
.pagination .page-item.next .page-link {
    font-size: 14px; /* Adjust the font size as needed */
    padding: 0.3rem 0.5rem; /* Adjust padding as needed */
}

</style>

<!--Breadcrumb-->
<section>
    <div class="bannerimg cover-image bg-background3" data-image-src="../assets/images/banners/banner2.jpg">
        <div class="mb-0 header-text">
            <div class="container">
                <div class="text-center text-white">
                    <h1 class="">{{ $content->title }}</h1>
                    <ol class="text-center breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ $content->slug }}</a></li>

                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/Breadcrumb-->

<!--listing-->
<section class="sptb">
    <div class="container">
      <div class="col-md-12">
        <div class="header">
            <h3>{{$content->title}}</h3>

        </div>

        <div class="description">
            {!! $content->description !!}
        </div>
      </div>
    </div>
</section>
<!--listing-->


@endsection
