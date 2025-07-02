<!-- Display your ad here -->
<div style="margin:0 auto" class="col-lg-4 col-md-6 col-xl-4 col-sm-6 col-xs-12">
    @php
        $banners = \App\Models\Banner::where('position', 'auto page middle')->first();
    @endphp
    <!-- Your ad content goes here -->
    @isset($banners->image)
        <a href="{{ $banners->url ? $banners->url : '' }}" {{ $banners->new_window == 1? 'target=_blank' : '' }}>
            <img class="auto-middle-banner"
                src="{{ asset('/dashboard/images/banners/' . $banners->image) }}"
                alt="Used cars for sale for Best Dream car middle banner image" />
        </a>
    @else
        <img class="auto-middle-banner" src="{{ asset('/dashboard/images/banners/middle.png') }}"
            alt="Used cars for sale for Best Dream car middle banner alter image" />
    @endisset
</div>
