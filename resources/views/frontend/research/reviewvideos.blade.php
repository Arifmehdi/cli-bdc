@extends('frontend.website.layout.app')
@push('head')
    <link rel="canonical" href="{{ url()->current() }}">
@endpush
@php 
$description = 'Watch in-depth car walkarounds, test drives, and expert reviews. See vehicles in action before visiting the dealership.'
@endphp 

@section('meta_description', $description ??  app('globalSeo')['description'])
@section('meta_keyword', app('globalSeo')['keyword'])
@section('gtm')
    {!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])
@section('og_title', 'Videos | ' . app('globalSeo')['name'] ?? app('globalSeo')['og_title'])
@section('og_description',  $description ??   app('globalSeo')['og_description'])
@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])
@section('twitter_title', 'Videos | ' . app('globalSeo')['name'] ?? app('globalSeo')['twitter_title'])
@section('twitter_description',  $description ??   app('globalSeo')['twitter_description'])
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])
@section('title', 'Videos | ' . app('globalSeo')['name'])

@section('content')
    <style>
        .pagination .page-item.prev .page-link,
        .pagination .page-item.next .page-link {
            font-size: 14px;
            /* Adjust the font size as needed */
            padding: 0.3rem 0.5rem;
            /* Adjust padding as needed */
        }

        .new-tit {
            font-size: 30px;
            margin-bottom: 30px;
        }

        .new-tit-2 {
            font-size: 25px;
            margin-bottom: 30px;
        }

        .video-banner-wrapper {
            position: relative;
            cursor: pointer;
            width: 100%;
        }

        .video-banner {
            width: 100%;
            padding-top: 56.25%;
            /* Aspect ratio 16:9 */
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .play-icon-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 50px;
            color: rgba(255, 255, 255, 0.8);
        }

        .hidden-video {
            display: none;
            width: 100%;
        }
    </style>

    <!--Breadcrumb-->
    <section>
        <x-breadcrumb :slug="$slug" category="true" main="Research"  :route="route('frontend.research.review')" />
    </section>
    <!--/Breadcrumb-->

    {{-- sub menu start here  --}}
    @include('frontend.website.layout.research_sub_menu')
    {{-- sub menu end here  --}}

    <!--listing-->
    <section class="sptb">
        <div class="container">
            <div class="row">
                <!--lists-->
                <div style="width:100%" class="col-xl-12 col-lg-12 col-md-12 col-sm-6 col-xs-12 new-first-option">
                    @php
                    use Carbon\Carbon;
                    @endphp

                    @if($videos->count() > 1)
                    <!-- Check if there are videos after the first -->
                    <h2 class="mb-5 mt-5 latest-video header-title">Videos</h2>
                    @endif
                    @if($videos->isNotEmpty())
                    <div style="margin-bottom:60px" class="row">
                        @foreach($videos as $key => $video)

                        <!-- Only include videos after the first -->
                        @php
                        preg_match('/(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $video->url, $matches);
                        $embedUrl = isset($matches[1]) ? 'https://www.youtube.com/embed/' . $matches[1] : '';
                        @endphp

                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-5">
                            <div class="video-banner-wrapper">
                                <iframe width="100%" height="200" src="{{$embedUrl}}" frameborder="0"
                                    allow="autoplay; encrypted-media" allowfullscreen class="video-frame"></iframe>
                            </div>
                            <p class="m-0 mb-1" style="font-size:14px; color:rgb(4, 122, 126)">Video</p>
                            {{--<h5 style="font-family: 'Aperçu Pro', sans-serif; font-weight:800;" class="fs-18 mt-1 mb-0 hyperlink-title news-bold-title">
                                {{$video->title}}
                            </h5>--}}
                            <a href="{{ route('frontend.vedio.page.details', $video->id) }}"  class="fs-18 mt-1 mb-0 hyperlink-title news-bold-title"><p style="font-size:18px;">{{$video->title}}</p></a>

                            {{-- <p class="m-0">{{ \Illuminate\Support\Str::limit($video->sub_title, 100, '...') }}</p> --}}
                            <p style="font-size:12px">{{ Carbon::parse($video->created_at)->format('F d, Y') }}</p>
                        </div>

                        @endforeach
                        <div class="col-md-12 text-end m-0">
                            <a href="#" style="border-bottom:1px solid black" ><strong>See All Videos</strong></a>
                            {{--<a href="{{ route('frontend.articles','videos')}}" style="border-bottom:1px solid black" ><strong>See All Videos</strong></a>--}}
                        </div>
                    </div>
                    @endif
                </div>
                <!--/lists-->
            </div>
        </div>
    </section>
    <!--listing-->
    {{-- sub menu start here  --}}
    @include('frontend.website.layout.new_footer_beyond_sub_menu')
    {{-- sub menu end here  --}}
    <style>
        .card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .card-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
        }
        .card-text {
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .card-category {
            margin-bottom: 0.5rem;
        }
        .news-show-image-middle {
            height: 200px;
            object-fit: cover;
        }
    </style>

@endsection
