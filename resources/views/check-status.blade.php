{{--
    resources/views/check-status.blade.php

    Public status lookup (no login). A resident enters the tracking code from
    the report form and sees its status: GET /concerns/status/{code}
    (ConcernStatusController returns only status, category, dates and the
    official's public update). This device only remembers the codes.

    Look: same identity as the landing page (fonts, horizon, Dark/Sky theme).
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Check Report Status - Tugon</title>

  {{-- Same theme as the landing page (dark by default, or sky) --}}
  <script>
    try { if (localStorage.getItem('pabarang.landingTheme') === 'sky') document.documentElement.setAttribute('data-theme', 'sky'); } catch (e) {}
  </script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    /* ===== Night palette (default), shared with the landing page ===== */
    :root {
      --ink: #0B1530; --ink-2: #0F1C3B; --ink-3: #152447;
      --txt: #E7EAF6; --txt-dim: #9AA3C7; --line: rgba(255,255,255,.1);
      --gold: #7FB2FF; --gold-hi: #A9CCFF; --green: #3FB37F; --accent: #7F8DFF; --btn: #3D4ED1; --btn-hi: #4E60E8;
      --sky-top: #0A1430; --sky-mid: #11214A; --sky-low: #1F305A;
      --field-1: #13283E; --field-2: #163049; --field-3: #1A3852; --field-4: #1E4058;
      --dike: rgba(232,184,78,.16); --tree: #091527; --lamp: #E8B84E; --road: #24384F;
      --pend-bg: rgba(245,185,66,.16); --pend: #F5C76A;
      --prog-bg: rgba(127,141,255,.18); --prog: #A9B3FF;
      --done-bg: rgba(76,175,80,.18);  --done: #7FD08A; --done-solid: #3E9B4F;
      --err-bg: rgba(214,69,69,.16); --err: #F3A0A0;
    }
    /* ===== Sky palette ===== */
    [data-theme="sky"] {
      --ink: #C9E3F5; --ink-2: #D6EAF8; --ink-3: #E6F2FB;
      --txt: #0E2A47; --txt-dim: #46607D; --line: rgba(14,42,71,.14);
      --gold: #2459C4; --gold-hi: #3A70DD; --green: #1F8A57; --accent: #2563B8; --btn: #2563B8; --btn-hi: #1E56A3;
      --sky-top: #6FB8E8; --sky-mid: #A3D2F2; --sky-low: #CFE7F7;
      --field-1: #8EC47F; --field-2: #7DB86E; --field-3: #6DAA5F; --field-4: #609E53;
      --dike: #B6DA9E; --tree: #3F7B48; --lamp: transparent; --road: #CDB98A;
      --pend-bg: #FBE7BD; --pend: #7A5408;
      --prog-bg: #D3E3F7; --prog: #1E4F93;
      --done-bg: #D4EED5; --done: #1E6B30; --done-solid: #2E8B42;
      --err-bg: #F8DADA; --err: #9B2226;
    }

    body { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif; color: var(--txt); background: var(--ink); min-height: 100vh; font-size: 1.0625rem; }
    h1, h2, .display { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif; letter-spacing: -0.02em; color: var(--txt); }
    a { color: var(--accent); }

    /* ===== Header ===== */
    .site-header { position: relative; z-index: 2; }
    .site-header .inner { max-width: 960px; margin: 0 auto; padding: 0 20px; height: 68px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .brand { display: flex; align-items: center; gap: 10px; font-family: 'Host Grotesk', sans-serif; font-weight: 800; font-size: 19px; color: var(--txt); text-decoration: none; }
    .brand svg { width: 34px; height: 34px; transform: rotate(3deg); }
    .head-actions { display: flex; align-items: center; gap: 14px; }
    .back-link { font-weight: 700; color: var(--txt); text-decoration: none; font-size: .95rem; }
    .back-link:hover { color: var(--gold); }
    .theme-toggle { width: 38px; height: 38px; border-radius: 50%; border: 1.5px solid var(--line); background: transparent; color: var(--txt); display: inline-flex; align-items: center; justify-content: center; font-size: 17px; }
    .theme-toggle:hover { border-color: var(--gold); }
    .theme-toggle .icon-moon { display: none; }
    [data-theme="sky"] .theme-toggle .icon-sun { display: none; }
    [data-theme="sky"] .theme-toggle .icon-moon { display: inline; }

    /* ===== Top band: the landing page's horizon, smaller ===== */
    .band { position: relative; margin-top: -68px; padding: 108px 20px 150px; overflow: hidden;
            background: linear-gradient(180deg, var(--sky-top) 0%, var(--sky-mid) 60%, var(--sky-low) 100%); }
    [data-theme="sky"] .band { background: radial-gradient(420px 260px at 85% 0%, rgba(255,236,170,.8), transparent 62%), linear-gradient(180deg, var(--sky-top) 0%, var(--sky-mid) 60%, var(--sky-low) 100%); }
    .band-inner { max-width: 600px; margin: 0 auto; position: relative; z-index: 1; }
    .page-title { font-weight: 800; font-size: clamp(2rem, 5vw, 2.6rem); line-height: 1.08; margin-bottom: .6rem; }
    .page-lead { color: var(--txt-dim); margin: 0; max-width: 34rem; }
    .horizon { position: absolute; left: 0; right: 0; bottom: 0; width: 100%; height: 130px; display: block; }
    .horizon .f1 { fill: var(--field-1); } .horizon .f2 { fill: var(--field-2); }
    .horizon .f3 { fill: var(--field-3); } .horizon .f4 { fill: var(--field-4); }
    .horizon .hz-tree { fill: var(--tree); }
    .horizon .hz-palm { fill: none; stroke: var(--tree); stroke-width: 3; stroke-linecap: round; }
    .horizon .hz-dike { stroke: var(--dike); stroke-width: 1.5; vector-effect: non-scaling-stroke; }
    .horizon .hz-light { fill: var(--lamp); }
    .horizon .hz-road { fill: var(--road); }

    /* ===== Page ===== */
    .page-wrap { max-width: 600px; margin: -70px auto 0; padding: 0 20px 56px; position: relative; z-index: 1; }
    .box { background: var(--ink-3); border: 1px solid var(--line); border-radius: 16px; padding: 24px; box-shadow: 0 18px 44px rgba(3,8,24,.35); }
    [data-theme="sky"] .box { box-shadow: 0 16px 40px rgba(30,80,140,.16); border-color: rgba(255,255,255,.75); }

    /* Lookup */
    .lookup-label { font-weight: 700; margin-bottom: 8px; display: block; }
    .lookup-row { display: flex; gap: 10px; }
    #trackingCode { text-transform: uppercase; letter-spacing: .06em; font-weight: 700; font-size: 17px; padding: 12px 14px; border-radius: 10px;
                    background: var(--ink-2); color: var(--txt); border: 1.5px solid var(--line); }
    [data-theme="sky"] #trackingCode { background: #F1F8FE; }
    #trackingCode::placeholder { text-transform: none; letter-spacing: 0; font-weight: 400; color: var(--txt-dim); opacity: .8; }
    #trackingCode:focus { border-color: var(--btn); box-shadow: 0 0 0 .2rem rgba(61,78,209,.25); }
    .btn-brand { background: var(--btn); border-color: var(--btn); color: #fff; font-weight: 700; border-radius: 10px; }
    .btn-brand:hover, .btn-brand:focus { background: var(--btn-hi); border-color: var(--btn-hi); color: #fff; }
    .btn-brand:disabled { background: var(--btn); border-color: var(--btn); opacity: .7; color: #fff; }
    .lookup-row .btn-brand { padding: 0 26px; }
    .lookup-hint { font-size: .9rem; color: var(--txt-dim); margin: 10px 0 0; }
    .lookup-error { font-size: .95rem; color: var(--err); background: var(--err-bg); border-radius: 8px; padding: 10px 12px; margin-top: 12px; }

    /* Result */
    .result-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .result-label { font-size: .85rem; color: var(--txt-dim); margin-bottom: 2px; }
    .result-code { font-family: 'Host Grotesk', sans-serif; font-weight: 800; font-size: 1.5rem; letter-spacing: .02em; }
    .status-pill { font-size: .85rem; font-weight: 700; padding: 5px 14px; border-radius: 999px; white-space: nowrap; }
    .status-pending { background: var(--pend-bg); color: var(--pend); }
    .status-progress { background: var(--prog-bg); color: var(--prog); }
    .status-resolved { background: var(--done-bg); color: var(--done); }

    /* Progress trail: Submitted > In progress > Resolved */
    .steps { display: flex; margin: 26px 0 22px; padding: 0; list-style: none; }
    .step { flex: 1; text-align: center; position: relative; font-size: .9rem; font-weight: 700; color: var(--txt-dim); }
    .step::before { content: ''; position: absolute; top: 13px; left: -50%; width: 100%; height: 3px; background: var(--line); }
    .step:first-child::before { display: none; }
    .step-dot { position: relative; z-index: 1; width: 28px; height: 28px; margin: 0 auto 8px; border-radius: 50%; background: var(--ink-3); border: 3px solid var(--line);
                display: flex; align-items: center; justify-content: center; font-size: 13px; line-height: 1; color: #fff; }
    .step.done::before, .step.current::before { background: var(--accent); }
    .step.done .step-dot { background: var(--accent); border-color: var(--accent); }
    .step.done .step-dot::after { content: '\2713'; }
    .step.current .step-dot { border-color: var(--accent); box-shadow: 0 0 0 4px rgba(127,141,255,.2); }
    .step.current .step-dot::after { content: ''; width: 10px; height: 10px; border-radius: 50%; background: var(--accent); }
    .step.done, .step.current { color: var(--txt); }
    .steps.is-resolved .step.current .step-dot { background: var(--done-solid); border-color: var(--done-solid); box-shadow: 0 0 0 4px rgba(62,155,79,.2); }
    .steps.is-resolved .step.current .step-dot::after { content: '\2713'; width: auto; height: auto; background: none; border-radius: 0; color: #fff; }
    .steps.is-resolved .step.current::before { background: var(--done-solid); }

    .detail-list { margin: 0; }
    .detail-row { display: flex; justify-content: space-between; gap: 16px; padding: 11px 0; }
    .detail-row + .detail-row { border-top: 1px dashed var(--line); }
    .detail-row dt { font-weight: 400; color: var(--txt-dim); margin: 0; }
    .detail-row dd { margin: 0; text-align: right; font-weight: 700; }

    /* The official's note, like a slip pinned to the barangay board */
    .note-box { border-left: 4px solid var(--green); background: var(--ink-2); border-radius: 0 10px 10px 0; padding: 14px 16px; margin-top: 18px; }
    [data-theme="sky"] .note-box { background: #F1F8FE; }
    .note-box .note-title { font-size: .85rem; font-weight: 700; color: var(--green); margin-bottom: 4px; }
    .note-box p { margin: 0; }

    .copy-link { font-weight: 700; color: var(--accent); text-decoration: none; padding: 0; margin-top: 16px; }
    .copy-link:hover { text-decoration: underline; color: var(--accent); }

    /* Recent reports */
    .recent-title { font-weight: 800; font-size: 1.35rem; margin-bottom: 4px; }
    .recent-note { font-size: .9rem; color: var(--txt-dim); margin-bottom: 14px; }
    .recent-item { display: flex; align-items: center; gap: 12px; background: var(--ink-3); border: 1px solid var(--line); border-radius: 12px; padding: 12px 14px; margin-bottom: 8px; flex-wrap: wrap; }
    .recent-item .code { font-family: 'Host Grotesk', sans-serif; font-weight: 800; letter-spacing: .02em; }
    .recent-item .meta { font-size: .88rem; color: var(--txt-dim); }
    .recent-item.is-missing { background: transparent; border-style: dashed; }
    .recent-item.is-missing .code { color: var(--txt-dim); text-decoration: line-through; text-decoration-thickness: 1px; }
    .recent-actions { display: flex; align-items: center; gap: 6px; margin-left: auto; }
    .recent-remove { border: 0; background: none; color: var(--txt-dim); font-size: .88rem; font-weight: 700; padding: 6px 8px; border-radius: 8px; }
    .recent-remove:hover { color: var(--err); background: var(--err-bg); }

    .page-foot { text-align: center; color: var(--txt-dim); margin-top: 32px; }
    .page-foot a { color: var(--green); font-weight: 700; text-decoration: none; }
    .page-foot a:hover { text-decoration: underline; }

    a:focus-visible, button:focus-visible, input:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }
    @media (max-width: 480px) {
      .band { padding: 96px 16px 130px; }
      .box { padding: 18px; }
      .lookup-row .btn-brand { padding: 0 18px; }
      .back-link { display: none; }
    }
  </style>
</head>
<body>

  <header class="site-header">
    <div class="inner">
      <a href="{{ url('/') }}" class="brand">
        @include('partials.tugon-logo')
        Tugon
      </a>
      <div class="head-actions">
        <a href="{{ url('/') }}#report" class="back-link">Back to home</a>
        <button type="button" id="themeToggle" class="theme-toggle" aria-label="Switch to sky theme">
          <i class="ti ti-sun icon-sun" aria-hidden="true"></i><i class="ti ti-moon icon-moon" aria-hidden="true"></i>
        </button>
      </div>
    </div>
  </header>

  <section class="band">
    <div class="band-inner">
      <h1 class="page-title">Check your report</h1>
      <p class="page-lead">Enter the tracking code from your report to see where it stands and read the barangay's latest note.</p>
    </div>
    <svg class="horizon" viewBox="0 0 1440 230" preserveAspectRatio="none" aria-hidden="true" focusable="false"><defs><linearGradient id="hzFadeS" x1="0" y1="0" x2="0" y2="1"><stop offset="0" style="stop-color:var(--ink);stop-opacity:0"/><stop offset="1" style="stop-color:var(--ink);stop-opacity:1"/></linearGradient></defs><path class="hz-tree" d="M0,46 L0,41 L55,41 L113,43 L151,36 L209,41 L246,35 L300,38 L335,35 L363,43 L414,41 L450,43 L476,42 L506,34 L534,37 L575,43 L602,41 L648,41 L686,42 L726,44 L770,41 L796,44 L827,41 L870,40 L901,38 L947,37 L1005,38 L1032,35 L1064,40 L1096,38 L1146,35 L1173,44 L1199,37 L1238,34 L1294,40 L1345,40 L1375,43 L1413,44 L1440,54 L0,54 Z"/><path class="hz-palm" d="M150,48 Q151.8,30.0 156,16"/><path class="hz-palm" d="M156,16 q-11.0,-4.6 -22,7"/><path class="hz-palm" d="M156,16 q-7.0,-3.4 -14,13"/><path class="hz-palm" d="M156,16 q0.0,-3.8 0,11"/><path class="hz-palm" d="M156,16 q7.0,-3.4 14,13"/><path class="hz-palm" d="M156,16 q11.0,-4.6 22,7"/><path class="hz-palm" d="M156,16 q-4.5,-6.8 -9,-4"/><path class="hz-palm" d="M156,16 q5.0,-6.6 10,-3"/><path class="hz-palm" d="M176,48 Q174.8,33.0 172,22"/><path class="hz-palm" d="M172,22 q-11.0,-4.6 -22,7"/><path class="hz-palm" d="M172,22 q-7.0,-3.4 -14,13"/><path class="hz-palm" d="M172,22 q0.0,-3.8 0,11"/><path class="hz-palm" d="M172,22 q7.0,-3.4 14,13"/><path class="hz-palm" d="M172,22 q11.0,-4.6 22,7"/><path class="hz-palm" d="M172,22 q-4.5,-6.8 -9,-4"/><path class="hz-palm" d="M172,22 q5.0,-6.6 10,-3"/><path class="hz-palm" d="M1000,48 Q997.9,28.0 993,12"/><path class="hz-palm" d="M993,12 q-11.0,-4.6 -22,7"/><path class="hz-palm" d="M993,12 q-7.0,-3.4 -14,13"/><path class="hz-palm" d="M993,12 q0.0,-3.8 0,11"/><path class="hz-palm" d="M993,12 q7.0,-3.4 14,13"/><path class="hz-palm" d="M993,12 q11.0,-4.6 22,7"/><path class="hz-palm" d="M993,12 q-4.5,-6.8 -9,-4"/><path class="hz-palm" d="M993,12 q5.0,-6.6 10,-3"/><path class="hz-palm" d="M1290,48 Q1291.5,31.0 1295,18"/><path class="hz-palm" d="M1295,18 q-11.0,-4.6 -22,7"/><path class="hz-palm" d="M1295,18 q-7.0,-3.4 -14,13"/><path class="hz-palm" d="M1295,18 q0.0,-3.8 0,11"/><path class="hz-palm" d="M1295,18 q7.0,-3.4 14,13"/><path class="hz-palm" d="M1295,18 q11.0,-4.6 22,7"/><path class="hz-palm" d="M1295,18 q-4.5,-6.8 -9,-4"/><path class="hz-palm" d="M1295,18 q5.0,-6.6 10,-3"/><path class="hz-palm" d="M1316,48 Q1318.4,34.0 1324,24"/><path class="hz-palm" d="M1324,24 q-11.0,-4.6 -22,7"/><path class="hz-palm" d="M1324,24 q-7.0,-3.4 -14,13"/><path class="hz-palm" d="M1324,24 q0.0,-3.8 0,11"/><path class="hz-palm" d="M1324,24 q7.0,-3.4 14,13"/><path class="hz-palm" d="M1324,24 q11.0,-4.6 22,7"/><path class="hz-palm" d="M1324,24 q-4.5,-6.8 -9,-4"/><path class="hz-palm" d="M1324,24 q5.0,-6.6 10,-3"/><polygon class="f3" points="-133,54 -13,54 -24,61 -146,61"/><polygon class="f2" points="-13,54 110,54 100,61 -24,61"/><polygon class="f1" points="110,54 207,54 199,61 100,61"/><polygon class="f2" points="207,54 277,54 270,61 199,61"/><polygon class="f2" points="277,54 381,54 375,61 270,61"/><polygon class="f4" points="381,54 526,54 523,61 375,61"/><polygon class="f1" points="526,54 634,54 632,61 523,61"/><polygon class="f3" points="634,54 706,54 705,61 632,61"/><polygon class="f2" points="706,54 802,54 803,61 705,61"/><polygon class="f2" points="802,54 895,54 897,61 803,61"/><polygon class="f1" points="895,54 1015,54 1019,61 897,61"/><polygon class="f1" points="1015,54 1162,54 1168,61 1019,61"/><polygon class="f3" points="1162,54 1305,54 1313,61 1168,61"/><polygon class="f4" points="1305,54 1387,54 1396,61 1313,61"/><polygon class="f4" points="1387,54 1462,54 1472,61 1396,61"/><polygon class="f2" points="1462,54 1550,54 1562,61 1472,61"/><line class="hz-dike" x1="0" y1="61" x2="1440" y2="61"/><polygon class="f3" points="-146,61 -24,61 -41,71 -165,71"/><polygon class="f4" points="-24,61 100,61 87,71 -41,71"/><polygon class="f3" points="100,61 199,61 187,71 87,71"/><polygon class="f4" points="199,61 270,61 260,71 187,71"/><polygon class="f4" points="270,61 375,61 367,71 260,71"/><polygon class="f4" points="375,61 523,61 518,71 367,71"/><polygon class="f4" points="523,61 632,61 629,71 518,71"/><polygon class="f1" points="632,61 705,61 704,71 629,71"/><polygon class="f1" points="705,61 803,61 804,71 704,71"/><polygon class="f3" points="803,61 897,61 900,71 804,71"/><polygon class="f1" points="897,61 1019,61 1024,71 900,71"/><polygon class="f4" points="1019,61 1168,61 1176,71 1024,71"/><polygon class="f1" points="1168,61 1313,61 1325,71 1176,71"/><polygon class="f2" points="1313,61 1396,61 1409,71 1325,71"/><polygon class="f1" points="1396,61 1472,61 1487,71 1409,71"/><polygon class="f4" points="1472,61 1562,61 1578,71 1487,71"/><line class="hz-dike" x1="0" y1="71" x2="1440" y2="71"/><polygon class="f3" points="-165,71 -41,71 -64,85 -191,85"/><polygon class="f3" points="-41,71 87,71 67,85 -64,85"/><polygon class="f1" points="87,71 187,71 171,85 67,85"/><polygon class="f2" points="187,71 260,71 245,85 171,85"/><polygon class="f4" points="260,71 367,71 356,85 245,85"/><polygon class="f4" points="367,71 518,71 511,85 356,85"/><polygon class="f2" points="518,71 629,71 626,85 511,85"/><polygon class="f1" points="629,71 704,71 702,85 626,85"/><polygon class="f3" points="704,71 804,71 805,85 702,85"/><polygon class="f2" points="804,71 900,71 904,85 805,85"/><polygon class="f2" points="900,71 1024,71 1032,85 904,85"/><polygon class="f1" points="1024,71 1176,71 1188,85 1032,85"/><polygon class="f1" points="1176,71 1325,71 1341,85 1188,85"/><polygon class="f4" points="1325,71 1409,71 1428,85 1341,85"/><polygon class="f1" points="1409,71 1487,71 1508,85 1428,85"/><polygon class="f3" points="1487,71 1578,71 1602,85 1508,85"/><line class="hz-dike" x1="0" y1="85" x2="1440" y2="85"/><polygon class="f2" points="-64,85 67,85 41,104 -95,104"/><polygon class="f1" points="67,85 171,85 149,104 41,104"/><polygon class="f4" points="171,85 245,85 226,104 149,104"/><polygon class="f1" points="245,85 356,85 341,104 226,104"/><polygon class="f2" points="356,85 511,85 501,104 341,104"/><polygon class="f3" points="511,85 626,85 621,104 501,104"/><polygon class="f2" points="626,85 702,85 700,104 621,104"/><polygon class="f4" points="702,85 805,85 806,104 700,104"/><polygon class="f2" points="805,85 904,85 909,104 806,104"/><polygon class="f4" points="904,85 1032,85 1042,104 909,104"/><polygon class="f4" points="1032,85 1188,85 1204,104 1042,104"/><polygon class="f1" points="1188,85 1341,85 1362,104 1204,104"/><polygon class="f2" points="1341,85 1428,85 1453,104 1362,104"/><polygon class="f4" points="1428,85 1508,85 1536,104 1453,104"/><line class="hz-dike" x1="0" y1="104" x2="1440" y2="104"/><polygon class="f3" points="-95,104 41,104 6,130 -137,130"/><polygon class="f2" points="41,104 149,104 118,130 6,130"/><polygon class="f4" points="149,104 226,104 200,130 118,130"/><polygon class="f1" points="226,104 341,104 320,130 200,130"/><polygon class="f4" points="341,104 501,104 488,130 320,130"/><polygon class="f1" points="501,104 621,104 614,130 488,130"/><polygon class="f3" points="621,104 700,104 697,130 614,130"/><polygon class="f2" points="700,104 806,104 809,130 697,130"/><polygon class="f4" points="806,104 909,104 917,130 809,130"/><polygon class="f1" points="909,104 1042,104 1056,130 917,130"/><polygon class="f4" points="1042,104 1204,104 1226,130 1056,130"/><polygon class="f4" points="1204,104 1362,104 1392,130 1226,130"/><polygon class="f2" points="1362,104 1453,104 1488,130 1392,130"/><polygon class="f2" points="1453,104 1536,104 1575,130 1488,130"/><line class="hz-dike" x1="0" y1="130" x2="1440" y2="130"/><polygon class="f4" points="-137,130 6,130 -41,164 -192,164"/><polygon class="f1" points="6,130 118,130 79,164 -41,164"/><polygon class="f1" points="118,130 200,130 165,164 79,164"/><polygon class="f3" points="200,130 320,130 293,164 165,164"/><polygon class="f4" points="320,130 488,130 472,164 293,164"/><polygon class="f4" points="488,130 614,130 605,164 472,164"/><polygon class="f4" points="614,130 697,130 693,164 605,164"/><polygon class="f3" points="697,130 809,130 812,164 693,164"/><polygon class="f4" points="809,130 917,130 926,164 812,164"/><polygon class="f2" points="917,130 1056,130 1074,164 926,164"/><polygon class="f3" points="1056,130 1226,130 1255,164 1074,164"/><polygon class="f1" points="1226,130 1392,130 1431,164 1255,164"/><polygon class="f1" points="1392,130 1488,130 1533,164 1431,164"/><polygon class="f2" points="1488,130 1575,130 1625,164 1533,164"/><line class="hz-dike" x1="0" y1="164" x2="1440" y2="164"/><polygon class="f1" points="-192,164 -41,164 -101,208 -264,208"/><polygon class="f1" points="-41,164 79,164 27,208 -101,208"/><polygon class="f1" points="79,164 165,164 120,208 27,208"/><polygon class="f2" points="165,164 293,164 258,208 120,208"/><polygon class="f3" points="293,164 472,164 450,208 258,208"/><polygon class="f4" points="472,164 605,164 593,208 450,208"/><polygon class="f4" points="605,164 693,164 688,208 593,208"/><polygon class="f1" points="693,164 812,164 816,208 688,208"/><polygon class="f2" points="812,164 926,164 939,208 816,208"/><polygon class="f4" points="926,164 1074,164 1098,208 939,208"/><polygon class="f1" points="1074,164 1255,164 1293,208 1098,208"/><polygon class="f1" points="1255,164 1431,164 1482,208 1293,208"/><polygon class="f3" points="1431,164 1533,164 1591,208 1482,208"/><line class="hz-dike" x1="0" y1="208" x2="1440" y2="208"/><polygon class="f1" points="-101,208 27,208 -10,240 -145,240"/><polygon class="f4" points="27,208 120,208 87,240 -10,240"/><polygon class="f4" points="120,208 258,208 232,240 87,240"/><polygon class="f1" points="258,208 450,208 434,240 232,240"/><polygon class="f2" points="450,208 593,208 585,240 434,240"/><polygon class="f1" points="593,208 688,208 685,240 585,240"/><polygon class="f3" points="688,208 816,208 818,240 685,240"/><polygon class="f2" points="816,208 939,208 948,240 818,240"/><polygon class="f1" points="939,208 1098,208 1115,240 948,240"/><polygon class="f3" points="1098,208 1293,208 1320,240 1115,240"/><polygon class="f4" points="1293,208 1482,208 1519,240 1320,240"/><polygon class="f2" points="1482,208 1591,208 1633,240 1519,240"/><line class="hz-dike" x1="0" y1="240" x2="1440" y2="240"/><line class="hz-dike" x1="-13" y1="54" x2="-316" y2="240"/><line class="hz-dike" x1="110" y1="54" x2="-145" y2="240"/><line class="hz-dike" x1="207" y1="54" x2="-10" y2="240"/><line class="hz-dike" x1="277" y1="54" x2="87" y2="240"/><line class="hz-dike" x1="381" y1="54" x2="232" y2="240"/><line class="hz-dike" x1="526" y1="54" x2="434" y2="240"/><line class="hz-dike" x1="634" y1="54" x2="585" y2="240"/><line class="hz-dike" x1="706" y1="54" x2="685" y2="240"/><line class="hz-dike" x1="802" y1="54" x2="818" y2="240"/><line class="hz-dike" x1="895" y1="54" x2="948" y2="240"/><line class="hz-dike" x1="1015" y1="54" x2="1115" y2="240"/><line class="hz-dike" x1="1162" y1="54" x2="1320" y2="240"/><line class="hz-dike" x1="1305" y1="54" x2="1519" y2="240"/><line class="hz-dike" x1="1387" y1="54" x2="1633" y2="240"/><line class="hz-dike" x1="1462" y1="54" x2="1737" y2="240"/><polygon class="hz-road" points="750,54 770,54 871,240 649,240"/><circle class="hz-light" cx="118" cy="49" r="1.8"/><circle class="hz-light" cx="352" cy="49" r="1.8"/><circle class="hz-light" cx="366" cy="49" r="1.8"/><circle class="hz-light" cx="640" cy="49" r="1.8"/><circle class="hz-light" cx="1046" cy="49" r="1.8"/><circle class="hz-light" cx="1180" cy="49" r="1.8"/><circle class="hz-light" cx="1398" cy="49" r="1.8"/><rect x="0" y="160" width="1440" height="70" fill="url(#hzFadeS)"/></svg>

  </section>

  <main class="page-wrap">
    <form id="statusForm" class="box">
      <label class="lookup-label" for="trackingCode">Tracking code</label>
      <div class="lookup-row">
        <input type="text" id="trackingCode" name="tracking_code" class="form-control" required placeholder="e.g. PB-2026-K7M2XQ" autocomplete="off">
        <button type="submit" id="checkBtn" class="btn btn-brand">Check</button>
      </div>
      <p class="lookup-hint">The code is on the confirmation screen you saw after sending your report.</p>
      <div id="lookupError" class="lookup-error d-none" role="alert"></div>
    </form>

    {{-- Result, filled in by the script after a lookup --}}
    <div id="resultCard" class="box d-none mt-3">
      <div class="result-head">
        <div>
          <div class="result-label">Tracking code</div>
          <div class="result-code" id="resultCode"></div>
        </div>
        <span id="resultStatusBadge" class="status-pill"></span>
      </div>

      <ol class="steps" id="resultSteps" aria-label="Report progress">
        <li class="step" data-step="0"><div class="step-dot"></div>Submitted</li>
        <li class="step" data-step="1"><div class="step-dot"></div>In progress</li>
        <li class="step" data-step="2"><div class="step-dot"></div>Resolved</li>
      </ol>

      <dl class="detail-list">
        <div class="detail-row"><dt>Category</dt><dd id="resultCategory"></dd></div>
        <div class="detail-row"><dt>Submitted</dt><dd id="resultSubmitted"></dd></div>
        <div class="detail-row"><dt>Last updated</dt><dd id="resultUpdated"></dd></div>
      </dl>

      <div class="note-box">
        <div class="note-title">Latest note from the barangay</div>
        <p id="resultNote"></p>
      </div>

      <button type="button" id="copyLinkBtn" class="btn btn-link copy-link">Copy link to this report</button>
    </div>

    {{-- Codes remembered on this device (filled in by the script). Hidden when empty. --}}
    <section id="recentSection" class="d-none mt-5">
      <h2 class="recent-title">Your recent reports</h2>
      <p class="recent-note">Only the codes are kept on this device, and the status is always read fresh. Write your code down in case you change phones.</p>
      <div id="recentList"></div>
    </section>

    <p class="page-foot">
      No tracking code yet? <a href="{{ url('/') }}#report">Report a concern</a> to get one,
      or <a href="{{ url('/login') }}">log in</a> to see all your reports.
    </p>
  </main>

  <script>
    // Looks a code up on the server. Returns the report, null (not found) or undefined (server unreachable).
    async function fetchReport(code) {
      try {
        const r = await fetch("{{ url('/concerns/status') }}/" + encodeURIComponent(code), { headers: { 'Accept': 'application/json' } });
        if (r.status === 404) return null;
        if (!r.ok) return undefined;
        return await r.json();
      } catch (e) { return undefined; }
    }

    // Codes this device has submitted or looked up successfully (newest first).
    function myCodes() {
      try { return JSON.parse(localStorage.getItem('pb_my_codes') || '[]'); } catch (e) { return []; }
    }
    function saveMyCodes(list) {
      try { localStorage.setItem('pb_my_codes', JSON.stringify(list.slice(0, 10))); } catch (e) {}
    }
    function rememberCode(code) { saveMyCodes([code, ...myCodes().filter(c => c !== code)]); }
    function forgetCode(code) { saveMyCodes(myCodes().filter(c => c !== code)); }

    const statusClass = {
      'Pending': 'status-pending',
      'In Progress': 'status-progress',
      'Resolved': 'status-resolved',
    };
    // Which step of the progress line is the current one for each status.
    const statusStep = { 'Pending': 0, 'In Progress': 1, 'Resolved': 2 };

    const input = document.getElementById('trackingCode');
    const checkBtn = document.getElementById('checkBtn');
    const errorEl = document.getElementById('lookupError');
    const resultEl = document.getElementById('resultCard');
    const NOT_FOUND = 'No report found with that tracking code. Check it and try again.';
    const NO_SERVER = 'Could not check right now. Please try again in a moment.';
    const cache = {};   // code -> report, so the recent list doesn't re-ask the server needlessly
    let currentCode = '';

    function setSteps(status) {
      const current = statusStep[status] ?? 0;
      const steps = document.getElementById('resultSteps');
      steps.classList.toggle('is-resolved', status === 'Resolved');
      steps.querySelectorAll('.step').forEach(function (el) {
        const n = parseInt(el.dataset.step, 10);
        el.classList.toggle('done', n < current);
        el.classList.toggle('current', n === current);
      });
    }

    async function lookup(raw) {
      const code = raw.trim().toUpperCase();
      checkBtn.disabled = true;
      checkBtn.textContent = 'Checking...';
      const report = await fetchReport(code);
      checkBtn.disabled = false;
      checkBtn.textContent = 'Check';

      if (!report) {
        errorEl.textContent = report === null ? NOT_FOUND : NO_SERVER;
        errorEl.classList.remove('d-none');
        resultEl.classList.add('d-none');
        return;
      }
      errorEl.classList.add('d-none');
      currentCode = code;
      cache[code] = report;
      rememberCode(code);
      renderRecent();

      document.getElementById('resultCode').textContent = code;
      document.getElementById('resultCategory').textContent = report.category;
      document.getElementById('resultSubmitted').textContent = report.submitted;
      document.getElementById('resultUpdated').textContent = report.updated;
      document.getElementById('resultNote').textContent = report.note;
      setSteps(report.status);

      const badge = document.getElementById('resultStatusBadge');
      badge.textContent = report.status;
      badge.className = 'status-pill ' + (statusClass[report.status] || 'status-pending');

      resultEl.classList.remove('d-none');
      resultEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    let renderToken = 0;
    async function renderRecent() {
      const token = ++renderToken;
      const codes = myCodes();
      document.getElementById('recentSection').classList.toggle('d-none', codes.length === 0);

      // Fetch only the codes we haven't seen yet in this page visit
      await Promise.all(codes.filter(c => !(c in cache)).map(async c => { cache[c] = await fetchReport(c); }));
      if (token !== renderToken) return; // a newer render started, skip this one

      const list = document.getElementById('recentList');
      list.replaceChildren();

      codes.forEach(code => {
        const report = cache[code];
        const row = document.createElement('div');
        row.className = 'recent-item';

        const info = document.createElement('div');
        info.className = 'flex-grow-1 min-w-0';
        const codeEl = document.createElement('div');
        codeEl.className = 'code text-truncate';
        codeEl.textContent = code;
        const meta = document.createElement('div');
        meta.className = 'meta';
        meta.textContent = report ? report.category + ', filed ' + report.submitted
                         : report === null ? 'Not found. It may have been removed by the barangay.' : 'Could not load the status right now';
        if (report === null) row.classList.add('is-missing');
        info.append(codeEl, meta);
        row.appendChild(info);

        if (report) {
          const badge = document.createElement('span');
          badge.className = 'status-pill ' + (statusClass[report.status] || 'status-pending');
          badge.textContent = report.status;
          row.appendChild(badge);
        }

        const actions = document.createElement('div');
        actions.className = 'recent-actions';
        if (report !== null) {
          const check = document.createElement('button');
          check.type = 'button';
          check.className = 'btn btn-brand btn-sm';
          check.textContent = 'View';
          check.addEventListener('click', () => { input.value = code; lookup(code); });
          actions.appendChild(check);
        }
        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'recent-remove';
        remove.setAttribute('aria-label', 'Remove ' + code + ' from this device');
        remove.textContent = 'Remove';
        remove.addEventListener('click', () => { forgetCode(code); renderRecent(); });
        actions.appendChild(remove);
        row.appendChild(actions);
        list.appendChild(row);
      });
    }

    document.getElementById('copyLinkBtn').addEventListener('click', function () {
      const url = location.origin + location.pathname + '?code=' + encodeURIComponent(currentCode);
      const done = () => { this.textContent = 'Link copied'; setTimeout(() => { this.textContent = 'Copy link to this report'; }, 2000); };
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(done, done);
      } else {
        const t = document.createElement('textarea');
        t.value = url; document.body.appendChild(t); t.select();
        try { document.execCommand('copy'); } catch (err) {}
        t.remove(); done();
      }
    });

    document.getElementById('statusForm').addEventListener('submit', function (e) {
      e.preventDefault();
      lookup(input.value);
    });

    renderRecent();

    // Landing page links here as /check-status?code=PB-2026-K7M2XQ
    const prefill = new URLSearchParams(location.search).get('code');
    if (prefill) { input.value = prefill.toUpperCase(); lookup(prefill); }

    // ---- Theme switch (same choice as the landing page) ----
    (function () {
      var html = document.documentElement, btn = document.getElementById('themeToggle');
      function label() { btn.setAttribute('aria-label', html.getAttribute('data-theme') === 'sky' ? 'Switch to dark theme' : 'Switch to sky theme'); }
      label();
      btn.addEventListener('click', function () {
        var next = html.getAttribute('data-theme') === 'sky' ? 'dark' : 'sky';
        function apply() {
          if (next === 'sky') html.setAttribute('data-theme', 'sky'); else html.removeAttribute('data-theme');
          try { localStorage.setItem('pabarang.landingTheme', next); } catch (e) {}
          label();
        }
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!reduce && document.startViewTransition) document.startViewTransition(apply); else apply();
      });
    })();
  </script>
</body>
</html>
