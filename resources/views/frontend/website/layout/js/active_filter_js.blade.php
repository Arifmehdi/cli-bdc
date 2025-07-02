<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Track active filters
        const activeFilters = {
            conditions: new Set(),
            search: '',
            distance: '75',
            zip: '',
            allConditions: false,
            make: '',
            model: '',
            bodyTypes: new Set(),
            exteriorColors: new Set(),
            interiorColors: new Set(),
            driveTrains: new Set(),
            fuelTypes: new Set(),
            allExteriorColors: false,
            allInteriorColors: false,
            allDriveTrains: false,
            allFuelTypes: false
        };

        // Initialize from URL parameters
        function initializeFromUrl() {
            const params = new URLSearchParams(window.location.search);

            // Search
            if (params.has('search')) {
                activeFilters.search = params.get('search');
                document.getElementById('web_search_any').value = activeFilters.search;
            }

            // Distance
            if (params.has('distance')) {
                activeFilters.distance = params.get('distance');
                document.getElementById('web_radios').value = activeFilters.distance;
            }

            // Zip
            if (params.has('zip')) {
                activeFilters.zip = params.get('zip');
                document.getElementById('web_location').value = activeFilters.zip;
            }

            // Conditions
            if (params.has('conditions')) {
                const conditions = params.get('conditions').split(',');
                conditions.forEach(condition => {
                    if (condition === 'all') {
                        activeFilters.allConditions = true;
                        document.getElementById('selectAllWebConditionCheckbox').checked = true;
                        document.querySelectorAll('.autoWebConditionCheckbox').forEach(cb => cb.checked = true);
                    } else {
                        activeFilters.conditions.add(condition);
                        const checkbox = document.querySelector(`.autoWebConditionCheckbox[value="${condition}"]`);
                        if (checkbox) checkbox.checked = true;
                    }
                });
            }

            // Make
            if (params.has('make')) {
                activeFilters.make = params.get('make');
                document.getElementById('webMakeFilterMakeInput').value = activeFilters.make;
            }

            // Model
            if (params.has('model')) {
                activeFilters.model = params.get('model');
                document.getElementById('webModelFilterInput').value = activeFilters.model;
            }

            // Body Types
            if (params.has('bodyTypes')) {
                const bodyTypes = params.get('bodyTypes').split(',');
                bodyTypes.forEach(bodyType => {
                    activeFilters.bodyTypes.add(bodyType);
                    const checkbox = document.querySelector(`.web_body-checkbox[value="${bodyType}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.closest('.custom-col').classList.add('active');
                    }
                });
            }

            // Exterior Colors
            if (params.has('exteriorColors')) {
                const exteriorColors = params.get('exteriorColors').split(',');
                exteriorColors.forEach(color => {
                    if (color === 'all') {
                        activeFilters.allExteriorColors = true;
                        document.getElementById('selectAllWebExteriorColor').checked = true;
                        document.querySelectorAll('.autoWebExteriorColorCheckbox').forEach(cb => cb.checked = true);
                    } else {
                        activeFilters.exteriorColors.add(color);
                        const checkbox = document.querySelector(`.autoWebExteriorColorCheckbox[value="${color}"]`);
                        if (checkbox) checkbox.checked = true;
                    }
                });
            }

            // Interior Colors
            if (params.has('interiorColors')) {
                const interiorColors = params.get('interiorColors').split(',');
                interiorColors.forEach(color => {
                    if (color === 'all') {
                        activeFilters.allInteriorColors = true;
                        document.getElementById('selectAllWebInteriorColor').checked = true;
                        document.querySelectorAll('.autoWebInteriorColorCheckbox').forEach(cb => cb.checked = true);
                    } else {
                        activeFilters.interiorColors.add(color);
                        const checkbox = document.querySelector(`.autoWebInteriorColorCheckbox[value="${color}"]`);
                        if (checkbox) checkbox.checked = true;
                    }
                });
            }

            // Drive Trains
            if (params.has('driveTrains')) {
                const driveTrains = params.get('driveTrains').split(',');
                driveTrains.forEach(driveTrain => {
                    if (driveTrain === 'all') {
                        activeFilters.allDriveTrains = true;
                        document.getElementById('selectAllWebDriveTrain').checked = true;
                        document.querySelectorAll('.autoWebDriveTrainCheckbox').forEach(cb => cb.checked = true);
                    } else {
                        activeFilters.driveTrains.add(driveTrain);
                        const checkbox = document.querySelector(`.autoWebDriveTrainCheckbox[value="${driveTrain}"]`);
                        if (checkbox) checkbox.checked = true;
                    }
                });
            }

            // Fuel Types
            if (params.has('fuelTypes')) {
                const fuelTypes = params.get('fuelTypes').split(',');
                fuelTypes.forEach(fuelType => {
                    if (fuelType === 'all') {
                        activeFilters.allFuelTypes = true;
                        document.getElementById('selectAllFuel').checked = true;
                        document.querySelectorAll('.autoWebFuelCheckbox').forEach(cb => cb.checked = true);
                    } else {
                        activeFilters.fuelTypes.add(fuelType);
                        const checkbox = document.querySelector(`.autoWebFuelCheckbox[value="${fuelType}"]`);
                        if (checkbox) checkbox.checked = true;
                    }
                });
            }

            updateActiveFiltersDisplay();
        }

        // Update the visible filter chips
        function updateActiveFiltersDisplay() {
            const container = document.getElementById('activeFiltersContainer');
            container.innerHTML = '';

            // Search filter
            if (activeFilters.search) {
                addFilterChip('Search', activeFilters.search, `"${activeFilters.search}"`);
            }

            // Distance filter (if not default)
            if (activeFilters.distance && activeFilters.distance !== '75') {
                addFilterChip('Distance', activeFilters.distance, `${activeFilters.distance} miles`);
            }

            // Zip filter
            if (activeFilters.zip) {
                addFilterChip('Zip', activeFilters.zip, activeFilters.zip);
            }

            // Condition filters
            if (activeFilters.allConditions) {
                addFilterChip('Condition', 'all', 'All Conditions');
            } else if (activeFilters.conditions.size > 0) {
                activeFilters.conditions.forEach(condition => {
                    addFilterChip('Condition', condition, condition);
                });
            }

            // Make filter
            if (activeFilters.make) {
                const makeOption = document.querySelector(`#webMakeFilterMakeInput option[value="${activeFilters.make}"]`);
                if (makeOption) {
                    addFilterChip('Make', activeFilters.make, makeOption.textContent);
                }
            }

            // Model filter
            if (activeFilters.model) {
                addFilterChip('Model', activeFilters.model, activeFilters.model);
            }

            // Body Type filters
            activeFilters.bodyTypes.forEach(bodyType => {
                addFilterChip('BodyType', bodyType, bodyType);
            });

            // Exterior Color filters
            if (activeFilters.allExteriorColors) {
                addFilterChip('ExteriorColor', 'all', 'All Exterior Colors');
            } else if (activeFilters.exteriorColors.size > 0) {
                activeFilters.exteriorColors.forEach(color => {
                    addFilterChip('ExteriorColor', color, color);
                });
            }

            // Interior Color filters
            if (activeFilters.allInteriorColors) {
                addFilterChip('InteriorColor', 'all', 'All Interior Colors');
            } else if (activeFilters.interiorColors.size > 0) {
                activeFilters.interiorColors.forEach(color => {
                    addFilterChip('InteriorColor', color, color);
                });
            }

            // Drive Train filters
            if (activeFilters.allDriveTrains) {
                addFilterChip('DriveTrain', 'all', 'All Drive Trains');
            } else if (activeFilters.driveTrains.size > 0) {
                activeFilters.driveTrains.forEach(driveTrain => {
                    addFilterChip('DriveTrain', driveTrain, driveTrain);
                });
            }

            // Fuel Type filters
            if (activeFilters.allFuelTypes) {
                addFilterChip('FuelType', 'all', 'All Fuel Types');
            } else if (activeFilters.fuelTypes.size > 0) {
                activeFilters.fuelTypes.forEach(fuelType => {
                    addFilterChip('FuelType', fuelType, fuelType);
                });
            }
        }

        // Add a filter chip to the display
        function addFilterChip(type, value, displayText) {
            const container = document.getElementById('activeFiltersContainer');
            const chip = document.createElement('div');
            chip.className = 'filter-chip me-2 mb-2';

            // Special handling for color chips
            if (type.toLowerCase().includes('color')) {
                chip.innerHTML = `
                <span>
                    <span class="circle-color me-2" style="background-color: ${value === 'all' ? 'transparent' : value}; width: 15px; height: 15px; display: inline-block; border-radius: 50%; border: 1px solid #C0BDBD;"></span>
                    ${displayText}
                </span>
                <button class="filter-close common_selector_btn" data-filter-type="${type.toLowerCase()}" data-filter-value="${value}">&times;</button>
            `;
            } else {
                chip.innerHTML = `
                <span>${displayText}</span>
                <button class="filter-close common_selector_btn"  data-filter-type="${type.toLowerCase()}" data-filter-value="${value}">&times;</button>
            `;
            }

            container.appendChild(chip);

            // Add remove handler
            chip.querySelector('.filter-close').addEventListener('click', function() {
                removeFilter(type.toLowerCase(), value);
            });
        }

        // Remove a filter
        function removeFilter(type, value) {
            switch (type) {
                case 'search':
                    activeFilters.search = '';
                    document.getElementById('web_search_any').value = '';
                    break;

                case 'distance':
                    activeFilters.distance = '75';
                    document.getElementById('web_radios').value = '75';
                    break;

                case 'zip':
                    activeFilters.zip = '';
                    document.getElementById('web_location').value = '';
                    break;

                case 'condition':
                    if (value === 'all') {
                        activeFilters.allConditions = false;
                        document.getElementById('selectAllWebConditionCheckbox').checked = false;
                        document.querySelectorAll('.autoWebConditionCheckbox').forEach(cb => cb.checked = false);
                    } else {
                        activeFilters.conditions.delete(value);
                        document.querySelector(`.autoWebConditionCheckbox[value="${value}"]`).checked = false;
                        document.getElementById('selectAllWebConditionCheckbox').checked = false;
                    }
                    break;

                case 'make':
                    activeFilters.make = '';
                    document.getElementById('webMakeFilterMakeInput').value = '';
                    activeFilters.model = '';
                    document.getElementById('webModelFilterInput').value = '';
                    break;

                case 'model':
                    activeFilters.model = '';
                    document.getElementById('webModelFilterInput').value = '';
                    break;

                case 'bodytype':
                    activeFilters.bodyTypes.delete(value);
                    document.querySelector(`.web_body-checkbox[value="${value}"]`).checked = false;
                    document.querySelector(`[data-Testvalue="${value}"]`).closest('.custom-col').classList.remove('active');
                    break;

                case 'exteriorcolor':
                    if (value === 'all') {
                        activeFilters.allExteriorColors = false;
                        document.getElementById('selectAllWebExteriorColor').checked = false;
                        document.querySelectorAll('.autoWebExteriorColorCheckbox').forEach(cb => cb.checked = false);
                    } else {
                        activeFilters.exteriorColors.delete(value);
                        document.querySelector(`.autoWebExteriorColorCheckbox[value="${value}"]`).checked = false;
                        document.getElementById('selectAllWebExteriorColor').checked = false;
                    }
                    break;

                case 'interiorcolor':
                    if (value === 'all') {
                        activeFilters.allInteriorColors = false;
                        document.getElementById('selectAllWebInteriorColor').checked = false;
                        document.querySelectorAll('.autoWebInteriorColorCheckbox').forEach(cb => cb.checked = false);
                    } else {
                        activeFilters.interiorColors.delete(value);
                        document.querySelector(`.autoWebInteriorColorCheckbox[value="${value}"]`).checked = false;
                        document.getElementById('selectAllWebInteriorColor').checked = false;
                    }
                    break;

                case 'drivetrain':
                    if (value === 'all') {
                        activeFilters.allDriveTrains = false;
                        document.getElementById('selectAllWebDriveTrain').checked = false;
                        document.querySelectorAll('.autoWebDriveTrainCheckbox').forEach(cb => cb.checked = false);
                    } else {
                        activeFilters.driveTrains.delete(value);
                        document.querySelector(`.autoWebDriveTrainCheckbox[value="${value}"]`).checked = false;
                        document.getElementById('selectAllWebDriveTrain').checked = false;
                    }
                    break;

                case 'fueltype':
                    if (value === 'all') {
                        activeFilters.allFuelTypes = false;
                        document.getElementById('selectAllFuel').checked = false;
                        document.querySelectorAll('.autoWebFuelCheckbox').forEach(cb => cb.checked = false);
                    } else {
                        activeFilters.fuelTypes.delete(value);
                        document.querySelector(`.autoWebFuelCheckbox[value="${value}"]`).checked = false;
                        document.getElementById('selectAllFuel').checked = false;
                    }
                    break;
            }

            updateActiveFiltersDisplay();
            applyFilters();
        }

        // Apply all filters (update URL)
        function applyFilters() {
            const params = new URLSearchParams();

            if (activeFilters.search) params.set('search', activeFilters.search);
            if (activeFilters.distance && activeFilters.distance !== '75') params.set('distance', activeFilters.distance);
            if (activeFilters.zip) params.set('zip', activeFilters.zip);

            // // Handle condition parameters
            // if (activeFilters.allConditions) {
            //     params.set('conditions', 'all');
            // } else if (activeFilters.conditions.size > 0) {
            //     params.set('conditions', Array.from(activeFilters.conditions).join(','));
            // }

            // Handle condition parameters
            if (activeFilters.conditions.size > 0) {
                params.set('conditions', Array.from(activeFilters.conditions).join(','));
            }

            // Make and Model
            if (activeFilters.make) params.set('make', activeFilters.make);
            if (activeFilters.model) params.set('model', activeFilters.model);

            // Body Types
            if (activeFilters.bodyTypes.size > 0) {
                params.set('bodyTypes', Array.from(activeFilters.bodyTypes).join(','));
            }

            // Exterior Colors
            if (activeFilters.exteriorColors.size > 0) {
                params.set('exteriorColors', Array.from(activeFilters.exteriorColors).join(','));
            }

            // Interior Colors
            if (activeFilters.interiorColors.size > 0) {
                params.set('interiorColors', Array.from(activeFilters.interiorColors).join(','));
            }

            // Drive Trains
            if (activeFilters.driveTrains.size > 0) {
                params.set('driveTrains', Array.from(activeFilters.driveTrains).join(','));
            }

            // Fuel Types
            if (activeFilters.fuelTypes.size > 0) {
                params.set('fuelTypes', Array.from(activeFilters.fuelTypes).join(','));
            }


            // // Exterior Colors
            // if (activeFilters.allExteriorColors) {
            //     params.set('exteriorColors', 'all');
            // } else if (activeFilters.exteriorColors.size > 0) {
            //     params.set('exteriorColors', Array.from(activeFilters.exteriorColors).join(','));
            // }

            // // Interior Colors
            // if (activeFilters.allInteriorColors) {
            //     params.set('interiorColors', 'all');
            // } else if (activeFilters.interiorColors.size > 0) {
            //     params.set('interiorColors', Array.from(activeFilters.interiorColors).join(','));
            // }

            // // Drive Trains
            // if (activeFilters.allDriveTrains) {
            //     params.set('driveTrains', 'all');
            // } else if (activeFilters.driveTrains.size > 0) {
            //     params.set('driveTrains', Array.from(activeFilters.driveTrains).join(','));
            // }

            // // Fuel Types
            // if (activeFilters.allFuelTypes) {
            //     params.set('fuelTypes', 'all');
            // } else if (activeFilters.fuelTypes.size > 0) {
            //     params.set('fuelTypes', Array.from(activeFilters.fuelTypes).join(','));
            // }


            // Update URL without reload
            const newUrl = window.location.pathname + (params.toString() ? `?${params.toString()}` : '');
            window.history.pushState({}, '', newUrl);

            // Here you would typically also trigger your data loading function
            console.log('Filters applied:', activeFilters);
            // loadFilteredData();
        }

        // Event listeners for existing filters
        document.getElementById('webSearchBtn').addEventListener('click', function() {
            activeFilters.search = document.getElementById('web_search_any').value.trim();
            updateActiveFiltersDisplay();
            applyFilters();
        });

        document.getElementById('web_radios').addEventListener('change', function() {
            activeFilters.distance = this.value;
            updateActiveFiltersDisplay();
            applyFilters();
        });

        document.getElementById('web_location').addEventListener('change', function() {
            activeFilters.zip = this.value.trim();
            updateActiveFiltersDisplay();
            applyFilters();
        });

        // Handle individual condition checkboxes
        document.querySelectorAll('.autoWebConditionCheckbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                document.getElementById('selectAllWebConditionCheckbox').checked = false;
                activeFilters.allConditions = false;

                if (this.checked) {
                    activeFilters.conditions.add(this.value);
                } else {
                    activeFilters.conditions.delete(this.value);
                }

                const allCheckboxes = document.querySelectorAll('.autoWebConditionCheckbox');
                const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);

                if (allChecked) {
                    document.getElementById('selectAllWebConditionCheckbox').checked = true;
                    activeFilters.allConditions = true;
                    activeFilters.conditions.clear();
                }

                updateActiveFiltersDisplay();
                applyFilters();
            });
        });

        // Handle "All" checkbox for conditions
        document.getElementById('selectAllWebConditionCheckbox').addEventListener('change', function() {
            const isChecked = this.checked;
            activeFilters.allConditions = isChecked;

            document.querySelectorAll('.autoWebConditionCheckbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });

            if (isChecked) {
                activeFilters.conditions.clear();
            }

            updateActiveFiltersDisplay();
            applyFilters();
        });

        // Event listeners for Make, Model, and Body Type filters
        document.getElementById('webMakeFilterMakeInput').addEventListener('change', function() {
            activeFilters.make = this.value;
            activeFilters.model = '';
            document.getElementById('webModelFilterInput').value = '';
            updateActiveFiltersDisplay();
            applyFilters();
        });

        document.getElementById('webModelFilterInput').addEventListener('change', function() {
            activeFilters.model = this.value;
            updateActiveFiltersDisplay();
            applyFilters();
        });

        // Handle body type clicks
        document.querySelectorAll('.web_body_type_click').forEach(element => {
            element.addEventListener('click', function() {
                const bodyType = this.getAttribute('data-Testvalue');
                const checkbox = document.querySelector(`.web_body-checkbox[value="${bodyType}"]`);
                const isActive = this.closest('.custom-col').classList.contains('active');

                if (isActive) {
                    activeFilters.bodyTypes.delete(bodyType);
                    this.closest('.custom-col').classList.remove('active');
                    checkbox.checked = false;
                } else {
                    activeFilters.bodyTypes.add(bodyType);
                    this.closest('.custom-col').classList.add('active');
                    checkbox.checked = true;
                }

                updateActiveFiltersDisplay();
                applyFilters();
            });
        });

        // Handle Exterior Color checkboxes
        document.querySelectorAll('.autoWebExteriorColorCheckbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                document.getElementById('selectAllWebExteriorColor').checked = false;
                activeFilters.allExteriorColors = false;

                if (this.checked) {
                    activeFilters.exteriorColors.add(this.value);
                } else {
                    activeFilters.exteriorColors.delete(this.value);
                }

                const allCheckboxes = document.querySelectorAll('.autoWebExteriorColorCheckbox');
                const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);

                if (allChecked) {
                    document.getElementById('selectAllWebExteriorColor').checked = true;
                    activeFilters.allExteriorColors = true;
                    activeFilters.exteriorColors.clear();
                }

                updateActiveFiltersDisplay();
                applyFilters();
            });
        });

        // Handle "All" checkbox for Exterior Colors
        document.getElementById('selectAllWebExteriorColor').addEventListener('change', function() {
            const isChecked = this.checked;
            activeFilters.allExteriorColors = isChecked;

            document.querySelectorAll('.autoWebExteriorColorCheckbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });

            if (isChecked) {
                activeFilters.exteriorColors.clear();
            }

            updateActiveFiltersDisplay();
            applyFilters();
        });

        // Handle Interior Color checkboxes
        document.querySelectorAll('.autoWebInteriorColorCheckbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                document.getElementById('selectAllWebInteriorColor').checked = false;
                activeFilters.allInteriorColors = false;

                if (this.checked) {
                    activeFilters.interiorColors.add(this.value);
                } else {
                    activeFilters.interiorColors.delete(this.value);
                }

                const allCheckboxes = document.querySelectorAll('.autoWebInteriorColorCheckbox');
                const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);

                if (allChecked) {
                    document.getElementById('selectAllWebInteriorColor').checked = true;
                    activeFilters.allInteriorColors = true;
                    activeFilters.interiorColors.clear();
                }

                updateActiveFiltersDisplay();
                applyFilters();
            });
        });

        // Handle "All" checkbox for Interior Colors
        document.getElementById('selectAllWebInteriorColor').addEventListener('change', function() {
            const isChecked = this.checked;
            activeFilters.allInteriorColors = isChecked;

            document.querySelectorAll('.autoWebInteriorColorCheckbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });

            if (isChecked) {
                activeFilters.interiorColors.clear();
            }

            updateActiveFiltersDisplay();
            applyFilters();
        });

        // Handle Drive Train checkboxes
        document.querySelectorAll('.autoWebDriveTrainCheckbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                document.getElementById('selectAllWebDriveTrain').checked = false;
                activeFilters.allDriveTrains = false;

                if (this.checked) {
                    activeFilters.driveTrains.add(this.value);
                } else {
                    activeFilters.driveTrains.delete(this.value);
                }

                const allCheckboxes = document.querySelectorAll('.autoWebDriveTrainCheckbox');
                const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);

                if (allChecked) {
                    document.getElementById('selectAllWebDriveTrain').checked = true;
                    activeFilters.allDriveTrains = true;
                    activeFilters.driveTrains.clear();
                }

                updateActiveFiltersDisplay();
                applyFilters();
            });
        });

        // Handle "All" checkbox for Drive Trains
        document.getElementById('selectAllWebDriveTrain').addEventListener('change', function() {
            const isChecked = this.checked;
            activeFilters.allDriveTrains = isChecked;

            document.querySelectorAll('.autoWebDriveTrainCheckbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });

            if (isChecked) {
                activeFilters.driveTrains.clear();
            }

            updateActiveFiltersDisplay();
            applyFilters();
        });

        // Handle Fuel Type checkboxes
        document.querySelectorAll('.autoWebFuelCheckbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                document.getElementById('selectAllFuel').checked = false;
                activeFilters.allFuelTypes = false;

                if (this.checked) {
                    activeFilters.fuelTypes.add(this.value);
                } else {
                    activeFilters.fuelTypes.delete(this.value);
                }

                const allCheckboxes = document.querySelectorAll('.autoWebFuelCheckbox');
                const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);

                if (allChecked) {
                    document.getElementById('selectAllFuel').checked = true;
                    activeFilters.allFuelTypes = true;
                    activeFilters.fuelTypes.clear();
                }

                updateActiveFiltersDisplay();
                applyFilters();
            });
        });

        // Handle "All" checkbox for Fuel Types
        document.getElementById('selectAllFuel').addEventListener('change', function() {
            const isChecked = this.checked;
            activeFilters.allFuelTypes = isChecked;

            document.querySelectorAll('.autoWebFuelCheckbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });

            if (isChecked) {
                activeFilters.fuelTypes.clear();
            }

            updateActiveFiltersDisplay();
            applyFilters();
        });

        // Clear all filters
        document.getElementById('clearAllFiltersBtn').addEventListener('click', function() {
            // Clear all filters
            activeFilters.search = '';
            activeFilters.distance = '75';
            activeFilters.zip = '';
            activeFilters.conditions.clear();
            activeFilters.allConditions = true;
            activeFilters.make = '';
            activeFilters.model = '';
            activeFilters.bodyTypes.clear();
            activeFilters.exteriorColors.clear();
            activeFilters.allExteriorColors = true;
            activeFilters.interiorColors.clear();
            activeFilters.allInteriorColors = true;
            activeFilters.driveTrains.clear();
            activeFilters.allDriveTrains = true;
            activeFilters.fuelTypes.clear();
            activeFilters.allFuelTypes = true;

            // Reset form elements
            document.getElementById('web_search_any').value = '';
            document.getElementById('web_radios').value = '75';
            document.getElementById('web_location').value = '';
            document.getElementById('selectAllWebConditionCheckbox').checked = true;
            // document.querySelectorAll('.autoWebConditionCheckbox').forEach(cb => cb.checked = true);
            document.getElementById('webMakeFilterMakeInput').value = '';
            document.getElementById('webModelFilterInput').value = '';
            document.querySelectorAll('.web_body-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.querySelectorAll('.custom-col').forEach(col => {
                col.classList.remove('active');
            });
            document.getElementById('selectAllWebExteriorColor').checked = true;
            // document.querySelectorAll('.autoWebExteriorColorCheckbox').forEach(cb => cb.checked = true);
            document.getElementById('selectAllWebInteriorColor').checked = true;
            // document.querySelectorAll('.autoWebInteriorColorCheckbox').forEach(cb => cb.checked = true);
            document.getElementById('selectAllWebDriveTrain').checked = true;
            // document.querySelectorAll('.autoWebDriveTrainCheckbox').forEach(cb => cb.checked = true);
            document.getElementById('selectAllFuel').checked = true;
            // document.querySelectorAll('.autoWebFuelCheckbox').forEach(cb => cb.checked = true);

            updateActiveFiltersDisplay();
            applyFilters();
        });

        // Initialize
        initializeFromUrl();
    });


    // exterior
    document.getElementById('selectAllWebExteriorColor').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.autoWebExteriorColorCheckbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // When any color checkbox is clicked, uncheck "All" if not all are selected
    document.querySelectorAll('.autoWebExteriorColorCheckbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allCheckbox = document.getElementById('selectAllWebExteriorColor');
            const allChecked = [...document.querySelectorAll('.autoWebExteriorColorCheckbox')]
                .every(c => c.checked);

            allCheckbox.checked = allChecked;
        });
    });
</script>


<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
    // Track active filters
    const activeFilters = {
        conditions: new Set(),
        search: '',
        distance: '75', // Default value
        zip: ''
    };

    // Initialize from URL parameters if needed
    function initializeFromUrl() {
        const params = new URLSearchParams(window.location.search);
        
        // Search
        if (params.has('search')) {
            activeFilters.search = params.get('search');
            document.getElementById('web_search_any').value = activeFilters.search;
        }
        
        // Distance
        if (params.has('distance')) {
            activeFilters.distance = params.get('distance');
            document.getElementById('web_radios').value = activeFilters.distance;
        }
        
        // Zip
        if (params.has('zip')) {
            activeFilters.zip = params.get('zip');
            document.getElementById('web_location').value = activeFilters.zip;
        }
        
        // Conditions
        if (params.has('conditions')) {
            const conditions = params.get('conditions').split(',');
            conditions.forEach(condition => {
                activeFilters.conditions.add(condition);
                const checkbox = document.querySelector(`.autoWebConditionCheckbox[value="${condition}"]`);
                if (checkbox) checkbox.checked = true;
            });
        }
        
        updateActiveFiltersDisplay();
    }

    // Update the visible filter chips
    function updateActiveFiltersDisplay() {
        const container = document.getElementById('activeFiltersContainer');
        container.innerHTML = '';
        
        // Search filter
        if (activeFilters.search) {
            addFilterChip('Search', activeFilters.search, `"${activeFilters.search}"`);
        }
        
        // Distance filter (if not default)
        if (activeFilters.distance && activeFilters.distance !== '75') {
            addFilterChip('Distance', activeFilters.distance, `${activeFilters.distance} miles`);
        }
        
        // Zip filter
        if (activeFilters.zip) {
            addFilterChip('Zip', activeFilters.zip, activeFilters.zip);
        }
        
        // Condition filters
        activeFilters.conditions.forEach(condition => {
            addFilterChip('Condition', condition, condition);
        });
    }

    // Add a filter chip to the display
    function addFilterChip(type, value, displayText) {
        const container = document.getElementById('activeFiltersContainer');
        const chip = document.createElement('div');
        chip.className = 'filter-chip me-2 mb-2';
        chip.innerHTML = `
            <span>${displayText}</span>
            <button class="filter-close" data-filter-type="${type.toLowerCase()}" data-filter-value="${value}">&times;</button>
        `;
        container.appendChild(chip);
        
        // Add remove handler
        chip.querySelector('.filter-close').addEventListener('click', function() {
            removeFilter(type.toLowerCase(), value);
        });
    }

    // Remove a filter
    function removeFilter(type, value) {
        switch(type) {
            case 'search':
                activeFilters.search = '';
                document.getElementById('web_search_any').value = '';
                break;
                
            case 'distance':
                activeFilters.distance = '75';
                document.getElementById('web_radios').value = '75';
                break;
                
            case 'zip':
                activeFilters.zip = '';
                document.getElementById('web_location').value = '';
                break;
                
            case 'condition':
                activeFilters.conditions.delete(value);
                document.querySelector(`.autoWebConditionCheckbox[value="${value}"]`).checked = false;
                break;
        }
        
        updateActiveFiltersDisplay();
        applyFilters();
    }

    // Apply all filters (update URL or submit form)
    function applyFilters() {
        const params = new URLSearchParams();
        
        if (activeFilters.search) params.set('search', activeFilters.search);
        if (activeFilters.distance && activeFilters.distance !== '75') params.set('distance', activeFilters.distance);
        if (activeFilters.zip) params.set('zip', activeFilters.zip);
        if (activeFilters.conditions.size > 0) params.set('conditions', Array.from(activeFilters.conditions).join(','));
        
        // Update URL without reload (or submit form if preferred)
        const newUrl = window.location.pathname + (params.toString() ? `?${params.toString()}` : '');
        window.history.pushState({}, '', newUrl);
        
        // Here you would typically also trigger your data loading function
        console.log('Filters applied:', activeFilters);
        // loadFilteredData();
    }

    // Event listeners
    document.getElementById('webSearchBtn').addEventListener('click', function() {
        activeFilters.search = document.getElementById('web_search_any').value.trim();
        updateActiveFiltersDisplay();
        applyFilters();
    });

    document.getElementById('web_radios').addEventListener('change', function() {
        activeFilters.distance = this.value;
        updateActiveFiltersDisplay();
        applyFilters();
    });

    document.getElementById('web_location').addEventListener('change', function() {
        activeFilters.zip = this.value.trim();
        updateActiveFiltersDisplay();
        applyFilters();
    });

    document.querySelectorAll('.autoWebConditionCheckbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                activeFilters.conditions.add(this.value);
            } else {
                activeFilters.conditions.delete(this.value);
            }
            updateActiveFiltersDisplay();
            applyFilters();
        });
    });

    document.getElementById('selectAllWebConditionCheckbox').addEventListener('change', function() {
        document.querySelectorAll('.autoWebConditionCheckbox').forEach(checkbox => {
            checkbox.checked = this.checked;
            if (this.checked) {
                activeFilters.conditions.add(checkbox.value);
            } else {
                activeFilters.conditions.delete(checkbox.value);
            }
        });
        updateActiveFiltersDisplay();
        applyFilters();
    });

    document.getElementById('clearAllFiltersBtn').addEventListener('click', function() {
        // Clear all filters
        activeFilters.search = '';
        activeFilters.distance = '75';
        activeFilters.zip = '';
        activeFilters.conditions.clear();
        
        // Reset form elements
        document.getElementById('web_search_any').value = '';
        document.getElementById('web_radios').value = '75';
        document.getElementById('web_location').value = '';
        document.querySelectorAll('.autoWebConditionCheckbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        document.getElementById('selectAllWebConditionCheckbox').checked = true;
        
        updateActiveFiltersDisplay();
        applyFilters();
    });

    // Initialize
    initializeFromUrl();
});
</script> -->