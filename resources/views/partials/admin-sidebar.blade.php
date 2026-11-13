<link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  /* Same font as the resident pages. Icons keep their own icon font. */
  html body { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif !important; }
  html body h1, html body h2, html body h3, html body h4, html body h5, html body h6,
  html body .btn, html body input, html body select, html body textarea, html body button,
  html body .modal, html body .dropdown-menu, html body .table, html body .badge {
    font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif;
  }
</style>
{{-- Admin sidebar, shared by every admin page: @include('partials.admin-sidebar')
     Links depend on the official's role ($me comes from EnsureAdmin). --}}
@php
  $role = $me['role'] ?? '';
  $full = in_array($role, ['captain', 'secretary'], true);
  $on = fn ($path) => request()->is('admin/' . $path) || request()->is('admin/' . $path . '/*') ? 'active' : '';
  $pending = $full ? \App\Models\Resident::where('status', 'pending')->count() : 0;
@endphp

<style>
  .sidebar-link .count {
    margin-left: auto; min-width: 22px; padding: 1px 8px; border-radius: 999px;
    background: #F5B83D; color: #3A2900; font-size: 11.5px; font-weight: 700; text-align: center;
  }
  .sidebar-link .count:empty { display: none; }
  .sidebar-me { margin: 18px 6px 4px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.08); }
  .sidebar-me .me-name { color: #fff; font-size: 13.5px; font-weight: 700; }
  .sidebar-me .me-pos { color: #7A80A8; font-size: 12px; }

  /* ===== Phones and tablets: sidebar becomes a slide-in menu ===== */
  .admin-mobile-topbar, .admin-backdrop { display: none; }

  @media (max-width: 991.98px) {
    .admin-sidebar {
      position: fixed !important; top: 0; bottom: 0; left: 0; z-index: 1050;
      width: 270px !important; max-width: 85vw; overflow-y: auto;
      transform: translateX(-100%); transition: transform 0.25s ease-out;
    }
    .admin-sidebar.open { transform: translateX(0); box-shadow: 0 0 40px rgba(0, 0, 0, 0.5); }
    .admin-mobile-topbar {
      display: flex; align-items: center; gap: 12px;
      position: fixed; top: 0; left: 0; right: 0; height: 56px; padding: 0 14px;
      background: #0E1430; color: #fff; z-index: 1040;
    }
    .admin-menu-btn {
      width: 44px; height: 44px; border: 0; border-radius: 9px; background: transparent;
      color: #fff; font-size: 22px; display: flex; align-items: center; justify-content: center;
    }
    .admin-menu-btn:active { background: #182049; }
    .admin-mobile-brand { font-weight: 700; font-size: 15px; }
    .admin-backdrop.show { display: block; position: fixed; inset: 0; background: rgba(4, 6, 18, 0.6); z-index: 1045; }
    body.admin-menu-open { overflow: hidden; }

    /* Page area uses the full width under the top bar */
    .admin-layout { display: block !important; }
    .admin-main { padding: 72px 16px 24px !important; min-width: 0; }
    .admin-topbar { flex-wrap: wrap; gap: 10px; }
    .stat-strip { flex-wrap: wrap; }  /* tablets too, not only phones */
    .admin-main canvas { max-width: 100% !important; }

    /* Concern Map: map first, list underneath */
    .map-layout { flex-direction: column; }
    .map-side-list { width: 100% !important; max-height: 300px; order: 2; }
    .map-container { flex: none !important; }
    #fullConcernMap { min-height: 440px !important; }
  }

  @media (max-width: 767.98px) {
    .admin-topbar h4 { font-size: 20px; }
    /* Stat numbers: two per row instead of one long row */
    .stat-strip { flex-wrap: wrap; }
    .stat-strip .stat-mini { flex: 1 1 calc(50% - 14px); }
    /* Filter bars: full-width selects, easy to tap */
    .filter-bar > select, .filter-bar > input { width: 100% !important; min-height: 44px; }
    /* Concern cards: badges drop under the title */
    .concern-card { flex-wrap: wrap; }
    .concern-card .flex-grow-1 { flex-basis: calc(100% - 60px); }
    /* Wide tables scroll sideways instead of squeezing */
    .admin-main table { display: block; overflow-x: auto; white-space: nowrap; }
    /* ...except calendar grids, which must stay real tables */
    .admin-main .fc table, .admin-main table[class*="fc-"] { display: table; white-space: normal; overflow: visible; }
    /* Bigger tap targets; 16px text stops iPhones zooming into inputs */
    .admin-main .btn-sm { min-height: 38px; }
    .admin-main input, .admin-main select, .admin-main textarea { font-size: 16px !important; }
    .modal-dialog { margin: 8px; }
  }

  /* ===== Phone safety net (Tugon mobile pass, Nov 7) =====
     Real data can be longer than the sample data: long emails, tracking codes,
     notice titles, wide tables. These keep any page from sliding sideways. */
  @media (max-width: 767.98px) {
    html { overflow-x: clip; }
    .admin-main { overflow-wrap: break-word; }
    /* Long words (emails, codes) in text blocks break instead of pushing the row wider.
       Buttons, pills and badges are left alone. */
    .admin-main :is(h1, h2, h3, h4, h5, h6, p, dd, li, td, label, [class*="title"], [class*="meta"], [class*="desc"], [class*="value"], [class*="name"], [class*="note"], .greet-place, .greet-date):not(.btn):not(.badge):not([class*="pill"]):not([class*="tag"]) { overflow-wrap: anywhere; }
    .admin-main .min-w-0, .admin-main .flex-grow-1 { min-width: 0; }
    .admin-main img, .admin-main video, .admin-main iframe { max-width: 100%; }
    .admin-main canvas { max-width: 100% !important; }
    .admin-main pre, .admin-main code { white-space: pre-wrap; word-break: break-word; }
    .admin-main .table-responsive, .admin-main .table-wrap { -webkit-overflow-scrolling: touch; }
    .admin-main .d-flex:not(.flex-nowrap) > .btn { white-space: normal; }
    .admin-main .btn-group { flex-wrap: wrap; }
    /* Pop-up windows: fill the phone width with a small margin, buttons wrap */
    .modal-dialog { margin: .75rem; max-width: none; }
    .modal-dialog-centered { min-height: calc(100% - 1.5rem); }
    .modal-body { overflow-wrap: break-word; }
    .modal-footer { flex-wrap: wrap; gap: .5rem; }
    .modal-footer > * { margin: 0; }
    .modal-footer > .btn { flex: 1 1 auto; }
    /* Calendar toolbar: buttons wrap under the month name */
    .fc .fc-toolbar { flex-wrap: wrap; gap: .5rem; }
    .fc .fc-toolbar-title { font-size: 1.05rem !important; }
    /* Dropdowns and toasts stay on screen */
    .dropdown-menu { max-width: calc(100vw - 1.5rem); }
    .toast-container, .toast { max-width: calc(100vw - 1.5rem); }
  }
</style>

{{-- Phones only: top bar with the menu button, and the dim layer behind the open menu --}}
<div class="admin-mobile-topbar">
  <button type="button" class="admin-menu-btn" id="adminMenuOpen" aria-label="Open menu"
          aria-controls="adminSidebar" aria-expanded="false">
    <i class="ti ti-menu-2"></i>
  </button>
  <span class="admin-mobile-brand d-inline-flex align-items-center gap-2"><span style="width:26px;height:26px;display:inline-flex">@include('partials.tugon-logo')</span>Tugon Admin</span>
</div>
<div class="admin-backdrop" id="adminBackdrop"></div>

<div class="admin-sidebar" id="adminSidebar">
  <div class="brand">
    <div class="brand-logo-lg" style="overflow:hidden;">
      @include('partials.tugon-logo')
    </div>
    <span class="brand-name">Tugon Admin</span>
  </div>

  <div class="sidebar-label">Overview</div>
  <a href="{{ url('/admin/dashboard') }}" class="sidebar-link {{ $on('dashboard') }}"><i class="ti ti-layout-dashboard"></i> Dashboard</a>
  <a href="{{ url('/admin/calendar') }}" class="sidebar-link {{ $on('calendar') }}"><i class="ti ti-calendar-event"></i> Calendar</a>

  <div class="sidebar-label">Manage</div>
  <a href="{{ url('/admin/concerns') }}" class="sidebar-link {{ $on('concerns') }}"><i class="ti ti-alert-triangle"></i> Concerns</a>
  <a href="{{ url('/admin/map') }}" class="sidebar-link {{ $on('map') }}"><i class="ti ti-map-pin"></i> Concern Map</a>
  @if ($full)
    <a href="{{ url('/admin/documents') }}" class="sidebar-link {{ $on('documents') }}"><i class="ti ti-file-text"></i> Document Requests</a>
  @endif
  <a href="{{ url('/admin/blotters') }}" class="sidebar-link {{ $on('blotters') }}"><i class="ti ti-clipboard-text"></i> Blotter Reports</a>
  @if ($full)
    <a href="{{ url('/admin/announcements') }}" class="sidebar-link {{ $on('announcements') }}"><i class="ti ti-speakerphone"></i> Announcements</a>
    <a href="{{ url('/admin/officials') }}" class="sidebar-link {{ $on('officials') }}"><i class="ti ti-users"></i> Officials</a>
    <a href="{{ url('/admin/accounts') }}" class="sidebar-link {{ $on('accounts') }}">
      <i class="ti ti-user-check"></i> Resident Accounts<span class="count" id="pendingBadge">{{ $pending ?: '' }}</span>
    </a>
  @endif

  <div class="sidebar-label">System</div>
  @if ($full)
    <a href="{{ url('/admin/audit-log') }}" class="sidebar-link {{ $on('audit-log') }}"><i class="ti ti-history"></i> Audit Log</a>
  @endif
  @if ($role === 'captain')
    <a href="{{ url('/admin/official-accounts') }}" class="sidebar-link {{ $on('official-accounts') }}"><i class="ti ti-shield-lock"></i> Official Accounts</a>
  @endif
  <a href="{{ url('/admin/password') }}" class="sidebar-link {{ $on('password') }}"><i class="ti ti-key"></i> Change Password</a>
  <a href="{{ url('/admin/logout') }}" class="sidebar-link"><i class="ti ti-logout"></i> Log Out</a>

  <div class="sidebar-me">
    <div class="me-name">{{ $me['name'] ?? '' }}</div>
    <div class="me-pos">{{ $me['position'] ?? '' }}</div>
  </div>
</div>

<script>
// Phone menu: open with the top-bar button; close with the dim layer, a link, Escape, or a wider screen.
(function () {
  var sidebar = document.getElementById('adminSidebar');
  var backdrop = document.getElementById('adminBackdrop');
  var btn = document.getElementById('adminMenuOpen');
  if (!sidebar || !backdrop || !btn) return;
  function setOpen(open) {
    sidebar.classList.toggle('open', open);
    backdrop.classList.toggle('show', open);
    document.body.classList.toggle('admin-menu-open', open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  btn.addEventListener('click', function () { setOpen(!sidebar.classList.contains('open')); });
  backdrop.addEventListener('click', function () { setOpen(false); });
  sidebar.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', function () { setOpen(false); }); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
  window.addEventListener('resize', function () { if (window.innerWidth >= 992) setOpen(false); });
})();
</script>
