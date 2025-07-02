<!doctype html>
<html lang="en" dir="ltr">

<head>
    <!-- End Google Tag Manager -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    @stack('head')
    <meta name="description" content="@yield('meta_description')">
    <meta property="og:site_name" content="@yield('og_site_name')">
    <meta property="og:type" content="@yield('og_type')">
    <meta property="og:title" content="@yield('og_title')">
    <meta property="og:url" content="@yield('og_url')">
    <meta property="og:description" content="@yield('og_description')">

    <meta property="og:image" content="@yield('og_img')">
    <meta property="fb:app_id" content="@yield('app_id')">

    <meta name="twitter:site" content="@yield('twitter_site')">
    <meta name="twitter:card" content="@yield('twitter_card')">
    <meta name="twitter:title" content="@yield('twitter_title')">
    <meta name="twitter:description" content="@yield('twitter_description')">

    <meta name="twitter:image" content="@yield('twitter_image')">
    <title>@yield('title')</title>
    @if (Request::route()->getName() === 'auto.details')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    @else
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    @endif

    @yield('hedo')

    {{-- <!-- Title -->
	<!-- <meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="HandheldFriendly" content="True">
		<meta name="Duplex VehiclesOptimized" content="320">
		<meta name="robots" content="index, follow"> --> --}}
    {{--
	<!-- <meta property="og:locale" content="@yield('og_locale')"> -->
	<!-- <meta name="twitter:creator" content="@yield('twitter_creator')"> -->
	<!-- Title --> --}}

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="@yield('meta_keyword')">
    <link rel="icon" href="{{ asset('frontend/assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontend/assets/images/favicon.png') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />


    <!-- Bootstrap Css -->
    <link id="style" href="{{ asset('frontend') }}/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Dashboard Css -->
    <link href="{{ asset('frontend') }}/assets/css/style.css" rel="stylesheet">
    <link href="{{ asset('frontend') }}/assets/css/frontend.css" rel="stylesheet">
    <link href="{{ asset('frontend') }}/assets/css/plugins.css" rel="stylesheet">
    <link href="{{ asset('frontend') }}/assets/css/skeleton.css" rel="stylesheet">

    <!-- Font-awesome  Css -->
    <link href="{{ asset('frontend') }}/assets/css/icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <!-- Link Swiper's CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/header.css') }}">

    @stack('css')
</head>
