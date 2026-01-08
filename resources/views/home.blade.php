<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Café Athena</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Styles -->
    {{-- Main Home CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
    
</head>
<body>

    <header>
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
    </header>

    <main>
        <section id="hero">
          <h1 class="title">Café Athena</h1>
          <p>Where every cup is a masterpiece, inspired by the wisdom of the gods.</p>
          <a href="#featured-products" class="cta-button">Explore Our Menu</a>
        </section>

        <section id="about" class="section">
          <h2 class="section-title">Our Story</h2>
          <p>
            Nestled in the heart of the city, Cafe Athena is more than just a coffee shop; it's a sanctuary for thinkers,
            dreamers, and creators. Inspired by the wisdom and grace of the goddess Athena, we are dedicated to the craft of
            brewing the perfect cup of coffee. Our beans are ethically sourced from the finest growers around the world, and
            each blend is roasted to perfection to bring out its unique, divine flavor. Join us for a taste of inspiration.
          </p>
        </section>

        <section id="featured-products" class="section">
          <h2 class="section-title">Featured Offerings</h2>
          <div class="product-grid">
            <div class="product-card">
              <img src="{{ asset('assets/uploads/hot-brew/the-strategist-latte.webp') }}" alt="The Strategist Latte">
              <div class="product-card-content">
                <h3>The Strategist's Latte</h3>
                <p>A smooth, creamy latte, perfectly balanced to sharpen your focus and inspire your next great idea.</p>
              </div>
            </div>
            <div class="product-card">
              <img src="{{ asset('assets/uploads/iced-&-cold/the-oracle-mocha.webp') }}" alt="The Oracle's Mocha">
              <div class="product-card-content">
                <h3>The Oracle's Mocha</h3>
                <p>A rich and decadent mocha, blending dark chocolate and bold espresso for a truly prophetic experience.
                </p>
              </div>
            </div>
            <div class="product-card">
              <img src="{{ asset('assets/uploads/pastry/baklava.webp') }}" alt="Baklava">
              <div class="product-card-content">
                <h3>Ambrosial Baklava</h3>
                <p>A heavenly pastry with layers of flaky phyllo, chopped nuts, and sweet honey. A treat worthy of the gods.
                </p>
              </div>
            </div>
          </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
          <div class="logo">
            <img src="{{ asset('assets/images/cafe-atina-logo-nobg.png') }}" alt="Cafe Athena Logo">
          </div>
          <p>&copy; {{ date("Y") }} Cafe Athena. All Rights Reserved.</p>
          <div class="social-links">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          </div>
        </div>
    </footer>

    <script>
        // Simple script to activate nav links on scroll
        const sections = document.querySelectorAll('.section');
        const navLi = document.querySelectorAll('nav .nav-links a');

        window.addEventListener('scroll', () => {
          let current = '';
          sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (pageYOffset >= sectionTop - 60) {
              current = section.getAttribute('id');
            }
          })

          navLi.forEach(a => {
            a.classList.remove('active');
            if (a.getAttribute('href').includes(`#${current}`)) {
              a.classList.add('active');
            }
          })
        });
    </script>
</body>
</html>