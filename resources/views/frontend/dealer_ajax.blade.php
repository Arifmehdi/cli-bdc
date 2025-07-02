<div class="">
    <div class="item2-gl ">
        <div class=" mb-0 ">
            <div class="">
                <div class="bg-white p-5 item2-gl-nav d-lg-flex auto-page-filter-topbar">
                    <h6 class="mb-0 mt-3 text-left show-text">Showing 1 to {{ $single_inventories_count }} of
                        {{ $total_count }} results</h6>
                    <ul style="visibility: hidden" class="nav item2-gl-menu ms-auto mt-1">
                        <li class=""><a href="#tab-11" class=" show" data-bs-toggle="tab" title="List style"><i
                                    class="fa fa-list"></i></a></li>
                        <li><a href="#tab-12" data-bs-toggle="tab" class="" title="Grid"><i
                                    class="fa fa-th"></i></a></li>
                    </ul>
                    <div class="d-sm-flex item2-gl-group mobile-short-by">
                        <label class="me-2 mt-2 mb-sm-1">Sort By:</label>
                        <div class="selectgroup">
                            <label class="selectgroup-item mb-md-0">
                                <input type="radio" name="value" value="Price" class="selectgroup-input"
                                    checked="">
                                <span class="selectgroup-button">Price <i class="fa fa-sort ms-1"></i></span>
                            </label>
                            <label class="selectgroup-item mb-md-0">
                                <input type="radio" name="value" value="Popularity" class="selectgroup-input">
                                <span class="selectgroup-button flex-wrap">Popularity</span>
                            </label>
                            <label class="selectgroup-item mb-md-0">
                                <input type="radio" name="value" value="Latest" class="selectgroup-input">
                                <span class="selectgroup-button">Latest</span>
                            </label>
                            <label class="selectgroup-item mb-0">
                                <input type="radio" name="value" value="Rating" class="selectgroup-input">
                                <span class="selectgroup-button">Rating</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane active" id="tab-11">

                <div class="row">

                    @if(isset($message) && !empty($message))
                    {!! $message !!}
                    @endif

                    @forelse ($inventories as $index => $inventory)
                        <div class="col-lg-4 col-md-6 col-xl-4 col-sm-12">
                            <div class="card overflow-hidden">
                                <div style="margin-top:-21px !important" class="item-card9-img">
                                    @php
                                        $image_obj = $inventory->local_img_url;
                                        $image_splice = explode(',', $image_obj);
                                        $image = str_replace(['[', "'"], '', $image_splice[0]);

                                        $vin_string_replace = str_replace(' ', '', $inventory->vin);
                                        $route_string = str_replace(
                                            ' ',
                                            '',
                                            $inventory->year .
                                                '-' .
                                                $inventory->make .
                                                '-' .
                                                $inventory->model .
                                                '-in-' .
                                                $inventory->dealer_city .
                                                '-' .
                                                $inventory->dealer_state,
                                        );
                                    @endphp

                                    <div class="item-card9-imgs">
                                        <a  class="link " href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"></a>

                                        @if ($image_obj != '' && $image_obj != '[]')

                                            <img width="100%"  src="{{ asset('frontend/') }}/{{ $image }}"
                                                alt="Used cars for sale {{ $inventory->title }}, price is {{ $inventory->price }}, vin {{ $inventory->vin }} in {{ $inventory->dealer_city }},{{ $inventory->dealer_state }}, dealer name is {{ $inventory->dealer_name }} Best Dream car dealer page image"
                                                class="auto-ajax-photo"/>
                                        @elseif ($image_obj == '[]')
                                            <img width="100%" src="{{ asset('frontend/uploads/NotFound.png') }}"
                                                alt="Used cars for sale coming soon dealer image dream best">
                                        @else
                                            <img width="100%" src="{{ asset('frontend/uploads/NotFound.png') }}"
                                                alt="Used cars for sale coming soon dealer image dream best">
                                        @endif
                                    </div>
                                    <a data-id="{{ $inventory->id }}" href="javascript:void(0)" id="quick"><img
                                            class="quick-option"
                                            src="{{ asset('/frontend/assets/images/more.png') }}" alt="Used cars for sale Best Dream car more image"/></a>

                                    <div class="hide-action" id="hide-action-{{ $inventory->id }}" >
                                        <input type="hidden" id="all_id">
                                        <a href="javascript:void(0)"
                                            style="display:flex; align-items:center; margin-top:20px; margin-left:15px; text-decoration:none; margin-bottom:13px"
                                            id="view-data">
                                            <img style="width:20px; height:20px;"
                                                src="{{ asset('/frontend/assets/images/show.png') }}" class="me-3" alt="Used cars for sale Dream Best dealer show image"/>
                                            <p style="color:black; font-size:15px; margin:0;">Quick View</p>
                                        </a>

                                        <a href="javascript:void(0)"
                                        data-id="{{ $inventory->id }}"
                                            style="display:flex; align-items:center; margin-left:15px; text-decoration:none; margin-bottom:13px" id="compare_listing">
                                            <img style="width:20px; height:20px;"
                                                src="{{ asset('/frontend/assets/images/swap.png') }}" class="me-3" alt="Used cars for sale Dream Best dealer swap image"/>
                                            <p style="color:black; font-size:15px; margin:0;">Compare Listing</p>
                                        </a>

                                        {{-- <a data-bs-toggle="modal" data-bs-target="#ShareModal" href="javascript:void(0)"
                                            style="display:flex; align-items:center; margin-left:15px; text-decoration:none; margin-bottom:13px">
                                            <img style="width:20px; height:20px;"
                                                src="{{ asset('/frontend/assets/images/share.png') }}"
                                                class="me-3" alt="Used cars for sale Best Dream car share image"/>
                                            <p style="color:black; font-size:15px; margin:0;">Share</p>
                                        </a> --}}
                                        <a data-bs-toggle="modal" data-bs-target="#exampleModal"  href="javascript:void(0)"
                                            style="display:flex; align-items:center; margin-left:15px; text-decoration:none; margin-bottom:13px">
                                            <img style="width:20px; height:20px;"
                                                src="{{ asset('/frontend/assets/images/share.png') }}"
                                                class="me-3" alt="Used cars for sale Best Dream car share image"/>
                                            <p style="color:black; font-size:15px; margin:0;">Share</p>
                                        </a>

                                        {{-- <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                            style="display:flex; align-items:center; margin-left:15px; text-decoration:none; margin-bottom:13px">
                                            <img style="width:20px; height:20px;"
                                                src="{{ asset('/frontend/assets/images/coin.png') }}"
                                                class="me-3" alt="Used cars for sale Best Dream car coin image"/>
                                            <p style="color:black; font-size:15px; margin:0;">See Actual Pricing</p>
                                        </a> --}}
                                    </div>

                                    @php
                                    $countWishList = 0;
                                    if (session()->has('favourite')) {
                                        $favourites = session('favourite');
                                        foreach ($favourites as $favorite) {
                                            if ($favorite['id'] == $inventory->id) {
                                                $countWishList = 1;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                    <div class="item-card9-icons">
                                        <a href="javascript:void(0);" class="item-card9-icons1 wishlist"
                                            data-productid="{{ $inventory->id }}">
                                            @if ($countWishList > 0)
                                                <i class="fa fa-heart" style="color: red"></i>
                                            @else
                                                <i class="fa fa fa-heart-o"></i>
                                            @endif
                                        </a>
                                    </div>
                                </div>
                                <div style="background: rgb(255, 255, 255);
                            background: linear-gradient(0deg, rgb(232, 245, 243) 0%, rgb(255, 255, 255) 100%);"
                                    class="card border-0 mb-0">
                                    <div style="padding:12px !important" class="card-body ">
                                        <div class="item-card9">
                                            @php
                                                $title = Str::substr($inventory->title, 0, 27);
                                            @endphp
                                            <a href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"
                                                class="text-dark">
                                                <h6 style="color:black !important" class="font-weight-semibold mt-1">
                                                    {{ $title }}</h6>
                                            </a>
                                            <div class="item-card9-desc mb-2">
                                                @php
                                            $transmission= substr($inventory->transmission, 0,25)
                                            @endphp
                                                <a href="javascript:void(0);" class="me-4 d-inline-block"><span
                                                        class=""> {{ $transmission }}</span></a>
                                                <p style="margin:0">@if(in_array($inventory->type, ['Preowned', 'Certified Preowned']))Used @else {{ $inventory->type }}
                                                @endif
                                                </p>
                                                {{-- <a href="javascript:void(0);" class="me-4 d-inline-block"><span class=""><i class="fa fa-calendar-o text-muted me-1"></i> {{($inventory->created_at)->diffForHumans()}}</span></a> --}}
                                            </div>
                                            <div style="height: 25px" class="d-flex">
                                                <h4 class="me-3 price-formate" style="font-weight:600">
                                                    {{ $inventory->price_formate }}</h4>
                                                <p
                                                    style="color:black; font-weight:600; font-size:12px; margin-top:2px">
                                                    ${{ $inventory->payment_price }}/mo*</p>
                                            </div>

                                            {{-- <p class="mb-0 leading-tight">Lorem Ipsum available, but the majority have suffered alteration in some form</p> --}}
                                        </div>
                                        <div class="item-card9-footer d-sm-flex">


                                            <a href="javascript:void(0);" class="w-50 mt-1 mb-1 float-start"
                                                title="Mileage"><i
                                                    class="fa fa-road text-muted me-1 "></i>{{ number_format($inventory->miles).' miles' }}</a>
                                            {{-- <a href="javascript:void(0);" class="w-50 mt-1 mb-1 float-start" title="Kilometrs"><i class="fa fa-road text-muted me-1 "></i>{{number_format($inventory->miles)}} miles</a> --}}

                                        </div>
                                        <div style="margin-top:7px" class="float:left">
                                            <button
                                                style="background:rgb(68, 29, 70); padding-left:15px; padding-right:15px; border-radius:7px; color:white;"
                                                id="check_availability" type="button"
                                                data-inventory_id="{{ $inventory->id }}"
                                                data-user_id="{{ $inventory->user_id }}">Check Availibility</button>
                                        </div>
                                    </div>
                                    <div class="card-footer pe-4 ps-4 pt-4 pb-4">
                                        <div class="item-card9-footer d-flex">
                                            <i style="color:black !important"
                                                class="fa fa-map-marker text-muted me-1"></i>
                                                @php
                                                $cus_dealer = explode(' in ',$inventory->dealer->name)[0];
                                                @endphp
                                            <h6 class="dealer-add" style="color:black">{{ $cus_dealer }}
                                                <br>{{ $inventory->dealer->address }}, {{ $inventory->zip_code }}</h6>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($index == 6)
                            <!-- Display your ad here -->
                            <div style="margin:0 auto" class="col-lg-4 col-md-6 col-xl-4 col-sm-12">
                                @php
                                    $banners = \App\Models\Banner::where('position', 'auto page middle')->first();
                                @endphp
                                <!-- Your ad content goes here -->
                                @isset($banners->image)
                                    <img class="auto-middle-banner" src="{{ asset('/dashboard/images/banners/' . $banners->image) }}"
                                    alt="Used cars for sale Best Dream car dealer banner middle image" />
                                @else
                                    <img src="{{ asset('/dashboard/images/banners/middle.png') }}"  alt="Used cars for sale Best Dream car dealer banner middle image" />
                                @endisset
                            </div>
                        @endif
                    @empty
                    <section style="padding-top: 5px !important; padding-bottom:3px !important" class="sptb2">
                        <div style="border-radius:5px" class="container bg-white p-5">
                            <div class="text-center">
                                <h4 class="text-center mt-3">Oops, it looks like no vehicles match your filters.</h4>
                                <p class="text-center">Please update or reset your filters to see more results.</p>
                            </div>
                        </div>
                    </section>
                        <section style="padding-top: 5px !important; padding-bottom:3px !important" class="sptb2">
                            <div style="border-radius:5px" class="container bg-white p-3">
                                <div class="text-center">
                                    <h5 style="font-weight:500; margin-bottom:45px; margin-top:17px">Search Used Cars in Popular Cities</h5>
                                </div>
                                <div style="margin: 0 auto" class="row mb-2">
                                    <div class="col-md-4 col-lg-3 col-sm-4 mb-4">
                                        <a href="{{ route('auto', ['homeDealerCitySearch' => 'Austin', 'homeDealerStateSearch' => 'TX']) }}"
                                            style="font-size: 14px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
                                            class="city">Used Cars in Austin, TX</a>
                                    </div>
                                    <div class="col-md-4 col-lg-3 col-sm-4 mb-4">
                                        <a href="{{ route('auto', ['homeDealerCitySearch' => 'Dallas', 'homeDealerStateSearch' => 'TX']) }}"
                                            style="font-size: 14px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
                                            class="city">Used Cars in Dallas, TX</a>
                                    </div>
                                    <div class="col-md-4 col-lg-3 col-sm-4 mb-4">
                                        <a href="{{ route('auto', ['homeDealerCitySearch' => 'Houston', 'homeDealerStateSearch' => 'TX']) }}"
                                            style="font-size: 14px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
                                            class="city">Used Cars in Houston, TX</a>
                                    </div>
                                    <div class="col-md-4 col-lg-3 col-sm-4 mb-4">
                                        <a href="{{ route('auto', ['homeDealerCitySearch' => 'San Antonio', 'homeDealerStateSearch' => 'TX']) }}"
                                            style="font-size: 14px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
                                            class="city">Used Cars in San Antonio, TX</a>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="center-block text-center mb-4">
        <div class="custom-pagination" style="display: flex; justify-content: flex-end">
            <ul class="pagination">
                @if ($inventories->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">Previous</span>
                    </li>
                @else
                    <li class="page-item"><a class="page-link"
                            href="{{ $inventories->previousPageUrl() }}">Previous</a>
                    </li>
                @endif

                @php
                    $currentPage = $inventories->currentPage();
                    $lastPage = $inventories->lastPage();
                    $maxPagesToShow = 5; // Adjust this number to determine how many page links to display
                    $startPage = max($currentPage - floor($maxPagesToShow / 2), 1);
                    $endPage = min($startPage + $maxPagesToShow - 1, $lastPage);
                @endphp

                @if ($startPage > 1)
                    <li class="page-item"><a class="page-link" href="{{ $inventories->url(1) }}">1</a>
                    </li>
                    @if ($startPage > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif

                @for ($page = $startPage; $page <= $endPage; $page++)
                    @if ($page == $currentPage)
                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item"><a class="page-link"
                                href="{{ $inventories->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endfor

                @if ($endPage < $lastPage)
                    @if ($endPage < $lastPage - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item"><a class="page-link"
                            href="{{ $inventories->url($lastPage) }}">{{ $lastPage }}</a>
                    </li>
                @endif

                @if ($inventories->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $inventories->nextPageUrl() }}">Next</a>
                    </li>
                @else
                    <li class="page-item disabled"><span class="page-link">Next</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>