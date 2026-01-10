<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Settings</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/customer/customer-dashboard-new.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/customer/customer-setting.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <script src="{{ asset('assets/js/toast.js') }}"></script>
</head>

<body>
  <header class="main-header">
    <div class="logo-container">
      <img src="{{ asset('assets/images/cafe-atina-logo.png') }}" alt="Café Athena Logo" class="sidebar-logo">
      <h2 class="sidebar-title">Café Athena</h2>
    </div>
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search for your favorite coffee..." class="search-input"
        oninput="window.location.href='{{ route('customer.dashboard') }}?search=' + this.value">
    </div>
    <div class="profile-section">
      <img src="https://placehold.co/100x100/A0AEC0/ffffff?text={{ $initials }}" alt="User Profile"
        class="profile-img" onclick="toggleDropdown()">
      <span onclick="toggleDropdown()">{{ $user->user_firstname }}</span>
      <svg class="dropdown-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
        onclick="toggleDropdown()">
        <path fill-rule="evenodd"
          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
          clip-rule="evenodd" />
      </svg>
      <div id="profileDropdown" class="dropdown-menu hidden">
        <a href="{{ route('customer.dashboard') }}">Dashboard</a>
        <a href="{{ route('customer.settings') }}">Settings</a>
        <a href="{{ route('customer.history') }}">History</a>
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-link">Logout</a>
        </form>
      </div>
    </div>
  </header>

  <div class="main-container">
    <div class="settings-container">
      <a href="{{ route('customer.dashboard') }}" class="back-to-dashboard-btn">&larr; Back to Dashboard</a>
      <div class="tabs">
        <button class="tab-link active" onclick="openTab(event, 'edit-profile')">Edit Profile</button>
        <button class="tab-link" onclick="openTab(event, 'change-password')">Change Password</button>
      </div>

      <div id="edit-profile" class="tab-content" style="display: block;">
        <h2>Edit Profile</h2>
        <form action="{{ route('customer.settings.update-profile') }}" method="POST">
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
          <input type="text" id="phone" name="user_phone" value="{{ old('user_phone', $user->user_phone) }}">

          <label for="address">Address:</label>
          <textarea id="address" name="user_address">{{ old('user_address', $user->user_address) }}</textarea>

          <button type="submit">Save Profile</button>
        </form>
      </div>

      <div id="change-password" class="tab-content" style="display: none;">
        <h2>Change Password</h2>
        <form action="{{ route('customer.settings.change-password') }}" method="POST">
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

    function toggleDropdown() {
      const dropdown = document.getElementById('profileDropdown');
      dropdown.classList.toggle('hidden');
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
            backgroundColor: "#d32f2f",
         }).showToast();
      }
    }

    @if(session('success'))
      toastSuccess("{{ session('success') }}");
    @endif
    @if(session('error'))
      toastError("{{ session('error') }}");
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            toastError("{{ $error }}");
        @endforeach
    @endif
  </script>

</body>

</html>
