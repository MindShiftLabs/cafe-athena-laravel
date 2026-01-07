<div class="logo">
  <a href="{{ route('home') }}"><img src="{{ asset('assets/images/cafe-atina-logo-nobg.png') }}" alt="Cafe Athena Logo"></a>
</div>
<nav>
  <div class="nav-links">
    <a href="{{ route('home') }}#hero" class="active">Home</a>
    <a href="{{ route('home') }}#about">About</a>
    <a href="{{ route('home') }}#featured-products">Menu</a>
  </div>
  <div class="nav-auth">
    @auth
      {{-- Dynamic Dashboard Link based on Role --}}
      @if(Auth::user()->user_role === 'admin')
         <a href="{{ route('admin.dashboard') }}" class="nav-button">Dashboard</a>
      @elseif(Auth::user()->user_role === 'barista')
         <a href="{{ route('barista.dashboard') }}" class="nav-button">Dashboard</a>
      @else
         <a href="{{ route('customer.dashboard') }}" class="nav-button">Dashboard</a>
      @endif

      {{-- Logout Form --}}
      <form method="POST" action="{{ route('logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="nav-button-secondary" style="border: none; cursor: pointer; font-family: inherit; font-size: inherit;">Logout</button>
      </form>
    @else
      <a href="{{ route('login') }}" class="nav-button-secondary">Login</a>
      <a href="{{ route('register') }}" class="nav-button">Register</a>
    @endauth
  </div>
</nav>