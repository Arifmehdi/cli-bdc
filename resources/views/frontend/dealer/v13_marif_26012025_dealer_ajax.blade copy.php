<section>
    <div class="container">
        <div class="mt-4">
            <div class="item2-gl ">
                <div class="mb-0">
                    <div class="">
                        <div class="mb-0 row">
                            <div class="col-lg-12">
                                <div style="padding:18px; border-radius:4px; width:100%" class="bg-white auto-page-filter-topbar">
                                    <!-- Flex container for heading and select dropdown -->
                                    <div class="d-flex justify-content-between top-fil align-items-center w-100 mt-1" style="gap: 4%;">
                                        <!-- Heading -->
                                        <h6 class="mb-0  text-center show-text  p-1 highlight-count d-flex align-items-center justify-content-center"
                                            style="width: 55%; border: 1px solid rgba(190, 190, 190, 0.5); border-radius: 7px;">
                                            Showing 1 to {{ $single_dealer_count }} of {{ $total_count }} results
                                        </h6>


                                        <!-- Select dropdown for sorting -->
                                        <div style="width: 41%; margin-top: 12px;" class="dealer-search">
                                            <input type="text" placeholder="Search Name" value="{{ $target_name ? $target_name : ''}}" name="dealer_name" class="form-control" id="dealer_name">
                                        </div>
                                        <div style="width: 41%; margin-top: 12px;">
                                            <select class="mobile_selected_sort_search p-1 border"
                                                id="city_sort_search"
                                                style="width: 100%; border: 1px solid rgba(190, 190, 190, 0.5) !important; color: #080e1b; border-radius:7px">
                                                <option value="">Select City</option>
                                                @forelse ($cities as $city)
                                                    <option value="{{$city->city_name}}" {{ ($city->city_name == $target_city) ? 'selected' : ''}}>{{$city->city_name}}</option>
                                                @empty
                                                    <option>No City</option>
                                                @endforelse
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

        <div style="margin:0 auto; background:white; width:99.7%" class="row p-3 mt-3 mb-5">
            @forelse ($dealers as $dealer)

            @php
                $parts = explode("Map", $dealer->address);
                $lines = explode("\n", trim($parts[0]));

                $address = isset($lines[0]) ? trim($lines[0]) : '';
                $cityStateZip = isset($lines[1]) ? trim($lines[1]) : '';
            @endphp
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mt-3 border">
                    <img width="20%" src="{{asset('/frontend/assets/images/dd.png')}}"/>
                    <div style="overflow:hidden" class="find-dealer-results-item">
                        <div class="dealer-info">
                            <div class="dealer-result-name mb-1">{{ $dealer->name }}</div>
                            <div class="dealer-result-location">{{ $address }}</div>
                            <div class="dealer-result-location">{{ $dealer->city.', '. $dealer->state . ' '. $dealer->zip}}</div>
                            <div class="dealer-result-phone">
                                <a href="tel:{{ $dealer->phone }}">{{$cityStateZip}}</a>
                            </div>
                        </div>
                        @php
                            $stockId = $dealer->dealer_id ?? '0000';
                            $dealer_name_data = $dealer->name;
                            $dealer_name = str_replace(' ', '_',str_replace(' in Austin, TX', '', $dealer_name_data));
                            $dealerId = $dealer->id;
                         @endphp

                        <div class="dealer-result-inventory mt-3 mb-5">
                            <a href="{{ route('dealer', ['stockId' => $stockId, 'dealer_name' => $dealer_name, 'id' => $dealerId]) }}" class="showInventoryBtn">View Inventory ({{ count($dealer->inventories) }})</a>
                        </div>
                        {{-- @if (isset($dealer->distance) && $dealer->distance > 0.9)
                        <span>
                            ({{ round($dealer->distance, 0) }} mi. away)
                        </span>
                        @endif --}}
                    </div>
                </div>
            @empty
                <div class="col-md-3 mt-4">
                    <div class="find-dealer-results-item">
                        <div class="dealer-info">
                            <div class="dealer-result-name">No Data Available Here</div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="center-block text-center mb-4">
            <div class="custom-pagination" style="display: flex; justify-content: flex-end">
                <ul class="pagination">
                    @if ($dealers->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span>
                        </li>
                    @else
                        <li class="page-item"><a class="page-link"
                                href="{{ $dealers->previousPageUrl() }}">Previous</a>
                        </li>
                    @endif

                    @php
                        $currentPage = $dealers->currentPage();
                        $lastPage = $dealers->lastPage();
                        $maxPagesToShow = 5; // Adjust this number to determine how many page links to display
                        $startPage = max($currentPage - floor($maxPagesToShow / 2), 1);
                        $endPage = min($startPage + $maxPagesToShow - 1, $lastPage);
                    @endphp

                    @if ($startPage > 1)
                        <li class="page-item"><a class="page-link" href="{{ $dealers->url(1) }}">1</a>
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
                                    href="{{ $dealers->url($page) }}">{{ $page }}</a>
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
                                href="{{ $dealers->url($lastPage) }}">{{ $lastPage }}</a>
                        </li>
                    @endif

                    @if ($dealers->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $dealers->nextPageUrl() }}">Next</a>
                        </li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</section>
