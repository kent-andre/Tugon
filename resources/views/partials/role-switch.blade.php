{{--
  Resident / Admin login switch.
  Usage:
    @include('partials.role-switch', ['active' => 'resident'])
    @include('partials.role-switch', ['active' => 'admin'])
  Save as: resources/views/partials/role-switch.blade.php
--}}
<div class="d-flex justify-content-center">
  <nav class="role-switch {{ $active === 'admin' ? 'is-admin' : '' }}" aria-label="Choose login type">
    <span class="role-switch__thumb" aria-hidden="true"></span>

    <a href="{{ url('/') }}"
       class="role-switch__opt {{ $active === 'resident' ? 'active' : '' }}"
       @if ($active === 'resident') aria-current="page" @endif>
      <span>Resident</span>
    </a>

    <a href="{{ url('/admin-login') }}"
       class="role-switch__opt {{ $active === 'admin' ? 'active' : '' }}"
       @if ($active === 'admin') aria-current="page" @endif>
      <span>Admin</span>
    </a>
  </nav>
</div>

<script>
  // Slide the highlight first, then go to the other login page.
  document.querySelectorAll('.role-switch__opt:not(.active)').forEach(function (link) {
    link.addEventListener('click', function (e) {
      var sw = link.closest('.role-switch');
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      e.preventDefault();
      sw.classList.toggle('is-admin');
      setTimeout(function () { window.location.href = link.href; }, 280);
    });
  });
</script>
