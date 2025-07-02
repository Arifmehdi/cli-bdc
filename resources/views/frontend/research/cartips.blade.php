@extends('frontend.website.layout.app')
@push('head')
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@php 
$description = 'Pro maintenance tips, fuel-saving tricks, and driving advice. Extend your cars life and save money with our guides.';
@endphp

@section('meta_description', $description ?? app('globalSeo')['description'])

@section('meta_keyword', app('globalSeo')['keyword'])
@section('gtm')
    {!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])
@section('og_title', 'Car Tips | ' . app('globalSeo')['name'] ?? app('globalSeo')['og_title'])
@section('og_description', $description ?? app('globalSeo')['og_description'])
@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])
@section('twitter_title', 'Car Tips | ' . app('globalSeo')['name'] ?? app('globalSeo')['twitter_title'])
@section('twitter_description', $description ?? app('globalSeo')['twitter_description'])
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])
@section('title', 'Car Tips | ' . app('globalSeo')['name'])

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
                    @if($datas->count() > 1)
                        <h2 class="new-tit mb-4 header-title">{{$slug ?? 'News'}}</h2>
                    @endif

                    @php
                        $firstNews = $datas[0] ?? null;
                        $news = $datas;
                    @endphp

                    @if($news->isNotEmpty())
                        <div class="row">
                            @foreach ($news as $key => $data)
                                <x-blog-card
                                :data="$data"
                                path='/frontend/assets/images/blog/'
                                type="Car Tips"
                                {{-- :route="route('frontend.tips.details', ['slug' => $data->slug])" --}}
                                :route="route('frontend.research.review.autonews.details', ['categorySlug' => 'car-tips','slug' => $data->slug])"
                                :main="route('frontend.research.car.tips')"
                                />
                            @endforeach

                            {{-- <x-view-more-link btn="news" /> --}}
                        </div>
                    @else
                        {{-- <div class="row">
                            <div class="col-12 text-center py-5">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="text-muted">Coming Soon</h3>
                                        <p class="text-muted">We're working on bringing you fresh content. Stay tuned!</p>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <x-coming-soon />
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
