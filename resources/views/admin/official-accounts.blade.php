<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Official Accounts - Tugon Admin</title>
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

    .admin-main { flex: 1; padding: 28px 36px; min-width: 0; }
    .admin-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
    .admin-topbar h4 { font-weight: 800; margin: 0; }
    .admin-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--ref-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; }

    .box { background: #fff; border: 1px solid var(--ref-border); border-radius: 14px; padding: 22px 24px; }
    .form-label { font-size: 12.5px; font-weight: 600; margin-bottom: 4px; }
    .hint { font-size: 12.5px; color: var(--ref-gray-text); }

    .acct-row { display: flex; align-items: center; gap: 14px; padding: 15px 0; border-bottom: 1px solid var(--ref-border); }
    .acct-row:last-child { border-bottom: none; }
    .acct-avatar { width: 40px; height: 40px; border-radius: 50%; background: #E7EAFD; color: var(--ref-blue); display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
    .acct-name { font-size: 14.5px; font-weight: 700; }
    .acct-meta { font-size: 12px; color: var(--ref-gray-text); }
    .pill { font-size: 11.5px; font-weight: 600; padding: 4px 11px; border-radius: 10px; white-space: nowrap; }
    .role-captain { background: #E7EAFD; color: var(--ref-blue); }
    .role-secretary { background: #E1EEFB; color: #0C5FA1; }
    .role-staff { background: #EDEDED; color: #555; }
    .off { background: #FDE8E8; color: var(--ref-red); }
  </style>
</head>
<body>
  <div class="admin-layout">
    @include('partials.admin-sidebar')

    <div class="admin-main">
      <div class="admin-topbar">
        <h4>Official Accounts</h4>
        @include('partials.admin-user')
      </div>

      @if (session('ok'))
        <div class="alert alert-success py-2">{{ session('ok') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
      @endif

      <div class="box mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
          <p class="hint mb-0">Each official logs in with their own account. Every action they take is written in the Audit Log with their name.</p>
          <button class="btn btn-sm" style="background: var(--ref-blue); color: #fff;" data-bs-toggle="modal" data-bs-target="#addModal"><i class="ti ti-plus"></i> Add Official</button>
        </div>
      </div>

      <div class="box">
        @foreach ($admins as $a)
          @php $plain = preg_replace('/^(Hon\.|Kgd\.|Mr\.|Ms\.|Mrs\.)\s*/i', '', $a->name); @endphp
          <div class="acct-row">
            <div class="acct-avatar">{{ mb_strtoupper(mb_substr($plain, 0, 1)) }}</div>
            <div class="flex-grow-1">
              <div class="acct-name">{{ $a->name }} @if ($a->id === $me['id'])<span class="hint">(you)</span>@endif</div>
              <div class="acct-meta">{{ $a->position }} &middot; {{ $a->email }} &middot; Last login {{ $a->last_login_at?->format('M j, Y, g:i A') ?? 'never' }}</div>
            </div>
            @unless ($a->is_active)<span class="pill off">Disabled</span>@endunless
            <span class="pill role-{{ $a->role }}">{{ ucfirst($a->role === 'captain' ? 'Punong Barangay' : $a->role) }}</span>
            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#edit{{ $a->id }}">Edit</button>
          </div>

          <div class="modal fade" id="edit{{ $a->id }}" tabindex="-1">
            <div class="modal-dialog">
              <form class="modal-content" method="POST" action="{{ url('/admin/official-accounts/' . $a->id) }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Edit {{ $a->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                  <div class="mb-2"><label class="form-label">Full name</label><input name="name" class="form-control form-control-sm" value="{{ $a->name }}" required maxlength="120"></div>
                  <div class="mb-2"><label class="form-label">Position</label><input name="position" class="form-control form-control-sm" value="{{ $a->position }}" required maxlength="120"></div>
                  @php $self = $a->id === $me['id']; @endphp
                  <div class="mb-2"><label class="form-label">Access</label>
                    <select name="role" class="form-select form-select-sm" @if ($self) disabled @endif>
                      @foreach ($roles as $key => $label)<option value="{{ $key }}" @selected($a->role === $key)>{{ $label }}</option>@endforeach
                    </select>
                    @if ($self)<input type="hidden" name="role" value="{{ $a->role }}">@endif
                  </div>
                  <div class="mb-2"><label class="form-label">Status</label>
                    <select name="is_active" class="form-select form-select-sm" @if ($self) disabled @endif>
                      <option value="1" @selected($a->is_active)>Active</option>
                      <option value="0" @selected(! $a->is_active)>Disabled (cannot log in)</option>
                    </select>
                    @if ($self)<input type="hidden" name="is_active" value="1">@endif
                  </div>
                  <div><label class="form-label">New temporary password <span class="text-muted fw-normal">(leave blank to keep)</span></label><input name="password" type="text" class="form-control form-control-sm" minlength="8" autocomplete="off"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button class="btn btn-sm" style="background: var(--ref-blue); color: #fff;">Save</button></div>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" method="POST" action="{{ url('/admin/official-accounts') }}">
        @csrf
        <div class="modal-header"><h5 class="modal-title">Add Official Account</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-2"><label class="form-label">Full name</label><input name="name" class="form-control form-control-sm" placeholder="Kgd. Juan P. Reyes" value="{{ old('name') }}" required maxlength="120"></div>
          <div class="mb-2"><label class="form-label">Position</label><input name="position" class="form-control form-control-sm" placeholder="Kagawad, Committee on Peace and Order" value="{{ old('position') }}" required maxlength="120"></div>
          <div class="mb-2"><label class="form-label">Email</label><input name="email" type="email" class="form-control form-control-sm" value="{{ old('email') }}" required></div>
          <div class="mb-2"><label class="form-label">Access</label>
            <select name="role" class="form-select form-select-sm">
              @foreach ($roles as $key => $label)<option value="{{ $key }}" @selected(old('role', 'staff') === $key)>{{ $label }}</option>@endforeach
            </select>
          </div>
          <div><label class="form-label">Temporary password</label><input name="password" type="text" class="form-control form-control-sm" minlength="8" required autocomplete="off">
            <div class="hint mt-1">Give this to the official in person. They change it under Change Password.</div></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button class="btn btn-sm" style="background: var(--ref-blue); color: #fff;">Create Account</button></div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <link href="{{ asset('css/pabarang-admin.css') }}" rel="stylesheet">
</body>
</html>
