@extends('backend.admin.layouts.master')

@section('content')
<div class="row">


    {{-- add user modal--}}
    <div class="modal fade" id="faqAddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Faq</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div class="modal-body">
                    <form action="{{ route('admin.faq.add')}}" method="post" id="faqAdd" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="status">Select Page</label>
                            <select class="form-control" name="type" id="type">
                                <option value="" seleted>Select Any</option>
                                <option value="faq">FAQ</option>
                                <option value="research">Research</option>
                                <option value="carsforsale">Cars For sale</option>

                            </select>

                        </div>
                        <div class="form-group">
                            <label for="title">Title <span style="color: red; font-weight:bold">*</span></label>
                            <input type="text" class="form-control" name="title" id="title"
                                placeholder="Enter Your Title">
                            <span class="text-danger error-message" id="title-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="description">Description <span
                                    style="color: red; font-weight:bold">*</span></label>
                            <textarea name="description" id="description" rows="12" class="form-control rounded"
                                placeholder=""></textarea>
                            <span class="text-danger error-message" id="description-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddOne" name="status" value="1"
                                checked>&nbsp;&nbsp;&nbsp;<label for="statusAddOne">Active</label>
                            &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddTwo" name="status"
                                value="0">&nbsp;&nbsp;&nbsp;<label for="statusAddTwo">Inactive</label>
                            <span class="text-danger error-message" id="status-error"></span>
                        </div>



                        <button type="submit"
                            style="float:right; margin-bottom:8px; padding-left:25px; padding-right:25px; font-size:15px"
                            class="btn btn-success" id="faq_button">Submit</button>
                    </form>
                </div>


            </div>
        </div>
    </div>

    {{-- End add user modal--}}


    {{-- news edit modal start --}}

    <!-- Modal -->
    <div class="modal fade" id="faqEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">FAQ Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-body">
                    <form id="faqEditFrom" action="{{ route('admin.faq.update') }}" method="POST"
                        class="form-horizontal mt-2 sales-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="faq_id" id="faq_id">
                        <div class="form-group">
                            <label for="status">Select Page</label>
                            <select class="form-control" name="up_type" id="up_type">
                                <option value="" seleted>Select Any</option>
                                <option value="faq">FAQ</option>
                                <option value="research">Research</option>
                                <option value="carsforsale">Cars For sale</option>

                            </select>

                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label class="control-label">Title <span
                                        style="color:red;font-weight:bold">*</span></label>
                                <input id="up_title" name="up_title" class="form-control rounded"
                                    placeholder="car title" type="text">
                                <div class="text-danger  error-up_title"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 col-lg-12 col-xs-12  col-sm-12">
                                <label class="control-label">Description </small> <span
                                        style="color:red;font-weight:bold">*</span></label>
                                <textarea name="up_description" id="faq_edit" rows="12"
                                    class="form-control rounded up_description" placeholder=""></textarea>
                                <div class="text-danger  error-up_description"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;<input type="radio" id="statusEditOne" name="status" value="1"
                                checked>&nbsp;&nbsp;&nbsp;<label for="statusEditOne">Active</label>
                            &nbsp;&nbsp;&nbsp;<input type="radio" id="statusEditTwo" name="status"
                                value="0">&nbsp;&nbsp;&nbsp;<label for="statusEditTwo">Inactive</label>
                            <span class="text-danger error-message" id="status-error"></span>
                        </div>




                        <button type="submit" class="btn btn-primary float-right mt-4"
                            id="faq_update_button">Update</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- news edit modal close --}}


    {{-- News show modal--}}
    <div class="modal fade" id="newsShowModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">FAQ View</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>





                <div class="modal-body">
                    <img width="100%" id="news-img" src="" alt="news-img" />


                    <h4 style="font-weight:bold; margin-top:10px" id="news-title"></h4>
                    <p id="news-des"></p>
                </div>


            </div>
        </div>
    </div>

    {{-- End add user modal--}}

    <div class="col-md-12">
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">FAQ List</h3>
                    <a href="" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                        data-target="#faqAddModal"> <i class="fas fa-plus-circle"></i> Add FAQ</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    <table class="table table-bordered table-striped faqs_table">
                        <thead>
                            <tr>
                                <th>SL</th>

                                <th>Title</th>
                                <th>Description</th>
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

            <!-- /.card -->

        </section>

    </div>
</div>

@endsection

@push('js')
<script>
    // description editor js start
    let editorInstance;

ClassicEditor
    .create(document.querySelector('#description'), {
        ckfinder: {
            uploadUrl: "{{ route('admin.ckeditor.upload', ['_token' => csrf_token()]) }}"
        },
        toolbar: [
            'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'ckfinder', 'imageUpload', 'insertTable', 'mediaEmbed', 'undo', 'redo'
        ],
        mediaEmbed: {
            previewsInData: true
        }
    })
    .then(editor => {
        editorInstance = editor;
    })
    .catch(error => {
        console.error(error);
    });

    // description editor js close
    // news add data
    $(document).ready(function(){

        $(function() {
    var table = $('.faqs_table').DataTable({
    dom: "lBfrtip",
    buttons: ["copy", "csv", "excel", "pdf", "print"],
    pageLength: 25,
    processing: true,
    serverSide: true,
    searchable: true,
    "ajax": {
        "url": "{{ route('admin.faq.show') }}",
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
        data: 'title',
        name: 'title',
        sWidth: '27%'
    },
    {
        data: 'description',
        name: 'description',
        sWidth: '45%'
    },
    {
        data: 'status',
        name: 'status',
        sWidth: '10%'
    },
    {
        data: 'action',
        name: 'action',
        sWidth: "15%",
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
        $.ajaxSetup({
         headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
       });

    $(document).on('submit','#faqAdd', function (e) {
    e.preventDefault();
    var formData = new FormData($(this)[0]);
        $('#faq_button').text('Loading...');
    var form = this;
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
                $.each(res.errors, function (key, value) {
                    $('#' + key + '-error').html(value[0]);
                });
                $('#faq_button').text('Submit');
            }

            if (res.status === 'success') {
                $('#faqAdd')[0].reset();
                $('.faqs_table').DataTable().draw(false);
                toastr.success(res.message);
                // Reset CKEditor content
                if (editorInstance) {
                editorInstance.setData('');
                }
                form.reset();
                $('#faqAddModal').modal('hide');
                $('#faq_button').text('Submit');
               
            }
        },
        error: function (xhr) {
            var errors = xhr.responseJSON.errors;
            $.each(errors, function (key, value) {
                $('#' + key + '-error').text(value[0]);
            });
            $('#faq_button').text('Submit');
        }
    });
});


// faq delete
$(document).on('click', "#faq_delete", function(e) {
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
                    // No action needed for "No"
                }
            },
            confirm: {
                text: 'Yes',
                btnClass: 'btn-primary',
                action: function () {
                    $.ajax({
                        url: "{{ route('admin.faq.delete') }}",
                        type: 'POST',
                        data: {
                            id: id,
                            _token: "{{ csrf_token() }}" // Include CSRF token
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                $('.faqs_table').DataTable().draw(false); // Redraw table without reloading
                            } else {
                                toastr.error("Unable to delete FAQ.");
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || 'An error occurred.');
                        }
                    });
                }
            }
        }
    });
});



// news show
         let editor;
        $(document).on('click', '.editFaq', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let type = $(this).data('type');
                let title = $(this).data('title');
                let description = $(this).data('description');
                let status = $(this).data('status');
                var tempElement = document.createElement('div');
                tempElement.innerHTML = description;
                var textOnly = tempElement.textContent || tempElement.innerText || '';
                $('input[name="status"][value="' + status + '"]').prop('checked', true);
                $('#faq_id').val(id);
                $('#up_title').val(title);
                $('#up_type').val(type);
                $('#faq_edit').text(textOnly);
                $('#faqEdit').modal('show');
                if (editor) {
                    editor.destroy();
                }
                ClassicEditor.create(document.querySelector('#faq_edit'))
                    .then(newEditor => {
                        editor = newEditor;
                        editor.setData(description);
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });
            // news edit code
            $(document).on('submit', '#faqEditFrom', function(e) {
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                $('#faq_update_button').text('Loading...');
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        console.log(res);
                        toastr.success(res.message);
                        $('#faqEdit').modal('hide');
                        $('.faqs_table').DataTable().draw(false);
                        var errors = res.errors;
                        $('#faq_update_button').text('Update');
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
                    url:"{{route('admin.faqs.status.change')}}",
                    method:"post",
                    data:{id:id},
                    success:function(res){
                        console.log(res)
                        toastr.success(res.message);
                        $('.faqs_table').DataTable().draw(false);
                    }
                })
            });








    })


</script>

@endpush