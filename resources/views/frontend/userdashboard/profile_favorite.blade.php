@extends('frontend.userdashboard.master')
@section('content-user')
    <div class="card mb-0">
        <div style="background:white" class="card-header">
            <h3 class="card-title">My Favorite</h3>
        </div>
        <div class="card-body">
            <div class="my-favadd table-responsive userprof-tab">
                <table class="table table-bordered table-hover mb-0 text-nowrap">
                    <thead>
                        <tr>

                            <th>Inventory</th>

                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($favorites as $favorite)
                            <tr>

                                <td>
                                    <div class="media mt-0 mb-0">
                                        <div class="card-aside-img">
                                            {{-- @php
                                $image_obj =  $favorite->inventory->local_img_url ?? '';
                               $images = explode(',',$favorite->inventory->local_img_url);

                               $image_obj =   $favorite->inventory->local_img_url;
                                    $image_splice = explode(',',$image_obj);
                                    $image = str_replace(["[", "'"], "", $image_splice[0]);
                             @endphp
                              @if ($image_obj != '')
                              <img src="{{ asset('frontend').'/'. $image }}" alt="img" class="br-3">
                              @else
                              <img src="{{ asset('frontend/uploads/NotFound.png') }}" alt="img" class="br-3">
                              @endif --}}

                                            @php
                                                $image_obj = $favorite->inventory->local_img_url ?? '';
                                                $image = '';

                                                if ($image_obj != '') {
                                                    $image_splice = explode(',', $image_obj);
                                                    $image = str_replace(['[', ']', "'"], '', $image_splice[0]);
                                                }
                                            @endphp

                                            @php
                                                $vin_string_replace = str_replace(' ', '', $favorite->inventory->vin);
                                                $route_string = str_replace(
                                                    ' ',
                                                    '',
                                                    $favorite->inventory->year .
                                                        '-' .
                                                        $favorite->inventory->make .
                                                        '-' .
                                                        $favorite->inventory->model .
                                                        '-in-' .
                                                        $favorite->inventory->dealer_city .
                                                        '-' .
                                                        $favorite->inventory->dealer_state,
                                                );
                                            @endphp

                                            @if ($image_obj != '')

                                                <a href="{{ route('auto.details',['vin' =>$vin_string_replace, 'param' => $route_string]) }}" class="text-dark"></a>
                                        <img src="{{ asset('frontend/' . $image) }}" alt="img" class="br-3">
                                            @else
                                                <img src="{{ asset('frontend/uploads/NotFound.png') }}" alt="img"
                                                    class="br-3">
                                            @endif


                                        </div>
                                        <div class="media-body">
                                            <div class="card-item-desc ms-4 p-0 mt-2">
                                                @php
                                                    $dato_formate = \Carbon\Carbon::parse(
                                                        $favorite->inventory->created_date,
                                                    );
                                                @endphp
                                                <a href="{{ route('auto.details',['vin' =>$vin_string_replace, 'param' => $route_string]) }}" class="text-dark">
                                                    <h4 class="font-weight-semibold mt-1">{{ $favorite->inventory->year . ' ' . $favorite->inventory->make . ' ' . $favorite->inventory->model}}</h4>
                                                </a>

                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="font-weight-semibold fs-16">{{ $favorite->inventory->price_formate }}</td>
                                <td>
                                    <a href="javascript:void(0);" class="badge badge-success">Active</a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-primary btn-sm text-white DeleteFavorite"
                                        data-productid="{{ $favorite->inventory->id }}"><i class="fa fa-trash"></i></a>

                                </td>

                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $('.DeleteFavorite').on('click', function(e) {
            e.preventDefault();
            var inventory_id = $(this).data('productid');
            var url = "{{ route('buyer.delete.favorite') }}";
            $.confirm({
                'title': 'Delete Confirmation',
                'message': 'Are you sure?',
                'buttons': {
                    'No': {
                        'btnClass': 'no btn-danger',
                        'action': function() {}
                    },
                    'Yes': {
                        'btnClass': 'btn-secondary',
                        'action': function() {
                            // $('#delete_form').submit();
                            $.ajax({
                                url: url,
                                type: 'post',
                                data: {
                                    inventory_id: inventory_id,

                                },

                                success: function(response) {


                                    if (response.status == 'success') {

                                        toastr.success(response.message);
                                        location.reload();
                                    } else {
                                        toastr.success(response.message);
                                    }

                                }


                            });
                        }
                    },

                }
            });
        });
    </script>
@endpush
