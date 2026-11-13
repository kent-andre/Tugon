<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Account - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=2">

  {{-- Register page styles live right here, so the page looks right even if style.css is cached or not updated. --}}
  <style>
/* ============================================================
   REGISTER: the left side shows the resident card being filled in
   as you type. One idea, kept quiet: no gradients, no decoration.
   ============================================================ */
:root {
  --navy: #13204A; --navy-2: #1B2A5C; --ink: #0E1A3A; --paper: #F6F7FB; --line: #DDE2EE;
  --text: #16203F; --muted: #5D6785; --gold: #7FB2FF; --gold-deep: #2459C4; --green: #3FB37F; --blue: #3D4ED1;
}
body { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif; color: var(--text); background: var(--paper); }
h1, h2, .display { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif; letter-spacing: -0.02em; }
.register-main h1, .register-main h2 { color: var(--text); }
.register-aside h1 { color: #fff; }

.register-shell { display: grid; grid-template-columns: minmax(330px, 440px) 1fr; min-height: 100vh; }

/* ---- Left: solid navy, the card, and what happens next ---- */
.register-aside {
  position: sticky; top: 0; height: 100vh; overflow-y: auto;
  display: flex; flex-direction: column; gap: 2rem; padding: 2.5rem 2.25rem; color: #fff; background: var(--navy);
}
.aside-brand { display: flex; align-items: center; gap: .6rem; font-family: 'Host Grotesk', sans-serif; font-weight: 800; font-size: 1.15rem; color: #fff; text-decoration: none; }
.aside-brand svg { width: 34px; height: 34px; transform: rotate(3deg); }
.register-aside h1 { font-size: 1.75rem; font-weight: 800; line-height: 1.15; margin: 0 0 .4rem; }
.aside-lead { color: #B9C2E3; margin: 0; }

/* The resident card */
.res-card {
  background: #F6F9FF; color: var(--ink); border-radius: 14px; overflow: hidden;
  box-shadow: 0 1px 0 rgba(255,255,255,.08), 0 18px 40px rgba(0,0,0,.35); transform: rotate(-1.5deg);
}
.res-card-top { display: flex; align-items: center; gap: .7rem; padding: .7rem 1rem; background: var(--gold); }
.res-card-top img { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; background: #fff; border: 2px solid rgba(20,24,58,.2); }
.res-card-top .t1 { font-family: 'Host Grotesk', sans-serif; font-weight: 800; font-size: .95rem; line-height: 1.1; }
.res-card-top .t2 { font-size: .75rem; opacity: .8; }
.res-card-body { display: flex; gap: 1rem; padding: 1rem; align-items: center; }
.res-photo { flex-shrink: 0; width: 64px; height: 76px; border-radius: 8px; background: #DDE8FB; display: flex; align-items: center; justify-content: center;
             font-family: 'Host Grotesk', sans-serif; font-weight: 800; font-size: 1.6rem; color: #3A5BA8; }
.res-fields { min-width: 0; flex: 1; }
.res-label { font-size: .68rem; color: #8A8370; line-height: 1.2; }
.res-name { font-family: 'Host Grotesk', sans-serif; font-weight: 800; font-size: 1.15rem; line-height: 1.15; margin-bottom: .45rem;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.res-name.is-empty { color: #B9B19A; }
.res-row { display: flex; gap: 1rem; }
.res-val { font-weight: 700; font-size: .88rem; }
.res-val.is-empty { color: #B9B19A; font-weight: 400; }
.res-card-foot { display: flex; justify-content: space-between; align-items: center; padding: .55rem 1rem; border-top: 1px dashed #C9D8F2; font-size: .75rem; color: #8A8370; }
.res-stamp { border: 2px solid #B23A3A; color: #B23A3A; border-radius: 6px; padding: 0 .4rem; font-weight: 700; font-size: .72rem; transform: rotate(-4deg); }

.aside-steps { list-style: none; padding: 0; margin: auto 0 0; }
.aside-steps li { display: flex; gap: .85rem; align-items: flex-start; margin-bottom: 1rem; color: #C9D0EC; line-height: 1.4; font-size: .95rem; }
.aside-steps li strong { display: block; color: #fff; }
.aside-steps .step-num { flex: 0 0 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .85rem; border: 1.5px solid rgba(255,255,255,.35); }
.aside-steps li:first-child .step-num { background: var(--green); color: var(--ink); border-color: var(--green); }

/* ---- Right: the form ---- */
.register-main { padding: 3rem 2.5rem 4rem; display: flex; justify-content: center; }
.register-shell .register-card { background: transparent; border: 0; border-radius: 0; padding: 0; margin: 0; max-width: 660px; width: 100%; }
.register-card .form-heading { font-size: 2rem; font-weight: 800; margin: 0 0 .25rem; }
.register-card .page-subtext { color: var(--muted); }

/* Each part of the form sits on its own sheet */
.form-sheet { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 1.4rem 1.4rem .4rem; margin-top: 1.1rem; }
.form-section { display: flex; align-items: baseline; gap: .6rem; font-size: 1.1rem; font-weight: 800; margin: 0 0 1rem; }
.form-section .n { font-family: 'Host Grotesk', sans-serif; color: var(--gold-deep); font-size: .95rem; }
.form-section small { font-family: 'Host Grotesk', sans-serif; font-weight: 400; font-size: .85rem; color: var(--muted); margin-left: auto; }

.register-card .form-label { font-weight: 700; font-size: .92rem; margin-bottom: .35rem; }
.register-card .form-control, .register-card .form-select {
  border: 1.5px solid var(--line); border-radius: 10px; padding: .7rem .85rem; background-color: #FBFCFE; font-size: 1rem; min-height: 46px;
}
.register-card .form-control:focus, .register-card .form-select:focus { background-color: #fff; border-color: var(--blue); box-shadow: 0 0 0 .2rem rgba(61,78,209,.15); }
.register-card .form-control:disabled { background-color: #F0F2F8; color: var(--muted); }
.hint { font-size: .82rem; color: var(--muted); margin-top: .3rem; }

.res-val, .res-name { overflow-wrap: anywhere; min-width: 0; }  /* long names wrap on the card */
.pw-wrap { position: relative; }
.pw-wrap .form-control { padding-right: 4rem; }  /* room for the Show button */
.pw-toggle { position: absolute; right: .5rem; top: 50%; transform: translateY(-50%); border: 0; background: none; color: var(--blue); font-weight: 700; font-size: .85rem; padding: .3rem .4rem; }

#idPreviewBox { background: #F4F6FC; border: 1.5px dashed #B9C1E4; border-radius: 12px; }

.consent-box { background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: .9rem 1rem .9rem 2.4rem; margin: 1.25rem 0; }
.register-card .btn-gold { background: var(--gold); border-color: var(--gold); color: var(--ink); font-weight: 800; border-radius: 10px; padding: .85rem; font-size: 1.05rem; }
.register-card .btn-gold:hover { background: #A3C6FF; border-color: #A3C6FF; }
.link-gold { color: var(--gold-deep) !important; font-weight: 700; }
a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }

@media (max-width: 900px) {
  .register-shell { grid-template-columns: 1fr; }
  .register-aside { position: static; height: auto; padding: 1.75rem 1.25rem; gap: 1.25rem; }
  .register-aside h1 { font-size: 1.45rem; }
  .res-card { transform: none; max-width: 420px; }
  .aside-steps { display: none; }   /* the steps are repeated in the note at the end of the form */
  .register-main { padding: 1.5rem 1rem 3rem; }
  .form-sheet { padding: 1.1rem 1rem .2rem; }
}
@media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
  </style>
</head>
<body>

  @include('partials.page-loader')


  <div class="register-shell">

    <aside class="register-aside">
      <a href="{{ url('/') }}" class="aside-brand">
        @include('partials.tugon-logo')
        Tugon
      </a>

      <div>
        <h1>Become a registered resident of Alejal</h1>
        <p class="aside-lead">Your account lets you request documents and follow every report you send.</p>
      </div>

      {{-- Live preview: fills in as the resident types. Nothing here is saved until they submit. --}}
      <div class="res-card" aria-hidden="true">
        <div class="res-card-top">
          <img src="{{ asset('images/logo.jpg') }}" alt="">
          <div><div class="t1">Barangay Alejal</div><div class="t2">Carmen, Davao del Norte</div></div>
        </div>
        <div class="res-card-body">
          <div class="res-photo" id="cardInitials">?</div>
          <div class="res-fields">
            <div class="res-label">Resident</div>
            <div class="res-name is-empty" id="cardName">Your name</div>
            <div class="res-row">
              <div><div class="res-label">Purok</div><div class="res-val is-empty" id="cardPurok">Not set</div></div>
              <div><div class="res-label">Age</div><div class="res-val is-empty" id="cardAge">Not set</div></div>
            </div>
          </div>
        </div>
        <div class="res-card-foot"><span>Resident No. given on approval</span><span class="res-stamp">For approval</span></div>
      </div>

      <ol class="aside-steps">
        <li><span class="step-num">1</span><span><strong>Fill out this form</strong>Your details and a photo of a valid ID.</span></li>
        <li><span class="step-num">2</span><span><strong>The barangay checks it</strong>An official compares your details with your ID.</span></li>
        <li><span class="step-num">3</span><span><strong>Log in</strong>Once approved, your account is ready.</span></li>
      </ol>
    </aside>

    <main class="register-main">
    <div class="register-card">

      <div class="mb-2">
        <h2 class="form-heading">Create your account</h2>
        <p class="page-subtext mb-0">It takes about 5 minutes. Have a valid ID ready.</p>
      </div>

      @if ($errors->any())
        <div class="alert alert-danger py-2 mt-3">
          <strong>Please check the form.</strong>
          <ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      <form action="{{ url('/register') }}" method="POST" enctype="multipart/form-data" id="registerForm">
      @csrf

        <div class="form-sheet"><div class="row">
          <div class="col-12"><h2 class="form-section"><span class="n">1</span>About you</h2></div>

          <!-- Full Name, split into parts for official records -->
          <div class="col-md-4 mb-3">
            <label for="firstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstName" name="first_name" value="{{ old('first_name') }}"
                   placeholder="Juan" required>
          </div>
          <div class="col-md-4 mb-3">
            <label for="middleName" class="form-label">Middle Name</label>
            <input type="text" class="form-control" id="middleName" name="middle_name" value="{{ old('middle_name') }}"
                   placeholder="(optional)">
          </div>
          <div class="col-md-4 mb-3">
            <label for="lastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastName" name="last_name" value="{{ old('last_name') }}"
                   placeholder="Dela Cruz" required>
          </div>

          <!-- Birthdate + auto-computed Age, so residents don't enter age by hand and it can't drift out of sync -->
          <div class="col-md-6 mb-3">
            <label for="birthdate" class="form-label">Birthdate</label>
            <input type="date" class="form-control" id="birthdate" name="birthdate" value="{{ old('birthdate') }}" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="age" class="form-label">Age</label>
            <input type="text" class="form-control" id="age" name="age" placeholder="Auto-filled from birthdate" disabled>
          </div>

          <!-- Gender -->
          <div class="col-md-6 mb-3">
            <label for="gender" class="form-label">Gender</label>
            <select class="form-select" id="gender" name="gender" required>
              <option value="" selected disabled>Select gender</option>
              <option value="Male" @selected(old('gender') === 'Male')>Male</option>
              <option value="Female" @selected(old('gender') === 'Female')>Female</option>
              <option value="Prefer not to say" @selected(old('gender') === 'Prefer not to say')>Prefer not to say</option>
            </select>
          </div>

          <!-- Civil Status -->
          <div class="col-md-6 mb-3">
            <label for="civilStatus" class="form-label">Civil Status</label>
            <select class="form-select" id="civilStatus" name="civil_status" required>
              <option value="" selected disabled>Select civil status</option>
              <option value="Single" @selected(old('civil_status') === 'Single')>Single</option>
              <option value="Married" @selected(old('civil_status') === 'Married')>Married</option>
              <option value="Widowed" @selected(old('civil_status') === 'Widowed')>Widowed</option>
              <option value="Separated" @selected(old('civil_status') === 'Separated')>Separated</option>
            </select>
          </div>

          <!-- Occupation -->
          <div class="col-12 mb-3">
            <label for="occupation" class="form-label">Occupation</label>
            <input type="text" class="form-control" id="occupation" name="occupation" value="{{ old('occupation') }}"
                   placeholder="(optional)">
          </div>

        </div></div>
        <div class="form-sheet"><div class="row">
          <div class="col-12"><h2 class="form-section"><span class="n">2</span>Contact and address</h2></div>

          <!-- Email -->
          <div class="col-md-6 mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                   placeholder="you@example.com" required>
          </div>

          <!-- Mobile Number -->
          <div class="col-md-6 mb-3">
            <label for="mobileNumber" class="form-label">Mobile Number</label>
            <input type="tel" class="form-control" id="mobileNumber" name="mobile_number" value="{{ old('mobile_number') }}"
                   placeholder="09XXXXXXXXX" pattern="09[0-9]{9}" required>
          </div>

          <!-- Purok, same list as the concern form -->
          <div class="col-md-4 mb-3">
            <label for="purok" class="form-label">Purok</label>
            <select class="form-select" id="purok" name="purok" required>
              <option value="" disabled @selected(! old('purok'))>Select your purok</option>
              @foreach (\App\Http\Controllers\ResidentAuthController::PUROKS as $p)
                <option value="{{ $p }}" @selected(old('purok') === $p)>{{ $p }}</option>
              @endforeach
            </select>
          </div>

          <!-- Address -->
          <div class="col-md-8 mb-3">
            <label for="address" class="form-label">Complete Address</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}"
                   placeholder="House No., Street" required>
          </div>

          <!-- Emergency Contact Person -->
          <div class="col-12 mb-3">
            <label for="emergencyContact" class="form-label">Emergency Contact Person</label>
            <input type="text" class="form-control" id="emergencyContact" name="emergency_contact" value="{{ old('emergency_contact') }}"
                   placeholder="Name - Mobile Number">
          </div>

        </div></div>
        <div class="form-sheet"><div class="row">
          <div class="col-12"><h2 class="form-section"><span class="n">3</span>Your login</h2></div>

          <!-- Password -->
          <div class="col-md-6 mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="pw-wrap">
              <input type="password" class="form-control" id="password" name="password"
                     placeholder="Create a strong password" minlength="8" required autocomplete="new-password">
              <button type="button" class="pw-toggle" data-target="password">Show</button>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="col-md-6 mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <div class="pw-wrap">
              <input type="password" class="form-control" id="confirmPassword" name="password_confirmation"
                     placeholder="Type it again" minlength="8" required autocomplete="new-password">
              <button type="button" class="pw-toggle" data-target="confirmPassword">Show</button>
            </div>
          </div>

          <!-- Live password rules: the button stays off until all are met -->
          <div class="col-12 mb-2">
            @include('partials.password-strength', ['input' => 'password', 'confirm' => 'confirmPassword', 'fields' => ['firstName', 'lastName', 'email']])
            @error('password')<div class="text-danger mt-2" style="font-size: .85rem;">{{ $message }}</div>@enderror
          </div>

        </div></div>
        <div class="form-sheet"><div class="row">
          <div class="col-12"><h2 class="form-section"><span class="n">4</span>Valid ID <small>Only barangay officials see it</small></h2></div>

          <div class="col-12 mb-3">
            <label for="idType" class="form-label">Type of ID</label>
            <select class="form-select" id="idType" name="id_type" required>
              <option value="" disabled @selected(! old('id_type'))>Select the ID you will upload</option>
              @foreach (\App\Http\Controllers\ResidentAuthController::ID_TYPES as $t)
                <option value="{{ $t }}" @selected(old('id_type') === $t)>{{ $t }}</option>
              @endforeach
            </select>
          </div>

          <!-- Upload ID -->
          <div class="col-12 mb-4">
            <label for="idUpload" class="form-label">Upload Valid ID</label>
            <input type="file" class="form-control" id="idUpload" name="valid_id"
                   accept="image/jpeg,image/png,image/webp" required>

            <div id="idPreviewBox" class="mt-2">
              <span id="idPreviewText">No photo selected yet</span>
              <img id="idPreviewImg" alt="Preview of uploaded ID">
            </div>
            <div id="idUploadNote" class="text-danger mt-2 d-none" style="font-size:.85rem;" role="alert"></div>
            <div class="hint">Choose a photo from your gallery or take one now. Make sure the name and photo on the ID are readable. Large photos are made smaller before sending.</div>
          </div>

        </div></div>

        <p class="page-subtext mt-3 mb-0" style="font-size: 0.9rem;">
          After you send this, a barangay official checks your details and ID. You can log in once it is approved.
        </p>

        <div class="form-check consent-box">
          <input class="form-check-input" type="checkbox" id="dataConsent" name="data_consent" required>
          <label class="form-check-label" for="dataConsent" style="font-size: 0.85rem;">
            I agree to the collection and processing of the information above by Barangay Alejal, in accordance with the
            <a href="https://privacy.gov.ph/data-privacy-act/" target="_blank" rel="noopener" class="link-gold">Data Privacy Act of 2012 (RA 10173)</a>.
          </label>
        </div>

        <button type="submit" class="btn btn-gold w-100">Send for approval</button>

        <p class="text-center mt-4" style="font-size: 0.9rem;">
          Already have an account?
          <a href="{{ url('/login') }}" class="link-gold">Log in</a>
        </p>

        <p class="text-center mt-2" style="font-size: 0.85rem; color: var(--color-gray);">
          Just want to report a concern? <a href="{{ url('/') }}#report" class="link-gold">No account needed</a>.
        </p>

      </form>

    </div>
    </main>
  </div>

  <!-- Shown after the form is submitted -->
  <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalTitle"
       aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-3">
        <div class="modal-body">
          <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
               style="width: 72px; height: 72px; background: #E7EAFD; color: #4F5FE0;">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="12" r="9"/>
              <path d="M12 7v5l3 2"/>
            </svg>
          </div>
          <h4 class="fw-bold mb-2" id="approvalModalTitle">Registration received</h4>
          <p class="mb-2">
            Please wait for the barangay admin to approve your account.
          </p>
          <p class="page-subtext mb-4" style="font-size: 0.9rem;">
            They will check your details and ID first. You can log in once your account is approved.
          </p>
          <a href="{{ url('/login') }}" class="btn btn-gold w-100">Go to login</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/script.js') }}"></script>

  <script>
    // Age is worked out from the birthdate, so the two can never disagree.
    function showAge() {
      const dob = new Date(this.value);
      if (isNaN(dob)) return;

      const today = new Date();
      let age = today.getFullYear() - dob.getFullYear();
      const hasHadBirthdayThisYear =
        today.getMonth() > dob.getMonth() ||
        (today.getMonth() === dob.getMonth() && today.getDate() >= dob.getDate());
      if (!hasHadBirthdayThisYear) age--;

      document.getElementById('age').value = age >= 0 ? age : '';
    }
    document.getElementById('birthdate').addEventListener('change', showAge);
    showAge.call(document.getElementById('birthdate'));

    // Password rules and the "both boxes match" check live in partials/password-strength.

    // ---- Live resident card on the left ----
    (function () {
      const $ = id => document.getElementById(id);
      function setVal(el, text, empty) {
        el.textContent = text || empty;
        el.classList.toggle('is-empty', !text);
      }
      function update() {
        const first = $('firstName').value.trim(), mid = $('middleName').value.trim(), last = $('lastName').value.trim();
        const name = [first, mid ? mid.charAt(0).toUpperCase() + '.' : '', last].filter(Boolean).join(' ');
        setVal($('cardName'), name, 'Your name');
        $('cardInitials').textContent = ((first.charAt(0) || '') + (last.charAt(0) || '')).toUpperCase() || '?';
        setVal($('cardPurok'), $('purok').value, 'Not set');
        setVal($('cardAge'), $('age').value ? $('age').value : '', 'Not set');
      }
      ['firstName', 'middleName', 'lastName'].forEach(id => $(id).addEventListener('input', update));
      $('purok').addEventListener('change', update);
      $('birthdate').addEventListener('change', update);
      update();

      // Show / hide passwords
      document.querySelectorAll('.pw-toggle').forEach(btn => btn.addEventListener('click', function () {
        const input = $(this.dataset.target);
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        this.textContent = show ? 'Hide' : 'Show';
      }));
    })();

    // Phone photos can be several MB, and the server only takes small uploads.
    // Before sending, the ID photo is redrawn as a JPEG under 1.8 MB.
    // Two ways to read the photo, so it works in Safari, Chrome and the Messenger browser.
    (function () {
      const input = document.getElementById('idUpload');
      const note = document.getElementById('idUploadNote');
      const LIMIT = 1.8 * 1024 * 1024;
      let ready = true;   // false while a photo is still being prepared

      function showNote(text) { note.textContent = text; note.classList.toggle('d-none', !text); }

      // Way 1: createImageBitmap. Way 2: a plain <img>, for browsers where way 1 fails.
      function loadWithImg(file) {
        return new Promise((resolve, reject) => {
          const url = URL.createObjectURL(file);
          const img = new Image();
          img.onload = () => resolve(img);
          img.onerror = () => { URL.revokeObjectURL(url); reject(new Error('unreadable')); };
          img.src = url;
        });
      }
      async function readPhoto(file) {
        if (typeof createImageBitmap !== 'undefined') {
          try { return await createImageBitmap(file); } catch (e) { /* try the other way */ }
        }
        return loadWithImg(file);
      }
      function toJpeg(pic, side, quality) {
        const w = pic.naturalWidth || pic.width, h = pic.naturalHeight || pic.height;
        const scale = Math.min(1, side / Math.max(w, h));
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(w * scale));
        canvas.height = Math.max(1, Math.round(h * scale));
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#fff';   // PNGs with see-through parts get a white back
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(pic, 0, 0, canvas.width, canvas.height);
        return new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', quality));
      }

      input.addEventListener('change', async function () {
        const file = this.files[0];
        showNote('');
        if (!file) return;
        if (typeof DataTransfer === 'undefined') {
          if (file.size > LIMIT) showNote('This photo is too large. Please take a screenshot of your ID and upload that instead.');
          return;
        }
        ready = false;
        try {
          const pic = await readPhoto(file);
          // Shrink step by step until it fits
          let side = 1400, quality = 0.82, blob = null;
          for (let i = 0; i < 5; i++) {
            blob = await toJpeg(pic, side, quality);
            if (blob && blob.size <= LIMIT) break;
            side = Math.round(side * 0.8);
            quality = Math.max(0.6, quality - 0.06);
          }
          if (!blob || blob.size > LIMIT) throw new Error('too big');
          const dt = new DataTransfer();
          dt.items.add(new File([blob], 'valid-id.jpg', { type: 'image/jpeg', lastModified: Date.now() }));
          this.files = dt.files;
        } catch (e) {
          if (file.size > LIMIT) {
            showNote('This photo could not be prepared. Please take a screenshot of your ID and upload that instead.');
          }
        }
        ready = true;
      });

      // Stop the form from sending a photo the server would refuse
      document.getElementById('registerForm').addEventListener('submit', function (e) {
        const file = input.files[0];
        if (!ready) {
          e.preventDefault();
          showNote('Still preparing your photo. Please wait a second and tap the button again.');
        } else if (file && file.size > LIMIT) {
          e.preventDefault();
          showNote('This photo is too large. Please take a screenshot of your ID and upload that instead.');
          input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      });
    })();

    // Shown after the server saved the registration.
    @if (session('registered'))
      new bootstrap.Modal(document.getElementById('approvalModal')).show();
    @endif
  </script>
</body>
</html>