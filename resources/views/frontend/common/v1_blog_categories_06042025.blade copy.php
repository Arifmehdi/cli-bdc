<style>
    /* body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .category-section {
            /* background-color: #fff; */
    /* padding: 20px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 300px;
        } */
    .category-section h2 {
        font-size: 22px;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
    }

    .category-section hr {
        border: 0;
        height: 1px;
        background-color: #ddd;
        margin: 10px 0;
    }

    .category-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .category-list li {
        padding: 2px 0;
        font-size: 16px;
        color: #555;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .category-list li:hover {
        color: #007BFF;
    }

    .category-list li a {
        text-decoration: none;
        color: inherit;
    }

    .category-list li a:hover {
        text-decoration: underline;
    }
</style>
<div class="category-section">
    <h2>Blog Categories</h2>
    <hr>
    <ul class="category-list">
        <li><a href="{{ route('frontend.reviews', ['slug' => 'tools_&_expert_device']) }}">Tools Expert Device</a></li>
        <li><a href="{{ route('frontend.reviews', ['slug' => 'car_buying_advice']) }}">Car Buying Advice</a></li>
        <li><a href="{{ route('frontend.reviews', ['slug' => 'beyond_cars']) }}">Beyond Cars</a></li>
        <li><a href="{{ route('research') }}">Research & Reviews</a></li>
        <li><a href="{{ route('frontend.news.page') }}">All News</a></li>
        <li><a href="{{ route('frontend.tips.page')}}">All Tips</a></li>
    </ul>
    <!-- tools_&_expert_device
car_buying_advice
beyond_cars -->
    <hr>
</div>
<hr>