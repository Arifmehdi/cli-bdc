<style>
    .mobile-categories-container {
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 16px;
        margin: 12px 0;
        width: calc(100% - 8px);
    }

    .mobile-categories-header {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f1f1;
        display: flex;
        align-items: center;
    }

    .mobile-categories-header::before {
        content: "≡";
        font-size: 24px;
        margin-right: 10px;
        color: #3498db;
    }

    .mobile-category-group {
        margin-bottom: 20px;
    }

    .mobile-category-title {
        display: flex;
        align-items: center;
        font-size: 16px;
        font-weight: 600;
        color: #3498db;
        margin-bottom: 12px;
        padding-left: 8px;
        border-left: 4px solid #3498db;
    }

    .mobile-category-title::before {
        content: "›";
        margin-right: 8px;
        font-size: 18px;
    }

    .mobile-category-items {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .mobile-category-item {
        padding: 10px 12px;
        font-size: 14px;
        color: #555;
        background-color: #f8f9fa;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .mobile-category-item:hover {
        background-color: #e8f4fc;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .mobile-category-link {
        text-decoration: none;
        color: #34495e;
        display: flex;
        align-items: center;
        transition: color 0.2s;
    }

    .mobile-category-link::before {
        content: "•";
        color: #3498db;
        margin-right: 8px;
        font-size: 18px;
    }

    .mobile-category-item:hover .mobile-category-link {
        color: #2980b9;
    }

    .mobile-category-item.active {
        background-color: #e8f4fc;
        border-left: 3px solid #3498db;
    }

    /* Show only on mobile devices */
    @media only screen and (min-width: 601px) {
        .mobile-categories-container {
            display: none;
        }
    }

    /* Adjustments for small mobile devices */
    @media (max-width: 400px) {
        .mobile-category-items {
            grid-template-columns: 1fr;
        }

        .mobile-categories-container {
            padding: 12px;
        }

        .mobile-categories-header {
            font-size: 18px;
        }

        .mobile-category-title {
            font-size: 15px;
        }

        .mobile-category-item {
            padding: 8px 10px;
        }
    }

    /* Animation for better interaction */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .mobile-category-group {
        animation: fadeIn 0.3s ease forwards;
    }

    .mobile-category-group:nth-child(2) {
        animation-delay: 0.1s;
    }
</style>

<div class="mobile-categories-container">
    <h2 class="mobile-categories-header">Browse Categories</h2>

    <div class="mobile-category-group">
        <div class="mobile-category-title">Research</div>
        <ul class="mobile-category-items">
            <li class="mobile-category-item"><a class="mobile-category-link" href="{{ route('frontend.research.auto.news') }}">Auto News</a></li>
            <li class="mobile-category-item"><a class="mobile-category-link" href="{{ route('frontend.research.auto.review') }}">Reviews</a></li>
            <li class="mobile-category-item"><a class="mobile-category-link" href="{{ route('frontend.research.tools.advice') }}">Tools & Advice</a></li>
            <li class="mobile-category-item"><a class="mobile-category-link" href="{{ route('frontend.research.car.buying.advice') }}">Car Buying Advice</a></li>
            <li class="mobile-category-item"><a class="mobile-category-link" href="{{ route('frontend.research.car.tips') }}">Car Tips</a></li>
            <li class="mobile-category-item"><a class="mobile-category-link" href="{{ route('frontend.research.videos') }}">Videos</a></li>
            <li class="mobile-category-item"><a class="mobile-category-link" href="{{ route('frontend.faq') }}">FAQs</a></li>
        </ul>
    </div>

    <div class="mobile-category-group">
        <div class="mobile-category-title">Beyond Cars</div>
        <ul class="mobile-category-items">
            <li class="mobile-category-item"><a class="mobile-category-link" href="{{ route('frontend.beyondcar.news') }}">News</a></li>
            <li class="mobile-category-item"><a class="mobile-category-link" href="{{ route('frontend.beyondcar.innovation') }}">Innovation</a></li>
            <li class="mobile-category-item"><a class="mobile-category-link" href="{{ route('frontend.beyondcar.opinion')}}">Opinion</a></li>
            <li class="mobile-category-item"><a class="mobile-category-link" href="{{ route('frontend.beyondcar.financial') }}">Financial</a></li>
        </ul>
    </div>
</div>
