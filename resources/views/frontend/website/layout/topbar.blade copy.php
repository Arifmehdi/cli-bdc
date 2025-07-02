


<body class="">

<!--Loader-->
<div id="global-loader">
    <img src="{{ asset('frontend') }}/assets/images/loader.svg" class="loader-img " alt="Used cars for sale Dream Best Car loader  image">
</div>

<!--Topbar-->
<div class="header-main">
    <div style="background:rgb(247, 246, 246)" class="top-bar">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-8 col-sm-4 col-7">
                    <div class="top-bar-left d-flex">
                        <div class="clearfix">
                            <ul class="socials">
                                <li>
                                    <a target="_blank" class="social-icon text-dark" href="https://www.facebook.com"><i class="fa fa-facebook"></i></a>
                                </li>
                                <li>
                                    <a class="social-icon text-dark" href="https://twitter.com"><i class="fa fa-twitter"></i></a>
                                </li>
                                <li>
                                    <a class="social-icon text-dark" href="https://www.linkedin.com"><i class="fa fa-linkedin"></i></a>
                                </li>
                                {{-- <li>
                                    <a target="_blank" class="social-icon text-dark" href="{{$flinks->link}}"><i class="fa fa-facebook"></i></a>
                                </li>
                                <li>
                                    <a class="social-icon text-dark" href="{{$tlinks->link}}"><i class="fa fa-twitter"></i></a>
                                </li>
                                <li>
                                    <a class="social-icon text-dark" href="{{$llinks->link}}"><i class="fa fa-linkedin"></i></a>
                                </li> --}}

                            </ul>
                        </div>

                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-8 col-5">
                    <div class="top-bar-right">
                        <ul class="custom">

                          @if(Auth()->check())
                            <li class="nav-item dropdown">
                               <img src="{{asset('/frontend/assets/images/user.png')}}" alt="Used cars for sale Dream Best Car user image" height="20" width="20">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{(Auth::user()->name) ? Auth::user()->name : Auth::user()->email}}
                                </a>
                                @php

                                $url = (auth()->check() && auth()->user()->hasAnyRole(['admin', 'editor', 'dealer'])) ? '/admin/dashboard' : '/profile';
                                @endphp
                                <ul class="dropdown-menu">
                                  <x-profile href="{{ $url }}"/>
                                  <li><a class="dropdown-item" href="{{ route('buyer.logout')}}">Log out</a></li>

                                </ul>
                              </li>

                            @else
                            {{-- <li>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="text-dark"><i class="fa fa-user me-1"></i> <span>Register</span></a>
                            </li> --}}
                            <li>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="text-dark"><i class="fa fa-sign-in me-1"></i> <span>Login</span></a>
                            </li>

                            @endif


                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Horizontal Header -->
    <div class="horizontal-header clearfix ">
        <div class="container">
            <a id="horizontal-navtoggle" class="animated-arrow"><span></span></a>

            <span  class="smllogo">
                <a href="{{ route('home') }}"><img src="{{ asset('frontend') }}/assets/images/car77.png" width="120" alt="Used cars for sale Dream Best Car logo image"></a>

            </span>
            <span class="smllogo-dark">
                <a href="{{ route('home') }}"><img src="{{ asset('frontend') }}/assets/images/car77.png" width="120" alt="Used cars for sale Dream Best Car logo image"></a>

            </span>
            <div  class="top-bar-signin">
                <ul style="" class="control">

                  @if(Auth()->check())
                    <li style="margin-left:-25px !important" class="nav-item dropdown">
                       <img src="{{asset('/frontend/assets/images/user.png')}}" alt="Used cars for sale Dream Best Car user image" height="20" width="20">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{(Auth::user()->name) ? Auth::user()->name : Auth::user()->email}}
                        </a>
                        @php

                        $url = (auth()->check() && auth()->user()->hasAnyRole(['admin', 'editor', 'dealer'])) ? '/admin/dashboard' : '/profile';
                        @endphp
                        <ul class="dropdown-menu">
                          <x-profile href="{{ $url }}"/>
                          <li><a class="dropdown-item" href="{{ route('buyer.logout')}}">Log out</a></li>

                        </ul>
                      </li>

                    @else
                    {{-- <li>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="text-dark"><i class="fa fa-user me-1"></i> <span>Register</span></a>
                    </li> --}}
                    <li>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="text-dark"><i class="fa fa-sign-in me-1"></i> <span>Login</span></a>
                    </li>

                    @endif


                </ul>
            </div>


        </div>
    </div>
    <!-- /Horizontal Header -->

    <!-- Horizontal Main -->
    <div style="background: white !important; z-index:9; border-bottom: 1px solid rgb(212, 212, 212)" class="horizontal-main  clearfix">
        <div class="horizontal-mainwrapper container clearfix">
            <div style="margin-right:15px;" class="desktoplogo">
                <a href="{{ route('home') }}"><img src="{{ asset('frontend') }}/assets/images/car77.png"  width="120" height="45px" alt="Used cars for sale Dream Best Car Logo  image"></a>

            </div>
            <div class="desktoplogo-1">
                <a href="{{ route('home') }}"><img src="{{ asset('frontend') }}/assets/images/car77.png"  width="160" height="45px" alt="Used cars for sale Dream Best Car Logo  image"></a>
            </div>

            <nav  class="horizontalMenu clearfix d-md-flex">
                <ul  class="horizontalMenu-list">


                    <li style=" margin-top:2px" aria-haspopup="true"><a style="color:black" href="javascript:void(0);" >Used <span style="color:black" class="fa fa-caret-down m-0"></span></a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true"><a href="{{ route('auto') }}">Used Car Listings</a></li>
                            <li aria-haspopup="true"><a href="{{ route('auto',['homeBodySearch' => 'ev']) }}">Used EVs</a></li>
                            <li aria-haspopup="true"><a href="{{ route('auto',['homeBodySearch' => 'suv']) }}">Used SUVs</a></li>
                            <li aria-haspopup="true"><a href="{{ route('auto',['homeBodySearch' => 'truck']) }}">Used Trucks</a></li>
                            <li aria-haspopup="true"><a href="{{ route('auto',['homeBodySearch' => 'van']) }}">Used Vans</a></li>
                            <li aria-haspopup="true"><a href="{{ route('auto',['homeBodySearch' => 'convertible']) }}">Used Converibles</a></li>
                        </ul>
                    </li>
                    <li style="margin-top:2px;" aria-haspopup="true"><a style="color:black" href="#">Research </a></li>

                    <li style="margin-top:2px" aria-haspopup="true"><a style="color:black" href="{{route('frontend.news.page')}}"> News <span class="horizontalarrow"></span></a></li>
                    @if (!auth()->user())
                    <li style="margin-top:2px;" aria-haspopup="true"><a style="color:black" href="{{ route('favourite.listing') }}">Favorites </a></li>
                    @endif


                    <div style="position:relative;">
                        <input style="float:right; margin-top:24px; width:400px; height:42px; border-radius:20px; background:white; border:1px solid rgb(105, 105, 105); font-size:17px; padding-left:40px; color:black;" class="search-item" type="text" placeholder="Search" />
                        <i style="position: absolute; right: 370px; top: 38px; color:rgb(10, 10, 10)" class="fa fa-search"></i>
                    </div>



                </ul>

            </nav>



            <!--Nav-->

            <!--Nav-->
        </div>
    </div>
    <!-- Horizontal Main -->
</div>
<!--/Topbar-->

<!-- Register Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" style="margin-top:-140px">
      <div class="modal-content">

        <div style="margin-bottom:30px;" class="modal-body">


            <button style="float:right !important" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            {{-- <h4 style="text-align:center; margin-top:35px">Log in or sign up</h4>
            <p style="text-align:center; margin-top:12px">Continue with email or your Facebook or Google account.</p> --}}
            <div style="margin-left:16%" class="row registar-modal">

                <div style="" class="col-lg-12 col-xl-12">
                    <form>
                        <div class="row mb-3" id="rowOne">
                            <div class="tab-1">
                                <h4 style="text-align: center !important; margin-top:45px;margin-right:55px">Log in or sign up</h4>
                                <p style="text-align: center !important; margin-top:23px;margin-right:51px">Continue with email or your Facebook or Google account.</p>

                                    <input style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px" type="text" name="email" id="email" placeholder="Email Address*"/>
                                <div class="text-danger error_email"></div>

                                    <button  id="CheckEmail" style="width:300px; height:42px; margin-top:22px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px">Continue</button>
                                    <div style="display: flex; margin-top: 25px; width:300px;">
                                        <div style=" width:180px !important; height: 1px; background: rgb(218, 217, 217);"></div>
                                        <p style="margin-top:-9px; margin-right:6px; margin-left:6px" >Or</p>
                                        <div style=" height: 1px; width:180px !important; background: rgb(218, 217, 217);"></div>
                                        </div>

                                       <button style="width:300px; height:42px; margin-top:19px; border-radius:15px; background:white;  border:2px solid black; font-size:17px"><i style="color:rgb(192, 4, 4); margin-right:10px; margin-left:-21px; font-size:19px; font-weight:bold; margin-top:1px" class="fa fa-google"></i><a href="{{ route('google.login')}}">Continue with Google </a></button>

                                       <button style="width:300px; height:42px; margin-top:19px;  border-radius:15px; background:white; border:2px solid black; font-size:17px"><img style="margin-right:9px; margin-top:-1px" src="{{ asset('frontend') }}/assets/images/facebook.svg" width="20px"  alt="Used cars for sale Dream Best Car facebook image"><a href="{{ route('facebook.login')}}"> Continue with Facebook </a> </button>
                            </div>


                            <div style="display: none !important" class="tab-2">
                                <i style="position:absolute; top:4px;float: left;left:-67px; cursor:pointer;" class="fa fa-arrow-left previous_link" onclick="GoBack(2)"></i>
                                <h4 class="text-center fw-bold" style="margin-top: 49px; margin-right: 74px;margin-bottom:38px">Welcome Back !</h4>
                                <p class="text-center" style="margin-top: 11px; margin-right: 50px;  margin-bottom: 5px;">Please Enter Your Password to Log In.</p>
                                <p class="text-center " style="margin-top: 11px; margin-right: 50px; margin-bottom: 5px;"><i class="fa fa-envelope"></i> <span class="email_session"></span></p>
                                <form id="login_byuer" method="post" action="">
                                    @csrf
                                    <input style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px;margin-bottom: 20px" type="password" name="password" id="password" placeholder="Enter Your Password*"/>
                                <input type="hidden" name="email_session_one" class="email_session_one">
                                <div class="text-danger error_password"></div>
                                <div class="col-md-12 col-sm-12 col-xs-12 ">
                                    <div class="form-group ">
                                        <p style="color:rgb(32, 32, 31); font-weight:bold; margin-bottom:15px; margin-top:10px"><span class="text-danger">*</span> Security Question (Enter the Correct answer)</p>
                                        <div style="display: flex">
                                            <div style="background-color:rgb(5, 145, 145); width:50%;  margin-right:10px; text-align:center; padding-top:7px; font-weight:600;  margin-top:2px; margin-left:3px; height:35px; border-radius:5px; color:white">
                                                {{ app('mathcaptcha')->label() }}
                                                {{-- {{app('mathcaptcha')->reset()}} --}}

                                            </div>
                                            <div>
                                                <input style="width:78%; border-radius:5px" class="form-control @error('mathcaptcha') is-invalid @enderror" type="text"
                                                    name="mathcaptcha" placeholder="Your answer">
                                                <span id="Lmathcaptcha" class="text-danger" role="alert">
                                                    @error('mathcaptcha')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>

                                        </div>



                                    </div>
                                    </div>

                                <a href="#" style="text-decoration:underline" id="forgotPassword" class="text-center">Forgot your password?</a>

                                    <button type="submit"   style="width:300px; height:42px; margin-top:22px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px">Login</button>
                                </form>

                            </div>

                            <div  style="display: none !important" class="tab-3">
                                <i style="position:absolute; top:4px;float: left; left:-67px; cursor:pointer;" class="fa fa-arrow-left previous_link" onclick="GoBack(3)"></i>
                                <h4 class="text-center fw-bold" style="margin-top: 49px; margin-right: 74px;margin-bottom:38px">Forgot your password?</h4>
                                <p class="text-center" style="margin-top: 11px; margin-right: 50px;  margin-bottom: 5px;">Enter your email address and we’ll send you a reset code.</p>
                                <input style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px;margin-bottom: 20px" type="email" name="forgot_email" id="forget_email" class="forgot_email" placeholder="Enter Your Email*"/>
                                <input type="hidden" name="email_session_one" class="email_session_one">
                                <div class="text-danger email-error"></div>


                                    <button type="button"  id="send_email" style="width:300px; height:42px; margin-top:22px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px">Send</button>
                            </div>

                            <div  id="otp_section" style="display: none !important" class="tab-4">
                                <i style="position:absolute; top:4px;float: left;left:-67px; cursor:pointer;" class="fa fa-arrow-left previous_link" onclick="GoBack(4)"></i>
                                <h4 class="text-center fw-bold" style="margin-top: 49px; margin-right: 74px;margin-bottom:38px">Verify your email</h4>
                                <p class="text-center" style="margin-top: 11px; margin-right: 50px;  margin-bottom: 5px;">Please confirm the 6-digit code sent to you at:</p>
                                <p class="text-center " style="margin-top: 11px; margin-right: 50px; margin-bottom: 5px;"><i class="fa fa-envelope"></i> <span class="email_session"></span></p>
                                <input style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px;margin-bottom: 20px" type="text" name="email_otp" class="email_otp" placeholder="Enter verification code*"/>
                                <div class="text-danger otp-error"></div>
                                <input style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px;margin-bottom: 20px" type="text" name="new_password" class="new_password" placeholder="Create new Password*"/>
                                <div class="text-danger password-error"></div>

                                    <button type="button"  id="check_otp" style="width:300px; height:42px; margin-top:22px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px;margin-bottom: 22px">Continue</button>

                                    <a href="#" id="send_email" style="text-decoration:underline; color:green; margin-left: 119px">Resend</a>
                            </div>



                        </div>


                        <div class="row mb-3 tab-5" style="display:none">
                            <i style="position:absolute; top:4px;float: left;left:-67px; cursor:pointer;" class="fa fa-arrow-left previous_link" onclick="GoBack(5)"></i>
                            <h3 style="margin-top: 35px;margin-left: 70px; margin-bottom: 28px;" class="fw-medium">Sign up</h3>
                            <h4 class="fw-medium"><i class="fa fa-envelope"></i> <span
                                    id="email_session_two"></span></h4>



                                <input style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px" type="password" name="password" id="res_password" placeholder="Enter Your Password*"/>
                            {{-- <span style="text-align: center">Your password must be at least 6 characters long, and contain a number or a symbol.</span> --}}
                            <input type="hidden" name="email_session" class="email_session_two">
                            {{-- <div class="text-danger sign_up_email"></div> --}}
                            <div class="text-danger sign_up_password"></div>

                            <div class="col-md-12 col-sm-12 col-xs-12 ">
                                <div class="form-group ">
                                    <p style="color:rgb(32, 32, 31); font-weight:bold; margin-bottom:15px; margin-top:10px"><span class="text-danger">*</span> Security Question (Enter the Correct answer)</p>
                                    <div style="display: flex">
                                        <div style="background-color:rgb(5, 145, 145); width:50%;  margin-right:10px; text-align:center; padding-top:7px; font-weight:600;  margin-top:2px; margin-left:3px; height:35px; border-radius:5px; color:white">
                                            {{ app('mathcaptcha')->label() }}
                                            {{-- {{app('mathcaptcha')->reset()}} --}}

                                        </div>
                                        <div>
                                            <input style="width:78%; border-radius:5px" class="form-control @error('mathcaptcha') is-invalid @enderror" type="text"
                                                name="mathcaptcha" id="mathcaptcha" placeholder="Your answer">
                                            <span id="Smathcaptcha" class="text-danger" role="alert">
                                                @error('mathcaptcha')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>

                                    </div>



                                </div>
                                </div>

                                <button id="SignUp" type="button" style="width:300px; height:42px; margin-top:22px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px">Create Account</button>
                        </div>
                    </form>

                </div>

            </div>

        </div>

      </div>
    </div>
  </div>

  @push('js')

  <script>

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

 $(document).ready(function(){


    // check mail
    $('#CheckEmail').on('click',function(e){
       e.preventDefault();
       var email = $('#email').val();
       $.ajax({
        url: "{{route('check.email')}}",
        type: "post",
        data:{
            email:email
        },
        success:function(res){
            if (res.email) {
                $('.error_email').text(res.email);
            }
            if(res.status == 1){
                // $('.tab-1').addClass('html_hide');
                $('.tab-1').css('display','none');
                $('.previous_link').css('display','block');
                $('.tab-2').css('display', 'block');
                $('.email_session').text(res.email);
                $('.email_session_one').val(res.email);
            }
            if (res.status == 0) {
                $('#rowOne').css('display', 'none');
                $('.tab-5').css('display', 'block');
                $('.previous_link').css('display','block');
                $('#email_session_two').text(res.email);
                $('.email_session_two').val(res.email);

            }
        }
       })
    });

    // login check
    $('#login_byuer').on('submit', function(){

        var formData = new FormData($(this)[0]);

    // $('#login_byuer').text('Loading....');

    $.ajax({
        url: "{{ route('buyer.login') }}",
        type: "post",
        data: formData,
        success: function(res){
            console.log(res);
            return;

            if (res.password) {
                $('.error_password').text(res.password);
            }
            if (res.mathcaptcha) {
                $('.Lmathcaptcha').text(res.mathcaptcha);
            }
            if (res.status == 'error') {
                $('.error_password').text(res.message);
            }
            if (res.status == 'success') {
                window.location.href = res.redirectUrl;
            }
        }
    });
});



    // forgot password
    $('#forgotPassword').on('click', function(){

        var email = "{{ session('email') }}";
        $('.tab-3').css('display', 'block');
        $('.tab-2').css('display', 'none');
        $('.forgot_email').val(email);



        $('#send_email').on('click',function(){
            $('#send_email').text('Loading....');
            var email =  $('.forgot_email').val();
            $.ajax({
                    url:"{{route('forgot.password.email')}}",
                    type:"post",
                    data:{
                        email:email
                    },
                    success:function(res){
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
    $('#check_otp').on('click', function(){

        var email = "{{ session('email') }}";
        var otp = $('.email_otp').val();
        var password = $('.new_password').val();
        $('#check_otp').text('Loading....');

            $.ajax({
                    url:"{{route('check.otp.email')}}",
                    type:"post",
                    data:{
                        email:email,
                        otp:otp,
                        password:password
                    },
                    success:function(res){
                       console.log(res.otp);
                        if (res.otp) {
                          toastr.warning(res.otp)
                          $('#check_otp').text('Continue');
                        }
                        if (res.password) {
                          toastr.warning(res.password);
                          $('#check_otp').text('Continue');
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



//  create new account

$('#SignUp').on('click', function() {

var email = $('.email_session_two').val();

var password = $('#res_password').val();
$('#SignUp').text('Loading....');
$.ajax({
     url: "{{ route('create.account')}}",
    type: 'post',
    data: {
        email: email,
        password: password
    },
    success: function(res) {

         console.log(res);
        // return;
        if (res.password) {
            $('.sign_up_password').text(res.password);
        }
        if (res.status == 'success') {

            toastr.success(res.message);
            // window.location.href = "{{ route('buyer.login') }}";
            window.location.reload();

        }

    }
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

    if(value == 5)
    {
        $('#rowOne').css('display','block');
        $('.tab-5').css('display','none');
        $('.error_email').text('');
    }
    if(value == 4)
    {
        $('.tab-3').css('display','block');
        $('.tab-4').css('display','none');
    }
    if(value == 3)
    {
        $('.tab-2').css('display','block');
        $('.tab-3').css('display','none');
    }
    if(value == 2)
    {

        $('.tab-1').css('display','block');
        $('.tab-2').css('display','none');
        $('.error_email').text('');
    }
    else
    {
        $('.previous_link').prop('disabled', true);
    }

}



  </script>


  @endpush
