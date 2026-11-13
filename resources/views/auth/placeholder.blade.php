<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $title }} - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body style="background: var(--color-bg);">

  {{--
    BACKEND TODO (final term):
    This is a shared placeholder used by many routes. When you build the
    REAL version of whichever feature this currently stands in for
    (Submit a Concern, Request a Document, etc.), you'll:

    1. Create a proper Blade file for it (e.g. resident/submit-concern.blade.php)
    2. Create a Controller with methods to show the form (GET) and
       process/save it (POST) - probably touching the database via an
       Eloquent Model (e.g. Concern::create([...]))
    3. Update the matching line in routes/web.php to point at that
       controller instead of this placeholder view.

    Nothing on THIS file itself needs backend logic - it's meant to be
    deleted from use (not literally deleted) once every route has a
    real page instead.
  --}}

  <div class="container py-5">
    <a href="{{ url('/home') }}" class="link-gold" style="font-size: 14px;">&larr; Back to Home</a>

    <div class="text-center py-5 mt-4">
      <i class="ti ti-tools" style="font-size: 48px; color: var(--color-gold);"></i>
      <h2 class="fw-bold mt-3">{{ $title }}</h2>
      <p class="page-subtext">
        This page isn't built yet — it's next on the list.
      </p>
    </div>
  </div>

   <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
<script src="{{ asset('js/pabarang.js') }}"></script>

</body>
</html>