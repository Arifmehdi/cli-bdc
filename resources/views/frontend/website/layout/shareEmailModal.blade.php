<!-- Modal -->
<div class="modal fade" id="ShareModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div style="margin-top:-70px" class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header d-flex justify-content-center align-items-center mb-5">
                <p style="font-size:19px; margin-top:10px; fone-weight:600" class="modal-title mb-3">Share This Listing</p>
                <button style="margin-top:-15px" type="button" class="btn-close position-absolute end-0 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="shareEmailSubmitBtn" action="{{ route('frontend.share.email') }}" method="post">
                    @csrf

                    <input type="hidden" id="inv_id" name="id">

                   <input style="border-radius:7px; padding-top:21px; padding-bottom:21px; margin-bottom:5px; height:50px"
                        name="name" id="name" type="text" placeholder="Enter your name" class="form-control">
                        <span class="text-danger error-message mb-1" id="name-error"></span>
                    <input style="border-radius:7px; padding-top:21px; padding-bottom:21px; margin-bottom:5px; margin-top:20px;  height:50px"
                        name="email" id="email" type="text" placeholder="Enter receiver email" class="form-control">
                        <span class="text-danger error-message mb-1" id="email-error"></span>
                    <button
                        style="width:100%; background:black; color:white;border-radius:7px; padding-top:10px; padding-bottom:10px; font-size:16px; border:1px solid black; margin-top:20px;  height:50px "
                        class="mb-4" id="share-btn" >Submit</button>
                </form>



            </div>

        </div>
    </div>
</div>
