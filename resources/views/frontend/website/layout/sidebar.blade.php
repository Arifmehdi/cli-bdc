<div class="settings-sidebar-area" id="settings-sidebar-area">
    <nav  class="clearfix horizontalMenu d-md-flex">
    <a  class="settings-bar"><img style="width:11px; height:11px; float:right" class="mt-4 me-4 colse-img-topbar" src="{{asset('/frontend/assets/images/close.png')}}"/></a>
    <hr style="margin-top: 50px !important; margin-bottom:6px">
        <ul   class="horizontalMenu-list mobile-hori-menu">
            @php
            // Sort the $header_menus array by position

            // @dd($header_menus);
            @endphp
            @foreach ($header_menus as $menu)
            <li style="margin-top: 2px;" aria-haspopup="true">
                <a style="color: black; font-size:16px"  href="{{ ($menu->submenus->isNotEmpty()) ? '#' : (($menu->route_url != null) ? route($menu->route_url) : url($menu->slug)) }}" >
                    {{ $menu->name }}
                    @if ($menu->submenus->isNotEmpty())
                        <span style="color: black; " class="m-0 fa fa-caret-down"></span>
                    @endif
                </a>
                @if ($menu->submenus->isNotEmpty())
                    <ul class="sub-menu">
                        @foreach ($menu->submenus as $submenu)
                            <li aria-haspopup="true">
                                @if ($submenu->param)
                                <a style="color: black; font-size:15px" href="{{ route($submenu->route_url, ['body' => $submenu->param,'home'=>true]) }}">{{ $submenu->name }}</a>
                                @else
                                <a style="color: black; font-size:15px" href="{{ ($submenu->route_url != null) ? route($submenu->route_url) : url($submenu->slug)   }}">{{ $submenu->name }}</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
            <li style="margin-top: 2px;" aria-haspopup="true">
                <a style="color: black; font-size:16px" href="{{ route('frontend.research.review') }}">Research
                    <span style="color: black;" class="m-0 fa fa-caret-down"></span>
                </a>
                <ul class="sub-menu">
                    <li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.research.auto.news') }}">Auto News</a></li>
                    {{--<li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('research') }}">Reviews</a></li>
                    <li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.reviews', ['slug' => 'tools_&_expert_device']) }}">Tools and Advice</a></li>--}}

                    <li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.research.auto.review') }}">Reviews</a></li>
                    <li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.research.tools.advice') }}">Tools and Advice</a></li>
                    <li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.research.car.buying.advice') }}">Car Buying Advice</a></li>
                    <li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.research.car.tips') }}">Car Tips</a></li>
                    <li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.research.videos') }}">Videos</a></li>
                    <li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.faq') }}">FAQ</a></li>
                </ul>
            </li>
            <li style="margin-top: 2px;" aria-haspopup="true">
                <a style="color: black; font-size:16px" href="{{ route('frontend.beyondcar') }}">Beyond Cars
                    <span style="color: black;" class="m-0 fa fa-caret-down"></span>
                </a>
                <ul class="sub-menu">
                    <li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.beyondcar.news') }}">News</a></li>
                    <li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.beyondcar.innovation') }}">Innovation</a></li>
                    {{--<li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.beyondcar.toolsandadvice') }}">Tools and Advice</a></li>--}}
                    <li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.beyondcar.opinion') }}">Opinion</a></li>
                    <li aria-haspopup="true"><a style="color: black; font-size:15px" href="{{ route('frontend.beyondcar.financial') }}">Financial</a></li>
                </ul>
            </li>
            @if (!auth()->user())
            <li style="margin-top:2px;" aria-haspopup="true"><a style="color:black; font-size:16px" href="{{ route('favourite.listing') }}">Favorites </a></li>
            @endif
            @php
            $url = (auth()->check() && auth()->user()->hasAnyRole(['admin', 'editor',
            'dealer'])) ? '/admin/dashboard' : '/profile';
            @endphp
            @if(Auth()->check())
            <li style="margin-top:2px;" aria-haspopup="true"><a style="color:black; font-size:16px" href="{{ route('favourite.listing') }}">Favorites </a></li>
            <li style="margin-top:2px;" aria-haspopup="true"><a style="color:black; font-size:16px" href="{{ route('buyer.user.message') }}">Message </a></li>
            <li style="margin-top:2px;" aria-haspopup="true"><a style="color:black; font-size:16px" href="{{  $url }}">Account Information </a></li>
            <li style="margin-top:2px;" aria-haspopup="true"><a style="color:black; font-size:16px" href="{{ route('buyer.logout')}}">Log out </a></li>
            @else
            <li style="margin-top:2px;" aria-haspopup="true"><a href="#"   data-bs-toggle="modal" data-bs-target="#exampleModal"class="text-dark" >Login</a></li>
            @endif
            <div style="position:relative;">
                <form id="searchForm" action="{{ route('search') }}" method="GET">
                    <input id="searchInput" style="float:right; margin-top:24px; width:400px; height:42px; border-radius:20px; background:white; border:1px solid rgb(105, 105, 105); font-size:16px; font-weight:500;  padding-left:40px; color:black;" class="search-item" type="text" name="query" placeholder="Search" />
                    <i style="position: absolute; right: 203px; top: 38px; color:rgb(10, 10, 10)" class="fa fa-search"></i>
                </form>
            </div>
        </ul>
    </nav>
</div>
@push('script')
@endpush
