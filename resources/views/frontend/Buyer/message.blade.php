@extends('frontend.website.layout.app')
@section('meta_description', app('globalSeo')['description'])
@section('meta_keyword', app('globalSeo')['keyword'])
@section('title', 'Messages | ' . app('globalSeo')['name'])
@section('gtm')
    {!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])
@section('og_title', app('globalSeo')['og_title'])
@section('og_description', app('globalSeo')['og_description'])
@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])
@section('twitter_title', app('globalSeo')['twitter_title'])
@section('twitter_description', app('globalSeo')['twitter_description'])
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])
@section('content')
    <div class="container">
        <div style="margin-top:150px; margin-bottom:50px" class="row mess-con">
            <!-- Modal -->
            <div class="modal fade " id="XModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5>Message with admin</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div style="background-image: url('/frontend/assets/images/w.jpg');"
                                class="message-content-modal p-4">
                                <div class="message-details">
                                    <div class="message_all"
                                        style="width: 100%; height: 500px; overflow-y: auto; padding-top:7px"
                                        class="pt-3">
                                    </div>
                                    <div class="chat-form" style="margin-bottom: 20px;">
                                        <form id="message_form" role="form" action="{{ route('buyer.byermessage.add') }}"
                                            class="form-inline" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group">
                                                <input name="message" id="modal_mess" placeholder="Type a message here..."
                                                    class="form-control mess-type" type="text">
                                                <input name="lead_id" id="modal_lead_id" value="" class="form-control"
                                                    type="hidden">
                                                <input name="receiver_id" id="modal_receiver_id" value=""
                                                    class="form-control" type="hidden">
                                            </div>
                                            <button class="btn btn-info ms-2 mess-send-btn" type="submit">Send</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message 1 -->
            <div style="margin:0 auto" class="row">
                <div style="margin-top:-15px" class="col-sm-12 message-load">
                    <div class="card p-0 message-item">
                        <div style="background:white" class="card-header">
                            <span style="font-size:17px" class="">My Message</span>
                        </div>
                        <div class="card-body p-0">
                            <section class="section-padding  gray">
                                <div class="row p-0 m-0">
                                    <div style="background:rgb(31, 31, 31); height:600px; overflow-y:scroll; "
                                        class="col-md-3 col-lg-3 message-person col-sm-12 col-xs-12">
                                        <div class="message-inbox">
                                            <div class="message-history mt-3">
                                                @forelse ($lead_messages as $message)
                                                    @if ($message->is_seen == '0')
                                                        <div style="margin-bottom:8px; border-radius:2px; background:rgb(222, 238, 234); padding:5px; position:relative"
                                                            class="message-grid">
                                                            <div class="badge-mess"></div>
                                                            <a href="#" data-sender_id="{{ $message->sender_id }}"
                                                                data-lead_id="{{ $message->lead_id }}"
                                                                class="messageSelect">
                                                                @php
                                                                    // Check if lead and inventory exist before accessing their properties

                                                                    if ($message->lead && $message->lead->mainInventory->additionalInventory) {
                                                                        $image_obj =
                                                                            $message->lead->mainInventory->additionalInventory->local_img_url;
                                                                        $image_splice = explode(',', $image_obj);
                                                                        $image = str_replace(
                                                                            ['[', "'"],
                                                                            '',
                                                                            $image_splice[0],
                                                                        );
                                                                    } else {
                                                                        $image = 'default-image.png'; // Use a default image or handle the null case
                                                                    }
                                                                @endphp
                                                                <div style="display:flex">
                                                                    <div style="" class="image">
                                                                        <img style="margin-top:5px; height:50px"
                                                                            src="{{ asset($image) }}"
                                                                            alt="{{ 'bestdreamcar.com '.$message->lead->mainInventory->title . ' images '}}" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';">
                                                                    </div>
                                                                    <div class="user-name">
                                                                        <div class="author ms-2 mt-2">
                                                                            <p style="width:85%; color:black; font-size:8px"
                                                                                id="title">
                                                                                {{ $message->lead->inventory->title ?? 'No Title' }}<br>#{{ $message->lead->inventory->stock ?? 'No Stock' }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div style="margin-bottom:8px; border-radius:2px; background:rgb(222, 238, 234); padding:5px"
                                                            class="message-grid">
                                                            <a href="#" data-sender_id="{{ $message->sender_id }}"
                                                                data-lead_id="{{ $message->lead_id }}"
                                                                class="messageSelect">
                                                                @php
                                                                    // Check if lead and inventory exist before accessing their properties
                                                                    if ($message->lead && $message->lead->mainInventory->additionalInventory) {
                                                                        $image_obj = $message->lead->mainInventory->additionalInventory->local_img_url;
                                                                        $image_splice = explode(',', $image_obj);
                                                                        $image = str_replace(
                                                                            ['[', "'"],
                                                                            '',
                                                                            $image_splice[0],
                                                                        );
                                                                    } else {
                                                                        $image = 'NotFound.png'; // Use a default image or handle the null case
                                                                    }
                                                                @endphp

                                                                <div style="display:flex">
                                                                    <div style="" class="image">
                                                                        <img style="margin-top:5px; height:50px"
                                                                            src="{{ asset($image)  }}"
                                                                            alt="{{ 'bestdreamcar.com '.$message->lead->mainInventory->title . ' images '}}" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';">
                                                                    </div>
                                                                    <div class="user-name">
                                                                        <div class="author ms-2 mt-2">
                                                                            <p style="width:85%; color:black; font-size:8px"
                                                                                id="title">
                                                                                {{ $message->lead->mainInventory->title ?? 'No Title' }}<br>#{{ $message->lead->mainInventory->stock ?? 'No Stock' }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    @endif
                                                @empty
                                                <p class="text-white text-center mt-4">No Listing Here<p>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                    <div style="background-image: url('/frontend/assets/images/w.jpg');"
                                        class="col-md-9 col-lg-9 clearfix col-sm-12 col-xs-12 message-content p-4">
                                        <h4 id="chose">Select a listing to show messages</h4>
                                        <div class="message-details">
                                            <div class="message_all"
                                                style="width: 100%; height: 500px; overflow-y: auto; padding-top:8px"
                                                class="pt-3"></div>
                                            <div class="chat-form" style="margin-bottom: 20px;">
                                                <form id="message_form" role="form"
                                                    action="{{ route('buyer.byermessage.add') }}" class="form-inline"
                                                    method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-group">
                                                        <input name="message" id="mess"
                                                            placeholder="Type a message here..."
                                                            class="form-control mess-type" type="text">
                                                        <input name="lead_id" id="lead_id" class="form-control"
                                                            type="hidden">
                                                        <input name="receiver_id" id="receiver_id" class="form-control"
                                                            type="hidden">
                                                    </div>
                                                    <button style="padding-left:38px; padding-right:38px; font-size:18px;" class="btn btn-info ms-2 mess-send-btn"
                                                        type="submit">Send</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Add more messages as needed -->
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // When the message is selected (clicked)
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
                    loadDataAndDisplay(res);

                    // Check viewport width and toggle modal display
                    if (window.innerWidth <= 767) {
                        $('#XModal').modal('show'); // Show modal on smaller screens
                    } else {
                        $('#XModal').modal('hide'); // Hide modal on larger screens
                    }
                }
            });
        });

        // Function to load and display data in the message container
        function loadDataAndDisplay(res) {
            $('.message_all').empty();

            $('#lead_id').val(res.data[0].lead_id);
            $('#receiver_id').val(res.data[0].receiver_id);
            $('#modal_lead_id').val(res.data[0].lead_id);
            $('#modal_receiver_id').val(res.data[0].receiver_id);

            if (res.status === 'success' && res.data.length > 0) {
                res.data.forEach(function(message) {
                    var formattedTime = new Intl.DateTimeFormat('en-US', {
                        day: 'numeric',
                        month: 'short',
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true
                    }).format(new Date(message.created_at));

                    if (message.sender_id === {{ Auth::id() }}) {
                        // Append sender's messages
                        $('.message_all').append(
                            '<div style="padding-bottom:10px !important">' +
                            '<p><span class="date-date" style="margin-left:300px;float:left;margin-top:-25px;color:white;margin-bottom:6px;">' +
                            formattedTime + '</span></p>' +
                            '<p class="receive" style="color:black;background-color:#F0FFF0;padding:10px;border-radius:3px;margin-left:300px;">' +
                            message.message + '</p></div>'
                        );
                        $('#chose').hide();
                    } else {
                        // Append receiver's messages
                        $('.message_all').append(
                            '<div class="first-message" style="padding-bottom:10px !important">' +
                            '<p><span class="date-america" style="float:left;margin-top:-25px;color:white;margin-bottom:6px;">' +
                            formattedTime + '</span></p>' +
                            '<p class="sender" style="color:black;background-color:#B0E0E6;padding:10px; border-radius:3px; margin-right:260px;">' +
                            message.message + '</p></div>'
                        );
                    }
                });
            }
        }

        // Message form submission
        $(document).on('submit', '#message_form', function(e) {
            e.preventDefault();

            let leadId = $('#lead_id').val() || $('#modal_lead_id').val();
            let message = $('#mess').val() || $('#modal_mess').val();

            if (!leadId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Error',
                    text: 'Select a listing to send your message',
                });
            } else if (!message) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Error',
                    text: 'Must type a message',
                });
            } else {
                var formData = new FormData($(this)[0]);

                $.ajax({
                    processData: false,
                    contentType: false,
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    success: function(res) {
                        var formattedTime = new Intl.DateTimeFormat('en-US', {
                            day: 'numeric',
                            month: 'short',
                            hour: 'numeric',
                            minute: 'numeric',
                            hour12: true
                        }).format(new Date(res.data.created_at));

                        if (res.status === 'success') {
                            toastr.success(res.message);
                            $('#mess, #modal_mess').val('');

                            if (res.data.sender_id === {{ Auth::id() }}) {
                                $('.message_all').append(
                                    '<div style="padding-bottom:10px !important">' +
                                    '<p><span class="date-date" style="margin-left:300px;float:left;margin-top:-25px;color:white;margin-bottom:6px;">' +
                                    formattedTime + '</span></p>' +
                                    '<p class="receive" style="color:black;background-color:#F0FFF0;padding:10px;border-radius:3px;margin-left:300px;">' +
                                    res.data.message + '</p></div>'
                                );
                                $('#chose').hide();
                            }

                            $('#message').val('');
                        } else if (res.errors) {
                            // Dynamically display validation errors
                            $.each(res.errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;

                        // Display validation errors
                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]);
                        });
                    }
                });
            }
        });
    });
</script>

    @include('frontend.reapted_js')
@endpush
