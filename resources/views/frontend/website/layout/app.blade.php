@include('frontend.website.layout.header')
@include('frontend.website.layout.topbar')
@include('frontend.website.layout.sidebar')

@stack('css')
<body>
    <div id="body-overlay" class="body-overlay"></div>
    @yield('content')
    <!-- Location Access Modal -->
</body>
@include('frontend.website.layout.footer')
@stack('js')
