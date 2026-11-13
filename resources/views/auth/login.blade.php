<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ ($role ?? 'resident') === 'admin' ? 'Official Login' : 'Login' }} - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    /* LOGIN: the barangay photo on the left under a blue tint, with the seal
       and a short line for residents or officials. The form sits on white on the right. Own class names (lg-*). */
    :root { --navy: #13204A; --gold: #7FB2FF; --bg: #F2F3F7; --line: #D6DAE4; --ink: #18213F; --muted: #5E6681; --focus: #3D4ED1; }

    body.lg { margin: 0; min-height: 100vh; background: var(--bg); color: var(--ink); font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif; }
    .lg h1 { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif; letter-spacing: -0.02em; }

    .lg-shell { position: relative; display: grid; grid-template-columns: 1.1fr 1fr; min-height: 100vh; }

    /* Left: the photo under a blue tint */
    .lg-photo { position: relative; background: rgba(19,32,74,.72) url('{{ asset('images/barangay.png') }}') center / cover no-repeat; background-blend-mode: multiply; display: flex; flex-direction: column; justify-content: space-between; color: #fff; }
    .lg-photo-top { padding: 1.5rem; }
    .lg-brand { display: inline-flex; align-items: center; gap: .55rem; color: #fff; text-decoration: none; font-family: 'Host Grotesk', sans-serif; font-weight: 800; font-size: 1.1rem; padding: .45rem .85rem; border-radius: 8px; }
    .lg-copy { padding: 0 2.5rem 3.5rem; }
    .lg-copy img { width: 96px; height: 96px; border-radius: 50%; object-fit: cover; background: #fff; border: 3px solid #3FB37F; margin-bottom: 1.1rem; }
    .lg-copy h2 { font-family: 'Host Grotesk', sans-serif; font-weight: 800; font-size: clamp(1.9rem, 2.8vw, 2.6rem); line-height: 1.1; letter-spacing: -0.02em; margin: 0 0 .6rem; color: #fff; max-width: 16ch; }
    .lg-copy p { margin: 0; color: #D5DBF0; font-size: 1.05rem; max-width: 32rem; }
    /* Resident and official words share one spot and cross-fade */
    .lg-swap { display: grid; }
    .lg-swap > div { grid-area: 1 / 1; transition: opacity .35s ease, transform .35s ease, visibility 0s; }
    body.lg[data-role="resident"] .for-admin, body.lg[data-role="admin"] .for-resident {
      opacity: 0; transform: translateY(8px); visibility: hidden; transition: opacity .2s ease, transform .2s ease, visibility 0s linear .2s; }

    /* Right: the form on white */
    .lg-side { background: #fff; display: flex; flex-direction: column; }
    .lg-side-top { display: flex; justify-content: flex-end; padding: 1.25rem 1.75rem; }
    /* Back to home: a small outlined button, the arrow nudges left on hover */
    .lg-home { display: inline-flex; align-items: center; gap: .5rem; color: var(--ink); text-decoration: none; font-size: .92rem; font-weight: 700;
      border: 1px solid var(--line); border-radius: 999px; padding: .45rem .95rem .45rem .8rem; transition: border-color .2s, background-color .2s; }
    .lg-home::before { content: ''; width: .5em; height: .5em; border-left: 2px solid currentColor; border-bottom: 2px solid currentColor;
      transform: rotate(45deg); transition: transform .2s ease; }
    .lg-home:hover { border-color: var(--navy); background: #F3F5FB; }
    .lg-home:hover::before { transform: translateX(-3px) rotate(45deg); }
    .lg-wrap { flex: 1; display: flex; align-items: center; justify-content: center; padding: 1rem 1.5rem 3rem; }
    .lg-panel { width: 100%; max-width: 400px; }

    .lg-head { margin-bottom: 1.6rem; }
    .lg-head h1 { font-size: 2rem; font-weight: 800; margin: 0 0 .25rem; color: var(--ink); }
    .lg-head p { color: var(--muted); margin: 0; font-size: .95rem; }

    /* Resident / Official tabs: plain text with an underline */
    .lg-tabs { position: relative; display: flex; border-bottom: 1px solid var(--line); margin-bottom: 1.4rem; }
    .lg-tab { flex: 1; border: 0; background: none; padding: .7rem .5rem; font-weight: 700; color: var(--muted); transition: color .25s; }
    .lg-tab[aria-selected="true"] { color: var(--navy); }
    .lg-tab:hover { color: var(--ink); }
    /* The underline slides between the two tabs */
    .lg-ink { position: absolute; left: 0; bottom: -1px; width: 50%; height: 3px; background: var(--navy); border-radius: 3px 3px 0 0;
      transition: transform .35s cubic-bezier(.3,.7,.2,1); }
    body.lg[data-role="admin"] .lg-ink { transform: translateX(100%); }

    /* Both forms share one grid cell so the panel keeps its height */
    .lg-stack { display: grid; }
    .lg-form { grid-area: 1 / 1; }
    body.lg[data-role="resident"] .lg-form[data-role="admin"],
    body.lg[data-role="admin"] .lg-form[data-role="resident"] { visibility: hidden; }
    /* The form slides in from the side of the tab you picked */
    .lg-form { transition: opacity .3s ease, transform .3s ease, visibility 0s; }
    body.lg[data-role="admin"] .lg-form[data-role="resident"] { opacity: 0; transform: translateX(-14px); transition: opacity .18s ease, transform .18s ease, visibility 0s linear .18s; }
    body.lg[data-role="resident"] .lg-form[data-role="admin"] { opacity: 0; transform: translateX(14px); transition: opacity .18s ease, transform .18s ease, visibility 0s linear .18s; }

    .lg-field { margin-bottom: 1rem; }
    .lg-label { display: block; font-weight: 700; font-size: .92rem; margin-bottom: .35rem; }
    .lg-input { width: 100%; border: 1px solid var(--line); border-radius: 8px; padding: .7rem .85rem; font-size: 1rem; background: #fff; color: var(--ink); min-height: 46px; }
    .lg-input { transition: border-color .2s, box-shadow .2s; }
    .lg-input:focus { outline: none; border-color: var(--focus); box-shadow: 0 0 0 3px rgba(61,78,209,.15); }
    .lg-pw { position: relative; }
    .lg-pw .lg-input { padding-right: 4rem; }
    .lg-show { position: absolute; right: .35rem; top: 50%; transform: translateY(-50%); border: 0; background: none; color: var(--focus); font-weight: 700; font-size: .9rem; padding: .35rem .5rem; }

    .lg-forgot { text-align: right; font-size: .9rem; color: var(--muted); margin: -.2rem 0 1.25rem; }
    .lg-form[data-role="admin"] .lg-forgot { text-align: left; margin-top: 0; }
    .lg-form a { color: var(--focus); font-weight: 700; text-decoration: none; }
    .lg-form a:hover { text-decoration: underline; }
    .lg-btn { width: 100%; border: 0; border-radius: 8px; padding: .8rem; background: var(--navy); color: #fff; font-weight: 700; font-size: 1.02rem; }
    .lg-btn { transition: background-color .2s, transform .1s; }
    .lg-btn:hover { background: #1E2E66; }
    .lg-btn:active { transform: scale(.98); }

    /* One entrance when the page opens: the words, then the form */
    @keyframes lgIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
    @keyframes lgZoom { from { transform: scale(1.06); } to { transform: scale(1); } }
    .lg-copy { animation: lgIn .6s ease .1s both; }
    .lg-panel { animation: lgIn .6s ease .25s both; }
    .lg-photo { overflow: hidden; }
    .lg-photo::before { content: ''; position: absolute; inset: 0; background: inherit; background-blend-mode: multiply; animation: lgZoom 1.6s ease-out both; z-index: 0; }
    .lg-photo > * { position: relative; z-index: 1; }

    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation: none !important; transition: none !important; }
    }
    .lg-links { text-align: center; font-size: .92rem; color: var(--muted); margin: 1.25rem 0 0; line-height: 1.7; }

    .lg-alert { font-size: .92rem; padding: .6rem .8rem; border-radius: 8px; margin-bottom: 1rem; }
    .lg-alert.ok { background: #E7F4EC; color: #1D5A38; }
    .lg-alert.bad { background: #FBEBEA; color: #8A2318; }

    .lg-foot { color: var(--muted); font-size: .88rem; margin: 1.5rem 0 0; padding-top: 1rem; border-top: 1px solid var(--line); }
    .lg a:focus-visible, .lg button:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }

    @media (max-width: 900px) {
      .lg-shell { grid-template-columns: 1fr; }
      .lg-photo-top { padding: 1rem; }
      .lg-photo { min-height: 0; }
      .lg-copy { padding: 0 1.1rem 1.4rem; }
      .lg-copy img { width: 60px; height: 60px; margin-bottom: .6rem; }
      .lg-copy h2 { font-size: 1.5rem; }
      .lg-copy p { display: none; }
      .lg-side-top { position: absolute; top: 0; right: 0; padding: .9rem 1rem; }
      .lg-home { color: #fff; border-color: rgba(255,255,255,.5); background: rgba(11,21,48,.35); }
      .lg-home:hover { background: rgba(11,21,48,.55); border-color: #fff; }
      .lg-wrap { padding: 1.75rem 1.1rem 2.5rem; align-items: flex-start; }
      .lg-input { font-size: 16px; }
    }
  </style>
</head>
<body class="lg" data-role="{{ $role }}">
  @include('partials.page-loader')

  <div class="lg-shell">

    {{-- Left: the barangay hall under a blue tint, with words for residents or officials --}}
    <aside class="lg-photo">
      <div class="lg-photo-top"><a href="{{ url('/') }}" class="lg-brand"><span style="width:34px;height:34px;display:inline-flex">@include('partials.tugon-logo')</span>Tugon</a></div>
      <div class="lg-copy">
        <img src="{{ asset('images/logo.jpg') }}" alt="Barangay Alejal seal">
        <div class="lg-swap">
        <div class="for-resident">
          <h2>Your barangay, a few taps away</h2>
          <p>Follow your reports, request documents and read barangay notices in one place.</p>
        </div>
        <div class="for-admin">
          <h2>Barangay Alejal officials</h2>
          <p>Handle concerns, documents, blotters and notices for the residents of Alejal.</p>
        </div>
        </div>
      </div>
    </aside>

    {{-- Right: the form --}}
    <section class="lg-side">
      <div class="lg-side-top"><a href="{{ url('/') }}" class="lg-home">Back to home</a></div>

  <main class="lg-wrap">
    <div class="lg-panel">
      <div class="lg-head">
        <h1>Log in</h1>
        <p>Use the email and password of your Tugon account.</p>
      </div>

      <div class="lg-tabs" role="tablist" aria-label="Choose login type">
        <button type="button" role="tab" class="lg-tab" data-role="resident"
                aria-selected="{{ $role === 'resident' ? 'true' : 'false' }}">Resident</button>
        <button type="button" role="tab" class="lg-tab" data-role="admin"
                aria-selected="{{ $role === 'admin' ? 'true' : 'false' }}">Barangay official</button>
        <span class="lg-ink" aria-hidden="true"></span>
      </div>

      <div class="lg-stack">
            <form class="lg-form" data-role="resident"
                  action="{{ url('/login') }}" method="POST" @if ($role !== 'resident') inert @endif>
              @csrf

              @if ($role === 'resident' && session('status'))
                <div class="lg-alert ok">{{ session('status') }}</div>
              @endif

              @if ($role === 'resident' && ($errors->any() || session('error')))
                <div class="lg-alert bad">
                  {{ $errors->first('login') ?: $errors->first() ?: session('error') }}
                </div>
              @endif

              <div class="lg-field">
                <label for="email" class="lg-label">Email address</label>
                <input type="email" class="lg-input" id="email" name="email"
                       value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="email">
              </div>

              <div class="lg-field">
                <label for="password" class="lg-label">Password</label>
                <div class="lg-pw">
                  <input type="password" class="lg-input" id="password" name="password"
                         placeholder="Enter your password" required autocomplete="current-password">
                  <button class="lg-show" type="button" id="togglePassword">Show</button>
                </div>
              </div>

              <p class="lg-forgot"><a href="{{ url('/forgot-password') }}">Forgot password?</a></p>

              <button type="submit" class="lg-btn">Log in</button>

              <p class="lg-links">
                No account yet? <a href="{{ url('/register') }}">Register as a resident</a><br>
                Only reporting a concern? <a href="{{ url('/') }}#report">No account needed</a>
              </p>
            </form>

            {{-- Official form --}}
            <form class="lg-form" data-role="admin"
                  action="{{ url('/admin-login') }}" method="POST" @if ($role !== 'admin') inert @endif>
              @csrf

              @if ($role === 'admin' && ($errors->any() || session('error')))
                <div class="lg-alert bad">
                  {{ $errors->first('login') ?: $errors->first() ?: session('error') }}
                </div>
              @endif

              <div class="lg-field">
                <label for="adminEmail" class="lg-label">Email address</label>
                <input type="email" class="lg-input" id="adminEmail" name="email"
                       value="{{ old('email') }}" placeholder="official@alejal.gov.ph" required autocomplete="email">
              </div>

              <div class="lg-field">
                <label for="adminPassword" class="lg-label">Password</label>
                <div class="lg-pw">
                  <input type="password" class="lg-input" id="adminPassword" name="password"
                         placeholder="Enter your password" required autocomplete="current-password">
                  <button class="lg-show" type="button" id="toggleAdminPassword">Show</button>
                </div>
              </div>

              <p class="lg-forgot">Forgot your password? Ask the Punong Barangay to reset it.</p>

              <button type="submit" class="lg-btn">Log in as official</button>
            </form>
      </div>

      <p class="lg-foot">Trouble logging in? Visit the barangay hall during office hours.</p>
    </div>
  </main>
    </section>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/script.js') }}"></script>

  <script>
    // Resident / Official tabs: change data-role, update the URL, no reload
    (function () {
      var urls   = { resident: "{{ url('/login') }}", admin: "{{ url('/admin-login') }}" };
      var titles = { resident: 'Login - Tugon', admin: 'Official Login - Tugon' };

      function setRole(role) {
        document.body.dataset.role = role;
        document.title = titles[role];
        document.querySelectorAll('.lg-tab').forEach(function (b) {
          b.setAttribute('aria-selected', b.dataset.role === role ? 'true' : 'false');
        });
        // inert = the hidden form can't be tabbed into or clicked
        document.querySelectorAll('.lg-form').forEach(function (f) {
          f.toggleAttribute('inert', f.dataset.role !== role);
        });
        history.replaceState({ role: role }, '', urls[role]);
      }

      document.querySelectorAll('.lg-tab').forEach(function (b) {
        b.addEventListener('click', function () { setRole(b.dataset.role); });
      });
    })();

    // Show/Hide for the official password (the resident one is handled by script.js)
    document.getElementById('toggleAdminPassword').addEventListener('click', function () {
      var input = document.getElementById('adminPassword');
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      this.textContent = show ? 'Hide' : 'Show';
    });
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <script src="{{ asset('js/pabarang.js') }}"></script>
</body>
</html>
