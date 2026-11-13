<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
 <script src="{{ asset('js/pabarang.js') }}"></script>

  <style>
    :root {
      --ref-blue: #4F5FE0;
      --ref-navy-text: #1E2340;
      --ref-bg: #F5F6FA;
      --ref-border: #E4E6EF;
      --ref-gray-text: #6B7280;
      --navy: #181a35;
      --navy-active: #5b52f5;
    }
    body { font-family: 'Lato', sans-serif; color: var(--ref-navy-text); background: var(--ref-bg); }

    /* ===== Sidebar (shared across all resident pages) ===== */

    .main { margin-left: 250px; padding: 28px 32px; }
    .avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--navy-active); color: #fff;
      display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; cursor: pointer; }

    /* ===== Dashboard cards ===== */
    .identity-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 16px; padding: 32px; height: 100%; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
    .actions-panel { background: #fff; border: 1px solid var(--ref-border); border-radius: 16px; padding: 28px; height: 100%; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
    .seal-badge-sm { width: 78px; height: 78px; border-radius: 50%; background: var(--ref-bg); border: 1px solid var(--ref-border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .seal-badge-sm i { font-size: 34px; color: var(--ref-navy-text); }
    .stat-tile-light { background: #EEF0FD; border-radius: 12px; padding: 18px; text-align: center; }
    .stat-tile-light .stat-number { font-size: 28px; font-weight: 800; color: var(--ref-navy-text); }
    .stat-tile-light .stat-label { font-size: 12px; color: var(--ref-gray-text); margin-top: 4px; }
    .action-tile { border: 1px solid var(--ref-border); border-radius: 12px; padding: 16px; margin-bottom: 12px; display: flex; align-items: center; gap: 14px; text-decoration: none; color: inherit; }
    .action-tile:last-child { margin-bottom: 0; }
    .action-icon-circle { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .action-icon-circle.urgent { background: #FDE8E8; }
    .action-icon-circle.neutral { background: #E7EAFD; }
    .action-tile .tile-title { font-size: 15px; font-weight: 700; color: var(--ref-navy-text); }
    .action-tile .tile-desc { font-size: 12px; color: var(--ref-gray-text); }
    .tile-arrow { margin-left: auto; color: var(--ref-gray-text); }

    /* ===== Section cards (My Requests / Announcements) ===== */
    .section-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 16px; padding: 28px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); margin-top: 24px; }
    .section-card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
    .section-card-head h2 { font-size: 18px; font-weight: 800; margin: 0; }
    .section-card-head a { font-size: 13px; color: var(--ref-blue); font-weight: 600; text-decoration: none; }

    .panel-box { border: 1px solid var(--ref-border); border-radius: 10px; padding: 16px 18px; margin-bottom: 12px; background: #fff; }
    .panel-box:last-child { margin-bottom: 0; }
    .status-pill { font-size: 12px; padding: 3px 10px; border-radius: 10px; font-weight: 600; }
    .status-processing { background: #FDF1D8; color: #92650C; }
    .status-review { background: #E1EEFB; color: #0C5FA1; }

    .news-card { border-radius: 10px; overflow: hidden; border: 1px solid var(--ref-border); }
    .news-card img { width: 100%; height: 150px; object-fit: cover; }
    .news-tag { font-size: 12px; font-weight: 700; color: var(--ref-blue); letter-spacing: 0.04em; padding: 12px 14px 14px; display: block; }

    /* ===== Newest-announcement modal ===== */
    .announce-modal-img { width: 100%; height: 220px; object-fit: cover; border-radius: 10px; margin-bottom: 16px; }
    .announce-modal-tag { font-size: 12px; font-weight: 700; color: var(--ref-blue); letter-spacing: 0.04em; }
  </style>
</head>
<body>

  <!-- ===================== SIDEBAR ===================== -->
  @include('partials.resident-sidebar')

  <!-- ===================== MAIN ===================== -->
  <div class="main">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-0 fs-4 font-weight-bold">Welcome back, {{ session('resident.first_name', 'Resident') }}</h1>
      </div>
    </div>

    <!-- ===== DASHBOARD + QUICK ACTIONS (priority row) ===== -->
    {{--
      BACKEND TODO (final term): the 4 numbers below are hardcoded for the GUI
      stage. A controller will later fetch these per-resident from the
      `concerns` table and pass them into this view.
    --}}
    <div class="row g-4">
      <div class="col-lg-7">
        <div class="identity-card d-flex flex-column justify-content-between">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="seal-badge-sm" style="overflow:hidden;">
              <img src="{{ asset('images/logo.jpg') }}" alt="Barangay Alejal Logo"
                   style="width:100%;height:100%;object-fit:cover;border-radius:50%;"
                   onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <i class="ti ti-shield-check" style="display:none;"></i>
            </div>
            <div>
              <div style="font-size:12px;color:var(--ref-gray-text);">Republic of the Philippines</div>
              <div style="font-size:24px;font-weight:800;color:var(--ref-navy-text);">BARANGAY ALEJAL</div>
            </div>
          </div>
          <div class="row g-3">
            <div class="col-6 col-md-3"><div class="stat-tile-light"><div class="stat-number">{{ $stats['total'] ?? 0 }}</div><div class="stat-label">Total concerns</div></div></div>
            <div class="col-6 col-md-3"><div class="stat-tile-light"><div class="stat-number">{{ $stats['pending'] ?? 0 }}</div><div class="stat-label">Pending</div></div></div>
            <div class="col-6 col-md-3"><div class="stat-tile-light"><div class="stat-number">{{ $stats['progress'] ?? 0 }}</div><div class="stat-label">In progress</div></div></div>
            <div class="col-6 col-md-3"><div class="stat-tile-light"><div class="stat-number">{{ $stats['resolved'] ?? 0 }}</div><div class="stat-label">Resolved</div></div></div>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="actions-panel d-flex flex-column">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span style="font-size:16px;font-weight:700;color:var(--ref-navy-text);">Quick Actions</span>
          </div>
          {{-- FR-08/FR-12: a logged-in resident's concern should link to their
               account so it shows up on My Reports & Status - the guest form
               on the landing page can't do that, so this now points at the
               dedicated resident.submit-concern page instead. --}}
          <a href="{{ url('/submit-concern') }}" class="action-tile">
            <div class="action-icon-circle urgent"><i class="ti ti-alert-triangle" style="color:#D64545;"></i></div>
            <div><div class="tile-title">Submit a Concern</div><div class="tile-desc">Report an incident or issue</div></div>
            <i class="ti ti-chevron-right tile-arrow"></i>
          </a>
          <a href="{{ url('/request-document') }}" class="action-tile">
            <div class="action-icon-circle neutral"><i class="ti ti-file-text" style="color:var(--ref-navy-text);"></i></div>
            <div><div class="tile-title">Request a Document</div><div class="tile-desc">Clearance, residency, and more</div></div>
            <i class="ti ti-chevron-right tile-arrow"></i>
          </a>
          <a href="{{ url('/blotter-report') }}" class="action-tile">
            <div class="action-icon-circle neutral"><i class="ti ti-clipboard-text" style="color:var(--ref-navy-text);"></i></div>
            <div><div class="tile-title">File a Blotter Report</div><div class="tile-desc">Formal record of a dispute</div></div>
            <i class="ti ti-chevron-right tile-arrow"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- ===== MY REQUESTS ===== -->
    {{-- BACKEND TODO (final term): fetch this resident's own requests via auth()->id(), @foreach instead. --}}
    <div class="section-card">
      <div class="section-card-head">
        <h2>My Requests</h2>
        <a href="{{ url('/my-reports') }}">View all</a>
      </div>
      <div class="panel-box">
        <div class="d-flex justify-content-between align-items-center">
          <span style="font-weight:700;">Barangay Clearance</span>
          <span class="status-pill status-processing">Processing</span>
        </div>
        <div style="font-size:12px;color:var(--ref-gray-text);margin-top:4px;">Requested Sep 9</div>
      </div>
      <div class="panel-box">
        <div class="d-flex justify-content-between align-items-center">
          <span style="font-weight:700;">Blotter Report - fence dispute</span>
          <span class="status-pill status-review">Under review</span>
        </div>
        <div style="font-size:12px;color:var(--ref-gray-text);margin-top:4px;">Filed Sep 8</div>
      </div>
    </div>

    <!-- ===== RECENT NEWS & ANNOUNCEMENTS ===== -->
    {{-- BACKEND TODO (final term): fetch Announcement::latest()->take(3)->get() and @foreach instead. --}}
    <div class="section-card">
      <div class="section-card-head">
        <h2>Recent News &amp; Announcements</h2>
        <a href="{{ url('/announcements') }}">View all</a>
      </div>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="news-card">
            <img src="{{ asset('images/community1.jpg') }}" alt="Barangay Alejal community"
                 onerror="this.onerror=null;this.src='https://placehold.co/400x220/E4E6EF/1E2340?text=Community'">
            <span class="news-tag">HEALTH</span>
          </div>
        </div>
        <div class="col-md-4">
          <div class="news-card">
            <img src="{{ asset('images/' . rawurlencode('water supply.jpg')) }}" alt="Water interruption notice"
                 onerror="this.onerror=null;this.src='https://placehold.co/400x220/E4E6EF/1E2340?text=Water+Notice'">
            <span class="news-tag">ADVISORY</span>
          </div>
        </div>
        <div class="col-md-4">
          <div class="news-card">
            <img src="{{ asset('images/assembly.jpg') }}" alt="Barangay assembly"
                 onerror="this.onerror=null;this.src='https://placehold.co/400x220/E4E6EF/1E2340?text=Assembly'">
            <span class="news-tag">EVENTS</span>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- ===================== NEWEST ANNOUNCEMENT MODAL (auto-opens on load) ===================== -->
  {{--
    Filled in by the script at the bottom from whatever the admin saved on
    Announcements > Resident Popup (localStorage key 'pb_popup_announcement').
    BACKEND TODO (final term): read the popup from the database instead, and
    remember dismissals per resident rather than per browser tab.
  --}}
  <div class="modal fade" id="latestAnnouncementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <span class="announce-modal-tag" id="popTag">NEWEST ANNOUNCEMENT</span>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-2">
          <img id="popImg" class="announce-modal-img d-none" alt="">
          <h5 class="fw-bold mb-2" id="popTitle"></h5>
          <p id="popBody" style="color:var(--ref-gray-text); font-size:14px; white-space:pre-line;"></p>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
          <a href="{{ url('/announcements') }}" class="btn" style="background:var(--ref-blue); color:#fff;">View all announcements</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // The admin edits this on Announcements > Resident Popup; both pages must
    // use the same key and the same default.
    var POPUP_KEY = 'pb_popup_announcement';
    var POPUP_LABELS = { health: 'HEALTH', advisory: 'ADVISORY', utility: 'UTILITY NOTICE', event: 'EVENTS', assistance: 'ASSISTANCE PROGRAMS' };
    var POPUP_DEFAULT = {
      id: 0, enabled: true, category: 'event',
      title: 'Barangay Assembly, September 28',
      body: 'All registered residents are invited to the Barangay Assembly at 2:00 PM at the Barangay Hall covered court, where the annual budget and community programs will be presented.',
      image: '', updatedAt: null
    };
    var ANNOUNCE_SEEN_KEY = 'pabarang_announcement_seen';

    function loadPopup() {
      try {
        var raw = localStorage.getItem(POPUP_KEY);
        return raw ? Object.assign({}, POPUP_DEFAULT, JSON.parse(raw)) : Object.assign({}, POPUP_DEFAULT);
      } catch (e) { return Object.assign({}, POPUP_DEFAULT); }
    }

    document.addEventListener('DOMContentLoaded', function () {
      // Log Out should reset the flag so the popup shows again on the next login.
      document.querySelectorAll('a[href$="/logout"]').forEach(function (link) {
        link.addEventListener('click', function () {
          try { sessionStorage.removeItem(ANNOUNCE_SEEN_KEY); } catch (e) {}
        });
      });

      var popup = loadPopup();
      if (!popup.enabled) return;                       // admin switched the popup off

      // One popup per session, but a re-saved/edited popup counts as new.
      var version = String(popup.id) + ':' + String(popup.updatedAt);
      try { if (sessionStorage.getItem(ANNOUNCE_SEEN_KEY) === version) return; } catch (e) {}

      document.getElementById('popTag').textContent = (POPUP_LABELS[popup.category] || '') + ' | NEWEST ANNOUNCEMENT';
      document.getElementById('popTitle').textContent = popup.title;
      document.getElementById('popBody').textContent = popup.body;
      if (popup.image) {
        var img = document.getElementById('popImg');
        img.src = popup.image; img.alt = popup.title; img.classList.remove('d-none');
      }

      new bootstrap.Modal(document.getElementById('latestAnnouncementModal')).show();
      try { sessionStorage.setItem(ANNOUNCE_SEEN_KEY, version); } catch (e) {}
    });
  </script>
</body>
</html>