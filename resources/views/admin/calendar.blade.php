{{--
  Admin calendar. Data comes from Admin\CalendarController@feed as JSON.
  Captain and secretary add, edit, move and delete events. Staff can view.
  Blotter hearings (summons/hearing cases) show here only, never to residents.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Calendar - Tugon Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

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

    .admin-main { flex: 1; min-width: 0; padding: 28px 36px; }
    .admin-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
    .admin-topbar h4 { font-weight: 800; margin: 0; }
    .admin-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; }

    .toolbar { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .legend { display: flex; flex-wrap: wrap; gap: 14px; font-size: 12.5px; color: var(--ref-gray-text); }
    .legend span { display: inline-flex; align-items: center; gap: 6px; }
    .legend i.dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
    .btn-add { background: var(--ref-blue); color: #fff; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 600; font-size: 13.5px; }
    .btn-add:hover { background: #3E4DCB; color: #fff; }

    .cal-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 14px; padding: 18px; }
    .fc { --fc-border-color: var(--ref-border); --fc-today-bg-color: #EEF0FD; --fc-button-bg-color: #fff; --fc-button-border-color: var(--ref-border);
          --fc-button-text-color: var(--ref-navy-text); --fc-button-hover-bg-color: #F1F3FE; --fc-button-hover-border-color: var(--ref-blue);
          --fc-button-active-bg-color: var(--ref-blue); --fc-button-active-border-color: var(--ref-blue); font-size: 13px; }
    .fc .fc-button-primary:not(:disabled).fc-button-active { color: #fff; }
    .fc .fc-toolbar-title { font-size: 18px; font-weight: 800; }
    .fc .fc-event { cursor: pointer; border-radius: 6px; padding: 1px 3px; }
    .fc .fc-event.is-private { opacity: .75; border-style: dashed !important; }

    .detail-list { display: grid; grid-template-columns: 110px 1fr; gap: 6px 12px; font-size: 13.5px; margin: 0; }
    .detail-list dt { color: var(--ref-gray-text); font-weight: 600; }
    .detail-list dd { margin: 0; white-space: pre-line; }
    .form-label { font-weight: 600; font-size: 13.5px; }
    .hint { font-size: 12px; color: var(--ref-gray-text); }

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

  <div class="admin-layout">

    @include('partials.admin-sidebar')

    <div class="admin-main">

      <div class="admin-topbar">
        <h4>Calendar</h4>
        @include('partials.admin-user')
      </div>

      <div class="toolbar">
        <div class="legend">
          @foreach ($categories as $key => $c)
            <span><i class="dot" style="background:{{ $c['color'] }}"></i>{{ $c['label'] }}</span>
          @endforeach
          <span><i class="dot" style="background:{{ $hearingColor }}"></i>Blotter hearing (officials only)</span>
        </div>
        <div class="d-flex align-items-center gap-3">
          <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="showHearings" checked>
            <label class="form-check-label" for="showHearings" style="font-size:13px;">Show hearings</label>
          </div>
          @if ($canEdit)
            <button type="button" class="btn-add" id="addBtn"><i class="ti ti-plus"></i> Add event</button>
          @endif
        </div>
      </div>

      <div class="cal-card">
        <div id="calendar"></div>
      </div>
      @if ($canEdit)
        <p class="hint mt-2">Click a day to add an event. Drag an event to move it. Dashed events are for officials only.</p>
      @endif

    </div>
  </div>

  <!-- View details (hearings, and events for staff) -->
  <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewTitle">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title" id="viewTitle"></h5>
            <span class="badge rounded-pill mt-1" id="viewBadge"></span>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body"><dl class="detail-list" id="viewDetails"></dl></div>
        <div class="modal-footer">
          <a class="btn btn-sm btn-outline-primary d-none" id="viewLink" href="#">Open in Blotter Reports</a>
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  @if ($canEdit)
  <!-- Add / edit event -->
  <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalTitle">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="eventModalTitle">Add event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="eventForm" novalidate>
          <div class="modal-body">
            <div class="alert alert-danger py-2 d-none" id="formError" style="font-size:13px;"></div>
            <div class="mb-3">
              <label class="form-label" for="fTitle">Title</label>
              <input type="text" class="form-control" id="fTitle" maxlength="160" required placeholder="e.g. Barangay Assembly">
            </div>
            <div class="mb-3">
              <label class="form-label" for="fCategory">Category</label>
              <select class="form-select" id="fCategory" required>
                @foreach ($categories as $key => $c)
                  <option value="{{ $key }}">{{ $c['label'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="fAllDay">
              <label class="form-check-label" for="fAllDay" style="font-size:13.5px;">Whole day</label>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-sm-6">
                <label class="form-label" for="fStart">Starts</label>
                <input type="datetime-local" class="form-control" id="fStart" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label" for="fEnd">Ends <span class="text-muted fw-normal">(optional)</span></label>
                <input type="datetime-local" class="form-control" id="fEnd">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="fLocation">Place</label>
              <input type="text" class="form-control" id="fLocation" maxlength="160" placeholder="e.g. Barangay Hall covered court">
            </div>
            <div class="mb-3">
              <label class="form-label" for="fDescription">Details</label>
              <textarea class="form-control" id="fDescription" rows="3" maxlength="2000" placeholder="What residents should know or bring"></textarea>
            </div>
            <div class="form-check mb-1">
              <input class="form-check-input" type="checkbox" id="fPublic" checked>
              <label class="form-check-label" for="fPublic" style="font-size:13.5px;">Show to residents</label>
            </div>
            <div class="hint mb-2">Untick for internal items, like a planning meeting.</div>
            <div class="form-check d-none" id="preregWrap">
              <input class="form-check-input" type="checkbox" id="fPrereg">
              <label class="form-check-label" for="fPrereg" style="font-size:13.5px;">Residents can pre-register</label>
              <div class="hint">Adds a "Pre-register" button that opens the Vaccination Scheduling form with this date. Requests appear in Document Requests.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-outline-danger me-auto d-none" id="deleteBtn"><i class="ti ti-trash"></i> Delete</button>
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm" style="background:var(--ref-blue); color:#fff;" id="saveBtn">Save event</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 4000;">
    <div id="pbToast" class="toast align-items-center text-bg-dark border-0" role="status" aria-live="polite">
      <div class="d-flex">
        <div class="toast-body" id="pbToastText"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const CAN_EDIT = @json($canEdit);
    const FEED = @json(route('admin.calendar.feed'));
    const BASE = @json(url('/admin/calendar/events'));
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const $ = id => document.getElementById(id);

    function toast(msg) {
      $('pbToastText').textContent = msg;
      bootstrap.Toast.getOrCreateInstance($('pbToast'), { delay: 3500 }).show();
    }
    const pad = n => String(n).padStart(2, '0');
    const toDate = d => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    const toLocal = d => `${toDate(d)}T${pad(d.getHours())}:${pad(d.getMinutes())}`;

    // Replaced below for captain and secretary; staff only view.
    let openForm = () => {};
    let moveEvent = info => info.revert();

    // ---------- calendar ----------
    const calendar = new FullCalendar.Calendar($('calendar'), {
      initialView: window.innerWidth < 768 ? 'listMonth' : 'dayGridMonth',
      headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listMonth' },
      buttonText: { today: 'Today', month: 'Month', week: 'Week', list: 'List' },
      height: 'auto',
      dayMaxEvents: 3,
      fixedWeekCount: false,   // only the weeks this month needs
      nowIndicator: true,
      editable: CAN_EDIT,
      eventStartEditable: CAN_EDIT,
      eventDurationEditable: false,
      events: { url: FEED, failure: () => toast('Could not load the calendar. Refresh the page.') },
      eventDidMount(info) {
        const p = info.event.extendedProps;
        if (p.kind === 'event' && p.isPublic === false) info.el.classList.add('is-private');
        if (p.kind === 'hearing' && !$('showHearings').checked) info.el.style.display = 'none';
        info.el.title = info.event.title + (p.location ? ' - ' + p.location : '');
      },
      // Month view: a click means "this day", so default to a timed event at 8 AM.
      dateClick(info) { if (CAN_EDIT) openForm(null, info.date, info.view.type.startsWith('timeGrid') ? info.allDay : false); },
      eventClick(info) {
        info.jsEvent.preventDefault();
        const p = info.event.extendedProps;
        if (p.kind === 'event' && CAN_EDIT) openForm(info.event);
        else openView(info.event);
      },
      eventDrop(info) { moveEvent(info); },
    });
    calendar.render();

    $('showHearings').addEventListener('change', () => calendar.refetchEvents());

    // ---------- details (read-only) ----------
    function openView(ev) {
      const p = ev.extendedProps;
      $('viewTitle').textContent = ev.title;
      const badge = $('viewBadge');
      badge.textContent = p.categoryLabel || '';
      badge.style.background = ev.backgroundColor;
      const rows = p.kind === 'hearing'
        ? [['When', p.when], ['Parties', p.parties], ['Place', p.location], ['Mediator', p.mediator]]
        : [['When', p.when], ['Place', p.location], ['Details', p.description], ['Residents', p.isPublic === false ? 'Hidden (officials only)' : 'Visible']];
      const dl = $('viewDetails');
      dl.replaceChildren();
      rows.filter(r => r[1]).forEach(([k, v]) => {
        const dt = document.createElement('dt'); dt.textContent = k;
        const dd = document.createElement('dd'); dd.textContent = v;
        dl.append(dt, dd);
      });
      const link = $('viewLink');
      link.classList.toggle('d-none', p.kind !== 'hearing');
      if (p.url) link.href = p.url;
      bootstrap.Modal.getOrCreateInstance($('viewModal')).show();
    }

    if (CAN_EDIT) {
      const modal = new bootstrap.Modal($('eventModal'));
      let editingId = null;

      function setAllDay(on) {
        const s = $('fStart').value, e = $('fEnd').value;
        $('fStart').type = $('fEnd').type = on ? 'date' : 'datetime-local';
        $('fStart').value = s ? (on ? s.slice(0, 10) : (s.length === 10 ? s + 'T08:00' : s)) : '';
        $('fEnd').value = e ? (on ? e.slice(0, 10) : (e.length === 10 ? e + 'T17:00' : e)) : '';
      }
      function syncPrereg() {
        const show = $('fCategory').value === 'health' && $('fPublic').checked;
        $('preregWrap').classList.toggle('d-none', !show);
        if (!show) $('fPrereg').checked = false;
      }
      $('fAllDay').addEventListener('change', e => setAllDay(e.target.checked));
      $('fCategory').addEventListener('change', syncPrereg);
      $('fPublic').addEventListener('change', syncPrereg);

      openForm = function (ev, date, allDay) {
        $('eventForm').reset();
        $('formError').classList.add('d-none');
        editingId = ev ? ev.extendedProps.eventId : null;
        $('eventModalTitle').textContent = ev ? 'Edit event' : 'Add event';
        $('deleteBtn').classList.toggle('d-none', !ev);

        if (ev) {
          const p = ev.extendedProps;
          $('fTitle').value = ev.title;
          $('fCategory').value = p.category;
          $('fAllDay').checked = ev.allDay;
          setAllDay(ev.allDay);
          $('fStart').value = p.rawStart || '';
          $('fEnd').value = p.rawEnd || '';
          $('fLocation').value = p.location || '';
          $('fDescription').value = p.description || '';
          $('fPublic').checked = p.isPublic !== false;
          $('fPrereg').checked = !!p.prereg;
        } else {
          const d = date || new Date();
          const isAllDay = allDay !== undefined ? allDay : false;
          $('fAllDay').checked = isAllDay;
          setAllDay(isAllDay);
          if (isAllDay) { $('fStart').value = toDate(d); }
          else {
            const start = new Date(d);
            if (date === undefined || (start.getHours() === 0 && start.getMinutes() === 0)) start.setHours(8, 0, 0, 0);
            $('fStart').value = toLocal(start);
          }
          $('fPublic').checked = true;
        }
        syncPrereg();
        modal.show();
      };
      $('addBtn').addEventListener('click', () => openForm(null));

      async function send(method, url, body) {
        const res = await fetch(url, {
          method,
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
          body: body ? JSON.stringify(body) : undefined,
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Could not save.'));
        return data;
      }

      function formData() {
        return {
          title: $('fTitle').value.trim(),
          category: $('fCategory').value,
          all_day: $('fAllDay').checked,
          starts_at: $('fStart').value,
          ends_at: $('fEnd').value || null,
          location: $('fLocation').value.trim() || null,
          description: $('fDescription').value.trim() || null,
          is_public: $('fPublic').checked,
          allows_preregistration: $('fPrereg').checked,
        };
      }

      $('eventForm').addEventListener('submit', async e => {
        e.preventDefault();
        const body = formData();
        if (!body.title || !body.starts_at) {
          $('formError').textContent = 'Add a title and a start date.';
          $('formError').classList.remove('d-none');
          return;
        }
        $('saveBtn').disabled = true;
        try {
          await send(editingId ? 'PUT' : 'POST', editingId ? `${BASE}/${editingId}` : BASE, body);
          modal.hide();
          calendar.refetchEvents();
          toast(editingId ? 'Event updated.' : 'Event added.');
        } catch (err) {
          $('formError').textContent = err.message;
          $('formError').classList.remove('d-none');
        } finally {
          $('saveBtn').disabled = false;
        }
      });

      $('deleteBtn').addEventListener('click', async () => {
        if (!editingId || !confirm('Delete this event? Residents will no longer see it.')) return;
        try {
          await send('DELETE', `${BASE}/${editingId}`);
          modal.hide();
          calendar.refetchEvents();
          toast('Event deleted.');
        } catch (err) { toast(err.message); }
      });

      // Drag to another day/time: FullCalendar keeps the length, so save its new start and end.
      // All-day ends are stored as the last day, FullCalendar's end is the day after.
      moveEvent = async function (info) {
        const ev = info.event, p = ev.extendedProps;
        if (p.kind !== 'event') { info.revert(); return; }
        let end = null;
        if (p.rawEnd && ev.end) {
          const e = new Date(ev.end);
          if (ev.allDay) e.setDate(e.getDate() - 1);
          end = ev.allDay ? toDate(e) : toLocal(e);
        }
        const body = {
          title: ev.title, category: p.category, all_day: ev.allDay,
          starts_at: ev.allDay ? toDate(ev.start) : toLocal(ev.start),
          ends_at: end,
          location: p.location || null, description: p.description || null,
          is_public: p.isPublic !== false, allows_preregistration: !!p.prereg,
        };
        try {
          await send('PUT', `${BASE}/${p.eventId}`, body);
          calendar.refetchEvents();
          toast('Event moved.');
        } catch (err) { info.revert(); toast(err.message); }
      };
    }
  </script>

  <link href="{{ asset('css/pabarang.css') }}?v=5" rel="stylesheet">
  <link href="{{ asset('css/pabarang-admin.css') }}?v=5" rel="stylesheet">
  <link href="{{ asset('css/admin-static.css') }}?v=5" rel="stylesheet">
</body>
</html>
