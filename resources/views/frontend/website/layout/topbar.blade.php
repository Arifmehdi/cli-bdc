<body class="">
    <!--Loader-->
    <div id="global-loader">
        <img src="{{ asset('frontend') }}/assets/images/loader.svg" class="loader-img "
            alt="Used cars for sale Best Dream car loader image">
    </div>

    <!--Topbar-->
    <div class="header-main">
        <div style="background:rgb(247, 246, 246)" class="top-bar">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 col-lg-8 col-sm-4 col-7">
                        <div class="top-bar-left d-flex">
                            <div class="clearfix">
                                <ul class="socials p-0 m-0">
                                    @foreach ($files as $file)
                                        <li class="icon-item">
                                            <a target="_blank" class="social-icon" href="{{ $file->link }}"
                                                rel="nofollow">
                                                <img src="{{ asset('frontend/assets/images/links') . '/' . $file->image }}"
                                                    alt="{{ $file->image }}" width="10" loading="lazy" />
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-sm-8 col-5">
                        <div class="top-bar-right">
                            <ul class="custom">
                                @if (Auth()->check())
                                    @php
                                        $nameBeforeIn = \Illuminate\Support\Str::before(auth()->user()->name, 'in');
                                    @endphp

                                    <li class="nav-item dropdown">
                                        <img style="border-radius:50%"
                                            src="{{ Auth::user()->image ? asset('frontend/assets/images/') . '/' . Auth::user()->image : asset('/frontend/assets/images/profile.png') }}
                              "
                                            alt="Used cars for sale Best Dream car user image" height="28"
                                            width="28">
                                        <a style="padding:8px" class="nav-link dropdown-toggle" href="#"
                                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{-- {{(Auth::user()->name) ? $nameBeforeIn : Auth::user()->email}} --}}
                                        </a>
                                        @php
                                            $url =
                                                auth()->check() &&
                                                auth()
                                                    ->user()
                                                    ->hasAnyRole(['admin', 'editor', 'dealer'])
                                                    ? '/admin/dashboard'
                                                    : '/profile';
                                        @endphp
                                        <ul class="dropdown-menu">
                                            <ul class="d-flex align-items-center mt-1 mb-2">
                                                <li class="me-2" style="width: auto;">
                                                    <a href="{{ $url }}">
                                                        <img style="border-radius:50%" class="ms-1"
                                                            src="{{ Auth::user()->image ? asset('frontend/assets/images/') . '/' . Auth::user()->image : asset('/frontend/assets/images/profile.png') }}"
                                                            alt="User profile image" height="25" width="25" loading="lazy">
                                                    </a>
                                                </li>
                                                <li style="width: auto;">
                                                    <a href="{{ $url }}">{{ Auth::user()->name ? Auth::user()->name : Auth::user()->email }}
                                                    </a>
                                                </li>
                                            </ul>
                                            <hr style="margin-top:3px; margin-bottom:9px">
                                            @if (!auth()->user()->hasAnyRole(['admin', 'editor', 'dealer']))
                                                <li><a style="font-size:15px;margin-left:4px; color:rgb(68, 68, 68);padding-left:10px"
                                                        class="dropdown-item mb-2"
                                                        href="{{ route('favourite.listing') }}">Favorite Cars</a></li>
                                                <li><a style="font-size:15px; margin-left:4px; color:rgb(68, 68, 68);padding-left:10px"
                                                        class="dropdown-item mb-2"
                                                        href="{{ route('buyer.user.message') }}">Message</a></li>
                                                {{-- <li><a style="font-size:15px; margin-left:4px; color:rgb(68, 68, 68);padding-left:10px"
                                                class="dropdown-item mb-2"
                                                href="{{ route('buyer.cargarage.show') }}">Car Garage</a></li> --}}
                                            @endif
                                            @if (auth()->check() &&
                                                    auth()->user()->hasAnyRole(['admin', 'editor', 'dealer']))
                                                <li><a style="font-size:15px;margin-left:4px; color:rgb(68, 68, 68);padding-left:10px"
                                                        class="dropdown-item mb-2"
                                                        href="{{ $url }}">Administrator </a></li>
                                            @else
                                                <li><a style="font-size:15px;margin-left:4px; color:rgb(68, 68, 68);padding-left:10px"
                                                        class="dropdown-item mb-2" href="{{ $url }}">Account
                                                        Information</a>
                                                </li>
                                            @endif
                                            <hr style="margin-top:3px; margin-bottom:9px">
                                            <li><a style="font-size:15px; margin-left:4px; color:rgb(68, 68, 68);padding-left:10px"
                                                    class="dropdown-item mb-2" href="{{ route('buyer.logout') }}">Log
                                                    out</a>
                                            </li>
                                        </ul>
                                    </li>
                                @else
                                    <li>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                            class="text-dark" rel="nofollow"><i class="fa fa-sign-in me-1"></i>
                                            <span>Login</span></a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Horizontal Header -->
        <div class="clearfix horizontal-header ">
            <div class="container">
                <a id="" class="animated-arrow settings-bar"><span></span></a>
                <a href="{{ route('home') }}">
                    <span class="smllogo">
                        @if (!empty($logo) && !empty($logo->image))
                            <img src="{{ asset('frontend/assets/images/logos/' . $logo->image) }}" class="s-logo"
                                alt="Used cars for sale Best Dream car logo image">
                        @else
                            <img src="{{ asset('frontend/assets/images/car77.png') }}" width="120"
                                alt="Default image">
                        @endif
                    </span>
                    <span class="smllogo-dark">
                        @if (!empty($logo) && !empty($logo->image))
                            <img src="{{ asset('frontend/assets/images/logos/' . $logo->image) }}" class="s-logo"
                                alt="Used cars for sale Best Dream car logo dark image">
                        @else
                            <img src="{{ asset('frontend/assets/images/car77.png') }}" width="120"
                                alt="Default image">
                        @endif
                    </span>
                </a>
                <div class="top-bar-signin">
                    <ul class="control">
                        @if (!auth()->user())
                            <li>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                    class="text-dark mob-log"><i class="fa fa-sign-in me-1"></i> <span
                                        class="mob-login-text">Login</span></a>
                            </li>
                        @endif

                    </ul>
                </div>
            </div>
        </div>

        <!-- /Horizontal Header -->
        <!-- Horizontal Main -->
        <div style="background: white !important; z-index:9; border-bottom: 1px solid rgb(212, 212, 212)"
            class="clearfix horizontal-main">
            <div class="container clearfix horizontal-mainwrapper">
                <a href="{{ route('home') }}">
                    <div style="margin-right:15px;" class="desktoplogo">

                        @if (!empty($logo) && !empty($logo->image))
                            <img src="{{ asset('frontend/assets/images/logos/' . $logo->image) }}" class="des-logo"
                                alt="Used cars for sale Best Dream car logo main image">
                        @else
                            <img src="{{ asset('frontend/assets/images/car77.png') }}" width="120"
                                alt="Default image">
                        @endif

                    </div>
                    <div class="desktoplogo-1">
                        @if (!empty($logo) && !empty($logo->image))
                            <img src="{{ asset('frontend/assets/images/logos/' . $logo->image) }}" class="des-logo"
                                alt="Used cars for sale Best Dream car logo desktop image">
                        @else
                            <img src="{{ asset('frontend/assets/images/car77.png') }}" width="120"
                                alt="Default image">
                        @endif
                    </div>
                </a>
                <nav class="clearfix horizontalMenu d-md-flex">
                    <ul class="horizontalMenu-list">
                        @foreach ($header_menus as $menu)
                            <li style="margin-top: 2px;" aria-haspopup="true">
                                <a style="color: black"
                                    href="{{ $menu->submenus->isNotEmpty() ? '#' : ($menu->route_url != null ? route($menu->route_url) : url($menu->slug)) }}"
                                    @if ($menu->name == 'Used') rel="noopener" @endif>
                                    {{ $menu->name }}
                                    @if ($menu->submenus->isNotEmpty())
                                        <span style="color: black" class="m-0 fa fa-caret-down"></span>
                                    @endif
                                </a>
                                @if ($menu->submenus->isNotEmpty())
                                    <ul class="sub-menu">
                                        @foreach ($menu->submenus as $submenu)
                                            <li aria-haspopup="true">
                                                @if ($submenu->param)
                                                    <a href="{{ route($submenu->route_url, ['body' => $submenu->param, 'home' => true]) }}"
                                                        @if ($submenu->name == 'Cars for sale' || $submenu->name == 'Used EVs') rel="nofollow" @endif>{{ $submenu->name }}</a>
                                                @else
                                                    <a href="{{ $submenu->route_url != null ? route($submenu->route_url) : url($submenu->slug) }}"
                                                        @if ($submenu->name == 'Cars for sale' || $submenu->name == 'Used EVs') rel="nofollow" @endif>{{ $submenu->name }}</a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                        <li style="margin-top: 2px;" aria-haspopup="true">
                            <a style="color: black" href="{{ route('frontend.research.review') }}">Research
                                <span style="color: black" class="m-0 fa fa-caret-down"></span>
                            </a>
                            <ul class="sub-menu">
                                <li aria-haspopup="true"><a href="{{ route('frontend.research.auto.news') }}">Auto
                                        News</a></li>
                                {{-- <li aria-haspopup="true"><a href="{{ route('research') }}">Reviews</a></li>
                                <li aria-haspopup="true"><a href="{{ route('frontend.research.toolsexpertadvice') }}">Tools and Advice</a></li> --}}

                                <li aria-haspopup="true"><a
                                        href="{{ route('frontend.research.auto.review') }}">Reviews</a></li>
                                <li aria-haspopup="true"><a
                                        href="{{ route('frontend.research.tools.advice') }}">Tools and Advice</a></li>
                                <li aria-haspopup="true"><a
                                        href="{{ route('frontend.research.car.buying.advice') }}">Car Buying
                                        Advice</a></li>
                                {{-- <li aria-haspopup="true"><a href="{{ route('frontend.tips.page')}}">Car Tips</a></li> --}}
                                <li aria-haspopup="true"><a href="{{ route('frontend.research.car.tips') }}">Car
                                        Tips</a></li>
                                <li aria-haspopup="true"><a href="{{ route('frontend.research.videos') }}">Videos</a>
                                </li>
                                <li aria-haspopup="true"><a href="{{ route('frontend.faq') }}">FAQ</a></li>
                            </ul>
                        </li>
                        <li style="margin-top: 2px;" aria-haspopup="true">
                            <a style="color: black" href="{{ route('frontend.beyondcar') }}">Beyond Cars
                                <span style="color: black" class="m-0 fa fa-caret-down"></span>
                            </a>
                            <ul class="sub-menu">
                                <li aria-haspopup="true"><a href="{{ route('frontend.beyondcar.news') }}">News</a>
                                </li>
                                <li aria-haspopup="true"><a
                                        href="{{ route('frontend.beyondcar.innovation') }}">Innovation</a></li>
                                <li aria-haspopup="true"><a
                                        href="{{ route('frontend.beyondcar.opinion') }}">Opinion</a></li>
                                <li aria-haspopup="true"><a
                                        href="{{ route('frontend.beyondcar.financial') }}">Financial</a></li>
                            </ul>
                        </li>
                        @if (
                            !auth()->check() ||
                                (auth()->check() &&
                                    auth()->user()->hasAnyRole(['admin', 'editor', 'dealer'])))
                            <li style="margin-top:2px;" aria-haspopup="true">
                                <a style="color:black" href="{{ route('favourite.listing') }}"
                                    rel="nofollow">Favorites</a>
                            </li>
                        @endif




                        {{-- <div style="position:relative;">
                            <form id="searchForm" action="{{ route('search') }}" method="GET">
                                <input id="searchInput"
                                    style="float:right; margin-top:24px; width:400px; height:42px; border-radius:20px; background:white; border:1px solid rgb(105, 105, 105); font-size:17px; padding-left:40px; color:black; box-sizing: border-box;"
                                    class="search-item" type="text" name="query" placeholder="Search" />
                                <i style="position: absolute; right: 370px; top: 38px; color:rgb(10, 10, 10)"
                                    class="fa fa-search search-icon-topbar"></i>
                            </form>
                        </div> --}}
                        <div style="position: relative; width: 400px; float: right; margin-top: 24px;"
                            class="search-content-manage">
                            <form id="searchForm" action="{{ route('search') }}" method="GET" style="margin: 0;">
                                <input id="searchInput"
                                    style="width: 100%; height: 42px; border-radius: 20px; background: white; border: 1px solid rgb(105, 105, 105); font-size: 17px; padding-left: 40px; color: black; box-sizing: border-box;"
                                    class="search-item" type="text" name="query" placeholder="Search" />
                                <i style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: rgb(10, 10, 10);"
                                    class="fa fa-search search-icon-topbar"></i>
                            </form>
                        </div>

                    </ul>
                </nav>
                <!--Nav-->
                <!--Nav-->
            </div>
        </div>
    </div>
    <!--/Topbar-->

    <!-- Register Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" style="margin-top:-90px">
            <div class="modal-content">
                <div style="margin-bottom:30px;" class="modal-body">
                    <button style="float:right !important" type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <div style=" width:100%" class="row registar-modal m-0 p-0">
                        <div class="col-lg-12 col-xl-12">
                            <div class="mb-3 row " id="rowOne">
                                {{-- // login form  --}}
                                <div style="text-align:center" class="tab-1 col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                    <h4 style="text-align: center !important; margin-top:45px">Log in or sign up</h4>
                                    <p style="text-align: center !important; margin-top:23px">Use email or another
                                        service to continue (it's free)!</p>
                                    <input class="email-input"
                                        style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px"
                                        type="text" name="email" id="email" placeholder="Email Address*" />
                                    <div class="text-danger error_email"></div>
                                    <button class="ctn-btn" id="CheckEmail"
                                        style="width:300px; height:42px; margin-top:22px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px">Continue</button>
                                    <div style="display: flex; margin-top: 35px; margin-left:75px;" class="or_line">
                                        <div
                                            style=" width:125px !important; height: 1px; background: rgb(218, 217, 217);">
                                        </div>
                                        <p style="margin-top:-9px; margin-right:6px; margin-left:6px">Or</p>
                                        <div
                                            style=" height: 1px; width:125px !important; background: rgb(218, 217, 217);">
                                        </div>
                                    </div>
                                    <button class="google-btn"
                                        style="width:300px; height:42px; margin-top:10px; border-radius:11px;background:white;  border:1px solid black; font-size:17px"><i
                                            style="color:rgb(192, 4, 4); margin-right:10px; margin-left:-21px; font-size:19px; font-weight:bold; margin-top:1px"
                                            class="fa fa-google"></i><a class="google-text"
                                            href="{{ route('google.login') }}">Continue with Google </a></button>
                                    {{-- <button class="google-fb"
                                        style="width:300px; height:42px; margin-top:19px;  border-radius:11px;background:white; border:1px solid black; font-size:17px"><img
                                            style="margin-right:9px; margin-top:-1px"
                                            src="{{ asset('frontend') }}/assets/images/facebook.svg" width="20px"
                                            alt="Used cars for sale Best Dream car facebook logo image"><a
                                            class="fab-text" href="{{ route('facebook.login')}}"> Continue with Facebook
                                        </a> </button> --}}
                                </div>

                                {{-- // password form  --}}
                                <div style="display: none !important; text-align:center"
                                    class="tab-2 col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                    <i style="position:absolute; font-size:26px; top:-19px; float: left; left:-1px; cursor:pointer;"
                                        class="fa fa-angle-left previous_link" onclick="GoBack(2)"></i>
                                    <h4 class="text-center fw-bold"
                                        style="margin-top: 49px; margin-right: 74px;margin-bottom:14px; width:100%">
                                        Welcome Back !</h4>
                                    <p class="text-center"
                                        style="margin-top: 11px; margin-right: 50px;  margin-bottom: 5px; width:100%">
                                        Please Enter Your Password to Log In.</p>
                                    <p class="text-center"
                                        style="margin-top: 11px; margin-right: 50px; margin-bottom: 5px;  width:100%">
                                        <i class="fa fa-envelope"></i> <span class="email_session"></span>
                                    </p>
                                    <form id="login_byuer">
                                        @csrf
                                        <input
                                            style="width:280px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px;margin-bottom: 20px"
                                            type="password" name="password" id="password"
                                            placeholder="Enter Your Password*" />
                                        <input type="hidden" name="email_session_one" class="email_session_one">
                                        <div class="text-danger error_password"></div>
                                        <div class="p-0 col-md-12 col-sm-12 col-xs-12">
                                            <div class="form-group ">
                                                <p
                                                    style="color:rgb(32, 32, 31); font-weight:bold; margin-bottom:15px; margin-top:10px; font-size:12px">
                                                    <span class="text-danger">*</span> Security Question (Enter the
                                                    Correct answer)
                                                </p>
                                                <div style="display: flex">
                                                    <div id="captchaLabelForm" class="cap-field"
                                                        style="background-color:rgb(5, 145, 145); width:30%;  margin-right:10px; text-align:center; padding-top:7px; font-weight:600;  margin-top:2px; margin-left:75px; height:35px; border-radius:5px; color:white">
                                                        {{ app('mathcaptcha')->label(true) }}

                                                    </div>
                                                    <div>
                                                        <input id="captchaInputForm"
                                                            style="width:73%; border-radius:5px"
                                                            class="res-input form-control @error('mathcaptcha') is-invalid @enderror"
                                                            type="text" name="mathcaptcha"
                                                            placeholder="Your answer">
                                                        <span id="Lmathcaptcha" class="text-danger" role="alert">
                                                            {{-- @error('mathcaptcha')
                                                            {{ $message }}
                                                            @enderror --}}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <a href="#" style="text-decoration:underline; font-size:13px"
                                            id="forgotPassword" class="text-center">Forgot your password?</a>
                                        <button type="submit" id="loginBtn"
                                            style="width:300px; height:42px; margin-top:22px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px">Login</button>
                                    </form>
                                </div>

                                <div style="display: none !important; text-align:center"
                                    class="tab-3 col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                    <i style="position:absolute; font-size:26px; top:-19px; float: left; left:-1px; cursor:pointer;"
                                        class="fa fa-angle-left previous_link" onclick="GoBack(3)"></i>
                                    <h4 class="text-center fw-bold"
                                        style="margin-top: 49px; margin-right: 74px; width:100%">Forgot your password?
                                    </h4>
                                    <p class="text-center"
                                        style="margin-top:9px; margin-right: 74px;margin-bottom:14px; width:100%">Enter
                                        your email address and we’ll send you a reset code.</p>
                                    <input
                                        style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px;margin-bottom: 20px"
                                        type="email" name="forgot_email" id="forget_email" class="forgot_email"
                                        placeholder="Enter Your Email*" />
                                    <input type="hidden" name="email_session_one" class="email_session_one">
                                    <div class="text-danger email-error"></div>
                                    <button type="button" id="send_email"
                                        style="width:300px; height:42px; margin-top:5px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px">Send</button>
                                </div>

                                <div id="otp_section" style="display: none !important; text-align:center"
                                    class="tab-4 col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                    <i style="position:absolute; font-size:26px; top:-19px; float: left; left:5px; cursor:pointer;"
                                        cursor:pointer;" class="fa fa-angle-left previous_link"
                                        onclick="GoBack(4)"></i>
                                    <h4 class="text-center fw-bold"
                                        style="margin-top: 49px; margin-right: 74px;margin-bottom:18px; width:100%">
                                        Verify your email</h4>
                                    <p class="text-center"
                                        style="margin-top: 11px; margin-right: 50px;  margin-bottom: 5px; width:100%">
                                        Please confirm the 6-digit code sent to you at:</p>
                                    <p class="text-center "
                                        style="margin-top: 11px; margin-right: 50px; margin-bottom: 5px; width:100%"><i
                                            class="fa fa-envelope"></i> <span class="email_session"></span></p>
                                    <input
                                        style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px;margin-bottom: 10px"
                                        type="text" name="email_otp" class="email_otp"
                                        placeholder="Enter verification code*" />
                                    <div class="text-danger otp-error"></div>
                                    <input
                                        style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px;"
                                        type="text" name="new_password" class="new_password"
                                        placeholder="Create new Password*" />
                                    <div class="text-danger password-error"></div>
                                    <button type="button" id="check_otp"
                                        style="width:300px; height:42px; margin-top:22px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px;margin-bottom: 15px">Continue</button>
                                    <a href="#" class="send_email"
                                        style="text-decoration:underline; color:green; margin-left: 240px">Resend</a>
                                </div>
                            </div>
                            <div class="mb-3 row tab-5" style="display:none; text-align:center">
                                <i style="position:absolute; font-size:26px; top:-19px;
                            left:-207px; cursor:pointer;"
                                    class="fa fa-angle-left previous_link-signup" onclick="GoBack(5)"></i>
                                <h4 style="margin-top: 35px; margin-bottom: 10px;" class="text-center ">Sign up</h4>
                                <p class="text-center fw-medium"><i class="fa fa-envelope"></i> <span
                                        id="email_session_two"></span></p>
                                <form id="SignUpForm">
                                    @csrf
                                    <input class="pass-input"
                                        style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px"
                                        type="password" name="password" id="res_password"
                                        placeholder="Enter Your Password*" />
                                    {{-- <span style="text-align: center">Your password must be at least 6 characters
                                        long, and contain a number or a symbol.</span> --}}
                                    <input type="hidden" name="email_session" class="email_session_two">
                                    {{-- <div class="text-danger sign_up_email"></div> --}}
                                    <div class="text-danger sign_up_password"></div>
                                    <p
                                        style="text-align:center; margin-left: 63px;
                             margin-right:58px ; font-size:12px; margin-top:10px">
                                        Your password must be at least 8
                                        characters long and contain at least 1 letter and a number or a symbol.
                                    </p>

                                    <div class="p-0 col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group ">
                                            <p
                                                style="color:rgb(32, 32, 31); font-weight:bold; font-size:12px; margin-bottom:13px; margin-top:10px">
                                                <span class="text-danger">*</span> Security Question (Enter the Correct
                                                answer)
                                            </p>
                                            <div style="display: flex">
                                                <div id="captchaLabelSign"
                                                    style="background-color:rgb(5, 145, 145); width:30%;  margin-right:10px; text-align:center; padding-top:7px; font-weight:600;  margin-top:2px; margin-left:75px; height:35px; border-radius:5px; color:white">
                                                    {{ app('mathcaptcha')->label(true) }}

                                                </div>
                                                <div>
                                                    <input id="captchaInputSign" style="width:73%; border-radius:5px"
                                                        class="form-control" type="text" name="mathcaptcha"
                                                        id="mathcaptcha" placeholder="Your answer">
                                                    <span id="Smathcaptcha" class="text-danger" role="alert">
                                                        @error('mathcaptcha')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="ctn-btn" id="SignUpBtn" type="submit"
                                        style="width:300px; height:42px; margin-top:12px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px">Create
                                        Account</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('js')
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <script>
                    toastr.error("{{ $error }}");
                </script>
            @endforeach
        @endif
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).ready(function() {
                // check mail
                $('#CheckEmail').on('click', function(e) {
                    e.preventDefault();
                    var email = $('#email').val();
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
                                refreshCaptcha();
                            }
                            if (res.status == 0) {
                                $('#rowOne').css('display', 'none');
                                $('.tab-5').css('display', 'block');
                                $('.previous_link').css('display', 'block');
                                $('#email_session_two').text(res.email);
                                $('.email_session_two').val(res.email);
                                refreshCaptcha();
                            }
                        }
                    })
                });

                // login check
                $('#login_byuer').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData($(this)[0]);

                    $('#loginBtn').text('Loading....');

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
                                    $('#loginBtn').text('Login');
                                }
                                if (error.mathcaptcha) {
                                    $('#Lmathcaptcha').text(error.mathcaptcha);
                                    $('#loginBtn').text('Login');
                                }
                            }
                            if (res.status == 'incorrect') {
                                $('.error_password').text(res.message);
                                $('#loginBtn').text('Login');
                            }
                            if (res.status == 'success') {
                                if (res.role == 'admin') {
                                    window.open(res.redirectUrl,
                                        '_blank'); // Open the redirectUrl in a new tab
                                    window.location.reload(); // Reload the current page immediately
                                } else if (res.role == 'dealer') {
                                    window.open(res.redirectUrl,
                                        '_blank'); // Open the redirectUrl in a new tab
                                    window.location.reload(); // Reload the current page immediately
                                } else {
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 100);
                                }
                            }
                            // if (res.status == 'success') {
                            //     // window.location.href = res.redirectUrl;

                            //     setTimeout(function() {
                            //         window.location.reload();
                            //     }, 100);
                            // }
                        }
                    });
                });



                // forgot password
                $('#forgotPassword').on('click', function() {

                    var email = "{{ session('email') }}";
                    $('.tab-3').css('display', 'block');
                    $('.tab-2').css('display', 'none');
                    $('.forgot_email').val(email);



                    $('#send_email').on('click', function() {
                        $('#send_email').text('Loading....');
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

                    });

                    $('.send_email').on('click', function() {
                        $('.send_email').text('Loading....');
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
                                    $('.send_email').text('Resend');
                                    $('.tab-4').css('display', 'block');
                                    $('.tab-3').css('display', 'none');
                                }
                            }
                        })
                    })
                });
                // check otp
                $('#check_otp').on('click', function() {

                    var email = "{{ session('email') }}";
                    var otp = $('.email_otp').val();
                    var password = $('.new_password').val();
                    $('#check_otp').text('Loading....');

                    $.ajax({
                        url: "{{ route('check.otp.email') }}",
                        type: "post",
                        data: {
                            email: email,
                            otp: otp,
                            password: password
                        },
                        success: function(res) {
                            console.log(res);
                            if (res.otp) {
                                toastr.warning(res.otp);
                                $('#check_otp').text('Continue');
                            }
                            if (res.password) {
                                toastr.warning(res.password);
                                $('#check_otp').text('Continue');
                            }

                            if (res.message) {

                                toastr.success(res.message);
                                // window.location.href = res.redirectUrl;
                                window.location.reload();

                            }
                            if (res.error) {

                                toastr.warning(res.error);
                                $('#check_otp').text('Continue');
                                // window.location.href = res.redirectUrl;

                            }
                        }
                    });
                });
            });

            //  create new account
            $(document).ready(function() {
                $('#SignUpForm').on('submit', function(e) {
                    e.preventDefault();

                    var formData = new FormData($(this)[0]);
                    $('#SignUpBtn').text('Loading....');
                    $.ajax({
                        url: "{{ route('create.account') }}",
                        type: 'post',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {

                            // console.log(res);
                            // return;
                            if (res.status == 'error') {
                                var error = res.errors;
                                if (error.password) {
                                    $('.sign_up_password').text(error.password);
                                    $('#SignUpBtn').text('Create Account');
                                }
                                if (error.mathcaptcha) {
                                    $('#Smathcaptcha').text(error.mathcaptcha);
                                    $('#SignUpBtn').text('Create Account');
                                }

                            }
                            if (res.status == 'success') {

                                toastr.success(res.message);
                                // window.location.href = "{{ route('buyer.login') }}";
                                setTimeout(function() {
                                    window.location.reload();
                                }, 100);


                            }

                        }
                    });
                });

            });

            // $('.previous_link').on('click',function(){

            // console.log('hellow');
            // // $('.tab-2').css('display', 'none');
            // $('#replace_html').css('display', 'block');
            // });


            // back button working in javascript

            // let currentTab = 2;

            function GoBack(value) {
                if (value == 5) {
                    $('#rowOne').css('display', 'block');
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
        </script>
    @endpush
