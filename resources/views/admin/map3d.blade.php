<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>3D Concern Map - Tugon Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- MapLibre GL JS (free, open source, no API key) -->
  <link href="https://unpkg.com/maplibre-gl@5/dist/maplibre-gl.css" rel="stylesheet">
  <script src="https://unpkg.com/maplibre-gl@5/dist/maplibre-gl.js"></script>

  <style>
    :root {
      --ref-blue: #4F5FE0; --ref-navy-text: #1E2340; --ref-gray-text: #6B7280;
      --ref-bg: #F5F6FA; --ref-border: #E4E6EF; --ref-red: #D64545;
      --c-pending: #F5B942; --c-progress: #4F5FE0; --c-resolved: #4CAF50;
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
    .admin-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; gap: 12px; flex-wrap: wrap; }
    .admin-topbar h4 { font-weight: 800; margin: 0; }
    .admin-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; }

    .map-card { position: relative; background: #fff; border: 1px solid var(--ref-border); border-radius: 14px; overflow: hidden; height: calc(100vh - 130px); min-height: 460px; }
    #map3d { position: absolute; inset: 0; }

    /* Top-left controls: Street / Satellite, tilt, back to 2D */
    .map-tools { position: absolute; top: 12px; left: 12px; z-index: 5; display: flex; gap: 8px; flex-wrap: wrap; }
    .seg { display: flex; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); overflow: hidden; }
    .seg button, .seg a { border: 0; background: #fff; color: #1E2340; font: 600 12.5px 'Lato', sans-serif; padding: 7px 12px; display: flex; align-items: center; gap: 5px; cursor: pointer; text-decoration: none; }
    .seg > * + * { border-left: 1px solid #E4E6EF !important; }
    .seg button:hover, .seg a:hover { background: #F1F3FE; }
    .seg button.active { background: #4F5FE0; color: #fff; }

    .map-count { position: absolute; top: 12px; right: 52px; z-index: 5; background: #fff; border-radius: 8px; padding: 6px 12px; font-size: 12.5px; font-weight: 700; box-shadow: 0 2px 6px rgba(0,0,0,0.12); }

    .map-legend { position: absolute; bottom: 28px; left: 12px; z-index: 5; background: #fff; border-radius: 8px; padding: 8px 10px; font-size: 11px; box-shadow: 0 2px 8px rgba(0,0,0,0.12); }
    .legend-row { display: flex; align-items: center; gap: 6px; margin-bottom: 3px; }
    .legend-row:last-child { margin-bottom: 0; }
    .legend-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
    .map-help { position: absolute; bottom: 28px; right: 12px; z-index: 5; background: rgba(30,35,64,.85); color: #fff; border-radius: 8px; padding: 6px 10px; font-size: 11.5px; }

    /* Pins: same look as the 2D map */
    .concern-marker { width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.4); cursor: pointer; }
    .concern-marker.unverified-ring { border: 3px solid var(--ref-red); }
    .popup-badge { font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 8px; }
    .popup-anon { background: #EDEDED; color: #555; }
    .maplibregl-popup-content { font-family: 'Lato', sans-serif; border-radius: 10px; padding: 12px 14px; }

    /* Shown when 3D cannot run; then the page goes back to the 2D satellite map */
    .map-fallback { position: absolute; inset: 0; z-index: 10; display: none; align-items: center; justify-content: center; background: rgba(245,246,250,.96); text-align: center; padding: 20px; }
    .map-fallback.show { display: flex; }

    @media (max-width: 600px) {
      .admin-main { padding: 16px 12px; }
      .map-card { height: calc(100vh - 110px); }
      .map-help { display: none; }
    }
  </style>
</head>
<body>

  <div class="admin-layout">

    @include('partials.admin-sidebar')

    <div class="admin-main">
      <div class="admin-topbar">
        <h4>3D Concern Map</h4>
        @include('partials.admin-user')
      </div>

      <div class="map-card">
        <div id="map3d"></div>

        <div class="map-tools">
          <div class="seg" role="group" aria-label="Base map">
            <button type="button" data-mode="street"><i class="ti ti-map"></i> Street</button>
            <button type="button" data-mode="satellite"><i class="ti ti-satellite"></i> Satellite</button>
          </div>
          <div class="seg">
            <button type="button" id="tiltBtn" class="active"><i class="ti ti-cube-3d-sphere"></i> 3D</button>
            <a href="{{ url('/admin/map') }}" id="back2d"><i class="ti ti-map-2"></i> 2D map</a>
          </div>
        </div>

        <div class="map-count" id="mapCount"></div>

        <div class="map-legend">
          <div class="legend-row"><span class="legend-dot" style="background:var(--c-pending);"></span> Pending</div>
          <div class="legend-row"><span class="legend-dot" style="background:var(--c-progress);"></span> In Progress</div>
          <div class="legend-row"><span class="legend-dot" style="background:var(--c-resolved);"></span> Resolved</div>
          <div class="legend-row"><span class="legend-dot" style="border: 2px solid var(--ref-red); background:#fff;"></span> Unverified</div>
        </div>

        <div class="map-help">Right-drag or Ctrl + drag to tilt and rotate. Two fingers on a phone.</div>

        <div class="map-fallback" id="mapFallback" role="alert">
          <div>
            <i class="ti ti-alert-circle" style="font-size: 32px; color: var(--ref-gray-text);"></i>
            <p class="fw-bold mb-1 mt-2" id="fallbackMsg">The 3D map can't run on this device.</p>
            <p class="mb-3" style="font-size: 13px; color: var(--ref-gray-text);">Opening the 2D satellite map instead...</p>
            <a href="{{ url('/admin/map') }}" class="btn btn-sm" style="background: var(--ref-blue); color: #fff;">Open 2D map now</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const concerns = @json($concerns);
    const MAP_2D = "{{ url('/admin/map') }}";
    const CONCERNS_URL = "{{ url('/admin/concerns') }}";
    const CENTER = [125.706, 7.421]; // Barangay Alejal area [lng, lat]
    const STATUS_COLOR = { pending: '#F5B942', progress: '#4F5FE0', blotter: '#4F5FE0', resolved: '#4CAF50' };

    // Free tile sources, no API key or billing:
    // streets + building heights from OpenFreeMap (OpenStreetMap data),
    // satellite from Esri World Imagery, terrain from AWS Terrain Tiles.
    const STREET_STYLE = 'https://tiles.openfreemap.org/styles/liberty';
    const ESRI_IMAGERY = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
    const TERRAIN_TILES = 'https://s3.amazonaws.com/elevation-tiles-prod/terrarium/{z}/{x}/{y}.png';

    function esc(v) { const d = document.createElement('div'); d.textContent = v == null ? '' : v; return d.innerHTML; }

    // If 3D cannot run: show a short message, remember Satellite, and open the 2D map.
    let fellBack = false;
    function fallback(reason) {
      if (fellBack) return;
      fellBack = true;
      document.getElementById('fallbackMsg').textContent = reason;
      document.getElementById('mapFallback').classList.add('show');
      try { localStorage.setItem('pb_basemap', 'satellite'); localStorage.setItem('pb_map_view', '2d'); } catch (e) {}
      setTimeout(() => { location.href = MAP_2D; }, 3000);
    }

    function hasWebGL() {
      try {
        const c = document.createElement('canvas');
        return !!(window.WebGL2RenderingContext && c.getContext('webgl2')) || !!c.getContext('webgl');
      } catch (e) { return false; }
    }

    document.getElementById('mapCount').textContent =
      concerns.length + (concerns.length === 1 ? ' report on the map' : ' reports on the map');
    document.getElementById('back2d').addEventListener('click', () => {
      try { localStorage.setItem('pb_map_view', '2d'); } catch (e) {}
    });

    if (typeof maplibregl === 'undefined') {
      fallback('The 3D map library could not be loaded. Check the internet connection.');
    } else if (!hasWebGL()) {
      fallback('This browser or device does not support 3D maps (WebGL).');
    } else {
      try { localStorage.setItem('pb_map_view', '3d'); } catch (e) {}
      start3d();
    }

    function start3d() {
      const map = new maplibregl.Map({
        container: 'map3d',
        style: STREET_STYLE,
        center: CENTER,
        zoom: 15.5,
        pitch: 60,
        bearing: -20,
        maxPitch: 75,
        attributionControl: { compact: true }
      });
      map.addControl(new maplibregl.NavigationControl({ visualizePitch: true }), 'top-right');
      map.addControl(new maplibregl.FullscreenControl(), 'top-right');

      // The style must load within 15 seconds, or we fall back to the 2D map.
      const loadTimer = setTimeout(() => {
        if (!loaded) fallback('The 3D map took too long to load. Check the internet connection.');
      }, 15000);

      let loaded = false;
      map.on('error', e => {
        // A missing tile is normal (it has a sourceId). An error before the map
        // has loaded, with no sourceId, means the style itself failed.
        if (!loaded && !(e && e.sourceId)) {
          fallback('The 3D map could not be loaded. Check the internet connection.');
        }
      });

      map.on('load', () => {
        loaded = true;
        clearTimeout(loadTimer);
        addSatellite(map);
        addTerrain(map);
        add3dBuildings(map);
        addPins(map);
        setupSwitch(map);
        setupTilt(map);
      });
    }

    // Esri imagery under the labels, hidden until Satellite is picked
    function addSatellite(map) {
      map.addSource('esri', { type: 'raster', tiles: [ESRI_IMAGERY], tileSize: 256, maxzoom: 18,
        attribution: 'Imagery &copy; Esri, Maxar, Earthstar Geographics' });
      const firstSymbol = map.getStyle().layers.find(l => l.type === 'symbol');
      map.addLayer({ id: 'esri-imagery', type: 'raster', source: 'esri', layout: { visibility: 'none' } },
        firstSymbol ? firstSymbol.id : undefined);
    }

    // Raised ground from free elevation tiles
    function addTerrain(map) {
      try {
        map.addSource('terrain', { type: 'raster-dem', tiles: [TERRAIN_TILES], encoding: 'terrarium',
          tileSize: 256, maxzoom: 15, attribution: 'Terrain: Mapzen / AWS Terrain Tiles' });
        map.setTerrain({ source: 'terrain', exaggeration: 1.5 });
      } catch (e) { /* the map still works flat */ }
    }

    // Buildings raised by their height. Uses the style's own 3D layer if it has one.
    function add3dBuildings(map) {
      const layers = map.getStyle().layers;
      if (layers.some(l => l.type === 'fill-extrusion')) return;
      const src = Object.keys(map.getStyle().sources).find(k => map.getStyle().sources[k].type === 'vector');
      if (!src) return;
      const firstSymbol = layers.find(l => l.type === 'symbol');
      map.addLayer({
        id: 'buildings-3d', type: 'fill-extrusion', source: src, 'source-layer': 'building', minzoom: 14,
        paint: {
          'fill-extrusion-color': '#D9DCE8',
          'fill-extrusion-height': ['coalesce', ['get', 'render_height'], 6],
          'fill-extrusion-base': ['coalesce', ['get', 'render_min_height'], 0],
          'fill-extrusion-opacity': 0.85
        }
      }, firstSymbol ? firstSymbol.id : undefined);
    }

    function popupHtml(p) {
      const badge = p.reporter === 'Anonymous'
        ? '<span class="popup-badge popup-anon">Anonymous</span>'
        : (p.reporter ? `<span style="font-size:11.5px; color:#6B7280;">${esc(p.reporter)}</span>` : '');
      const meta = [p.ref, p.category, p.zone].filter(Boolean).map(esc).join(' · ');
      return `
        <div style="font-size:13px; min-width:180px;">
          <div style="font-weight:700; margin-bottom:2px;">${esc(p.title)}</div>
          <div style="color:#6B7280; font-size:11.5px; margin-bottom:6px;">${meta} · Filed ${esc(p.filed)}</div>
          ${badge}
          <div style="margin-top:8px;">
            <a href="${CONCERNS_URL}?q=${encodeURIComponent(p.ref)}" style="font-size:12px; color:#4F5FE0;">View full details &rarr;</a>
          </div>
        </div>`;
    }

    function addPins(map) {
      const bounds = new maplibregl.LngLatBounds();
      concerns.forEach(p => {
        const el = document.createElement('div');
        el.className = 'concern-marker' + (p.verify === 'unverified' ? ' unverified-ring' : '');
        el.style.background = STATUS_COLOR[p.status] || STATUS_COLOR.pending;
        el.setAttribute('role', 'button');
        el.setAttribute('aria-label', p.ref + ', ' + p.category);
        new maplibregl.Marker({ element: el })
          .setLngLat([p.lng, p.lat])
          .setPopup(new maplibregl.Popup({ offset: 14 }).setHTML(popupHtml(p)))
          .addTo(map);
        bounds.extend([p.lng, p.lat]);
      });
      if (concerns.length > 1) {
        map.fitBounds(bounds, { padding: 80, maxZoom: 17, pitch: 60, bearing: -20 });
      } else if (concerns.length === 1) {
        map.jumpTo({ center: [concerns[0].lng, concerns[0].lat], zoom: 17 });
      }
    }

    // Street / Satellite, remembered with the same key as the 2D maps
    function setupSwitch(map) {
      const buttons = document.querySelectorAll('.seg [data-mode]');
      function setMode(mode) {
        map.setLayoutProperty('esri-imagery', 'visibility', mode === 'satellite' ? 'visible' : 'none');
        buttons.forEach(b => {
          b.classList.toggle('active', b.dataset.mode === mode);
          b.setAttribute('aria-pressed', b.dataset.mode === mode);
        });
        try { localStorage.setItem('pb_basemap', mode); } catch (e) {}
      }
      buttons.forEach(b => b.addEventListener('click', () => setMode(b.dataset.mode)));
      let saved = 'street';
      try { saved = localStorage.getItem('pb_basemap') || 'street'; } catch (e) {}
      setMode(saved === 'satellite' ? 'satellite' : 'street');
    }

    // 3D button: tilt on (60 degrees) or flat top-down view
    function setupTilt(map) {
      const btn = document.getElementById('tiltBtn');
      btn.addEventListener('click', () => {
        const tilted = map.getPitch() > 5;
        map.easeTo({ pitch: tilted ? 0 : 60, bearing: tilted ? 0 : -20, duration: 700 });
        btn.classList.toggle('active', !tilted);
      });
      map.on('pitchend', () => btn.classList.toggle('active', map.getPitch() > 5));
    }
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <link href="{{ asset('css/pabarang-admin.css') }}" rel="stylesheet">
</body>
</html>
