
<section style="padding-top: 5px !important; padding-bottom:3px !important" class="sptb2">
    <div style="border-radius:5px" class="container bg-white p-3">
        <div class="text-center">
            <h5 style="font-weight:500; margin-bottom:45px; margin-top:17px">Search Used Cars
                in Popular Cities</h5>
        </div>
        <div style="margin: 0 auto" class="row mb-2">
            <div class="col-md-4 col-lg-3 col-sm-4 mb-4">
                <a href="{{ route('auto', ['zip' => '78702', 'home2' => true]) }}"
                    style="font-size: 14px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
                    class="city" data-zip="78702">Used Cars in Austin, TX</a>
            </div>
            <div class="col-md-4 col-lg-3 col-sm-4 mb-4">
                <a href="{{ route('auto', ['zip' => '75241', 'home2' => true]) }}"
                    style="font-size: 14px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
                    class="city" data-zip="75241">Used Cars in Dallas, TX</a>
            </div>
            <div class="col-md-4 col-lg-3 col-sm-4 mb-4">
                <a href="{{ route('auto', ['zip' => '77007', 'home2' => true]) }}"
                    style="font-size: 14px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
                    class="city" data-zip="77007">Used Cars in Houston, TX</a>
            </div>
            <div class="col-md-4 col-lg-3 col-sm-4 mb-4">
                <a href="{{ route('auto', ['zip' => '78205', 'home2' => true]) }}"
                    style="font-size: 14px; border-bottom:1px solid rgb(18, 176, 197); color:rgb(18, 176, 197) !important"
                    class="city" data-zip="78205">Used Cars in San Antonio, TX</a>
            </div>
        </div>
    </div>
</section>
