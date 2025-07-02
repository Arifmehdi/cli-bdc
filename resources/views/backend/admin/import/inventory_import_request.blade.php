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

<div class="row">
    <div class="col-md-12">
        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Import Details </h3>
                {{--<a href="" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                            data-target="#vehicleModal"> <i class="fas fa-plus-circle"></i> Add Make</a>--}}
                {{--<a href="#" class="btn btn-info btn-sm float-right " id="inventoryImport"> <i class="fas fa-plus-circle"></i> Import Data</a>
                 <button id="inventoryImport">Start FTP Process</button> --}}
                <div id="progressUpdates"></div>
            </div>
            <div class="card">
                {{--<div class="card-header">
                        <h3 class="card-title">DataTable with default features</h3>
                    </div>--}}
                <!-- /.card-header -->
                <div class="card-body">
                    <h2>Inventory Import Summary (Stock)</h2>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <h5>Add Inventory Stock</h5>
                            <hr>
                            <span id="addInventory">Not Available</span><span id="totalAdd" class="text-info"></span>
                        </div>
                        <div class="col-6">
                            <h5>Sold Inventory Stock</h5>
                            <hr>
                            <span id="soldInventory">Not Available</span><span id="totalSold" class="text-info"></span>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.card-body -->
    </div>
    <div class="col-md-12">
        <!-- Main content -->
        <section class="content row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add CSV File</h3><br>
                        <hr>
                        <form action="{{route('admin.inventory.store.v1')}}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="make">User </label>
                                <select name="user" id="user" class="form-control" required>
                                    <option value="" selected>-- Choose User--</option>
                                    @foreach ($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-message" id="import_file-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="make">Import File</label>
                                <input type="file" class="form-control" name="import_file" id="import_file"
                                    placeholder="Enter Your File">
                                <span class="text-danger error-message" id="import_file-error"></span>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="radio" name="splitOption" id="split"> Split
                                </label>
                                <label>
                                    <input type="radio" name="splitOption" id="other" checked> Other
                                </label>

                                <div id="numberInput">
                                    <label for="splitNumber">Enter Rows Number:</label>
                                    <input type="number" name="splitNumber" id="splitNumber" placeholder="Enter number" value="0">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="uploadButton">Upload File</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">File Availability</h3><br>
                        <hr>
                        <form action="{{ route('admin.inventory.csv.store') }}" method="post" id="csvFormData">
                            @csrf
                            <div id="fileCards" class="d-flex flex-row flex-wrap gap-3"></div>
                            <div class="text-right">
                                <button class="btn btn-primary  mt-2" id="inventoryCsv">Import CSV</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Connect FTP & CSV</h3><br>
                        <hr>
                        <form action="{{ route('admin.update.ftp.settings') }}" method="post" enctype="multipart/form-data" id="ftpFormSettings">
                            @csrf
                            <table>
                                <tr>
                                    <td><label for="ftp_user_by">User By:</label></td>
                                    <td>
                                        <select name="ftp_user_by" id="ftp_user_by" class="form-control" required>
                                            <option value="" selected>-- Choose User--</option>
                                            @foreach ($users as $user)
                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-message" id="ftp_user_by-error"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="ftp_server">FTP Server:</label></td>
                                    <td>
                                        <input type="text" class="form-control" id="ftp_server" placeholder="Ftp Server" name="ftp_server" value="{{ env('FTP_SERVER') }}">
                                        <span class="text-danger error-message" id="ftp_server-error"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="ftp_user">FTP User Name:</label></td>
                                    <td>
                                        <input type="text" class="form-control" id="ftp_user" placeholder="Ftp User" name="ftp_user" value="{{ env('FTP_USER') }}">
                                        <span class="text-danger error-message" id="ftp_user-error"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="ftp_pass">FTP Password:</label></td>
                                    <td>
                                        <input type="password" class="form-control" id="ftp_pass" placeholder="Ftp Password" name="ftp_pass" value="{{ env('FTP_PASS') }}">
                                        <span class="text-danger error-message" id="ftp_pass-error"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="ftp_file">FTP File:</label></td>
                                    <td>
                                        <input type="text" class="form-control" id="ftp_file" placeholder="Ftp File" name="ftp_file" value="{{ env('FTP_FILE') }}">
                                        <span class="text-danger error-message" id="ftp_file-error"></span>
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="ftpUploadButton">Connect FTP</button>
                            </div>
                        </form>
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
            {{--<div class="col-md-12 col-sm-12">
                <section class="content">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">All Requesting Inventory</h3>
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
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>City</th>
                                            <th>State</th>
                                            <th>Zip Code</th>
                                            <th>Inventory num.</th>
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

            </div>--}}
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
            formData.append("splitNumber", $("#splitNumber").val());
            // var formData = new FormData($("#import_file")[0].files[0]);

            // alert(formData);
            $(this).addClass('btn-loading');
            $.ajax({
                url: "{{ route('admin.inventory.store.v1')}}",
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
                    $('#uploadButton').removeClass('btn-loading');
                    location.reload();
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
                    location.reload();
                    // console.log(res)
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
                // success: function(response) {
                //     $('#addInventory').text(response.add)
                //     $('#totalAdd').text(' = Total add ' + response.total_add + ' inventories')
                //     $('#soldInventory').text(response.sold)
                //     $('#totalSold').text(' = Total sold ' + response.total_sold + ' inventories')
                //     if (response.add == []) {
                //         console.log('empty')
                //     } else {

                //         console.log(response.add)
                //         console.log(response);
                //     }
                //     // console.log(response);
                //     $('#inventoryCsv').removeClass('btn-loading');
                // },
                // error: function(xhr) {
                //     console.error(xhr.responseText);
                //     $('#inventoryCsv').removeClass('btn-loading');
                // }

                success: function(response) {
                    $('#inventoryCsv').removeClass('btn-loading');
                    
                    if (response.status === 'success') {
                        let summaryHtml = `
                            <p><strong>Imported:</strong> ${response.summary.imported}</p>
                            <p><strong>Updated:</strong> ${response.summary.updated}</p>
                            <p><strong>Skipped:</strong> ${response.summary.skipped}</p>
                        `;
                        
                        // Show skipped rows if they exist
                        if (response.summary.skipped_rows && response.summary.skipped_rows.length > 0) {
                            let skippedRows = response.summary.skipped_rows.map(row => `<li>${row.join(', ')}</li>`).join('');
                            summaryHtml += `<p><strong>Skipped Rows:</strong></p><ul>${skippedRows}</ul>`;
                        }

                        Swal.fire({
                            title: 'Import Summary',
                            html: summaryHtml,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'An error occurred during the import process.',
                            icon: 'error',
                            confirmButtonText: 'Try Again'
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    $('#inventoryCsv').removeClass('btn-loading');
                    Swal.fire({
                        title: 'Error',
                        text: 'An unexpected error occurred.',
                        icon: 'error',
                        confirmButtonText: 'Close'
                    });
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
<script>
    // Add event listeners to handle showing and hiding the number input
    document.querySelectorAll('input[name="splitOption"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const numberInput = document.getElementById('numberInput');
            if (document.getElementById('split').checked) {
                numberInput.style.display = 'block'; // Show the input if "Split" is selected
            } else {
                numberInput.style.display = 'none'; // Hide the input otherwise
            }
        });
    });
</script>


<!-- Datatable ajax table start here  -->
<script>

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// // user yajra code start
// $(document).ready(function() {
//     $(function() {
//         var allRow = '';
//         var trashedRow = '';
//         var table = $('.contact_table').DataTable({
//             dom: "lBfrtip",
//             buttons: ["copy", "csv", "excel", "pdf", "print"],
//             pageLength: 25,
//             processing: true,
//             serverSide: true,
//             searchable: true,
//             "ajax": {
//                 "url": "{{ route('admin.inventory.import.v1') }}",
//                 "datatype": "json",
//                 "dataSrc": "data",
//                 "data": function(data) {
//                     data.showTrashed = $('#trashed_item').attr('showtrash');
//                 }
//             },
//             "drawCallback": function(data) {
//                         allRow = data.json.allRow;
//                         trashedRow = data.json.trashedRow;
//                         $('#all_item').text('All (' + allRow + ')');
//                         $('#is_check_all').prop('checked', false);
//                         $('#trashed_item').text('');
//                         $('#trash_separator').text('');
//                         $("#bulk_action_field option:selected").prop("selected", false);
//                         if (trashedRow > 0) {
//                             $('#trash_separator').text('|');
//                             $('#trashed_item').text('Trash (' + trashedRow + ')');
//                         }
//                         if (trashedRow < 1) {
//                             $('#all_item').addClass("font-weight-bold");
//                         }
//                     },
//                     initComplete: function() {
//                         var toolbar = `<div style="" class="d-flex">
//                                     <div class="me-2 mt-3">
//                                             <a href="#" style="color:#2688cd;" class="font-weight-bold" id="all_item">All</a>
//                                             <span style="color:#2688cd; margin-right:3px;" id="trash_separator"></span><a style="color:#2688cd" href="#" id="trashed_item"></a>
//                                     </div>
//                                     <div class="form-group row  mt-2">
//                                         <div class="col-8" >
//                                             <select name="action_type" id="bulk_action_field" class="form-control submit_able form-select" required>
//                                                 <option value="" selected>Bulk Actions</option>
//                                                 <option value="restore_from_trash" id="restore_option">Restore From Trash</option>
//                                                 <option value="move_to_trash" id="move_to_trash">Move To Trash</option>
//                                                 <option value="delete_permanently" id="delete_option">Delete Permanently</option>
//                                             </select>
//                                         </div>
//                                         <div class="col-4 me-5">
//                                             <button style="padding-left:28px; padding-right:28px" type="submit" id="filter_button" class="btn btn-md btn-info">Apply</button>
//                                         </div>
//                                     </div>
//                                 </div>`;
//                         $("div.dataTables_filter").prepend(toolbar);
//                         $("div.dataTables_filter").addClass('d-flex justify-content-between');
//                         $("#restore_option").css('display', 'none');
//                         $("#delete_option").css('display', 'none');
//                         $("#move_to_trash").css('display', 'block');
//                         $('#all_item').text('All (' + allRow + ')');
//                         $('#is_check_all').prop('checked', false);
//                         $('#trashed_item').text('');
//                         $('#trash_separator').text('');
//                         $("#bulk_action_field option:selected").prop("selected", false);
//                         if (trashedRow > 0) {
//                             $('#trash_separator').text('|');
//                             $('#trashed_item').text('Trash (' + trashedRow + ')');
//                         }
//                     },

//             columns: [
//                 {
//                     name: 'check',
//                     data: 'check',
//                     sWidth: '5%',
//                     orderable: false,
//                     targets: 0
//                 },
//                 {
//                 name: 'DT_RowIndex',
//                 data: 'DT_RowIndex',
//                 sWidth: '3%'
//             },

//             {
//                 data: 'dealer_name',
//                 name: 'dealer_name',
//                 width: "25%",
//             },

//             {
//                 data: 'dealer_email',
//                 name: 'dealer_email',
//                 width: "20%",
//             },
//             {
//                 data: 'dealer_phone',
//                 name: 'dealer_phone',
//             },
//             {
//                 data: 'dealer_city',
//                 name: 'dealer_city',
//                 width: "10%",
//             },
//             {
//                 data: 'dealer_state',
//                 name: 'dealer_state',
//             },
//             {
//                 data: 'dealer_zip',
//                 name: 'dealer_zip',
//                 width: "10%",
//             },
//             {
//                 data: 'inventory_num',
//                 name: 'inventory_num',
//                 width: "15%",
//             },
//             {
//                     data: 'action',
//                     name: 'action',
//                     width: "20%",
//                     orderable: false,
//                     searchable: false
//             },

//             ],
//             lengthMenu: [
//                 [10, 25, 50, 100, 500, 1000, -1],
//                 [10, 25, 50, 100, 500, 1000, "All"]
//             ],
//         });
//         table.buttons().container().appendTo('#exportButtonsContainer');

//         $(document.body).on('click', '#is_check_all', function(event) {

//             var checked = event.target.checked;
//             if (true == checked) {
//                 $('.check1').prop('checked', true);
//             }
//             if (false == checked) {
//                 $('.check1').prop('checked', false);
//             }
//         });

//         $('#is_check_all').parent().addClass('text-center');

//         $(document.body).on('click', '.check1', function(event) {
//             var allItem = $('.check1');
//             var array = $.map(allItem, function(el, index) {
//                 return [el]
//             })
//             var allChecked = array.every(isSameAnswer);
//             function isSameAnswer(el, index, arr) {
//                 if (index === 0) {
//                     return true;
//                 } else {
//                     return (el.checked === arr[index - 1].checked);
//                 }
//             }

//             if (allChecked && array[0].checked) {
//                 $('#is_check_all').prop('checked', true);
//             } else {
//                 $('#is_check_all').prop('checked', false);
//             }
//         });

//         //Submit filter form by select input changing
//         $(document).on('change', '.submitable', function() {
//             table.ajax.reload();
//         });
//     });


//     // view contact
//     $(document).on('click', ".view-contact", function(e){
//     e.preventDefault();
//     let id = $(this).data('id');
//     $.ajax({
//         url: "{{ route('admin.single.contact.view') }}",
//         method: 'get',
//         data: { id: id },
//         success: function(res){
//             $('#single_contact_show').modal('show');


//             $("#contact-name").html(res.singleContact.name ? "User Name : " + res.singleContact.name : "User Name : Not Available"  );
//             $("#contact-email").html(res.singleContact.email ? "User Email : " + res.singleContact.email : "User Email : Not Available"  );
//             $("#contact-message").html(res.singleContact.message ? "User Message : " + res.singleContact.message : "User message : Not Available"  );

//         },
//         error: function(err) {
//             console.error(err);
//         }
//     });
// });
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
                        url: "{{ route('admin.inventory.list.delete.v1') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function (response) {
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

        $(document).on('click', '.c-delete', function(e) {
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

@endpush
