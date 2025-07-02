<!-- Modal -->
<div class="modal fade" id="QuickModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header d-flex justify-content-center align-items-center modal-header-custom">
                <p class="modal-title mb-3 text-center">Quick View</p>
                <button type="button" class="btn-close position-absolute end-0 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-xl-9 col-lg-9 col-md-10 col-sm-12">
                        <!-- Swiper -->
                        <div class="swiper mySwiper quickSwiper">
                            <div class="swiper-wrapper quick-swiper">
                                <!-- Slides will be dynamically added here -->
                            </div>
                            <div class="swiper-button-next">
                                <i class="fa fa-angle-right"></i>
                            </div>
                            <div class="swiper-button-prev">
                                <i class="fa fa-angle-left"></i>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Data -->
                <div class="row justify-content-center mt-3">
                    <div class="col-xl-9 col-lg-9 col-md-10 col-sm-12">
                        <div id="quick_data" class="w-100">
                            <!-- Content will be appended here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer" id="quick_footer">
                <!-- Footer content will be appended here -->
            </div>
        </div>
    </div>
</div>
