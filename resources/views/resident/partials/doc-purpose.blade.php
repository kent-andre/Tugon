{{-- Purpose dropdown + "please specify" box. Needs: $type, $purposes, $old (closure from request-document). --}}
<div class="mb-3">
  <label class="form-label" for="{{ $type }}Purpose">Purpose</label>
  <select class="form-select purpose-select" id="{{ $type }}Purpose" name="purpose" required>
    <option value="" disabled @selected($old($type, 'purpose') === '')>Select purpose</option>
    @foreach ($purposes as $p)
      <option @selected($old($type, 'purpose') === $p)>{{ $p }}</option>
    @endforeach
  </select>
  <div class="other-reason-box mt-2" style="display: none;">
    <input type="text" class="form-control" name="purpose_other" maxlength="120" value="{{ $old($type, 'purpose_other') }}"
           placeholder="Please specify your reason" aria-label="Reason">
  </div>
</div>
