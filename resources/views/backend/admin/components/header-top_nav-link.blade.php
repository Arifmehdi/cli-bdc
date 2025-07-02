 <!-- Right navbar links -->
 <ul class="navbar-nav ml-auto">
    <!-- Navbar Search -->
    <li class="nav-item">
      <a class="nav-link" data-widget="navbar-search" href="#" role="button">
        <i class="fas fa-search"></i>
      </a>
      <div class="navbar-search-block">
        <form class="form-inline">
          <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-navbar" type="submit">
                <i class="fas fa-search"></i>
              </button>
              <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </li>

    <!-- Messages Dropdown Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">


        <i  class="fa fa-cart-plus"></i>
        <span class="badge badge-danger navbar-badge" id="cart-count">{{ (isset($invoices)) ? count($invoices) : '' }}</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

        <div class="cart-content" id="cart-content" style="height: 300px; overflow:auto">
            <div style="height: 260px; overflow-y: auto;" id="invoice-section"></div>
        </div>

      </div>
    </li>
    <!-- Notifications Dropdown Menu -->

    <li class="nav-item dropdown">

        <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-bell"></i>
            <span style="color:black" class="badge badge-warning navbar-badge">{{$number}}</span>
          </a>




      <div style="height:400px; overflow-y: scroll" class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span  class="dropdown-item dropdown-header">{{$number}} Notifications</span>
        <div class="dropdown-divider"></div>
        @foreach ($notifications as $notify )
        <a href="{{ $notify->call_back_url .'/'. $notify->id }}" class="dropdown-item">
        <i class="fas fa-envelope mr-2"></i> <span style="font-size:14px !important">{{ $notify->title }}</span>
        <span style="font-size:11px !important; margin-top:5px" class="float-right text-muted text-sm">{{ \Carbon\Carbon::parse($notify->created_at)->diffForHumans() }}</span>
    </a>
@endforeach


        <div class="dropdown-divider"></div>


      </div>
    </li>
    @php
    $nameBeforeIn = \Illuminate\Support\Str::before(auth()->user()->name, 'in');
     @endphp
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false" title="{{ $nameBeforeIn}}">
            {{ \Illuminate\Support\Str::limit(auth()->user()->name, 20) }}
        </a>

        <ul class="dropdown-menu">
          <x-profile href="{{ auth()->user()->hasAllaccess() ? route('admin.profile')  : route('dealer.profile') }}"/>

          <form method="POST" action="{{ route('logout') }}">
            @csrf

            <x-responsive-nav-link :href="route('logout')" class="dropdown-item"
                    onclick="event.preventDefault();
                                this.closest('form').submit();">
                {{ __('Log Out') }}
            </x-responsive-nav-link>
        </form>
        </ul>
      </li>


  </ul>

  @push('js')
  <script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // $(document).on('click', '.adiiifLead', function () {
    //     alert('ok');
    // });

    $(document).on('click', '.clearAllBtn', function() {
                $.ajax({
                    url: "{{ route('admin.cart.deleteAll') }}", // Updated route name
                    method: "post",
                    data: {}, // No need to send any specific data for deleting all items
                    success: function(res) {
                        console.log(res);
                        if (res.status == 'success') {
                            toastr.success(res.message, {
                                timeOut: 1000
                            });
                            // Remove all rows from the cart table or update your UI accordingly
                            $('.cart-table tbody tr').remove();
                            updateCartData();
                        }
                    }
                });
            });
    $(document).ready(function() {
        $(document).on('click', '.deleteCart', function(e) {
                e.preventDefault();

                var clickedButton = $(this);
                var id = clickedButton.data('id');
                var type = clickedButton.data('type') ?? '';
                var invoice_id = clickedButton.data('invoice_id') ?? '';
                // const price = $('#cost').val();
                // updateTotals(price);
                $.ajax({
                    url: "{{ route('admin.cart.data.delete') }}",
                    method: "post",
                    data: {
                        id: id,
                        type:type,
                        invoice_id:invoice_id
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            toastr.success(res.message, {
                                timeOut: 1000
                            });
                            clickedButton.closest('tr').remove();
                            updateCartData();

                        }
                    }
                });
            });



            function updateTotals(price) {

            const subtotalElem = document.getElementById('subtotal');
            const totalElem = document.getElementById('total_price');
            let subtotal = parseFloat(subtotalElem.value.replace('$', ''));
            let total = parseFloat(totalElem.value.replace('$', ''));

            // Subtract the price of the deleted item from the subtotal and total
            subtotal -= price;
            total -= price;

            // Update the values in the inputs
            subtotalElem.value = `$${subtotal.toFixed(2)}`;
            totalElem.value = `$${total.toFixed(2)}`;
        }



    });
</script>
  @endpush
