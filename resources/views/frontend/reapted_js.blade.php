<script>
    function setModalId($id) {
        $('#inv_id').val($id);
    }

    $(document).ready(function() {
        $(document).on('click', '#quick', function(e) {

            let id = $(this).data('id');
            $('#all_id').val(id);
            var target = $('#hide-action-' + id);

            if (target.is(':visible')) {
                target.hide();
            } else {
                $('.hide-action').hide(); // Hide all action sections first
                target.show(); // Show the specific action section
            }
            // $('.hide-action').toggle();
        });
        $(document).on('click', '#view-data', function(e) {
            let id = $('#all_id').val();

            $.ajax({
                url: `{{ route('quick.show', ':id') }}`.replace(':id', id),
                method: 'GET',
                success: function(response) {
                    console.log(response);

                    $('#quick_data').html('');
                    $('#quick_footer').html('');
                    if (response.status == 'success') {
                        // Clear Swiper wrapper
                        $('.quick-swiper').html('');

                        // Append image slides to Swiper
                        response.image_urls.forEach((url, index) => {
                        const sanitizedUrl = url.replace(/'/g, ""); // Remove any extra single quotes
                        const notFoundImage = "{{ asset('frontend/NotFound.png') }}"; // Path to the "Not Found" image

                        $('.quick-swiper').append(`
                            <div class="swiper-slide">
                                <img
                                    style="width: auto; height: auto; object-fit: contain;"
                                    class="quick-view-image"
                                    src="${sanitizedUrl}"
                                    alt="Image ${index + 1}"
                                    onerror="this.onerror=null; this.src='${notFoundImage}';"
                                >
                            </div>
                        `);
                                            });


                        // Update Swiper after adding slides


                        // Append other details and footer
                        var newRow = $('<div class="row"></div>');
                        if (response.inventory.make) {
                            newRow.append(`
                        <div style="font-size:16px" class="mt-1 mb-2 col-lg-6 col-sm-12 view-append-data">
                            <img title="Make" style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:5px" class="me-2" src="{{ asset('frontend/assets/images/car.png') }}" alt="Used cars for sale Best Dream car modal make image"/>
                            Make: ${response.inventory.make}
                        </div>
                    `);
                        }


                        if (response.inventory.model) {
                            newRow.append(`
                        <div style="font-size:16px" class="mt-1 mb-2 col-lg-6 col-sm-12 view-append-data">
                            <img title="Model" style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:5px" class="me-2" src="{{ asset('frontend/assets/images/model.png') }}" alt="Used cars for sale Best Dream car modal model image"/>
                            Model: ${response.inventory.model}
                        </div>
                        `);
                        }

                        if (response.inventory.fuel) {
                            newRow.append(`
                            <div style="font-size:16px" class="mt-1 mb-2 col-lg-6 col-sm-12 view-append-data">
                                <img title="Fuel" style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:5px" class="me-2" src="{{ asset('frontend/assets/images/g.png') }}" alt="Used cars for sale Best Dream car modal fuel image"/>
                                Fuel: ${response.inventory.fuel}
                            </div>
                            `);
                        }
                        if (response.inventory.drive_info) {
                            newRow.append(`
                            <div style="font-size:16px" class="mt-1 mb-2 col-lg-6 col-sm-12 view-append-data">
                                <img title="Drive Info" style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:5px" class="me-2" src="{{ asset('frontend/assets/images/drive.png') }}" alt="Used cars for sale Best Dream car modal drive info image"/>
                               Drivetrain: ${response.inventory.drive_info}
                            </div>
                            `);
                        }

                        if (response.inventory.year) {
                            newRow.append(`
                            <div style="font-size:16px" class="mt-1 mb-2 col-lg-6 col-sm-12 view-append-data">
                                <img title="Year" style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:5px" class="me-2" src="{{ asset('frontend/assets/images/calendar.png') }}" alt="Used cars for sale Best Dream car modal year image"/>
                                Year: ${response.inventory.year}
                            </div>
                            `);
                        }

                        if (response.inventory.mpg_city && response.inventory.mpg_highway) {
                            newRow.append(`
                            <div style="font-size:16px" class="mt-1 mb-2 col-lg-6 col-sm-12 view-append-data">
                                <img title="Mpg City Highway" style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:5px" class="me-2" src="{{ asset('frontend/assets/images/gas.png') }}" alt="Used cars for sale Best Dream car modal gas image"/>
                                ${response.inventory.mpg_city} city / ${response.inventory.mpg_highway} highway
                            </div>
                            `);
                        }

                        if (response.inventory.stock) {
                            newRow.append(`
                            <div style="font-size:16px" class="mt-1 mb-2 col-lg-6 col-sm-12 view-append-data">
                                <img title="Stock" style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:5px" class="me-2" src="{{ asset('frontend/assets/images/stock.png') }}" alt="Used cars for sale Best Dream car modal stock image"/>
                                Stock: ${response.inventory.stock}
                            </div>
                            `);
                        }

                        if (response.inventory.transmission) {
                            newRow.append(`
                            <div style="font-size:16px" class="d-flex mt-1 mb-2 col-lg-6 col-sm-12 view-append-data">
                                <img title="Transmission" style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:5px" class="me-2" src="{{ asset('frontend/assets/images/transmission.png') }}" alt="Used cars for sale Best Dream car modal transmission image"/>
                                <div class="pt-2">
                                   Transmission: ${response.inventory.transmission}
                                </div>

                            </div>
                            `);
                        }

                        if (response.inventory.exterior_color) {
                            newRow.append(`
                            <div style="font-size:16px" class="d-flex mt-1 mb-2 col-lg-6 col-sm-12 view-append-data">
                                <img title="Exterior Color" style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:5px" class="me-2" src="{{ asset('frontend/assets/images/art.png') }}" alt="Used cars for sale Best Dream car modal exterior color image"/>
                                <div class="pt-2">Exterior: ${response.inventory.exterior_color}</div>

                            </div>
                            `);
                        }
                        if (response.inventory.created_at) {
                            // Convert created_at to a JavaScript Date object
                            let createdAt = new Date(response.inventory.created_at);
                            let today = new Date();
                            
                            // Calculate the difference in days
                            let timeDiff = today - createdAt;
                            let daysListed = Math.floor(timeDiff / (1000 * 60 * 60 * 24)); // Convert milliseconds to days
                            
                            newRow.append(`
                                <div style="font-size:16px" class="d-flex mt-1 mb-2 col-lg-6 col-sm-12 view-append-data">
                                    <img title="Exterior Color" style="width:40px; height:40px; border:0.5px solid gray; border-radius:50%; padding:5px" class="me-2" src="{{ asset('frontend/assets/images/add.png') }}" alt="Used cars for sale Best Dream car modal exterior color image"/>
                                    <div class="pt-2">Added: ${daysListed} days listed ago</div>
                                </div>
                            `);
                        }



                        // Continue appending other details...

                        $('#quick_data').append(newRow);

                        var vinStringReplace = response.vin_string_replace;
                        var routeString = response.route_string;

                        var url = "{{ route('auto.details', ['vin' => 'vinStringReplace', 'param' => 'routeString']) }}".replace('vinStringReplace', encodeURIComponent(vinStringReplace)).replace('routeString', encodeURIComponent(routeString));

                        var $button = $('<a style="background:none; color:rgb(18, 176, 197); padding:7px 20px; display:flex; align-items:center; justify-content:center; font-size:14px; border-radius:7px; text-decoration:none; margin-top:-8px"></a>')
                            .attr('href', url)
                            .addClass('btn')
                            .html('View full listing <i class="fa fa-angle-right ms-2" style="margin-right:8px; font-size:17px"></i> ');

                        $('#quick_footer').html('').append($button).css({
                            'display': 'flex',
                            'justify-content': 'center',
                            'align-items': 'center'
                        });

                        // Show the modal
                        $('#QuickModal').modal('show');
                        initializeQuickSwiper();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });


    // compare all code start

    $(document).ready(function() {



        $(document).on('click', '#compare_listing', function() {


            let id = $('#all_id').val();

            compare_data(id);
        });

        function compare_data(id) {
            $.ajax({
                url: "{{ route('frontend.compare.listing') }}",
                method: "post",
                data: {
                    id: id
                },
                success: function(res) {
                    console.log(res);

                    var limit = res.limit;
                    var count = limit + 1;



                    if (res.status == 'success') {
                        $('#ComModal').modal('show');
                        $('#comIcon').attr('src', '/frontend/assets/images/check.png');
                        $('#addValue').text('You have added ' + count + ' of 3 vehicles')
                    }
                    if (res.status == 'error') {

                        $('#ComModal').modal('show');
                        $('#comIcon').attr('src', '/frontend/assets/images/warning.png');
                        $('#addValue').text(res.message);
                    }
                },
                error: function(res) {

                }
            });
        }



        $(document).on('click', '#compare-collect', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('frontend.compare.listing.collect') }}",
                type: 'post',
                success: function(res) {
                    console.log(res);
                    $('#ComModal').modal('hide');
                    $('#ComDataModal').modal('show');

                    // Clear any existing content in the modal
                    $('#ComDataModal .modal-body').html('');

                    // Create a row to hold the cards
                    var row = $('<div  class="row"></div>');

                    // Iterate over the coms array and generate the HTML for each card
                    res.coms.forEach(function(com) {
                        console.log(com.title);
                        var card = `
                    <div class="mb-1 col-lg-4" data-id="${com.id}">
                        <div class="card">
                            <!-- Card image -->
                            <img height="200px" src="${com.image_path}" class="card-img-top" alt="${com.title}">

                            <!-- Delete link positioned at the top-right corner -->
                            <a href="#"
                               class="top-0 m-2 btn btn-danger btn-sm position-absolute end-0"
                               onclick="event.preventDefault(); deleteItem(${com.id});">
                               <i class="fa fa-trash"></i>
                            </a>

                            <!-- Card body with card details -->
                            <div class="card-body">
                                <h5 class="card-text">${com.price}</h5>
                                <p class="">${com.title}</p>
                            </div>
                        </div>
                    </div>
                `;

                        // Append the card to the row
                        row.append(card);
                    });

                    // Append the row to the modal body
                    $('#ComDataModal .modal-body').append(row);
                }
            });
        });


        // share email code start
        $(document).on('submit', '#shareEmailSubmitBtn', function(e) {
            e.preventDefault();

            var formData = new FormData($(this)[0]);
            $('#share-btn').text('Loading ....');
            var form = this;
            // Set the content of the editor to an empty string
            $.ajax({
                processData: false,
                contentType: false,
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                success: function(res) {
                    console.log(res);

                    if (res.errors) {
                        $.each(res.errors, function(key, value) {
                            $('#' + key + '-error').html(value[0]);
                        });
                        $('#share-btn').text('submit');
                    }

                    if (res.status === 'success') {
                        $('#share-btn').text('Submit');
                        toastr.success(res.message);
                        $('#ShareModal').modal('hide');
                        form.reset();

                    }

                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key + '-error').text(value[0]);
                    });

                }
            });
        });

        // share email code close




    });



    function deleteItem(id) {
        console.log('Delete function called with ID:', id);

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/delete-item/' + id, // URL with item ID
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}' // Include CSRF token if using Laravel
                    },
                    success: function(response) {
                        console.log('AJAX success response:', response);
                        if (response.status === 'success') {
                            // Remove the card from the DOM
                            $('div[data-id="' + id + '"]').remove();

                            Swal.fire(
                                'Deleted!',
                                'Your item has been deleted.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Failed!',
                                'Failed to delete item.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        console.log('AJAX error response');
                        Swal.fire(
                            'Error!',
                            'Error occurred while deleting item.',
                            'error'
                        );
                    }
                });
            }
        });
    }


    // compare all code end

    // start wishlist work

    $(document).on('click', '.wishlist', function() {
        var inventory_id = $(this).data('productid');

        //  return
        var url = "{{ route('update.wishlist') }}";

        $.ajax({
            url: url,
            type: 'post',
            data: {
                inventory_id: inventory_id,

            },

            success: function(response) {
                console.log(response.favourite);
                if (response.action === 'add') {
                    $('a[data-productid=' + inventory_id + ']').html(
                        ` <i class="fa fa-heart" style ="color:red; margin-top:9px"></i>`
                    );
                    toastr.success(response.message);
                } else if (response.action === 'remove') {

                    $('a[data-productid=' + inventory_id + ']').html(
                        `<i class="fa fa fa-heart-o" style="margin-top:9px"></i>`
                    );
                    toastr.error(response.message);
                }
            },
            error: function(error) {
                // Handle error here
            }


        });



    });

    // end wishlist work




    // custom number format function create

    function number_format(number, decimals = 2, dec_point = '.', thousands_sep = ',') {
        number = parseFloat(number).toFixed(decimals);
        var parts = number.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

        return parts[0];
    }
    // custom number format function create End

    // check availabilty code start here

    $(document).on('click', '#check_availability', function() {
        let id = $(this).data('inventory_id');
        let user_id = $(this).data('user_id');

        $('#dealer_id').val(user_id);

        $('#SendLead').trigger('reset');
        $('.is-invalid').removeClass('is-invalid');
        $('.text-danger').text('');



        $.ajax({
            url: "{{ route('get_lead_data') }}",
            type: 'GET',
            data: {
                id: id,
            },
            success: function(res) {
                var id = res.id;
                $('#inventory_id').val(id);

                var title = res.year + ' ' + res.make + ' ' + res.model;
                var price = number_format(res.price);


                $('#w3review').text('I am interested and want to know more about the ' +
                    title +
                    ' Sport Utility, you have listed for $ ' + price +
                    ' on Best Dream car.');

                $('#checkModal').modal('show');
                $('#checkModal').on('shown.bs.modal', function() {
                    refreshCaptcha();
                    $('#checkModal form')[0].reset();
                });
            },
            error: function(err) {
                console.error('Error:', err);
            }
        });
    });

    $('.telephoneInput').inputmask('(999) 999-9999');

    $(document).on('change', '#tradeChecked', function() {
        var isChecked = this.checked;
        if (isChecked == true) {
            $('#Auto_Trade_block_content').css('display', 'block');
        } else {
            $('#Auto_Trade_block_content').css('display', 'none');
        }
    });



    $('#SendLead').submit(function(e) {
        e.preventDefault();

        // Serialize the form data
        var formData = new FormData($(this)[0]);
        $('.Aloading').text('Loading....');

        // Make Ajax request
        $.ajax({

            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#SendLead')[0].reset();
                toastr.success(response.message);
                $('#checkModal').modal('hide');
                $('#first_name').val(null);
                $('#last_name').val(null);
                $('#email').val(null);
                $('#phone').val(null);
                $('.Aloading').text('Send Message');
                $('#first_name_error').html('');
                $('#last_name_error').html('');
                $('#email_error').html('');
                $('#phone_error').html('');
                $('#Amathcaptcha').html('');

            },
            error: function(xhr) {

                $('.Aloading').text('Send Message');
                // Handle error response
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    $('#first_name_error').html(errors.first_name);
                    $('#last_name_error').html(errors.last_name);
                    $('#email_error').html(errors.email);
                    $('#phone_error').html(errors.phone);
                    $('#Amathcaptcha').html(errors.mathcaptcha);
                }
            }
        });
    });

    // check availabilty code End  here
</script>


<script>
    let quickSwiper;

    function initializeQuickSwiper() {
        if (quickSwiper) {
            quickSwiper.destroy(true, true); // Destroy the existing Swiper instance
        }

        // Set the width explicitly before initializing Swiper
        $('.quickSwiper').css('width', '570px');

        quickSwiper = new Swiper(".quickSwiper", {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
    }
</script>
