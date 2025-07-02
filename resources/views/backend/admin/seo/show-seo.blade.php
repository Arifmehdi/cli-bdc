@extends('backend.admin.layouts.master')
@push('css')
@endpush
@section('content')
    <div class="row" >
        <div class="col-md-12">
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session()->get('message') }}</div>
            @endif
            <section class="content">


                <div class="col-md-12">
                    <div class="card">

                        <div class="card-header">
                            <!-- <form action="{{ route('admin.frontend.store.seo')}}" method="POST" enctype="multipart/form-data"> -->
                            <form action="{{ route('admin.frontend.update.seo',$seo_first->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                 <h4>1. Default Meta Tag.</h4>
                                 <hr>
                                <div class="mb-3 row">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="make">Title</label>
                                            <input type="text" name="name" class="form-control" id="name"
                                                value="{{ isset($seo_first) ? $seo_first->name : old('name') }}">
                                            <span id="nameError" class="text-danger error-message"></span>
                                            @if ($errors->has('name'))
                                                <span class="text-danger error-message">{{ $errors->first('name') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label for="make">Keyword</label>
                                            <input type="text" class="form-control" id="keyword"
                                            name="keyword" value="{{ isset($seo_first) ? $seo_first->keyword : old('keyword') }}">
                                                <div id="keywordTags"></div>
                                                <input type="hidden" name="keywords[]" id="hiddenKeyword" value="{{ isset($seo_first) ? $seo_first->keyword : old('keyword') }}">
                                        </div>

                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="make">Description</label>
                                            <input type="text" name="description" class="form-control" id="description"
                                                value="{{ isset($seo_first) ?  $seo_first->description : old('description') }}">

                                            {{-- <textarea name="description" class="form-control" cols="50" rows="5"></textarea> --}}

                                        </div>
                                    </div>


                                </div>

                                 <h4>2. Open Graph Protocol (OGP) meta tags for Facebook, LinkedIn, Instagram.</h4>
                                 <hr>
                                <div class="mb-3 row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">og:title</label>
                                            <input type="text" name="og_title" class="form-control" id="og_title"
                                                value="{{ isset($seo_first) ?  $seo_first->og_title : old('og_title') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">

                                        <div class="form-group">
                                            <label for="make">og:description</label>
                                            <input type="text" name="og_description" class="form-control"
                                                id="og_description" value="{{ $seo_first->og_description ?? old('og_description') }}" id="og_description">
                                        </div>

                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">og:image</label>
                                            <input type="file" name="og_img" class="form-control" id="og_img">

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">og:url</label>
                                            <input type="url" name="og_url" class="form-control" id="og_url"
                                                value="{{ route('home') ?? old('og_url') }}">

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">og:type</label>
                                            <input type="text" name="og_type" class="form-control" id="og_type"
                                                value="{{'website'?? old('og_type')}}">

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">og:site_name</label>
                                            <input type="text" name="og_site_name" class="form-control" id="og_site_name"
                                                value="{{ env('APP_NAME') ?? 'Dream Best Car' }}">

                                        </div>
                                    </div>
                                    <div class="col-md-3" style="display:none">
                                        <div class="form-group">
                                            <label for="make">og:locale</label>
                                            <input type="text" name="og_locale" class="form-control" id="og_locale"
                                                value="{{ old('og_locale') }}">

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">fb:app_id</label>
                                            <input type="text" name="app_id" class="form-control" id="app_id"
                                                value="{{ $seo_first->app_id ??  old('app_id') }}">

                                        </div>
                                    </div>


                                </div>

                                 <h4>3. Open Graph Protocol (OGP) meta tags for Twitter.</h4>
                                 <hr>
                                <div class="mb-3 row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">twitter:card</label>
                                            <input type="text" name="twitter_card" class="form-control" id="twitter_card"
                                                value="{{ 'summary_large_image ' ?? old('twitter_card') }}">

                                        </div>
                                    </div>
                                    <div class="col-md-3">

                                        <div class="form-group">
                                            <label for="make">twitter:title</label>
                                            <input type="text" name="twitter_title" class="form-control" id="twitter_title"
                                                value="{{ $seo_first->twitter_title ?? old('twitter_title') }}">

                                        </div>

                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">twitter:description</label>
                                            <input type="text" name="twitter_description" class="form-control" id="twitter_description"
                                                value="{{ $seo_first->twitter_description ?? old('twitter_description') }}">

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">twitter:image</label>
                                            <input type="file" name="twitter_img" class="form-control" id="twitter_img">

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">twitter:site</label>
                                            <input type="text" name="twitter_site" class="form-control" id="twitter_site"
                                                value="{{ $seo_first->twitter_site ??old('twitter_site') }}">

                                        </div>
                                    </div>
                                    <div class="col-md-3" style="display:none">
                                        <div class="form-group">
                                            <label for="make">twitter:creator</label>
                                            <input type="text" name="twitter_creator" class="form-control" id="twitter_creator"
                                                value="{{ old('twitter_creator') }}">

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="make">GTM( Google Tag Manager)  <i data-toggle="tooltip" data-placement="top" title="Paste Google Tag Script " class="fa fa-question"></i></label>
                                            <textarea name="gtm" id="gtm" cols="5" rows="5"  class="form-control" placeholder="Type Google Tag Manager Script ">{{$seo_first->gtm}}</textarea>

                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="submit" value="Update" class="btn btn-success float-middle px-5" style="margin-top:31px">

                                        </div>
                                    </div>

                                </div>


                            </form>
                        </div>

                        <div class="card-body" style='display:none'>

                            <table class="table table-striped">
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Keyword</th>
                                    <th>Action</th>
                                </tr>

                                @forelse ($seos as $seo)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{ $seo->name }}</td>
                                    <td>{{ $seo->description }}</td>
                                    <td>{{ $seo->keyword }}</td>
                                    <td><a href="{{ route('admin.frontend.edit.seo',$seo->id)}}" class="btn btn-sm btn-success" title="edit"><i class="fas fa-edit"></i></a> | <a href="#" title="delete" class="btn btn-danger btn-sm deleteSeo" data-id="{{$seo->id}}"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">No Seo Found</td>
                                </tr>
                                @endforelse

                            </table>

                        </div>
                    </div>
                </div>
                </form>
            </section>

        </div>
    </div>
@endsection
@push('js')
    <script>

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        document.getElementById("keyword").addEventListener("keyup", function(event) {
    if (event.key === "Enter" || event.key === ",") {
        event.preventDefault();
        var keywords = document.getElementById("keyword").value.trim().split(",");
        var keywordTags = document.getElementById("keywordTags");
        var hiddenKeywordInput = document.getElementById("hiddenKeyword");

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
                    this.parentNode.parentNode.removeChild(this.parentNode); // Remove the tag when the close button is clicked
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
        keywordsArray.push(tag.textContent);
    });
    document.getElementById("hiddenKeyword").value = keywordsArray.join(",");
}


$(document).on('click', ".deleteSeo", function(e){
    e.preventDefault();
    let id = $(this).data('id');
    $.confirm({
        title: 'Delete Confirmation',
        content: 'Are you sure?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-primary',
                action: function () {
                    // Do nothing on cancel
                }
            },
            confirm: {
                text: 'Yes',
                btnClass: 'btn-danger',
                action: function () {
                    // Use the 'id' from the outer scope
                    $.ajax({
                        url: "{{ route('admin.frontend.delete.seo') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                location.reload();
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
</script>

<script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        document.getElementById("keyword").addEventListener("keyup", function(event) {
    if (event.key === "Enter" || event.key === ",") {
        event.preventDefault();
        var keywords = document.getElementById("keyword").value.trim().split(",");
        var keywordTags = document.getElementById("keywordTags");
        var hiddenKeywordInput = document.getElementById("hiddenKeyword");

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
                    this.parentNode.parentNode.removeChild(this.parentNode); // Remove the tag when the close button is clicked
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
        keywordsArray.push(tag.textContent);
    });
    document.getElementById("hiddenKeyword").value = keywordsArray.join(",");
}

$(document).on('blur','#description',function(){
    var data = $('#description').val();
                $('#og_description').val(data);
                $('#twitter_description').val(data);
});
$(document).on('blur','#name',function(){
    var data = $('#name').val();
                $('#og_title').val(data);
                $('#twitter_title').val(data);
});

</script>
@endpush
