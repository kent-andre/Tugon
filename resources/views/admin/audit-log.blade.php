<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Audit Log - Tugon Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --ref-blue: #4F5FE0; --ref-navy-text: #1E2340; --ref-gray-text: #6B7280;
      --ref-bg: #F5F6FA; --ref-border: #E4E6EF; --ref-red: #D64545;
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

    .admin-main { flex: 1; padding: 28px 36px; }
    .admin-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
    .admin-topbar h4 { font-weight: 800; margin: 0; }
    .admin-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; }

    .stat-strip { display: flex; gap: 14px; margin-bottom: 22px; flex-wrap: wrap; }
    .stat-mini { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 14px 18px; flex: 1; min-width: 120px; }
    .stat-mini .stat-number { font-size: 22px; font-weight: 800; }
    .stat-mini .stat-label { font-size: 11.5px; color: var(--ref-gray-text); }

    .filter-bar { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 14px 18px; margin-bottom: 18px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .filter-bar select, .filter-bar input { font-size: 13px; }
    .filter-bar input[type="search"] { flex: 1 1 240px; }

    .box { background: #fff; border: 1px solid var(--ref-border); border-radius: 14px; overflow: hidden; }
    .log-table { margin: 0; --bs-table-bg: transparent; }
    .log-table th {
      font-size: 12.5px; font-weight: 700; color: var(--ref-gray-text); text-transform: uppercase; letter-spacing: .03em;
      padding: 13px 16px; border-bottom: 1px solid var(--ref-border); white-space: nowrap; background: #FAFAFD;
    }
    .log-table td { padding: 14px 16px; vertical-align: middle; border-bottom: 1px solid var(--ref-border); font-size: 13.5px; }
    .log-table tbody tr:last-child td { border-bottom: none; }
    .log-time { color: var(--ref-gray-text); white-space: nowrap; font-size: 13px; }
    .log-ip { color: var(--ref-gray-text); font-size: 12.5px; white-space: nowrap; }

    .cat-badge { font-size: 10.5px; font-weight: 700; padding: 3px 9px; border-radius: 10px; white-space: nowrap; }
    .cat-concerns      { background: #FDE8E8; color: var(--ref-red); }
    .cat-documents     { background: #E1EEFB; color: #0C5FA1; }
    .cat-blotters      { background: #F0E6FB; color: #7A3FC2; }
    .cat-announcements { background: #E7EAFD; color: var(--ref-blue); }
    .cat-officials     { background: #E1F5E4; color: #1E7A34; }
    .cat-accounts      { background: #FDF1D8; color: #92650C; }
    .cat-system        { background: #EDEDED; color: #555; }

    .empty { padding: 56px 20px; text-align: center; color: var(--ref-gray-text); }
    .empty i { font-size: 34px; color: #B8BDD6; display: block; margin-bottom: 8px; }

    .table-foot { padding: 13px 20px; border-top: 1px solid var(--ref-border); font-size: 13px; color: var(--ref-gray-text); }
  </style>
</head>
<body>

  <div class="admin-layout">

    @include('partials.admin-sidebar')


    <!-- Main content -->
    <div class="admin-main">

      <div class="admin-topbar">
        <h4>Audit Log</h4>
        @include('partials.admin-user')
      </div>

      <div class="stat-strip">
        <div class="stat-mini"><div class="stat-number">{{ $totalCount }}</div><div class="stat-label">Total Entries</div></div>
        <div class="stat-mini"><div class="stat-number">{{ $todayCount }}</div><div class="stat-label">Logged Today</div></div>
        <div class="stat-mini"><div class="stat-number">{{ $weekCount }}</div><div class="stat-label">Last 7 Days</div></div>
        <div class="stat-mini"><div class="stat-number">{{ $failedCount }}</div><div class="stat-label">Failed Logins</div></div>
      </div>

      <div class="filter-bar">
        <input type="search" class="form-control form-control-sm" id="searchInput" placeholder="Search by action, official, or IP address">
        <select class="form-select form-select-sm" id="filterCategory" style="width: auto;">
          <option value="all">All Categories</option>
          @foreach ($categoryLabels as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
        <select class="form-select form-select-sm" id="filterRange" style="width: auto;">
          <option value="all">Any Date</option>
          <option value="today">Today</option>
          <option value="7">Last 7 days</option>
          <option value="30">Last 30 days</option>
        </select>
      </div>

      <div class="box">
        <div class="table-responsive">
          <table class="table log-table">
            <thead>
              <tr>
                <th>Timestamp</th>
                <th>Action</th>
                <th>By</th>
                <th>Category</th>
                <th>IP Address</th>
              </tr>
            </thead>
            <tbody id="logRows">
              @foreach ($logs as $l)
                <tr data-category="{{ $l['category'] }}"
                    data-search="{{ strtolower($l['action'].' '.$l['actor'].' '.$l['ip']) }}" data-ts="{{ $l['ts'] }}">
                  <td class="log-time">{{ $l['at'] }}</td>
                  <td>@if (str_contains($l['action'], 'Failed login'))<i class="ti ti-alert-circle me-1" style="color:var(--ref-red);"></i>@endif{{ $l['action'] }}</td>
                  <td class="log-ip">{{ $l['actor'] }}</td>
                  <td><span class="cat-badge cat-{{ $l['category'] }}">{{ $categoryLabels[$l['category']] }}</span></td>
                  <td class="log-ip">{{ $l['ip'] }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="empty d-none" id="emptyState">
          <i class="ti ti-history-toggle"></i>
          <strong>{{ count($logs) ? 'No log entries match these filters' : 'Nothing has been logged yet' }}</strong>
        </div>

        <div class="table-foot" id="tableFoot"></div>
      </div>

    </div>
  </div>

  <script>
    // Filters the rows already on the page (the latest {{ $limit }} entries).
    const rows = Array.from(document.querySelectorAll('#logRows tr'));
    const total = rows.length;
    const DAY = 24 * 60 * 60 * 1000;

    function inRange(ts, range) {
      if (range === 'all') return true;
      if (range === 'today') {
        const start = new Date();
        start.setHours(0, 0, 0, 0);
        return ts >= start.getTime();
      }
      return ts >= Date.now() - Number(range) * DAY;
    }

    function applyFilters() {
      const q = document.getElementById('searchInput').value.trim().toLowerCase();
      const category = document.getElementById('filterCategory').value;
      const range = document.getElementById('filterRange').value;
      let visible = 0;

      rows.forEach(row => {
        const match = (category === 'all' || row.dataset.category === category)
          && (!q || row.dataset.search.includes(q))
          && inRange(Number(row.dataset.ts), range);
        row.classList.toggle('d-none', !match);
        if (match) visible++;
      });

      document.getElementById('emptyState').classList.toggle('d-none', visible > 0);
      let foot = `Showing ${visible} of ${total} entries`;
      if ({{ $totalCount }} > total) foot += ` (latest ${total} of {{ $totalCount }})`;
      document.getElementById('tableFoot').textContent = foot;
    }

    ['searchInput', 'filterCategory', 'filterRange'].forEach(id => {
      document.getElementById(id).addEventListener('input', applyFilters);
      document.getElementById(id).addEventListener('change', applyFilters);
    });

    applyFilters();
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <link href="{{ asset('css/pabarang-admin.css') }}" rel="stylesheet">
</body>
</html>
