{{--
  Shared resident sidebar. Include on every resident page:
      @include('partials.resident-sidebar')
  Colors match the Tugon landing page. The active link comes from the URL.
--}}
@php
  $groupRoutes = [
    'report'    => ['submit-concern', 'my-reports', 'emergency-hotlines'],
    'services'  => ['services', 'request-document'],
    'community' => ['calendar', 'announcements', 'officials', 'faq'],
  ];
  $residentName = session('resident.full_name', 'Resident');
  $residentInitial = mb_strtoupper(mb_substr($residentName, 0, 1));
  $onAccountPage = request()->is('profile') || request()->is('settings');
  $openGroup = null;
  foreach ($groupRoutes as $group => $routes) {
      foreach ($routes as $r) {
          if (request()->is($r)) { $openGroup = $group; break 2; }
      }
  }
@endphp

{{-- Settings page choices (Larger Text, theme, Reduce motion). Runs before the page
     content is drawn, so a dark page never flashes white first. --}}
<script>
  (function () {
    var html = document.documentElement;
    function get(k) { try { return localStorage.getItem(k); } catch (e) { return null; } }
    if (get('pabarang.largeText') === '1') html.classList.add('large-text');
    if (get('pabarang.reduceMotion') === '1') html.classList.add('reduce-motion');

    // Theme: light, dark, or system (follows the phone or computer setting)
    var media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
    window.pbApplyTheme = function (choice) {
      choice = choice || get('pabarang.theme') || 'light';
      var dark = choice === 'dark' || (choice === 'system' && media && media.matches);
      html.setAttribute('data-theme', dark ? 'dark' : 'light');
      html.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
    };
    window.pbApplyTheme();
    document.title = document.title.replace(/Tugon|Tugon/g, 'Tugon');
    if (media && media.addEventListener) media.addEventListener('change', function () { window.pbApplyTheme(); });
  })();
</script>

<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
  /* Sidebar */
  :root { --navy: #0B1026; --navy-2: #131A3D; --navy-active: #3D4ED1; }
  .sidebar {
    width: 250px; background: var(--navy); color: #AEB6DA; min-height: 100vh;
    position: fixed; top: 0; left: 0; padding: 22px 16px; overflow-y: auto;
  }
  .sidebar .brand { color: #fff; font-family: 'Host Grotesk', sans-serif; font-weight: 800; font-size: 17px; }
  .brand-logo-lg {
    width: 34px; height: 34px; border-radius: 9px;
    background: transparent;
    display: flex; align-items: center; justify-content: center;
    color: #14183A; font-weight: 800; font-size: 15px; overflow: hidden; position: relative; flex-shrink: 0;
  }
  .brand-logo-lg img { width: 100%; height: 100%; object-fit: cover; }
  .brand-logo-fallback { display: none; width: 100%; height: 100%; align-items: center; justify-content: center; }
  .sidebar .nav-label { font-size: 12px; font-weight: 700; color: #6F79A8; margin: 16px 6px 8px; }
  .sidebar .nav-link { color: #AEB6DA; font-size: 14px; border-radius: 8px; padding: 9px 12px; display: block; transition: background-color .15s, color .15s; }
  .sidebar .nav-link:hover { background: var(--navy-2); color: #fff; }
  /* Active page: a lighter navy with a gold edge, no glow */
  .sidebar .nav-link.active { background: #1C2657; color: #fff; box-shadow: inset 3px 0 0 #3FB37F; font-weight: 700; }
  .sidebar .nav-link i, .sidebar .brand-logo-lg i { display: inline-block; width: 18px; text-align: center; }

  .sidebar .collapsing { transition: height 0.2s ease-out !important; }

  .nav-group-toggle {
    display: flex; align-items: center; justify-content: space-between;
    width: 100%; background: none; border: none; text-align: left;
    font-size: 12px; font-weight: 700; color: #6F79A8; margin: 16px 0 8px; padding: 2px 6px;
    cursor: pointer;
  }
  .nav-group-toggle:hover { color: #AEB6DA; }
  .nav-group-toggle .chevron { font-size: 13px; transition: transform 0.2s ease-out; }
  .nav-group-toggle[aria-expanded="true"] .chevron { transform: rotate(180deg); }

  /* ===== Account button pinned to the bottom ===== */
  .sidebar { padding-bottom: 0; }
  .sidebar-account {
    margin-top: auto; position: sticky; bottom: 0; background: var(--navy);
    padding: 12px 0 16px; border-top: 1px solid var(--navy-2);
  }
  .account-btn {
    width: 100%; display: flex; align-items: center; gap: 10px; padding: 8px 10px;
    background: none; border: 0; border-radius: 10px; color: #AEB6DA; text-align: left; cursor: pointer;
  }
  .account-btn:hover, .account-btn.open, .account-btn.active { background: var(--navy-2); color: #fff; }
  .account-avatar {
    width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0; background: var(--navy-active);
    color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;
  }
  .account-text { display: flex; flex-direction: column; min-width: 0; flex: 1; line-height: 1.25; }
  .account-name { font-size: 14px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .account-role { font-size: 11.5px; color: #5C6694; }
  .account-chevron { font-size: 15px; transition: transform 0.2s ease-out; }
  .account-btn.open .account-chevron { transform: rotate(180deg); }

  .account-popup {
    display: none; position: absolute; left: 0; right: 0; bottom: 100%; margin-bottom: 8px;
    background: var(--navy-2); border-radius: 12px; padding: 6px; box-shadow: 0 8px 28px rgba(0, 0, 0, 0.45);
  }
  .account-popup.show { display: block; }
  .account-item {
    display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 8px;
    color: #AEB6DA; font-size: 14px; text-decoration: none;
  }
  .account-item:hover { background: #1C2452; color: #fff; }
  .account-item.active { background: var(--navy-active); color: #fff; }
  .account-item.logout { color: #ff9a9a; }
  .account-item.logout:hover { background: #3A2440; color: #ffb3b3; }
  .account-item i { width: 18px; text-align: center; }


  /* ===== Shared look for every resident page =====
     Font: Host Grotesk for headings and text.
     Each page keeps its own layout. These rules only even out the type, colors,
     cards and headers so all pages feel like one app. The html[data-theme] prefix
     makes them win over the page styles and pabarang.css without editing each page. */
  html[data-theme="light"] {
    --ref-blue: #3D4ED1 !important; --ref-navy-text: #18213F !important; --ref-gray-text: #5E6681 !important;
    --ref-bg: #F2F3F7 !important; --ref-border: #DDE1EA !important;
  }
  html[data-theme] body { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif !important; }
  html[data-theme] .main h1, html[data-theme] .main h2, html[data-theme] .main h3, html[data-theme] .main h4,
  html[data-theme] .main h5, html[data-theme] .modal-title, html[data-theme] .fc .fc-toolbar-title {
    font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif; letter-spacing: -0.01em; color: var(--ref-navy-text);
  }

  /* One page title instead of two: the small grey top bar repeated the big heading */
  html[data-theme] .top-bar { display: none !important; }
  html[data-theme] .page-wrap { padding-top: 32px; }
  html[data-theme] .page-wrap > h3:first-child { font-size: 1.75rem; font-weight: 800 !important; margin-bottom: .35rem !important; }

  /* Labels in normal case, not small capitals */
  html[data-theme] .section-label, html[data-theme] .faq-section-label, html[data-theme] .section-title {
    text-transform: none !important; letter-spacing: 0 !important; font-size: 15px !important; color: var(--ref-navy-text) !important;
  }
  html[data-theme] .fc .fc-col-header-cell-cushion { text-transform: none !important; letter-spacing: 0 !important; font-size: 12.5px !important; }
  html[data-theme] .category-tag, html[data-theme] .news-tag, html[data-theme] .announce-modal-tag { letter-spacing: 0 !important; }

  /* Cards: one radius, one border, no floating shadows. A hover only changes the border. */
  html[data-theme] .card-b, html[data-theme] .announcement-card, html[data-theme] .report-card, html[data-theme] .service-row,
  html[data-theme] .hotline-card, html[data-theme] .doc-card, html[data-theme] .official-card, html[data-theme] .profile-card,
  html[data-theme] .settings-card, html[data-theme] .form-card, html[data-theme] .box, html[data-theme] .chart-card,
  html[data-theme] .stat-card, html[data-theme] .accordion-item {
    border-radius: 12px !important; box-shadow: none !important; border-color: var(--ref-border);
    transition: border-color .15s ease, background-color .15s ease;
  }
  html[data-theme] .report-card:hover, html[data-theme] a.hotline-card:hover, html[data-theme] .service-row:hover,
  html[data-theme] .doc-card:hover, html[data-theme] .action-tile:hover, html[data-theme] .req-row:hover, html[data-theme] .news-card:hover {
    border-color: var(--ref-blue) !important; box-shadow: none !important;
  }
  html[data-theme] .accordion { --bs-accordion-border-radius: 12px; --bs-accordion-inner-border-radius: 11px; }
  html[data-theme] .accordion-item + .accordion-item { margin-top: 8px; border-top: 1px solid var(--ref-border); }
  html[data-theme] .accordion-button:not(.collapsed) { box-shadow: none; }

  /* Filter pills and tabs: navy when picked */
  html[data-theme] .filter-pill { transition: background-color .15s, color .15s, border-color .15s; }
  html[data-theme] .filter-pill:hover { border-color: var(--ref-navy-text); color: var(--ref-navy-text); }
  html[data-theme="light"] .filter-pill.active { background: #13204A !important; border-color: #13204A !important; color: #fff !important; }

  /* Buttons and fields */
  html[data-theme] .main .btn, html[data-theme] .btn-request, html[data-theme] .btn-brand { border-radius: 8px; }
  html[data-theme] .btn-request, html[data-theme] .btn-brand { transition: background-color .15s, transform .1s; }
  html[data-theme] .btn-request:active, html[data-theme] .btn-brand:active, html[data-theme] .main .btn:active { transform: scale(.98); }
  html[data-theme] .main .form-control:focus, html[data-theme] .main .form-select:focus, html[data-theme] .modal .form-control:focus, html[data-theme] .modal .form-select:focus {
    border-color: var(--ref-blue); box-shadow: 0 0 0 3px rgba(61,78,209,.15);
  }
  html[data-theme] .modal-content { border-radius: 14px; border: 0; }
  html[data-theme] a:focus-visible, html[data-theme] button:focus-visible, html[data-theme] [role="button"]:focus-visible { outline: 3px solid #3D4ED1; outline-offset: 2px; }

  /* One quiet entrance when a page opens */
  @keyframes pbIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
  html[data-theme] .page-wrap, html[data-theme] .main > .bento, html[data-theme] .main > .dash-head { animation: pbIn .35s ease-out both; }
  @media (prefers-reduced-motion: reduce) { html[data-theme] .page-wrap, html[data-theme] .main > .bento { animation: none; } }

  /* ===== Mobile / tablet drawer ===== */
  .mobile-topbar, .sidebar-backdrop { display: none; }

  @media (max-width: 991.98px) {
    .sidebar {
      bottom: 0; min-height: 0; width: 270px; max-width: 85vw; z-index: 1050;
      transform: translateX(-100%); transition: transform 0.25s ease-out;
    }
    .sidebar.open { transform: translateX(0); box-shadow: 0 0 40px rgba(0, 0, 0, 0.5); }
    .main { margin-left: 0 !important; padding-top: 56px !important; }
    .mobile-topbar {
      display: flex; align-items: center; gap: 12px;
      position: fixed; top: 0; left: 0; right: 0; height: 56px; padding: 0 14px;
      background: var(--navy); color: #fff; z-index: 1040;
      border-bottom: 1px solid var(--navy-2);
    }
    .mobile-menu-btn {
      width: 40px; height: 40px; border: 0; border-radius: 9px; background: transparent;
      color: #fff; font-size: 22px; display: flex; align-items: center; justify-content: center;
    }
    .mobile-menu-btn:active { background: var(--navy-2); }
    .mobile-brand { font-weight: 700; font-size: 15px; }
    .sidebar-backdrop.show {
      display: block; position: fixed; inset: 0; background: rgba(4, 6, 18, 0.6); z-index: 1045;
    }
    body.sidebar-open { overflow: hidden; }
  }

  @media (max-width: 767.98px) {
    .top-bar { padding: 14px 16px 0 !important; }
    .page-wrap { padding: 20px 14px !important; }
    .profile-card, .settings-card { padding: 20px !important; }
    .service-row { flex-wrap: wrap; }
    .service-row .service-link, .service-row .service-badge { margin-left: 62px; }
    .hotline-card { flex-wrap: wrap; }
    .hotline-number { margin-left: 56px; }
    /* 16px text stops iPhones zooming into a field; taller targets are easier to tap */
    .main input, .main select, .main textarea { font-size: 16px !important; }
    .main .form-control, .main .form-select { min-height: 44px; }
    .main .btn { min-height: 44px; }
    .sidebar .nav-link { padding: 11px 12px; }
  }


  /* ===== Phone safety net (Tugon mobile pass, Nov 7) =====
     Real data can be longer than the sample data: long emails, tracking codes,
     notice titles, wide tables. These keep any page from sliding sideways. */
  @media (max-width: 767.98px) {
    html { overflow-x: clip; }
    .main { overflow-wrap: break-word; }
    /* Long words (emails, codes) in text blocks break instead of pushing the row wider.
       Buttons, pills and badges are left alone. */
    .main :is(h1, h2, h3, h4, h5, h6, p, dd, li, td, label, [class*="title"], [class*="meta"], [class*="desc"], [class*="value"], [class*="name"], [class*="note"], .greet-place, .greet-date):not(.btn):not(.badge):not([class*="pill"]):not([class*="tag"]) { overflow-wrap: anywhere; }
    .main .min-w-0, .main .flex-grow-1 { min-width: 0; }
    .main img, .main video, .main iframe { max-width: 100%; }
    .main canvas { max-width: 100% !important; }
    .main pre, .main code { white-space: pre-wrap; word-break: break-word; }
    .main .table-responsive, .main .table-wrap { -webkit-overflow-scrolling: touch; }
    .main .d-flex:not(.flex-nowrap) > .btn { white-space: normal; }
    .main .btn-group { flex-wrap: wrap; }
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
  /* ===== Larger Text (Settings > Accessibility) ===== */
  @media (min-width: 992px) {
    html.large-text .main { zoom: 1.15; margin-left: calc(250px / 1.15) !important; }
  }
  @media (max-width: 991.98px) {
    html.large-text .main { zoom: 1.15; padding-top: calc(56px / 1.15) !important; }
  }
  html.large-text .modal-dialog { zoom: 1.15; }

  /* ===== Reduce motion (Settings > Accessibility) ===== */
  html.reduce-motion *, html.reduce-motion *::before, html.reduce-motion *::after {
    animation-duration: 0.001ms !important; animation-iteration-count: 1 !important;
    transition-duration: 0.001ms !important; scroll-behavior: auto !important;
  }

  /* ===== Dark theme (Settings > Appearance) =====
     Bootstrap 5.3 darkens its own parts through data-bs-theme="dark". These rules
     darken the page colors and the white cards the resident pages draw themselves.
     !important is needed because pabarang.css sets the same colors with !important. */
  html[data-theme="dark"] {
    --ref-bg: #0E1324 !important; --ref-border: #283052 !important;
    --ref-navy-text: #E6E9F5 !important; --ref-gray-text: #9AA3C7 !important;
    --ref-tint: #1B2347 !important; --ref-tint-border: #2F3A6E !important;
    color-scheme: dark;
  }
  html[data-theme="dark"] body { background: var(--ref-bg) !important; color: var(--ref-navy-text) !important; }
  html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) [class*="-card"], html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .card,
  html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .optional-box, html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .drop,
  html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .cat-option .btn-check + label, html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .id-card,
  html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .lock-note, html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .list-group-item {
    background-color: #161C33 !important; border-color: var(--ref-border) !important; color: var(--ref-navy-text);
  }
  html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .cat-option .btn-check:checked + label { background-color: #3D4ED1 !important; color: #fff !important; }
  html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .text-muted, html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .hint { color: var(--ref-gray-text) !important; }
  html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .table { --bs-table-bg: transparent; color: var(--ref-navy-text); }
  html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .privacy-banner { background: #1B2347 !important; border-color: #2F3A6E !important; color: var(--ref-navy-text) !important; }
  html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) .urgent-banner { background: #3A1A22 !important; border-color: #6B2A36 !important; color: #FFB3B3 !important; }
  html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) a:not(.btn):not(.nav-link):not(.call-btn):not(.hotline-card):not(.sos-bar) { color: #8F9BFF; }
  html[data-theme="dark"] body > :not(.sidebar):not(.mobile-topbar) img { opacity: .95; }
  /* Added by the dark-mode scanner below to boxes and text with fixed light colors */
  html[data-theme="dark"] .pb-dk-bg { background-color: #161C33 !important; border-color: var(--ref-border) !important; }
  html[data-theme="dark"] .pb-dk-bg2 { background-color: #1D2442 !important; border-color: var(--ref-border) !important; }
  html[data-theme="dark"] .pb-dk-fg { color: #E6E9F5 !important; }
  html[data-theme="dark"] .pb-dk-muted { color: #9AA3C7 !important; }
  html[data-theme="dark"] .pb-dk-line { border-color: var(--ref-border) !important; }
</style>

{{-- Mobile-only header bar + dim backdrop --}}
<div class="mobile-topbar">
  <button type="button" class="mobile-menu-btn" id="sidebarOpen" aria-label="Open menu"
          aria-controls="residentSidebar" aria-expanded="false">
    <i class="ti ti-menu-2"></i>
  </button>
  <span class="mobile-brand d-inline-flex align-items-center gap-2"><span style="width:26px;height:26px;display:inline-flex">@include('partials.tugon-logo')</span>Tugon</span>
</div>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<nav class="sidebar d-flex flex-column" id="residentSidebar">
  <div class="brand mb-4 d-flex align-items-center gap-2">
    <div class="brand-logo-lg">
      @include('partials.tugon-logo')
    </div>
    Tugon
  </div>

  <div class="nav-label">Overview</div>
  <a class="nav-link {{ request()->is('home') ? 'active' : '' }}" href="{{ url('/home') }}">
    <i class="ti ti-layout-dashboard me-2"></i>Dashboard
  </a>

  <button class="nav-group-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#group-report"
          aria-expanded="{{ $openGroup === 'report' ? 'true' : 'false' }}">
    <span>Report</span><i class="ti ti-chevron-down chevron"></i>
  </button>
  <div class="collapse {{ $openGroup === 'report' ? 'show' : '' }}" id="group-report">
    <a class="nav-link {{ request()->is('submit-concern') ? 'active' : '' }}" href="{{ url('/submit-concern') }}"><i class="ti ti-file-plus me-2"></i>Submit a Concern</a>
    <a class="nav-link {{ request()->is('my-reports') ? 'active' : '' }}" href="{{ url('/my-reports') }}"><i class="ti ti-list-check me-2"></i>My Reports &amp; Status</a>
    <a class="nav-link {{ request()->is('emergency-hotlines') ? 'active' : '' }}" href="{{ url('/emergency-hotlines') }}"><i class="ti ti-phone-call me-2"></i>Emergency Hotlines</a>
  </div>

  <button class="nav-group-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#group-services"
          aria-expanded="{{ $openGroup === 'services' ? 'true' : 'false' }}">
    <span>Services</span><i class="ti ti-chevron-down chevron"></i>
  </button>
  <div class="collapse {{ $openGroup === 'services' ? 'show' : '' }}" id="group-services">
    <a class="nav-link {{ request()->is('services') ? 'active' : '' }}" href="{{ url('/services') }}"><i class="ti ti-apps me-2"></i>Browse All Services</a>
    <a class="nav-link {{ request()->is('request-document') ? 'active' : '' }}" href="{{ url('/request-document') }}"><i class="ti ti-file-text me-2"></i>Request a Document</a>
  </div>

  <button class="nav-group-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#group-community"
          aria-expanded="{{ $openGroup === 'community' ? 'true' : 'false' }}">
    <span>Community</span><i class="ti ti-chevron-down chevron"></i>
  </button>
  <div class="collapse {{ $openGroup === 'community' ? 'show' : '' }}" id="group-community">
    <a class="nav-link {{ request()->is('calendar') ? 'active' : '' }}" href="{{ url('/calendar') }}"><i class="ti ti-calendar-event me-2"></i>Calendar</a>
    {{-- Transparency Dashboard moved to the public landing page (Nov 8) --}}
    <a class="nav-link {{ request()->is('announcements') ? 'active' : '' }}" href="{{ url('/announcements') }}"><i class="ti ti-speakerphone me-2"></i>Announcements</a>
    <a class="nav-link {{ request()->is('officials') ? 'active' : '' }}" href="{{ url('/officials') }}"><i class="ti ti-users me-2"></i>Officials Directory</a>
    <a class="nav-link {{ request()->is('faq') ? 'active' : '' }}" href="{{ url('/faq') }}"><i class="ti ti-help-circle me-2"></i>FAQ</a>
  </div>

  {{-- Account button --}}
  <div class="sidebar-account" id="accountWrap">
    <div class="account-popup" id="accountPopup" role="menu">
      <a class="account-item {{ request()->is('profile') ? 'active' : '' }}" href="{{ url('/profile') }}" role="menuitem"><i class="ti ti-user"></i>My Profile</a>
      <a class="account-item {{ request()->is('settings') ? 'active' : '' }}" href="{{ url('/settings') }}" role="menuitem"><i class="ti ti-settings"></i>Settings</a>
      <a class="account-item logout" href="{{ url('/logout') }}" role="menuitem"><i class="ti ti-logout"></i>Log Out</a>
    </div>
    <button type="button" class="account-btn {{ $onAccountPage ? 'active' : '' }}" id="accountBtn"
            aria-haspopup="true" aria-expanded="false" aria-controls="accountPopup">
      <span class="account-avatar">{{ $residentInitial }}</span>
      <span class="account-text">
        <span class="account-name">{{ $residentName }}</span>
        <span class="account-role">Resident</span>
      </span>
      <i class="ti ti-chevron-up account-chevron"></i>
    </button>
  </div>
</nav>

<script>
(function () {
  var STORAGE_KEY = 'pabarang.sidebar.openSections';
  var groups = ['report', 'services', 'community'];
  function getStored() { try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; } catch (e) { return []; } }
  function setStored(list) { try { localStorage.setItem(STORAGE_KEY, JSON.stringify(list)); } catch (e) {} }

  var stored = getStored();
  groups.forEach(function (g) {
    var panel = document.getElementById('group-' + g);
    var toggle = document.querySelector('[data-bs-target="#group-' + g + '"]');
    if (!panel || !toggle) return;
    if (panel.classList.contains('show')) {
      if (stored.indexOf(g) === -1) stored.push(g);
    } else if (stored.indexOf(g) !== -1) {
      panel.classList.add('show');
      toggle.setAttribute('aria-expanded', 'true');
    }
  });
  setStored(stored);

  groups.forEach(function (g) {
    var panel = document.getElementById('group-' + g);
    if (!panel) return;
    panel.addEventListener('shown.bs.collapse', function () {
      var list = getStored();
      if (list.indexOf(g) === -1) { list.push(g); setStored(list); }
    });
    panel.addEventListener('hidden.bs.collapse', function () {
      setStored(getStored().filter(function (x) { return x !== g; }));
    });
  });
})();
</script>

<script>
(function () {
  var sidebar = document.getElementById('residentSidebar');
  var backdrop = document.getElementById('sidebarBackdrop');
  var openBtn = document.getElementById('sidebarOpen');
  if (!sidebar || !backdrop || !openBtn) return;
  function setOpen(open) {
    sidebar.classList.toggle('open', open);
    backdrop.classList.toggle('show', open);
    document.body.classList.toggle('sidebar-open', open);
    openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  openBtn.addEventListener('click', function () { setOpen(!sidebar.classList.contains('open')); });
  backdrop.addEventListener('click', function () { setOpen(false); });
  sidebar.querySelectorAll('a.nav-link').forEach(function (a) {
    a.addEventListener('click', function () { setOpen(false); });
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
  window.addEventListener('resize', function () { if (window.innerWidth >= 992) setOpen(false); });
})();
</script>

<script>
(function () {
  var btn = document.getElementById('accountBtn');
  var popup = document.getElementById('accountPopup');
  var wrap = document.getElementById('accountWrap');
  if (!btn || !popup || !wrap) return;
  function setOpen(open) {
    popup.classList.toggle('show', open);
    btn.classList.toggle('open', open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  btn.addEventListener('click', function () { setOpen(!popup.classList.contains('show')); });
  document.addEventListener('click', function (e) { if (!wrap.contains(e.target)) setOpen(false); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
})();
</script>

<script>
// Dark mode for colors the pages set themselves (white cards, light tiles, dark text).
// It looks at the colors each element really shows and swaps only the plain white /
// light-gray ones; colored things (status pills, buttons, events) keep their colors.
(function () {
  var html = document.documentElement;
  var SKIP = 'img, svg, canvas, video, iframe, script, style, .sidebar, .mobile-topbar, .sidebar-backdrop, .leaflet-container, .maplibregl-map, .modal-backdrop, .sos-bar';
  var ADDED = ['pb-dk-bg', 'pb-dk-bg2', 'pb-dk-fg', 'pb-dk-muted', 'pb-dk-line'];

  function rgb(str) {
    var m = str && str.match(/rgba?\(([\d.]+),\s*([\d.]+),\s*([\d.]+)(?:,\s*([\d.]+))?/);
    return m ? { r: +m[1], g: +m[2], b: +m[3], a: m[4] === undefined ? 1 : +m[4] } : null;
  }
  function light(c) { return (c.r * 299 + c.g * 587 + c.b * 114) / 1000; }
  function grayish(c) { return Math.max(c.r, c.g, c.b) - Math.min(c.r, c.g, c.b) < 40; }

  function fix(el) {
    if (el.closest(SKIP)) return;
    var cs = getComputedStyle(el);
    var bg = rgb(cs.backgroundColor);
    if (bg && bg.a > 0.5 && grayish(bg)) {
      var L = light(bg);
      if (L > 246) el.classList.add('pb-dk-bg');          // white cards
      else if (L > 215) el.classList.add('pb-dk-bg2');    // light gray / lavender tiles
    }
    var fg = rgb(cs.color);
    if (fg && grayish(fg)) {
      var F = light(fg);
      if (F < 90) el.classList.add('pb-dk-fg');           // near-black text
      else if (F < 150) el.classList.add('pb-dk-muted');  // gray text
    }
    var bc = rgb(cs.borderTopColor);
    if (bc && parseFloat(cs.borderTopWidth) > 0 && grayish(bc) && light(bc) > 200) el.classList.add('pb-dk-line');
  }

  function scan(root) {
    if (html.getAttribute('data-theme') !== 'dark' || !root) return;
    if (root.nodeType === 1) fix(root);
    root.querySelectorAll && root.querySelectorAll('*').forEach(fix);
  }
  function clear() {
    ADDED.forEach(function (c) {
      document.querySelectorAll('.' + c).forEach(function (el) { el.classList.remove(c); });
    });
  }
  function refresh() {
    clear();
    scan(document.body);
  }

  // Re-run when the theme changes (Settings page or the device setting)
  var apply = window.pbApplyTheme;
  window.pbApplyTheme = function (choice) {
    apply(choice);
    if (document.readyState !== 'loading') refresh();
  };

  // Whole page except the sidebar (some pages don't wrap their content in .main)
  document.addEventListener('DOMContentLoaded', function () {
    var main = document.body;
    scan(main);
    // Parts drawn later (calendar, charts, lists) get the same treatment
    var queued = [];
    new MutationObserver(function (list) {
      if (html.getAttribute('data-theme') !== 'dark') return;
      list.forEach(function (m) { m.addedNodes.forEach(function (n) { if (n.nodeType === 1) queued.push(n); }); });
      if (queued.length) requestAnimationFrame(function () { var q = queued; queued = []; q.forEach(scan); });
    }).observe(main, { childList: true, subtree: true });
  });
  // Stylesheets at the end of the page can change colors after DOMContentLoaded
  window.addEventListener('load', function () { if (html.getAttribute('data-theme') === 'dark') refresh(); });
})();
</script>

