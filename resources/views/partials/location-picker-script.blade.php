{{--
  Shared location picker for both concern forms (landing page and resident Submit a Concern).
  The page needs these ids: useLocationBtn, locationText, latitudeInput, longitudeInput,
  accuracyInput, adjustedInput, locationMap. Optional: locationMissing, locationDrop.

  Why: a single quick reading often comes from Wi-Fi or cell towers and can be off by
  hundreds of meters. Here the phone's GPS is asked for several readings for up to
  12 seconds, the most accurate one is kept, and the resident can drag the pin to the
  exact spot. The accuracy (in meters) is saved so officials know how sure it is.

  Weak readings (over 50 m), computers without GPS, and failed readings all ask the
  resident to tap their exact spot. The form will not send an unconfirmed weak pin.
--}}
<script>
(function () {
    var el = function (id) { return document.getElementById(id); };
    var btn = el('useLocationBtn'), out = el('locationText');
    var latI = el('latitudeInput'), lngI = el('longitudeInput');
    var accI = el('accuracyInput'), adjI = el('adjustedInput'), box = el('locationMap');
    if (!btn || !out || !latI || !lngI) return;

    var GOOD = 20, WEAK = 50, MAX_WAIT = 12000;
    var BARANGAY = { lat: 7.421, lng: 125.706 };   // map center when there is no reading at all
    var map = null, marker = null, circle = null, watchId = null, timer = null, best = null;
    var needsConfirm = false;   // true while a weak pin waits for the resident to tap the right spot
    var isComputer = !(/Android|iPhone|iPad|iPod/i.test(navigator.userAgent) || navigator.maxTouchPoints > 1);

    function say(html, tone) {
        out.innerHTML = html;
        out.style.color = tone === 'bad' ? '#E57373' : '';
    }
    function setPoint(lat, lng) {
        latI.value = lat.toFixed(6);
        lngI.value = lng.toFixed(6);
        if (el('locationMissing')) el('locationMissing').classList.add('d-none');
        if (el('locationDrop')) el('locationDrop').classList.remove('is-invalid');
    }
    function stopWatching() {
        if (watchId !== null) navigator.geolocation.clearWatch(watchId);
        watchId = null;
        clearTimeout(timer);
        btn.disabled = false;
    }

    // Leaflet is only downloaded when a location is captured
    function loadLeaflet() {
        if (window.L) return Promise.resolve();
        return new Promise(function (resolve, reject) {
            var css = document.createElement('link');
            css.rel = 'stylesheet';
            css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(css);
            var js = document.createElement('script');
            js.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            js.onload = function () { window.L ? resolve() : reject(); };
            js.onerror = reject;
            document.head.appendChild(js);
        });
    }

    function markBox() {
        if (!box) return;
        box.style.outline = needsConfirm ? '3px solid #E57373' : '';
        box.style.outlineOffset = needsConfirm ? '2px' : '';
    }

    // p = { lat, lng, accuracy } or null when there is no reading (pin waits for a tap)
    function showMap(p) {
        if (!box) return;
        loadLeaflet().then(function () {
            box.classList.remove('d-none');
            markBox();
            var center = p || BARANGAY;
            if (!map) {
                map = L.map(box, { zoomControl: true, attributionControl: true });
                // Satellite shows houses and roads, which helps people find their exact spot
                L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19, maxNativeZoom: 18, attribution: 'Imagery &copy; Esri'
                }).addTo(map);
                L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19, maxNativeZoom: 18
                }).addTo(map);
                marker = L.marker([center.lat, center.lng], { draggable: true, autoPan: true });
                marker.on('dragend', function () { movedByHand(marker.getLatLng()); });
                map.on('click', function (e) { marker.setLatLng(e.latlng).addTo(map); movedByHand(e.latlng); });
            }
            if (circle) { map.removeLayer(circle); circle = null; }
            if (p) {
                marker.setLatLng([p.lat, p.lng]).addTo(map);
                // The shaded area shows how far off the reading may be (capped so it stays readable)
                if (p.accuracy) circle = L.circle([p.lat, p.lng], { radius: Math.min(p.accuracy, 1500), color: p.accuracy > WEAK ? '#E57373' : '#7FB2FF', weight: 1, fillOpacity: .12, interactive: false }).addTo(map);
            } else {
                map.removeLayer(marker);
            }
            var zoom = !p ? 16 : (p.accuracy > 300 ? 16 : (p.accuracy > WEAK ? 17 : 18));
            map.setView([center.lat, center.lng], zoom);
            setTimeout(function () { map.invalidateSize(); }, 50);
        }).catch(function () { /* no map, the coordinates are still saved */ });
    }

    function movedByHand(ll) {
        setPoint(ll.lat, ll.lng);
        if (adjI) adjI.value = '1';
        needsConfirm = false;
        markBox();
        say('Pin placed by you at <span class="font-monospace">' + ll.lat.toFixed(5) + ', ' + ll.lng.toFixed(5) + '</span>. Officials will see that you placed it.');
    }

    // No reading at all: show the barangay map and ask for a tap
    function askForTap(reason) {
        stopWatching();
        [latI, lngI, accI].forEach(function (i) { if (i) i.value = ''; });
        needsConfirm = false;   // nothing to confirm yet; a tap sets the spot
        say(reason + ' <strong>Tap your exact spot on the map below.</strong>', 'bad');
        showMap(null);
    }

    function finish() {
        stopWatching();
        if (!best) {
            askForTap('Could not get your location.');
            return;
        }
        setPoint(best.lat, best.lng);
        if (accI) accI.value = Math.round(best.accuracy);
        if (adjI) adjI.value = '0';
        var m = Math.round(best.accuracy);
        if (isComputer) {
            needsConfirm = true;
            say('Computers have no GPS, so this spot is only a guess (about ' + m + ' m). <strong>Tap your exact spot on the map</strong> before sending.', 'bad');
        } else if (m <= WEAK) {
            needsConfirm = false;
            say('Location found, accurate to about <strong>' + m + ' m</strong>. If the pin is not exactly right, drag it or tap the spot on the map.');
        } else {
            needsConfirm = true;
            say('Your phone is not sure where you are (could be off by about ' + m + ' m). <strong>Tap your exact spot on the map</strong> before sending, or step outside and tap the button again.', 'bad');
        }
        showMap(best);
    }

    btn.addEventListener('click', function () {
        if (!navigator.geolocation) { askForTap('Location is not supported by this browser.'); return; }
        stopWatching();
        best = null;
        needsConfirm = false;
        markBox();
        btn.disabled = true;
        say('Finding your exact spot. Stay still for a few seconds, ideally outdoors...');

        watchId = navigator.geolocation.watchPosition(function (pos) {
            var c = pos.coords;
            if (!best || c.accuracy < best.accuracy) best = { lat: c.latitude, lng: c.longitude, accuracy: c.accuracy };
            say('Finding your exact spot... now accurate to about <strong>' + Math.round(best.accuracy) + ' m</strong>');
            if (best.accuracy <= GOOD) finish();
        }, function (err) {
            if (best) return;   // keep the readings we already have
            askForTap(err.code === 1 ? 'Location is blocked for this site.' : 'Could not get your location.');
        }, { enableHighAccuracy: true, maximumAge: 0, timeout: MAX_WAIT });

        // Computers have no GPS, so there is no point waiting long for a better reading
        timer = setTimeout(finish, isComputer ? 4000 : MAX_WAIT);
    });

    // A weak pin that was never confirmed stops the form from sending.
    // Runs before the page's own submit code (capture), so nothing is sent.
    if (btn.form) {
        btn.form.addEventListener('submit', function (e) {
            if (!(needsConfirm && latI.value)) return;
            e.preventDefault();
            e.stopImmediatePropagation();
            say('Please <strong>tap your exact spot on the map</strong> first. The current pin may be far from where you are.', 'bad');
            markBox();
            (box || out).scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, true);
    }

    // Lets a page clear everything after a report is sent
    window.tugonLocationReset = function () {
        stopWatching();
        best = null;
        needsConfirm = false;
        markBox();
        [latI, lngI, accI].forEach(function (i) { if (i) i.value = ''; });
        if (adjI) adjI.value = '0';
        if (box) box.classList.add('d-none');
        say('No location captured yet.');
    };
})();
</script>
