<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Barista Dashboard - Café Athena</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/barista/barista-dashboard.css') }}">
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
        <li><a href="{{ route('barista.dashboard') }}" class="active"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
        <li><a href="{{ route('barista.orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="{{ route('barista.products') }}"><i class="fas fa-box"></i> Products</a></li>
        <li><a href="{{ route('barista.settings') }}"><i class="fas fa-cog"></i> Settings</a></li>
      </ul>
    </div>

    @if ($coffeeOfTheDay)
      <div class="coffee-of-the-day-card-sidebar">
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
      <p>Here's your barista overview for today.</p>
    </header>

    <div class="dashboard-cards">
      <div class="card">
        <div class="card-icon"><i class="fas fa-shopping-cart"></i></div>
        <div class="card-info">
          <h2>Orders</h2>
          <p>Manage orders.</p>
          <a href="{{ route('barista.orders') }}" class="card-link">Go to Orders</a>
        </div>
      </div>
      <div class="card">
        <div class="card-icon"><i class="fas fa-box"></i></div>
        <div class="card-info">
          <h2>Products</h2>
          <p>Manage products.</p>
          <a href="{{ route('barista.products') }}" class="card-link">Go to Products</a>
        </div>
      </div>
      <div class="card">
        <div class="card-icon"><i class="fas fa-cog"></i></div>
        <div class="card-info">
          <h2>Settings</h2>
          <p>Configure your settings.</p>
          <a href="{{ route('barista.settings') }}" class="card-link">Go to Settings</a>
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
        <div class="card-icon"><i class="fas fa-hourglass-half"></i></div>
        <div class="card-info">
          <h3>Pending Orders</h3>
          <p>{{ $pendingOrdersCount }}</p>
        </div>
      </div>
      <div class="card stat-card">
        <div class="card-icon"><i class="fas fa-boxes"></i></div>
        <div class="card-info">
          <h3>Products in Stock</h3>
          <p>{{ $totalProducts }}</p>
        </div>
      </div>
    </div>

    <div class="recent-registrations">
      <h2>Recent Orders</h2>
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Total</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @if ($recentOrders->count() > 0)
            @foreach ($recentOrders as $order)
              @php
                  $status = strtolower($order->order_status);
              @endphp
              <tr>
                <td>#{{ $order->order_id }}</td>
                <td>{{ $order->user_firstname }} {{ $order->user_lastname }}</td>
                <td>{{ ucfirst($status) }}</td>
                <td>₱{{ number_format($order->order_total, 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($order->order_createdat)->format('M d, Y h:i A') }}</td>
              </tr>
            @endforeach
          @else
            <tr><td colspan='5'><div class='no-orders'>
                  <i class='fas fa-inbox' style='font-size: 48px; color: #ccc; margin-bottom: 10px;'></i><br>
                  No recent orders found.
                </div></td></tr>
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
  </script>
</body>
</html>
