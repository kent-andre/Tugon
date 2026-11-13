<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    /* Same centered card as before, with the Tugon look */
    body.fp { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif !important; background: #F2F3F7; color: #18213F; }
    .fp .register-card { border: 1px solid #DDE1EA; border-radius: 14px; box-shadow: none; animation: fpIn .35s ease-out both; }
    .fp h2, .fp h5 { font-family: 'Host Grotesk', sans-serif; color: #18213F; letter-spacing: -0.015em; }
    .fp .fp-seal { width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 2px solid #3FB37F; background: #fff; }
    .fp .fp-back { display: inline-flex; align-items: center; gap: .45rem; font-size: .92rem; font-weight: 700; color: #3D4ED1; text-decoration: none; }
    .fp .fp-back::before { content: ''; width: .45em; height: .45em; border-left: 2px solid currentColor; border-bottom: 2px solid currentColor; transform: rotate(45deg); transition: transform .2s; }
    .fp .fp-back:hover::before { transform: translateX(-3px) rotate(45deg); }
    .fp .form-label { font-weight: 700; }
    .fp .form-control { border: 1px solid #DDE1EA; border-radius: 8px; min-height: 48px; background: #fff; transition: border-color .2s, box-shadow .2s; }
    .fp .form-control:focus { border-color: #3D4ED1; box-shadow: 0 0 0 3px rgba(61,78,209,.15); }
    .fp .btn-gold { background: #13204A !important; border: 0 !important; color: #fff !important; border-radius: 8px; padding: .8rem; font-weight: 700; transition: background-color .2s, transform .1s; }
    .fp .btn-gold:hover { background: #1E2E66 !important; }
    .fp .btn-gold:active { transform: scale(.98); }
    .fp .fp-sent-icon { width: 56px; height: 56px; border-radius: 50%; background: #E7F4EC; color: #1E8E4A; display: inline-flex; align-items: center; justify-content: center; animation: fpPop .4s cubic-bezier(.3,1.4,.5,1) .2s both; }
    .fp .fp-note { font-size: .88rem; color: #5E6681; border-top: 1px solid #DDE1EA; margin-top: 1.5rem; padding-top: 1rem; }
    .fp a:focus-visible, .fp button:focus-visible { outline: 3px solid #7FB2FF; outline-offset: 2px; }
    @keyframes fpIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
    @keyframes fpPop { from { opacity: 0; transform: scale(.6); } to { opacity: 1; transform: none; } }
    @media (prefers-reduced-motion: reduce) { .fp * { animation: none !important; transition: none !important; } }
  </style>
</head>
<body class="fp">

  <div class="container">
    <div class="register-card" style="max-width: 480px; text-align: center;">

      <h5 class="fw-bold mb-0 d-inline-flex align-items-center gap-2"><span style="width:40px;height:40px;display:inline-flex">@include('partials.tugon-logo')</span>Tugon</h5>
      <p class="page-subtext mb-4">Barangay Alejal, Carmen, Davao del Norte</p>

      <div class="text-start">
        <a href="{{ url('/login') }}" class="fp-back">Back to log in</a>

        <h2 class="fw-bold mt-4 mb-1">Forgot your password?</h2>
        <p class="page-subtext mb-4">
          Enter the email you use for Tugon and we'll send you a link to set a new password.
        </p>

        {{-- Sends a real reset link (PasswordResetController@send). The message is the
             same whether or not the email has an account, so the page never reveals
             who is registered. --}}
        @if (session('sent'))
          <div class="text-center mb-4" role="status">
            <span class="fp-sent-icon mb-3" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
            </span>
            <h5 class="fw-bold mb-2">Check your email</h5>
            <p class="page-subtext mb-0">
              If <strong>{{ session('sent') }}</strong> belongs to a Tugon account, a reset link is on its way.
              It works for 60 minutes. Look in your spam folder too.
            </p>
          </div>
          <a href="{{ url('/login') }}" class="btn btn-gold w-100">Back to log in</a>
          <p class="page-subtext text-center mt-3 mb-0" style="font-size: 0.85rem;">
            No email after a few minutes? <a href="{{ url('/forgot-password') }}" style="color:#3D4ED1; font-weight:700; text-decoration:none">Send again</a>
          </p>
        @else
          @if ($errors->any())
            <div class="alert alert-danger py-2">
              {{ $errors->first('email') ?: $errors->first() }}
            </div>
          @endif
          <form action="{{ url('/forgot-password') }}" method="POST" id="forgotForm">
            @csrf
            <div class="mb-4">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                     placeholder="you@example.com" required autocomplete="email" autofocus>
            </div>

            <button type="submit" class="btn btn-gold w-100" id="sendBtn">Send reset link</button>
          </form>
          <p class="fp-note mb-0">Officials: ask the Punong Barangay to reset your password.</p>
        @endif
      </div>


    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Stop double sends while the email goes out
    var form = document.getElementById('forgotForm');
    if (form) form.addEventListener('submit', function () {
      var b = document.getElementById('sendBtn');
      b.disabled = true; b.textContent = 'Sending...';
    });
  </script>
   <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
<script src="{{ asset('js/pabarang.js') }}"></script>
</body>
</html>