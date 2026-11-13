<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Leaflet (free, no API key) for the map card -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <style>
    :root {
      --ref-blue: #4F5FE0; --ref-navy-text: #1E2340; --ref-gray-text: #6B7280;
      --ref-bg: #F5F6FA; --ref-border: #E4E6EF; --ref-red: #D64545;
      --c-pending: #F5B942; --c-progress: #4F5FE0; --c-resolved: #4CAF50;
    }
    body { font-family: 'Lato', sans-serif; color: var(--ref-navy-text); background: var(--ref-bg); margin: 0; }

    .admin-layout { display: flex; min-height: 100vh; }

    /* Sidebar */
    .admin-sidebar { width: 250px; background: #1E2340; padding: 24px 18px; flex-shrink: 0; }
    .admin-sidebar .brand { display: flex; align-items: center; gap: 10px; margin-bottom: 30px; padding: 0 6px; }
    .admin-sidebar .brand-logo-lg { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; }
    .admin-sidebar .brand-name { color: #fff; font-size: 15px; font-weight: 700; }
    .sidebar-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #7A80A8; padding: 10px 12px 6px; }
    .sidebar-link { display: flex; align-items: center; gap: 10px; color: #C4C8E8; text-decoration: none; padding: 10px 12px; border-radius: 8px; font-size: 14px; margin-bottom: 2px; }
    .sidebar-link i { font-size: 17px; }
    .sidebar-link:hover { background: rgba(255,255,255,0.06); color: #fff; }
    .sidebar-link.active { background: var(--ref-blue); color: #fff; }

    /* Main */
    .admin-main { flex: 1; min-width: 0; padding: 28px 36px; }
    .admin-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .admin-topbar h4 { font-weight: 800; margin: 0; }
    .admin-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; }

    /* Bento grid: 4 columns. Stats on top, work lists and calendar in the
       middle, the map across the bottom. */
    .bento-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 16px;
      grid-template-areas:
        "total    pending    inprogress resolved"
        "queue    queue      calendar   calendar"
        "docs     docs       calendar   calendar"
        "photos   photos     accounts   docpending"
        "map      map        map        map";
    }
    /* Staff don't handle documents, so their grid has no document cards. */
    .bento-grid.no-docs {
      grid-template-areas:
        "total    pending    inprogress resolved"
        "queue    queue      calendar   calendar"
        "accounts photos     calendar   calendar"
        "map      map        map        map";
    }

    .bento-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 14px; padding: 20px; overflow: hidden; position: relative; }
    .card-title-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .card-title { font-size: 15px; font-weight: 700; }
    .card-link { font-size: 12.5px; color: var(--ref-blue); text-decoration: none; font-weight: 600; }

    /* Stat cards */
    .card-stat { display: flex; align-items: center; gap: 14px; min-height: 104px; }
    .stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
    .stat-number { font-size: 28px; font-weight: 800; line-height: 1; }
    .stat-label { font-size: 12.5px; color: var(--ref-gray-text); margin-top: 6px; }
    .card-total { grid-area: total; }
    .card-pending { grid-area: pending; }
    .card-inprogress { grid-area: inprogress; }
    .card-resolved { grid-area: resolved; }

    /* Lists */
    .card-queue { grid-area: queue; }
    .card-docs { grid-area: docs; }
    .queue-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--ref-border); font-size: 13.5px; }
    .queue-row:last-child { border-bottom: none; }
    .queue-ref { color: var(--ref-gray-text); font-size: 12px; width: 130px; flex-shrink: 0; font-family: ui-monospace, Menlo, monospace; }
    .queue-cat { flex: 1; min-width: 0; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .doc-res { color: var(--ref-gray-text); font-size: 12px; width: 150px; flex-shrink: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .queue-empty { color: var(--ref-gray-text); font-size: 13.5px; padding: 14px 0; }

    /* Small count cards (accounts, documents waiting) */
    .card-count { display: flex; flex-direction: column; justify-content: space-between; gap: 10px; }
    .card-count .count-num { font-size: 28px; font-weight: 800; line-height: 1; }
    .card-count .count-label { font-size: 12.5px; margin-top: 6px; }
    .card-count a { font-size: 12.5px; font-weight: 700; text-decoration: none; }
    .card-accounts { grid-area: accounts; background: #E7EAFD; border-color: #C6CBF7; }
    .card-accounts .count-num, .card-accounts a { color: var(--ref-blue); }
    .card-docpending { grid-area: docpending; background: #FDF1D8; border-color: #F3DFA7; }
    .card-docpending .count-num, .card-docpending a { color: #92650C; }
    /* Photo check tile (Step 8): amber while photos wait for review */
    .card-photos { grid-area: photos; }
    .card-photos.has-items { background: #FFF7E6; border-color: #F3DFA7; }
    .card-photos .count-num, .card-photos a { color: #92650C; }
    .card-photos .photo-row { font-size: 12.5px; padding: 3px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .card-photos .photo-row .queue-ref { width: auto; margin-right: 6px; }

    /* Calendar card */
    .card-calendar { grid-area: calendar; display: flex; flex-direction: column; }
    .cal-dots { display: flex; justify-content: center; gap: 2px; height: 5px; margin-top: 2px; }
    .cal-dots i { width: 5px; height: 5px; border-radius: 50%; display: block; }
    .cal-grid .day.today .cal-dots i { box-shadow: 0 0 0 1px #fff; }
    .cal-next { margin-top: 12px; border-top: 1px solid var(--ref-border); padding-top: 8px; }
    .cal-next-row { display: flex; align-items: center; gap: 10px; font-size: 13px; padding: 5px 0; }
    .cal-next-row .bar { width: 4px; align-self: stretch; border-radius: 2px; flex-shrink: 0; }
    .cal-next-row .when { color: var(--ref-gray-text); font-size: 12px; width: 92px; flex-shrink: 0; }
    .cal-next-row .what { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cal-month { font-size: 13px; font-weight: 700; color: var(--ref-gray-text); margin-bottom: 8px; }
    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center; font-size: 12.5px; flex: 1; align-content: start; }
    .cal-grid .dow { font-size: 11px; font-weight: 700; color: var(--ref-gray-text); padding-bottom: 4px; }
    .cal-grid .day { padding: 5px 0 3px; border-radius: 8px; }
    .cal-grid .day.muted { color: #C2C6D6; }
    .cal-grid .day.today { background: var(--ref-blue); color: #fff; font-weight: 700; }

    /* Map card */
    .card-map { grid-area: map; padding: 0; height: 420px; }
    .card-map .map-label {
      position: absolute; top: 14px; left: 60px; z-index: 500;
      background: #fff; border-radius: 8px; padding: 6px 12px; font-size: 12.5px; font-weight: 700;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .card-map .map-open { position: absolute; top: 14px; right: 14px; z-index: 500; background: #fff; border-radius: 8px; padding: 6px 12px; font-size: 12.5px; font-weight: 700; color: var(--ref-blue); text-decoration: none; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    #concernMap { width: 100%; height: 100%; }
    .concern-marker { width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.4); }
    .concern-marker.unverified-ring { border: 3px solid var(--ref-red); }
    .map-legend { position: absolute; bottom: 14px; left: 14px; z-index: 500; background: #fff; border-radius: 8px; padding: 8px 10px; font-size: 11px; box-shadow: 0 2px 8px rgba(0,0,0,0.12); }
    .legend-row { display: flex; align-items: center; gap: 6px; margin-bottom: 3px; }
    .legend-row:last-child { margin-bottom: 0; }
    .legend-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
    .popup-badge { font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 8px; }
    .popup-anon { background: #EDEDED; color: #555; }

    /* Status pills */
    .status-pill { font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 10px; white-space: nowrap; }
    .status-pending-pill { background: #FDF1D8; color: #92650C; }
    .status-progress-pill { background: #E1EEFB; color: #0C5FA1; }
    .status-resolved-pill { background: #E1F5E4; color: #1E7A34; }
    .status-processing-pill { background: #E7EAFD; color: #3B48B8; }
    .status-ready-pill { background: #DBF3F0; color: #0B6B63; }
    .status-rejected-pill { background: #FDE6E6; color: #9B2226; }

    @media (max-width: 1100px) {
      .bento-grid, .bento-grid.no-docs {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        grid-template-areas:
          "total pending" "inprogress resolved" "queue queue" "docs docs"
          "accounts docpending" "photos photos" "calendar calendar" "map map";
      }
      .bento-grid.no-docs { grid-template-areas: "total pending" "inprogress resolved" "queue queue" "accounts photos" "calendar calendar" "map map"; }
    }
    @media (max-width: 600px) {
      .admin-main { padding: 20px 16px; }
      .bento-grid, .bento-grid.no-docs { grid-template-columns: 1fr; }
      .bento-grid { grid-template-areas: "total" "pending" "inprogress" "resolved" "queue" "docs" "accounts" "docpending" "photos" "calendar" "map"; }
      .bento-grid.no-docs { grid-template-areas: "total" "pending" "inprogress" "resolved" "queue" "accounts" "photos" "calendar" "map"; }
    }

    /* Street / Satellite switch on the map */
    .basemap-switch { display: flex; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); overflow: hidden; }
    .basemap-switch button { border: 0; background: #fff; color: #1E2340; font: 600 12.5px 'Lato', sans-serif; padding: 7px 12px; display: flex; align-items: center; gap: 5px; cursor: pointer; }
    .basemap-switch button + button { border-left: 1px solid #E4E6EF; }
    .basemap-switch button:hover { background: #F1F3FE; }
    .basemap-switch button.active { background: #4F5FE0; color: #fff; }

    /* Emergency team status bar (shown on the landing page next to the barangay hotline) */
    .team-bar { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; background: #fff; border: 1px solid var(--ref-border); border-radius: 14px; padding: 14px 20px; margin-bottom: 16px; }
    .team-bar .team-title { font-size: 15px; font-weight: 700; }
    .team-bar .team-sub { font-size: 12.5px; color: var(--ref-gray-text); }
    .team-bar form { margin: 0; }
    .team-options { display: flex; gap: 8px; flex-wrap: wrap; margin-left: auto; }
    .team-btn { border: 1.5px solid var(--ref-border); background: #fff; color: var(--ref-navy-text); border-radius: 10px; padding: 8px 14px; font-size: 13.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
    .team-btn:hover { border-color: var(--ref-blue); }
    .team-btn .dot { width: 9px; height: 9px; border-radius: 50%; }
    .team-btn.is-on { background: #13204A; border-color: #13204A; color: #fff; }
    .team-saved { font-size: 12.5px; color: #1E7A34; font-weight: 600; }
    @media (max-width: 600px) { .team-options { margin-left: 0; width: 100%; } .team-btn { flex: 1 1 auto; justify-content: center; } }
  </style>
</head>
<body>

  @php
    $canDocs = in_array($me['role'] ?? '', ['captain', 'secretary'], true);

    if ($canDocs) {
      // Newest 5 document requests still being worked on, and how many are waiting.
      $docOpen = \App\Models\DocumentRequest::with('resident')->whereIn('status', ['pending', 'processing', 'ready'])->latest()->take(5)->get();
      $docPendingCount = \App\Models\DocumentRequest::where('status', 'pending')->count();
      $docPill = ['pending' => ['status-pending-pill', 'Pending'], 'processing' => ['status-processing-pill', 'Processing'],
                  'ready' => ['status-ready-pill', 'Ready'], 'completed' => ['status-resolved-pill', 'Completed'], 'rejected' => ['status-rejected-pill', 'Rejected']];
    }

    $pillClass = ['pending' => 'status-pending-pill', 'progress' => 'status-progress-pill', 'blotter' => 'status-progress-pill', 'resolved' => 'status-resolved-pill'];
    $pillLabel = ['pending' => 'Pending', 'progress' => 'In Progress', 'blotter' => 'In Progress', 'resolved' => 'Resolved'];

    // Month preview for the calendar card (Sunday first).
    $today = now();
    $first = $today->copy()->startOfMonth();
    $start = $first->copy()->subDays($first->dayOfWeek);
    $days = [];
    for ($d = $start->copy(); count($days) < 42; $d->addDay()) { $days[] = $d->copy(); }
    if ($days[35]->month !== $today->month) { $days = array_slice($days, 0, 35); }

    // Calendar card: colored dots for this month's events and hearings, and the next 4 items.
    $hearingColor = config('pabarang.hearing_color', '#6B7280');
    $monthEvents = \App\Models\Event::between($days[0]->copy()->startOfDay(), end($days)->copy()->endOfDay())->get();
    $monthHearings = \Illuminate\Support\Facades\DB::table('blotters')->whereIn('status', ['summons', 'hearing'])
      ->whereBetween('hearing_at', [$days[0]->copy()->startOfDay(), end($days)->copy()->endOfDay()])->pluck('hearing_at');
    $dots = [];
    foreach ($monthEvents as $ev) {
      $d = $ev->starts_at->copy()->startOfDay();
      $last = ($ev->ends_at ?? $ev->starts_at)->copy()->startOfDay();
      for ($i = 0; $d->lte($last) && $i < 31; $d->addDay(), $i++) { $dots[$d->toDateString()][] = $ev->color(); }
    }
    foreach ($monthHearings as $h) { $dots[\Illuminate\Support\Carbon::parse($h)->toDateString()][] = $hearingColor; }

    $nextItems = \App\Models\Event::where('starts_at', '>=', now()->startOfDay())->orderBy('starts_at')->take(4)->get()
      ->map(fn ($ev) => ['at' => $ev->starts_at, 'title' => $ev->title, 'color' => $ev->color(), 'allDay' => $ev->all_day])
      ->concat(\Illuminate\Support\Facades\DB::table('blotters')->whereIn('status', ['summons', 'hearing'])
        ->where('hearing_at', '>=', now())->orderBy('hearing_at')->take(4)->get(['id', 'hearing_at'])
        ->map(fn ($b) => ['at' => \Illuminate\Support\Carbon::parse($b->hearing_at), 'title' => 'Hearing BR-' . str_pad((string) $b->id, 5, '0', STR_PAD_LEFT), 'color' => $hearingColor, 'allDay' => false]))
      ->sortBy('at')->take(4)->values();
  @endphp

  <div class="admin-layout">

    @include('partials.admin-sidebar')

    <div class="admin-main">

      <div class="admin-topbar">
        <h4>Dashboard</h4>
        @include('partials.admin-user')
      </div>

      {{-- Emergency team status: residents see it next to the barangay hotline on the landing page --}}
      @php $team = \App\Support\EmergencyTeam::current(); @endphp
      <div class="team-bar">
        <div>
          <div class="team-title"><i class="ti ti-ambulance"></i> Emergency team</div>
          <div class="team-sub">
            Residents see: "{{ $team['public'] }}"
            @if ($team['updated']) · Set {{ $team['updated'] }}{{ $team['by'] ? ' by ' . $team['by'] : '' }}@endif
          </div>
          @if (session('team_saved'))<div class="team-saved">{{ session('team_saved') }}</div>@endif
        </div>
        <div class="team-options">
          @foreach (\App\Support\EmergencyTeam::STATUSES as $key => [$label, $public])
            <form method="POST" action="{{ route('admin.emergency-team') }}">
              @csrf
              <input type="hidden" name="status" value="{{ $key }}">
              <button type="submit" class="team-btn {{ $team['status'] === $key ? 'is-on' : '' }}" aria-pressed="{{ $team['status'] === $key ? 'true' : 'false' }}">
                <span class="dot" style="background: {{ ['available' => '#3FB37F', 'responding' => '#5B8CFF', 'off' => '#8A93B5'][$key] }}"></span>{{ $label }}
              </button>
            </form>
          @endforeach
        </div>
      </div>

      {{-- Concern numbers, recent list and map pins come from AdminConcernController@dashboard. --}}
      <div class="bento-grid {{ $canDocs ? '' : 'no-docs' }}">

        <!-- Stats -->
        <div class="bento-card card-stat card-total">
          <div class="stat-icon" style="background:#E7EAFD; color:var(--ref-blue);"><i class="ti ti-clipboard-list"></i></div>
          <div><div class="stat-number">{{ $counts['total'] }}</div><div class="stat-label">Total Concerns</div></div>
        </div>
        <div class="bento-card card-stat card-pending">
          <div class="stat-icon" style="background:#FDF1D8; color:#92650C;"><i class="ti ti-clock"></i></div>
          <div><div class="stat-number">{{ $counts['pending'] }}</div><div class="stat-label">Pending Review</div></div>
        </div>
        <div class="bento-card card-stat card-inprogress">
          <div class="stat-icon" style="background:#E1EEFB; color:#0C5FA1;"><i class="ti ti-progress"></i></div>
          <div><div class="stat-number">{{ $counts['progress'] }}</div><div class="stat-label">In Progress</div></div>
        </div>
        <div class="bento-card card-stat card-resolved">
          <div class="stat-icon" style="background:#E1F5E4; color:#1E7A34;"><i class="ti ti-circle-check"></i></div>
          <div><div class="stat-number">{{ $counts['resolved'] }}</div><div class="stat-label">Resolved</div></div>
        </div>

        <!-- Recent concerns -->
        <div class="bento-card card-queue">
          <div class="card-title-row">
            <span class="card-title">Recent Concerns</span>
            <a href="{{ url('/admin/concerns') }}" class="card-link">View all &rarr;</a>
          </div>
          @forelse ($recent as $c)
            <div class="queue-row">
              <span class="queue-ref">{{ $c['ref'] }}</span>
              <span class="queue-cat">{{ $c['cat'] }} · {{ $c['zone'] }}</span>
              @if (! empty($c['photo']['needs_review']))
                <i class="ti ti-photo-exclamation" style="color:#92650C;" title="Photo flagged for review"></i>
              @endif
              <span class="status-pill {{ $pillClass[$c['status']] ?? 'status-pending-pill' }}">{{ $pillLabel[$c['status']] ?? 'Pending' }}</span>
            </div>
          @empty
            <div class="queue-empty">No concerns have been submitted yet.</div>
          @endforelse
        </div>

        @if ($canDocs)
          <!-- Open document requests -->
          <div class="bento-card card-docs">
            <div class="card-title-row">
              <span class="card-title">Open Document Requests</span>
              <a href="{{ url('/admin/documents') }}" class="card-link">View all &rarr;</a>
            </div>
            @forelse ($docOpen as $d)
              @php
                [$cls, $lbl] = $docPill[$d->status] ?? $docPill['pending'];
                if ($d->type === 'vaccination' && $d->status === 'ready') { $lbl = 'Scheduled'; }
              @endphp
              <div class="queue-row">
                <span class="queue-ref">{{ $d->ref }}</span>
                <span class="queue-cat">{{ $d->typeName() }}</span>
                <span class="doc-res">{{ $d->residentName() }}</span>
                <span class="status-pill {{ $cls }}">{{ $lbl }}</span>
              </div>
            @empty
              <div class="queue-empty">No open document requests.</div>
            @endforelse
          </div>
        @endif

        <!-- Calendar: this month with dots, then the next 4 events and hearings -->
        <div class="bento-card card-calendar">
          <div class="card-title-row">
            <span class="card-title"><i class="ti ti-calendar-event"></i> Calendar</span>
            <a href="{{ url('/admin/calendar') }}" class="card-link">Open calendar &rarr;</a>
          </div>
          <div class="cal-month">{{ $today->format('F Y') }}</div>
          <div class="cal-grid">
            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dow)
              <div class="dow">{{ $dow }}</div>
            @endforeach
            @foreach ($days as $day)
              @php $dayDots = array_slice(array_unique($dots[$day->toDateString()] ?? []), 0, 3); @endphp
              <div class="day {{ $day->month !== $today->month ? 'muted' : '' }} {{ $day->isSameDay($today) ? 'today' : '' }}">
                {{ $day->day }}
                <div class="cal-dots">@foreach ($dayDots as $c)<i style="background: {{ $c }}"></i>@endforeach</div>
              </div>
            @endforeach
          </div>
          <div class="cal-next">
            @forelse ($nextItems as $it)
              <div class="cal-next-row">
                <span class="bar" style="background: {{ $it['color'] }}"></span>
                <span class="when">{{ $it['at']->format('M j') }}{{ $it['allDay'] ? '' : ', ' . $it['at']->format('g:i A') }}</span>
                <span class="what">{{ $it['title'] }}</span>
              </div>
            @empty
              <div class="queue-empty py-1">Nothing scheduled yet.</div>
            @endforelse
          </div>
        </div>

        <!-- Accounts awaiting approval -->
        <div class="bento-card card-count card-accounts">
          <div>
            <div class="count-num">{{ $pendingAccounts ?? 0 }}</div>
            <div class="count-label">Accounts awaiting approval</div>
          </div>
          <a href="{{ url('/admin/accounts') }}">Review now &rarr;</a>
        </div>

        <!-- Photo check (Step 8): flagged photos waiting for an official -->
        <div class="bento-card card-count card-photos {{ ($counts['photos_to_review'] ?? 0) ? 'has-items' : '' }}">
          <div>
            <div class="count-num">{{ $counts['photos_to_review'] ?? 0 }}</div>
            <div class="count-label">Photos to review</div>
            @foreach ($photoQueue ?? [] as $pq)
              @php $firstFlag = collect($pq['photo']['flags'])->first(fn ($f) => ($f["severity"] ?? "") !== "info"); @endphp
              <div class="photo-row" title="{{ $firstFlag['label'] ?? '' }}">
                <span class="queue-ref">{{ $pq['ref'] }}</span>{{ $firstFlag['label'] ?? 'Flagged' }}
              </div>
            @endforeach
          </div>
          <a href="{{ url('/admin/concerns') }}?photo=review">{{ ($counts['photos_to_review'] ?? 0) ? 'Review photos' : 'View concerns' }} &rarr;</a>
        </div>

        @if ($canDocs)
          <!-- Document requests waiting -->
          <div class="bento-card card-count card-docpending">
            <div>
              <div class="count-num">{{ $docPendingCount }}</div>
              <div class="count-label">Document requests waiting</div>
            </div>
            <a href="{{ url('/admin/documents') }}">Start processing &rarr;</a>
          </div>
        @endif

        <!-- Map -->
        <div class="bento-card card-map">
          <span class="map-label"><i class="ti ti-map-pin"></i> Concern Locations</span>
          <a href="{{ url('/admin/map') }}?view=3d" class="map-open" style="right: 150px;"><i class="ti ti-cube-3d-sphere"></i> 3D view</a>
          <a href="{{ url('/admin/map') }}" class="map-open">Open full map &rarr;</a>
          <div id="concernMap"></div>
          <div class="map-legend">
            <div class="legend-row"><span class="legend-dot" style="background:var(--c-pending);"></span> Pending</div>
            <div class="legend-row"><span class="legend-dot" style="background:var(--c-progress);"></span> In Progress</div>
            <div class="legend-row"><span class="legend-dot" style="background:var(--c-resolved);"></span> Resolved</div>
            <div class="legend-row"><span class="legend-dot" style="border: 2px solid var(--ref-red); background:#fff;"></span> Unverified</div>
          </div>
        </div>

      </div>

    </div>
  </div>

  <script>
    // Leaflet map. One pin per saved report that has a location.
    const map = L.map('concernMap').setView([7.42, 125.70], 14);

    const street = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'
    });

    // Street / Satellite switch. Satellite = Esri World Imagery (free, no key),
    // with road and place labels on top. The choice is remembered in this browser.
    function addBasemapSwitch(map, streetLayer, position) {
      const imagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19, maxNativeZoom: 18,
        attribution: 'Imagery &copy; Esri, Maxar, Earthstar Geographics, and the GIS User Community'
      });
      const roads = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, maxNativeZoom: 18 });
      const places = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, maxNativeZoom: 18 });
      const satellite = L.layerGroup([imagery, roads, places]);

      let mode = 'street';
      const Switch = L.Control.extend({
        options: { position },
        onAdd() {
          const box = L.DomUtil.create('div', 'basemap-switch');
          box.innerHTML = '<button type="button" data-mode="street"><i class="ti ti-map"></i> Street</button>'
                        + '<button type="button" data-mode="satellite"><i class="ti ti-satellite"></i> Satellite</button>';
          L.DomEvent.disableClickPropagation(box);
          box.querySelectorAll('button').forEach(b => b.addEventListener('click', () => setMode(b.dataset.mode)));
          this.box = box;
          return box;
        }
      });
      const control = new Switch().addTo(map);

      function setMode(next) {
        mode = next;
        if (mode === 'satellite') { map.removeLayer(streetLayer); satellite.addTo(map); }
        else { map.removeLayer(satellite); streetLayer.addTo(map); }
        control.box.querySelectorAll('button').forEach(b => {
          b.classList.toggle('active', b.dataset.mode === mode);
          b.setAttribute('aria-pressed', b.dataset.mode === mode);
        });
        try { localStorage.setItem('pb_basemap', mode); } catch (e) {}
      }

      let saved = 'street';
      try { saved = localStorage.getItem('pb_basemap') || 'street'; } catch (e) {}
      setMode(saved === 'satellite' ? 'satellite' : 'street');
    }
    addBasemapSwitch(map, street, 'bottomright');

    const pins = @json($pins);
    const statusColorHex = { pending: '#F5B942', progress: '#4F5FE0', blotter: '#4F5FE0', resolved: '#4CAF50' };

    // Escape text before putting it into HTML (reports are typed by residents).
    function esc(v) { const d = document.createElement('div'); d.textContent = v == null ? '' : v; return d.innerHTML; }

    function buildIcon(p) {
      const ring = p.verify === 'unverified' ? ' unverified-ring' : '';
      const color = statusColorHex[p.status] || statusColorHex.pending;
      return L.divIcon({
        className: '',
        html: `<div class="concern-marker${ring}" style="background:${color};"></div>`,
        iconSize: [22, 22],
        iconAnchor: [11, 11]
      });
    }

    function popupHtml(p) {
      const badge = p.reporter === 'Anonymous'
        ? `<span class="popup-badge popup-anon">Anonymous</span>`
        : (p.reporter ? `<span style="font-size:11.5px; color:var(--ref-gray-text);">${esc(p.reporter)}</span>` : '');
      const meta = [p.ref, p.category, p.zone].filter(Boolean).map(esc).join(' · ');
      return `
        <div style="font-size:13px; min-width:180px;">
          <div style="font-weight:700; margin-bottom:2px;">${esc(p.title || p.label)}</div>
          ${meta ? `<div style="color:var(--ref-gray-text); font-size:11.5px; margin-bottom:6px;">${meta}</div>` : ''}
          ${badge}
          <div style="margin-top:8px;">
            <a href="{{ url('/admin/concerns') }}" style="font-size:12px; color:var(--ref-blue);">View full details &rarr;</a>
          </div>
        </div>`;
    }

    const pinLayer = L.layerGroup().addTo(map);

    function drawPins(list) {
      pinLayer.clearLayers();
      list.forEach(p => {
        L.marker([p.lat, p.lng], { icon: buildIcon(p) }).addTo(pinLayer).bindPopup(popupHtml(p));
      });
      if (list.length) {
        map.fitBounds(list.map(p => [p.lat, p.lng]), { padding: [40, 40], maxZoom: 16 });
      }
    }

    // $pins has no status, so the colors come from the Concern Map page's data
    // (same login). Falls back to $pins if that fails.
    fetch("{{ url('/admin/map') }}", { credentials: 'same-origin' })
      .then(r => r.text())
      .then(html => {
        const m = html.match(/const concerns = (\[[\s\S]*?\]);\s*\n/);
        const full = m ? JSON.parse(m[1]) : null;
        drawPins(full && full.length ? full : pins);
      })
      .catch(() => drawPins(pins));

    // The map sits in a grid cell, so let Leaflet re-measure once the page has laid out.
    window.addEventListener('load', () => map.invalidateSize());
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="{{ asset('css/pabarang.css') }}?v=4" rel="stylesheet">
  <link href="{{ asset('css/pabarang-admin.css') }}?v=4" rel="stylesheet">

  {{-- Admin static mode: nothing moves. Must stay after the two links above.
       Bump the "?v=" number whenever you change the CSS. --}}
  <link href="{{ asset('css/admin-static.css') }}?v=4" rel="stylesheet">

  {{-- Same idea inline, in case admin-static.css is missing or cached. --}}
  <style>
    .admin-layout .bento-card,
    .admin-layout .bento-card:hover,
    .admin-layout .bento-card:focus-within,
    .admin-layout .bento-card:not(.card-map) *,
    .admin-layout .bento-card:not(.card-map):hover * {
      transform: none !important;
      transition: none !important;
      animation: none !important;
      box-shadow: none !important;
      filter: none !important;
    }
    /* The map card is left alone because Leaflet positions pins and tiles with transforms. */
  </style>
</body>
</html>
