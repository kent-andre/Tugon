<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Resident Accounts - Tugon Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --ref-blue: #4F5FE0; --ref-navy-text: #1E2340; --ref-gray-text: #6B7280;
      --ref-bg: #F5F6FA; --ref-border: #E4E6EF; --ref-red: #D64545;
    }
    body { font-family: 'Lato', sans-serif; color: var(--ref-navy-text); background: var(--ref-bg); margin: 0; }

    .admin-layout { display: flex; min-height: 100vh; }
    .admin-sidebar { width: 250px; background: #1E2340; padding: 24px 18px; flex-shrink: 0; }
    .admin-sidebar .brand { display: flex; align-items: center; gap: 10px; margin-bottom: 30px; padding: 0 6px; }
    .admin-sidebar .brand-logo-lg { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; }
    .admin-sidebar .brand-name { color: #fff; font-size: 15px; font-weight: 700; }
    .sidebar-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #7A80A8; padding: 10px 12px 6px; }
    .sidebar-link { display: flex; align-items: center; gap: 10px; color: #C4C8E8; text-decoration: none; padding: 10px 12px; border-radius: 8px; font-size: 14px; margin-bottom: 2px; }
    .sidebar-link i { font-size: 17px; }
    .sidebar-link:hover { background: rgba(255,255,255,0.06); color: #fff; }
    .sidebar-link.active { background: var(--ref-blue); color: #fff; }
    .sidebar-link .count {
      margin-left: auto; min-width: 22px; padding: 1px 8px;
      border-radius: 999px; background: #F5B83D; color: #3A2900;
      font-size: 11.5px; font-weight: 700; text-align: center;
    }
    .sidebar-link .count:empty { display: none; }

    .admin-main { flex: 1; padding: 28px 36px; }
    .admin-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
    .admin-topbar h4 { font-weight: 800; margin: 0; }
    .admin-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; }

    /* Stat strip */
    .stat-strip { display: flex; gap: 14px; margin-bottom: 22px; flex-wrap: wrap; }
    .stat-mini { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 14px 18px; flex: 1; min-width: 120px; }
    .stat-mini .stat-number { font-size: 22px; font-weight: 800; }
    .stat-mini .stat-label { font-size: 11.5px; color: var(--ref-gray-text); }

    /* Tabs */
    .box { background: #fff; border: 1px solid var(--ref-border); border-radius: 14px; overflow: hidden; }
    .tabs { display: flex; gap: 6px; padding: 0 18px; border-bottom: 1px solid var(--ref-border); overflow-x: auto; }
    .tab {
      background: none; border: none; border-bottom: 3px solid transparent;
      padding: 15px 10px 12px; font-size: 14px; font-weight: 600; color: var(--ref-gray-text);
      white-space: nowrap; display: flex; align-items: center; gap: 7px;
    }
    .tab:hover { color: var(--ref-navy-text); }
    .tab.active { color: var(--ref-blue); border-bottom-color: var(--ref-blue); }
    .tab-count { background: var(--ref-bg); color: var(--ref-gray-text); border-radius: 999px; padding: 1px 9px; font-size: 12px; font-weight: 700; }
    .tab.active .tab-count { background: #E7EAFD; color: var(--ref-blue); }

    /* Resident rows */
    .resident-row { display: flex; align-items: center; gap: 14px; padding: 16px 20px; border-bottom: 1px solid var(--ref-border); }
    .resident-row:last-child { border-bottom: none; }
    .resident-avatar {
      width: 42px; height: 42px; border-radius: 50%; background: #E7EAFD; color: var(--ref-blue);
      display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0;
    }
    .resident-name { font-size: 14.5px; font-weight: 700; }
    .resident-meta { font-size: 12px; color: var(--ref-gray-text); }
    .resident-actions { display: flex; gap: 8px; margin-left: auto; flex-shrink: 0; }
    .resident-actions button { font-size: 12.5px; }

    .status-pill { font-size: 11.5px; font-weight: 600; padding: 4px 11px; border-radius: 10px; white-space: nowrap; }
    .st-pending  { background: #FDF1D8; color: #92650C; }
    .st-approved { background: #E1F5E4; color: #1E7A34; }
    .st-rejected { background: #FDE8E8; color: var(--ref-red); }

    .empty { padding: 56px 20px; text-align: center; color: var(--ref-gray-text); }
    .empty i { font-size: 34px; color: #B8BDD6; display: block; margin-bottom: 8px; }

    /* Modal */
    .modal-section-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--ref-gray-text); margin: 18px 0 8px; }
    .modal-section-label:first-of-type { margin-top: 0; }
    #modalIdImg { width: 100%; max-height: 220px; object-fit: cover; border-radius: 8px; border: 1px solid var(--ref-border); }
    .rid-box { display: flex; justify-content: space-between; align-items: center; background: #E1F5E4; border: 1px solid #BFE3C7; border-radius: 10px; padding: 12px 16px; margin-bottom: 4px; }
    .rid-box .rid-label { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #1E7A34; }
    .rid-box .rid-value { font-size: 18px; font-weight: 800; color: #1E7A34; }
    .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px 18px; }
    .info-grid .span-2 { grid-column: span 2; }
    .info-grid .span-3 { grid-column: span 3; }
    .info-label { font-size: 12px; font-weight: 600; color: var(--ref-gray-text); margin-bottom: 1px; }
    .info-value { font-size: 14px; font-weight: 600; }
    @media (max-width: 575.98px) { .info-grid { grid-template-columns: 1fr 1fr; } .info-grid .span-3 { grid-column: span 2; } }
    .reject-box { background: #FDE8E8; border: 1px solid #F3C4C4; border-radius: 10px; padding: 12px 14px; font-size: 13.5px; }
  </style>
</head>
<body>

  <div class="admin-layout">

    @include('partials.admin-sidebar')


    <!-- ===== Main content ===== -->
    <div class="admin-main">

      <div class="admin-topbar">
        <h4>Resident Accounts</h4>
        @include('partials.admin-user')
      </div>

      @php
        $pendingCount = collect($residents)->where('status', 'pending')->count();
        $approvedCount = collect($residents)->where('status', 'approved')->count();
        $rejectedCount = collect($residents)->where('status', 'rejected')->count();
      @endphp
      <div class="stat-strip">
        <div class="stat-mini"><div class="stat-number">{{ count($residents) }}</div><div class="stat-label">Total Registrations</div></div>
        <div class="stat-mini"><div class="stat-number" id="statPending">{{ $pendingCount }}</div><div class="stat-label">Awaiting Approval</div></div>
        <div class="stat-mini"><div class="stat-number" id="statApproved">{{ $approvedCount }}</div><div class="stat-label">Approved</div></div>
        <div class="stat-mini"><div class="stat-number" id="statRejected">{{ $rejectedCount }}</div><div class="stat-label">Rejected</div></div>
      </div>

      <div class="box">
        <div class="tabs" id="statusTabs" role="group" aria-label="Filter by status">
          <button type="button" class="tab active" data-status="all">All<span class="tab-count">{{ count($residents) }}</span></button>
          <button type="button" class="tab" data-status="pending">Pending<span class="tab-count">{{ $pendingCount }}</span></button>
          <button type="button" class="tab" data-status="approved">Approved<span class="tab-count">{{ $approvedCount }}</span></button>
          <button type="button" class="tab" data-status="rejected">Rejected<span class="tab-count">{{ $rejectedCount }}</span></button>
        </div>

        <div id="residentList">
          @foreach ($residents as $r)
            @php
              $initials = collect(explode(' ', $r['name']))->filter()->map(fn($w) => $w[0])->take(2)->implode('');
            @endphp
            <div class="resident-row" data-status="{{ $r['status'] }}" data-info="{{ json_encode($r) }}">
              <div class="resident-avatar">{{ $initials }}</div>
              <div class="flex-grow-1">
                <div class="resident-name">{{ $r['name'] }}</div>
                <div class="resident-meta" data-meta></div>
              </div>
              <span class="status-pill st-{{ $r['status'] }}">{{ ucfirst($r['status']) }}</span>
              <div class="resident-actions">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#residentModal" data-open="view">View</button>
                @if ($r['status'] === 'pending')
                  <button type="button" class="btn btn-outline-success btn-sm" data-action="approve">Approve</button>
                  <button type="button" class="btn btn-outline-danger btn-sm" data-action="reject">Reject</button>
                @endif
              </div>
            </div>
          @endforeach
        </div>

        <div class="empty d-none" id="emptyState">
          <i class="ti ti-user-off"></i>
          <strong>No accounts in this category</strong>
        </div>
      </div>

    </div>
  </div>

  <!-- Detail modal -->
  <div class="modal fade" id="residentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalName">Resident Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <div class="d-flex justify-content-between align-items-start mb-3">
            <p style="font-size: 13px; color: var(--ref-gray-text); margin: 0;" id="modalMeta"></p>
            <span class="status-pill" id="modalStatusBadge"></span>
          </div>

          <div id="modalRidBox" class="rid-box d-none">
            <div>
              <div class="rid-label">Resident ID</div>
              <div class="rid-value" id="modalRid"></div>
            </div>
            <div style="font-size: 12.5px; color:#1E7A34;" id="modalApprovedOn"></div>
          </div>

          <div class="modal-section-label">Personal Information</div>
          <div class="info-grid">
            <div class="span-3"><div class="info-label">Full Name</div><div class="info-value" id="mFullName"></div></div>
            <div><div class="info-label">Birthdate</div><div class="info-value" id="mBirthdate"></div></div>
            <div><div class="info-label">Age</div><div class="info-value" id="mAge"></div></div>
            <div><div class="info-label">Gender</div><div class="info-value" id="mGender"></div></div>
            <div><div class="info-label">Civil Status</div><div class="info-value" id="mCivil"></div></div>
            <div class="span-2"><div class="info-label">Occupation</div><div class="info-value" id="mOccupation"></div></div>
          </div>

          <div class="modal-section-label">Contact Details</div>
          <div class="info-grid">
            <div class="span-2"><div class="info-label">Email</div><div class="info-value" id="mEmail"></div></div>
            <div><div class="info-label">Mobile Number</div><div class="info-value" id="mMobile"></div></div>
            <div class="span-3"><div class="info-label">Emergency Contact Person</div><div class="info-value" id="mEmergency"></div></div>
          </div>

          <div class="modal-section-label">Address</div>
          <div class="info-grid">
            <div><div class="info-label">Zone</div><div class="info-value" id="mZone"></div></div>
            <div class="span-2"><div class="info-label">Complete Address</div><div class="info-value" id="mAddress"></div></div>
          </div>

          <div class="modal-section-label">Valid ID Submitted</div>
          <p style="font-size: 13px; color: var(--ref-gray-text); margin-bottom:8px;" id="modalIdType"></p>
          <img id="modalIdImg" src="" alt="Valid ID">

          <div id="modalRejectBox" class="reject-box mt-3 d-none">
            <strong>Rejection reason:</strong> <span id="modalRejectReason"></span>
          </div>

        </div>
        <div class="modal-footer" id="modalFooter">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000;">
    <div class="toast align-items-center text-bg-dark border-0" id="appToast" role="status" aria-live="polite" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="toastMsg"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const STATUS_LABEL = { pending: 'Pending', approved: 'Approved', rejected: 'Rejected' };
    const NO_ID_IMG = 'https://placehold.co/700x300/E4E6EF/6B7280?text=No+ID+photo+on+file';
    let currentFilter = 'all';
    let activeRow = null;
    const residentModal = new bootstrap.Modal(document.getElementById('residentModal'));

    // Every row carries its registration record as JSON; keep it on the element.
    document.querySelectorAll('.resident-row').forEach(row => {
      row._info = JSON.parse(row.dataset.info);
      renderMeta(row);
    });

    function toast(msg) {
      document.getElementById('toastMsg').textContent = msg;
      bootstrap.Toast.getOrCreateInstance(document.getElementById('appToast')).show();
    }

    function renderMeta(row) {
      const i = row._info;
      const parts = [];
      if (i.status === 'approved' && i.residentId) parts.push(i.residentId);
      parts.push(i.zone, i.contact, 'Registered ' + i.registered);
      row.querySelector('[data-meta]').textContent = parts.filter(Boolean).join(' · ');
    }

    function refreshCounts() {
      const rows = document.querySelectorAll('.resident-row');
      const counts = { all: rows.length, pending: 0, approved: 0, rejected: 0 };
      rows.forEach(r => counts[r.dataset.status]++);
      document.querySelectorAll('.tab').forEach(t => t.querySelector('.tab-count').textContent = counts[t.dataset.status]);
      document.getElementById('statPending').textContent = counts.pending;
      document.getElementById('statApproved').textContent = counts.approved;
      document.getElementById('statRejected').textContent = counts.rejected;
      const badge = document.getElementById('pendingBadge');
      if (badge) badge.textContent = counts.pending || '';
      applyFilter();
    }

    function applyFilter() {
      let visible = 0;
      document.querySelectorAll('.resident-row').forEach(row => {
        const match = currentFilter === 'all' || row.dataset.status === currentFilter;
        row.classList.toggle('d-none', !match);
        if (match) visible++;
      });
      document.getElementById('emptyState').classList.toggle('d-none', visible > 0);
    }

    document.getElementById('statusTabs').addEventListener('click', e => {
      const b = e.target.closest('.tab');
      if (!b) return;
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      b.classList.add('active');
      currentFilter = b.dataset.status;
      applyFilter();
    });

    // Saves to the server first. The row only changes, and onDone only runs, if the server accepted it.
    async function setRowStatus(row, status, reason, onDone) {
      const url = "{{ url('/admin/accounts') }}/" + row._info.id + '/' + (status === 'approved' ? 'approve' : 'reject');
      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
          body: JSON.stringify({ reason: reason || '' })
        });
        if (res.status === 401) { toast('Your session expired. Please log in again.'); return; }
        const b = await res.json().catch(() => ({}));
        if (!res.ok) { toast(b.errors ? Object.values(b.errors)[0][0] : (b.message || 'Could not save.')); return; }

        const i = row._info;
        i.status = status;
        i.reason = reason || '';
        if (status === 'approved') {
          i.residentId = b.resident_id;
          i.approvedOn = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
        row.dataset.status = status;
        const pill = row.querySelector('.status-pill');
        pill.className = 'status-pill st-' + status;
        pill.textContent = STATUS_LABEL[status];
        row.querySelectorAll('[data-action]').forEach(btn => btn.remove());
        renderMeta(row);
        refreshCounts();
        if (onDone) onDone();
      } catch (e) {
        toast('Could not reach the server. Check your connection and try again.');
      }
    }

    function approve(row, after) {
      const name = row._info.name;
      if (!confirm('Approve ' + name + "'s account? Check the details and the ID photo first.")) return;
      setRowStatus(row, 'approved', null, () => { toast(name + ' approved. Resident ID ' + row._info.residentId); if (after) after(); });
    }
    function reject(row, after) {
      const name = row._info.name;
      const reason = prompt('Reason for rejecting ' + name + "'s registration (the resident sees this when logging in):");
      if (!reason || !reason.trim()) return;
      setRowStatus(row, 'rejected', reason.trim(), () => { toast(name + ' rejected'); if (after) after(); });
    }

    document.getElementById('residentList').addEventListener('click', e => {
      const row = e.target.closest('.resident-row');
      if (!row) return;
      if (e.target.closest('[data-open="view"]')) { activeRow = row; fillModal(row); }
      else if (e.target.closest('[data-action="approve"]')) approve(row);
      else if (e.target.closest('[data-action="reject"]')) reject(row);
    });

    function setText(id, value) { document.getElementById(id).textContent = value; }

    function fillModal(row) {
      const d = row._info;
      setText('modalName', d.name);
      setText('modalMeta', d.zone + ' · Registered ' + d.registered);

      const ridBox = document.getElementById('modalRidBox');
      if (d.status === 'approved' && d.residentId) {
        setText('modalRid', d.residentId);
        setText('modalApprovedOn', d.approvedOn ? 'Approved ' + d.approvedOn : '');
        ridBox.classList.remove('d-none');
      } else {
        ridBox.classList.add('d-none');
      }

      setText('mFullName', d.fullName);
      setText('mBirthdate', d.birthdateLabel);
      setText('mAge', d.age);
      setText('mGender', d.gender);
      setText('mCivil', d.civil_status);
      setText('mOccupation', d.occupation || 'Not provided');
      setText('mEmail', d.email);
      setText('mMobile', d.contact);
      setText('mEmergency', d.emergency || 'Not provided');
      setText('mZone', d.zone);
      setText('mAddress', d.address);
      setText('modalIdType', d.idType);
      document.getElementById('modalIdImg').src = d.idUrl || NO_ID_IMG;

      const badge = document.getElementById('modalStatusBadge');
      badge.className = 'status-pill st-' + d.status;
      badge.textContent = STATUS_LABEL[d.status];

      const rejectBox = document.getElementById('modalRejectBox');
      if (d.status === 'rejected' && d.reason) {
        rejectBox.classList.remove('d-none');
        setText('modalRejectReason', d.reason);
      } else {
        rejectBox.classList.add('d-none');
      }

      const footer = document.getElementById('modalFooter');
      footer.innerHTML = '<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>';
      if (d.status === 'pending') {
        footer.insertAdjacentHTML('afterbegin', `
          <button type="button" class="btn btn-outline-danger btn-sm me-auto" id="modalRejectBtn">Reject</button>
          <button type="button" class="btn btn-sm" style="background: var(--ref-blue); color:#fff;" id="modalApproveBtn">Approve</button>`);
        document.getElementById('modalApproveBtn').addEventListener('click', () => approve(activeRow, () => residentModal.hide()));
        document.getElementById('modalRejectBtn').addEventListener('click', () => reject(activeRow, () => residentModal.hide()));
      }
      residentModal.show();
    }

    refreshCounts();
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <link href="{{ asset('css/pabarang-admin.css') }}" rel="stylesheet">
</body>
</html>