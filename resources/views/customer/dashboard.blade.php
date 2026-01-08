<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard</title>
  <link rel="stylesheet" href="{{ asset('assets/css/customer/customer-dashboard-new.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
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
        oninput="searchMenu()">
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
    <main class="menu-section">
      <h2>Menu</h2>
      <div class="categories" id="categoriesCarousel">
        <!-- Categories will be generated here -->
      </div>
      <div class="menu-grid" id="menuContainer">
        <!-- Menu items will be generated here -->
      </div>
    </main>

    <aside class="cart-section">
      <h2>Your Order</h2>
      <div class="cart-items" id="cartItems">
        <div class='no-orders'>
          <i class='fas fa-shopping-cart' style='font-size: 48px; color: #ccc; margin-bottom: 10px;'></i><br>
          Your cart is empty.
        </div>
      </div>
      <div class="cart-total">
        <span>Total:</span>
        <span id="cartTotal">₱0.00</span>
      </div>
      <button class="checkout-btn" onclick="openCheckoutModal()">Checkout</button>
    </aside>
  </div>

  <div id="checkoutModal" class="modal-overlay hidden">
    <div class="modal-content">
      <button class="modal-close" onclick="closeCheckoutModal()">&times;</button>
      <div id="modalBody">
        <!-- Checkout stages will be rendered here -->
      </div>
    </div>
  </div>

  <script>
    const userData = {
      phone: @json($user->user_phone),
      address: @json($user->user_address)
    };

    // --- STATE VARIABLES ---

    let menu = []; // Menu will be fetched from DB
    let cart = [];
    let currentCategory = 'All';
    let currentSearchQuery = '';
    let checkoutState = 0; // 0: Cart, 1: Options, 2: Confirmation, 3: Tracking
    let checkoutData = {
      method: null,
      payment: null,
      address: userData.address // Use the real address
    };

    // --- CHECKOUT MODAL FUNCTIONS ---
    function openCheckoutModal() {
      if (!userData.phone || !userData.address) {
        Toastify({
          text: "Please add your phone and address in Settings to proceed.",
          duration: 3000,
          gravity: "top",
          position: "right",
          backgroundColor: "#FFA000",
          stopOnFocus: true,
          borderRadius: "8px",
        }).showToast();
        return;
      }

      if (cart.length === 0) {
        toastError("Your cart is empty!");
        return;
      }
      checkoutState = 1;
      document.getElementById('checkoutModal').classList.remove('hidden');
      renderCheckoutStage();
    }

    function closeCheckoutModal() {
      document.getElementById('checkoutModal').classList.add('hidden');
      checkoutState = 0;
      checkoutData.method = null;
      checkoutData.payment = null;
    }

    function setMethod(method) {
      checkoutData.method = method;
      // If method is Delivery and payment was Cash, reset payment method
      if (method === 'Delivery' && checkoutData.payment === 'Cash') {
        checkoutData.payment = null;
      }
      renderCheckoutStage();
    }

    function setPayment(payment) {
      checkoutData.payment = payment;
      renderCheckoutStage();
    }

    function confirmOptions() {
      if (checkoutData.method && checkoutData.payment) {
        checkoutState = 2;
        renderCheckoutStage();
      }

    }

    function placeOrder() {
      checkoutState = 3; // Go to "Placing Order..." view
      renderCheckoutStage();

      const orderData = {
        cart: cart,
        checkout: checkoutData,
        totals: calculateTotals()
      };

      fetch("{{ route('customer.order.process') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(orderData)
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            toastSuccess(data.message || "Order placed successfully!");
            setTimeout(() => {
              closeCheckoutModal();
              cart = [];
              renderCart();
              renderMenu();
            }, 1500);
          } else {
            toastError(data.message || "There was a problem placing your order.");
            // Maybe go back to the confirmation screen
            checkoutState = 2;
            renderCheckoutStage();
          }
        })
        .catch(error => {
          console.error('Error placing order:', error);
          toastError("An unexpected error occurred. Please try again.");
          checkoutState = 2;
          renderCheckoutStage();
        });
    }

    function calculateTotals() {
      const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
      const finalTotal = checkoutData.method === 'Delivery' ? total + 5.00 : total;
      return { total, finalTotal };
    }

    function renderCheckoutStage() {
      const modalBody = document.getElementById('modalBody');
      const totals = calculateTotals();

      switch (checkoutState) {
        case 1: // Options
          let paymentOptionsHTML = `
              <div class="option-box ${checkoutData.payment === 'Card' ? 'selected' : ''}" onclick="setPayment('Card')">
                  <p><b>Card</b></p>
              </div>
              `;
          if (checkoutData.method !== 'Delivery') {
            paymentOptionsHTML += `
              <div class="option-box ${checkoutData.payment === 'Cash' ? 'selected' : ''}" onclick="setPayment('Cash')">
                  <p><b>Cash</b></p>
              </div>
          `;
          }
          modalBody.innerHTML = `
              <h2>Checkout Options</h2>
              <h4>1. Select Method</h4>
              <div class="checkout-options-grid">
              <div class="option-box ${checkoutData.method === 'Delivery' ? 'selected' : ''}" onclick="setMethod('Delivery')">
                  <p><b>Delivery</b></p>
                  <p><small>Fee: ₱5.00</small></p>
              </div>
              <div class="option-box ${checkoutData.method === 'Pickup' ? 'selected' : ''}" onclick="setMethod('Pickup')">
                  <p><b>Pickup</b></p>
                  <p><small>Free</small></p>
              </div>
              </div>
              <h4 style="margin-top: 1.5rem;">2. Select Payment</h4>
              <div class="checkout-options-grid">
                  ${paymentOptionsHTML}
              </div>
              <button class="confirm-options-btn" onclick="confirmOptions()" ${!(checkoutData.method && checkoutData.payment) ? 'disabled' : ''}>
                  Confirm Options
              </button>
          `;
          break;
        case 2: // Confirmation
          modalBody.innerHTML = `
            <h2>Confirm Order</h2>
            <p><b>Method:</b> ${checkoutData.method}</p>
            <p><b>Payment:</b> ${checkoutData.payment}</p>
            <p><b>Address:</b> ${checkoutData.address}</p>
            <hr style="margin: 1rem 0;">
            <p><b>Total:</b> ₱${totals.total.toFixed(2)}</p>
            ${checkoutData.method === 'Delivery' ? `<p><b>Delivery Fee:</b> ₱5.00</p>` : ''}
            <h3>Final Total: ₱${totals.finalTotal.toFixed(2)}</h3>
            <button class="confirm-options-btn" onclick="placeOrder()">Place Order</button>
            <button class="back-to-options-btn" onclick="checkoutState=1; renderCheckoutStage();">&larr; Back to Options</button>
          `;
          break;
        case 3: // Tracking (simplified)
          modalBody.innerHTML = `
            <h2>Placing Order...</h2>
            <p>Please wait while we confirm your order.</p>
            `;
          break;
      }
    }
    // --- CORE UI FUNCTIONS ---

    function searchMenu() {
      const searchInput = document.getElementById('searchInput');
      currentSearchQuery = searchInput.value.toLowerCase();
      renderMenu();

    }

    function renderCategories() {

      const categoriesContainer = document.getElementById('categoriesCarousel');
      const categories = ['All', 'Hot Brew', 'Iced & Cold', 'Pastry', 'Coffee Beans']; // Hardcoded categories
      categoriesContainer.innerHTML = categories.map(category => `
        <button class="category-btn ${currentCategory === category ? 'active' : ''}" onclick="filterMenu('${category}')">
            ${category}
        </button>
        `).join('');
    }

    function renderMenu() {
      const menuContainer = document.getElementById('menuContainer');
      const filteredMenu = menu.filter(item => {
        const matchesCategory = currentCategory === 'All' || item.category === currentCategory;
        const matchesSearch = item.name.toLowerCase().includes(currentSearchQuery);
        return matchesCategory && matchesSearch;
      });

      if (filteredMenu.length === 0) {
        menuContainer.innerHTML = `
          <div class='no-orders'>
            <i class='fas fa-inbox' style='font-size: 48px; color: #ccc; margin-bottom: 10px;'></i><br>
            No items found.
          </div>
          `;
        return;
      }

      menuContainer.innerHTML = filteredMenu.map(item => {
        const cartItem = cart.find(ci => ci.id === item.id);
        const isInStock = item.hasStock;

        // Ensure image URL is absolute
        const imageUrl = item.imageUrl.startsWith('http') ? item.imageUrl : `{{ url('/') }}/${item.imageUrl}`;

        return `
            <div class="menu-card ${!isInStock ? 'out-of-stock' : ''}">
              <img src="${imageUrl}" alt="${item.name}">
              <div class="menu-card-content">
                  <h3 class="menu-card-title">${item.name}</h3>
                  <p class="menu-card-price">₱${item.price.toFixed(2)}</p>
                  ${isInStock ?
            (cartItem ? `
                      <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decreaseQuantity(${item.id})">-</button>
                        <span class="quantity-display">${cartItem.quantity}</span>
                        <button class="quantity-btn" onclick="increaseQuantity(${item.id})">+</button>
                      </div>
                    ` : `
                      <button class="add-to-cart-btn" onclick="addToCart(${item.id})">Add to Cart</button>
                    `)
            :
            `<button class="add-to-cart-btn" disabled>Out of Stock</button>`
          }
                </div>
            </div>
        `
      }).join('');
    }

    function filterMenu(category) {
      currentCategory = category;
      renderCategories();
      renderMenu();
    }

    function addToCart(itemId) {
      const itemToAdd = menu.find(item => item.id == itemId);
      if (!itemToAdd.hasStock) return; // Double check
      cart.push({ ...itemToAdd, quantity: 1 });
      renderCart();
      renderMenu();
    }

    function increaseQuantity(itemId) {
      const itemInCart = cart.find(item => item.id == itemId);
      if (itemInCart) {
        itemInCart.quantity++;
      }
      renderCart();
      renderMenu();
    }

    function decreaseQuantity(itemId) {
      const itemInCart = cart.find(item => item.id == itemId);
      if (itemInCart) {
        itemInCart.quantity--;
        if (itemInCart.quantity === 0) {
          cart = cart.filter(item => item.id != itemId);
        }
      }
      renderCart();
      renderMenu();
    }

    function renderCart() {
      const cartItemsContainer = document.getElementById('cartItems');
      const cartTotalEl = document.getElementById('cartTotal');

      if (cart.length === 0) {
        cartItemsContainer.innerHTML = "<div class='no-orders'><i class='fas fa-shopping-cart' style='font-size: 48px; color: #ccc; margin-bottom: 10px;'></i><br>Your cart is empty.</div>";
        cartTotalEl.textContent = '₱0.00';
        return;
      }

      cartItemsContainer.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <span>${item.name} x ${item.quantity}</span>
                        <span>₱${(item.price * item.quantity).toFixed(2)}</span>
                    </div>
                `).join('');

      const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
      cartTotalEl.textContent = `₱${total.toFixed(2)}`;
    }

    // --- DROPDOWN FUNCTIONS ---
    function toggleDropdown() {
      const dropdown = document.getElementById('profileDropdown');
      dropdown.classList.toggle('hidden');
    }

    // --- EVENT LISTENERS ---
    document.addEventListener('DOMContentLoaded', () => {
      fetch("{{ route('customer.products.api') }}")
        .then(response => response.json())
        .then(data => {
          menu = data;
          renderCategories();
          renderMenu();
          renderCart();
        })
        .catch(error => {
          console.error('Error fetching products:', error);
        });
    });

    document.addEventListener('click', function (event) {
      const dropdown = document.getElementById('profileDropdown');
      const profileSection = document.querySelector('.profile-section');

      if (!profileSection.contains(event.target)) {
        if (!dropdown.classList.contains('hidden')) {
          dropdown.classList.add('hidden');
        }
      }
    });

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
