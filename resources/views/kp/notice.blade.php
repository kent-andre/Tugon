{{-- Body of the Summons and the Notice of Hearing. Used by the email and the print page.
     Needs $kind ('summons' or 'notice') and $case (from BlotterController::noticeData). --}}
@php $summons = $kind === 'summons'; @endphp
<div style="font-family: Georgia, 'Times New Roman', serif; color: #111; max-width: 640px; margin: 0 auto; font-size: 15px; line-height: 1.55;">

  <div style="text-align: center;">
    Republic of the Philippines<br>
    @if ($case['address']){{ $case['address'] }}<br>@endif
    <strong>{{ strtoupper($case['barangay']) }}</strong><br>
    OFFICE OF THE LUPONG TAGAPAMAYAPA
  </div>

  <table style="width: 100%; margin-top: 26px; font-size: 15px; border-collapse: collapse;">
    <tr>
      <td style="vertical-align: top; padding: 0;">
        <strong>{{ $case['complainant'] }}</strong><br><em>Complainant</em><br><br>
        against<br><br>
        <strong>{{ $case['respondent'] }}</strong><br><em>Respondent</em>
      </td>
      <td style="vertical-align: top; text-align: right; padding: 0;">
        Barangay Case No. {{ $case['ref'] }}<br>
        For: {{ $case['type'] }}
      </td>
    </tr>
  </table>

  <h3 style="text-align: center; letter-spacing: .08em; margin: 30px 0 18px; font-size: 18px;">
    {{ $summons ? 'SUMMONS' : 'NOTICE OF HEARING' }}
  </h3>

  <p>TO: <strong>{{ $summons ? $case['respondent'] : $case['complainant'] }}</strong></p>

  @if ($summons)
    <p>You are hereby summoned to appear before me in person, together with your witnesses, on
      <strong>{{ $case['hearing_date'] }}</strong> at <strong>{{ $case['hearing_time'] }}</strong>,
      at <strong>{{ $case['place'] }}</strong>, to answer the complaint made before me for the mediation
      of your dispute with the complainant.</p>
    <p>If you refuse or willfully fail to appear in obedience to this summons, you may be barred from
      filing any counterclaim arising from this complaint.</p>
  @else
    <p>You are hereby required to appear before me on <strong>{{ $case['hearing_date'] }}</strong> at
      <strong>{{ $case['hearing_time'] }}</strong>, at <strong>{{ $case['place'] }}</strong>, for the hearing
      of your complaint.</p>
    <p>If you willfully fail to appear, your complaint may be dismissed and you may be barred from
      filing the same action in court.</p>
  @endif

  <p>Please appear in person and bring one valid ID.</p>

  <p>Issued this {{ $case['issued'] }}.</p>

  <div style="margin-top: 46px; text-align: right;">
    <div style="display: inline-block; text-align: center; min-width: 260px;">
      <div style="border-top: 1px solid #111; padding-top: 4px;"><strong>{{ strtoupper($case['captain']) }}</strong></div>
      Punong Barangay / Lupon Chairman
    </div>
  </div>
</div>
