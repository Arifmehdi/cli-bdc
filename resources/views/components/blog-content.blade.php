@if($blogs->count() > 1)
<section aria-label="{{$categoryInfo}} Articles" class="sptb" style="padding-top: 5px !important; padding-bottom:5px !important">
    <div style="border-radius:5px; padding-bottom:25px" class="container bg-white p-5">
        <div class="text-center section-title center-block">
            <h2 class="h2" style="margin-top:15px">{{$categoryInfo}}</h2>
            <!-- Optional subtitle with SEO-friendly keywords -->
            <!-- <p class="lead">Explore the latest reviews and insights on vehicles and automotive trends.</p> -->
        </div>

        <!-- Carousel with structured data -->
        <div
            id="defaultCarousel1"
            class="owl-carousel Card-owlcarousel owl-carousel-icons mb-5"
            style="width:90% !important; margin: 0 auto !important;"
            itemscope itemtype="https://schema.org/ItemList"
        >
            @foreach ($blogs as $new)
                @php
                    $sub = ucwords($new->subcategory_slug);
                    $title = Str::limit($new->title, 21, '...');
                    $sub_title = Str::limit(strip_tags($new->description), 100, '...') ?? Str::limit($new->sub_title, 100, '...');
                    $articleUrl = route($articleUrlInfo, ['categorySlug' => $new->subcategory_slug, 'slug' => $new->slug]);
                @endphp

                <div class="item" itemprop="itemListElement" itemscope itemtype="https://schema.org/Article">
                    <div class="mb-0 card">
                        <div class="item7-card-img" style="position: relative;">
                            <a href="{{ $articleUrl }}" aria-label="Read more: {{ $title }}">
                                <span class="visually-hidden">Read more: {{ $title }}</span>
                              </a>

                            <!-- Category Badge -->
                            <x-image-badge data="{{ $sub }}"/>

                            @if (isset($new->img))
                                <img
                                    src="{{ asset('frontend/assets/images/blog/' . $new->img) }}"
                                    alt="{{ $title }} - Review & Insights"
                                    class="news-img"
                                    style="width: 100%; height:260px; object-fit:cover;"
                                    itemprop="image"
                                    loading="lazy"
                                    onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';"
                                />
                            @else
                                <img
                                    src="{{ asset('frontend/found/NotFound.png') }}"
                                    alt="Article Image: {{ $title }}"
                                    class="news-img"
                                    style="width: 100%; height:260px; object-fit:cover;"
                                    loading="lazy"
                                />
                            @endif
                        </div>
                        <div class="card-body p-4" itemprop="description">
                            <a href="{{ $articleUrl }}" class="text-dark">
                                <h2 class="fs-20 news-tit hyperlink-title" style="font-weight:600" itemprop="headline">{{ $title }}</h2>
                            </a>
                            <p style="height:80px" class="news-par" itemprop="abstract">{{ $sub_title }}</p>
                            <a
                                href="{{ $articleUrl }}"
                                style="float:right; color:darkcyan"
                                aria-label="Continue reading: {{ $title }}"
                            >
                                Read more <i class="fa fa-angle-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- "See All" Button -->
        <div style="text-align:center; margin-top:30px !important">
            <a
                href="{{ $main }}"
                style="border:2px solid black; border-radius:17px; padding-left:35px; padding-right:35px; padding-top:3px; padding-bottom:3px;"
                class="btn"
                aria-label="Explore all {{$categoryInfo}} articles"
            >
                See All
            </a>
        </div>
    </div>
</section>
@endif
