<style>
    .category-section {
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        padding: 25px;
        margin: 20px 0;
        max-width: 100%;
    }

    .category-section h2 {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f1f1;
    }

    .category-group {
        margin-bottom: 25px;
    }

    .category-group-title {
        display: flex;
        align-items: center;
        font-size: 18px;
        font-weight: 600;
        color: #3498db;
        margin-bottom: 15px;
        padding-left: 10px;
        border-left: 4px solid #3498db;
    }

    .category-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0;
    }

    .category-list li {
        padding: 8px 12px;
        font-size: 15px;
        color: #555;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 6px;
    }

    .category-list li:hover {
        background-color: #f8f9fa;
        transform: translateX(3px);
    }

    .category-list li a {
        text-decoration: none;
        color: #34495e;
        display: block;
        transition: color 0.2s;
    }

    .category-list li:hover a {
        color: #2980b9;
        text-decoration: none;
    }

    .category-list li.active {
        background-color: #e8f4fc;
    }

    .category-list li.active a {
        color: #2980b9;
        font-weight: 500;
    }

    /* Hide on mobile devices (phones, 600px and down) */
    @media only screen and (max-width: 990px) {
        .category-section {
            display: none;
        }
    }

    /* Show on tablets and larger (601px and up) */
    @media only screen and (min-width: 991px) {
        .category-section {
            display: block;
        }
    }

    /* Responsive adjustments for tablets */
    @media (max-width: 768px) {
        .category-list {
            grid-template-columns: 1fr;
        }

        .category-section {
            padding: 15px;
        }

        .category-section h2 {
            font-size: 20px;
        }
    }

    @media (max-width: 900px) {
        .category-group-title {
            font-size: 16px;
        }

        .category-list li {
            font-size: 14px;
            padding: 6px 10px;
        }
    }
</style>

<style>
    /* ===== NEW HASHTAG STYLES ===== */
    .hashtag-section {
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        padding: 25px;
        margin: 20px 0;
        max-width: 100%;
    }

    .hashtag-section h2 {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f1f1;
    }

    .hashtag-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .hashtag-item {
        background: #f0f8ff;
        color: #3498db;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .hashtag-item:hover {
        background: #d4e6f7;
        transform: translateY(-2px);
    }

    .hashtag-item a {
        text-decoration: none;
        color: inherit;
    }


    /* ===== HIDE ON MOBILE (like your category-section) ===== */
    @media only screen and (max-width: 990px) {
        .hashtag-section {
            display: none;
        }
    }

    /* ===== KEEP ORIGINAL CATEGORIES (NO CHANGES) ===== */
    /* .category-section {
        /* Your existing category styles remain untouched */
    /* } */


</style>

<!-- ===== NEW HASHTAGS SECTION ===== -->
@if(!empty(trim($art->hash_keyword ?? '')))
<div class="hashtag-section">
    <h2>Trending Tags</h2>
    <ul class="hashtag-list">
        @foreach(array_filter(explode('#', $art->hash_keyword)) as $hashtag)
            @php $hashtag = trim($hashtag); @endphp
            @if(!empty($hashtag))
                <li class="hashtag-item">
                    <a href="#">#{{ $hashtag }}</a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

<!-- categories menu start here  -->
<div class="category-section">
    <h2>Categories</h2>

    <div class="category-group">
        <div class="category-group-title">Research</div>
        <ul class="category-list">
            {{--<li><a href="{{ route('frontend.research.review') }}">Research</a></li>--}}
            <li><a href="{{ route('frontend.research.auto.news') }}">Auto News</a></li>
            <li><a href="{{ route('frontend.research.auto.review') }}">Reviews</a></li>
            <li><a href="{{ route('frontend.research.tools.advice') }}">Tools & Advice</a></li>
            <li><a href="{{ route('frontend.research.car.buying.advice') }}">Car Buying Advice</a></li>
            <li><a href="{{ route('frontend.research.car.tips') }}">Car Tips</a></li>
            <li><a href="{{ route('frontend.research.videos') }}">Video</a></li>
            <li><a href="{{ route('frontend.faq') }}">FAQs</a></li>
        </ul>
    </div>

    <div class="category-group">
        <div class="category-group-title">Beyond Cars</div>
        <ul class="category-list">
            {{--<li><a href="{{ route('frontend.beyondcar') }}">Beyond Cars</a></li>--}}
            <li><a href="{{ route('frontend.beyondcar.news')  }}">News</a></li>
            <li><a href="{{ route('frontend.beyondcar.innovation') }}">Innovation</a></li>
            <li><a href="{{ route('frontend.beyondcar.opinion')}}">Opinion</a></li>
            <li><a href="{{ route('frontend.beyondcar.financial') }}">Financial</a></li>
        </ul>
    </div>
</div>
<!-- categories menu end here  -->