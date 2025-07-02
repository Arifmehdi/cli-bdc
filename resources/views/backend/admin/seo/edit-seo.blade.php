@extends('backend.admin.layouts.master')
@push('css')
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session()->get('message') }}</div>
            @endif
            <section class="content">

                <div class="col-md-12">
                    <div class="card">

                        <div class="card-header">
                            <form action="{{ route('admin.frontend.update.seo',$seo->id)}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <h4 class="mb-4">1. Default Meta Tag.</h4>
                                <hr>
                                <div class="mb-3 row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="make">Name</label>
                                            <input type="text" name="name" class="form-control" id="name"
                                                value="{{ isset($seo) ? $seo->name : old('name') }}">
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
                                                 id="keyword" value="{{ isset($seo) ? $seo->keyword : old('keyword') }}">
                                                <div id="keywordTags"></div>
                                                <input type="hidden" name="keywords[]" id="hiddenKeyword" value="{{ isset($seo) ? $seo->keyword : old('keyword') }}">
                                        </div>

                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="make">Description</label>
                                            <input type="text" name="description" class="form-control" id="description"
                                             value="{{ isset($seo) ?  $seo->description : old('description') }}">

                                        </div>
                                    </div>


                                 <h4 class="mb-4">2. Open Graph Protocol (OGP) meta tags for Facebook, LinkedIn, Instagram.</h4>
                                 <hr>
                                <div class="mb-3 row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">og:title</label>
                                            <input type="text" name="og_title" class="form-control" id="og_title"
                                                value="{{ isset($seo) ?  $seo->og_title : old('og_title') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">

                                        <div class="form-group">
                                            <label for="make">og:description</label>
                                            <input type="text" name="og_description" class="form-control"
                                                value="{{ isset($seo) ?  $seo->og_description : old('og_description') }}" id="og_description">
                                        </div>

                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">og:image</label>
                                            <input type="file" name="og_img" class="form-control" id="og_img">
                                            @if ($seo->og_img)
                                            <img src="{{ asset('/seo'.'/'. $seo->og_img) }}" width="300px" height="300px" alt="">
                                            @endif


                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">og:url</label>
                                            <input type="url" name="og_url" class="form-control" id="og_url"
                                                value="{{ isset($seo) ?  $seo->og_url : old('og_url') }}">

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">og:type</label>
                                            <input type="text" name="og_type" class="form-control" id="og_type"
                                                value="{{ isset($seo) ?  $seo->og_type : old('og_type') }}">

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">og:site_name</label>
                                            <input type="text" name="og_site_name" class="form-control" id="og_site_name"
                                                value="{{ isset($seo) ?  $seo->og_site_name : old('og_site_name') }}">

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">og:locale</label>
                                            <input type="text" name="og_locale" class="form-control" id="og_locale"
                                                value="{{ isset($seo) ?  $seo->og_locale : old('og_locale') }}">

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">fb:app_id</label>
                                            <input type="text" name="app_id" class="form-control" id="app_id"
                                                value="{{ isset($seo) ?  $seo->app_id : old('app_id') }}">

                                        </div>
                                    </div>


                                </div>

                                 <h4 class="mb-4">3. Open Graph Protocol (OGP) meta tags for Twitter.</h4>
                                 <hr>
                                <div class="mb-3 row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">twitter:card</label>
                                            <input type="text" name="twitter_card" class="form-control" id="twitter_card"
                                                value="{{ isset($seo) ?  $seo->twitter_card : old('twitter_card') }}">

                                        </div>
                                    </div>
                                    <div class="col-md-3">

                                        <div class="form-group">
                                            <label for="make">twitter:title</label>
                                            <input type="text" name="twitter_title" class="form-control" id="twitter_title"
                                                value="{{ isset($seo) ?  $seo->twitter_title : old('twitter_title') }}" id="keyword">

                                        </div>

                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">twitter:description</label>
                                            <input type="text" name="twitter_description" class="form-control" id="twitter_description"
                                                value="{{ isset($seo) ?  $seo->twitter_description : old('twitter_description') }}">

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">twitter:image</label>
                                            <input type="file" name="twitter_img" class="form-control" id="twitter_img">
                                            @if ($seo->twitter_img)
                                            <img src="{{ asset('/seo'.'/' . $seo->twitter_img) }}" width="300px" height="300px" alt="">
                                            @endif

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">twitter:site</label>
                                            <input type="text" name="twitter_site" class="form-control" id="twitter_site"
                                                value="{{ isset($seo) ?  $seo->twitter_site : old('twitter_site') }}">

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="make">twitter:creator</label>
                                            <input type="text" name="twitter_creator" class="form-control" id="twitter_creator"
                                                value="{{ isset($seo) ?  $seo->twitter_creator : old('twitter_creator') }}">

                                        </div>
                                    </div>

                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="make">GTM( Google Tag Manager)  <i data-toggle="tooltip" data-placement="top" title="Paste Google Tag Script " class="fa fa-question"></i></label>
                                            <textarea name="gtm" id="gtm" cols="5" rows="5"  class="form-control" placeholder="Type Google Tag Manager Script ">{{ isset($seo) ?  $seo->gtm : '' }}</textarea>

                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="submit" value="Update" class="btn btn-success float-middle px-5" style="margin-top:31px">

                                        </div>
                                    </div>
                                </div>


                            </form>
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

</script>
@endpush
