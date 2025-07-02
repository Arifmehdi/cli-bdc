<?php
use Illuminate\Support\Facades\Cookie;
$zipcodeData = request()->cookie('zipcode') ?? '';

?>
{{-- <x-favourite-auth-register/> --}}

<!--Footer Section-->
<section>
    <footer class="bg-dark-purple text-white">
        <div class="footer-main">
            <div class="container">
                <h6 class="mb-1 footer-tit" style="margin:0">{{ $footer_content->title }}</h6>
                <p class="footer-para" style="margin-bottom:25px">{!! $footer_content->description !!}</p>
                <div class="row">
                    <div class="col-lg-2 col-md-2">
                        <h6 class="footer-about">About</h6>
                        <ul class="list-unstyled mb-0 about-submenu">
                            {{-- <li><a href="{{ route('home') }}">Home</a></li> --}}
                            @foreach ($footer_menus as $footer_menu)
                                @if ($footer_menu->column_position == 1)
                                    <li><a class="fs-16"
                                            href="{{ $footer_menu->route_url != null ? route($footer_menu->route_url) : url($footer_menu->slug) }}">{{ $footer_menu->name }}</a>
                                    </li>
                                @endif
                            @endforeach

                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <h6 class="footer-Products">Products</h6>

                        <ul class="list-unstyled mb-0 footer-Products-submenu">
                            {{-- <li><a href="{{route('auto')}}">Used</a></li> --}}
                            @foreach ($footer_menus as $footer_menu)
                                @if ($footer_menu->column_position == 2)
                                    <li><a class="fs-16"
                                            href="{{ $footer_menu->route_url != null ? route($footer_menu->route_url) : url($footer_menu->slug) }}" rel="nofollow">{{ $footer_menu->name }}</a>
                                    </li>
                                @endif
                            @endforeach
                            <!-- <li><a href="{{ route('sitemap') }}">Sitemap</a></li> -->
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-3">
                        <h6 class="footer-Resources">Research</h6>

                        <ul class="list-unstyled mb-0 footer-Resources-submenu">
                            @foreach ($footer_menus as $footer_menu)
                                @if ($footer_menu->column_position == 3)
                                    <li><a class="fs-16"
                                            href="{{ $footer_menu->route_url != null ? route($footer_menu->route_url) : url($footer_menu->slug) }}" rel="nofollow">{{ $footer_menu->name }}</a>
                                    </li>
                                @endif
                            @endforeach
                            {{-- <li><a href="{{ route('frontend.news.page')}}">News</a></li>
									<li><a href="{{ route('frontend.faq')}}">FAQ</a></li>
									<li><a href="{{ route('frontend.terms.condition')}}">Terms and Conditions</a></li> --}}
                        </ul>

                    </div>
                    <div class="col-lg-4 col-md-4">
                        <h6 class="footer-work">Beyond Cars</h6>

                        <ul class="list-unstyled mb-0 footer-work-submenu">
                            @foreach ($footer_menus as $footer_menu)
                                @if ($footer_menu->column_position == 4)
                                    <li>
                                        <a class="fs-16" @if ($footer_menu->name == 'Find Dealership') rel="nofollow" @endif
                                            href="{{ $footer_menu->route_url != null ? route($footer_menu->route_url) : url($footer_menu->slug) }}">
                                            {{ $footer_menu->name }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-dark-purple text-white p-0">
            <div class="container">
                <div class="row d-flex">
                    <div class="col-lg-12 col-sm-12 mt-3 mb-3 text-center " id="footer_copyright">

                        {!! $footer_content->copyright !!}
                    </div>
                </div>
            </div>
        </div>
    </footer>
</section>

<!-- zip location modal added -->
<div id="locationModal">
    <div id="locationModalContent">
        <p><span><i class="fa fa-map-marker"></i></span> <small> Best Dream Car requests your location to offer
                personalized results and improve your experience.</small></p>
        <p>Would you like to allow location access?</p>
        <button id="denyLocation">Don't Allow</button>
        <button id="allowLocation" style="background-color: darkcyan">Allow</button>
    </div>
</div>


<!--Footer Section-->

<!-- Back to top -->
<a href="#top" id="back-to-top"><i class="fa fa-rocket"></i></a>
<!-- Google Tag Manager -->

<script>
    @yield('gtm')
</script>

<!-- JQuery js-->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="{{ asset('frontend') }}/assets/plugins/bootstrap/js/3.7.1.jquery.min.js"></script>
<script src="{{ asset('frontend') }}/assets/js/vendors/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Bootstrap js -->
<script src="{{ asset('frontend') }}/assets/plugins/bootstrap/js/popper.min.js"></script>
<script src="{{ asset('frontend') }}/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<!--JQueryVehiclerkline Js-->
<script src="{{ asset('frontend') }}/assets/js/vendors/jquery.sparkline.min.js"></script>

<!-- Circle Progress Js-->
<script src="{{ asset('frontend') }}/assets/js/vendors/circle-progress.min.js"></script>

<!-- Star Rating-1 Js-->
<script src="{{ asset('frontend') }}/assets/plugins/ratings-2/jquery.star-rating.js"></script>
<script src="{{ asset('frontend') }}/assets/plugins/ratings-2/star-rating.js"></script>

<!--Owl Carousel js -->

<script src="{{ asset('frontend') }}/assets/plugins/owl-carousel/owl.carousel.js"></script>

<!--Horizontal Menu-->
<script src="{{ asset('frontend') }}/assets/plugins/horizontal-menu/horizontal.js"></script>

<!--Counters -->
<script src="{{ asset('frontend') }}/assets/plugins/counters/counterup.min.js"></script>
<script src="{{ asset('frontend') }}/assets/plugins/counters/waypoints.min.js"></script>
<script src="{{ asset('frontend') }}/assets/plugins/counters/numeric-counter.js"></script>

<!--JQuery TouchSwipe js-->
<script src="{{ asset('frontend') }}/assets/js/jquery.touchSwipe.min.js"></script>

<!--Select2 js -->
<!-- <script src="{{ asset('frontend') }}/assets/plugins/select2/select2.full.min.js"></script> -->
<!-- <script src="{{ asset('frontend') }}/assets/js/select2.js"></script> -->

<!-- Cookie js -->
<script src="{{ asset('frontend') }}/assets/plugins/cookie/jquery.ihavecookies.js"></script>
<script src="{{ asset('frontend') }}/assets/plugins/cookie/cookie.js"></script>

<!-- Sticky Js-->
<script src="{{ asset('frontend') }}/assets/js/sticky.js"></script>

<!-- Custom scroll bar Js-->
<script src="{{ asset('frontend') }}/assets/plugins/scroll-bar/jquery.mCustomScrollbar.js"></script>



<!-- Owl Carousel Js-->
<script src="{{ asset('frontend') }}/assets/js/owl-carousel.js"></script>

<!-- Typewritter Js-->
<script src="{{ asset('frontend') }}/assets/js/typewritter.js"></script>

<!--ThemeColors JS -->
<script src="{{ asset('frontend') }}/assets/js/themeColors.js"></script>

<!-- Custom Js-->
<script src="{{ asset('frontend') }}/assets/js/custom.js"></script>

<!-- SwitcherCustom Js-->
<script src="{{ asset('frontend') }}/assets/js/switcher-custom.js"></script>

{{-- <script src="{{ asset('frontend') }}/assets/plugins/boot-slider/boot-slider.min.js"></script>
		<script src="{{ asset('frontend') }}/assets/js/boots-slider.js"></script> --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
<script src="{{ asset('frontend/assets/js/calculate.js') }}"></script>
<script src="{{ asset('js/lazyloader-api.js') }}"></script>

{{-- share js --}}

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('frontend/assets/js-link/js/jquery-confirm.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/hammerjs"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('frontend') }}/assets/js/frontend.js"></script>

@php
    $withoutHttp = str_replace('http://', '', route('home'));
@endphp
<script type="application/ld+json">
    {
        "name": "{{$withoutHttp}}",
        "@context": "http://www.schema.org",
        "url": "{{route('home')}}",
        "@type": "WebSite"
    }
</script>
<script>
    // alert(zipcodeData);
    $(document).ready(function() {
        setTimeout(function() {
            var zipcodeData = @json($zipcodeData);
            var isAutoPage = "{{ Request::route()->getName() === 'auto' ? 'true' : 'false' }}";
            var isNewCarPage = "{{ Request::route()->getName() === 'car.new' ? 'true' : 'false' }}";
            var isAutoNewPage = "{{ Request::route()->getName() === 'auto.new' ? 'true' : 'false' }}";
            var isHomePage = "{{ Request::route()->getName() === 'home' ? 'true' : 'false' }}";
            var isCarUsedPage = "{{ Request::route()->getName() === 'car.used' ? 'true' : 'false' }}";
            var zip = getUrlParameter('zip');
            //var zip = $('#web_location').val();

            if (isAutoPage == 'true' || isAutoNewPage == 'true' || isHomePage == 'true' ||
                isNewCarPage == 'true' || isCarUsedPage == 'true') {
                if (!zipcodeData) {
                    showLocationModal();
                }
            }
            if (isAutoPage == 'true') {
                if (!zip) {
                    showLocationModal();
                } else {
                    hideLocationModal();
                }
            }
            if (isAutoNewPage == 'true') {
                if (!zip) {
                    showLocationModal();
                } else {
                    hideLocationModal();
                }
            }
        }, 5000); // Delay of 1000 milliseconds (1 second)
    });

    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    function showLocationModal() {
        $('#locationModal').show();
    }

    function hideLocationModal() {
        $('#locationModal').hide();

    }

    $('#allowLocation').click(function(e) {
        e.preventDefault();
        hideLocationModal();
        $.ajax({
            url: '/get-location',
            type: 'GET',
            success: function(response) {
                updateUrlParameter('zip', response.zipcode);
                window.location.reload();

                function updateUrlParameter(param, value) {
                    var currentUrl = window.location.href;
                    var url = new URL(currentUrl);
                    var searchParams = new URLSearchParams(url.search);
                    searchParams.set(param, value);
                    url.search = searchParams.toString();
                    window.history.replaceState(null, '', url.toString());
                }
                console.log(response);
                if (response.error) {
                    alert(response.error);
                } else {
                    console.log(response);
                    // Process the location data as needed
                }
            },
            error: function(xhr) {
                alert('Unable to retrieve location');
            }
        });

    });

    $('#denyLocation').click(function() {
        hideLocationModal();
        // Handle location access denied
    });


    // Settings
    $('.settings-bar').on('click', function(e) {
        e.preventDefault();
        var settingsSidebar = $('.settings-sidebar-area');
        settingsSidebar.toggleClass('active');
        $('.body-overlay').toggleClass('active', settingsSidebar.hasClass('active'));

    });
    $('#body-overlay').on('click', function(e) {
        e.preventDefault();
        $('.settings-sidebar-area').removeClass('active');
        $('.body-overlay').removeClass('active');
    });


    //automatic login alert model show  ajax code start here
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function showModal(ipAddress) {
        $('#favourite_add_auth_modal').modal('show');
        localStorage.setItem('modal_shown_' + ipAddress, true);
    }

    function checkAndShowModal() {
        if ($('.modal.show').length === 0) {
            $.ajax({
                url: "{{ route('get.ipaddress') }}",
                type: 'GET',
                success: function(response) {
                    var ipAddress = response.ip;
                    var modalShown = localStorage.getItem('modal_shown_' + ipAddress);
                    if (!modalShown) {
                        showModal(ipAddress);
                        //refreshCaptcha();
                        console.log('Modal shown for IP:', ipAddress);
                    } else {
                        console.log('Modal was shown before for IP:', ipAddress);
                        setTimeout(function() {
                            console.log('Removing item from localStorage for IP:', ipAddress);
                            localStorage.removeItem('modal_shown_' + ipAddress);
                        }, 1200000); // 2.5 minutes in milliseconds
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching IP address:", error);
                }
            });
        }
    }

    $(document).ready(function() {
        var userId = "{{ Auth::id() }}";
        var isHomePage = "{{ Request::route()->getName() === 'home' ? 'true' : 'false' }}";
        if (userId === "") {
            if (isHomePage === 'false') {
                // Call the function after an initial delay
                setTimeout(function() {
                    checkAndShowModal();

                    // // Call the function every 2 minutes (120000 milliseconds)
                    setInterval(function() {
                        checkAndShowModal();
                    }, 1200000); // 20 minutes in milliseconds
                }, 60000); // Initial delay of 1 min before the first call
            } else {
                console.log('this route is home');
            }
        }
    });



    // signup model customization and action route here

    function GoBack(value) {
        if (value == 5) {
            $('#automatic_rowOne').css('display', 'block');
            $('.tab-5').css('display', 'none');
            $('.error_email').text('');
        }
        if (value == 4) {
            $('.tab-3').css('display', 'block');
            $('.tab-4').css('display', 'none');
        }
        if (value == 3) {
            $('.tab-2').css('display', 'block');
            $('.tab-3').css('display', 'none');
        }
        if (value == 2) {

            $('.tab-1').css('display', 'block');
            $('.tab-2').css('display', 'none');
            $('.error_email').text('');
        } else {
            $('.previous_link').prop('disabled', true);
        }
    }


    //  create new account for automatic load page
    $(document).ready(function() {
        $('#automatic_SignUpForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            $('#automatic_SignUpBtn').text('Loading....');
            $.ajax({
                url: "{{ route('create.account') }}",
                type: 'post',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status == 'error') {
                        var error = res.errors;
                        if (error.password) {
                            $('.sign_up_password').text(error.password);
                            $('#automatic_SignUpBtn').text('Create Account');
                        }
                        if (error.mathcaptcha) {
                            $('#automatic_Smathcaptcha').text(error.mathcaptcha);
                            $('#automatic_SignUpBtn').text('Create Account');
                        }
                    }
                    if (res.status == 'success') {

                        toastr.success(res.message);
                        // window.location.href = "{{ route('buyer.login') }}";
                        window.location.reload();
                    }
                }
            });
        });

    });

    $(document).ready(function() {
        // check mail
        $('#automatic_CheckEmail').on('click', function(e) {
            e.preventDefault();
            var email = $('#automatic_email').val();
            $.ajax({
                url: "{{ route('check.email') }}",
                type: "post",
                data: {
                    email: email
                },
                success: function(res) {

                    if (res.email) {
                        $('.error_email').text(res.email);
                    }
                    if (res.status == 2) {

                        toastr.warning(res.message);
                    }
                    if (res.status == 1) {
                        // $('.tab-1').addClass('html_hide');
                        $('.tab-1').css('display', 'none');
                        $('.previous_link').css('display', 'block');
                        $('.tab-2').css('display', 'block');
                        $('.email_session').text(res.email);
                        $('.email_session_one').val(res.email);
                    }
                    if (res.status == 0) {
                        $('#automatic_rowOne').css('display', 'none');
                        $('.tab-5').css('display', 'block');
                        $('.previous_link').css('display', 'block');
                        $('#automatic_email_session_two').text(res.email);
                        $('.email_session_two').val(res.email);
                    }
                }
            })
        });

        // login check
        $('#automatic_login_byuer').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);

            $('#automatic_loginBtn').text('Loading....');

            $.ajax({
                url: "{{ route('buyer.login') }}",
                type: "post",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {

                    if (res.status == 'error') {
                        var error = res.errors;
                        if (error.password) {
                            $('.error_password').text(error.password);
                            $('#automatic_loginBtn').text('Login');
                        }
                        if (error.mathcaptcha) {
                            $('#Lmathcaptcha').text(error.mathcaptcha);
                            $('#automatic_loginBtn').text('Login');
                        }

                    }

                    if (res.status == 'incorrect') {
                        $('.error_password').text(res.message);
                        $('#automatic_loginBtn').text('Login');
                    }

                    if (res.status == 'success') {
                        window.location.href = res.redirectUrl;
                    }
                }
            });
        });



        // forgot password
        $('#automatic_forgotPassword').on('click', function() {

            var email = "{{ session('email') }}";
            $('.tab-3').css('display', 'block');
            $('.tab-2').css('display', 'none');
            $('.forgot_email').val(email);



            $('#automatic_send_email').on('click', function() {
                $('#automatic_send_email').text('Loading....');
                var email = $('.forgot_email').val();
                $.ajax({
                    url: "{{ route('forgot.password.email') }}",
                    type: "post",
                    data: {
                        email: email
                    },
                    success: function(res) {
                        console.log(res);
                        console.log(res.error);
                        if (res.error) {
                            if (res.error.email) {
                                $('.email-error').text(res.error.email[0]);
                            } else {
                                $('.email-error').text(res.error);
                            }
                        }
                        if (res.success) {

                            toastr.success(res.success)
                            $('.tab-4').css('display', 'block');
                            $('.tab-3').css('display', 'none');
                        }
                    }
                })

            })


        });
        // check otp
        $('#automatic_check_otp').on('click', function() {

            var email = "{{ session('email') }}";
            var otp = $('.email_otp').val();
            var password = $('.new_password').val();
            $('#automatic_check_otp').text('Loading....');

            $.ajax({
                url: "{{ route('check.otp.email') }}",
                type: "post",
                data: {
                    email: email,
                    otp: otp,
                    password: password
                },
                success: function(res) {
                    console.log(res.otp);
                    if (res.otp) {
                        toastr.warning(res.otp)
                        $('#automatic_check_otp').text('Continue');
                    }
                    if (res.password) {
                        toastr.warning(res.password);
                        $('#automatic_check_otp').text('Continue');
                    }

                    if (res.message) {

                        toastr.success(res.message)
                        // window.location.href = res.redirectUrl;
                        window.location.reload();

                    }
                }
            });


        });



    });


    function showNotification(type, message) {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 3000,
        };

        toastr[type](message);
    }

    // Function to refresh the captcha via AJAX
    function refreshCaptcha() {

        console.log('hello33');
        $.ajax({
            url: '/refresh-captcha',
            type: 'GET',
            success: function(data) {
                console.log(data);
                $('#captchaLabel').html(data.label);
                $('#captchaLabelDetailsMob').html(data.label);
                $('#captchaLabelContact').html(data.label);
                $('input[name="mathcaptcha"]').val('');
                $('#captchaInput').val('');
                $('#captchaInputContact').val('');
                $('#captchaLabelForm').html(data.label);
                $('#captchaInputForm').val('');
                $('#captchaLabelSign').html(data.label);
                $('#captchaInputSign').val('');


            },
            error: function(xhr, status, error) {
                console.error('Error refreshing captcha:', error);
            }
        });
    }
</script>
</body>
</html>
