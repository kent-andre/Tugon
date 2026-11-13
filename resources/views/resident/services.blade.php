<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Barangay Services - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --ref-blue: #4F5FE0; --ref-navy-text: #1E2340; --ref-gray-text: #6B7280;
      --ref-bg: #F5F6FA; --ref-border: #E4E6EF;
    }
    body { font-family: 'Host Grotesk', sans-serif; color: var(--ref-navy-text); background: var(--ref-bg); }

    /* ===== Sidebar (shared across all resident pages) ===== */
    :root { --navy: #181a35; --navy-active: #5b52f5; }
    .main { margin-left: 250px; }
    .avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--navy-active); color: #fff;
      display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; cursor: pointer; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 18px 40px 0; }

    .page-wrap { max-width: 900px; margin: 0 auto; padding: 40px 20px; }

    .service-row {
      background: #fff; border: 1px solid var(--ref-border); border-radius: 12px;
      padding: 18px 20px; margin-bottom: 12px; display: flex; align-items: center; gap: 16px;
    }
    .service-icon { width: 46px; height: 46px; border-radius: 10px; background: var(--ref-bg); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .service-icon i { font-size: 22px; color: var(--ref-blue); }
    .service-title { font-size: 15px; font-weight: 700; }
    .service-desc { font-size: 12.5px; color: var(--ref-gray-text); margin-top: 2px; }
    .service-link { font-size: 13px; font-weight: 700; color: var(--ref-blue); text-decoration: none; white-space: nowrap; border: 1px solid var(--ref-border); border-radius: 8px; padding: 7px 12px; }
    .service-link:hover { border-color: var(--ref-blue); }
    .service-badge { font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 10px; background: #E1F5E4; color: #1E7A34; white-space: nowrap; }
  </style>
</head>
<body>

  @include('partials.resident-sidebar')

  <div class="main">
    <div class="top-bar">
      <h5 class="fw-bold mb-0">Barangay Services</h5>
    </div>



  <div class="page-wrap">
    <h3 class="fw-bold mb-1">Barangay Services</h3>
    <p style="color: var(--ref-gray-text); font-size: 14px;" class="mb-4">
      Every service Barangay Alejal provides, and where to find it in Tugon.
    </p>

    <!-- 1. Administrative -->
    <div class="service-row">
      <div class="service-icon"><i class="ti ti-file-certificate"></i></div>
      <div class="flex-grow-1">
        <div class="service-title">Administrative Services</div>
        <div class="service-desc">Barangay Clearance, Certificate of Residency, Certificate of Indigency, Business Clearance</div>
      </div>
      <a href="{{ url('/request-document') }}" class="service-link">Request a Document</a>
    </div>

    <!-- 2. Peace and Order -->
    <div class="service-row">
      <div class="service-icon"><i class="ti ti-shield-check"></i></div>
      <div class="flex-grow-1">
        <div class="service-title">Peace &amp; Order</div>
        <div class="service-desc">Blotter reports, minor dispute records. Patrols and curfew enforcement are handled directly by Tanod on duty.</div>
      </div>
      <a href="{{ url('/blotter-report') }}" class="service-link">File a Blotter Report</a>
    </div>

    <!-- 3. Katarungang Pambarangay -->
    <div class="service-row">
      <div class="service-icon"><i class="ti ti-scale"></i></div>
      <div class="flex-grow-1">
        <div class="service-title">Katarungang Pambarangay (Dispute Resolution)</div>
        <div class="service-desc">Mediation between residents, required before many court cases. Initiated through a Blotter Report.</div>
      </div>
      <a href="{{ url('/faq') }}" class="service-link">Learn more in FAQ</a>
    </div>

    <!-- 4. Health Services -->
    <div class="service-row">
      <div class="service-icon"><i class="ti ti-heart-handshake"></i></div>
      <div class="flex-grow-1">
        <div class="service-title">Health Services</div>
        <div class="service-desc">Vaccination scheduling, basic checkups, maternal care, vitamin distribution.</div>
      </div>
      <a href="{{ url('/request-document') }}" class="service-link">Schedule Vaccination</a>
    </div>

    <!-- 5. Social Services -->
    <div class="service-row">
      <div class="service-icon"><i class="ti ti-users-group"></i></div>
      <div class="flex-grow-1">
        <div class="service-title">Social Services &amp; Community Support</div>
        <div class="service-desc">Senior Citizen / PWD ID, 4Ps coordination, relief distribution, feeding programs.</div>
      </div>
      <a href="{{ url('/request-document') }}" class="service-link">Apply for ID</a>
    </div>

    <!-- 6. Environmental -->
    <div class="service-row">
      <div class="service-icon"><i class="ti ti-leaf"></i></div>
      <div class="flex-grow-1">
        <div class="service-title">Environmental Services</div>
        <div class="service-desc">Clean-up drives, waste collection schedules, anti-dengue campaigns, tree planting.</div>
      </div>
      <a href="{{ url('/announcements') }}" class="service-badge" style="text-decoration:none;">See Announcements</a>
    </div>

    <!-- 7. Disaster/Emergency -->
    <div class="service-row">
      <div class="service-icon"><i class="ti ti-alert-triangle"></i></div>
      <div class="flex-grow-1">
        <div class="service-title">Disaster Risk Reduction &amp; Emergency</div>
        <div class="service-desc">Evacuation plans, rescue operations, emergency response contacts.</div>
      </div>
      <a href="{{ url('/emergency-hotlines') }}" class="service-link">Emergency Hotlines</a>
    </div>

    <!-- 8. Infrastructure -->
    <div class="service-row">
      <div class="service-icon"><i class="ti ti-road"></i></div>
      <div class="flex-grow-1">
        <div class="service-title">Infrastructure &amp; Community Projects</div>
        <div class="service-desc">Road maintenance, streetlights, barangay hall upkeep. Report an issue directly.</div>
      </div>
      <a href="{{ url('/submit-concern') }}" class="service-link">Submit a Concern</a>
    </div>

    <!-- 9. Community Programs -->
    <div class="service-row">
      <div class="service-icon"><i class="ti ti-confetti"></i></div>
      <div class="flex-grow-1">
        <div class="service-title">Community Programs &amp; Activities</div>
        <div class="service-desc">Sports leagues, SK youth programs, livelihood trainings, fiestas.</div>
      </div>
      <a href="{{ url('/announcements') }}" class="service-badge" style="text-decoration:none;">See Announcements</a>
    </div>

  </div>
  </div>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
<script src="{{ asset('js/pabarang.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>