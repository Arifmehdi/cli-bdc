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


{{-- <style>
    .nav-container {
        padding: 10px 0;
    }
    .nav-menu {
        list-style: none;
        padding: 0;
        display: flex;
        gap: 20px;
        background: #333;
        padding: 10px;
        border-radius: 5px;
    }
    .nav-menu li {
        position: relative;
    }
    .nav-menu li a {
        text-decoration: none;
        color: white;
        padding: 10px 15px;
        display: block;
        font-weight: bold;
    }
    .nav-menu li:hover > a {
        background: #555;
        border-radius: 5px;
    }
    .nav-menu .sub-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background: #444;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        list-style: none;
        padding: 10px;
        min-width: 200px;
        z-index: 1000;
        border-radius: 5px;
    }
    .nav-menu .sub-menu li a {
        color: white;
        padding: 8px 12px;
        display: block;
    }
    .nav-menu .sub-menu li:hover a {
        background: #666;
        border-radius: 5px;
    }
    .nav-menu li:hover .sub-menu {
        display: block;
        width: 100%;
    }
</style> --}}


<style>
    .nav-container {
        padding: 0;
        border-top: 2px solid violet;
        border-bottom: 2px solid violet;
        background: transparent;
    }

    .nav-menu {
        list-style: none;
        padding: 10px 0;
        display: flex;
        gap: 20px;
        justify-content: center;
        align-items: center;
    }

    .nav-menu li {
        position: relative;
    }

    .nav-menu li a {
        text-decoration: none;
        padding: 10px 15px;
        display: block;
        font-weight: bold;
        position: relative;
        transition: all 0.3s ease;
    }

    .nav-menu li a:hover::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 3px;
        background: violet;
        font-weight: bold;
    }

    /* Submenu Styling for Web */
    .nav-menu .sub-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background: rgba(190, 189, 189, 0.8);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        list-style: none;
        padding: 10px;
        min-width: 200px;
        z-index: 1000;
        border-radius: 5px;
    }

    .nav-menu .sub-menu li a {
        padding: 8px 12px;
        display: block;
    }

    .nav-menu .sub-menu li a:hover {
        background: violet;
        border-radius: 5px;
        color: white;
    }

    .nav-menu li:hover .sub-menu {
        display: block;
    }

    /* Hide Dropdown on Web & Show Only in Mobile */
    .menu-dropdown {
        display: none;
    }

    @media (max-width: 768px) {
        .nav-menu {
            display: none;
        }

        .menu-dropdown {
            display: block;
            width: 100%;
            border: 2px solid violet;
            background: transparent;
            padding: 8px;
            font-size: 16px;
            color: black;
        }
    }
</style>



{{--
<style>
    .nav-container {
        padding: 0;
        border-top: 2px solid black;
        border-bottom: 2px solid black;
        background: transparent;
    }
    .nav-menu {
        list-style: none;
        padding: 10px 0;
        display: flex;
        gap: 20px;
        justify-content: center;
        align-items: center;
    }
    .nav-menu li {
        position: relative;
    }
    .nav-menu li a {
        text-decoration: none;
        padding: 10px 15px;
        display: block;
        font-weight: bold;
        position: relative;
        transition: all 0.3s ease;
    }
    .nav-menu li a:hover::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 3px;
        background: violet;
        font-weight: bold;
    }
    .nav-menu .sub-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background: rgba(190, 189, 189, 0.8);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        list-style: none;
        padding: 10px;
        min-width: 200px;
        z-index: 1000;
        border-radius: 5px;
    }
    .nav-menu .sub-menu li a {
        padding: 8px 12px;
        display: block;
    }
    .nav-menu .sub-menu li a:hover {
        background: violet;
        border-radius: 5px;
        color:white
    }
    .nav-menu li:hover .sub-menu {
        display: block;
        width: 100%;
    }
</style> --}}

<!--Breadcrumb-->
<section>
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
</section>
<!--/Breadcrumb-->

{{-- <section>
    <div class="container nav-container mt-3">
        <ul class="nav-menu">
            <li><a href="{{ route('frontend.news.page') }}">Auto News</a></li>
            <li><a href="{{ route('research') }}">Reviews</a></li>
            <li><a href="{{ route('frontend.reviews', ['slug' => 'tools_&_expert_device']) }}">Tools and Advice</a></li>
            <li><a href="#">Latest Car Buying Advice</a></li>
            <li><a href="#">Videos</a></li>
            <li><a href="{{ route('frontend.faq') }}">FAQ</a></li>
            <li>
                <a href="#">More <i class="fa fa-caret-down"></i></a>
                <ul class="sub-menu">
                    <li><a href="#">Other</a></li>
                </ul>
            </li>
        </ul>
    </div>
</section> --}}


<section>
    <div class="container nav-container mt-3">
        <!-- Mobile Dropdown -->
        <select class="menu-dropdown" onchange="navigateToPage(this)">
            <option value="" selected disabled>Choose Category</option>
            <option value="{{ route('frontend.news.page') }}">Auto News</option>
            <option value="{{ route('research') }}">Reviews</option>
            <option value="{{ route('frontend.reviews', ['slug' => 'tools_&_expert_advice']) }}">Tools and Advice</option>
            <option value="{{ route('frontend.research.latestcarbuyingadvice') }}">Latest Car Buying Advice</option>
            <option value="{{ route('frontend.research.videos') }}">Videos</option>
            <option value="{{ route('frontend.faq') }}">FAQ</option>
            {{--<option value="#">Other</option>--}}
        </select>

        <!-- Desktop Navigation -->
        <ul class="nav-menu">
            <li><a href="{{ route('frontend.news.page') }}">Auto News</a></li>
            <li><a href="{{ route('research') }}">Reviews</a></li>
            <li><a href="{{ route('frontend.reviews', ['slug' => 'tools_&_expert_advice']) }}">Tools and Advice</a></li>
            <li><a href="{{ route('frontend.research.latestcarbuyingadvice') }}">Latest Car Buying Advice</a></li>
            <li><a href="{{ route('frontend.research.videos') }}">Videos</a></li>
            <li><a href="{{ route('frontend.faq') }}">FAQ</a></li>
            <li>
                <a href="#">More <i class="fa fa-caret-down"></i></a>
                <ul class="sub-menu">
                    {{--<li><a href="#">Other</a></li>--}}
                </ul>
            </li>
        </ul>
    </div>
</section>

{{-- <section>
    <div class="container nav-container">
        <ul class="nav-menu">
            <li><a href="{{ route('frontend.news.page') }}">Auto News</a></li>
            <li><a href="{{ route('research') }}">Reviews</a></li>
            <li><a href="{{ route('frontend.reviews', ['slug' => 'tools_&_expert_device']) }}">Tools and Advice</a></li>
            <li><a href="#">Latest Car Buying Advice</a></li>
            <li><a href="#">Videos</a></li>
            <li><a href="{{ route('frontend.faq') }}">FAQ</a></li>
            <li>
                <a href="#">More <i class="fa fa-caret-down"></i></a>
                <ul class="sub-menu">
                    <li><a href="#">Others</a></li>
                </ul>
            </li>
        </ul>
    </div>
</section> --}}

<!--listing-->
<section class="sptb">
    <div class="container">
        <div class="row">
            <!--lists-->
            <div style="width:70%" class="col-xl-8 col-lg-8 col-md-7 col-sm-6 col-xs-12 new-first-option">
                @if($all->count() > 1)
                <h2 class="new-tit mb-4">Latest News</h2>
                @endif

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div>
                            <div class=" blog">
                                @if (isset($firstNews->img))
                                <a href="{{ route('frontend.news.details', ['slug' => $firstNews->slug]) }}">
                                    <img height="500px" width="100%"
                                        src="{{ asset('/frontend/assets/images/news/' . $firstNews->img) }}" alt="img"
                                        class="news-show-image-top" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';"/>
                                </a>
                                @elseif($firstNews && $firstNews->img == '')
                                <a href="{{ route('frontend.news.details', ['slug' => $firstNews->slug]) }}">
                                    <img height="500px" width="100%" src="{{ asset('frontend/found/NotFound.png') }}"
                                        alt="img" class="news-show-image-top">
                                </a>
                                @endif


                            </div>
                            @if (isset($firstNews))
                            <div style="margin-bottom:30px">

                                @php
                                $title = Str::limit($firstNews->title, 20, '...');

                                @endphp
                                @php
                                $des = Str::limit($firstNews->sub_title, 100, '...');
                                @endphp
                                <a href="{{ route('frontend.news.page') }}" class="text-dark">
                                    <p style="font-size:12px; color:rgb(4, 122, 126)" class="mt-3 mb-0">AUTO NEWS</p>
                                </a>
                                <a href="{{ route('frontend.news.details', ['slug' => $firstNews->slug]) }}"
                                    class="text-dark">
                                    <h5 style="font-size:25px; font-weight:600;"
                                        class=" mt-1 mb-0 hyperlink-title news-bold-title">{{ $firstNews->title }}</h5>
                                </a>

                                <p style="font-size:15px !important; width:95%" class="mt-1 mb-0">{{ $des }}</p>
                                <p style="font-size:13px" class="mt-1">
                                    {{\Carbon\Carbon::parse($firstNews->created_at)->format('F d, Y')}} </p>

                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($news->isNotEmpty())
                <div class="row">
                    @foreach ($news as $key => $data)
                    <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                        <div>
                            <div class="blog">
                                @if (isset($data->img))
                                <a href="{{ route('frontend.news.details', ['slug' => $data->slug]) }}">
                                    <img height="270px" width="100%"
                                        src="{{ asset('/frontend/assets/images/news/' . $data->img) }}" alt="img"
                                        class="news-show-image-middle" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';"/>
                                </a>
                                @elseif($data->img == '')
                                <a href="{{ route('frontend.news.details', ['slug' => $data->slug]) }}">
                                    <img height="270px" width="100%" src="{{ asset('frontend/found/NotFound.png') }}"
                                        alt="img" class="news-show-image-middle">
                                </a>
                                @endif
                            </div>
                            <div style="margin-bottom:30px">
                                @php
                                $title = Str::limit($data->title, 22, '...');
                                @endphp
                                @php
                                $des = Str::limit($data->sub_title, 100, '...');
                                @endphp
                                <a href="{{ route('frontend.news.page') }}" class="text-dark">
                                    <p style="font-size:12px; color:rgb(4, 122, 126)" class="mt-3 mb-0">AUTO NEWS</p>
                                </a>
                                <a href="{{ route('frontend.news.details', ['slug' => $data->slug]) }}"
                                    class="text-dark">
                                    <h5 style="font-weight:600;"
                                        class="fs-18 mt-1 mb-0 hyperlink-title news-bold-title">{{
                                            $data->title }}</h5>
                                </a>
                                <p style="font-size:16px; width:95%" class="mt-1 mb-0">{{ $des }}</p>
                                <p style="font-size:13px" class="mt-1">
                                    {{\Carbon\Carbon::parse($data->created_at)->format('F d, Y')}} </p>
                            </div>
                        </div>
                    </div>

                    @endforeach
                    <div class="col-md-12 text-end m-2">
                        <a href="{{ route('frontend.news.page') }}" style="border-bottom:1px solid black" ><strong>View more News Articles</strong></a>
                        {{--<a href="{{ route('frontend.articles','videos')}}" style="border-bottom:1px solid black" ><strong>View more news articles</strong></a>--}}
                    </div>
                </div>
                @endif
            </div>
            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-6 col-xs-12 news-right-part">
                @include('frontend.common.blog_categories')
                <h3 class="new-tit-2 mb-4">Latest Expert Reviews</h3>

                <div>
                    <div class=" blog">
                        @if (isset($firstreviews->img))
                        <a href="{{ route('article.details', $firstreviews->slug) }}">
                            <img height="220px" width="100%"
                                src="{{ asset('/frontend/assets/images/blog/' . $firstreviews->img) }}" alt="img"
                                class="news-show-image-middle" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';"/>
                        </a>
                        @elseif($firstreviews && $firstreviews->img == '')
                        <a href="{{ route('article.details', $firstreviews->slug) }}">
                            <img height="220px" width="100%" src="{{ asset('frontend/found/NotFound.png') }}" alt="img"
                                class="news-show-image-middle">
                        </a>
                        @endif
                    </div>
                    @if (isset($firstreviews))
                    <div style="margin-bottom:30px">

                        @php
                        $title = Str::limit($firstreviews->title, 18, '...');

                        @endphp
                        @php
                        $des = Str::limit($firstreviews->sub_title, 100, '...');
                        @endphp
                        <a href="{{ route('frontend.news.page') }}" class="text-dark">
                            <p style="font-size:12px; color:rgb(4, 122, 126)" class="mt-3 mb-0">Expert Review</p>
                        </a>
                        <a href="{{ route('article.details', $firstreviews->slug) }}" class="text-dark">
                            <h5 style="font-weight:600" class="fs-18 mt-1 mb-0 hyperlink-title news-bold-title">{{
                                $firstreviews->title }}</h5>
                        </a>
                        <p style="font-size:16px; width:97%" class="mt-1 mb-0">{{ $des }}...</p>
                        <p style="font-size:13px" class="mt-1">
                            {{\Carbon\Carbon::parse($firstreviews->created_at)->format('F d, Y')}} </p>
                        <hr>
                    </div>
                    @endif
                </div>

                @if($reviews->isNotEmpty())
                @foreach($reviews as $key => $rev)
                <div class="d-flex justify-between mt-4">
                    <div style="width:160px; margin-right:20px" class="rev-left">
                        <p class="m-0 mb-1">Expert Review</p>
                        <a href="{{ route('article.details', $rev->slug) }}">
                            <h6 style="font-weight:600" class="hyperlink-title">{{$rev->title}}</h6>
                        </a>
                        <p style="font-size:11px"> {{\Carbon\Carbon::parse($rev->created_at)->format('F d, Y')}} </p>
                        {{-- <p style="font-size:11px" class="m-0">{{$rev->owner_name}}</p>
                        <p style="font-size:11px">{{$rev->owner_title}}</p> --}}
                    </div>
                    <div>
                        <a href="{{ route('article.details', $rev->slug) }}">
                            @if (isset($rev->img))
                                <img class="rev-img" src="{{asset('/frontend/assets/images/blog/' . $rev->img )}}" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';" />
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
                <div class="col-md-12 text-end m-5">
                    <a href="{{ route('frontend.articles','review')}}" style="border-bottom:1px solid black" ><strong>See All Expert Reviews</strong></a>
                    {{--<a href="{{ route('frontend.articles','review')}}" style="border-bottom:1px solid black" ><strong>See All Expert Reviews</strong></a>--}}
                </div>
            </div>

            <!--/lists-->

        </div>

        @php
        use Carbon\Carbon;
        @endphp

        @if($videos->count() > 1)
        <!-- Check if there are videos after the first -->
        <h2 class="mb-5 mt-5 latest-video">Latest Videos</h2>
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
                {{--<h5 style="font-family: 'Aperçu Pro', sans-serif; font-weight:800; lign-height:22px;" class="fs-18 mt-1 mb-0 hyperlink-title news-bold-title">
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

        {{--@if($advices->count() > 1)
        <!-- Check if there are videos after the first -->
        <h2 class="mb-5 mt-5 advice-title">Latest car-buying advice</h2>
        @endif--}}

        {{--@if($advices->isNotEmpty())
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
                                $data->title }}
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
        @endif--}}
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
<script>
    function navigateToPage(select) {
        if (select.value) {
            window.location.href = select.value;
        }
    }
</script>

@endsection
