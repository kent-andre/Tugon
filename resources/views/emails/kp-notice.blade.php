<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $kind === 'summons' ? 'Summons' : 'Notice of Hearing' }}</title>
</head>
<body style="margin: 0; padding: 24px 12px; background: #ffffff;">

  @include('kp.notice', ['kind' => $kind, 'case' => $case])

  <div style="font-family: Arial, sans-serif; max-width: 640px; margin: 36px auto 0; padding-top: 14px; border-top: 1px solid #ddd; font-size: 12.5px; color: #555; line-height: 1.5;">
    @if ($kind === 'summons')
      This email is a copy for your reference. The official summons is served in person by a barangay officer.<br>
    @endif
    Please do not reply to this email. For questions, visit the barangay hall.
  </div>

</body>
</html>
