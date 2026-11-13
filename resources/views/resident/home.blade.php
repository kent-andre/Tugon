<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <script src="{{ asset('js/pabarang.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

  <style>
    :root {
      --ref-blue: #4F5FE0; --ref-navy-text: #1E2340; --ref-bg: #F5F6FA;
      --ref-border: #E4E6EF; --ref-gray-text: #6B7280;
    }
    body { font-family: 'Host Grotesk', sans-serif; color: var(--ref-navy-text); background: var(--ref-bg); }
    .main { margin-left: 250px; padding: 28px 32px; }

    /* Bento grid: 3 columns. Overview on top, calendar in the middle,
       requests and news at the bottom. */
    .bento {
      display: grid; gap: 18px;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      grid-template-areas:
        "identity identity actions"
        "calendar calendar upcoming"
        "requests news     news";
    }
    .card-b { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 24px; min-width: 0; }
    .card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
    .card-head h2 { font-size: 17px; font-weight: 800; margin: 0; }
    .card-head a { font-size: 13px; color: var(--ref-blue); font-weight: 600; text-decoration: none; }

    /* Identity + my concern numbers */
    /* Greeting with the seal, then this resident's concern counts */
    .b-identity { grid-area: identity; display: flex; flex-direction: column; justify-content: space-between; gap: 18px; }
    .greet-row { display: flex; align-items: center; gap: 18px; }
    .seal { width: 76px; height: 76px; border-radius: 50%; background: #fff; border: 2px solid #2F9E6B; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
    .seal img { width: 100%; height: 100%; object-fit: cover; }
    .seal i { font-size: 32px; display: none; }
    .greet-date { font-size: 13px; color: var(--ref-gray-text); }
    .greet { font-size: 1.7rem; font-weight: 800; margin: 2px 0 2px; line-height: 1.15; }
    .greet-place { font-size: 14px; color: var(--ref-gray-text); }
    .greet-note { margin: 0; font-size: 14.5px; padding: 12px 14px; border-left: 3px solid #2F9E6B; background: var(--ref-bg); border-radius: 0 8px 8px 0; }
    .greet-note a { color: var(--ref-blue); font-weight: 700; text-decoration: none; }
    .greet-note a:hover { text-decoration: underline; }
    .stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .stat-tile { display: block; background: var(--ref-bg); border: 1px solid transparent; border-radius: 10px; padding: 14px 16px; text-decoration: none; color: inherit; transition: border-color .15s; }
    .stat-tile:hover { border-color: var(--ref-blue); color: inherit; }
    .stat-tile .n { font-size: 28px; font-weight: 800; line-height: 1; }
    .stat-tile .l { font-size: 13px; color: var(--ref-gray-text); margin-top: 6px; display: flex; align-items: center; gap: 6px; }
    .stat-tile .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    @media (max-width: 575.98px) { .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .greet { font-size: 1.4rem; } .seal { width: 60px; height: 60px; } }

    /* Quick actions */
    .b-actions { grid-area: actions; }
    .action-tile { border: 1px solid var(--ref-border); border-radius: 12px; padding: 12px 14px; margin-bottom: 10px; display: flex; align-items: center; gap: 12px; text-decoration: none; color: inherit; }
    .action-tile:last-child { margin-bottom: 0; }
    .action-tile:hover { border-color: var(--ref-blue); }
    .action-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px; }
    .action-tile .t { font-size: 14.5px; font-weight: 700; }
    .action-tile .d { font-size: 12px; color: var(--ref-gray-text); }
    .action-tile .arrow { margin-left: auto; color: var(--ref-gray-text); }

    /* Calendar + upcoming */
    .b-calendar { grid-area: calendar; }
    .b-upcoming { grid-area: upcoming; }
    .up-item { display: flex; gap: 12px; padding: 10px 6px; border-bottom: 1px solid var(--ref-border); cursor: pointer; border-radius: 8px; }
    .up-item:last-child { border-bottom: none; }
    .up-item:hover, .up-item:focus { background: #F6F7FD; outline: none; }
    .up-date { width: 46px; flex-shrink: 0; text-align: center; border-radius: 8px; padding: 4px 0; color: #fff; }
    .up-date .m { font-size: 11px; font-weight: 700; }
    .up-date .d { font-size: 18px; font-weight: 800; line-height: 1.1; }
    .up-title { font-size: 14px; font-weight: 700; }
    .up-meta { font-size: 12px; color: var(--ref-gray-text); }
    .up-tag { font-size: 10.5px; font-weight: 700; color: #1E7A34; background: #E1F5E4; border-radius: 8px; padding: 1px 7px; }
    .empty-note { font-size: 13.5px; color: var(--ref-gray-text); margin: 0; }

    /* My requests */
    .b-requests { grid-area: requests; }
    .req-row { display: block; border: 1px solid var(--ref-border); border-radius: 10px; padding: 12px 14px; margin-bottom: 10px; text-decoration: none; color: inherit; }
    .req-row:last-child { margin-bottom: 0; }
    .req-row:hover { border-color: var(--ref-blue); }
    .req-title { font-weight: 700; font-size: 14px; }
    .req-meta { font-size: 12px; color: var(--ref-gray-text); margin-top: 4px; }
    .status-pill { font-size: 11.5px; padding: 3px 10px; border-radius: 10px; font-weight: 600; white-space: nowrap; }
    .status-pending { background: #FDF1D8; color: #92650C; }
    .status-processing, .status-progress { background: #E7EAFD; color: #5b52f5; }
    .status-ready { background: #DBF3F0; color: #0B6B63; }
    .status-completed, .status-resolved { background: #E1F5E4; color: #1E7A34; }
    .status-rejected { background: #FDE8E8; color: #D64545; }

    /* News */
    .b-news { grid-area: news; }
    .news-card { display: block; height: 100%; border-radius: 12px; overflow: hidden; border: 1px solid var(--ref-border); text-decoration: none; color: inherit; background: #fff; }
    .news-card:hover { border-color: var(--ref-blue); }
    .news-card img { width: 100%; height: 120px; object-fit: cover; }
    .news-tag { font-size: 11.5px; font-weight: 700; color: var(--ref-blue); letter-spacing: 0.04em; padding: 10px 12px 2px; display: block; }
    .news-title { font-size: 13.5px; font-weight: 700; padding: 0 12px 2px; }
    .news-date { font-size: 12px; color: var(--ref-gray-text); padding: 0 12px 12px; }

    /* FullCalendar look (same as the Calendar page) */
    .fc { --fc-border-color: var(--ref-border); --fc-today-bg-color: #F6F7FE; --fc-button-bg-color: #fff; --fc-button-border-color: var(--ref-border);
          --fc-button-text-color: var(--ref-navy-text); --fc-button-hover-bg-color: #F1F3FE; --fc-button-hover-border-color: var(--ref-blue);
          --fc-button-active-bg-color: var(--ref-blue); --fc-button-active-border-color: var(--ref-blue); font-size: 13px; }
    .fc a { color: inherit !important; text-decoration: none !important; }
    .fc .fc-toolbar-title { font-size: 16px; font-weight: 800; }
    .fc .fc-button { font-size: 12.5px; font-weight: 600; padding: 5px 10px; box-shadow: none !important; }
    .fc .fc-button-primary:not(:disabled).fc-button-active { color: #fff; }
    .fc .fc-scrollgrid { border-radius: 10px; overflow: hidden; }
    .fc .fc-col-header-cell { background: #FAFAFD; }
    .fc .fc-col-header-cell-cushion { display: block; padding: 8px 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--ref-gray-text) !important; }
    .fc .fc-daygrid-day-frame { min-height: 72px; }
    .fc .fc-daygrid-day-top { flex-direction: row; padding: 5px 5px 0; }
    .fc .fc-daygrid-day-number { font-size: 12px; font-weight: 700; color: var(--ref-navy-text) !important; width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
    .fc .fc-day-other .fc-daygrid-day-number { color: #C2C6D6 !important; font-weight: 600; }
    .fc .fc-day-today .fc-daygrid-day-number { background: var(--ref-blue); color: #fff !important; }
    .fc .fc-event { cursor: pointer; border-radius: 6px; }
    .fc .fc-daygrid-event { margin: 2px 3px 0; font-size: 11.5px; }
    .fc .fc-daygrid-block-event .fc-event-title { font-weight: 600; padding: 1px 2px; }
    .fc .fc-daygrid-dot-event { color: var(--ref-navy-text) !important; }
    .fc .fc-daygrid-more-link { font-size: 11px; font-weight: 700; color: var(--ref-blue) !important; }
    .legend { display: flex; flex-wrap: wrap; gap: 10px; font-size: 11.5px; color: var(--ref-gray-text); margin-top: 10px; }
    .legend span { display: inline-flex; align-items: center; gap: 5px; }
    .legend i.dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; }

    .detail-list { display: grid; grid-template-columns: 80px 1fr; gap: 6px 12px; font-size: 14px; margin: 0; }
    .detail-list dt { color: var(--ref-gray-text); font-weight: 600; }
    .detail-list dd { margin: 0; white-space: pre-line; }
    .announce-modal-img { width: 100%; height: 220px; object-fit: cover; border-radius: 10px; margin-bottom: 16px; }
    .announce-modal-tag { font-size: 12px; font-weight: 700; color: var(--ref-blue); letter-spacing: 0.04em; }

    /* Tablet: 2 columns. Phone: 1 column, upcoming list before the calendar. */
    @media (max-width: 1199.98px) {
      .bento { grid-template-columns: repeat(2, minmax(0, 1fr));
        grid-template-areas: "identity identity" "actions upcoming" "calendar calendar" "requests requests" "news news"; }
    }
    @media (max-width: 767.98px) {
      .main { padding: 20px 14px; }
      .bento { grid-template-columns: minmax(0, 1fr);
        grid-template-areas: "identity" "actions" "upcoming" "calendar" "requests" "news"; }
      .card-b { padding: 18px; }
    }
  </style>
</head>
<body>

  @include('partials.resident-sidebar')

  @php
    $upcomingJson = $upcoming->map->toCalendarArray(false)->values();
    $newsImg = ['health' => 'images/community1.jpg', 'utility' => 'images/' . rawurlencode('water supply.jpg'),
                'advisory' => 'images/community1.jpg', 'event' => 'images/assembly.jpg', 'assistance' => 'images/community1.jpg'];
  @endphp

  <div class="main">

    {{-- Data: Resident\PortalController@home. $stats counts this resident's own concerns. --}}
    <div class="bento">

      <!-- Overview -->
      <div class="card-b b-identity">
        <div class="greet-row">
          <div class="seal">
            <img src="{{ asset('images/logo.jpg') }}" alt="Barangay Alejal seal"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <i class="ti ti-shield-check"></i>
          </div>
          <div>
            <div class="greet-date">{{ now()->format('l, F j, Y') }}</div>
            <h1 class="greet">Welcome back, {{ session('resident.first_name', 'Resident') }}</h1>
            <div class="greet-place">Barangay Alejal, Carmen, Davao del Norte</div>
          </div>
        </div>
        @php $open = ($stats['pending'] ?? 0) + ($stats['progress'] ?? 0); @endphp
        <p class="greet-note">
          @if ($open > 0)
            You have {{ $open }} open {{ $open === 1 ? 'report' : 'reports' }}. Officials post updates on <a href="{{ url('/my-reports') }}">My Reports</a>.
          @else
            No open reports right now. If something needs fixing in your purok, <a href="{{ url('/submit-concern') }}">submit a concern</a>.
          @endif
        </p>
        {{-- Each count opens My Reports --}}
        <div class="stat-grid">
          <a href="{{ url('/my-reports') }}" class="stat-tile"><div class="n">{{ $stats['total'] ?? 0 }}</div><div class="l"><span class="dot" style="background:#13204A"></span>My concerns</div></a>
          <a href="{{ url('/my-reports') }}" class="stat-tile"><div class="n">{{ $stats['pending'] ?? 0 }}</div><div class="l"><span class="dot" style="background:#E0A21B"></span>Pending</div></a>
          <a href="{{ url('/my-reports') }}" class="stat-tile"><div class="n">{{ $stats['progress'] ?? 0 }}</div><div class="l"><span class="dot" style="background:#3D4ED1"></span>In progress</div></a>
          <a href="{{ url('/my-reports') }}" class="stat-tile"><div class="n">{{ $stats['resolved'] ?? 0 }}</div><div class="l"><span class="dot" style="background:#1E8E4A"></span>Resolved</div></a>
        </div>
      </div>

      <!-- Quick actions -->
      <div class="card-b b-actions">
        <div class="card-head"><h2>Quick Actions</h2></div>
        <a href="{{ url('/submit-concern') }}" class="action-tile">
          <div class="action-icon" style="background:#FDE8E8; color:#D64545;"><i class="ti ti-alert-triangle"></i></div>
          <div><div class="t">Submit a Concern</div><div class="d">Report an incident or issue</div></div>
          <i class="ti ti-chevron-right arrow"></i>
        </a>
        <a href="{{ url('/request-document') }}" class="action-tile">
          <div class="action-icon" style="background:#E7EAFD; color:var(--ref-navy-text);"><i class="ti ti-file-text"></i></div>
          <div><div class="t">Request a Document</div><div class="d">Clearance, residency, and more</div></div>
          <i class="ti ti-chevron-right arrow"></i>
        </a>
        <a href="{{ url('/calendar') }}" class="action-tile">
          <div class="action-icon" style="background:#E1F5E4; color:#1E7A34;"><i class="ti ti-calendar-event"></i></div>
          <div><div class="t">Barangay Calendar</div><div class="d">Events and activities</div></div>
          <i class="ti ti-chevron-right arrow"></i>
        </a>
      </div>

      <!-- Calendar -->
      <div class="card-b b-calendar">
        <div class="card-head">
          <h2><i class="ti ti-calendar-event"></i> Barangay Calendar</h2>
          <a href="{{ url('/calendar') }}">Open calendar</a>
        </div>
        <div id="homeCalendar"></div>
        <div class="legend">
          @foreach (config('pabarang.event_categories') as $c)
            <span><i class="dot" style="background:{{ $c['color'] }}"></i>{{ $c['label'] }}</span>
          @endforeach
        </div>
      </div>

      <!-- Upcoming events -->
      <div class="card-b b-upcoming">
        <div class="card-head"><h2>Upcoming Events</h2></div>
        @forelse ($upcoming as $i => $e)
          <div class="up-item" role="button" tabindex="0" data-index="{{ $i }}">
            <div class="up-date" style="background: {{ $e->color() }};">
              <div class="m">{{ $e->starts_at->format('M') }}</div>
              <div class="d">{{ $e->starts_at->format('j') }}</div>
            </div>
            <div class="min-w-0">
              <div class="up-title">{{ $e->title }}</div>
              <div class="up-meta">{{ $e->all_day ? 'Whole day' : $e->starts_at->format('g:i A') }}@if ($e->location) · {{ $e->location }}@endif</div>
              @if ($e->canPreregister())<span class="up-tag">Pre-registration open</span>@endif
            </div>
          </div>
        @empty
          <p class="empty-note">No upcoming events yet.</p>
        @endforelse
      </div>

      <!-- My requests -->
      <div class="card-b b-requests">
        <div class="card-head">
          <h2>My Requests</h2>
          <a href="{{ url('/my-reports') }}">View all</a>
        </div>
        @forelse ($recent as $item)
          <a href="{{ url('/my-reports') }}" class="req-row">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <span class="req-title">{{ $item['title'] }}</span>
              <span class="status-pill status-{{ $item['status'] }}">{{ $item['label'] }}</span>
            </div>
            <div class="req-meta">{{ $item['meta'] }}</div>
          </a>
        @empty
          <p class="empty-note">No requests yet. Use Quick Actions to file a concern or request a document.</p>
        @endforelse
      </div>

      <!-- News -->
      <div class="card-b b-news">
        <div class="card-head">
          <h2>Recent News &amp; Announcements</h2>
          <a href="{{ url('/announcements') }}">View all</a>
        </div>
        <div class="row g-3">
          @forelse ($news as $n)
            <div class="col-sm-4">
              <a href="{{ url('/announcements') }}" class="news-card">
                <img src="{{ asset($newsImg[$n->category] ?? 'images/community1.jpg') }}" alt=""
                     onerror="this.onerror=null;this.src='https://placehold.co/400x220/E4E6EF/1E2340?text={{ urlencode(ucfirst($n->category)) }}'">
                <span class="news-tag">{{ $n->label() }}</span>
                <div class="news-title">{{ \Illuminate\Support\Str::limit($n->title, 60) }}</div>
                <div class="news-date">{{ $n->created_at->format('M j, Y') }}</div>
              </a>
            </div>
          @empty
            <div class="col-12"><p class="empty-note">No announcements yet.</p></div>
          @endforelse
        </div>
      </div>

    </div>
  </div>

  <!-- Event details (calendar and upcoming list) -->
  <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="evTitle">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title" id="evTitle"></h5>
            <span class="badge rounded-pill mt-1" id="evBadge"></span>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <dl class="detail-list" id="evDetails"></dl>
          <div class="mt-3 p-2 rounded d-none" id="evPrereg" style="background:#E1F5E4; font-size:13px;">
            This event takes pre-registration. The health center will confirm your slot, and you can follow it on My Reports.
          </div>
        </div>
        <div class="modal-footer">
          <a class="btn d-none" id="evPreregBtn" href="#" style="background:var(--ref-blue); color:#fff;">Pre-register</a>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Resident popup from Announcements > Resident Popup. Shown once per login;
       an edited popup counts as new (PortalController@home). --}}
  @if ($showPopup)
  <div class="modal fade" id="latestAnnouncementModal" tabindex="-1" aria-labelledby="popTitle">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <span class="announce-modal-tag">{{ config('pabarang.announcement_categories.' . $showPopup->category) }}, newest announcement</span>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-2">
          @if ($showPopup->imageUrl())
            <img src="{{ $showPopup->imageUrl() }}" class="announce-modal-img" alt="">
          @endif
          <h5 class="fw-bold mb-2" id="popTitle">{{ $showPopup->title }}</h5>
          <p style="color:var(--ref-gray-text); font-size:14px; white-space:pre-line;">{{ $showPopup->body }}</p>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Dismiss</button>
          <a href="{{ url('/announcements') }}" class="btn" style="background:var(--ref-blue); color:#fff;">View all announcements</a>
        </div>
      </div>
    </div>
  </div>
  @endif

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const UPCOMING = @json($upcomingJson);
    const $ = id => document.getElementById(id);
    const eventModal = new bootstrap.Modal($('eventModal'));

    function showEvent(title, color, p) {
      $('evTitle').textContent = title;
      $('evBadge').textContent = p.categoryLabel || '';
      $('evBadge').style.background = color;
      const dl = $('evDetails');
      dl.replaceChildren();
      [['When', p.when], ['Place', p.location], ['Details', p.description]].filter(r => r[1]).forEach(([k, v]) => {
        const dt = document.createElement('dt'); dt.textContent = k;
        const dd = document.createElement('dd'); dd.textContent = v;
        dl.append(dt, dd);
      });
      const canPrereg = !!p.preregUrl;
      $('evPrereg').classList.toggle('d-none', !canPrereg);
      $('evPreregBtn').classList.toggle('d-none', !canPrereg);
      if (canPrereg) $('evPreregBtn').href = p.preregUrl;
      eventModal.show();
    }

    const calendar = new FullCalendar.Calendar($('homeCalendar'), {
      initialView: window.innerWidth < 768 ? 'listMonth' : 'dayGridMonth',
      headerToolbar: { left: 'prev,next', center: 'title', right: 'today' },
      buttonText: { today: 'Today' },
      height: 'auto',
      fixedWeekCount: false,
      dayMaxEvents: 2,
      noEventsContent: 'No events this month.',
      events: { url: @json(route('resident.calendar.feed')) },
      eventClick(info) {
        info.jsEvent.preventDefault();
        showEvent(info.event.title, info.event.backgroundColor, info.event.extendedProps);
      },
    });
    calendar.render();

    document.querySelectorAll('.up-item').forEach(el => {
      const open = () => { const e = UPCOMING[el.dataset.index]; showEvent(e.title, e.backgroundColor, e.extendedProps); };
      el.addEventListener('click', open);
      el.addEventListener('keydown', ev => { if (ev.key === 'Enter' || ev.key === ' ') { ev.preventDefault(); open(); } });
    });

    @if ($showPopup)
      document.addEventListener('DOMContentLoaded', () => new bootstrap.Modal($('latestAnnouncementModal')).show());
    @endif
  </script>
</body>
</html>
