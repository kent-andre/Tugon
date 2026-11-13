{{--
    resources/views/resident/submit-concern.blade.php

    Concern form for logged-in residents (guests use the #report form on landing.blade.php).
    Sends the report to POST /concerns, the same route the landing form uses, so it is saved
    on the server and shows up on the admin Concerns page and on /check-status.

    No database yet: ConcernStore keeps the reports in a file. The resident's own list on
    My Reports is still kept in this browser until resident accounts are backed by data.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit a Concern - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --ref-blue: #4F5FE0; --ref-navy-text: #1E2340; --ref-gray-text: #6B7280;
      --ref-bg: #F5F6FA; --ref-border: #E4E6EF;
    }
    body { font-family: 'Lato', sans-serif; color: var(--ref-navy-text); background: var(--ref-bg); }

    /* ===== Layout (same as My Reports) ===== */
    :root { --navy: #181a35; --navy-active: #5b52f5; }
    .main { margin-left: 250px; }
    .avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--navy-active); color: #fff;
      display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; cursor: pointer; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 18px 40px 0; }
    .page-wrap { max-width: 720px; margin: 0 auto; padding: 40px 20px; }

    .form-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 28px; }
    .form-label { font-size: 13.5px; font-weight: 700; }
    .form-control, .form-select { border-color: var(--ref-border); border-radius: 8px; font-size: 14px; padding: 10px 12px; }
    .form-control:focus, .form-select:focus { border-color: var(--ref-blue); box-shadow: 0 0 0 3px rgba(79,95,224,.15); }
    .hint { font-size: 12px; color: var(--ref-gray-text); }

    .cat-option { display: block; }
    .cat-option .btn-check + label {
      width: 100%; border: 1px solid var(--ref-border); background: #fff; border-radius: 10px;
      padding: 10px 8px; font-size: 13px; font-weight: 600; color: var(--ref-gray-text);
      display: flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; margin: 0;
    }
    .cat-option .btn-check:checked + label { background: var(--ref-blue); border-color: var(--ref-blue); color: #fff; }
    .cat-option .btn-check:focus-visible + label { outline: 2px solid var(--ref-blue); outline-offset: 2px; }

    .drop {
      border: 2px dashed var(--ref-border); border-radius: 10px; padding: 14px 16px; cursor: pointer;
      display: flex; align-items: center; gap: 12px; font-size: 14px; color: var(--ref-gray-text); background: #FAFBFD;
    }
    .drop:hover { border-color: var(--ref-blue); }
    .drop.is-invalid { border-color: #dc3545; }
    button.drop { font: inherit; }
    /* Live camera (same flow as the landing page form) */
    .camera-panel { background: #000; border: 1px solid var(--ref-border); border-radius: 10px; overflow: hidden; margin-top: 12px; }
    .camera-panel video { width: 100%; display: block; max-height: 22rem; object-fit: cover; }
    .camera-bar { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; padding: 8px; background: #FAFBFD; }
    .camera-bar .btn { font-size: 13px; font-weight: 600; border-radius: 8px; }
    .btn-brand { background: var(--ref-blue); color: #fff; font-weight: 700; border-radius: 10px; padding: 12px; border: 0; }
    .btn-brand:hover { background: #3E4DCB; color: #fff; }
    .optional-box { border: 1px solid var(--ref-border); background: #FAFBFD; border-radius: 10px; padding: 12px 14px; cursor: pointer; }
  </style>
</head>
<body>

  @include('partials.resident-sidebar')

  <div class="main">
    <div class="top-bar">
      <h5 class="fw-bold mb-0">Submit a Concern</h5>
    </div>

    <div class="page-wrap">
      <h3 class="fw-bold mb-1">Submit a Concern</h3>
      <p style="color: var(--ref-gray-text); font-size: 14px;" class="mb-4">
        Barangay officials will be notified. Only you and officials can see this report, and you can follow its progress in
        <a href="{{ url('/my-reports') }}" style="color: var(--ref-blue); font-weight: 600;">My Reports &amp; Status</a>.
      </p>

      <form action="{{ route('concerns.store') }}" method="POST" enctype="multipart/form-data" id="concernForm" class="form-card" novalidate>
        @csrf

        {{-- Honeypot spam trap (backend: silently ignore submissions where this is filled) --}}
        <input type="text" name="website" id="website" autocomplete="off" tabindex="-1"
               style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;" aria-hidden="true">

        <div class="mb-4">
          <label class="form-label">Category</label>
          <div class="row g-2">
            @foreach (['Crime/Safety' => 'ti-shield', 'Environment' => 'ti-leaf', 'Infrastructure' => 'ti-building', 'Water Supply' => 'ti-droplet', 'Streetlights' => 'ti-bulb', 'Noise' => 'ti-volume-off', 'Neighbor Dispute' => 'ti-users', 'Other' => 'ti-pin'] as $cat => $icon)
              <div class="col-6 col-sm-3 cat-option">
                <input type="radio" class="btn-check" name="category" id="cat-{{ Str::slug($cat) }}" value="{{ $cat }}" required autocomplete="off">
                <label for="cat-{{ Str::slug($cat) }}"><i class="ti {{ $icon }}"></i>{{ $cat }}</label>
              </div>
            @endforeach
          </div>
          <p id="catError" class="d-none text-danger mt-2 mb-0" style="font-size:12px;">Pick a category.</p>
        </div>

        {{-- Shown only for Crime/Safety --}}
        <div id="hotlineWarning" class="d-none alert alert-danger d-flex gap-2 mb-4" style="font-size:13.5px;">
          <i class="ti ti-alert-triangle fs-5"></i>
          <span>If this is happening now or you're in danger, call the barangay emergency team at <a href="tel:{{ \App\Support\EmergencyTeam::HOTLINE_TEL }}" class="alert-link">{{ \App\Support\EmergencyTeam::HOTLINE }}</a>, or <strong>911</strong>. This form notifies barangay officials but is not an emergency response.</span>
        </div>

        {{-- Shown only for Neighbor Dispute --}}
        <div id="disputeNote" class="d-none alert alert-secondary d-flex gap-2 mb-4" style="font-size:13.5px;">
          <i class="ti ti-info-circle fs-5"></i>
          <span>Officials review your report first. If the dispute needs mediation, the barangay will ask you to come to the barangay hall to give your statement and sign a blotter entry. Submitting this form does not file a blotter by itself.</span>
        </div>

        <div class="mb-4">
          <label class="form-label" for="title">Title</label>
          <input type="text" id="title" name="title" maxlength="80" class="form-control" placeholder="e.g. Broken streetlight on Rizal St.">
          <p id="titleError" class="d-none text-danger mt-2 mb-0" style="font-size:12px;">Add a short title.</p>
        </div>

        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label mb-0" for="description">Description</label>
            <span class="hint"><span id="charCount">0</span>/900</span>
          </div>
          <textarea id="description" name="description" rows="4" maxlength="900" class="form-control"
                    placeholder="What happened, where, when, and who is involved."></textarea>
          <p id="descError" class="d-none text-danger mt-2 mb-0" style="font-size:12px;">Describe the concern.</p>
        </div>

        <div class="mb-4">
          <label class="form-label" for="purok">Zone / Purok</label>
          <select id="purok" name="purok" class="form-select">
            <option value="" selected disabled>Select your zone</option>
            @foreach (['Purok 1','Purok 2','Purok 3','Purok 4','Purok 5'] as $p)
              <option>{{ $p }}</option>
            @endforeach
          </select>
          <p id="purokError" class="d-none text-danger mt-2 mb-0" style="font-size:12px;">Select a zone.</p>
        </div>

        <div class="mb-4">
          <label class="form-label">Incident location <span class="hint fw-normal">(required)</span></label>
          <div class="drop" id="locationDrop" style="cursor:default;">
            <button type="button" id="useLocationBtn" class="btn btn-outline-secondary btn-sm fw-semibold">
              <i class="ti ti-map-pin me-1"></i>Use my current location
            </button>
            <span id="locationText" class="hint" aria-live="polite">No location captured yet.</span>
          </div>
          <input type="hidden" name="latitude" id="latitudeInput">
          <input type="hidden" name="longitude" id="longitudeInput">
          <input type="hidden" name="location_accuracy" id="accuracyInput">
          <input type="hidden" name="location_adjusted" id="adjustedInput" value="0">
          {{-- Shown after the location is found: drag the pin to the exact spot --}}
          <div id="locationMap" class="d-none mt-2" style="height:240px;border-radius:10px;border:1px solid var(--ref-border);overflow:hidden;"></div>
          <p id="locationMissing" class="d-none text-danger mt-2 mb-0" style="font-size:12px;">Tap "Use my current location" so officials know where this happened.</p>
        </div>

        <div class="mb-4 position-relative">
          <label class="form-label" for="evidenceDrop">Photo evidence <span class="hint fw-normal">(required, taken with your camera, max 5MB)</span></label>
          {{-- Taken live with the camera, like the landing page form. Phones whose
               browser can't open the camera fall back to the phone's own camera app. --}}
          <button type="button" id="evidenceDrop" class="drop w-100 text-start">
            <i class="ti ti-camera fs-4"></i>
            <span id="fileLabel">Tap to take a photo</span>
          </button>
          <input type="file" id="evidenceUpload" name="evidence" accept="image/jpeg,image/png,image/webp" capture="environment"
                 style="position:absolute;opacity:0;width:1px;height:1px;pointer-events:none;" tabindex="-1">
          <div id="cameraPanel" class="camera-panel d-none">
            <video id="cameraVideo" autoplay playsinline muted></video>
            <div class="camera-bar">
              <button type="button" id="shutterBtn" class="btn btn-brand px-4"><i class="ti ti-camera me-1"></i>Take photo</button>
              <button type="button" id="cameraSwitchBtn" class="btn btn-outline-secondary d-none" title="Switch between the front and back camera"><i class="ti ti-camera-rotate me-1"></i>Switch</button>
              <button type="button" id="cameraFlipBtn" class="btn btn-outline-secondary" title="Flip the image left to right"><i class="ti ti-flip-horizontal me-1"></i>Flip</button>
              <button type="button" id="cameraCancelBtn" class="btn btn-outline-secondary">Cancel</button>
            </div>
          </div>
          <canvas id="cameraCanvas" class="d-none"></canvas>
          <p id="cameraNote" class="d-none text-danger mt-2 mb-0" style="font-size:12px;"></p>
          <p id="fileError" class="d-none text-danger mt-2 mb-0" style="font-size:12px;">That photo is too large (over 5MB). Please retake it.</p>
          <p id="fileTypeError" class="d-none text-danger mt-2 mb-0" style="font-size:12px;">The photo must be a JPG, PNG or WebP image. Please retake it.</p>
          <p id="fileMissing" class="d-none text-danger mt-2 mb-0" style="font-size:12px;">A photo is required. Take a photo of the concern.</p>
          <img id="previewImg" alt="Evidence preview" class="d-none rounded-3 mt-3" style="max-height:11rem;">
        </div>

        <div class="row g-3 mb-4">
          <div class="col-sm-6">
            <label class="optional-box d-flex align-items-start gap-2 mb-0">
              <input type="checkbox" name="hide_name" class="form-check-input mt-1">
              <span style="font-size:13px;">Hide my name<br><span class="hint">Officials will see this report as anonymous, and it won't be linked to your account. Keep the tracking code to follow it on Check Status.</span></span>
            </label>
          </div>
          <div class="col-sm-6">
            <label class="optional-box d-flex align-items-start gap-2 mb-0">
              <input type="checkbox" id="dataConsent" name="data_consent" class="form-check-input mt-1">
              <span class="hint">
                I agree to the processing of this information by Barangay Alejal under the
                <a href="https://privacy.gov.ph/data-privacy-act/" target="_blank" rel="noopener" style="color: var(--ref-blue); font-weight: 600;">Data Privacy Act (RA 10173)</a>.
                @include('partials.photo-check-consent')
              </span>
            </label>
            <p id="consentError" class="d-none text-danger mt-2 mb-0" style="font-size:12px;">Agree to continue.</p>
          </div>
        </div>

        <button type="submit" class="btn btn-brand w-100">Submit concern</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @include('partials.location-picker-script')
  <script>
    const $ = id => document.getElementById(id);

    document.querySelectorAll('input[name="category"]').forEach(radio =>
      radio.addEventListener('change', function () {
        $('hotlineWarning').classList.toggle('d-none', this.value !== 'Crime/Safety');
        $('disputeNote').classList.toggle('d-none', this.value !== 'Neighbor Dispute');
        $('catError').classList.add('d-none');
      })
    );
    $('description').addEventListener('input', function () { $('charCount').textContent = this.value.length; });

    // Location: see partials/location-picker-script (several GPS readings, draggable pin)

    // ---- Photo evidence: taken live with the camera (same as the landing page) ----
    const evidenceInput = $('evidenceUpload');
    const cameraPanel = $('cameraPanel'), cameraVideo = $('cameraVideo'), cameraCanvas = $('cameraCanvas'), cameraNote = $('cameraNote');
    let cameraStream = null;
    let cameraFlipped = false;         // never flipped unless the resident taps Flip
    let cameraFacing = 'environment';  // back camera first

    function stopTracks() {
      if (cameraStream) { cameraStream.getTracks().forEach(t => t.stop()); cameraStream = null; }
      cameraVideo.srcObject = null;
    }
    function stopCamera() { stopTracks(); cameraPanel.classList.add('d-none'); }

    // Checks the photo, then shows it and clears the error messages
    function applyEvidence(file) {
      const okType = ['image/jpeg', 'image/png', 'image/webp'].includes(file.type);
      const okSize = file.size <= 5 * 1024 * 1024;
      $('fileTypeError').classList.toggle('d-none', okType);
      $('fileError').classList.toggle('d-none', !okType || okSize);
      if (!okType || !okSize) {
        evidenceInput.value = '';
        $('previewImg').classList.add('d-none');
        $('fileLabel').textContent = 'Tap to take a photo';
        return;
      }
      $('fileMissing').classList.add('d-none');
      $('evidenceDrop').classList.remove('is-invalid');
      $('fileLabel').textContent = 'Photo taken. Tap to retake.';
      const reader = new FileReader();
      reader.onload = e => { $('previewImg').src = e.target.result; $('previewImg').classList.remove('d-none'); };
      reader.readAsDataURL(file);
    }

    // If the live camera can't start, phones use their own camera app instead.
    // Computers only get a message, so a photo is never picked from the files.
    function cameraFallback(message) {
      if (/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) { evidenceInput.click(); return; }
      cameraNote.textContent = message;
      cameraNote.classList.remove('d-none');
    }

    // Switch only shows on touch devices with two or more cameras
    async function updateSwitchButton() {
      let show = false;
      try {
        const isTouch = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent) || navigator.maxTouchPoints > 1;
        if (isTouch) show = (await navigator.mediaDevices.enumerateDevices()).filter(d => d.kind === 'videoinput').length > 1;
      } catch (err) {}
      $('cameraSwitchBtn').classList.toggle('d-none', !show);
    }

    async function startCamera() {
      cameraStream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: { ideal: cameraFacing }, width: { ideal: 1280 } }, audio: false
      });
      cameraVideo.srcObject = cameraStream;
      cameraVideo.style.transform = cameraFlipped ? 'scaleX(-1)' : 'none';
      cameraPanel.classList.remove('d-none');
      updateSwitchButton();
    }

    $('evidenceDrop').addEventListener('click', async function () {
      stopCamera();
      cameraNote.classList.add('d-none');
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        cameraFallback('The camera is not available here. Open this page over https on a device with a camera.');
        return;
      }
      try {
        await startCamera();
        cameraPanel.scrollIntoView({ behavior: 'smooth', block: 'center' });
      } catch (err) {
        cameraFallback('Could not open the camera. Allow camera access in your browser and try again.');
      }
    });

    $('cameraSwitchBtn').addEventListener('click', async function () {
      const previous = cameraFacing;
      cameraFacing = cameraFacing === 'environment' ? 'user' : 'environment';
      cameraFlipped = false;
      stopTracks();
      try { await startCamera(); }
      catch (err) {
        cameraFacing = previous;
        try { await startCamera(); } catch (e2) { stopCamera(); }
        cameraNote.textContent = 'Could not switch the camera.';
        cameraNote.classList.remove('d-none');
      }
    });

    $('shutterBtn').addEventListener('click', function () {
      const w = cameraVideo.videoWidth, h = cameraVideo.videoHeight;
      if (!w || !h) return;
      // Longest side at most 1600px keeps the file well under 5MB
      const scale = Math.min(1, 1600 / Math.max(w, h));
      cameraCanvas.width = Math.round(w * scale);
      cameraCanvas.height = Math.round(h * scale);
      const ctx = cameraCanvas.getContext('2d');
      if (cameraFlipped) { ctx.translate(cameraCanvas.width, 0); ctx.scale(-1, 1); }
      ctx.drawImage(cameraVideo, 0, 0, cameraCanvas.width, cameraCanvas.height);
      cameraCanvas.toBlob(function (blob) {
        if (!blob) return;
        const file = new File([blob], 'evidence-' + Date.now() + '.jpg', { type: 'image/jpeg' });
        const transfer = new DataTransfer();
        transfer.items.add(file);
        evidenceInput.files = transfer.files;
        stopCamera();
        applyEvidence(file);
      }, 'image/jpeg', 0.85);
    });

    $('cameraFlipBtn').addEventListener('click', function () {
      cameraFlipped = !cameraFlipped;
      cameraVideo.style.transform = cameraFlipped ? 'scaleX(-1)' : 'none';
    });
    $('cameraCancelBtn').addEventListener('click', stopCamera);
    window.addEventListener('pagehide', stopCamera);

    // Only used when a phone falls back to its own camera app
    evidenceInput.addEventListener('change', function () { if (this.files[0]) applyEvidence(this.files[0]); });

    // Clear inline errors as the resident fixes each field
    [['title', 'titleError'], ['description', 'descError'], ['purok', 'purokError'], ['dataConsent', 'consentError']].forEach(([f, e]) => {
      $(f).addEventListener('input', () => { $(e).classList.add('d-none'); $(f).classList.remove('is-invalid'); });
      $(f).addEventListener('change', () => { $(e).classList.add('d-none'); $(f).classList.remove('is-invalid'); });
    });

    $('concernForm').addEventListener('submit', function (e) {
      e.preventDefault();
      if ($('website').value !== '') return; // honeypot: bot

      const cat = document.querySelector('input[name="category"]:checked');
      const checks = [
        [!cat, 'catError', document.querySelector('.cat-option')],
        [!$('title').value.trim(), 'titleError', $('title')],
        [!$('description').value.trim(), 'descError', $('description')],
        [!$('purok').value, 'purokError', $('purok')],
        [!($('latitudeInput').value && $('longitudeInput').value), 'locationMissing', $('locationDrop')],
        [!$('evidenceUpload').files.length, 'fileMissing', $('evidenceDrop')],
        [!$('dataConsent').checked, 'consentError', $('dataConsent')],
      ];
      let firstBad = null;
      checks.forEach(([bad, errId, el]) => {
        if (!bad) return;
        $(errId).classList.remove('d-none');
        if (el.classList.contains('drop')) el.classList.add('is-invalid');
        else if (el.tagName !== 'DIV') el.classList.add('is-invalid');
        firstBad = firstBad || el;
      });
      if (firstBad) {
        firstBad.scrollIntoView({ behavior: 'smooth', block: 'center' });
        if (firstBad.focus && firstBad.tagName !== 'DIV' && !firstBad.classList.contains('drop')) firstBad.focus({ preventScroll: true });
        return;
      }

      const form = this;
      const btn = form.querySelector('button[type="submit"]');
      const btnLabel = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Submitting...';

      // The server has one description field and no Neighbor Dispute category,
      // so the title goes in front of the description and disputes are filed as Other.
      const isDispute = cat.value === 'Neighbor Dispute';
      const title = $('title').value.trim();
      const text = (isDispute ? '[Neighbor dispute] ' : '') + title + ' - ' + $('description').value.trim();
      const hideName = document.querySelector('input[name="hide_name"]').checked;

      const data = new FormData(form);
      data.set('category', isDispute ? 'Other' : cat.value);
      data.set('description', text);
      data.delete('title');
      data.delete('hide_name');
      if (hideName) data.set('anonymous', '1');

      fetch(form.action, { method: 'POST', body: data, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
        .then(async res => {
          const body = await res.json().catch(() => ({}));
          if (!res.ok) {
            throw new Error(body.errors
              ? Object.values(body.errors).flat().join('\n')
              : (body.message || 'Something went wrong. Please try again.'));
          }
          return body;
        })
        .then(body => {
          const code = body.tracking_code;
          const today = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

          // Browser-only copies, until resident accounts and the admin map read from the server:
          // My Reports lists this browser's reports, and the admin map reads pb_map_concerns.
          try {
            const mine = JSON.parse(localStorage.getItem('pb_resident_reports') || '[]');
            localStorage.setItem('pb_resident_reports', JSON.stringify([{
              code, title: title, category: cat.value, zone: $('purok').value,
              description: $('description').value.trim(), filed: today, status: 'Pending'
            }, ...mine]));

            const lat = parseFloat($('latitudeInput').value), lng = parseFloat($('longitudeInput').value);
            if (!isNaN(lat) && !isNaN(lng)) {
              const pins = JSON.parse(localStorage.getItem('pb_map_concerns') || '[]');
              pins.unshift({
                ref: code, title: title, category: cat.value, zone: $('purok').value.replace('Purok', 'Zone'),
                status: 'pending', verify: 'unverified',
                reporter: hideName ? 'Anonymous' : 'Logged-in resident',
                lat, lng, filed: today
              });
              localStorage.setItem('pb_map_concerns', JSON.stringify(pins));
            }
          } catch (err) { /* storage blocked, the list just won't show it */ }

          // Anonymous reports are not linked to the account (Step 5), so show the code on Check Status
          window.location.href = hideName
            ? "{{ url('/check-status') }}?code=" + encodeURIComponent(code)
            : "{{ url('/my-reports') }}?filed=" + encodeURIComponent(code);
        })
        .catch(err => {
          alert(err.message);
          btn.disabled = false;
          btn.textContent = btnLabel;
        });
    });
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <script src="{{ asset('js/pabarang.js') }}"></script>
</body>
</html>