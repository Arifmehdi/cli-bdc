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
        {{-- <div class="card"> --}}
            {{-- <div class="card-header"> --}}
                {{-- <h3 class="card-title">Import Details </h3> --}}
                {{--<a href="" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                            data-target="#vehicleModal"> <i class="fas fa-plus-circle"></i> Add Make</a>--}}
                {{--<a href="#" class="btn btn-info btn-sm float-right " id="inventoryImport"> <i class="fas fa-plus-circle"></i> Import Data</a>
                 <button id="inventoryImport">Start FTP Process</button> --}}
                {{-- <div id="progressUpdates"></div> --}}
            {{-- </div> --}}
            {{-- <div class="card"> --}}
                {{--<div class="card-header">
                        <h3 class="card-title">DataTable with default features</h3>
                    </div>--}}
                <!-- /.card-header -->
                {{--<div class="card-body">
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
                </div>--}}
                <!-- /.card-body -->
            {{-- </div> --}}
            <!-- /.card -->
        </div>
        <!-- /.card-body -->
    </div>
    <div class="col-md-12">
        <!-- Main content -->
        <section class="content row">
            {{--<div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add CSV File</h3><br>
                        <hr>
                        <form action="{{route('admin.inventory.store')}}" method="post" enctype="multipart/form-data">
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
            </div>--}}

            <!-- /.card -->
            <div class="clearfix d-md-none"></div>
            <div class="card w-100">
                <div class="row p-4">
                    @foreach ($inventories as $inventory )


                    <div class="col-lg-3 col-md-4 col-xl-3 col-sm-12">
                        <div class="card overflow-hidden">
                            <div style="margin-top:-1px !important" class="item-card9-img">
                                @php
                                $image_obj = $inventory->additionalInventory->local_img_url;
                                $image_splice = explode(',',$image_obj);
                                $image = str_replace(["[", "'"], "", $image_splice[0]);

                                $vin_string_replace = str_replace(' ','',$inventory->vin);
                                $route_string = str_replace(' ','',$inventory->year.'-'.$inventory->make.'-'.$inventory->model.'-in-'.$inventory->dealer->city.'-'.$inventory->dealer->state)
                                @endphp

                                <div class="item-card9-imgs">
                                    <a class="link" href="javascript:void(0)"></a>
                                    @if($image_obj !='' && $image_obj !='[]')
                                    <img src="{{ asset($image) }}" alt="Used cars for sale {{ $inventory->title }}, price is {{ $inventory->price }}, vin {{ $inventory->vin }} in {{ $inventory->dealer->city }},{{ $inventory->dealer->state }}, dealer name is {{ $inventory->dealer->name }} Dream Best Car image" class="lazyload admin-inventory-image" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';">

                                    @elseif($image_obj =='[]')
                                    <img width="100%" src="{{ asset('frontend/uploads/NotFound.png') }}" alt="Used cars for sale coming soon image dream best" class="">
                                    @else
                                    <img width="100%" src="{{ asset('frontend/uploads/NotFound.png') }}" alt="Used cars for sale coming soon image dream best" class="">
                                    @endif
                                </div>

                                @php
                                $countWishList = 0;
                                if (session()->has('favourite')) {
                                $favourites = session('favourite');
                                foreach ($favourites as $favorite) {
                                if ($favorite['id'] == $inventory->id) {
                                $countWishList = 1;
                                break; // No need to continue the loop if found
                                }
                                }
                                }
                                @endphp


                            </div>
                            <div style="background: rgb(255, 255, 255);
                            background: linear-gradient(0deg, rgb(232, 245, 243) 0%, rgb(255, 255, 255) 100%);" class=" mb-0">
                                <div style="padding:12px !important" class="card-body ">
                                    <div class="item-card9">
                                        @php
                                        $title = Str::substr($inventory->title, 0, 27)
                                        @endphp
                                        <a href="{{route('admin.inventory.edit.page',$inventory->id )}}" class="text-dark">
                                            <h6 style="color:black !important; font-weight:600; opacity:90%" class="font-weight-semibold mt-1"> {{$title}}</h6>
                                        </a>

                                        <div class="item-card9-desc mb-2">
                                            @php
                                            // Safely check for the existence of transmission
                                            $transmissionValue = $inventory->transmission ?? null;
                                            $transmission = strtolower($transmissionValue);

                                            if (strpos($transmission, 'automatic') !== false) {
                                            $transmission = 'Automatic';
                                            } elseif (strpos($transmission, 'variable') !== false) {
                                            $transmission = 'Variable';
                                            } else {
                                            $transmission = 'Manual'; // or any default value
                                            }

                                            // Limit transmission string to 25 characters if needed
                                            $transmission = substr($transmission, 0, 25);
                                            @endphp

                                            <p style="margin:0" class="me-4 d-inline-block"><span class=""> {{$transmission}}</span></p>
                                            <p style="margin:0">Used</p>

                                        </div>
                                        <div style="height: 25px" class="d-flex">
                                            <h5 class="me-3" style="font-weight:600">{{ $inventory->price_formate}}</h5>
                                            <p style="color:black; font-weight:600; font-size:14px; margin-top:5px; margin-left:12px">${{ $inventory->payment_price}}/mo*</p>
                                        </div>


                                    </div>
                                    <div class="item-card9-footer d-sm-flex">
                                        <p class="w-50 mt-2 mb-1 float-start" title="Mileage"><i class="fa fa-road text-muted me-1 "></i> {{ number_format($inventory->miles ).' miles' }}</p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div style="float:right;" class="item-card9-footer d-sm-flex">
                                        <a href="{{route('admin.inventory.edit.page',$inventory->id )}}" class="btn btn-info">Edit Inventory</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- pagination --}}

                <div class="custom-pagination me-2" style="display: flex;justify-content: flex-end;">
                    <ul style="float:right" class="pagination">
                        @if ($inventories->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                        <li class="page-item"><a class="page-link" href="{{ $inventories->previousPageUrl() }}">Previous</a></li>
                        @endif

                        @php
                        $start = max($inventories->currentPage() - 2, 1);
                        $end = min($start + 4, $inventories->lastPage());
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i==$inventories->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                            @else
                            <li class="page-item"><a class="page-link" href="{{ $inventories->url($i) }}">{{ $i }}</a></li>
                            @endif
                            @endfor

                            @if ($end < $inventories->lastPage())
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                <li class="page-item"><a class="page-link" href="{{ $inventories->url($inventories->lastPage() - 2) }}">{{ $inventories->lastPage() - 2 }}</a></li>
                                <li class="page-item"><a class="page-link" href="{{ $inventories->url($inventories->lastPage() - 1) }}">{{ $inventories->lastPage() - 1 }}</a></li>
                                <li class="page-item"><a class="page-link" href="{{ $inventories->url($inventories->lastPage()) }}">{{ $inventories->lastPage() }}</a></li>
                                @endif

                                @if ($inventories->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $inventories->nextPageUrl() }}">Next</a></li>
                                @else
                                <li class="page-item disabled"><span class="page-link">Next</span></li>
                                @endif
                    </ul>
                </div>
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
            formData.append("splitNumber", $("#splitNumber").val());
            // var formData = new FormData($("#import_file")[0].files[0]);

            // alert(formData);
            $(this).addClass('btn-loading');
            $.ajax({
                url: "{{ route('admin.inventory.store')}}",
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
                },
                error: function(xhr, status, error) {
                    try {
                        const resp = JSON.parse(xhr.responseText);
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
@endpush
