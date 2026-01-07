<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Order Management - Café Athena</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/admin-dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/user-management.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/modal.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/order-management.css') }}">
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
        <li><a href="{{ route('admin.orders') }}" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="{{ route('admin.settings') }}"><i class="fas fa-cog"></i> Settings</a></li>
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
      <h1>Order Management</h1>
      <p>Here you can manage all the orders of the application.</p>
      <div class="header-actions">
        <form action="{{ route('admin.orders') }}" method="GET">
          <input type="text" name="search" placeholder="Search by customer or status"
            value="{{ request('search') }}">
          <select name="sort">
            <option value="order_id DESC" {{ request('sort') == 'order_id DESC' ? 'selected' : '' }}>Newest</option>
            <option value="order_id ASC" {{ request('sort') == 'order_id ASC' ? 'selected' : '' }}>Oldest</option>
            <option value="order_total DESC" {{ request('sort') == 'order_total DESC' ? 'selected' : '' }}>Highest Total</option>
            <option value="order_total ASC" {{ request('sort') == 'order_total ASC' ? 'selected' : '' }}>Lowest Total</option>
            <option value="pending" {{ request('sort') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="preparing" {{ request('sort') == 'preparing' ? 'selected' : '' }}>Preparing</option>
            <option value="ready" {{ request('sort') == 'ready' ? 'selected' : '' }}>Ready</option>
            <option value="completed" {{ request('sort') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('sort') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
          <button type="submit">Filter</button>
          <button type="button" class="clear-btn" onclick="window.location.href='{{ route('admin.orders') }}'">Clear</button>
        </form>
      </div>
    </header>

    <div class="order-table-container">
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Total</th>
            <th>Payment Method</th>
            <th>Payment Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if ($orders->isEmpty())
            <tr>
              <td colspan="8">
                <div class='no-orders'>
                  <i class='fas fa-inbox' style='font-size: 48px; color: #ccc; margin-bottom: 10px;'></i><br>
                  No orders found.
                </div>
              </td>
            </tr>
          @else
            @foreach ($orders as $order)
              <tr data-order-id="{{ $order->order_id }}">
                <td>#{{ $order->order_id }}</td>
                <td>{{ $order->user_firstname }} {{ $order->user_lastname }}</td>
                <td class="status {{ strtolower($order->order_status) }}">
                  {{ ucfirst($order->order_status) }}
                </td>
                <td>₱{{ number_format($order->order_total, 2) }}</td>
                <td>{{ ucfirst($order->order_payment_method) }}</td>
                <td class="status {{ strtolower($order->order_payment_status) }}">
                  {{ ucfirst($order->order_payment_status) }}
                </td>
                <td>{{ $order->order_createdat }}</td>
                <td class="action-links">
                  <a href="#" class="edit-link">Manage</a>
                </td>
              </tr>
            @endforeach
          @endif
        </tbody>
      </table>
    </div>
  </div>

  <!-- Edit Order Modal -->
  <div id="editOrderModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Edit Order</h2>
      <form action="{{ route('admin.orders.update') }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-order-id" name="order_id">
        <label for="edit-order-status">Order Status:</label>
        <select id="edit-order-status" name="order_status" required>
          <option value="pending">Pending</option>
          <option value="preparing">Preparing</option>
          <option value="ready">Ready</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <label for="edit-payment-status">Payment Status:</label>
        <select id="edit-payment-status" name="order_payment_status" required>
          <option value="unpaid">Unpaid</option>
          <option value="paid">Paid</option>
        </select>
        <button type="submit">Save Changes</button>
      </form>
    </div>
  </div>

  <script>
    // Get the modal
    var editOrderModal = document.getElementById("editOrderModal");

    // Get the <span> element that closes the modal
    var spans = document.getElementsByClassName("close");

    // When the user clicks on <span> (x), close the modal
    for (var i = 0; i < spans.length; i++) {
      spans[i].onclick = function () {
        editOrderModal.style.display = "none";
      }
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
      if (event.target == editOrderModal) {
        editOrderModal.style.display = "none";
      }
    }

    // Handle edit button clicks
    var editLinks = document.getElementsByClassName("edit-link");

    for (var i = 0; i < editLinks.length; i++) {
      editLinks[i].onclick = function (e) {
        e.preventDefault();
        editOrderModal.style.display = "flex";
        var row = this.closest('tr');
        var id = row.dataset.orderId;
        var orderStatus = row.cells[2].innerText.trim().toLowerCase();
        var paymentStatus = row.cells[5].innerText.trim().toLowerCase();

        document.getElementById("edit-order-id").value = id;
        document.getElementById("edit-order-status").value = orderStatus;
        document.getElementById("edit-payment-status").value = paymentStatus;
      }
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
