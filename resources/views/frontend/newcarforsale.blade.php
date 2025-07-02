@extends('frontend.website.layout.app')
<?php
use Illuminate\Support\Facades\Cookie;
$cookie_zipcode = request()->cookie('zipcode') ?? '';
?>
@foreach (app('globalStaticPage') as $page)
    @if ($page->slug == 'new-cars')
        @if ($page->description)
            @section('meta_description', $page->description)
        @else
            @section('meta_description',
                'Find the perfect car for your needs at '.
                route('home') .
                ' Shop new and used
                cars, sell your car, compare prices, and explore financing options to find your Best Dream car today!' ??
                app('globalSeo')['description'])
            @endif
            @if ($page->keyword)
                @section('meta_keyword', $page->keyword)
            @else
            @section('meta_keyword', app('globalSeo')['keyword'])
        @endif
        @section('title')
            {{ $page->title }}
        @endsection
    @endif
@endforeach

@section('gtm')
    {!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])
{{--@section('og_title', 'New Cars || Used Cars, Car News, Car Reviews and Pricing listing | Best Dream car' ??
    app('globalSeo')['og_title'])
@section('og_description', app('globalSeo')['og_description'])--}}

@section('og_title')
            {{ $page->title }}
@endsection

@section('og_description')
    {{ $page->description }}
@endsection

@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])
{{--@section('twitter_title', 'New Cars || Used Cars, Car News, Car Reviews and Pricing listing | Best Dream car' ??
    app('globalSeo')['twitter_title'])
@section('twitter_description', app('globalSeo')['twitter_description'])--}}

@section('twitter_title')
            {{ $page->title }}
@endsection

@section('twitter_description')
    {{ $page->description }}
@endsection


@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])

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
        font-size: 16px
            /*var(--font-size)*/
        ;
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
        max-height: 150px;
    }

    /* card css */
    /* card css */
    .flex-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .flex-item {
        flex: 1 1 calc(33.333% - 10px);
        max-width: calc(33.333% - 10px);

    }

    .flex-item-car {
        flex: 1 1 calc(16.5% - 10px);
        max-width: calc(16.5% - 10px);
        border-radius: 5px;
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

        .flex-item-car {
            flex: 1 1 calc(33.333% - 10px);
            max-width: calc(33.333% - 10px);

        }

        .car-type-name {
            font-size: 10px !important;
            margin-bottom: 10px
        }

        #text5::placeholder {
        color:rgb(22, 22, 22); /* Replace with your desired color */
}
    }
</style>
<section style="background:#f8f8f8;" class="sptb">
    <div class="container">
        @php
        $data = \App\Models\Banner::where('position', 'new cars search page top')->first();
        @endphp

        <div class="new-top-ban" style="margin-top:40px; width:100%; height:180px; text-align:center;">
            @isset($data->image)
            <img style="margin:0 auto; width:80%; height:100px; margin-top:55px" src="{{ asset('/dashboard/images/banners/' .$data->image) }}" alt="New cars for sale Best Dream car new car image"/>
            @else
            <img style="width:728px; height:90px;" src="{{ asset('/dashboard/images/banners/top.png') }}"
            alt="Used cars for sale dealer banner image dream best" />
            @endisset
        </div>
    </div>
</section>
<section class="bg-white sptb">
    <div style="margin-bottom:55px; margin-top:40px" class="container">
        <h2 style="text-align:center" class="new-top-title">New cars for sale</h2>
        <p style="text-align:center; margin-bottom:5px; font-size:16px">Find one that fits your needs and your budget
        </p>
        <div class="mb-0 header-text1">
            <div class="container">
                <div class="row">
                    <div class="mx-auto col-xl-10 col-lg-12 col-md-12 d-block">

                        <div class="mt-5 bg-transparent search-background">
                            <form action="{{ route('auto.new') }}" method="get">
                                <input type="hidden" name="homeBodySearch" value="new">
                                <input type="hidden" name="home2" value="{{ true }}">
                                <div class="form row no-gutters ">
                                    <div class="mb-0 bg-transparent form-group col-xl-3 col-lg-3 col-md-12 select2-lg w-100 new-car-input" style="border-radius:0px !important;">
                                        <select style="height:46px !important; border:1px solid rgb(230, 229, 229) !important; color:rgb(70, 70, 70)"  id="searchSecondFilterMakeInput" class="w-100 makeData select2 new-make-style form-select" name="make">
                                            <option value="">Select Make</option>
                                            @foreach ($vehicles as $vehicle_info)
                                            <option value="{{ $vehicle_info->make_name }}" data-makeid="{{ $vehicle_info->id }}">{{ $vehicle_info->make_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div style="border-left:none" class="mb-0 bg-transparent form-group col-xl-3 col-lg-3 col-md-12 select2-lg new-car-input">
                                        <select style="height:46px !important; color:rgb(70, 70, 70)" id="searchSecondFilterModelInput" class="form-select makeData select2 border-start-0 border-end-0 new-model-style" name="model">
                                            <option value="">Select Model</option>
                                        </select>
                                    </div>
                                    <div class="mb-0 bg-transparent form-group col-xl-3 col-lg-3 col-md-12">
                                        <input style="height:46px !important;" type="text" class="form-control br-md-0 " id="text5" placeholder="Enter Location" name="zip" value="{{ $cookie_zipcode ?? ''}}">
                                        <span><i class="fa fa-map-marker location-gps me-1"></i> </span>
                                    </div>

                                    <div class="mb-0 col-xl-2 col-lg-3 col-md-12">
                                        <button style="padding-bottom:7px; height:46px !important; background:darkcyan; color:white;font-size:14px" class="right-0 btn btn-block br-bs-md-0 br-ts-md-0 ">Search Here</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /header-text -->
    </div>
</section>
<section class="bg-white sptb">
    <div class="container all-sell">
        <div class="p-0 m-0 row">
            <div style="margin-top:0px; margin-bottom:15px" class="col-xl-5 col-lg-5 col-md-5 col-s2">
                <h3 class="mb-4 car-title-for-sell">Sell your car yourself</h3>
                <p>Vetted buyers. Smart tools. The offer you deserve.</p>
                <button style="background:rgb(18, 176, 197) ; border:1px solid rgb(18, 176, 197) ; color:white; font-size:16px; border-radius:3px" class="p-2 mb-4">Sell Your Car</button>
                <img style="height:323px" class="sell-car-image" src="{{ asset('/frontend/assets/images/cn.jpg') }}" />
            </div>
            <div style="margin-top:0px; margin-left:95px" class="col-xl-6 col-lg-6 col-md-6 col-sm-12 certified-show">
                <h3 class="mb-3 new-carsale-title">Browse Certified New Vehicles</h3>
                <p class="mb-5 new-carsale-article">If you're in the market for a new vehicle, browsing certified new vehicles offers several advantages.
                    Certified new vehicles undergo rigorous inspections and providing peace of mind regarding quality
                    and reliability.</p>

                <div class="p-2 flex-container">
                    <div class="flex-item">
                        <a href="{{ route('auto.new', ['make' => 'Ford','homeBodySearch'=>'new']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/ford.svg') }}" alt="Ford" />
                        </a>
                    </div>
                    <div class="flex-item">
                        <a href="{{ route('auto.new', ['make' => 'GMC','homeBodySearch'=>'new']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/gmc.svg') }}" alt="GMC" />
                        </a>
                    </div>
                    <div class="flex-item">
                        <a href="{{ route('auto.new', ['make' => 'BMW','homeBodySearch'=>'new']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/bmw.svg') }}" alt="BMW" />
                        </a>
                    </div>
                    <div class="flex-item">
                        <a href="{{ route('auto.new', ['make' => 'Toyota','homeBodySearch'=>'new']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/toyota.svg') }}" alt="Toyota" />
                        </a>
                    </div>
                    <div class="flex-item">
                        <a href="{{ route('auto.new', ['make' => 'Honda','homeBodySearch'=>'new']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/honda.svg') }}" alt="Honda" />
                        </a>
                    </div>
                    <div class="flex-item">
                        <a href="{{ route('auto.new', ['make' => 'Dodge','homeBodySearch'=>'new']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/dodge.svg') }}" alt="Dodge" />
                        </a>
                    </div>
                    <div class="flex-item">
                        <a href="{{ route('auto.new', ['make' => 'Chevrolet','homeBodySearch'=>'new']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/chevrolet.svg') }}" alt="Chevrolet" />
                        </a>
                    </div>
                    <div class="flex-item">
                        <a href="{{ route('auto.new', ['make' => 'Jeep','homeBodySearch'=>'new']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/jeep.svg') }}" alt="Jeep" />
                        </a>
                    </div>
                    <div class="border flex-item browse-all">
                        <a href="{{ route('auto.new') }}">
                            <h4 class="text-center browse-btn-text">Browse All</h4>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white sptb">
    <div style="border-radius:5px" class="container p-5 bg-white">
        <div class="">
            <h2 style="margin-top:15px; margin-bottom:15px" class="find-car-style">Find new cars by body type</h2>
            <p class="mb-5">Tools and buying guides for car shoppers.</p>
        </div>
        <div class="p-2 flex-container">
            <div style="border:1px solid rgb(226, 226, 226);" class="p-3 flex-item-car">
                <a href="{{ route('auto.new', ['body' =>'full size suv','home'=> true,'homeBodySearch'=>'new']) }}">
                    <img src="{{ asset('/frontend/assets/images/suv.png') }}" />
                    <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">SUV/Crossover</h6>
                </a>
            </div>
            <div style="border:1px solid rgb(226, 226, 226);" class="flex-item-car">
                <a href="{{ route('auto.new', ['body' =>'sedan','home'=> true,'homeBodySearch'=>'new']) }}">
                    <img src="{{ asset('/frontend/assets/images/sedan.png') }}" />
                    <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Sedan</h6>
                </a>

            </div>
            <div style="border:1px solid rgb(226, 226, 226);" class="flex-item-car">
                <a href="{{ route('auto.new', ['body' =>'hatchback','home'=> true,'homeBodySearch'=>'new']) }}">
                    <img src="{{ asset('/frontend/assets/images/hatchback.png') }}" />
                    <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Hatchback</h6>
                </a>
            </div>
            <div style="border:1px solid rgb(226, 226, 226);" class="flex-item-car">
                <a href="{{ route('auto.new', ['body' =>'truck','home'=> true,'homeBodySearch'=>'new']) }}">
                    <img src="{{ asset('/frontend/assets/images/truck.png') }}" />
                    <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Pickup Truck</h6>
                </a>
            </div>
            <div style="border:1px solid rgb(226, 226, 226);" class="flex-item-car">
                <a href="{{ route('auto.new', ['body' =>'coupe','home'=> true,'homeBodySearch'=>'new']) }}">
                    <img src="{{ asset('/frontend/assets/images/coupe.png') }}" />
                    <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Coupe</h6>
                </a>
            </div>
            <div style="border:1px solid rgb(226, 226, 226);" class="flex-item-car">
                <a href="{{ route('auto.new', ['body' =>'convertible','home'=> true,'homeBodySearch'=>'new']) }}">
                    <img src="{{ asset('/frontend/assets/images/convertible.png') }}" />
                    <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Convertible</h6>
                </a>
            </div>
            <div style="border:1px solid rgb(226, 226, 226);" class="flex-item-car">
                <a href="{{ route('auto.new', ['body' =>'Station Wagon','home'=> true,'homeBodySearch'=>'new']) }}">
                    <img src="{{ asset('/frontend/assets/images/wagon.png') }}" />
                    <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Wagon</h6>
                </a>
            </div>
            <div style="border:1px solid rgb(226, 226, 226);" class="flex-item-car">
                <a href="{{ route('auto.new', ['body' =>'minivan','home'=> true,'homeBodySearch'=>'new']) }}">
                    <img src="{{ asset('/frontend/assets/images/minivan.png') }}" />
                    <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Minivan</h6>
                </a>
            </div>
        </div>
    </div>
</section>
<section style="background:#f8f8f8;" class="sptb">
    <div class="container">
        <div class="text-center section-title center-block">
            <h2>Tips & Advice</h2>
            <p>Find your best car in Best Dream car</p>
        </div>
        <div class="col-md-12">
            <div class="items-gallery">
                <div class="text-center items-blog-tab">
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-1">
                        <div class="row" id="cars-containers">
                            <div class="col-xl-4 col-lg-4 col-md-12">
                                <div class="card">
                                    <div class="item-card8-img br-ts-7 br-bs-7">
                                        <img class="tips-img" style="width:100%; height:233px" src="{{ asset('/frontend/assets/images/ee.jpg') }}" />
                                    </div>

                                    <div class="card-body">
                                        <div class="item-card8-desc">

                                            <a class="text-dark" href="#">
                                                <h5 style="font-size:16px" class="font-weight-semibold">The Best Used
                                                    Electric Cars in 2024</h5>
                                            </a>
                                            <p class="mt-2 mb-2">When you’re shopping for a new electric vehicle, price
                                                tags can be intimidating. But EVs have been around long enough now that
                                                they're hitting the used market at a decent discount.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-12">
                                <div class="card">

                                    <div class="item-card8-img br-ts-7 br-bs-7">
                                        <img class="tips-img" style="width:100%" src="{{ asset('/frontend/assets/images/tt.webp') }}" />
                                    </div>

                                    <div class="card-body">
                                        <div class="item-card8-desc">

                                            <a class="text-dark" href="#">
                                                <h5 style="font-size:16px" class="font-weight-semibold">The Best
                                                    Trucks Under $20,000 in 2024</h5>
                                            </a>
                                            <p class="mt-2 mb-2">Used truck prices are finally starting to cool, but
                                                new ones cost more than ever, with average prices over $60,000.
                                                Fortunately, you can still get a great pickup on a budget.</p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-12">
                                <div class="card">

                                    <div class="item-card8-img br-ts-7 br-bs-7">
                                        <img class="tips-img" style="width:100%; height:233px" src="{{ asset('/frontend/assets/images/ss.jpg') }}" />
                                    </div>

                                    <div class="card-body">
                                        <div class="item-card8-desc">

                                            <a class="text-dark" href="#">
                                                <h5 style="font-size:16px" class="font-weight-semibold">The Best Used
                                                    Small SUVs in 2024</h5>
                                            </a>
                                            <p class="mt-2 mb-2">Small SUVs have soared in popularity in recent years,
                                                which in turn has driven more choice than ever before. Here are our
                                                favourite models on the used market.  </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<section class="p-2 bg-white sptb">
    <div style="border-radius:5px" class="container p-2 bg-white">
        <div class="text-center section-title center-block">
            <h4 style="margin-top:25px; font-size:21px">Popular Sedans</h4>
        </div>

        <div style="margin: 0 auto" class="row make-list">

        @forelse($sedans as $key => $sedan)
            <div class="mb-0 col-md-4 col-lg-3 col-xs-6 make-card">
                <div class="p-0 mt-0 mb-2 mb-lg-0 br-3">
                    <div class="media-body">
                        <h4 class="mt-4 mb-1 fs-15 fw-medium">
                            <a href="{{ route('auto.new', ['make' => ucwords(strtolower($sedan->make)), 'body'=> 'sedan','homeBodySearch'=>'new','home'=>true]) }}" class="text-body" style="color:rgb(18, 176, 197) !important">{{'NEW '.$sedan->make}}</a>
                        </h4>
                    </div>
                </div>

                <p style="font-size:13px">{{$sedan->count}} listings starting at ${{ number_format($sedan->min_price)}}
                </p>
            </div>

        @empty
        @endforelse
        </div>
    </div>
</section>
<section class="p-2 bg-white sptb">
    <div style="border-radius:5px" class="container p-2 bg-white">
        <div class="text-center section-title center-block">

            <h4 style="margin-top:25px; font-size:21px">Popular SUVs / Crossovers</h4>
        </div>

        <div style="margin: 0 auto" class="row make-list">

        @forelse($suvs as $key => $suv)

            <div class="mb-0 col-md-4 col-lg-3 col-xs-6 make-card">
                <div class="p-0 mt-0 mb-2 mb-lg-0 br-3">
                    <div class="media-body">
                        <h4 class="mt-4 mb-1 fs-15 fw-medium">
                            <a href="{{ route('auto.new', ['make' => ucwords(strtolower($suv->make)), 'body'=> 'full size suv','homeBodySearch'=>'new','home'=>true]) }}" class="text-body" style="color:rgb(18, 176, 197) !important">{{'NEW '.$suv->make}}</a>
                        </h4>
                    </div>
                </div>

                <p style="font-size:13px">{{$suv->count}} listings starting at ${{ number_format($suv->min_price)}}
                </p>
            </div>
        @empty
        @endforelse
        </div>
    </div>
</section>

<section style="padding-top: -45px" class="bg-white ">
    <div style="border-radius:5px;" class="container p-5 bg-white">
        <div class="text-center section-title center-block">
            <h4 style="margin-top:25px; font-size:21px">Popular Pickup Trucks</h4>
        </div>

        <div style="margin: 0 auto" class="row make-list">
        @forelse($trucks as $key => $truck)

            <div class="mb-0 col-md-4 col-lg-3 col-xs-6 make-card">
                <div class="p-0 mt-0 mb-2 mb-lg-0 br-3">
                    <div class="media-body">
                        <h4 class="mt-4 mb-1 fs-15 fw-medium">
                            <a href="{{ route('auto.new', ['make' => ucwords(strtolower($truck->make)), 'body'=> 'truck','homeBodySearch'=>'new','home'=>true]) }}" class="text-body" style="color:rgb(18, 176, 197) !important">{{'NEW '.$truck->make}}</a>
                        </h4>
                    </div>
                </div>

                <p style="font-size:13px">{{$truck->count}} listings starting at ${{ number_format($truck->min_price)}}
                </p>
            </div>
        @empty
        @endforelse

        </div>
    </div>
</section>


<section class="p-2 bg-white sptb">
    <div style="border-radius:5px" class="container p-2 bg-white">
        <div class="text-center section-title center-block">
            <h4 style="margin-top:25px; font-size:21px">Popular Coupes</h4>
        </div>

        <div style="margin: 0 auto" class="row make-list">

        @forelse($coupes as $key => $coupe)
            <div class="mb-0 col-md-4 col-lg-3 col-xs-6 make-card">
                <div class="p-0 mt-0 mb-2 mb-lg-0 br-3">
                    <div class="media-body">
                        <h4 class="mt-4 mb-1 fs-15 fw-medium">
                            <a href="{{ route('auto.new', ['make' => ucwords(strtolower($coupe->make)), 'body'=> 'coupe','homeBodySearch'=>'new','home'=>true]) }}" class="text-body" style="color:rgb(18, 176, 197) !important">{{'NEW '.$coupe->make}}</a>
                        </h4>
                    </div>
                </div>

                <p style="font-size:13px">{{$coupe->count}} listings starting at ${{ number_format($coupe->min_price)}}
                </p>
            </div>
        @empty
        @endforelse
        </div>
    </div>
</section>

<section class="p-2 bg-white sptb">
    <div style="border-radius:5px; margin-bottom:45px" class="container p-2 bg-white">
        <div class="text-center section-title center-block">
            <h4 style="margin-top:25px; font-size:21px">Popular Wagons</h4>
        </div>

        <div style="margin: 0 auto" class="row make-list">

        @forelse($wagons as $key => $wagon)
            <div class="mb-0 col-md-4 col-lg-3 col-xs-6 make-card">
                <div class="p-0 mt-0 mb-2 mb-lg-0 br-3">
                    <div class="media-body">
                        <h4 class="mt-4 mb-1 fs-15 fw-medium">
                            <a href="{{ route('auto.new', ['make' => ucwords(strtolower($wagon->make)), 'body'=> 'station wagon','homeBodySearch'=>'new','home'=>true]) }}" class="text-body" style="color:rgb(18, 176, 197) !important">{{'NEW '.$wagon->make}}</a>
                        </h4>
                    </div>
                </div>

                <p style="font-size:13px">{{$wagon->count}} listings starting at ${{ number_format($wagon->min_price)}}
                </p>
            </div>
        @empty
        @endforelse
        </div>
    </div>
</section>

<section class="p-2 bg-white sptb">
    <div style="border-radius:5px; margin-bottom:45px" class="container p-2 bg-white">
        <div class="text-center section-title center-block">
            <h4 style="margin-top:25px; font-size:21px">Popular Minivans</h4>
        </div>

        <div style="margin: 0 auto" class="row make-list">

        @forelse($minivans as $key => $minivan)
            <div class="mb-0 col-md-4 col-lg-3 col-xs-6 make-card">
                <div class="p-0 mt-0 mb-2 mb-lg-0 br-3">
                    <div class="media-body">
                        <h4 class="mt-4 mb-1 fs-15 fw-medium">
                            <a href="{{ route('auto.new', ['make' => ucwords(strtolower($minivan->make)), 'body'=> 'minivan','homeBodySearch'=>'new','home'=>true]) }}" class="text-body" style="color:rgb(18, 176, 197) !important">{{'NEW '.$minivan->make}}</a>
                        </h4>
                    </div>
                </div>

                <p style="font-size:13px">{{$minivan->count}} listings starting at ${{ number_format($minivan->min_price)}}
                </p>
            </div>
        @empty
        @endforelse
        </div>
    </div>
</section>


<section class="p-2 bg-white sptb">
    <div style="border-radius:5px" class="container p-2 bg-white">
        <div class="text-center section-title center-block">
            <h4 style="margin-top:25px; font-size:21px">Popular Hatchbacks</h4>
        </div>

        <div style="margin: 0 auto" class="row make-list">

        @forelse($hatchbacks as $key => $hatchback)
            <div  style="margin-bottom:75px !important" class="mb-0 col-md-4 col-lg-3 col-xs-6 make-card">
                <div class="p-0 mt-0 mb-2 mb-lg-0 br-3">
                    <div class="media-body">
                        <h4 class="mt-4 mb-1 fs-15 fw-medium">
                            <a href="{{ route('auto.new', ['make' => ucwords(strtolower($hatchback->make)), 'body'=> 'hatchback','homeBodySearch'=>'new','home'=>true]) }}" class="text-body" style="color:rgb(18, 176, 197) !important">{{'NEW '.$hatchback->make}}</a>
                        </h4>
                    </div>
                </div>

                <p style="font-size:13px">{{$hatchback->count}} listings starting at ${{ number_format($hatchback->min_price)}}
                </p>
            </div>
        @empty
        @endforelse
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>

    $(document).ready(function() {
        $('#searchSecondFilterMakeInput').on('change', function() {
            var makeId = $(this).find('option:selected').data('makeid');
            var url = "{{ route('homePage.modelSearch', '') }}" + '/' + makeId;
            $.ajax({
                url: url,
                typs: 'post',
                data: {
                    id: makeId
                },
                success: function(res) {
                    $('#searchSecondFilterModelInput').empty();
                    $('#searchSecondFilterModelInput').append(
                        '<option value="">Choose Model</option>')
                    $.each(res, function(index, item) {
                        console.log(item.model_name);
                        // var option = $('<option>');
                        // option.val(index);
                        // option.text(item)
                        var option = "<option value='" + item.model_name + "' data-id='" +
                            index + "'>" + item.model_name + "</option>"
                        $('#searchSecondFilterModelInput').append(option);
                    });
                },
                error: function(error) {

                }
            });
        });

//  $('#searchSecondFilterMakeInput').select2();
//  $('#searchSecondFilterModelInput').select2();


            // $('#searchSecondFilterMakeInput').on('select2:open', function(e) {
            //     focusSearchField(this);
            // });
    });


</script>
@endsection
