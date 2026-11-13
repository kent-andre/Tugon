<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password - Tugon Admin</title>
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
  </style>
</head>
<body>
  <div class="admin-layout">
    @include('partials.admin-sidebar')

    <div class="admin-main">
      <div class="admin-topbar">
        <h4>Change Password</h4>
        @include('partials.admin-user')
      </div>

      @if (session('ok'))
        <div class="alert alert-success py-2">{{ session('ok') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
      @endif

      <div class="box" style="max-width: 460px;">
        <form method="POST" action="{{ url('/admin/password') }}">
          @csrf
          <div class="mb-3"><label class="form-label">Current password</label><input type="password" name="current_password" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">New password</label><input type="password" name="password" class="form-control" minlength="8" required>
            <div class="hint mt-1">At least 8 characters. Do not reuse a password from another site.</div></div>
          <div class="mb-3"><label class="form-label">Confirm new password</label><input type="password" name="password_confirmation" class="form-control" minlength="8" required></div>
          <button class="btn" style="background: var(--ref-blue); color: #fff;">Change Password</button>
        </form>
      </div>
    </div>
  </div>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
  <link href="{{ asset('css/pabarang-admin.css') }}" rel="stylesheet">
</body>
</html>
