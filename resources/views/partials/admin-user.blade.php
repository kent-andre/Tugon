{{-- Logged-in official, shown at the right of each admin page's top bar --}}
<div class="d-flex align-items-center gap-2">
  <span style="font-size: 13.5px; color: var(--ref-gray-text);">{{ $me['name'] ?? '' }}</span>
  <div class="admin-avatar" title="{{ $me['position'] ?? '' }}">{{ $me['initial'] ?? '' }}</div>
</div>
