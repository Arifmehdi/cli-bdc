@extends('frontend.website.layout.app')

@section('content')
    <style>
        .item1-links a.active {
            background-color: #027979;
            /* Example background color */
            color: white;
            /* Example text color */
            font-weight: bold;
            /* Make the active link bold */
        }

        @media (max-width: 1000px) {
            .buyer-all-drop {
                display: none;
            }

            .buyer-all-drop.block-on-small-screen {
                display: block;
            }
        }
    </style>


    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="user-dashboard-tilte">My Dashboard</h1>

                        <ol class="breadcrumb text-center user-dashboard-page-bred">
                            <li class=""><a style="color:white" href="{{ route('home') }}">Home<span
                                        style="margin-left:4px; margin-right:4px;">/</span> </a></li>
                            <li class=""><a style="color:white" href="javascript:void(0);">My Dashboard</a></li>

                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Breadcrumb-->

    <!-- Required Modal SHow -->
    <div class="modal fade" id="requiredModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-md" style="margin-top:-140px">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Please Check e-mail and verify Email then unlock your profile</h4>
                    <a href="/" style="text-decoration: underline;color:red">Go back</a> <br />
                    <span style="float:right ">didn't get code ? <a href="{{ route('buyer.again-verify.email') }}"
                            style="text-decoration: underline;">send again</a></span>

                </div>
            </div>
        </div>
    </div>

    {{--    End required modal --}}

    <!--Section-->
    <section class="sptb">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-12">
                    <div class="card">
                        <div style="background:white" class="card-header">
                            <h3 class="card-title">My Dashboard</h3>
                        </div>
                        <div class="card-body text-center item-user border-bottom buyer-card">
                            <div class="profile-pic">
                                <div class="profile-pic-img mb-1">
                                    <span class="bg-success dots" data-bs-toggle="tooltip" data-placement="top"
                                        title="online"></span>

                                    @if (Auth::user()->image)
                                        <img src="{{ asset('/storage') . '/' . Auth::user()->image ?? Auth::user()->image }}"
                                            class="" alt="user"
                                            style="width:73px; height:73px; border-radius:50%">
                                    @else
                                        <img src="{{ asset('frontend/assets/images/user.png') }}" class=""
                                            alt="user">
                                    @endif


                                </div>
                                {{ Auth::user()->name ? Auth::user()->name : Auth::user()->email }}
                            </div>
                            {{-- <i id="drop-view" style="display:none" class="fa fa-ellipsis-v buyer-drop-mobile-icon"></i> --}}


                              <img id="toggle_menu_das" class="menu-toggle-icon" style="float:right; position:relative" width="6%" src="{{asset('/frontend/assets/images/list.png')}}">
                              <div class="buyre-das-mobile-menu" style="position:absolute;width:80%; height:330px; background:white; display:none; box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;margin-top:30px; right:0px; border-radius:5px; z-index:999">
                                <div class="item1-links mb-0">
                                    <a href="{{ route('buyer.profile') }}"
                                        class="d-flex border-bottom {{ request()->routeIs('buyer.profile') ? 'active' : '' }}">
                                        <span class="icon1 me-3"><i class="icon icon-diamond"></i></span> Dashboard
                                    </a>
                                    <a href="{{ route('buyer.profile.info') }}"
                                        class="d-flex border-bottom {{ request()->routeIs('buyer.profile.info') ? 'active' : '' }}">
                                        <span class="icon1 me-3"><i class="icon icon-user"></i></span> Profile
                                    </a>
                                    <a href="{{ route('buyer.profile.edit') }}"
                                        class="d-flex border-bottom {{ request()->routeIs('buyer.profile.edit') ? 'active' : '' }}">
                                        <span class="icon1 me-3"><i class="fa fa-edit"></i></span> Edit Profile
                                    </a>
                                    <a href="{{ route('buyer.profile.favorite') }}"
                                        class="d-flex border-bottom {{ request()->routeIs('buyer.profile.favorite') ? 'active' : '' }}">
                                        <span class="icon1 me-3"><i class="icon icon-heart"></i></span> My Favorite
                                    </a>
                                    @php
                                        $messageCount = \App\Models\Message::where('is_seen', 0)->count();
                                        $mCount = $messageCount > 0 ? $messageCount - 1 : 0;
                                    @endphp
                                    <a href="{{ route('buyer.user.message') }}"
                                        class="d-flex border-bottom {{ request()->routeIs('buyer.user.message') ? 'active' : '' }}">
                                        <span class="icon1 me-3"><i class="icon icon-folder-alt"></i></span> My Message <span
                                            style="margin-top:-5px; margin-left:5px; color:white; background-color:rgb(240, 4, 4); width:20px; height:20px; border-radius:50%; line-height:20px; text-align:center; font-weight:500; padding:1px">
                                            {{ $mCount }}
                                        </span>
                                    </a>
                                    <a href="{{ route('buyer.logout') }}"
                                        class="d-flex {{ request()->routeIs('buyer.logout') ? 'active' : '' }}">
                                        <span class="icon1 me-3"><i class="icon icon-power"></i></span> Logout
                                    </a>
                                </div>
                              </div>
                        </div>
                        <div class="item1-links mb-0 bayer-dasboard-manu-all">
                            <a href="{{ route('buyer.profile') }}"
                                class="d-flex border-bottom {{ request()->routeIs('buyer.profile') ? 'active' : '' }}">
                                <span class="icon1 me-3"><i class="icon icon-diamond"></i></span> Dashboard
                            </a>
                            <a href="{{ route('buyer.profile.info') }}"
                                class="d-flex border-bottom {{ request()->routeIs('buyer.profile.info') ? 'active' : '' }}">
                                <span class="icon1 me-3"><i class="icon icon-user"></i></span> Profile
                            </a>
                            <a href="{{ route('buyer.profile.edit') }}"
                                class="d-flex border-bottom {{ request()->routeIs('buyer.profile.edit') ? 'active' : '' }}">
                                <span class="icon1 me-3"><i class="fa fa-edit"></i></span> Edit Profile
                            </a>
                            <a href="{{ route('buyer.profile.favorite') }}"
                                class="d-flex border-bottom {{ request()->routeIs('buyer.profile.favorite') ? 'active' : '' }}">
                                <span class="icon1 me-3"><i class="icon icon-heart"></i></span> My Favorite
                            </a>
                            @php
                                $messageCount = \App\Models\Message::where('is_seen', 0)->count();
                                $mCount = $messageCount > 0 ? $messageCount - 1 : 0;
                            @endphp
                            <a href="{{ route('buyer.user.message') }}"
                                class="d-flex border-bottom {{ request()->routeIs('buyer.user.message') ? 'active' : '' }}">
                                <span class="icon1 me-3"><i class="icon icon-folder-alt"></i></span> My Message <span
                                    style="margin-top:-5px; margin-left:5px; color:white; background-color:rgb(240, 4, 4); width:20px; height:20px; border-radius:50%; line-height:20px; text-align:center; font-weight:500; padding:1px">
                                    {{ $mCount }}
                                </span>
                            </a>
                            <a href="{{ route('buyer.logout') }}"
                                class="d-flex {{ request()->routeIs('buyer.logout') ? 'active' : '' }}">
                                <span class="icon1 me-3"><i class="icon icon-power"></i></span> Logout
                            </a>
                        </div>

                    </div>








                    <div class="card  mb-5 mb-xl-0 safty-card">
                        <div class="card-header">
                            <h3 class="card-title">Safety Tips Sellers</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled widget-spec  mb-0">
                                <li class="">
                                    <i class="fa fa-check text-success" aria-hidden="true"></i> Meet Seller at public Place
                                </li>
                                <li class="">
                                    <i class="fa fa-check text-success" aria-hidden="true"></i> Check item before you buy
                                </li>
                                <li class="">
                                    <i class="fa fa-check text-success" aria-hidden="true"></i> Pay only after collecting
                                    item
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-9 col-md-12">

                    @yield('content-user')
                </div>
            </div>
        </div>
    </section>
    <!--/Section-->
@endsection

@push('js')



<script>
    document.getElementById('toggle_menu_das').addEventListener('click', function() {
    var menu = document.querySelector('.buyre-das-mobile-menu');
    if (menu.style.display === "none" || menu.style.display === "") {
        menu.style.display = "block";
    } else {
        menu.style.display = "none";
    }
});

</script>



    @if (session()->has('message'))
        <script>
            function toggleDropdown() {
                var dropdown = document.getElementById("buyer-all-drop");
                dropdown.classList.toggle("block-on-small-screen");
            }

            document.getElementById("drop-view").addEventListener("click", toggleDropdown);


            toastr.success('{{ session()->get('message') }}');
        </script>
    @endif



    @if (Auth::user()->email_verified_at == null)
        <script>
            $(document).ready(function() {
                $('.item1-links a').on('click', function() {
                    $('.item1-links a').removeClass('active');
                    $(this).addClass('active');
                });
            });

            $(document).ready(function() {
                $('#requiredModal').modal('show');
            })
        </script>
    @endif
@endpush
