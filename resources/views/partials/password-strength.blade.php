{{--
  Live password checklist. Put it right under the "type it again" box:

    @include('partials.password-strength', [
        'input'    => 'password',          // id of the new password box
        'confirm'  => 'confirmPassword',   // id of the "type it again" box
        'fields'   => ['firstName', 'lastName', 'email'],  // ids of boxes with the person's name/email (optional)
        'personal' => [$name, $email],     // name/email already known (optional)
    ])

  The submit button of the form stays off until every rule is met and both boxes match.
  The server checks the same rules again (App\Support\StrongPassword).
--}}
@php
  $pwsId = 'pws' . substr(md5(($input ?? 'password') . ($confirm ?? '')), 0, 6);
  $pwsTokens = \App\Support\StrongPassword::tokens($personal ?? []);
@endphp
<div class="pws" id="{{ $pwsId }}" data-input="{{ $input ?? 'password' }}" data-confirm="{{ $confirm ?? '' }}"
     data-fields="{{ implode(',', $fields ?? []) }}" data-tokens='@json($pwsTokens)' aria-live="polite">
  <div class="pws-head">
    <div class="pws-bar" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
    <div class="pws-level">Password strength: <b>Not set</b></div>
  </div>
  <ul class="pws-list">
    <li data-rule="len">At least 8 characters</li>
    <li data-rule="upper">An uppercase letter (A-Z)</li>
    <li data-rule="lower">A lowercase letter (a-z)</li>
    <li data-rule="num">A number (0-9)</li>
    <li data-rule="sym">A symbol, like ! @ # ? or *</li>
    <li data-rule="mine">Doesn't contain your name or email</li>
    <li data-rule="match">Both password boxes match</li>
  </ul>
  <p class="pws-hint">Fill in every rule above to continue.</p>
</div>

@once
<style>
  .pws { margin-top: .5rem; font-size: .88rem; }
  .pws-head { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; margin-bottom: .45rem; }
  .pws-bar { display: grid; grid-template-columns: repeat(4, 1fr); gap: 4px; width: 160px; }
  .pws-bar span { height: 6px; border-radius: 3px; background: rgba(127,127,127,.22); transition: background-color .25s ease; }
  .pws[data-level="1"] .pws-bar span:nth-child(-n+1) { background: #D64545; }
  .pws[data-level="2"] .pws-bar span:nth-child(-n+2) { background: #E0A21B; }
  .pws[data-level="3"] .pws-bar span:nth-child(-n+3) { background: #3FB37F; }
  .pws[data-level="4"] .pws-bar span { background: #2F9E6B; }
  .pws-level { opacity: .85; }
  .pws-list { list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(min(220px, 100%), 1fr)); gap: .2rem .9rem; }
  .pws-list li { display: flex; align-items: center; gap: .45rem; opacity: .75; transition: opacity .2s, color .2s; }
  .pws-list li::before { content: ''; flex: none; width: 1rem; height: 1rem; border-radius: 50%; border: 1.5px solid currentColor; opacity: .55; }
  .pws-list li.ok { opacity: 1; color: #2F9E6B; }
  .pws-list li.ok::before { opacity: 1; border-color: #2F9E6B; background: #2F9E6B
    url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M4 8.5l2.5 2.5L12 5.5' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center / 100% no-repeat; }
  .pws-list li.bad { opacity: 1; color: #D64545; }
  .pws-hint { margin: .5rem 0 0; font-size: .82rem; opacity: .75; }
  .pws.all-ok .pws-hint { display: none; }
  html[data-theme="dark"] .pws-list li.ok, html[data-theme="dark"] .pws-list li.ok::before { color: #5BD39A; }
  @media (prefers-reduced-motion: reduce) { .pws * { transition: none !important; } }
</style>
@endonce

<script>
  (function () {
    var box = document.getElementById(@json($pwsId));
    if (!box) return;
    var pw = document.getElementById(box.dataset.input);
    var pw2 = box.dataset.confirm ? document.getElementById(box.dataset.confirm) : null;
    if (!pw) return;
    var form = pw.form;
    // Found when needed: the button sits below this script, so it isn't on the page yet at this point
    function submitBtn() { return form ? form.querySelector('[type="submit"]') : null; }
    var fixed = JSON.parse(box.dataset.tokens || '[]');
    var fields = (box.dataset.fields || '').split(',').filter(Boolean).map(function (id) { return document.getElementById(id); }).filter(Boolean);
    var item = function (r) { return box.querySelector('[data-rule="' + r + '"]'); };
    var levels = ['Not set', 'Weak', 'Okay', 'Strong', 'Very strong'];
    var touched = false;

    // Same idea as App\Support\StrongPassword::tokens(): name parts and the part before @, 3+ letters
    function tokens() {
      var out = fixed.slice();
      fields.forEach(function (f) {
        var v = (f.value || '').toLowerCase().trim();
        if (v.indexOf('@') > -1) v = v.split('@')[0];
        v.split(/[^\p{L}\p{N}]+/u).forEach(function (p) { if (p.length >= 3) out.push(p); });
      });
      return out;
    }

    function check() {
      var v = pw.value, low = v.toLowerCase();
      var r = {
        len: v.length >= 8,
        upper: /[A-Z]/.test(v),
        lower: /[a-z]/.test(v),
        num: /[0-9]/.test(v),
        sym: /[^A-Za-z0-9]/.test(v),
        mine: v.length > 0 && !tokens().some(function (t) { return low.indexOf(t) > -1; }),
        match: !pw2 || (pw2.value.length > 0 && pw2.value === v)
      };
      Object.keys(r).forEach(function (k) {
        var li = item(k); if (!li) return;
        li.classList.toggle('ok', r[k]);
        // Red only after the person has started typing, not on an empty form
        li.classList.toggle('bad', !r[k] && touched && (k !== 'match' || (pw2 && pw2.value.length > 0)));
      });

      var core = ['len', 'upper', 'lower', 'num', 'sym'].filter(function (k) { return r[k]; }).length;
      var level = v.length === 0 ? 0 : core <= 2 ? 1 : core <= 4 ? 2 : (v.length >= 12 ? 4 : 3);
      if (!r.mine && level > 1) level = 1;
      box.dataset.level = level;
      box.querySelector('.pws-level b').textContent = levels[level];

      var allOk = Object.keys(r).every(function (k) { return r[k]; });
      box.classList.toggle('all-ok', allOk);
      var btn = submitBtn();
      if (btn) { btn.disabled = !allOk; btn.title = allOk ? '' : 'Your password does not meet all the rules yet.'; }
      return allOk;
    }

    function onInput() { touched = true; check(); }
    pw.addEventListener('input', onInput);
    if (pw2) pw2.addEventListener('input', onInput);
    fields.forEach(function (f) { f.addEventListener('input', check); });
    if (form) form.addEventListener('submit', function (e) {
      if (!check()) { e.preventDefault(); touched = true; check(); pw.focus(); }
    });
    check();
    // Once the whole page is drawn, set the button for the first time
    document.addEventListener('DOMContentLoaded', check);
  })();
</script>
