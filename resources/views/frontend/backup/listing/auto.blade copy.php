@extends('frontend.website.layout.app')
@push('head')
    <link rel="canonical" href="{{ url()->current() }}">
@endpush
<?php

use Illuminate\Support\Facades\Cookie;

$searchData = json_decode(request()->cookie('searchData'), true) ?? [];
$cookie_zipcode = request()->cookie('zipcode') ?? '';
// dd($cookie_zipcode);
// $searchData = Session::get('searchData', []);
$autoMobileTypeCheckbox = $searchData['autoMobileTypeCheckbox'] ?? [];
$autoWebConditionCheckbox = $searchData['autoWebConditionCheckbox'] ?? [];
$autoMobileTransmissionCheckbox = $searchData['autoMobileTransmissionCheckbox'] ?? [];
$autoWebTransmissionCheckbox = $searchData['autoWebTransmissionCheckbox'] ?? [];
$autoMobileFuelCheckbox = $searchData['autoMobileFuelCheckbox'] ?? [];
$autoWebDriveTrainCheckbox = $searchData['autoWebDriveTrainCheckbox'] ?? [];
$autoWebFuelCheckbox = $searchData['autoWebFuelCheckbox'] ?? [];
$mobileColorFilter = $searchData['mobileColorFilter'] ?? [];
$webColorFilter = $searchData['webColorFilter'] ?? [];
$webBodyFilter = isset($searchData['webBodyFilter']) ? (array) $searchData['webBodyFilter'] : [];
$mobileBody = isset($searchData['mobileBody']) ? (array) $searchData['mobileBody'] : [];
$webMakeFilterMakeInput = $searchData['webMakeFilterMakeInput'] ?? '';
$selectedMake = $searchData['secondFilterMakeInputNew'] ?? '';
//dd($webBodyFilter);
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
$bodyTypes = [
    'Full Size SUV' => 'suv.svg',
    'Sedan' => 'sed.svg',
    'Station Wagon' => 'wag.svg',
    'Coupe' => 'coup.svg',
    'Truck' => 'truc.svg',
    'Convertible' => 'conver.svg',
    'Minivan' => 'vans.svg',
    'Hatchback' => 'hat.svg',
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
$colors = ['red', 'blue', 'orange', 'green', 'black', 'white', 'violet', 'gray', 'pink', 'yellow'];
$colorChecks = getColorChecks($colors, (array) $webColorFilter);
$mobileColorChecks = getColorChecks($colors, (array) $mobileColorFilter);

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
$driveTrains = ['FWD', 'RWD', 'AWD', '4WD', '4x4', '4x2', '10x4', '10x6', '10x8', '12x4', '12x6', '14x4', '14x6', '6x2', '6x4', '6x6', '8x2', '8x4', '8x6', '8x8', '10x10'];
$webDriveTrainChecks = getDriveTrainChecks($driveTrains, (array) $autoWebDriveTrainCheckbox);
// drivetrain code End here ***********************8********

// vehicles_fuel code start here ****************************************
if (!function_exists('getFuelChecks')) {
    function getFuelChecks(array $fuels, array $selectedFuels)
    {
        $allFuelsChecked =
            empty($selectedFuels) ||
            !array_filter($fuels, function ($fuel) use ($selectedFuels) {
                return in_array($fuel, $selectedFuels);
            });
        $checkedStatuses = ['all' => $allFuelsChecked ? 'checked' : ''];
        foreach ($fuels as $fuel) {
            $checkedStatuses[$fuel] = in_array($fuel, $selectedFuels) ? 'checked' : '';
        }

        return $checkedStatuses;
    }
}

$vehicles_fuel = ['Gas', 'Diesel', 'Electric', 'Hybrid', 'Flex Fuel', 'Gasoline','Biodiesel','Flex Fuel Vehicle','N/A'];
$mobileFuelChecks = getFuelChecks($vehicles_fuel, (array) $autoMobileFuelCheckbox);
$webFuelChecks = getFuelChecks($vehicles_fuel, (array) $autoWebFuelCheckbox);

// vehicles_fuel code End here ****************************************

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

$conditions = ['Certified', 'Preowned'];
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
@section('meta_description',
'Find the perfect car for your needs at ' .
route('home') .
' Shop new and used
cars, sell your car, compare prices, and explore financing options to find your dream best car today!' ??
app('globalSeo')['description'])
@endif
@if ($page->keyword)
@section('meta_keyword', $page->keyword)
@else
@section('meta_keyword', app('globalSeo')['keyword'])
@endif
@section('title')
    Autos | Best Used Cars for Sale - dreambestcar.com®
    {{--{{ $page->title . '| ' . app('globalSeo')['name'] }}--}}
@endsection
@endif
@endforeach

@section('gtm')
{!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])
@section('og_title')
    Autos | Best Used Cars for Sale - dreambestcar.com®
@endsection
@section('og_description', app('globalSeo')['og_description'])
@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])
@section('twitter_title')
    Autos | Best Used Cars for Sale - dreambestcar.com®
@endsection
@section('twitter_description', app('globalSeo')['twitter_description'])
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])

@push('css')
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.css"
                                                            integrity="sha512-MKxcSu/LDtbIYHBNAWUQwfB3iVoG9xeMCm32QV5hZ/9lFaQZJVaXfz9aFa0IZExWzCpm7OWvp9zq9gVip/nLMg=="
                                                            crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
<link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/15.7.1.nouislider.css">
<!-- @include('frontend.website.layout.css.auto_list_css') -->

<style>
    .body{
        height:100vh;
    }
    #merging-tooltips .noUi-tooltip {
        display: none;
    }

    #merging-tooltips .noUi-active .noUi-tooltip {
        display: block;
    }

    .noUi-connect,
    .noUi-origin {
        will-change: transform;
        position: absolute;
        z-index: 1;
        top: 0;
        left: 0;
        height: 100%;
        width: 96%;
        -ms-transform-origin: 0 0;
        -webkit-transform-origin: 0 0;
        -webkit-transform-style: preserve-3d;
        transform-origin: 0 0;
        transform-style: flat;
    }

    .c-1-color {
        background: red;
    }

    .c-2-color {
        background: yellow;
    }

    .c-3-color {
        background: green;
    }

    .c-4-color {
        background: blue;
    }

    .c-5-color {
        background: purple;
    }


    .item-card9-icons {
        position: absolute;
        top: 32px;
        right: 133px;
        z-index: 98;
    }


    /* toggle new/used css */

    .toggle-button-cover {
        display: table-cell;
        position: relative;
        width: 10px;
        ;
        height: 80px;
        box-sizing: border-box;
    }

    .button-cover {
        height: 100px;
        margin: 20px;
        background-color: #fff;
        box-shadow: 0 10px 20px -8px #c5d6d6;
        border-radius: 4px;
    }

    .button-cover,
    .knobs,
    .layer {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }

    .button {
        position: relative;

        width: 234px;
        height: 45px;
        margin-left: -25px;
        overflow: hidden;
    }

    .button.r,
    .button.r .layer {
        border-radius: 100px;
    }

    .button.b2 {
        border-radius: 2px;
    }

    .checkbox {
        position: relative;
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
        opacity: 0;
        cursor: pointer;
        z-index: 3;

    }

    .knobs {
        z-index: 2;
    }

    .layer {
        width: 100%;
        background-color: #ebf7fc;
        transition: 0.3s ease all;
        z-index: 1;
    }

    /* Button 10 */
    #button-10 .knobs:before,
    #button-10 .knobs:after,
    #button-10 .knobs span {
        position: absolute;
        top: 4px;
        width: 120px;
        height: 50px;
        font-size: 12px;
        font-weight: bold;
        text-align: center;

        padding: 12px 5px;
        border-radius: 2px;
        transition: 0.3s ease all;
    }

    #button-10 .knobs:before {
        content: "";
        width: 112px;
        left: 0px;
        top: 0px;
        background-color: darkcyan;
        ;
    }

    #button-10 .knobs:after {
        content: "New Car";
        right: -10px;
        color: #4e4e4e;

    }

    #button-10 .knobs span {
        display: inline-block;
        left: 0px;
        color: #fff;
        z-index: 1;
    }

    #button-10 .checkbox:checked+.knobs span {
        color: #4e4e4e;
    }

    #button-10 .checkbox:checked+.knobs:before {
        left: 122px;
        background: darkcyan;
    }

    #button-10 .checkbox:checked+.knobs:after {
        color: #fff;
    }

    #button-10 .checkbox:checked~.layer {
        background-color: #ebf7fc;
    }

    .input-container {
        position: relative;
        display: inline-block;
    }

    .dollar-sign {
        position: absolute;
        left: 10px;
        top: 18%;
        transform: translateY(-18%);
        color: #8b8a8a;
        font-size: 16px;
    }

    .dollar-sign-modal {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-18%);
        color: #8b8a8a;
        font-size: 16px;
    }

    .common_calculator-modal {

        font-size: 13px;
        padding-left: 23px;


    }

    .dollar-input {

        font-size: 13px;
        padding-left: 23px;


    }

    .dollar-input::placeholder {

        padding-left: 0px;


    }

    .accordion-button:not(.collapsed) {
        background: none !important;
        color: none !important;
        box-shadow: none !important;
    }

    .accordion-button:focus {
        border-color: none !important;
    }

    .nav-tabs .nav-link:hover:not(.disabled),
    .nav-tabs .nav-link.active {
        background: white !important;
        border-top: 0.03px solid gray;
        border-left: 0.03px solid gray;
        border-right: 0.03px solid gray;
        border-bottom: 2px solid white;
        color: black;
        border-top-right-radius: 4px;
        border-top-left-radius: 4px;
        border-bottom-right-radius: 1px;
        border-bottom-left-radius: 1px;

    }


    .custom-row {
        display: flex;
        width: 100%;
        flex-wrap: wrap;
        gap: 10px;
    }

    .custom-col {
        flex: 1 1 calc(50% - 5px);
        padding: 10px 10px 5px 10px;
        box-sizing: border-box;
    }

    @media (min-width: 450px) and (max-width: 600px) {
        .custom-row {
            display: flex;
            width: 380px;
            flex-wrap: wrap;
            gap: 10px;
            margin: 0 auto;

        }

        .custom-col {
            flex: 1 1 calc(50% - 5px);
            padding: 10px 10px 5px 10px;
            box-sizing: border-box;
        }

    }

    @media (min-width: 601px) and (max-width: 1000px) {
        .custom-row {
            display: flex;
            width: 450px;
            flex-wrap: wrap;
            gap: 10px;
            margin: 0 auto;

        }

        .custom-col {
            flex: 1 1 calc(50% - 5px);
            padding: 10px 10px 5px 10px;
            box-sizing: border-box;
        }

    }
    @media (min-width: 992px) and (max-width: 1279px) {
        .custom-row {
            display: flex;
            width: 100%;
            flex-wrap: wrap;
            gap: 10px;
            margin: 0 auto;

        }

        .custom-col {
            flex: 1 1 calc(50% - 5px);
            padding: 10px 10px 5px 5px;
            box-sizing: border-box;
            margin-left:-5px;
        }
        .accordion-button{
            font-size:14px !important;
        }
        .mobile-auto-top{
            margin-top:90px !important;
            margin-bottom:25px;
        }
        .filter-btn-styel-auto{
          display:none;  
        }
        .search-card{
            
        }
        .count-item{
            margin-right:40px !important;
            font-size:8px;
        }
        .count-item-miles{
            margin-right:35px !important;
            font-size:8px;
        }
        #web_search_any::placeholder {
        font-size: 11px; 
        }

    }






    #searchSecondFilterModelInput,
    #zipCodeInput {
        padding: 5px;
        width: 100%;
        /* Ensures inputs take up full width of their container */
        box-sizing: border-box;
        /* Ensures padding is included in the width */
    }






    .overlay-modal {
        position: fixed;
        width: 100%;
        height: 100%;
        display: block;
        background-color: rgba(218, 233, 232, 0.7);
        z-index: 99;
        content: "";
        left: 0;
        top: 0;
    }

    .selected div {
        border: 3px solid #000;
        /* or any style you want to indicate selection */
    }

    .color-box {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        width: cal(100% - 4px);
        height: 50px;
        border-radius: 3px;
    }

    .check-icon {
        display: none;
        font-weight: bold;
        font-size: 25px;
    }

    .selected .check-icon {
        display: block;
    }

    .nav-link-web.active {
        background: white !important;
        padding: 15px;
        border-top: 1px solid rgb(219, 219, 219);
        border-left: 1px solid rgb(219, 219, 219);
        border-right: 1px solid rgb(219, 219, 219);
        font-weight: 600;
    }


    #loading {
        background: url("{{ asset(' frontend/assets/images/loader_update.gif')}}") no-repeat center;
        background-size: 100px;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
    }

    .custom-col img {
        transition: all 0.3s ease;
    }

    .custom-col.active {
        border: 0.02px solid rgb(78, 152, 170);
        /* Change to your preferred border color */
        background: rgb(245, 245, 245);
        border-radius: 4px;
    }


    .loader {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .noUi-horizontal .noUi-tooltip {
        -webkit-transform: translate(-50%, 0);
        transform: translate(-50%, 0);
        left: 50%;
        bottom: 120%;
        font-size: 11px;
    }

    .open-close-all {
        background: rgb(245, 244, 244);
        border: 1px solid rgb(206, 206, 206);
        border-radius: 3px;
        height: 0;
        overflow: hidden;
        transition: height 3s ease-in-out, opacity 3s ease-in-out;
    }


    .common_calculator {
        width: 50px;
        height: 40px;
        text-align: center;
        border-radius: 5px;
        border: 1px solid gray;
    }

    .common_calculator:focus,
    .common_calculator.active {
        border: none;
        background-color: #222224;
        color: white;
        /* Light blue background when active */
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
        width: 30px;
        height: 30px;
        margin-top: -15px;

    }

    .swiper-button-next {
        right: 10px !important;
        border-radius: 0 !important;

    }

    .swiper-button-prev {
        left: 10px;

    }

    .swiper-pagination {
        bottom: 10px;

    }

    .DeleteFavorite
    {
        background: #080e1b;
    }
</style>
@endpush
@section('content')

<!--Breadcrumb-->
<div style="margin-top: 90px" class="mobile-auto-top">
    <div class="container">
        <div class="">

            <ol style="margin-top:32px" class="breadcrumb">
                <li style="color:black !important" class="breadcrumb-item"><a style="color:black !important"
                        href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active"><a style="color:black !important" href="{{ route('auto') }}">Cars For
                        Sale</a></li>

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
            <h3 style="margin-top:10px; font-weight:600">Cars For Sale</h3>
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
                                                class="form-control email" type="text" id="email"
                                                name="email" value="{{Auth()->check() ? Auth()->user()->email : ''}}">
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
                                                rows="4" cols="55">I am interested and want to know more about the Sport Utility, you have listed for $  on Dream Best car.
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
                                                    name="isEmailSend" style="cursor: pointer" checked> Email me price drops for this vehicle </p>

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
            <img style="width:728px; height:90px;" src="{{ asset('/dashboard/images/banners/' . $banners->image) }}"
                alt="Used cars for sale dealer banner image dream best" />
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
        <div  class="row">
            <!--Left Side Content-->
            <div  class="col-xl-3 col-lg-3 col-md-12 auto-filter-sidebar">
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
                                data-url="{{ route('auto', ['clear' => 'flush']) }}"
                                id="filter_usedCar_clear-web" class="cls">Clear ALL</a>

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

                    <div class="card-body">
                        <div style="display: flex; gap: 10px;" class="mb-2">
                            <div style="flex: 1; display: flex; flex-direction: column;">
                                <label for="searchSecondFilterModelInput">Distance</label>
                                <select class="form-control " name="web_radios" id="web_radios">
                                    <option value="">Nationwide</option>
                                    <option value="10">10 miles</option>
                                    <option value="25">25 miles</option>
                                    <option value="50">50 miles</option>
                                    <option value="75">75 miles</option>
                                    <option value="100">100 miles</option>
                                </select>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column;">
                                <label for="web_location">Zip Code</label>
                                <input class="form-control common_selector" name="weblocationNewInput"
                                    id="web_location" type="number" placeholder="Zip Code"
                                    value="">
                            </div>
                        </div>

                    </div>
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
                    <div class="px-4 py-3 border-bottom border-top">
                        <h4 class="mb-0">Price Range</h4>
                    </div>
                    <div style="width:100%; padding-bottom: 38px; padding-left:27px" class="pt-2 card-body pe-4">
                        <div class="text-center h6">
                            <input style="width:40px" type="text" id="price">
                            <span id="price-range-display" class="count-item">$0 – $150,000+</span>
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
                            <span id="mileage-range-display" class="count-item-miles">0 – 150,000+</span>
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
                            <span id="year-range-display" class="count-item">1985 – 2024</span>
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
                                                @foreach ($vehicles as $vehicle_information)
                                                <option value="{{ $vehicle_information->make_name }}"
                                                    {{-- ($make_data == $vehicle_information->make_name) ? 'selected' : (($webMakeFilterMakeInput == $vehicle_information->make_name) ? 'selected' : '') --}}
                                                    {{ $make_data == $vehicle_information->make_name ? 'selected' : '' }}
                                                    data-makeid="{{ $vehicle_information->id }}">
                                                    {{ $vehicle_information->make_name }}
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
                                                        class="pt-3 img-fluid" alt="{{ $bodyType }} Image">
                                                    <p style="text-align:center; margin-top:6px">
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


                    <div class="px-4 py-3 border-bottom">
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
                    </div>

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
                                id="flush-collapseOne-drive" class="accordion-collapse collapse show"
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
                                id="flush-collapseOne-fuel-web" class="accordion-collapse collapse show"
                                aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <div class="filter-product-checkboxs">
                                        <label class="mb-2 custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input common_selector"
                                                name="allWebFuellName" value="allWebFuelValue"
                                                id="selectWebAllFuelCheckbox" {{ $webFuelChecks['all'] }}>
                                            <span class="custom-control-label">All</span>
                                        </label>
                                        @forelse ($vehicles_fuel_other as $fuel_info)
                                        <label class="mb-2 custom-control custom-checkbox">
                                            <input type="checkbox"
                                                class="custom-control-input autoWebFuelCheckbox common_selector"
                                                name="checkboxweb11" value="{{ $fuel_info }}"
                                                {{ $webFuelChecks[$fuel_info] }}>
                                            <span class="custom-control-label">{{ $fuel_info == 'N/A' ? 'other' : $fuel_info }}</span>
                                        </label>
                                        @empty
                                        <div>There have no data</div>
                                        @endforelse
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

            <div  class="col-xl-9 col-lg-9 col-md-12 ">
                <!--Lists-->
                <div class="mb-0 " id="auto_ajax">

                </div>
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
            <div class="d-flex justify-content-between" style="background:rgb(243, 243, 243);">
                <a style="margin-top:20px; margin-bottom:15px; color:darkcyan; font-size:16px; font-weight:400;cursor: pointer; padding-left:15px; font-weight:500"
                    id="filter_usedCar_clear">Clear ALL</a>
                <button
                    style="margin-top:10px; margin-bottom:10px; color:darkcyan; font-size:16px; font-weight:400;cursor: pointer; padding-right:15px; font-weight:500"
                    type="button" class="btn" data-bs-dismiss="modal">Close</button>
            </div>

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
                                    <div style="flex: 1; display: flex; flex-direction: column;">
                                        <label style="font-weight:600; font-size:15px; color:rgb(82, 81, 81)"
                                            for="searchSecondFilterModelInput">Search Radius</label>
                                        <select class="form-control  mobile_common_selector" name="mobile_radios"
                                            id="mobile_radios">
                                            <option value="">Nationwide</option>
                                            <option value="10">10 miles</option>
                                            <option value="25">25 miles</option>
                                            <option value="50">50 miles</option>
                                            <option value="75">75 miles</option>
                                            <option value="100">100 miles</option>
                                        </select>
                                    </div>
                                    <div style="flex: 1; display: flex; flex-direction: column;">
                                        <label style="font-weight:600; font-size:15px; color:rgb(82, 81, 81)"
                                            for="mobile_location">Zip Code</label>
                                        <input class="form-control mobile_common_selector" type="text"
                                            id="mobile_location" name="mobilelocation" placeholder="Zip Code"
                                            value="">
                                    </div>
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
                                                2024</span>
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
                                                            @foreach ($vehicles as $vehicle_infor)
                                                            <option value="{{ $vehicle_infor->make_name }}"
                                                                {{ $make_data == $vehicle_infor->make_name ? 'selected' : '' }}
                                                                data-makeid="{{ $vehicle_infor->id }}">
                                                                {{ $vehicle_infor->make_name }}
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

                                            <div class="tab-pane fade" id="body-card" role="tabpanel" aria-labelledby="mobile-tab">
                                                <div class="p-3 mt-4">
                                                    <div class="custom-row d-flex flex-wrap justify-content-center">
                                                        <input type="hidden" value="" id="mobileBody">
                                                        @foreach ($bodyTypes as $bodyType => $image)
                                                        <input type="checkbox" class="mobile_body-checkbox" style="display:none"
                                                            value="{{ $bodyType }}" {{ in_array($bodyType, $mobileBody) ? 'checked' : '' }}>
                                                        <div class="custom-col {{ in_array($bodyType, $mobileBody) ? 'active' : '' }} d-flex flex-column justify-content-center align-items-center">
                                                            <a href="javascript:void(0)" class="common_selector mobile_shadow-set mobile_body_type_click"
                                                                data-Testvalue="{{ $bodyType }}">
                                                                <img src="{{ asset('/frontend/assets/images/' . $image) }}" class="pt-3 img-fluid"
                                                                    alt="{{ $bodyType }} Image">
                                                                <p style="text-align:center; margin-top:6px">{{ $bodyType }}</p>
                                                            </a>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>



                                    <div class="px-4 py-3 border-bottom border-top">
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
                                    </div>
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
                                                    All Transmissions
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
                                        class="">Drivetrain</p>
                                </div>
                                <div style="padding: 0; height: auto; margin-bottom: 15px" class="card-body">
                                    <div class="accordion accordion-flush" id="accordionFlushExample">
                                        <div class="accordion-item">
                                            <h4 style="border: 0.02px solid rgb(206, 205, 205);"
                                                class="accordion-header" id="flush-headingOne">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#flush-collapseOne-drivetrain"
                                                    aria-expanded="false" aria-controls="flush-collapseOne">
                                                    All Drivetrain
                                                </button>
                                            </h4>
                                            <div style="border-left: 1px solid rgb(206, 205, 205); border-right: 1px solid rgb(206, 205, 205); border-bottom: 1px solid rgb(206, 205, 205); "
                                                id="flush-collapseOne-drivetrain" class="accordion-collapse collapse"
                                                aria-labelledby="flush-headingOne"
                                                data-bs-parent="#accordionFlushExample">
                                                <div style="height:360px; overflow-y:auto" class="accordion-body">
                                                    <div class="filter-product-checkboxs">
                                                        <label class="mb-2 custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="custom-control-input common_selector"
                                                                name="allMobileDriveTrainlName"
                                                                value="allMobileDriveTrainValue"
                                                                id="selectAllMobileDriveTrain"
                                                                {{ $webDriveTrainChecks['all'] }}>
                                                            <span class="custom-control-label">All</span>
                                                        </label>
                                                        @foreach ($driveTrains as $driveTrain)
                                                        <label class="mb-2 custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="custom-control-input autoMobileDriveTrainCheckbox common_selector"
                                                                name="checkbox{{ $driveTrain }}"
                                                                value="{{ $driveTrain }}"
                                                                {{ $webDriveTrainChecks[$driveTrain] }}>
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
                                                    data-bs-toggle="collapse" data-bs-target="#flush-collapseOne-fuel"
                                                    aria-expanded="false" aria-controls="flush-collapseOne">
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
                                                                {{ $mobileFuelChecks['all'] }}>
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

            <div class="modal-footer fixed-footer">
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
            </div>
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
                $('#webModelFilterInput').empty().append(
                    '<option value="">Choose Model</option>');

                $.each(res, function(index, item) {
                    var option = "<option value='" + item.model_name + "' data-id='" + item
                        .id +
                        "'>" + item.model_name + "</option>";
                    $('#webModelFilterInput').append(option);
                });
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
                    $('#secondFilterModelInputNew').empty();
                    $('#secondFilterModelInputNew').append(
                        '<option value="">Choose Model</option>')
                    $.each(res, function(index, item) {

                        var option = "<option value='" + item.model_name +
                            "' data-id='" +
                            index + "'>" + item.model_name + "</option>"
                        $('#secondFilterModelInputNew').append(option);
                    });
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
<!-- Place this script at the end of the body or in a <head> with defer attribute -->


@include('frontend.website.layout.js.multiSelectDropdwn_js')
@include('frontend.website.layout.js.auto_list_js')
@include('frontend.reapted_js')
<!-- @include('frontend.website.layout.js.price_ranger_slider') -->
@endpush
