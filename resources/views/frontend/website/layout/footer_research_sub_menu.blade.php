<style>
    .nav-container {
        padding: 0;
        background: transparent;
        /* border-top: 2px solid violet;
        border-bottom: 2px solid violet; */
    }

    .nav-menu {
        list-style: none;
        padding: 10px 0;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 0;
        margin: 0;
    }

    .nav-menu li {
        position: relative;
        display: flex;
        align-items: center;
    }

    /* Divider styling - thicker and more visible */
    .nav-menu li:not(:last-child)::after {
        content: '';
        display: block;
        width: 7px; /* Increased from 2px to 3px */
        height: 24px; /* Increased from 20px to 24px */
        background: black;
        margin: 0 15px;
        opacity: 0.7; /* Slightly transparent for better appearance */
    }

    .nav-menu li a {
        text-decoration: none;
        padding: 10px 15px;
        display: block;
        font-weight: 800;
        position: relative;
        transition: all 0.3s ease;
        white-space: nowrap;
        color: #333; /* Ensure good contrast */
        font-family: 'Montserrat', 'Arial Black', sans-serif;
        font-size: 22px;
        
    }

    .nav-menu li a:hover::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 3px;
        background: violet;
    }

    /* Submenu Styling for Web */
    .nav-menu .sub-menu {
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

    .nav-menu .sub-menu li a {
        padding: 8px 12px;
        display: block;
    }

    .nav-menu .sub-menu li a:hover {
        background: violet;
        border-radius: 5px;
        color: white;
    }

    .nav-menu li:hover .sub-menu {
        display: block;
    }

    /* Active link style */
    .nav-menu li a.active::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 3px;
        background: violet;
    }

    /* Mobile-specific styles */
/* Mobile-specific styles - Modified for proper wrapping */
@media (max-width: 768px) {
    .nav-menu {
        padding: 8px 0;
        justify-content: center; /* Center items when they wrap */
        flex-wrap: wrap; /* Allow items to wrap */
    }
    
    .nav-menu li {
        margin: 3px 0;
    }
    
    /* Keep dividers but make them shorter */
    .nav-menu li:not(:last-child)::after {
        height: 18px;
        margin: 0 10px;
    }
    
    .nav-menu li a {
        padding: 8px 10px;
        font-size: 14px;
    }
}

/* For small mobile devices - Simplified */
@media (max-width: 480px) {
    .nav-menu li a {
        padding: 6px 8px;
        font-size: 13px;
    }
    
    .nav-menu li:not(:last-child)::after {
        height: 16px;
        margin: 0 8px;
    }
}

/* For very small screens - Only adjust sizing, keep wrapping */
@media (max-width: 360px) {
    .nav-menu li a {
        font-size: 12px;
        padding: 5px 6px;
    }
    
    .nav-menu li:not(:last-child)::after {
        margin: 0 5px;
        height: 14px; /* Added for consistency */
    }
}
</style>
<section>
    <div class="container nav-container mt-3">
        <ul class="nav-menu">
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