<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Order History</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/customer/customer-dashboard-new.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/customer/customer-history.css') }}">
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
    <div class="history-container">
      <a href="{{ route('customer.dashboard') }}" class="back-to-dashboard-btn">&larr; Back to Dashboard</a>
      <div class="history-header">
        <h1>Your Order History</h1>
        <div class="filter-controls">
          <input type="text" id="searchInput" placeholder="Search by Order ID...">
          <select id="statusFilter">
            <option value="all">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="ready">Ready</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>
      <div id="order-history-list">
        <!-- Orders will be loaded here by JavaScript -->
      </div>
    </div>
  </div>

  <!-- Modals -->
  <div id="orderDetailsModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Order Details</h2>
        <span class="close close-details-modal">&times;</span>
      </div>
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
        <tbody id="details-order-items"></tbody>
      </table>
      <div class="order-total">
        <strong>Total:</strong> <span id="details-total"></span>
      </div>
    </div>
  </div>

  <div id="confirmReceiveModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Confirm Order Received</h2>
        <span class="close close-confirm-modal">&times;</span>
      </div>
      <p>Are you sure you have received this order? This action cannot be undone.</p>
      <div class="modal-actions">
        <button id="cancelReceiveBtn" class="btn btn-secondary">Cancel</button>
        <button id="confirmReceiveBtn" class="btn btn-primary">Yes, I've Received It</button>
      </div>
    </div>
  </div>


  <script>
    let allOrders = [];
    let currentOrderIdForConfirmation = null;

    document.addEventListener('DOMContentLoaded', function () {
      fetchOrderHistory();

      document.getElementById('searchInput').addEventListener('input', filterAndRenderOrders);
      document.getElementById('statusFilter').addEventListener('change', filterAndRenderOrders);
    });

    function fetchOrderHistory() {
      fetch("{{ route('customer.orders.api') }}")
        .then(response => response.json())
        .then(orders => {
          allOrders = orders;
          renderOrders(allOrders);
        })
        .catch(error => {
          console.error('Error fetching order history:', error);
          toastError('Could not load your order history.');
        });
    }

    function filterAndRenderOrders() {
      const searchValue = document.getElementById('searchInput').value.toLowerCase();
      const statusValue = document.getElementById('statusFilter').value;

      let filteredOrders = allOrders.filter(order => {
        const matchesSearch = order.order_id.toString().includes(searchValue);
        const matchesStatus = statusValue === 'all' || order.order_status === statusValue;
        return matchesSearch && matchesStatus;
      });

      renderOrders(filteredOrders);
    }

    function renderOrders(orders) {
      const container = document.getElementById('order-history-list');
      if (orders.length === 0) {
        container.innerHTML = `<div class="no-history"><i class="fas fa-receipt"></i><p>No orders found.</p></div>`;
        return;
      }

      let html = '';
      orders.forEach(order => {
        html += `
              <div class="order-card" id="order-card-${order.order_id}">
                <div class="order-card-header">
                  <h3>Order #${order.order_id}</h3>
                  <span class="status-badge status-${order.order_status}">${order.order_status}</span>
                </div>
                <div class="order-card-body">
                  <p><strong>Date:</strong> ${new Date(order.order_createdat).toLocaleDateString()}</p>
                  <p><strong>Total:</strong> ₱${parseFloat(order.order_total).toFixed(2)}</p>
                  <p><strong>Type:</strong> ${order.order_type}</p>
                </div>
                <div class="order-card-footer">
                  <button class="btn btn-secondary" onclick="openOrderDetails(${order.order_id})">View Details</button>
                  ${order.order_status === 'ready' ?
            `<button class="btn btn-primary" onclick="receiveOrder(${order.order_id})">Received</button>` : ''
          }
                </div>
              </div>
            `;
      });
      container.innerHTML = html;
    }

    function receiveOrder(orderId) {
      currentOrderIdForConfirmation = orderId;
      document.getElementById('confirmReceiveModal').style.display = 'flex';
    }

    function confirmAndReceiveOrder() {
      if (!currentOrderIdForConfirmation) return;

      fetch("{{ route('customer.order.update') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ order_id: currentOrderIdForConfirmation })
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            toastSuccess(data.message);
            // Update local order data and re-render
            const orderIndex = allOrders.findIndex(o => o.order_id == currentOrderIdForConfirmation);
            if (orderIndex !== -1) {
              allOrders[orderIndex].order_status = 'completed';
            }
            filterAndRenderOrders();
          } else {
            toastError(data.message);
          }
        })
        .catch(error => {
          console.error('Error updating order status:', error);
          toastError('An unexpected error occurred.');
        })
        .finally(() => {
          closeConfirmModal();
        });
    }

    function openOrderDetails(orderId) {
      // Use the barista's API for order details since it's the same logic, 
      // or duplicate it in CustomerController. 
      // I'll assume we duplicate/reuse. 
      // Let's use the route we created in BaristaController since it's generic enough?
      // Actually, better to use the one in CustomerController if we want to secure it (ensure user owns order).
      // But for now, I haven't created a 'details' endpoint in CustomerController.
      // Wait, I did not create `getOrderDetails` in CustomerController in the previous step.
      // I should add it or use the Barista one. The Barista one doesn't check for user ownership.
      // So I should create one in CustomerController that checks user ownership.
      // For now, I will use a placeholder and then add the method.
      
      const url = "{{ route('customer.order.details', ['id' => 'ID_PLACEHOLDER']) }}".replace('ID_PLACEHOLDER', orderId);

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
            itemsTbody.innerHTML = '';
            data.items.forEach(item => {
              const row = `<tr>
                            <td>${item.product_name}</td>
                            <td>${item.orderitem_quantity}</td>
                            <td>₱${parseFloat(item.orderitem_price).toFixed(2)}</td>
                            <td>₱${parseFloat(item.orderitem_subtotal).toFixed(2)}</td>
                         </tr>`;
              itemsTbody.innerHTML += row;
            });

            document.getElementById("orderDetailsModal").style.display = "flex";
          }
        })
        .catch(error => {
          console.error('Error:', error);
          toastError('Failed to load order details');
        });
    }

    function capitalizeWords(str) {
      if (!str) return '';
      return str.replace(/\b\w/g, char => char.toUpperCase());
    }

    function toggleDropdown() {
      document.getElementById('profileDropdown').classList.toggle('hidden');
    }

    // Modal closing logic
    const orderDetailsModal = document.getElementById("orderDetailsModal");
    const confirmReceiveModal = document.getElementById("confirmReceiveModal");

    document.querySelector(".close-details-modal").onclick = () => orderDetailsModal.style.display = "none";
    document.querySelector(".close-confirm-modal").onclick = closeConfirmModal;
    document.getElementById("cancelReceiveBtn").onclick = closeConfirmModal;
    document.getElementById("confirmReceiveBtn").onclick = confirmAndReceiveOrder;

    function closeConfirmModal() {
      confirmReceiveModal.style.display = "none";
      currentOrderIdForConfirmation = null;
    }

    window.onclick = (event) => {
      if (event.target == orderDetailsModal) orderDetailsModal.style.display = "none";
      if (event.target == confirmReceiveModal) closeConfirmModal();
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
  </script>

</body>
</html>
