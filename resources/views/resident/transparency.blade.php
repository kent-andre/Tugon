<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transparency Dashboard - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

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

    .privacy-banner {
      background: #E7EAFD; border: 1px solid #C6CBF7; border-radius: 10px;
      padding: 14px 16px; font-size: 13px; color: var(--ref-navy-text); margin-bottom: 28px;
    }

    .stat-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 20px; text-align: center; height: 100%; }
    .stat-card .stat-number { font-size: 28px; font-weight: 800; }
    .stat-card .stat-label { font-size: 12.5px; color: var(--ref-gray-text); margin-top: 4px; }

    .chart-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 24px; height: 100%; }
    .chart-title { font-size: 15px; font-weight: 700; margin-bottom: 16px; }
    .chart-card canvas { max-width: 100% !important; }  /* never wider than its card, even before the chart draws */

    .updated-note { font-size: 12px; color: var(--ref-gray-text); text-align: center; margin-top: 24px; }
  </style>
</head>
<body>

  @include('partials.resident-sidebar')

  <div class="main">
    <div class="top-bar">
      <h5 class="fw-bold mb-0">Transparency Dashboard</h5>
    </div>



  <div class="page-wrap">
    <h3 class="fw-bold mb-1">Transparency Dashboard</h3>
    <p style="color: var(--ref-gray-text); font-size: 14px;" class="mb-3">
      Barangay-wide concern statistics.
    </p>

    {{--
      Data: Resident\PortalController@transparency. Aggregates only (counts and an
      average); this page must never list individual concerns.
    --}}
    @php
      // Same totals the charts below use. An average from only a handful of
      // cases says nothing (and can point at one person in a small barangay),
      // so it stays hidden until there are enough resolved cases.
      $minForAverage = 5;
      $rate = $total > 0 ? round(($resolved / $total) * 100) : 0;
    @endphp
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-number" style="color: var(--ref-navy-text);">{{ $total }}</div><div class="stat-label">Total Concerns</div></div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-number" style="color: var(--ref-navy-text);">{{ $resolved }}</div><div class="stat-label">Resolved</div></div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-number" style="color: var(--ref-navy-text);">{{ $rate }}%</div><div class="stat-label">Resolution Rate</div></div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-number" style="color: var(--ref-navy-text);">{{ $resolved >= $minForAverage ? $avgDays . ' days' : 'N/A' }}</div><div class="stat-label">Avg. Resolution Time{{ $resolved >= $minForAverage ? '' : ' (needs ' . $minForAverage . '+ resolved)' }}</div></div>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="chart-card">
          <div class="chart-title">By Status</div>
          <canvas id="statusChart" height="200"></canvas>
        </div>
      </div>
      <div class="col-md-4">
        <div class="chart-card">
          <div class="chart-title">By Category</div>
          <canvas id="categoryChart" height="200"></canvas>
        </div>
      </div>
      <div class="col-md-4">
        <div class="chart-card">
          <div class="chart-title">By Zone / Purok</div>
          <canvas id="zoneChart" height="200"></canvas>
        </div>
      </div>
    </div>

    <p class="updated-note">Figures as of {{ $dataAsOf }}</p>
  </div>
  </div>

  <script>
    // Grouped counts from PortalController@transparency.
    new Chart(document.getElementById('statusChart'), {
      type: 'doughnut',
      data: {
        labels: ['Pending', 'In Progress', 'Resolved'],
        datasets: [{
          data: @json($byStatus),
          backgroundColor: ['#F5B942', '#4F5FE0', '#4CAF50']
        }]
      },
      options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } }
    });

    new Chart(document.getElementById('categoryChart'), {
      type: 'bar',
      data: {
        labels: @json($catLabels),
        datasets: [{
          data: @json($catData),
          backgroundColor: '#4F5FE0'
        }]
      },
      options: {
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { ticks: { stepSize: 1 } } }
      }
    });

    new Chart(document.getElementById('zoneChart'), {
      type: 'bar',
      data: {
        labels: @json($zoneLabels),
        datasets: [{
          data: @json($zoneData),
          backgroundColor: '#7C87E8'
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        scales: { y: { ticks: { stepSize: 1 } } }
      }
    });
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
<script src="{{ asset('js/pabarang.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>