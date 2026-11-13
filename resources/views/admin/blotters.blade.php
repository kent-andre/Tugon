<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Blotter Reports - Tugon Admin</title>
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

    .admin-main { flex: 1; padding: 28px 36px; }
    .admin-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
    .admin-topbar h4 { font-weight: 800; margin: 0; }
    .admin-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; }

    .stat-strip { display: flex; gap: 14px; margin-bottom: 22px; flex-wrap: wrap; }
    .stat-mini { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 14px 18px; flex: 1; min-width: 120px; }
    .stat-mini .stat-number { font-size: 22px; font-weight: 800; }
    .stat-mini .stat-label { font-size: 11.5px; color: var(--ref-gray-text); }

    .filter-bar { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 14px 18px; margin-bottom: 18px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .filter-bar select, .filter-bar input { font-size: 13px; }
    .filter-bar input[type="search"] { flex: 1 1 220px; }

    .blotter-card {
      background: #fff; border: 1px solid var(--ref-border); border-radius: 12px;
      padding: 16px 18px; margin-bottom: 10px; display: flex; align-items: center; gap: 14px;
      border-left: 4px solid var(--ref-border); cursor: pointer;
    }
    .blotter-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .blotter-card.border-escalated { border-left-color: var(--ref-red); }
    .blotter-card.border-active { border-left-color: var(--ref-blue); }

    .blotter-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: var(--ref-bg); color: var(--ref-blue); font-size: 19px; }
    .blotter-title { font-size: 14.5px; font-weight: 700; }
    .blotter-meta { font-size: 12px; color: var(--ref-gray-text); }

    .status-pill { font-size: 11.5px; font-weight: 600; padding: 4px 11px; border-radius: 10px; white-space: nowrap; }
    .st-filed     { background: #FDF1D8; color: #92650C; }
    .st-summons   { background: #E1EEFB; color: #0C5FA1; }
    .st-hearing   { background: #E7EAFD; color: var(--ref-blue); }
    .st-settled   { background: #E1F5E4; color: #1E7A34; }
    .st-escalated { background: #FDE8E8; color: var(--ref-red); }
    .st-dismissed { background: #EDEDED; color: #555; }
    .st-recorded  { background: #EDEDED; color: #555; }
    .st-referred  { background: #F0E8FB; color: #6B3FA0; }

    .empty-state { padding: 56px 20px; text-align: center; color: var(--ref-gray-text); background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; }
    .empty-state i { font-size: 34px; color: #B8BDD6; display: block; margin-bottom: 8px; }

    .modal-section-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--ref-gray-text); margin: 18px 0 8px; }
    .modal-section-label:first-of-type { margin-top: 0; }
    .party-row { display: flex; gap: 18px; }
    .party-box { flex: 1; background: var(--ref-bg); border-radius: 10px; padding: 12px 14px; }
    .party-box .party-role { font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--ref-gray-text); letter-spacing: .04em; }
    .party-box .party-name { font-size: 14px; font-weight: 700; margin-top: 2px; }
    .party-box .party-info { font-size: 12px; color: var(--ref-gray-text); margin-top: 2px; }
    .cfa-box { background: #FDE8E8; border: 1px solid #F3C4C4; border-radius: 10px; padding: 12px 14px; font-size: 13.5px; }
    .settlement-box { background: #E1F5E4; border: 1px solid #BFE6C6; border-radius: 10px; padding: 12px 14px; font-size: 13.5px; }
    .note-box { background: #F0E8FB; border: 1px solid #DCCBF2; border-radius: 10px; padding: 12px 14px; font-size: 13.5px; }
    .summons-box { background: #E1EEFB; border: 1px solid #C3DBF3; border-radius: 10px; padding: 12px 14px; font-size: 13.5px; }
    .detail-list { display: grid; grid-template-columns: 130px 1fr; gap: 6px 14px; font-size: 13.5px; margin: 0; }
    .detail-list dt { font-weight: 600; color: var(--ref-gray-text); }
    .detail-list dd { margin: 0; }
    .small-label { font-size: 12.5px; }
    .hint { font-size: 12.5px; color: var(--ref-gray-text); }
  </style>
</head>
<body>

  @php
    // FR-26: mediators come only from the Lupong Tagapamayapa. The Punong Barangay
    // chairs the Lupon, so the captain is listed first, then the Lupon members
    // from the Officials page.
    $captainRow = \App\Models\Official::where('position', 'Punong Barangay')->first();
    $captain = $captainRow ? 'Hon. ' . $captainRow->name : config('pabarang.captain_name');
    $mediators = collect([$captain])
      ->merge(\App\Models\Official::lupon()->ordered()->pluck('name'))
      ->filter()->unique()->values();
  @endphp

  <div class="admin-layout">

    @include('partials.admin-sidebar')


    <!-- Main content -->
    <div class="admin-main">

      <div class="admin-topbar">
        <h4>Blotter Reports</h4>
        @include('partials.admin-user')
      </div>

      @php
        $counts = ['filed'=>0,'summons'=>0,'hearing'=>0,'settled'=>0,'escalated'=>0,'dismissed'=>0,'recorded'=>0,'referred'=>0];
        foreach ($blotters as $b) { $counts[$b['status']]++; }
        $statusLabels = [
          'filed' => 'Filed', 'summons' => 'Summons Sent', 'hearing' => 'Hearing Scheduled',
          'settled' => 'Settled', 'escalated' => 'Escalated', 'dismissed' => 'Dismissed',
          'recorded' => 'Recorded', 'referred' => 'Referred',
        ];
      @endphp
      <div class="stat-strip">
        <div class="stat-mini"><div class="stat-number" id="statTotal">{{ count($blotters) }}</div><div class="stat-label">Total Entries</div></div>
        <div class="stat-mini"><div class="stat-number" id="statAwaiting">{{ $counts['filed'] + $counts['summons'] }}</div><div class="stat-label">Awaiting Hearing</div></div>
        <div class="stat-mini"><div class="stat-number" id="statHearing">{{ $counts['hearing'] }}</div><div class="stat-label">Hearing Scheduled</div></div>
        <div class="stat-mini"><div class="stat-number" id="statSettled">{{ $counts['settled'] }}</div><div class="stat-label">Settled</div></div>
        <div class="stat-mini"><div class="stat-number" id="statEscalated">{{ $counts['escalated'] }}</div><div class="stat-label">Escalated to Court</div></div>
        <div class="stat-mini"><div class="stat-number" id="statOther">{{ $counts['recorded'] + $counts['referred'] }}</div><div class="stat-label">Recorded / Referred</div></div>
      </div>

      <div class="filter-bar">
        <input type="search" class="form-control form-control-sm" id="searchInput" placeholder="Search by reference, complainant, or respondent">
        <select class="form-select form-select-sm" id="filterStatus" style="width: auto;">
          <option value="all">All Statuses</option>
          @foreach ($statusLabels as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
        <select class="form-select form-select-sm" id="filterType" style="width: auto;">
          <option value="all">All Types</option>
          @foreach (array_unique(array_column($blotters, 'type')) as $t)
            <option value="{{ $t }}">{{ $t }}</option>
          @endforeach
        </select>
      </div>

      <div id="caseList">
        @foreach ($blotters as $b)
          <div class="blotter-card {{ $b['status'] === 'escalated' ? 'border-escalated' : (in_array($b['status'], ['summons','hearing']) ? 'border-active' : '') }}"
               data-status="{{ $b['status'] }}" data-type="{{ $b['type'] }}"
               data-search="{{ strtolower($b['ref'].' '.$b['complainant'].' '.$b['respondent']) }}"
               data-bs-toggle="modal" data-bs-target="#blotterModal"
               data-id="{{ $b['id'] }}" data-ref="{{ $b['ref'] }}" data-type-val="{{ $b['type'] }}" data-zone="{{ $b['zone'] }}"
               data-complainant="{{ $b['complainant'] }}" data-respondent="{{ $b['respondent'] }}"
               data-comp-info="{{ $b['complainant_info'] }}" data-resp-info="{{ $b['respondent_info'] }}"
               data-comp-email="{{ $b['complainant_email'] }}" data-resp-email="{{ $b['respondent_email'] }}"
               data-resp-unknown="{{ $b['respondent_unknown'] ? '1' : '' }}"
               data-narrative="{{ $b['narrative'] }}" data-filed="{{ $b['filed'] }}"
               data-status-val="{{ $b['status'] }}" data-hearing="{{ $b['hearing_date'] }}"
               data-hearing-place="{{ $b['hearing_place'] }}" data-summons-sent="{{ $b['summons_sent'] }}"
               data-served-by="{{ $b['served_by'] }}" data-served-at="{{ $b['served_at'] }}"
               data-served-at-input="{{ $b['served_at_input'] }}" data-service-method="{{ $b['service_method'] }}"
               data-service-label="{{ $b['service_label'] }}"
               data-mediator="{{ $b['mediator'] }}" data-settlement="{{ $b['settlement'] }}"
               data-cfa="{{ $b['cfa_no'] }}"
               data-place="{{ $b['place'] }}" data-recorded="{{ $b['recorded_by'] }}"
               data-source="{{ $b['source'] }}" data-incident="{{ $b['incident'] }}"
               data-witnesses="{{ $b['witnesses'] }}" data-referred="{{ $b['referred_to'] }}">
            <div class="blotter-icon"><i class="ti ti-gavel"></i></div>
            <div class="flex-grow-1">
              <div class="blotter-title">{{ $b['complainant'] }} vs. {{ $b['respondent'] }}</div>
              <div class="blotter-meta">{{ $b['ref'] }} &middot; {{ $b['type'] }} &middot; {{ $b['zone'] }} &middot; Filed {{ $b['filed'] }}</div>
            </div>
            <span class="status-pill st-{{ $b['status'] }}">{{ $statusLabels[$b['status']] ?? $b['status'] }}</span>
          </div>
        @endforeach
      </div>

      <div class="empty-state {{ count($blotters) ? 'd-none' : '' }}" id="emptyState">
        <i class="ti ti-clipboard-off"></i>
        <strong>{{ count($blotters) ? 'No cases match these filters' : 'No blotter cases have been recorded yet' }}</strong>
      </div>

    </div>
  </div>

  <!-- One shared modal, filled in from the card that was clicked -->
  <div class="modal fade" id="blotterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Blotter Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <div class="d-flex justify-content-between align-items-start mb-3">
            <p style="font-size: 13px; color: var(--ref-gray-text); margin: 0;" id="modalMeta"></p>
            <span class="status-pill" id="modalStatusBadge"></span>
          </div>

          <div class="party-row mb-2">
            <div class="party-box">
              <div class="party-role">Complainant</div>
              <div class="party-name" id="modalComplainant"></div>
              <div class="party-info" id="modalComplainantInfo"></div>
              <div class="party-info" id="modalComplainantEmail"></div>
            </div>
            <div class="party-box">
              <div class="party-role">Respondent</div>
              <div class="party-name" id="modalRespondent"></div>
              <div class="party-info" id="modalRespondentInfo"></div>
              <div class="party-info" id="modalRespondentEmail"></div>
            </div>
          </div>

          <div class="modal-section-label">Incident Narrative</div>
          <p style="font-size: 13.5px;" id="modalNarrative"></p>

          <div id="modalDetailsWrap">
            <div class="modal-section-label">Record Details</div>
            <dl class="detail-list mb-3" id="modalDetails"></dl>
          </div>

          <div id="modalCfaBox" class="cfa-box mb-3 d-none">
            <i class="ti ti-alert-triangle"></i> Certificate to File Action issued: <strong id="modalCfaNo"></strong>.
            This case may now be filed in court or with the appropriate government office.
            <div class="mt-2"><button class="btn btn-sm btn-outline-danger" id="printCfaBtn"><i class="ti ti-printer"></i> Print CFA</button></div>
          </div>

          <div id="modalSettlementBox" class="settlement-box mb-3 d-none">
            <div class="modal-section-label" style="margin-top:0; color:#1E7A34;">Settlement / Resolution</div>
            <span id="modalSettlementText"></span>
          </div>

          <div id="modalNoteBox" class="note-box mb-3 d-none">
            <span id="modalNoteText"></span>
            <div class="mt-2 d-none" id="reopenWrap"><button class="btn btn-sm btn-outline-secondary" id="reopenBtn">Open for mediation</button></div>
          </div>

          <div id="mediationPanel">

            <!-- Issue Summons: only for a newly filed case -->
            <div id="summonsRow" class="d-none">
              <div class="modal-section-label">Issue Summons</div>
              <div class="row g-2">
                <div class="col-md-4">
                  <label class="form-label small-label">Hearing date</label>
                  <input type="date" class="form-control form-control-sm" id="sumDate">
                </div>
                <div class="col-md-3">
                  <label class="form-label small-label">Time</label>
                  <input type="time" class="form-control form-control-sm" id="sumTime" value="09:00" min="08:00" max="17:00">
                </div>
                <div class="col-md-5">
                  <label class="form-label small-label">Place</label>
                  <input type="text" class="form-control form-control-sm" id="sumPlace" maxlength="255">
                </div>
              </div>
              <div class="hint mt-2" id="sumEmails"></div>
              <div class="d-flex align-items-center gap-3 flex-wrap mt-2">
                <button class="btn btn-sm" style="background: var(--ref-blue); color: #fff;" id="summonsBtn"><i class="ti ti-send"></i> Issue Summons</button>
                <span class="hint">Hearings are set on weekdays, 8:00 AM to 5:00 PM. The respondent is summoned by the next working day after filing. If mediation fails within 15 days of the first meeting, a Pangkat ng Tagapagkasundo is formed.</span>
              </div>
            </div>

            <!-- After the summons: details, print, Officer's Return -->
            <div id="summonsInfo" class="d-none">
              <div class="modal-section-label">Summons</div>
              <div class="summons-box">
                <div id="summonsInfoText"></div>
                <div class="mt-2"><a class="btn btn-sm btn-outline-primary" id="printSummonsBtn" target="_blank"><i class="ti ti-printer"></i> Print Summons</a></div>
              </div>

              <div class="modal-section-label">Officer's Return <span style="font-weight:400; text-transform:none;">(copy from the signed return)</span></div>
              <div class="hint mb-2" id="returnSaved"></div>
              <div class="row g-2 align-items-end">
                <div class="col-md-4">
                  <label class="form-label small-label">Served by</label>
                  <input type="text" class="form-control form-control-sm" id="retBy" maxlength="120" placeholder="Name of the officer">
                </div>
                <div class="col-md-4">
                  <label class="form-label small-label">Date &amp; time served</label>
                  <input type="datetime-local" class="form-control form-control-sm" id="retAt">
                </div>
                <div class="col-md-4">
                  <label class="form-label small-label">How</label>
                  <select class="form-select form-select-sm" id="retMethod">
                    <option value="" selected disabled>Select</option>
                    @foreach ($methods as $key => $label)
                      <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <button class="btn btn-sm btn-outline-secondary mt-2" id="returnBtn">Save Return</button>
            </div>

            <div id="hearingRow">
              <div class="modal-section-label">Next Hearing</div>
              <div class="row g-2 align-items-end">
                <div class="col-md-5">
                  <label class="form-label small-label">Hearing date &amp; time</label>
                  <input type="datetime-local" class="form-control form-control-sm" id="hearingDateInput">
                </div>
                <div class="col-md-5">
                  <label class="form-label small-label">Mediator</label>
                  {{-- FR-26: Punong Barangay + Lupong Tagapamayapa members (Officials page). --}}
                  <select class="form-select form-select-sm" id="mediatorSelect">
                    <option value="" selected disabled>Select a Lupon member</option>
                    @foreach ($mediators as $m)
                      <option>{{ $m }}</option>
                    @endforeach
                  </select>
                  @if ($mediators->count() < 2)
                    <div class="hint mt-1">Add Lupon members on the <a href="{{ url('/admin/officials') }}">Officials</a> page.</div>
                  @endif
                </div>
                <div class="col-md-2">
                  <button class="btn btn-sm w-100" style="background: var(--ref-blue); color: #fff;" id="scheduleBtn">Schedule</button>
                </div>
              </div>
              <div id="currentHearingNote" class="mt-2" style="font-size: 13px; color: var(--ref-gray-text);"></div>
            </div>

            <div class="modal-section-label">Resolve Case</div>
            <textarea class="form-control mb-2" rows="2" id="settlementNoteInput" placeholder="Terms of the Kasunduang Pag-aayos (amicable settlement)..."></textarea>
            <div class="d-flex gap-2 flex-wrap">
              <button class="btn btn-sm btn-outline-success" id="settleBtn"><i class="ti ti-check"></i> Mark Settled</button>
              <button class="btn btn-sm btn-outline-danger" id="escalateBtn"><i class="ti ti-gavel"></i> Escalate &amp; Issue CFA</button>
              <button class="btn btn-sm btn-outline-secondary" id="dismissBtn"><i class="ti ti-x"></i> Dismiss Case</button>
            </div>
          </div>

        </div>
        <div class="modal-footer">
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
    const $ = id => document.getElementById(id);
    const STATUS_LABEL = @json($statusLabels);
    const DEFAULT_PLACE = @json(config('pabarang.hearing_place'));
    // Staff accounts can view cases but not change them (the server checks this too).
    const CAN_EDIT = {{ in_array($me['role'] ?? '', ['captain', 'secretary'], true) ? 'true' : 'false' }};
    const BASE = "{{ url('/admin/blotters') }}";
    let activeCard = null;
    let busy = false;

    function toast(msg) {
      $('toastMsg').textContent = msg;
      bootstrap.Toast.getOrCreateInstance($('appToast')).show();
    }
    function today() {
      const d = new Date();
      return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    // Stats
    function refreshStats() {
      const c = { all: 0, filed: 0, summons: 0, hearing: 0, settled: 0, escalated: 0, other: 0 };
      document.querySelectorAll('.blotter-card').forEach(card => {
        const st = card.dataset.status;
        c.all++;
        if (st === 'recorded' || st === 'referred') c.other++;
        else if (st in c) c[st]++;
      });
      $('statTotal').textContent = c.all;
      $('statAwaiting').textContent = c.filed + c.summons;
      $('statHearing').textContent = c.hearing;
      $('statSettled').textContent = c.settled;
      $('statEscalated').textContent = c.escalated;
      $('statOther').textContent = c.other;
    }
    refreshStats();

    // Filters
    function applyFilters() {
      const q = $('searchInput').value.trim().toLowerCase();
      const status = $('filterStatus').value;
      const type = $('filterType').value;
      let visible = 0;

      document.querySelectorAll('.blotter-card').forEach(card => {
        const matches = (status === 'all' || card.dataset.status === status)
          && (type === 'all' || card.dataset.type === type)
          && (!q || card.dataset.search.includes(q));
        card.classList.toggle('d-none', !matches);
        if (matches) visible++;
      });
      $('emptyState').classList.toggle('d-none', visible > 0);
    }
    $('searchInput').addEventListener('input', applyFilters);
    $('filterStatus').addEventListener('change', applyFilters);
    $('filterType').addEventListener('change', applyFilters);

    // Mediation controls only show while a case is open. A filed case gets Issue Summons;
    // after the summons it gets the summons details, the Officer's Return and Next Hearing.
    function syncPanels(status) {
      $('mediationPanel').classList.toggle('d-none', !CAN_EDIT || !['filed', 'summons', 'hearing'].includes(status));
      $('summonsRow').classList.toggle('d-none', status !== 'filed');
      $('summonsInfo').classList.toggle('d-none', !['summons', 'hearing'].includes(status));
      $('hearingRow').classList.toggle('d-none', status === 'filed');
    }

    function fillSummons(d) {
      // Issue Summons form (filed cases)
      $('sumDate').value = '';
      $('sumDate').min = today();
      $('sumTime').value = '09:00';
      $('sumPlace').value = DEFAULT_PLACE;
      const unknown = d.respUnknown === '1';
      $('summonsBtn').disabled = unknown;
      const to = [];
      if (d.respEmail) to.push(`the respondent (${d.respEmail})`);
      if (d.compEmail) to.push(`the complainant (${d.compEmail})`);
      $('sumEmails').textContent = unknown
        ? 'The respondent is unknown, so a summons cannot be issued yet.'
        : to.length
          ? 'Email copies go to ' + to.join(' and ') + '. Print the summons and serve it in person too.'
          : 'No email on file. Print the summons and serve it in person.';

      // Summons details (after it was issued)
      let info = d.summonsSent ? `Summons issued ${d.summonsSent}.` : 'Summons marked as issued.';
      if (d.hearing) info += ` Hearing: ${d.hearing} at ${d.hearingPlace || DEFAULT_PLACE}.`;
      $('summonsInfoText').textContent = info;
      const print = $('printSummonsBtn');
      print.href = BASE + '/' + encodeURIComponent(d.id) + '/summons';
      print.classList.toggle('disabled', !d.hearing);

      // Officer's Return
      $('retBy').value = d.servedBy || '';
      $('retAt').value = d.servedAtInput || '';
      $('retMethod').value = d.serviceMethod || '';
      $('returnSaved').textContent = d.servedBy
        ? `Saved: served by ${d.servedBy} on ${d.servedAt}. ${d.serviceLabel}.`
        : 'Not recorded yet. After the officer serves the summons and writes the return on the printed copy, type the same details here.';
    }

    $('blotterModal').addEventListener('show.bs.modal', function (event) {
      activeCard = event.relatedTarget;
      const d = activeCard.dataset;

      $('modalTitle').textContent = d.ref;
      $('modalMeta').textContent = `${d.typeVal} · ${d.zone} · Filed ${d.filed}`;
      $('modalComplainant').textContent = d.complainant;
      $('modalComplainantInfo').textContent = d.compInfo || '';
      $('modalComplainantEmail').textContent = d.compEmail || '';
      $('modalRespondent').textContent = d.respondent;
      $('modalRespondentInfo').textContent = d.respInfo || '';
      $('modalRespondentEmail').textContent = d.respEmail || '';
      $('modalNarrative').textContent = d.narrative;

      const badge = $('modalStatusBadge');
      badge.textContent = STATUS_LABEL[d.statusVal];
      badge.className = 'status-pill st-' + d.statusVal;

      const rows = [['Incident', d.incident], ['Place', d.place], ['Witnesses', d.witnesses], ['Recorded by', d.recorded], ['Source concern', d.source]].filter(r => r[1]);
      const dl = $('modalDetails');
      dl.replaceChildren();
      rows.forEach(([k, v]) => {
        const dt = document.createElement('dt'); dt.textContent = k;
        const dd = document.createElement('dd'); dd.textContent = v;
        dl.append(dt, dd);
      });
      $('modalDetailsWrap').classList.toggle('d-none', rows.length === 0);

      $('hearingDateInput').value = '';
      // A mediator saved before the list came from Officials may not be in it; keep it selectable.
      const sel = $('mediatorSelect');
      if (d.mediator && ![...sel.options].some(o => o.value === d.mediator)) sel.add(new Option(d.mediator, d.mediator));
      sel.value = d.mediator || '';
      $('currentHearingNote').textContent = d.hearing ? `Currently scheduled: ${d.hearing}${d.mediator ? ' · Mediator: ' + d.mediator : ''}` : 'No hearing scheduled yet.';
      $('settlementNoteInput').value = '';
      fillSummons(d);
      syncPanels(d.statusVal);

      const noteBox = $('modalNoteBox');
      if (d.statusVal === 'referred') {
        $('modalNoteText').textContent = `Referred to ${d.referred || 'another office'}. The barangay's part ends at the referral; any further action is handled by that office.`;
        $('reopenWrap').classList.add('d-none');
        noteBox.classList.remove('d-none');
      } else if (d.statusVal === 'recorded') {
        $('modalNoteText').textContent = 'Recorded for reference only. No mediation was requested. If the complainant later asks for mediation, it can be opened from here.';
        $('reopenWrap').classList.toggle('d-none', !CAN_EDIT);
        noteBox.classList.remove('d-none');
      } else {
        noteBox.classList.add('d-none');
      }

      const cfaBox = $('modalCfaBox');
      if (d.statusVal === 'escalated' && d.cfa) {
        cfaBox.classList.remove('d-none');
        $('modalCfaNo').textContent = d.cfa;
      } else {
        cfaBox.classList.add('d-none');
      }

      const settleBox = $('modalSettlementBox');
      if ((d.statusVal === 'settled' || d.statusVal === 'dismissed') && d.settlement) {
        settleBox.classList.remove('d-none');
        $('modalSettlementText').textContent = d.settlement;
      } else {
        settleBox.classList.add('d-none');
      }
    });

    // Copies the saved case (as returned by the server) onto the open card.
    function updateCard(b) {
      if (!activeCard) return;
      const d = activeCard.dataset;
      d.status = b.status; d.statusVal = b.status;
      d.hearing = b.hearing_date || ''; d.hearingPlace = b.hearing_place || '';
      d.mediator = b.mediator || ''; d.summonsSent = b.summons_sent || '';
      d.servedBy = b.served_by || ''; d.servedAt = b.served_at || '';
      d.servedAtInput = b.served_at_input || ''; d.serviceMethod = b.service_method || '';
      d.serviceLabel = b.service_label || '';
      d.settlement = b.settlement || ''; d.cfa = b.cfa_no || '';
      activeCard.classList.remove('border-escalated', 'border-active');
      if (b.status === 'escalated') activeCard.classList.add('border-escalated');
      if (['summons', 'hearing'].includes(b.status)) activeCard.classList.add('border-active');
      const pill = activeCard.querySelector('.status-pill');
      pill.className = 'status-pill st-' + b.status;
      pill.textContent = STATUS_LABEL[b.status];
      refreshStats();
      applyFilters();
    }
    function closeModal() { bootstrap.Modal.getInstance($('blotterModal')).hide(); }

    // Sends one action to the server. Only changes the screen if the server accepted it.
    // okMsg can be text or a function that builds the text from the server reply.
    async function act(action, extra, okMsg) {
      if (!activeCard || busy) return;
      busy = true;
      const ref = activeCard.dataset.ref;
      try {
        const res = await fetch(BASE + '/' + encodeURIComponent(activeCard.dataset.id), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ action, ...extra })
        });
        if (res.status === 401) { toast('Your session expired. Please log in again.'); return; }
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
          toast(data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Could not save.'));
          return;
        }
        updateCard(data.blotter);
        closeModal();
        toast(ref + ' ' + (typeof okMsg === 'function' ? okMsg(data) : okMsg));
      } catch (e) {
        toast('Could not reach the server. Check your connection and try again.');
      } finally {
        busy = false;
      }
    }

    $('summonsBtn').addEventListener('click', () => {
      const date = $('sumDate').value, time = $('sumTime').value, place = $('sumPlace').value.trim();
      if (!date || !time || !place) { toast('Fill in the hearing date, time and place first'); return; }
      if (!confirm('Issue the summons? Email copies are sent right away.')) return;
      act('summons', { hearing_at: date + ' ' + time, hearing_place: place }, data =>
        data.sent && data.sent.length
          ? 'summons issued, emailed to the ' + data.sent.join(' and the ') + '.'
          : 'summons issued. No email on file, print it from the case.');
    });

    $('returnBtn').addEventListener('click', () => {
      const by = $('retBy').value.trim(), at = $('retAt').value, method = $('retMethod').value;
      if (!by || !at || !method) { toast("Fill in who served it, when, and how"); return; }
      act('return', { served_by: by, served_at: at.replace('T', ' '), service_method: method }, "officer's return saved");
    });

    $('scheduleBtn').addEventListener('click', () => {
      const dt = $('hearingDateInput').value;
      const mediator = $('mediatorSelect').value;
      if (!dt || !mediator) { toast('Pick a date/time and a mediator first'); return; }
      act('schedule', { hearing_at: dt, mediator }, 'hearing scheduled');
    });

    $('settleBtn').addEventListener('click', () => {
      const note = $('settlementNoteInput').value.trim();
      if (!note) { toast('Add the settlement terms first'); return; }
      act('settle', { settlement: note }, 'marked settled');
    });

    $('escalateBtn').addEventListener('click', () => {
      if (!confirm('Escalate this case and issue a Certificate to File Action?')) return;
      act('escalate', {}, 'escalated, CFA issued');
    });

    $('dismissBtn').addEventListener('click', () => {
      act('dismiss', { settlement: $('settlementNoteInput').value.trim() || null }, 'dismissed');
    });

    $('reopenBtn').addEventListener('click', () => act('reopen', {}, 'opened for mediation'));

    $('printCfaBtn').addEventListener('click', () => toast('CFA printing comes in a later step'));
  </script>

  <link href="{{ asset('css/pabarang.css') }}?v=5" rel="stylesheet">
  <link href="{{ asset('css/pabarang-admin.css') }}?v=5" rel="stylesheet">
  {{-- Keep this after the two links above --}}
  <link href="{{ asset('css/admin-static.css') }}?v=5" rel="stylesheet">
</body>
</html>
