<div class="col-md-4 col-lg-3 col-sm-6 col-xs-6 mb-4 make-card">
    <a href="{{ route('auto', ['zip' => $zip, 'radius' => $radius, 'home2' => filter_var($home2, FILTER_VALIDATE_BOOLEAN)]) }}"
        style="font-size: 16px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
        class="city">
        {{ $text }}
    </a>
</div>
