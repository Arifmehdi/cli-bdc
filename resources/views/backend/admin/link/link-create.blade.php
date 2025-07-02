@extends('backend.admin.layouts.master')

@section('content')


{{--   add link modal--}}
<div class="modal fade" id="linksAddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Social Media</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('admin.links.add')}}" method="post" id="linksAdd" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="title">Title <span style="color: red; font-weight:bold">*</span></label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Enter Your Title">
                        <span class="text-danger error-message" id="title-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="title">Link <span style="color: red; font-weight:bold">*</span></label>
                        <input type="text" class="form-control" name="link" id="link" placeholder="Enter Your Link">
                        <span class="text-danger error-message" id="link-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="image">Image <span style="color: red; font-weight:bold">*</span></label>
                        <input type="file" name="image" id="image" class="">
                        <div id="imagePreviewContainerFavicon">
                            <img width="80px" height="50px" id="mediaImagePreview" style="display: none; margin-top:5px">
                            <a id="removeImageButtonmedia" style="display: none;
                            color:red; cursor:pointer">x</a>
                        </div>
                        <span class="text-danger error-message" id="image-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddOne" name="status" value="1"
                            checked>&nbsp;&nbsp;&nbsp;<label for="statusAddOne">Active</label>
                        &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddTwo" name="status"
                            value="0">&nbsp;&nbsp;&nbsp;<label for="statusAddTwo">Inactive</label>
                        <span class="text-danger error-message" id="status-error"></span>
                    </div>

                    <button type="submit"  style="float:right; margin-bottom:8px; padding-left:25px; padding-right:25px; font-size:15px"  class="btn btn-success" id="link_button">Submit</button>
                </form>
            </div>


        </div>
    </div>
</div>

{{--  End add link modal--}}




{{-- links edit modal start --}}

    <!-- Modal -->
    <div class="modal fade" id="linksEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Social Media Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.links.edit')}}" method="post" id="linksEditFrom" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="links_id" id="links_id">
                        <div class="form-group">
                            <label for="title">Title <span style="color: red; font-weight:bold">*</span></label>
                            <input type="text" class="form-control" name="up_title" id="up_title" placeholder="Enter Your Title">
                            <span class="text-danger error-message" id="up_title-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="title">Link <span style="color: red; font-weight:bold">*</span></label>
                            <input type="text" class="form-control" name="up_link" id="up_link" placeholder="Enter Your Link">
                            <span class="text-danger error-message" id="up_link-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" name="up_img" id="up_img">
                            <img width="80px"  height="50px" class="UpmediaimagePreview" style="margin-top:5px">
                            <div id="editImagePreview">
                                <img width="80px" height="50px" id="UploadmediaImagePreview" style="display: none; margin-top:5px">
                                <a id="UpremoveImageButtonmedia" style="display: none;
                                color:red; cursor:pointer">x</a>
                            </div>
                            <span class="text-danger error-message" id="up_img-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;<input type="radio" id="statusEditOne" name="status" value="1"
                                checked>&nbsp;&nbsp;&nbsp;<label for="statusEditOne">Active</label>
                            &nbsp;&nbsp;&nbsp;<input type="radio" id="statusEditTwo" name="status"
                                value="0">&nbsp;&nbsp;&nbsp;<label for="statusEditTwo">Inactive</label>
                            <span class="text-danger error-message" id="status-error"></span>
                        </div>
                        <button type="submit" style="float:right; margin-bottom:8px; padding-left:25px; padding-right:25px; font-size:15px"  class="btn btn-success" id="edit_link_button">Update</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- links edit modal close --}}

<div class="col-md-12">
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Social Media List</h3>
                <a href="" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#linksAddModal"> <i class="fas fa-plus-circle"></i> Add Links</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table  class="table table-bordered table-striped links_table">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Icon</th>
                        <th>Title</th>
                        <th>Link</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
            <!-- /.card-body -->
    </section>

</div>

@endsection

@push('js')
<script>
$(document).ready(function(){
    $.ajaxSetup({
         headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
       });

    //    yajra implement

    $(function() {

var table = $('.links_table').DataTable({

    dom: "lBfrtip",
    buttons: ["copy", "csv", "excel", "pdf", "print"],

    pageLength: 25,
    processing: true,
    serverSide: true,
    searchable: true,
    "ajax": {
        "url": "{{ route('admin.links.show') }}",
        "datatype": "json",
        "dataSrc": "data",
        "data": function(data) {

        }
    },

    drawCallback: function(settings) {

        $('#is_check_all').prop('checked', false);

    },

    columns: [
        {
        name: 'DT_RowIndex',
        data: 'DT_RowIndex',
        sWidth: '3%'
    },
    {
        data: 'Icon',
        name: 'Icon',
    },


    {
        data: 'title',
        name: 'title',
    },
    {
        data: 'link',
        name: 'link',
    },


    {
        data: 'status',
        name: 'status',
    },
        {
            data: 'action',
            name: 'action',
            width: "10%",
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
    alert('Checkbox clicked!');
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

//    image show and remove

$(document).ready(function () {
    // When the file input changes
    $("#image").change(function () {
        readURL(this);
    });

    // Function to read the URL and display the image preview
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                // Display the image preview
                $("#mediaImagePreview").attr("src", e.target.result).show();

                // Show the remove button
                $("#removeImageButtonmedia").show();
                $(".mediaimagePreview").hide();
            };

            // Read the file as a data URL
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Click event for the remove button
    $("#removeImageButtonmedia").click(function() {
        // Clear the file input and hide the image preview
        $("#image").val("");
        $("#mediaImagePreview").attr("src", "").hide();
        // Hide the remove button
        $(this).hide();
    });
});
$(document).ready(function () {
    // When the file input changes
    $("#up_img").change(function () {
        readURL(this);
    });

    // Function to read the URL and display the image preview
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                // Display the image preview
                $("#UploadmediaImagePreview").attr("src", e.target.result).show();

                // Show the remove button
                $("#UpremoveImageButtonmedia").show();
                $(".UpmediaimagePreview").hide();
            };

            // Read the file as a data URL
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Click event for the remove button
    $("#UpremoveImageButtonmedia").click(function() {
        // Clear the file input and hide the image preview
        $("#up_img").val("");
        $("#UploadmediaImagePreview").attr("src", "").hide();
        // Hide the remove button
        $(this).hide();
    });
});

    $(document).on('submit','#linksAdd', function (e) {
    e.preventDefault();
    var formData = new FormData($(this)[0]);
        $('#link_button').text('Loading...');
         // Clear previous validation errors
         var errorFields = ['title','link', 'image'];
            errorFields.forEach(function (field) {
            $('#' + field + '-error').text('');
            });

    $.ajax({
        processData: false,
        contentType: false,
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: formData,
        success: function (res) {
            console.log(res);
            $('.error-message').html('');
            if (res.errors) {
                // Display validation errors dynamically
                $.each(res.errors, function (key, value) {
                    $('#' + key + '-error').html(value[0]);
                });

                $('#link_button').text('Submit');
            }

            if (res.status === 'success') {
                $('#linksAdd')[0].reset();
                $('.links_table').DataTable().draw(false);
                toastr.success(res.message);
                $('#linksAddModal').modal('hide');
                $('#link_button').text('Submit');
                $('#imagePreviewContainerFavicon').html('');
            }
        },

        error: function (xhr) {
            // Handle error response
            var errors = xhr.responseJSON.errors;

            // Display validation errors dynamically
            $.each(errors, function (key, value) {
                $('#' + key + '-error').text(value[0]);
            });
            $('#link_button').text('Submit');
        }
    });
});


// links delete
$(document).on('click', "#links_delete", function(e){
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
                        url: "{{ route('admin.single.links.delete') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                $('.links_table').DataTable().draw(false);
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

$(document).on('click', '.editBtn', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let title = $(this).data('title');
                let link = $(this).data('link');
                let status = $(this).data('status');
                let image = $(this).data('image');
                $('.UpmediaimagePreview').attr('src', "{{ asset('/frontend/assets/images/links') }}" + '/' + image);
                $('input[name="status"][value="' + status + '"]').prop('checked', true);
                $('#links_id').val(id);
                $('#up_title').val(title);
                $('#up_link').val(link);
                //  $('#up_img').val(image);
                $('#linksEdit').modal('show');

            });

             // news edit code
             $(document).on('submit', '#linksEditFrom', function(e) {
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                $('#edit_link_button').text('Loading...');
                var errorFields = ['up_title','up_link', 'up_img'];
                errorFields.forEach(function (field) {
                $('#' + field + '-error').text('');
                });


                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        console.log(res);
                        if (res.errors) {
                            $.each(res.errors, function (key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            $('#edit_link_button').text('Submit');
                        }

                        if (res.status === 'success') {
                        toastr.success(res.message);
                        $('#linksEdit').modal('hide');
                        $('.links_table').DataTable().draw(false);
                        $('#edit_link_button').text('Update');
                        $('#linksEditFrom')[0].reset();
                        $('#editImagePreview').html('');
                        }

                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });


            // status change
           $(document).on('change', '.action-select', function(e){
                e.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    url:"{{route('admin.link.status.change')}}",
                    method:"post",
                    data:{id:id},
                    success:function(res){
                        console.log(res)
                        toastr.success(res.message);
                        $('.links_table').DataTable().draw(false);
                    }
                })
            });



})
</script>
@endpush
