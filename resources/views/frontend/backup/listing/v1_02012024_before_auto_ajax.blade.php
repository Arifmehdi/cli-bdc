@php
$sortOptions = [
'datecreated|desc' => 'Days Listed: Newest',
'datecreated|asc' => 'Days Listed: Oldest',
'distance|asc' => 'Distance: Nearest',
'searchprice|asc' => 'Price: Lowest',
'searchprice|desc' => 'Price: Highest',
'mileage|asc' => 'Mileage: Lowest',
'mileage|desc' => 'Mileage: Highest',
'modelyear|desc' => 'Year: Newest',
'modelyear|asc' => 'Year: Oldest',
'payment|asc' => 'Payment: Lowest',
'payment|desc' => 'Payment: Highest',
];
$selectedSort = session()->get('selected_sort_search');
@endphp

<section>

</section>

<div class="">
    <div class="item2-gl ">
        <div class="mb-0">
            <div class="">
                <div class="mb-0">
                    <div class="">
                        <div style="padding:18px; border-radius:4px;" class="bg-white auto-page-filter-topbar">
                            <!-- Flex container for heading and select dropdown -->
                            <div class="d-flex justify-content-between top-fil align-items-center w-100 mt-1" style="gap: 4%;">
                                <!-- Heading -->
                                <h6 class="mb-0  text-center show-text  p-1 highlight-count d-flex align-items-center justify-content-center"
                                    style="width: 55%; border: 1px solid rgba(190, 190, 190, 0.5); border-radius: 7px;">
                                    Showing 1 to {{ $single_inventories_count }} of {{ $total_count }} results
                                </h6>


                                <!-- Select dropdown for sorting -->
                                <div style="width: 41%; margin-top: 12px;">
                                    <select class="mobile_selected_sort_search p-1 border"
                                        id="mobile_selected_sort_search"
                                        style="width: 100%; border: 1px solid rgba(190, 190, 190, 0.5) !important; color: #080e1b; border-radius:7px">
                                        <option value="all">Best Match</option>
                                        @foreach ($sortOptions as $value => $label)
                                        <option value="{{ $value }}" {{ $selectedSort==$value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>

            </div>
        </div>



    </div>

</div>

</div>


</div>

<hr class="line-auto mb-4">
<div style="margin-top:26px" class="tab-content car-content">
    <div class="tab-pane active" id="tab-11">

        <div class="row">

            @if (isset($message) && !empty($message))
            {!! $message !!}
            @endif

            @forelse ($inventories as $index => $inventory)

            <div class="col-lg-4 col-md-6 col-xl-4 col-sm-6 col-xs-12 main-element-card">
                <div class="card">
                    <div class="item-card9-img">
                        @php
                        $image_obj = $inventory->local_img_url;
                        $image_splice = explode(',', $image_obj);
                        $imageData  = trim(str_replace(['[', "'"], '', $image_splice[0]));
                        // $image  = str_replace('http://carbazar.test/', 'https://bestdreamcar.com/', $imageData);
                        $image  = $imageData;

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
                        $inventory->dealer->city .
                        '-' .
                        strtoupper($inventory->dealer->state),
                        );
                        @endphp
                        <div class="item-card9-imgs">
                            <a class="link "
                                href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"></a>

                            @if (!empty($image) && $image != '[]')
                            <img width="100%" src="{{ asset(  $image ) }}"
                                alt="Used cars for sale: {{ $inventory->title }}, price: {{ $inventory->price }}, VIN: {{ $inventory->vin }} in {{ $inventory->dealer_city }}, {{ $inventory->dealer_state }}, dealer name: {{ $inventory->dealer_name }}. Best Dream car image"
                                class="auto-ajax-photo" loading="lazy"
                                onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';">
                            @elseif ($image_obj == '[]')
                            <img class="auto-ajax-photo" width="100%" src="{{ asset('frontend/NotFound.png') }}"
                                alt="Used cars for sale coming soon image dream best">
                            @else
                            <img class="auto-ajax-photo" width="100%" src="{{ asset('frontend/NotFound.png') }}"
                                alt="Used cars for sale coming soon image dream best">
                            @endif

                        </div>

                        <a data-id="{{ $inventory->id }}" href="javascript:void(0)" id="quick"><img class="quick-option"
                                src="{{ asset('/frontend/assets/images/more.png') }}" alt="Used cars for sale for image Best Dream car more image" /></a>


                        <div class="hide-action" id="hide-action-{{ $inventory->id }}" style="">
                            <input type="hidden" id="all_id">
                            <a href="javascript:void(0)"
                                style="display:flex; align-items:center; margin-top:20px; margin-left:15px; text-decoration:none; margin-bottom:13px"
                                id="view-data">
                                <img style="width:20px; height:20px;"
                                    src="{{ asset('/frontend/assets/images/show.png') }}" class="me-3" alt="Used cars for sale for image dream best show more image"/>
                                <p style="color:black; font-size:15px; margin:0;">Quick View</p>


                            </a>

                            <a href="javascript:void(0)" data-id="{{ $inventory->id }}"
                                style="display:flex; align-items:center; margin-left:15px; text-decoration:none; margin-bottom:13px"
                                id="compare_listing">
                                <img style="width:20px; height:20px;"
                                    src="{{ asset('/frontend/assets/images/swap.png') }}" class="me-3" alt="Used cars for sale for image dream best swap image"/>
                                <p style="color:black; font-size:15px; margin:0;">Compare Listing</p>
                            </a>

                            <a data-bs-toggle="modal" data-bs-target="#ShareModal"
                                onclick="setModalId('{{ $inventory->id }}')" href="javascript:void(0)"
                                style="display:flex; align-items:center; margin-left:15px; text-decoration:none; margin-bottom:13px">
                                <img style="width:20px; height:20px;"
                                    src="{{ asset('/frontend/assets/images/share.png') }}" class="me-3" alt="Used cars for sale for image Best Dream car share image"/>
                                <p style="color:black; font-size:15px; margin:0;">Share</p>
                            </a>


                            {{-- <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                style="display:flex; align-items:center; margin-left:15px; text-decoration:none; margin-bottom:13px">
                                <img style="width:20px; height:20px;"
                                    src="{{ asset('/frontend/assets/images/coin.png') }}" class="me-3" alt="Used cars for sale for image Best Dream car coin image"/>
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
                        break; // No need to continue the loop if found
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
                                $title = Str::limit($inventory->title, 23, '...');
                                @endphp
                                <a href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"
                                    class="text-dark">
                                    <h5 style="color:black !important; font-weight:600" class=" mt-1 car-tit">
                                        {{ $title }}
                                    </h5>
                                </a>

                                <div class="item-card9-desc mb-2">
                                    @php
                                    $transmission = substr($inventory->formatted_transmission, 0, 25);
                                    @endphp
                                    <p class="me-4 mb-0"><span class=""> {{
                                            $transmission }}</span></p>
                                    <p style="margin:0">
                                        @if (in_array($inventory->type, ['Preowned', 'Certified Preowned']))
                                        Used
                                        @else
                                        {{ $inventory->type }}
                                        @endif
                                    </p>

                                </div>
                                <div style="height: 25px" class="d-flex">
                                    <h4 class="me-3 price-formate" style="font-weight:600; font-size:19px">
                                        {{ $inventory->price_formate }}
                                    </h4>
                                    <p style="color:black; font-weight:600; font-size:14px; margin-top:0px">
                                        ${{ number_format($inventory->payment_price) }}/mo*</p>
                                </div>


                            </div>
                            <div class="item-card9-footer d-flex justify-content-between align-items-center">
                                <p class="w-100 mt-2 mb-2 float-start" title="Mileage">
                                    <img class="me-1" style="width:21px; height:21px; margin-top:-2px"
                                        src="{{ asset('/frontend/assets/images/miles.png') }}"
                                        alt="Used cars for sale for image Best Dream car mileage image"/>
                                    {{ $inventory->miles == 0 ? 'TBD' : number_format($inventory->miles) . ' miles' }}
                                </p>
                                @if($inventory->created_at->diffInDays() != 0)
                                <span class="d-flex align-items-center">
                                    <i class="fa fa-calendar ms-2 text-muted" title="Days"></i>
                                    <span>&nbsp;{{ $inventory->created_at->diffInDays() ?? 'TBD' }}</span>
                                </span>
                                @endif
                            </div>
                            <div class="check-button" style="margin-top:7px">
                                <button
                                    style="background:rgb(68, 29, 70); padding-top:5px; padding-bottom:5px;  padding-left:15px; padding-right:15px; border-radius:7px; color:white; border:1px solid rgb(68, 29, 70)"
                                    id="check_availability" type="button" data-inventory_id="{{ $inventory->id }}"
                                    data-user_id="{{ $inventory->user_id }}">Check Availibility</button>
                            </div>


                        </div>
                        <div class="card-footer pe-4 ps-4 pt-4 pb-4">


                            <div class="item-card9-footer d-flex">
                                <i style="color:black !important" class="fa fa-map-marker text-muted me-1"></i>
                                @php
                                $cus_dealer = explode(' in ', $inventory->dealer->name)[0];
                                $nameLength = strlen($cus_dealer);
                                $name = Str::substr($cus_dealer, 0, 20);
                                @endphp


                                @if($nameLength <= '25' ) <h5 class="dealer-add" style="color:black">{{ $cus_dealer }}
                                    <br>
                                    <span style="font-size:14px">{{ $inventory->dealer->city }}, {{
                                        strtoupper($inventory->dealer->state) }}
                                        {{ $inventory->zip_code }}

                                        @if (isset($inventory->distance) && $inventory->distance > 0.9)
                                        <span>
                                            ({{ round($inventory->distance, 0) }} mi. away)
                                        </span>
                                        @endif
                                    </span>
                                    </h5>
                                    @else


                                    <h5  class="dealer-add" style="color:black">
                                        <span title="{{$cus_dealer}}">{{ $name }}</span>
                                        <br>
                                        <span style="font-size:14px">{{ $inventory->dealer->city }}, {{
                                            strtoupper($inventory->dealer->state) }}
                                            {{ $inventory->zip_code }}

                                            @if (isset($inventory->distance) && $inventory->distance > 0.9)
                                            <span>
                                                ({{ round($inventory->distance, 0) }} mi. away)
                                            </span>
                                            @endif
                                        </span>
                                    </h5>
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            @if ($index == 6)
            <!-- Display your ad here -->
            <div style="margin:0 auto" class="col-lg-4 col-md-6 col-xl-4 col-sm-6 col-xs-12">
                @php
                $banners = \App\Models\Banner::where('position', 'auto page middle')->first();
                @endphp
                <!-- Your ad content goes here -->
                @isset($banners->image)
                <img class="auto-middle-banner" src="{{ asset('/dashboard/images/banners/' . $banners->image) }}"
                    alt="Used cars for sale for Best Dream car middle banner image" />
                @else
                <img class="auto-middle-banner" src="{{ asset('/dashboard/images/banners/middle.png') }}" alt="Used cars for sale for Best Dream car middle banner alter image" />
                @endisset
            </div>
            @endif

            @empty

            <section style="padding-top: 5px !important; padding-bottom:3px !important" class="sptb2">
                <div style="border-radius:5px" class="container bg-white p-5">
                    <div class="text-center">
                        <h4 class="text-center mt-3">Oops, it looks like no vehicles match your filters.
                        </h4>
                        <p class="text-center">Please update or reset your filters to see more results.</p>
                    </div>
                </div>
            </section>

            <section style="padding-top: 5px !important; padding-bottom:3px !important" class="sptb2">
                <div style="border-radius:5px" class="container bg-white p-3">
                    <div class="text-center">
                        <h5 style="font-weight:500; margin-bottom:45px; margin-top:17px">Search Used Cars
                            in Popular Cities</h5>
                    </div>
                    <div style="margin: 0 auto" class="row mb-2">
                        <div class="col-md-4 col-lg-3 col-sm-4 mb-4">
                            <a href="{{ route('auto', ['zip' => '78702', 'home2' => true]) }}"
                                style="font-size: 14px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
                                class="city" data-zip="78702">Used Cars in Austin, TX</a>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-4 mb-4">
                            <a href="{{ route('auto', ['zip' => '75241', 'home2' => true]) }}"
                                style="font-size: 14px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
                                class="city" data-zip="75241">Used Cars in Dallas, TX</a>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-4 mb-4">
                            <a href="{{ route('auto', ['zip' => '77007', 'home2' => true]) }}"
                                style="font-size: 14px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
                                class="city" data-zip="77007">Used Cars in Houston, TX</a>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-4 mb-4">
                            <a href="{{ route('auto', ['zip' => '78205', 'home2' => true]) }}"
                                style="font-size: 14px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
                                class="city" data-zip="78205">Used Cars in San Antonio, TX</a>
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
            <li class="page-item"><a class="page-link" href="{{ $inventories->previousPageUrl() }}">Previous</a>
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

            @for ($page = $startPage; $page <= $endPage; $page++) @if ($page==$currentPage) <li
                class="page-item active"><span class="page-link">{{ $page }}</span>
                </li>
                @else
                <li class="page-item"><a class="page-link" href="{{ $inventories->url($page) }}">{{ $page }}</a>
                </li>
                @endif
                @endfor

                @if ($endPage < $lastPage) @if ($endPage < $lastPage - 1) <li class="page-item disabled">
                    <span class="page-link">...</span>
                    </li>
                    @endif
                    <li class="page-item"><a class="page-link" href="{{ $inventories->url($lastPage) }}">{{ $lastPage
                            }}</a>
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
