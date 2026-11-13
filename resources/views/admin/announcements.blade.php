{{--
  Manage Announcements. Data: Admin\AnnouncementController@index
  ($announcements pinned-first, $popup = the single resident popup row,
  $categories from config('pabarang.announcement_categories')).
  Every save/delete/popup change is written to audit_logs by the controller.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Announcements - Tugon Admin</title>
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

    .btn-new-announcement { background: var(--ref-blue); color: #fff; border: none; border-radius: 8px; padding: 9px 18px; font-weight: 600; font-size: 13.5px; }
    .btn-new-announcement:hover { background: #3E4DCB; color: #fff; }

    /* Same tag colors as the resident page - the admin picks the category,
       so these must match exactly what the resident will see. */
    .category-tag { font-size: 11px; font-weight: 700; letter-spacing: 0.04em; padding: 3px 10px; border-radius: 10px; }
    .tag-health { background: #E1F5E4; color: #1E7A34; }
    .tag-advisory { background: #FDE8E8; color: #D64545; }
    .tag-utility { background: #FDF1D8; color: #92650C; }
    .tag-event { background: #E7EAFD; color: var(--ref-blue); }
    .tag-assistance { background: #E1EEFB; color: #0C5FA1; }

    .announcement-row { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 18px 20px; margin-bottom: 12px; }
    .announcement-title { font-size: 15px; font-weight: 700; margin: 8px 0 4px; }
    .announcement-meta { font-size: 12px; color: var(--ref-gray-text); margin-bottom: 8px; }
    .announcement-body-preview { font-size: 13px; color: var(--ref-navy-text); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .pinned-badge { font-size: 11px; color: var(--ref-blue); font-weight: 700; }
    .list-empty { background: #fff; border: 1px dashed var(--ref-border); border-radius: 12px; padding: 28px; text-align: center; color: var(--ref-gray-text); font-size: 13.5px; }

    .row-actions { display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
    .row-actions button { font-size: 12.5px; }

    /* ===== Resident popup card + live preview ===== */
    .popup-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 14px; padding: 22px; margin-bottom: 26px; display: flex; gap: 28px; align-items: flex-start; flex-wrap: wrap; }
    .popup-card-info { flex: 1; min-width: 260px; }
    .popup-status { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 10px; }
    .popup-status.on { background: #E1F5E4; color: #1E7A34; }
    .popup-status.off { background: #EDEDED; color: #555; }
    .popup-updated { font-size: 12px; color: var(--ref-gray-text); }

    .popup-preview { width: 100%; max-width: 340px; background: #fff; border: 1px solid var(--ref-border); border-radius: 16px; box-shadow: 0 10px 30px rgba(20, 24, 55, .14); padding: 16px; flex-shrink: 0; }
    .popup-preview.is-off { opacity: .55; }
    .pv-eyebrow { font-size: 10.5px; font-weight: 700; letter-spacing: .04em; color: var(--ref-blue); margin-bottom: 8px; }
    .pv-image { height: 120px; border-radius: 8px; background: #E9EAF0 center / cover no-repeat; display: flex; align-items: center; justify-content: center; color: #9AA0B4; font-size: 30px; margin-bottom: 12px; }
    .pv-title { font-size: 15px; font-weight: 800; margin-bottom: 6px; overflow-wrap: anywhere; }
    .pv-body { font-size: 12px; color: var(--ref-gray-text); margin-bottom: 14px; overflow-wrap: anywhere; }
    .pv-actions { display: flex; justify-content: flex-end; gap: 8px; }
    .pv-btn { font-size: 11.5px; font-weight: 600; padding: 5px 12px; border-radius: 6px; border: 0; pointer-events: none; }
    .pv-btn.ghost { background: #F1F2F6; color: #333; }
    .pv-btn.primary { background: var(--ref-blue); color: #fff; }
    .preview-caption { font-size: 11.5px; color: var(--ref-gray-text); margin-bottom: 8px; }
  </style>
</head>
<body>

  <div class="admin-layout">

    @include('partials.admin-sidebar')

    <!-- ===== Main content ===== -->
    <div class="admin-main">

      <div class="admin-topbar">
        <h4>Manage Announcements</h4>
        @include('partials.admin-user')
      </div>

      @if ($errors->any())
        <div class="alert alert-danger" style="font-size:13.5px;">
          <strong>Nothing was saved.</strong>
          <ul class="mb-0 mt-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      <!-- ===== Resident popup: what residents see when they open their dashboard ===== -->
      <div class="popup-card">
        <div class="popup-card-info">
          <div class="d-flex align-items-center gap-2 mb-2">
            <h6 class="fw-bold mb-0">Resident Popup</h6>
            <span class="popup-status {{ $popup->enabled ? 'on' : 'off' }}">
              @if ($popup->enabled)<i class="ti ti-eye"></i> Live @else<i class="ti ti-eye-off"></i> Off @endif
            </span>
          </div>
          <p style="font-size: 13px; color: var(--ref-gray-text); max-width: 420px;">
            This is the announcement residents see as a popup after they log in. Update it whenever
            there is something new they should know about.
          </p>
          <div class="popup-updated">
            {{ $popup->updated_at ? 'Last updated ' . $popup->updated_at->format('M j, Y g:i A') : 'Not edited yet' }}
          </div>
          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn-new-announcement" onclick="openPopupEditor()">
              <i class="ti ti-pencil"></i> Edit popup
            </button>
            <form method="POST" action="{{ route('admin.announcements.popup.toggle') }}">
              @csrf
              <button type="submit" class="btn btn-outline-secondary btn-sm h-100">{{ $popup->enabled ? 'Turn off' : 'Turn on' }}</button>
            </form>
          </div>
        </div>
        <div>
          <div class="preview-caption">What residents see</div>
          <div id="popupPreviewCard" class="popup-preview {{ $popup->enabled ? '' : 'is-off' }}"></div>
        </div>
      </div>

      <button type="button" class="btn-new-announcement mb-4" onclick="openForCreate()">
        <i class="ti ti-plus"></i> New Announcement
      </button>

      <div id="announcementList">
        @forelse ($announcements as $a)
          @php
            $payload = [
              'category' => $a->category, 'title' => $a->title, 'body' => $a->body,
              'pinned' => $a->pinned, 'posted_by' => $a->posted_by,
              'update' => route('admin.announcements.update', $a),
            ];
          @endphp
          <div class="announcement-row" data-announcement="{{ json_encode($payload) }}">
            <div class="d-flex justify-content-between align-items-start">
              <span class="category-tag tag-{{ $a->category }}">{{ $a->label() }}</span>
              @if ($a->pinned)<span class="pinned-badge"><i class="ti ti-pin"></i> Pinned</span>@endif
            </div>
            <div class="announcement-title">{{ $a->title }}</div>
            <div class="announcement-meta">Posted by {{ $a->posted_by }} · {{ $a->created_at->format('F j, Y') }}</div>
            <div class="announcement-body-preview">{{ $a->body }}</div>
            <div class="row-actions">
              <button type="button" class="btn btn-outline-secondary btn-sm" onclick="openForEdit(this)"><i class="ti ti-pencil"></i> Edit</button>
              <form method="POST" action="{{ route('admin.announcements.use-as-popup', $a) }}">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm"><i class="ti ti-bell-ringing"></i> Use as popup</button>
              </form>
              <form method="POST" action="{{ route('admin.announcements.destroy', $a) }}" onsubmit="return confirm('Delete this announcement? This cannot be undone.');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="ti ti-trash"></i> Delete</button>
              </form>
            </div>
          </div>
        @empty
          <div class="list-empty">No announcements yet. Post the first one with <strong>New Announcement</strong>.</div>
        @endforelse
      </div>

    </div>
  </div>

  <!-- Create/Edit modal - shared by both actions -->
  <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalTitle">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="announcementModalTitle">New Announcement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="announcementForm" method="POST" action="{{ route('admin.announcements.store') }}">
          @csrf
          <input type="hidden" name="_method" id="annMethod" value="PUT" disabled>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label" for="formCategory">Category</label>
              <select class="form-select" id="formCategory" name="category" required>
                <option value="" selected disabled>Select a category</option>
                @foreach ($categories as $key => $label)
                  <option value="{{ $key }}">{{ ucwords(strtolower($label)) }}</option>
                @endforeach
              </select>
              <div class="form-text">This decides the colored tag shown on the resident-facing Announcements page.</div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="formTitle">Title</label>
              <input type="text" class="form-control" id="formTitle" name="title" maxlength="160" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="formBody">Details</label>
              <textarea class="form-control" id="formBody" name="body" rows="4" maxlength="5000" required></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label" for="formPostedBy">Posted by <span class="text-muted fw-normal">(optional)</span></label>
              <input type="text" class="form-control" id="formPostedBy" name="posted_by" maxlength="120" placeholder="Leave blank to use your name, or e.g. Barangay Health Center">
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="formPinned" name="pinned" value="1">
              <label class="form-check-label" for="formPinned" style="font-size: 13.5px;">Pin this announcement to the top</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm" style="background: var(--ref-blue); color: #fff;">Save Announcement</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit popup modal: form on the left, live preview on the right -->
  <div class="modal fade" id="popupModal" tabindex="-1" aria-label="Edit Resident Popup">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Resident Popup</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="popupForm" method="POST" action="{{ route('admin.announcements.popup') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="remove_image" id="pfRemoveFlag" value="0">
          <div class="modal-body">
            <div class="row g-4">

              <div class="col-md-7">
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" type="checkbox" role="switch" id="pfEnabled" name="enabled" value="1">
                  <label class="form-check-label" for="pfEnabled" style="font-size: 13.5px;">Show this popup to residents</label>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="pfCategory">Category</label>
                  <select class="form-select" id="pfCategory" name="category" required>
                    @foreach ($categories as $key => $label)
                      <option value="{{ $key }}">{{ ucwords(strtolower($label)) }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="pfTitle">Headline</label>
                  <input type="text" class="form-control" id="pfTitle" name="title" maxlength="80" required>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="pfBody">Message</label>
                  <textarea class="form-control" id="pfBody" name="body" rows="4" maxlength="280" required></textarea>
                  <div class="form-text text-end"><span id="pfCount">0</span>/280</div>
                </div>

                <div>
                  <label class="form-label" for="pfImage">Picture <span style="color: var(--ref-gray-text); font-weight: 400;">(optional, up to 4 MB)</span></label>
                  <input type="file" class="form-control" id="pfImage" name="image" accept="image/*">
                  <button type="button" class="btn btn-link btn-sm p-0 mt-1" id="pfRemoveImage" style="font-size: 12.5px;">Remove picture</button>
                </div>
              </div>

              <div class="col-md-5">
                <div class="preview-caption">Live preview</div>
                <div id="popupPreviewLive" class="popup-preview"></div>
              </div>

            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm" style="background: var(--ref-blue); color: #fff;">Save popup</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 4000;">
    <div id="pbToast" class="toast align-items-center text-bg-dark border-0" role="status" aria-live="polite">
      <div class="d-flex">
        <div class="toast-body" id="pbToastText"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const $id = id => document.getElementById(id);
    const LABELS = @json($categories);
    const SAVED_POPUP = @json($popup->toPreviewArray());

    function esc(t) {
      return String(t ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }
    function showToast(message) {
      $id('pbToastText').textContent = message;
      bootstrap.Toast.getOrCreateInstance($id('pbToast'), { delay: 3500 }).show();
    }

    // ---------------- Announcements ----------------
    const annModal = new bootstrap.Modal($id('announcementModal'));
    const annForm = $id('announcementForm');
    const STORE_URL = @json(route('admin.announcements.store'));

    function openForCreate() {
      annForm.reset();
      annForm.action = STORE_URL;
      $id('annMethod').disabled = true;
      $id('announcementModalTitle').textContent = 'New Announcement';
      annModal.show();
    }

    function openForEdit(button) {
      const d = JSON.parse(button.closest('.announcement-row').dataset.announcement);
      annForm.reset();
      annForm.action = d.update;
      $id('annMethod').disabled = false;
      $id('announcementModalTitle').textContent = 'Edit Announcement';
      $id('formCategory').value = d.category;
      $id('formTitle').value = d.title;
      $id('formBody').value = d.body;
      $id('formPostedBy').value = d.posted_by || '';
      $id('formPinned').checked = !!d.pinned;
      annModal.show();
    }

    // ---------------- Resident popup ----------------
    let draft = null;        // what the editor modal is showing
    let pickedPreview = '';  // data URL of a newly chosen picture (preview only; the file itself is uploaded)

    function renderPreview(el, data) {
      const img = data.image
        ? `<div class="pv-image" style="background-image:url('${esc(data.image)}')"></div>`
        : `<div class="pv-image"><i class="ti ti-photo"></i></div>`;
      el.innerHTML = `
        <div class="pv-eyebrow">${esc(LABELS[data.category] || '')} · NEWEST ANNOUNCEMENT</div>
        ${img}
        <div class="pv-title">${esc(data.title) || 'Headline goes here'}</div>
        <div class="pv-body">${esc(data.body) || 'Your message goes here.'}</div>
        <div class="pv-actions">
          <span class="pv-btn ghost">Dismiss</span>
          <span class="pv-btn primary">View all announcements</span>
        </div>`;
    }

    function openPopupEditor() {
      draft = Object.assign({}, SAVED_POPUP);
      pickedPreview = '';
      $id('pfEnabled').checked = draft.enabled;
      $id('pfCategory').value = draft.category;
      $id('pfTitle').value = draft.title;
      $id('pfBody').value = draft.body;
      $id('pfImage').value = '';
      $id('pfRemoveFlag').value = '0';
      updateLivePreview();
      bootstrap.Modal.getOrCreateInstance($id('popupModal')).show();
    }

    function updateLivePreview() {
      draft.enabled  = $id('pfEnabled').checked;
      draft.category = $id('pfCategory').value;
      draft.title    = $id('pfTitle').value;
      draft.body     = $id('pfBody').value;
      $id('pfCount').textContent = draft.body.length;
      const shown = pickedPreview || draft.image;
      $id('pfRemoveImage').style.display = shown ? '' : 'none';
      renderPreview($id('popupPreviewLive'), Object.assign({}, draft, { image: shown }));
    }

    ['pfEnabled', 'pfCategory', 'pfTitle', 'pfBody'].forEach(id => {
      $id(id).addEventListener('input', updateLivePreview);
      $id(id).addEventListener('change', updateLivePreview);
    });

    $id('pfImage').addEventListener('change', function () {
      const file = this.files[0];
      if (!file) { pickedPreview = ''; updateLivePreview(); return; }
      if (file.size > 4 * 1024 * 1024) {
        showToast('That picture is over 4 MB. Please choose a smaller one.');
        this.value = ''; return;
      }
      const reader = new FileReader();
      reader.onload = () => { pickedPreview = reader.result; $id('pfRemoveFlag').value = '0'; updateLivePreview(); };
      reader.onerror = () => showToast('That file could not be read as a picture.');
      reader.readAsDataURL(file);
    });

    $id('pfRemoveImage').addEventListener('click', function () {
      pickedPreview = '';
      draft.image = '';
      $id('pfImage').value = '';
      $id('pfRemoveFlag').value = '1';
      updateLivePreview();
    });

    renderPreview($id('popupPreviewCard'), SAVED_POPUP);

    @if (session('status'))
      showToast(@json(session('status')));
    @endif
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <link href="{{ asset('css/pabarang-admin.css') }}" rel="stylesheet">
</body>
</html>
