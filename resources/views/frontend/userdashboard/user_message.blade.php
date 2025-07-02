@extends('frontend.userdashboard.master')
@section('content-user')
    <style>
        /* Add your own styles here */
        .message-container {
            padding-top: 15px !important;
        }

        .message-item {
            margin-bottom: 20px;
        }

        .card {
            border: none;
        }

        .message-sender {
            font-size: 18px;
            font-weight: bold;
        }

        .message-content {
            font-size: 16px;
        }

        .message_all::-webkit-scrollbar {
            width: 10px;
        }

        .badge-mess {
            position: absolute;
            width: 10px;
            height: 10px;
            line-height: 10px;
            border-radius: 50%;
            animation-duration: 1.4s;
            animation-iteration-count: infinite;
            animation-name: pulse;
            background-color: rgb(238, 23, 23);
            top: 5px;
            right: 5px
        }

        @-webkit-keyframes pulse {
            0% {
                -webkit-box-shadow: 0 0 0 0 rgba(223, 8, 8, 0.4)
            }

            70% {
                -webkit-box-shadow: 0 0 0 10px rgba(204, 169, 44, 0);
            }

            100% {
                -webkit-box-shadow: 0 0 0 0 rgba(204, 169, 44, 0);
            }
        }

        @keyframes pulse {
            0% {
                -moz-box-shadow: 0 0 0 0 rgba(223, 5, 5, 0.4);
                box-shadow: 0 0 0 0 rgba(19, 197, 78, 0.4);
            }

            70% {
                -moz-box-shadow: 0 0 0 10px rgba(204, 169, 44, 0);
                box-shadow: 0 0 0 10px rgba(204, 169, 44, 0);
            }

            100% {
                -moz-box-shadow: 0 0 0 0 rgba(204, 169, 44, 0);
                box-shadow: 0 0 0 0 rgba(204, 169, 44, 0);
            }
        }
    </style>

   
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('click', '.messageSelect', function(e) {
                e.preventDefault();
                let sender_id = $(this).data('sender_id');
                let lead_id = $(this).data('lead_id');

                $.ajax({
                    url: "{{ route('buyer.message.collect') }}",
                    method: "post",
                    data: {
                        sender_id: sender_id,
                        lead_id: lead_id
                    },
                    success: function(res) {


                        // Check if viewport width is 1000px or less
                        // Check if viewport width is 1000px or less
                        if (window.innerWidth <= 1000) {
                            // Show the modal
                            $('#XModal').modal('show');

                            // Load and display data
                            loadDataAndDisplay(res);
                        }
                        if (window.innerWidth > 1000) {
                            // Hide the modal


                            // Load and display data
                            loadDataAndDisplay(res);
                        }

                    }
                });
            });

            // Function to load and display data
            function loadDataAndDisplay(res) {
                console.log(res);



                $('.message_all').empty();

                $('#lead_id').val(res.data[0].lead_id);
                $('#receiver_id').val(res.data[0].receiver_id);
                $('#modal_lead_id').val(res.data[0].lead_id);
                $('#modal_receiver_id').val(res.data[0].receiver_id);

                if (res.status == 'success' && res.data.length > 0) {

                    res.data.forEach(function(message) {


                        var formattedTime = new Intl.DateTimeFormat('en-US', {
                            day: 'numeric',
                            month: 'short',
                            hour: 'numeric',
                            minute: 'numeric',
                            hour12: true
                        }).format(new Date(message.created_at));

                        if (message.sender_id == {{ Auth::id() }}) {

                            $('.message_all').append(
                                '<div style="padding-bottom:10px !important"><p><span class="date-date" style="margin-left:300px;float:left;margin-top:-25px;color:white;margin-bottom:6px;">' +
                                formattedTime +
                                '</span></p><p class="receive" style="color:black;background-color:#F0FFF0;padding:10px;border-radius:3px;margin-left:300px;">' +
                                message.message + '</p></div>');



                            $('#chose').css('display', 'none');
                        } else {

                            $('.message_all').append(
                                '<div class="first-message" style="padding-bottom:10px !important"><p><span class="date-america" style="float:left;margin-top:-25px;color:white;margin-bottom:6px;">' +
                                formattedTime +
                                '</span></p><p class="sender" style="color:black;background-color:#B0E0E6;padding:10px; border-radius:3px; margin-right:260px">' +
                                message.message + '</p></div>');

                        }
                    });
                }
            }



            $(document).on('submit', '#message_form', function(e) {
                e.preventDefault();

                let id = $('#lead_id').val() || $('#modal_lead_id').val();
                let message = $('#mess').val() || $('#modal_mess').val();

                if (id === '') {
                    // Swal.fire({
                    //     icon: 'warning',
                    //     title: 'Warning!',
                    //     text: 'Select a listing to send your messag',
                    //     showConfirmButton: false,
                    //     timer: 1500
                    // });
                    alert('Select a listing to send your message');
                } else if (message === '') {
                    alert('Must type a message');
                } else {
                    var formData = new FormData($(this)[0]);

                    $.ajax({
                        processData: false,
                        contentType: false,
                        url: $(this).attr('action'),
                        type: $(this).attr('method'),
                        data: formData,
                        success: function(res) {
                            console.log(res);

                            var formattedTime = new Intl.DateTimeFormat('en-US', {
                                day: 'numeric',
                                month: 'short',
                                hour: 'numeric',
                                minute: 'numeric',
                                hour12: true
                            }).format(new Date(res.data.created_at));

                            if (res.status === 'success') {
                                toastr.success(res.message);
                                $('#mess').val('');
                                $('#modal_mess').val('');

                                if (res.data.sender_id === {{ Auth::id() }}) {

                                    $('.message_all').append(
                                        '<div style="padding-bottom:10px !important"><p><span class="date-date" style="margin-left:300px;float:left;margin-top:-25px;color:white;margin-bottom:6px;">' +
                                        formattedTime +
                                        '</span></p><p class="receive" style="color:black;background-color:#F0FFF0;padding:10px;border-radius:3px;margin-left:300px;">' +
                                        res.data.message + '</p></div>');
                                    $('#chose').css('display', 'none');
                                }

                                $('#message').val('');
                            } else if (res.errors) {
                                // Display validation errors dynamically
                                $.each(res.errors, function(key, value) {
                                    $('#' + key + '-error').html(value[0]);
                                });
                            }
                        },
                        error: function(xhr) {
                            // Handle error response
                            var errors = xhr.responseJSON.errors;

                            // Display validation errors dynamically
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').text(value[0]);
                            });
                        }
                    });
                }
            });







        });
    </script>
@endpush
