@extends('backend.admin.layouts.master')

@section('content')
<div class="row">
    {{-- create new modal start here --}}
    <div class="modal fade" id="blogssAddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Blog</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('admin.blogs.create') }}" method="post" id="newsAdd"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="category_id">Category <span style="color: red; font-weight:bold">*</span></label>
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="">Choose Category</option>
                                {{-- @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach --}}
                            </select>
                            <span class="text-danger error-message" id="category-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="sub_category_id">Sub Category <span style="color: red; font-weight:bold">*</span></label>
                            <select name="sub_category_id" id="sub_category_id" class="form-control">
                                <option value="">Choose Sub Category</option>
                                {{-- @foreach ($sub_categories as $sub_category)
                                    <option value="{{ $sub_category->id }}">{{ $sub_category->name }}</option>
                                @endforeach --}}
                            </select>
                            <span class="text-danger error-message" id="sub_category-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="title">Title <span style="color: red; font-weight:bold">*</span></label>
                            <input type="text" class="form-control" name="title" id="title"
                                placeholder="Enter Your Title">
                            <span class="text-danger error-message" id="title-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="title">Sub Title <span style="color: red; font-weight:bold">*</span></label>
                            <input type="text" class="form-control" name="sub_title" id="sub_title"
                                placeholder="Enter Your Sub Title">
                            <span class="text-danger error-message" id="sub-title-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="description">Description <span
                                    style="color: red; font-weight:bold">*</span></label>
                            <textarea name="description" id="description" rows="12" class="rounded form-control"
                                placeholder=""></textarea>
                            <span class="text-danger error-message" id="description-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>

                            </select>

                        </div>

                        <div class="mt-4 form-group">
                            <label for="image">Image</label>
                            <input type="file" name="image" id="image" class="">

                            <div id="imagePreviewContainer">
                                <img width="80px" height="50px" id="imagePreview" style="display: none; margin-top:5px">
                                <a id="removeImageButton" style="display: none;
                                        color:red; margin-top:5px; cursor:pointer">x</a>
                            </div>
                        </div>

                        <p class="mt-4">SEO Part</p>
                        <hr />

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="make">SEO Description</label>
                                    <textarea name="seo_description" id="seo_description" class="form-control"
                                        style="height: 37px;" cols="30" rows="10"></textarea>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="make">SEO Keyword</label>
                                    <input type="text" class="form-control" id="keyword">
                                    <div id="keywordTags"></div>

                                    <input type="hidden" name="keywords[]" id="hiddenKeyword">
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            style="float:right; margin-bottom:8px; padding-left:25px; padding-right:25px; font-size:15px"
                            class="btn btn-success" id="news_submit_button">Submit</button>
                    </form>
                </div>


            </div>
        </div>
    </div>
    {{-- create new modal end here --}}


    {{-- news edit modal start --}}

    <!-- Modal -->
    <div class="modal fade" id="newsEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Blog Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-body">
                    <form id="NewsEditFrom" action="{{ route('admin.blogs.update') }}" method="POST"
                        class="mt-2 form-horizontal sales-form" enctype="multipart/form-data">
                        @csrf


                        <input type="hidden" name="news_id" id="news_id">
                        <div class="form-group">
                            <label for="category_id">Category <span style="color: red; font-weight:bold">*</span></label>
                            <select name="category_id" id="edit_category_id" class="form-control">
                                <option value="">Choose Category</option>
                                {{-- @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach --}}
                            </select>
                            <span class="text-danger error-message" id="category-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="sub_category_id">Sub Category <span style="color: red; font-weight:bold">*</span></label>
                            <select name="sub_category_id" id="edit_sub_category_id" class="form-control">
                                <option value="">Choose Sub Category</option>
                                {{-- @foreach ($sub_categories as $sub_category)
                                    <option value="{{ $sub_category->id }}">{{ $sub_category->name }}</option>
                                @endforeach --}}
                            </select>
                            <span class="text-danger error-message" id="sub_category-error"></span>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label class="control-label">Title <span
                                        style="color:red;font-weight:bold">*</span></label>
                                <input id="up_title" name="up_title" class="rounded form-control"
                                    placeholder="car title" type="text">
                                <div class="text-danger error-up_title"></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label class="control-label">Sub Title <span
                                        style="color:red;font-weight:bold">*</span></label>
                                <input id="up_sub_title" name="up_sub_title" class="rounded form-control"
                                    placeholder="car title" type="text">
                                <div class="text-danger error-up_sub_title"></div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label class="control-label">Car Description </small> <span
                                        style="color:red;font-weight:bold">*</span></label>
                                <textarea name="up_description" id="news_edit" rows="12"
                                    class="rounded form-control up_description" placeholder=""></textarea>
                                <div class="text-danger error-up_description"></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <div class="form-group">
                                    <label for="description">Status</label>
                                    <select class="form-control" name="status" id="up_status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>

                                    </select>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label class="control-label">Car Image Upload</label>
                                <br />
                                <input name="up_img" type="file" id="up_img">
                                <img width="80px" height="50px" src="" alt="" class="imagePreview"
                                    style="margin-top:5px">
                                <div id="imagePreviewContainer">
                                    <img width="80px" height="50px" id="upimagePreview"
                                        style="display: none; margin-top:5px">
                                    <a id="UpremoveImageButtonnews" style="display: none;
                                    color:red; margin-top:5px; cursor:pointer">x</a>
                                </div>
                                <div class="text-danger error-up_img"></div>
                            </div>
                        </div>

                        <p class="mt-4">SEO Part</p>
                        <hr />

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="make">SEO Description</label>
                                    <textarea name="seo_description" id="seo_description"
                                        class="form-control seo_description" style="height: 37px;" cols="30"
                                        rows="10"></textarea>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="make">SEO Keyword</label>
                                    <input type="text" class="form-control keyword" id="up_keyword" placeholder="Use coma after the word">
                                    <div id="up_keywordTags"></div>
                                    <input type="hidden" name="up_keywords[]" id="up_hiddenKeyword">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="float-right mt-4 btn btn-primary"
                            id="update_button">Update</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- news edit modal close --}}


    {{-- News show modal --}}
    <div class="modal fade" id="newsShowModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cache Zip Code View</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 style="font-weight:bold; margin-top:10px" id="news-title"></h4>
                    <textarea name="news-des" id="news-des" cols="10" rows="10" class="form-control" disabled></textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- End add user modal --}}


    <div class="col-md-12">
        <section class="content">
            <!-- Default box -->
            <div class="card">
                <div class="card-header">

                    <h3 class="card-title">Cache List</h3>

                    {{--<a href="" class="float-right btn btn-primary btn-sm" data-toggle="modal"
                        data-target="#blogssAddModal"> <i class="fas fa-plus-circle"></i> Make All Cache</a>--}}

                        <!-- For links -->
                        <a href="#" class="float-right btn btn-danger btn-sm mr-2" id="deleteAllBtn">
                            <i class="fas fa-trash-alt mr-1"></i> Clear All Cache
                        </a>
                        <a href="#" class="float-right btn btn-primary btn-sm mr-2" id="runAllBtn">
                            <i class="fas fa-sync-alt mr-1"></i> Regenerate All Cache
                        </a>

                </div>

                <!-- Filter Section -->
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label for="dealerState">Cache State : </label>
                            <select class="form-control submitable" id="dealerState">
                                <option value="">Choose State</option>
                                @foreach ($inventory_dealer_state as $stateData => $index)
                                <option value="{{ $stateData }}">{{ $stateData }}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                        </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped news_table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Command</th>
                                <th>Created Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
    // all necessary code start here
    // news add data

    $(document).ready(function() {


        $(function() {
            var route = "{{ route('admin.cache-commands.index') }}";
            console.log(route);
            var table = $('.news_table').DataTable({

                dom: "lBfrtip",
                buttons: ["copy", "csv", "excel", "pdf", "print"],

                pageLength: 25,
                processing: true,
                serverSide: true,
                searchable: true,
                "ajax": {
                    "url": route,
                    "datatype": "json",
                    "dataSrc": "data",
                    "data": function(data) {
                        data.dealer_state = $('#dealerState').val();
                    }
                },

                drawCallback: function(settings) {

                    $('#is_check_all').prop('checked', false);

                },

                columns: [{
                        name: 'DT_RowIndex',
                        data: 'DT_RowIndex',
                        sWidth: '3%'
                    },

                    {
                        data: 'city',
                        name: 'city',
                        sWidth: '20%'
                    },
                    {
                        data: 'state',
                        name: 'state',
                        sWidth: '10%'
                    },
                    {
                        data: 'command',
                        name: 'command',
                        sWidth: '30%'
                    },
                    {
                        data: 'date',
                        name: 'date',
                        sWidth: '10%'
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
        $(document).on('submit', '#newsAdd', function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            $('#news_submit_button').text('Loading...');
            var errorFields = ['description', 'title'];
            errorFields.forEach(function(field) {
                $('#' + field + '-error').text('');
            });
            var form = this;

            $.ajax({
                processData: false,
                contentType: false,
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                success: function(res) {
                    console.log(res);
                    $('.error-message').html('');

                    if (res.errors) {
                        // Display validation errors dynamically
                        $.each(res.errors, function(key, value) {
                            $('#' + key + '-error').html(value[0]);
                        });
                        $('#news_submit_button').text('Submit');
                    }

                    if (res.status === 'success') {

                        $('.news_table').DataTable().draw(false);


                        // Reset CKEditor content
                        if (editorInstance) {
                            editorInstance.setData('');
                        }


                        $('#imagePreviewContainer').html('');
                        form.reset();
                        $('#news_submit_button').text('Submit');
                        toastr.success(res.message);
                        $('#blogssAddModal').modal('hide');
                    }


                },

                error: function(xhr) {
                    // Handle error response
                    var errors = xhr.responseJSON.errors;

                    // Display validation errors dynamically
                    $.each(errors, function(key, value) {
                        $('#' + key + '-error').text(value[0]);
                    });
                    $('#news_submit_button').text('Submit');
                }
            });
        });


        // news delete
        $(document).on('click', "#news_delete", function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            $.confirm({
                title: 'Delete Confirmation',
                content: 'Are you sure?',
                buttons: {
                    cancel: {
                        text: 'No',
                        btnClass: 'btn-danger',
                        action: function() {
                            // Do nothing on cancel
                        }
                    },
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-primary',
                        action: function() {
                            // Use the 'id' from the outer scope
                            $.ajax({
                                url: "{{ route('admin.blog.delete') }}",
                                type: 'post',
                                data: {
                                    id: id
                                },
                                success: function(response) {
                                    if (response.status == "success") {
                                        toastr.success(response.message);
                                        $('.news_table').DataTable().draw(
                                            false);
                                    }
                                },
                                error: function(error) {
                                    // Show Toastr error message
                                    toastr.error(error.responseJSON.message);
                                }
                            });
                        }
                    }
                }
            });
        });

        // news show

        $(document).on('click', ".single-news-show", function(e) {
            e.preventDefault();
            let id = $(this).data('id');

            $.ajax({
                url: "{{ route('admin.single.cache.view') }}",
                method: 'get',
                data: {
                    id: id
                },
                success: function(res) {

                    console.log(res.data);
                    $('#newsShowModal').modal('show');
                    $('#news-title').html(res.data.name);
                    $('#news-des').html(res.data.description);
                },
                error: function(error) {
                    console.error('Error in AJAX request:', error);
                }
            });
        });

        let editor;

        $(document).on('click', '.editBtn', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let category_id = $(this).data('category_id');
            let sub_category_id = $(this).data('sub_category_id');
            let title = $(this).data('title');
            let sub_title = $(this).data('sub_title');
            let description = $(this).data('description');
            let status = $(this).data('status');
            let seo_description = $(this).data('seo_description');
            let seo_keyword = $(this).data('seo_keyword');
            let image = $(this).data('image');

            $('.imagePreview').attr('src', "{{ asset('/frontend/assets/images/blog') }}" + '/' + image);

            var tempElement = document.createElement('div');
            tempElement.innerHTML = description;
            var textOnlyDescription = tempElement.textContent || tempElement.innerText || '';

            $('#news_id').val(id);

            $('#edit_category_id').val(category_id);
            $('#edit_sub_category_id').val(sub_category_id);

            $('#up_title').val(title);
            $('#up_sub_title').val(sub_title);
            $('#up_status').val(status);
            $('#news_edit').text(textOnlyDescription);
            $('.seo_description').text(seo_description);
            $('.keyword').val(seo_keyword);
            $('.update_image').attr('src', image);
            $('#newsEdit').modal('show');
            if (editor) {
                editor.destroy();
            }

            ClassicEditor.create(document.querySelector('#news_edit'))
                .then(newEditor => {
                    editor = newEditor;
                    editor.setData(description);
                })
                .catch(error => {
                    console.error(error);
                });
        });



        // news edit code
        $(document).on('submit', '#NewsEditFrom', function(e) {
            e.preventDefault();

            var formData = new FormData($(this)[0]);
            $('#update_button').text('Loading...');

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
                    $('#newsEdit').modal('hide');
                    $('.news_table').DataTable().draw(false);
                    $('#update_button').text('Update');
                },
                error: function(error) {
                    console.log(error);
                    $('#update_button').text('Update');
                }
            });
        });


        // status change
        $(document).on('change', '.action-select', function(e) {
            e.preventDefault();
            let id = $(this).data('id');


            $.ajax({
                url: "{{ route('admin.blogs.status.change') }}",
                method: "post",
                data: {
                    id: id
                },
                success: function(res) {
                    console.log(res)
                    toastr.success(res.message);
                    $('.news_table').DataTable().draw(false);
                }
            })
        });




        // image show and remove
        $(document).ready(function() {
            // When the file input changes
            $("#image").change(function() {
                readURL(this);
            });

            // Function to read the URL and display the image preview
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        // Display the image preview
                        $("#imagePreview").attr("src", e.target.result).show();

                        // Show the remove button
                        $("#removeImageButton").show();

                    };

                    // Read the file as a data URL
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Click event for the remove button
            $("#removeImageButton").click(function() {
                // Clear the file input and hide the image preview
                $("#image").val("");
                $("#imagePreview").attr("src", "").hide();
                // Hide the remove button
                $(this).hide();
            });
        });

        $(document).ready(function() {
            // When the file input changes
            $("#up_img").change(function() {
                readURL(this);
            });

            // Function to read the URL and display the image preview
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        // Display the image preview
                        $("#upimagePreview").attr("src", e.target.result).show();

                        // Show the remove button
                        $("#UpremoveImageButtonnews").show();
                        $(".imagePreview").hide();

                    };

                    // Read the file as a data URL
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Click event for the remove button
            $("#UpremoveImageButtonnews").click(function() {
                // Clear the file input and hide the image preview
                $("#up_img").val("");
                $("#upimagePreview").attr("src", "").hide();
                // Hide the remove button
                $(this).hide();
            });
        });




    });

    // all necessary code end  here


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




    // add meta tag javascript

    document.getElementById("keyword").addEventListener("keyup", function(event) {
        if (event.key === "Enter" || event.key === ",") {
            event.preventDefault();
            var keywords = document.getElementById("keyword").value.trim().split(",");
            var keywordTags = document.getElementById("keywordTags");

            for (var i = 0; i < keywords.length; i++) {
                var keyword = keywords[i].trim();
                if (keyword !== "") {
                    var tagElement = document.createElement("span");
                    tagElement.textContent = keyword;
                    tagElement.className = "badge bg-primary me-2";

                    // Add a close button to each tag
                    var closeButton = document.createElement("button");
                    closeButton.innerHTML = "&times;"; // Close icon (X)
                    closeButton.className = "btn-close";
                    closeButton.setAttribute("aria-label", "Close");
                    closeButton.addEventListener("click", function() {
                        this.parentNode.remove(); // Remove the tag when the close button is clicked
                        updateHiddenKeywordInput(); // Update hidden input when removing a tag
                    });

                    tagElement.appendChild(closeButton);
                    keywordTags.appendChild(tagElement);
                }
            }
            updateHiddenKeywordInput(); // Update hidden input with all keywords
            document.getElementById("keyword").value = ""; // Clear input after adding tags
        }
    });

    function updateHiddenKeywordInput() {
        var tags = document.querySelectorAll("#keywordTags .badge");
        var keywordsArray = [];
        tags.forEach(function(tag) {
            keywordsArray.push(tag.textContent.slice(0, -1).trim()); // Remove the close button text (X)
        });
        document.getElementById("hiddenKeyword").value = keywordsArray.join(","); // Update hidden input
    }



    // edit meta tag javascript

    document.getElementById("up_keyword").addEventListener("keyup", function(event) {
        // alert('ok');
        if (event.key === "Enter" || event.key === ",") {
            event.preventDefault();
            var keywords = document.getElementById("up_keyword").value.trim().split(",");
            var keywordTags = document.getElementById("up_keywordTags");
            var hiddenKeywordInput = document.getElementById("up_hiddenKeyword");

            for (var i = 0; i < keywords.length; i++) {
                var keyword = keywords[i].trim();
                if (keyword !== "") {
                    var tagElement = document.createElement("span");
                    tagElement.textContent = keyword;
                    tagElement.className = "badge bg-primary me-2 hh";

                    // Add a close button to each tag
                    var closeButton = document.createElement("button");
                    closeButton.innerHTML = "&times;"; // Close icon (X)
                    closeButton.className = "btn-close";
                    closeButton.setAttribute("aria-label", "Close");
                    closeButton.addEventListener("click", function() {
                        this.parentNode.parentNode.removeChild(this
                            .parentNode); // Remove the tag when the close button is clicked
                        edit_updateHiddenKeywordInput(); // Update hidden input when removing a tag
                    });
                    tagElement.appendChild(closeButton);

                    keywordTags.appendChild(tagElement);
                }
            }
            edit_updateHiddenKeywordInput(); // Update hidden input with all keywords
            document.getElementById("up_keyword").value = ""; // Clear input after adding tags
        }
    });

    function edit_updateHiddenKeywordInput() {
        var tags = document.querySelectorAll("#up_keywordTags .badge");
        var keywordsArray = [];
        tags.forEach(function(tag) {
            keywordsArray.push(tag.textContent);
        });
        document.getElementById("up_hiddenKeyword").value = keywordsArray.join(",");
    }
</script>

<script>
    $(document).ready(function() {

        // $('#runAllBtn').click(function() {
        //     if (confirm('Are you sure you want to run all cache commands?')) {
        //         $.post('{{ route("admin.cache-commands.run-all") }}', {
        //             _token: '{{ csrf_token() }}'
        //         }, function(response) {
        //             if (response.success) {
        //                 toastr.success(response.message);
        //                 $('.news_table').DataTable().draw(false);
        //             }
        //         });
        //     }
        // });

        // Run all commands
        $('#runAllBtn').click(function(e) {
            e.preventDefault(); // Prevent default button behavior

            if (confirm('Are you sure you want to run all cache commands?')) {
                // Store original button HTML
                var originalHtml = $(this).html();

                // Add loading state
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                $(this).prop('disabled', true);

                $.post('{{ route("admin.cache-commands.run-all") }}', {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('.news_table').DataTable().draw(false);
                    }
                })
                .always(function() {
                    // Restore original button state whether success or fail
                    $('#runAllBtn').html(originalHtml);
                    $('#runAllBtn').prop('disabled', false);
                })
                .fail(function(xhr) {
                    // Handle errors if needed
                    toastr.error(xhr.responseJSON.message || 'An error occurred');
                });
            }
        });

        // $('#deleteAllBtn').click(function() {
        //     if (confirm('Are you sure you want to run all cache commands?')) {
        //         $.post('{{ route("admin.cache-commands.delete-all") }}', {
        //             _token: '{{ csrf_token() }}'
        //         }, function(response) {
        //             if (response.success) {
        //                 toastr.success(response.message);
        //                 $('.news_table').DataTable().draw(false);
        //             }
        //         });
        //     }
        // });

        // Run All Cache Commands

        // Delete All Cache Commands
        $(document).on('click', '.delete-cache', function() {
            var button = $(this); // Store button reference
            var id = button.data('id');
            var url = '{{ route("admin.cache-commands.delete-cache", ["id" => ":id"]) }}'.replace(':id', id);
            var originalHtml = button.html(); // Store original button content

            if (confirm('Are you sure you want to delete this cache?')) {
                // Add loading state
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('.news_table').DataTable().draw(false);
                        } else {
                            toastr.error(response.message || 'Failed to delete cache');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON?.message || 'Failed to delete cache'));
                    },
                    complete: function() {
                        // Restore original button state
                        button.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });

        // Delete All Cache Commands
        $('#deleteAllBtn').click(function(e) {
            e.preventDefault(); // Prevent default behavior

            if (confirm('Are you sure you want to delete all cache commands?')) {
                // Store original button HTML
                var originalHtml = $(this).html();

                // Add loading state
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
                $(this).prop('disabled', true);

                $.post('{{ route("admin.cache-commands.delete-all") }}', {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('.news_table').DataTable().draw(false);
                    } else {
                        toastr.error(response.message || 'Failed to delete cache commands');
                    }
                })
                .always(function() {
                    // Restore original button state
                    $('#deleteAllBtn').html(originalHtml);
                    $('#deleteAllBtn').prop('disabled', false);
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'An error occurred while deleting cache commands');
                });
            }
        });

        $(document).on('click', '.run-command', function() {
            var button = $(this); // Store button reference
            var id = button.data('id');
            var url = '{{ route("admin.cache-commands.run", ["id" => ":id"]) }}'.replace(':id', id);
            var originalHtml = button.html(); // Store original HTML

            if (confirm('Are you sure you want to run this command?')) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        // Show loading indicator using stored button reference
                        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Running...');
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('.news_table').DataTable().draw(false);
                        } else {
                            toastr.error(response.message || 'Command execution failed');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON?.message || 'Command execution failed'));
                    },
                    complete: function() {
                        // Re-enable button using stored button reference
                        button.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });
    })
</script>
@endpush
