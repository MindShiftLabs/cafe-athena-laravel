<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Café Athena</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/admin-dashboard.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

  {{-- Conditionally load toast.js if it exists, or just use inline script --}}
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
        <li><a href="{{ route('admin.dashboard') }}" class="active"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
        <li><a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="{{ route('admin.products') }}"><i class="fas fa-box"></i> Products</a></li>
        <li><a href="{{ route('admin.orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="{{ route('admin.settings') }}"><i class="fas fa-cog"></i> Settings</a></li>
      </ul>
    </div>

    @if ($coffeeOfTheDay)
      <div class="coffee-of-the-day-card-sidebar">
        <!-- Debug Image Path: {{ url($coffeeOfTheDay->product_image) }} -->
        <div class="card-image">
          <img src="{{ url($coffeeOfTheDay->product_image) }}"
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
      <h1>Welcome, {{ $username }}!</h1>
      <p>Here's what's happening with your store today.</p>
    </header>

    <div class="dashboard-cards">
      <div class="card">
        <div class="card-icon"><i class="fas fa-users"></i></div>
        <div class="card-info">
          <h2>Users</h2>
          <p>Manage accounts.</p>
          <a href="{{ route('admin.users') }}" class="card-link">Go to Users</a>
        </div>
      </div>
      <div class="card">
        <div class="card-icon"><i class="fas fa-box"></i></div>
        <div class="card-info">
          <h2>Products</h2>
          <p>Manage products.</p>
          <a href="{{ route('admin.products') }}" class="card-link">Go to Products</a>
        </div>
      </div>
      <div class="card">
        <div class="card-icon"><i class="fas fa-shopping-cart"></i></div>
        <div class="card-info">
          <h2>Orders</h2>
          <p>Manage orders.</p>
          <a href="{{ route('admin.orders') }}" class="card-link">Go to Orders</a>
        </div>
      </div>
      <div class="card">
        <div class="card-icon"><i class="fas fa-cog"></i></div>
        <div class="card-info">
          <h2>Settings</h2>
          <p>Configure app.</p>
          <a href="{{ route('admin.settings') }}" class="card-link">Go to Settings</a>
        </div>
      </div>
    </div>

    <div class="statistics-cards">
      <div class="card stat-card">
        <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
        <div class="card-info">
          <h3>Total Revenue</h3>
          <p>₱{{ number_format($totalRevenue, 2) }}</p>
        </div>
      </div>
      <div class="card stat-card">
        <div class="card-icon"><i class="fas fa-receipt"></i></div>
        <div class="card-info">
          <h3>New Orders</h3>
          <p>{{ $newOrdersCount }}</p>
        </div>
      </div>
      <div class="card stat-card">
        <div class="card-icon"><i class="fas fa-users"></i></div>
        <div class="card-info">
          <h3>Total Customers</h3>
          <p>{{ $totalCustomers }}</p>
        </div>
      </div>
      <div class="card stat-card">
        <div class="card-icon"><i class="fas fa-user-tie"></i></div>
        <div class="card-info">
          <h3>Total Staff</h3>
          <p>{{ $totalStaff }}</p>
        </div>
      </div>
    </div>

    <div class="recent-registrations">
      <h2>Recent Customer Registrations</h2>
      <table>
        <thead>
          <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody>
          @if ($recentRegistrations->count() > 0)
            @foreach ($recentRegistrations as $user)
              <tr>
                <td>{{ $user->user_firstname }}</td>
                <td>{{ $user->user_lastname }}</td>
                <td>{{ $user->user_email }}</td>
              </tr>
            @endforeach
          @else
            <tr><td colspan='3'><div class='no-orders'>
                  <i class='fas fa-user-slash' style='font-size: 48px; color: #ccc; margin-bottom: 10px;'></i><br>
                  No recent registrations.</div></td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>

  <script>
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
      } else {
          console.log("Toastify not loaded: " + message);
      }
    }

    @if(session('success'))
      toastSuccess("{{ session('success') }}");
    @endif
  </script>

</body>

</html>
