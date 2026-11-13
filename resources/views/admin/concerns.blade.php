<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Concerns - Tugon Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

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

    /* Stat strip */
    .stat-strip { display: flex; gap: 14px; margin-bottom: 22px; }
    .stat-mini { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 14px 18px; flex: 1; }
    .stat-mini .stat-number { font-size: 22px; font-weight: 800; }
    .stat-mini .stat-label { font-size: 11.5px; color: var(--ref-gray-text); }

    /* Filter bar */
    .filter-bar { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 14px 18px; margin-bottom: 18px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .filter-bar select, .filter-bar input { font-size: 13px; }

    /* Concern cards */
    .concern-card {
      background: #fff; border: 1px solid var(--ref-border); border-radius: 12px;
      padding: 16px 18px; margin-bottom: 10px; display: flex; align-items: center; gap: 14px;
      border-left: 4px solid var(--ref-border); cursor: pointer;
    }
    .concern-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .concern-card.urgent-border { border-left-color: var(--ref-red); }
    .concern-card.normal-border { border-left-color: var(--ref-blue); }

    .concern-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: var(--ref-bg); }
    .concern-title { font-size: 14.5px; font-weight: 700; }
    .concern-meta { font-size: 12px; color: var(--ref-gray-text); }

    .badge-pill { font-size: 10.5px; font-weight: 700; padding: 3px 9px; border-radius: 10px; white-space: nowrap; }
    .badge-anon { background: #EDEDED; color: #555; }
    .badge-unverified { background: #FDE8E8; color: var(--ref-red); }
    .badge-verified { background: #E1F5E4; color: #1E7A34; }
    .badge-flagged { background: #3A1D1D; color: #F3B8B8; }

    /* Photo check (Step 8) */
    .badge-photo { background: #FDF1D8; color: #92650C; }
    .badge-photo-kept { background: #3A2A0C; color: #F5D58A; }
    .photo-check { border: 1px solid var(--ref-border); border-radius: 10px; padding: 12px 14px; margin-top: 10px; font-size: 13px; }
    .photo-check.is-flagged { border-color: #F3DFA7; background: #FFFBF1; }
    .photo-check ul { margin: 6px 0 0; padding-left: 18px; }
    .photo-check li { margin-bottom: 3px; }
    .photo-check .sev { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; margin-right: 4px; }
    .photo-check .sev-strong { color: var(--ref-red); }
    .photo-check .sev-medium { color: #92650C; }
    .photo-check .sev-weak, .photo-check .sev-info { color: var(--ref-gray-text); }
    .photo-check .src { font-size: 11px; color: var(--ref-gray-text); }
    .evidence-wrap { position: relative; }
    .evidence-wrap.is-sensitive #modalEvidenceImg { filter: blur(24px); }
    .sensitive-cover { position: absolute; inset: 0; border-radius: 8px; background: rgba(20,22,40,.92); color: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; cursor: pointer; font-size: 13px; text-align: center; padding: 12px; }

    .status-pill { font-size: 11.5px; font-weight: 600; padding: 4px 11px; border-radius: 10px; white-space: nowrap; }
    .status-pending-pill { background: #FDF1D8; color: #92650C; }
    .status-progress-pill { background: #E1EEFB; color: #0C5FA1; }
    .status-resolved-pill { background: #E1F5E4; color: #1E7A34; }
    .status-blotter-pill { background: #F0E8FB; color: #6B3FA0; }

    /* Modal */
    .timeline-item { position: relative; padding-left: 26px; padding-bottom: 16px; }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-item::before { content: ''; position: absolute; left: 4px; top: 4px; width: 9px; height: 9px; border-radius: 50%; background: var(--ref-blue); }
    .timeline-item::after { content: ''; position: absolute; left: 8px; top: 13px; bottom: 0; width: 2px; background: var(--ref-border); }
    .timeline-item:last-child::after { display: none; }
    .timeline-label { font-size: 13px; font-weight: 600; }
    .timeline-date { font-size: 11.5px; color: var(--ref-gray-text); }

    .modal-section-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--ref-gray-text); margin: 18px 0 8px; }
    .modal-section-label:first-of-type { margin-top: 0; }
    #modalEvidenceImg { width: 100%; max-height: 220px; object-fit: cover; border-radius: 8px; }

    /* Convert-to-blotter modal */
    .source-box { background: var(--ref-bg); border-radius: 10px; padding: 14px 16px; font-size: 13.5px; }
    .source-box .src-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--ref-gray-text); margin-top: 12px; }
    .source-box .src-label:first-child { margin-top: 0; }
    .convert-form .form-label { font-size: 12.5px; font-weight: 600; margin-bottom: 4px; }
    .convert-form .form-section { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--ref-gray-text); margin: 20px 0 10px; padding-top: 16px; border-top: 1px solid var(--ref-border); }
    .convert-form .form-section:first-child { margin-top: 0; padding-top: 0; border-top: none; }
  </style>
</head>
<body>

  @php
    $captain = config('pabarang.captain_name');
  @endphp

  <div class="admin-layout">

    @include('partials.admin-sidebar')


    <!-- Main content -->
    <div class="admin-main">

      <div class="admin-topbar">
        <h4>Manage Concerns</h4>
        @include('partials.admin-user')
      </div>

      <div class="stat-strip">
        <div class="stat-mini"><div class="stat-number" id="cntTotal">{{ $counts['total'] }}</div><div class="stat-label">Total</div></div>
        <div class="stat-mini"><div class="stat-number" id="cntUnverified">{{ $counts['unverified'] }}</div><div class="stat-label">Unverified</div></div>
        <div class="stat-mini"><div class="stat-number" id="cntPending">{{ $counts['pending'] }}</div><div class="stat-label">Pending</div></div>
        <div class="stat-mini"><div class="stat-number" id="cntProgress">{{ $counts['progress'] }}</div><div class="stat-label">In Progress</div></div>
        <div class="stat-mini"><div class="stat-number" id="cntResolved">{{ $counts['resolved'] }}</div><div class="stat-label">Resolved</div></div>
        <div class="stat-mini"><div class="stat-number" id="cntPhotos" style="color:#92650C;">{{ $counts['photos_to_review'] ?? 0 }}</div><div class="stat-label">Photos to review</div></div>
      </div>

      <div class="filter-bar">
        <select class="form-select form-select-sm" style="width: auto;" id="fStatus">
          <option value="">All Statuses</option>
          <option value="pending">Pending</option>
          <option value="progress">In Progress</option>
          <option value="resolved">Resolved</option>
          <option value="blotter">Converted to Blotter</option>
        </select>
        <select class="form-select form-select-sm" style="width: auto;" id="fCat">
          <option value="">All Categories</option>
          <option>Crime/Safety</option>
          <option>Environment</option>
          <option>Infrastructure</option>
          <option>Streetlights</option>
          <option>Noise</option>
          <option>Water Supply</option>
          <option>Other</option>
        </select>
        <select class="form-select form-select-sm" style="width: auto;" id="fZone">
          <option value="">All Puroks</option>
          <option>Purok 1</option>
          <option>Purok 2</option>
          <option>Purok 3</option>
          <option>Purok 4</option>
          <option>Purok 5</option>
        </select>
        <select class="form-select form-select-sm" style="width: auto;" id="fPhoto">
          <option value="">All Photos</option>
          <option value="review">Flagged photos to review</option>
          <option value="flagged">All flagged photos</option>
          <option value="ai">Possibly AI-generated</option>
        </select>
        <input type="text" class="form-control form-control-sm" id="fSearch" placeholder="Search reference #..." style="width: 200px;">
      </div>

      {{-- Contact info is for admin follow-up only; the name is never stored for anonymous reports. --}}
      @php
        $statusLabels = ['pending' => 'Pending', 'progress' => 'In Progress', 'resolved' => 'Resolved', 'blotter' => 'Blotter'];
        $verifyLabels = ['unverified' => 'Unverified', 'verified' => 'Verified', 'flagged' => 'Flagged'];
      @endphp

      <div id="concernList">
      @foreach ($concerns as $c)
        <div class="concern-card {{ $c['cat'] === 'Crime/Safety' ? 'urgent-border' : 'normal-border' }}"
             data-bs-toggle="modal" data-bs-target="#concernModal"
             data-ref="{{ $c['ref'] }}" data-title="{{ $c['title'] }}" data-cat="{{ $c['cat'] }}" data-zone="{{ $c['zone'] }}"
             data-reporter="{{ $c['reporter'] }}" data-contact="{{ $c['contact'] }}"
             data-status="{{ $c['status'] }}" data-verify="{{ $c['verify'] }}"
             data-flagreason="{{ $c['flag_reason'] }}" data-assigned="{{ $c['assigned'] }}"
             data-internal="{{ $c['internal'] }}" data-public="{{ $c['public'] }}"
             data-blotter="{{ $c['blotter'] }}" data-evidence="{{ $c['evidence'] }}"
             data-desc="{{ $c['desc'] }}" data-filed="{{ $c['filed'] }}"
             data-photo="{{ json_encode($c['photo'] ?? []) }}">
          <div class="concern-icon"><i class="ti {{ $c['icon'] }}" style="color:{{ $c['color'] }};"></i></div>
          <div class="flex-grow-1">
            <div class="concern-title">{{ $c['title'] }}</div>
            <div class="concern-meta">{{ $c['ref'] }} · {{ $c['cat'] }} · {{ $c['zone'] }} · Filed {{ $c['filed'] }}</div>
          </div>
          @if ($c['reporter'] === 'Anonymous')
            <span class="badge-pill badge-anon">Anonymous</span>
          @endif
          {{-- Filled by refreshCard() from data-photo --}}
          <span class="badge-pill pbadge d-none"></span>
          <span class="badge-pill badge-{{ $c['verify'] }} vbadge">{{ $verifyLabels[$c['verify']] ?? 'Unverified' }}</span>
          <span class="status-pill status-{{ $c['status'] }}-pill spill">{{ $c['status'] === 'blotter' ? 'Blotter ' . $c['blotter'] : ($statusLabels[$c['status']] ?? 'Pending') }}</span>
        </div>
      @endforeach
      </div>

      <div id="emptyMsg" class="text-center py-5 {{ count($concerns) ? 'd-none' : '' }}" style="color: var(--ref-gray-text); font-size: 14px;">
        {{ count($concerns) ? 'No concerns match your filters.' : 'No concerns have been submitted yet.' }}
      </div>

    </div>
  </div>

  <!-- One shared modal, filled in from the card that was clicked -->
  <div class="modal fade" id="concernModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Concern Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <div class="d-flex justify-content-between align-items-start mb-2">
            <p style="font-size: 13px; color: var(--ref-gray-text); margin: 0;" id="modalMeta"></p>
            <span class="badge-pill" id="modalReporterBadge"></span>
          </div>

          <div class="modal-section-label">Description</div>
          <p style="font-size: 13.5px;" id="modalDesc"></p>

          <div class="modal-section-label">Contact Info <span style="font-weight:400; text-transform:none; color:var(--ref-gray-text);">(optional, from the reporter; for follow-up only, never shown publicly)</span></div>
          <p style="font-size: 13.5px;" id="modalContact"></p>

          <div class="modal-section-label">Photo Evidence</div>
          <div class="evidence-wrap" id="evidenceWrap">
            <img id="modalEvidenceImg" src="https://placehold.co/700x300/E4E6EF/6B7280?text=No+photo+attached" alt="Evidence">
            <div class="sensitive-cover d-none" id="sensitiveCover" role="button" tabindex="0">
              <i class="ti ti-eye-off fs-4"></i>
              <span id="sensitiveText">This photo may show sensitive content.</span>
              <span style="font-size:12px; opacity:.8;">Click to show</span>
            </div>
          </div>

          {{-- Photo check result (Step 8). Never rejects a report; officials decide. --}}
          <div class="photo-check" id="photoCheck">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <div><strong>Photo check:</strong> <span id="photoStatus"></span></div>
              <span class="src" id="photoSource"></span>
            </div>
            <ul id="photoFlags" class="d-none"></ul>
            <div id="photoReviewLine" class="src mt-2 d-none"></div>
            <div class="d-flex gap-2 mt-2 d-none" id="photoReviewBtns">
              <button type="button" class="btn btn-sm btn-outline-success" id="photoOkBtn"><i class="ti ti-check"></i> Photo is OK</button>
              <button type="button" class="btn btn-sm btn-outline-warning" id="photoKeepBtn"><i class="ti ti-flag"></i> Keep flag</button>
            </div>
          </div>

          <div class="modal-section-label">Verification</div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-success" id="verifyBtn"><i class="ti ti-check"></i> Verify</button>
            <button type="button" class="btn btn-sm btn-outline-danger" id="flagBtn"><i class="ti ti-flag"></i> Flag</button>
          </div>
          <div class="collapse mt-2" id="flagReasonBox">
            <textarea class="form-control" rows="2" id="flagReason" maxlength="300" placeholder="Reason for flagging this report..."></textarea>
          </div>

          <div class="modal-section-label">Status</div>
          <select class="form-select form-select-sm" style="width: auto;" id="modalStatusSelect">
            <option value="pending">Pending</option>
            <option value="progress">In Progress</option>
            <option value="resolved">Resolved</option>
            <option value="blotter" disabled>Converted to Blotter</option>
          </select>

          <div class="modal-section-label">Assign To</div>
          <select class="form-select form-select-sm" style="width: auto;" id="modalAssign">
            <option value="" selected>Select an official/tanod</option>
            <option>Hon. Elena P. Santos (Committee on Health)</option>
            <option>Hon. Mario D. Reyes (Committee on Peace & Order)</option>
            <option>Mr. Federico L. Bautista (Chief Barangay Tanod)</option>
          </select>

          <div class="modal-section-label">Internal Notes <span style="font-weight:400; text-transform:none; color:var(--ref-gray-text);">(not visible to resident)</span></div>
          <textarea class="form-control mb-3" rows="2" id="modalInternal" maxlength="1000" placeholder="Notes for other officials only..."></textarea>

          <div class="modal-section-label">Public Update <span style="font-weight:400; text-transform:none; color:var(--ref-gray-text);">(visible to the resident)</span></div>
          <textarea class="form-control" rows="2" id="modalPublicUpdate" maxlength="500" placeholder="Message the resident will see on their status timeline..."></textarea>
          <button type="button" class="btn btn-link btn-sm p-0 mt-1" id="inviteBtn">Use "please visit the barangay hall" message</button>

          <div class="modal-section-label">Blotter</div>
          <p class="mb-2" style="font-size: 13px; color: var(--ref-gray-text);" id="convertHint"></p>
          <button type="button" class="btn btn-sm" style="border: 1px solid var(--ref-blue); color: var(--ref-blue);" id="convertBtn">
            <i class="ti ti-clipboard-text"></i> Convert to Blotter Report
          </button>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-sm" style="background: var(--ref-blue); color: #fff;" id="saveBtn">Save Changes</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Convert to Blotter. The officer types the entry from the complainant's
       statement; the resident's report on the left is for reference only. -->
  <div class="modal fade" id="convertModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form id="convertForm" class="convert-form">
          <div class="modal-header">
            <h5 class="modal-title">Convert to Blotter Report</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row g-4">

              <div class="col-lg-5">
                <div class="source-box">
                  <div class="src-label">Concern on file</div>
                  <div class="fw-bold" id="srcTitle"></div>
                  <div style="font-size: 12px; color: var(--ref-gray-text);" id="srcMeta"></div>
                  <div class="src-label">Reported by</div>
                  <div id="srcReporter"></div>
                  <div class="src-label">Contact</div>
                  <div id="srcContact"></div>
                  <div class="src-label">Resident's description</div>
                  <div id="srcDesc"></div>
                </div>
                <p class="mt-2 mb-0" style="font-size: 12px; color: var(--ref-gray-text);">
                  Write the blotter entry in your own words from the complainant's statement at the barangay hall. The report above is only a reference.
                </p>
              </div>

              <div class="col-lg-7">
                <div class="form-section">Incident</div>
                <div class="row g-2 mb-2">
                  <div class="col-md-4"><label class="form-label">Date</label><input type="date" class="form-control form-control-sm" id="cvDate" required></div>
                  <div class="col-md-3"><label class="form-label">Time</label><input type="time" class="form-control form-control-sm" id="cvTime" required></div>
                  <div class="col-md-5"><label class="form-label">Place</label><input type="text" class="form-control form-control-sm" id="cvPlace" placeholder="Address or landmark" required></div>
                </div>
                <div class="mb-2">
                  <label class="form-label">Type of incident</label>
                  <select class="form-select form-select-sm" id="cvType" required>
                    <option value="" selected disabled>Select type</option>
                    <option>Boundary Dispute</option>
                    <option>Verbal Altercation</option>
                    <option>Physical Injury</option>
                    <option>Property Damage</option>
                    <option>Theft</option>
                    <option>Threats / Harassment</option>
                    <option>Debt / Financial Dispute</option>
                    <option>Noise Disturbance</option>
                    <option>Family / Domestic Dispute</option>
                    <option>Other</option>
                  </select>
                </div>
                <div id="vawcNote" class="alert alert-warning py-2 d-none" style="font-size: 12.5px;">
                  If this involves violence or threats against a woman or her child, it falls under RA 9262 (VAWC) and is not subject to mediation. Refer it to the Women and Children Protection Desk and ask the Punong Barangay about a Barangay Protection Order.
                </div>
                <div class="mb-2">
                  <label class="form-label">Narrative</label>
                  <textarea class="form-control form-control-sm" rows="5" id="cvNarrative" placeholder="What happened, in order, as told by the complainant. State facts only, no conclusions of guilt." required></textarea>
                </div>

                <div class="form-section">Complainant</div>
                <div class="row g-2 mb-2">
                  <div class="col-md-6"><label class="form-label">Full name</label><input type="text" class="form-control form-control-sm" id="cvCompName" required></div>
                  <div class="col-md-2"><label class="form-label">Age</label><input type="number" min="0" class="form-control form-control-sm" id="cvCompAge" required></div>
                  <div class="col-md-4">
                    <label class="form-label">Civil status</label>
                    <select class="form-select form-select-sm" id="cvCompCivil" required>
                      <option value="" selected disabled>Select</option>
                      <option>Single</option><option>Married</option><option>Widowed</option><option>Separated</option>
                    </select>
                  </div>
                </div>
                <div class="row g-2 mb-2">
                  <div class="col-md-8"><label class="form-label">Address</label><input type="text" class="form-control form-control-sm" id="cvCompAddress" required></div>
                  <div class="col-md-4"><label class="form-label">Contact no. <span class="text-muted fw-normal">(optional)</span></label><input type="tel" class="form-control form-control-sm" id="cvCompContact"></div>
                </div>
                <div class="mb-2">
                  <label class="form-label">Email <span class="text-muted fw-normal">(optional, for the Notice of Hearing)</span></label>
                  <input type="email" class="form-control form-control-sm" id="cvCompEmail" maxlength="255">
                </div>
                <div class="form-text mb-2" style="font-size: 12px;">Ask the complainant for these details and check a valid ID. The resident's account is for comparison only.</div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" id="cvAppeared" required>
                  <label class="form-check-label" for="cvAppeared" style="font-size: 12.5px;">Complainant appeared in person and signed the blotter entry</label>
                </div>

                <div class="form-section">Respondent</div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" id="cvUnknown">
                  <label class="form-check-label" for="cvUnknown" style="font-size: 12.5px;">Identity of the respondent is unknown</label>
                </div>
                <div class="row g-2 mb-2" id="cvRespFields">
                  <div class="col-md-5"><label class="form-label">Full name</label><input type="text" class="form-control form-control-sm" id="cvRespName" required></div>
                  <div class="col-md-7"><label class="form-label">Address <span class="text-muted fw-normal">(if known)</span></label><input type="text" class="form-control form-control-sm" id="cvRespAddress"></div>
                  <div class="col-12"><label class="form-label">Email <span class="text-muted fw-normal">(optional, for the Summons copy)</span></label><input type="email" class="form-control form-control-sm" id="cvRespEmail" maxlength="255"></div>
                </div>

                <div class="form-section">Witnesses</div>
                <input type="text" class="form-control form-control-sm mb-2" id="cvWitnesses" placeholder="Names, separated by commas (optional)">

                <div class="form-section">Recording</div>
                <div class="row g-2 mb-2">
                  <div class="col-md-6">
                    <label class="form-label">Recorded by</label>
                    <select class="form-select form-select-sm" id="cvRecordedBy" required>
                      <option value="" selected disabled>Select officer</option>
                      <option>Barangay Secretary</option>
                      <option>{{ $captain }} (Punong Barangay)</option>
                      <option>Mr. Federico L. Bautista (Chief Barangay Tanod)</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Action taken</label>
                    <select class="form-select form-select-sm" id="cvAction" required>
                      <option value="" selected disabled>Select action</option>
                      <option value="mediation">For mediation (Katarungang Pambarangay)</option>
                      <option value="record">Record only, no action requested</option>
                      <option value="pnp">Refer to PNP / prosecutor</option>
                      <option value="vawc">Refer to Women and Children Protection Desk</option>
                      <option value="bcpc">Refer to BCPC / MSWDO (child in conflict with the law)</option>
                    </select>
                  </div>
                </div>
                <div class="form-text" style="font-size: 12px;">
                  Mediation covers disputes between residents of the same barangay (or adjoining barangays) where there is a private offended party and the offense carries no more than 1 year imprisonment or a Php 5,000 fine. Anything else is recorded and referred.
                </div>
              </div>

            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm" style="background: var(--ref-blue); color: #fff;">Record in Blotter</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000;">
    <div class="toast align-items-center text-bg-dark border-0" id="appToast" role="status" aria-live="polite" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="toastMsg"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const $ = id => document.getElementById(id);
    let activeCard = null;
    // Only the Punong Barangay and Secretary record blotter entries (the server checks this too).
    const CAN_CONVERT = {{ in_array($me['role'] ?? '', ['captain', 'secretary'], true) ? 'true' : 'false' }};
    function toast(msg, linkText) {
      const body = $('toastMsg');
      body.textContent = msg;
      if (linkText) {
        const a = document.createElement('a');
        a.href = "{{ url('/admin/blotters') }}";
        a.textContent = linkText;
        a.className = 'text-white fw-bold ms-2';
        body.appendChild(a);
      }
      bootstrap.Toast.getOrCreateInstance($('appToast')).show();
    }
    function fmtTime(t) {
      const [h, m] = t.split(':').map(Number);
      return (h % 12 || 12) + ':' + String(m).padStart(2, '0') + ' ' + (h >= 12 ? 'PM' : 'AM');
    }
    function fmtDate(d) {
      return new Date(d + 'T00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    const VERIFY_LABELS = { unverified: 'Unverified', verified: 'Verified', flagged: 'Flagged' };
    const STATUS_LABELS = { pending: 'Pending', progress: 'In Progress', resolved: 'Resolved' };
    const PLACEHOLDER_IMG = 'https://placehold.co/700x300/E4E6EF/6B7280?text=No+photo+attached';
    let verification = 'unverified';

    // Re-draw one card's badges from its data-* attributes
    function refreshCard(card) {
      const d = card.dataset;
      const pill = card.querySelector('.spill');
      pill.className = 'status-pill spill status-' + d.status + '-pill';
      pill.textContent = d.status === 'blotter' ? 'Blotter ' + d.blotter : (STATUS_LABELS[d.status] || 'Pending');
      const vb = card.querySelector('.vbadge');
      vb.className = 'badge-pill vbadge badge-' + d.verify;
      vb.textContent = VERIFY_LABELS[d.verify] || 'Unverified';

      // Photo check badge: shown while a flagged photo waits for review, or after "Keep flag"
      const p = photoOf(card), pb = card.querySelector('.pbadge');
      const show = p.status === 'flagged' && p.review !== 'ok';
      const ai = isAiFlagged(p);
      pb.className = 'badge-pill pbadge ' + (p.review === 'kept' ? 'badge-photo-kept' : 'badge-photo') + (show ? '' : ' d-none');
      pb.innerHTML = '<i class="ti ' + (ai ? 'ti-robot' : 'ti-photo-exclamation') + '"></i> ';
      pb.append(ai ? (p.review === 'kept' ? 'AI image (flag kept)' : 'Possibly AI image')
                   : (p.review === 'kept' ? 'Photo flag kept' : 'Photo flagged'));
      pb.title = (p.flags || []).filter(f => f.severity !== 'info').map(f => f.label).join('\n');
    }

    // ---- Photo check (Step 8) ----
    // Flag codes meaning "possibly AI-generated" (same list as BasicPhotoChecker::AI_CODES)
    const AI_CODES = @json(\App\Support\PhotoCheck\BasicPhotoChecker::AI_CODES);
    function isAiFlagged(p) {
      return p.status === 'flagged' && (p.flags || []).some(f => AI_CODES.includes(f.code));
    }
    function photoOf(card) {
      try { return JSON.parse(card.dataset.photo || '{}'); } catch (e) { return {}; }
    }
    const PHOTO_STATUS = {
      pending: 'Not checked yet', clear: 'No problems found', flagged: 'Flagged for review', error: 'Could not be checked'
    };
    const PHOTO_SOURCE = {
      'basic': 'Basic checks', 'basic+ai': 'Basic + AI checks', 'basic+ai_failed': 'Basic checks (AI check failed)'
    };
    const SEV_LABEL = { strong: 'Strong', medium: 'Check', weak: 'Hint', info: 'Note' };

    function renderPhoto(card) {
      const p = photoOf(card);
      const flagged = p.status === 'flagged';
      $('photoCheck').classList.toggle('is-flagged', flagged && p.review !== 'ok');
      $('photoStatus').textContent = PHOTO_STATUS[p.status] || PHOTO_STATUS.pending;
      $('photoSource').textContent = PHOTO_SOURCE[p.source] || '';

      const list = $('photoFlags');
      list.innerHTML = '';
      (p.flags || []).forEach(f => {
        const li = document.createElement('li');
        const sev = document.createElement('span');
        sev.className = 'sev sev-' + f.severity;
        sev.textContent = (SEV_LABEL[f.severity] || '') + (f.source === 'ai' ? ' · AI' : '');
        li.append(sev, f.label);
        (f.related || []).forEach(code => {
          const a = document.createElement('a');
          a.href = '#';
          a.className = 'ms-1';
          a.textContent = code;
          a.addEventListener('click', e => {
            e.preventDefault();
            $('fSearch').value = code; $('fPhoto').value = '';
            bootstrap.Modal.getInstance($('concernModal')).hide();
            applyFilters();
          });
          li.append(' ', a);
        });
        list.appendChild(li);
      });
      list.classList.toggle('d-none', !(p.flags || []).length);

      const line = $('photoReviewLine');
      line.textContent = p.review
        ? (p.review === 'ok' ? 'Marked OK' : 'Flag kept') + ' by ' + (p.reviewed_by || 'an official') + (p.reviewed_at ? ', ' + p.reviewed_at : '')
        : '';
      line.classList.toggle('d-none', !p.review);
      $('photoReviewBtns').classList.toggle('d-none', !flagged);
      $('photoOkBtn').classList.toggle('active', p.review === 'ok');
      $('photoKeepBtn').classList.toggle('active', p.review === 'kept');

      // Nudity or gore found by the AI check: covered until the official clicks
      const sensitive = !!p.sensitive && !!card.dataset.evidence;
      $('evidenceWrap').classList.toggle('is-sensitive', sensitive);
      $('sensitiveCover').classList.toggle('d-none', !sensitive);
      const sf = (p.flags || []).find(f => f.code === 'sensitive');
      $('sensitiveText').textContent = sf ? sf.label.replace(' (blurred for admins)', '') + '.' : 'This photo may show sensitive content.';
    }
    function revealPhoto() {
      $('evidenceWrap').classList.remove('is-sensitive');
      $('sensitiveCover').classList.add('d-none');
    }
    $('sensitiveCover').addEventListener('click', revealPhoto);
    $('sensitiveCover').addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); revealPhoto(); } });

    async function reviewPhoto(decision, btn) {
      if (!activeCard) return;
      btn.disabled = true;
      try {
        const res = await fetch("{{ url('/admin/concerns') }}/" + encodeURIComponent(activeCard.dataset.ref) + '/photo-review', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
          body: JSON.stringify({ decision })
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) { toast(data.message || 'Could not save the photo review.'); return; }
        activeCard.dataset.photo = JSON.stringify(data.photo);
        refreshCard(activeCard); renderPhoto(activeCard); recount(); applyFilters();
        toast(data.message);
      } catch (e) {
        toast('Could not reach the server. Check your connection and try again.');
      } finally {
        btn.disabled = false;
      }
    }
    $('photoOkBtn').addEventListener('click', function () { reviewPhoto('ok', this); });
    $('photoKeepBtn').addEventListener('click', function () { reviewPhoto('kept', this); });

    // Stat strip + filters work on the cards already on the page
    const cards = () => Array.from(document.querySelectorAll('.concern-card'));
    function recount() {
      const c = cards();
      const n = f => c.filter(f).length;
      $('cntTotal').textContent = c.length;
      $('cntUnverified').textContent = n(x => x.dataset.verify === 'unverified');
      $('cntPending').textContent = n(x => x.dataset.status === 'pending');
      $('cntProgress').textContent = n(x => x.dataset.status === 'progress' || x.dataset.status === 'blotter');
      $('cntResolved').textContent = n(x => x.dataset.status === 'resolved');
      $('cntPhotos').textContent = n(x => { const p = photoOf(x); return p.status === 'flagged' && !p.review; });
    }
    function applyFilters() {
      const st = $('fStatus').value, cat = $('fCat').value, zone = $('fZone').value, ph = $('fPhoto').value;
      const q = $('fSearch').value.trim().toUpperCase();
      let shown = 0;
      cards().forEach(card => {
        const d = card.dataset;
        const p = photoOf(card);
        const photoOk = !ph || (ph === 'ai' ? isAiFlagged(p) : (p.status === 'flagged' && (ph === 'flagged' || !p.review)));
        const ok = (!st || d.status === st) && (!cat || d.cat === cat) && (!zone || d.zone === zone) && (!q || d.ref.includes(q)) && photoOk;
        card.classList.toggle('d-none', !ok);
        if (ok) shown++;
      });
      $('emptyMsg').textContent = cards().length ? 'No concerns match your filters.' : 'No concerns have been submitted yet.';
      $('emptyMsg').classList.toggle('d-none', shown > 0);
    }
    ['fStatus', 'fCat', 'fZone', 'fPhoto'].forEach(id => $(id).addEventListener('change', applyFilters));
    $('fSearch').addEventListener('input', applyFilters);

    // Draw the photo badges, and open with "Flagged photos to review" when the
    // dashboard link adds ?photo=review
    cards().forEach(refreshCard);
    const qRef = new URLSearchParams(location.search).get('q');
    if (qRef) { $('fSearch').value = qRef.toUpperCase(); applyFilters(); }
    if (new URLSearchParams(location.search).get('photo') === 'review') {
      $('fPhoto').value = 'review';
      applyFilters();
    }

    // Saves to the server. Returns true on success. Updates the card that is open.
    async function saveConcern(ref, payload) {
      try {
        const res = await fetch("{{ url('/admin/concerns') }}/" + encodeURIComponent(ref), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(payload)
        });
        if (res.status === 401) { toast('Your session expired. Please log in again.'); return false; }
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
          const first = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Could not save.');
          toast(first);
          return false;
        }
        const c = data.concern, d = activeCard.dataset;
        d.status = c.status; d.verify = c.verify; d.flagreason = c.flag_reason; d.assigned = c.assigned;
        d.internal = c.internal; d.public = c.public; d.blotter = c.blotter;
        if (c.photo) d.photo = JSON.stringify(c.photo);
        refreshCard(activeCard);
        recount();
        applyFilters();
        return true;
      } catch (e) {
        toast('Could not reach the server. Check your connection and try again.');
        return false;
      }
    }

    // Verify / Flag buttons: click again to undo
    function syncVerify() {
      $('verifyBtn').classList.toggle('active', verification === 'verified');
      $('flagBtn').classList.toggle('active', verification === 'flagged');
      const box = bootstrap.Collapse.getOrCreateInstance($('flagReasonBox'), { toggle: false });
      verification === 'flagged' ? box.show() : box.hide();
    }
    $('verifyBtn').addEventListener('click', () => { verification = verification === 'verified' ? 'unverified' : 'verified'; syncVerify(); });
    $('flagBtn').addEventListener('click', () => { verification = verification === 'flagged' ? 'unverified' : 'flagged'; syncVerify(); });

    $('saveBtn').addEventListener('click', async function () {
      if (!activeCard) return;
      const reason = $('flagReason').value.trim();
      if (verification === 'flagged' && !reason) { toast('Please add a reason before flagging this report.'); return; }
      this.disabled = true;
      const ok = await saveConcern(activeCard.dataset.ref, {
        status: $('modalStatusSelect').value,
        verification,
        flag_reason: verification === 'flagged' ? reason : null,
        assigned_to: $('modalAssign').value || null,
        internal_notes: $('modalInternal').value.trim() || null,
        public_update: $('modalPublicUpdate').value.trim() || null
      });
      this.disabled = false;
      if (ok) {
        bootstrap.Modal.getInstance($('concernModal')).hide();
        toast('Changes saved.');
      }
    });

    // Fill the shared concern modal from the clicked card's data-* attributes
    $('concernModal').addEventListener('show.bs.modal', function (event) {
      const card = event.relatedTarget;
      activeCard = card;

      $('modalTitle').textContent = card.dataset.title;
      $('modalMeta').textContent =
        `${card.dataset.ref} · ${card.dataset.cat} · ${card.dataset.zone} · Filed ${card.dataset.filed}`;
      $('modalDesc').textContent = card.dataset.desc;
      $('modalContact').textContent = card.dataset.contact
        ? card.dataset.contact
        : (card.dataset.reporter === 'Anonymous' ? 'Not provided (anonymous report; follow up through a Public Update)' : 'Not provided');
      $('modalStatusSelect').value = card.dataset.status;
      $('modalEvidenceImg').src = card.dataset.evidence || PLACEHOLDER_IMG;
      renderPhoto(card);
      verification = card.dataset.verify || 'unverified';
      $('flagReason').value = card.dataset.flagreason || '';
      $('modalAssign').value = card.dataset.assigned || '';
      $('modalInternal').value = card.dataset.internal || '';
      $('modalPublicUpdate').value = card.dataset.public || '';
      syncVerify();

      const badge = $('modalReporterBadge');
      if (card.dataset.reporter === 'Anonymous') {
        badge.textContent = 'Anonymous';
        badge.className = 'badge-pill badge-anon';
      } else {
        badge.textContent = card.dataset.reporter;
        badge.className = 'badge-pill badge-verified';
      }

      // A blotter entry needs a named complainant who signs it, so anonymous reports can't be converted.
      const anon = card.dataset.reporter === 'Anonymous';
      const done = card.dataset.blotter;
      const resolved = card.dataset.status === 'resolved';
      $('convertBtn').disabled = anon || !!done || resolved || !CAN_CONVERT;
      $('convertHint').textContent = done
        ? `Already recorded as ${done}. Follow it up in Blotter Reports.`
        : anon
          ? 'Anonymous reports cannot be recorded in the blotter. A blotter entry needs a named complainant who signs it at the barangay hall.'
          : resolved
            ? 'This concern is already resolved.'
            : !CAN_CONVERT
            ? 'Only the Punong Barangay or the Secretary can record a blotter entry.'
            : 'Use this when the concern is a dispute or incident that needs mediation, a referral, or an official record. You will type the blotter entry yourself.';
    });

    $('inviteBtn').addEventListener('click', () => {
      $('modalPublicUpdate').value = 'Your report needs to be recorded at the barangay hall. Please visit with a valid ID so an officer can take your statement.';
    });

    // Close the concern modal, then open the blotter form
    $('convertBtn').addEventListener('click', () => {
      const concernEl = $('concernModal');
      concernEl.addEventListener('hidden.bs.modal', () => {
        bootstrap.Modal.getOrCreateInstance($('convertModal')).show();
      }, { once: true });
      bootstrap.Modal.getInstance(concernEl).hide();
    });

    $('convertModal').addEventListener('show.bs.modal', () => {
      const d = activeCard.dataset;
      $('convertForm').reset();
      $('cvRespFields').classList.remove('d-none');
      $('cvRespName').required = true;
      $('vawcNote').classList.add('d-none');

      $('srcTitle').textContent = d.title;
      $('srcMeta').textContent = `${d.ref} · ${d.cat} · ${d.zone} · Filed ${d.filed}`;
      $('srcReporter').textContent = d.reporter;
      $('srcContact').textContent = d.contact || 'Not provided';
      $('srcDesc').textContent = d.desc;
    });

    $('cvType').addEventListener('change', function () {
      $('vawcNote').classList.toggle('d-none', this.value !== 'Family / Domestic Dispute');
    });
    $('cvUnknown').addEventListener('change', function () {
      $('cvRespFields').classList.toggle('d-none', this.checked);
      $('cvRespName').required = !this.checked;
    });

    $('convertForm').addEventListener('submit', async function (e) {
      e.preventDefault();
      const d = activeCard.dataset;
      const unknown = $('cvUnknown').checked;
      const action = $('cvAction').value;
      const respAddr = $('cvRespAddress').value.trim();
      const contact = $('cvCompContact').value.trim();
      const compInfo = [$('cvCompAge').value && ($('cvCompAge').value + ' yrs old'), $('cvCompCivil').value, $('cvCompAddress').value.trim(), contact].filter(Boolean).join(' · ');
      let ref;
      try {
        const res = await fetch("{{ url('/admin/concerns') }}/" + encodeURIComponent(d.ref) + '/blotter', { method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
          body: JSON.stringify({ action, type: $('cvType').value, complainant: $('cvCompName').value.trim(), complainant_info: compInfo,
            complainant_email: $('cvCompEmail').value.trim() || null,
            respondent: unknown ? 'Unknown person' : $('cvRespName').value.trim(), respondent_info: unknown ? '' : respAddr,
            respondent_email: unknown ? null : ($('cvRespEmail').value.trim() || null),
            narrative: $('cvNarrative').value.trim(), incident: fmtDate($('cvDate').value) + ', ' + fmtTime($('cvTime').value),
            place: $('cvPlace').value.trim(), witnesses: $('cvWitnesses').value.trim(), recorded_by: $('cvRecordedBy').value.trim() }) });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) { toast(data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Could not save the blotter.')); return; }
        ref = data.blotter_ref;
        const c = data.concern;
        d.status = c.status; d.verify = c.verify; d.flagreason = c.flag_reason; d.assigned = c.assigned;
        d.internal = c.internal; d.public = c.public; d.blotter = c.blotter;
        refreshCard(activeCard); recount(); applyFilters();
      } catch (err) { toast('Could not reach the server.'); return; }

      bootstrap.Modal.getInstance($('convertModal')).hide();
      toast(d.ref + ' recorded as ' + ref + '.', 'View in Blotter Reports');
    });
  </script>
  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <link href="{{ asset('css/pabarang-admin.css') }}" rel="stylesheet">
</body>
</html>