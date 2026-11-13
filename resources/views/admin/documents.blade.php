<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document Requests - Tugon Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --font: 'Lato', sans-serif;
      --ref-blue: #4F5FE0;
      --ref-blue-dark: #1F2540;
      --ref-navy-text: #1E2340;
      --ref-bg: #F5F6FA;
      --ref-border: #E4E6EF;
      --ref-gray-text: #6B7280;
      --ref-tint: #E9ECFC;
      --ref-tint-border: #D3D8F5;
      --sidebar-w: 250px;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: var(--font);
      color: var(--ref-navy-text);
      background: var(--ref-bg);
    }

    :focus-visible { outline: 3px solid rgba(79,95,224,.45); outline-offset: 2px; }

    /* ===== Layout (same as the other admin pages; sidebar comes from partials.admin-sidebar) ===== */
    .admin-layout { display: flex; min-height: 100vh; }
    .admin-sidebar { width: var(--sidebar-w); background: var(--ref-blue-dark); padding: 24px 18px; flex-shrink: 0; }
    .admin-sidebar .brand { display: flex; align-items: center; gap: 10px; margin-bottom: 30px; padding: 0 6px; }
    .admin-sidebar .brand-logo-lg { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0; overflow: hidden; }
    .admin-sidebar .brand-logo-lg svg, .admin-sidebar .brand-logo-lg img { width: 100%; height: 100%; display: block; }
    .admin-sidebar .brand-name { color: #fff; font-size: 15px; font-weight: 700; }
    .sidebar-label { color: #7A80A8; font-size: 11px; letter-spacing: .05em; text-transform: uppercase; padding: 10px 12px 6px; }
    .sidebar-link { display: flex; align-items: center; gap: 10px; padding: 10px 12px; margin: 0 0 2px 0; border-radius: 8px; color: #C4C8E8; font-size: 14px; text-decoration: none; }
    .sidebar-link i { font-size: 17px; }
    .sidebar-link:hover { background: rgba(255,255,255,.06); color: #fff; }
    .sidebar-link.active { background: var(--ref-blue); color: #fff; }

    .admin-main { flex: 1; min-width: 0; padding: 28px 36px; }
    .admin-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
    .admin-topbar h4 { font-weight: 800; margin: 0; }
    .admin-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }


    .info-banner {
      display: flex; gap: 12px; align-items: flex-start;
      background: var(--ref-tint); border: 1px solid var(--ref-tint-border);
      border-radius: 12px; padding: 14px 18px;
      font-size: 13px; line-height: 1.55; margin-bottom: 18px;
    }
    .info-banner i { font-size: 18px; color: var(--ref-blue); margin-top: 1px; }

    .box {
      background: #fff; border: 1px solid var(--ref-border);
      border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.03);
    }

    /* Filters */
    .filter-bar { display: flex; flex-wrap: wrap; gap: 10px; padding: 14px 18px; margin-bottom: 18px; align-items: center; }
    .search-wrap { position: relative; flex: 1 1 260px; max-width: 380px; }
    .search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--ref-gray-text); font-size: 18px; pointer-events: none; }
    .search-wrap input { padding-left: 38px; }
    .filter-bar .form-control, .filter-bar .form-select {
      border-color: var(--ref-border); border-radius: 8px; height: 38px; font-size: 13px; width: auto;
    }
    .search-wrap .form-control { width: 100%; }
    .btn-clear { background: none; border: none; color: var(--ref-blue); font-weight: 600; font-size: 13px; padding: 8px 4px; }
    .btn-clear:hover { text-decoration: underline; }

    /* Status tabs */
    .tabs { display: flex; gap: 6px; padding: 0 18px; border-bottom: 1px solid var(--ref-border); overflow-x: auto; }
    .tab {
      background: none; border: none; border-bottom: 3px solid transparent;
      padding: 13px 12px 10px; font-size: 13.5px; font-weight: 600; color: var(--ref-gray-text);
      white-space: nowrap; display: flex; align-items: center; gap: 8px;
    }
    .tab:hover { color: var(--ref-navy-text); }
    .tab.active { color: var(--ref-blue); border-bottom-color: var(--ref-blue); }
    .tab-count {
      background: var(--ref-bg); color: var(--ref-gray-text);
      border-radius: 999px; padding: 1px 8px; font-size: 11.5px; font-weight: 700;
    }
    .tab.active .tab-count { background: var(--ref-tint); color: var(--ref-blue); }

    /* Table */
    .req-table { margin: 0; --bs-table-bg: transparent; }
    .req-table th {
      font-size: 12px; font-weight: 600; color: var(--ref-gray-text);
      padding: 12px 14px; border-bottom: 1px solid var(--ref-border); white-space: nowrap; background: #FAFAFD;
    }
    .req-table td { padding: 12px 14px; vertical-align: middle; border-bottom: 1px solid var(--ref-border); font-size: 13px; }
    .req-table tbody tr[data-id] { cursor: pointer; }
    .req-table tbody tr[data-id]:hover td { background: #F8F9FE; }
    .req-table tbody tr:last-child td { border-bottom: none; }
    .ref { font-weight: 600; color: var(--ref-gray-text); white-space: nowrap; }
    .cell-person { display: flex; align-items: center; gap: 10px; }
    .mini-avatar {
      width: 32px; height: 32px; border-radius: 50%; background: var(--ref-tint); color: var(--ref-blue);
      display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0;
    }
    .cell-main { font-weight: 600; line-height: 1.25; }
    .cell-sub { font-size: 12px; color: var(--ref-gray-text); }
    .cell-doc { display: flex; align-items: center; gap: 10px; font-weight: 600; }
    .cell-doc i { font-size: 18px; color: var(--ref-blue); }
    .muted { color: var(--ref-gray-text); }
    .btn-view {
      background: #fff; border: 1px solid var(--ref-border); border-radius: 8px;
      padding: 5px 12px; font-size: 12.5px; font-weight: 600; color: var(--ref-navy-text);
    }
    .btn-view:hover { border-color: var(--ref-blue); color: var(--ref-blue); }

    .table-foot { padding: 12px 18px; border-top: 1px solid var(--ref-border); font-size: 12.5px; color: var(--ref-gray-text); }

    .empty { padding: 56px 20px; text-align: center; color: var(--ref-gray-text); }
    .empty i { font-size: 34px; color: #B8BDD6; display: block; margin-bottom: 8px; }
    .empty strong { display: block; color: var(--ref-navy-text); font-size: 16px; margin-bottom: 4px; }

    /* Status pills */
    .pill { display: inline-flex; align-items: center; gap: 7px; padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 600; white-space: nowrap; }
    .pill::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    .st-pending    { background: #FFF3D6; color: #8A5A00; }
    .st-pending::before    { background: #F5B83D; }
    .st-processing { background: #E8EBFF; color: #3B48B8; }
    .st-processing::before { background: #4F5FE0; }
    .st-ready      { background: #DBF3F0; color: #0B6B63; }
    .st-ready::before      { background: #14A79B; }
    .st-completed  { background: #E0F4E6; color: #1C6B34; }
    .st-completed::before  { background: #3DB35A; }
    .st-rejected   { background: #FDE6E6; color: #9B2226; }
    .st-rejected::before   { background: #DC4C4C; }

    /* ===== Drawer ===== */
    .offcanvas { --bs-offcanvas-width: 500px; }
    .offcanvas-header { border-bottom: 1px solid var(--ref-border); padding: 20px 24px; align-items: flex-start; gap: 14px; }
    .drawer-icon {
      width: 46px; height: 46px; border-radius: 12px; background: var(--ref-tint);
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .drawer-icon i { font-size: 24px; color: var(--ref-blue); }
    .drawer-title { font-size: 17px; font-weight: 700; margin: 0; line-height: 1.25; }
    .drawer-sub { font-size: 13px; color: var(--ref-gray-text); }
    .offcanvas-body { padding: 22px 24px; }
    .offcanvas-footer { border-top: 1px solid var(--ref-border); padding: 16px 24px; display: flex; gap: 10px; flex-wrap: wrap; background: #fff; }

    .d-section { margin-bottom: 26px; }
    .d-heading { font-size: 14px; font-weight: 700; margin-bottom: 12px; }
    .person-row { display: flex; align-items: center; gap: 14px; }
    .person-row .mini-avatar { width: 46px; height: 46px; font-size: 16px; }

    .kv { display: grid; grid-template-columns: 150px 1fr; gap: 10px 16px; margin: 0; font-size: 13.5px; }
    .kv dt { color: var(--ref-gray-text); font-weight: 500; }
    .kv dd { margin: 0; font-weight: 600; }

    .file-tile {
      display: flex; align-items: center; gap: 12px;
      border: 1px solid var(--ref-border); border-radius: 10px; padding: 10px 14px; margin-bottom: 8px;
    }
    .file-tile i { font-size: 24px; color: var(--ref-gray-text); }
    .file-tile .fname { font-size: 14px; font-weight: 600; flex: 1; word-break: break-all; }
    .file-tile a { font-size: 13.5px; font-weight: 600; color: var(--ref-blue); text-decoration: none; }
    .file-tile a:hover { text-decoration: underline; }

    .assess-box { background: #FFF8E6; border: 1px solid #F3DFA7; border-radius: 10px; padding: 12px 14px; margin-bottom: 26px; font-size: 14px; }

    .timeline { list-style: none; margin: 0; padding: 0; }
    .timeline li { position: relative; padding: 0 0 18px 30px; color: #9AA0B8; }
    .timeline li:last-child { padding-bottom: 0; }
    .timeline li::before { content: ""; position: absolute; left: 7px; top: 18px; bottom: -2px; width: 2px; background: var(--ref-border); }
    .timeline li:last-child::before { display: none; }
    .tl-dot { position: absolute; left: 0; top: 3px; width: 16px; height: 16px; border-radius: 50%; border: 2px solid #C9CDE0; background: #fff; }
    .tl-label { font-size: 13.5px; font-weight: 600; }
    .tl-time { font-size: 13px; }
    .timeline li.is-done, .timeline li.is-current { color: var(--ref-navy-text); }
    .timeline li.is-done .tl-dot { background: var(--ref-blue); border-color: var(--ref-blue); }
    .timeline li.is-current .tl-dot { background: var(--ref-blue); border-color: var(--ref-blue); box-shadow: 0 0 0 4px rgba(79,95,224,.2); }
    .timeline li.is-done .tl-time, .timeline li.is-current .tl-time { color: var(--ref-gray-text); }
    .timeline li.is-rejected { color: #9B2226; }
    .timeline li.is-rejected .tl-dot { background: #DC4C4C; border-color: #DC4C4C; }
    .timeline li.is-rejected .tl-time { color: #9B2226; }
    .tl-note { margin-top: 6px; font-size: 13.5px; color: var(--ref-navy-text); background: #FDF1F1; border-radius: 8px; padding: 8px 12px; }

    .btn-primary-ref { background: var(--ref-blue); border: 1px solid var(--ref-blue); color: #fff; border-radius: 8px; font-weight: 600; padding: 8px 16px; font-size: 13px; }
    .btn-primary-ref:hover { background: #3E4DCB; color: #fff; }
    .btn-primary-ref:disabled { background: #B9BFEE; border-color: #B9BFEE; }
    .btn-outline-ref { background: #fff; border: 1px solid var(--ref-border); color: var(--ref-navy-text); border-radius: 8px; font-weight: 600; padding: 8px 16px; font-size: 13px; }
    .btn-outline-ref:hover { border-color: var(--ref-blue); color: var(--ref-blue); }
    .btn-danger-ref { background: #fff; border: 1px solid #F0C4C4; color: #B02A2A; border-radius: 8px; font-weight: 600; padding: 8px 16px; font-size: 13px; margin-left: auto; }
    .btn-danger-ref:hover { background: #FDE6E6; }

    @media (max-width: 991.98px) {
      .admin-main { padding: 20px 16px 40px; }
    }
    @media (max-width: 575.98px) {
      .kv { grid-template-columns: 1fr; gap: 2px 0; }
      .kv dd { margin-bottom: 8px; }
    }
  </style>
</head>
<body>

  <div class="admin-layout">

    @include('partials.admin-sidebar')

  <!-- ===================== MAIN ===================== -->
  <main class="admin-main">
    <div class="admin-topbar">
      <h4>Document Requests</h4>
      @include('partials.admin-user')
    </div>
    <!-- Filters -->
    <div class="box filter-bar">
      <div class="search-wrap">
        <i class="ti ti-search"></i>
        <input type="search" class="form-control" id="searchInput" placeholder="Search by name, reference, or purpose" aria-label="Search requests">
      </div>
      <select class="form-select" id="typeFilter" aria-label="Filter by document type">
        <option value="all">All Documents</option>
      </select>
      <select class="form-select" id="rangeFilter" aria-label="Filter by date">
        <option value="all">Any Date</option>
        <option value="today">Today</option>
        <option value="7">Last 7 days</option>
        <option value="30">Last 30 days</option>
      </select>
      <button type="button" class="btn-clear" id="clearFilters">Clear filters</button>
    </div>

    <!-- Requests table -->
    <div class="box" style="overflow: hidden;">
      <div class="tabs" id="statusTabs" role="group" aria-label="Filter by status"></div>

      <div class="table-responsive">
        <table class="table req-table">
          <thead>
            <tr>
              <th>Reference</th>
              <th>Resident</th>
              <th>Document</th>
              <th class="d-none d-xl-table-cell">Details</th>
              <th class="d-none d-md-table-cell">Requested</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="requestRows"></tbody>
        </table>
      </div>

      <div class="table-foot" id="tableFoot"></div>
    </div>
  </main>
  </div>

  <!-- ===================== DETAIL DRAWER ===================== -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="requestDrawer" aria-labelledby="drawerTitle">
    <div class="offcanvas-header" id="drawerHeader"></div>
    <div class="offcanvas-body" id="drawerBody"></div>
    <div class="offcanvas-footer" id="drawerFooter"></div>
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
    /* ------------------------------------------------------------------
       Data comes from Admin\DocumentRequestController@index.
       Status changes POST to /admin/documents/{id} (@update), which checks
       the allowed move, saves it, adds a history row and writes audit_logs.
    ------------------------------------------------------------------ */
    const DOC_TYPES = @json($docTypes);
    const REQUESTS  = @json($requests);

    const STATUSES = [
      ['all', 'All'], ['pending', 'Pending'], ['processing', 'Processing'],
      ['ready', 'Ready'], ['completed', 'Completed'], ['rejected', 'Rejected']
    ];
    const filters = { status: 'all', type: 'all', range: 'all', q: '' };
    let currentId = null;

    const $ = (sel, el = document) => el.querySelector(sel);
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const isVacc = r => r.type === 'vaccination';
    const byId = id => REQUESTS.find(r => r.id === id);
    const requestedAt = r => r.log[0][1];
    const initials = name => name.split(' ').filter(Boolean).slice(0, 2).map(w => w[0]).join('').toUpperCase();

    /* ---------- labels & dates ---------- */
    function pillLabel(r) {
      if (r.status === 'ready') return isVacc(r) ? 'Scheduled' : 'Ready';
      if (r.status === 'completed') return isVacc(r) ? 'Vaccinated' : 'Completed';
      return { pending: 'Pending', processing: 'Processing', rejected: 'Rejected' }[r.status];
    }
    const pill = r => `<span class="pill st-${r.status}">${pillLabel(r)}</span>`;

    const dayStart = d => new Date(d.getFullYear(), d.getMonth(), d.getDate());
    const timeOf = d => d.toLocaleTimeString('en-PH', { hour: 'numeric', minute: '2-digit' });
    function fmtShort(iso) {
      const d = new Date(iso);
      const diff = Math.round((dayStart(new Date()) - dayStart(d)) / 86400000);
      if (diff === 0) return 'Today, ' + timeOf(d);
      if (diff === 1) return 'Yesterday';
      return d.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
    }
    const fmtFull = iso => {
      const d = new Date(iso);
      return d.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }) + ', ' + timeOf(d);
    };

    /* ---------- filtering ---------- */
    function inRange(iso) {
      if (filters.range === 'all') return true;
      const days = { today: 0, '7': 7, '30': 30 }[filters.range];
      const from = dayStart(new Date());
      from.setDate(from.getDate() - days);
      return new Date(iso) >= from;
    }
    function baseList() {
      const q = filters.q.trim().toLowerCase();
      return REQUESTS
        .filter(r => filters.type === 'all' || r.type === filters.type)
        .filter(r => inRange(requestedAt(r)))
        .filter(r => !q || [r.ref, r.resident, r.summary, DOC_TYPES[r.type]?.name].join(' ').toLowerCase().includes(q))
        .sort((a, b) => new Date(requestedAt(b)) - new Date(requestedAt(a)));
    }

    /* ---------- table ---------- */
    function render() {
      const base = baseList();
      const counts = { all: base.length };
      STATUSES.slice(1).forEach(([k]) => counts[k] = base.filter(r => r.status === k).length);

      $('#statusTabs').innerHTML = STATUSES.map(([k, label]) =>
        `<button type="button" class="tab ${filters.status === k ? 'active' : ''}" data-status="${k}" aria-pressed="${filters.status === k}">
           ${label}<span class="tab-count">${counts[k]}</span>
         </button>`).join('');

      const rows = filters.status === 'all' ? base : base.filter(r => r.status === filters.status);

      $('#requestRows').innerHTML = rows.length ? rows.map(r => {
        const t = DOC_TYPES[r.type];
        return `<tr data-id="${r.id}">
          <td class="ref">${esc(r.ref)}</td>
          <td><div class="cell-person"><div class="mini-avatar">${esc(initials(r.resident))}</div>
              <div><div class="cell-main">${esc(r.resident)}</div><div class="cell-sub">${esc(r.zone)}</div></div></div></td>
          <td><div class="cell-doc"><i class="ti ${t.icon}"></i>${esc(t.name)}</div></td>
          <td class="d-none d-xl-table-cell muted">${esc(r.summary)}</td>
          <td class="d-none d-md-table-cell muted">${fmtShort(requestedAt(r))}</td>
          <td>${pill(r)}</td>
          <td class="text-end"><button type="button" class="btn-view" data-open="${r.id}">View</button></td>
        </tr>`;
      }).join('') : `<tr><td colspan="7"><div class="empty">
          <i class="ti ti-file-search"></i>
          <strong>No requests match these filters</strong>
          Try a different status or date range, or <button type="button" class="btn-clear" data-clear>clear all filters</button>.
        </div></td></tr>`;

      $('#tableFoot').textContent = `Showing ${rows.length} of ${REQUESTS.length} requests`;

      const pending = REQUESTS.filter(r => r.status === 'pending').length;
      const badge = $('#pendingBadge');   // only if partials.admin-sidebar has it
      if (badge) badge.textContent = pending || '';
    }

    /* ---------- drawer ---------- */
    const STEPS = {
      doc:  [['pending', 'Request received'], ['processing', 'Being processed'], ['ready', 'Ready for pickup'], ['completed', 'Claimed by resident']],
      vacc: [['pending', 'Request received'], ['processing', 'Being reviewed'], ['ready', 'Schedule confirmed'], ['completed', 'Vaccinated']]
    };

    function timelineHtml(r) {
      const logMap = {};
      r.log.forEach(([s, at]) => logMap[s] = at);
      const last = r.log[r.log.length - 1][0];
      let steps = STEPS[isVacc(r) ? 'vacc' : 'doc'];
      if (r.status === 'rejected') steps = steps.filter(([k]) => logMap[k]);

      let html = steps.map(([k, label]) => {
        const at = logMap[k];
        const cls = at ? (k === last ? 'is-current' : 'is-done') : '';
        return `<li class="${cls}"><span class="tl-dot"></span><div class="tl-label">${label}</div>
                <div class="tl-time">${at ? fmtFull(at) : 'Not yet'}</div></li>`;
      }).join('');

      if (r.status === 'rejected') {
        html += `<li class="is-rejected"><span class="tl-dot"></span><div class="tl-label">Rejected</div>
                 <div class="tl-time">${fmtFull(logMap.rejected)}</div>
                 ${r.note ? `<div class="tl-note">${esc(r.note)}</div>` : ''}</li>`;
      }
      return html;
    }

    function drawerHeader(r) {
      const t = DOC_TYPES[r.type];
      $('#drawerHeader').innerHTML = `
        <div class="drawer-icon"><i class="ti ${t.icon}"></i></div>
        <div class="flex-grow-1">
          <h2 class="drawer-title" id="drawerTitle">${esc(t.name)}</h2>
          <div class="drawer-sub">${esc(r.ref)}, requested ${fmtFull(requestedAt(r))}</div>
          <div class="mt-2">${pill(r)}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>`;
    }

    function drawerBody(r) {
      const open = r.status === 'pending' || r.status === 'processing';
      const canNote = open || r.status === 'ready';
      const fileIcon = f => f.name.toLowerCase().endsWith('.pdf') ? 'ti-file-type-pdf' : 'ti-photo';

      $('#drawerBody').innerHTML = `
        <div class="d-section">
          <div class="person-row">
            <div class="mini-avatar">${esc(initials(r.resident))}</div>
            <div><div class="cell-main" style="font-size:16px">${esc(r.resident)}</div>
                 <div class="cell-sub">${esc(r.zone)}, ${esc(r.contact)}</div></div>
          </div>
        </div>

        <div class="d-section">
          <div class="d-heading">Request details</div>
          <dl class="kv">${Object.entries(r.details).map(([k, v]) => `<dt>${esc(k)}</dt><dd>${esc(v)}</dd>`).join('')}</dl>
        </div>

        ${r.files.length ? `<div class="d-section">
          <div class="d-heading">Uploaded files</div>
          ${r.files.map(f => `<div class="file-tile"><i class="ti ${fileIcon(f)}"></i>
              <span class="fname">${esc(f.name)}</span><a href="${esc(f.url)}" target="_blank" rel="noopener">View</a></div>`).join('')}
        </div>` : ''}

        ${r.type === 'indigency' && open ? `<div class="assess-box">
          <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" id="assessedCheck" ${r.assessed ? 'checked' : ''}>
            <label class="form-check-label" for="assessedCheck"><strong>Social worker assessment done.</strong>
              This must be checked before the certificate can be marked ready.</label>
          </div></div>` : ''}

        <div class="d-section">
          <div class="d-heading">Progress</div>
          <ul class="timeline">${timelineHtml(r)}</ul>
        </div>

        ${canNote ? `<div class="d-section">
          <label class="d-heading" for="noteInput">Note to resident</label>
          <textarea class="form-control" id="noteInput" rows="3" style="border-radius:8px"
            placeholder="Optional. Required if you reject this request.">${esc(r.note)}</textarea>
          <div class="invalid-feedback">Add a reason so the resident knows what to fix.</div>
        </div>` : ''}`;
    }

    function drawerFooter(r) {
      const vacc = isVacc(r);
      const readyBlocked = r.type === 'indigency' && !r.assessed;
      let html = '';

      if (r.status === 'pending') {
        html = `<button class="btn-primary-ref" data-act="processing">${vacc ? 'Start review' : 'Start processing'}</button>
                <button class="btn-danger-ref" data-act="rejected">Reject</button>`;
      } else if (r.status === 'processing') {
        html = `<button class="btn-primary-ref" data-act="ready" ${readyBlocked ? 'disabled title="Complete the social worker assessment first"' : ''}>
                  ${vacc ? 'Confirm schedule' : 'Mark ready for pickup'}</button>
                <button class="btn-danger-ref" data-act="rejected">Reject</button>`;
      } else if (r.status === 'ready') {
        html = `<button class="btn-primary-ref" data-act="completed">${vacc ? 'Mark as vaccinated' : 'Mark as claimed'}</button>
                ${vacc ? '' : '<button class="btn-outline-ref" data-act="print"><i class="ti ti-printer me-1"></i>Print</button>'}`;
      } else if (r.status === 'completed' && !vacc) {
        html = `<button class="btn-outline-ref" data-act="print"><i class="ti ti-printer me-1"></i>Print copy</button>`;
      }
      html += `<button class="btn-outline-ref ${html ? '' : 'ms-auto'}" data-bs-dismiss="offcanvas">Close</button>`;
      $('#drawerFooter').innerHTML = html;
    }

    function buildDrawer() {
      const r = byId(currentId);
      drawerHeader(r); drawerBody(r); drawerFooter(r);
    }

    const drawer = new bootstrap.Offcanvas($('#requestDrawer'));
    function openRequest(id) { currentId = id; buildDrawer(); drawer.show(); }

    /* ---------- actions ---------- */
    const TOAST_TEXT = {
      processing: 'moved to Processing',
      ready: 'marked ready',
      completed: 'marked completed',
      rejected: 'rejected'
    };
    function toast(msg) {
      $('#toastMsg').textContent = msg;
      bootstrap.Toast.getOrCreateInstance($('#appToast')).show();
    }

    function act(action, btn) {
      const unlock = () => { if (btn) btn.disabled = false; };
      const r = byId(currentId);

      if (action === 'print') { toast(r.ref + ' sent to print'); return; }

      if (action === 'rejected' && !r.note.trim()) {
        const box = $('#noteInput');
        box.classList.add('is-invalid');
        box.focus();
        unlock();
        return;
      }
      if (action === 'ready' && r.type === 'indigency' && !r.assessed) { unlock(); return; }

      fetch("{{ url('/admin/documents') }}/" + r.id, { method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ status: action, note: r.note, assessed: r.assessed }) })
        .then(res => res.json().then(b => ({ ok: res.ok, b })))
        .then(({ ok, b }) => {
          if (!ok) { toast(b.message || 'Could not update.'); unlock(); return; }
          Object.assign(r, b.request);
          render(); buildDrawer();
          toast(r.ref + ' ' + TOAST_TEXT[action]);
        })
        .catch(() => { toast('Could not reach the server.'); unlock(); });
    }

    /* ---------- events ---------- */
    $('#statusTabs').addEventListener('click', e => {
      const b = e.target.closest('[data-status]');
      if (b) { filters.status = b.dataset.status; render(); }
    });
    $('#requestRows').addEventListener('click', e => {
      if (e.target.closest('[data-clear]')) { clearFilters(); return; }
      const tr = e.target.closest('tr[data-id]');
      if (tr) openRequest(Number(tr.dataset.id));
    });
    $('#drawerFooter').addEventListener('click', e => {
      const b = e.target.closest('[data-act]');
      if (!b || b.disabled) return;
      if (b.dataset.act !== 'print') b.disabled = true;
      act(b.dataset.act, b);
    });
    $('#drawerBody').addEventListener('input', e => {
      if (e.target.id === 'noteInput') {
        byId(currentId).note = e.target.value;
        if (e.target.value.trim()) e.target.classList.remove('is-invalid');
      }
    });
    $('#drawerBody').addEventListener('change', e => {
      if (e.target.id === 'assessedCheck') {
        byId(currentId).assessed = e.target.checked;
        drawerFooter(byId(currentId));
      }
    });

    Object.entries(DOC_TYPES).forEach(([k, t]) => $('#typeFilter').insertAdjacentHTML('beforeend', `<option value="${k}">${esc(t.name)}</option>`));
    $('#typeFilter').addEventListener('change', e => { filters.type = e.target.value; render(); });
    $('#rangeFilter').addEventListener('change', e => { filters.range = e.target.value; render(); });
    $('#searchInput').addEventListener('input', e => { filters.q = e.target.value; render(); });

    function clearFilters() {
      Object.assign(filters, { status: 'all', type: 'all', range: 'all', q: '' });
      $('#typeFilter').value = 'all'; $('#rangeFilter').value = 'all'; $('#searchInput').value = '';
      render();
    }
    $('#clearFilters').addEventListener('click', clearFilters);

    render();
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
<link href="{{ asset('css/pabarang-admin.css') }}" rel="stylesheet">
</body>
</html>