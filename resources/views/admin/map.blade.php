<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Concern Map - Tugon Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">
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
    .admin-sidebar { width: 250px; background: #1E2340; padding: 24px 18px; flex-shrink: 0; }
    .admin-sidebar .brand { display: flex; align-items: center; gap: 10px; margin-bottom: 30px; padding: 0 6px; }
    .admin-sidebar .brand-logo-lg { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; }
    .admin-sidebar .brand-name { color: #fff; font-size: 15px; font-weight: 700; }
    .sidebar-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #7A80A8; padding: 10px 12px 6px; }
    .sidebar-link { display: flex; align-items: center; gap: 10px; color: #C4C8E8; text-decoration: none; padding: 10px 12px; border-radius: 8px; font-size: 14px; margin-bottom: 2px; }
    .sidebar-link i { font-size: 17px; }
    .sidebar-link:hover { background: rgba(255,255,255,0.06); color: #fff; }
    .sidebar-link.active { background: var(--ref-blue); color: #fff; }

    .admin-main { flex: 1; padding: 28px 36px; display: flex; flex-direction: column; }
    .admin-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
    .admin-topbar h4 { font-weight: 800; margin: 0; }
    .admin-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; }

    .privacy-banner { background: #E7EAFD; border: 1px solid #C6CBF7; border-radius: 10px; padding: 12px 16px; font-size: 12.5px; margin-bottom: 16px; }

    .filter-bar { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; display: flex; gap: 10px; flex-wrap: wrap; }
    .filter-bar select { font-size: 13px; }

    .map-layout { display: flex; gap: 16px; flex: 1; min-height: 520px; }

    /* Side list */
    .map-side-list { width: 300px; flex-shrink: 0; background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 14px; overflow-y: auto; }
    .side-list-title { font-size: 13px; font-weight: 700; margin-bottom: 10px; }
    .side-item { border: 1px solid var(--ref-border); border-radius: 8px; padding: 10px 12px; margin-bottom: 8px; cursor: pointer; font-size: 12.5px; }
    .side-item:hover { border-color: var(--ref-blue); }
    .side-item .side-ref { color: var(--ref-gray-text); font-size: 11px; }
    .side-item .side-title { font-weight: 700; margin: 2px 0; }
    .side-dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; margin-right: 6px; }

    /* Map container */
    .map-container { flex: 1; border: 1px solid var(--ref-border); border-radius: 12px; overflow: hidden; position: relative; }
    #fullConcernMap { width: 100%; height: 100%; min-height: 520px; }

    .map-legend {
      position: absolute; bottom: 16px; left: 16px; z-index: 500;
      background: #fff; border-radius: 10px; padding: 12px 14px; font-size: 11.5px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.12);
    }
    .legend-row { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
    .legend-row:last-child { margin-bottom: 0; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; }

    /* Custom marker styling */
    .concern-marker { width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.4); }
    .concern-marker.unverified-ring { border: 3px solid var(--ref-red); }

    .popup-badge { font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 8px; }
    .popup-anon { background: #EDEDED; color: #555; }

    /* Street / Satellite / 3D switch. Sits on top of both the 2D and the 3D map. */
    .basemap-switch { position: absolute; top: 12px; right: 12px; z-index: 1001; display: flex; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); overflow: hidden; }
    .basemap-switch button { border: 0; background: #fff; color: #1E2340; font: 600 12.5px 'Lato', sans-serif; padding: 7px 12px; display: flex; align-items: center; gap: 5px; cursor: pointer; }
    .basemap-switch button + button { border-left: 1px solid #E4E6EF; }
    .basemap-switch button:hover { background: #F1F3FE; }
    .basemap-switch button.active { background: #4F5FE0; color: #fff; }
    .basemap-switch .sep { width: 6px; background: #F5F6FA; }

    /* 3D map (MapLibre GL JS) fills the same box as the 2D map */
    #map3dWrap { position: absolute; inset: 0; z-index: 450; display: none; }
    #map3dWrap.show { display: block; }
    #concernMap3d { position: absolute; inset: 0; }
    .map-container.is-3d #fullConcernMap { visibility: hidden; }
    .map-help { position: absolute; bottom: 16px; right: 12px; z-index: 500; background: rgba(30,35,64,.85); color: #fff; border-radius: 8px; padding: 6px 10px; font-size: 11.5px; display: none; }
    .map-container.is-3d .map-help { display: block; }
    .map-notice { position: absolute; top: 56px; right: 12px; z-index: 1001; max-width: 320px; background: #FFF7E6; border: 1px solid #F3DFA7; color: #7A520A; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: none; }
    .map-notice.show { display: block; }
    .maplibregl-popup-content { font-family: 'Lato', sans-serif; border-radius: 10px; padding: 12px 14px; }
    .maplibregl-marker .concern-marker { cursor: pointer; }
    @media (max-width: 600px) { .map-help { display: none !important; } }
  </style>
</head>
<body>

  <div class="admin-layout">

    @include('partials.admin-sidebar')


    <!-- ===== Main content ===== -->
    <div class="admin-main">

      <div class="admin-topbar">
        <h4>Concern Map</h4>
        @include('partials.admin-user')
      </div>

      <!-- Filters: change both the map and the side list -->
      <div class="filter-bar">
        <select class="form-select form-select-sm" id="filterStatus" style="width: auto;">
          <option value="all">All Statuses</option>
          <option value="pending">Pending</option>
          <option value="progress">In Progress</option>
          <option value="resolved">Resolved</option>
        </select>
        <select class="form-select form-select-sm" id="filterCategory" style="width: auto;">
          <option value="all">All Categories</option>
          <option value="Crime/Safety">Crime/Safety</option>
          <option value="Environment">Environment</option>
          <option value="Infrastructure">Infrastructure</option>
          <option value="Streetlights">Streetlights</option>
          <option value="Noise">Noise</option>
          <option value="Water Supply">Water Supply</option>
          <option value="Neighbor Dispute">Neighbor Dispute</option>
          <option value="Other">Other</option>
        </select>
        <select class="form-select form-select-sm" id="filterVerify" style="width: auto;">
          <option value="all">All Verification</option>
          <option value="unverified">Unverified only</option>
          <option value="verified">Verified only</option>
        </select>
      </div>

      <div class="map-layout">

        <!-- Side list, synced with the map -->
        <div class="map-side-list" id="sideList">
          <div class="side-list-title">Concerns (<span id="listCount">0</span>)</div>
          <!-- populated by JavaScript -->
        </div>

        <!-- Map -->
        <div class="map-container">
          <div id="fullConcernMap"></div>
          <div id="map3dWrap"><div id="concernMap3d"></div></div>

          <div class="basemap-switch" role="group" aria-label="Map view">
            <button type="button" data-mode="street"><i class="ti ti-map"></i> Street</button>
            <button type="button" data-mode="satellite"><i class="ti ti-satellite"></i> Satellite</button>
            <span class="sep" aria-hidden="true"></span>
            <button type="button" id="btn3d" aria-pressed="false" title="Tilted 3D view with terrain and buildings"><i class="ti ti-cube-3d-sphere"></i> 3D</button>
          </div>
          <div class="map-notice" id="mapNotice" role="status"></div>
          <div class="map-help">Right-drag or Ctrl + drag to tilt and rotate. Two fingers on a phone.</div>

          <div class="map-legend">
            <div class="legend-row"><span class="legend-dot" style="background:var(--c-pending);"></span> Pending</div>
            <div class="legend-row"><span class="legend-dot" style="background:var(--c-progress);"></span> In Progress</div>
            <div class="legend-row"><span class="legend-dot" style="background:var(--c-resolved);"></span> Resolved</div>
            <div class="legend-row"><span class="legend-dot" style="border: 2px solid var(--ref-red); background:#fff;"></span> Unverified (red ring)</div>
            <div class="legend-row"><span class="legend-dot" style="background:rgba(79,95,224,.18); border:1px solid #4F5FE0; width:14px; height:14px;"></span> Shaded area: where it could really be</div>
          </div>
        </div>

      </div>

    </div>
  </div>

  <script>
    // Real saved reports with a location, sent by AdminConcernController@map.
    // Anonymous reports already arrive with reporter = "Anonymous" (never a name).
    const concerns = @json($concerns);

    @php
      // How sure each pin is, keyed by tracking code (empty until the Nov 8 migration has run)
      $locAccuracy = [];
      try {
        if (\Illuminate\Support\Facades\Schema::hasColumn('concerns', 'location_accuracy')) {
          $locAccuracy = \App\Models\Concern::whereNotNull('latitude')
            ->get(['tracking_code', 'location_accuracy', 'location_adjusted'])
            ->mapWithKeys(fn ($c) => [$c->tracking_code => ['m' => $c->location_accuracy, 'hand' => (bool) $c->location_adjusted]])
            ->all();
        }
      } catch (\Throwable $e) {}
    @endphp
    // m = meters the phone was sure of, hand = the resident moved the pin themselves
    const accuracy = @json((object) $locAccuracy);

    // One line for the popup and side list
    function accuracyText(c) {
      const a = accuracy[c.ref];
      if (!a) return 'Location accuracy not recorded (older report)';
      if (a.hand) return 'Pin placed by hand by the resident';
      if (a.m == null) return 'Location accuracy not recorded';
      if (a.m <= 20) return 'Location accurate to about ' + a.m + ' m';
      if (a.m <= 50) return 'Location accurate to about ' + a.m + ' m (fair)';
      return 'Weak location: could be off by about ' + a.m + ' m';
    }
    function accuracyWeak(c) {
      const a = accuracy[c.ref];
      return !!(a && !a.hand && a.m != null && a.m > 50);
    }

    // Escape text before putting it into HTML (reports are typed by residents).
    function esc(v) { const d = document.createElement('div'); d.textContent = v == null ? '' : v; return d.innerHTML; }

    const statusColor = { pending: 'var(--c-pending)', progress: 'var(--c-progress)', blotter: 'var(--c-progress)', resolved: 'var(--c-resolved)' };
    const statusColorHex = { pending: '#F5B942', progress: '#4F5FE0', blotter: '#4F5FE0', resolved: '#4CAF50' };

    const map = L.map('fullConcernMap').setView([7.421, 125.706], 14);
    const street = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'
    });

    // 3D view state (filled in by the 3D section below)
    let map3d = null, is3d = false, markers3d = {}, lastFiltered = [];

    // Satellite = Esri World Imagery (free, no key) with road and place labels on top.
    const ESRI_IMAGERY = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
    const imagery = L.tileLayer(ESRI_IMAGERY, {
      maxZoom: 19, maxNativeZoom: 18,
      attribution: 'Imagery &copy; Esri, Maxar, Earthstar Geographics, and the GIS User Community'
    });
    const roads = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, maxNativeZoom: 18 });
    const places = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, maxNativeZoom: 18 });
    const satellite = L.layerGroup([imagery, roads, places]);

    // Street / Satellite works in 2D and 3D. The choice is remembered in this browser.
    let baseMode = 'street';
    function setBase(next) {
      baseMode = next === 'satellite' ? 'satellite' : 'street';
      if (baseMode === 'satellite') { map.removeLayer(street); satellite.addTo(map); }
      else { map.removeLayer(satellite); street.addTo(map); }
      if (map3d && map3d.getLayer('esri-imagery')) {
        map3d.setLayoutProperty('esri-imagery', 'visibility', baseMode === 'satellite' ? 'visible' : 'none');
      }
      document.querySelectorAll('.basemap-switch [data-mode]').forEach(b => {
        b.classList.toggle('active', b.dataset.mode === baseMode);
        b.setAttribute('aria-pressed', b.dataset.mode === baseMode);
      });
      try { localStorage.setItem('pb_basemap', baseMode); } catch (e) {}
    }
    document.querySelectorAll('.basemap-switch [data-mode]').forEach(b => b.addEventListener('click', () => setBase(b.dataset.mode)));
    let savedBase = 'street';
    try { savedBase = localStorage.getItem('pb_basemap') || 'street'; } catch (e) {}
    setBase(savedBase);

    let markers = {}; // keyed by ref, so the side list can open a specific marker's popup
    let circles = []; // shaded area: where the place could really be

    function buildIcon(c) {
      const ring = c.verify === 'unverified' ? ' unverified-ring' : '';
      return L.divIcon({
        className: '',
        html: `<div class="concern-marker${ring}" style="background:${statusColorHex[c.status]};"></div>`,
        iconSize: [22, 22],
        iconAnchor: [11, 11]
      });
    }

    function popupHtml(c) {
      const badge = c.reporter === 'Anonymous'
        ? `<span class="popup-badge popup-anon">Anonymous</span>`
        : `<span style="font-size:11.5px; color:var(--ref-gray-text);">${esc(c.reporter)}</span>`;
      return `
        <div style="font-size:13px; min-width:180px;">
          <div style="font-weight:700; margin-bottom:2px;">${esc(c.title)}</div>
          <div style="color:var(--ref-gray-text); font-size:11.5px; margin-bottom:6px;">
            ${esc(c.ref)} · ${esc(c.category)} · ${esc(c.zone)}
          </div>
          ${badge}
          <div style="margin-top:6px; font-size:11.5px; color:${accuracyWeak(c) ? 'var(--ref-red)' : 'var(--ref-gray-text)'};">
            <i class="ti ti-current-location"></i> ${esc(accuracyText(c))}
          </div>
          <div style="margin-top:8px;">
            <a href="{{ url('/admin/concerns') }}?q=${encodeURIComponent(c.ref)}" style="font-size:12px; color:var(--ref-blue);">View full details &rarr;</a>
          </div>
        </div>`;
    }

    function render() {
      const fStatus = document.getElementById('filterStatus').value;
      const fCategory = document.getElementById('filterCategory').value;
      const fVerify = document.getElementById('filterVerify').value;

      // Clear existing markers and accuracy circles
      Object.values(markers).forEach(m => map.removeLayer(m));
      markers = {};
      circles.forEach(ci => map.removeLayer(ci));
      circles = [];

      const filtered = concerns.filter(c =>
        (fStatus === 'all' || c.status === fStatus) &&
        (fCategory === 'all' || c.category === fCategory) &&
        (fVerify === 'all' || c.verify === fVerify)
      );

      // Rebuild markers
      filtered.forEach(c => {
        const marker = L.marker([c.lat, c.lng], { icon: buildIcon(c) })
          .addTo(map)
          .bindPopup(popupHtml(c));
        markers[c.ref] = marker;

        // Accuracy circle (not for pins the resident placed by hand)
        const a = accuracy[c.ref];
        if (a && !a.hand && a.m) {
          circles.push(L.circle([c.lat, c.lng], {
            radius: a.m, weight: 1, interactive: false,
            color: a.m > 50 ? '#D64545' : statusColorHex[c.status] || '#4F5FE0',
            fillOpacity: a.m > 50 ? 0.08 : 0.12
          }).addTo(map));
        }
      });
      drawPins3d(filtered);

      // Rebuild side list
      const list = document.getElementById('sideList');
      document.getElementById('listCount').textContent = filtered.length;

      // Remove old items (keep the title)
      list.querySelectorAll('.side-item').forEach(el => el.remove());

      filtered.forEach(c => {
        const item = document.createElement('div');
        item.className = 'side-item';
        item.innerHTML = `
          <div class="side-ref">${esc(c.ref)}</div>
          <div class="side-title"><span class="side-dot" style="background:${statusColorHex[c.status]};"></span>${esc(c.title)}</div>
          <div style="color:var(--ref-gray-text);">${esc(c.category)} · ${esc(c.zone)}</div>
          <div style="font-size:11px; color:${accuracyWeak(c) ? 'var(--ref-red)' : 'var(--ref-gray-text)'};">${esc(accuracyText(c))}</div>
        `;
        item.addEventListener('click', () => {
          if (is3d && map3d) {
            map3d.flyTo({ center: [c.lng, c.lat], zoom: 17, pitch: 60 });
            const m = markers3d[c.ref];
            if (m && !m.getPopup().isOpen()) m.togglePopup();
          } else {
            map.setView([c.lat, c.lng], 16);
            markers[c.ref].openPopup();
          }
        });
        list.appendChild(item);
      });
    }

    // ===================== 3D view (MapLibre GL JS) =====================
    // Free sources, no API key or billing: streets and building heights from
    // OpenFreeMap (OpenStreetMap data), satellite from Esri, terrain from AWS Terrain Tiles.
    // MapLibre is only downloaded the first time 3D is opened.
    const MAPLIBRE_JS = 'https://unpkg.com/maplibre-gl@5/dist/maplibre-gl.js';
    const MAPLIBRE_CSS = 'https://unpkg.com/maplibre-gl@5/dist/maplibre-gl.css';
    const STREET_STYLE = 'https://tiles.openfreemap.org/styles/liberty';
    const TERRAIN_TILES = 'https://s3.amazonaws.com/elevation-tiles-prod/terrarium/{z}/{x}/{y}.png';

    const container = document.querySelector('.map-container');
    const wrap3d = document.getElementById('map3dWrap');
    const btn3d = document.getElementById('btn3d');

    function notice(msg) {
      const n = document.getElementById('mapNotice');
      n.textContent = msg;
      n.classList.toggle('show', !!msg);
      if (msg) setTimeout(() => n.classList.remove('show'), 7000);
    }

    function hasWebGL() {
      try {
        const c = document.createElement('canvas');
        return !!(window.WebGL2RenderingContext && c.getContext('webgl2')) || !!c.getContext('webgl');
      } catch (e) { return false; }
    }

    function loadMapLibre() {
      if (window.maplibregl) return Promise.resolve();
      return new Promise((resolve, reject) => {
        const css = document.createElement('link');
        css.rel = 'stylesheet'; css.href = MAPLIBRE_CSS;
        document.head.appendChild(css);
        const js = document.createElement('script');
        js.src = MAPLIBRE_JS;
        js.onload = () => window.maplibregl ? resolve() : reject();
        js.onerror = reject;
        document.head.appendChild(js);
      });
    }

    // If 3D cannot run: say so, and show the 2D satellite map instead.
    function fallbackTo2d(msg) {
      show2d();
      setBase('satellite');
      notice(msg + ' Showing the 2D satellite map instead.');
    }

    function show2d() {
      if (is3d && map3d) {
        // MapLibre zoom is one level below Leaflet for the same view
        const c = map3d.getCenter();
        map.setView([c.lat, c.lng], Math.round(map3d.getZoom() + 1));
      }
      is3d = false;
      container.classList.remove('is-3d');
      wrap3d.classList.remove('show');
      btn3d.classList.remove('active');
      btn3d.setAttribute('aria-pressed', 'false');
      try { localStorage.setItem('pb_map_view', '2d'); } catch (e) {}
      setTimeout(() => map.invalidateSize(), 0);
    }

    async function show3d() {
      if (!hasWebGL()) { fallbackTo2d('This browser or device does not support 3D maps (WebGL).'); return; }
      btn3d.disabled = true;
      try {
        await loadMapLibre();
      } catch (e) {
        btn3d.disabled = false;
        fallbackTo2d('The 3D map could not be downloaded. Check the internet connection.');
        return;
      }
      btn3d.disabled = false;

      is3d = true;
      container.classList.add('is-3d');
      wrap3d.classList.add('show');
      btn3d.classList.add('active');
      btn3d.setAttribute('aria-pressed', 'true');
      try { localStorage.setItem('pb_map_view', '3d'); } catch (e) {}

      const c = map.getCenter();
      if (!map3d) {
        init3d([c.lng, c.lat], map.getZoom() - 1);
      } else {
        map3d.resize();
        map3d.jumpTo({ center: [c.lng, c.lat], zoom: map.getZoom() - 1 });
        map3d.easeTo({ pitch: 60, bearing: -20, duration: 600 });
      }
    }

    function init3d(center, zoom) {
      let loaded = false;
      map3d = new maplibregl.Map({
        container: 'concernMap3d', style: STREET_STYLE, center, zoom: Math.max(zoom, 14),
        pitch: 60, bearing: -20, maxPitch: 75, attributionControl: { compact: true }
      });
      map3d.addControl(new maplibregl.NavigationControl({ visualizePitch: true }), 'top-left');

      // Must load within 15 seconds; a single missing tile (has a sourceId) is fine.
      const timer = setTimeout(() => {
        if (!loaded) { destroy3d(); fallbackTo2d('The 3D map took too long to load.'); }
      }, 15000);
      map3d.on('error', e => {
        if (!loaded && !(e && e.sourceId)) { clearTimeout(timer); destroy3d(); fallbackTo2d('The 3D map could not be loaded.'); }
      });

      map3d.on('load', () => {
        loaded = true;
        clearTimeout(timer);
        const layers = map3d.getStyle().layers;
        const firstSymbol = layers.find(l => l.type === 'symbol');

        // Satellite imagery under the labels, shown when Satellite is picked
        map3d.addSource('esri', { type: 'raster', tiles: [ESRI_IMAGERY], tileSize: 256, maxzoom: 18,
          attribution: 'Imagery &copy; Esri, Maxar, Earthstar Geographics' });
        map3d.addLayer({ id: 'esri-imagery', type: 'raster', source: 'esri',
          layout: { visibility: baseMode === 'satellite' ? 'visible' : 'none' } }, firstSymbol ? firstSymbol.id : undefined);

        // Raised ground
        try {
          map3d.addSource('terrain', { type: 'raster-dem', tiles: [TERRAIN_TILES], encoding: 'terrarium',
            tileSize: 256, maxzoom: 15, attribution: 'Terrain: Mapzen / AWS Terrain Tiles' });
          map3d.setTerrain({ source: 'terrain', exaggeration: 1.5 });
        } catch (e) { /* still works flat */ }

        // Buildings raised by height, unless the style already has a 3D layer
        if (!layers.some(l => l.type === 'fill-extrusion')) {
          const sources = map3d.getStyle().sources;
          const src = Object.keys(sources).find(k => sources[k].type === 'vector');
          if (src) {
            map3d.addLayer({
              id: 'buildings-3d', type: 'fill-extrusion', source: src, 'source-layer': 'building', minzoom: 14,
              paint: {
                'fill-extrusion-color': '#D9DCE8',
                'fill-extrusion-height': ['coalesce', ['get', 'render_height'], 6],
                'fill-extrusion-base': ['coalesce', ['get', 'render_min_height'], 0],
                'fill-extrusion-opacity': 0.85
              }
            }, firstSymbol ? firstSymbol.id : undefined);
          }
        }
        drawPins3d(lastFiltered);
      });
    }

    function destroy3d() {
      Object.values(markers3d).forEach(m => m.remove());
      markers3d = {};
      if (map3d) { try { map3d.remove(); } catch (e) {} }
      map3d = null;
    }

    // Same pins as 2D: status color, red ring when unverified
    function drawPins3d(list) {
      lastFiltered = list;
      if (!map3d) return;
      Object.values(markers3d).forEach(m => m.remove());
      markers3d = {};
      list.forEach(c => {
        const el = document.createElement('div');
        el.className = 'concern-marker' + (c.verify === 'unverified' ? ' unverified-ring' : '');
        el.style.background = statusColorHex[c.status] || statusColorHex.pending;
        el.setAttribute('role', 'button');
        el.setAttribute('aria-label', c.ref + ', ' + c.category);
        markers3d[c.ref] = new maplibregl.Marker({ element: el })
          .setLngLat([c.lng, c.lat])
          .setPopup(new maplibregl.Popup({ offset: 14 }).setHTML(popupHtml(c)))
          .addTo(map3d);
      });
    }

    btn3d.addEventListener('click', () => { is3d ? show2d() : show3d(); });

    document.getElementById('filterStatus').addEventListener('change', render);
    document.getElementById('filterCategory').addEventListener('change', render);
    document.getElementById('filterVerify').addEventListener('change', render);

    render(); // initial draw

    let startView = new URLSearchParams(location.search).get('view');
    if (!startView) { try { startView = localStorage.getItem('pb_map_view'); } catch (e) {} }
    if (startView === '3d') show3d();
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
<link href="{{ asset('css/pabarang-admin.css') }}" rel="stylesheet">
</body>
</html>