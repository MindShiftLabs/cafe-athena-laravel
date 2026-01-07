@extends('layouts.main')

@section('title', 'Welcome to Café Athena')

@section('content')
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
@endsection

@push('scripts')
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
@endpush
