<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Officials Directory - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --ref-blue: #4F5FE0; --ref-navy-text: #1E2340; --ref-gray-text: #6B7280;
      --ref-bg: #F5F6FA; --ref-border: #E4E6EF;
    }
    body { font-family: 'Lato', sans-serif; color: var(--ref-navy-text); background: var(--ref-bg); }

    /* ===== Sidebar (shared across all resident pages) ===== */
    :root { --navy: #181a35; --navy-active: #5b52f5; }
    .main { margin-left: 250px; }
    .avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--navy-active); color: #fff;
      display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; cursor: pointer; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 18px 40px 0; }

    .page-wrap { max-width: 980px; margin: 0 auto; padding: 40px 20px; }
    .section-label { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--ref-gray-text); margin: 32px 0 16px; }
    .section-label.first { margin-top: 0; }

    .official-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 20px; text-align: center; height: 100%; }
    .official-photo { width: 96px; height: 96px; aspect-ratio: 1; border-radius: 50%; object-fit: cover; object-position: center 25%; margin: 0 auto 12px; display: block; }  /* always a circle; faces sit near the top */
    .official-initials { width: 96px; height: 96px; border-radius: 50%; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center;
      font-size: 30px; font-weight: 800; color: #fff; }
    .official-name { font-size: 14.5px; font-weight: 700; }
    .official-role { font-size: 12.5px; color: var(--ref-blue); font-weight: 600; margin-top: 2px; }
    .official-committee { font-size: 11.5px; color: var(--ref-gray-text); margin-top: 6px; }
  </style>
</head>
<body>

  @include('partials.resident-sidebar')

  <div class="main">
    <div class="top-bar">
      <h5 class="fw-bold mb-0">Officials Directory</h5>
    </div>

  <div class="page-wrap">
    <h3 class="fw-bold mb-1">Barangay Officials Directory</h3>
    <p style="color: var(--ref-gray-text); font-size: 14px;" class="mb-4">
      Elected and appointed officials serving Barangay Alejal, Carmen, Davao del Norte.
    </p>
    {{-- Data: Resident\PortalController@officials - same `officials` table the admin edits. --}}

    @php $colors = ['council' => '4F5FE0', 'sk' => '0C5FA1', 'tanod' => '92650C', 'lupon' => 'D64545']; $firstSection = true; @endphp

    @foreach ($groupLabels as $key => $label)
      @php $list = $officials->get($key, collect()); @endphp
      @continue($list->isEmpty())
      <div class="section-label {{ $firstSection ? 'first' : '' }}">{{ $label }}</div>
      @php $firstSection = false; @endphp
      <div class="row g-3">
        @foreach ($list as $o)
          <div class="col-6 col-md-4 col-lg-3">
            <div class="official-card">
              @php $bg = $o->position === 'Punong Barangay' ? '1E2340' : $colors[$key]; @endphp
              @if ($o->photoUrl())
                <img src="{{ $o->photoUrl() }}" class="official-photo" alt="Photo of {{ $o->name }}"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="official-initials" style="background:#{{ $bg }}; display:none;" aria-hidden="true">{{ $o->initials() }}</div>
              @else
                <div class="official-initials" style="background:#{{ $bg }}" aria-hidden="true">{{ $o->initials() }}</div>
              @endif
              <div class="official-name">{{ $o->name }}</div>
              <div class="official-role">{{ $o->position }}</div>
              @if ($o->committee)<div class="official-committee">{{ $o->committee }}</div>@endif
            </div>
          </div>
        @endforeach
      </div>
    @endforeach

    @if ($officials->isEmpty())
      <p class="text-center py-5" style="color: var(--ref-gray-text);">The officials list has not been published yet.</p>
    @endif

    <p class="mt-4" style="font-size: 12px; color: var(--ref-gray-text);">
      Not an official government record. For certified information, visit the Barangay Alejal hall.
    </p>
  </div>
  </div>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <script src="{{ asset('js/pabarang.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
