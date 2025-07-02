@extends('frontend.website.layout.app')

@foreach (app('globalStaticPage') as $page)
    @if ($page->slug == 'search')
        @if ($page->description)
            @section('meta_description', $page->description)
        @else
            @section('meta_description', app('globalSeo')['description'])
        @endif
        @if ($page->keyword)
            @section('meta_keyword', $page->keyword)
        @else
            @section('meta_keyword', app('globalSeo')['keyword'])
        @endif
        @section('title')
            Search | Best Used Cars for Sale - bestdreamcar.com®
        @endsection
    @endif
@endforeach

@section('hedo')
<meta name="robots" content="noindex, follow">
@endsection

@section('gtm')
    {!! app('globalSeo')['gtm'] !!}

@endsection
@section('app_id', app('globalSeo')['app_id'])
@section('og_title')
            Search | Best Used Cars for Sale - bestdreamcar.com®
@endsection
@section('og_description', app('globalSeo')['og_description'])
@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])
@section('twitter_title')
            Search | Best Used Cars for Sale - bestdreamcar.com®
@endsection
@section('twitter_description', app('globalSeo')['twitter_description'])
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])


@section('content')
<style>
    .DeleteFavorite
    {
        background: #080e1b;
    }
    .swiper {
        width: 100%;
        height: 100%;
    }

    .swiper-slide {
        text-align: center;
        font-size: 18px;
        background: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .swiper-slide img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .swiper {
        margin-left: auto;
        margin-right: auto;
    }

    .swiper-button-next,
    .swiper-button-prev {
        color: #000;
        width: 30px !important ;
        height: 30px !important;
        margin-top: -15px;

    }

    .swiper-button-next {
        right: 10px;
        border-radius: 0 !important;

    }

    .swiper-button-prev {
        left: 10px;

    }

    .swiper-pagination {
        bottom: 10px;

    }
</style>

    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="favorite-header">Search Result</h1>

                        <ol class="breadcrumb text-center">
                            <li class="favorite-bc"><a style="color:white" href="{{ route('home') }}">Home<span
                                        style="margin-left:4px; margin-right:4px;">/</span> </a></li>
                            <li class="favorite-bc"><a style="color:white" href="javascript:void(0);">Search Result</a></li>

                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    <!-- Check availibility modal start -->
    <div class="modal fade" id="checkModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Message Seller</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    @php
                        $user = auth()->user();
                    @endphp
                    <div class="row">
                        <div class="col-md-12 p-2">
                            <form id="SendLead" action="{{ route('lead.send') }}" method="post"
                                style="background-color: #d6d6d6">
                                @csrf
                                <div class="">
                                    <div class="row p-2">
                                        <div class="col-md-6 col-sm-6 col-xs-12 " style="margin-top:20px">
                                            <div class="form-group ">
                                                <input type="hidden" id="inventory_id" name="inventories_id"
                                                    value="">
                                                <input type="hidden" id="dealer_id" name="dealer_id" value="">
                                                <input style="border-radius:5px; color:black" placeholder="First Name*"
                                                    class="form-control fname" type="text" id="first_name"
                                                    name="first_name" value="{{Auth()->check() ? Auth()->user()->fname : ''}}">
                                                <span id="first_name_error" class="text-danger" role="alert"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 second-name" style="margin-top:20px">
                                            <div class="form-group ">
                                                <input style="border-radius:5px; color:black" placeholder="Last Name*"
                                                    class="form-control lname" type="text" id="last_name"
                                                    name="last_name" value="{{Auth()->check() ? Auth()->user()->lname : ''}}">
                                                <span id="last_name_error" class="text-danger" role="alert"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                                            <div class="form-group ">
                                                <input style="border-radius:5px; color:black" placeholder="E-mail*"
                                                    class="form-control email" type="text" id="email" name="email" value="{{Auth()->check() ? Auth()->user()->email : ''}}">
                                                <span id="email_error" class="text-danger" role="alert"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                                            <div class="form-group ">
                                                <input style="border-radius:5px; color:black"
                                                    class="form-control phone telephoneInput" type="text"
                                                    placeholder="cell" id="phone" name="phone" value="{{Auth()->check() ? Auth()->user()->phone : ''}}">

                                                <span id="phone_error" class="text-danger" role="alert"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-xs-12 ">
                                            <div class="form-group ">
                                                <textarea style="border-radius:5px; color:black" id="w3review" class="form-control description" name="description"
                                                    rows="4" cols="55">I am interested and want to know more about the Sport Utility, you have listed for $  on Best Dream car.
                                                        </textarea>

                                                <span id="description_error" class="text-danger" role="alert"></span>

                                            </div>

                                        </div>
                                        <div class="col-md-12 col-sm-12 col-xs-12 ">
                                            <div class="form-group ">
                                                <p
                                                    style="color:rgb(168, 11, 155); font-weight:bold; margin-bottom:15px; margin-top:10px">
                                                    <span class="text-danger">*</span> Security Question (Enter the Correct
                                                    answer)
                                                </p>
                                                <div style="display: flex">
                                                    <div id="captchaLabel"
                                                        style="background-color:white; width:50%;  margin-right:10px; text-align:center; padding-top:7px; font-weight:600;  margin-top:2px; margin-left:3px; height:35px; border-radius:5px">
                                                        {{ app('mathcaptcha')->label() }}
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
                                        </div>

                                        <div class="col-md-12 col-sm-12 col-xs-12 ">
                                            <div class="form-group ">
                                                <p style="color: black"><input type="checkbox" name="ask_trade"
                                                        id="tradeChecked" style="cursor: pointer"> <label for="tradeChecked" style="cursor: pointer">Do you have a
                                                            trade-in?</label> </p>
                                            </div>
                                        </div>

                                        <div class="row p-0 m-0" style="margin-left: 0px; margin-right:0px; display:none"
                                            id="Auto_Trade_block_content">
                                            <div class="row p-0 m-0">
                                                <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                    <div class="form-group ">

                                                        <input style="border-radius:5px" placeholder="Year*"
                                                            class="form-control year_trade" type="text" name="year"
                                                            value="">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                    <div class="form-group ">

                                                        <input style="border-radius:5px" placeholder="Make*"
                                                            class="form-control make_trade" type="text" name="make"
                                                            value="">

                                                        <span class="invalid-feedback7 text-danger" role="alert">

                                                        </span>

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                    <div class="form-group ">

                                                        <input style="border-radius:5px" placeholder="Model*"
                                                            class="form-control model_trade" type="text"
                                                            name="model" value="">

                                                        <span class="invalid-feedback8 text-danger" role="alert"></span>

                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                    <div class="form-group ">

                                                        <input style="border-radius:5px" placeholder="Mileage*"
                                                            class="form-control mileage" type="text" name="mileage"
                                                            value="">

                                                        <span class="invalid-feedback9 text-danger" role="alert">

                                                        </span>

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                    <div class="form-group ">

                                                        <input style="border-radius:5px" placeholder="Color*"
                                                            class="form-control color" type="text" name="color"
                                                            value="">

                                                        <span class="invalid-feedback10 text-danger" role="alert">
                                                        </span>

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                    <div class="form-group ">
                                                        <input style="border-radius:5px" placeholder="VIN (optional)"
                                                            class="form-control vin" type="text" name="vin"
                                                            value="">

                                                    </div>
                                                </div>
                                            </div>






                                        </div>
                                        <div class="col-md-12 col-sm-12 col-xs-12 ">
                                            <div class="form-group ">
                                                <p style="color: black"><input type="checkbox" class="isEmailSend"
                                                        name="isEmailSend" style="cursor: pointer" checked> Email
                                                    me
                                                    price
                                                    drops for this vehicle </p>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12 ">
                                        <div class="form-group">

                                            <button
                                                style="background:rgb(68, 29, 70); width:100%; margin-bottom:15px; color:white; font-size:16px;"
                                                type="submit" class="btn Aloading">Send Message</button>

                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- Form -->
                            <p style="font-size: 12px; line-height: 11px; color: #999; margin-top: 5px;text-align:justify">
                                By clicking "SEND EMAIL", I
                                consent to be contacted by dbc.com and the dealer selling
                                this car at any telephone number I provide, including, without
                                limitation, communications sent via text message to my cell
                                phone or communications sent using an autodialer or prerecorded
                                message. This acknowledgment constitutes my written consent to
                                receive such communications. I have read and agree to the Terms
                                and Conditions of Use and Privacy Policy of dbc.com. This
                                site is protected by reCAPTCHA and the Google Privacy Policy and
                                Terms of Service apply.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Check availibility modal close-->

    {{-- compare data alert modal start --}}
<!-- Modal -->
<div class="modal fade" id="ComModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div style="margin-top:-350px" class="modal-content">
            <div style="background:black; border:none;  box-shadow: none;" class="modal-header">

                <div style="display:flex;">
                    <div style="width:50px; margin-right:15px">
                        <img id="comIcon" style="width:100%" alt="Used cars for sale for Best Dream car love icon image"/>
                    </div>
                    <div>
                        <p id="addValue" class="p-0 m-0 mb-1" style="color:white; font-size:16px"></p>
                        <a href="javascript:void(0)" id="compare-collect"
                            style="color: rgb(5, 181, 212); text-decoration: underline; cursor:pointer">See Your
                            Comparisons</a>

                    </div>

                </div>
                <button type="button" style="background:none; color:white; border:none" data-bs-dismiss="modal"
                    aria-label="Close">X</button>
            </div>


        </div>
    </div>
</div>
{{-- compare data alert modal close --}}


{{-- compare data show modal start --}}

<div class="modal fade" id="ComDataModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div style="border-top-left-radius: 8px;" class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Your Comparisons</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body h-50">

            </div>
            <div class="modal-footer">

                <a href="{{ route('frontend.compare.show') }}"
                    style="background: rgb(3, 148, 153); color:white; font-size:15px" type="button"
                    class="btn">Compare</a>
            </div>
        </div>
    </div>
</div>
{{-- compare data show modal close --}}
    <!--listing-->

    <div class="container">
        <div class="row mt-5">
            @if($infos->total() > 0)
                <h3 class="mb-4 mt-5">
                    Showing {{ number_format($infos->firstItem()) }} to {{ number_format($infos->lastItem()) }} of {{ number_format($totalResults) }} results
                </h3 class="mb-4 mt-5">
            @else
                <h3 style="margin-left:-8px" class="mb-4 mt-5 serc-title">Search results</h3 class="">
            @endif

            @forelse($infos as $sear)


                <div class="col-lg-4 col-md-6 col-xl-3 col-xxl-3 col-sm-6 col-xs-12">

                    <div class="card overflow-hidden">
                        <div class="item-card9-img">
                            <div class="item-card9-img">
                                <div class="item-card9-imgs">

                                @php
                                    $image_obj = $sear->additionalInventory->local_img_url; // Get the image URLs as a string
                                    $image_splice = explode(',', $image_obj); // Split the string into an array
                                    $images_count = count(array_filter($image_splice)); // Count non-empty images
                                    $not_found_image = 'frontend/NotFound.png'; // Path to the "not found" image

                                    if ($images_count > 1) {
                                        // If there are multiple images, use the first valid one
                                        $imageData = trim(str_replace(['[', "'"], '', $image_splice[0]));
                                        $image = $imageData;
                                    } else {
                                        // If only one or no image is provided, show the "not found" image
                                        $image = $not_found_image;
                                    }

                                    $vin_string_replace = str_replace(' ', '', $sear->vin);
                                    $route_string = str_replace(
                                    ' ',
                                    '',
                                    $sear->year .
                                    '-' .
                                    $sear->make .
                                    '-' .
                                    $sear->model .
                                    '-in-' .
                                    $sear->dealer->city .
                                    '-' .
                                    strtoupper($sear->dealer->state),
                                    );
                                @endphp


                                    @if (!empty($image_obj) && $image_obj != '[]')
                                        <a title="{{ $sear->title }}"
                                            href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"
                                            class="text-dark"></a>
                                            <img class="auto-ajax-photo-fav" src="{{ asset($image) }}"
                                                alt="Used cars for sale {{ $sear->title }}, price is {{ $sear->price }}, vin {{ $sear->vin }} in {{ $sear->dealer->city }}, {{ strtoupper($sear->dealer->state) }}, dealer name is {{ $sear->dealer->name }} Best Dream car image " loading="lazy"
                                                onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';">
                                    @endif
                                </div>


                                @php
                                $countWishList = 0;
                                if (session()->has('favourite')) {
                                    $favourites = session('favourite');
                                    foreach ($favourites as $favorite) {
                                        if ($favorite['id'] == $sear->id) {
                                            $countWishList = 1;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                                <div class="item-card9-icons">
                                    <a href="javascript:void(0);" class="item-card9-icons1 wishlist }}"
                                        data-productid="{{ $sear->id }}">
                                        @if ($countWishList > 0)
                                            <i class="fa fa-heart" style="color: red;margin-top:9px"></i>
                                        @else
                                            <i class="fa fa fa-heart-o" style="margin-top:9px"></i>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>


                        <a data-id="{{ $sear->id }}" href="javascript:void(0)" id="quick"><img
                                        class="quick-option-fav"
                                        src="{{ asset('/frontend/assets/images/more.png') }}" alt="Used cars for sale for Best Dream car search more image"/></a>

                                        <div class="hide-action-fav" id="hide-action-{{ $sear->id }}" style="">
                                    <input type="hidden" id="all_id">
                                    <a href="javascript:void(0)"
                                        style="display:flex; align-items:center; margin-top:20px; margin-left:15px; text-decoration:none; margin-bottom:13px"
                                        id="view-data">
                                        <img style="width:20px; height:20px;"
                                            src="{{ asset('/frontend/assets/images/show.png') }}" class="me-3" alt="Used cars for sale for Best Dream car search show image"/>
                                        <p style="color:black; font-size:15px; margin:0;">Quick View</p>


                                    </a>

                                    <a href="javascript:void(0)" data-id="{{ $sear->id }}"
                                        style="display:flex; align-items:center; margin-left:15px; text-decoration:none; margin-bottom:13px"
                                        id="compare_listing">
                                        <img style="width:20px; height:20px;"
                                            src="{{ asset('/frontend/assets/images/swap.png') }}" class="me-3" alt="Used cars for sale for Best Dream car search swap image"/>
                                        <p style="color:black; font-size:15px; margin:0;">Compare Listing</p>
                                    </a>

                                    <a data-bs-toggle="modal" data-bs-target="#ShareModal"
                                        onclick="setModalId('{{ $sear->id }}')" href="javascript:void(0)"
                                        style="display:flex; align-items:center; margin-left:15px; text-decoration:none; margin-bottom:13px">
                                        <img style="width:20px; height:20px;"
                                            src="{{ asset('/frontend/assets/images/share.png') }}"
                                            class="me-3" alt="Used cars for sale for Best Dream car search share image"/>
                                        <p style="color:black; font-size:15px; margin:0;">Share</p>
                                    </a>

{{--
                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal"
                                        style="display:flex; align-items:center; margin-left:15px; text-decoration:none; margin-bottom:13px">
                                        <img style="width:20px; height:20px;"
                                            src="{{ asset('/frontend/assets/images/coin.png') }}" class="me-3" alt="Used cars for sale for Best Dream car search coin image"/>
                                        <p style="color:black; font-size:15px; margin:0;">See Actual Pricing</p>
                                    </a> --}}


                                </div>

                        <div style="background: rgb(255, 255, 255);
                            background: linear-gradient(0deg, rgb(232, 245, 243) 0%, rgb(255, 255, 255) 100%);" class="card border-0 mb-0">
                            <div class="card-body">
                                <div class="item-card9">
                                    @php
                                        // $image_obj =  $inventory->local_img_url;
                                        // $image_splice = explode(',',$image_obj);
                                        // $image = str_replace(["[", "'"], "", $image_splice[0]);

                                        $vin_string_replace = str_replace(' ', '', $sear->vin);
                                        $route_string = str_replace(
                                            ' ',
                                            '',
                                            $sear->year .
                                                '-' .
                                                $sear->make .
                                                '-' .
                                                $sear->model .
                                                '-in-' .
                                                $sear->dealer->city .
                                                '-' .
                                                strtoupper($sear->dealer->state),
                                        );
                                    @endphp


                                    {{-- <a href="{{ route('auto.details', ['vin' => $sear->vin, 'id' => $sear->id]) }}" class="text-dark">
                                            <h4 class="font-weight-semibold mt-1">{{ $sear->year . ' ' . $sear->make . ' ' . $sear->model}}</h4>
                                        </a> --}}

                                    @php
                                        $titleLength = strlen($sear->title);
                                        $title = Str::limit($sear->title, 21, '...');
                                    @endphp

                                    <a title="{{ $sear->title }}"
                                        href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"
                                        class="text-dark">
                                        <h5 style="font-weight:600; color:black !important" class="">{{ $title }}</h5>
                                    </a>


                                    @php
                                        $transmissionLength = strlen($sear->transmission);
                                        $transmission = substr($sear->transmission, 0, 25);
                                        $transmission = substr($sear->formatted_transmission, 0, 25);
                                    @endphp

                                    <p style="margin-top:4px"
                                        class="me-4 mb-0"><span class="">
                                            {{ $transmission }}</span></p>

                                    <p style="margin-top:4px" class="mb-2">
                                        @if (in_array($sear->type, ['Preowned', 'Certified Preowned']))
                                            Used
                                        @else
                                            {{ $sear->type }}
                                        @endif
                                    </p>

                                    <div style="height: 25px" class="d-flex">
                                        <h4 class="me-3 mb-3" style="font-weight:600; font-size:19px">{{ $sear->price_formate }}</h4>
                                        <p style="color:black; font-weight:600; font-size:14px; margin-top:0px">
                                            ${{ $sear->payment_price }}/mo*</p>
                                    </div>

                                    <div class="item-card9-footer d-sm-flex">
                                        <div>
                                            <p  class="w-100 mt-2 mb-0 float-start" title="Mileage"><img class="me-1" style="width:21px; height:21px; margin-top:-2px"
                                                src="{{ asset('/frontend/assets/images/miles.png') }}" alt="Used cars for sale for Best Dream car search miles  image"/> {{ $sear->miles == 0 ? 'TBD' : number_format($sear->miles) . ' miles' }}</p>

                                        </div>

                                    </div>
                                    <div style="margin-top:7px" class="float:left">
                                        <button
                                            style="background:rgb(68, 29, 70); padding-top:5px; padding-bottom:5px;  padding-left:15px; padding-right:15px; border-radius:7px; color:white; border:1px solid rgb(68, 29, 70)"
                                            id="check_availability" class="mt-1" type="button"
                                            data-inventory_id="{{ $sear->id }}"
                                            data-user_id="{{ $sear->user_id }}">Check Availibility</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer pt-4 pb-4 pe-4 ps-4">
                                @php
                                    $cus_dealer = explode(' in ', $sear->dealer->name)[0];
                                    $nameLength = strlen($cus_dealer);
                                    $name = Str::substr($cus_dealer, 0, 20);
                                @endphp
                                <div class="item-card9-desc mb-2">
                                    @if($nameLength <= '25')
                                    <h5 style="color:black" class="me-4 d-inline-block">
                                        <span class=""><i
                                                class="fa fa-map-marker text-muted me-1"></i>{{ $cus_dealer }}
                                            <br><span style="margin-left:13px; font-size:14px">{{  $sear->dealer->city }}, {{ strtoupper($sear->dealer->state)  }} {{ $sear->zip_code }}</span></span>
                                        </h5>
                                    @else
                                    <h5 style="color:black" title="{{$cus_dealer}}"  class="me-4 d-inline-block">
                                        <span class=""><i
                                                class="fa fa-map-marker text-muted me-1"></i>{{ $name }}
                                            <br><span style="margin-left:13px; font-size:14px">{{  $sear->dealer->city }}, {{ strtoupper($sear->dealer->state)  }} {{ $sear->zip_code }}</span></span>
                                        </h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="border-radius:5px;" class="container col-12 border text-center mb-4">
                    <div class="mx-auto">
                        <img style="margin-top:50px;" width="4%" class="img-responsive-fav"
                            src="{{ '/frontend/assets/images/model.png' }}" alt="No Favorites" alt="Used cars for sale for Best Dream car search model image">
                        <h4  class="mt-1 mb-2">No Search Listings.</h4>
                        <p class="mb-1">Browse local inventory and find your</p>
                        <p class="mb-4">next car</p>
                        <a href="{{ route('auto') }}">
                            <button
                                style="background: black; color:white; padding: 8px 65px 8px 65px; border:1px solid black; border-radius:18px; margin-bottom:70px; font-size:15px">Browse
                                Local Inventory</button>
                        </a>

                    </div>
                </div>
            @endforelse
        </div>
        <div class="clearfix"></div>
        <div class="center-block text-center mb-4">
            <div class="custom-pagination" style="display: flex; justify-content: flex-end">
                <ul class="pagination">
                    @if ($infos->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $infos->previousPageUrl() }}">Previous</a>
                        </li>
                    @endif

                    @php
                        $currentPage = $infos->currentPage();
                        $lastPage = $infos->lastPage();
                        $maxPagesToShow = 5;
                        $startPage = max($currentPage - floor($maxPagesToShow / 2), 1);
                        $endPage = min($startPage + $maxPagesToShow - 1, $lastPage);
                    @endphp

                    @if ($startPage > 1)
                        <li class="page-item"><a class="page-link" href="{{ $infos->url(1) }}">1</a></li>
                        @if ($startPage > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endif

                    @for ($page = $startPage; $page <= $endPage; $page++)
                        @if ($page == $currentPage)
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link"
                                    href="{{ $infos->url($page) }}">{{ $page }}</a></li>
                        @endif
                    @endfor

                    @if ($endPage < $lastPage)
                        @if ($endPage < $lastPage - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item"><a class="page-link"
                                href="{{ $infos->url($lastPage) }}">{{ $lastPage }}</a></li>
                    @endif

                    @if ($infos->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $infos->nextPageUrl() }}">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </div>
        </div>

    </div>
    <!--/Listing-->

    @include('frontend.website.layout.quickViewModal')
    @include('frontend.website.layout.shareEmailModal')
@endsection
@push('js')
    @include('frontend.reapted_js')
@endpush
