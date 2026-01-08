<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Order Management - Café Athena</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/barista/barista-dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/barista/order-management.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/modal.css') }}">
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
        <li><a href="{{ route('barista.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
        <li><a href="{{ route('barista.orders') }}" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
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
      <h1>Order Management</h1>
      <p>Manage and track all café orders here.</p>
      <div class="header-actions">
        <form method="GET" action="{{ route('barista.orders') }}">
          <input type="text" name="search" placeholder="Search by customer or status"
            value="{{ $search }}">
          <select name="sort">
            <option value="order_id DESC" {{ $sort == 'order_id DESC' ? 'selected' : '' }}>Newest</option>
            <option value="order_id ASC" {{ $sort == 'order_id ASC' ? 'selected' : '' }}>Oldest</option>
            <option value="order_total DESC" {{ $sort == 'order_total DESC' ? 'selected' : '' }}>Highest Total</option>
            <option value="order_total ASC" {{ $sort == 'order_total ASC' ? 'selected' : '' }}>Lowest Total</option>
            <option value="pending" {{ $sort == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="preparing" {{ $sort == 'preparing' ? 'selected' : '' }}>Preparing</option>
            <option value="ready" {{ $sort == 'ready' ? 'selected' : '' }}>Ready</option>
            <option value="completed" {{ $sort == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ $sort == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
          <button type="submit">Search</button>
          <button type="button" onclick="window.location.href='{{ route('barista.orders') }}'" class="clear-btn">Clear</button>
        </form>
      </div>
    </header>

    <div class="order-registrations">
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
          @if ($orders->count() > 0)
            @foreach ($orders as $order)
              @php
                  $statusClass = strtolower($order->order_status);
                  $paymentStatusClass = strtolower($order->order_payment_status);
              @endphp
              <tr class='order-row' data-order-id='{{ $order->order_id }}' data-order-status='{{ $order->order_status }}' data-payment-method='{{ $order->order_payment_method }}' data-payment-status='{{ $order->order_payment_status }}' data-order-total='{{ $order->order_total }}'>
                      <td>#{{ $order->order_id }}</td>
                      <td>{{ $order->user_firstname }} {{ $order->user_lastname }}</td>
                      <td class='status {{ $statusClass }}'>{{ ucfirst($order->order_status) }}</td>
                      <td>₱{{ number_format($order->order_total, 2) }}</td>
                      <td>{{ ucfirst($order->order_payment_method) }}</td>
                      <td class='status {{ $paymentStatusClass }}'>{{ ucfirst($order->order_payment_status) }}</td>
                      <td>{{ \Carbon\Carbon::parse($order->order_createdat)->format('M d, Y h:i A') }}</td>
                      <td class='action-links'>
                        <a href='#' class='update-status-link'>Manage</a>
                      </td>
              </tr>
            @endforeach
          @else
            <tr><td colspan='8'><div class='no-orders'>
                  <i class='fas fa-inbox' style='font-size: 48px; color: #ccc; margin-bottom: 10px;'></i><br>
                  No orders found.</div></td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>

  <!-- Order Details Modal -->
  <div id="orderDetailsModal" class="modal">
    <div class="modal-content">
      <span class="close close-details-modal">&times;</span>
      <h2>Order Details</h2>
      <div class="order-info">
        <p><strong>Order ID:</strong> <span id="details-order-id"></span></p>
        <p><strong>Customer:</strong> <span id="details-customer"></span></p>
        <p><strong>Date:</strong> <span id="details-date"></span></p>
        <p><strong>Payment Method:</strong> <span id="details-payment-method"></span></p>
      </div>
      <table class="order-items-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody id="details-order-items">
          <!-- Items will be populated here -->
        </tbody>
      </table>
      <div class="order-total">
        <strong>Total:</strong> <span id="details-total"></span>
      </div>
    </div>
  </div>

  <!-- Update Status Modal -->
  <div id="updateStatusModal" class="modal">
    <div class="modal-content">
      <span class="close close-update-modal">&times;</span>
      <h2>Update Order</h2>
      <form id="updateOrderForm" action="{{ route('barista.orders.update') }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="update-order-id" name="order_id">

        <label for="update-order-status">Order Status:</label>
        <select id="update-order-status" name="order_status" required>
          <option value="pending">Pending</option>
          <option value="preparing">Preparing</option>
          <option value="ready">Ready</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>

        <div id="payment-section"
          style="display: none; margin-top: 1rem; border-top: 1px solid #eee; padding-top: 1rem;">
          <h4>Cash Payment</h4>
          <p>Order Total: <strong id="payment-order-total"></strong></p>
          <div class="form-row">
            <div class="form-group">
              <label for="amount-paid">Amount Paid:</label>
              <input type="number" id="amount-paid" name="amount_paid" step="0.01">
            </div>
            <div class="form-group">
              <label for="change-given">Change:</label>
              <input type="text" id="change-given" name="change_given" readonly>
            </div>
          </div>
          <input type="hidden" name="payment_status" value="paid">
        </div>

        <button type="submit">Save Changes</button>
      </form>
    </div>
  </div>

  <script>
    const orderDetailsModal = document.getElementById("orderDetailsModal");
    const updateStatusModal = document.getElementById("updateStatusModal");
    const closeDetails = document.querySelector(".close-details-modal");
    const closeUpdate = document.querySelector(".close-update-modal");

    function capitalizeWords(str) {
      if (!str) return '';
      return str.replace(/\b\w/g, char => char.toUpperCase());
    }

    document.querySelectorAll('.order-row').forEach(row => {
      row.addEventListener('dblclick', (e) => {
        const orderId = row.dataset.orderId;

        // Use a placeholder and replace it with the actual ID
        const url = "{{ route('barista.orders.details', ['id' => 'ID_PLACEHOLDER']) }}".replace('ID_PLACEHOLDER', orderId);

        fetch(url)
          .then(response => response.json())
          .then(data => {
            if (data.error) {
              toastError(data.error);
            } else {
              document.getElementById('details-order-id').textContent = '#' + data.order_id;
              document.getElementById('details-customer').textContent = capitalizeWords(data.customer_name);
              document.getElementById('details-date').textContent = new Date(data.order_createdat).toLocaleString();
              document.getElementById('details-payment-method').textContent = capitalizeWords(data.order_payment_method);
              document.getElementById('details-total').textContent = `₱${parseFloat(data.order_total).toFixed(2)}`;

              const itemsTbody = document.getElementById('details-order-items');
              itemsTbody.innerHTML = ''; // Clear previous items
              data.items.forEach(item => {
                const row = `<tr>
                                <td>${item.product_name}</td>
                                <td>${item.orderitem_quantity}</td>
                                <td>₱${parseFloat(item.orderitem_price).toFixed(2)}</td>
                                <td>₱${parseFloat(item.orderitem_subtotal).toFixed(2)}</td>
                             </tr>`;
                itemsTbody.innerHTML += row;
              });

              orderDetailsModal.style.display = "flex";
            }
          })
          .catch(error => {
            console.error('Error:', error);
            toastError('Failed to load order details');
          });
      });
    });

    document.querySelectorAll('.update-status-link').forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const row = e.target.closest('.order-row');
        const orderId = row.dataset.orderId;
        const orderStatus = row.dataset.orderStatus;
        const paymentMethod = row.dataset.paymentMethod;
        const paymentStatus = row.dataset.paymentStatus;
        const orderTotal = row.dataset.orderTotal;

        document.getElementById('update-order-id').value = orderId;
        document.getElementById('update-order-status').value = orderStatus;

        const paymentSection = document.getElementById('payment-section');
        const amountPaidInput = document.getElementById('amount-paid');
        const changeGivenInput = document.getElementById('change-given');

        if (paymentMethod === 'cash' && paymentStatus === 'unpaid') {
          document.getElementById('payment-order-total').textContent = `₱${parseFloat(orderTotal).toFixed(2)}`;
          paymentSection.style.display = 'block';
          amountPaidInput.value = '';
          changeGivenInput.value = '';
        } else {
          paymentSection.style.display = 'none';
        }

        updateStatusModal.style.display = 'flex';
      });
    });

    const amountPaidInput = document.getElementById('amount-paid');
    const changeGivenInput = document.getElementById('change-given');
    amountPaidInput.addEventListener('input', () => {
      const total = parseFloat(document.getElementById('payment-order-total').textContent.replace('₱', ''));
      const paid = parseFloat(amountPaidInput.value);
      if (!isNaN(total) && !isNaN(paid) && paid >= total) {
        changeGivenInput.value = `₱${(paid - total).toFixed(2)}`;
      } else {
        changeGivenInput.value = '';
      }
    });

    closeDetails.onclick = () => orderDetailsModal.style.display = "none";
    closeUpdate.onclick = () => updateStatusModal.style.display = "none";

    window.onclick = (event) => {
      if (event.target == orderDetailsModal) {
        orderDetailsModal.style.display = "none";
      }
      if (event.target == updateStatusModal) {
        updateStatusModal.style.display = "none";
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
