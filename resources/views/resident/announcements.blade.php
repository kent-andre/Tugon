<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Announcements - Tugon</title>
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

    .page-wrap { max-width: 860px; margin: 0 auto; padding: 40px 20px; }

    .filter-pill { border: 1px solid var(--ref-border); background: #fff; border-radius: 20px; padding: 6px 16px; font-size: 13px; color: var(--ref-gray-text); cursor: pointer; }
    .filter-pill.active { background: var(--ref-blue); border-color: var(--ref-blue); color: #fff; }

    .announcement-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 20px 22px; margin-bottom: 14px; }
    .category-tag { font-size: 11px; font-weight: 700; letter-spacing: 0.04em; padding: 3px 10px; border-radius: 10px; }
    .tag-health { background: #E1F5E4; color: #1E7A34; }
    .tag-advisory { background: #FDE8E8; color: #D64545; }
    .tag-utility { background: #FDF1D8; color: #92650C; }
    .tag-event { background: #E7EAFD; color: var(--ref-blue); }
    .tag-assistance { background: #E1EEFB; color: #0C5FA1; }

    .announcement-title { font-size: 16px; font-weight: 700; margin: 10px 0 6px; }
    .announcement-meta { font-size: 12px; color: var(--ref-gray-text); margin-bottom: 10px; }
    .announcement-body { font-size: 13.5px; color: var(--ref-navy-text); white-space: pre-line; }
    .pinned-badge { font-size: 11px; color: var(--ref-blue); font-weight: 700; }
  </style>
</head>
<body>

  @include('partials.resident-sidebar')

  <div class="main">
    <div class="top-bar">
      <h5 class="fw-bold mb-0">Announcements</h5>
    </div>

  <div class="page-wrap">
    <h3 class="fw-bold mb-1">Announcements</h3>
    <p style="color: var(--ref-gray-text); font-size: 14px;" class="mb-3">
      Official notices and updates from Barangay Alejal.
    </p>

    {{-- Data: Resident\PortalController@announcements (pinned first, newest next). --}}
    <div class="d-flex gap-2 mb-4 flex-wrap" role="group" aria-label="Filter by category">
      <button type="button" class="filter-pill active" data-filter="all">All</button>
      @foreach ($categories as $key => $label)
        <button type="button" class="filter-pill" data-filter="{{ $key }}">{{ ucwords(strtolower($label)) }}</button>
      @endforeach
    </div>

    @foreach ($announcements as $a)
      <div class="announcement-card" data-category="{{ $a->category }}">
        <div class="d-flex justify-content-between align-items-start">
          <span class="category-tag tag-{{ $a->category }}">{{ $a->label() }}</span>
          @if ($a->pinned)<span class="pinned-badge"><i class="ti ti-pin"></i> Pinned</span>@endif
        </div>
        <div class="announcement-title">{{ $a->title }}</div>
        <div class="announcement-meta">Posted by {{ $a->posted_by }} · {{ $a->created_at->format('F j, Y') }}</div>
        <div class="announcement-body">{{ $a->body }}</div>
      </div>
    @endforeach

    <div id="emptyState" class="text-center py-5 {{ $announcements->isEmpty() ? '' : 'd-none' }}" style="color: var(--ref-gray-text); font-size: 14px;">
      <i class="ti ti-speakerphone d-block mb-2" style="font-size: 30px; color: #B8BDD6;"></i>
      No announcements here yet.
    </div>

  </div>
  </div>

  <script>
    document.querySelectorAll('.filter-pill').forEach(pill => {
      pill.addEventListener('click', function () {
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        const f = this.dataset.filter;
        let shown = 0;
        document.querySelectorAll('.announcement-card').forEach(card => {
          const match = f === 'all' || card.dataset.category === f;
          card.classList.toggle('d-none', !match);
          if (match) shown++;
        });
        document.getElementById('emptyState').classList.toggle('d-none', shown > 0);
      });
    });
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <script src="{{ asset('js/pabarang.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
