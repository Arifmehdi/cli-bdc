{{-- @dump($attributes->getAttributes()) --}}

@props([
    'data',
    'path' => '',
    'type' => 'NEWS',
    'route' => '#',
    'main' => '#',
])
<div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12 mb-4">
    <div class="card h-100">
        <div class="blog">
            @if (isset($data->img))
            <a href="{{ $route }}">
                <img class="card-img-top news-show-image-middle"
                    src="{{ asset($path . $data->img) }}"
                    alt="img"
                    onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';"/>
            </a>
            @elseif($data->img == '')
            <a href="{{ $route }}">
                <img class="card-img-top news-show-image-middle"
                    src="{{ asset('frontend/found/NotFound.png') }}"
                    alt="img">
            </a>
            @endif
        </div>
        <div class="card-body">
            <a href="{{ $main }}" class="text-dark">
                <p class="card-category" style="font-size:12px; color:rgb(4, 122, 126)">{{ $type }}</p>
            </a>
            <a href="{{ $route }}" class="text-dark">
                <h5 class="card-title news-bold-title">{{ $data->title }}</h5>
            </a>
            <p class="card-text">{{ Str::limit($data->sub_title, 100, '...') }}</p>
        </div>
        <div class="card-footer bg-white border-top-0">
            <small class="text-muted">{{\Carbon\Carbon::parse($data->created_at)->format('F d, Y')}}</small>
        </div>
    </div>
</div>
