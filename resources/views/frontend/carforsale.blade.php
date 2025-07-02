@extends('frontend.website.layout.app')
<?php
use Illuminate\Support\Facades\Cookie;
$cookie_zipcode = request()->cookie('zipcode') ?? '';

?>
@foreach (app('globalStaticPage') as $page)
    @if ($page->slug == 'used-cars')
        @if ($page->description)
            @section('meta_description', $page->description)
        @else
            @section('meta_description',
                'Find the perfect car for your needs at ' .
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

{{--@section('og_title', 'Cars For Sale || New Cars, Used Cars, Car News, Car Reviews and Pricing listing | Best Dream car' ??
    app('globalSeo')['og_title'])--}}
    {{--@section('og_description', app('globalSeo')['og_description'])--}}
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

{{--@section('twitter_title', 'Cars For Sale || New Cars, Used Cars, Car News, Car Reviews and Pricing listing | Best Dream car' ??
    app('globalSeo')['twitter_title'])--}}
{{--@section('twitter_description', app('globalSeo')['twitter_description'])--}}

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

<div class="modal fade" id="reqModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Listing Add For sell</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                @php
                $user = auth()->user();
                @endphp
                <div class="row">
                    <div class="p-2 col-md-12">
                        <form id="reqAddInventoryAll" action="{{ route('request.inventory.store') }}" method="post" enctype="multipart/form-data"
                            style="background-color: #d6d6d6">
                            @csrf
                            <div class="">
                                <div class="p-2 row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 " style="margin-top:20px">
                                        <div class="form-group ">
                                            <input type="hidden" id="inventory_id" name="inventories_id"
                                                value="">
                                            <input type="hidden" id="dealer_id" name="dealer_id" value="">
                                            <input style="border-radius:5px; color:black" placeholder="First Name*"
                                                class="form-control fname" type="text" id="first_name"
                                                name="first_name" value="{{Auth()->check() ? Auth()->user()->fname : ''}}">
                                                <span style="" class="text-danger error-message mb-2  ms-3" id="first_name-error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 second-name" style="margin-top:20px">
                                        <div class="form-group ">
                                            <input style="border-radius:5px; color:black" placeholder="Last Name*"
                                                class="form-control lname" type="text" id="last_name"
                                                name="last_name" value="{{Auth()->check() ? Auth()->user()->lname : ''}}">
                                                <span style="" class="text-danger error-message mb-2  ms-3" id="last_name-error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">
                                            <input style="border-radius:5px; color:black" placeholder="E-mail*"
                                                class="form-control email" type="text" id="email"
                                                name="email" value="{{Auth()->check() ? Auth()->user()->email : ''}}">
                                                <span style="" class="text-danger error-message mb-2  ms-3" id="email-error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">
                                            <input style="border-radius:5px; color:black"
                                                class="form-control phone telephoneInput" type="text"
                                                placeholder="cell" id="phone" name="phone" value="{{Auth()->check() ? Auth()->user()->phone : ''}}">

                                                <span style="" class="text-danger error-message mb-2  ms-3" id="phone-error"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">

                                            <input style="border-radius:5px" placeholder="Year*"
                                                class="form-control year_trade" type="text" name="year"
                                                value="">
                                                <span style="" class="text-danger error-message mb-2  ms-3" id="year-error"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">

                                            <input style="border-radius:5px" placeholder="Make*"
                                                class="form-control make_trade" type="text" name="make"
                                                value="">

                                                <span style="" class="text-danger error-message mb-2  ms-3" id="make-error"></span>

                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">

                                            <input style="border-radius:5px" placeholder="Model*"
                                                class="form-control model_trade" type="text"
                                                name="model" value="">

                                                <span style="" class="text-danger error-message mb-2  ms-3" id="model-error"></span>

                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">

                                            <input style="border-radius:5px" placeholder="Price*"
                                                class="form-control mileage" type="text" name="price"
                                                value="">

                                                <span style="" class="text-danger error-message mb-2  ms-0"
                                                id="price-error"></span>

                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">

                                            <input style="border-radius:5px" placeholder="Mileage*"
                                                class="form-control mileage" type="text" name="miles"
                                                value="">

                                                <span style="" class="text-danger error-message mb-2  ms-3" id="miles-error"></span>

                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">

                                            <input style="border-radius:5px" placeholder="Color*"
                                                class="form-control color" type="text" name="exterior_color"
                                                value="">

                                                <span style="" class="text-danger error-message mb-2  ms-3"
                                                id="exterior_color-error"></span>

                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">
                                            <input style="border-radius:5px" placeholder="VIN*"
                                                class="form-control vin" type="text" name="vin"
                                                value="">
                                                <span style="" class="text-danger error-message mb-2  ms-3" id="vin-error"></span>

                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">
                                            <input style="border-radius:5px" placeholder="Drivetrain*"
                                                class="form-control vin" type="text" name="drive_info"
                                                value="">
                                                <span style="" class="text-danger error-message mb-2  ms-3"
                                                id="drive_info-error"></span>

                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">
                                            <input style="border-radius:5px" placeholder="Transmission*"
                                                class="form-control vin" type="text" name="transmission"
                                                value="">
                                                <span style="" class="text-danger error-message mb-2  ms-3" id="transmission-error"></span>

                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">
                                            <input style="border-radius:5px" placeholder="Fuel Type*"
                                                class="form-control vin" type="text" name="fuel"
                                                value="">
                                                <span style="" class="text-danger error-message mb-2  ms-3" id="fuel-error"></span>

                                        </div>
                                    </div>
                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                        <div style="width:95.8%; margin-left:12px" class="form-outline mb-4 p-0">
                                        <label for="images" class="form-label">Upload Multiple Images</label>
                                        <input class="form-control" type="file" id="imageUpload" name="img_from_url[]" multiple accept="image/*" />
                                        <span style="" class="text-danger error-message mb-2  ms-3" id="img_from_url-error"></span>
                                        <div id="preview"></div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-12 col-sm-12 col-xs-12 ">
                                        <div class="form-group ">
                                            <p
                                                style="color:rgb(168, 11, 155); font-weight:bold; margin-bottom:15px; margin-top:10px">
                                                <span class="text-danger">*</span> Security Question (Enter the Correct
                                                answer)
                                            </p>
                                            <div style="display: flex">
                                                <div id="captchaLabel"
                                                    style="background-color:white; width:50%;  margin-right:10px; text-align:center; padding-top:7px; font-weight:600;  margin-top:2px; margin-left:3px; height:35px; border-radius:5px">

                                                    {{ app('mathcaptcha')->label(true) }}

                                                </div>
                                                <div>
                                                    <input id="captchaInput"
                                                        class="form-control @error('mathcaptcha') is-invalid @enderror"
                                                        type="text" name="mathcaptcha"
                                                        placeholder="Enter your result">
                                                    <span id="Amathcaptcha" class="text-danger" role="alert">
                                                        @error('mathcaptcha')
                                                        {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>
                                            </div>

                                        </div>
                                    </div> --}}




                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12 ">
                                    <div class="form-group">

                                        <button
                                            style="background:rgb(68, 29, 70); width:100%; margin-bottom:15px; color:white; font-size:16px;"
                                            type="submit" class="btn req_button">Add Listing</button>

                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- Form -->

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>




    <section style="background:#f8f8f8;" class="sptb">
        @php
            $data = \App\Models\Banner::where('position', 'cars for sale page top')->first();
        @endphp

        <div class="container">
            <p style="color:black;" class="mt-5">Home / Used car</p>

            @isset($data->image)
            <div style="margin-top:40px; width:100%; text-align:center; margin-bottom:15px; height:165px">
                <img style="margin: 0 auto; width: 80%; height: 100px; margin-top:35px" src="{{ asset('/dashboard/images/banners/' . $data->image) }}"  alt="Used cars for sale Best Dream car for sale top image"/>

            </div>
            @else

            <img style="width:728px; height:90px;" src="{{ asset('/dashboard/images/banners/top.png') }}"
            alt="Used cars for sale dealer cars for sale top banner image dream best"/>

            @endisset
        </div>
    </section>


    <section class="sptb bg-white">
        <div class="container">

            <h2 style="text-align:center" class="cars-forsale-title">Shopping for a Used Car?</h2>
            <p style="text-align:center; margin-bottom:20px; font-size:18px">Know more, shop wisely</p>

            <div class="row p-0 m-0">

                    <div style="margin-top:30px" class="col-xl-5 col-lg-5 col-md-5 col-sm-6">
                        <h4 class="mb-5">By Make & Model</h4>

                        <form action="{{ route('auto') }}" method="get">
                        <input type="hidden" name="home" value="2">
                        <div class="form-group mb-3">
                            <select id="searchSecondFilterMakeInput" class="form-control makeData select2" name="make">
                                <option value="">Select Make</option>
                                @foreach ($vehicles as $vehicle_info)
                                    <option value="{{ $vehicle_info->make_name }}" data-makeid="{{ $vehicle_info->id }}">{{ $vehicle_info->make_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <select id="searchSecondFilterModelInput" class="form-control makeData select2" name="model">
                                <option value="">Select Model</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group mb-3">
                                    <select class="form-control" data-placeholder="Select" name="min_price">
                                        <optgroup label="Min Price">
                                            <option value="">Min Price</option>
                                            <option value="1000">$1,000</option>
                                            <option value="2000">$2,000</option>
                                            <option value="3000">$3,000</option>
                                            <option value="4000">$4,000</option>
                                            <option value="5000">$5,000</option>
                                            <option value="6000">$6,000</option>
                                            <option value="7000">$7,000</option>
                                            <option value="8000">$8,000</option>
                                            <option value="10000">$10,000</option>
                                            <option value="12000">$12,000</option>
                                            <option value="15000">$15,000</option>
                                            <option value="20000">$20,000</option>
                                            <option value="25000">$25,000</option>
                                            <option value="30000">$30,000</option>
                                            <option value="40000">$40,000</option>
                                            <option value="50000">$50,000</option>
                                            <option value="75000">$75,000</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group mb-3">
                                    <select class="form-control" data-placeholder="Select" name="max_price">
                                        <optgroup label="Max Price">
                                            <option value="">Max Price</option>
                                            <option value="1000">$1,000</option>
                                            <option value="2000">$2,000</option>
                                            <option value="3000">$3,000</option>
                                            <option value="4000">$4,000</option>
                                            <option value="5000">$5,000</option>
                                            <option value="6000">$6,000</option>
                                            <option value="7000">$7,000</option>
                                            <option value="8000">$8,000</option>
                                            <option value="10000">$10,000</option>
                                            <option value="12000">$12,000</option>
                                            <option value="15000">$15,000</option>
                                            <option value="20000">$20,000</option>
                                            <option value="25000">$25,000</option>
                                            <option value="30000">$30,000</option>
                                            <option value="40000">$40,000</option>
                                            <option value="50000">$50,000</option>
                                            <option value="75000">$75,000</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group mb-3">
                                    <select class="form-control select2">
                                        <option value="">Any miles</option>
                                        <option value="10">10 miles</option>
                                        <option value="20">20 miles</option>
                                        <option value="30">30 miles</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group mb-3">
                                    <input type="text" class="form-control" id="sale-location" placeholder="Zip Code" name="zip" value="{{ $cookie_zipcode ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <button style="background:rgb(18, 176, 197); border:1px solid rgb(18, 176, 197); color:white; font-size:16px; border-radius:3px" class="w-100  pt-3 pb-3">Search</button>
                    </form>
                    </div>


                <div style="margin-top:30px; margin-left:95px" class="col-xl-6 col-lg-6 col-md-6 col-sm-12 car-show">
                    <h4 class="mb-4">By Body Type</h4>
                    <div class="flex-container ">
                        <div class="flex-item">
                            <a class="fs-18 car-sale-body-mob" href="{{ route('auto', ['body' => 'full size suv','home'=>true,'homeBodySearch'=>'used']) }}">
                                <img src="{{ asset('/frontend/assets/images/suv.png') }}" alt="Used cars for sale dealer cars for sale suv image dream best"/>
                                <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">SUV/Crossover</h6>
                            </a>
                        </div>
                        <div class="flex-item">
                            <a class="fs-18 car-sale-body-mob" href="{{ route('auto', ['body' =>'sedan', 'home'=>true,'homeBodySearch'=>'used']) }}">
                                <img src="{{ asset('/frontend/assets/images/sedan.png') }}" alt="Used cars for sale dealer cars for sale sedan image dream best"/>
                                <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Sedan</h6>
                            </a>
                        </div>
                        <div class="flex-item">
                            <a class="fs-18 car-sale-body-mob" href="{{ route('auto', ['body' =>'hatchback', 'home'=>true,'homeBodySearch'=>'used']) }}">
                                <img src="{{ asset('/frontend/assets/images/hatchback.png') }}" alt="Used cars for sale dealer cars for sale hatchback image dream best"/>
                                <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Hatchback</h6>
                            </a>
                        </div>
                        <div class="flex-item">
                            <a class="fs-18 car-sale-body-mob" href="{{ route('auto', ['body' =>'truck', 'home'=>true,'homeBodySearch'=>'used']) }}">
                                <img src="{{ asset('/frontend/assets/images/truck.png') }}" alt="Used cars for sale dealer cars for sale truck image dream best"/>
                                <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Pickup Truck</h6>
                            </a>
                        </div>
                        <div class="flex-item">
                            <a class="fs-18 car-sale-body-mob" href="{{ route('auto', ['body' =>'coupe', 'home'=>true,'homeBodySearch'=>'used']) }}">
                                <img src="{{ asset('/frontend/assets/images/coupe.png') }}" alt="Used cars for sale dealer cars for sale coupe image dream best"/>
                                <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Coupe</h6>
                            </a>
                        </div>
                        <div class="flex-item">
                            <a class="fs-18 car-sale-body-mob" href="{{ route('auto', ['body' =>'convertible', 'home'=>true,'homeBodySearch'=>'used']) }}">
                                <img src="{{ asset('/frontend/assets/images/convertible.png') }}" alt="Used cars for sale dealer cars for sale convertible image dream best"/>
                                <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Convertible</h6>
                            </a>
                        </div>
                        <div class="flex-item">
                            <a class="fs-18 car-sale-body-mob" href="{{ route('auto', ['body' =>'Station Wagon', 'home'=>true,'homeBodySearch'=>'used']) }}">
                                <img src="{{ asset('/frontend/assets/images/wagon.png') }}" alt="Used cars for sale dealer cars for sale wagon image dream best"/>
                                <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Wagon</h6>
                            </a>
                        </div>
                        <div class="flex-item">
                            <a class="fs-18 car-sale-body-mob" href="{{ route('auto', ['body' =>'minivan', 'home'=>true,'homeBodySearch'=>'used']) }}">
                                <img src="{{ asset('/frontend/assets/images/minivan.png') }}" alt="Used cars for sale dealer cars for sale minivan image dream best"/>
                                <h6 class="car-type-name" style="margin-top:7px; color:rgb(4, 148, 148)">Minivan</h6>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="sptb bg-white">
        <div class="container">
            <div class="row p-0 m-0">
                <div style="margin-top:0px" class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                    <h3 class="mb-4">Sell your car yourself</h3>
                    <p>Vetted buyers. Smart tools. The offer you deserve.</p>

                    <button data-bs-toggle="modal" data-bs-target="#reqModal"
                    style="background:rgb(18, 176, 197) ; border:1px solid rgb(18, 176, 197) ; color:white; font-size:16px; border-radius:3px"
                    class="mb-4 p-2">Sell Your Car</button>


                    <img style="height:323px" class="sell-image" src="{{ asset('/frontend/assets/images/cn.jpg') }}" alt="Used cars for sale dealer cars for sale cn image Best Dream car"/>
                </div>
                <div style="margin-top:0px; margin-left:95px" class="col-xl-6 col-lg-6 col-md-6 col-sm-12 certified-show">
                    <h3 class="mb-5 used-carsale-title">Browse Certified Pre-Owned Vehicles</h3>
                    <p>CPO vehicles benefit from that extra assurance that the vehicle you are buying is free of defects. In
                        the event that you have a problem, the manufacturer or dealer will often provide repairs free of
                        charge or at a limited cost.</p>

                    <div class="flex-container p-2">
                        <div class="flex-item">
                            <a href="{{ route('auto', ['make' => 'Ford','homeBodySearch'=>'used']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/ford.svg') }}"
                            alt="Used cars for sale dealer cars for sale ford image Best Dream car"/>
                            </a>
                        </div>
                        <div class="flex-item">
                            <a href="{{ route('auto', ['make' => 'GMC','homeBodySearch'=>'used']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/gmc.svg') }}"
                            alt="Used cars for sale dealer cars for sale GMC image Best Dream car" />
                            </a>
                        </div>
                        <div class="flex-item">
                            <a href="{{ route('auto', ['make' => 'BMW','homeBodySearch'=>'used']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/bmw.svg') }}"
                            alt="Used cars for sale dealer cars for sale GMC image Best Dream car" />
                            </a>
                        </div>
                        <div class="flex-item">
                            <a href="{{ route('auto', ['make' => 'Toyota','homeBodySearch'=>'used']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/toyota.svg') }}"
                            alt="Used cars for sale dealer cars for sale Toyota image Best Dream car"/>
                            </a>
                        </div>
                        <div class="flex-item">
                            <a href="{{ route('auto', ['make' => 'Honda','homeBodySearch'=>'used']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/honda.svg') }}"
                            alt="Used cars for sale dealer cars for sale Honda image Best Dream car" />
                            </a>
                        </div>
                        <div class="flex-item">
                            <a href="{{ route('auto', ['make' => 'Dodge','homeBodySearch'=>'used']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/dodge.svg') }}"
                            alt="Used cars for sale dealer cars for sale Dodge image Best Dream car" />
                            </a>
                        </div>
                        <div class="flex-item">
                            <a href="{{ route('auto', ['make' => 'Chevrolet','homeBodySearch'=>'used']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/chevrolet.svg') }}"
                            alt="Used cars for sale dealer cars for sale Chevrolet image Best Dream car" />
                            </a>
                        </div>
                        <div class="flex-item">
                            <a href="{{ route('auto', ['make' => 'Jeep','homeBodySearch'=>'used']) }}">
                            <img class="border img-fluid" src="{{ asset('/frontend/assets/images/jeep.svg') }}"
                            alt="Used cars for sale dealer cars for sale Jeep image Best Dream car" />
                            </a>
                        </div>
                        <div class="flex-item browse-all border">
                            <a href="{{ route('auto') }}" class="text-center browse-btn-text">Browse All</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section style="background:#f8f8f8;" class="sptb">
        <div class="container">
            @if($tips->count() > 1)
            <div class="section-title center-block text-center">
                <h2>Tips & Advice</h2>
                <p>Find your best car in Best Dream car</p>
            </div>
            @endif
            @if($tips->isNotEmpty())
            <div class="col-md-12">
                <div class="items-gallery">
                    <div class="items-blog-tab text-center">

                    </div>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-1">
                            <div class="row" id="cars-containers">
                                @foreach($tips as $key => $tip)
                                
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <div class="card">

                                        <div class="item-card8-img  br-ts-7 br-bs-7">
                                            @if (isset($tip->img))
                                            <a href="{{ route('frontend.research.review.autonews.details', ['categorySlug' => 'car-tips','slug' => $tip->slug]) }} ">
                                                <img class="tips-img" style="width:100%; height:273px"
                                                src="{{ asset('/frontend/assets/images/blog/' .$tip->img) }}" alt="Used cars for sale dealer cars for sale ee image Best Dream car" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';"/>
                                            </a>
                                            

                                            @else
                                            <a href="{{ route('frontend.research.review.autonews.details', ['categorySlug' => 'car-tips','slug' => $tip->slug]) }}">
                                                <img style="width:100%; height:273px" src="{{ asset('frontend/found/NotFound.png') }}" alt="img"
                                                class="tips-img">
                                            </a>

                                            @endif
                                            
                                        </div>
                                        @php
                                            $title = Str::limit($tip->title, 28, '...')
                                            
                                        @endphp

                                        <div class="card-body">
                                            <div class="item-card8-desc">

                                                <a class="text-dark" href="{{ route('frontend.research.review.autonews.details', ['categorySlug' => 'car-tips','slug' => $tip->slug]) }}">
                                                    <h5 style="font-size:18px; font-weight:600" class="tips-title">{{ $title}}</h5>
                                                </a>
                                                <p class="mb-2 mt-2"> {{ \Illuminate\Support\Str::limit(strip_tags($tip->description ?? ''), 150, '...') }}</p>

                                            </div>
                                        </div>
                                    </div>
                                </div> 
                               
                                @endforeach
                                
                                

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
           
        </div>
        </div>
    </section>
    <section class="sptb bg-white">
        <div class="container">
            <div>
                <div>
                    <div class="faq-content">
                        @if($faqs->count() > 1)
                        <h1>Used Cars FAQs</h1>
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
                                            <img
                                                src="https://raw.githubusercontent.com/Romerof/FAQ-accordion-card/main/images/icon-arrow-down.svg" alt="Used cars for sale dealer cars for sale arrow down image Best Dream car">
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
        });
    </script>
    <script>
        document.getElementById('imageUpload').addEventListener('change', function (event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('preview');

            Array.from(files).forEach(file => {
                const reader = new FileReader();

                reader.onload = function (e) {
                    // Create a new container for each image and its remove button
                    const imageContainer = document.createElement('div');
                    imageContainer.style.display = 'inline-block';
                    imageContainer.style.margin = '5px';

                    // Create image element
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px';
                    img.style.margin = '5px';

                    // Create remove button
                    const removeButton = document.createElement('button');
                    removeButton.textContent = 'Remove';
                    removeButton.style.display = 'block';
                    removeButton.style.marginTop = '5px';

                    // Add remove button event listener
                    removeButton.addEventListener('click', function () {
                        imageContainer.remove();
                    });

                    // Append image and remove button to the container
                    imageContainer.appendChild(img);
                    imageContainer.appendChild(removeButton);

                    // Append container to the preview area
                    previewContainer.appendChild(imageContainer);
                };

                // Read the file as a data URL
                reader.readAsDataURL(file);
            });
        });
    </script>

    @push('js')
    <script>
        $(document).ready(function(){
            $.ajaxSetup({
         headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
       });

        $('.telephoneInput').inputmask('(999) 999-9999');

       $('#reqAddInventoryAll').on('submit', function(e) {
    e.preventDefault();

    var formData = new FormData($(this)[0]);
    $('.req_button').text('Loading....');
    var form = this;
    // Make Ajax request
    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: formData,
        contentType: false,
        processData: false,
        success: function(res) {
            console.log(res.errors);

            $('.error-message').html(''); // Clear any previous errors
            if (res.errors) {
                $.each(res.errors, function(key, value) {
                    $('#' + key + '-error').html(value[0]);
                });
                $('.req_button').text('Submit');
            }

            if (res.status === 'success') {

    toastr.success(res.message);
    $('#reqModal').modal('hide');
    const previewContainer = document.getElementById('preview');
        if (previewContainer) {
            previewContainer.innerHTML = ''; // Clear all image previews
        }
    $('.req_button').text('Add Listing');
    form.reset();

}

        },
        error: function(xhr) {
            // Handle error response (e.g., 400, 500 errors)
            var errors = xhr.responseJSON.errors;
            if (errors) {
                $('.error-message').html(''); // Clear previous errors
                $.each(errors, function(key, value) {
                    $('#' + key + '-error').html(value[0]);
                });
            } else {
                // Handle any general errors (not validation related)
                toastr.error('An error occurred. Please try again.');
            }

            $('#listing_button').text('Submit'); // Reset button text
        }
    });
});







        })

    </script>


    @endpush
@endsection
