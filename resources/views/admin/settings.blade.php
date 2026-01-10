<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Settings - Café Athena</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/admin-dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/settings.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <script src="{{ asset('assets/js/toast.js') }}"></script>
</head>

<body>
  <div class="sidebar">
    <div class="logo-container">
      <img src="{{ asset('assets/images/cafe-atina-logo.png') }}" alt="Café Athena Logo" class="sidebar-logo">
      <h2 class="sidebar-title">Café Athena</h2>
    </div>
    <div class="sidebar-menu">
      <ul>
        <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
        <li><a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="{{ route('admin.products') }}"><i class="fas fa-box"></i> Products</a></li>
        <li><a href="{{ route('admin.orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="{{ route('admin.settings') }}" class="active"><i class="fas fa-cog"></i> Settings</a></li>
      </ul>
    </div>

    @if ($coffeeOfTheDay)
      <div class="coffee-of-the-day-card-sidebar">
        <div class="card-image">
          <img src="{{ asset($coffeeOfTheDay->product_image) }}"
            alt="{{ $coffeeOfTheDay->product_name }}">
        </div>
        <div class="card-info">
          <h4>{{ $coffeeOfTheDay->product_name }}</h4>
          <p>{{ Str::limit($coffeeOfTheDay->product_description, 50) }}</p>
        </div>
      </div>
    @endif
    <div class="sidebar-logout">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none; border:none; color:inherit; font:inherit; cursor:pointer; width: 100%; text-align: left; padding: 10px 15px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
  </div>

  <div class="main-content">
    <header>
      <h1>Settings</h1>
      <p>Manage your account settings and preferences.</p>
    </header>

    <div class="settings-container">
      <div class="tabs">
        {{-- Tabs controlled by simple JS --}}
        <button class="tab-link active" onclick="openTab(event, 'edit-profile')">Edit Profile</button>
        <button class="tab-link" onclick="openTab(event, 'change-password')">Change Password</button>
      </div>

      <div id="edit-profile" class="tab-content" style="display: block;">
        <div class="card">
          <h2>Edit Profile</h2>
          <form action="{{ route('admin.settings.update-profile') }}" method="POST">
            @csrf
            @method('PUT')
            
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="user_firstname"
              value="{{ old('user_firstname', $user->user_firstname) }}" required>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="user_lastname"
              value="{{ old('user_lastname', $user->user_lastname) }}" required>

            <label for="email">Email:</label>
            <input type="email" id="email" value="{{ $user->user_email }}" disabled readonly>

            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" name="user_birthday"
              value="{{ old('user_birthday', $user->user_birthday) }}">

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="user_phone"
              value="{{ old('user_phone', $user->user_phone) }}">

            <label for="address">Address:</label>
            <textarea id="address" name="user_address">{{ old('user_address', $user->user_address) }}</textarea>

            <button type="submit">Save Profile</button>
          </form>
        </div>
      </div>

      <div id="change-password" class="tab-content" style="display: none;">
        <div class="card">
          <h2>Change Password</h2>
          <form action="{{ route('admin.settings.change-password') }}" method="POST">
            @csrf
            @method('PUT')
            
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="new_password_confirmation" required>

            <button type="submit">Change Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    function openTab(evt, tabName) {
      var i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("tab-content");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
      }
      tablinks = document.getElementsByClassName("tab-link");
      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
      }
      document.getElementById(tabName).style.display = "block";
      evt.currentTarget.className += " active";
    }

    function toastSuccess(message) {
      if (typeof Toastify === 'function') {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#4bb543",
        }).showToast();
      }
    }

    function toastError(message) {
      if (typeof Toastify === 'function') {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#ff5f6d",
        }).showToast();
      }
    }

    @if(session('success'))
      toastSuccess("{{ session('success') }}");
    @endif

    @if($errors->any())
      @foreach ($errors->all() as $error)
        toastError("{{ $error }}");
      @endforeach
    @endif
  </script>

</body>

</html>
