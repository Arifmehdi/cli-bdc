@extends('frontend.website.layout.app')
@push('head')
<link rel="canonical" href="{{ url()->current() }}">
@endpush
@section('meta_description', app('globalSeo')['description'])
@section('meta_keyword', app('globalSeo')['keyword'])
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
@section('title', 'News | ' . app('globalSeo')['name'])

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

{{-- work 100% it show breadcumb --}}
<!--Breadcrumb-->
{{-- <section>
    <div class="bannerimg cover-image bg-background3" data-image-src="../assets/images/banners/banner2.jpg">
        <div class="header-text mb-0">
            <div class="container">
                <div class="text-center text-white">
                    <h2 class="new-page-tilte">News</h2>
                    <ol class="breadcrumb text-center new-page-bred">
                        <li class=""><a style="color:white" href="/">Home<span
                                    style="margin-left:4px; margin-right:4px">/</span> </a></li>
                        <li class=""><a style="color:white" href="javascript:void(0);">News</a></li>

                    </ol>
                </div>
            </div>
        </div>
    </div>
</section> --}}
<!--/Breadcrumb-->

<!--Breadcrumb-->
<div style="margin-top: 90px" class="mobile-auto-top">
    <div class="container">
        <div class="">

            <ol style="margin-top:32px" class="breadcrumb">
                <li style="color:black !important;border-bottom: 1px solid black" class="breadcrumb-item"><a style="color:black !important"
                        href="{{ route('home') }}"><strong>Home</strong></a></li>
                <li class="breadcrumb-item active"><a style="color:black !important;border-bottom: 1px solid black" href="{{ route('frontend.news.page') }}"><strong>News</strong></a></li>
                <li class="breadcrumb-item active">
                    <a style="color:black !important" href="#">{{ 'Videos' }}</a>
                </li>
            </ol>
        </div>
    </div>
</div>
<!--/Breadcrumb-->

<!--listing-->
<section >
    <div class="container mt-5">
        <div class="row">
            <!--lists-->
            <div style="width:70%" class="col-xl-8 col-lg-8 col-md-7 col-sm-6 col-xs-12 new-first-option">
                <h1  style="font-weight:900">{{ $vedio->title }}</h1>
                <div class="row">
                    @php
                    preg_match('/(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $vedio->url, $matches);
                    $embedUrl = isset($matches[1]) ? 'https://www.youtube.com/embed/' . $matches[1] : '';
                    @endphp
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div>
                                <div class="video-banner-wrapper">
                                    <iframe width="100%" height="600" src="{{$embedUrl}}" frameborder="0"
                                        allow="autoplay; encrypted-media" allowfullscreen class="video-frame"></iframe>
                                        <p style="margin-bottom:0;">By DreamBestCar.com</p>
                                        <p style="margin-top:0; padding-top:0">{{ $vedio->created_at->format('F d, Y') }}</p>
                                </div>
                                <h3 style="font-size:25px; font-weight:500;"
                                        class=" mt-5 mb-0  news-bold-title"><strong>{{ __('About the video') }}</strong></h3>
                                <p>{{ $vedio->sub_title}}</p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-6 col-xs-12 news-right-part">
                <h3 class="new-tit-2 mb-4">Featured stories</h3>
                <div>
                    <div class=" blog">
                        @if (isset($lastNews->img))
                        <a href="{{ route('frontend.news.details', ['slug' => $lastNews->slug]) }}">
                            <img height="220px" width="100%"
                                src="{{ asset('/frontend/assets/images/news/' . $lastNews->img) }}" alt="img"
                                class="news-show-image-middle" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';"/>
                        </a>
                        @elseif($lastNews && $lastNews->img == '')
                        <a href="{{ route('frontend.news.details', ['slug' => $lastNews->slug]) }}">
                            <img height="220px" width="100%" src="{{ asset('frontend/found/NotFound.png') }}" alt="img"
                                class="news-show-image-middle">
                        </a>
                        @endif


                    </div>
                    @if (isset($lastNews))
                    <div style="margin-bottom:30px">

                        @php
                        $title = Str::limit($lastNews->title, 18, '...');

                        @endphp
                        @php
                        $des = Str::limit($lastNews->sub_title, 100, '...');
                        @endphp
                        <a href="{{ route('frontend.news.page') }}" class="text-dark">
                            <p style="font-size:12px; color:rgb(4, 122, 126)" class="mt-3 mb-0">AUTO NEWS</p>
                        </a>
                        <a href="{{ route('frontend.news.details', ['slug' => $lastNews->slug]) }}" class="text-dark">
                            <h5 style="font-weight:600" class="fs-18 mt-1 mb-0 hyperlink-title news-bold-title">{{
                                $title }}</h5>
                        </a>

                        <p style="font-size:16px; width:97%" class="mt-1 mb-0">{{ $des }}...</p>
                        <p style="font-size:13px" class="mt-1">
                            {{\Carbon\Carbon::parse($lastNews->created_at)->format('F d, Y')}} </p>

                        <hr>

                    </div>
                    @endif

                </div>


                @if (isset($firstreviews))
                <div class="d-flex justify-between mb-5">
                    <div style="width:160px; margin-right:20px" class="rev-left">
                        <p class="m-0 mb-1">Expert Review</p>
                        <a
                            href="{{ route('frontend.review.details', ['id' => $firstreviews->id, 'title' => str_replace(' ', '-', $firstreviews->title)]) }}">

                            <h6 style="font-weight:600" class="hyperlink-title">{{$firstreviews->title}}</h6>
                        </a>

                        <p style="font-size:11px"> {{\Carbon\Carbon::parse($firstreviews->created_at)->format('F d,
                            Y')}} </p>
                        {{-- <p style="font-size:11px" class="m-0">{{$firstreviews->owner_name}}</p>
                        <p style="font-size:11px">{{$firstreviews->owner_title}}</p> --}}
                    </div>
                    <div>
                        <a
                            href="{{ route('frontend.review.details', ['id' => $firstreviews->id, 'title' => str_replace(' ', '-', $firstreviews->title)]) }}">

                            <img class="rev-img"
                                src="{{asset('/frontend/assets/images/review/' . $firstreviews->img )}}" />
                        </a>
                    </div>
                </div>
                @endif

                @if(isset($lastreviews))
                <div class="d-flex justify-between">
                    <div style="width:160px; margin-right:20px" class="rev-left">
                        <p class="m-0 mb-1">Expert Review</p>
                        <a
                            href="{{ route('frontend.review.details', ['id' => $firstreviews->id, 'title' => str_replace(' ', '-', $firstreviews->title)]) }}">

                            <h6 style="font-weight:600" class="hyperlink-title">{{$firstreviews->title}}</h6>
                        </a>


                        <p style="font-size:11px"> {{\Carbon\Carbon::parse($lastreviews->created_at)->format('F d, Y')}}
                        </p>
                        {{-- <p style="font-size:11px" class="m-0">{{$lastreviews->owner_name}}</p>
                        <p style="font-size:11px">{{$lastreviews->owner_title}}</p> --}}
                    </div>
                    <div>
                        <a
                            href="{{ route('frontend.review.details', ['id' => $lastreviews->id, 'title' => str_replace(' ', '-', $lastreviews->title)]) }}">
                            <img class="rev-img"
                                src="{{asset('/frontend/assets/images/review/' . $lastreviews->img )}}" />
                        </a>
                    </div>
                </div>
                @endif







                <h3 style="margin-top:85px" class="mb-5">Latest expert reviews</h3>
                <div>
                    <div class=" blog">
                        @if (isset($lastNews->img))
                        <a href="{{ route('frontend.news.details', ['slug' => $lastNews->slug]) }}">
                            <img height="220px" width="100%"
                                src="{{ asset('/frontend/assets/images/news/' . $lastNews->img) }}" alt="img"
                                class="news-show-image-middle">
                        </a>
                        @elseif($lastNews && $lastNews->img == '')
                        <a href="{{ route('frontend.news.details', ['slug' => $lastNews->slug]) }}">
                            <img height="220px" width="100%" src="{{ asset('frontend/found/NotFound.png') }}" alt="img"
                                class="news-show-image-middle">
                        </a>
                        @endif


                    </div>
                    @if (isset($lastNews))
                    <div style="margin-bottom:50px">

                        @php
                        $title = Str::limit($lastNews->title, 18, '...');

                        @endphp
                        @php
                        $des = Str::limit($lastNews->sub_title, 100,'...');
                        @endphp
                        <a href="{{ route('frontend.news.page') }}" class="text-dark">
                            <p style="font-size:12px; color:rgb(4, 122, 126)" class="mt-3 mb-0">Expert Review</p>
                        </a>
                        <a href="{{ route('frontend.news.details',['slug' => $lastNews->slug]) }}" class="text-dark">
                            <h5 style="font-weight:600"
                                class="fs-18 mt-1 mb-0 hyperlink-title news-bold-title">{{ $title }}</h5>
                        </a>

                        <p style="font-size:15px !important; width:95%" class="mt-1 mb-1">{{ $des }}...</p>
                        <p style="font-size:12px"> {{\Carbon\Carbon::parse($lastNews->created_at)->format('F d, Y')}}
                        </p>
                        <hr>

                    </div>
                    @endif

                    </div>
                @if($reviews->isNotEmpty())
                @foreach($reviews as $key => $rev)

                <div class="d-flex justify-between">
                    <div style="width:160px; margin-right:20px" class="rev-left">
                        <p class="m-0 mb-1">Expert Review</p>
                        <a
                            href="{{ route('frontend.review.details', ['id' => $lastreviews->id, 'title' => str_replace(' ', '-', $lastreviews->title)]) }}">
                            <h6 style="font-weight:600" class="hyperlink-title">{{$rev->title}}</h6>
                        </a>

                        <p style="font-size:11px"> {{\Carbon\Carbon::parse($rev->created_at)->format('F d, Y')}} </p>
                        {{-- <p style="font-size:11px" class="m-0">{{$rev->owner_name}}</p>
                        <p style="font-size:11px">{{$rev->owner_title}}</p> --}}
                    </div>
                    <div>
                        <a
                            href="{{ route('frontend.review.details', ['id' => $rev->id, 'title' => str_replace(' ', '-', $rev->title)]) }}">

                            @if (isset($rev->img))
                            <img class="rev-img" src="{{asset('/frontend/assets/images/review/' . $rev->img )}}" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';" />

                                            @else
                                            <a href="#">
                                                <img  src="{{ asset('frontend/found/NotFound.png') }}" alt="img"
                                                class="rev-img">
                                            </a>

                                            @endif
                        </a>
                    </div>
                </div>

                @endforeach
                @endif

            </div>
            <!--/lists-->














        </div>

        @php
        use Carbon\Carbon;
        @endphp

        @if($advices->count() > 1)
        <!-- Check if there are videos after the first -->
        <h2 class="mb-5 mt-5 advice-title">Featured stories</h2>
        @endif

        @if($advices->isNotEmpty())
        <div class="row">

            @foreach ($advices as $key => $data)


            @php
            $title = Str::limit($data->title, 18,'...');

            @endphp
            @php
            $des = Str::limit($data->sub_title, 130, '...');
            @endphp
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4">
                <div>
                    <div class=" blog">
                        <a href="{{ route('frontend.news.details', ['slug' => $data->slug]) }}">

                                @if (isset($data->img))
                                <img height="190px" width="100%"
                                src="{{ asset('frontend/assets/images/news/' . $data->img) }}" alt="img"
                                class="news-show-image-bottom" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';"/>
                                            @else
                                            <a href="#">
                                                <img height="190px" width="100%" src="{{ asset('frontend/found/NotFound.png') }}" alt="img"
                                                class="news-show-image-bottom">
                                            </a>
                                            @endif
                        </a>
                    </div>
                    <div style="margin-bottom:30px">
                        <a href="{{ route('frontend.news.page') }}" class="text-dark">
                            <p style="font-size:12px; color:rgb(4, 122, 126)" class="mt-3 mb-0">AUTO NEWS</p>
                        </a>
                        <a href="{{ route('frontend.news.details', ['slug' => $firstNews->slug]) }}" class="text-dark">
                            <h5 style="font-weight:600" class="fs-18 mt-1 mb-0 hyperlink-title news-bold-title">{{
                                $title }}
                            </h5>
                        </a>


                        <p style="font-size:16px !important; width:95%" class="mt-1 mb-0">{{$des}}...</p>
                        <p style="font-size:13px" class="mt-1"> {{\Carbon\Carbon::parse($data->created_at)->format('F d,
                            Y')}} </p>


                    </div>
                </div>
            </div>

            @endforeach




        </div>
        @endif





    </div>



</section>
<!--listing-->

@push('js')
<script>
    function playVideo(wrapper) {
    const banner = wrapper.querySelector('.video-banner');
    const video = wrapper.querySelector('.hidden-video');

    // Hide the banner and show the video
    banner.style.display = 'none';
    video.style.display = 'block';

    // Play the video
    video.play();
}


</script>
@endpush


@endsection
