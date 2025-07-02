<div class="logo-card @isset($style) additional-make @endisset" @isset($style) style="{{ $style }}" @endisset>
    <a href="{{ route('auto', ['make' => $make, 'homeBodySearch' => 'used']) }}"
        class="logo-link">
        <img class="logo-img" src="{{ asset('/frontend/assets/images/make-logo/'.$src) }}"
            alt="Used {{ ucfirst($make) }} cars for sale" />
    </a>
</div>
