{{-- Validation errors for one document form (errors are stored in a bag named after the type). --}}
@if ($errors->hasBag($type) && $errors->getBag($type)->any())
  <div class="alert alert-danger py-2" style="font-size: 13px;" role="alert">
    <strong>Please fix the following:</strong>
    <ul class="mb-0 ps-3">
      @foreach ($errors->getBag($type)->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
    @if ($type !== 'vaccination')
      <div class="mt-1" style="font-size: 12px;">For your security, please choose your file(s) again.</div>
    @endif
  </div>
@endif
