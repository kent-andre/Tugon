<!DOCTYPE html>
<html lang="en">
<body style="margin:0; padding:24px; background:#EEF0F8; font-family: Arial, sans-serif; color:#12183C;">
  <div style="max-width:480px; margin:0 auto; background:#fff; border-radius:12px; padding:28px;">
    <h2 style="margin:0 0 12px; font-size:20px;">Reset your password</h2>
    <p style="font-size:14px; line-height:1.5;">Hi {{ $name }},</p>
    <p style="font-size:14px; line-height:1.5;">
      Someone asked to reset the password of your Tugon account for {{ config('pabarang.barangay_name', 'Barangay Alejal') }}.
      Tap the button to choose a new password. The link works once and expires in {{ $minutes }} minutes.
    </p>
    <p style="text-align:center; margin:24px 0;">
      <a href="{{ $link }}" style="background:#3D4ED1; color:#fff; text-decoration:none; padding:12px 22px; border-radius:8px; font-weight:bold; font-size:14px; display:inline-block;">Choose a new password</a>
    </p>
    <p style="font-size:12px; color:#6B7280; line-height:1.5;">
      If you didn't ask for this, ignore this email. Your password stays the same.
    </p>
    <p style="font-size:12px; color:#6B7280; line-height:1.5; word-break:break-all;">
      Button not working? Copy this link into your browser:<br>{{ $link }}
    </p>
  </div>
</body>
</html>
