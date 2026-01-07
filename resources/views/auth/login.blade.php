<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Café Athena</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  {{-- Using basic asset() helper instead of Vite --}}
  <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
  
  <!-- Toastify CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  {{-- Assuming you might have a global toast.js helper, referencing it if it exists --}}
  @if(file_exists(public_path('assets/js/toast.js')))
    <script src="{{ asset('assets/js/toast.js') }}"></script>
  @endif
</head>

<body>
  <div class="left-login-container">
    <div class="logo-container">
      <div class="logo-container-inner">
        <img class="cafe-logo-left" src="{{ asset('assets/images/cafe-atina-logo.png') }}" alt="Cafe Athena Logo">
        <h2 class="cafe-name">Café Athena</h2>
      </div>
      <a href="{{ route('home') }}" class="back-navigation">
        <i class="fa-solid fa-arrow-left fa-xs"></i>
        <h5>Back to Home</h5>
      </a>
    </div>
    <div class="left-footer-text">
      <h1>Brew Fresh. Serve Fast. Enjoy Anywhere.</h1>
      <p>From early risers to night owls, Cafe Athena brings handcrafted coffee right to your hands — freshly brewed,
        anytime, anywhere.</p>
    </div>

  </div>

  <div class="right-login-container">
    <img class="cafe-logo-right" src="{{ asset('assets/images/cafe-atina-logo.png') }}" alt="Cafe Athena Logo">
    <h1>Welcome to Café Athena</h1>
    <p>Log in to start your day with a cup of coffee</p>
    
    <form class="login-form" action="{{ route('login') }}" method="post">
      @csrf
      
      <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
      {{-- Kept inline errors as they are standard Laravel, but also ensuring Toasts work --}}
      
      <input type="password" name="password" placeholder="Password" required>

      <button type="submit">Login</button>
    </form>
    
    <div class="register-link">
      <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
    </div>

    <div class="right-footer-text">
      <p>"Brewed with love, served with purpose."</p>
      <small>© {{ date('Y') }} Cafe Athena. All rights reserved.</small>
    </div>
  </div>

  <script>
    // Helper function to match your original toast.js if possible, or direct Toastify usage
    function toastError(message) {
      Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: "top", 
        position: "right", 
        backgroundColor: "#ff5f6d",
      }).showToast();
    }

    function toastSuccess(message) {
      Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: "top", 
        position: "right", 
        backgroundColor: "#4bb543", // Green for success
      }).showToast();
    }
  </script>

  {{-- Handle Session Errors (Toast) --}}
  @if(session('error'))
    <script>
      toastError("{{ session('error') }}");
    </script>
  @endif

  {{-- Handle Validation Errors (Toast - displaying the first one) --}}
  @if($errors->any())
    <script>
      toastError("{{ $errors->first() }}");
    </script>
  @endif

</body>
</html>