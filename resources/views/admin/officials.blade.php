{{--
  Officials roster. Data: Admin\OfficialController@index ($officials grouped by
  `group`, $groupLabels from config('pabarang.official_groups')).
  Add / Edit / Remove post to OfficialController@store / @update / @destroy,
  each logged in audit_logs. The resident Officials Directory reads the same
  table, and blotter mediators should come from Official::lupon().
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Officials - Tugon Admin</title>
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

    .btn-new-official { background: var(--ref-blue); color: #fff; border: none; border-radius: 8px; padding: 9px 18px; font-weight: 600; font-size: 13.5px; }
    .btn-new-official:hover { background: #3E4DCB; color: #fff; }

    .group-heading { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--ref-gray-text); margin: 26px 0 12px; display: flex; align-items: center; gap: 8px; }
    .group-count { background: #E7EAFD; color: var(--ref-blue); border-radius: 999px; padding: 1px 8px; font-size: 11px; }
    .group-empty { font-size: 13px; color: var(--ref-gray-text); background: #fff; border: 1px dashed var(--ref-border); border-radius: 12px; padding: 16px 18px; }

    .official-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px; }
    .official-card { background: #fff; border: 1px solid var(--ref-border); border-radius: 12px; padding: 16px 18px; position: relative; }
    .official-card .avatar {
      width: 44px; height: 44px; border-radius: 50%; background: #E7EAFD; color: var(--ref-blue);
      display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; margin-bottom: 10px; overflow: hidden;
    }
    .official-card .avatar img { width: 100%; height: 100%; object-fit: cover; }
    .official-card.head .avatar { background: var(--ref-blue); color: #fff; }
    .official-name { font-size: 14.5px; font-weight: 700; padding-right: 50px; }
    .official-position { font-size: 12.5px; color: var(--ref-blue); font-weight: 600; margin-bottom: 6px; }
    .official-meta { font-size: 12px; color: var(--ref-gray-text); line-height: 1.6; }
    .official-actions { position: absolute; top: 14px; right: 14px; display: flex; gap: 4px; }
    .official-actions button { background: none; border: none; color: var(--ref-gray-text); font-size: 15px; padding: 2px 4px; }
    .official-actions button:hover { color: var(--ref-blue); }
    .official-actions .btn-remove:hover { color: var(--ref-red); }
  </style>
</head>
<body>

  <div class="admin-layout">

    @include('partials.admin-sidebar')

    <!-- ===== Main content ===== -->
    <div class="admin-main">

      <div class="admin-topbar">
        <h4>Officials</h4>
        @include('partials.admin-user')
      </div>

      @if ($errors->any())
        <div class="alert alert-danger" style="font-size:13.5px;">
          <strong>The official was not saved.</strong>
          <ul class="mb-0 mt-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      <button type="button" class="btn-new-official mb-2" onclick="openForCreate()">
        <i class="ti ti-plus"></i> Add Official
      </button>

      @foreach ($groupLabels as $key => $label)
        @php $list = $officials->get($key, collect()); @endphp
        <div class="group-heading">{{ $label }} <span class="group-count">{{ $list->count() }}</span></div>
        @if ($list->isEmpty())
          <div class="group-empty">No one listed yet.@if ($key === 'lupon') Blotter mediators are picked from this group, so add the Lupon members here.@endif</div>
        @else
          <div class="official-grid">
            @foreach ($list as $o)
              @php
                $payload = [
                  'id' => $o->id, 'group' => $o->group, 'name' => $o->name, 'position' => $o->position,
                  'committee' => $o->committee, 'contact' => $o->contact, 'term' => $o->term, 'photo' => $o->photoUrl(),
                  'update' => route('admin.officials.update', $o), 'destroy' => route('admin.officials.destroy', $o),
                ];
              @endphp
              <div class="official-card {{ $o->position === 'Punong Barangay' ? 'head' : '' }}" data-official="{{ json_encode($payload) }}">
                <div class="official-actions">
                  <button type="button" onclick="openForEdit(this)" title="Edit" aria-label="Edit {{ $o->name }}"><i class="ti ti-pencil"></i></button>
                  <button type="button" class="btn-remove" onclick="removeOfficial(this)" title="Remove" aria-label="Remove {{ $o->name }}"><i class="ti ti-trash"></i></button>
                </div>
                <div class="avatar">
                  @if ($o->photoUrl())
                    <img src="{{ $o->photoUrl() }}" alt="" onerror="this.remove()">
                  @endif
                  {{ $o->initials() }}
                </div>
                <div class="official-name">{{ $o->name }}</div>
                <div class="official-position">{{ $o->position }}</div>
                <div class="official-meta">
                  @if ($o->committee){{ $o->committee }}<br>@endif
                  @if ($o->contact)<i class="ti ti-phone" style="font-size:12px;"></i> {{ $o->contact }} &middot; @endif{{ $o->term ?: 'No term set' }}
                </div>
              </div>
            @endforeach
          </div>
        @endif
      @endforeach

    </div>
  </div>

  <!-- Create/Edit modal -->
  <div class="modal fade" id="officialModal" tabindex="-1" aria-labelledby="officialModalTitle">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="officialModalTitle">Add Official</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="officialForm" method="POST" action="{{ route('admin.officials.store') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="_method" id="formMethod" value="POST" disabled>
          <input type="hidden" name="_editing" id="editingId" value="">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label" for="formGroup">Group</label>
              <select class="form-select" id="formGroup" name="group" required>
                @foreach ($groupLabels as $key => $label)
                  <option value="{{ $key }}" @selected(old('group') === $key)>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label" for="formName">Full Name</label>
              <input type="text" class="form-control" id="formName" name="name" maxlength="120" value="{{ old('name') }}" placeholder="e.g. Juan D. Cruz" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="formPosition">Position</label>
              <input type="text" class="form-control" id="formPosition" name="position" maxlength="80" value="{{ old('position') }}" placeholder="e.g. Barangay Kagawad" list="positionList" required>
              <datalist id="positionList">
                <option>Punong Barangay</option><option>Barangay Kagawad</option><option>Barangay Secretary</option>
                <option>Barangay Treasurer</option><option>SK Chairman</option><option>SK Kagawad</option>
                <option>Chief Barangay Tanod</option><option>Barangay Tanod</option><option>Lupong Tagapamayapa Member</option>
              </datalist>
            </div>
            <div class="mb-3">
              <label class="form-label" for="formCommittee">Committee / Assignment</label>
              <input type="text" class="form-control" id="formCommittee" name="committee" maxlength="120" value="{{ old('committee') }}" placeholder="e.g. Committee on Health">
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label" for="formContact">Contact Number</label>
                <input type="text" class="form-control" id="formContact" name="contact" maxlength="30" value="{{ old('contact') }}" placeholder="09XX XXX XXXX">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" for="formTerm">Term</label>
                <input type="text" class="form-control" id="formTerm" name="term" maxlength="40" value="{{ old('term') }}" placeholder="e.g. 2023 - 2026">
              </div>
            </div>
            <div>
              <label class="form-label" for="formPhoto">Photo <span class="text-muted fw-normal">(optional, shown on the resident directory)</span></label>
              <input type="file" class="form-control" id="formPhoto" name="photo" accept="image/*">
              <div class="form-check mt-2 d-none" id="removePhotoWrap">
                <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="formRemovePhoto">
                <label class="form-check-label" for="formRemovePhoto" style="font-size:13px;">Remove the current photo</label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm" style="background: var(--ref-blue); color: #fff;">Save Official</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Hidden delete form, reused for every card -->
  <form id="deleteForm" method="POST" class="d-none">@csrf @method('DELETE')</form>

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
    const STORE_URL = @json(route('admin.officials.store'));
    const modal = new bootstrap.Modal(document.getElementById('officialModal'));
    const form = document.getElementById('officialForm');
    const method = document.getElementById('formMethod');
    const field = id => document.getElementById(id);

    function openForCreate() {
      form.reset();
      form.action = STORE_URL;
      method.disabled = true;                 // plain POST = create
      field('editingId').value = '';
      field('officialModalTitle').textContent = 'Add Official';
      field('removePhotoWrap').classList.add('d-none');
      modal.show();
    }

    function openForEdit(button) {
      const d = JSON.parse(button.closest('.official-card').dataset.official);
      form.reset();
      form.action = d.update;
      method.value = 'PUT'; method.disabled = false;
      field('editingId').value = d.id;
      field('officialModalTitle').textContent = 'Edit Official';
      field('formGroup').value = d.group;
      field('formName').value = d.name;
      field('formPosition').value = d.position;
      field('formCommittee').value = d.committee || '';
      field('formContact').value = d.contact || '';
      field('formTerm').value = d.term || '';
      field('removePhotoWrap').classList.toggle('d-none', !d.photo);
      modal.show();
    }

    function removeOfficial(button) {
      const d = JSON.parse(button.closest('.official-card').dataset.official);
      if (!confirm('Remove ' + d.name + ' from the roster?')) return;
      const del = document.getElementById('deleteForm');
      del.action = d.destroy;
      del.submit();
    }

    // Validation failed: reopen the form with what was typed.
    @if ($errors->any())
      @if (old('_method') === 'PUT' && ctype_digit((string) old('_editing')))
        form.action = @json(url('/admin/officials/' . old('_editing')));
        method.value = 'PUT'; method.disabled = false;
        field('officialModalTitle').textContent = 'Edit Official';
        field('editingId').value = @json(old('_editing'));
      @endif
      modal.show();
    @endif

    @if (session('status'))
      document.getElementById('pbToastText').textContent = @json(session('status'));
      bootstrap.Toast.getOrCreateInstance(document.getElementById('pbToast'), { delay: 3500 }).show();
    @endif
  </script>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <link href="{{ asset('css/pabarang-admin.css') }}" rel="stylesheet">
</body>
</html>
