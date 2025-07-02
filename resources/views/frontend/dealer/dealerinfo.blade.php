@extends('frontend.website.layout.app')
@section('meta_description', app('globalSeo')['description'])
@section('meta_keyword', app('globalSeo')['keyword'])
@section('title', str_replace('_', ' ', $dealer_name) . ' | ' . app('globalSeo')['name'])
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
@section('content')


<!--Breadcrumb-->
<section>
    <div class="bannerimg cover-image bg-background3" data-image-src="../assets/images/banners/banner2.jpg">
        <div class="header-text mb-0">
            <div class="container">
                <div class="text-center text-white">
                    <h1 class="favorite-header">{{str_replace('_', ' ', $dealer_name)}}</h1>

                    <ol class="breadcrumb text-center">
                        <li   class="favorite-bc"><a style="color:white" href="{{ route('home') }}">Home<span style="margin-left:4px; margin-right:4px;">/</span> </a></li>
                        <li  class="favorite-bc"><a style="color:white" href="{{ route('frontend.find.dealership') }}">Dealership<span style="margin-left:4px; margin-right:4px;">/</span></a></li>
                        <li  class="favorite-bc"><a style="color:white" href="javascript:void(0);">Listing</a></li>

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
                                                name="first_name">
                                            <span id="first_name_error" class="text-danger" role="alert"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 " style="margin-top:20px">
                                        <div class="form-group ">
                                            <input style="border-radius:5px; color:black" placeholder="Last Name*"
                                                class="form-control lname" type="text" id="last_name"
                                                name="last_name">
                                            <span id="last_name_error" class="text-danger" role="alert"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">
                                            <input style="border-radius:5px; color:black" placeholder="E-mail*"
                                                class="form-control email" type="text" id="email"
                                                name="email">
                                            <span id="email_error" class="text-danger" role="alert"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                        <div class="form-group ">
                                            <input style="border-radius:5px; color:black"
                                                class="form-control phone telephoneInput" type="text"
                                                placeholder="cell" id="phone" name="phone">

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
                                                <div
                                                    style="background-color:white; width:50%;  margin-right:10px; text-align:center; padding-top:7px; font-weight:600;  margin-top:2px; margin-left:3px; height:35px; border-radius:5px">
                                                    {{ app('mathcaptcha')->label() }}
                                                    {{-- {{app('mathcaptcha')->reset()}} --}}

                                                </div>
                                                <div>
                                                    <input
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
                                                    id="tradeChecked" style="cursor: pointer"> Do you have a
                                                trade-in?</p>
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
                            consent to be contacted by bestdreamcar.com and the dealer selling
                            this car at any telephone number I provide, including, without
                            limitation, communications sent via text message to my cell
                            phone or communications sent using an autodialer or prerecorded
                            message. This acknowledgment constitutes my written consent to
                            receive such communications. I have read and agree to the Terms
                            and Conditions of Use and Privacy Policy of bestdreamcar.com. This
                            site is protected by reCAPTCHA and the Google Privacy Policy and
                            Terms of Service apply.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Check availibility modal close-->

    <div class="container">
        <div class="row mt-5">
            <h3 class="mb-4 mt-5">{{ $user->name ?? 'Dealer' }} Listings</h3>
            @forelse($listings as $list)
                @php
                    $image_obj = $list->local_img_url;
                    $images = explode(',', $list->local_img_url);
                    $image = str_replace(['[', "'"], '', $images[0]);
                @endphp
                <div class="col-lg-4 col-md-6 col-xl-3 col-xxl-3 col-sm-6 col-xs-12">

                    <div class="card">
                        <div class="item-card9-img">
                            <div class="item-card9-img">
                                <div class="item-card9-imgs">
                                    @php
                                        $vin_string_replace = str_replace(' ','',$list->vin);
                                        $route_string = str_replace(' ','',$list->year.'-'.$list->make.'-'.$list->model.'-in-'.$list->dealer_city.'-'.$list->dealer_state);
                                    @endphp

                                    @if ($image_obj != '' && $image_obj != '[]')

                                    <a href="{{ route('auto.details',['vin' =>$vin_string_replace, 'param' => $route_string]) }}" class="text-dark"></a>
                                        <img src="{{ asset('frontend/' . $image) }}" alt="img" style="width: 100%; height:200px;" class="auto-ajax-photo">



                                    @else

                                            <img src="{{ asset('frontend/uploads/NotFound.png') }}" alt="img" style="width: 100%; height:100%">

                                    @endif
                                </div>
                                <div class="item-card9-icons">
                                    <a href="javascript:void(0);" class="item-card9-icons1 wishlist" data-productid="{{ $list->id }}">
                                        <i class="fa fa-heart-o" style="color: red"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="item-card9-icons">
                                <a href="javascript:void(0);" class="item-card9-icons1 wishlist" data-productid="{{ $list->id }}">
                                    <i class="fa fa-heart-o" style="color: red"></i>
                                </a>
                            </div>
                        </div>

                        <div class="card border-0 mb-0">
                            <div class="card-body">
                                <div class="item-card9">
                                    @php


                                    $vin_string_replace = str_replace(' ','',$list->vin);
                                    $route_string = str_replace(' ','',$list->year.'-'.$list->make.'-'.$list->model.'-in-'.$list->dealer_city.'-'.$list->dealer_state)
                                @endphp

                                    <a href="{{ route('auto.details',['vin' =>$vin_string_replace, 'param' => $route_string]) }}" class="text-dark">
                                        <h4 class="font-weight-semibold mt-1">{{ $list->year . ' ' . $list->make . ' ' . $list->model}}</h4>
                                    </a>
                                    <p class="mb-1">@if(in_array($list->type, ['Preowned', 'Certified Preowned']))Used @else {{ $list->type }}
                                        @endif</p>


                                    <div style="height: 25px" class="d-flex">
                                        <h4 class="me-3" style="font-weight:600">{{ $list->price_formate}}</h4>
                                        <p style="color:black; font-weight:600; font-size:12px; margin-top:2px">${{ $list->payment_price}}/mo*</p>
                                    </div>



                                    <div class="item-card9-footer d-sm-flex">
                                        <div>
                                            <a href="javascript:void(0);" class="me-2 d-inline-block" title="Kilometrs"><i class="fa fa-road text-muted me-1"></i>{{ $list->miles }} miles</a>

                                        </div>

                                    </div>
                                    <div style="margin-top:7px" class="float:left">
                                        <button
                                            style="background:rgb(68, 29, 70); padding-left:15px; padding-right:15px; border-radius:7px; color:white;"
                                            id="check_availability" type="button"
                                            data-inventory_id="{{ $list->id }}"
                                            data-user_id="{{ $list->user_id }}">Check Availibility</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer pt-4 pb-4 pe-4 ps-4">
                                @php
                                $cus_dealer = explode(' in ',$list->dealer->name)[0];
                                @endphp
                                <div class="item-card9-desc mb-2">
                                    <a href="javascript:void(0);" class="me-4 d-inline-block">
                                        <span class=""><i class="fa fa-map-marker text-muted me-1"></i>{{ $cus_dealer }}
                                            <br>{{ $list->dealer->address }}, {{ $list->zip_code }}</span>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
            <div style="border-radius:5px; margin-bottom:35px" class="container col-12 border text-center">
                <div class="mx-auto">
                    <img style="margin-top:50px" width="4%" class="img-responsive-fav" src="{{'/frontend/assets/images/model.png'}}" alt="No Favorites">
                    <h4 class="mt-4 mb-2">No Dealer Listings.</h4>
                    <p class="mb-1">Browse local inventory and find your</p>
                    <p class="mb-4">next car</p>
                    <a href="{{ route('auto') }}" >
                        <button style="background: black; color:white; padding: 8px 65px 8px 65px; border:1px solid black; border-radius:18px; margin-bottom:30px; font-size:15px">Browse Local Inventory</button>
                    </a>

                </div>
            </div>
            @endforelse
        </div>
        <div class="clearfix"></div>
        <div class="center-block text-center mb-4">
            <div class="custom-pagination" style="display: flex; justify-content: flex-end">
                <ul class="pagination">
                    @if ($listings->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $listings->previousPageUrl() }}">Previous</a></li>
                    @endif

                    @php
                        $currentPage = $listings->currentPage();
                        $lastPage = $listings->lastPage();
                        $maxPagesToShow = 5;
                        $startPage = max($currentPage - floor($maxPagesToShow / 2), 1);
                        $endPage = min($startPage + $maxPagesToShow - 1, $lastPage);
                    @endphp

                    @if ($startPage > 1)
                        <li class="page-item"><a class="page-link" href="{{ $listings->url(1) }}">1</a></li>
                        @if ($startPage > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endif

                    @for ($page = $startPage; $page <= $endPage; $page++)
                        @if ($page == $currentPage)
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $listings->url($page) }}">{{ $page }}</a></li>
                        @endif
                    @endfor

                    @if ($endPage < $lastPage)
                        @if ($endPage < $lastPage - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ $listings->url($lastPage) }}">{{ $lastPage }}</a></li>
                    @endif

                    @if ($listings->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $listings->nextPageUrl() }}">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </div>
        </div>

    </div>



@endsection

@push('js')
    @include('frontend.reapted_js')
@endpush
