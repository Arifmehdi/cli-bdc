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

{{-- listing page show number and filter start here  --}}

<section>
    <div class="">
        <div class="item2-gl ">
            <div class="mb-0">
                <div class="">
                    <div class="mb-0">
                        <div class="">
                            <div style="padding:18px; border-radius:4px;" class="bg-white auto-page-filter-topbar">
                                <!-- Flex container for heading and select dropdown -->
                                <div class="d-flex justify-content-between top-fil align-items-center w-100 mt-1"
                                    style="gap: 4%;">
                                    <!-- Heading -->
                                    <h6 class="mb-0  text-center show-text  p-1 highlight-count d-flex align-items-center justify-content-center"
                                        style="width: 55%; border: 1px solid rgba(190, 190, 190, 0.5); border-radius: 7px;">
                                        Showing {{ $range_with_total }} results


                                    </h6>

                                    <!-- Select dropdown for sorting -->
                                    <div style="width: 41%; margin-top: 12px;">
                                        <select class="mobile_selected_sort_search p-1 border"
                                            id="mobile_selected_sort_search"
                                            style="width: 100%; border: 1px solid rgba(190, 190, 190, 0.5) !important; color: #080e1b; border-radius:7px">
                                            <option value="all">Best Match</option>
                                            @foreach ($sortOptions as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ $selectedSort == $value ? 'selected' : '' }}>
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

{{-- </div>
</div> --}}
</section>
{{-- listing page show number and filter end here  --}}
<hr class="line-auto mb-4" >
{{-- listing page vehicle card start here  --}}
<x-listing-card :messageData="$messageData" :message="$message" :inventories="$inventories"/>
{{-- listing page vehicle card end here  --}}

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
