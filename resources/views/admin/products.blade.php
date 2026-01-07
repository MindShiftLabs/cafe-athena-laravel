<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Product Management - Café Athena</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/admin-dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/user-management.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/modal.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/product-management.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <script src="{{ asset('assets/js/toast.js') }}"></script>
  {{-- CSRF Token for AJAX --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
        <li><a href="{{ route('admin.products') }}" class="active"><i class="fas fa-box"></i> Products</a></li>
        <li><a href="{{ route('admin.orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a></li>
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
      <h1>Product Management</h1>
      <p>Here you can manage all the products of the application.</p>
      <div class="header-actions">
        <form action="{{ route('admin.products') }}" method="GET">
          <input type="text" name="search" placeholder="Search products..."
            value="{{ request('search') }}">
          <button type="submit">Search</button>
          <button type="button" class="clear-btn" onclick="window.location.href='{{ route('admin.products') }}'">Clear</button>
        </form>
        <button type="button" id="addProductBtn">Add New Product</button>
      </div>
    </header>

    <div class="product-table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Status (In Stock)</th>
            <th>Category</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if ($products->isEmpty())
            <tr>
              <td colspan="9">
                <div class='no-orders'>
                  <i class='fas fa-box-open' style='font-size: 48px; color: #ccc; margin-bottom: 10px;'></i><br>
                  No products found.
                </div>
              </td>
            </tr>
          @else
            @foreach ($products as $product)
              <tr data-status="{{ $product->product_status }}">
                <td>{{ $product->product_id }}</td>
                <td><img src="{{ asset($product->product_image) }}"
                    alt="{{ $product->product_name }}" width="50"
                    data-relative-path="{{ $product->product_image }}"></td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->product_description }}</td>
                <td>₱{{ $product->product_price }}</td>
                <td>
                  <label class="switch">
                    {{-- Mapping 'available' to checked --}}
                    <input type="checkbox" class="stock-toggle" data-id="{{ $product->product_id }}" {{ $product->product_status === 'available' ? 'checked' : '' }}>
                    <span class="slider"></span>
                  </label>
                </td>
                <td>{{ $product->product_category }}</td>
                <td>{{ $product->product_createdat }}</td>
                <td class="action-links">
                  <a href="#" class="edit-link">Edit</a>
                  <a href="#" class="delete-link">Delete</a>
                </td>
              </tr>
            @endforeach
          @endif
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add Product Modal -->
  <div id="addProductModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Add New Product</h2>
      <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required>
        <label for="image">Image:</label>
        <input type="file" id="image" name="image" required>
        <label for="status">Status:</label>
        <select id="status" name="status" required>
          <option value="available">Available</option>
          <option value="unavailable">Unavailable</option>
        </select>
        <label for="category">Category:</label>
        <select id="category" name="category" required>
          <option value="Hot Brew">Hot Brew</option>
          <option value="Iced & Cold">Iced & Cold</option>
          <option value="Pastry">Pastry</option>
          <option value="Coffee Beans">Coffee Beans</option>
        </select>
        <button type="submit">Add Product</button>
      </form>
    </div>
  </div>

  <!-- Edit Product Modal -->
  <div id="editProductModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Edit Product</h2>
      <form id="editProductForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST') {{-- Using POST for file upload updates, Laravel handles Method Spoofing internally, but sometimes multipart/form-data with PUT is tricky. Let's stick to POST with a dedicated update route or use _method field --}}
        <input type="hidden" name="_method" value="PUT">
        
        <input type="hidden" id="edit-product-id" name="product_id">
        <label for="edit-name">Name:</label>
        <input type="text" id="edit-name" name="name" required>
        <label for="edit-description">Description:</label>
        <textarea id="edit-description" name="description" required></textarea>
        <label for="edit-price">Price:</label>
        <input type="number" id="edit-price" name="price" step="0.01" required>

        <label>Current Image:</label>
        <img id="edit-current-image" src="" alt="Current Product Image" width="50">

        <label for="edit-image">New Image:</label>
        <input type="file" id="edit-image" name="image">
        <span>Leave blank to keep the current image.</span>

        <label for="edit-status">Status:</label>
        <select id="edit-status" name="status" required>
          <option value="available">Available</option>
          <option value="unavailable">Unavailable</option>
        </select>
        <label for="edit-category">Category:</label>
        <select id="edit-category" name="category" required>
          <option value="Hot Brew">Hot Brew</option>
          <option value="Iced & Cold">Iced & Cold</option>
          <option value="Pastry">Pastry</option>
          <option value="Coffee Beans">Coffee Beans</option>
        </select>
        <button type="submit">Save Changes</button>
      </form>
    </div>
  </div>

  <!-- Delete Product Modal -->
  <div id="deleteProductModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Delete Product</h2>
      <p>Are you sure you want to delete this product?</p>
      <form id="deleteProductForm" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" id="delete-product-id" name="product_id">
        <button type="submit">Delete</button>
        <button type="button" class="close-modal">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    // Get the modals
    var addProductModal = document.getElementById("addProductModal");
    var editProductModal = document.getElementById("editProductModal");
    var deleteProductModal = document.getElementById("deleteProductModal");

    // Get the button that opens the modal
    var addProductBtn = document.getElementById("addProductBtn");

    // Get the <span> element that closes the modal
    var spans = document.getElementsByClassName("close");

    // When the user clicks the button, open the modal
    addProductBtn.onclick = function () {
      addProductModal.style.display = "flex";
    }

    // When the user clicks on <span> (x), close the modal
    for (var i = 0; i < spans.length; i++) {
      spans[i].onclick = function () {
        addProductModal.style.display = "none";
        editProductModal.style.display = "none";
        deleteProductModal.style.display = "none";
      }
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
      if (event.target == addProductModal || event.target == editProductModal || event.target == deleteProductModal) {
        addProductModal.style.display = "none";
        editProductModal.style.display = "none";
        deleteProductModal.style.display = "none";
      }
    }

    // Handle edit and delete button clicks
    var editLinks = document.getElementsByClassName("edit-link");
    var deleteLinks = document.getElementsByClassName("delete-link");

    for (var i = 0; i < editLinks.length; i++) {
      editLinks[i].onclick = function (e) {
        e.preventDefault();
        editProductModal.style.display = "flex";
        var row = this.closest('tr');
        var id = row.cells[0].innerText;
        var name = row.cells[2].innerText;
        var description = row.cells[3].innerText;
        var price = row.cells[4].innerText;
        var status = row.dataset.status;
        var category = row.cells[6].innerText;
        var imageRelativePath = row.cells[1].getElementsByTagName('img')[0].getAttribute('data-relative-path');
        
        // Clean price string
        var priceFiltered = price.replace('₱', '').trim();

        document.getElementById("edit-product-id").value = id;
        document.getElementById("edit-name").value = name;
        document.getElementById("edit-description").value = description;
        document.getElementById("edit-price").value = priceFiltered;
        document.getElementById("edit-current-image").src = "{{ asset('') }}" + imageRelativePath;

        var statusSelect = document.getElementById("edit-status");
        statusSelect.value = status;

        var categorySelect = document.getElementById("edit-category");
        // Decode HTML entities if needed
        var txt = document.createElement("textarea");
        txt.innerHTML = category;
        categorySelect.value = txt.value;

        // Set Update Action URL
        document.getElementById("editProductForm").action = "/admin/products/" + id;
      }
    }

    for (var i = 0; i < deleteLinks.length; i++) {
      deleteLinks[i].onclick = function (e) {
        e.preventDefault();
        deleteProductModal.style.display = "flex";
        var row = this.parentNode.parentNode;
        var id = row.cells[0].innerText;
        document.getElementById("delete-product-id").value = id;

        // Set Delete Action URL
        document.getElementById("deleteProductForm").action = "/admin/products/" + id;
      }
    }

    var closeButtons = document.getElementsByClassName("close-modal");
    for (var i = 0; i < closeButtons.length; i++) {
      closeButtons[i].onclick = function () {
        deleteProductModal.style.display = "none";
      }
    }

    // Helper functions for Toasts
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

    // Handle status toggle via AJAX
    document.querySelectorAll('.stock-toggle').forEach(toggle => {
      toggle.addEventListener('change', function () {
        const productId = this.dataset.id;
        const hasStock = this.checked;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('{{ route('admin.products.toggle') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({
            product_id: productId,
            has_stock: hasStock,
          }),
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              toastSuccess('Stock status updated successfully!');
            } else {
              toastError('Failed to update stock status: ' + data.message);
              // Revert the toggle on failure
              this.checked = !this.checked;
            }
          })
          .catch(error => {
            toastError('An error occurred.');
            console.error('Error:', error);
            this.checked = !this.checked;
          });
      });
    });

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
