@extends('frontend.website.layout.app')
@push('head')
    <link rel="canonical" href="{{ url()->current() }}">

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Vehicle",
        "name": "{{ addslashes($inventory->year ?? '') }} {{ addslashes($inventory->make ?? '') }} {{ addslashes($inventory->model ?? '') }} {{ ($inventory->trim ?? '') != 'N/A' ? addslashes($inventory->trim ?? '') : '' }}",
        "description": "{{ addslashes($custom_description ?? (app('globalSeo')['description'] ?? '')) }}",
        "image": "{{ $image_src ?? '' }}",
        "vehicleIdentificationNumber": "{{ $inventory->vin ?? '' }}",
        "make": {
            "@type": "Brand",
            "name": "{{ addslashes($inventory->make ?? '') }}"
        },
        "model": "{{ addslashes($inventory->model ?? '') }}",
        @if(!empty($inventory->year))
        "dateVehicleFirstRegistered": "{{ $inventory->year }}-01-01",
        @endif
        "fuelType": "{{ addslashes($inventory->fuel ?? '') }}",
        "vehicleTransmission": "{{ addslashes($inventory->transmission ?? '') }}",
        "bodyType": "{{ addslashes($inventory->body_formated ?? '') }}",
        @if(!empty($inventory->exterior_color))
        "color": "{{ addslashes($inventory->exterior_color) }}",
        @endif
        @if(isset($inventory->miles) && is_numeric(str_replace(',', '', $inventory->miles)))
        "mileageFromOdometer": {
            "@type": "QuantitativeValue",
            "value": {{ (int)str_replace(',', '', $inventory->miles) }},
            "unitText": "miles"
        },
        @endif
        "offers": {
            "@type": "Offer",
            "price": "{{ (float)str_replace(['$', ','], '', $inventory->price ?? 0) }}",
            "priceCurrency": "USD",
            "availability": "https://schema.org/InStock",
            "url": "{{ url()->current() }}",
            @if($inventory->dealer)
            "seller": {
            "@type": "Organization",
            "name": "{{ addslashes($inventory->dealer->name ?? '') }}",
            @if(!empty($inventory->dealer->city) && !empty($inventory->dealer->state))
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "{{ addslashes($inventory->dealer->dealer_full_address ?? ($inventory->dealer->address_line_1 ?? '') . ' ' . ($inventory->dealer->address_line_2 ?? '')) }}",
                "addressLocality": "{{ addslashes($inventory->dealer->city ?? '') }}",
                "addressRegion": "{{ addslashes($inventory->dealer->state ?? '') }}",
                "postalCode": "{{ addslashes($inventory->dealer->zip ?? ($inventory->zip_code ?? '')) }}"
            }
            @endif
         }
            @endif
        }
    }
    </script>
@endpush
@php
    $custom_title = $inventory->title . ' For Sale $' . number_format(floatval($inventory->price));

    $custom_description =
        'Used ' .
        $inventory->title .
        ' for sale at ' .
        $inventory->dealer->name .
        ' in ' .
        $inventory->dealer->city .
        ',' .
        $inventory->dealer->state .
        ' for $' .
        number_format(floatval($inventory->price)) .
        ' View now on bestdreamcar.com';

    $custom_keyword =
        ($inventory->make ?? '') .
        ', ' .
        ($inventory->model ?? '') .
        ', ' .
        ($inventory->trim ?? '') .
        ', ' .
        ($inventory->body_formated ?? '');
    $custom_keyword .=
        ', used cars, cars for sale, ' .
        ($inventory->dealer->city ?? '') .
        ' used cars, ' .
        strtolower($inventory->make ?? '') .
        ' for sale';

    // Process image URLs
    $image_obj = $inventory->additionalInventory->local_img_url;
    $image_str = str_replace(['[', ' ', "'", ']'], '', $image_obj); // Clean the string
    $images = array_filter(explode(',', $image_str)); // Remove empty values
    $image_count = count($images); // Count the images
    $not_found_image = asset('frontend/notfound.jpg'); // Path to your "not found" image

    // Use the first image or "not found" image if there's only one or no image
$image_src = $image_count > 1 ? asset($images[0]) : $not_found_image;

// Build the URL
$url_id =
    route('home') .
    '/best-used-cars-for-sale' .
    '/' .
    'listing' .
    '/' .
    $inventory->vin .
    '/' .
    $inventory->year .
    '-' .
    $inventory->make .
    '-' .
    $inventory->model .
    '-in-' .
    $inventory->dealer->city .
    '-' .
        strtoupper($inventory->dealer->state);
@endphp


@section('meta_description',
    'Find this ' .
    ($inventory->year ?? '') .
    ' ' .
    ($inventory->make ?? '') .
    ' ' .
    ($inventory->model ?? '') .
    ' ' .
    ($inventory->trim != 'N/A' ? $inventory->trim : '') .
    ' for sale at $' .
    number_format(floatval($inventory->price ?? 0)) .
    ' in ' .
    ($inventory->dealer->city ?? '') .
    ', ' .
    strtoupper($inventory->dealer->state ?? '') .
    '. VIN: ' .
    ($inventory->vin ?? '') .
    '. View details, photos, and more on
    BestDreamCar.com.')
@section('meta_keyword', $custom_keyword ?? app('globalSeo')['keyword'])
@section('gtm')
    {!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])


@section('og_title', $custom_title . ' | BestDreamCar.com' ?? app('globalSeo')['og_title'])
@section('og_img', asset($image_src) ?? app('globalSeo')['og_img'])
@section('og_description', $custom_description ?? app('globalSeo')['og_description'])

@section('og_type', app('globalSeo')['og_type'])
@section('og_url', url()->current() ?? ($url_id ?? app('globalSeo')['og_url']))
@section('og_site_name', 'BestDreamCar.com' ?? app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])

@section('twitter_title', $custom_title ?? app('globalSeo')['twitter_title'])
@section('twitter_description', $custom_description ?? app('globalSeo')['twitter_description'])
@section('twitter_image', asset($image_src) ?? app('globalSeo')['og_img'])
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('title', ($inventory->year ?? '') . ' ' . ($inventory->make ?? '') . ' ' . ($inventory->model ?? '') . ' ' .
    ($inventory->trim != 'N/A' ? $inventory->trim : '') . ' for Sale in ' . ($inventory->dealer->city ?? '') . ', ' .
    strtoupper($inventory->dealer->state ?? '') . ' - VIN ' . ($inventory->vin ?? '') . ' | BestDreamCar.com')
@section('content')

    <style>
        p.inventory-title {
            line-height: 1;
            font-size: 1.125rem;
        }

        @media (min-width: 48rem) {
            p.inventory-title {
                font-size: 24px;
            }
        }
    </style>
    <!--Breadcrumb-->
    <div>
        <div style="height:10px !important;" class="bannerimg  deatails-breadcrumb">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="">
                        <ol class="breadcrumb">
                            <li style="color:black !important" class="breadcrumb-item brd"><a href="{{ route('home') }}"
                                    style="color:black !important">Home</a></li>
                            <li class="breadcrumb-item brd"><a style="color:black !important"
                                    href="{{ route('auto') }}">Cars for Sale</a></li>
                            @if (isset($inventory->make) && $inventory->make)
                                <li class="breadcrumb-item brd">
                                    <a style="color:black !important"
                                        href="{{ route('auto', ['make' => $inventory->make]) }}">{{ $inventory->make }}</a>
                                </li>
                            @endif
                            @if (isset($inventory->make) && $inventory->make && isset($inventory->model) && $inventory->model)
                                <li class="breadcrumb-item brd">
                                    <a style="color:black !important"
                                        href="{{ route('auto', ['make' => $inventory->make, 'model' => $inventory->model]) }}">{{ $inventory->model }}</a>
                                </li>
                            @elseif(isset($inventory->model) && $inventory->model)
                                {{-- Fallback if only model is somehow present --}}
                                <li class="breadcrumb-item brd">
                                    <a style="color:black !important"
                                        href="{{ route('auto', ['model' => $inventory->model]) }}">{{ $inventory->model }}</a>
                                </li>
                            @endif
                            @if (isset($inventory->vin) && $inventory->vin)
                                <li class="breadcrumb-item brd active" style="color:black !important" aria-current="page">
                                    {{ $inventory->vin }}</li>
                            @endif
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/Breadcrumb-->


    <!--listing-->
    <section style="margin-top:0; margin-bottom:45px;" class="deatails-card">
        <div class="container">
            {{-- <div class="item-det mb-4">
            <h1 style="font-size: 1.5em; font-weight: 600; margin: 0;">{{ $inventory->title }} - {{ $inventory->price_formate }}</h1>
        <div class=" details-extra-item">


            <p class="d-flex  align-items-center">
                <span>
                    <i class="fa fa-car text-muted me-1" title="Transmission"></i>
                    {{ $inventory->type ?? 'No Type' }}

                    <img style="width:20px; height:20px; margin-top:-2px" class="ms-2 me-1"
                        src="{{ asset('/frontend/assets/images/miles.png') }}" alt="Used cars for sale for Best Dream car miles image icon" />
                    {{ number_format($inventory->miles) . ' miles' }}
                </span>
            </p>


        </div>
    </div> --}}

            <div class="row">
                <div class="col-xl-7 col-lg-7 col-md-12">
                    <!--Classified Description-->
                    <div class="card overflow-hidden">
                        <div style="background: rgb(255, 255, 255);" class="card-header w-100">
                            <div class="row  w-100 h-50 p-0 m-0 justify-content-between">
                                @php
                                    $image_obj = $inventory->additionalInventory->local_img_url;
                                    $image_str = str_replace(['[', ' ', "'", ']'], '', $image_obj);
                                    $images = explode(',', $image_str);
                                    $count = count($images);
                                @endphp
                                <div class="col-xl-8 col-lg-7 col-md-8 col-sm-0 photo-collection">
                                    <span class="photos mt-3"><i
                                            class="fa fa-image  icon-icon-photos me-2"></i>Photos({{ $count }})</span>
                                </div>
                                <div style="text-align:center;" class="col-xl-4 col-lg-5 col-md-4 col-sm-12">

                                    <a title="share" style="margin-right:40px ; font-size:15px; font-weight:500"
                                        href="#" class="dropdown-toggle" id="dropdownMenuButton"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                            class="fa fa-share-alt ms-3"></i></a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}&quote={{ $inventory->title }}"
                                            target="_blank">
                                            <img src="{{ asset('frontend/assets/social/fb.png') }}" alt="Facebook"
                                                class="social-icon mx-2" style="width: 24px; height: 24px; margin: 0 8px;">
                                        </a>

                                        <a href="https://x.com/intent/tweet?url={{ url()->current() }}&text={{ $inventory->title }}&via=bestdreamcar"
                                            target="_blank">
                                            <img src="{{ asset('frontend/assets/social/x.png') }}" alt="Twitter X"
                                                class="social-icon mx-2" style="width: 24px; height: 24px; margin: 0 8px;">
                                        </a>
                                        {{-- {!! $shareButtons !!} --}}
                                    </div>

                                    <a style="margin-right:40px; font-size:15px; font-weight:500" href="#"
                                        class="cpy" title="Copy Link" id="copyUrlButton"><i
                                            class="fa fa-copy icon-icon"></i>
                                    </a>
                                    @php
                                        $countWishList = 0;
                                        $favourites = session()->get('favourite', []); // Ensure default is an empty array

                                        foreach ($favourites as $favorite) {
                                            if (isset($favorite['id']) && $favorite['id'] == $inventory->id) {
                                                $countWishList = 1;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <a title="favorite" style="background:none !important; margin-right:0px"
                                        href="javascript:void(0);" class="item-card9-icons1 wishlist"
                                        data-productid="{{ $inventory->id }}">
                                        @if ($countWishList > 0)
                                            <i class="fa fa-heart" style="color: red"></i>
                                        @else
                                            <i style="font-size:16px" title="favorite" class="fa fa fa-heart-o"></i>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div style="padding:0 !important" class="card-body">
                            <div class="product-slider">
                                <!-- Swiper for main slider -->
                                <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
                                    class="swiper mySwiper2">
                                    <div class="swiper-wrapper">
                                        @php
                                            $image_obj = $inventory->additionalInventory->local_img_url;
                                            $image_str = str_replace(['[', ' ', "'", ']'], '', $image_obj); // Clean up the string
                                            $images = explode(',', $image_str); // Split into array
                                            $images_count = count(array_filter($images)); // Count non-empty images
                                            $not_found_image = 'frontend/NotFound.png'; // Path to the "not found" image
                                        @endphp

                                        @if ($images_count <= 1)
                                            {{-- Check if 0 or 1 image exists --}}
                                            <div class="swiper-slide">
                                                <a href="javascript:void(0);" data-bs-toggle="modal"
                                                    data-bs-target="#imageOpenModal" style="cursor:pointer">
                                                    <img style="width:674px !important; margin:0 !important; height:500px"
                                                        src="{{ asset($not_found_image) }}"
                                                        alt="Vehicle image not available" class="swipter-top-img"
                                                        loading="lazy"
                                                        onerror="this.onerror=null; this.src='{{ asset($not_found_image) }}'; this.alt='Vehicle image not available';">
                                                </a>
                                            </div>
                                        @else
                                            @foreach ($images as $image)
                                                <div class="swiper-slide">
                                                    <a href="javascript:void(0);" data-bs-toggle="modal"
                                                        data-bs-target="#imageOpenModal" style="cursor:pointer">
                                                        <img style="width:674px !important; margin:0 !important; height:500px"
                                                            src="{{ asset($image) }}"
                                                            alt="Used cars for sale {{ $inventory->title ?? '' }}, price is {{ $inventory->price ?? '' }}, vin {{ $inventory->vin ?? '' }} in {{ $inventory->dealer->city ?? '' }}, {{ strtoupper($inventory->dealer->state ?? '') }}, dealer name is {{ $inventory->dealer->name ?? '' }} Best Dream car image"
                                                            class="swipter-top-img" loading="lazy"
                                                            onerror="this.onerror=null; this.src='{{ asset($not_found_image) }}'; this.alt='Vehicle image not available';">
                                                    </a>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <!-- Navigation buttons for the main slider -->

                                    <div style="background:none" class="swiper-button-next"><i
                                            style="font-size:22px; font-weight:700" class="fa fa-angle-right "></i></div>
                                    <div style="color:black" class="swiper-button-prev"><i
                                            style="font-size:22px; font-weight:700" class="fa fa-angle-left"></i></div>
                                </div>

                                <!-- Swiper for thumbnails -->
                                <div style="width:97%; margin:0 auto" class="row mt-2 mb-3">
                                    <div thumbsSlider="" class="swiper mySwiper">
                                        <div class="swiper-wrapper">
                                            @php
                                                $image_obj = $inventory->additionalInventory->local_img_url;
                                                $image_str = str_replace(['[', ' ', "'", ']'], '', $image_obj);
                                                $images = array_filter(explode(',', $image_str)); // Filter out empty values
                                                $images_count = count($images);
                                                $not_found_image = 'frontend/NotFound.png';
                                            @endphp

                                            @if ($images_count <= 1)
                                                <div class="swiper-slide">
                                                    <img class="me-2 br-3 swipper-bottom-image"
                                                        src="{{ asset($not_found_image) }}"
                                                        alt="Vehicle image not available" loading="lazy"
                                                        onerror="this.onerror=null; this.src='{{ asset($not_found_image) }}'; this.alt='Vehicle image not available';">
                                                </div>
                                            @else
                                                @foreach ($images as $image)
                                                    <div class="swiper-slide">
                                                        <img class="me-2 br-3 swipper-bottom-image"
                                                            src="{{ asset($image) }}"
                                                            alt="Used cars for sale {{ $inventory->title ?? '' }}, price is {{ $inventory->price ?? '' }}, vin {{ $inventory->vin ?? '' }} in {{ $inventory->dealer->city ?? '' }}, {{ strtoupper($inventory->dealer->state ?? '') }}, dealer name is {{ $inventory->dealer->name ?? '' }} Best Dream car image"
                                                            loading="lazy"
                                                            onerror="this.onerror=null; this.src='{{ asset($not_found_image) }}'; this.alt='Vehicle image not available';">
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                        <section>

                                            @php
                                                // Parse the car price
                                                $price_cus_amount = (int) str_replace(
                                                    ['$', ','],
                                                    '',
                                                    $inventory->price_formate,
                                                ); // Car price: 11995

                                                // Calculate sales tax (8.5%)
                                                $price_sales_tax = $price_cus_amount * (8.5 / 100); // 8.5% sales tax

                                                // Total loan amount (principal)
                                                $total_loan_amount = $price_cus_amount + $price_sales_tax;

                                                // Annual APR (9%) converted to monthly rate
                                                $monthly_interest_rate = 9 / 100 / 12;

                                                // Number of months (72 months)
                                                $loan_term_months = 72;

                                                // Calculate monthly payment using amortization formula
                                                $global_cus_monthly_payment =
                                                    ($total_loan_amount *
                                                        $monthly_interest_rate *
                                                        pow(1 + $monthly_interest_rate, $loan_term_months)) /
                                                    (pow(1 + $monthly_interest_rate, $loan_term_months) - 1);

                                                // Output debug information

                                            @endphp
                                            <p style="margin-top:25px; line-height:1; font-size:12px">
                                                {{ ucfirst($inventory->type) }}</p>
                                            @php
                                                $make_title =
                                                    $inventory->make .
                                                    ' ' .
                                                    $inventory->model .
                                                    ' ' .
                                                    ucwords(
                                                        str_replace(
                                                            '_',
                                                            ' ',
                                                            $inventory->trim == 'N/A' ? '' : $inventory->trim,
                                                        ),
                                                    );
                                            @endphp

                                            {{-- <h1 class="inventory-title ">{{ $inventory->year ?? '' }}
                                                {{ $inventory->make ?? '' }} {{ $inventory->model ?? '' }}
                                                {{ $inventory->trim != 'N/A' ? $inventory->trim : '' }}</h1> --}}
                                            <h1 class="inventory-title">{{ $make_title }}</h1>
                                            <p style="line-height:1; font-size:14px">
                                                {{ number_format($inventory->miles) . ' miles' }}</p>
                                            <h4 style="line-height:1.1; font-size:20px ;font-weight:700">
                                                {{ $inventory->PriceFormate }}

                                                @php
                                                    $totalPriceChange = 0; // Initialize the total price change
                                                @endphp

                                                @foreach ($inventory->mainPriceHistory as $history)
                                                    @if (strpos($history->change_amount, '+') !== false || strpos($history->change_amount, '-') !== false)
                                                        @php
                                                            // Remove dollar signs and commas, and convert the amount to a float
                                                            $changeAmount = (float) str_replace(
                                                                ['$', ',', '+'],
                                                                '',
                                                                $history->change_amount,
                                                            );

                                                            // Add to total price change regardless of the sign
                                                            $totalPriceChange += $changeAmount;
                                                        @endphp
                                                    @endif
                                                @endforeach

                                                @if ($totalPriceChange != 0)
                                                    <small style="font-size:12px">
                                                        <strong>
                                                            @if ($totalPriceChange > 0)
                                                                ${{ number_format($totalPriceChange, 0) }} price rise
                                                            @else
                                                                ${{ number_format(abs($totalPriceChange), 0) }} price drop
                                                            @endif
                                                        </strong>
                                                    </small>
                                                @endif
                                            </h4>
                                            <p
                                                style="display: inline-block;border-bottom:1px solid black;line-height:1; font-size:14px">
                                                EST . ${{ number_format($inventory->payment_price) ?? floor(number_format($global_cus_monthly_payment)) }}/mo*</p>
                                            <br>

                                            @if ($inventory->price_rating != null)
                                                @if ($inventory->price_rating == 'great-deal')
                                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                                        <span class="badge rounded-pill badge-info"
                                                            style="display: inline-flex; align-items: center;">
                                                            <i class="fa fa-angle-double-down pr-2"></i>
                                                        </span>
                                                        <p style="line-height:1; font-size:14px; margin: 0;">Excellent
                                                            Price</p>
                                                    </div>
                                                @elseif($inventory->price_rating == 'good-deal')
                                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                                        <span class="badge rounded-pill badge-success"
                                                            style="display: inline-flex; align-items: center;">
                                                            <i class="fa fa-angle-down pr-2"></i>
                                                        </span>
                                                        <p style="line-height:1; font-size:14px; margin: 0;">Great Price
                                                        </p>
                                                    </div>
                                                @elseif($inventory->price_rating == 'fair-deal')
                                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                                        <span class="badge rounded-pill badge-warning"
                                                            style="display: inline-flex; align-items: center;">
                                                            <i class="fa fa-check-circle pr-2"></i>
                                                        </span>
                                                        <p style="line-height:1; font-size:14px; margin: 0;">Fair Price</p>
                                                    </div>
                                                @endif
                                            @endif
                                        </section>
                                        <!-- Navigation buttons for the thumbnails -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- practising practising practising practising practising practising practising practising practising practising  -->
                    </div>


                    {{-- <div class="container mobile-price">
                    <div style=" margin-top: -10px;" class="row mb-2 ">
                        <div style=" border-top-left-radius: 7px !important; border-bottom-left-radius: 7px !important; background: white; border-right:1px solid gray; border-left:1px solid gray"
                            class="col-6">
                            <h4
                                style="margin-top: 15px; margin-left:25px; color:rgb(0, 0, 0); font-size:20px; font-weight:bold">
                                {{ $inventory->PriceFormate }}
            </h4>
        </div>
        <div style="border-left:1px solid gray;  border-right:1px solid gray; border-top-right-radius: 7px !important; border-bottom-right-radius: 7px !important; background: white; height:55px"
            class="col-6">
            <h4 style="margin-top: 15px; margin-left:25px; color:rgb(0, 0, 0); font-size:20px; font-weight:bold" id="mobile_monthly_pay">
                ${{ floor($global_cus_monthly_payment) }}/ <span style="font-size:20px">{{ 'mo*' }}</h4>
        </div>
    </div>
    </div> --}}

                    <div class="details-info-card">
                        <div class="">
                            <div class="border-0">
                                {{-- <div class="wideget-user-tab wideget-user-tab3">
                                <div class="tab-menu-heading">
                                    <div class="tabs-menu1">
                                        <ul class="nav">
                                            <li class=""><a href="#tab-1" class="active overview-btn"
                                                    data-bs-toggle="tab">Overview</a></li>
                                            <li><a href="#tab-4" data-bs-toggle="tab" class="v-info">More
                                                    Information</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div> --}}
                                <div class="tab-content border br-ts-0 br-3 p-5 bg-white mb-4 over-view-card">
                                    <div class="tab-pane active" id="tab-1">
                                        <h2 class=" mb-3 mt-5"><strong>Overview</strong></h2>
                                        <div class="mb-4">
                                            {{-- <p>
                                            For an overview of the best cars and a comprehensive review, you might find
                                            bestdreamcar reviews particularly insightful. They offer a detailed look
                                            at the used vehicles, including first drive impressions, instrumented tests,
                                            comparison tests, and long-term evaluations. This ensures you get a
                                            well-rounded view of each vehicle's performance, comfort, and reliability.
                                        </p> --}}

                                        </div>

                                        <div class="row">
                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                                <div class="row p-2">

                                                    <div style="margin-left:-12px !important"
                                                        class="col-xs-12 col-md-6 col-sm-6">

                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Exterior Color ">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/art.png') }}"
                                                                alt="Used cars for sale for Best Dream car exteriror color image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Exterior :
                                                                {{ $inventory->exterior_color }}
                                                            </p>
                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Mileage">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/miles.png') }}"
                                                                alt="Used cars for sale for Best Dream car miles image icon " />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Mileage :
                                                                {{ number_format($inventory->miles) }} miles
                                                            </p>
                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Fuel ">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/gas.png') }}"
                                                                alt="Used cars for sale for Best Dream car fuel image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">MPG :
                                                                {{ str_replace('----', ' ', $inventory->mpg_city) }} City/
                                                                {{ str_replace('----', ' ', $inventory->mpg_highway) }}
                                                                Highway
                                                            </p>
                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px " class="boxicons"
                                                            title="Drive Train ">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/drive.png') }}"
                                                                alt="Used cars for sale for Best Dream car drivetrain image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Drive Train :
                                                                {{ $inventory->drive_info }}
                                                            </p>
                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="VIN">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/calendar.png') }}"
                                                                alt="Used cars for sale for Best Dream car vin image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">VIN : {{ $inventory->vin }}</p>
                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Vehicle Type">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/used.png') }}"
                                                                alt="Used cars for sale for Best Dream car vehicle type image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Condition : {{ $inventory->type }}
                                                            </p>
                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Year">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/calendar.png') }}"
                                                                alt="Used cars for sale for Best Dream car calendar image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Year : {{ $inventory->year }}</p>
                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Location">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/location.png') }}"
                                                                alt="Used cars for sale for Best Dream car location image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Location
                                                                : {{ $inventory->dealer->city }},
                                                                {{ $inventory->dealer->state }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div style="margin-left:-12px" class="col-xs-12 col-md-6  col-sm-6 ">
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Interior Color">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/model.png') }}"
                                                                alt="Used cars for sale for Best Dream car interior color image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Interior :
                                                                {{ $inventory->interior_color }}</p>
                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons "
                                                            title="Fuel">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/g.png') }}"
                                                                alt="Used cars for sale for Best Dream car fuel image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Fuel Type :
                                                                {{ str_replace('----', ' ', $inventory->fuel) }}
                                                            </p>
                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Transmission">
                                                            <i style="color:rgb(212, 9, 185); font-size:25px; font-weight:500"
                                                                class="flaticon-gearshift transmission icon-auto"></i>
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/transmission.png') }}"
                                                                alt=" Used cars for sale for Best Dream car transmission image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Transmission :
                                                                {{ $inventory->formatted_transmission }}
                                                            </p>
                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Engine">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/engine.png') }}"
                                                                alt="Used cars for sale for Best Dream car engine image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Engine
                                                                :
                                                                {{ str_replace('----', ' ', $inventory->engine_details) }}
                                                            </p>
                                                        </div>

                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Listed">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/add.png') }}"
                                                                alt="Used cars for sale for Best Dream car listed day image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Added :
                                                                @if($inventory->created_date)
                                                                    {{ (int)\Carbon\Carbon::parse($inventory->created_date)->diffInDays() }} Days ago
                                                                @endif
                                                            </p>

                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Model">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/model.png') }}"
                                                                alt="Used cars for sale for Best Dream car model image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Model : {{ $inventory->model }}</p>
                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Body Type">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/b.png') }}"
                                                                alt="Used cars for sale for Best Dream car vehicle body type image icon" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Body :
                                                                {{ ucwords($inventory->body_formated) }}
                                                            </p>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <h2 class=" mb-3 mt-5"><strong>Features</strong></h2>
                                        @php
                                            // Sample data from the database
                                            $description =
                                                $inventory->additionalInventory->vehicle_additional_description;
                                            // Split sections by the main delimiter (comma)
                                            $sections = explode(',', $description);

                                            foreach ($sections as $section) {
                                                // Split category and features by colon
                                                if (strpos($section, ':') !== false) {
                                                    [$category, $featuresList] = explode(':', $section, 2);
                                                    $features[trim($category)] = array_map(
                                                        'trim',
                                                        explode(';', $featuresList),
                                                    );
                                                }
                                            }
                                        @endphp

                                        @if (!empty($features))
                                            <table class="table">
                                                </thead>
                                                <tbody>
                                                    @foreach ($features as $category => $featureList)
                                                        <tr>
                                                            <td>
                                                                <h4><strong>{{ $category }}</strong></h4>
                                                            </td>
                                                            <td>
                                                                <ul style="list-style-type: none; padding-left: 0;">
                                                                    @foreach ($featureList as $feature)
                                                                        <li>{{ $feature }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                        <h4><strong>Additional popular features:</strong></h4>

                                        <p class="mb-5"
                                            style="max-height: 60px; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $inventory->additionalInventory->vehicle_feature_description }}
                                        </p>
                                        <a id="viewMoreAdditionalButton" class="p-0" data-bs-toggle="modal"
                                            data-bs-target="#featuresModal">
                                            <strong class="hyperlink-title" style="border-bottom: 1px solid black;">View
                                                all features</strong> <i class="fa fa-caret-down"></i>
                                        </a>

                                        <!-- Modal Structure -->
                                        <div class="modal fade" id="featuresModal" tabindex="-1"
                                            aria-labelledby="featuresModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 id="featuresModalLabel"><strong>Vehicle Features</strong></h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                                                        @php
                                                            // Split the features by commas
                                                            $features = explode(
                                                                ',',
                                                                $inventory->additionalInventory
                                                                    ->vehicle_feature_description,
                                                            );
                                                        @endphp

                                                        <!-- Display Features in Two Columns -->
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <ul style="list-style-type: disc; padding-left: 20px;">
                                                                    @foreach ($features as $index => $feature)
                                                                        @if ($index % 2 == 0)
                                                                            <!-- Features for first column -->
                                                                            <li style="color: aqua; font-size:16px"> <span
                                                                                    style="color:black; font-size:16px">{{ trim($feature) }}</span>
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <ul style="list-style-type: disc; padding-left: 20px;">
                                                                    @foreach ($features as $index => $feature)
                                                                        @if ($index % 2 != 0)
                                                                            <!-- Features for second column -->
                                                                            <li style="color: aqua; font-size:16px"> <span
                                                                                    style="color:black; font-size:16px">{{ trim($feature) }}</span>
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="tab-4">
                                        <h2 class="card-title mb-3 font-weight-semibold">Vehicle Information</h2>
                                        <div class="mb-4">
                                            {{-- <p>The {{ $inventory->title }} led the premium cars category, while the Ford
                            Mustang Dark Horse was celebrated for its thrilling performance and
                            handling​. These vehicles exemplify the advancements and variety available
                            in the automotive market this year.
                            </p> --}}
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                                <div class="row p-2">
                                                    <div style="margin-left:-12px !important"
                                                        class="col-xs-12 col-md-6 col-sm-6">

                                                        <div style="display: flex; margin-bottom:6px" class="boxicons "
                                                            title="Fuel">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/t.png') }}" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Trim : {{ $inventory->trim }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div style="margin-left:-12px" class="col-xs-12 col-md-6  col-sm-6 ">
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Model">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/model.png') }}" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Make : {{ $inventory->make }}</p>
                                                        </div>
                                                        <div style="display: flex; margin-bottom:6px" class="boxicons"
                                                            title="Exterior Color ">
                                                            <img style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:6px"
                                                                src="{{ asset('/frontend/assets/images/gas.png') }}" />
                                                            <p style="color:black; margin-left:10px; font-weight:600; margin-top:11px"
                                                                class="auto-icon-para">Mpg City :
                                                                {{ $inventory->mpg_city }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="row">
                                        <div class="col-lg-6">
                                            <div class="table-responsive">
                                                <table class="table table-bordered border-top">
                                                    <tbody>
                                                        <tr>
                                                            <td>Make</td>
                                                            <td>{{ $inventory->make }}</td>
                        </tr>

                        <tr>
                            <td>Body</td>
                            <td>{{ $inventory->body_formated }}</td>
                        </tr>
                        <tr>
                            <td>Trim</td>
                            <td>{{ $inventory->trim }}</td>
                        </tr>
                        </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="table-responsive">
                        <table class="table table-bordered border-top mb-0">
                            <tbody>
                                <tr>
                                    <td>MPG City</td>
                                    <td>{{ $inventory->mpg_city }} Miles</td>
                                </tr>
                                <tr>
                                    <td>MPG Highway</td>
                                    <td>{{ $inventory->mpg_highway }} Miles</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> --}}
                                    </div>
                                    <div class="tab-pane" id="tab-5">
                                        <ul class="list-unstyled video-list-thumbs row ">
                                            <li class="mb-0">
                                                <a data-bs-toggle="modal" data-bs-target="#homeVideo">
                                                    <img src="{{ 'frontend' }}/assets/images/products/cars/v1.jpg"
                                                        alt="Barca" class="img-responsive w-100 br-3">
                                                    <span
                                                        class="mdi mdi-arrow-right-drop-circle-outline text-white"></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/Classified Description-->

                    <div style="background:#e0f8f3 " class="card mt-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 p-2">
                                    <h2 style="margin-top:-7px; margin-bottom:29px;"><strong>Seller's info</strong> </h2>
                                    <div class="row">
                                        @php
                                            $address = $inventory->dealer->dealer_full_address;
                                            $formattedAddress = preg_replace(
                                                '/(\d+\s[\w\s]+)(St|Ave|Blvd|Rd|Dr)(\s)([\w\s]+,\s\w{2})(\s\d{5})/',
                                                '$1$2. $4$5',
                                                $address,
                                            );
                                        @endphp
                                        <div class="mb-3">
                                            <div class="mb-4">
                                                <a href="#" class="text-dark">
                                                    <h4>
                                                        <strong>{{ $inventory->dealer->name ?? explode(' in ', $inventory->dealer->name)[0] }}</strong>
                                                    </h4>
                                                </a>
                                            </div>
                                            @php
                                                // Assuming these values come dynamically
                                                $averageRating = $inventory->dealer->rating ?? 4.7; // Replace with the actual rating (number or string)
                                                $reviews = $inventory->dealer->review ?? 43; // Replace with the actual review count

                                                // Ensure the rating is within the valid range (1-5)
                                                $averageRating =
                                                    $averageRating >= 1 && $averageRating <= 5 ? $averageRating : 0;

                                                // Generate star HTML
                                                if (!function_exists('renderStars')) {
                                                    function renderStars($rating)
                                                    {
                                                        $fullStars = floor($rating); // Full stars
                                                        $halfStar = $rating - $fullStars >= 0.5 ? 1 : 0; // Half star logic
                                                        $emptyStars = 5 - ($fullStars + $halfStar); // Empty stars

                                                        $starsHtml = str_repeat(
                                                            '<i class="fa fa-star text-warning"></i>',
                                                            $fullStars,
                                                        );
                                                        if ($halfStar) {
                                                            $starsHtml .=
                                                                '<i class="fa fa-star-half-o text-warning"></i>';
                                                        }
                                                        $starsHtml .= str_repeat(
                                                            '<i class="fa fa-star text-secondary"></i>',
                                                            $emptyStars,
                                                        ); // Empty stars
                                                        return $starsHtml;
                                                    }
                                                }
                                            @endphp

                                            <div class="mb-2">
                                                <span>{{ $averageRating }} {!! renderStars($averageRating) !!}
                                                    {{ $reviews }}</span>
                                            </div>

                                            <div class="mb-5">
                                                <span>{{ $formattedAddress ?? $inventory->dealer->city . ' ' . strtoupper($inventory->dealer->state . ' ' . $inventory->zip_code) }}</span>
                                            </div>

                                        </div>
                                        <div class="mt-5">
                                            <h3 style="font-size:20px"><strong>Seller’s notes about this car</strong></h3>
                                            <!-- Seller's Notes Section -->
                                            <p id="sellerNotes" class="mb-5"
                                                style="max-height: 60px; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $inventory->additionalInventory->seller_note }}
                                            </p>
                                        </div>
                                    </div>
                                    <!-- View All Button -->
                                    <a id="viewMoreButton" class=" p-0">
                                        <strong class="hyperlink-title" style="border-bottom:1px solid black">View all
                                            seller's note</strong> <i class="fa fa-caret-down"></i>
                                    </a>
                                    @if (!auth()->user())
                                        <!-- <button data-bs-toggle="modal" data-bs-target="#exampleModal"
                                                                            style="background: black; border:1px solid black; padding: 9px 40px; border-radius:22px; color:white">Sign
                                                                            in or register</button> -->
                                    @endif

                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                        </div>
                    </div>
                    @if ($inventory->dealer_comment)
                        <div class="card">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-md-12 p-2">
                                        <h3 style="margin-bottom: 12px; margin-top:-8px">Seller description</h3>

                                        @php
                                            $description_data = substr($inventory->dealer_comment, 0, 520);
                                            $lest_data = substr($inventory->dealer_comment, 520);

                                        @endphp

                                        @if ($inventory->dealer_comment)
                                            <p>{{ $description_data }} <span id="text_data"
                                                    style="display: none;">{{ $lest_data }}</span></p>
                                            <a id="show-more-button" onclick="truncateText()"
                                                style="float:right; color:rgb(14, 87, 223)"><u>Show more</u></a>
                                        @else
                                            <p
                                                style="text-align:center; margin-top:65px; font-size:17px; margin-bottom:65px">
                                                No
                                                Seller Description</p>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                            </div>
                        </div>
                    @endif
                    {{-- this part show only md  device  --}}

                    @if ($relateds->count() > 1)
                        <div class="hide-on-phone hide-on-tablet">
                            <h2 class="mb-5 mt-6">Recommended For You</h2>
                            <!--Related Posts-->
                            <div id="myCarousel5" class="owl-carousel owl-carousel-icons3">
                                @forelse ($relateds as $item)
                                    <div class="item">
                                        <div style="background: rgb(255, 255, 255);
                                background: linear-gradient(0deg, rgb(232, 245, 243) 0%, rgb(255, 255, 255) 100%);"
                                            class="card mb-0">
                                            <div class="power-ribbon power-ribbon-top-left text-warning">
                                                <span class="bg-warning"><i class="fa fa-bolt"></i></span>
                                            </div>

                                            @php
                                                $image_obj = $item->additionalInventory->local_img_url;
                                                $image_str = str_replace(['[', ' ', "'", ']'], '', $image_obj); // Clean up the string
                                                $images = explode(',', $image_str); // Split into array
                                                $images_count = count(array_filter($images)); // Count non-empty images
                                                $not_found_image = 'frontend/NotFound.png'; // Path to the "not found" image
                                                $vin_string_replace = str_replace(' ', '', $item->vin);
                                                $route_string = str_replace(
                                                    ' ',
                                                    '',
                                                    $item->year .
                                                        '-' .
                                                        $item->make .
                                                        '-' .
                                                        $item->model .
                                                        '-in-' .
                                                        $item->dealer->city .
                                                        '-' .
                                                        $item->dealer->state,
                                                );
                                            @endphp

                                            <div class="item-card2-img">
                                                @if ($images_count > 1)
                                                    <a class="link"
                                                        href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"></a>
                                                    <img src="{{ asset($images[0]) }}" alt="img"
                                                        class="auto-ajax-photo-details" loading="lazy"
                                                        onerror="this.onerror=null; this.src='{{ asset($not_found_image) }}';">
                                                @else
                                                    <a class="link"
                                                        href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"></a>
                                                    <img src="{{ asset($not_found_image) }}" width="100%"
                                                        alt="img" class="auto-ajax-photo-details-not-found">
                                                @endif
                                            </div>


                                            <div class="item-card2-icons">
                                                @php
                                                    $countWishList = 0;
                                                    $favourites = session()->get('favourite', []); // Ensure default is an empty array

                                                    foreach ($favourites as $favorite) {
                                                        if (isset($favorite['id']) && $favorite['id'] == $item->id) {
                                                            $countWishList = 1;
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                <div style="margin-top:-20px; margin-left:28px" class="item-card9-icons">
                                                    <a href="javascript:void(0);" class="item-card9-icons1 "
                                                        data-productid="{{ $item->id }}">
                                                        @if ($countWishList > 0)
                                                            <i class="fa fa-heart" style="color: red"></i>
                                                        @else
                                                            <i class="fa fa-heart-o"></i>
                                                        @endif
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body pb-4">
                                                <div class="item-card2">
                                                    <div class="item-card2-desc">
                                                        <div class="item-card2-text">
                                                            @php
                                                                $title = Str::limit($item->title, 25, '...');
                                                            @endphp
                                                            <a href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"
                                                                class="text-dark">
                                                                <h4 style="font-weight:600" class="mb-0"
                                                                    title="{{ $item->title }}">
                                                                    {{ $title }}
                                                                </h4>
                                                            </a>
                                                        </div>
                                                        <div class="item-card9-desc mb-0 mt-1">
                                                            @php
                                                                $transmission = substr(
                                                                    $item->formatted_transmission,
                                                                    0,
                                                                    25,
                                                                );
                                                            @endphp
                                                            <p href="javascript:void(0);"
                                                                class="me-4 d-inline-block mb-0"><span class="">
                                                                    {{ $transmission }}</span></p>
                                                            <p class="mb-1" style="margin:0">
                                                                @if (in_array($item->type, ['Preowned', 'Certified Preowned']))
                                                                    Used
                                                                @else
                                                                    {{ $item->type }}
                                                                @endif
                                                            </p>
                                                            {{-- <a href="javascript:void(0);" class="me-4 d-inline-block"><span
                                                        class=""><i class="fa fa-calendar-o text-muted me-1"></i>
                                                        {{($inventory->created_at)->diffForHumans()}}</span></a> --}}
                                                        </div>
                                                        <div style="height: 25px" class="d-flex mb-1">
                                                            <h4 class="me-3" style="font-weight:600">
                                                                {{ $item->price_formate }}
                                                            </h4>
                                                            <p
                                                                style="color:black; font-weight:600; font-size:12px; margin-top:2px">
                                                                ${{ number_format($item->payment_price) }}/mo*</p>
                                                        </div>
                                                        <div>
                                                            <p class="d-flex mb-1">
                                                                <img class="me-1"
                                                                    style="width:21px; height:21px; margin-top:0px"
                                                                    src="{{ asset('/frontend/assets/images/miles.png') }}" />
                                                                {{ number_format($item->miles) != 0 ? number_format($item->miles) . ' miles' : 'TBD' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer pe-4 ps-4 pt-4 pb-4">
                                                <div class="item-card9-footer d-flex">
                                                    <i style="color:black !important"
                                                        class="fa fa-map-marker text-muted me-1"></i>
                                                    @php
                                                        $cus_dealer = explode(' in ', $item->dealer->name)[0];
                                                        $nameLength = strlen($cus_dealer);
                                                        $name = Str::substr($cus_dealer, 0, 25);
                                                    @endphp
                                                    @if ($nameLength <= '25')
                                                        <h5 class="dealer-add" style="color:black">{{ $cus_dealer }}
                                                            <br>
                                                            <span style="font-size:14px">{{ $inventory->dealer->city }},
                                                                {{ strtoupper($inventory->dealer->state) }}
                                                                {{ $item->zip_code }}

                                                                @if (isset($item->distance) && $item->distance > 0.9)
                                                                    <span>
                                                                        ({{ round($item->distance, 0) }} mi. away)
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </h5>
                                                    @else
                                                        <h5 title="{{ $cus_dealer }}" class="dealer-add"
                                                            style="color:black">
                                                            {{ $name }}...
                                                            <br>
                                                            <span style="font-size:14px">{{ $inventory->dealer->city }},
                                                                {{ strtoupper($inventory->dealer->state) }}
                                                                {{ $item->zip_code }}

                                                                @if (isset($item->distance) && $item->distance > 0.9)
                                                                    <span>
                                                                        ({{ round($item->distance, 0) }} mi. away)
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </h5>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-card">
                                        <div style="background: #f5f5f5; padding: 20px; text-align: center;"
                                            class="card mb-0">
                                            <h4 style="color: #999;">No related items found</h4>
                                            <p style="color: #666;">Please check back later or explore other categories.
                                            </p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                    {{-- this part show only md  device  --}}
                    <!--/Related Posts-->
                </div>

                <!--Right Side Content-->
                <div class="col-xl-5 col-lg-5 col-md-12">
                    <!-- message seller card  -->
                    <div class="hide-on-laptop hide-on-phone card shadow-sm mx-auto "
                        style="max-width: 31rem; position: relative;">
                        <!-- Love and Share Icons -->
                        <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                            <!-- Love Icon -->
                            {{-- <a href="javascript:void(0)" class="me-3" id="wishlist2" style="font-size:1.5rem;"
                                title="Favorites">
                                @if ($countWishList > 0)
                                    <i class="fa fa-heart" style="color: red"></i>
                                @else
                                    <i class="fa fa-heart-o"></i>
                                @endif
                            </a> --}}
                            <!-- favorite Icon -->
                            {{-- <a href="#" class=" me-3" style="font-size:1.5rem" title="Add to Copy">
                    <i class="fa fa-copy"></i>
                </a> --}}
                            <!-- Share Icon -->
                            {{-- <a href="javascript:void(0)" class="text-muted" style="font-size:1.5rem" title="Share">
                                <i class="fas fa-share-alt"></i>
                            </a> --}}

                        </div>

                        <div class="card-body">
                            <!-- Vehicle Condition -->
                            <small class="text-muted">{{ ucfirst($inventory->type) }}</small>
                            <!-- Vehicle Title -->
                            <h5 class="card-title mt-2">
                                <span class="card-focus-header-year fw-bold">{{ $inventory->year }}</span>

                                <span class="card-focus-header">{{ $make_title }}</span>
                            </h5>
                            <!-- Vehicle Details -->
                            <p class="text-muted mb-4">
                                {{ number_format($inventory->miles) }} miles
                                • {{ $inventory->formatted_transmission ?? 'No transmission' }}
                                • {{ $inventory->fuel }}
                            </p>
                            <!-- Price -->
                            <h3 class="fw-bold text-dark" style="font-size:2.5rem">{{ $inventory->price_formate }}
                                <strong
                                    style="font-weight:300px; font-size:12px">${{ number_format($inventory->payment_price) ?? floor(number_format($global_cus_monthly_payment)) }}
                                    /mo* </strong>
                            </h3>
                            <!-- Prequalification Section -->
                            <div class="bg-light p-3 rounded mt-4">
                                <h6 class="fw-bold mb-3">Message Seller</h6>
                                <!-- Checkbox 1 -->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="creditScore" checked disabled>
                                    <label class="form-check-label" for="creditScore">
                                        You can contact with dealer.
                                    </label>
                                </div>
                                <!-- Checkbox 2 -->
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="tailoredOffer" checked disabled>
                                    <label class="form-check-label" for="tailoredOffer">
                                        Receive a tailored offer instantly, complete with realistic monthly payments for
                                        this vehicle.
                                    </label>
                                </div>
                                <!-- Button -->
                                <button class="front-btn btn btn-block mt-3" target="#submitBtn"
                                    onclick="document.getElementById('message-seller').scrollIntoView({ behavior: 'smooth' });">Message
                                    Seller</button>
                            </div>
                        </div>
                    </div>

                    <!-- message seller card  -->
                    <div class="card">
                        <div class="special-card card-body dealer-details">
                            <div class="profile-pic mb-0">
                                {{-- <img src="{{ asset('frontend/assets/images/ex.png') }}" class="brround avatar-xxl"
                    alt="user"> --}}
                                <div>
                                    <a href="#" class="text-dark">
                                        <h4 style="color:#f8f3f3" class="mt-3 mb-1  font-weight-semibold">
                                            {{ explode(' in ', $inventory->dealer->name)[0] }}
                                        </h4>
                                    </a>

                                    <span style="color:#e9e6e6" class=" ">{{ $inventory->dealer->city }},
                                        {{ strtoupper($inventory->dealer->state) }}
                                        {{ $inventory->zip_code }}</span>

                                </div>
                            </div>
                        </div>
                        <div style="background: rgb(228, 228, 235);"
                            class="card-body item-user auto-deatails-message-option">
                            <h4 class="mb-4" id="message-seller">Message Seller</h4>
                            <div class="row p-1">
                                <div style="border-radius:6px; box-shadow: rgba(100, 100, 111, 0.2) 0px 4px 7px 0px;"
                                    class="col-md-12 p-2 bg-white">
                                    <form id="SendLeaddetails" style=" border-radius:5px">
                                        @csrf
                                        <input type="hidden" id="inventory_id" name="inventories_id"
                                            value="{{ $inventory->id }}">
                                        <input type="hidden" id="dealer_id" name="dealer_id"
                                            value="{{ $inventory->user_id }}">

                                        <div class="">
                                            <div class="row p-2">
                                                <div class="col-md-6 col-sm-6 col-xs-12 " style="margin-top:20px">
                                                    <div class="form-group ">

                                                        <input
                                                            style="border-radius:5px; border:1px solid rgb(189, 188, 188)"
                                                            placeholder="First Name*" class="form-control fname"
                                                            type="text" id="first_name" name="first_name"
                                                            value="{{ Auth()->check() ? Auth()->user()->fname : '' }}">
                                                        <span id="first_name_error" class="text-danger"
                                                            role="alert"></span>

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 " style="margin-top:20px">
                                                    <div class="form-group ">

                                                        <input
                                                            style="border-radius:5px; border:1px solid rgb(189, 188, 188)"
                                                            placeholder="Last Name*" class="form-control lname"
                                                            type="text" id="last_name" name="last_name"
                                                            value="{{ Auth()->check() ? Auth()->user()->lname : '' }}">

                                                        <span id="last_name_error" class="text-danger"
                                                            role="alert"></span>

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                    <div class="form-group ">

                                                        <input
                                                            style="border-radius:5px; border:1px solid rgb(189, 188, 188)"
                                                            placeholder="E-mail*" class="form-control email"
                                                            type="text" id="email" name="email"
                                                            value="{{ Auth()->check() ? Auth()->user()->email : '' }}">

                                                        <span id="email_error" class="text-danger" role="alert"></span>

                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                    <div class="form-group ">

                                                        <input
                                                            style="border-radius:5px; border:1px solid rgb(189, 188, 188)"
                                                            class="form-control phone telephoneInput" type="text"
                                                            placeholder="cell" id="phone" name="phone"
                                                            value="{{ Auth()->check() ? Auth()->user()->phone : '' }}">

                                                        <span id="phone_error" class="text-danger" role="alert"></span>

                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12 ">
                                                    <div class="form-group ">
                                                        <textarea style="border-radius:5px; border:1px solid rgb(189, 188, 188)" id="w3review"
                                                            class="form-control description" name="description" rows="6" cols="55">I am interested and want to know more about the {{ $inventory->title }}, you have listed for {{ $inventory->price_formate }}  on Best Dream car.
                                                                                </textarea>

                                                        <span id="description_error" class="text-danger"
                                                            role="alert"></span>

                                                    </div>

                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12 ">
                                                    <div class="form-group ">
                                                        <p
                                                            style="color:rgb(67, 68, 68); font-weight:600; margin-bottom:15px; margin-top:10px">
                                                            <span class="text-black">*</span> Security Question (Enter the
                                                            Correct answer)
                                                        </p>

                                                        <div style="display: flex">
                                                            <div id="captchaLabel"
                                                                style="background-color:white; width:50%;  margin-right:10px; text-align:center; padding-top:7px; font-weight:600;  margin-top:2px; margin-left:3px; height:35px; border-radius:5px; border:1px solid rgb(189, 188, 188)">
                                                                {{ app('mathcaptcha')->label() }}
                                                            </div>
                                                            <div>
                                                                <input
                                                                    class="form-control @error('mathcaptcha') is-invalid @enderror"
                                                                    type="text" name="mathcaptcha"
                                                                    placeholder="Enter your result">
                                                                <span id="Wmathcaptcha" class="text-danger"
                                                                    role="alert">
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
                                                        <p style="color: rgb(27, 26, 26)"><input type="checkbox"
                                                                name="ask_trade" id="tradeChecked"
                                                                style="cursor: pointer">
                                                            <label for="tradeChecked" style="cursor: pointer">Do you have
                                                                a
                                                                trade-in?</label>
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="row p-0 m-0"
                                                    style="margin-left: 0px; margin-right:0px; display:none;"
                                                    id="Mobile_Trade_block_content">
                                                    <div class="row p-0 m-0">
                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <div class="form-group ">
                                                                <input style="border-radius:5px" placeholder="Year*"
                                                                    class="form-control year_trade" type="text"
                                                                    name="year" value="">
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                            <div class="form-group ">
                                                                <input style="border-radius:5px" placeholder="Make*"
                                                                    class="form-control make_trade" type="text"
                                                                    name="make" value="">
                                                                <span class="invalid-feedback7 text-danger"
                                                                    role="alert">
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                            <div class="form-group ">
                                                                <input style="border-radius:5px" placeholder="Model*"
                                                                    class="form-control model_trade" type="text"
                                                                    name="model" value="">
                                                                <span class="invalid-feedback8 text-danger"
                                                                    role="alert"></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                            <div class="form-group ">
                                                                <input style="border-radius:5px" placeholder="Mileage*"
                                                                    class="form-control mileage" type="text"
                                                                    name="mileage" value="">
                                                                <span class="invalid-feedback9 text-danger"
                                                                    role="alert">
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                            <div class="form-group ">

                                                                <input style="border-radius:5px" placeholder="Color*"
                                                                    class="form-control color" type="text"
                                                                    name="color" value="">

                                                                <span class="invalid-feedback10 text-danger"
                                                                    role="alert">
                                                                </span>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                            <div class="form-group ">
                                                                <input style="border-radius:5px"
                                                                    placeholder="VIN (optional)" class="form-control vin"
                                                                    type="text" name="vin" value="">

                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12 ">
                                                    <div class="form-group ">
                                                        <p style="color: rgb(77, 74, 74)"><input type="checkbox"
                                                                class="isEmailSend" name="isEmailSend"
                                                                style="cursor: pointer" checked> Email me price drops for
                                                            this vehicle </p>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-12 col-sm-12 col-xs-12 ">
                                                <div class="form-group">
                                                    <button type="submit" class="front-btn btn loading"
                                                        id="submitBtn">Send Message</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Form -->
                                    <p
                                        style="font-size: 12px; line-height: 11px; color: #999; margin-top: 5px;text-align:justify">
                                        By clicking "SEND EMAIL", I
                                        consent to be contacted by Best Dream car.com and the dealer selling
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
                    <!-- marif calculator start here  -->

                    {{-- <div class="card">
                    <div style="background: rgb(228, 228, 235);" class="card-body item-user">
                        <h4 class="mb-4">Financing Calculator</h4>
                        <div class="row p-0">
                            <div style="border-radius:6px; box-shadow: rgba(100, 100, 111, 0.2) 0px 4px 7px 0px;"
                                class="col-md-12 p-0 bg-white">

                                <div style="background: white; height:auto;" class="widget">

                                    <div
                                        style="border:1px solid rgb(43, 69, 73); border-radius: 5px 5px 0 0; width:100%; background: rgb(126, 87, 133); padding:0">
                                        @php
                                        $down_paym = ($inventory->price * 10) / 100;
                                        $loan_amount = $inventory->price - ($inventory->price * 10) / 100;
                                        @endphp
                                        <p
                                            style="font-size:19px; display:block; text-align:center; color:rgb(241, 240, 240); margin-top:8px">
                                            <small>Estimated Monthly Payment</small>
                                        </p>
                                        <h2 style="display:block; text-align:center; color:white; font-weight:500">
                                            $<span class="ms-1" id="monthly_pay">{{
                                                number_format($inventory->payment_price) }}</span>
        </h2>
        <p class="p-0 m-0"
            style="font-size:16px; display:block; text-align:center; color:rgb(235, 231, 231)"
            id="loan_amount"><small>Total Loan Amount:
                ${{ number_format($loan_amount) }}
            </small></p>
        <p
            style="font-size:15px; display:block; text-align:center; color:rgb(235, 231, 231)">
            <small>*Est. on 10% down & good credit </small>
        </p>
    </div>
    <div class="widget-content ">
        <div class="finance-calculator">
            <ul>
                <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                    <div class="row">

                        <div class=" col-lg-6 col-sm-6 col-md-6">
                            <label style="margin-top:15px">Credit Score</label>
                            <select class="common_calculate form-control mb-2"
                                id="credit_calculate">
                                <option value="rebuild">Rebuilding (0-620)</option>
                                <option value="fair">Fair (621-699)</option>
                                <option value="good" selected>Good (700-759)</option>
                                <option value="excellent">Excellent (760+)</option>
                            </select>
                        </div>
                        <div class=" col-lg-6 col-sm-6 col-md-6">
                            <label style="margin-top:15px">Vehicle Price</label>
                            <div style="width:100%" class="input-container">
                                <span class="dollar-sign-price">$</span>
                                <input type="text"
                                    class="form-control common_calculate  mb-2"
                                    placeholder="Enter a vehicle price"
                                    value="{{ $inventory->price }}"
                                    id="price_calculate">
                            </div>
                        </div>
                    </div>
                </li>
                <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                    <div class="row">
                        <div class=" col-lg-6 col-sm-6 col-md-6">
                            <label style="margin-top:15px">Interest Rate (APR)
                                %</label>
                            <input type="text"
                                class="form-control common_calculate mb-2"
                                placeholder="Enter an interest rate" value="5.82"
                                id="calculate_interest">
                        </div>

                        <div class=" col-lg-6 col-sm-6 col-md-6">
                            <label style="margin-top:15px">Down Payment</label>
                            <div style="width:100%" class="input-container">
                                <span class="dollar-sign">$</span>
                                <input type="text" class="form-control common_calculate"
                                    placeholder="Enter a down payment"
                                    id="calculate_downpayment" value="{{ $down_paym }}">
                            </div>
                        </div>
                    </div>
                </li>


                <ul class="finance-radio-list">
                    <label style="margin-top: 20px; margin-left:15px">Period
                        month:</label>
                    <div class="d-flex flex-wrap mt-3 ms-2">
                        <div class="p-2 monthly-package">
                            <li>
                                <input
                                    style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray; cursor:pointer"
                                    type="text" value="36" class="calculate_month"
                                    id="36" readonly />
                            </li>
                        </div>
                        <div class="p-2 monthly-package">
                            <li>
                                <input
                                    style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray; cursor:pointer"
                                    type="text" value="48" class="calculate_month"
                                    id="48" readonly />
                            </li>
                        </div>
                        <div class="p-2 monthly-package">
                            <li>
                                <input
                                    style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray; cursor:pointer"
                                    type="text" value="60" class="calculate_month"
                                    id="60" readonly />
                            </li>
                        </div>
                        <div class="p-2 monthly-package">
                            <li>
                                <input
                                    style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray; cursor:pointer"
                                    type="text" value="72"
                                    class="calculate_month active" id="72" readonly />
                            </li>
                        </div>
                        <div class="p-2 monthly-package">
                            <li>
                                <input
                                    style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray; cursor:pointer"
                                    type="text" value="84" class="calculate_month"
                                    id="84" readonly />
                            </li>
                        </div>
                    </div>
                </ul>

                <li style="height:auto;"
                    class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="auto-field">
                        <button
                            style="background:rgb(68, 29, 70); width:100%; margin-bottom:15px; color:white; font-size:15px; margin-top:15px; padding-top:8px; padding-bottom:8px;"
                            class="btn btn-theme btn-sm margin-bottom-20 common_calculate"
                            type="submit" value="Calculate">Calculate</button>
                    </div>
                </li>
                <p
                    style="padding:14px; text-align:justify; font-size:12px; margin-top:12px">
                    *This calculation is an estimate only. We’ve estimated your taxes
                    based on your provided ZIP code. Title, other fees, and incentives
                    are not included. Monthly payment estimates are for informational
                    purposes and do not represent a financing offer from the seller of
                    this vehicle. Other taxes may apply.</p>
            </ul>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div> --}}
                    <!-- new calculate marif calculator start here  -->

                    <div class="card">
                        <div style="background: rgb(228, 228, 235);" class="card-body item-user">
                            <h4 class="mb-4">Financing Calculator</h4>
                            <div class="row p-0">
                                <div style="border-radius:6px; box-shadow: rgba(100, 100, 111, 0.2) 0px 4px 7px 0px;"
                                    class="col-md-12 p-0 bg-white">

                                    <div style="background: white; height:auto;" class="widget">
                                        <div class="financial-detail-card">
                                            @php
                                                $down_paym = ($inventory->price * 10) / 100;
                                                $loan_amount = $inventory->price;
                                            @endphp
                                            <p
                                                style="font-size:19px; display:block; text-align:center; color:rgb(241, 240, 240); margin-top:8px">
                                                <small>Estimated Monthly Payment</small>
                                            </p>
                                            <h2 style="display:block; text-align:center; color:white; font-weight:500">
                                                $<span class="ms-1"
                                                    id="calculator_monthly_pay">{{ number_format($inventory->payment_price) ?? floor(number_format($global_cus_monthly_payment)) }}</span>
                                            </h2>
                                            <p class="p-0 m-0"
                                                style="font-size:16px; display:block; text-align:center; color:rgb(235, 231, 231)"
                                                id="calculator_loan_amount"><small>Total Loan Amount :
                                                    {{ number_format($loan_amount) }}</small></p>
                                            <p
                                                style="font-size:15px; display:block; text-align:center; color:rgb(235, 231, 231)">
                                                <small>*Est. on sales tax 8% & good credit </small>
                                            </p>
                                        </div>
                                        <div class="widget-content ">
                                            <div class="finance-calculator">
                                                <ul>
                                                    <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                                                        <div class="row">
                                                            <div class=" col-lg-6 col-sm-6 col-md-6">
                                                                <label style="margin-top:15px">Credit Score</label>
                                                                <select class="common_calculator form-control mb-2"
                                                                    id="credit_calculator">
                                                                    <!-- <option value="rebuild">Rebuilding (0-620)</option>
                                                                                    <option value="fair">Fair (621-699)</option>
                                                                                    <option value="good" selected>Good (700-759)</option>
                                                                                    <option value="excellent">Excellent (760+)</option> -->
                                                                </select>
                                                            </div>
                                                            <div class=" col-lg-6 col-sm-6 col-md-6">
                                                                <label style="margin-top:15px">Vehicle Price</label>
                                                                <div style="width:100%" class="input-container">
                                                                    <span class="dollar-sign-price">$</span>
                                                                    <input type="text"
                                                                        class="form-control common_calculator  mb-2"
                                                                        placeholder="Enter a vehicle price"
                                                                        value="{{ $inventory->price }}"
                                                                        id="price_calculator">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                                                        <div class="row">
                                                            <div class=" col-lg-6 col-sm-6 col-md-6">
                                                                <label style="margin-top:15px">Down Payment</label>
                                                                <div style="width:100%" class="input-container">
                                                                    <span class="dollar-sign">$</span>
                                                                    <input type="text"
                                                                        class="form-control common_calculator"
                                                                        placeholder="Enter a down payment"
                                                                        id="calculator_downpayment" value="0">
                                                                </div>
                                                            </div>
                                                            <div class=" col-lg-6 col-sm-6 col-md-6">
                                                                <label style="margin-top:15px">Trade In Value</label>
                                                                <div style="width:100%" class="input-container">
                                                                    <span class="dollar-sign">$</span>
                                                                    <input type="text"
                                                                        class="form-control common_calculator"
                                                                        placeholder="Trade In Value" id="trade_in_value"
                                                                        value="0">
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </li>
                                                    <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                                                        <div class="row">
                                                            <div class=" col-lg-6 col-sm-6 col-md-6">
                                                                <label style="margin-top:15px">Interest Rate (APR)
                                                                    %</label>
                                                                <input type="text"
                                                                    class="form-control common_calculator mb-2"
                                                                    placeholder="Enter an interest rate" value="9"
                                                                    id="calculator_interest">
                                                            </div>
                                                            <div class=" col-lg-6 col-sm-6 col-md-6">
                                                                <label style="margin-top:15px">Sales Tax <span
                                                                        id="sales_tax_rate_level">{{ $cityrate ?? $stateRate }}</span>%</label>
                                                                <input type="hidden" name="sales_tax_rate"
                                                                    id="sales_tax_rate"
                                                                    value="{{ $cityrate ?? $stateRate }}">
                                                                <input type="text"
                                                                    class="form-control common_calculator mb-2"
                                                                    placeholder="Sales Tax 8%" id="sales_tax">
                                                            </div>
                                                        </div>
                                                    </li>


                                                    <ul class="finance-radio-list">
                                                        <label style="margin-top: 20px; margin-left:15px">Period
                                                            month:</label>
                                                        <div class="d-flex flex-wrap mt-3 ms-2">
                                                            <div class="p-2 monthly-package">
                                                                <li>
                                                                    <input
                                                                        style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray; cursor:pointer"
                                                                        type="text" value="24"
                                                                        class="calculate_month" id="24" readonly />
                                                                </li>
                                                            </div>
                                                            <div class="p-2 monthly-package">
                                                                <li>
                                                                    <input
                                                                        style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray; cursor:pointer"
                                                                        type="text" value="36"
                                                                        class="calculate_month" id="36" readonly />
                                                                </li>
                                                            </div>
                                                            <div class="p-2 monthly-package">
                                                                <li>
                                                                    <input
                                                                        style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray; cursor:pointer"
                                                                        type="text" value="48"
                                                                        class="calculate_month" id="48" readonly />
                                                                </li>
                                                            </div>
                                                            <div class="p-2 monthly-package">
                                                                <li>
                                                                    <input
                                                                        style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray; cursor:pointer"
                                                                        type="text" value="60"
                                                                        class="calculate_month" id="60" readonly />
                                                                </li>
                                                            </div>
                                                            <div class="p-2 monthly-package">
                                                                <li>
                                                                    <input
                                                                        style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray; cursor:pointer"
                                                                        type="text" value="72"
                                                                        class="calculate_month active" id="72"
                                                                        readonly />
                                                                </li>
                                                            </div>
                                                        </div>
                                                    </ul>

                                                    {{-- <li style="height:auto;"
                                                    class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="auto-field">
                                                        <button
                                                            style="background:rgb(68, 29, 70); width:100%; margin-bottom:15px; color:white; font-size:15px; margin-top:15px; padding-top:8px; padding-bottom:8px;"
                                                            class="btn btn-theme btn-sm margin-bottom-20 common_calculate"
                                                            type="submit" value="Calculate">Calculate</button>
                                                    </div>
                                                </li> --}}
                                                    <p
                                                        style="padding:14px; text-align:justify; font-size:12px; margin-top:12px">
                                                        *This calculation is an estimate only. We’ve estimated your taxes
                                                        based on your provided ZIP code. Title, other fees, and incentives
                                                        are not included. Monthly payment estimates are for informational
                                                        purposes and do not represent a financing offer from the seller of
                                                        this vehicle. Other taxes may apply.</p>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- price history start here -->
                    @if (!$inventory->mainPriceHistory->isEmpty())
                        <div class="card">
                            <div class="card-body item-user">
                                <h2 class="mb-4"><strong>Price History</strong></h2>
                                        <p class="mt-3 mb-0"> Listed <strong>{{ (int)\Carbon\Carbon::parse($inventory->created_date)->diffInDays() }} Days ago</strong></p>
                                @php
                                    $totalPriceReduction = 0;
                                    $totalPriceChange = 0;
                                @endphp

                                <p class="mt-0"><strong>
                                        @foreach ($inventory->mainPriceHistory as $history)
                                            @if (strpos($history->change_amount, '+') !== false || strpos($history->change_amount, '-') !== false)
                                                @php
                                                    $changeAmount = (float) str_replace(
                                                        ['$', ','],
                                                        '',
                                                        $history->change_amount,
                                                    );
                                                    $totalPriceChange += $changeAmount;
                                                @endphp
                                            @endif
                                        @endforeach
                                        @php
                                            $displayAmount = $totalPriceChange == (int)$totalPriceChange
                                                ? number_format(abs($totalPriceChange), 0)
                                                : number_format(abs($totalPriceChange), 2);
                                        @endphp

                                        @if ($totalPriceChange < 0)
                                            ${{ $displayAmount }} total price reduction
                                        @elseif($totalPriceChange > 0)
                                            ${{ $displayAmount }} total price increase
                                        @else
                                            No price change
                                        @endif
                                    </strong></p>
                                <div style="border:1px solid black">
                                    <canvas id="myChart"></canvas>
                                </div>
                                <div class="row p-0">
                                    <table class="table">
                                        @foreach ($inventory->mainPriceHistory as $history)
                                            @if ($loop->last)
                                                <!-- Check if this is the last iteration -->
                                                <tr>
                                                    <th>{{ \Carbon\Carbon::parse($history->change_date)->format('m/d/Y') }}
                                                    </th>
                                                    <th>{{ $history->change_amount }}</th>
                                                    <th>${{ number_format($history->amount, 0) }}</th>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($history->change_date)->format('m/d/Y') }}
                                                    </td>
                                                    <td>{{ $history->change_amount }}</td>
                                                    <td>${{ number_format($history->amount, 0) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table>
                                    <p class="text-muted" style="font-size:12px"><small>Total price amount is based on
                                            price change information provided by the seller</small></p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- price history end here -->

                    <style>
                        /* Favorite Heart Icon */
                        .favorite-icon {
                            position: absolute;
                            top: 10px;
                            right: 10px;
                            background: rgba(255, 255, 255, 0.8);
                            border-radius: 50%;
                            padding: 8px;
                            cursor: pointer;
                            z-index: 10;
                        }

                        /* Card Image */
                        .card-img-top {
                            height: 180px;
                            object-fit: cover;
                        }

                        .price {
                            font-weight: bold;
                            font-size: 1.2rem;
                        }

                        .mileage,
                        .location {
                            font-size: 0.9rem;
                            color: gray;
                        }

                        /* Adjust card width for fewer than 4 cards */
                        .fewer-cards .col-md-3 {
                            flex: 0 0 50%;
                            /* Show 2 cards per row */
                            max-width: 50%;
                        }

                        @media (max-width: 768px) {
                            .fewer-cards .col-md-3 {
                                flex: 0 0 100%;
                                /* Show 1 card per row on smaller screens */
                                max-width: 100%;
                            }
                        }
                    </style>
                    @if ($relateds->count() > 1)
                        <section class="d-block d-md-none">
                            <h2 class="mb-5 mt-6">Recommended For You</h2>
                            <!--Related Posts-->
                            <div id="myCarousel5" class="owl-carousel owl-carousel-icons3">
                                @forelse ($relateds as $item)
                                    <div class="item">
                                        <div style="background: rgb(255, 255, 255);
                                background: linear-gradient(0deg, rgb(232, 245, 243) 0%, rgb(255, 255, 255) 100%);"
                                            class="card mb-0">
                                            <div class="power-ribbon power-ribbon-top-left text-warning">
                                                <span class="bg-warning"><i class="fa fa-bolt"></i></span>
                                            </div>

                                            @php
                                                $image_obj = $item->additionalInventory->local_img_url;
                                                $image_str = str_replace(['[', ' ', "'", ']'], '', $image_obj); // Clean up the string
                                                $images = explode(',', $image_str); // Split into array
                                                $images_count = count(array_filter($images)); // Count non-empty images
                                                $not_found_image = 'frontend/NotFound.png'; // Path to the "not found" image
                                                $vin_string_replace = str_replace(' ', '', $item->vin);
                                                $route_string = str_replace(
                                                    ' ',
                                                    '',
                                                    $item->year .
                                                        '-' .
                                                        $item->make .
                                                        '-' .
                                                        $item->model .
                                                        '-in-' .
                                                        $item->dealer->city .
                                                        '-' .
                                                        $item->dealer->state,
                                                );
                                            @endphp

                                            <div class="item-card2-img">
                                                @if ($images_count > 1)
                                                    <a class="link"
                                                        href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"></a>
                                                    <img src="{{ asset($images[0]) }}" alt="img"
                                                        class="auto-ajax-photo-details" loading="lazy"
                                                        onerror="this.onerror=null; this.src='{{ asset($not_found_image) }}';">
                                                @else
                                                    <a class="link"
                                                        href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"></a>
                                                    <img src="{{ asset($not_found_image) }}" width="100%"
                                                        alt="img" class="auto-ajax-photo-details-not-found">
                                                @endif
                                            </div>


                                            <div class="item-card2-icons">
                                                @php
                                                    $countWishList = 0;
                                                    $favourites = session()->get('favourite', []); // Ensure default is an empty array

                                                    foreach ($favourites as $favorite) {
                                                        if (isset($favorite['id']) && $favorite['id'] == $item->id) {
                                                            $countWishList = 1;
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                <div style="margin-top:-20px; margin-left:28px" class="item-card9-icons">
                                                    <a href="javascript:void(0);" class="item-card9-icons1 "
                                                        data-productid="{{ $item->id }}">
                                                        @if ($countWishList > 0)
                                                            <i class="fa fa-heart" style="color: red"></i>
                                                        @else
                                                            <i class="fa fa-heart-o"></i>
                                                        @endif
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body pb-4">
                                                <div class="item-card2">
                                                    <div class="item-card2-desc">
                                                        <div class="item-card2-text">
                                                            @php
                                                                $title = Str::limit($item->title, 25, '...');
                                                            @endphp
                                                            <a href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"
                                                                class="text-dark">
                                                                <h4 style="font-weight:600" class="mb-0"
                                                                    title="{{ $item->title }}">
                                                                    {{ $title }}
                                                                </h4>
                                                            </a>
                                                        </div>
                                                        <div class="item-card9-desc mb-0 mt-1">
                                                            @php
                                                                $transmission = substr(
                                                                    $item->formatted_transmission,
                                                                    0,
                                                                    25,
                                                                );
                                                            @endphp
                                                            <p href="javascript:void(0);"
                                                                class="me-4 d-inline-block mb-0"><span class="">
                                                                    {{ $transmission }}</span></p>
                                                            <p class="mb-1" style="margin:0">
                                                                @if (in_array($item->type, ['Preowned', 'Certified Preowned']))
                                                                    Used
                                                                @else
                                                                    {{ $item->type }}
                                                                @endif
                                                            </p>
                                                            {{-- <a href="javascript:void(0);" class="me-4 d-inline-block"><span
                                                        class=""><i class="fa fa-calendar-o text-muted me-1"></i>
                                                        {{($inventory->created_at)->diffForHumans()}}</span></a> --}}
                                                        </div>
                                                        <div style="height: 25px" class="d-flex mb-1">
                                                            <h4 class="me-3" style="font-weight:600">
                                                                {{ $item->price_formate }}
                                                            </h4>

                                                            @php
                                                                // Parse the car price
                                                                $price_cus_amount = (int) str_replace(
                                                                    ['$', ','],
                                                                    '',
                                                                    $item->price_formate,
                                                                ); // Car price: 11995

                                                                // Calculate sales tax (8.5%)
                                                                $price_sales_tax = $price_cus_amount * (8.5 / 100); // 8.5% sales tax

                                                                // Total loan amount (principal)
                                                                $total_loan_amount =
                                                                    $price_cus_amount + $price_sales_tax;

                                                                // Annual APR (9%) converted to monthly rate
                                                                $monthly_interest_rate = 9 / 100 / 12;

                                                                // Number of months (72 months)
                                                                $loan_term_months = 72;

                                                                // Calculate monthly payment using amortization formula
                                                                $cus_monthly_payment =
                                                                    ($total_loan_amount *
                                                                        $monthly_interest_rate *
                                                                        pow(
                                                                            1 + $monthly_interest_rate,
                                                                            $loan_term_months,
                                                                        )) /
                                                                    (pow(
                                                                        1 + $monthly_interest_rate,
                                                                        $loan_term_months,
                                                                    ) -
                                                                        1);

                                                                // Output debug information

                                                            @endphp


                                                            <p
                                                                style="color:black; font-weight:600; font-size:12px; margin-top:2px">
                                                                ${{ floor($cus_monthly_payment) }}/mo*</p>
                                                        </div>
                                                        <div>
                                                            <p class="d-flex mb-1">
                                                                <img class="me-1"
                                                                    style="width:21px; height:21px; margin-top:0px"
                                                                    src="{{ asset('/frontend/assets/images/miles.png') }}" />
                                                                {{ number_format($item->miles) != 0 ? number_format($item->miles) . ' miles' : 'TBD' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer pe-4 ps-4 pt-4 pb-4">
                                                <div class="item-card9-footer d-flex">
                                                    <i style="color:black !important"
                                                        class="fa fa-map-marker text-muted me-1"></i>
                                                    @php
                                                        $cus_dealer = explode(' in ', $item->dealer->name)[0];
                                                        $nameLength = strlen($cus_dealer);
                                                        $name = Str::substr($cus_dealer, 0, 25);
                                                    @endphp
                                                    @if ($nameLength <= '25')
                                                        <h5 class="dealer-add" style="color:black">{{ $cus_dealer }}
                                                            <br>
                                                            <span style="font-size:14px">{{ $inventory->dealer->city }},
                                                                {{ strtoupper($inventory->dealer->state) }}
                                                                {{ $item->zip_code }}

                                                                @if (isset($item->distance) && $item->distance > 0.9)
                                                                    <span>
                                                                        ({{ round($item->distance, 0) }} mi. away)
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </h5>
                                                    @else
                                                        <h5 title="{{ $cus_dealer }}" class="dealer-add"
                                                            style="color:black">
                                                            {{ $name }}...
                                                            <br>
                                                            <span style="font-size:14px">{{ $inventory->dealer->city }},
                                                                {{ strtoupper($inventory->dealer->state) }}
                                                                {{ $item->zip_code }}

                                                                @if (isset($item->distance) && $item->distance > 0.9)
                                                                    <span>
                                                                        ({{ round($item->distance, 0) }} mi. away)
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </h5>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-card">
                                        <div style="background: #f5f5f5; padding: 20px; text-align: center;"
                                            class="card mb-0">
                                            <h4 style="color: #999;">No related items found</h4>
                                            <p style="color: #666;">Please check back later or explore other categories.
                                            </p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    @endif
                    <!-- marif calculator end here  -->
                </div>
                <!--/Right Side Content-->
            </div>




            <!-- part 02 -->

            <!-- e ieroikwer oiwer yhtoi toihuwoirt oiroituqe oituqi qwirutoi utoiqwjuoit oiqwutiu woituwoitu oirtuoiwuti qwi  -->
            <style>
                /* Favorite Heart Icon */
                .favorite-icon {
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    background: rgba(255, 255, 255, 0.8);
                    border-radius: 50%;
                    padding: 8px;
                    cursor: pointer;
                    z-index: 10;
                }

                /* Card Image */
                .card-img-top {
                    height: 180px;
                    object-fit: cover;
                }

                .price {
                    font-weight: bold;
                    font-size: 1.2rem;
                }

                .mileage,
                .location {
                    font-size: 0.9rem;
                    color: gray;
                }
            </style>

            <!-- oiwerthoiwtjhoi 098ietywuit uoirthuiet uieryhtui etuyherui h  -->


        </div>

        <section>
            @if ($other_vehicles->count() > 3)
                <div class="container py-5 hide-on-phone">
                    <h2>Other Vehicles From This Seller</h2>
                    <div id="cardSlider" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <!-- Dynamic Slide Generation -->
                            @foreach ($other_vehicles->chunk(4) as $chunk)
                                <div class="carousel-item @if ($loop->first) active @endif">
                                    <div class="row">
                                        <!-- Card Loop -->
                                        @foreach ($chunk as $carData)
                                            @php
                                                $carDataCity = $carData->dealer->city;
                                                $carDataState = $carData->dealer->state;

                                                $image_obj = $carData->additionalInventory->local_img_url;
                                                $image_splice = explode(',', $image_obj);
                                                $image = str_replace(['[', ' ', "'", ']'], '', $image_splice[0]);

                                                $vin_string_replace = str_replace(' ', '', $carData->vin);
                                                $route_string = str_replace(
                                                    ' ',
                                                    '',
                                                    $carData->year .
                                                        '-' .
                                                        $carData->make .
                                                        '-' .
                                                        $carData->model .
                                                        '-in-' .
                                                        $carData->dealer->city .
                                                        '-' .
                                                        strtoupper($carData->dealer->state),
                                                );
                                                $title = Str::limit($carData->title, 25, '...');
                                            @endphp

                                            @php
                                                $carDataCity = $carData->dealer->city;
                                                $carDataState = $carData->dealer->state;

                                                $image_obj = $carData->additionalInventory->local_img_url;
                                                $image_str = str_replace(['[', ' ', "'", ']'], '', $image_obj); // Clean image string
                                                $images = explode(',', $image_str); // Split into array
                                                $images_count = count(array_filter($images)); // Count non-empty images

                                                $vin_string_replace = str_replace(' ', '', $carData->vin);
                                                $route_string = str_replace(
                                                    ' ',
                                                    '',
                                                    $carData->year .
                                                        '-' .
                                                        $carData->make .
                                                        '-' .
                                                        $carData->model .
                                                        '-in-' .
                                                        $carData->dealer->city .
                                                        '-' .
                                                        strtoupper($carData->dealer->state),
                                                );
                                                $title = Str::limit($carData->title, 25, '...');
                                            @endphp
                                            <div class="col-md-3">
                                                <div class="card position-relative shadow-sm">
                                                    <div class="favorite-icon">
                                                        <i class="fas fa-heart"></i>
                                                    </div>
                                                    <a
                                                        href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}">
                                                        @if ($images_count > 1)
                                                            <img src="{{ asset($images[0]) }}" class="card-img-top"
                                                                alt="Car Image" loading="lazy"
                                                                onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';">
                                                        @else
                                                            <img src="{{ asset('frontend/NotFound.png') }}"
                                                                class="card-img-top" alt="Not Found Image"
                                                                loading="lazy">
                                                        @endif
                                                    </a>
                                                    <div class="card-body">
                                                        <small>
                                                            @if (in_array($carData->type, ['Preowned', 'Certified Preowned']))
                                                                Used
                                                            @else
                                                                {{ $carData->type }}
                                                            @endif
                                                        </small>
                                                        <a
                                                            href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}">
                                                            <h6 class="card-title">{{ $title }}</h6>
                                                        </a>
                                                        <p class="price">{{ $carData->price_formate }}</p>
                                                        <p class="mileage">
                                                            {{ number_format($carData->miles) . ' miles' }}</p>
                                                        <p class="location">{{ $carDataCity }}, {{ $carDataState }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Carousel Controls -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#cardSlider"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#cardSlider"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            @endif
        </section>
    </section>
    {{-- mobile filter modal start --}}

    <div class="modal fade" id="MobileFilterModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
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
                            <form id="SendLeadweb" style="background-color: #d6d6d6">
                                @csrf
                                <div class="">
                                    <div class="row p-2">
                                        <div class="col-md-6 col-sm-6 col-xs-12 " style="margin-top:20px">
                                            <div class="form-group ">
                                                <input type="hidden" id="inventory_id" name="inventories_id"
                                                    value="{{ $inventory->id }}">

                                                <input style="border-radius:5px; color:black" placeholder="First Name*"
                                                    class="form-control fname" type="text" name="first_name"
                                                    value="{{ $user ? $user->fname : old('first_name') }}">
                                                <span id="f_name_error" class="text-danger" role="alert"></span>

                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 second-name" style="margin-top:20px">
                                            <div class="form-group ">
                                                <input style="border-radius:5px; color:black" placeholder="Last Name*"
                                                    class="form-control lname" type="text" name="last_name"
                                                    value="{{ $user ? $user->lname : old('last_name') }}">
                                                <span id="l_name_error" class="text-danger" role="alert"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                                            <div class="form-group ">
                                                <input style="border-radius:5px; color:black" placeholder="E-mail*"
                                                    class="form-control email" type="text" name="email"
                                                    value="{{ $user ? $user->email : old('email') }}">
                                                <span id="e_error" class="text-danger" role="alert"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                                            <div class="form-group ">
                                                <input style="border-radius:5px; color:black"
                                                    class="form-control phone telephoneInput" type="text"
                                                    placeholder="cell" name="phone"
                                                    value="{{ $user ? $user->phone : old('phone') }}">
                                                <span id="p_error" class="text-danger" role="alert"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-xs-12 ">
                                            <div class="form-group ">
                                                <textarea style="border-radius:5px; color:black" id="w3review" class="form-control description"
                                                    name="description" rows="6" cols="55">I am interested and want to know more about the {{ $inventory->title }}, you have listed for {{ $inventory->price_formate }}  on Best Dream car.
                                                            </textarea>

                                                <span id="description_error" class="text-danger"
                                                    role="alert"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-xs-12 ">
                                            <div class="form-group ">
                                                <p
                                                    style="color:rgb(168, 11, 155); font-weight:bold; margin-bottom:15px; margin-top:10px">
                                                    <span class="text-danger">*</span> Security Question (Enter the
                                                    Correct
                                                    answer)
                                                </p>


                                                <div style="display: flex">
                                                    <div id="captchaLabelDetailsMob"
                                                        style="background-color:white; width:50%;  margin-right:10px; text-align:center; padding-top:7px; font-weight:600;  margin-top:2px; margin-left:3px; height:35px; border-radius:5px; border:1px solid rgb(189, 188, 188)">
                                                        {{ app('mathcaptcha')->label() }}
                                                    </div>
                                                    <div>
                                                        <input id="captchaInput"
                                                            class="form-control @error('mathcaptcha') is-invalid @enderror"
                                                            type="text" name="mathcaptcha"
                                                            placeholder="Enter your result">
                                                        <span id="Mmathcaptcha" class="text-danger" role="alert">
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
                                                        id="tradeCheckedModal" style="cursor: pointer"> <label
                                                        for="tradeCheckedModal" style="cursor: pointer"> Do you have a
                                                        trade-in?</label></p>
                                            </div>
                                        </div>
                                        <div class="row p-0 m-0"
                                            style="margin-left: 0px; margin-right:0px; display:none"
                                            id="Auto_Trade_block_content">
                                            <div class="row p-0 m-0">
                                                <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                    <div class="form-group ">
                                                        <input style="border-radius:5px" placeholder="Year*"
                                                            class="form-control year_trade" type="text"
                                                            name="year" value="">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                    <div class="form-group ">
                                                        <input style="border-radius:5px" placeholder="Make*"
                                                            class="form-control make_trade" type="text"
                                                            name="make" value="">
                                                        <span class="invalid-feedback7 text-danger" role="alert">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                    <div class="form-group ">
                                                        <input style="border-radius:5px" placeholder="Model*"
                                                            class="form-control model_trade" type="text"
                                                            name="model" value="">
                                                        <span class="invalid-feedback8 text-danger"
                                                            role="alert"></span>
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
                                                style="background: darkcyan; width:100%; margin-bottom:15px; color:white; font-size:16px;"
                                                type="submit" class="btn leadLoading">Send Message</button>

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
    {{-- mobile filter modal close --}}

    {{-- mobile device filter button start --}}
    <div class="filter-btn-styel-auto">
        <div class="filter-all-btn">
            <button style="" type="button" class="btn  filter-option-details" data-bs-toggle="modal"
                data-bs-target="#MobileFilterModal">
                Check Availability
            </button>
        </div>
    </div>
    {{-- mobile device filter button close --}}
    <!--/listing-->
    {{-- image slider modal start --}}
    <div class="modal fade" id="imageOpenModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header mobile-slider-view">
                    <h5 class="modal-title" id="exampleModalLabel">Listing Preview</h5>
                    <button
                        style="position: absolute; top:15px; right:10px; background-color: white; z-index:9; padding:10px; border-radius:50%; color:black; font-size:11px"
                        type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>
                <div style="position:relative; padding:0 !important; margin:0 !important;" class="modal-body">
                    <div style="--swiper-navigation-color: #ffffff; --swiper-pagination-color: #fff"
                        class="swiper deatailSwiper2">
                        <button
                            style="position: absolute; top:15px; right:10px; background-color: white; z-index:9; padding:10px; border-radius:50%; color:black; font-size:11px"
                            type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="swiper-wrapper">
                            @php
                                $image_obj = $inventory->additionalInventory->local_img_url;
                                $image_str = str_replace(['[', ' ', "'", ']'], '', $image_obj);
                                $images = explode(',', $image_str);

                                $images_count = count(array_filter($images)); // Count non-empty images
                                $not_found_image = 'frontend/NotFound.png'; // Path to the "not found" image

                                if ($images_count > 1) {
                                    $image = $images[0];
                                } else {
                                    $image = $not_found_image;
                                }
                            @endphp

                            @foreach ($images as $image)
                                <div class="swiper-slide">
                                    @if ($image_obj != '' && $image_obj != '[]')
                                        <img style="width:100% !important; margin:0 !important; height:600px"
                                            class="br-3" src="{{ asset($image) }}"
                                            alt="Used cars for sale {{ $inventory->title ?? '' }}, price is {{ $inventory->price ?? '' }}, vin {{ $inventory->vin ?? '' }} in {{ $inventory->dealer->city ?? '' }}, {{ strtoupper($inventory->dealer->state ?? '') }}, dealer name is {{ $inventory->dealer->name ?? '' }} Best Dream car image"
                                            loading="lazy"
                                            onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}'; this.alt='Vehicle image not available';">
                                    @elseif ($image_obj == '[]')
                                        <img class="auto-ajax-photo" width="100%"
                                            src="{{ asset('frontend/NotFound.png') }}"
                                            alt="Vehicle image not available">
                                    @else
                                        <img class="auto-ajax-photo" width="100%"
                                            src="{{ asset('frontend/NotFound.png') }}"
                                            alt="Vehicle image not available">
                                    @endif

                                </div>
                            @endforeach
                        </div>
                        <!-- Navigation buttons for the main slider -->
                        <div style="background: rgb(250, 249, 249); color:black;  width:30px; right:0 !important; border-top-left-radius:5px; border-bottom-left-radius:5px; height:80px"
                            class="swiper-button-next"><i style="font-size:22px; font-weight:700"
                                class="fa fa-angle-right"></i></div>
                        <div style="background: rgb(250, 249, 249); color:black;  width:30px; left:0 !important; border-top-right-radius:5px; border-bottom-right-radius:5px; height:80px"
                            class="swiper-button-prev"><i style="font-size:22px; font-weight:700"
                                class="fa fa-angle-left"></i></div>
                    </div>

                    <!-- Swiper for thumbnails -->
                    <div style="width:97%; margin:0 auto" thumbsSlider="" class="swiper deatailSwiper mt-1">
                        <div class="swiper-wrapper">
                            @php
                                $image_obj = $inventory->additionalInventory->local_img_url;
                                $image_str = str_replace(['[', ' ', "'", ']'], '', $image_obj);
                                $images = explode(',', $image_str);

                                $images_count = count(array_filter($images));
                                $not_found_image = 'frontend/NotFound.png'; // Path to the "not found" image
                                $image = $images_count > 1 ? $images[0] : $not_found_image;
                            @endphp

                            @foreach ($images as $image)
                                <div class="swiper-slide">

                                    @if ($image_obj != '' && $image_obj != '[]')
                                        <img class="me-2 br-3 swipper-bottom-image" src="{{ asset($image) }}"
                                            alt="Used cars for sale {{ $inventory->title ?? '' }}, price is {{ $inventory->price ?? '' }}, vin {{ $inventory->vin ?? '' }} in {{ $inventory->dealer->city ?? '' }}, {{ strtoupper($inventory->dealer->state ?? '') }}, dealer name is {{ $inventory->dealer->name ?? '' }} Best Dream car image"
                                            loading="lazy"
                                            onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}'; this.alt='Vehicle image not available';">
                                    @elseif ($image_obj == '[]')
                                        <img class="auto-ajax-photo" width="100%"
                                            src="{{ asset('frontend/NotFound.png') }}"
                                            alt="Vehicle image not available">
                                    @else
                                        <img class="auto-ajax-photo" width="100%"
                                            src="{{ asset('frontend/NotFound.png') }}"
                                            alt="Vehicle image not available">
                                    @endif

                                </div>
                            @endforeach
                        </div>
                        <!-- Navigation buttons for the thumbnails -->
                        <div style="background: white; color:black" class="swiper-button-next"><i
                                class="fa fa-angle-right"></i></div>
                        <div style="background: white; color:black" class="swiper-button-prev"><i
                                class="fa fa-angle-left"></i></div>
                    </div>

                    {{-- Repeating div --}}

                    <div class="repate">
                        <div class="row">
                            <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="">
                                    <div class="col-md-12 col-xs-12 col-sm-12">
                                        <!-- Ad Box -->
                                        <div class="category-grid-box mt-3">
                                            <!-- Ad Img -->
                                            <div class="ad-archive-img">
                                                @php
                                                    $image_obj = $inventory->additionalInventory->local_img_url;
                                                    $image_str = str_replace(['[', ' ', "'", ']'], '', $image_obj);
                                                    $images = explode(',', $image_str);
                                                @endphp
                                                @foreach ($images as $image)
                                                    <div class="swiper-slide">
                                                        <img src="{{ asset($image) }}"
                                                            alt="Used cars for sale {{ $inventory->title ?? '' }}, price is {{ $inventory->price ?? '' }}, vin {{ $inventory->vin ?? '' }} in {{ $inventory->dealer->city ?? '' }}, {{ strtoupper($inventory->dealer->state ?? '') }}, dealer name is {{ $inventory->dealer->name ?? '' }} Best Dream car image"
                                                            class="mini-image mb-3 rounded"
                                                            onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}'; this.alt='Vehicle image not available';">
                                                    </div>
                                                @endforeach
                                            </div>
                                            <!-- Ad Img End -->

                                            <!-- Addition Info -->

                                        </div>
                                        <!-- Ad Box End -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
    {{-- image slider modal end --}}
@endsection

@push('js')
    <script>
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            spaceBetween: 6,
            slidesPerView: 6,
            freeMode: true,
            watchSlidesProgress: true,
        });
        var swiper2 = new Swiper(".mySwiper2", {
            loop: true,
            spaceBetween: 10,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            thumbs: {
                swiper: swiper,
            },
        });
    </script>
    <script>
        var swiper = new Swiper(".deatailSwiper", {
            spaceBetween: 4,
            slidesPerView: 6,
            freeMode: true,
            watchSlidesProgress: true,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
        var swiper2 = new Swiper(".deatailSwiper2", {
            spaceBetween: 10,

            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            thumbs: {
                swiper: swiper,
            },
        });
    </script>


    <script>
        $(document).ready(function() {
            refreshCaptcha();
            arpRateDropDown();
            // exclusive_calculator();
        });
        // document.addEventListener('DOMContentLoaded', function() {
        //     var inputs = document.querySelectorAll('.calculate_month');
        //     var fourthInput = document.getElementById('72');

        //     inputs.forEach(function(input) {
        //         input.addEventListener('click', function() {
        //             if (input !== fourthInput) {
        //                 fourthInput.setAttribute('readonly', true);
        //                 fourthInput.classList.remove('active');
        //             }
        //             this.removeAttribute('readonly');
        //             this.classList.add('active');
        //             this.focus();
        //         });

        //         input.addEventListener('blur', function() {
        //             if (this !== fourthInput) {
        //                 this.setAttribute('readonly', true);
        //                 this.classList.remove('active');
        //             }
        //         });
        //     });
        // });

        function arpRateDropDown() {
            $.ajax({
                url: "{{ route('arp.ajax') }}",
                type: 'GET',
                success: function(resp) {
                    let dropdown = $('#credit_calculator');
                    // let mobiledropdown = $('#mobileArpRateDropdown');
                    dropdown.empty(); // Clear previous options

                    // Populate dropdown with options
                    $.each(resp, function(key, value) {
                        dropdown.append(`<option value="${value}">${key}</option>`);
                        // mobiledropdown.append(`<option value="${value}">${key}</option>`);
                    });
                    console.log(resp);
                    // alert(resp);
                },
                error: function(xhr) {

                }
            })

        }

        function truncateText() {
            var content = document.getElementById("text_data");
            var button = document.getElementById("show-more-button");

            if (content.style.display === "none") {
                content.style.display = "block";
                button.innerHTML = "Show Less";
            } else {
                content.style.display = "none";
                button.innerHTML = "Show More";
            }
        }
        //   copy link code start
        document.getElementById('copyUrlButton').addEventListener('click', function() {
            // Get the current URL from the browser's address bar
            const currentUrl = window.location.href;

            // Create a temporary input element to hold the URL
            const tempInput = document.createElement('input');
            tempInput.value = currentUrl;
            document.body.appendChild(tempInput);

            // Select the input's value and copy it to the clipboard
            tempInput.select();
            document.execCommand('copy');

            // Remove the temporary input element
            document.body.removeChild(tempInput);

            // Display a message to indicate that the URL has been copied
            Swal.fire({
                icon: 'success',
                title: 'URL Copied!',
                text: 'The URL has been copied to your clipboard.',
                showConfirmButton: false,
                timer: 1500,
                background: '#f4f6f7',
                customClass: {
                    popup: 'animated bounceIn'
                }
            });
        });
        //   copy link code close


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $(document).on('change', '#tradeChecked', function() {
            var isChecked = this.checked;
            if (isChecked == true) {
                $('#Trade_block_content').css('display', 'block');
            } else {
                $('#Trade_block_content').css('display', 'none');
            }
        });
        $(document).on('change', '#tradeCheckedModal', function() {
            var isChecked = this.checked;
            if (isChecked == true) {
                $('#Auto_Trade_block_content').css('display', 'block');
            } else {
                $('#Auto_Trade_block_content').css('display', 'none');
            }
        });
        // mobile device
        $(document).on('change', '#tradeChecked', function() {
            var isChecked = this.checked;
            if (isChecked == true) {
                $('#Mobile_Trade_block_content').css('display', 'block');
            } else {
                $('#Mobile_Trade_block_content').css('display', 'none');
            }
        });

        $(document).ready(function() {
            $('.telephoneInput').inputmask('(999) 999-9999');
            $('#SendLeaddetails').on('submit', function(e) {
                e.preventDefault();

                // Serialize the form data
                var formData = new FormData($(this)[0]);
                $('.loading').text('Loading....');

                $.ajax({
                    url: "{{ route('lead.send') }}",
                    type: 'post',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {

                        console.log(response);
                        $('#SendLeaddetails')[0].reset();
                        toastr.success(response.message);
                        $('#first_name').val(null);
                        $('#last_name').val(null);
                        $('#email').val(null);
                        $('#phone').val(null);
                        $('.loading').text('Send Message');
                        $('#first_name_error').html('');
                        $('#last_name_error').html('');
                        $('#email_error').html('');
                        $('#phone_error').html('');
                        $('#Wmathcaptcha').html('');
                        refreshCaptcha();

                    },
                    error: function(xhr) {

                        $('.loading').text('Send Message');
                        var errors = xhr.responseJSON.errors;
                        if (errors) {
                            $('#first_name_error').html(errors.first_name);
                            $('#last_name_error').html(errors.last_name);
                            $('#email_error').html(errors.email);
                            $('#phone_error').html(errors.phone);
                            $('#Wmathcaptcha').html(errors.mathcaptcha);
                        }
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#SendLeadweb').on('submit', function(e) {
                e.preventDefault();

                // Serialize the form data
                var formData = new FormData($(this)[0]);
                $('.leadLoading').text('Loading....');



                $.ajax({
                    url: "{{ route('lead.send') }}",
                    type: 'post',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Handle success response
                        console.log(response);
                        $('#SendLeadweb')[0].reset();
                        toastr.success(response.message);
                        $('#MobileFilterModal').modal('hide');
                        $('.leadLoading').text('Send Message');
                        refreshCaptcha();
                    },
                    error: function(xhr) {
                        // Handle error response
                        var errors = xhr.responseJSON.errors;
                        if (errors) {
                            $('#f_name_error').html(errors.first_name);
                            $('#l_name_error').html(errors.last_name);
                            $('#e_error').html(errors.email);
                            $('#p_error').html(errors.phone);
                            $('#Mmathcaptcha').html(errors.mathcaptcha);
                        }
                    }
                });
            });

        });
    </script>
    <script>
        $(document).ready(function() {

            $(document).on('input change click',
                '#credit_calculator, #price_calculator, #calculator_interest, #calculator_downpayment, #trade_in_value, .calculate_month.active',
                function() {
                    exclusive_calculator();
                });

            function exclusive_calculator() {
                var cal_interest_rate = $('#credit_calculator').val();
                $('#calculator_interest').val(cal_interest_rate);
                // alert(cal_interest_rate)
                // var cal_interest_rate;

                // // Set the correct interest rate based on credit score
                // if (cal_credit == 'rebuild') {
                //     cal_interest_rate = 18;
                //     $('#calculator_interest').val(18);
                // } else if (cal_credit == 'fair') {
                //     cal_interest_rate = 12;
                //     $('#calculator_interest').val(12);
                // } else if (cal_credit == 'good') {
                //     cal_interest_rate = 9;
                //     $('#calculator_interest').val(9);
                // } else if (cal_credit == 'excellent') {
                //     cal_interest_rate = 8;
                //     $('#calculator_interest').val(8);
                // }

                // Ensure all inputs are treated as numbers
                var price_calculator = parseFloat($('#price_calculator').val()) || 0;
                var price_down_pay_calculator = parseFloat($('#calculator_downpayment').val()) || 0;
                var price_trade_in_value_calculator = parseFloat($('#trade_in_value').val()) || 0;
                var price_calculator_month = parseInt($('.calculate_month.active').val()) || 0;
                var salesTaxRate = $('#sales_tax_rate').val(); // Assuming the sales tax is fixed at 8%

                // Step 1: Calculate total loan amount (P)
                const salesTax = price_calculator * (salesTaxRate / 100);
                const loanAmount = price_calculator + salesTax - (price_down_pay_calculator +
                    price_trade_in_value_calculator);

                // alert(price_trade_in_value_calculator)
                // Step 2: Calculate monthly interest rate (r)
                const monthlyInterestRate = (parseFloat(cal_interest_rate) / 100) / 12;

                // Step 3: Calculate the monthly payment (M)
                if (price_calculator_month > 0 && monthlyInterestRate > 0) {
                    const numerator = loanAmount * monthlyInterestRate * Math.pow(1 + monthlyInterestRate,
                        price_calculator_month);
                    const denominator = Math.pow(1 + monthlyInterestRate, price_calculator_month) - 1;
                    var monthlyPayment = numerator / denominator;
                } else {
                    var monthlyPayment = 0; // Prevent invalid calculations
                }

                // Display loan amount and monthly payment
                var calculator_loan_amount = price_calculator + salesTax - price_down_pay_calculator;
                var calculator_loan_amount_element = "<small>Total Loan Amount : " + numberWithCommas(
                    calculator_loan_amount.toFixed(2)) + "</small>";
                $('#sales_tax').val(salesTax.toFixed(0));
                $('#calculator_loan_amount').html(calculator_loan_amount_element);

                // $('#calculator_monthly_pay').html(monthlyPayment.toFixed(0));
                math_monthly_pay = Math.floor(monthlyPayment)
                $('#calculator_monthly_pay').html(math_monthly_pay);
                $('#mobile_monthly_pay').html('$' + math_monthly_pay + '/ mo*');
                $('#strong_monthly_pay').html('$' + math_monthly_pay + '/ mo*');
            }

            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        });
    </script>
    <script>
        // JavaScript to toggle the seller's notes visibility
        document.getElementById('viewMoreButton').addEventListener('click', function() {
            const sellerNotes = document.getElementById('sellerNotes');
            const isCollapsed = sellerNotes.style.maxHeight === '60px';

            if (isCollapsed) {
                // Expand the content
                sellerNotes.style.maxHeight = 'none';
                sellerNotes.style.overflow = 'visible';
                this.innerHTML =
                    '<strong class="hyperlink-title" style="border-bottom:1px solid black">View less seller\'s note</strong> <i class="fa fa-caret-up"></i>';
            } else {
                // Collapse the content
                sellerNotes.style.maxHeight = '60px';
                sellerNotes.style.overflow = 'hidden';
                this.innerHTML =
                    '<strong class="hyperlink-title" style="border-bottom:1px solid black">View all seller\'s note</strong> <i class="fa fa-caret-down"></i>';
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Format date to mm/dd
        const formatDate = (date) => {
            const d = new Date(date);
            const month = String(d.getMonth() + 1).padStart(2, '0'); // Month (0-indexed)
            const day = String(d.getDate()).padStart(2, '0'); // Day
            return `${month}/${day}`;
        };

        const priceHistory = @json($inventory->mainPriceHistory);
        console.log(priceHistory);

        // Extract labels (dates) and data (prices) from priceHistory
        const labels = priceHistory.map(item => formatDate(item.change_date)); // Get formatted dates
        const data = priceHistory.map(item => parseFloat(item.amount)); // Get prices (converted to numbers)

        const ctx = document.getElementById('myChart').getContext('2d');

        const myChart = new Chart(ctx, {
            type: 'line', // Line chart type
            data: {
                labels: labels, // X-axis labels (formatted dates)
                datasets: [{
                    label: 'Price over Time',
                    data: data, // Y-axis data (prices)
                    borderColor: 'rgba(75, 192, 192, 1)', // Line color
                    backgroundColor: 'rgba(75, 192, 192, 0.2)', // Line fill (optional)
                    borderWidth: 2, // Line width
                    tension: 0.4, // Curved lines
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date', // X-axis title
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Price ($)', // Y-axis title
                        },
                        beginAtZero: false, // Adjust to suit your data range
                        ticks: {
                            callback: function(value) {
                                return `$${value}`; // Format Y-axis labels as $amount
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                }
            }
        });
    </script>
    @include('frontend.reapted_js')
@endpush
