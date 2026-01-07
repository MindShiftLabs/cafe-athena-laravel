<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>User Management - Café Athena</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/admin-dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/admin/user-management.css') }}">
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
        <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
        <li><a href="{{ route('admin.users') }}" class="active"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="{{ route('admin.products') }}"><i class="fas fa-box"></i> Products</a></li>
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
      <h1>User Management</h1>
      <p>Here you can manage all the users of the application.</p>
      <div class="header-actions">
        <form action="{{ route('admin.users') }}" method="GET">
          <input type="text" name="search" placeholder="Search users..."
            value="{{ request('search') }}">
          <button type="submit">Search</button>
          <button type="button" class="clear-btn" onclick="window.location.href='{{ route('admin.users') }}'">Clear</button>
        </form>
        <button type="button" id="addUserBtn">Add New User</button>
      </div>
    </header>

    <div class="user-table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Registered At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if ($users->isEmpty())
            <tr>
              <td colspan="7">
                <div class='no-orders'>
                  <i class='fas fa-users-slash' style='font-size: 48px; color: #ccc; margin-bottom: 10px;'></i><br>
                  No users found.
                </div>
              </td>
            </tr>
          @else
            @foreach ($users as $user)
              <tr>
                <td>{{ $user->user_id }}</td>
                <td>{{ $user->user_firstname }}</td>
                <td>{{ $user->user_lastname }}</td>
                <td>{{ $user->user_email }}</td>
                <td>{{ $user->user_role }}</td>
                <td>{{ $user->user_createdat }}</td>
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

  <!-- Add User Modal -->
  <div id="addUserModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Add New User</h2>
      <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" required>
        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastname" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <label for="role">Role:</label>
        <select id="role" name="role">
          <option value="customer">Customer</option>
          <option value="barista">Barista</option>
          <option value="admin">Admin</option>
        </select>
        <button type="submit">Add User</button>
      </form>
    </div>
  </div>

  <!-- Edit User Modal -->
  <div id="editUserModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Edit User</h2>
      {{-- Form action is set dynamically by JS --}}
      <form id="editUserForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-user-id" name="user_id">
        <label for="edit-firstname">First Name:</label>
        <input type="text" id="edit-firstname" name="firstname" required>
        <label for="edit-lastname">Last Name:</label>
        <input type="text" id="edit-lastname" name="lastname" required>
        <label for="edit-email">Email:</label>
        <input type="email" id="edit-email" name="email" required>
        <label for="edit-role">Role:</label>
        <select id="edit-role" name="role">
          <option value="customer">Customer</option>
          <option value="barista">Barista</option>
          <option value="admin">Admin</option>
        </select>
        <button type="submit">Save Changes</button>
      </form>
    </div>
  </div>

  <!-- Delete User Modal -->
  <div id="deleteUserModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Delete User</h2>
      <p>Are you sure you want to delete this user?</p>
      {{-- Form action is set dynamically by JS --}}
      <form id="deleteUserForm" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" id="delete-user-id" name="user_id">
        <button type="submit">Delete</button>
        <button type="button" class="close-modal">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    // Get the modals
    var addUserModal = document.getElementById("addUserModal");
    var editUserModal = document.getElementById("editUserModal");
    var deleteUserModal = document.getElementById("deleteUserModal");

    // Get the button that opens the modal
    var addUserBtn = document.getElementById("addUserBtn");

    // Get the <span> element that closes the modal
    var spans = document.getElementsByClassName("close");

    // When the user clicks the button, open the modal
    addUserBtn.onclick = function () {
      addUserModal.style.display = "flex";
    }

    // When the user clicks on <span> (x), close the modal
    for (var i = 0; i < spans.length; i++) {
      spans[i].onclick = function () {
        addUserModal.style.display = "none";
        editUserModal.style.display = "none";
        deleteUserModal.style.display = "none";
      }
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
      if (event.target == addUserModal || event.target == editUserModal || event.target == deleteUserModal) {
        addUserModal.style.display = "none";
        editUserModal.style.display = "none";
        deleteUserModal.style.display = "none";
      }
    }

    // Handle edit and delete button clicks
    var editLinks = document.getElementsByClassName("edit-link");
    var deleteLinks = document.getElementsByClassName("delete-link");

    for (var i = 0; i < editLinks.length; i++) {
      editLinks[i].onclick = function (e) {
        e.preventDefault();
        editUserModal.style.display = "flex";
        var row = this.parentNode.parentNode;
        var id = row.cells[0].innerText;
        var firstname = row.cells[1].innerText;
        var lastname = row.cells[2].innerText;
        var email = row.cells[3].innerText;
        var role = row.cells[4].innerText.toLowerCase();

        document.getElementById("edit-user-id").value = id;
        document.getElementById("edit-firstname").value = firstname;
        document.getElementById("edit-lastname").value = lastname;
        document.getElementById("edit-email").value = email;
        document.getElementById("edit-role").value = role;

        // Set the form action dynamically
        document.getElementById("editUserForm").action = "/admin/users/" + id;
      }
    }

    for (var i = 0; i < deleteLinks.length; i++) {
      deleteLinks[i].onclick = function (e) {
        e.preventDefault();
        deleteUserModal.style.display = "flex";
        var row = this.parentNode.parentNode;
        var id = row.cells[0].innerText;
        document.getElementById("delete-user-id").value = id;
        
        // Set the form action dynamically
        document.getElementById("deleteUserForm").action = "/admin/users/" + id;
      }
    }

    var closeButtons = document.getElementsByClassName("close-modal");
    for (var i = 0; i < closeButtons.length; i++) {
      closeButtons[i].onclick = function () {
        deleteUserModal.style.display = "none";
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
