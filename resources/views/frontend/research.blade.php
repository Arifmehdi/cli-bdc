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
@section('og_title')
Research |Best Used Cars for Sale - bestdreamcar.com®
@endsection
@section('og_description', app('globalSeo')['og_description'])
@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])
@section('twitter_title')
Research |Best Used Cars for Sale - bestdreamcar.com®
@endsection
@section('twitter_description', app('globalSeo')['twitter_description'])
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])
@section('title')
Research |Best Used Cars for Sale - bestdreamcar.com®
@endsection
@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;700&display=swap');

    :root {
        --principal-font: 'Kumbh Sans', sans-serif;
        --font-size: 12px;

        --bg-gradient: linear-gradient(to bottom, hsl(273, 75%, 66%), hsl(240, 73%, 65%));

        --primary-dark-color: hsl(238, 29%, 16%);
        --primary-soft-color: hsl(14, 88%, 65%);

        --neutral-dark-color: hsl(237, 12%, 33%);
        --neutral-soft-color: hsl(240, 6%, 50%);

    }


    html {
        box-sizing: border-box;
    }

    * {
        box-sizing: inherit;
    }

    .attribution {
        font-size: 11px;
        text-align: center;
        background-color: hsl(240, 5%, 91%);
        padding: 8px 0 5px;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
    }

    .attribution a {
        color: hsl(228, 45%, 44%);
    }

    /* global */
    /*================================================*/

    .mobile {
        display: block;
        margin: 0 auto;
    }


    /* FAQ card: main */
    /*================================================*/

    .faq-content {
        padding: 9px 25px 3rem;
    }

    .faq-content h1 {
        font-size: 32px;
        text-align: center;
        color: var(--primary-dark-color);
    }

    .faq-accordion {
        padding: 8px 0;
        border-bottom: 1px solid hsl(240, 5%, 91%);
    }


    /* FAQ card: main title */
    /*================================================*/

    /* checkbox tgg-title*/
    input.tgg-title {
        appearance: unset;
        all: unset;
    }

    .faq-accordion-title label {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .faq-accordion-title h2 {
        font-size: 16px;
        font-weight: 400;
        color: var(--neutral-dark-color);

    }

    .faq-accordion-title span {
        margin-left: auto;
        transition: transform .4s ease-in-out;
    }


    /* FAQ card: main content */
    /*================================================*/

    .faq-accordion-content {
        color: var(--neutral-soft-color);
        overflow: hidden;
        max-height: 0;
        transition: max-height .4s ease-in-out;
    }


    /* Effects */
    /*================================================*/

    /* main title, accordion title effects */

    .faq-accordion-title:hover h2 {
        color: var(--primary-soft-color)
    }

    /* onclick "" */
    .faq-accordion .tgg-title:checked+div>label>h2 {
        font-weight: 700;
    }

    .faq-accordion .tgg-title:checked+div>label>span {
        will-change: transform;
        transform: rotate(180deg);
    }

    /* main content, acordion text effect */

    .faq-accordion .tgg-title:checked~.faq-accordion-content {
        will-change: max-height;
        max-height: 600px;
    }


    /* card css */
    .flex-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .flex-item {
        flex: 1 1 calc(33.333% - 10px);
        max-width: calc(33.333% - 10px);
        text-align: center;
    }

    .flex-item img {
        width: 100%;
        height: auto;
    }

    .browse-all {
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 5px;
        height: 96px;
        border-radius: 7px;
    }

    /* Adjust the flex-basis for smaller screens */


    @media (max-width: 1279px) {
        .flex-item {
            flex: 1 1 calc(33.333% - 10px);
            max-width: calc(33.333% - 10px);

        }

        .car-type-name {
            font-size: 10px !important;
            margin-bottom: 10px
        }
    }
</style>


<!--Breadcrumb-->
<div>
    <div class="bannerimg cover-image bg-background3" data-image-src="../assets/images/banners/banner2.jpg">
        <div class="header-text mb-0">
            <div class="container">
                <div class="text-center text-white ">
                    <h1 class="favorite-header">Research</h1>
                    <ol class="breadcrumb text-center">
                        <li class="favorite-bc"><a style="color:white" href="{{ route('home') }}">Home<span
                                    style="margin-left:4px; margin-right:4px;">/</span> </a></li>
                        <li class="favorite-bc"><a style="color:white" href="javascript:void(0);">Research</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/Breadcrumb-->

{{-- sub menu start here  --}}
@include('frontend.website.layout.research_sub_menu')
{{-- sub menu end here  --}}

<section style="padding-top: 5px !important; padding-bottom:45px !important" class="">
    <div style="border-radius:5px; padding-bottom:25px" class="container">
        @if($news->count() > 1)
        <div class="text-center section-title center-block">
            <h3 style="margin-top:55px">Recent Auto News</h3>
            <p style="margin-bottom:10px">Start your search by choosing one of the most popular news </p>
        </div>
        @endif

        @if($news->isNotEmpty())
        <div style="width:90% !important; margin: 0 auto !important; " id="defaultCarousel1"
            class="owl-carousel Card-owlcarousel owl-carousel-icons mb-5">
            @foreach ($news as $key => $new)

            <div class="item">
                <div class="mb-0 card">
                    <div class="item7-card-img">
                        <a href="{{ route('frontend.news.details', ['slug' => $new->slug]) }}"></a>


                        @if (isset($new->img))
                        <img style="width: 100%; height:260px" class="res-news-img"
                            src="{{ asset('frontend') }}/assets/images/news/{{ $new->img }}"
                            alt="Used cars for sale Best Dream car {{ $new->title }} News image"
                            onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';" />

                        @else
                        <a href="#">
                            <img style="width: 100%; height:260px" src="{{ asset('frontend/found/NotFound.png') }}"
                                alt="img" class="res-news-img">
                        </a>
                        @endif
                    </div>
                    <div class="card-body p-4">

                        @php
                        $title = Str::limit($new->title, 25, '...');
                        @endphp
                        <a href="{{ route('frontend.news.details', ['slug' => $new->slug]) }}"
                            class="text-dark hyperlink-title">
                            <h5 class="fs-18 res-news-tit hyperlink-title">{{ $title }}</h5>
                        </a>

                        @php
                        $sub_title = Str::limit($new->sub_title, 100, '...');
                        @endphp
                        <p style="height:80px" class="news-par">{{ $sub_title }}</p>
                        <a href="{{ route('frontend.news.details', ['slug' => $new->slug]) }}"
                            style="float:right; color:darkcyan">Read more <i class="fa fa-angle-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif



    </div>
</section>

<section class="sptb bg-white">
    <div class="container">
        <div>
            @if($sels->count() > 1)
            <div class="resharch-content-title">
                <h4 style="font-size:24px" class="mb-3">Tools & Expert Device</h4>
            </div>
            @endif
            @if($sels->isNotEmpty())
            <div class="col-md-12 m-0 p-0">
                <div class="row p-0" id="cars-containers">
                    @foreach($sels as $key => $sel)

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-1">
                        <div style="width:100%" class="card">
                            <a href="{{route('article.details', $sel->slug )}}" class="text-decoration-none">
                                <div class="">
                                    @if (isset($sel->img))
                                    <img class="res-first-img" style="width:100%; height:195px"
                                        src="{{ asset('frontend/assets/images/blog/' . $sel->img) }}"
                                        alt="Used cars for sale for Best Dream car {{ $sel->title }} image"
                                        onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';" />
                                    @else
                                    <a href="#">
                                        <img class="res-first-img" style="width:100%; height:195px"
                                            src="{{ asset('frontend/found/NotFound.png') }}" alt="img" />
                                    </a>
                                    @endif
                                </div>
                                @php
                                $title = Str::limit($sel->title, 20, '...');
                                $sub_title = Str::limit($sel->sub_title, 105, '...');
                                @endphp

                                <div class="mt-1 resh-tit p-3">
                                    <h5 style="margin-top:0px" class="fs-18  text-dark res-des-sell hyperlink-title">{{ $sel->title }}
                                    </h5>
                                    <p style="height:80px" class="news-par">{{ $sub_title }}</p>
                                    <a class="mt-5 mb-0 res-more" href="{{route('article.details', $sel->slug )}}"
                                        style="float:right; color:darkcyan">Read more <i
                                            class="fa fa-angle-right ms-1 res-more"></i></a>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endforeach
                    <div class="col-md-12 text-end m-2">
                        <a href="{{ route('frontend.reviews',['slug' => 'tools_&_expert_device'])}}" style="border-bottom:1px solid black" ><strong>View more Tools & Expert Device</strong></a>
                    </div>
                </div>
            </div>
            @endif
        </div>
</section>
<section style="margin-top:-45px" class="sptb bg-white">
    <div class="container">
        <div>
            @if($shops->count() > 1)
            <div class="resharch-content-title">
                <h4 style="font-size:24px" class="mb-3">Latest Car Buying Advice</h4>
            </div>
            @endif

            @if($shops->isNotEmpty())
            <div class="col-md-12 m-0 p-0">
                <div class="row p-0" id="cars-containers">
                    @foreach($shops as $key => $shop)

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-5">

                        <div style="width:100%" class="card">
                            <a href="{{route('article.details', $shop->slug )}}" class="text-decoration-none">
                                <div class="">

                                    @if (isset($shop->img))
                                    <img class="res-first-img" style="width:100%; height:195px"
                                        src="{{ asset('frontend/assets/images/blog/' . $shop->img) }}"
                                        alt="Used cars for sale for Best Dream car {{ $shop->title }} image"
                                        onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';" />

                                    @else
                                    <a href="#">
                                        <img class="res-first-img" style="width:100%; height:195px"
                                            src="{{ asset('frontend/found/NotFound.png') }}" alt="img" >
                                    </a>

                                    @endif
                                </div>

                                @php
                                $title = Str::limit($shop->title, 20, '...');
                                $sub_title = Str::limit($shop->sub_title, 105, '...');
                                @endphp

                                <div class="mt-1 resh-tit p-3">
                                    <h5 style="margin-top:0px" class="fs-18  text-dark res-des hyperlink-title">{{ $shop->title }}</h5>
                                    <p style="height:80px;" class="res-par">{{ $sub_title }}</p>
                                    <a class="mt-5 mb-0 res-more" href="{{route('article.details', $shop->slug )}}"
                                        style="float:right; color:darkcyan">Read more <i
                                            class="fa fa-angle-right ms-1 res-more"></i></a>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endforeach
                    <div class="col-md-12 text-end m-2">
                        <a href="{{ route('frontend.reviews',['slug' => 'car_buying_advice'])}}" style="border-bottom:1px solid black" ><strong>View more Latest Car Buying Advice</strong></a>
                    </div>
                </div>
            </div>
            @endif


        </div>
</section>
<section style="margin-top:-45px" class="sptb bg-white">
    <div class="container">
        <div>
            @if($shops->count() > 1)
            <div class="resharch-content-title">
                <h4 style="font-size:24px" class="mb-3">Latest Car Tips</h4>
            </div>
            @endif

            @if($shops->isNotEmpty())
            <div class="col-md-12 m-0 p-0">
                <div class="row p-0" id="cars-containers">
                    @foreach($tips as $key => $tip)

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-5">

                        <div style="width:100%" class="card">
                            <a href="{{route('frontend.tips.details', $tip->slug )}}" class="text-decoration-none">
                                <div class="">

                                    @if (isset($tip->img))
                                    <img class="res-first-img" style="width:100%; height:195px"
                                        src="{{ asset('frontend/assets/images/tips/' . $tip->img) }}"
                                        alt="Used cars for sale for Best Dream car {{ $tip->title }} image"
                                        onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';" />

                                    @else
                                    <a href="#">
                                        <img class="res-first-img" style="width:100%; height:195px"
                                            src="{{ asset('frontend/found/NotFound.png') }}" alt="img" >
                                    </a>

                                    @endif
                                </div>

                                @php
                                $title = Str::limit($tip->title, 20, '...');
                                $sub_title = Str::limit($tip->sub_title, 105, '...');
                                $des = Str::limit(strip_tags($tip->description), 100, '...');
                                @endphp

                                <div class="mt-1 resh-tit p-3">
                                    <h5 style="margin-top:0px" class="fs-18  text-dark res-des hyperlink-title">{{ $tip->title }}</h5>
                                    <p style="height:80px;" class="res-par">{{ $des }}</p>
                                    <a class="mt-5 mb-0 res-more" href="{{route('frontend.tips.details', $tip->slug )}}"
                                        style="float:right; color:darkcyan">Read more <i
                                            class="fa fa-angle-right ms-1 res-more"></i></a>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endforeach
                    <div class="col-md-12 text-end m-2">
                        <a href="{{ route('frontend.tips.page')}}" style="border-bottom:1px solid black" ><strong>View more Latest Car Tips</strong></a>
                    </div>
                </div>
            </div>
            @endif


        </div>
</section>
<section style="margin-top:-45px" class="sptb bg-white">
    <div class="container">
        <div>
            @if($owners->count() > 1)
            <div class="resharch-content-title">
                <h4 style="font-size:24px" class="mb-3">Beyond Cars</h4>

            </div>
            @endif

            @if($owners->isNotEmpty())
            <div class="col-md-12 m-0 p-0">
                <div class="row p-0" id="cars-containers">
                    @foreach($owners as $key => $own)
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-5">
                        <div style="width:100%" class="card">
                            <a href="{{route('article.details', $own->slug )}}" class="text-decoration-none">
                                <div class="">

                                    @if (isset($own->img))
                                    <img class="res-first-img" style="width:100%; height:195px"
                                        src="{{ asset('frontend/assets/images/blog/' . $own->img) }}"
                                        alt="Used cars for sale for Best Dream car {{ $own->title }} image"
                                        onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';" />

                                    @else
                                    <a href="#">
                                        <img class="res-first-img" style="width:100%; height:195px" src=" {{
                                            asset('frontend/found/NotFound.png') }}" alt="img" >
                                    </a>

                                    @endif
                                </div>
                                @php
                                $title = Str::limit($own->title, 20, '...');
                                $sub_title = Str::limit($own->sub_title, 150, '...');
                                @endphp

                                <div class="mt-1  resh-tit p-3">
                                    <h5 style="margin-top:0px" class="fs-18  text-dark res-des hyperlink-title">{{ $own->title }}</h5>
                                    <p style="height:80px" class="news-par">{{ $sub_title }}</p>
                                    <a class="mt-5 mb-0 res-more" href="{{route('article.details', $own->slug )}}"
                                        style="float:right; color:darkcyan">Read more <i
                                            class="fa fa-angle-right ms-1 "></i></a>
                                </div>
                            </a>
                        </div>

                    </div>

                    @endforeach
                    <div class="col-md-12 text-end m-2">
                        <a href="{{ route('frontend.reviews',['slug' => 'beyond_cars'])}}" style="border-bottom:1px solid black" ><strong>View more Beyond Cars</strong></a>
                    </div>
                </div>
            </div>
            @endif
        </div>
</section>

<section class="sptb bg-white">
    <div class="container">
        @if($faqs->count() > 1)
        <hr>
        @endif

        <div>
            <div>
                <div class="faq-content">
                    @if($faqs->count() > 1)
                    <h2>Frequently Asked Questions</h2>
                    @endif

                    @if($faqs->isNotEmpty())
                    <div class="faq-articles">
                        @foreach ($faqs as $index => $faq)


                        <article class="faq-accordion">

                            <!-- Input checkbox to toggle -->
                            <input type="checkbox" class="tgg-title" id="tgg-title-{{ $faq->id }}" {{ $index==0
                                ? 'checked' : '' }}>

                            <div class="faq-accordion-title">
                                <label for="tgg-title-{{ $faq->id }}">
                                    <h2>{{ $faq->title }}</h2>
                                    <span class="arrow-icon">
                                        <img src="https://raw.githubusercontent.com/Romerof/FAQ-accordion-card/main/images/icon-arrow-down.svg"
                                            alt="Used cars for sale for Best Dream car arrow icon image">
                                    </span>
                                </label>
                            </div>

                            <div class="faq-accordion-content">
                                <p>{!! $faq->description !!}</p>

                            </div>

                        </article>

                        @endforeach







                    </div> <!-- faq articles -->
                    @endif


                    </main> <!-- faq -->

                </div> <!-- faq card -->

            </div>
        </div>
</section>



@endsection

@push('js')
<script>

</script>
@endpush
