
<style>
    .active-filters-container {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
        /* margin: 0 10px 15px 10px; */
    }
    
    .filter-chips-container {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px; /* This creates consistent spacing between chips */
    }
    
    .filter-chip {
        display: inline-flex;
        align-items: center;
        background-color: #e9ecef;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 14px;
        white-space: nowrap; /* Prevent text wrapping within chips */
    }
    
    .filter-close {
        background: none;
        border: none;
        margin-left: 5px;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        padding: 0 0 0 5px;
        color: #6c757d;
    }
    
    .filter-close:hover {
        color: #dc3545;
    }
    
    #clearAllFiltersBtn {
        color: #0d6efd;
        font-size: 14px;
        padding-right: 2rem;
    }
    
    #clearAllFiltersBtn:hover {
        color: #0a58ca;
        text-decoration: underline;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .active-filters-container {
            padding: 8px 12px;
            margin: 0 5px 10px 5px;
        }
        
        .active-filters-container .d-flex {
            flex-direction: row; /* Keep horizontal layout on mobile */
            flex-wrap: wrap; /* Allow wrapping */
            align-items: center;
            gap: 8px;
        }
        
        .active-filters-container h6 {
            margin-bottom: 0 !important;
            margin-right: 8px;
        }
        
        #clearAllFiltersBtn {
            margin-top: 0;
            margin-left: auto !important; /* Push to the right */
        }
        
        .filter-chip {
            font-size: 13px;
            padding: 4px 8px;
            margin: 0; /* Remove margin since we're using gap */
        }

        /* This ensures chips stay inline and wrap naturally */
        .filter-chips-container {
            display: inline-flex;
            flex-wrap: wrap;
            flex-grow: 1;
        }
    }

    /* Fix for margin-right */
    /* .active-filters-container {
        margin-right: 20px !important;
    } */
</style>
<div class="active-filters-container mb-3">
    <div class="d-flex align-items-center">
        <h6 class="mb-0 me-2">Active filters:</h6>
        <!-- Active Filter Chips Container -->
        <div class="d-flex flex-wrap" id="activeFiltersContainer">
            <!-- Filters will be added here dynamically -->
        </div>

        <!-- Active Filter Chips -->
       
        {{--<div class="d-flex flex-wrap">
            <!-- Category Filter -->


            @if (!empty($messageCookieData))
            <div class="filter-chip me-2 mb-2">
                <span>        
                    {{ $messageCookieData }}
                </span>
                <button class="filter-close" data-filter-type="category">&times;</button>
            </div>
            @endif
            <div class="filter-chip me-2 mb-2">
                <span>All Category</span>
                <button class="filter-close" data-filter-type="category">&times;</button>
            </div>
            
            <!-- Government Filter -->
            <div class="filter-chip me-2 mb-2">
                <span>Government</span>
                <button class="filter-close" data-filter-type="government">&times;</button>
            </div>
            
            <!-- Add more filter chips dynamically as filters are applied -->
        </div>--}}
        
        <!-- Clear All Button -->
        <button class="btn btn-link text-decoration-none ms-auto pr-2" id="clearAllFiltersBtn">
            Clear all
        </button>
    </div>
</div>
