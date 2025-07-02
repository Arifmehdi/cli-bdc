<script>
    $('#userUpdate').submit(function(e) {
    e.preventDefault();

    // Create a FormData object
    var formData = new FormData(this); // Use 'this' to reference the form element

    $.ajax({
        url: $(this).attr("action"),
        method: $(this).attr("method"),
        data: formData,
        contentType: false, // Prevent jQuery from overriding content type
        processData: false, // Prevent jQuery from processing the data
        success: function(response) {
            if (response.status == "success") {
                toastr.success(response.message);
                $('#profile_image').val('');
            }
        },
        error: function(xhr) {
            // Handle error response
            var errors = xhr.responseJSON.errors;
            console.log(errors);
            // Display validation errors
            $.each(errors, function(key, value) {
                // Display the error messages
                toastr.error(value[0]);
            });
        }
    });
});
</script>
