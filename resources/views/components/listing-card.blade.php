<section>
    <div style="margin-top:26px" class="tab-content car-content">
        <div class="tab-pane active" id="tab-11">

            @if (isset($messageData) && !empty($messageData))
                {{ $messageData }}
            @endif
            <div class="row">

                @if (isset($message) && !empty($message))
                    {!! $message !!}
                @endif

                @forelse ($inventories as $index => $inventory)

                    <div class="col-lg-4 col-md-6 col-xl-4 col-sm-6 col-xs-12 main-element-card">
                        <div class="card">
                            <div class="item-card9-img">
                                @php
                                    if (is_array($inventory)) {
                                        $inventory = (object) $inventory;
                                    }

                                    if (is_array($inventory->dealer)) {
                                        $inventory->dealer = (object) $inventory->dealer;
                                    }

                                    if (is_array($inventory->additional_inventory)) {
                                        $inventory->additionalInventory = (object) $inventory->additional_inventory;
                                    }
                                    if (is_array($inventory->main_price_history)) {
                                        $inventory->mainPriceHistory = (object) $inventory->main_price_history;
                                    }
                                @endphp

                                @php
                                    $image_obj =
                                        $inventory->local_img_url ?? $inventory->additional_inventory['local_img_url']; // Get the image URLs as a string
                                    $image_splice = explode(',', $image_obj); // Split the string into an array
                                    $images_count = count(array_filter($image_splice)); // Count non-empty images
                                    $not_found_image = 'frontend/NotFound.png'; // Path to the "not found" image

                                    if ($images_count > 1) {
                                        // If there are multiple images, use the first valid one
                                        $imageData = trim(str_replace(['[', "'"], '', $image_splice[0]));
                                        $image = $imageData;
                                    } else {
                                        // If only one or no image is provided, show the "not found" image
                                        $image = $not_found_image;
                                    }

                                    $vin_string_replace = str_replace(' ', '', $inventory->vin);
                                    $city = $inventory->city ?? $inventory->dealer->city;
                                    $route_string = str_replace(
                                        ' ',
                                        '',
                                        $inventory->year .
                                            '-' .
                                            $inventory->make .
                                            '-' .
                                            $inventory->model .
                                            '-in-' .
                                            $city .
                                            '-' .
                                            strtoupper($inventory->state ?? $inventory->dealer->state),
                                    );
                                @endphp

                                <div class="item-card9-imgs">
                                    <a class="link "
                                        href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}"></a>

                                    {{-- @if (!empty($image) && $image != '[]')
                                <img width="100%" src="{{ asset(  $image ) }}"
                                    alt="Used cars for sale: {{ $inventory->title }}, price: {{ $inventory->price }}, VIN: {{ $inventory->vin }} in {{ $inventory->city }}, {{ $inventory->state }}, dealer name: {{ $inventory->name }}.'s Best Dream car image"
                                    class="auto-ajax-photo" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';">
                                @endif --}}

                                    @if (!empty($image) && $image != '[]')
                                        <img width="100%" data-src="{{ asset($image) }}"
                                            alt="Used cars for sale: {{ $inventory->title }}, price: {{ $inventory->price }}, VIN: {{ $inventory->vin }} in {{ $inventory->dealer->city }}, {{ $inventory->dealer->state }}, dealer name: {{ $inventory->dealer->name }}.'s Best Dream car image"
                                            class="auto-ajax-photo lazyload" loading="lazy"
                                            onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';">
                                    @endif

                                </div>

                                <a data-id="{{ $inventory->id }}" href="javascript:void(0)" id="quick"><img
                                        class="quick-option" src="{{ asset('/frontend/assets/images/more.png') }}"
                                        alt="Used cars for sale for image Best Dream car more image" /></a>


                                <div class="hide-action" id="hide-action-{{ $inventory->id }}">
                                    <input type="hidden" id="all_id">
                                    <a href="javascript:void(0)"
                                        style="display:flex; align-items:center; margin-top:20px; margin-left:15px; text-decoration:none; margin-bottom:13px"
                                        id="view-data">
                                        <img style="width:20px; height:20px;"
                                            src="{{ asset('/frontend/assets/images/show.png') }}" class="me-3"
                                            alt="Used cars for sale for image dream best show more image" />
                                        <p style="color:black; font-size:15px; margin:0;">Quick View</p>
                                    </a>

                                    <a href="javascript:void(0)" data-id="{{ $inventory->id }}"
                                        style="display:flex; align-items:center; margin-left:15px; text-decoration:none; margin-bottom:13px"
                                        id="compare_listing">
                                        <img style="width:20px; height:20px;"
                                            src="{{ asset('/frontend/assets/images/swap.png') }}" class="me-3"
                                            alt="Used cars for sale for image dream best swap image" />
                                        <p style="color:black; font-size:15px; margin:0;">Compare Listing</p>
                                    </a>

                                    <a data-bs-toggle="modal" data-bs-target="#ShareModal"
                                        onclick="setModalId('{{ $inventory->id }}')" href="javascript:void(0)"
                                        style="display:flex; align-items:center; margin-left:15px; text-decoration:none; margin-bottom:13px">
                                        <img style="width:20px; height:20px;"
                                            src="{{ asset('/frontend/assets/images/share.png') }}" class="me-3"
                                            alt="Used cars for sale for image Best Dream car share image" />
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

                                <div style="padding:12px !important " class="card-body ">
                                    <a
                                        href="{{ route('auto.details', ['vin' => $vin_string_replace, 'param' => $route_string]) }}">
                                        <div class="item-card9">
                                            <style>
                                                .title-wrapper {
                                                    word-wrap: break-word;
                                                    /* Breaks long words if needed */
                                                    word-break: break-word;
                                                    /* Ensures proper line breaking for long words */
                                                    white-space: normal;
                                                    /* Allows wrapping to the next line */
                                                    width: 100%;
                                                    /* Adjust the width as needed */
                                                }

                                                .flex-container {
                                                    display: flex;
                                                    justify-content: space-between;
                                                    align-items: center;
                                                }

                                                .price-rating {
                                                    text-align: right;
                                                }
                                            </style>
                                            <p style="margin-top:5px; line-height:.5; font-size:12px">
                                                {{ ucfirst($inventory->type) }}</p>
                                            <p class="title-wrapper" style="margin:0">
                                                <strong>{{ $inventory->title }}</strong>
                                            </p>
                                            <p style="line-height:.8; font-size:12px"
                                                title="{{ ucfirst($inventory->engine_details) }}">
                                                {{ Str::limit(ucfirst($inventory->engine_details), 38) }}</p>
                                            <div class="flex-container">
                                                <p style="margin-bottom:0; font-size:14px">
                                                    {{ number_format($inventory->miles) ? number_format($inventory->miles) . ' miles' : ' TBD' }}
                                                </p>
                                                <h4 style="margin-bottom:0; font-size:20px ;font-weight:700">
                                                    ${{ number_format($inventory->price) }}</h4>
                                            </div>

                                            <div class="flex-container">
                                                {{-- <p> @php
                                                $totalPriceChange = 0; // Initialize the total price change
                                                @endphp

                                                @foreach ($inventory->mainPriceHistory as $history)
                                                @php
                                                    if (is_array($history)) {
                                                        $history = (object) $history; // Convert to object if it's an array
                                                    }

                                                    if (is_array($history->change_amount)) {
                                                        $history->change_amount = implode(', ', $history->change_amount); // Convert array to string
                                                    }
                                                @endphp
                                                @if (strpos($history->change_amount, '+') !== false || strpos($history->change_amount, '-') !== false)
                                                @php
                                                // Remove dollar signs and commas, and convert the amount to a float
                                                $changeAmount = (float) str_replace(['$', ',', '+'], '', $history->change_amount);

                                                // Add to total price change regardless of the sign
                                                $totalPriceChange += $changeAmount;
                                                @endphp
                                                @endif
                                                @endforeach

                                                @if ($totalPriceChange != 0)
                                                <small style="font-size:12px">
                                                    <strong>
                                                        @if ($totalPriceChange > 0)
                                                        ${{ number_format($totalPriceChange, 0) }} price rise
                                                        @else
                                                        ${{ number_format(abs($totalPriceChange), 0) }} price drop
                                                        @endif
                                                    </strong>
                                                </small>
                                                @endif
                                            </p> --}}
                                                <p
                                                    style="display: inline-block;border-bottom:1px solid black;line-height:1; font-size:14px">
                                                    EST. ${{ floor($inventory->payment_price) }}/mo*</p>
                                            </div>

                                            <div class="flex-container">
                                                <p></p>
                                                @if ($inventory->price_rating != null)
                                                    @if ($inventory->price_rating == 'great-deal')
                                                        <div
                                                            style="display: inline-flex; align-items: center; gap: 8px;">
                                                            <span class="badge rounded-pill badge-info"
                                                                style="display: inline-flex; align-items: center;">
                                                                <i class="fa fa-angle-double-down pr-2"></i>
                                                            </span>
                                                            <p style="line-height:1; font-size:14px; margin: 0;">
                                                                Excellent
                                                                Price</p>
                                                        </div>
                                                    @elseif($inventory->price_rating == 'good-deal')
                                                        <div
                                                            style="display: inline-flex; align-items: center; gap: 8px;">
                                                            <span class="badge rounded-pill badge-success"
                                                                style="display: inline-flex; align-items: center;">
                                                                <i class="fa fa-angle-down pr-2"></i>
                                                            </span>
                                                            <p style="line-height:1; font-size:14px; margin: 0;">Great
                                                                Price</p>
                                                        </div>
                                                    @elseif($inventory->price_rating == 'fair-deal')
                                                        <div
                                                            style="display: inline-flex; align-items: center; gap: 8px;">
                                                            <span class="badge rounded-pill badge-warning"
                                                                style="display: inline-flex; align-items: center;">
                                                                <i class="fa fa-check-circle pr-2"></i>
                                                            </span>
                                                            <p style="line-height:1; font-size:14px; margin: 0;">Fair
                                                                Price
                                                            </p>
                                                        </div>
                                                    @endif
                                                @endif

                                            </div>

                                            <p class="mt-1 mb-1" style="font-size:14px; border-top:1px solid #E4E4E5">
                                                <strong>{{ $inventory->dealer->name }}</strong>
                                            </p>

                                            <!-- dealer star rating start here  -->
                                            {{-- @php
                                    // Ensure the rating is within the valid range (1-5)
                                    $averageRating = $inventory->dealer->rating ?? 4.7; // Default to 3 if not set
                                    $reviews = $inventory->dealer->review ?? 43; // Default to 0 if not set
                                    $averageRating = $averageRating >= 1 && $averageRating <= 5 ? $averageRating : 0;

                                    // Check if function already exists to avoid redefinition
                                    if (!function_exists('renderStars')) {
                                        function renderStars($rating) {
                                            $fullStars = floor($rating); // Full stars
                                            $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0; // Half star logic
                                            $emptyStars = 5 - ($fullStars + $halfStar); // Empty stars

                                            $starsHtml = str_repeat('<i class="fa fa-star text-warning"></i>', $fullStars);
                                            if ($halfStar) {
                                                $starsHtml .= '<i class="fa fa-star-half-o text-warning"></i>';
                                            }
                                            $starsHtml .= str_repeat('<i class="fa fa-star text-secondary"></i>', $emptyStars); // Empty stars
                                            return $starsHtml;
                                        }
                                    }
                                @endphp

                                <div class="mb-2">
                                    <span>{{ $averageRating }} {!! renderStars($averageRating) !!} {{ $reviews }}</span>
                                    </div> --}}

                                            <!-- dealer star rating end  here  -->

                                            @php
                                                $city = $inventory->city ?? $inventory->dealer->city;
                                                $state = $inventory->city ?? $inventory->dealer->state;
                                                $dealer_full_address = $inventory->city ?? $inventory->dealer->state;
                                            @endphp

                                            <i style="color:black !important"
                                                class="fa fa-map-marker text-muted me-1"></i>
                                            <span
                                                class=" ">{{ $city . ', ' . strtoupper($state) ?? $dealer_full_address }}</span>

                                            @if (isset($inventory->distance) && $inventory->distance > 0.9)
                                                <span>
                                                    ({{ round($inventory->distance, 0) }} mi. away)
                                                </span>
                                            @endif


                                        </div>
                                    </a>
                                    <div class="check-button" style="margin-top:15px">
                                        <button
                                            style="font-size: 16px; background:rgb(68, 29, 70); padding:10px 15px ; border-radius:25px; color:white; border:1px solid rgb(68, 29, 70)"
                                            id="check_availability" type="button"
                                            data-inventory_id="{{ $inventory->id }}">Check Availibility</button>
                                    </div>
                                </div>

                                {{-- <div class="card-footer pe-4 ps-4 pt-4 pb-4">


                                <div class="item-card9-footer d-flex">
                                    <i style="color:black !important" class="fa fa-map-marker text-muted me-1"></i>
                                    @php
                                    $cus_dealer = explode(' in ', $inventory->dealer->name)[0];
                                    $nameLength = strlen($cus_dealer);
                                    $name = Str::substr($cus_dealer, 0, 20);
                                    @endphp


                                    @if ($nameLength <= '25') <h5 class="dealer-add" style="color:black">{{ $cus_dealer }}
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


                        <h5 class="dealer-add" style="color:black">
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
                </div> --}}
                            </div>
                        </div>
                    </div>


                    @if ($index == 6)
                        <x-listing-middle-banner />
                    @endif
                @empty
                    <x-oops-message />
                    <x-oops-message-popular-city />
                @endforelse
            </div>
        </div>
    </div>
</section>
