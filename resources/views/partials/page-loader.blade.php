{{--
  Loading screen. Include right after <body>:  @include('partials.page-loader')
  Shows the Tugon logo while the page loads: the two walls slide in from the sides,
  the gold bar spreads across the top, the stem comes down between the walls, and a
  thin gold line runs under the name until the page is ready. Self-contained (own styles and script).
--}}
<div id="tgLoader" class="tg-loader" role="status" aria-live="polite">
  <div class="tg-loader-mark" aria-hidden="true">
    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
      <rect class="tg-l-tile" x="1" y="1" width="98" height="98" rx="19" fill="#13204A" stroke="#FFFFFF" stroke-opacity=".22" stroke-width="2"/>
      <rect class="tg-l-wall tg-l-w1" x="22" y="40" width="16" height="38" rx="2" fill="#FFFFFF" fill-opacity=".9"/>
      <rect class="tg-l-wall tg-l-w2" x="62" y="40" width="16" height="38" rx="2" fill="#FFFFFF" fill-opacity=".9"/>
      <rect class="tg-l-stem" x="44" y="34" width="12" height="44" rx="2" fill="#D9B44A"/>
      <rect class="tg-l-bar" x="22" y="22" width="56" height="12" rx="2" fill="#D9B44A"/>
    </svg>
  </div>
  <div class="tg-loader-name">Tugon</div>
  <div class="tg-loader-sub">Barangay Alejal</div>
  <div class="tg-loader-track" aria-hidden="true"><span></span></div>
  <span class="tg-sr">Loading</span>
</div>

<style>
  .tg-loader {
    position: fixed; inset: 0; z-index: 3000; background: #0B1530;
    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .35rem;
    font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif; color: #fff;
    transition: opacity .4s ease, visibility 0s linear .4s;
  }
  .tg-sr { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
  .tg-loader.done { opacity: 0; visibility: hidden; }
  .tg-loader-mark { width: 128px; height: 128px; margin-bottom: .9rem; }
  .tg-loader-mark svg { width: 100%; height: 100%; display: block; overflow: visible; }
  .tg-loader-name { font-weight: 800; font-size: 2.1rem; letter-spacing: -0.02em; opacity: 0; animation: tgFade .4s ease .9s forwards; }
  .tg-loader-sub { font-size: 1.05rem; color: #AEB6DA; opacity: 0; animation: tgFade .4s ease .9s forwards; }

  /* 1. the tile fades up, 2. the walls slide in from the sides, 3. the gold bar spreads
     from the middle, 4. the stem comes down from the bar */
  .tg-l-tile { transform-origin: 50% 50%; animation: tgTile .4s ease-out both; }
  .tg-l-w1 { animation: tgFromLeft .45s cubic-bezier(.2,.8,.2,1) .2s both; }
  .tg-l-w2 { animation: tgFromRight .45s cubic-bezier(.2,.8,.2,1) .2s both; }
  .tg-l-bar { transform-box: fill-box; transform-origin: 50% 50%; animation: tgSpread .4s cubic-bezier(.2,.8,.2,1) .5s both; }
  .tg-l-stem { transform-box: fill-box; transform-origin: 50% 0; animation: tgDown .35s ease-out .75s both; }

  /* Thin gold line under the name that keeps running until the page is ready */
  .tg-loader-track { width: 140px; height: 3px; border-radius: 3px; background: rgba(255,255,255,.12); overflow: hidden; margin-top: 1rem; opacity: 0; animation: tgFade .3s ease 1s forwards; }
  .tg-loader-track span { display: block; width: 40%; height: 100%; border-radius: 3px; background: #7FB2FF; animation: tgRun 1.1s ease-in-out 1s infinite; }

  @keyframes tgTile { from { transform: scale(.9); opacity: 0; } to { transform: none; opacity: 1; } }
  @keyframes tgFromLeft { from { transform: translateX(-22px); opacity: 0; } to { transform: none; opacity: 1; } }
  @keyframes tgFromRight { from { transform: translateX(22px); opacity: 0; } to { transform: none; opacity: 1; } }
  @keyframes tgSpread { from { transform: scaleX(0); } to { transform: none; } }
  @keyframes tgDown { from { transform: scaleY(0); } to { transform: none; } }
  @keyframes tgFade { to { opacity: 1; } }
  @keyframes tgRun { 0% { transform: translateX(-100%); } 100% { transform: translateX(250%); } }

  @media (prefers-reduced-motion: reduce) {
    .tg-loader *, .tg-loader-mark { animation: none !important; opacity: 1 !important; }
    .tg-loader-track { display: none; }
  }
  html.reduce-motion .tg-loader *, html.reduce-motion .tg-loader-mark { animation: none !important; opacity: 1 !important; }
  html.reduce-motion .tg-loader-track { display: none; }
</style>

<script>
  (function () {
    var el = document.getElementById('tgLoader');
    if (!el) return;
    var shownAt = Date.now();
    function hide() {
      // Stay at least 1.3s so the logo finishes drawing, never longer than needed after that
      var wait = Math.max(0, 1300 - (Date.now() - shownAt));
      setTimeout(function () {
        el.classList.add('done');
        setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 450);
      }, wait);
    }
    if (document.readyState === 'complete') hide();
    else window.addEventListener('load', hide);
    // Never block the page: give up after 5 seconds even if an image is still loading
    setTimeout(hide, 5000);
    // Coming back with the browser Back button: no loader
    window.addEventListener('pageshow', function (e) { if (e.persisted) hide(); });
  })();
</script>
