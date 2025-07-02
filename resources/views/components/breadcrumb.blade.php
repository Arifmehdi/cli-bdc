<div class="bannerimg cover-image bg-background3" data-image-src="../assets/images/banners/banner2.jpg">
    <div class="header-text mb-0">
        <div class="container">
            <div class="text-center text-white">
                <ol class="breadcrumb text-center new-page-bred">
                    <li class="">
                        <a style="color:white" href="/">Home
                            <span style="margin-left:4px; margin-right:4px">/</span>
                        </a>
                    </li>
                    @if($category)
                    <li class="">
                        <a style="color:white" href="{{ $route }}">{{ $main }}
                            <span style="margin-left:4px; margin-right:4px">/</span>
                        </a>
                    </li>
                    @endif
                    {{-- <li class="">
                        <a style="color:white"
                            href="javascript:void(0);">{{ $formattedSlug }}
                        </a>
                    </li> --}}
                </ol>
                <h2 class="new-page-tilte">{{ $formattedSlug }}</h2>
            </div>
        </div>
    </div>
</div>
