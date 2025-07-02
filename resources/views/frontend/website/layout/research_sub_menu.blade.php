<style>
    .nav-container-header {
        padding: 0;
        border-top: 2px solid violet;
        border-bottom: 2px solid violet;
        background: transparent;
    }

    .nav-menu-header {
        list-style: none;
        padding: 10px 0;
        display: flex;
        gap: 20px;
        justify-content: center;
        align-items: center;
    }

    .nav-menu-header li {
        position: relative;
    }

    .nav-menu-header li a {
        text-decoration: none;
        padding: 10px 15px;
        display: block;
        font-weight: bold;
        position: relative;
        transition: all 0.3s ease;
    }

    .nav-menu-header li a:hover::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 3px;
        background: violet;
        font-weight: bold;
    }

    /* Submenu Styling for Web */
    .nav-menu-header .sub-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background: rgba(190, 189, 189, 0.8);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        list-style: none;
        padding: 10px;
        min-width: 200px;
        z-index: 1000;
        border-radius: 5px;
    }

    .nav-menu-header .sub-menu li a {
        padding: 8px 12px;
        display: block;
    }

    .nav-menu-header .sub-menu li a:hover {
        background: violet;
        border-radius: 5px;
        color: white;
    }

    .nav-menu-header li:hover .sub-menu {
        display: block;
    }

    /* Hide Dropdown on Web & Show Only in Mobile */
    .menu-dropdown {
        display: none;
    }

    @media (max-width: 768px) {
        .nav-menu-header {
            display: none;
        }

        .menu-dropdown {
            display: block;
            width: 100%;
            border: 2px solid violet;
            background: transparent;
            padding: 8px;
            font-size: 16px;
            color: black;
        }
    }

    
    /* Add this to your existing CSS */
    .nav-menu-header li a.active::after
    {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 3px;
        background: violet;
        font-weight: bold;
    }
</style>
<section>
    <div class="container nav-container-header mt-3">
        <!-- Mobile Dropdown -->
        <select class="menu-dropdown" onchange="navigateToPage(this)">
            <option value="" selected disabled>Choose Category</option>
            <option value="{{ route('frontend.research.auto.news') }}" @if(Request::routeIs('frontend.research.auto.news')) selected @endif>Auto News</option>
            <option value="{{ route('frontend.research.auto.review') }}" @if(Request::routeIs('frontend.research.auto.review')) selected @endif>Reviews</option>
            <option value="{{ route('frontend.research.tools.advice') }}" @if(Request::routeIs('frontend.research.tools.advice')) selected @endif>Tools and Advice</option>
            <option value="{{ route('frontend.research.car.buying.advice') }}" @if(Request::routeIs('frontend.research.car.buying.advice')) selected @endif>Car Buying Advice</option>
            <option value="{{ route('frontend.research.car.tips') }}" @if(Request::routeIs('frontend.research.car.tips')) selected @endif>Car Tips</option>
            <option value="{{ route('frontend.research.videos') }}" @if(Request::routeIs('frontend.research.videos')) selected @endif>Videos</option>
            <option value="{{ route('frontend.faq') }}" @if(Request::routeIs('frontend.faq')) selected @endif>FAQ</option>
            {{--<option value="#">Other</option>--}}
        </select>

        <!-- Desktop Navigation -->
        <ul class="nav-menu-header">
            <li><a href="{{ route('frontend.research.auto.news') }}" class="@if(Request::routeIs('frontend.research.auto.news')) active @endif">Auto News</a></li>
            <li><a href="{{ route('frontend.research.auto.review') }}" class="@if(Request::routeIs('frontend.research.auto.review')) active @endif">Reviews</a></li>
            <li><a href="{{ route('frontend.research.tools.advice') }}" class="@if(Request::routeIs('frontend.research.tools.advice')) active @endif">Tools and Advice</a></li>
            <li><a href="{{ route('frontend.research.car.buying.advice') }}" class="@if(Request::routeIs('frontend.research.car.buying.advice')) active @endif">Car Buying Advice</a></li>
            <li><a href="{{ route('frontend.research.car.tips') }}" class="@if(Request::routeIs('frontend.research.car.tips')) active @endif">Car Tips</a></li>
            <li><a href="{{ route('frontend.research.videos') }}" class="@if(Request::routeIs('frontend.research.videos')) active @endif">Videos</a></li>
            <li><a href="{{ route('frontend.faq') }}" class="@if(Request::routeIs('frontend.faq')) active @endif">FAQ</a></li>
            {{--<li>
                <a href="#">More <i class="fa fa-caret-down"></i></a>
                <ul class="sub-menu">
                    <li><a href="#">Other</a></li>
                </ul>
            </li>--}}
        </ul>
    </div>
</section>

<script>
    function navigateToPage(select) {
        if (select.value) {
            window.location.href = select.value;
        }
    }
</script>
