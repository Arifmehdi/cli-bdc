<style>
    /* ===== MOBILE-ONLY HASHTAG STYLES ===== */
    .hashtag-mobile-section {
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        padding: 20px;
        margin: 15px 0;
        width: 100%;
        display: none; /* Hidden by default */
    }

    .hashtag-mobile-section h2 {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f1f1;
    }

    .hashtag-mobile-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
        word-break: break-word; /* Ensures long hashtags wrap */
    }

    .hashtag-mobile-item {
        background: #f0f8ff;
        color: #3498db;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 13px;
        transition: all 0.2s ease;
        max-width: 100%; /* Prevents overflow */
    }

    .hashtag-mobile-item:hover {
        background: #d4e6f7;
        transform: translateY(-2px);
    }

    .hashtag-mobile-item a {
        text-decoration: none;
        color: inherit;
        white-space: nowrap;
    }

    /* ===== SHOW ONLY ON MOBILE ===== */
    @media only screen and (max-width: 600px) {
        .hashtag-mobile-section {
            display: block; /* Only visible on mobile */
        }
    }
</style>

<!-- ===== HASHTAGS SECTION ===== -->
@if(!empty(trim($art->hash_keyword ?? '')))
<div class="hashtag-mobile-section">
    <h2>Trending Tags</h2>
    <ul class="hashtag-mobile-list">
        @foreach(array_filter(explode('#', $art->hash_keyword)) as $hashtag)
            @php $hashtag = trim($hashtag); @endphp
            @if(!empty($hashtag))
                <li class="hashtag-mobile-item">
                    <a href="/search?tag={{ urlencode($hashtag) }}">#{{ $hashtag }}</a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
@endif