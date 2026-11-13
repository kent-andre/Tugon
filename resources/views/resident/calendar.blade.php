<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Calendar - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

  <style>
    :root {
      --ref-blue: #4F5FE0; --ref-navy-text: #1E2340; --ref-gray-text: #6B7280;
      --ref-bg: #F5F6FA; --ref-border: #E4E6EF;
    }
    body { font-family: 'Lato', sans-serif; color: var(--ref-navy-text); background: var(--ref-bg); }

    .main { margin-left: 250px; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 18px 40px 0; }
    .page-wrap { max-width: 1100px; margin: 0 auto; padding: 40px 20px; }

    .box { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 18px; }
    .box-title { font-size: 15px; font-weight: 700; margin-bottom: 12px; }

    .up-item { display: flex; gap: 12px; padding: 10px 6px; border-bottom: 1px solid var(--ref-border); cursor: pointer; border-radius: 8px; }
    .up-item:last-child { border-bottom: none; }
    .up-item:hover, .up-item:focus { background: #F6F7FD; outline: none; }
    .up-date { width: 46px; flex-shrink: 0; text-align: center; border-radius: 8px; padding: 4px 0; color: #fff; }
    .up-date .m { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
    .up-date .d { font-size: 18px; font-weight: 800; line-height: 1.1; }
    .up-title { font-size: 14px; font-weight: 700; }
    .up-meta { font-size: 12px; color: var(--ref-gray-text); }
    .up-tag { font-size: 10.5px; font-weight: 700; color: #1E7A34; background: #E1F5E4; border-radius: 8px; padding: 1px 7px; }

    .legend { display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; color: var(--ref-gray-text); margin-bottom: 12px; }
    .legend span { display: inline-flex; align-items: center; gap: 6px; }
    .legend i.dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }

    .fc { --fc-border-color: var(--ref-border); --fc-today-bg-color: #EEF0FD; --fc-button-bg-color: #fff; --fc-button-border-color: var(--ref-border);
          --fc-button-text-color: var(--ref-navy-text); --fc-button-hover-bg-color: #F1F3FE; --fc-button-hover-border-color: var(--ref-blue);
          --fc-button-active-bg-color: var(--ref-blue); --fc-button-active-border-color: var(--ref-blue); font-size: 13px; }
    .fc .fc-button-primary:not(:disabled).fc-button-active { color: #fff; }
    .fc .fc-toolbar-title { font-size: 17px; font-weight: 800; }
    .fc .fc-event { cursor: pointer; border-radius: 6px; padding: 1px 3px; }
    @media (max-width: 575.98px) { .fc .fc-toolbar { flex-direction: column; gap: 8px; } }

    .detail-list { display: grid; grid-template-columns: 80px 1fr; gap: 6px 12px; font-size: 14px; margin: 0; }
    .detail-list dt { color: var(--ref-gray-text); font-weight: 600; }
    .detail-list dd { margin: 0; white-space: pre-line; }

    /* Calendar look: stop the site's link styles (blue, underlined) leaking into FullCalendar */
    .fc a { color: inherit !important; text-decoration: none !important; }
    .fc .fc-scrollgrid { border-radius: 10px; overflow: hidden; }
    .fc .fc-col-header-cell { background: #FAFAFD; }
    .fc .fc-col-header-cell-cushion { display: block; padding: 9px 4px; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--ref-gray-text) !important; }
    .fc .fc-daygrid-day-frame { min-height: 96px; }
    .fc .fc-daygrid-day-top { flex-direction: row; padding: 6px 6px 0; }
    .fc .fc-daygrid-day-number { font-size: 12.5px; font-weight: 700; color: var(--ref-navy-text) !important; width: 26px; height: 26px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
    .fc .fc-day-other .fc-daygrid-day-number { color: #C2C6D6 !important; font-weight: 600; }
    .fc .fc-day-today { background: #F6F7FE !important; }
    .fc .fc-day-today .fc-daygrid-day-number { background: var(--ref-blue); color: #fff !important; }
    .fc .fc-daygrid-event { margin: 2px 4px 0; font-size: 12px; }
    .fc .fc-daygrid-block-event .fc-event-title { font-weight: 600; padding: 1px 2px; }
    .fc .fc-daygrid-dot-event { color: var(--ref-navy-text) !important; border-radius: 6px; }
    .fc .fc-daygrid-dot-event:hover { background: #F1F3FE; }
    .fc .fc-daygrid-dot-event .fc-event-time { font-weight: 600; color: var(--ref-gray-text); }
    .fc .fc-daygrid-dot-event .fc-event-title { font-weight: 600; }
    .fc .fc-daygrid-more-link { font-size: 11.5px; font-weight: 700; color: var(--ref-blue) !important; padding-left: 4px; }
    .fc .fc-button { font-size: 13px; font-weight: 600; padding: 6px 12px; box-shadow: none !important; }
    .fc .fc-button-primary:not(:disabled):hover { color: var(--ref-blue); }
    .fc .fc-list-day-cushion { background: #FAFAFD !important; }
    .fc .fc-list-event:hover td { background: #F6F7FE; }
    .fc .fc-list-event-title { font-weight: 600; }
    .fc .fc-timegrid-slot-label-cushion, .fc .fc-timegrid-axis-cushion { font-size: 11.5px; color: var(--ref-gray-text); }
  </style>
</head>
<body>

  @include('partials.resident-sidebar')

  <div class="main">
    <div class="top-bar">
      <h5 class="fw-bold mb-0">Calendar</h5>
    </div>

    <div class="page-wrap">
      <h3 class="fw-bold mb-1">Barangay Calendar</h3>
      <p style="color: var(--ref-gray-text); font-size: 14px;" class="mb-4">
        Events from Barangay Alejal. Tap an event to see the details.
      </p>

      @php
        // Same shape the calendar uses, so a click opens the same details window.
        $upcomingJson = $upcoming->map->toCalendarArray(false)->values();
      @endphp

      <div class="row g-4">
        {{-- Upcoming list: first on phones, on the right on wide screens --}}
        <div class="col-lg-4 order-1 order-lg-2">
          <div class="box">
            <div class="box-title"><i class="ti ti-calendar-time"></i> Upcoming events</div>
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
              <p class="mb-0" style="font-size: 13.5px; color: var(--ref-gray-text);">No upcoming events yet. Check back soon.</p>
            @endforelse
          </div>
        </div>

        <div class="col-lg-8 order-2 order-lg-1">
          <div class="box">
            <div class="legend">
              @foreach ($categories as $c)
                <span><i class="dot" style="background:{{ $c['color'] }}"></i>{{ $c['label'] }}</span>
              @endforeach
            </div>
            <div id="calendar"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Event details -->
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const UPCOMING = @json($upcomingJson);
    const $ = id => document.getElementById(id);
    const modal = new bootstrap.Modal($('eventModal'));

    // Works for both a FullCalendar event and an item from the upcoming list.
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
      modal.show();
    }

    const calendar = new FullCalendar.Calendar($('calendar'), {
      initialView: window.innerWidth < 768 ? 'listMonth' : 'dayGridMonth',
      headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listMonth' },
      buttonText: { today: 'Today', month: 'Month', list: 'List' },
      height: 'auto',
      dayMaxEvents: 3,
      fixedWeekCount: false,   // only the weeks this month needs
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
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <script src="{{ asset('js/pabarang.js') }}"></script>
</body>
</html>
