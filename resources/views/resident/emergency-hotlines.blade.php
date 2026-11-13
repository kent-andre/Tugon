<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Emergency Hotlines - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="format-detection" content="telephone=yes">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --ref-blue: #4F5FE0; --ref-navy-text: #1E2340; --ref-gray-text: #6B7280;
      --ref-bg: #F5F6FA; --ref-border: #E4E6EF; --ref-red: #D64545; --ref-green: #1E8E4A;
    }
    body { font-family: 'Lato', sans-serif; color: var(--ref-navy-text); background: var(--ref-bg); }

    /* ===== Sidebar (shared across all resident pages) ===== */
    :root { --navy: #181a35; --navy-active: #5b52f5; }
    .main { margin-left: 250px; }
    .avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--navy-active); color: #fff;
      display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; cursor: pointer; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 18px 40px 0; }

    .page-wrap { max-width: 780px; margin: 0 auto; padding: 40px 20px 110px; }

    .urgent-banner {
      background: #FDE8E8; border: 1px solid #F3B8B8; border-radius: 10px;
      padding: 16px 18px; font-size: 14px; color: var(--ref-red); margin-bottom: 18px;
    }
    .tap-hint { display: none; font-size: 13px; color: var(--ref-gray-text); margin-bottom: 18px; }

    .group-heading { font-size: 15px; font-weight: 700; color: var(--ref-navy-text); margin: 28px 0 12px; }
    .group-heading:first-of-type { margin-top: 0; }

    /* One card per hotline. Single-number cards are one big tap target (tel: link). */
    .hotline-card {
      background: #fff; border: 1px solid var(--ref-border); border-radius: 12px;
      padding: 16px 18px; margin-bottom: 10px; display: flex; align-items: center; gap: 14px;
      text-decoration: none; color: inherit; -webkit-tap-highlight-color: rgba(79,95,224,.12);
      transition: transform .08s ease, box-shadow .15s ease;
    }
    a.hotline-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.06); color: inherit; }
    a.hotline-card:active { transform: scale(.985); background: #F7F8FF; }
    a.hotline-card:focus-visible, .call-btn:focus-visible { outline: 3px solid var(--ref-blue); outline-offset: 2px; }
    .hotline-card.is-urgent { border-color: #F3B8B8; }
    .hotline-card.multi { flex-wrap: wrap; }

    .hotline-icon { width: 42px; height: 42px; border-radius: 50%; background: var(--ref-bg); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .hotline-icon i { font-size: 19px; color: var(--ref-blue); }
    .is-urgent .hotline-icon { background: #FDE8E8; }
    .is-urgent .hotline-icon i { color: var(--ref-red); }
    .hotline-text { flex: 1; min-width: 0; }
    .hotline-name { font-size: 14.5px; font-weight: 700; }
    .hotline-desc { font-size: 12.5px; color: var(--ref-gray-text); }

    /* Call button: the number with a phone icon */
    .call-btn {
      display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; text-decoration: none;
      background: #E9FBF0; color: var(--ref-green); border: 1px solid #BFE9CF; border-radius: 999px;
      padding: 7px 14px; font-size: 14.5px; font-weight: 800;
    }
    .call-btn i { font-size: 16px; }
    .is-urgent .call-btn { background: var(--ref-red); border-color: var(--ref-red); color: #fff; font-size: 16px; }
    .call-label { font-size: 11px; font-weight: 600; opacity: .8; }
    .number-list { display: flex; flex-wrap: wrap; gap: 8px; width: 100%; padding-left: 56px; }
    .number-list .call-btn:active { transform: scale(.97); }

    .source-note { font-size: 12px; color: var(--ref-gray-text); margin-top: 30px; }

    /* Phones: hint text, bigger targets, and a fixed "Call 911" bar */
    .sos-bar { display: none; }
    @media (max-width: 767px) {
      .tap-hint { display: block; }
      .page-wrap { padding: 24px 14px 110px; }
      .hotline-card { padding: 14px; }
      .number-list { padding-left: 0; }
      .number-list .call-btn { flex: 1 1 100%; justify-content: center; padding: 11px 14px; }
      .sos-bar {
        /* Below the menu (1045-1050) so the open menu covers it */
        display: flex; position: fixed; left: 12px; right: 12px; bottom: 12px; z-index: 1030;
        align-items: center; justify-content: center; gap: 8px; padding: 14px; border-radius: 14px;
        background: var(--ref-red); color: #fff; font-size: 17px; font-weight: 800; text-decoration: none;
        box-shadow: 0 6px 18px rgba(214,69,69,.35);
      }
      .sos-bar:active { transform: scale(.98); }
    }
  </style>
</head>
<body>

  @php
    /*
     | Hotline list. Each number: [what to dial, what to show].
     | Sources checked Nov 2026:
     |  - 911, Red Cross 143: Emergency 911 National Office / Philippine Red Cross
     |  - NCMH 1553, 0919 057 1553, 0917 899 8727: DOH / NCMH (Dec 2025 announcements)
     |  - DavNor 911 Emergency & Disaster Hotline: davaodelnorte.gov.ph (PDRRMC contact page)
     |  - Carmen 24/7 response 0950 222 1280 and (084) 628 8667: municipal 24/7 radio
     |    communication service, as listed by a local directory (not an official LGU page)
     |  - Carmen police 0907 227 5245: not verified against an official source
     |  - Davao del Norte Hospital - Carmen Zone (084) 823 3120: HealthSpace facility directory
     | TODO before the defense: call each local number once to confirm it still works.
     */
    $groups = [
      'National Emergency Numbers' => [
        ['name' => 'National Emergency Hotline', 'desc' => 'Police, fire, ambulance and rescue: all-in-one, 24/7',
         'icon' => 'ti-phone-call', 'urgent' => true, 'numbers' => [['911', '911']]],
        ['name' => 'Philippine Red Cross', 'desc' => 'Emergency response, ambulance and disaster help',
         'icon' => 'ti-first-aid-kit', 'numbers' => [['143', '143']]],
        ['name' => 'NCMH Crisis Hotline', 'desc' => 'Mental health and suicide crisis support, 24/7',
         'icon' => 'ti-heart-handshake', 'numbers' => [
            ['1553', '1553', 'Landline'],
            ['09190571553', '0919 057 1553', 'Smart / TNT'],
            ['09178998727', '0917 899 8727', 'Globe / TM'],
         ]],
      ],
      'Province of Davao del Norte' => [
        ['name' => 'DavNor 911 Emergency & Disaster Hotline', 'desc' => 'Provincial disaster and emergency response (PDRRMO), Tagum City',
         'icon' => 'ti-alert-octagon', 'numbers' => [
            ['09992221967', '0999 222 1967', 'Smart'],
            ['09178103526', '0917 810 3526', 'Globe'],
            ['0846559415', '(084) 655 9415', 'Landline'],
         ]],
      ],
      'Municipality of Carmen, Davao del Norte' => [
        ['name' => 'Carmen 24/7 Emergency Response', 'desc' => 'Police, rescue, fire, and medical assistance',
         'icon' => 'ti-radio', 'numbers' => [
            ['09502221280', '0950 222 1280', 'Mobile'],
            ['0846288667', '(084) 628 8667', 'Landline'],
         ]],
        ['name' => 'Carmen Municipal Police Station', 'desc' => 'Local police station, Carmen',
         'icon' => 'ti-shield', 'numbers' => [['09072275245', '0907 227 5245']]],
        ['name' => 'Davao del Norte Hospital – Carmen Zone', 'desc' => 'Government hospital, National Highway, Carmen',
         'icon' => 'ti-building-hospital', 'numbers' => [['0848233120', '(084) 823 3120']]],
      ],
    ];

    // Set once the barangay hall / tanod number is confirmed; the card stays hidden until then.
    $barangayHallNumber = null;
    if ($barangayHallNumber) {
      $groups['Barangay Alejal'] = [
        ['name' => 'Barangay Alejal Hall / Tanod', 'desc' => 'Barangay hall and tanod on duty',
         'icon' => 'ti-home', 'numbers' => [[preg_replace('/\D+/', '', $barangayHallNumber), $barangayHallNumber]]],
      ];
    }
  @endphp

  @include('partials.resident-sidebar')

  <div class="main">
    <div class="top-bar">
      <h5 class="fw-bold mb-0">Emergency Hotlines</h5>
    </div>

  <div class="page-wrap">
    <h3 class="fw-bold mb-1">Emergency Hotlines</h3>
    <p style="color: var(--ref-gray-text); font-size: 14px;" class="mb-4">
      Numbers for Barangay Alejal, Carmen, Davao del Norte and nearby emergency services.
    </p>

    <div class="urgent-banner">
      ⚠ If you are in immediate danger, call <a href="tel:911" style="color: inherit; font-weight: 800;">911</a> first.
      In an active emergency, don't wait to submit a report through this site.
    </div>

    <p class="tap-hint"><i class="ti ti-hand-finger"></i> Tap a hotline to open your phone's dialer with the number ready.</p>

    @foreach ($groups as $heading => $hotlines)
      <div class="group-heading">{{ $heading }}</div>

      @foreach ($hotlines as $h)
        @php $urgent = ! empty($h['urgent']); @endphp

        @if (count($h['numbers']) === 1)
          @php [$dial, $shown] = $h['numbers'][0]; @endphp
          {{-- The whole card dials the number --}}
          <a href="tel:{{ $dial }}" class="hotline-card {{ $urgent ? 'is-urgent' : '' }}" aria-label="Call {{ $h['name'] }}, {{ $shown }}">
            <div class="hotline-icon"><i class="ti {{ $h['icon'] }}"></i></div>
            <div class="hotline-text">
              <div class="hotline-name">{{ $h['name'] }}</div>
              <div class="hotline-desc">{{ $h['desc'] }}</div>
            </div>
            <span class="call-btn"><i class="ti ti-phone"></i>{{ $shown }}</span>
          </a>
        @else
          {{-- Several numbers: each button dials its own number --}}
          <div class="hotline-card multi {{ $urgent ? 'is-urgent' : '' }}">
            <div class="hotline-icon"><i class="ti {{ $h['icon'] }}"></i></div>
            <div class="hotline-text">
              <div class="hotline-name">{{ $h['name'] }}</div>
              <div class="hotline-desc">{{ $h['desc'] }}</div>
            </div>
            <div class="number-list">
              @foreach ($h['numbers'] as $n)
                <a href="tel:{{ $n[0] }}" class="call-btn" aria-label="Call {{ $h['name'] }}, {{ $n[1] }}{{ isset($n[2]) ? ', ' . $n[2] : '' }}">
                  <i class="ti ti-phone"></i>{{ $n[1] }}
                  @isset($n[2])<span class="call-label">{{ $n[2] }}</span>@endisset
                </a>
              @endforeach
            </div>
          </div>
        @endif
      @endforeach
    @endforeach

    <p class="source-note">
      Calls are made from your own phone.
      Numbers can change. If a call doesn't connect, confirm with the municipal or barangay office.
    </p>
  </div>
  </div>

  {{-- Phones only: always-visible emergency call button --}}
  <a href="tel:911" class="sos-bar" aria-label="Call 911 now"><i class="ti ti-phone-call"></i> Call 911</a>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
<script src="{{ asset('js/pabarang.js') }}"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
