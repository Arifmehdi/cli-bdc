@extends('frontend.website.layout.app')

@section('title', $pageTitle ?? (app('globalSeo')['og_title'] ?? 'Used Cars for Sale | BestDreamCar.com'))
@section('meta_description',
    $metaDescription ??
    (app('globalSeo')['og_description'] ??
    'Find thousands of used cars for
    sale. Search by make, model, price, and location on BestDreamCar.com.'))
@section('og_title', $pageTitle ?? (app('globalSeo')['og_title'] ?? 'Used Cars for Sale | BestDreamCar.com'))
@section('og_description',
    $metaDescription ??
    (app('globalSeo')['og_description'] ??
    'Find thousands of used cars for
    sale on BestDreamCar.com.'))
@section('og_url', url()->full())

@push('head')
    <link rel="canonical" href="{{ url()->current() }}">
@endpush
<?php

use Illuminate\Support\Facades\Cookie;

$searchData = json_decode(request()->cookie('searchData'), true) ?? [];
// @dd($searchData);
$cookie_zipcode = request()->cookie('zipcode') ?? '';

// $searchData = Session::get('searchData', []);
$autoMobileTypeCheckbox = $searchData['autoMobileTypeCheckbox'] ?? [];
$autoWebConditionCheckbox = $searchData['autoWebConditionCheckbox'] ?? [];
$autoMobileTransmissionCheckbox = $searchData['autoMobileTransmissionCheckbox'] ?? [];
$autoWebTransmissionCheckbox = $searchData['autoWebTransmissionCheckbox'] ?? [];
$autoMobileFuelCheckbox = $searchData['autoMobileFuelCheckbox'] ?? [];
$autoWebDriveTrainCheckbox = $searchData['autoWebDriveTrainCheckbox'] ?? [];
$autoWebFuelCheckbox = $searchData['autoWebFuelCheckbox'] ?? [];
$mobileColorFilter = $searchData['mobileColorFilter'] ?? [];

$webBodyFilter = isset($searchData['webBodyFilter']) ? (array) $searchData['webBodyFilter'] : [];
$mobileBody = isset($searchData['mobileBody']) ? (array) $searchData['mobileBody'] : [];
$webMakeFilterMakeInput = $searchData['webMakeFilterMakeInput'] ?? '';
$webModelFilterInputByHome = $searchData['webModelFilterInput'] ?? '';
$selectedMake = $searchData['secondFilterMakeInputNew'] ?? '';

// $webInteriorColorFilter = $searchData['webInteriorColorFilter'] ?? [];
// $mobileInteriorColorFilter = $searchData['mobileInteriorColorFilter'] ?? [];

//Start Body type check function
if (!function_exists('getBodyChecks')) {
    function getBodyChecks(array $bodyTypes, array $selectedBodyTypes)
    {
        $checkedBodyType = [];
        foreach ($bodyTypes as $bodyType) {
            $checkedBodyType[$bodyType] = in_array($bodyType, $selectedBodyTypes) ? 'checked' : '';
        }
        return $checkedBodyType;
    }
}

//$bodyTypes = ['Full Size SUV', 'Sedan', 'Station Wagon', 'Coupe', 'Truck', 'Convertible', 'Minivan', 'Hatchback'];
// $bodyTypes = [
//     'Full Size SUV' => 'suv.png',
//     'Sedan' => 'sedan.png',
//     'Station Wagon' => 'wagon.png',
//     'Coupe' => 'coupe.png',
//     'Truck' => 'truck.png',
//     'Convertible' => 'convertible.png',
//     'Minivan' => 'minivan.png',
//     'Hatchback' => 'hatchback.png',
// ];
$bodyTypes = [
    'suv' => 'suv.png',
    'sedan' => 'sedan.png',
    'wagon' => 'wagon.png',
    'coupe' => 'coupe.png',
    'truck' => 'truck.png',
    'convertible' => 'convertible.png',
    'minivan' => 'minivan.png',
    'hatchback' => 'hatchback.png',
];
// Get web body checks and active body type
$bodyChecks = getBodyChecks($bodyTypes, (array) $webBodyFilter);
//dd($bodyChecks);
// Get mobile body checks and active body type
$mobileBodyChecks = getBodyChecks($bodyTypes, (array) $mobileBody);

//End Body type check function
// color code Start Here **********************************
if (!function_exists('getColorChecks')) {
    function getColorChecks(array $colors, array $selectedColors)
    {
        $checkedColor = [];
        foreach ($colors as $color) {
            $checkedColor[$color] = in_array($color, $selectedColors) ? 'checked' : '';
        }

        return $checkedColor;
    }
}
// $colors = ['red', 'blue', 'orange', 'green', 'black', 'white', 'violet', 'gray', 'pink', 'yellow'];
// $colorChecks = getColorChecks($colors, (array) $webColorFilter);
// $mobileColorChecks = getColorChecks($colors, (array) $mobileColorFilter);

// color code End Here **********************************

// drivetrain code Start here *****************************
if (!function_exists('getDriveTrainChecks')) {
    function getDriveTrainChecks(array $driveTrains, array $selectedDriveTrains)
    {
        $allItemsChecked =
            empty($selectedDriveTrains) ||
            !array_filter($driveTrains, function ($driveTrain) use ($selectedDriveTrains) {
                return in_array($driveTrain, $selectedDriveTrains);
            });
        $checkedStatuses = ['all' => $allItemsChecked ? 'checked' : ''];
        foreach ($driveTrains as $driveTrain) {
            $checkedStatuses[$driveTrain] = in_array($driveTrain, $selectedDriveTrains) ? 'checked' : '';
        }
        return $checkedStatuses;
    }
}

// $driveTrains = ['FWD', 'RWD', 'AWD', '4WD', '4x4', '4x2', '10x4', '10x6', '10x8', '12x4', '12x6', '14x4', '14x6', '6x2', '6x4', '6x6', '8x2', '8x4', '8x6', '8x8', '10x10'];
$driveTrains = ['4WD', 'AWD', 'FWD', 'RWD', 'Other'];
$webDriveTrainChecks = getDriveTrainChecks($driveTrains, (array) $autoWebDriveTrainCheckbox);
$mobileDriveTrainChecks = getDriveTrainChecks($driveTrains, (array) $autoWebDriveTrainCheckbox);
// drivetrain code End here ***********************8********

// color filter cookie start here  *****************************

// Define available colors
$exteriorColors = ['Beige', 'Black', 'Blue', 'Brown', 'Gold', 'Gray', 'Green', 'Orange', 'Pink', 'Purple', 'Red', 'Silver', 'White', 'Yellow', 'Other'];
$interiorColors = ['Beige', 'Black', 'Blue', 'Brown', 'Gold', 'Gray', 'Green', 'Orange', 'Pink', 'Purple', 'Red', 'Silver', 'White', 'Yellow', 'Other'];

// Get the color filters from searchData - use the correct keys that match your form
$webExteriorColorFilter = isset($searchData['autoWebExteriorColorCheckbox']) ? (array) $searchData['autoWebExteriorColorCheckbox'] : [];
$webInteriorColorFilter = isset($searchData['autoWebInteriorColorCheckbox']) ? (array) $searchData['autoWebInteriorColorCheckbox'] : [];

$mobileExteriorColorFilter = isset($searchData['autoMobileExteriorColorCheckbox']) ? (array) $searchData['autoMobileExteriorColorCheckbox'] : [];
$mobileInteriorColorFilter = isset($searchData['autoMobileInteriorColorCheckbox']) ? (array) $searchData['autoMobileInteriorColorCheckbox'] : [];

// Function to generate checked statuses
if (!function_exists('getColorChecks')) {
    function getColorChecks(array $colors, array $selectedColors)
    {
        // Check if 'all' colors are selected
        $allColorsChecked = empty($selectedColors);

        $checkedStatuses = ['all' => $allColorsChecked ? 'checked' : ''];

        foreach ($colors as $color) {
            $checkedStatuses[$color] = in_array($color, $selectedColors) ? 'checked' : '';
        }

        return $checkedStatuses;
    }
}

// Get checked statuses
$webExteriorColors = getColorChecks($exteriorColors, $webExteriorColorFilter);
$webInteriorColors = getColorChecks($interiorColors, $webInteriorColorFilter);

$mobileExteriorColors = getColorChecks($exteriorColors, $mobileExteriorColorFilter);
$mobileInteriorColors = getColorChecks($interiorColors, $mobileInteriorColorFilter);
// color filter cookie end  here  *****************************

// fuel code Start here
$fuelTypes = ['Diesel', 'Electric', 'Flex Fuel', 'Gasoline', 'Hybrid', 'Hydrogen Fuel Cell', 'Plug In Hybrid', 'Compressed Natural Gas', 'Other'];

// Get selected fuel types from search data
$webFuelTypeFilter = isset($searchData['autoWebFuelCheckbox']) ? (array) $searchData['autoWebFuelCheckbox'] : [];
$mobileFuelTypeFilter = isset($searchData['autoMobileFuelCheckbox']) ? (array) $searchData['autoMobileFuelCheckbox'] : [];

// Single function to handle both web and mobile checks
if (!function_exists('getFuelChecks')) {
    function getFuelChecks(array $allFuelTypes, array $selectedFuelTypes)
    {
        // Check if 'all' fuel types are selected
        $allChecked = empty($selectedFuelTypes) || count(array_intersect($allFuelTypes, $selectedFuelTypes)) === count($allFuelTypes);

        $checkedStatuses = ['all' => $allChecked ? 'checked' : ''];

        foreach ($allFuelTypes as $fuel) {
            $checkedStatuses[$fuel] = in_array($fuel, $selectedFuelTypes) ? 'checked' : '';
        }

        return $checkedStatuses;
    }
}

// Get checked statuses
$webFuelChecks = getFuelChecks($fuelTypes, $webFuelTypeFilter);
$mobileFuelChecks = getFuelChecks($fuelTypes, $mobileFuelTypeFilter);

// fuel code Start here  ****************************************

//condition code start here ********************
// vehicles_fuel code start here ****************************************
if (!function_exists('getConditionChecks')) {
    function getConditionChecks(array $conditions, array $selectedCondition)
    {
        $allConditionsChecked =
            empty($selectedCondition) ||
            !array_filter($conditions, function ($condition) use ($selectedCondition) {
                return in_array($condition, $selectedCondition);
            });
        $checkedStatuses = ['all' => $allConditionsChecked ? 'checked' : ''];
        foreach ($conditions as $condition) {
            $checkedStatuses[$condition] = in_array($condition, $selectedCondition) ? 'checked' : '';
        }

        return $checkedStatuses;
    }
}

$conditions = ['Certified', 'Used'];
$mobileConditionChecks = getFuelChecks($conditions, (array) $autoMobileTypeCheckbox);
$webConditionChecks = getFuelChecks($conditions, (array) $autoWebConditionCheckbox);
//condition code End here ********************
// Transmission code start here ****************************
if (!function_exists('getTransmissionChecks')) {
    function getTransmissionChecks(array $transmissions, array $selectedTransmissions)
    {
        $allTransmissionsChecked =
            empty($selectedTransmissions) ||
            !array_filter($transmissions, function ($transmission) use ($selectedTransmissions) {
                return in_array($transmission, $selectedTransmissions);
            });

        return [
            'all' => $allTransmissionsChecked ? 'checked' : '',
            'Automatic' => in_array('Automatic', $selectedTransmissions) ? 'checked' : '',
            'Manual' => in_array('Manual', $selectedTransmissions) ? 'checked' : '',
            'Variable' => in_array('Variable', $selectedTransmissions) ? 'checked' : '',
        ];
    }
}

$transmissions = ['Automatic', 'Manual', 'Variable', 'All'];
$mobileTransmissionChecks = getTransmissionChecks($transmissions, (array) $autoMobileTransmissionCheckbox);
$webTransmissionChecks = getTransmissionChecks($transmissions, (array) $autoWebTransmissionCheckbox);

$automaticChecked = $mobileTransmissionChecks['Automatic'];
$manualChecked = $mobileTransmissionChecks['Manual'];
$variableChecked = $mobileTransmissionChecks['Variable'];
$allChecked = $mobileTransmissionChecks['all'];

$webAutomaticChecked = $webTransmissionChecks['Automatic'];
$webManualChecked = $webTransmissionChecks['Manual'];
$webVariableChecked = $webTransmissionChecks['Variable'];
$webAllChecked = $webTransmissionChecks['all'];

// Transmission code End here **************************************

?>

@foreach (app('globalStaticPage') as $page)
    @if ($page->slug == 'autos')
        @if ($page->description)
            @section('meta_description', $page->description)
        @else
            @if ($page->description)
                @section('meta_description')
                    {{ $page->description }}
                @endsection
            @else
                @section('meta_description',
                    'Find the perfect car for your needs at ' .
                    route('home') .
                    ' Shop new and used
                    cars, sell your car, compare prices, and explore financing options to find your Best Dream car today!'
                    ??
                    app('globalSeo')['description'])
                @endif
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
@section('og_title')
    {{ $page->title }}
@endsection

@section('og_description')
    {{ $page->description }}
@endsection

{{-- @section('og_description', app('globalSeo')['og_description']) --}}

@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])

@section('twitter_title')
    {{ $page->title }}
@endsection

@section('twitter_description')
    {{ $page->description }}
@endsection

{{-- @section('twitter_description', app('globalSeo')['twitter_description']) --}}
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])

@push('css')
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/15.7.1.nouislider.css">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom.css') }}">
    {{-- @include('frontend.website.layout.css.auto_list_css') --}}
@endpush
@section('content')

    <!--Breadcrumb-->
    <div style="margin-top: 90px" class="mobile-auto-top">
        <div class="container">
            <div class="">

                <ol style="margin-top:32px" class="breadcrumb">
                    <li style="color:black !important" class="breadcrumb-item"><a style="color:black !important"
                            href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a style="color:black !important" href="{{ route('auto') }}">{{ $dealer_name }} Listing</a></li>

                    @if (request('homeBodySearch'))
                        <li class="breadcrumb-item active">
                            <a style="color:black !important"
                                href="{{ route('auto', ['homeBodySearch' => request('homeBodySearch')]) }}">{{ request('homeBodySearch') }}</a>
                        </li>
                    @endif
                    @if (request('makeTypeSearch'))
                        <li class="breadcrumb-item active">
                            <a style="color:black !important"
                                href="{{ route('auto', ['makeTypeSearch' => request('makeTypeSearch')]) }}">{{ request('makeTypeSearch') }}</a>
                        </li>
                    @endif
                    @if (request('homeMakeSearch'))
                        <li class="breadcrumb-item active">
                            <a style="color:black !important"
                                href="{{ route('auto', ['homeMakeSearch' => request('homeMakeSearch')]) }}">{{ request('homeMakeSearch') }}</a>
                        </li>
                    @endif
                    @if (request('homeModelSearch'))
                        <li class="breadcrumb-item active">
                            <a style="color:black !important"
                                href="{{ route('auto', ['homeMakeSearch' => request('homeMakeSearch'), 'homeModelSearch' => request('homeModelSearch')]) }}">{{ request('homeModelSearch') }}</a>
                        </li>
                    @endif
                    @if (request('homeModelSearch') == null &&
                            request('homeMakeSearch') == null &&
                            request('makeTypeSearch') == null &&
                            request('homeBodySearch') == null)
                        <li class="breadcrumb-item active">
                            <a style="color:black !important" href="{{ route('auto') }}">{{ 'Listing' }}</a>
                        </li>
                    @endif
                </ol>
                <h1 style="font-size: 1.5em; font-weight: 600; margin: 0;">{{ $h1Heading ?? 'Used Car Search Results' }}
                </h1>
            </div>
        </div>
    </div>
    <!--/Breadcrumb-->

    {{-- compare data alert modal start --}}
    <!-- Modal -->
    <div class="modal fade" id="ComModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div style="margin-top:-350px" class="modal-content">
                <div style="background:black; border:none;  box-shadow: none;" class="modal-header">

                    <div style="display:flex;">
                        <div style="width:50px; margin-right:15px">
                            <img id="comIcon" style="width:100%" />
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


    <!-- Button trigger modal start -->

    {{-- <section>
        <div class="container all-filter-button">
            <div style="margin-top:20px" class="btn-manage">
                <button
                    style="padding-left:8px; padding-right:8px; padding-top:1px; padding-bottom:1px; font-size:17px;  border-radius:10px; color:black; margin-right:3px; background:rgb(237, 220, 245)"
                    type="button" class="btn s-btn">
                    Location
                </button>
                <button
                    style="padding-left:8px; padding-right:8px; padding-top:1px; padding-bottom:1px; font-size:17px;  border-radius:10px; color:black; margin-right:3px; background:rgb(237, 220, 245)"
                    type="button" class="btn s-btn">
                    Price
                </button>
                <button
                    style="padding-left:8px; padding-right:8px; padding-top:1px; padding-bottom:1px; font-size:17px;  border-radius:10px; color:black; margin-right:3px; background:rgb(237, 220, 245)"
                    type="button" class="btn s-btn">
                    Mileage
                </button>
            </div>

        </div>
    </section> --}}
    <!-- Button trigger modal close -->

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
                        <div class="p-2 col-md-12">
                            <form id="SendLead" action="{{ route('lead.send') }}" method="post"
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
                                                    name="first_name"
                                                    value="{{ Auth()->check() ? Auth()->user()->fname : '' }}">
                                                <span id="first_name_error" class="text-danger" role="alert"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 second-name" style="margin-top:20px">
                                            <div class="form-group ">
                                                <input style="border-radius:5px; color:black" placeholder="Last Name*"
                                                    class="form-control lname" type="text" id="last_name"
                                                    name="last_name"
                                                    value="{{ Auth()->check() ? Auth()->user()->lname : '' }}">
                                                <span id="last_name_error" class="text-danger" role="alert"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                                            <div class="form-group ">
                                                <input style="border-radius:5px; color:black" placeholder="E-mail*"
                                                    class="form-control email" type="text" id="email"
                                                    name="email"
                                                    value="{{ Auth()->check() ? Auth()->user()->email : '' }}">
                                                <span id="email_error" class="text-danger" role="alert"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                                            <div class="form-group ">
                                                <input style="border-radius:5px; color:black"
                                                    class="form-control phone telephoneInput" type="text"
                                                    placeholder="cell" id="phone" name="phone"
                                                    value="{{ Auth()->check() ? Auth()->user()->phone : '' }}">

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
                                                        {{-- {{ app('mathcaptcha')->label() }} --}}
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
                                        </div>

                                        <div class="col-md-12 col-sm-12 col-xs-12 ">
                                            <div class="form-group ">
                                                <p style="color: black;cursor: pointer;"><input type="checkbox"
                                                        name="ask_trade" id="tradeChecked" style="cursor: pointer">
                                                    <label for="tradeChecked" style="cursor: pointer">Do you have a
                                                        trade-in?</label>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="p-0 m-0 row" style="margin-left: 0px; margin-right:0px; display:none"
                                            id="Auto_Trade_block_content">
                                            <div class="p-0 m-0 row">
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
                                                        name="isEmailSend" style="cursor: pointer" checked> Email me price
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
    <section>
        <div class="container auto-page-banner-image">
            <div class="auto-page-image" style="margin-top:40px; margin-left:20%">
                @php
                    $banners = \App\Models\Banner::where('position', 'auto page top')->first();
                @endphp

                @isset($banners->image)
                    <a href="{{ $banners->url ? $banners->url : '' }}"
                        {{ $banners->new_window == 1 ? 'target=_blank' : '' }}>
                        <img style="width:728px; height:90px;"
                            src="{{ asset('/dashboard/images/banners/' . $banners->image) }}"
                            alt="Used cars for sale dealer banner image dream best" />
                    </a>
                    @else
                        <img style="width:728px; height:90px;" src="{{ asset('/dashboard/images/banners/top.png') }}"
                            alt="Used cars for sale dealer banner image dream best" />
                    @endisset
                    <!-- Your ad content goes here -->
            </div>
        </div>
    </section>

    <!--listing-->
    <section style="padding-top:40px" class="all-autos">
        <div class="container">
            <div class="row mt-5">
                <!-- Add this section right below your search bar -->
                <!--Left Side Content-->
                <div class="col-xl-3 col-lg-3 col-md-12 auto-filter-sidebar">

                    <div class="card">
                        <div style="height:86px" class="card-body search-card">
                            <div class="input-group">
                                <input type="text" class="form-control br-ts-3 br-bs-3 common_selector"
                                    id="web_search_any" placeholder="Search Any">
                                <div class="">
                                    <button style="background:darkcyan; color:white" type="button"
                                        class="mb-3 btn br-ts-0 br-bs-0" id="webSearchBtn">
                                        Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div style="margin-top:1px;" class="d-flex justify-content-between">
                                <h4 class="fil-res">FILTER RESULT</h4>
                                <a style="margin-top:0px; color:darkcyan; font-size:15px; font-weight:400;cursor: pointer; padding-left:15px; 0;  text-decoration: underline;"
                                    data-url="{{ route('auto', ['clear' => 'flush']) }}" id="filter_usedCar_clear-web"
                                    class="cls">Clear ALL</a>

                            </div>

                            <!-- Confirmation Toggle -->
                            <div style="background:rgb(244, 245, 245); border:1px solid rgb(206, 206, 206); height:150px; border-radius:3px"
                                id="confirmationToggleWeb" class="mb-5 d-none open-close-all">

                                <div class="pt-2 pb-4 ps-3 pe-2">
                                    <p class="p-0 m-0" style="font-size:16px; font-weight:600">Are you sure?</p>
                                    <p style="font-size:14px;">This action clear all your filters.</p>
                                    <button
                                        style="float:right; background-color:rgb(189, 9, 9); color:white; font-size:13px; font-weight:500"
                                        data-url="{{ route('auto', ['clear' => 'flush']) }}" id="clearAllFilter"
                                        class="mb-5 btn me-1">Clear All</button>
                                    <button style="float:left; font-size:13px; font-weight:500; background-color:white"
                                        id="cancelClearWeb" class="btn">Cancel</button>
                                </div>

                            </div>


                        </div>

                        {{--<div class="card-body">
                               <div style="display: flex; gap: 10px;" class="mb-2">
                              <div style="flex: 1; display: flex; flex-direction: column;">
                                    <label for="searchSecondFilterModelInput">Distance</label>
                                    <select class="form-control custom-select-option" name="web_radios" id="web_radios">
                                        <!--                                     <option value="">Select radius</option>
                                            <option value="10">10 miles</option>
                                            <option value="25">25 miles</option>
                                            <option value="50">50 miles</option>
                                            <option value="75">75 miles</option>
                                            <option value="100">100 miles</option>
                                            <option value="150">150 miles</option>
                                            <option value="200">200 miles</option>
                                            <option value="250">250 miles</option>
                                            <option value="500">500 miles</option> -->
                                        <option value="">Select Distance</option>
                                        <option value="10">10 miles</option>
                                        <option value="25">25 miles</option>
                                        <option value="50">50 miles</option>
                                        <option value="75" selected>75 miles</option>
                                        <option value="100">100 miles</option>
                                        <option value="150">150 miles</option>
                                        <option value="200">200 miles</option>
                                        <option value="250">250 miles</option>
                                        <option value="500">500 miles</option>
                                        <option value="Nationwide">Nationwide</option>
                                    </select>
                                </div>
                                <div style="flex: 1; display: flex; flex-direction: column;">
                                    <label for="web_location">Zip Code</label>
                                    <input class="form-control common_selector" name="weblocationNewInput"
                                        id="web_location" type="number" placeholder="Zip Code" value="">
                                </div>
                            </div>

                        </div>--}}
                        <div class="px-4 py-3 border-bottom border-top w-100">
                            <h4 class="mb-0">Condition</h4>
                        </div>
                        <div class="card-body w-100">
                            <div class="filter-product-checkboxs">
                                <label class="mb-2 custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input common_selector"
                                        name="allWebConditionlName" value="allWebConditionValue"
                                        id="selectAllWebConditionCheckbox" <?php echo $webConditionChecks['all'];
                                        ?>>
                                    <span class="custom-control-label">All</span>
                                </label>

                                @foreach ($conditions as $condition)
                                    <label class="mb-2 custom-control custom-checkbox">
                                        <input type="checkbox"
                                            class="custom-control-input autoWebConditionCheckbox common_selector"
                                            name="checkboxUsed" value="{{ $condition }}"
                                            {{ $webConditionChecks[$condition] }}>
                                        <span class="custom-control-label"> {{ $condition }} </span>
                                    </label>
                                @endforeach

                            </div>
                        </div>
                        <div class="px-4 py-3 border-bottom border-top">
                            <h4 class="mb-0">Price Range</h4>
                        </div>
                        <div style="width:100%; padding-bottom: 38px; padding-left:27px" class="pt-2 card-body pe-4">
                            <div class="text-center h6">
                                <input style="width:40px" type="text" id="price">
                                <span id="price-range-display" class="count-item">$0 – $350,000+</span>
                            </div>
                            <div id="price-ranger"></div>
                            <input type="hidden" id="min-price-ranger">
                            <input style="margin-bottom: 30px" type="hidden" id="max-price-ranger">
                        </div>

                        <div class="px-4 py-3 border-bottom border-top">
                            <h4 class="mb-0">Mileage Range</h4>
                        </div>
                        <div style="width:100%; padding-bottom: 38px; padding-left:27px" class="pt-2 card-body pe-4">
                            <div class="text-center h6">
                                <input style="width:40px" type="text" id="price">
                                <span id="mileage-range-display" class="count-item-miles">0 – 300,000+</span>
                            </div>
                            <div id="mileage-ranger"></div>

                            <input type="hidden" id="min-mileage-ranger">
                            <input type="hidden" id="max-mileage-ranger">

                        </div>


                        <div class="px-4 py-3 border-bottom border-top">
                            <h4 class="mb-0">Year Range</h4>
                        </div>
                        <div style="width:100%; padding-bottom: 38px; padding-left:27px" class="pt-2 card-body pe-4">
                            <div class="text-center h6">
                                <input style="width:40px" type="text" id="price">
                                <span id="year-range-display" class="count-item">1985 – 2025</span>
                            </div>
                            <div id="year-ranger"></div>

                            <input type="hidden" id="min-year-ranger">
                            <input type="hidden" id="max-year-ranger">
                        </div>
                    </div>

                    <div style="margin-top:-10px; width:100%" class="container p-0 mb-4">
                        <ul style="" class="nav nav-tabs" id="myTab" role="tablist">
                            <li style="z-index:2; margin-top:3px" class="nav-item me-4" role="presentation">
                                <a style="font-size:14px; margin-left:5px; font-weight:600;" class="nav-link-web active"
                                    id="home-tab" data-bs-toggle="tab" href="#home" role="tab"
                                    aria-controls="home" aria-selected="true">Make / Model</a>
                            </li>
                            <li style="z-index:2;" class="nav-item" role="presentation">
                                <a style="font-size:14px; font-weight:600;" class="nav-link-web" id="web-tab"
                                    data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile"
                                    aria-selected="false">Body Style</a>
                            </li>

                        </ul>
                        <div style="border:none" class="card">
                            <div style="border:none" class="p-0 card-body">
                                <div class="tab-content" id="myTabContent">
                                    <div style="background:white; border:1px solid rgb(221, 221, 221); border-bottom-right-radius: 5px; border-bottom-left-radius: 5px;"
                                        class="tab-pane fade show active" id="home" role="tabpanel"
                                        aria-labelledby="home-tab">
                                        <div class="p-3">
                                            <div class="mb-3 form-group">
                                                <label for="searchSecondFilterMakeInput1">Make</label>
                                                <select id="webMakeFilterMakeInput"
                                                    class="form-control common_selector webMakeFilterMakeInput"
                                                    name="make1">
                                                    <option value="">Select Make</option>
                                                    @foreach ($vehicles as $vehiclMakeData => $vehicle_make_information)
                                                        <option value="{{ $vehiclMakeData }}" {{-- ($make_data == $vehiclMakeData) ? 'selected' : (($webMakeFilterMakeInput == $vehiclMakeData) ? 'selected' : '') --}}
                                                            {{ $make_data == $vehiclMakeData ? 'selected' : '' }}
                                                            data-makeid="{{ $vehicle_make_information }}">
                                                            {{ $vehiclMakeData }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3 form-group">
                                                <label for="searchSecondFilterModelInput2">Model</label>
                                                <select id="webModelFilterInput"
                                                    class="form-control modelData dropDown_selector common_selector webModelFilterInput">
                                                    <option value="">Select Model</option>
                                                </select>
                                            </div>
                                        </div>


                                    </div>
                                    <div style="background:white; border:1px solid rgb(221, 221, 221); border-bottom-right-radius: 5px; border-bottom-left-radius: 5px;"
                                        class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="web-tab">
                                        <div class="p-3">
                                            <div class="custom-row">
                                                <input type="hidden" value="" id="webbody">
                                                @foreach ($bodyTypes as $bodyType => $image)
                                                    <input type="checkbox" class="web_body-checkbox" style="display:none"
                                                        value="{{ $bodyType }}"
                                                        {{ in_array($bodyType, $webBodyFilter) ? 'checked' : '' }}>
                                                    <div
                                                        class="custom-col {{ in_array($bodyType, $webBodyFilter) ? 'active' : '' }}">
                                                        <a href="javascript:void(0)"
                                                            class="common_selector web_shadow-set web_body_type_click"
                                                            data-Testvalue="{{ $bodyType }}">
                                                            <img src="{{ asset('/frontend/assets/images/' . $image) }}"
                                                                class="pt-1 img-fluid" alt="{{ $bodyType }} Image">
                                                            <p
                                                                style="text-align:center; margin-top:4px; margin-bottom:0px">
                                                                {{ $bodyType }}
                                                            </p>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="overflow-hidden card" style="margin-bottom: 8px">


                        {{-- <div class="px-4 py-3 border-bottom">
                        <h4 class="mb-0">Exterior Color</h4>
                    </div>

                    <div class="card-body">
                        <div style="display: flex; flex-wrap: wrap; gap: 10px; width:100%">
                            <input type="hidden" value="" id="webColor">
                            @foreach ($colors as $color)
                            <input type="checkbox" class="web_color-checkbox" style="display:none"
                                value="{{ $color }}" {{ $colorChecks[$color] }}>
                    <a href="javascript:void(0)" style="flex-basis: calc(25% - 10px);"
                        class="web_common_selector_click common_selector"
                        data-value="{{ $color }}">

                        @if ($color == 'white')
                        <div class="border color-box" style="background: {{ $color }};">
                            <i class="fa fa-check check-icon"></i>
                        </div>
                        @else
                        <div class="color-box" style="background: {{ $color }};">
                            <i class="fa fa-check check-icon"></i>
                        </div>
                        @endif
                    </a>
                    @endforeach
                    <a href="javascript:void(0)" style="flex-basis: calc(25% - 10px);"
                        class="web_common_selector_click common_selector" data-value="none">
                        <div class="color-box"
                            style="background: white; border-radius: 3px; text-align:center; border:1px solid rgb(219, 219, 219)">
                            <img style="padding-top:1px;" width="25px"
                                src="{{ asset('/frontend/assets/images/nothing.png') }}" />
                        </div>
                    </a>
                </div>
            </div> --}}

                        <div class="px-4 py-3 border-top">
                            <h4 class="mt-4 mb-3">Price & payment</h4>
                            <div style="display:flex">
                                <div class="input-container">
                                    <span class="dollar-sign">$</span>
                                    <input width="55%" class="mb-5 form-control me-3 dollar-input" type="text"
                                        id="autoPayInput" placeholder="Max monthly budget*">
                                </div>
                                <!-- <button  style="text-align:center; width:50px; height:40px; border-radius:7px; border:1px solid red;" id="withoutModal"><i class="fa fa-calculator"></i></button> -->
                                <button data-bs-toggle="modal" data-bs-target="#PaymentModal"
                                    style="text-align:center; width:50px; height:38px; border-radius:5px; border:1px solid rgb(202, 214, 211); "
                                    class="ms-3" id="withModal" disabled><i class="fa fa-calculator"></i></button>
                            </div>
                        </div>

                        <div class="">
                            <p style="font-size:16px; margin-bottom:1px; padding:10px; margin-top:15px" class="">
                                Transmissions</p>
                        </div>

                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            <div class="accordion-item" style="padding:10px; margin-top:-13px">
                                <h4 style="border: 0.02px solid rgb(206, 205, 205);" class="accordion-header"
                                    id="flush-headingOne">
                                    <button class="accordion-button tran-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapseOne-transmission" aria-expanded="true"
                                        aria-controls="flush-collapseOne-transmission">
                                        All Transmissions
                                    </button>
                                </h4>
                                <div style="border-left:1px solid rgb(206, 205, 205); border-right:1px solid rgb(206, 205, 205); border-bottom:1px solid rgb(206, 205, 205)"
                                    id="flush-collapseOne-transmission" class="accordion-collapse collapse show"
                                    aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body">
                                        <div class="filter-product-checkboxs">
                                            <label class="mb-2 custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input common_selector"
                                                    name="allWebTransmissionlName" value="allWebTransmissionValue"
                                                    id="selectAllWebTransmissionCheckbox" <?php echo $webAllChecked; ?>>
                                                <span class="custom-control-label">All</span>
                                            </label>
                                            <label class="mb-2 custom-control custom-checkbox">
                                                <input type="checkbox"
                                                    class="custom-control-input autoWebTransmissionCheckbox common_selector"
                                                    name="checkboxAutomatic" value="Automatic" <?php echo $webAutomaticChecked; ?>>
                                                <span class="custom-control-label">Automatic</span>
                                            </label>
                                            <label class="mb-2 custom-control custom-checkbox">
                                                <input type="checkbox"
                                                    class="custom-control-input autoWebTransmissionCheckbox common_selector"
                                                    name="checkboxManual" value="Manual" <?php echo $webManualChecked; ?>>
                                                <span class="custom-control-label">Manual</span>
                                            </label>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="">
                            <p style="font-size:16px; margin-bottom:1px; padding:10px; margin-top:15px" class="">
                                Exterior Color</p>
                        </div>
                        <div class="accordion accordion-flush" id="accordionExteriorExample">
                            <div class="accordion-item" style="padding:10px; margin-top:-13px;">
                                <h4 style="border: 0.02px solid rgb(206, 205, 205);" class="accordion-header"
                                    id="flush-headingExterior">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapseExterior-drive" aria-expanded="true"
                                        aria-controls="flush-collapseExterior-drive">
                                        All Exterior Color
                                    </button>
                                </h4>
                                <div style="border-left:1px solid rgb(206, 205, 205); border-right:1px solid rgb(206, 205, 205); border-bottom:1px solid rgb(206, 205, 205); height:300px; overflow-y:auto"
                                    id="flush-collapseExterior-drive" class="accordion-collapse collapse show"
                                    aria-labelledby="flush-headingExterior" data-bs-parent="#accordionExteriorExample">
                                    <div class="accordion-body">
                                        <div class="filter-product-checkboxs">
                                            <label class="mb-2 custom-control custom-checkbox d-flex align-items-center">
                                                <input type="checkbox" class="custom-control-input me-2 common_selector"
                                                    name="allWebExteriorColorName" value="allWebExteriorColorValue"
                                                    id="selectAllWebExteriorColor">
                                                <span class="custom-control-label ms-1">All</span>
                                            </label>

                                            <input type="hidden" value="" id="webExteriorColor">

                                            @foreach ($exteriorColors as $exteriorColor)
                                                <label
                                                    class="mb-2 custom-control custom-checkbox d-flex align-items-center">
                                                    <input type="checkbox"
                                                        class="custom-control-input me-2 autoWebExteriorColorCheckbox common_selector"
                                                        name="webExteriorColorFilter[]" value="{{ $exteriorColor }}"
                                                        {{ $webExteriorColors[$exteriorColor] }}>
                                                    <span class="circle-color me-2"
                                                        style="background-color: {{ $exteriorColor }}; width: 20px; height: 20px; display: inline-block; border-radius: 50%; border: 1px solid #C0BDBD;"></span>
                                                    <span class="custom-control-label ms-1">{{ $exteriorColor }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="">
                            <p style="font-size:16px; margin-bottom:1px; padding:10px; margin-top:15px" class="">
                                Interior Color</p>
                        </div>
                        <div class="accordion accordion-flush" id="accordionInteriorExample">
                            <div class="accordion-item" style="padding:10px; margin-top:-13px;">
                                <h4 style="border: 0.02px solid rgb(206, 205, 205);" class="accordion-header"
                                    id="flush-headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapseInterior-drive" aria-expanded="true"
                                        aria-controls="flush-collapseOne-transmission">
                                        All Interior Color
                                    </button>
                                </h4>
                                <div style="border-left:1px solid rgb(206, 205, 205); border-right:1px solid rgb(206, 205, 205); border-bottom:1px solid rgb(206, 205, 205); height:300px; overflow-y:auto"
                                    id="flush-collapseInterior-drive" class="accordion-collapse collapse "
                                    aria-labelledby="flush-headingOne" data-bs-parent="#accordionInteriorExample">
                                    <div class="accordion-body">
                                        <div class="filter-product-checkboxs">
                                            <label class="mb-2 custom-control custom-checkbox d-flex align-items-center">
                                                <input type="checkbox" class="custom-control-input me-2 common_selector"
                                                    name="allWebInteriorColorName" value="allWebInteriorColorValue"
                                                    id="selectAllWebInteriorColor">
                                                <span class="custom-control-label ms-1">All</span>
                                            </label>

                                            <input type="hidden" value="" id="webInteriorColor">

                                            @foreach ($interiorColors as $interiorColor)
                                                <label
                                                    class="mb-2 custom-control custom-checkbox d-flex align-items-center">
                                                    <input type="checkbox"
                                                        class="custom-control-input me-2 autoWebInteriorColorCheckbox common_selector"
                                                        name="webInteriorColorFilter[]" value="{{ $interiorColor }}"
                                                        {{ $webInteriorColors[$interiorColor] }}>
                                                    <span class="circle-color me-2"
                                                        style="background-color: {{ $interiorColor }}; width: 20px; height: 20px; display: inline-block; border-radius: 50%; border: 1px solid #ccc;"></span>
                                                    <span class="custom-control-label ms-1">{{ $interiorColor }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="">
                            <p style="font-size:16px; margin-bottom:1px; padding:10px; margin-top:15px" class="">
                                Drivetrain</p>
                        </div>

                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            <div class="accordion-item" style="padding:10px; margin-top:-13px;">
                                <h4 style="border: 0.02px solid rgb(206, 205, 205);" class="accordion-header"
                                    id="flush-headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapseOne-drive" aria-expanded="true"
                                        aria-controls="flush-collapseOne-transmission">
                                        All Drivetrain
                                    </button>
                                </h4>
                                <div style="border-left:1px solid rgb(206, 205, 205); border-right:1px solid rgb(206, 205, 205); border-bottom:1px solid rgb(206, 205, 205); height:300px; overflow-y:auto"
                                    id="flush-collapseOne-drive" class="accordion-collapse collapse "
                                    aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body">
                                        <div class="filter-product-checkboxs">
                                            <label class="mb-2 custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input common_selector"
                                                    name="allWebDriveTrainlName" value="allWebDriveTrainValue"
                                                    id="selectAllWebDriveTrain" {{ $webDriveTrainChecks['all'] }}>
                                                <span class="custom-control-label">All</span>
                                            </label>

                                            @foreach ($driveTrains as $driveTrain)
                                                <label class="mb-2 custom-control custom-checkbox">
                                                    <input type="checkbox"
                                                        class="custom-control-input autoWebDriveTrainCheckbox common_selector"
                                                        name="checkbox{{ $driveTrain }}" value="{{ $driveTrain }}"
                                                        {{ $webDriveTrainChecks[$driveTrain] }}>
                                                    <span class="custom-control-label">{{ $driveTrain }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="">
                            <p style="font-size:16px; margin-bottom:1px; padding:10px; margin-top:15px" class="">
                                Fuel Type</p>
                        </div>

                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            <div class="accordion-item" style="padding:10px; margin-top:-13px">
                                <h4 style="border: 0.02px solid rgb(206, 205, 205);" class="accordion-header"
                                    id="flush-headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapseOne-fuel-web" aria-expanded="true"
                                        aria-controls="flush-collapseOne-transmission">
                                        All Fuel Type
                                    </button>
                                </h4>

                                <div style="border-left:1px solid rgb(206, 205, 205); border-right:1px solid rgb(206, 205, 205); border-bottom:1px solid rgb(206, 205, 205)"
                                    id="flush-collapseOne-fuel-web" class="accordion-collapse collapse"
                                    aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body">
                                        <div class="filter-product-checkboxs">
                                            <label class="mb-2 custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input common_selector"
                                                    name="allWebFuellName" value="allWebFuelValue" id="selectAllFuel"
                                                    @if (isset($webFuelChecks['all']) && $webFuelChecks['all'] == 'checked') checked @endif>
                                                <span class="custom-control-label">All</span>
                                            </label>
                                            @foreach ($fuelTypes as $fuelType)
                                                <label class="mb-2 custom-control custom-checkbox">
                                                    <input type="checkbox"
                                                        class="custom-control-input autoWebFuelCheckbox common_selector"
                                                        name="checkboxweb11" value="{{ $fuelType }}"
                                                        @if (isset($webFuelChecks[$fuelType]) && $webFuelChecks[$fuelType] == 'checked') checked @endif>
                                                    <span class="custom-control-label">{{ $fuelType }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cardFooter">
                        </div>
                    </div>
                    <!-- Sticky Button -->
                    <a href="#">
                        <p class="sticky-button" id="stickyButton" style="margin-bottom: 0px;">Over <span
                                id="web_sticky_btn">Loading...</span> Results</p>
                    </a>

                </div>

                <!--/Left Side Content-->

                <div class="col-xl-9 col-lg-9 col-md-12 ">
                    <!--Lists-->
                    <div class="mb-0 " id="auto_ajax">

                    </div>
                    <x-listing-filter />
                    <!--/Lists-->
                </div>
            </div>

        </div>
    </section>
    <!--/Listing-->


    <!-- Modal -->
    <div class="modal fade" id="FilterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-fullscreen auto-modal" style="width:100%; padding:0px; margin:0px">
            <div style="height:100vh;" class="modal-content filter-modal-content">
                <div class="modal-header">
                    <h3 style="margin-top:8px; font-weight:500; color:rgb(82, 81, 81)" class="modal-title fs-5"
                        id="exampleModalLabel">Filter Result</h3>

                    <button type="button" class="float-right mt-1 mb-2 btn-close fw-bold" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>


                <div style="background: #D8DCE3; padding: 10px; border-radius: 2px;">
                    <!-- Main Action Buttons (Cancel - Center Result - Apply) -->
                    <div class="d-flex justify-content-between align-items-center">
                        <button class="btn btn-light border px-4" class="btn" data-bs-dismiss="modal">Cancel</button>
                        <h4 class="m-0 text-center" style="font-size: 18px; font-weight: 500;"><span
                                style="color:rgb(177, 4, 4);" id="total-count">Loading...</span> Result</h4>

                        <!-- <button style="" type="button" class="btn filter-option-auto1-modal"
                                data-bs-toggle="modal" data-bs-target="#FilterModal">
                                <span style="color:rgb(177, 4, 4);" id="total-count">Loading...</span>
                                Result
                            </button>
                            <button type="button" class="btn filter-option-auto2" data-bs-toggle="modal"
                                data-bs-target="#FilterModal">
                                Apply
                            </button> -->
                        <button class="btn  px-4 " style="background:#008B8B; color:white" data-bs-dismiss="modal"
                            id="apply_button">Apply</button>
                    </div>

                    <!-- Clear ALL Link (Subtle, Bottom-Right) -->
                    <div class="text-center mt-2">
                        <a href="#"
                            style="color: darkcyan; font-size: 14px; font-weight: 500; text-decoration: none; border-bottom:1px solid #008B8B"
                            id="filter_usedCar_clear">Clear ALL</a>
                    </div>
                </div>

                {{-- <div style="background: rgb(243, 243, 243); padding: 10px;">
                <!-- Top Section with Clear ALL and Close -->
                <div class="d-flex justify-content-between align-items-center">
                    <a style="color: darkcyan; font-size: 16px; font-weight: 500; cursor: pointer;" id="filter_usedCar_clear">Clear ALL</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

                <hr>

                <!-- Bottom Section with Cancel, Result Count, and Apply -->
                <div class="d-flex justify-content-between align-items-center">
                    <button class="btn btn-outline-dark" id="cancel_button">Cancel</button>
                    <h4 class="m-0 text-center">63,500 results</h4>
                    <button class="btn btn-primary" id="apply_button">Apply</button>
                </div>
            </div> --}}

                {{-- <div class="d-flex justify-content-between" style="background:rgb(243, 243, 243);">
                <a style="margin-top:20px; margin-bottom:15px; color:darkcyan; font-size:16px; font-weight:400;cursor: pointer; padding-left:15px; font-weight:500"
                    id="filter_usedCar_clear">Clear ALL</a>

                    <h4>63,500 result</h4>
                <button
                    style="margin-top:10px; margin-bottom:10px; color:darkcyan; font-size:16px; font-weight:400;cursor: pointer; padding-right:15px; font-weight:500"
                    type="button" class="btn" data-bs-dismiss="modal">Close</button>


            </div> --}}

                <!-- Confirmation Toggle -->
                <div style="background:rgb(243, 243, 243);" id="confirmationToggle" class="d-none">
                    <hr style="margin:10px; background:rgb(4, 221, 221)">
                    <div class="pt-2 pb-5 ps-5 pe-5">
                        <p class="p-0 m-0" style="font-size:18px; font-weight:600">Are you sure?</p>
                        <p style="font-size:15px;">This action clear all your filters.</p>
                        <button
                            style="float:right; background-color:rgb(158, 8, 8); color:white; font-size:15px; font-weight:600"
                            data-url="{{ route('auto', ['clear' => 'flush']) }}" id="mobile_newCar_clear"
                            class="mb-5 btn">Clear All</button>
                        <button style="float:left; font-size:15px; font-weight:600; background-color:white"
                            id="cancelClear" class="btn">Cancel</button>
                    </div>

                </div>





                <div class="modal-body">
                    <section style="padding-top:20px" class="">
                        <div class="container">
                            <div class="row">
                                <!--Left Side Content-->
                                <div class="col-xl-3 col-lg-3 col-md-12">

                                    <div style="display: flex; gap: 10px;" class="mb-5">
                                        {{-- <div style="flex: 1; display: flex; flex-direction: column;">
                                            <label style="font-weight:600; font-size:15px; color:rgb(82, 81, 81)"
                                                for="searchSecondFilterModelInput">Search Radius</label>
                                            <select class="form-control  mobile_common_selector" name="mobile_radios"
                                                id="mobile_radios">
                                                <option value="">Select Distance</option>
                                                <option value="10">10 miles</option>
                                                <option value="25">25 miles</option>
                                                <option value="50">50 miles</option>
                                                <option value="75">75 miles</option>
                                                <option value="100">100 miles</option>
                                                <option value="150">150 miles</option>
                                                <option value="200">200 miles</option>
                                                <option value="250">250 miles</option>
                                                <option value="500">500 miles</option>
                                                <option value="Nationwide">Nationwide</option>
                                            </select>
                                        </div>
                                        <div style="flex: 1; display: flex; flex-direction: column;">
                                            <label style="font-weight:600; font-size:15px; color:rgb(82, 81, 81)"
                                                for="mobile_location">Zip Code</label>
                                            <input class="form-control mobile_common_selector" type="text"
                                                id="mobile_location" name="mobilelocation" placeholder="Zip Code"
                                                value="">
                                        </div> --}}
                                    </div>
                                    <div class="">
                                        <p style="font-weight:600; font-size:15px; color:rgb(82, 81, 81); margin-bottom:6px"
                                            class="">Condition</p>
                                    </div>

                                    <div style="padding:0; height:auto;" class="mb-4 card-body">
                                        <div class="accordion accordion-flush" id="accordionFlushExample">
                                            <div class="accordion-item">
                                                <h4 style="border: 0.02px solid rgb(206, 205, 205);"
                                                    class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                                                        aria-expanded="false" aria-controls="flush-collapseOne">
                                                        All Conditions
                                                    </button>
                                                </h4>
                                                <div style="border-left:1px solid rgb(206, 205, 205); border-right:1px solid rgb(206, 205, 205); border-bottom:1px solid rgb(206, 205, 205)"
                                                    id="flush-collapseOne" class="accordion-collapse collapse"
                                                    aria-labelledby="flush-headingOne"
                                                    data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body">
                                                        <div class="filter-product-checkboxs">
                                                            <label class="mb-2 custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                    class="custom-control-input mobile_common_selector"
                                                                    name="checkbox2" value="option2" <?php echo $mobileConditionChecks['all']; ?>
                                                                    id="selectAllCheckbox">
                                                                <span class="custom-control-label">
                                                                    All
                                                                </span>
                                                            </label>
                                                            @foreach ($conditions as $condition)
                                                                <label class="mb-2 custom-control custom-checkbox">
                                                                    <input type="checkbox"
                                                                        class="custom-control-input autoMobileTypeCheckbox mobile_common_selector"
                                                                        name="checkbox2" value="{{ $condition }}"
                                                                        {{ $mobileConditionChecks[$condition] }}>
                                                                    <span class="custom-control-label">
                                                                        @if ($condition == 'Preowned')
                                                                            Used
                                                                        @else
                                                                            {{ $condition }}
                                                                        @endif
                                                                    </span>
                                                                </label>
                                                            @endforeach

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-5 overflow-hidden card">


                                        <div class="px-4 py-3 border-bottom">
                                            <h4 class="mb-0">Price Range</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center h6">
                                                <input type="text" id="price">
                                                <span class="ranger-styel-mobile" id="mobile-price-range-display">$0 –
                                                    $100,000+</span>
                                            </div>

                                            <div id="mobile-price-ranger"></div>
                                            <span class="pt-1">Price</span>
                                            <input style="margin-left:10px" type="hidden" id="mobile-min-price-ranger">
                                            <input type="hidden" id="mobile-max-price-ranger">

                                        </div>
                                        <div class="card-body">
                                            <div class="text-center h6">
                                                <input type="text" id="price">
                                                <span class="ranger-styel-mobile" id="mobile-mileage-range-display">0 –
                                                    150,000+</span>
                                            </div>
                                            <div id="mobile-mileage-ranger"></div>
                                            <span>Mileage</span>
                                            <input type="hidden" id="mobile-min-mileage-ranger">
                                            <input type="hidden" id="mobile-max-mileage-ranger">

                                        </div>
                                        <div class="card-body">
                                            <div class="text-center h6">
                                                <input type="text" id="price">
                                                <span class="ranger-styel-mobile" id="mobile-year-range-display">1985 –
                                                    2025</span>
                                            </div>
                                            <div id="mobile-year-ranger"></div>
                                            <span>Year</span>
                                            <input type="hidden" id="mobile-min-year-ranger">
                                            <input type="hidden" id="mobile-max-year-ranger">
                                        </div>
                                        <div style="margin-top:45px" class="container p-0 mb-4">
                                            <ul style="border-bottom:0.03px solid gray; background: linear-gradient(180deg, #fff 0, #fff 50%, #f7f7f7 80%, #f2f2f2);"
                                                class="nav nav-tabs" id="myTab" role="tablist">
                                                <li class="nav-item ms-4" role="presentation">
                                                    <a style="font-size:15px" class="nav-link active" id="home-tab"
                                                        data-bs-toggle="tab" href="#make-card" role="tab"
                                                        aria-controls="home" aria-selected="true">Make / Model</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a style="font-size:15px" class="nav-link" id="mobile-tab"
                                                        data-bs-toggle="tab" href="#body-card" role="tab"
                                                        aria-controls="mobile-tab" aria-selected="false">Body Style</a>
                                                </li>
                                            </ul>

                                            <div class="tab-content" id="myTabContent">
                                                <div class="tab-pane fade show active" id="make-card" role="tabpanel"
                                                    aria-labelledby="home-tab">
                                                    <div class="p-3 mt-3">
                                                        <div class="mb-3 form-group">
                                                            <label for="searchSecondFilterMakeInput1">Make</label>
                                                            <select id="secondFilterMakeInputNew"
                                                                class="form-control mobile_common_selector"
                                                                name="make1">

                                                                <option value="">Select Make</option>
                                                                @foreach ($vehicles as $vehicleMakeDataMobile => $vehicle_make_infor_id)
                                                                    <option value="{{ $vehicleMakeDataMobile }}"
                                                                        {{ $make_data == $vehicleMakeDataMobile ? 'selected' : '' }}
                                                                        data-makeid="{{ $vehicle_make_infor_id }}">
                                                                        {{ $vehicleMakeDataMobile }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3 form-group">
                                                            <label for="searchSecondFilterModelInput2">Model</label>
                                                            <select id="secondFilterModelInputNew"
                                                                class="form-control modelData dropDown_selector mobile_common_selector">
                                                                <option value="">Select Model</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="body-card" role="tabpanel"
                                                    aria-labelledby="mobile-tab">
                                                    <div class="p-3 mt-4">
                                                        <div class="custom-row d-flex flex-wrap justify-content-center">
                                                            <input type="hidden" value="" id="mobileBody">
                                                            @foreach ($bodyTypes as $bodyType => $image)
                                                                <input type="checkbox" class="mobile_body-checkbox"
                                                                    style="display:none" value="{{ $bodyType }}"
                                                                    {{ in_array($bodyType, $mobileBody) ? 'checked' : '' }}>
                                                                <div
                                                                    class="custom-col {{ in_array($bodyType, $mobileBody) ? 'active' : '' }} d-flex flex-column justify-content-center align-items-center">
                                                                    <a href="javascript:void(0)"
                                                                        class="common_selector mobile_shadow-set mobile_body_type_click"
                                                                        data-Testvalue="{{ $bodyType }}">
                                                                        <img src="{{ asset('/frontend/assets/images/' . $image) }}"
                                                                            class="pt-3 img-fluid"
                                                                            alt="{{ $bodyType }} Image">
                                                                        <p style="text-align:center; margin-top:6px">
                                                                            {{ $bodyType }}</p>
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>



                                        {{-- <div class="px-4 py-3 border-bottom border-top">
                                        <h4 class="mb-0">Exterior Color</h4>
                                    </div>
                                    <div class="card-body">
                                        <div style="display: flex; flex-wrap: wrap; gap: 10px; width:100%">
                                            <input type="hidden" value="" id="mobileColor">
                                            @foreach ($colors as $color)
                                            <input type="checkbox" class="mobile_color-checkbox"
                                                style="display:none" value="{{ $color }}"
                                    {{ $mobileColorChecks[$color] }}>
                                    <a href="javascript:void(0)" style="flex-basis: calc(25% - 10px);"
                                        class="mobile_common_selector_click common_selector"
                                        data-value="{{ $color }}">

                                        @if ($color == 'white')
                                        <div class="border color-box"
                                            style="background: {{ $color }};">
                                            <i class="fa fa-check check-icon"></i>
                                        </div>
                                        @else
                                        <div class="color-box"
                                            style="background: {{ $color }};">
                                            <i class="fa fa-check check-icon"></i>
                                        </div>
                                        @endif
                                    </a>
                                    @endforeach
                                    <a href="javascript:void(0)" style="flex-basis: calc(25% - 10px);"
                                        class="mobile_common_selector_click common_selector"
                                        data-value="none">
                                        <div class="color-box"
                                            style="background: white; border-radius: 3px; text-align:center; border:1px solid rgb(219, 219, 219)">
                                            <img style="padding-top:1px;" width="25px"
                                                src="{{ asset('/frontend/assets/images/nothing.png') }}" />
                                        </div>
                                    </a>
                                </div>
                            </div> --}}
                                    </div>

                                    <div class="">
                                        <p style="font-weight:600; font-size:15px; color:rgb(82, 81, 81); margin-bottom:6px"
                                            class="">Transmissions</p>
                                    </div>
                                    <div style="padding: 0; height: auto; margin-bottom: 15px" class="card-body">
                                        <div class="accordion accordion-flush" id="accordionFlushExample">
                                            <div class="accordion-item">
                                                <h4 style="border: 0.02px solid rgb(206, 205, 205);"
                                                    class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#flush-collapseOne-transmission"
                                                        aria-expanded="false" aria-controls="flush-collapseOne">
                                                        All Transmissions ...
                                                    </button>
                                                </h4>
                                                <div style="border-left: 1px solid rgb(206, 205, 205); border-right: 1px solid rgb(206, 205, 205); border-bottom: 1px solid rgb(206, 205, 205)"
                                                    id="flush-collapseOne-transmission"
                                                    class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                                                    data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body">
                                                        <div class="filter-product-checkboxs">
                                                            <label class="mb-2 custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                    class="custom-control-input mobile_common_selector"
                                                                    name="checkbox11" value="All"
                                                                    id="selectAllTranscissionCheckbox" <?php echo $allChecked; ?>>
                                                                <span class="custom-control-label">
                                                                    All
                                                                </span>
                                                            </label>
                                                            <label class="mb-2 custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                    class="custom-control-input autoMobileTransmissionCheckbox mobile_common_selector"
                                                                    name="checkbox11" value="Automatic"
                                                                    <?php echo $automaticChecked; ?>>
                                                                <span class="custom-control-label">
                                                                    Automatic
                                                                </span>
                                                            </label>
                                                            <label class="mb-2 custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                    class="custom-control-input autoMobileTransmissionCheckbox mobile_common_selector"
                                                                    name="checkbox12" value="Manual" <?php echo $manualChecked; ?>>
                                                                <span class="custom-control-label">
                                                                    Manual
                                                                </span>
                                                            </label>
                                                            <label class="mb-2 custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                    class="custom-control-input autoMobileTransmissionCheckbox mobile_common_selector"
                                                                    name="checkbox13" value="Variable"
                                                                    <?php echo $variableChecked; ?>>
                                                                <span class="custom-control-label">
                                                                    Variable
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="">
                                        <p style="font-weight:600; font-size:15px; color:rgb(82, 81, 81); margin-bottom:6px"
                                            class="">Exterior Color</p>
                                    </div>
                                    <div style="padding: 0; height: auto; margin-bottom: 15px" class="card-body">
                                        <div class="accordion accordion-flush" id="accordionFlushExample-mobile">
                                            <div class="accordion-item">
                                                <h4 style="border: 0.02px solid rgb(206, 205, 205);"
                                                    class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#flush-collapseExterior-drive-mobile"
                                                        aria-expanded="false" aria-controls="flush-collapseOne">
                                                        All Exterior Color ....
                                                    </button>
                                                </h4>
                                                <div style="border-left: 1px solid rgb(206, 205, 205); border-right: 1px solid rgb(206, 205, 205); border-bottom: 1px solid rgb(206, 205, 205)"
                                                    id="flush-collapseExterior-drive-mobile"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="flush-headingOne"
                                                    data-bs-parent="#accordionFlushExample-mobile">
                                                    <div class="accordion-body">
                                                        <div class="filter-product-checkboxs">
                                                            <label
                                                                class="mb-2 custom-control custom-checkbox d-flex align-items-center">
                                                                <input type="checkbox"
                                                                    class="custom-control-input me-2 mobile_common_selector"
                                                                    name="allMobileExteriorColorName" value="All"
                                                                    id="selectAllMobileExteriorColor"
                                                                    {{ $mobileExteriorColors['all'] ?? '' }} selected>
                                                                <span class="custom-control-label ms-1">All</span>
                                                            </label>


                                                            <input type="hidden" value=""
                                                                id="mobileExteriorColor">

                                                            @foreach ($exteriorColors as $exteriorColor)
                                                                <label
                                                                    class="mb-2 custom-control custom-checkbox d-flex align-items-center">
                                                                    <input type="checkbox"
                                                                        class="custom-control-input me-2 filter-product-checkboxs autoMobileExteriorColorCheckbox common_selector"
                                                                        name="checkbox{{ $exteriorColor }}"
                                                                        value="{{ $exteriorColor }}"
                                                                        data-value="{{ $exteriorColor }}"
                                                                        {{ $mobileExteriorColors[$exteriorColor] ?? '' }}>
                                                                    <span class="circle-color me-2"
                                                                        style="background-color: {{ $exteriorColor }}; width: 20px; height: 20px; display: inline-block; border-radius: 50%; border: 1px solid #ccc;"></span>
                                                                    <span
                                                                        class="custom-control-label ms-1">{{ $exteriorColor }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="">
                                        <p style="font-weight:600; font-size:15px; color:rgb(82, 81, 81); margin-bottom:6px"
                                            class="">Interior Color</p>
                                    </div>
                                    <div style="padding: 0; height: auto; margin-bottom: 15px" class="card-body">
                                        <div class="accordion accordion-flush" id="accordionFlushExample-mobile">
                                            <div class="accordion-item">
                                                <h4 style="border: 0.02px solid rgb(206, 205, 205);"
                                                    class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#flush-collapseInterior-drive-mobile"
                                                        aria-expanded="false" aria-controls="flush-collapseOne">
                                                        All Interior Color .....
                                                    </button>
                                                </h4>
                                                <div style="border-left: 1px solid rgb(206, 205, 205); border-right: 1px solid rgb(206, 205, 205); border-bottom: 1px solid rgb(206, 205, 205)"
                                                    id="flush-collapseInterior-drive-mobile"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="flush-headingOne"
                                                    data-bs-parent="#accordionFlushExample-mobile">
                                                    <div class="accordion-body">
                                                        <div class="filter-product-checkboxs">
                                                            <label
                                                                class="mb-2 custom-control custom-checkbox d-flex align-items-center">
                                                                <input type="checkbox"
                                                                    class="custom-control-input mobile_common_selector"
                                                                    name="allMobileInteriorColorName" value="All"
                                                                    id="selectAllMobileInteriorColor"
                                                                    {{ $mobileInteriorColors['all'] ?? '' }} selected>
                                                                <span class="custom-control-label ms-1">All</span>
                                                            </label>

                                                            <input type="hidden" value=""
                                                                id="mobileInteriorColor">

                                                            @foreach ($interiorColors as $interiorColor)
                                                                <label
                                                                    class="mb-2 custom-control custom-checkbox d-flex align-items-center">
                                                                    <input type="checkbox"
                                                                        class="custom-control-input filter-product-checkboxs autoMobileInteriorColorCheckbox common_selector"
                                                                        name="checkbox{{ $interiorColor }}"
                                                                        value="{{ $interiorColor }}"
                                                                        data-value="{{ $interiorColor }}"
                                                                        {{ $mobileInteriorColors[$interiorColor] }}>
                                                                    <span class="circle-color me-2"
                                                                        style="background-color: {{ $interiorColor }}; width: 20px; height: 20px; display: inline-block; border-radius: 50%; border: 1px solid #ccc;"></span>
                                                                    <span
                                                                        class="custom-control-label ms-1">{{ $interiorColor }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="">
                                        <p style="font-weight:600; font-size:15px; color:rgb(82, 81, 81); margin-bottom:6px"
                                            class="">Drivetrain </p>
                                    </div>
                                    <div style="padding: 0; height: auto; margin-bottom: 15px" class="card-body">
                                        <div class="accordion accordion-flush"
                                            id="accordionFlushExample-drivetrain-mobile">
                                            <div class="accordion-item">
                                                <h4 style="border: 0.02px solid rgb(206, 205, 205);"
                                                    class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#flush-collapseOne-drivetrain-mobile"
                                                        aria-expanded="false" aria-controls="flush-collapseOne">
                                                        All Drivetrain ......
                                                    </button>
                                                </h4>
                                                <div style="border-left: 1px solid rgb(206, 205, 205); border-right: 1px solid rgb(206, 205, 205); border-bottom: 1px solid rgb(206, 205, 205); "
                                                    id="flush-collapseOne-drivetrain-mobile"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="flush-headingOne"
                                                    data-bs-parent="#accordionFlushExample-drivetrain-mobile">
                                                    <div style="height:360px; overflow-y:auto" class="accordion-body">
                                                        <div class="filter-product-checkboxs">
                                                            <label class="mb-2 custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                    class="custom-control-input mobile_common_selector"
                                                                    name="allMobileDriveTrainlName"
                                                                    value="allMobileDriveTrainValue"
                                                                    id="selectAllMobileDriveTrain"
                                                                    {{ $mobileDriveTrainChecks['all'] }}>
                                                                <span class="custom-control-label">All</span>
                                                            </label>
                                                            @foreach ($driveTrains as $driveTrain)
                                                                <label class="mb-2 custom-control custom-checkbox">
                                                                    <input type="checkbox"
                                                                        class="custom-control-input autoMobileDriveTrainCheckbox common_selector"
                                                                        name="autoMobileDriveTrainCheckbox[]"
                                                                        value="{{ $driveTrain }}"
                                                                        {{ $mobileDriveTrainChecks[$driveTrain] }}>
                                                                    <span
                                                                        class="custom-control-label">{{ $driveTrain }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="">
                                        <p style="font-weight:600; font-size:15px; color:rgb(82, 81, 81); margin-bottom:6px"
                                            class="">Fuel Type</p>
                                    </div>

                                    <div style="padding:0; height:auto; margin-bottom: 15px" class="mb-4 card-body">
                                        <div class="accordion accordion-flush" id="accordionFlushExample">
                                            <div class="accordion-item">
                                                <h4 style="border: 0.02px solid rgb(206, 205, 205); background: white;"
                                                    class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#flush-collapseOne-fuel" aria-expanded="false"
                                                        aria-controls="flush-collapseOne">
                                                        All Fuel Type
                                                    </button>
                                                </h4>
                                                <div style="border-left:1px solid rgb(206, 205, 205); border-right:1px solid rgb(206, 205, 205); border-bottom:1px solid rgb(206, 205, 205); background:#ffffff"
                                                    id="flush-collapseOne-fuel" class="accordion-collapse collapse"
                                                    aria-labelledby="flush-headingOne"
                                                    data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body">
                                                        <div class="filter-product-checkboxs">
                                                            <label class="mb-2 custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                    class="custom-control-input mobile_common_selector"
                                                                    name="allFuelName" value="allFuelValue"
                                                                    id="selectAllFuelCheckbox"
                                                                    @if ($mobileFuelChecks['all'] == 'checked') checked @endif>
                                                                <span class="custom-control-label">
                                                                    All
                                                                </span>
                                                            </label>
                                                            {{-- @forelse ($vehicles_fuel_other as $fuel)
                                                        <label class="mb-2 custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="custom-control-input autoMobileFuelCheckbox mobile_common_selector"
                                                                name="checkbox13" value="{{ $fuel }}"
                                                {{ $mobileFuelChecks[$fuel] }}>
                                                <span
                                                    class="custom-control-label">{{ $fuel }}</span>
                                                </label>
                                                @empty
                                                <div>There have no data</div>
                                                @endforelse --}}
                                                            @forelse ($fuelTypes as $fuelType)
                                                                <label class="mb-2 custom-control custom-checkbox">
                                                                    <input type="checkbox"
                                                                        class="custom-control-input autoMobileFuelCheckbox mobile_common_selector"
                                                                        name="checkbox13" value="{{ $fuelType }}"
                                                                        {{ $mobileFuelChecks[$fuelType] }}>
                                                                    <span
                                                                        class="custom-control-label">{{ $fuelType }}</span>
                                                                </label>
                                                            @empty
                                                                <div>There have no data</div>
                                                            @endforelse

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
                </div>

                {{-- <div class="modal-footer fixed-footer">
                <div class="filter-all-btn-modal">
                    <button style="" type="button" class="btn filter-option-auto1-modal"
                        data-bs-toggle="modal" data-bs-target="#FilterModal">
                        <span style="color:rgb(177, 4, 4);" id="total-count">Loading...</span>
                        Result
                    </button>
                    <button type="button" class="btn filter-option-auto2" data-bs-toggle="modal"
                        data-bs-target="#FilterModal">
                        Apply
                    </button>
                </div>
            </div> --}}
                {{-- auto filter and search action in mobile device --}}
            </div>
        </div>
    </div>

    <!-- payment modal start -->
    @include('frontend.website.layout.autoPaymentModal')
    @include('frontend.website.layout.quickViewModal')
    @include('frontend.website.layout.shareEmailModal')
    <!-- payment modal end -->

    {{-- auto filter and search action in mobile device --}}
    <div class="filter-btn-styel-auto">
        <div class="filter-all-btn">
            <button type="button" class="btn filter-option-auto1"
                data-url="{{ route('auto', ['clear' => 'newCar']) }}" id="newSearchBtn">
                <i style="margin-right: 7px" class="fa fa-search"></i>New Search
            </button>
            <button type="button" class="btn filter-option-auto2 mobileZipCode" data-bs-toggle="modal"
                data-bs-target="#FilterModal">
                <i style="margin-right: 7px" class="fa fa-filter"></i>Filter
            </button>
        </div>
    </div>


    <!-- Loader HTML -->
    <div class="loader" id="loader" style="display: none;"></div>

@endsection
@push('js')
    <script src="{{ asset('frontend') }}/assets/js/15.7.1.nouislider.min.js"
        integrity="sha512-UOJe4paV6hYWBnS0c9GnIRH8PLm2nFK22uhfAvsTIqd3uwnWsVri1OPn5fJYdLtGY3wB11LGHJ4yPU1WFJeBYQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        //************ Drivetrain checkbox javascript code start here *********************************************
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllWebDriveTrain = document.getElementById('selectAllWebDriveTrain');
            const webDrivetrainCheckboxes = document.querySelectorAll('.autoWebDriveTrainCheckbox');

            function updateDrivetrainParameter() {
                const selectedDrivetrains = Array.from(webDrivetrainCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

                updateUrlParameter('drivetrain', selectedDrivetrains.join(','));
            }


            selectAllWebDriveTrain.addEventListener('change', function() {
                if (selectAllWebDriveTrain.checked) {
                    webDrivetrainCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
                updateDrivetrainParameter();
                updateUrlParameter('lowestPrice', '');
                updateUrlParameter('lowestMileage', '');
                updateUrlParameter('owned', '');
            });

            webDrivetrainCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (checkbox.checked) {
                        selectAllWebDriveTrain.checked = false;
                    }
                    updateDrivetrainParameter();
                    updateUrlParameter('lowestPrice', '');
                    updateUrlParameter('lowestMileage', '');
                    updateUrlParameter('owned', '');
                });
            });

            // Initial call to set the parameter based on the current state of checkboxes
            updateDrivetrainParameter();
        });


        //************ Drivetrain checkbox javascript code End  here *********************************************

        //************ Drivetrain mobile checkbox javascript code start here *********************************************
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllMobileDriveTrain = document.getElementById('selectAllMobileDriveTrain');
            const mobileDrivetrainCheckboxes = document.querySelectorAll('.autoMobileDriveTrainCheckbox');

            function updatemobileDrivetrainParameter() {
                const selectedMobileDrivetrains = Array.from(mobileDrivetrainCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

                updateUrlParameter('drivetrain', selectedMobileDrivetrains.join(','));
            }


            selectAllMobileDriveTrain.addEventListener('change', function() {
                if (selectAllMobileDriveTrain.checked) {
                    mobileDrivetrainCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
                updatemobileDrivetrainParameter();
                updateUrlParameter('lowestPrice', '');
                updateUrlParameter('lowestMileage', '');
                updateUrlParameter('owned', '');
            });




            mobileDrivetrainCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (checkbox.checked) {
                        selectAllMobileDriveTrain.checked = false;
                    }
                    updatemobileDrivetrainParameter();
                    updateUrlParameter('lowestPrice', '');
                    updateUrlParameter('lowestMileage', '');
                    updateUrlParameter('owned', '');
                });
            });

            // Initial call to set the parameter based on the current state of checkboxes
            updatemobileDrivetrainParameter();
        });

        //************ Drivetrain mobile checkbox javascript code End  here *********************************************



        document.addEventListener('DOMContentLoaded', function() {
            const stickyButton = document.getElementById('stickyButton');
            const cardFooter = document.getElementById('cardFooter');
            const windowHeight = window.innerHeight || document.documentElement.clientHeight;
            const viewportWidth = window.innerWidth;

            window.addEventListener('scroll', function() {
                const cardFooterRect = cardFooter.getBoundingClientRect();
                const windowHeight = window.innerHeight || document.documentElement.clientHeight;

                // Check if the card footer is in view
                if (cardFooterRect.top <= windowHeight && cardFooterRect.bottom >= 0) {
                    // If in view, make the sticky button sticky
                    stickyButton.style.position = 'sticky';
                    stickyButton.style.width = '100%';

                } else {
                    // If not in view, make the sticky button fixed
                    if (cardFooterRect.top <= 0) {
                        stickyButton.style.position = 'sticky';
                        stickyButton.style.width = '100%';
                    } else {
                        stickyButton.style.position = 'fixed';
                        if (viewportWidth >= 1001 && viewportWidth <= 1025) {
                            stickyButton.style.width = '21.1%';
                        } else if (viewportWidth >= 1026 && viewportWidth <= 1050) {
                            stickyButton.style.width = '20.6%';
                        } else if (viewportWidth >= 1051 && viewportWidth <= 1075) {
                            stickyButton.style.width = '20.2%';
                        } else if (viewportWidth >= 1076 && viewportWidth <= 1100) {
                            stickyButton.style.width = '19.8%';
                        } else if (viewportWidth >= 1101 && viewportWidth <= 1125) {
                            stickyButton.style.width = '19.8%';
                        } else if (viewportWidth >= 1126 && viewportWidth <= 1150) {
                            stickyButton.style.width = '18.8%';
                        } else if (viewportWidth >= 1151 && viewportWidth <= 1200) {
                            stickyButton.style.width = '18%';
                        } else if (viewportWidth >= 1201 && viewportWidth <= 1250) {
                            stickyButton.style.width = '18%';
                        } else if (viewportWidth >= 1251 && viewportWidth <= 1300) {
                            stickyButton.style.width = '17.4%';
                        } else if (viewportWidth >= 1301 && viewportWidth <= 1325) {
                            stickyButton.style.width = '21%';
                        } else if (viewportWidth >= 1326 && viewportWidth <= 1350) {
                            stickyButton.style.width = '20.5%';
                        } else if (viewportWidth >= 1351 && viewportWidth <= 1380) {
                            stickyButton.style.width = '20.1%';
                        } else if (viewportWidth >= 1381 && viewportWidth <= 1400) {
                            stickyButton.style.width = '19.8%';
                        } else if (viewportWidth >= 1401 && viewportWidth <= 1425) {
                            stickyButton.style.width = '19.5%';
                            stickyButton.style.textAlign = 'center';
                        } else if (viewportWidth >= 1426 && viewportWidth <= 1450) {
                            stickyButton.style.width = '19.3%';
                            stickyButton.style.textAlign = 'center';
                        } else if (viewportWidth >= 1451 && viewportWidth <= 1475) {
                            stickyButton.style.width = '19%';
                            stickyButton.style.textAlign = 'center';
                        } else if (viewportWidth >= 1476 && viewportWidth <= 1500) {
                            stickyButton.style.width = '18.9%';
                            stickyButton.style.textAlign = 'center';
                        } else if (viewportWidth >= 1501 && viewportWidth <= 1600) {
                            stickyButton.style.width = '18.5%';
                        } else if (viewportWidth >= 1601 && viewportWidth <= 1700) {
                            stickyButton.style.width = '17%';
                        } else if (viewportWidth >= 1701 && viewportWidth <= 1800) {
                            stickyButton.style.width = '16%';
                        } else {
                            stickyButton.style.width = '14.5%';
                        }
                    }

                }
            });
        });



        //********************** New Search and clear all ajax code start  here  *****************************************************

        function showLoader() {
            document.getElementById('loader').style.display = 'block';
            document.getElementById('newSearchBtn').disabled = true;
        }

        // Function to hide loader
        function hideLoader() {
            document.getElementById('loader').style.display = 'none';
            document.getElementById('newSearchBtn').disabled = false;
        }

        document.getElementById('newSearchBtn').addEventListener('click', function(e) {
            e.preventDefault();
            var action = $(this).data('url');
            console.log(action);
            showLoader();
            $.ajax({
                url: action,
                method: "GET",
                success: function(response) {
                    console.log(response);
                    var url = new URL(window.location.href);
                    url.searchParams.set('showModal', 'true');
                    window.location.href = window.location.pathname + '?showModal=true';
                    // window.location.href = url.toString();
                },
                error: function(error) {
                    console.error('Error during AJAX request:', error);
                },
                complete: function() {
                    // Hide loader when AJAX completes
                    hideLoader();
                }
            });
        });

        // Add event listener to Clear ALL button
        document.getElementById('mobile_newCar_clear').addEventListener('click', function(e) {
            e.preventDefault();
            var action = $(this).data('url');
            console.log(action);
            showLoader();
            // Example AJAX call for clearing all filters
            $.ajax({
                url: action,
                method: "GET",
                success: function(response) {
                    console.log(response);
                    var url = new URL(window.location.href);
                    url.searchParams.set('showModal', 'true');
                    window.location.href = window.location.pathname;
                    // window.location.href = url.toString();
                },
                error: function(error) {
                    console.error('Error during AJAX request:', error);
                },
                complete: function() {
                    hideLoader();
                    // Perform additional actions after clearing filters if needed
                }
            });
        });


        document.addEventListener('DOMContentLoaded', function() {

            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('showModal') === 'true') {
                var modal = new bootstrap.Modal(document.getElementById('FilterModal'));
                modal.show();

                // Remove the query parameter from the URL to prevent the modal from showing on subsequent reloads
                var url = new URL(window.location.href);
                url.searchParams.delete('showModal');
                window.history.replaceState({}, document.title, url.toString());
            }
        });

        $('#clearAllFilter').on('click', function() {
            var requestURL = window.location.href;
            var action = $(this).data('url');
            $('#clearAllFilter').text('Loading...');
            // Clear session on the server
            $.ajax({
                url: action,
                method: "GET",
                success: function(response) {
                    if (response.success == 'clear') {
                        // Session cleared, now remove `clear=flush` from URL after 2 seconds
                        setTimeout(function() {
                            var url = new URL(requestURL);

                            url.searchParams.delete('clear');
                            // Update the browser's address bar

                            window.history.pushState({}, document.title, url.toString());
                        }, 1000); // 2000 milliseconds = 2 seconds
                        // window.location.reload();
                        window.location.href = window.location.pathname;
                        $('#clearAllFilter').text('Clear All');
                    }

                },
                error: function(xhr, status, error) {
                    console.error('Error clearing session:', error);
                }
            });;

        });
        //********************** New Search and clear all ajax code End  here  *****************************************************

        $('#check_system').change(function() {
            if ($(this).is(':checked')) {
                // Show second_content when checkbox is checked
                $('#first_content').hide();
                $('#second_content').show();
            } else {
                // Show first_content when checkbox is unchecked
                $('#first_content').show();
                $('#second_content').hide();
            }
        });

        $('#mobile_check_system').change(function() {
            if ($(this).is(':checked')) {
                // Show second_content when checkbox is checked
                $('#mobile_first_content').hide();
                $('#mobile_second_content').show();
            } else {
                // Show first_content when checkbox is unchecked
                $('#mobile_first_content').show();
                $('#mobile_second_content').hide();
            }
        });



        $('#searchMobilefirstFilterMakeInput').on('change', function() {
            var makeId = $(this).find('option:selected').data('makeid');
            var url = "{{ route('homePage.modelSearch', '') }}" + '/' + makeId;
            $('#searchMobileFirstFilterModelInput').html('<option value="">Loading...</option>');

            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                success: function(res) {
                    $('#searchMobileFirstFilterModelInput').empty().append(
                        '<option value="">Choose Model</option>');

                    $.each(res, function(index, item) {
                        var option = "<option value='" + item.model_name + "' data-id='" + item
                            .id +
                            "'>" + item.model_name + "</option>";
                        $('#searchMobileFirstFilterModelInput').append(option);
                    });
                },
                error: function(error) {
                    console.error('Error fetching models:', error);
                }
            });
        });


        $('#webMakeFilterMakeInput').on('change', function() {
            var makeId = $(this).find('option:selected').data('makeid');
            var url = "{{ route('homePage.modelSearch', '') }}" + '/' + makeId;
            $('#webModelFilterInput').html('<option value="">Loading...</option>');

            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                success: function(res) {

                    // //work it 100% well
                    // $('#webModelFilterInput').empty().append(
                    //     '<option value="">Choose Model</option>');

                    // $.each(res, function(index, item) {
                    //     var option = "<option value='" + item.model_name + "' data-id='" + item
                    //         .id +
                    //         "'>" + item.model_name + "</option>";
                    //     $('#webModelFilterInput').append(option);
                    // });

                    let modelDropdown = $('#webModelFilterInput');
                    let selectedModelInfo = getUrlParameter('model');
                    modelDropdown.empty().append('<option value="">Choose Model</option>');

                    $.each(res, function(index, item) {
                        let selectedAttr = (item.model_name === selectedModelInfo) ?
                            'selected' : '';
                        modelDropdown.append(
                            `<option value="${item.model_name}" data-id="${item.id}" ${selectedAttr}>${item.model_name}</option>`
                        );
                    });

                    // If a model is preselected from the URL, set it
                    if (selectedModelInfo) {
                        modelDropdown.val(selectedModelInfo).trigger('change');
                    }
                },
                error: function(error) {
                    console.error('Error fetching models:', error);
                }
            });
        });

        $('#searchMobileSecondFilterMakeInput').on('change', function() {
            var makeId = $(this).find('option:selected').data('makeid');
            var url = "{{ route('homePage.modelSearch', '') }}" + '/' + makeId;
            $('#searchMobileSecondFilterModelInput').html('<option value="">Loading...</option>');
            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                success: function(res) {
                    $('#searchMobileSecondFilterModelInput').empty().append(
                        '<option value="">Choose Model</option>');

                    $.each(res, function(index, item) {
                        var option = "<option value='" + item.model_name + "' data-id='" + item
                            .id +
                            "'>" + item.model_name + "</option>";
                        $('#searchMobileSecondFilterModelInput').append(option);
                    });
                },
                error: function(error) {
                    console.error('Error fetching models:', error);
                }
            });
        });
        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            let selectedMake = getUrlParameter('make');
            let selectedModel = getUrlParameter('model');

            // if (selectedMake) {
            //     $('#webMakeFilterMakeInput').val(selectedMake).trigger('change');
            // }

            // Ensure URL parameters are preserved before anything else happens
            function preserveUrlParams() {
                const urlParams = new URLSearchParams(window.location.search);
                history.replaceState(null, '', '?' + urlParams.toString()); // Persist URL parameters
            }

            // Preserve parameters when the page loads
            preserveUrlParams();

            // Usage on modal open
            // $('#FilterModal').on('shown.bs.modal', function() {
            if ($(window).width() < 1000) {
                if (selectedMake) {
                    $('#secondFilterMakeInputNew').val(selectedMake).trigger('change');
                }
            } else {
                if (selectedMake) {
                    $('#webMakeFilterMakeInput').val(selectedMake).trigger('change');
                }
            }
            // });


            //     // Detect screen width
            // if ($(window).width() < 1000) {
            //     alert(selectedMake + ' @@ ' + selectedModel);
            //     if (selectedMake) {
            //         $('#secondFilterMakeInputNew').val(selectedMake).trigger('change');
            //     }
            // } else {
            //     if (selectedMake) {
            //         $('#webMakeFilterMakeInput').val(selectedMake).trigger('change');
            //     }
            // }



            $('.telephoneInput').inputmask('(999) 999-9999');

            $(document).on('change', '#tradeChecked', function() {
                var isChecked = this.checked;
                if (isChecked == true) {
                    $('#Auto_Trade_block_content').css('display', 'block');
                } else {
                    $('#Auto_Trade_block_content').css('display', 'none');
                }
            });




            $('.autoMakeCheckbox').on('click', function() {
                var selectedVehicles = [];
                $(".autoMakeCheckbox:checked").each(function() {
                    selectedVehicles.push($(this).val());
                });
                pageLoad(selectedVehicles)
            });
        });






        $(document).ready(function() {
            $('#firstFilterMakeInput').on('change', function() {
                ;
                var makeId = $(this).find('option:selected').data('makeid');
                var url = "{{ route('homePage.modelSearch', '') }}" + '/' + makeId;
                $.ajax({
                    url: url,
                    typs: 'post',
                    data: {
                        id: makeId
                    },
                    success: function(res) {
                        $('#firstFilterModelInput').empty();
                        $('#firstFilterModelInput').append(
                            '<option value="">Choose Model</option>')
                        $.each(res, function(index, item) {
                            // var option = $('<option>');
                            // option.val(index);
                            // option.text(item)
                            var option = "<option value='" + item + "' data-id='" +
                                index + "'>" + item + "</option>"
                            $('#firstFilterModelInput').append(option);
                        });
                    },
                    error: function(error) {

                    }
                });
            });

            $('#searchFirstFilterMakeInput').on('change', function() {
                var makeId = $(this).find('option:selected').data('makeid');
                var url = "{{ route('homePage.modelSearch', '') }}" + '/' + makeId;
                $.ajax({
                    url: url,
                    typs: 'post',
                    data: {
                        id: makeId
                    },
                    success: function(res) {
                        $('#searchFirstFilterModelInput').empty();
                        $('#searchFirstFilterModelInput').append(
                            '<option value="">Choose Model</option>')
                        $.each(res, function(index, item) {
                            // var option = $('<option>');
                            // option.val(index);
                            // option.text(item)
                            var option = "<option value='" + item + "' data-id='" +
                                index + "'>" + item + "</option>"
                            $('#searchFirstFilterModelInput').append(option);
                        });
                    },
                    error: function(error) {

                    }
                });
            });

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

                            var option = "<option value='" + item + "' data-id='" +
                                index + "'>" + item + "</option>"
                            $('#searchSecondFilterModelInput').append(option);
                        });
                    },
                    error: function(error) {

                    }
                });
            });

            $('#secondFilterMakeInputNew').on('change', function() {
                ;
                var makeId = $(this).find('option:selected').data('makeid');
                var url = "{{ route('homePage.modelSearch', '') }}" + '/' + makeId;
                $.ajax({
                    url: url,
                    typs: 'post',
                    data: {
                        id: makeId
                    },
                    success: function(res) {
                        // $('#secondFilterModelInputNew').empty();
                        // $('#secondFilterModelInputNew').append(
                        //     '<option value="">Choose Model</option>')
                        // $.each(res, function(index, item) {

                        //     var option = "<option value='" + item.model_name +
                        //         "' data-id='" +
                        //         index + "'>" + item.model_name + "</option>"
                        //     $('#secondFilterModelInputNew').append(option);
                        // });


                        let mobileModelDropdown = $('#secondFilterModelInputNew');
                        let selectedMobileModelInfo = getUrlParameter(
                            'model'); // Get model from URL

                        mobileModelDropdown.empty().append(
                            '<option value="">Choose Model</option>');

                        $.each(res, function(index, item) {
                            let mobileSelectedAttr = (item.model_name ===
                                selectedMobileModelInfo) ? 'selected' : '';
                            mobileModelDropdown.append(
                                `<option value="${item.model_name}" data-id="${item.id}" ${mobileSelectedAttr}>${item.model_name}</option>`
                            );
                        });

                        // If a model is preselected from the URL, set it correctly
                        if (selectedMobileModelInfo) {
                            mobileModelDropdown.val(selectedMobileModelInfo).trigger(
                                'change'); // Fixed variable name
                        }
                    },
                    error: function(error) {

                    }
                });
            });
        });





        $(document).ready(function() {
            $('.searchMobilefirstFilterMakeInput10').select2();
            $('.searchMobileFirstFilterModelInput').select2();
            $('.webModelFilterInput').select2();

            $('.searchMobileFirstFilterModelInput1').select2();
            $('.searchMobileSecondFilterMakeInput2').select2();

            $('.webMakeFilterMakeInput').select2();

            $('.searchMobileSecondFilterModelInput22').select2();


            function focusSearchField(element) {
                setTimeout(function() {
                    let searchField = $(element).data('select2').dropdown.$search || $(element).data(
                            'select2')
                        .selection.$search;
                    if (searchField && searchField.length > 0) {
                        searchField[0].focus();
                    }
                }, 0);
            }

            $('.searchMobilefirstFilterMakeInput10').on('select2:open', function(e) {
                focusSearchField(this);
            });
            $('.webModelFilterInput').on('select2:open', function(e) {
                focusSearchField(this);
            });

            $('.searchMobileFirstFilterModelInput').on('select2:open', function(e) {
                focusSearchField(this);
            });

            $('.searchMobileFirstFilterModelInput1').on('select2:open', function(e) {
                focusSearchField(this);
            });
            $('.searchMobileSecondFilterMakeInput2').on('select2:open', function(e) {
                focusSearchField(this);
            });
            $('.webMakeFilterMakeInput').on('select2:open', function(e) {
                focusSearchField(this);
            });
            $('.searchMobileSecondFilterModelInput22').on('select2:open', function(e) {
                focusSearchField(this);
            });
        });


        $(document).ready(function() {

            $(document).on('click', '.clearfilterAjax', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('auto', ['clear' => 'flush']) }}",
                    method: "get",
                    success: function(res) {
                        if (res.success == 'clear') {
                            setTimeout(function() {
                                var url = new URL(requestURL);
                                url.searchParams.delete('clear');
                                // Update the browser's address bar
                                window.history.pushState({}, document.title, url
                                    .toString());
                            }, 1000); // 2000 milliseconds = 2 seconds
                            // window.location.reload();
                            window.location.href = window.location.pathname;
                            // $('#clearAllFilter').text('Clear All');

                        }
                        // if (res.status == 'success') {
                        //     toastr.success(res.message);
                        // }
                        // window.location.reload();
                    },
                    error: function(res) {
                        toastr.error(res.message);
                    }
                });
            });
        });

        document.getElementById('filter_usedCar_clear').addEventListener('click', function() {
            document.getElementById('confirmationToggle').classList.toggle('d-none');
        });

        document.getElementById('cancelClear').addEventListener('click', function() {
            document.getElementById('confirmationToggle').classList.add('d-none');
        });


        document.getElementById('filter_usedCar_clear-web').addEventListener('click', function() {
            document.getElementById('confirmationToggleWeb').classList.toggle('d-none');
        });

        document.getElementById('cancelClearWeb').addEventListener('click', function() {
            document.getElementById('confirmationToggleWeb').classList.add('d-none');
        });



        $('.mobileZipCode').on('click', function(e) {
            e.preventDefault();
            var zip = getUrlParameter('zip');
            $('#mobile_location').val(zip);
        });
    </script>

    <!-- lkfghjhgjkhg ghsdjkghjksdgh dsjkghdjkgh sdjkghjksd gjksdfhgjk dghsdfjkg sdjkghjksdfg jsdfhgjksdfg sdjkfghj  -->
    <!-- Add this CSS to your stylesheet -->


    <!-- Add this JavaScript to handle filter interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to add a new filter chip
            function addFilterChip(filterName, filterValue, filterType) {
                const filterContainer = document.querySelector('.filter-chips-container');

                // Check if this filter already exists
                const existingChips = document.querySelectorAll('.filter-chip');
                for (let chip of existingChips) {
                    if (chip.querySelector('span').textContent === filterValue) {
                        return;
                    }
                }

                const chip = document.createElement('div');
                chip.className = 'filter-chip';
                chip.innerHTML = `
                <span>${filterValue}</span>
                <button class="filter-close" data-filter-type="${filterType}">&times;</button>
            `;
                filterContainer.appendChild(chip);
            }

            // Handle close button clicks
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('filter-close')) {
                    const filterType = e.target.getAttribute('data-filter-type');
                    const chip = e.target.closest('.filter-chip');
                    chip.remove();
                    console.log(`Removed filter: ${filterType}`);
                }
            });

            // Clear all filters
            document.getElementById('clearAllFiltersBtn').addEventListener('click', function() {
                const filterChips = document.querySelectorAll('.filter-chip');
                filterChips.forEach(chip => chip.remove());
                console.log('Cleared all filters');
            });
        });
    </script>

    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get the URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const zipCode = urlParams.get("zipcode"); // Get zipcode parameter
            const radius = urlParams.get("radius"); // Get radius parameter

            // If the zipcode exists but radius is not set, set the default radius (e.g., 75 miles)
            if (zipCode && !radius) {
                document.querySelectorAll("#web_radios").forEach(select => {
                    select.value = "75"; // Set default radius when zip code is present
                });
            } else if (radius) {
                // If radius exists in URL, use that value
                document.querySelectorAll("#web_radios").forEach(select => {
                    select.value = radius;
                });
            }
        });
    </script> --}}



    {{-- <script>
        $(document).ready(function() {
            console.log("Script is running");

            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const zipParam = urlParams.get("zip"); // Get zip code from URL
            let radiusParam = urlParams.get("radius"); // Get radius from URL

            console.log("Zip Param:", zipParam);
            console.log("Radius Param:", radiusParam);

            // Select the distance dropdown elements for both web and mobile
            const radiusDropdown = $("#web_radios");
            const mobileRadiusDropdown = $("#mobile_radios");

            console.log("Web Dropdown:", radiusDropdown);
            console.log("Mobile Dropdown:", mobileRadiusDropdown);

            // Log dropdown values before updating
            console.log("Web Dropdown Value Before:", radiusDropdown.val());
            console.log("Mobile Dropdown Value Before:", mobileRadiusDropdown.val());

            // If zip exists but radius is not set, default to 75
            if (zipParam) {
                // Set default radius value to 75 if not already set
                if (!radiusParam) {
                    radiusParam = "75"; // Default radius if not set in URL
                    urlParams.set('radius', '75'); // Update the URL with radius
                    window.history.replaceState({}, '', `${window.location.pathname}?${urlParams.toString()}`);
                }

                // Set both web and mobile radius dropdowns to 75 or the selected radius
                if (radiusDropdown) radiusDropdown.val(radiusParam); // Set the web dropdown
                if (mobileRadiusDropdown) mobileRadiusDropdown.val(radiusParam); // Set the mobile dropdown
            } else {
                // Clear radius values if zip code is not found
                if (radiusDropdown) radiusDropdown.val(''); // Clear the web dropdown
                if (mobileRadiusDropdown) mobileRadiusDropdown.val(''); // Clear the mobile dropdown
            }

            // Trigger 'change' event to ensure the dropdowns are updated visually
            if (radiusDropdown) radiusDropdown.trigger('change');
            if (mobileRadiusDropdown) mobileRadiusDropdown.trigger('change');

            // Log dropdown values after updating
            console.log("Web Dropdown Value After:", radiusDropdown.val());
            console.log("Mobile Dropdown Value After:", mobileRadiusDropdown.val());
        });

        // Ensure radius is added to the URL if zip is present and radius is missing
        $(window).on('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const zipParam = urlParams.get("zip");
            if (zipParam && !urlParams.has('radius')) {
                urlParams.set('radius', '75'); // Set default radius to 75 if not already set
                window.history.replaceState({}, '', `${window.location.pathname}?${urlParams.toString()}`);
            }
        });
    </script> --}}






    {{-- <script>
        // This function runs when the page has fully loaded.
        document.addEventListener("DOMContentLoaded", function() {
            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const zipParam = urlParams.get("zip"); // Get zip code from URL
            let radiusParam = urlParams.get("radius"); // Get radius from URL

            // Select the distance dropdown element
            const radiusDropdown = document.getElementById("web_radios");
            const mobileRadiusDropdown = document.getElementById("mobile_radios");

            alert(urlParams + ' ' + zipParam + ' ' + radiusParam + ' ' + radiusDropdown)
            // If zip exists but radius is not set, default to 75
            if (zipParam) {
                radiusParam = radiusParam ? radiusParam : "75";
            } else {
                radiusParam = ""; // Clear radius if zip is empty
            }

            // Function to select the correct option in the dropdown
            function selectRadiusOption(dropdown, radius) {
                if (dropdown) {
                    for (let option of dropdown.options) {
                        option.selected = option.value === radius;
                    }
                }
            }

            // Set the default selection in the dropdown
            selectRadiusOption(radiusDropdown, radiusParam);

            // Update the URL with the default radius if it's not already set
            if (!urlParams.has('radius')) {
                urlParams.set('radius', '75'); // Set default radius to 75
                window.history.replaceState({}, '', `${window.location.pathname}?${urlParams.toString()}`);
            }
        });

        // If radius isn't set yet, ensure it's added to the URL
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (!urlParams.has('radius')) {
                urlParams.set('radius', '75'); // Set default radius to 75
                window.history.replaceState({}, '', `${window.location.pathname}?${urlParams.toString()}`);
            }
        };
    </script> --}}




    <!-- Place this script at the end of the body or in a <head> with defer attribute -->


    @include('frontend.website.layout.js.multiSelectDropdwn_js')
    @include('frontend.website.layout.js.dealer_auto_list_js')

    @include('frontend.reapted_js')
    {{-- @include('frontend.website.layout.js.price_ranger_slider') --}}
@endpush
