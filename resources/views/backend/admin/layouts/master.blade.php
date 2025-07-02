<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{csrf_token() }}">
  <title>
    @auth
        @if (Auth::user()->hasRole('dealer'))
            Dealer | Dashboard
        @else
            Admin | Dashboard
        @endif
    @else
        Dashboard
    @endauth
</title>


  @include('backend.admin.components.header-link')
  @stack('css')
  <style>
    /* Add styles for user list */
    .user-list {
        max-height: 300px;
        overflow-y: auto; /* Enables scrolling for large lists */
        margin-top: 10px;
    }

    .user-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #f1f1f1;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .user-item:hover {
        background-color: #f7f7f7;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-size: 16px;
        font-weight: bold;
    }

    .user-status {
        font-size: 12px;
        color: gray;
    }

    .user-status.online {
        color: green;
    }

    .user-status.offline {
        color: red;
    }

    /* Wrapper styling for better alignment */
    #messenger_wrapper {
        position: fixed; /* Ensures it's fixed relative to the viewport */
        bottom: 20px; /* Adds some padding from the bottom */
        padding: 10px; /* Adds space around the image */
        border-radius: 10px; /* Smooth corners for a modern look */
        right: 20px;
    }

    /* Image styling */
    #messanger_link img {
        height: 60px; /* Professional size */
        width: 60px; /* Maintain aspect ratio */
        margin: 0 auto; /* Ensures alignment within the wrapper */
        cursor: pointer; /* Pointer cursor for interactivity */
        z-index: 1100;
    }

    /* Modal styles */
    #messenger_modal {
        position: fixed;
        bottom: -400px; /* Start hidden below the viewport */
        right: 20px;
        width: 300px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
        overflow: hidden;
        transition: bottom 0.4s ease; /* Sliding effect */
        z-index: 1200;
    }

    #messenger_modal.active {
        bottom: 100px; /* Position it slightly above the image */
    }

    #messenger_modal_header {
        background-color: #0078FF; /* Messenger blue color */
        color: #fff;
        padding: 10px;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
    }

    #messenger_modal_content {
        padding: 15px;
    }

    #messenger_modal_close {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #fff;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
    }
</style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{asset('backend')}}/dist/img/AdminLTELogo.png" alt="Carbazar logo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

      </ul>

    @include('backend.admin.components.header-top_nav-link')


  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard')}}" class="brand-link">
      <img src="{{asset('frontend/assets/images/logos/1712380969.png')}}" alt="Carbazar Logo" class="brand-image img-circle elevation-3" style="opacity: .8;width: 75px;height: 109px;background: white;">
      <span class="brand-text font-weight-light">Dream Best Car</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="@if (Auth::user()->image)
          {{ asset('frontend/assets/images/' . Auth::user()->image) }}
       @else
          {{ asset('frontend/assets/images/profile.png') }}
       @endif" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          {{-- <a href="{{ route('dealer.profile')}}" class="d-block">{{ \Illuminate\Support\Str::limit(auth()->user()->name, 20) }}</a> --}}
          @php
          $nameBeforeIn = \Illuminate\Support\Str::before(auth()->user()->name, 'in');
        @endphp
          <a href="{{ auth()->user()->hasAllaccess() ? route('admin.profile')  : route('dealer.profile') }}" class="d-block" title="{{ $nameBeforeIn}}">
            {{ \Illuminate\Support\Str::limit(auth()->user()->name, 20) }}
        </a>

        </div>
      </div>


      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

       @include('backend.admin.components.dropdown-link')
       @include('backend.admin.components.group-dropdown-link')

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    @include('backend.admin.components.breadcumb')
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      @yield('content')
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


</div>
<!-- ./wrapper -->

<div id="messenger_modal">
    <div id="messenger_modal_header">
        Messenger
        <span id="messenger_modal_close">&times;</span>
    </div>
    <div id="messenger_modal_content">
        <p>Select a user to start a conversation:</p>
        <div class="user-list" id="user_list">
            <!-- User items will be dynamically loaded here -->
        </div>
    </div>
</div>

{{-- <div id="messenger_modal">
    <div id="messenger_modal_header">
        Messenger
        <span id="messenger_modal_close">&times;</span>
    </div>
    <div id="messenger_modal_content">
        <p>How can we assist you today?</p>
        <form>
            <textarea
                rows="3"
                placeholder="Type your message here..."
                style="width: 100%; border-radius: 5px; padding: 5px; border: 1px solid #ccc;"
            ></textarea>
            <button
                type="submit"
                style="margin-top: 10px; background-color: #0078FF; color: #fff; padding: 10px; border: none; border-radius: 5px; width: 100%;"
            >
                Send Message
            </button>
        </form>
    </div>
</div> --}}
<div id="messenger_wrapper">
    <a href="#" id="messanger_link">
        <img src="{{ asset('frontend/messenger.png') }}" alt="messenger image" title="Message us">
    </a>
</div>

@include('backend.admin.components.footer-link')
<script>

document.addEventListener('DOMContentLoaded', function () {
    const messengerLink = document.getElementById('messanger_link');
    const messengerModal = document.getElementById('messenger_modal');
    const modalClose = document.getElementById('messenger_modal_close');
    const userList = document.getElementById('user_list');

    // Example data for users
    const users = [
        { id: 1, name: 'John Doe', avatar: 'https://via.placeholder.com/40', status: 'online' },
        { id: 2, name: 'Jane Smith', avatar: 'https://via.placeholder.com/40', status: 'offline' },
        { id: 3, name: 'Alice Johnson', avatar: 'https://via.placeholder.com/40', status: 'online' },
        { id: 4, name: 'Bob Brown', avatar: 'https://via.placeholder.com/40', status: 'offline' },
    ];

    // Open the modal with sliding effect
    messengerLink.addEventListener('click', function () {
        messengerModal.classList.add('active');
        // loadUsers(); // Load the user list
        $.ajax({
            url:"{{route('messages.users')}}",
            method:"POST",
            success:function(res){
              if(res.status == 'success')
              {
                loadUsers(res.users);
              }else
              {
                toastr.danger('Something went wrong!')
              }
            }
        });
    });

    // Close the modal
    modalClose.addEventListener('click', function () {
        messengerModal.classList.remove('active');
    });

    // Optional: Close the modal when clicking outside of it
    window.addEventListener('click', function (event) {
        if (!messengerModal.contains(event.target) && !messengerLink.contains(event.target)) {
            messengerModal.classList.remove('active');
        }
    });

    // Load users into the modal
    function loadUsers(users) {
        const defaultAvatar = 'https://via.placeholder.com/40';
        userList.innerHTML = ''; // Clear previous list

        users.forEach(user => {
            // const userAvatar = user.image ? user.image : defaultAvatar;
            const userItem = document.createElement('div');
            userItem.className = 'user-item';
            userItem.innerHTML = `
                <img src="${defaultAvatar}" alt="${user.name}" class="user-avatar">
                <div class="user-info">
                    <div class="user-name">${user.name}</div>
                    <div class="user-status "></div>
                </div>
            `;
            userItem.addEventListener('click', function () {
                alert(`Start chatting with ${user.name}!`);
                // Replace this alert with navigation or chat initialization logic
            });
            userList.appendChild(userItem);
        });
    }
});

// document.addEventListener('DOMContentLoaded', function () {
//     const messengerLink = document.getElementById('messanger_link');
//     const messengerModal = document.getElementById('messenger_modal');
//     const modalClose = document.getElementById('messenger_modal_close');

//     // Open the modal with sliding effect
//     messengerLink.addEventListener('click', function () {
//         messengerModal.classList.add('active');
//     });

//     // Close the modal
//     modalClose.addEventListener('click', function () {
//         messengerModal.classList.remove('active');
//     });

//     // Optional: Close the modal when clicking outside of it
//     window.addEventListener('click', function (event) {
//         if (!messengerModal.contains(event.target) && !messengerLink.contains(event.target)) {
//             messengerModal.classList.remove('active');
//         }
//     });
// });



    function updateCartData() {
        $.ajax({
            url: "{{ route('admin.get.cart_item') }}",
            method: "GET", // Corrected the method to "GET"
            success: function(res) {
                console.log(res);
                if (res.status === 'success') {
                    var count = res.count;
                    console.log(count);
                    $('#cart-count').text(count);
                    $('#invoice-section').html(res.data); // Use .html() to replace the content
                }
                if (res.count == 0) {
                    $('.checkInvoiceNull').prop('disabled', true);
                }
            },
            error: function(err) {
                console.error(err);
            }
        });
    }

    $(document).ready(function() {
        updateCartData();

    });
</script>
<script>
        @if(Session::has('status'))
            var type = "{{ Session::get('status') }}";
            var message = "{{ Session::get('message') }}";

            switch(type){
                case 'success':
                    toastr.success(message);
                    break;
                case 'error':
                    toastr.error(message);
                    break;
                default:
                    toastr.info(message);
            }
        @endif
    </script>
<!-- jQuery -->

@stack('js')
</body>
</html>
