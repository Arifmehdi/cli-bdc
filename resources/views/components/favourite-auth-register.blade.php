
<div class="modal fade" id="favourite_add_auth_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" style="margin-top:-90px">
        <div class="modal-content">

            <div style="margin-bottom:30px;" class="modal-body">


                <button style="float:right !important" type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>

                <div style="width:100%" class="row registar-modal m-0 p-0">
                    <div class="col-lg-12 col-xl-12">
                        <div class="mb-3 row " id="automatic_rowOne">
                            <div style="text-align:center" class="tab-1 col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                <h4 style="text-align: center !important; margin-top:45px">Log in or sign up</h4>
                                <p style="text-align: center !important; margin-top:23px">Continue with email or your
                                    Facebook or Google account.</p>

                                <input class="email-input"
                                    style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px"
                                    type="text" name="email" id="automatic_email" placeholder="Email Address*" />
                                <div class="text-danger error_email"></div>

                                <button class="ctn-btn" id="automatic_CheckEmail"
                                    style="width:300px; height:42px; margin-top:22px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px">Continue</button>
                                <div style="display: flex; margin-top: 35px; margin-left:75px;" class="or_line">

                                    <div style=" width:125px !important; height: 1px; background: rgb(218, 217, 217);">
                                    </div>
                                    <p style="margin-top:-9px; margin-right:6px; margin-left:6px">Or</p>
                                    <div style=" height: 1px; width:125px !important; background: rgb(218, 217, 217);">
                                    </div>
                                </div>

                                <button class="google-btn"
                                    style="width:300px; height:42px; margin-top:10px; border-radius:11px;background:white;  border:1px solid black; font-size:17px"><i
                                        style="color:rgb(192, 4, 4); margin-right:10px; margin-left:-21px; font-size:19px; font-weight:bold; margin-top:1px"
                                        class="fa fa-google"></i><a href="{{ route('google.login') }}">Continue with
                                        Google </a></button>

                                <button class="google-fb"
                                    style="width:300px; height:42px; margin-top:19px;  border-radius:11px;background:white; border:1px solid black; font-size:17px"><img
                                        style="margin-right:9px; margin-top:-1px"
                                        src="{{ asset('frontend') }}/assets/images/facebook.svg" width="20px"
                                        alt="Used cars for sale Best Dream car facebook logo image"><a
                                        href="{{ route('facebook.login') }}"> Continue with Facebook </a> </button>
                            </div>


                            <div style="display: none !important; text-align:center"
                                class="tab-2 col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                <i style="position:absolute; font-size:26px; top:-19px; float: left; left:-1px; cursor:pointer;"
                                    class="fa fa-angle-left previous_link" onclick="GoBack(2)"></i>
                                <h4 class="text-center fw-bold"
                                    style="margin-top: 49px; margin-right: 74px;margin-bottom:14px; width:100%">Welcome
                                    Back !</h4>
                                <p class="text-center"
                                    style="margin-top: 11px; margin-right: 50px;  margin-bottom: 5px; width:100%">Please
                                    Enter Your Password to Log In.</p>
                                <p class="text-center"
                                    style="margin-top: 11px; margin-right: 50px; margin-bottom: 5px;  width:100%"><i
                                        class="fa fa-envelope"></i> <span class="email_session"></span></p>
                                <form id="automatic_login_byuer">
                                    @csrf
                                    <input
                                        style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px;margin-bottom: 20px"
                                        type="password" name="password" id="password"
                                        placeholder="Enter Your Password*" />
                                    <input type="hidden" name="email_session_one" class="email_session_one">
                                    <div class="text-danger error_password"></div>
                                    <div class="p-0 col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group ">
                                            <p
                                                style="color:rgb(32, 32, 31); font-weight:bold; margin-bottom:15px; margin-top:10px; font-size:12px">
                                                <span class="text-danger">*</span> Security Question (Enter the Correct
                                                answer)</p>
                                            <div style="display: flex">
                                                <div id="captchaLabel" class="cap-field"
                                                    style="background-color:rgb(5, 145, 145); width:30%;  margin-right:10px; text-align:center; padding-top:7px; font-weight:600;  margin-top:2px; margin-left:75px; height:35px; border-radius:5px; color:white">
                                                    {{ app('mathcaptcha')->label(true) }}


                                                </div>
                                                <div>
                                                    <input id="captchaInput" style="width:73%; border-radius:5px"
                                                        class="res-input form-control @error('mathcaptcha') is-invalid @enderror"
                                                        type="text" name="mathcaptcha" placeholder="Your answer">
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
                                        id="automatic_forgotPassword" class="text-center">Forgot your password?</a>

                                    <button type="submit" id="automatic_loginBtn"
                                        style="width:300px; height:42px; margin-top:22px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px">Login</button>
                                </form>

                            </div>

                            <div style="display: none !important; text-align:center"
                                class="tab-3 col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                <i style="position:absolute; font-size:26px; top:-19px; float: left; left:-1px; cursor:pointer;"
                                    class="fa fa-angle-left previous_link" onclick="GoBack(3)"></i>

                                <h4 class="text-center fw-bold"
                                    style="margin-top: 49px; margin-right: 74px; width:100%">Forgot your password?</h4>
                                <p class="text-center"
                                    style="margin-top:9px; margin-right: 74px;margin-bottom:14px; width:100%">Enter
                                    your email address and we’ll send you a reset code.</p>
                                <input
                                    style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px;margin-bottom: 20px"
                                    type="email" name="forgot_email" id="forget_email" class="forgot_email"
                                    placeholder="Enter Your Email*" />
                                <input type="hidden" name="email_session_one" class="email_session_one">
                                <div class="text-danger email-error"></div>


                                <button type="button" id="automatic_send_email"
                                    style="width:300px; height:42px; margin-top:5px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px">Send</button>
                            </div>

                            <div id="otp_section" style="display: none !important; text-align:center"
                                class="tab-4 col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                <i style="position:absolute; font-size:26px; top:-19px; float: left; left:5px; cursor:pointer;"
                                    cursor:pointer;" class="fa fa-angle-left previous_link" onclick="GoBack(4)"></i>
                                <h4 class="text-center fw-bold"
                                    style="margin-top: 49px; margin-right: 74px;margin-bottom:18px; width:100%">Verify
                                    your email</h4>
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

                                <button type="button" id="automatic_check_otp"
                                    style="width:300px; height:42px; margin-top:22px; background:rgb(17, 17, 17); border-radius:11px; color:white; border:1px solid black; font-size:17px;margin-bottom: 15px">Continue</button>

                                <a href="#" id="automatic_send_email"
                                    style="text-decoration:underline; color:green; margin-left: 240px">Resend</a>
                            </div>



                        </div>


                        <div class="mb-3 row tab-5" style="display:none; text-align:center">
                            <i style="position:absolute; font-size:26px; top:-19px;
                            left:-207px; cursor:pointer;"
                                class="fa fa-angle-left previous_link-signup" onclick="GoBack(5)"></i>

                            <h4 style="margin-top: 35px; margin-bottom: 10px;" class="text-center ">Sign up</h4>

                            <p class="text-center fw-medium"><i class="fa fa-envelope"></i> <span
                                    id="automatic_email_session_two"></span></p>

                            <form id="automatic_SignUpForm">
                                @csrf

                                <input class="pass-input"
                                    style="width:300px; height:42px; border-radius:6px; padding-left:8px; margin-top:9px"
                                    type="password" name="password" id="res_password"
                                    placeholder="Enter Your Password*" />



                                {{-- <span style="text-align: center">Your password must be at least 6 characters long, and contain a number or a symbol.</span> --}}
                                <input type="hidden" name="email_session" class="email_session_two">
                                {{-- <div class="text-danger sign_up_email"></div> --}}
                                <div class="text-danger sign_up_password"></div>
                                <p
                                    style="text-align:center; margin-left: 63px;
                             margin-right:58px ; font-size:12px; margin-top:10px">
                                    Your password must be at least 8 characters long and contain at least 1 letter and a
                                    number or a symbol..

                                </p>

                                <div class="p-0 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group ">
                                        <p
                                            style="color:rgb(32, 32, 31); font-weight:bold; font-size:12px; margin-bottom:13px; margin-top:10px">
                                            <span class="text-danger">*</span> Security Question (Enter the Correct
                                            answer)</p>
                                        <div style="display: flex">
                                            <div
                                                style="background-color:rgb(5, 145, 145); width:30%;  margin-right:10px; text-align:center; padding-top:7px; font-weight:600;  margin-top:2px; margin-left:75px; height:35px; border-radius:5px; color:white">
                                                {{ app('mathcaptcha')->label(true) }}


                                            </div>
                                            <div>
                                                <input style="width:73%; border-radius:5px" class="form-control"
                                                    type="text" name="mathcaptcha" id="mathcaptcha"
                                                    placeholder="Your answer">
                                                <span id="automatic_Smathcaptcha" class="text-danger" role="alert">
                                                    @error('mathcaptcha')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>

                                        </div>



                                    </div>
                                </div>

                                <button class="ctn-btn" id="automatic_SignUpBtn" type="submit"
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
