<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Set a New Password - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>body { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif !important; }</style>
</head>
<body>

  <div class="container">
    <div class="register-card" style="max-width: 480px; text-align: center;">

      <h5 class="fw-bold mb-0 d-inline-flex align-items-center gap-2"><span style="width:40px;height:40px;display:inline-flex">@include('partials.tugon-logo')</span>Tugon</h5>
      <p class="page-subtext mb-4">Barangay Alejal</p>

      <div class="text-start">
        <a href="{{ url('/login') }}" class="link-gold" style="font-size: 0.9rem;">&larr; Go back to log in</a>

        <h2 class="fw-bold mt-4 mb-1">Set a new password</h2>

        {{-- Opened from the reset email (PasswordResetController@edit) --}}
        @if (! $valid)
          <div class="alert alert-warning mt-3" role="alert">
            This reset link is invalid or has expired. Links work once and last 60 minutes.
          </div>
          <a href="{{ url('/forgot-password') }}" class="btn btn-gold w-100">Send a new link</a>
        @else
          <p class="page-subtext mb-4">For <strong>{{ $email }}</strong>. Use at least 8 characters.</p>

          @if ($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
          @endif

          <form action="{{ url('/reset-password') }}" method="POST" id="resetForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3">
              <label for="password" class="form-label">New password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="password" name="password"
                       minlength="8" required autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button" id="togglePw">Show</button>
              </div>
            </div>

            <div class="mb-4">
              <label for="password_confirmation" class="form-label">Type it again</label>
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                     minlength="8" required autocomplete="new-password">
              <p id="matchError" class="d-none text-danger mt-2 mb-0" style="font-size: 0.85rem;">The two passwords do not match.</p>
              @include('partials.password-strength', ['input' => 'password', 'confirm' => 'password_confirmation', 'personal' => [$email]])
            </div>

            <button type="submit" class="btn btn-gold w-100" id="saveBtn">Save new password</button>
          </form>
        @endif
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (function () {
      var form = document.getElementById('resetForm');
      if (!form) return;
      var pw = document.getElementById('password'), pw2 = document.getElementById('password_confirmation');
      document.getElementById('togglePw').addEventListener('click', function () {
        var show = pw.type === 'password';
        pw.type = pw2.type = show ? 'text' : 'password';
        this.textContent = show ? 'Hide' : 'Show';
      });
      pw2.addEventListener('input', function () { document.getElementById('matchError').classList.add('d-none'); });
      form.addEventListener('submit', function (e) {
        if (pw.value !== pw2.value) {
          e.preventDefault();
          document.getElementById('matchError').classList.remove('d-none');
          pw2.focus();
          return;
        }
        var b = document.getElementById('saveBtn');
        b.disabled = true; b.textContent = 'Saving...';
      });
    })();
  </script>
  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <script src="{{ asset('js/pabarang.js') }}"></script>
</body>
</html>
