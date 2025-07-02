<script>
        // Event handler for dropdown change
        $('.dropDown_selector').on('change', function() {
        var dropdownId = $(this).attr('id');
        var selectedValue = $(this).val();
        updateDataShow(dropdownId, selectedValue);
    });

    // Event handler for removing a selected value
    $('#dataShow').on('click', '.remove-value', function() {
        var valueToRemove = $(this).siblings('.dropdown-value').text();
        enableOption(valueToRemove);
        $(this).parent('.selected-value').remove();
        console.log('Value removed:', valueToRemove);
    });

    // Event handler for clear filters button
    $('#clearFilterBtn').on('click', function() {
        $('#dataShow').empty();
        $('.dropDown_selector').val('');
        enableAllOptions();
    });

    function updateDataShow(dropdownId, selectedValue) {
        // Remove previous entry for the same dropdown
        $('#dataShow .selected-value[data-dropdown="' + dropdownId + '"]').remove();

        // Add a new entry to #dataShow
        $('#dataShow').append('<span class="selected-value" data-dropdown="' + dropdownId + '"><span class="dropdown-value">' + selectedValue + '</span><button class="remove-value" data-value="' + selectedValue + '">&times;</button></span>');

        // Disable the selected option in the dropdown
        // $('.dropDown_selector option:selected').prop('disabled', true);

        console.log('Value added:', dropdownId, selectedValue);
    }

    function enableOption(value) {
        // $('.dropDown_selector option[value="' + value + '"]').prop('disabled', false);
    }

    function enableAllOptions() {
        // $('.dropDown_selector option').prop('disabled', false);
    }
    $(document).on('change','#firstFilterMakeInput', function(){
       
    });
</script>