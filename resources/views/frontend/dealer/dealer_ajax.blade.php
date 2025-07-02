<section>
    <div class="container">
        <!-- Header Section -->
        <div class="bg-white p-3 rounded mt-3 mb-3">
            <div class="row align-items-center">
                <!-- Search Results -->
                <div class="col-md-3 col-sm-6 mb-3">
                    <h6 class="text-center p-2 border rounded d-flex align-items-center justify-content-center">
                        Showing 1 to {{ $single_dealer_count }} of {{ $total_count }} results
                    </h6>
                </div>
                <!-- Search Input -->
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="input-group">
                        <input type="text" placeholder="Search Name" value="{{ $target_name ? $target_name : '' }}"
                            name="dealer_name" class="form-control" id="dealer_name">
                        <span class="input-group-text">
                            <a id="search_pointer"
                                style="cursor: pointer; height: 100%; display: flex; align-items: center;">
                                <span id="search_icon">
                                    <i class="fa fa-search"></i>
                                </span>
                            </a>
                        </span>
                    </div>
                </div>
                <!-- State Dropdown -->
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="input-group">
                        <select class="form-select text-dark" id="state_sort_search"
                            style="
                                    color: black;
                                    border: 1px solid rgba(190, 190, 190, 0.5);
                                    border-radius: 5px;
                                    padding: 0.5rem;
                                    appearance: auto;
                                    background-color: white;
                                ">
                            <option value="">Select State</option>
                            @forelse ($state_names as $state => $short_state)
                                <option value="{{ $short_state }}"
                                    {{ $short_state == $target_state ? 'selected' : '' }}>
                                    {{ $state }}
                                </option>
                            @empty
                                <option>No State</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="input-group">
                        <select class="form-select text-dark" id="city_sort_search"
                            style="
                                    color: black;
                                    border: 1px solid rgba(190, 190, 190, 0.5);
                                    border-radius: 5px;
                                    padding: 0.5rem;
                                    appearance: auto;
                                    background-color: white;
                                ">
                            <option value="">Select City</option>
                            @forelse ($select_cities as $city)
                                <option value="{{ $city }}" {{ $city == $target_city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @empty
                                <option>No City</option>
                            @endforelse
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <div style="margin:0 auto; background:white; width:99.7%" class="row p-3 mt-3 mb-5">
            @forelse ($dealers as $dealer)
                @php
                    $parts = explode('Map', $dealer->address);
                    $lines = explode("\n", trim($parts[0]));
                    $address = isset($lines[0]) ? trim($lines[0]) : '';
                    $cityStateZip = isset($lines[1]) ? trim($lines[1]) : '';

                    // Collect all VINs for this dealer
                    $vins = [];
                    if (collect($dealer->main_inventories)->isNotEmpty()) {
                        $vins = collect($dealer->main_inventories)->pluck('vin')->toArray();
                    }
                @endphp
                {{-- @if (!empty($dealer->main_inventories) && count($dealer->main_inventories) > 0) --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mt-3 border">
                    <img width="20%" src="{{ asset('/frontend/assets/images/dd.png') }}" />
                    <div style="overflow:hidden" class="find-dealer-results-item">
                        <div class="dealer-info">
                            <div class="dealer-result-name mb-1">{{ $dealer->name }}</div>
                            <div class="dealer-result-location">{{ $address }}</div>
                            <div class="dealer-result-location">
                                {{ $dealer->city . ', ' . $dealer->state . ' ' . $dealer->zip }}</div>
                            <div class="dealer-result-phone">
                                <a href="tel:{{ $dealer->phone }}">{{ $cityStateZip }}</a>
                            </div>

                            <!-- Convert to Collection and check if not empty -->
                            @if (collect($dealer->main_inventories)->isNotEmpty())
                               {{--  <h4>Inventory:</h4>
                                <ul>
                                    @foreach ($dealer->main_inventories as $inventory)
                                        <li>
                                            <!-- Display the VIN for each inventory item -->
                                            VIN: {{ $inventory->vin }}
                                        </li>
                                    @endforeach
                                </ul> --}}
                            @else
                                <p>No vehicles in inventory.</p>
                            @endif


                        </div>
                        @php
                            $stockId = $dealer->dealer_id ?? '0000';
                            $dealer_name_data = $dealer->name;
                            $dealer_name = str_replace(' ', '_', str_replace(' in Austin, TX', '', $dealer_name_data));
                            $dealerId = $dealer->id;

                            $mainInventoryData = $dealer->maininventories ?? $dealer->main_inventories;
                        @endphp

                        <div class="dealer-result-inventory mt-3 mb-5">
                            <form method="GET"
                                action="{{ route('dealer', ['stockId' => $stockId, 'dealer_name' => $dealer_name, 'id' => $dealerId]) }}"
                                class="d-inline">
                                <input type="hidden" name="vins" value="{{ implode(',', $vins) }}">
                                <input type="hidden" name="name" value="{{ $dealer_name_data }}">
                                <button type="submit" class="showInventoryBtn" rel="nofollow">
                                    View Inventory ({{ count($mainInventoryData) }})
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- @endif --}}
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

        {{-- @if (!empty($dealer->main_inventories) && count($dealer->main_inventories) > 0) --}}
        <div class="center-block text-center mb-4">
            <div class="custom-pagination" style="display: flex; justify-content: flex-end">
                <ul class="pagination">
                    @if ($dealers->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link"
                                href="{{ $dealers->previousPageUrl() }}">Previous</a></li>
                    @endif

                    @php
                        $currentPage = $dealers->currentPage();
                        $lastPage = $dealers->lastPage();
                        $maxPagesToShow = 5;
                        $startPage = max($currentPage - floor($maxPagesToShow / 2), 1);
                        $endPage = min($startPage + $maxPagesToShow - 1, $lastPage);
                    @endphp

                    @if ($startPage > 1)
                        <li class="page-item"><a class="page-link" href="{{ $dealers->url(1) }}">1</a></li>
                        @if ($startPage > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endif

                    @for ($page = $startPage; $page <= $endPage; $page++)
                        @if ($page == $currentPage)
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link"
                                    href="{{ $dealers->url($page) }}">{{ $page }}</a></li>
                        @endif
                    @endfor

                    @if ($endPage < $lastPage)
                        @if ($endPage < $lastPage - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item"><a class="page-link"
                                href="{{ $dealers->url($lastPage) }}">{{ $lastPage }}</a></li>
                    @endif

                    @if ($dealers->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $dealers->nextPageUrl() }}">Next</a>
                        </li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </div>
        </div>
        {{-- @endif --}}
    </div>
</section>
