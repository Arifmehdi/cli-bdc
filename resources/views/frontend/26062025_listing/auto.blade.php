<?php
    use Illuminate\Support\Facades\Cookie;
    $searchData = json_decode(request()->cookie('searchData'), true) ?? [];
    $pricerangeData = $priceRange['used']['maxPrice'];
    ?>

<script>
    $(document).ready(function() {


            // This function runs when the page has fully loaded.
    document.addEventListener("DOMContentLoaded", function () {
        // Get URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const zipParam = urlParams.get("zip"); // Get zip code from URL
        let radiusParam = urlParams.get("radius"); // Get radius from URL

        // Select the distance dropdown element
        const radiusDropdown = document.getElementById("web_radios");
        const mobileRadiusDropdown = document.getElementById("mobile_radios");

        // If zip exists but radius is not set, default to 75
        if (zipParam && !radiusParam) {
            radiusParam = "75";
        }

        // Function to select the correct option in the dropdown
        function selectRadiusOption(dropdown, radius) {
            if (dropdown) {
                for (let option of dropdown.options) {
                    option.selected = option.value === radius;
                }
            }
        }

        // Set the default selection in the dropdown
        selectRadiusOption(radiusDropdown, radiusParam);
        selectRadiusOption(mobileRadiusDropdown, radiusParam);
    });
        //     // Handle change event for radius dropdown
        // $('#web_radios').on('change', function() {
        //     var newRadiusCode = $(this).val();
        //     var selectZipCode = $('#web_location').val();

        //     if (!selectZipCode) {
        //         $('#web_radios').val('');
        //         Swal.fire({
        //             icon: 'warning',
        //             title: 'Oops...',
        //             text: 'Please enter your zip code first to get near cars!',
        //             confirmButtonText: 'OK',
        //             confirmButtonColor: '#4caf50',
        //             background: '#f4f6f7',
        //             customClass: {
        //                 popup: 'animated tada'
        //             }
        //         });
        //     } else {
        //         updateUrlParameter('radius', newRadiusCode);  // Update URL with new radius
        //     }

        //     if (newRadiusCode && selectZipCode) {
        //         updateUrlParameter('radius', newRadiusCode);  // Update URL with new radius
        //     }
        // });

        // Ensure default radius value is set in the URL on page load if not present
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (!urlParams.has('radius')) {
                urlParams.set('radius', '75');  // Set default radius to 75 if missing
                window.history.replaceState({}, '', `${window.location.pathname}?${urlParams.toString()}`);
            }
        };
    // check for zip code start here check
        $('#web_location').on('change', function() {
            var web_radios = $('#web_radios').val();
            var webZipCode = $(this).val();
            updateUrlParameter('zip', webZipCode);

            if (!webZipCode && web_radios != 'Nationwide') {
                $('#web_radios').val('').change(); // Deselect all options
            } else {
                $('#web_radios').val('75').change(); // Set radius to 75 miles
            }
        });

                // check for zip code start here check  default 75 for web marif zip

        $('#mobile_location').on('change', function() {
            var mobileNewZipCode = $(this).val();
            updateUrlParameter('zip', mobileNewZipCode);

            if (mobileNewZipCode) {
                $('#mobile_radios').val('75').change(); // Set radius to 75 miles
            } else {
                $('#mobile_radios').val('').change(); // Deselect all options
            }
        });



        $('#web_radios').on('change', function() {

            var newRadiusCode = $(this).val();
            var selectZipCode = $('#web_location').val();
            if(!selectZipCode && newRadiusCode != 'Nationwide'){
                $('#web_radios').val('')
                Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Please enter your zip code first to get near cars!.....',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4caf50',
                background: '#f4f6f7',
                customClass: {
                popup: 'animated tada'
            }
        });
            }else{

                updateUrlParameter('radius', newRadiusCode);
                pageLoad();
            }
            if(newRadiusCode && selectZipCode){
                updateUrlParameter('radius', newRadiusCode);
                pageLoad();
            }
        });

    //     // check for zip code end here check



        // $('#mobile_location').on('change', function() {
        //     var newMobileZipCode = $(this).val();
        //     updateUrlParameter('zip', newMobileZipCode);

        //     if(newMobileZipCode)
        //      {
        //         $('#mobile_radios').val('75').change();
        //      } else {
        //         $('#mobile_radios').val('').change(); // Deselect all options
        //     }

        // });

        $('#mobile_location').on('change', function() {
            var newMobileZipCode = $(this).val();

            // Get the current URL
            var currentUrl = new URL(window.location.href);

            // Check if the URL has a 'radius' parameter
            var radius = currentUrl.searchParams.get("radius");
            var zip = currentUrl.searchParams.get("zip");

            if (!radius) {  // If 'radius' is missing
                if (zip) {  // If 'zip' exists, set radius=75
                    updateUrlParameter('radius', '75');
                }
            }

            // Update the 'zip' parameter
            updateUrlParameter('zip', newMobileZipCode);
        });



        $('#mobile_radios').on('change', function() {

            var newMobileRadiusCode = $(this).val();
            var selectMobileZipCode = $('#mobile_location').val();
            if(!selectMobileZipCode && newMobileRadiusCode != 'Nationwide'){
                $('#mobile_radios').val('')
                Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Please enter your zip code first to get near cars!',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4caf50',
                background: '#f4f6f7',
                customClass: {
                popup: 'animated tada'
            }
        });
            }else{

                updateUrlParameter('radius', newMobileRadiusCode);
                pageLoad();
            }
            if(newMobileRadiusCode && selectMobileZipCode){
                updateUrlParameter('radius', newMobileRadiusCode);
                pageLoad();
            }
        });


        $('#webMakeFilterMakeInput').on('change', function() {
            var selectedMake = $(this).val();
            // Update URL parameters
            updateUrlParameter('make', selectedMake);
            updateUrlParameter('model', '');
            updateUrlParameter('body', '');
        });

$('#secondFilterMakeInputNew').on('change', function() {
            var selectedMake = $(this).val();
            // Update URL parameters
            updateUrlParameter('make', selectedMake);
            updateUrlParameter('model', '');
            updateUrlParameter('body', '');
        });

        // Event listener for model dropdown
        $('#webModelFilterInput').on('change', function() {
            var selectedModel = $(this).val();
            updateUrlParameter('model', selectedModel);
        });


    // *************************** ui slider added start here *********************************
    var initialMinDisplayValue = 0;
    var initialMaxDisplayValue = 150000;

    // Replace with actual values or defaults if not present
    var searchData = @json($searchData);
    var nonLinearSlider = document.getElementById('price-ranger');
    var input0 = document.getElementById('min-price-ranger');
    var input1 = document.getElementById('max-price-ranger');
    var priceRangeDisplay = document.getElementById('price-range-display');

    var inputs = [input0, input1];

    var minValue = 0;
    var maxValue = 300000;

    var defaultMinValue = 0;
    var defaultMaxValue = 300000;

    // Extract values from session data or use defaults
    var priceMinValue = searchData && searchData.rangerMinPriceSlider ? parseFloat(searchData.rangerMinPriceSlider) : defaultMinValue;
    // var priceMaxValue = searchData && searchData.rangerMaxPriceSlider ? parseFloat(searchData.rangerMaxPriceSlider) : defaultMaxValue;
    var priceMaxValue = searchData && searchData.rangerMaxPriceSlider ? 300000 : defaultMaxValue;
    // alert(priceMaxValue);
    function priceFormatNumber(value) {
        return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function updatePriceRangeDisplay(minValue, maxValue) {
        var displayMinValue = (minValue === 0) ? initialMinDisplayValue : minValue;
        var displayMaxValue = (maxValue === initialMaxDisplayValue) ? initialMaxDisplayValue : maxValue;

        // Display 0 as 150000+ and any value above 150000+ as +150000
        priceRangeDisplay.textContent = (displayMinValue === 0 ? '0' : priceFormatNumber(displayMinValue)) + ' – ' + (displayMaxValue === initialMaxDisplayValue ? '150000+' : priceFormatNumber(displayMaxValue));
    }

    noUiSlider.create(nonLinearSlider, {
        connect: true,
        behaviour: 'tap',
        start: [priceMinValue, priceMaxValue],
        range: {
            'min': minValue,
            'max': maxValue
        },
        format: {
            to: function(value) {
                return Math.round(value);
            },
            from: function(value) {
                return Number(value);
            }
        }
    });

    // Initial price range display setup
    updatePriceRangeDisplay(priceMinValue, priceMaxValue);

    nonLinearSlider.noUiSlider.on('update', function(values, handle) {
        inputs[handle].value = values[handle];
        if (handle === 0) {
            priceMinValue = parseFloat(values[0]);
        } else {
            priceMaxValue = parseFloat(values[1]);
        }
        updatePriceRangeDisplay(priceMinValue, priceMaxValue);
    });

    nonLinearSlider.noUiSlider.on('change', function(values, handle) {
        inputs[handle].value = values[handle];

        // Update URL parameters
        updateUrlParameter('min_price', values[0]);
        updateUrlParameter('max_price', values[1]);
        updateUrlParameter('lowestPrice', '');
        updateUrlParameter('lowestMileage', '');
        updateUrlParameter('owned', '');

        if (handle === 0) {
            priceMinValue = parseFloat(values[0]);
        } else {
            priceMaxValue = parseFloat(values[1]);
        }

        pageLoad();
        updatePriceRangeDisplay(priceMinValue, priceMaxValue);
    });

    // If there are existing session values, update the slider inputs
    if (searchData) {
        nonLinearSlider.noUiSlider.set([
            parseFloat(searchData.rangerMinPriceSlider) || defaultMinValue,
            parseFloat(searchData.rangerMaxPriceSlider) || defaultMaxValue
        ]);
    }
    // **************************** ui slider added end here ***********************

    // *************************** mobile ui slider added start here **********************************************************************
    var initialMinDisplayValue = 0;
    var initialMaxDisplayValue = 150000;

    var mobileNonLinearSlider = document.getElementById('mobile-price-ranger');
    var mobileInput0 = document.getElementById('mobile-min-price-ranger');
    var mobileInput1 = document.getElementById('mobile-max-price-ranger');
    var mobilePriceRangeDisplay = document.getElementById('mobile-price-range-display');
    var mobileInputs = [mobileInput0, mobileInput1];

    var defaultMinPrice = 0; // Default minimum price
    var defaultMaxPrice = 150000; // Default maximum price

    // Extract values from session data or use defaults
    var minMobileValue = searchData ? (parseFloat(searchData.mobileRangerMinPriceSlider) || defaultMinPrice) : defaultMinPrice;
    var maxMobileValue = searchData ? (parseFloat(searchData.mobileRangerMaxPriceSlider) || defaultMaxPrice) : defaultMaxPrice;

    function priceMobileFormatNumber(value) {
        return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function updateMobilePriceRangeDisplay(minValue, maxValue) {
        var displayMinValue = minValue;
        var displayMaxValue = maxValue > initialMaxDisplayValue ? initialMaxDisplayValue : maxValue;
        mobilePriceRangeDisplay.textContent = priceMobileFormatNumber(displayMinValue) + ' – ' + priceMobileFormatNumber(displayMaxValue) + (maxValue > initialMaxDisplayValue ? '+' : '');
    }

    noUiSlider.create(mobileNonLinearSlider, {
        connect: true,
        behaviour: 'tap',
        start: [minMobileValue, maxMobileValue],
        range: {
            'min': defaultMinPrice,
            'max': defaultMaxPrice
        },
        format: {
            to: function(value) {
                return Math.round(value);
            },
            from: function(value) {
                return Number(value);
            }
        }
    });

    // Initial display range setup
    updateMobilePriceRangeDisplay(minMobileValue, maxMobileValue);

    mobileNonLinearSlider.noUiSlider.on('update', function(values, handle) {
        mobileInputs[handle].value = values[handle];
        if (handle === 0) {
            minMobileValue = parseFloat(values[0]);
        } else {
            maxMobileValue = parseFloat(values[1]);
        }
        updateMobilePriceRangeDisplay(minMobileValue, maxMobileValue);
    });

    mobileNonLinearSlider.noUiSlider.on('change', function(values, handle) {
        mobileInputs[handle].value = values[handle];

        updateUrlParameter('min_price', values[0]);
        updateUrlParameter('max_price', values[1]);
        updateUrlParameter('lowestPrice', '');
        updateUrlParameter('lowestMileage', '');
        updateUrlParameter('owned', '');

        if (handle === 0) {
            minMobileValue = parseFloat(values[0]);
        } else {
            maxMobileValue = parseFloat(values[1]);
        }

        // Backend update here
        pageLoad();
        console.log('Min Value:', minMobileValue, 'Max Value:', maxMobileValue);
        updateMobilePriceRangeDisplay(minMobileValue, maxMobileValue);
    });

    if (searchData) {
        mobileNonLinearSlider.noUiSlider.set([
            parseFloat(searchData.mobileRangerMinPriceSlider) || defaultMinPrice,
            parseFloat(searchData.mobileRangerMaxPriceSlider) || defaultMaxPrice
        ]);
    }
    // **************************** mobile ui slider added end here **********************
    // *************************** ui slider added for mileage start here *********************************

    var initialMinMileageDisplayValue = 0;
    var initialMaxMileageDisplayValue = 150000;

    var mileageNonLinearSlider = document.getElementById('mileage-ranger');
    var mileageInput0 = document.getElementById('min-mileage-ranger');
    var mileageInput1 = document.getElementById('max-mileage-ranger');
    var mileageRangeDisplay = document.getElementById('mileage-range-display');
    var mileageInputs = [mileageInput0, mileageInput1];

    var defaultMinMiles = 0; // Default minimum mileage
    var defaultMaxMiles = 150000; // Default maximum mileage

    // Extract values from session data or use defaults
    var mileageMinValue = searchData && searchData.rangerMileageMinPriceSlider ? parseFloat(searchData.rangerMileageMinPriceSlider) : defaultMinMiles;
    var mileageMaxValue = searchData && searchData.rangerMileageMaxPriceSlider ? parseFloat(searchData.rangerMileageMaxPriceSlider) : defaultMaxMiles;

    // Check if values are valid numbers
    if (isNaN(mileageMinValue)) {
        mileageMinValue = defaultMinMiles;
    }
    if (isNaN(mileageMaxValue)) {
        mileageMaxValue = defaultMaxMiles;
    }

    function milesFormatNumber(value) {
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function updateMileageRangeDisplay(minValue, maxValue) {
        var displayMinValue = minValue;
        var displayMaxValue = maxValue > initialMaxMileageDisplayValue ? initialMaxMileageDisplayValue : maxValue;
        var displayMaxValueText = maxValue >= initialMaxMileageDisplayValue ? milesFormatNumber(displayMaxValue) + '+' : milesFormatNumber(displayMaxValue);
        mileageRangeDisplay.textContent = milesFormatNumber(displayMinValue) + ' – ' + displayMaxValueText;
    }

    noUiSlider.create(mileageNonLinearSlider, {
        connect: true,
        behaviour: 'tap',
        start: [mileageMinValue, mileageMaxValue],
        range: {
            'min': defaultMinMiles,
            'max': defaultMaxMiles
        },
        format: {
            to: function(value) {
                return Math.round(value);
            },
            from: function(value) {
                return Number(value);
            }
        }
    });

    // Initial display range setup
    updateMileageRangeDisplay(mileageMinValue, mileageMaxValue);

    mileageNonLinearSlider.noUiSlider.on('update', function(values, handle) {
        mileageInputs[handle].value = values[handle];
        if (handle === 0) {
            mileageMinValue = parseFloat(values[0]);
        } else {
            mileageMaxValue = parseFloat(values[1]);
        }
        updateMileageRangeDisplay(mileageMinValue, mileageMaxValue);
    });

    mileageNonLinearSlider.noUiSlider.on('change', function(values, handle) {
        mileageInputs[handle].value = values[handle];

        // Update URL parameters
        updateUrlParameter('min-miles', values[0]);
        updateUrlParameter('max-miles', values[1]);
        updateUrlParameter('maximum_miles', '');
        updateUrlParameter('lowestPrice', '');
        updateUrlParameter('lowestMileage', '');
        updateUrlParameter('owned', '');

        if (handle === 0) {
            mileageMinValue = parseFloat(values[0]);
        } else {
            mileageMaxValue = parseFloat(values[1]);
        }

        // Backend update here
        pageLoad();
        updateMileageRangeDisplay(mileageMinValue, mileageMaxValue);
    });

    if (searchData) {
        mileageNonLinearSlider.noUiSlider.set([
            parseFloat(searchData.rangerMileageMinPriceSlider) || defaultMinMiles,
            parseFloat(searchData.rangerMileageMaxPriceSlider) || defaultMaxMiles
        ]);
    }

    // **************************** ui slider added for mileage end here ***********************

    // *************************** ui slider added for mobile mileage start here ***************************************************************************
    var initialMinMileageDisplayValue = 0;
    var initialMaxMileageDisplayValue = 150000;

    var mobileMileageNonLinearSlider = document.getElementById('mobile-mileage-ranger');
    var mobileMileageInput0 = document.getElementById('mobile-min-mileage-ranger');
    var mobileMileageInput1 = document.getElementById('mobile-max-mileage-ranger');
    var mobileMileageRangeDisplay = document.getElementById('mobile-mileage-range-display');
    var mobileMileageInputs = [mobileMileageInput0, mobileMileageInput1];

    var mobileMileageMinValue = searchData ? (parseFloat(searchData.mobileMileageRangerMinPriceSlider) || initialMinMileageDisplayValue) : initialMinMileageDisplayValue;
    var mobileMileageMaxValue = searchData ? (parseFloat(searchData.mobileMileageRangerMaxPriceSlider) || initialMaxMileageDisplayValue) : initialMaxMileageDisplayValue;

    function milesMobileFormatNumber(value) {
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function updateMobileMileageRangeDisplay(minValue, maxValue) {
        var displayMaxValue = maxValue > initialMaxMileageDisplayValue ? initialMaxMileageDisplayValue : maxValue;
        var displayMaxValueText = maxValue >= initialMaxMileageDisplayValue ? milesMobileFormatNumber(displayMaxValue) + '+' : milesMobileFormatNumber(displayMaxValue);
        mobileMileageRangeDisplay.textContent = milesMobileFormatNumber(minValue) + ' – ' + displayMaxValueText;
    }

    noUiSlider.create(mobileMileageNonLinearSlider, {
        connect: true,
        behaviour: 'tap',
        start: [mobileMileageMinValue, mobileMileageMaxValue],
        range: {
            'min': initialMinMileageDisplayValue,
            'max': initialMaxMileageDisplayValue
        },
        format: {
            to: function(value) {
                return Math.round(value);
            },
            from: function(value) {
                return Number(value);
            }
        }
    });

    // Initial display range setup
    updateMobileMileageRangeDisplay(mobileMileageMinValue, mobileMileageMaxValue);

    mobileMileageNonLinearSlider.noUiSlider.on('update', function(values, handle) {
        mobileMileageInputs[handle].value = values[handle];
        if (handle === 0) {
            mobileMileageMinValue = parseFloat(values[0]);
        } else {
            mobileMileageMaxValue = parseFloat(values[1]);
        }
        updateMobileMileageRangeDisplay(mobileMileageMinValue, mobileMileageMaxValue);
    });

    mobileMileageNonLinearSlider.noUiSlider.on('change', function(values, handle) {
        mobileMileageInputs[handle].value = values[handle];

        updateUrlParameter('min-miles', values[0]);
        updateUrlParameter('max-miles', values[1]);
        updateUrlParameter('maximum_miles', '');
        updateUrlParameter('lowestPrice', '');
        updateUrlParameter('lowestMileage', '');
        updateUrlParameter('owned', '');

        if (handle === 0) {
            mobileMileageMinValue = parseFloat(values[0]);
        } else {
            mobileMileageMaxValue = parseFloat(values[1]);
        }

        // Backend update here
        pageLoad();
        updateMobileMileageRangeDisplay(mobileMileageMinValue, mobileMileageMaxValue);
    });

    // If there are existing session values, update the slider inputs
    if (searchData) {
        mobileMileageNonLinearSlider.noUiSlider.set([
            parseFloat(searchData.mobileMileageRangerMinPriceSlider) || initialMinMileageDisplayValue,
            parseFloat(searchData.mobileMileageRangerMaxPriceSlider) || initialMaxMileageDisplayValue
        ]);
    }
    // **************************** ui slider added for mobile mileage end here ***********************

    // *************************** ui slider added for year start here *********************************
    var initialMinYearDisplayValue = 1985;
    var initialMaxYearDisplayValue = 2025;

    var yearNonLinearSlider = document.getElementById('year-ranger');
    var yearInput0 = document.getElementById('min-year-ranger');
    var yearInput1 = document.getElementById('max-year-ranger');
    var yearRangeDisplay = document.getElementById('year-range-display');
    var yearInputs = [yearInput0, yearInput1];

    var defaultMinYear = 1985;
    var defaultMaxYear = 2025;

    var yearMinValue = searchData ? (searchData.rangerYearMinPriceSlider || 1985) : 1985;
    var yearMaxValue = searchData ? (searchData.rangerYearMaxPriceSlider || 2025) : 2025;

    // Check if values are valid numbers
    if (isNaN(yearMinValue)) {
    yearMinValue = defaultMinYear;
    }
    if (isNaN(yearMaxValue)) {
        yearMaxValue = defaultMaxYear;
    }

    function updateYearRangeDisplay(minValue, maxValue) {
        if (minValue === defaultMinYear && maxValue === defaultMaxYear) {
            // yearRangeDisplay.textContent = initialMinYearDisplayValue + ' – ' + initialMaxYearDisplayValue;
            yearRangeDisplay.textContent = 'Any Year';
        } else if (minValue !== defaultMinYear && maxValue === defaultMaxYear) {
            yearRangeDisplay.textContent = minValue + ' – Newer';
        } else if (minValue === defaultMinYear && maxValue !== defaultMaxYear) {
            yearRangeDisplay.textContent = maxValue + ' – Older';
        } else {
            yearRangeDisplay.textContent = minValue + ' – ' + maxValue;
        }
    }

    noUiSlider.create(yearNonLinearSlider, {
        connect: true,
        // tooltips: true,
        behaviour: 'tap',
        start: [yearMinValue, yearMaxValue],
        range: {
            'min': [1985],
            'max': [2025]
        },
        format: {
            to: function(value) {
                return Math.round(value);
            },
            from: function(value) {
                return value;
            }
        }
    });


    // Initial display range setup
    updateYearRangeDisplay(yearMinValue, yearMaxValue);

    yearNonLinearSlider.noUiSlider.on('update', function(values, handle) {
        yearInputs[handle].value = values[handle];
        if (handle === 0) {
            yearMinValue = parseFloat(values[0]);
        } else {
            yearMaxValue = parseFloat(values[1]);
        }
        updateYearRangeDisplay(yearMinValue, yearMaxValue);
    });

    yearNonLinearSlider.noUiSlider.on('change', function(values, handle) {
        yearInputs[handle].value = values[handle];

        updateUrlParameter('min_year', values[0]);
        updateUrlParameter('max_year', values[1]);
        updateUrlParameter('lowestPrice', '');
        updateUrlParameter('lowestMileage', '');
        updateUrlParameter('owned', '');

        if (handle === 0) {
            yearMinValue = parseFloat(values[0]);
        } else {
            yearMaxValue = parseFloat(values[1]);
        }

        // Backend update here
        pageLoad();
        console.log('Min Year:', yearMinValue, 'Max Year:', yearMaxValue);
        updateYearRangeDisplay(yearMinValue, yearMaxValue);
    });

     // If there are existing session values, update the slider inputs
     if (searchData) {
            yearNonLinearSlider.noUiSlider.set([
                searchData.rangerYearMinPriceSlider || 1985,
                searchData.rangerYearMaxPriceSlider || 2025
            ]);
        }

    // **************************** ui slider added for year end here ***********************

    // *************************** ui slider added for mobile year start here *********************************
    var mobileYearNonLinearSlider = document.getElementById('mobile-year-ranger');
    var mobileYearInput0 = document.getElementById('mobile-min-year-ranger');
    var mobileYearInput1 = document.getElementById('mobile-max-year-ranger');
    var mobileYearRangeDisplay = document.getElementById('mobile-year-range-display');
    var mobileYearInputs = [mobileYearInput0, mobileYearInput1];

    var defaultMinYear = 1985;
    var defaultMaxYear = 2025;

    var mobileYearMinValue = searchData ? (searchData.mobileYearRangerMinPriceSlider || 1985) : 1985;
    var mobileYearMaxValue = searchData ? (searchData.mobileYearRangerMaxPriceSlider || 2025) : 2025;

    // Check if values are valid numbers
    if (isNaN(mobileYearMinValue)) {
    mobileYearMinValue = defaultMinYear;
    }
    if (isNaN(mobileYearMaxValue)) {
        mobileYearMaxValue = defaultMaxYear;
    }

    function updateMobileYearRangeDisplay(minValue, maxValue) {
        if (minValue === defaultMinYear && maxValue === defaultMaxYear) {
            // mobileYearRangeDisplay.textContent = initialMinYearDisplayValue + ' – ' + initialMaxYearDisplayValue;
            mobileYearRangeDisplay.textContent = 'Any Year';
        } else if (minValue !== defaultMinYear && maxValue === defaultMaxYear) {
            mobileYearRangeDisplay.textContent = minValue + ' – Newer';
        } else if (minValue === defaultMinYear && maxValue !== defaultMaxYear) {
            mobileYearRangeDisplay.textContent = maxValue + ' – Older';
        } else {
            mobileYearRangeDisplay.textContent = minValue + ' – ' + maxValue;
        }
    }

    noUiSlider.create(mobileYearNonLinearSlider, {
        connect: true,
        // tooltips: true,
        behaviour: 'tap',
        start: [mobileYearMinValue, mobileYearMaxValue],
        range: {
            'min': [1985],
            'max': [2025]
        },
        format: {
            to: function(value) {
                return Math.round(value);
            },
            from: function(value) {
                return value;
            }
        }
    });

    // Initial display range setup
    updateMobileYearRangeDisplay(mobileYearMinValue, mobileYearMaxValue);

    mobileYearNonLinearSlider.noUiSlider.on('update', function(values, handle) {
        mobileYearInputs[handle].value = values[handle];
        if (handle === 0) {
            mobileYearMinValue = parseFloat(values[0]);
        } else {
            mobileYearMaxValue = parseFloat(values[1]);
        }
        updateMobileYearRangeDisplay(mobileYearMinValue, mobileYearMaxValue);
    });

    mobileYearNonLinearSlider.noUiSlider.on('change', function(values, handle) {
        mobileYearInputs[handle].value = values[handle];

        updateUrlParameter('min_year', values[0]);
        updateUrlParameter('max_year', values[1]);
        updateUrlParameter('lowestPrice', '');
        updateUrlParameter('lowestMileage', '');
        updateUrlParameter('owned', '');

        if (handle === 0) {
            mobileYearMinValue = parseFloat(values[0]);
        } else {
            mobileYearMaxValue = parseFloat(values[1]);
        }

        // Backend update here
        pageLoad();
        console.log('Mobile Min Year:', mobileYearMinValue, 'Mobile Max Year:', mobileYearMaxValue);
        updateMobileYearRangeDisplay(mobileYearMinValue, mobileYearMaxValue);
    });



    // If there are existing session values, update the slider inputs
    if (searchData) {
        mobileYearNonLinearSlider.noUiSlider.set([
                searchData.mobileYearRangerMinPriceSlider || 1985,
                searchData.mobileYearRangerMaxPriceSlider || 2025
            ]);
        }



        $('.common_selector_click').on('click', function() {
                var color = $(this).data('value');
                $('#webColor').val(color);
                pageLoad();
            });

    // **************************** ui slider added for mobile year end here ***********************

            $(document).ready(function() {

                // WEB COLOR CODE START HERE
                const colorLinks = document.querySelectorAll('.web_common_selector_click');
                const selectedColor = document.getElementsByClassName('web_color-checkbox');
                const webColorInput = document.getElementById('webColor');

                function updateSelectedColors() {
                const selectedColorValues = Array.from(selectedColor)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

                colorLinks.forEach(link => {
                    const colorValue = link.getAttribute('data-value');
                    const icon = link.querySelector('.check-icon');

                    if (selectedColorValues.includes(colorValue)) {
                        $(link).addClass('selected');
                        $(icon).show();
                        if (colorValue === 'white') {
                            icon.style.color = 'black';
                        } else {
                            icon.style.color = 'white';
                        }
                    } else {
                        $(link).removeClass('selected');
                        $(icon).hide();
                    }
                });

                // Update the exterior-color parameter in the URL
                updateUrlParameter('exterior-color', selectedColorValues.join(','));

            }

            // Initial update based on stored cookie data
            updateSelectedColors();


                $('.web_common_selector_click').on('click', function() {
                    if ($(this).attr('data-value') === 'none') {
                        // Deselect all checkboxes and links
                        $('.web_color-checkbox').prop('checked', false);
                        $('.web_common_selector_click').removeClass('selected');
                        $('.check-icon').hide();
                        webColorInput.value = '';
                        updateUrlParameter('exterior-color', '');
                        updateUrlParameter('lowestPrice', '');
                        updateUrlParameter('lowestMileage', '');
                        updateUrlParameter('owned', '');
                        pageLoad();
                        return;
                    }

                    // Toggle checkbox
                    var $checkbox = $(this).prev('.web_color-checkbox');
                    $checkbox.prop('checked', !$checkbox.prop('checked'));

                    // Toggle selected class and icon color
                    $(this).toggleClass('selected');
                    const icon = this.querySelector('.check-icon');
                    if ($(this).hasClass('selected')) {
                        $(icon).show();
                        if ($(this).attr('data-value') === 'white') {
                            icon.style.color = 'black';
                        } else {
                            icon.style.color = 'white';
                        }
                    } else {
                        $(icon).hide();
                    }



                    // Update hidden input value
                    const selectedColors = Array.from(colorLinks)
                        .filter(link => link.classList.contains('selected'))
                        .map(link => link.getAttribute('data-value'));
                    webColorInput.value = selectedColors.join(',');

                    // Update the exterior-color parameter in the URL
                    updateUrlParameter('exterior-color', selectedColors.join(','));
                    updateUrlParameter('lowestPrice', '');
                    updateUrlParameter('lowestMileage', '');
                    updateUrlParameter('owned', '');
                    pageLoad();
                });


                // MOBILE COLOR CODE START HERE
                const mobileColorLinks = document.querySelectorAll('.mobile_common_selector_click');
                const mobileColorInput = document.getElementById('mobileColor');
                const mobileSelectedColor = document.getElementsByClassName('mobile_color-checkbox');

                function updateMobileSelectedColors() {
                const selectedColorValues = Array.from(mobileSelectedColor)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

                mobileColorLinks.forEach(link => {
                    const colorValue = link.getAttribute('data-value');
                    const icon = link.querySelector('.check-icon');

                    if (selectedColorValues.includes(colorValue)) {
                        $(link).addClass('selected');
                        $(icon).show();
                        if (colorValue === 'white') {
                            icon.style.color = 'black';
                        } else {
                            icon.style.color = 'white';
                        }
                    } else {
                        $(link).removeClass('selected');
                        $(icon).hide();
                    }
                });
            }

            // Initial update based on stored cookie data
            updateMobileSelectedColors();


                mobileColorLinks.forEach(link => {
                    var hammer = new Hammer(link);
                    hammer.on('tap', function (event) {
                        const target = event.target.closest('a');
                        if (target.getAttribute('data-value') === 'none') {
                            // Deselect all checkboxes and links
                            document.querySelectorAll('.mobile_color-checkbox').forEach(checkbox => checkbox.checked = false);
                            mobileColorLinks.forEach(link => link.classList.remove('selected'));
                            document.querySelectorAll('.check-icon').forEach(icon => icon.style.display = 'none');
                            mobileColorInput.value = '';
                            updateUrlParameter('lowestPrice', '');
                            updateUrlParameter('lowestMileage', '');
                            updateUrlParameter('owned', '');
                            pageLoad();
                            return;
                        }

                        // Toggle checkbox
                        var checkbox = target.previousElementSibling;
                        checkbox.checked = !checkbox.checked;

                        // Toggle selected class and icon color
                        target.classList.toggle('selected');
                        const icon = target.querySelector('.check-icon');
                        if (target.classList.contains('selected')) {
                            icon.style.display = 'block';
                            if (target.getAttribute('data-value') === 'white') {
                                icon.style.color = 'black';
                            } else {
                                icon.style.color = 'white';
                            }
                        } else {
                            icon.style.display = 'none';
                        }

                        // Update hidden input value
                        const selectedColors = Array.from(mobileColorLinks)
                            .filter(link => link.classList.contains('selected'))
                            .map(link => link.getAttribute('data-value'));
                        mobileColorInput.value = selectedColors.join(',');

                        // Update the exterior-color parameter in the URL
                        updateUrlParameter('exterior-color', selectedColors.join(','));
                        updateUrlParameter('lowestPrice', '');
                        updateUrlParameter('lowestMileage', '');
                        updateUrlParameter('owned', '');
                        pageLoad();
                    });
                });


// WEB BODY WORKING CODE HERE
var zip = getUrlParameter('zip');

var home = getUrlParameter('home');

if (zipcode && !zip && !home) {
    console.log('hello one');

    updateUrlParameter('zip', zipcode);
    $('#web_location').val(zipcode);
    $('#mobile_location').val(zipcode);
}

if(!zip && home == 2)
{
    console.log('hello two');
    updateUrlParameter('zip', '');

}

// if(zip != null)
// {
//     console.log('hello three');
//     updateUrlParameter('zip', zipcode);
//     $('#web_location').val(zipcode);
//     $('#mobile_location').val(zipcode);
// }


if (zipcode && (home === 1 || zip)) {
    console.log('hello four');
    updateUrlParameter('zip', zipcode);
    $('#web_location').val(zipcode);
    $('#mobile_location').val(zipcode);
}

if(zipcode && home == 1)
{
    console.log('Home one active');
    updateUrlParameter('zip', zipcode);
    $('#web_location').val(zipcode);
    $('#mobile_location').val(zipcode);
}

// zipcode && (home === 1 || zip)

// if(zipcode && (home == 1 || zip != null))
// {

//     console.log('hello four');
//     updateUrlParameter('zip', zipcode);
//     $('#web_location').val(zipcode);
//     $('#mobile_location').val(zipcode);

// }


// If `zip` is `null` and `home` is `1`
if (zip == null && home == 1) {
    console.log('hello five');
    // Update the URL to remove the `zip` parameter
    updateUrlParameter('zip', '');


}

// If `zipcode` exists and `zip` is still `null` (and `home` is not `1`)
// if (zipcode && zip == null && home != 1) {
//     console.log('hello six');
//     updateUrlParameter('zip', zipcode);
//     $('#web_location').val(zipcode);
//     $('#mobile_location').val(zipcode);
// }

var home2 = getUrlParameter('home2');
var body = getUrlParameter('body');

// If `home2` or `home` is present and `zip` is not `null`
if ((home2 || home) && zip) {

    $('#web_location').val(zip);
    $('#mobile_location').val(zip);
    updateUrlParameter('zip', zip);
}

const webBodyLinks = document.querySelectorAll('.web_body_type_click');
const webBodyInput = document.getElementById('webbody');
const webSelectedBody = document.getElementsByClassName('web_body-checkbox');

function updateWebSelectedBodies() {
    const selectedBodyValues = Array.from(webSelectedBody)
        .filter(checkbox => checkbox.checked)
        .map(checkbox => checkbox.value);

    webBodyLinks.forEach(link => {
        const bodyValue = link.getAttribute('data-Testvalue');
        const parentDiv = link.closest('.custom-col');

        if (selectedBodyValues.includes(bodyValue)) {
            link.classList.add('selected');
            parentDiv.classList.add('active');
        } else {
            link.classList.remove('selected');
            parentDiv.classList.remove('active');
        }
    });


    // Update the URL parameter
    if(home)
    {
        updateUrlParameter('body',body);
        $('#webbody').val('');

    }else
    {
        updateUrlParameter('body', selectedBodyValues.join(','));
    }

}

// Initial update based on stored cookie data
 updateWebSelectedBodies();

webBodyLinks.forEach(link => {
    link.addEventListener('click', function() {
        const bodyValue = link.getAttribute('data-Testvalue');
        const checkbox = Array.from(webSelectedBody).find(cb => cb.value === bodyValue);
        const parentDiv = link.closest('.custom-col');
            // updateUrlParameter('body',bodyValue);
            updateUrlParameter('home',false);
        if (checkbox.checked) {
            // Deselect if the body type is already selected
            checkbox.checked = false;
            link.classList.remove('selected');
            parentDiv.classList.remove('active');
        } else {
            // Deselect all checkboxes and links
            Array.from(webSelectedBody).forEach(cb => cb.checked = false);
            webBodyLinks.forEach(l => l.classList.remove('selected'));
            document.querySelectorAll('.custom-col').forEach(div => div.classList.remove('active'));

            // Select the clicked checkbox and link
            checkbox.checked = true;
            link.classList.add('selected');
            parentDiv.classList.add('active');
        }

        // Update hidden input value
        const selectedBodies = Array.from(webBodyLinks)
            .filter(l => l.classList.contains('selected'))
            .map(l => l.getAttribute('data-Testvalue'));
            webBodyInput.value = selectedBodies.join(',');
        // Update the body-type parameter in the URL
        updateUrlParameter('body', selectedBodies.join(','));
        pageLoad();
    });
});




// MOBILE BODY WORKING CODE HERE
var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body
.clientWidth;

if (screenWidth < 1000) {


const mobileBodyLinks = document.querySelectorAll('.mobile_body_type_click');
const mobileBodyInput = document.getElementById('mobileBody');
const mobileSelectedBody = document.getElementsByClassName('mobile_body-checkbox');

function updateMobileSelectedBodies() {
    const selectedBodyValues = Array.from(mobileSelectedBody)
        .filter(checkbox => checkbox.checked)
        .map(checkbox => checkbox.value);

    mobileBodyLinks.forEach(link => {
        const bodyValue = link.getAttribute('data-Testvalue');
        const img = link.querySelector('img');
        const customCol = link.closest('.custom-col');

        if (selectedBodyValues.includes(bodyValue)) {
            link.classList.add('selected');
            customCol.classList.add('active');
        } else {
            link.classList.remove('selected');
            customCol.classList.remove('active');
        }
    });

//     // Update hidden input value
     mobileBodyInput.value = selectedBodyValues.join(',');

    // Update the URL parameter
    if(home)
    {
        updateUrlParameter('body',body);
        $('#mobileBody').val('');

    }else
    {
        updateUrlParameter('body', selectedBodyValues.join(','));
    }

}

// // Initial update based on stored cookie data
 updateMobileSelectedBodies();

mobileBodyLinks.forEach(link => {
    link.addEventListener('click', function() {
        const bodyValue = link.getAttribute('data-Testvalue');
        const checkbox = Array.from(mobileSelectedBody).find(cb => cb.value === bodyValue);
        const customCol = link.closest('.custom-col');
        updateUrlParameter('home',false);
        if (checkbox.checked) {
            // Deselect if the body type is already selected
            checkbox.checked = false;
            link.classList.remove('selected');
            customCol.classList.remove('active');
        } else {
            // Deselect all checkboxes and links
            Array.from(mobileSelectedBody).forEach(cb => cb.checked = false);
            mobileBodyLinks.forEach(l => l.classList.remove('selected'));
            document.querySelectorAll('.custom-col').forEach(col => col.classList.remove('active'));

            // Select the clicked checkbox and link
            checkbox.checked = true;
            link.classList.add('selected');
            customCol.classList.add('active');
        }

        // Update hidden input value
        const selectedBodies = Array.from(mobileBodyLinks)
            .filter(l => l.classList.contains('selected'))
            .map(l => l.getAttribute('data-Testvalue'));
        mobileBodyInput.value = selectedBodies.join(',');

        // Update the body-type parameter in the URL
        updateUrlParameter('body', selectedBodies.join(','));
        pageLoad();
    });
});

}


  });



//   *****************mobile condition search ajax code start here  **********************************************************

$('.mobile_common_selector').on('change', function() { pageLoad(); });
//   *****************mobile condition  search ajax code End  here  **********************************************************


    //auto list filter start here
function pageLoad(page = 1) {


    // $(document).ready(function(){

    //     var web_radius = $('#web_radios').val();
    //     var mobile_radius = $('#mobile_radios').val();

    //     var web_location = $('#web_location').val();
    //     var mobile_location = $('#mobile_location').val();
    //     if(web_radius == 'Nationwide')
    //     {
    //         $('#web_location').val('');
    //     }
    //     if(mobile_radius == 'Nationwide')
    //     {
    //         $('#mobile_location').val('');
    //     }

    //     if(web_location == '' && web_radius != 'Nationwide')
    //     {
    //         $('#web_radios').val('');
    //     }
    //     if(mobile_location == '' && mobile_radius != 'Nationwide')
    //     {
    //         $('#mobile_radios').val('');
    //     }




    //     var action;
    //     var loaderId = 'loading';
    //     var loader = '<div id="loading" style="position: fixed; width: 100%; height: 100%; display: block; background-color: rgba(218, 233, 232, 0.7); left: 0; top: 0;"></div>';
    //     if ($('#' + loaderId).length) {
    //         $('#' + loaderId).remove();
    //     }
    //     $('#auto_ajax').append(loader);


    //     var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body
    //     .clientWidth;
    //     var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body
    //         .clientHeight;
    //     var requestURL = window.location.href;



    //     if (screenWidth < 1000) {
    //         updateUrlParameter('px', 900);
    //         action = 'mobile_check-box-data';
    //         var minPrice = $('#minPriceInput').val();
    //         var maxPrice = $('#maxPriceInput').val();
    //         var minPrice = $('#minimumPriceInput').val();
    //         var anymile = $('#minPriceInput').val();

    //         var secondFilterZipInput = $('#secondFilterZipInput').val();
    //         var secondFilterMakeInput = $('#secondFilterMakeInput').val();
    //         var secondFilterModelInput = $('#secondFilterModelInput').val();
    //         var mobile_web_search_any = $('#mobile_web_search_any').val();
    //         var autoMobileMakeCheckbox = get_filter('autoMobileMakeCheckbox');

    //         var makeCheckdata = get_filter('autoMakeCheckbox');
    //         var autoMaxBodyCheckbox = get_filter('autoMaxBodyCheckbox');
    //         var autoMobileMaxBodyCheckbox = get_filter('autoMobileMaxBodyCheckbox');
    //         var autoMobileMinYearCheckbox = get_filter('autoMobileMinYearCheckbox');
    //         var autoMobileMaxYearCheckbox = get_filter('autoMobileMaxYearCheckbox');
    //         var autoMobileTypeCheckbox = get_filter('autoMobileTypeCheckbox');

    //         var autoMobileDriveTrainCheckbox = get_filter('autoMobileDriveTrainCheckbox');
    //         var autoMobileExteriorColorCheckbox = get_filter('autoMobileExteriorColorCheckbox');
    //         var autoWebInteriorColorCheckbox = get_filter('autoWebInteriorColorCheckbox');


    //         var autoMobileFuelCheckbox = get_filter('autoMobileFuelCheckbox');
    //         var autoMobileTransmissionCheckbox = get_filter('autoMobileTransmissionCheckbox');
    //         var mobileColorFilter = get_filter('mobile_color-checkbox');
    //         var mobileRangerMinPriceSlider = $('#mobile-min-price-ranger').val();
    //         var mobileRangerMaxPriceSlider = $('#mobile-max-price-ranger').val();
    //         var mobileMileageRangerMinPriceSlider = $('#mobile-min-mileage-ranger').val();
    //         var mobileMileageRangerMaxPriceSlider = $('#mobile-max-mileage-ranger').val();
    //         var mobileYearRangerMinPriceSlider = $('#mobile-min-year-ranger').val();
    //         var mobileYearRangerMaxPriceSlider = $('#mobile-max-year-ranger').val();
    //         var secondFilterMakeInputNew =  $('#secondFilterMakeInputNew').val();
    //         var secondFilterModelInputNew =  $('#secondFilterModelInputNew').val();
    //         var mobileBody = $('#mobileBody').val();
    //         var mobilelocation = $('#mobile_location').val();
    //         var mobileRadios = $('#mobile_radios').val();
    //         var selected_sort_search = $('#mobile_selected_sort_search').val();



    //         $.ajax({
    //             url: "{{ route('auto') }}?page=" + page,
    //             type: "get",
    //             data: {

    //                 requestURL: requestURL,
    //                 action: action,
    //                 minPriceAuto: minPrice,
    //                 maxPriceAuto: maxPrice,
    //                 secondFilterZipInput: secondFilterZipInput,
    //                 secondFilterMakeInput: secondFilterMakeInput,
    //                 secondFilterModelInput: secondFilterModelInput,
    //                 web_search_any: web_search_any,
    //                 makeCheckdata: makeCheckdata,
    //                 autoMaxBodyCheckbox: autoMaxBodyCheckbox,
    //                 mobile_web_search_any: mobile_web_search_any,
    //                 autoMobileMakeCheckbox: autoMobileMakeCheckbox,
    //                 autoMobileMaxBodyCheckbox: autoMobileMaxBodyCheckbox,
    //                 autoMobileMinYearCheckbox: autoMobileMinYearCheckbox,
    //                 autoMobileMaxYearCheckbox: autoMobileMaxYearCheckbox,
    //                 mobileRangerMinPriceSlider: mobileRangerMinPriceSlider,
    //                 mobileRangerMaxPriceSlider: mobileRangerMaxPriceSlider,
    //                 mobileMileageRangerMinPriceSlider: mobileMileageRangerMinPriceSlider,
    //                 mobileMileageRangerMaxPriceSlider: mobileMileageRangerMaxPriceSlider,
    //                 mobileYearRangerMinPriceSlider: mobileYearRangerMinPriceSlider,
    //                 mobileYearRangerMaxPriceSlider: mobileYearRangerMaxPriceSlider,
    //                 autoMobileTypeCheckbox: autoMobileTypeCheckbox,
    //                 secondFilterMakeInputNew : secondFilterMakeInputNew,
    //                 secondFilterModelInputNew : secondFilterModelInputNew,
    //                 autoMobileFuelCheckbox : autoMobileFuelCheckbox,
    //                 autoMobileTransmissionCheckbox : autoMobileTransmissionCheckbox,
    //                 mobileColorFilter : mobileColorFilter,
    //                 autoMobileDriveTrainCheckbox : autoMobileDriveTrainCheckbox,
    //                 autoMobileExteriorColorCheckbox : autoMobileExteriorColorCheckbox,
    //                 autoWebInteriorColorCheckbox : autoWebInteriorColorCheckbox,
    //                 mobileBody : mobileBody,
    //                 mobilelocation : mobilelocation,
    //                 mobileRadios : mobileRadios,
    //                 selected_sort_search:selected_sort_search,
    //             },
    //             success: function(res) {
    //                 $(window).scrollTop(0);
    //                 $('#total-count').text(res.total_count);

    //                 $('#auto_ajax').html(res.view);
    //             },
    //             error: function(error) {

    //             }
    //         });

    //     } else {
    //         updateUrlParameter('px', 1000);

    //         loader;
    //         action = 'web_check-box-data';
    //         var minPrice = $('#minPriceInput').val();
    //         var maxPrice = $('#maxPriceInput').val();
    //         var minPrice = $('#minimumPriceInput').val();
    //         var anymile = $('#minPriceInput').val();
    //         var firstzipFilter = $('#firstFilterZipInput').val();
    //         var firstMakeFilter = $('#firstFilterMakeInput').val();
    //         var firstModelFilter = $('#firstFilterModelInput').val();
    //         var web_search_any = $('#web_search_any').val();
    //         var makeCheckdata = get_filter('autoMakeCheckbox');
    //         var autoMaxBodyCheckbox = get_filter('autoMaxBodyCheckbox');
    //         var autoMinYearCheckbox = $('#autoMinYearCheckbox').val();
    //         var autoMaxYearCheckbox = $('#autoMaxYearCheckbox').val();
    //         var rangerMinPriceSlider = $('#min-price-ranger').val();
    //         var rangerMaxPriceSlider = $('#max-price-ranger').val();
    //         var rangerMileageMinPriceSlider = $('#min-mileage-ranger').val();
    //         var rangerMileageMaxPriceSlider = $('#max-mileage-ranger').val();
    //         var rangerYearMinPriceSlider = $('#min-year-ranger').val();
    //         var rangerYearMaxPriceSlider = $('#max-year-ranger').val();
    //         var totalLoanAmountCalculation = $('#monthlyBudget').val();
    //         var autoWebFuelCheckbox = get_filter('autoWebFuelCheckbox');
    //         var autoWebExteriorColorCheckbox = get_filter('autoWebExteriorColorCheckbox');
    //         var autoWebInteriorColorCheckbox = get_filter('autoWebInteriorColorCheckbox');
    //         var autoWebConditionCheckbox = get_filter('autoWebConditionCheckbox');
    //         var autoWebTransmissionCheckbox = get_filter('autoWebTransmissionCheckbox');
    //         var autoWebDriveTrainCheckbox = get_filter('autoWebDriveTrainCheckbox');
    //         var webMakeFilterMakeInput = $('#webMakeFilterMakeInput').val()
    //         var webModelFilterInput = $('#webModelFilterInput').val();
    //         var weblocationNewInput = $('#web_location').val();
    //         var webBodyFilter = $('#webbody').val()
    //         var webColorFilter = get_filter('web_color-checkbox');
    //         var webCity = $('#web_city_data').val();
    //         var webRadios = $('#web_radios').val();
    //         // var selected_sort_search = $('#selected_sort_search').val();
    //         var selected_sort_search = $('#mobile_selected_sort_search').val();



    //         var count = 0 ;

    //         $.ajax({
    //             url: "{{ route('auto') }}?page=" + page,
    //             type: "get",
    //             data: {
    //                 // data:data,
    //                 requestURL: requestURL,
    //                 action: action,
    //                 minPriceAuto: minPrice,
    //                 maxPriceAuto: maxPrice,
    //                 firstzipFilter: firstzipFilter,
    //                 firstMakeFilter: firstMakeFilter,
    //                 firstModelFilter: firstModelFilter,
    //                 web_search_any: web_search_any,
    //                 makeCheckdata: makeCheckdata,
    //                 autoMaxBodyCheckbox: autoMaxBodyCheckbox,
    //                 autoMinYearCheckbox: autoMinYearCheckbox,
    //                 autoMaxYearCheckbox: autoMaxYearCheckbox,
    //                 rangerMinPriceSlider: rangerMinPriceSlider,
    //                 rangerMaxPriceSlider: rangerMaxPriceSlider,
    //                 rangerMileageMinPriceSlider: rangerMileageMinPriceSlider,
    //                 rangerMileageMaxPriceSlider: rangerMileageMaxPriceSlider,
    //                 rangerYearMinPriceSlider: rangerYearMinPriceSlider,
    //                 rangerYearMaxPriceSlider: rangerYearMaxPriceSlider,
    //                 totalLoanAmountCalculation: totalLoanAmountCalculation,
    //                 autoWebFuelCheckbox: autoWebFuelCheckbox,
    //                 autoWebExteriorColorCheckbox: autoWebExteriorColorCheckbox,
    //                 autoWebInteriorColorCheckbox: autoWebInteriorColorCheckbox,
    //                 autoWebConditionCheckbox: autoWebConditionCheckbox,
    //                 autoWebTransmissionCheckbox: autoWebTransmissionCheckbox,
    //                 autoWebDriveTrainCheckbox: autoWebDriveTrainCheckbox,
    //                 webMakeFilterMakeInput: webMakeFilterMakeInput,
    //                 webModelFilterInput: webModelFilterInput,
    //                 weblocationNewInput: weblocationNewInput,
    //                 webBodyFilter: webBodyFilter,
    //                 webColorFilter: webColorFilter,
    //                 webCity:webCity,
    //                 webRadios:webRadios,
    //                 selected_sort_search:selected_sort_search,
    //             },
    //             beforeSend: function() {
    //                 // Show the loader before the request is sent
    //                 $('#loader').show();
    //             },
    //             success: function(res) {
    //                 $(window).scrollTop(0);
    //                 $('#web_sticky_btn').text(res.total_count);
    //                 if(res.custom_reload == true)
    //                 {
    //                 console.log('hello check ');
    //                     window.location.reload();
    //                 }
    //                 $('#auto_ajax').html(res.view);

    //             },
    //             error: function(error) {
    //                 console.log('Error:', error);
    //             },
    //             complete: function() {
    //                 // Hide the loader after the request is complete
    //                 $('#loader').hide();
    //             }
    //         });
    //     }
    // });
}

    // calculator button
    $(document).ready(function(){
        $(document).on('click','#submitCalcultor', function(e){
            e.preventDefault()
            pageLoad();
        });
    });

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 'fast');
        var page = $(this).attr('href').split('page=')[1];
        pageLoad(page);
    });

    function get_filter(class_name) {
        var filter = [];
        $('.' + class_name + ':checked').each(function() {
            filter.push($(this).val());
        });
        return filter; // Add this line to return the filter array
    }

    $('.common_selector').on('change', function() {
        pageLoad();
    });


    $(document).on('change','#selected_sort_search', function() {
        var search_value = $(this).val();
        var zipCodeValue = $("#web_location").val();

        if (search_value == 'distance|asc') {
            if (!zipCodeValue) {
                Swal.fire({
                    title: 'Enter your zip code',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    showLoaderOnConfirm: true,
                    preConfirm: (zip) => {
                        return new Promise((resolve) => {
                            resolve(zip);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        var userZipCodeValue = result.value;
                        $("#web_location").val(userZipCodeValue);
                        updateUrlParameter('zip', userZipCodeValue);
                        $('#web_radios').val('75').change();
                    }
                });
            }
        }
        pageLoad();
    });

    $(document).on('change','#mobile_selected_sort_search', function() {
        pageLoad();
    });


    pageLoad();
});

$(document).on('click', '#webSearchBtn', function(e) {
    e.preventDefault();
    $(this).text('Loading...');
    setTimeout(function() {
        $('#webSearchBtn').text('Search');
        pageLoad();
    }, 500);
})

$(document).on('click', '#mobileSeartchBtn', function(e) {
    e.preventDefault();
    $(this).text('Loading...');
    setTimeout(function() {
        $('#FilterModal').modal('hide');
        $('#mobileSeartchBtn').text('Search');
    }, 2000);
});

$(document).on('click', '#applyWebSearchBtn', function(e) {
    e.preventDefault();
    $(this).text('Loading...');
    setTimeout(function() {
        $('#applyWebSearchBtn').text('Search');
        loader;
        pageLoad();
    }, 2000);
});

$(document).on('click', '#applyMobileSearchBtn', function(e) {
    e.preventDefault();
    $(this).text('Loading...');
    setTimeout(function() {
        $('#FilterModal').modal('hide');
        $('#applyMobileSearchBtn').text('Search');
    }, 1000);
})


// Check if the "showModal" query parameter is present
const urlParams = new URLSearchParams(window.location.search);
const lowestPriceValue = urlParams.get('lowestPrice');
const bestdealPriceValue = urlParams.get('bestDeal');
const ownedPriceValue = urlParams.get('owned');

</script>


<script>
// Use JavaScript to get the screen width not work yet
var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

// Send the screen width to the server using AJAX
var xhr = new XMLHttpRequest();
xhr.open('GET', 'update-screen-width.php?width=' + screenWidth, true);
xhr.send();
</script>

<script>



    // Function to merge tooltips and format them properly
    function mergePriceTooltips(slider, threshold, separator) {
        var textIsRtl = getComputedStyle(slider).direction === 'rtl';
        var isRtl = slider.noUiSlider.options.direction === 'rtl';
        var isVertical = slider.noUiSlider.options.orientation === 'vertical';
        var tooltips = slider.noUiSlider.getTooltips();
        var origins = slider.noUiSlider.getOrigins();

        tooltips.forEach(function(tooltip, index) {
            if (tooltip) {
                origins[index].appendChild(tooltip);
            }
        });

        slider.noUiSlider.on('update', function(values, handle, unencoded, tap, positions) {
            var pools = [
                []
            ];
            var poolPositions = [
                []
            ];
            var poolValues = [
                []
            ];
            var atPool = 0;

            if (tooltips[0]) {
                pools[0][0] = 0;
                poolPositions[0][0] = positions[0];
                poolValues[0][0] = values[0];
            }

            for (var i = 1; i < positions.length; i++) {
                if (!tooltips[i] || (positions[i] - positions[i - 1]) > threshold) {
                    atPool++;
                    pools[atPool] = [];
                    poolValues[atPool] = [];
                    poolPositions[atPool] = [];
                }

                if (tooltips[i]) {
                    pools[atPool].push(i);
                    poolValues[atPool].push(values[i]);
                    poolPositions[atPool].push(positions[i]);
                }
            }

            pools.forEach(function(pool, poolIndex) {
                var handlesInPool = pool.length;

                for (var j = 0; j < handlesInPool; j++) {
                    var handleNumber = pool[j];

                    if (j === handlesInPool - 1) {
                        var offset = 0;

                        poolPositions[poolIndex].forEach(function(value) {
                            offset += 1000 - value;
                        });

                        var direction = isVertical ? 'bottom' : 'right';
                        var last = isRtl ? 0 : handlesInPool - 1;
                        var lastOffset = 1000 - poolPositions[poolIndex][last];
                        offset = (textIsRtl && !isVertical ? 100 : 0) + (offset / handlesInPool) -
                            lastOffset;

                        tooltips[handleNumber].innerHTML = poolValues[poolIndex].map(priceFormatNumber).join(separator);
                        tooltips[handleNumber].style.display = 'block';
                        tooltips[handleNumber].style[direction] = offset + '%';
                    } else {
                        tooltips[handleNumber].style.display = 'none';
                    }
                }
            });
        });
    }



function mergeTooltips(slider, threshold, separator) {

var textIsRtl = getComputedStyle(slider).direction === 'rtl';
var isRtl = slider.noUiSlider.options.direction === 'rtl';
var isVertical = slider.noUiSlider.options.orientation === 'vertical';
var tooltips = slider.noUiSlider.getTooltips();
var origins = slider.noUiSlider.getOrigins();

// Move tooltips into the origin element. The default stylesheet handles this.
tooltips.forEach(function(tooltip, index) {
    if (tooltip) {
        origins[index].appendChild(tooltip);
    }
});

slider.noUiSlider.on('update', function(values, handle, unencoded, tap, positions) {

    var pools = [
        []
    ];
    var poolPositions = [
        []
    ];
    var poolValues = [
        []
    ];
    var atPool = 0;

    // Assign the first tooltip to the first pool, if the tooltip is configured
    if (tooltips[0]) {
        pools[0][0] = 0;
        poolPositions[0][0] = positions[0];
        poolValues[0][0] = values[0];
    }

    for (var i = 1; i < positions.length; i++) {
        if (!tooltips[i] || (positions[i] - positions[i - 1]) > threshold) {
            atPool++;
            pools[atPool] = [];
            poolValues[atPool] = [];
            poolPositions[atPool] = [];
        }

        if (tooltips[i]) {
            pools[atPool].push(i);
            poolValues[atPool].push(values[i]);
            poolPositions[atPool].push(positions[i]);
        }
    }

    pools.forEach(function(pool, poolIndex) {
        var handlesInPool = pool.length;

        for (var j = 0; j < handlesInPool; j++) {
            var handleNumber = pool[j];

            if (j === handlesInPool - 1) {
                var offset = 0;

                poolPositions[poolIndex].forEach(function(value) {
                    offset += 1000 - value;
                });

                var direction = isVertical ? 'bottom' : 'right';
                var last = isRtl ? 0 : handlesInPool - 1;
                var lastOffset = 1000 - poolPositions[poolIndex][last];
                offset = (textIsRtl && !isVertical ? 100 : 0) + (offset / handlesInPool) -
                    lastOffset;

                // Center this tooltip over the affected handles
                tooltips[handleNumber].innerHTML = poolValues[poolIndex].join(separator);
                tooltips[handleNumber].style.display = 'block';
                tooltips[handleNumber].style[direction] = offset + '%';
            } else {
                // Hide this tooltip
                tooltips[handleNumber].style.display = 'none';
            }
        }
    });
});

}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.autoMobileTypeCheckbox');

    // function updateFuelParameter() {
    //     const selectedFuels = Array.from(webFuelCheckboxes)
    //         .filter(checkbox => checkbox.checked)
    //         .map(checkbox => checkbox.value);

    //     updateUrlParameter('fuel', selectedFuels.join(','));
    // }

    selectAllCheckbox.addEventListener('change', function() {
        if (selectAllCheckbox.checked) {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                selectAllCheckbox.checked = false;
            }
        });
    });

});

document.addEventListener('DOMContentLoaded', function() {
    // web fuel
    const selectAllWebFuelCheckbox = document.getElementById('selectWebAllFuelCheckbox');
    const webFuelCheckboxes = document.querySelectorAll('.autoWebFuelCheckbox');

    function updateFuelParameter() {
        const selectedFuels = Array.from(webFuelCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        updateUrlParameter('fuel', selectedFuels.join(','));
    }

    selectAllWebFuelCheckbox.addEventListener('change', function() {
        if (selectAllWebFuelCheckbox.checked) {
            webFuelCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
        updateFuelParameter();
        updateUrlParameter('lowestPrice', '');
        updateUrlParameter('lowestMileage', '');
        updateUrlParameter('owned', '');
    });

    webFuelCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                selectAllWebFuelCheckbox.checked = false;
            }
            updateFuelParameter();
            updateUrlParameter('lowestPrice', '');
            updateUrlParameter('lowestMileage', '');
            updateUrlParameter('owned', '');
        });
    });

    // Initial call to set the parameter based on the current state of checkboxes
    updateFuelParameter();

    // mobile fuel
    const selectAllFuelCheckbox = document.getElementById('selectAllFuelCheckbox');
    const fuelCheckboxes = document.querySelectorAll('.autoMobileFuelCheckbox');

    function updateMobileFuelParameter() {
        const selectedFuels = Array.from(fuelCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        updateUrlParameter('fuel', selectedFuels.join(','));
    }

    selectAllFuelCheckbox.addEventListener('change', function() {
        if (selectAllFuelCheckbox.checked) {
            fuelCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
        updateMobileFuelParameter();
        updateUrlParameter('lowestPrice', '');
        updateUrlParameter('lowestMileage', '');
        updateUrlParameter('owned', '');
    });

    fuelCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                selectAllFuelCheckbox.checked = false;
            }
            updateMobileFuelParameter();
            updateUrlParameter('lowestPrice', '');
            updateUrlParameter('lowestMileage', '');
            updateUrlParameter('owned', '');
        });
    });

    // Initial call to set the parameter based on the current state of checkboxes
    updateMobileFuelParameter();
// });

// document.addEventListener('DOMContentLoaded', function() {
    // web condition
    const selectAllWebConditionCheckbox = document.getElementById('selectAllWebConditionCheckbox');
        const webConditionCheckboxes = document.querySelectorAll('.autoWebConditionCheckbox');

        function updateWebConditionParameter() {
            const selectedConditions = Array.from(webConditionCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);

            updateUrlParameter('condition', selectedConditions.join(','));
            // var newZipCode = $('#web_location').val();
            // updateUrlParameter('zip', newZipCode);
        }

        selectAllWebConditionCheckbox.addEventListener('change', function() {
            if (selectAllWebConditionCheckbox.checked) {
                webConditionCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
            updateWebConditionParameter();
            updateUrlParameter('lowestPrice', '');
            updateUrlParameter('lowestMileage', '');
            updateUrlParameter('owned', '');
        });

        webConditionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (checkbox.checked) {
                    selectAllWebConditionCheckbox.checked = false;
                }
                updateWebConditionParameter();
                updateUrlParameter('lowestPrice', '');
                updateUrlParameter('lowestMileage', '');
                updateUrlParameter('owned', '');
            });
        });

        // Initial call to set the parameter based on the current state of checkboxes
        updateWebConditionParameter();


    // web transmission
    const selectAllWebTransmissionCheckbox = document.getElementById('selectAllWebTransmissionCheckbox');
    const webTransmissionCheckboxes = document.querySelectorAll('.autoWebTransmissionCheckbox');

    function updateTransmissionParameter() {
        const selectedTransmissions = Array.from(webTransmissionCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        updateUrlParameter('transmission', selectedTransmissions.join(','));
        // var newZipCode = $('#web_location').val();
        //updateUrlParameter('zip', newZipCode);
    }

    selectAllWebTransmissionCheckbox.addEventListener('change', function() {
        if (selectAllWebTransmissionCheckbox.checked) {
            webTransmissionCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
        updateTransmissionParameter();
        updateUrlParameter('lowestPrice', '');
        updateUrlParameter('lowestMileage', '');
        updateUrlParameter('owned', '');
    });

    webTransmissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                selectAllWebTransmissionCheckbox.checked = false;
            }
            updateTransmissionParameter();
            updateUrlParameter('lowestPrice', '');
            updateUrlParameter('lowestMileage', '');
            updateUrlParameter('owned', '');
        });
    });

    // Initial call to set the parameter based on the current state of checkboxes
    updateTransmissionParameter();


    // mobile transmission
    const selectAllTransmissionCheckbox = document.getElementById('selectAllTranscissionCheckbox');
    const TransmissionCheckboxes = document.querySelectorAll('.autoMobileTransmissionCheckbox');

    function updateMobileTransmissionParameter() {
        const mobileSelectedTransmissions = Array.from(TransmissionCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        updateUrlParameter('transmission', mobileSelectedTransmissions.join(','));
    }

    selectAllTransmissionCheckbox.addEventListener('change', function() {
        if (selectAllTransmissionCheckbox.checked) {
            TransmissionCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
        updateMobileTransmissionParameter();
        updateUrlParameter('lowestPrice', '');
        updateUrlParameter('lowestMileage', '');
        updateUrlParameter('owned', '');
    });

    TransmissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                selectAllTransmissionCheckbox.checked = false;
            }
            updateMobileTransmissionParameter();
            updateUrlParameter('lowestPrice', '');
            updateUrlParameter('lowestMileage', '');
            updateUrlParameter('owned', '');
        });
    });

    // Initial call to set the parameter based on the current state of checkboxes
    updateMobileTransmissionParameter();
// });




    $(document).on('click','#stickyButton', function(e){
        e.preventDefault()
        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
    });

    $(document).on('change','#web_state_data', function(){
        var stateId = $(this).val()

        if(stateId != ''){
            $.ajax({
                url: "{{ route('frontend.state.search') }}/"+stateId,
                type:'post',
                success: function(res){
                    var cityDropdown = $('#web_city_data')
                    cityDropdown.empty()
                    cityDropdown.append('<option value="">Any City</option>')
                    $.each(res, function(index, city) {
                    cityDropdown.append('<option value="' + city.city + '">' + city.city + '</option>');
                });
                },
                error: function(errors){
                    console.log(errors);

                }
            });
        }else{

            updateUrlParameter('zip', '');
            $('#web_location').val('');
            $('#web_state_data').val('');
            window.location.reload()
        }

    });


    $(document).on('change','#web_city_data', function(){
        var cityId = $(this).val()
        alert('check');
        console.log(cityId)
        if(cityId == "Houston"){
            updateUrlParameter('zip', 77007);
            $('#web_location').val(77007);
        }
         if(cityId == "Dallas"){
            updateUrlParameter('zip', 75241);
            $('#web_location').val(75241);
        }
         if(cityId == "Austin"){
            updateUrlParameter('zip', 78702);
            $('#web_location').val(78702);
        }
         if(cityId == "San Antonio"){
            updateUrlParameter('zip', 78205);
            $('#web_location').val(78205);
        }
         if(cityId == ''){
            updateUrlParameter('zip', '');
            $('#web_location').val('');
            $('#web_state_data').val('');
            location.reload()
        }


    });
});

function getUrlParameter(name) {
name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
var results = regex.exec(location.search);
return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

function updateUrlParameter(param, value)
{
        var currentUrl = window.location.href;
        var url = new URL(currentUrl);
        var searchParams = new URLSearchParams(url.search);
        searchParams.set(param, value);
        url.search = searchParams.toString();
        window.history.replaceState(null, '', url.toString());
}



</script>
<!-- all checked unce=hecked  -->
<script>
    $(document).ready(function() {
        // Handle the "All" checkbox fuel
        $('#selectAllFuel').change(function() {
            if ($(this).prop('checked')) {
                // If "All" is checked, uncheck all individual checkboxes
                $('.autoWebFuelCheckbox').prop('checked', false);
            }
        });

        // Handle individual fuel type checkboxes
        $('.autoWebFuelCheckbox').change(function() {
            // If any individual checkbox is checked, uncheck the "All" checkbox
            if ($('.autoWebFuelCheckbox:checked').length > 0) {
                $('#selectAllFuel').prop('checked', false);
            } else {
                // If no individual checkboxes are checked, check the "All" checkbox
                $('#selectAllFuel').prop('checked', true);
            }
        });

        // Handle the "All" checkbox exterior color
        $('#selectAllWebExteriorColor').change(function() {
            if ($(this).prop('checked')) {
                // If "All" is checked, uncheck all individual checkboxes
                $('.autoWebExteriorColorCheckbox').prop('checked', false);
            }
        });

        // Handle individual fuel type checkboxes
        $('.autoWebExteriorColorCheckbox').change(function() {
            // If any individual checkbox is checked, uncheck the "All" checkbox
            if ($('.autoWebExteriorColorCheckbox:checked').length > 0) {
                $('#selectAllWebExteriorColor').prop('checked', false);
            } else {
                // If no individual checkboxes are checked, check the "All" checkbox
                $('#selectAllWebExteriorColor').prop('checked', true);
            }
        });

        // Handle the "All" checkbox interior color
        $('#selectAllWebInteriorColor').change(function() {
            if ($(this).prop('checked')) {
                // If "All" is checked, uncheck all individual checkboxes
                $('.autoWebInteriorColorCheckbox').prop('checked', false);
            }
        });

        // Handle individual fuel type checkboxes
        $('.autoWebInteriorColorCheckbox').change(function() {
            // If any individual checkbox is checked, uncheck the "All" checkbox
            if ($('.autoWebInteriorColorCheckbox:checked').length > 0) {
                $('#selectAllWebInteriorColor').prop('checked', false);
            } else {
                // If no individual checkboxes are checked, check the "All" checkbox
                $('#selectAllWebInteriorColor').prop('checked', true);
            }
        });


        // Handle the "All" checkbox interior color
        $('#selectAllFuelCheckbox').change(function() {
            if ($(this).prop('checked')) {
                // If "All" is checked, uncheck all individual checkboxes
                $('.autoMobileFuelCheckbox').prop('checked', false);
            }
        });

        // Handle individual fuel type checkboxes
        $('.autoMobileFuelCheckbox').change(function() {
            // If any individual checkbox is checked, uncheck the "All" checkbox
            if ($('.autoMobileFuelCheckbox:checked').length > 0) {
                $('#selectAllFuelCheckbox').prop('checked', false);
            } else {
                // If no individual checkboxes are checked, check the "All" checkbox
                $('#selectAllFuelCheckbox').prop('checked', true);
            }
        });


        // Handle the "All" checkbox interior color
        $('#selectAllFuelCheckbox').change(function() {
            if ($(this).prop('checked')) {
                // If "All" is checked, uncheck all individual checkboxes
                $('.autoMobileFuelCheckbox').prop('checked', false);
            }
        });

        // Handle individual fuel type checkboxes
        $('.autoMobileFuelCheckbox').change(function() {
            // If any individual checkbox is checked, uncheck the "All" checkbox
            if ($('.autoMobileFuelCheckbox:checked').length > 0) {
                $('#selectAllFuelCheckbox').prop('checked', false);
            } else {
                // If no individual checkboxes are checked, check the "All" checkbox
                $('#selectAllFuelCheckbox').prop('checked', true);
            }
        });

        // Handle the "All" checkbox mobile exteroior color
        $('#selectAllMobileExteriorColor').change(function() {
            if ($(this).prop('checked')) {
                // If "All" is checked, uncheck all individual checkboxes
                $('.autoMobileExteriorColorCheckbox').prop('checked', false);
            }
        });

        // Handle individual fuel type checkboxes
        $('.autoMobileExteriorColorCheckbox').change(function() {
            // If any individual checkbox is checked, uncheck the "All" checkbox
            if ($('.autoMobileExteriorColorCheckbox:checked').length > 0) {
                $('#selectAllMobileExteriorColor').prop('checked', false);
            } else {
                // If no individual checkboxes are checked, check the "All" checkbox
                $('#selectAllMobileExteriorColor').prop('checked', true);
            }
        });

        // Handle the "All" checkbox mobile interoior color
        $('#selectAllMobileInteriorColor').change(function() {
            if ($(this).prop('checked')) {
                // If "All" is checked, uncheck all individual checkboxes
                $('.autoWebInteriorColorCheckbox').prop('checked', false);
            }
        });

        // Handle individual fuel type checkboxes
        $('.autoWebInteriorColorCheckbox').change(function() {
            // If any individual checkbox is checked, uncheck the "All" checkbox
            if ($('.autoWebInteriorColorCheckbox:checked').length > 0) {
                $('#selectAllMobileInteriorColor').prop('checked', false);
            } else {
                // If no individual checkboxes are checked, check the "All" checkbox
                $('#selectAllMobileInteriorColor').prop('checked', true);
            }
        });

    });
</script>
