<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile - Tugon</title>
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

    .page-wrap { max-width: 720px; margin: 0 auto; padding: 40px 20px; }
    .profile-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 14px; padding: 32px; margin-bottom: 20px; }
    .avatar-circle { width: 84px; height: 84px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 30px; font-weight: 700; margin: 0 auto 14px; }
    .status-badge { font-size: 12px; font-weight: 700; background: #E1F5E4; color: #1E7A34; padding: 4px 12px; border-radius: 12px; }
    .section-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--ref-gray-text); margin: 26px 0 14px; padding-top: 20px; border-top: 1px solid var(--ref-border); }
    .section-title.first { margin-top: 0; padding-top: 0; border-top: none; }

    .info-row { margin-bottom: 16px; }
    .info-label { font-size: 12px; font-weight: 600; color: var(--ref-gray-text); margin-bottom: 2px; }
    .info-value { font-size: 14.5px; font-weight: 600; }

    .lock-note { display:flex; gap:8px; align-items:flex-start; background: var(--ref-bg); border:1px solid var(--ref-border); border-radius:8px; padding:10px 12px; font-size:12.5px; color: var(--ref-gray-text); margin-bottom:22px; }
    .lock-note i { margin-top:2px; }
    .id-card { display:flex; align-items:center; gap:12px; border:1px dashed var(--ref-border); border-radius:10px; padding:12px 14px; background: var(--ref-bg); }
    .id-card i { font-size: 24px; color: var(--ref-blue); }

    .form-label { font-weight: 600; font-size: 13px; }
    .req-status { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 10px; }
    .req-pending { background: #FDF1D8; color: #92650C; }
    .req-approved { background: #E1F5E4; color: #1E7A34; }
    .req-rejected { background: #FDE8E8; color: #D64545; }
    table.req-table { font-size: 13px; }
    table.req-table th { font-size: 12px; color: var(--ref-gray-text); font-weight: 600; }
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
      <h5 class="fw-bold mb-0">My Profile</h5>
    </div>

  <div class="page-wrap">

    {{--
      BACKEND TODO (final term):
      This page is READ-ONLY on purpose. Every value below comes from the
      resident's verified registration record, so it must be pre-filled from
      auth()->user() instead of the hardcoded demo values.

      There is deliberately NO update route for these fields. Changes go
      through the correction-request form below, which becomes:
        POST /profile/change-request -> ProfileChangeRequestController@store
      inserting into a `profile_change_requests` table:
        id, user_id, field, old_value, new_value, reason, proof_path,
        status (pending/approved/rejected), reviewed_by, reviewed_at, remarks
      Only when a barangay official approves does the system update the
      `users` table, and the old/new values are kept for the audit trail.
    --}}

    <div class="profile-card">

      <div class="text-center mb-4">
        <div class="avatar-circle">{{ mb_strtoupper(mb_substr($residentName, 0, 1)) }}</div>
        <h4 class="fw-bold mb-1">{{ $residentName }}</h4>
        <span class="status-badge"><i class="ti ti-check"></i> Verified Resident</span>
      </div>

      <div class="section-title first">Account</div>
      <div class="row">
        <div class="col-md-4 info-row">
          <div class="info-label">Resident ID</div>
          <div class="info-value">ALJ-2026-0042</div>
        </div>
        <div class="col-md-4 info-row">
          <div class="info-label">Account Status</div>
          <div class="info-value">Active</div>
        </div>
        <div class="col-md-4 info-row">
          <div class="info-label">Date Verified</div>
          <div class="info-value">September 2, 2026</div>
        </div>
      </div>

      <div class="section-title">Personal Information</div>
      <div class="row">
        <div class="col-md-6 info-row">
          <div class="info-label">Full Name</div>
          <div class="info-value">{{ $residentName }}</div>
        </div>
        <div class="col-md-6 info-row">
          <div class="info-label">Civil Status</div>
          <div class="info-value">Single</div>
        </div>
        <div class="col-md-6 info-row">
          <div class="info-label">Purok</div>
          <div class="info-value">Purok 3</div>
        </div>
        <div class="col-md-6 info-row">
          <div class="info-label">Address</div>
          <div class="info-value">123 Mabini Street, Barangay Alejal</div>
        </div>
      </div>

      <div class="section-title">Contact Details</div>
      <div class="row">
        <div class="col-md-6 info-row">
          <div class="info-label">Email</div>
          <div class="info-value">{{ $residentEmail }}</div>
        </div>
        <div class="col-md-6 info-row">
          <div class="info-label">Contact Number</div>
          <div class="info-value">0917 123 4567</div>
        </div>
        <div class="col-md-12 info-row">
          <div class="info-label">Emergency Contact Person</div>
          <div class="info-value">Eddie Aguilar Jr. - 0917 987 6543</div>
        </div>
      </div>

      <div class="section-title">Verification Document</div>
      <div class="id-card">
        <i class="ti ti-id"></i>
        <div>
          <div class="info-value" style="font-size:14px;">Valid ID on file</div>
          <div style="font-size:12.5px; color: var(--ref-gray-text);">Uploaded during registration. For your privacy, the image is only viewable by barangay staff.</div>
        </div>
      </div>

      <div class="mt-4">
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changeRequestModal">
          <i class="ti ti-edit"></i> Request a Correction
        </button>
      </div>
    </div>

    <div class="profile-card">
      <div class="fw-bold mb-3" style="font-size:15px;">My Correction Requests</div>
      <div class="table-responsive">
        <table class="table req-table align-middle mb-0">
          <thead>
            <tr><th>Date</th><th>Field</th><th>Requested Change</th><th>Status</th></tr>
          </thead>
          <tbody id="requestRows">
            <tr>
              <td>Sep 15, 2026</td>
              <td>Contact Number</td>
              <td>0917 123 4567</td>
              <td><span class="req-status req-approved">APPROVED</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
  </div>

  <!-- ===== Correction request modal ===== -->
  <div class="modal fade" id="changeRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content" id="changeRequestForm">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Request a Correction</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p style="font-size:13px; color: var(--ref-gray-text);">
            Your request is reviewed by barangay staff. Nothing changes on your profile until it is approved.
          </p>

          <div class="mb-3">
            <label class="form-label">What needs to be corrected?</label>
            <select class="form-select" id="crField" required>
              <option value="" selected disabled>Select a field</option>
              <optgroup label="Personal information (proof required)">
                <option>Full Name</option>
                <option>Civil Status</option>
                <option>Address</option>
                <option>Purok</option>
              </optgroup>
              <optgroup label="Contact details">
                <option>Email</option>
                <option>Contact Number</option>
                <option>Emergency Contact Person</option>
              </optgroup>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Correct / New Value</label>
            <input type="text" class="form-control" id="crValue" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Reason for the change</label>
            <textarea class="form-control" rows="2" placeholder="e.g. Typo during registration, moved to a new house, got married" required></textarea>
          </div>

          <div class="mb-1">
            <label class="form-label">Supporting Document</label>
            <input type="file" class="form-control" accept="image/*,.pdf" required>
            <div style="font-size:12px; color: var(--ref-gray-text); margin-top:4px;">
              Upload a valid ID, marriage certificate, or another document that supports the change.
              Staff may ask you to visit the barangay hall to confirm.
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn" style="background: var(--ref-blue); color:#fff;">Submit Request</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // DEMO-ONLY: a real submission is stored as a pending request for staff review.
    document.getElementById('changeRequestForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const field = document.getElementById('crField').value;
      const value = document.getElementById('crValue').value;
      const today = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

      const row = document.createElement('tr');
      row.innerHTML = '<td></td><td></td><td></td><td><span class="req-status req-pending">PENDING REVIEW</span></td>';
      row.children[0].textContent = today;
      row.children[1].textContent = field;
      row.children[2].textContent = value;
      document.getElementById('requestRows').prepend(row);

      bootstrap.Modal.getInstance(document.getElementById('changeRequestModal')).hide();
      this.reset();
      alert('Request submitted for barangay review. (Demo only, nothing is saved to a database yet.)');
    });
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <script src="{{ asset('js/pabarang.js') }}"></script>
</body>
</html>