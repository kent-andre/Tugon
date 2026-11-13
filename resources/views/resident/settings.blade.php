<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Settings - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --ref-blue: #4F5FE0; --ref-navy-text: #1E2340; --ref-gray-text: #6B7280;
      --ref-bg: #F5F6FA; --ref-border: #E4E6EF;
    }
    body { font-family: 'Lato', sans-serif; color: var(--ref-navy-text); background: var(--ref-bg); }

    /* ===== Sidebar (shared across all resident pages) ===== */
    :root { --navy: #181a35; --navy-active: #5b52f5; }
    .main { margin-left: 250px; }
    .avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--navy-active); color: #fff;
      display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; cursor: pointer; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 18px 40px 0; }

    .page-wrap { max-width: 680px; margin: 0 auto; padding: 40px 20px; }
    .settings-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 14px; padding: 28px; margin-bottom: 20px; }
    .settings-title { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
    .settings-desc { font-size: 12.5px; color: var(--ref-gray-text); margin-bottom: 18px; }
    .account-row { display: flex; justify-content: space-between; align-items: center; font-size: 13.5px; padding: 8px 0; }
    .account-row + .account-row { border-top: 1px solid var(--ref-border); }
    .account-row .label { color: var(--ref-gray-text); }

    /* Larger Text mode: the styles live in the shared sidebar partial (html.large-text),
       so it applies on every resident page. */
    .switch-row { padding-left: 56px; min-height: 28px; }
    .switch-row .form-check-input { width: 42px; height: 22px; margin-left: -56px; margin-top: 0; cursor: pointer; }
    .switch-row .form-check-label { font-size: 13.5px; font-weight: 600; cursor: pointer; line-height: 22px; }
    .switch-row + .switch-row { margin-top: 14px; }
    .switch-hint { font-size: 12px; color: var(--ref-gray-text); margin-top: 2px; }

    /* Appearance: three choices side by side */
    .theme-opts { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
    .theme-opt input { position: absolute; opacity: 0; pointer-events: none; }
    .theme-opt label {
      display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 14px 8px; cursor: pointer;
      border: 2px solid var(--ref-border); border-radius: 12px; font-size: 13px; font-weight: 700; text-align: center;
    }
    .theme-opt label i { font-size: 22px; }
    .theme-opt input:checked + label { border-color: var(--ref-blue); color: var(--ref-blue); box-shadow: 0 0 0 3px rgba(61,78,209,.15); }
    .theme-opt input:focus-visible + label { outline: 2px solid var(--ref-blue); outline-offset: 2px; }
    .theme-swatch { width: 44px; height: 28px; border-radius: 6px; border: 1px solid var(--ref-border); }
    .sw-light { background: linear-gradient(135deg, #FFFFFF 50%, #EEF0F8 50%); }
    .sw-dark { background: linear-gradient(135deg, #161C33 50%, #0E1324 50%); }
    .sw-system { background: linear-gradient(135deg, #FFFFFF 50%, #161C33 50%); }

    .pw-form .form-label { font-size: 13px; font-weight: 600; }
  </style>
</head>
<body>

  @php
    $residentName  = session('resident.full_name', 'Resident');
    $residentEmail = session('resident.email', '');
  @endphp

  @include('partials.resident-sidebar')

  <div class="main">
    <div class="top-bar">
      <h5 class="fw-bold mb-0">Settings</h5>
    </div>

  <div class="page-wrap">
    <h3 class="fw-bold mb-4">Settings</h3>

    {{-- Read-only. Comes from the login session for now. --}}
    <div class="settings-card">
      <div class="settings-title">Account</div>
      <div class="settings-desc">Your login details. To correct them, send a request from your profile.</div>
      <div class="account-row"><span class="label">Name</span><span class="fw-bold">{{ $residentName }}</span></div>
      <div class="account-row"><span class="label">Login email</span><span class="fw-bold">{{ $residentEmail }}</span></div>
      <a href="{{ url('/profile') }}" class="btn btn-outline-secondary btn-sm mt-3"><i class="ti ti-user"></i> Go to My Profile</a>
    </div>

    {{-- Appearance and Accessibility are remembered in this browser and applied
         on every resident page by the sidebar partial. --}}
    <div class="settings-card">
      <div class="settings-title">Appearance</div>
      <div class="settings-desc">Choose how Tugon looks on this device.</div>
      <div class="theme-opts" role="radiogroup" aria-label="Theme">
        <div class="theme-opt">
          <input type="radio" name="theme" id="themeLight" value="light">
          <label for="themeLight"><span class="theme-swatch sw-light"></span><i class="ti ti-sun"></i>Light</label>
        </div>
        <div class="theme-opt">
          <input type="radio" name="theme" id="themeDark" value="dark">
          <label for="themeDark"><span class="theme-swatch sw-dark"></span><i class="ti ti-moon"></i>Dark</label>
        </div>
        <div class="theme-opt">
          <input type="radio" name="theme" id="themeSystem" value="system">
          <label for="themeSystem"><span class="theme-swatch sw-system"></span><i class="ti ti-device-mobile"></i>Match my device</label>
        </div>
      </div>
    </div>

    <div class="settings-card">
      <div class="settings-title">Accessibility</div>
      <div class="settings-desc">Make Tugon easier to read and use.</div>
      <div class="form-check form-switch switch-row">
        <input class="form-check-input" type="checkbox" role="switch" id="largeTextToggle">
        <label class="form-check-label" for="largeTextToggle">Larger Text</label>
        <div class="switch-hint">Makes text and buttons about 15% bigger.</div>
      </div>
      <div class="form-check form-switch switch-row">
        <input class="form-check-input" type="checkbox" role="switch" id="reduceMotionToggle">
        <label class="form-check-label" for="reduceMotionToggle">Reduce motion</label>
        <div class="switch-hint">Turns off animations and smooth scrolling.</div>
      </div>
    </div>

    {{-- Saved to the database by Resident\AccountController@updatePassword --}}
    <div class="settings-card" id="password">
      <div class="settings-title">Change Password</div>
      <div class="settings-desc">Use 8 or more characters with uppercase and lowercase letters, a number and a symbol. You stay logged in on this device.</div>

      @if (session('password_status'))
        <div class="alert alert-success py-2" role="status">{{ session('password_status') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ url('/settings/password') }}" class="pw-form" id="pwForm">
        @csrf
        <div class="mb-3">
          <label for="current_password" class="form-label">Current password</label>
          <input type="password" class="form-control" id="current_password" name="current_password" required autocomplete="current-password">
        </div>
        <div class="mb-3">
          <label for="new_password" class="form-label">New password</label>
          <input type="password" class="form-control" id="new_password" name="password" minlength="8" required autocomplete="new-password">
        </div>
        <div class="mb-3">
          <label for="new_password_confirmation" class="form-label">Type the new password again</label>
          <input type="password" class="form-control" id="new_password_confirmation" name="password_confirmation" minlength="8" required autocomplete="new-password">
          <p id="pwMatch" class="d-none text-danger mt-2 mb-0" style="font-size:12px;">The two new passwords do not match.</p>
          @include('partials.password-strength', ['input' => 'new_password', 'confirm' => 'new_password_confirmation', 'personal' => [$residentName, $residentEmail]])
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
          <button type="submit" class="btn btn-sm" id="pwSave" style="background: var(--ref-blue); color: #fff; font-weight: 700; padding: 8px 16px;">Save new password</button>
          <label class="form-check-label" style="font-size: 12.5px; cursor: pointer;">
            <input type="checkbox" class="form-check-input me-1" id="pwShow"> Show passwords
          </label>
        </div>
      </form>
      <p class="mt-3 mb-0" style="font-size: 12.5px; color: var(--ref-gray-text);">
        Forgot your current password? Log out and use <a href="{{ url('/forgot-password') }}" style="color: var(--ref-blue); font-weight: 600;">Forgot password</a> on the login page.
      </p>
    </div>

    <div class="settings-card">
      <div class="settings-title">Need Help?</div>
      <div class="settings-desc">For changes to your verified details, or anything else about your account, contact the barangay office.</div>
      <p style="font-size: 13.5px; margin-bottom: 4px;"><i class="ti ti-phone-call" style="color: var(--ref-blue);"></i> +63 912 345 6789</p>
      <p style="font-size: 13.5px; margin-bottom: 10px;"><i class="ti ti-mail" style="color: var(--ref-blue);"></i> barangayalejal@gmail.com</p>
      <a href="{{ url('/faq') }}" style="font-size: 13px; font-weight: 700; color: var(--ref-blue); text-decoration: none;">Read the FAQ</a>
    </div>

  </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (function () {
      var html = document.documentElement;
      function save(k, v) { try { localStorage.setItem(k, v); } catch (e) {} }
      function get(k) { try { return localStorage.getItem(k); } catch (e) { return null; } }

      // The sidebar partial already applied the saved choices to <html>; match the controls to them.
      var large = document.getElementById('largeTextToggle');
      large.checked = html.classList.contains('large-text');
      large.addEventListener('change', function () {
        html.classList.toggle('large-text', this.checked);
        save('pabarang.largeText', this.checked ? '1' : '0');
      });

      var motion = document.getElementById('reduceMotionToggle');
      motion.checked = html.classList.contains('reduce-motion');
      motion.addEventListener('change', function () {
        html.classList.toggle('reduce-motion', this.checked);
        save('pabarang.reduceMotion', this.checked ? '1' : '0');
      });

      var current = get('pabarang.theme') || 'light';
      document.querySelectorAll('input[name="theme"]').forEach(function (r) {
        r.checked = r.value === current;
        r.addEventListener('change', function () {
          save('pabarang.theme', this.value);
          if (window.pbApplyTheme) window.pbApplyTheme(this.value);
        });
      });

      // Change password: show/hide, and catch a mismatch before sending
      var pw = document.getElementById('new_password'), pw2 = document.getElementById('new_password_confirmation');
      document.getElementById('pwShow').addEventListener('change', function () {
        ['current_password', 'new_password', 'new_password_confirmation'].forEach(function (id) {
          document.getElementById(id).type = this.checked ? 'text' : 'password';
        }, this);
      });
      pw2.addEventListener('input', function () { document.getElementById('pwMatch').classList.add('d-none'); });
      document.getElementById('pwForm').addEventListener('submit', function (e) {
        if (pw.value !== pw2.value) {
          e.preventDefault();
          document.getElementById('pwMatch').classList.remove('d-none');
          pw2.focus();
          return;
        }
        var b = document.getElementById('pwSave');
        b.disabled = true; b.textContent = 'Saving...';
      });

      // After saving (or an error), bring the password card into view
      @if (session('password_status') || $errors->any())
        document.getElementById('password').scrollIntoView({ block: 'center' });
      @endif
    })();
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <script src="{{ asset('js/pabarang.js') }}"></script>
</body>
</html>