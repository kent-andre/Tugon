<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Request a Document - Tugon</title>
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

    /* ===== Sidebar (shared across all resident pages) ===== */
    :root { --navy: #181a35; --navy-active: #5b52f5; }
    .main { margin-left: 250px; }
    .avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--navy-active); color: #fff;
      display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; cursor: pointer; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 18px 40px 0; }

    .page-wrap { max-width: 900px; margin: 0 auto; padding: 40px 20px; }

    .doc-card {
      background: #fff; border: 1px solid var(--ref-border); border-radius: 12px;
      padding: 26px; height: 100%; text-align: center;
      display: flex; flex-direction: column;   /* lets the button sit at the bottom of every card */
    }
    .doc-icon { width: 56px; height: 56px; border-radius: 12px; background: var(--ref-bg); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
    .doc-icon i { font-size: 26px; color: var(--ref-blue); }
    .doc-title { font-size: 16px; font-weight: 700; line-height: 1.3; min-height: 2.6em; margin-bottom: 6px; display: flex; align-items: center; justify-content: center; }
    .doc-desc { font-size: 13px; color: var(--ref-gray-text); margin-bottom: 18px; flex-grow: 1; }
    .btn-request { background: var(--ref-blue); color: #fff; border: none; border-radius: 8px; padding: 9px 0; width: 100%; font-weight: 600; font-size: 14px; margin-top: auto; }
    .btn-request:hover { background: #3E4DCB; color: #fff; }

    .form-label { font-weight: 600; font-size: 13.5px; }
    .modal-note { font-size: 12.5px; color: var(--ref-gray-text); background: var(--ref-bg); border-radius: 8px; padding: 10px 12px; margin-bottom: 16px; }
    .file-hint { font-size: 12px; color: var(--ref-gray-text); margin-top: 4px; }
  </style>
</head>
<body>

  @php
    // Refill a form with what the resident typed, but only the form that failed.
    $preEvent = $preEvent ?? null;   // set when coming from a Health event on the calendar
    $old = fn ($type, $key, $default = '') => old('_doc') === $type ? (string) old($key, $default) : (string) $default;
    $modalFor = ['clearance' => 'clearanceModal', 'residency' => 'residencyModal', 'indigency' => 'indigencyModal',
                 'business' => 'businessModal', 'vaccination' => 'vaccinationModal', 'id' => 'seniorPwdModal'];
  @endphp

  @include('partials.resident-sidebar')

  <div class="main">
    <div class="top-bar">
      <h5 class="fw-bold mb-0">Request a Document</h5>
    </div>

  <div class="page-wrap">
    <h3 class="fw-bold mb-1">Request a Document</h3>
    <p style="color: var(--ref-gray-text); font-size: 14px;" class="mb-4">
      Choose the document you need. Each one asks for slightly different details.
      You can follow every request on <a href="{{ url('/my-reports') }}">My Reports &amp; Status</a>.
    </p>

    <div class="row g-4">

      <div class="col-md-6 col-lg-3">
        <div class="doc-card">
          <div class="doc-icon"><i class="ti ti-file-certificate"></i></div>
          <div class="doc-title">Barangay Clearance</div>
          <div class="doc-desc">Proof of no bad record, often needed for jobs or permits.</div>
          <button class="btn-request" data-bs-toggle="modal" data-bs-target="#clearanceModal">Request Now</button>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="doc-card">
          <div class="doc-icon"><i class="ti ti-home-check"></i></div>
          <div class="doc-title">Certificate of Residency</div>
          <div class="doc-desc">Confirms you live in Barangay Alejal.</div>
          <button class="btn-request" data-bs-toggle="modal" data-bs-target="#residencyModal">Request Now</button>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="doc-card">
          <div class="doc-icon"><i class="ti ti-heart-handshake"></i></div>
          <div class="doc-title">Certificate of Indigency</div>
          <div class="doc-desc">For aid, scholarships, or medical assistance requests.</div>
          <button class="btn-request" data-bs-toggle="modal" data-bs-target="#indigencyModal">Request Now</button>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="doc-card">
          <div class="doc-icon"><i class="ti ti-briefcase"></i></div>
          <div class="doc-title">Business Clearance</div>
          <div class="doc-desc">Permission for a business to operate in the barangay.</div>
          <button class="btn-request" data-bs-toggle="modal" data-bs-target="#businessModal">Request Now</button>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="doc-card">
          <div class="doc-icon"><i class="ti ti-vaccine"></i></div>
          <div class="doc-title">Vaccination Scheduling</div>
          <div class="doc-desc">Book a vaccination slot at the Barangay Health Center.</div>
          <button class="btn-request" data-bs-toggle="modal" data-bs-target="#vaccinationModal">Request Now</button>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="doc-card">
          <div class="doc-icon"><i class="ti ti-id-badge-2"></i></div>
          <div class="doc-title">Senior Citizen / PWD ID</div>
          <div class="doc-desc">Apply for a Senior Citizen or PWD identification card.</div>
          <button class="btn-request" data-bs-toggle="modal" data-bs-target="#seniorPwdModal">Request Now</button>
        </div>
      </div>

    </div>
  </div>
  </div>

  {{--
    Each form posts to /request-document/{type} (Resident\DocumentRequestController@store),
    which validates the fields for that type and saves one row in `document_requests`
    (type + details JSON). Uploaded IDs go to the private disk; only admins can open them.
  --}}

  <!-- Barangay Clearance modal -->
  <div class="modal fade" id="clearanceModal" tabindex="-1" aria-labelledby="clearanceTitle">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="clearanceTitle">Barangay Clearance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="doc-form" action="{{ url('/request-document/clearance') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="_doc" value="clearance">
          <div class="modal-body">
            @include('resident.partials.doc-errors', ['type' => 'clearance'])
            @include('resident.partials.doc-purpose', ['type' => 'clearance', 'purposes' => $docTypes['clearance']['purposes']])
            <div class="mb-3">
              <label class="form-label" for="clearanceCopies">Number of Copies</label>
              <input type="number" class="form-control" id="clearanceCopies" name="copies" min="1" max="10" value="{{ $old('clearance', 'copies', '1') }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="clearanceId">Upload Valid ID</label>
              <input type="file" class="form-control" id="clearanceId" name="valid_id" accept="image/*" required>
              <div class="file-hint">Photo of a government ID, up to 5 MB.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn" style="background: var(--ref-blue); color:#fff;">Submit Request</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Certificate of Residency modal -->
  <div class="modal fade" id="residencyModal" tabindex="-1" aria-labelledby="residencyTitle">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="residencyTitle">Certificate of Residency</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="doc-form" action="{{ url('/request-document/residency') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="_doc" value="residency">
          <div class="modal-body">
            @include('resident.partials.doc-errors', ['type' => 'residency'])
            @include('resident.partials.doc-purpose', ['type' => 'residency', 'purposes' => $docTypes['residency']['purposes']])
            <div class="mb-3">
              <label class="form-label" for="residencyYears">Years of Residency in Barangay Alejal</label>
              <input type="number" class="form-control" id="residencyYears" name="years" min="0" max="120" placeholder="e.g. 5" value="{{ $old('residency', 'years') }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="residencyId">Upload Valid ID</label>
              <input type="file" class="form-control" id="residencyId" name="valid_id" accept="image/*" required>
              <div class="file-hint">Photo of a government ID, up to 5 MB.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn" style="background: var(--ref-blue); color:#fff;">Submit Request</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Certificate of Indigency modal -->
  <div class="modal fade" id="indigencyModal" tabindex="-1" aria-labelledby="indigencyTitle">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="indigencyTitle">Certificate of Indigency</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="doc-form" action="{{ url('/request-document/indigency') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="_doc" value="indigency">
          <div class="modal-body">
            @include('resident.partials.doc-errors', ['type' => 'indigency'])
            <div class="modal-note">
              This certificate may require a follow-up assessment by the barangay social worker before release.
            </div>
            @include('resident.partials.doc-purpose', ['type' => 'indigency', 'purposes' => $docTypes['indigency']['purposes']])
            <div class="mb-3">
              <label class="form-label" for="indigencyId">Upload Valid ID</label>
              <input type="file" class="form-control" id="indigencyId" name="valid_id" accept="image/*" required>
              <div class="file-hint">Photo of a government ID, up to 5 MB.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn" style="background: var(--ref-blue); color:#fff;">Submit Request</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Business Clearance modal -->
  <div class="modal fade" id="businessModal" tabindex="-1" aria-labelledby="businessTitle">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="businessTitle">Business Clearance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="doc-form" action="{{ url('/request-document/business') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="_doc" value="business">
          <div class="modal-body">
            @include('resident.partials.doc-errors', ['type' => 'business'])
            <div class="mb-3">
              <label class="form-label" for="bizName">Business Name</label>
              <input type="text" class="form-control" id="bizName" name="business_name" maxlength="120" value="{{ $old('business', 'business_name') }}" placeholder="e.g. Alejal Sari-Sari Store" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="bizAddress">Business Address</label>
              <input type="text" class="form-control" id="bizAddress" name="business_address" maxlength="200" value="{{ $old('business', 'business_address') }}" placeholder="Complete address within the barangay" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="bizType">Type of Business</label>
              <input type="text" class="form-control" id="bizType" name="business_type" maxlength="80" value="{{ $old('business', 'business_type') }}" placeholder="e.g. Retail, Food Stall, Services" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="bizId">Upload Valid ID</label>
              <input type="file" class="form-control" id="bizId" name="valid_id" accept="image/*" required>
              <div class="file-hint">Photo of a government ID, up to 5 MB.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn" style="background: var(--ref-blue); color:#fff;">Submit Request</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Vaccination Scheduling modal -->
  <div class="modal fade" id="vaccinationModal" tabindex="-1" aria-labelledby="vaccinationTitle">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="vaccinationTitle">Vaccination Scheduling</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="doc-form" action="{{ url('/request-document/vaccination') }}" method="POST">
          @csrf
          <input type="hidden" name="_doc" value="vaccination">
          <div class="modal-body">
            @include('resident.partials.doc-errors', ['type' => 'vaccination'])
            @php $eventId = $old('vaccination', 'event_id', $preEvent?->id); @endphp
            @if ($eventId)
              <input type="hidden" name="event_id" value="{{ $eventId }}">
            @endif
            @if ($preEvent)
              <div class="modal-note">
                Pre-registering for <strong>{{ $preEvent->title }}</strong>, {{ $preEvent->whenText() }}.
                The health center will confirm your slot. You can follow it on My Reports.
              </div>
            @endif
            <div class="mb-3">
              <label class="form-label" for="vaccineFor">This vaccination is for</label>
              <select class="form-select" id="vaccineFor" name="vaccine_for" required>
                <option value="" disabled @selected($old('vaccination', 'vaccine_for') === '')>Select</option>
                <option value="self" @selected($old('vaccination', 'vaccine_for') === 'self')>Myself</option>
                <option value="dependent" @selected($old('vaccination', 'vaccine_for') === 'dependent')>A dependent (child, elderly parent, etc.)</option>
              </select>
            </div>
            <div id="dependentFields" style="display: none;">
              <div class="mb-3">
                <label class="form-label" for="dependentName">Dependent's Full Name</label>
                <input type="text" class="form-control" id="dependentName" name="dependent_name" maxlength="120" value="{{ $old('vaccination', 'dependent_name') }}">
              </div>
              <div class="mb-3">
                <label class="form-label" for="dependentAge">Dependent's Age</label>
                <input type="number" class="form-control" min="0" max="120" id="dependentAge" name="dependent_age" value="{{ $old('vaccination', 'dependent_age') }}">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="vaccineType">Vaccine Type</label>
              <select class="form-select" id="vaccineType" name="vaccine" required>
                <option value="" disabled @selected($old('vaccination', 'vaccine') === '')>Select vaccine type</option>
                @foreach ($docTypes['vaccination']['vaccines'] as $v)
                  <option @selected($old('vaccination', 'vaccine') === $v)>{{ $v }}</option>
                @endforeach
              </select>
              <div class="mt-2" id="vaccineOtherBox" style="display: none;">
                <input type="text" class="form-control" id="vaccineOther" name="vaccine_other" maxlength="80"
                       value="{{ $old('vaccination', 'vaccine_other') }}" placeholder="Please specify the vaccine" aria-label="Vaccine name">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="vaccineDate">Preferred Date</label>
              <input type="date" class="form-control" id="vaccineDate" name="preferred_date" min="{{ now()->toDateString() }}" value="{{ $old('vaccination', 'preferred_date', $preEvent ? $preEvent->starts_at->toDateString() : '') }}" required>
              <div class="file-hint">The health center will confirm the final schedule.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn" style="background: var(--ref-blue); color:#fff;">Submit Request</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Senior Citizen / PWD ID modal -->
  <div class="modal fade" id="seniorPwdModal" tabindex="-1" aria-labelledby="seniorPwdTitle">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="seniorPwdTitle">Senior Citizen / PWD ID</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="doc-form" action="{{ url('/request-document/id') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="_doc" value="id">
          <div class="modal-body">
            @include('resident.partials.doc-errors', ['type' => 'id'])
            <div class="mb-3">
              <label class="form-label" for="idTypeSelect">ID Type</label>
              <select class="form-select" id="idTypeSelect" name="id_type" required>
                <option value="" disabled @selected($old('id', 'id_type') === '')>Select ID type</option>
                <option value="senior" @selected($old('id', 'id_type') === 'senior')>Senior Citizen ID</option>
                <option value="pwd" @selected($old('id', 'id_type') === 'pwd')>PWD (Person with Disability) ID</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label" for="idFullName">Full Name</label>
              <input type="text" class="form-control" id="idFullName" name="full_name" maxlength="120" value="{{ $old('id', 'full_name') }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="idBirthdate">Birthdate</label>
              <input type="date" class="form-control" id="idBirthdate" name="birthdate" max="{{ now()->subDay()->toDateString() }}" value="{{ $old('id', 'birthdate') }}" required>
            </div>
            <div id="pwdFields" style="display: none;">
              <div class="mb-3">
                <label class="form-label" for="idDisability">Type of Disability</label>
                <select class="form-select" id="idDisability" name="disability">
                  <option value="" disabled @selected($old('id', 'disability') === '')>Select</option>
                  @foreach ($docTypes['id']['disabilities'] as $d)
                    <option @selected($old('id', 'disability') === $d)>{{ $d }}</option>
                  @endforeach
                </select>
                <div class="mt-2" id="disabilityOtherBox" style="display: none;">
                  <input type="text" class="form-control" id="disabilityOther" name="disability_other" maxlength="80"
                         value="{{ $old('id', 'disability_other') }}" placeholder="Please specify the disability" aria-label="Type of disability">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label" for="idMedCert">Upload Medical Certificate</label>
                <input type="file" class="form-control" id="idMedCert" name="medical_certificate" accept="image/*,.pdf">
                <div class="file-hint">Image or PDF, up to 5 MB.</div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="idValid">Upload Valid ID / Birth Certificate</label>
              <input type="file" class="form-control" id="idValid" name="valid_id" accept="image/*" required>
              <div class="file-hint">Photo, up to 5 MB.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn" style="background: var(--ref-blue); color:#fff;">Submit Request</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Show the "please specify" box only when "Other" is selected (all 3 forms with a Purpose).
    function syncPurpose(select) {
      const box = select.closest('.mb-3').querySelector('.other-reason-box');
      const input = box.querySelector('input');
      const other = select.value === 'Other';
      box.style.display = other ? 'block' : 'none';
      input.required = other;
      if (!other) input.value = '';   // so an old answer doesn't get submitted by accident
    }
    document.querySelectorAll('.purpose-select').forEach(select => {
      select.addEventListener('change', () => syncPurpose(select));
      syncPurpose(select);
    });

    // Dependent fields on the Vaccination form
    function syncDependent() {
      const show = document.getElementById('vaccineFor').value === 'dependent';
      document.getElementById('dependentFields').style.display = show ? 'block' : 'none';
      document.getElementById('dependentName').required = show;
      document.getElementById('dependentAge').required = show;
    }
    document.getElementById('vaccineFor').addEventListener('change', syncDependent);
    syncDependent();

    // "Other" vaccine / disability: show a box to type it in
    function syncOther(selectId, boxId, inputId, extraCheck) {
      const sel = document.getElementById(selectId);
      const show = sel.value === 'Other' && (!extraCheck || extraCheck());
      document.getElementById(boxId).style.display = show ? 'block' : 'none';
      const input = document.getElementById(inputId);
      input.required = show;
      if (!show) input.value = '';
    }
    const syncVaccineOther = () => syncOther('vaccineType', 'vaccineOtherBox', 'vaccineOther');
    const syncDisabilityOther = () => syncOther('idDisability', 'disabilityOtherBox', 'disabilityOther',
      () => document.getElementById('idTypeSelect').value === 'pwd');
    document.getElementById('vaccineType').addEventListener('change', syncVaccineOther);
    document.getElementById('idDisability').addEventListener('change', syncDisabilityOther);
    syncVaccineOther();

    // PWD-only fields on the Senior/PWD ID form
    function syncPwd() {
      const pwd = document.getElementById('idTypeSelect').value === 'pwd';
      document.getElementById('pwdFields').style.display = pwd ? 'block' : 'none';
      document.getElementById('idDisability').required = pwd;
      document.getElementById('idMedCert').required = pwd;
      syncDisabilityOther();
    }
    document.getElementById('idTypeSelect').addEventListener('change', syncPwd);
    syncPwd();

    // Stop double submits while the upload is going
    document.querySelectorAll('.doc-form').forEach(form => {
      form.addEventListener('submit', () => {
        const btn = form.querySelector('[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Submitting...';
      });
    });

    // A form came back with errors: reopen it
    @if (old('_doc') && isset($modalFor[old('_doc')]))
      new bootstrap.Modal(document.getElementById(@json($modalFor[old('_doc')]))).show();
    @elseif ($preEvent)
      // Came from "Pre-register" on the calendar
      new bootstrap.Modal(document.getElementById('vaccinationModal')).show();
    @endif
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <script src="{{ asset('js/pabarang.js') }}"></script>
  {{-- pb-doc-forms.js removed: the forms now post to the server. Add it back only if it does more than the old demo submit. --}}
</body>
</html>
