@php
use Illuminate\Support\Facades\Session;
@endphp
@extends('backend.admin.layouts.master')

@section('content')

<style>
    .admin-inventory-image {
        height: 300px;
    }

    #numberInput {
        display: none; /* Hide the number input field initially */
    }

</style>
<style>
    .btn-loading {
        position: relative;
        pointer-events: none;
        opacity: 0.6;
    }

    .btn-loading::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 1em;
        height: 1em;
        border: 2px solid transparent;
        border-top-color: white;
        border-radius: 50%;
        -webkit-animation: spinner 0.6s linear infinite;
        animation: spinner 0.6s linear infinite;
        transform: translate(-50%, -50%);
    }

    @-webkit-keyframes spinner {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes spinner {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>


<!-- file management style start here  -->
<style>
    body {
	font-family : sans-serif;
}

.button {
	background      : #005f95;
	border          : none;
	border-radius   : 3px;
	color           : white;
	display         : inline-block;
	font-size       : 19px;
	font-weight     : bolder;
	letter-spacing  : 0.02em;
	padding         : 10px 20px;
	text-align      : center;
	text-shadow     : 0px 1px 2px rgba(0, 0, 0, 0.75);
	text-decoration : none;
	text-transform  : uppercase;
	transition      : all 0.2s;
}

.btn:hover {
	background : #4499c9;
}

.btn:active {
	background : #49ADE5;
}

input[type="file"] {
	display : none;
}

#file-drag {
	border        : 2px dashed #555;
	border-radius : 7px;
	color         : #555;
	cursor        : pointer;
	display       : block;
	font-weight   : bold;
	margin        : 1em 0;
	padding       : 3em;
	text-align    : center;
	transition    : background 0.3s, color 0.3s;
}

#file-drag:hover {
	background : #ddd;
}

#file-drag:hover,
#file-drag.hover {
	border-color : #3070A5;
	border-style : solid;
	box-shadow   : inset 0 3px 4px #888;
	color        : #3070A5;
}

#file-progress {
	display : none;
	margin  : 1em auto;
	width   : 100%;
}

#file-upload-btn {
	margin : auto;
}

#file-upload-btn:hover {
	background : #4499c9;
}

#file-upload-form {
	margin : auto;
	width  : 40%;
}

progress {
	appearance    : none;
	background    : #eee;
	border        : none;
	border-radius : 3px;
	box-shadow    : 0 2px 5px rgba(0, 0, 0, 0.25) inset;
	height        : 30px;
}

progress[value]::-webkit-progress-value {
	background :
		-webkit-linear-gradient(-45deg,
			transparent 33%,
			rgba(0, 0, 0, .2) 33%,
			rgba(0,0, 0, .2) 66%,
			transparent 66%),
		-webkit-linear-gradient(right,
			#005f95,
			#07294d);
	background :
		linear-gradient(-45deg,
			transparent 33%,
			rgba(0, 0, 0, .2) 33%,
			rgba(0,0, 0, .2) 66%,
			transparent 66%),
		linear-gradient(right,
			#005f95,
			#07294d);
	background-size : 60px 30px, 100% 100%, 100% 100%;
	border-radius   : 3px;
}

progress[value]::-moz-progress-bar {
	background :
	-moz-linear-gradient(-45deg,
		transparent 33%,
		rgba(0, 0, 0, .2) 33%,
		rgba(0,0, 0, .2) 66%,
		transparent 66%),
	-moz-linear-gradient(right,
		#005f95,
		#07294d);
	background :
		linear-gradient(-45deg,
			transparent 33%,
			rgba(0, 0, 0, .2) 33%,
			rgba(0,0, 0, .2) 66%,
			transparent 66%),
		linear-gradient(right,
			#005f95,
			#07294d);
	background-size : 60px 30px, 100% 100%, 100% 100%;
	border-radius   : 3px;
}

ul {
	list-style-type : none;
	margin          : 0;
	padding         : 0;
}
</style>

<div class="row">
    <div class="col-md-12">
        <!-- Main content -->
        <section class="content row">


            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add Zip Import</h3><br>
                        <hr>
                        <!-- file management html form start here  -->
                        <form id="file-upload-form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                            <input id="file-upload" type="file" name="fileUpload" />
                            <label for="file-upload" id="file-drag">
                                Select a file to upload
                                <br />OR
                                <br />Drag a file into this box

                                <br /><br /><span id="file-upload-btn" class="button">Add ZIP file</span>
                            </label>

                            <progress id="file-progress" value="0">
                                <span>0</span>%
                            </progress>

                            <output for="file-upload" id="messages"></output>

                            <!-- Import Button will be shown after upload -->
                            <button type="button" id="import-btn" style="display:none;" class="button">Import</button>
                        </form>

                        <?php
                            $fn = (isset($_SERVER['HTTP_X_FILE_NAME']) ? $_SERVER['HTTP_X_FILE_NAME'] : false);
                            $targetDir = 'tmp/';

                            if ($fn) {
                                if (isFileValid($fn)) {
                                    // AJAX call
                                    file_put_contents(
                                        $targetDir . $fn,
                                        file_get_contents('php://input')
                                    );
                                    removeFile($fn);
                                }
                            }

                            function removeFile($file) {
                                unlink($targetDir . $file);
                            }
                        ?>
                    </div>
                </div>
            </div>

            <!-- /.card -->
            <div class="clearfix d-md-none"></div>
        </div>
    </div>
    <div class="row">
            {{--   add user modal--}}
            <div class="modal fade" id="single_contact_show" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Contact View</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h5 id="contact-name">User Name: </h5>
                            <h5 id="contact-email"></h5>
                            <p id="contact-message"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{--  End add user modal--}}
            <div class="col-md-12 col-sm-12">
                <section class="content">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">All Active Dealer</h3>
                            <form id="bulk_action_form" action="{{ route('admin.contact.delete') }}" method="POST">
                                @csrf
                                <div class="card-header">
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table  class="table table-bordered table-striped contact_table">
                                        <thead>
                                        <tr>
                                            <th class="text-start">
                                                <div>
                                                    <input type="checkbox" id="is_check_all">
                                                </div>
                                            </th>
                                            <th class="text-start">{{ __('SL') }}</th>
                                            <th>File Name</th>
                                            <th>File Path</th>
                                            <th>File Type</th>
                                            <th>Zip Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                        <!-- /.card-body -->
                            </form>
                        </div>
                    </div>
                        <!-- /.card-body -->
                    <!-- /.card -->
                </section>

            </div>
      </div>
    </section>
    <!-- /.content -->
</div>
</div>


@endsection
@push('js')
<script>
    $(document).ready(function() {
        $('#uploadButton').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();
            formData.append("import_file", $("#import_file")[0].files[0]);
            formData.append("user", $("#user").val());
            // var formData = new FormData($("#import_file")[0].files[0]);

            // alert(formData);
            $(this).addClass('btn-loading');
            $.ajax({
                url: "{{ route('admin.dealer.csv.store')}}",
                type: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                    } else {
                        toastr.error('Unexpected success response format.');
                    }
                    $('.contact_table').DataTable().draw(false);
                    $('#uploadButton').removeClass('btn-loading');
                },
                error: function(xhr, status, error) {

                    try {
                        const resp = JSON.parse(xhr.responseText);
                        console.log(resp)
                        if (resp.errors && resp.errors.import_file && resp.errors.import_file[0]) {
                            toastr.error(resp.errors.import_file[0]);
                        } else {
                            toastr.error('An unexpected error occurred.');
                        }
                    } catch (e) {
                        console.error("Error parsing response:", e);
                        toastr.error('Error occurred while handling the response.');
                    }
                    $('#uploadButton').removeClass('btn-loading');
                }
            });
        });


        $(document).on('click', '.deleteFile', function(e) {
            e.preventDefault()
            var filename = $(this).data('filename');
            $.ajax({
                url: "{{ route('admin.csv.delete', ['filename' => '__FILENAME__']) }}".replace('__FILENAME__', filename),
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    fileViwer()
                    console.log(res)
                },
                errror: function(xhr) {

                },
            })
            alert(filename);
        })


        $('#ftpUploadButton').on('click', function(e) {
            e.preventDefault();
            $(this).addClass('btn-loading');
            $.ajax({
                url: "{{ route('admin.update.ftp.settings') }}",
                type: 'POST',
                data: $('#ftpFormSettings').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.success);
                    $('#ftpUploadButton').removeClass('btn-loading');
                    fileViwer()
                    // console.log(response)
                },
                error: function(xhr) {
                    // console.log(xhr.responseJSON.errors)
                    var error_messages = xhr.responseJSON.errors;
                    for (var error_message in error_messages) {
                        if (error_messages.hasOwnProperty(error_message)) {
                            toastr.error(error_messages[error_message][0]);
                            // alert(error_messages[error_message][0]);
                        }
                    }
                    $('#ftpUploadButton').removeClass('btn-loading');
                }
            });
        });

        function generateFileCard(fileData, type) {
            const serverIcon = "{{ asset('backend/icon/server.png') }}";
            const nonServerIcon = "{{ asset('backend/icon/non-server.png') }}";
            const uploadFolder = "{{ asset('uploads/import') }}";
            const truncatedFileName = fileData.file.length > 20 ? fileData.file.slice(0, 28) + '...' : fileData.file;

            return `
            <div class="d-flex flex-column align-items-center border p-3" style="width: 50%; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <!-- Title and Radio Button -->
                <div class="text-center mb-3">
                    <h5>${type} File</h5>
                    <div class="form-check">
                        <label class="form-check-label" for="${fileData.file}" title="${fileData.file}">
                                ${truncatedFileName}
                        </label>
                    </div>
                </div>

                <!-- CSV Image Placeholder -->

                <div class="mb-1">
                    <input class="form-check-input csvFileDataInput" type="radio" name="file" id="${fileData.file}" value="${fileData.file}" checked>
                    <img src="${type === 'Server' ? serverIcon : nonServerIcon}" alt="${type} File" style="width: 50px; height: auto;">
                </div>

                <!-- Modified Time and Delete Button -->
                <div class="d-flex justify-content-between align-items-center w-100">
                    <span class="text-muted" style="font-size: 0.9rem;">
                        Modified: ${new Date(fileData.modified_time * 1000).toLocaleString()}
                    </span>
                    <br>

                </div>
                <div class="row">
                    <div>
                        <a href="${uploadFolder+"/"+fileData.file}" class="btn btn-warning btn-sm d-flex align-items-center"  download>
                            <i class="fa fa-download me-2"></i>
                        </a>
                    </div>&nbsp;
                    <div>
                        <button class="btn btn-danger btn-sm d-flex align-items-center deleteFile"  data-filename="${fileData.file}" >
                            <i class="fa fa-trash me-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        }

        function deleteFile(fileName) {
            alert(fileName)
            // if (confirm('Are you sure you want to delete ' + fileName + '?')) {
            //     // Perform your deletion action (e.g., send a request to delete the file)
            //     // For demonstration, just alert the file name
            //     alert(fileName + ' has been deleted.');

            //     // Optionally, make an AJAX call to delete the file from the server
            //     // Example:
            //     // fetch('/delete-file', {
            //     //     method: 'POST',
            //     //     body: JSON.stringify({ file: fileName }),
            //     //     headers: {
            //     //         'Content-Type': 'application/json'
            //     //     }
            //     // }).then(response => response.json())
            //     //   .then(data => console.log(data));
            // }
        }

        function displayFiles(response) {
            const fileCards = document.getElementById('fileCards');

            // Clear existing content (in case of re-rendering)
            fileCards.innerHTML = '';

            // Check if the server file exists and append the card
            if (response.latest_server_file) {
                fileCards.innerHTML += generateFileCard(response.latest_server_file, 'Server');
            }

            // Check if the non-server file exists and append the card
            if (response.latest_non_server_file) {
                fileCards.innerHTML += generateFileCard(response.latest_non_server_file, 'Non-Server');
            }
        }


        function fileViwer() {
            $.ajax({
                url: "{{ route('admin.update.csv.manager') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    displayFiles(res);
                    console.log(res)

                },
                error: function(xhr) {

                }
            });
        }

        fileViwer();

        $('#inventoryCsv').on('click', function(e) {
            e.preventDefault();
            var formData = $('#csvFormData').serialize();
            var csvFileName = $('.csvFileDataInput:checked').val();

            // console.log(formData);
            $(this).addClass('btn-loading');
            $.ajax({
                url: "{{ route('admin.inventory.csv.store')}}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    formData,
                    csvFileName,
                },
                success: function(response) {
                    $('#addInventory').text(response.add)
                    $('#totalAdd').text(' = Total add ' + response.total_add + ' inventories')
                    $('#soldInventory').text(response.sold)
                    $('#totalSold').text(' = Total sold ' + response.total_sold + ' inventories')
                    if (response.add == []) {
                        console.log('empty')
                    } else {

                        console.log(response.add)
                        console.log(response);
                    }
                    // console.log(response);
                    $('#inventoryCsv').removeClass('btn-loading');
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    $('#inventoryCsv').removeClass('btn-loading');
                }
            })

        });

        $('#inventoryImport').on('click', function(e) {
            e.preventDefault();

            // Start the FTP process
            $.ajax({
                url: "{{ route('admin.update.ftp.settings')}}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        fetchProgress(); // Start polling for progress
                    } else {
                        alert('Failed to start the process');
                    }
                },
                error: function(xhr) {

                    alert('Error starting the FTP process');
                    console.error(xhr.responseText);
                }
            });
        });


        function fetchProgress() {
            const interval = setInterval(function() {
                $.ajax({
                    url: "{{ route('admin.ftp.progress') }}",
                    type: 'GET',
                    success: function(response) {
                        $('#progressUpdates').append('<p>' + response.status + '</p>');

                        if (response.status === 'File downloaded successfully' ||
                            response.status === 'Failed to download the file') {
                            clearInterval(interval); // Stop polling on completion
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch progress');
                        clearInterval(interval); // Stop polling on error
                    }
                });
            }, 2000); // Poll every 2 seconds
        }
    });
</script>

<!-- Datatable ajax table start here  -->
<script>

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// user yajra code start
$(document).ready(function() {
    $(function() {
        var allRow = '';
        var trashedRow = '';
        var table = $('.contact_table').DataTable({
            dom: "lBfrtip",
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            pageLength: 25,
            processing: true,
            serverSide: true,
            searchable: true,
            "ajax": {
                "url": "{{ route('admin.file-management.index') }}",
                "datatype": "json",
                "dataSrc": "data",
                "data": function(data) {
                    data.showTrashed = $('#trashed_item').attr('showtrash');
                }
            },
            "drawCallback": function(data) {
                        allRow = data.json.allRow;
                        trashedRow = data.json.trashedRow;
                        $('#all_item').text('All (' + allRow + ')');
                        $('#is_check_all').prop('checked', false);
                        $('#trashed_item').text('');
                        $('#trash_separator').text('');
                        $("#bulk_action_field option:selected").prop("selected", false);
                        if (trashedRow > 0) {
                            $('#trash_separator').text('|');
                            $('#trashed_item').text('Trash (' + trashedRow + ')');
                        }
                        if (trashedRow < 1) {
                            $('#all_item').addClass("font-weight-bold");
                        }
                    },
                    initComplete: function() {
                        var toolbar = `<div style="" class="d-flex">
                                    <div class="me-2 mt-3">
                                            <a href="#" style="color:#2688cd;" class="font-weight-bold" id="all_item">All</a>
                                            <span style="color:#2688cd; margin-right:3px;" id="trash_separator"></span><a style="color:#2688cd" href="#" id="trashed_item"></a>
                                    </div>
                                    <div class="form-group row  mt-2">
                                        <div class="col-8" >
                                            <select name="action_type" id="bulk_action_field" class="form-control submit_able form-select" required>
                                                <option value="" selected>Bulk Actions</option>
                                                <option value="restore_from_trash" id="restore_option">Restore From Trash</option>
                                                <option value="move_to_trash" id="move_to_trash">Move To Trash</option>
                                                <option value="delete_permanently" id="delete_option">Delete Permanently</option>
                                            </select>
                                        </div>
                                        <div class="col-4 me-5">
                                            <button style="padding-left:28px; padding-right:28px" type="submit" id="filter_button" class="btn btn-md btn-info">Apply</button>
                                        </div>
                                    </div>
                                </div>`;
                        $("div.dataTables_filter").prepend(toolbar);
                        $("div.dataTables_filter").addClass('d-flex justify-content-between');
                        $("#restore_option").css('display', 'none');
                        $("#delete_option").css('display', 'none');
                        $("#move_to_trash").css('display', 'block');
                        $('#all_item').text('All (' + allRow + ')');
                        $('#is_check_all').prop('checked', false);
                        $('#trashed_item').text('');
                        $('#trash_separator').text('');
                        $("#bulk_action_field option:selected").prop("selected", false);
                        if (trashedRow > 0) {
                            $('#trash_separator').text('|');
                            $('#trashed_item').text('Trash (' + trashedRow + ')');
                        }
                    },

            columns: [
                {
                    name: 'check',
                    data: 'check',
                    sWidth: '5%',
                    orderable: false,
                    targets: 0
                },
                {
                name: 'DT_RowIndex',
                data: 'DT_RowIndex',
                sWidth: '3%'
            },

            {
                data: 'file_name',
                name: 'file_name',
                width: "25%",
            },

            {
                data: 'file_path',
                name: 'file_path',
                width: "20%",
            },
            {
                data: 'file_type',
                name: 'file_type',
                width: "20%",
            },
            {
                data: 'zip_status',
                name: 'zip_status',
                width: "10%",
            },
            {
                    data: 'action',
                    name: 'action',
                    width: "20%",
                    orderable: false,
                    searchable: false
            },

            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, 1000, -1],
                [10, 25, 50, 100, 500, 1000, "All"]
            ],
        });
        table.buttons().container().appendTo('#exportButtonsContainer');

        $(document.body).on('click', '#is_check_all', function(event) {

            var checked = event.target.checked;
            if (true == checked) {
                $('.check1').prop('checked', true);
            }
            if (false == checked) {
                $('.check1').prop('checked', false);
            }
        });

        $('#is_check_all').parent().addClass('text-center');

        $(document.body).on('click', '.check1', function(event) {
            var allItem = $('.check1');
            var array = $.map(allItem, function(el, index) {
                return [el]
            })
            var allChecked = array.every(isSameAnswer);
            function isSameAnswer(el, index, arr) {
                if (index === 0) {
                    return true;
                } else {
                    return (el.checked === arr[index - 1].checked);
                }
            }

            if (allChecked && array[0].checked) {
                $('#is_check_all').prop('checked', true);
            } else {
                $('#is_check_all').prop('checked', false);
            }
        });

        //Submit filter form by select input changing
        $(document).on('change', '.submitable', function() {
            table.ajax.reload();
        });
    });


    // view contact
    $(document).on('click', ".view-contact", function(e){
    e.preventDefault();
    let id = $(this).data('id');
    $.ajax({
        url: "{{ route('admin.single.contact.view') }}",
        method: 'get',
        data: { id: id },
        success: function(res){
            $('#single_contact_show').modal('show');


            $("#contact-name").html(res.singleContact.name ? "User Name : " + res.singleContact.name : "User Name : Not Available"  );
            $("#contact-email").html(res.singleContact.email ? "User Email : " + res.singleContact.email : "User Email : Not Available"  );
            $("#contact-message").html(res.singleContact.message ? "User Message : " + res.singleContact.message : "User message : Not Available"  );

        },
        error: function(err) {
            console.error(err);
        }
    });
});
    // delete contact
$(document).on('click', ".delete-inventory", function(e){
    e.preventDefault();
    let id = $(this).data('id');

    $.confirm({
        title: 'Delete Confirmation',
        content: 'Are you sure?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-danger',
                action: function () {
                    // Do nothing on cancel
                }
            },
            confirm: {
                text: 'Yes',
                btnClass: 'btn-primary',
                action: function () {
                    // Use the 'id' from the outer scope
                    $.ajax({
                        url: "{{ route('admin.file-management.delete') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function (response) {
                            toastr.success(response.success);
                            $('.contact_table').DataTable().draw(false);
                            console.log(response.success)
                            if (response.status == "success") {
                                toastr.success(response.message);
                                $('.contact_table').DataTable().draw(false);
                            }
                        },
                        error: function (error) {
                            // Show Toastr error message
                            toastr.error(error.responseJSON.message);
                        }
                    });
                }
            }
        }
    });
});


$(document).on('click', ".extract-inventory", function(e){
    e.preventDefault();
    let id = $(this).data('id');

    $.confirm({
        title: 'Extract Confirmation',
        content: 'Are you sure?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-danger',
                action: function () {
                    // Do nothing on cancel
                }
            },
            confirm: {
                text: 'Yes',
                btnClass: 'btn-primary',
                action: function () {
                    // Use the 'id' from the outer scope
                    $.ajax({
                        url: "{{ route('admin.file-management.extract') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function (response) {
                            toastr.success(response.success);
                            $('.contact_table').DataTable().draw(false);
                            console.log(response.success)
                            if (response.status == "success") {
                                toastr.success(response.message);
                                $('.contact_table').DataTable().draw(false);
                            }
                        },
                        error: function (error) {
                            // Show Toastr error message
                            toastr.error(error.responseJSON.message);
                        }
                    });
                }
            }
        }
    });
});

  //Bulk Action
  $('#bulk_action_form').on('submit', function(e) {
                    e.preventDefault();
                    var url = $(this).attr('action');
                    var request = $(this).serialize();
                    $('#filter_button').text('Loading...');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: request,
                        success: function(data) {
                            if(data.status=='success')
                            {
                                toastr.success(data.message);
                                $('#filter_button').text('Apply');

                            }
                            if(data.status=='error')
                            {
                                toastr.error(data.message);
                                $('#filter_button').text('Apply');

                            }

                            $('.contact_table').DataTable().draw(false);
                            $('#filter_button').text('Apply');


                        },
                        error: function(error) {

                            toastr.error(error.responseJSON.message);
                            $('#filter_button').text('Apply');
                        }
                    });
                });


                $(document).on('click', '#trashed_item', function(e) {
                e.preventDefault();
                $(this).attr("showtrash", true);
                $('.check1').prop('checked', false)
                $(this).addClass("font-weight-bold");
                $('.contact_table').DataTable().draw(false);
                $('#is_check_all').prop('checked', false);
                $('#all_item').removeClass("font-weight-bold");
                $("#delete_option").css('display', 'block');
                $("#restore_option").css('display', 'block');
                $("#move_to_trash").css('display', 'none');
            });

            $(document).on('click', '#all_item', function(e) {
                e.preventDefault();
                trashed_item = $('#trashed_item');
                $('#is_check_all').prop('checked', false);
                $('.check1').prop('checked', false);
                trashed_item.attr("showtrash", false);
                $(this).addClass("font-weight-bold");
                $('.contact_table').DataTable().draw(false);
                $('#trashed_item').removeClass("font-weight-bold")
                $("#delete_option").css('display', 'none');
                $("#restore_option").css('display', 'none');
                $("#move_to_trash").css('display', 'block');
            });

        // restore method
        $(document).on('click', '.restore', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            $.confirm({
                'title': 'Restore Confirmation',
                'message': 'Are you sure?',
                'buttons': {
                    'No': {
                        'btnClass': 'btn-danger',
                        'action': function() {}
                    },
                    'Yes': {
                        btnClass: 'btn-primary',
                        'action': function() {
                            $('.data_preloader').show();
                            $.ajax({
                                url: url,
                                type: 'get',
                                success: function(data) {
                                    $('.contact_table').DataTable().draw(false);
                                    $('.data_preloader').hide();
                                    toastr.success(data);
                                },
                                error: function(err) {
                                    $('.data_preloader').hide();
                                    if (err.status == 0) {
                                        toastr.error(
                                            'Net Connetion Error. Reload This Page.');
                                    } else {
                                        toastr.error(
                                            'Server Error. Please contact to the support team.'
                                        );
                                    }
                                }
                            });
                        }
                    },

                }
            });
        });

        $(document).on('click', '.delete', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $('#delete_form').attr('action', url);

            $.confirm({
                'title': 'Delete Confirmation',
                'message': 'Are you sure?',
                'buttons': {
                    'No': {
                        'btnClass': 'no btn-danger',
                        'action': function() {}
                    },
                    'Yes': {
                        btnClass: 'btn-primary',
                        'action': function() {
                            // $('#delete_form').submit();
                            $.ajax({
                                url: url,
                                type: 'DELETE',
                                success: function(data) {
                                    toastr.success(data);
                                    $('.loading_button').hide();
                                    $('.contact_table').DataTable().draw(false);
                                },
                                error: function(error) {
                                    $('.loading_button').hide();
                                    toastr.error(error.responseJSON.message);
                                }
                            });
                        }
                    },
                }
            });
        });
});
</script>

<!-- file management script here  -->

<!-- <script>
    (function() {
	function Init() {
		var fileSelect = document.getElementById('file-upload'),
			fileDrag = document.getElementById('file-drag'),
			submitButton = document.getElementById('submit-button');

		fileSelect.addEventListener('change', fileSelectHandler, false);

		// Is XHR2 available?
		var xhr = new XMLHttpRequest();
		if (xhr.upload)
		{
			// File Drop
			fileDrag.addEventListener('dragover', fileDragHover, false);
			fileDrag.addEventListener('dragleave', fileDragHover, false);
			fileDrag.addEventListener('drop', fileSelectHandler, false);
		}
	}

	function fileDragHover(e) {
		var fileDrag = document.getElementById('file-drag');

		e.stopPropagation();
		e.preventDefault();

		fileDrag.className = (e.type === 'dragover' ? 'hover' : 'modal-body file-upload');
	}

	function fileSelectHandler(e) {
		// Fetch FileList object
		var files = e.target.files || e.dataTransfer.files;

		// Cancel event and hover styling
		fileDragHover(e);

		// Process all File objects
		for (var i = 0, f; f = files[i]; i++) {
			parseFile(f);
			uploadFile(f);
		}
	}

	function output(msg) {
		var m = document.getElementById('messages');
		m.innerHTML = msg;
	}

	function parseFile(file) {
		output(
			'<ul>'
			+	'<li>Name: <strong>' + encodeURI(file.name) + '</strong></li>'
			+	'<li>Type: <strong>' + file.type + '</strong></li>'
			+	'<li>Size: <strong>' + (file.size / (1024 * 1024)).toFixed(2) + ' MB</strong></li>'
			+ '</ul>'
		);
	}

	function setProgressMaxValue(e) {
		var pBar = document.getElementById('file-progress');

		if (e.lengthComputable) {
			pBar.max = e.total;
		}
	}

	function updateFileProgress(e) {
		var pBar = document.getElementById('file-progress');

		if (e.lengthComputable) {
			pBar.value = e.loaded;
		}
	}

	function uploadFile(file) {

		var xhr = new XMLHttpRequest(),
			fileInput = document.getElementById('class-roster-file'),
			pBar = document.getElementById('file-progress'),
			fileSizeLimit = 1024;	// In MB
		if (xhr.upload) {
			// Check if file is less than x MB
			if (file.size <= fileSizeLimit * 1024 * 1024) {
				// Progress bar
				pBar.style.display = 'inline';
				xhr.upload.addEventListener('loadstart', setProgressMaxValue, false);
				xhr.upload.addEventListener('progress', updateFileProgress, false);

				// File received / failed
				xhr.onreadystatechange = function(e) {
					if (xhr.readyState == 4) {
						// Everything is good!

						// progress.className = (xhr.status == 200 ? "success" : "failure");
						// document.location.reload(true);
					}
				};

				// Start upload
				xhr.open('POST', document.getElementById('file-upload-form').action, true);
				xhr.setRequestHeader('X-File-Name', file.name);
				xhr.setRequestHeader('X-File-Size', file.size);
				xhr.setRequestHeader('Content-Type', 'multipart/form-data');
				xhr.send(file);
			} else {
				output('Please upload a smaller file (< ' + fileSizeLimit + ' MB).');
			}
		}
	}

	// Check for the various File API support.
	if (window.File && window.FileList && window.FileReader) {
		Init();
	} else {
		document.getElementById('file-drag').style.display = 'none';
	}
})();
</script> -->
<script>
(function() {
    function Init() {
        var fileSelect = document.getElementById('file-upload'),
            fileDrag = document.getElementById('file-drag'),
            submitButton = document.getElementById('submit-button');

        fileSelect.addEventListener('change', fileSelectHandler, false);

        // Is XHR2 available?
        var xhr = new XMLHttpRequest();
        if (xhr.upload) {
            // File Drop
            fileDrag.addEventListener('dragover', fileDragHover, false);
            fileDrag.addEventListener('dragleave', fileDragHover, false);
            fileDrag.addEventListener('drop', fileSelectHandler, false);
        }
    }

    function fileDragHover(e) {
        var fileDrag = document.getElementById('file-drag');

        e.stopPropagation();
        e.preventDefault();

        fileDrag.className = (e.type === 'dragover' ? 'hover' : 'modal-body file-upload');
    }

    function fileSelectHandler(e) {
        // Fetch FileList object
        var files = e.target.files || e.dataTransfer.files;

        // Cancel event and hover styling
        fileDragHover(e);

        // Process all File objects
        for (var i = 0, f; f = files[i]; i++) {
            parseFile(f);
            uploadFile(f);
        }
    }

    function output(msg) {
        var m = document.getElementById('messages');
        m.innerHTML = msg;
    }

    function parseFile(file) {
        output(
            '<ul>'
            +	'<li>Name: <strong>' + encodeURI(file.name) + '</strong></li>'
            +	'<li>Type: <strong>' + file.type + '</strong></li>'
            +	'<li>Size: <strong>' + (file.size / (1024 * 1024)).toFixed(2) + ' MB</strong></li>'
            + '</ul>'
        );

        // Show the "Import" button after file details
        document.getElementById('import-btn').style.display = 'inline-block';
    }

    function setProgressMaxValue(e) {
        var pBar = document.getElementById('file-progress');

        if (e.lengthComputable) {
            pBar.max = e.total;
        }
    }

    function updateFileProgress(e) {
        var pBar = document.getElementById('file-progress');

        if (e.lengthComputable) {
            pBar.value = e.loaded;
        }
    }

    function uploadFile(file) {

        var xhr = new XMLHttpRequest(),
            fileInput = document.getElementById('file-upload'),
            pBar = document.getElementById('file-progress'),
            fileSizeLimit = 1024;	// In MB
        if (xhr.upload) {
            // Check if file is less than x MB
            if (file.size <= fileSizeLimit * 1024 * 1024) {
                // Progress bar
                pBar.style.display = 'inline';
                xhr.upload.addEventListener('loadstart', setProgressMaxValue, false);
                xhr.upload.addEventListener('progress', updateFileProgress, false);

                // File received / failed
                xhr.onreadystatechange = function(e) {
                    if (xhr.readyState == 4) {
                        // Everything is good!
                    }
                };

                // Start upload
                xhr.open('POST', document.getElementById('file-upload-form').action, true);
                xhr.setRequestHeader('X-File-Name', file.name);
                xhr.setRequestHeader('X-File-Size', file.size);
                xhr.setRequestHeader('Content-Type', 'multipart/form-data');
                xhr.send(file);
            } else {
                output('Please upload a smaller file (< ' + fileSizeLimit + ' MB).');
            }
        }
    }

    // Check for the various File API support.
    if (window.File && window.FileList && window.FileReader) {
        Init();
    } else {
        document.getElementById('file-drag').style.display = 'none';
    }

    // // Handle the import button click
    // document.getElementById('import-btn').addEventListener('click', function() {
    //     var filePath = document.getElementById('file-upload').files[0].name;  // Get the file name
    //     var xhr = new XMLHttpRequest();
    //     xhr.open('POST', '/admin/file-management/upload', true);
    //     xhr.setRequestHeader('Content-Type', 'application/json');
    //     xhr.onreadystatechange = function() {
    //         if (xhr.readyState === 4 && xhr.status === 200) {
    //             alert('File has been imported and unzipped!');
    //         }
    //     };
    //     xhr.send(JSON.stringify({ fileName: filePath }));
    // });

    $(document).ready(function() {
        // $('#file-upload').on('change', function () {
        //     $('#import-btn').show();
        // });

        // Handle the import button click using jQuery
        $('#import-btn').on('click', function() {
            var formData = new FormData();
            var fileInput = $('#file-upload')[0];
            var file = fileInput.files[0];

            if (file) {
                // Append the file to the form data
                formData.append('file', file);

                // You can also append the file name or other data if needed
                formData.append('fileName', file.name);

                $.ajax({
                    url: "{{ route('admin.file-management.upload') }}", // Adjust to your route
                    type: 'POST',
                    data: formData,
                    contentType: false,  // This will let jQuery set the content-type to multipart/form-data
                    processData: false,  // Don't let jQuery process the data
                    xhr: function () {
                        // Custom XMLHttpRequest to handle progress bar
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                $('#file-progress').val(percentComplete); // Update progress bar
                                $('#file-progress').find('span').text(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        toastr.success('File has been imported and unzipped!');

                        // Reset UI
                        $('#file-upload').val('');
                        $('#file-progress').val(0);
                        $('#import-btn').hide();

                        // Optionally, display file details
                        $('#messages').html('<p>Uploaded: ' + file.name + '</p>');
                        $('.contact_table').DataTable().draw(false);
                    },
                    error: function(xhr, status, error) {
                        alert('Error: ' + error);
                    }
                });
            } else {
                alert('Please select a file to upload');
            }
        });
    });
})();
</script>

@endpush
