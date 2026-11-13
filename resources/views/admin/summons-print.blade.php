<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Summons {{ $case['ref'] }} - Tugon Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body { margin: 0; background: #E9EAF0; font-family: Georgia, 'Times New Roman', serif; }
    .toolbar { font-family: Arial, sans-serif; padding: 12px 16px; background: #1E2340; color: #fff; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .toolbar button { font-size: 14px; padding: 7px 14px; border: 0; border-radius: 6px; background: #4F5FE0; color: #fff; cursor: pointer; }
    .toolbar span { font-size: 13px; color: #C4C8E8; }
    .page { background: #fff; width: 210mm; max-width: 100%; min-height: 280mm; margin: 18px auto; padding: 20mm 18mm; box-sizing: border-box; }
    .return { margin-top: 36px; padding-top: 14px; border-top: 2px solid #111; font-size: 14.5px; line-height: 1.7; }
    .return h4 { margin: 0 0 8px; font-size: 15px; letter-spacing: .06em; }
    .line { display: inline-block; min-width: 200px; border-bottom: 1px solid #111; }
    .box { display: inline-block; width: 12px; height: 12px; border: 1px solid #111; margin-right: 6px; vertical-align: -1px; text-align: center; font-size: 11px; line-height: 12px; }
    .received { margin-top: 26px; display: flex; justify-content: space-between; gap: 20px; font-size: 14px; }
    .received div { flex: 1; text-align: center; border-top: 1px solid #111; padding-top: 4px; }
    @page { size: A4; margin: 12mm 14mm; }
    @media print {
      body { background: #fff; }
      .toolbar { display: none; }
      .page { width: auto; margin: 0; padding: 0; min-height: auto; }
      /* Break before each later form, not after, so nothing added at the end adds a blank sheet */
      .page + .page { page-break-before: always; }
      /* Smaller type so each form fits on one sheet */
      .page > div:first-child { font-size: 13px !important; line-height: 1.4 !important; }
      .page h3 { margin: 16px 0 10px !important; font-size: 16px !important; }
      .page p { margin: 0 0 8px; }
      .return { margin-top: 18px; font-size: 12.5px; line-height: 1.5; }
      .received { margin-top: 30px; font-size: 12px; }
    }
  </style>
</head>
<body>

  <div class="toolbar">
    <button type="button" onclick="window.print()">Print</button>
    <span>Page 1 is the Summons for the respondent. Page 2 is the Notice of Hearing for the complainant.</span>
  </div>

  {{-- Page 1: Summons, with the Officer's Return at the bottom --}}
  <div class="page">
    @include('kp.notice', ['kind' => 'summons', 'case' => $case])

    <div class="return">
      <h4>OFFICER'S RETURN</h4>
      {{-- Always blank: the officer fills this in by hand when serving the summons. --}}
      I served this summons upon the respondent, <strong>{{ $case['respondent'] }}</strong>,
      on <span class="line">&nbsp;</span> by:<br>
      @foreach ($methods as $label)
        <span class="box"></span>{{ $label }}<br>
      @endforeach

      <div class="received">
        <div>Name and signature of serving officer</div>
        <div>Signature of respondent and date received</div>
      </div>
    </div>
  </div>

  {{-- Page 2: Notice of Hearing for the complainant --}}
  <div class="page">
    @include('kp.notice', ['kind' => 'notice', 'case' => $case])

    <div class="received" style="margin-top: 50px;">
      <div>Signature of complainant and date received</div>
    </div>
  </div>

</body>
</html>
