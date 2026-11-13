@php
  /*
   | Data: Resident\PortalController@myReports
   |   $myDocs     - this resident's document requests with their status log
   |                 (document_request_logs) and the admin's "Note to resident".
   |   $myConcerns - this resident's concerns (concerns.resident_id).
   | Only the logged-in resident's own rows are ever passed to this view.
   */
  $docTypes = config('pabarang.doc_types');
  // Concerns: icon per category, colors per status bucket.
  $concernIcon = ['Crime/Safety' => 'ti-alert-triangle', 'Community Safety' => 'ti-alert-triangle', 'Environment' => 'ti-trash',
                  'Infrastructure' => 'ti-building', 'Streetlights' => 'ti-bulb', 'Noise' => 'ti-volume', 'Water Supply' => 'ti-droplet',
                  'Neighbor Dispute' => 'ti-users', 'Other' => 'ti-clipboard-text'];
  $concernColor = ['pending' => ['#FDF1D8', '#92650C'], 'progress' => ['#E7EAFD', '#4F5FE0'], 'resolved' => ['#E1F5E4', '#1E7A34'], 'rejected' => ['#FDE8E8', '#D64545']];
  $concernPill  = ['pending' => 'status-pending', 'review' => 'status-review', 'progress' => 'status-progress', 'resolved' => 'status-resolved', 'rejected' => 'status-rejected'];
  $concernDefaultMsg = [
    'pending'  => 'Thank you for reporting. Your concern is in the queue for review. We will update this report once an official has checked it.',
    'progress' => 'An official is working on your concern. This page will show any update.',
    'resolved' => 'This concern is resolved. Please file a new concern if the problem happens again.',
    'rejected' => 'This concern was closed by the barangay. Please file a new concern if you need further help.',
  ];
  $docSteps = [
    'doc'  => ['pending' => 'Request received', 'processing' => 'Being processed', 'ready' => 'Ready for pickup', 'completed' => 'Claimed by resident', 'rejected' => 'Rejected'],
    'vacc' => ['pending' => 'Request received', 'processing' => 'Being reviewed',   'ready' => 'Schedule confirmed', 'completed' => 'Vaccinated',         'rejected' => 'Rejected'],
  ];
  $docPillText  = ['pending' => 'Pending', 'processing' => 'Processing', 'ready' => 'Ready for pickup', 'completed' => 'Completed', 'rejected' => 'Rejected'];
  $docPillClass = ['pending' => 'status-pending', 'processing' => 'status-progress', 'ready' => 'status-ready', 'completed' => 'status-resolved', 'rejected' => 'status-rejected'];
  $docFilter    = ['pending' => 'pending', 'processing' => 'progress', 'ready' => 'progress', 'completed' => 'resolved', 'rejected' => 'rejected'];
  $docIconColor = [
    'pending' => ['#FDF1D8', '#92650C'], 'processing' => ['#E7EAFD', '#4F5FE0'], 'ready' => ['#DBF3F0', '#0B6B63'],
    'completed' => ['#E1F5E4', '#1E7A34'], 'rejected' => ['#FDE8E8', '#D64545'],
  ];
  // Shown when the admin has not written a note yet.
  $docDefaultMsg = [
    'pending'    => 'Thank you for your request. It is in the queue, and an official will check it and your valid ID soon.',
    'processing' => 'An official is working on your request. This page will show when it is ready.',
    'ready'      => 'Your document is ready for pickup at the barangay hall, weekdays 8 AM to 5 PM.',
    'ready_vacc' => 'Your vaccination schedule is confirmed.',
    'completed'  => 'This request is complete. Thank you.',
    'rejected'   => 'Your request was not approved. Please send a new request.',
  ];
  @endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Reports &amp; Status - Tugon</title>
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

    .page-wrap { max-width: 860px; margin: 0 auto; padding: 40px 20px; }
    .filter-pill {
      border: 1px solid var(--ref-border); background: #fff; border-radius: 20px;
      padding: 6px 16px; font-size: 13px; color: var(--ref-gray-text); cursor: pointer;
    }
    .filter-pill.active { background: var(--ref-blue); border-color: var(--ref-blue); color: #fff; }

    .report-card {
      background: #fff; border: 1px solid var(--ref-border); border-radius: 12px;
      padding: 18px 20px; margin-bottom: 14px; cursor: pointer;
    }
    .report-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .report-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .report-title { font-size: 15px; font-weight: 700; }
    .report-meta { font-size: 12px; color: var(--ref-gray-text); }

    .status-pill { font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 10px; white-space: nowrap; }
    .status-pending { background: #FDF1D8; color: #92650C; }
    .status-review { background: #E1EEFB; color: #0C5FA1; }
    .status-progress { background: #E7EAFD; color: #5b52f5; }
    .status-resolved { background: #E1F5E4; color: #1E7A34; }
    .status-ready { background: #DBF3F0; color: #0B6B63; }
    .status-rejected { background: #FDE8E8; color: #D64545; }
    .type-pill { font-size: 13px; font-weight: 600; color: var(--ref-gray-text); cursor: pointer; padding: 4px 2px; border: 0; background: none; border-bottom: 2px solid transparent; }
    .type-pill.active { color: var(--ref-blue); border-bottom-color: var(--ref-blue); }

    /* Timeline inside the modal */
    .timeline-item { position: relative; padding-left: 28px; padding-bottom: 20px; }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-item::before {
      content: ''; position: absolute; left: 6px; top: 4px; width: 10px; height: 10px;
      border-radius: 50%; background: var(--ref-blue);
    }
    .timeline-item::after {
      content: ''; position: absolute; left: 10px; top: 14px; bottom: 0; width: 2px; background: var(--ref-border);
    }
    .timeline-item:last-child::after { display: none; }
    .timeline-label { font-size: 13.5px; font-weight: 600; }
    .timeline-date { font-size: 12px; color: var(--ref-gray-text); }
    .timeline-item.is-rejected::before { background: #D64545; }

    /* Message from the barangay admin, shown inside each report modal */
    .admin-message { margin-top: 18px; background: #F1F3FE; border: 1px solid #D5D9F7; border-left: 4px solid var(--ref-blue); border-radius: 10px; padding: 12px 14px; }
    .admin-message-head { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
    .admin-message-icon { width: 30px; height: 30px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; }
    .admin-message-from { font-size: 13px; font-weight: 700; }
    .admin-message-date { font-size: 11.5px; color: var(--ref-gray-text); }
    .admin-message-text { font-size: 13.5px; margin: 0; }
    .admin-message.empty { background: #fff; border-color: var(--ref-border); border-left-color: var(--ref-border); }
    .admin-message.empty .admin-message-text { color: var(--ref-gray-text); }
  </style>
</head>
<body>

  @include('partials.resident-sidebar')

  <div class="main">
    <div class="top-bar">
      <h5 class="fw-bold mb-0">My Reports &amp; Status</h5>
    </div>


  <!-- Nav -->

  <div class="page-wrap">
    <h3 class="fw-bold mb-1">My Reports &amp; Status</h3>
    <p style="color: var(--ref-gray-text); font-size: 14px;" class="mb-1">
      Only you and barangay officials can see these. No other resident can view your reports.
    </p>

    @php $filedCode = session('filed') ?? request('filed'); @endphp
    @if (session('filed_doc'))
      <div class="alert alert-success d-flex align-items-center gap-2 mb-4" style="font-size:14px;" role="alert">
        <i class="ti ti-circle-check fs-5"></i>
        <span>{{ session('filed_doc.name') }} requested. Your reference is <strong class="font-monospace">{{ session('filed_doc.ref') }}</strong>. It's listed below as Pending.</span>
      </div>
    @elseif ($filedCode)
      <div class="alert alert-success d-flex align-items-center gap-2 mb-4" style="font-size:14px;" role="alert">
        <i class="ti ti-circle-check fs-5"></i>
        <span>Concern submitted. Your tracking code is <strong class="font-monospace">{{ $filedCode }}</strong>. It's listed below as Pending.</span>
      </div>
    @else
      <div class="mb-3"></div>
    @endif

    <div class="d-flex gap-4 mb-3" role="group" aria-label="Filter by type">
      <button type="button" class="type-pill active" data-type-filter="all">All</button>
      <button type="button" class="type-pill" data-type-filter="concern">Concerns</button>
      <button type="button" class="type-pill" data-type-filter="document">Documents</button>
    </div>

    <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
      <div class="d-flex gap-2 flex-wrap">
        <span class="filter-pill active" data-filter="all">All</span>
        <span class="filter-pill" data-filter="pending">Pending</span>
        <span class="filter-pill" data-filter="review">Reviewed</span>
        <span class="filter-pill" data-filter="progress">In Progress</span>
        <span class="filter-pill" data-filter="resolved">Resolved</span>
        <span class="filter-pill" data-filter="rejected">Rejected</span>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ url('/request-document') }}" class="btn btn-sm fw-semibold" style="border: 1px solid var(--ref-blue); color: var(--ref-blue); border-radius: 20px; padding: 7px 16px;">
          <i class="ti ti-file-text me-1"></i>Request a Document
        </a>
        <a href="{{ url('/submit-concern') }}" class="btn btn-sm fw-semibold" style="background: var(--ref-blue); color: #fff; border-radius: 20px; padding: 7px 16px;">
          <i class="ti ti-plus me-1"></i>New Concern
        </a>
      </div>
    </div>

    {{-- Concerns --}}
    @foreach ($myConcerns as $c)
      @php [$cBg, $cFg] = $concernColor[$c['bucket']] ?? $concernColor['progress']; @endphp
      <div class="report-card" data-type="concern" data-status="{{ $c['filter'] }}" data-bs-toggle="modal" data-bs-target="#concernModal{{ $c['id'] }}">
        <div class="d-flex align-items-center gap-3">
          <div class="report-icon" style="background:{{ $cBg }};"><i class="ti {{ $concernIcon[$c['category']] ?? 'ti-clipboard-text' }}" style="color:{{ $cFg }};"></i></div>
          <div class="flex-grow-1">
            <div class="report-title">{{ $c['title'] }}</div>
            <div class="report-meta">{{ collect([$c['zone'], $c['category'], $c['ref'], 'Filed ' . $c['filed']->format('M j')])->filter()->implode(' · ') }}</div>
          </div>
          <span class="status-pill {{ $concernPill[$c['filter']] ?? 'status-progress' }}">{{ $c['label'] }}</span>
        </div>
      </div>
    @endforeach

    {{-- Document requests. The status and the message come from what the admin sets on Document Requests. --}}
    @foreach ($myDocs as $i => $d)
      @php
        $isVacc   = $d['type'] === 'vaccination';
        $docName  = $docTypes[$d['type']]['name'];
        $pill     = ($isVacc && $d['status'] === 'ready') ? 'Scheduled' : $docPillText[$d['status']];
        [$iconBg, $iconFg] = $docIconColor[$d['status']];
        $filedOn  = \Carbon\Carbon::parse($d['log'][0][1])->format('M j');
      @endphp
      <div class="report-card" data-type="document" data-status="{{ $docFilter[$d['status']] }}" data-bs-toggle="modal" data-bs-target="#docModal{{ $i }}">
        <div class="d-flex align-items-center gap-3">
          <div class="report-icon" style="background: {{ $iconBg }};"><i class="ti {{ $docTypes[$d['type']]['icon'] }}" style="color: {{ $iconFg }};"></i></div>
          <div class="flex-grow-1">
            <div class="report-title">{{ $docName }} ({{ $d['summary'] }})</div>
            <div class="report-meta">Document request &middot; {{ $d['ref'] }} &middot; Requested {{ $filedOn }}</div>
          </div>
          <span class="status-pill {{ $docPillClass[$d['status']] }}">{{ $pill }}</span>
        </div>
      </div>
    @endforeach

    {{-- Empty state: shown when there is nothing to list, or a filter matches nothing. --}}
    <div id="emptyState" class="d-none text-center py-5">
      <div class="report-icon mx-auto mb-3" style="background:#E7EAFD;"><i class="ti ti-clipboard-plus" style="color:var(--ref-blue); font-size:20px;"></i></div>
      <div class="fw-bold mb-1">No reports here yet</div>
      <p style="color: var(--ref-gray-text); font-size: 13.5px;" class="mb-3">Concerns and document requests you file will show up here with their status.</p>
      <div class="d-flex gap-2 justify-content-center">
        <a href="{{ url('/submit-concern') }}" class="btn btn-sm fw-semibold" style="background: var(--ref-blue); color: #fff;">File a concern</a>
        <a href="{{ url('/request-document') }}" class="btn btn-sm fw-semibold" style="border: 1px solid var(--ref-blue); color: var(--ref-blue);">Request a document</a>
      </div>
    </div>

  </div>
  </div>

  <!-- Concern modals -->
  @foreach ($myConcerns as $c)
    @php
      $changed = \Carbon\Carbon::parse($c['changed'] ?? $c['filed']);
      $message = $c['note'] ?: ($concernDefaultMsg[$c['bucket']] ?? $concernDefaultMsg['progress']);
    @endphp
    <div class="modal fade" id="concernModal{{ $c['id'] }}" tabindex="-1" aria-labelledby="concernTitle{{ $c['id'] }}">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="concernTitle{{ $c['id'] }}">{{ $c['title'] }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p style="font-size: 13.5px; color: var(--ref-gray-text);">{{ collect([$c['ref'], $c['zone'], $c['category']])->filter()->implode(' · ') }}</p>
            <div class="timeline-item"><div class="timeline-label">Submitted</div><div class="timeline-date">{{ $c['filed']->format('M j, Y') }}</div></div>
            @if ($c['status'] !== 'pending')
              <div class="timeline-item {{ $c['status'] === 'rejected' ? 'is-rejected' : '' }}">
                <div class="timeline-label">{{ $c['label'] }}</div>
                <div class="timeline-date">{{ $changed->format('M j, Y') }}</div>
              </div>
            @endif
            <div class="admin-message {{ $c['note'] ? '' : 'empty' }}">
              <div class="admin-message-head">
                <span class="admin-message-icon" @unless ($c['note']) style="background:#9CA3AF;" @endunless><i class="ti ti-building-community"></i></span>
                <div>
                  <div class="admin-message-from">Message from Barangay Alejal</div>
                  <div class="admin-message-date">{{ $changed->format('M j, Y') }}</div>
                </div>
              </div>
              <p class="admin-message-text">{{ $message }}</p>
            </div>
            <hr>
            @if ($c['bucket'] === 'resolved')
              <p style="font-size: 13px; color: var(--ref-gray-text);" class="mb-0">This report is resolved. If it happens again, please <a href="{{ url('/submit-concern') }}">file a new concern</a>.</p>
            @else
              <p style="font-size: 13px; color: var(--ref-gray-text);" class="mb-0">Need to add details? Visit the barangay hall or call the office and mention <strong>{{ $c['ref'] }}</strong>.</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endforeach

  <!-- Document request modals (same layout as the concern modals) -->
  @foreach ($myDocs as $i => $d)
    @php
      $isVacc   = $d['type'] === 'vaccination';
      $docName  = $docTypes[$d['type']]['name'];
      $steps    = $docSteps[$isVacc ? 'vacc' : 'doc'];
      $lastDate = \Carbon\Carbon::parse(end($d['log'])[1])->format('M j, Y');
      $message  = $d['note'] ?: ($docDefaultMsg[($isVacc && $d['status'] === 'ready') ? 'ready_vacc' : $d['status']]);
    @endphp
    <div class="modal fade" id="docModal{{ $i }}" tabindex="-1" aria-label="{{ $docName }}">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $docName }} ({{ $d['summary'] }})</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p style="font-size: 13.5px; color: var(--ref-gray-text);">{{ $d['ref'] }} &middot; {{ $docName }} &middot; {{ $d['summary'] }}</p>
            @foreach ($d['log'] as $entry)
              <div class="timeline-item {{ $entry[0] === 'rejected' ? 'is-rejected' : '' }}">
                <div class="timeline-label">{{ $steps[$entry[0]] }}</div>
                <div class="timeline-date">{{ \Carbon\Carbon::parse($entry[1])->format('M j, Y') }}</div>
              </div>
            @endforeach
            <div class="admin-message">
              <div class="admin-message-head">
                <span class="admin-message-icon"><i class="ti ti-building-community"></i></span>
                <div>
                  <div class="admin-message-from">Message from Barangay Alejal</div>
                  <div class="admin-message-date">{{ $lastDate }}</div>
                </div>
              </div>
              <p class="admin-message-text">{{ $message }}</p>
            </div>
            <hr>
            @if (in_array($d['status'], ['pending', 'processing', 'ready']))
              <p style="font-size: 13px; color: var(--ref-gray-text);" class="mb-0">Questions about this request? Visit the barangay hall or call the office and mention <strong>{{ $d['ref'] }}</strong>.</p>
            @elseif ($d['status'] === 'rejected')
              <p style="font-size: 13px; color: var(--ref-gray-text);">To try again, send a new request with the correction.</p>
              <a href="{{ url('/request-document') }}" class="btn btn-sm" style="background: var(--ref-blue); color: #fff;">Request again</a>
            @else
              <p style="font-size: 13px; color: var(--ref-gray-text);">This request is completed. No further comments are needed.</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endforeach

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
<script src="{{ asset('js/pabarang.js') }}"></script>
  <script>
    // Two filters work together: type (All / Concerns / Documents) and status.
    // Runs once on load too, so the empty state shows for a brand-new account.
    (function () {
      var pills = document.querySelectorAll('.filter-pill');
      var typePills = document.querySelectorAll('.type-pill');
      var emptyState = document.getElementById('emptyState');
      var status = 'all';
      var type = 'all';

      function applyFilter() {
        var shown = 0;
        document.querySelectorAll('.report-card').forEach(function (card) {
          var match = (status === 'all' || card.getAttribute('data-status') === status) &&
                      (type === 'all' || card.getAttribute('data-type') === type);
          card.classList.toggle('d-none', !match);
          if (match) shown++;
        });
        if (emptyState) emptyState.classList.toggle('d-none', shown > 0);
      }

      pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
          pills.forEach(function (p) { p.classList.remove('active'); });
          this.classList.add('active');
          status = this.getAttribute('data-filter');
          applyFilter();
        });
      });

      typePills.forEach(function (pill) {
        pill.addEventListener('click', function () {
          typePills.forEach(function (p) { p.classList.remove('active'); });
          this.classList.add('active');
          type = this.getAttribute('data-type-filter');
          applyFilter();
        });
      });

      applyFilter();
    })();
  </script>
</body>
</html>