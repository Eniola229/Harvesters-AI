<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- SEO Meta -->
    <meta name="description" content="Harvesters AI, Church Ai, Church, Harvesters International Christain Centere." />
    <meta name="keyword" content="Harvesters AI, Church Ai, Church, Harvesters International Christain Center" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    

    <!--! BEGIN: Apps Title-->
    <title>{{ config('app.name', 'Harvesters AI') }} || @yield('title', 'Harvesters International Christain Center')</title>
    <!--! END:  Apps Title-->


    <!--! BEGIN: Favicon-->
      <link rel="shortcut icon" type="image/x-icon" href="{{ asset('https://harvestersng.org/wp-content/uploads/2022/04/cropped-Harvesters-Logo.jpg') }}" />
    <!--! END: Favicon-->

    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <!--! END: Bootstrap CSS-->

    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}" />
    <!--! END: Vendors CSS-->

    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}" />
    <!--! END: Custom CSS-->


    @stack('styles')
</head>
<body>
