@extends('frontend.website.layout.app')

@section('meta_description', app('globalSeo')['description'])
@section('meta_keyword', app('globalSeo')['keyword'])
@section('title', 'Account | ' . app('globalSeo')['name'])
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
<style>
    .account-settings .user-profile {
        margin: 0 0 1rem 0;
        padding-bottom: 1rem;
        text-align: center;
    }

    .account-settings .user-profile .user-avatar {
        margin: 0 0 1rem 0;
    }

    .account-settings .user-profile .user-avatar img {
        width: 100px;
        height: 100px;
        border-radius: 100px;
    }

    .account-settings .user-profile h5.user-name {
        margin: 0 0 0.5rem 0;
    }

    .account-settings .user-profile h6.user-email {
        margin: 0;
        font-size: 0.8rem;
        font-weight: 400;
        color: gray;
    }

    .account-settings .about {
        margin: 2rem 0 0 0;
        text-align: center;
    }

    .account-settings .about h5 {
        margin: 0 0 15px 0;
        color: #007ae1;
    }

    .account-settings .about p {
        font-size: 0.825rem;
    }

    .form-control {
        border: 1px solid #cfd1d8;
        border-radius: 5px;
        font-size: 0.825rem;
        background: #ffffff;
        color: #2e323c;
    }

    .card {
        background: #ffffff;
        border-radius: 5px;
        border: 0;
        margin-bottom: 1rem;
    }
</style>

<div class="container account-data" style="margin-top:160px; margin-bottom:60px">
    <div class="row gutters message-load">
        <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="account-settings">
                        @php
                        $user = Auth::user();
                        @endphp
                        <div class="user-profile">
                            <div class="user-avatar mt-2">
                                <img src="{{ $user->image ? asset('frontend/assets/images/').'/'.$user->image : asset('/frontend/assets/images/profile.png') }}" alt="User Image">
                            </div>
                            <h5 class="user-name">{{ $user->name }}</h5>
                            <h6 class="user-email">{{ $user->email }}</h6>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row gutters">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <h4 class="mb-5 ms-3">Account Information</h4>
                        </div>
                        <form action="{{ route('buyer.profile.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input
                                        type="text"
                                        placeholder="First Name"
                                        label="First Name"
                                        name="fname"
                                        value="{{ $user->fname ?? '' }}" />
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input
                                        type="text"
                                        placeholder="Last Name"
                                        label="Last Name"
                                        name="lname"
                                        value="{{ $user->lname ?? '' }}" />
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input
                                        type="email"
                                        placeholder="Email"
                                        label="Email address"
                                        name="email"
                                        value="{{ $user->email ?? '' }}" />
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input
                                        type="tel"
                                        placeholder="Cell Number"
                                        label="Cell Number"
                                        name="phone"
                                        data="telephoneInput"
                                        value="{{ $user->phone ?? '' }}" />
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input
                                        type="text"
                                        placeholder="Address"
                                        label="Address"
                                        name="address"
                                        value="{{ $user->address ?? '' }}" />
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input
                                        type="text"
                                        placeholder="City"
                                        label="City"
                                        name="city"
                                        value="{{ $user->city ?? '' }}" />
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input
                                        type="number"
                                        placeholder="ZIP Code"
                                        label="Zip Code"
                                        name="zip"
                                        value="{{ $user->zip ?? '' }}" />
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input type="file" style="width:40% !important" label="Upload Image" name="image" class="" />
                                </div>

                            </div>



                            <div class="row gutters">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <x-primary-button class="ms-3" style="float:left; background:darkcyan; margin-top:20px; margin-bottom:0px">
                                        Profile Update
                                    </x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Modal Show -->
    <div class="modal fade" id="requiredModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-md" style="margin-top:-140px">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Please check your email and verify your email to unlock your profile</h4>
                    <a href="/" style="text-decoration: underline; color:red">Go back</a> <br />
                    <span style="float:right">Didn't get the code? <a href="{{ route('buyer.again-verify.email') }}" style="text-decoration: underline;">Send again</a></span>
                </div>
            </div>
        </div>
    </div>
    {{-- End Required Modal --}}
</div>

@endsection

@push('js')


@if (Auth::user()->email_verified_at == null)
<script>
    $(document).ready(function() {


        $('.item1-links a').on('click', function() {
            $('.item1-links a').removeClass('active');
            $(this).addClass('active');
        });
    });

    $(document).ready(function() {
        $('.telephoneInput').inputmask('(999) 999-9999');
        $('#requiredModal').modal('show');
    })
</script>
@endif


@include('frontend.reapted_js')
@endpush
