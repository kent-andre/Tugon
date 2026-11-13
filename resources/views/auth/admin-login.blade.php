<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="login-page">
 
  <div class="container-fluid p-0">
    <div class="row g-0">
 
      <!-- ================= LEFT SIDE: Branding panel with photo carousel ================= -->
      <div class="col-md-5 d-none d-md-block brand-panel">
 
        <div id="brandCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
          <div class="carousel-inner h-100">
            <div class="carousel-item active h-100">
              <img src="images/login1.png"
                   class="brand-carousel-img" alt="Barangay Alejal hall">
            </div>
            <div class="carousel-item h-100">
              <img src="https://placehold.co/900x1200/2C2C2A/FFFFFF?text=Community+Event"
                   class="brand-carousel-img" alt="Barangay Alejal community event">
            </div>
            <div class="carousel-item h-100">
              <img src="https://placehold.co/900x1200/2C2C2A/FFFFFF?text=Neighborhood"
                   class="brand-carousel-img" alt="Barangay Alejal neighborhood">
            </div>
          </div>
 
          <div class="carousel-indicators" style="z-index: 3;">
            <button type="button" data-bs-target="#brandCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#brandCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#brandCarousel" data-bs-slide-to="2"></button>
          </div>
        </div>
 
        <div class="brand-overlay">
          <div class="d-flex align-items-center gap-2 mb-5">
            <div class="brand-logo" style="overflow:hidden;">
            <svg viewBox="0 0 100 100" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Tugon logo">
      <defs>
        <linearGradient id="pbGold4" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#FFDD85"/>
          <stop offset="100%" stop-color="#E4C65E"/>
        </linearGradient>
      </defs>
      <rect width="100" height="100" rx="22" fill="url(#pbGold4)"/>
      <path d="M50 18 L67 33 L59 33 L50 25 L41 33 L33 33 Z" fill="#2E3AA8"/>
      <text x="50" y="77" font-family="'Lato',Arial,sans-serif" font-weight="800" font-size="42" text-anchor="middle" fill="#2E3AA8" letter-spacing="-3">PB</text>
    </svg>
        </div>
 
      </div>
 
      <!-- ================= RIGHT SIDE: Admin login form ================= -->
      <div class="col-md-7 form-panel">
        <div class="form-wrapper">
 
          @include('partials.role-switch', ['active' => 'admin'])

          <h2 class="fw-bold mb-1">Official Login</h2>
          <p class="page-subtext mb-4">Sign in with your barangay official account</p>
 
          <form action="#" method="POST">
 
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" id="email" name="email"
                     placeholder="official@alejal.gov.ph" required>
            </div>
 
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Enter your password" required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                  Show
                </button>
              </div>
            </div>
 
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="rememberMe">
                <label class="form-check-label" for="rememberMe">
                  Remember me
                </label>
              </div>
              <a href="{{ url('/forgot-password') }}" class="link-gold" style="font-size: 0.85rem;">Forgot password?</a>
            </div>
 
            <button type="submit" class="btn btn-gold w-100">Login as Official</button>
 
          </form>
 
        </div>
      </div>
 
    </div>
  </div>
 
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/script.js') }}"></script>

  <script>
    // DEMO-ONLY: fakes a successful admin login, same trick as the
    // resident login page. No credentials are actually checked yet.
    document.querySelector('form').addEventListener('submit', function (e) {
      e.preventDefault();
      window.location.href = "{{ url('/admin/dashboard') }}";
    });
  </script>

   <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
<script src="{{ asset('js/pabarang.js') }}"></script>
</body>
</html>