<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FAQ - Tugon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --ref-blue: #4F5FE0; --ref-navy-text: #1E2340; --ref-gray-text: #6B7280;
      --ref-bg: #F5F6FA; --ref-border: #E4E6EF;
    }
    body { font-family: 'Host Grotesk', sans-serif; color: var(--ref-navy-text); background: var(--ref-bg); }

    /* ===== Sidebar (shared across all resident pages) ===== */
    :root { --navy: #181a35; --navy-active: #5b52f5; }
    .main { margin-left: 250px; }
    .avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--navy-active); color: #fff;
      display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; cursor: pointer; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 18px 40px 0; }

    .page-wrap { max-width: 760px; margin: 0 auto; padding: 40px 20px; }
    .accordion-button { font-weight: 700; font-size: 14.5px; }
    .accordion-button:not(.collapsed) { background: #E7EAFD; color: var(--ref-blue); }
    .accordion-body { font-size: 13.5px; color: var(--ref-gray-text); }
    .faq-section-label { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--ref-gray-text); margin: 28px 0 12px; }
    .faq-section-label:first-of-type { margin-top: 0; }
  </style>
</head>
<body>

  @include('partials.resident-sidebar')

  <div class="main">
    <div class="top-bar">
      <h5 class="fw-bold mb-0">FAQ</h5>
    </div>



  <div class="page-wrap">
    <h3 class="fw-bold mb-1">Frequently Asked Questions</h3>
    <p style="color: var(--ref-gray-text); font-size: 14px;" class="mb-4">
      Common questions about using Tugon.
    </p>

    <div class="faq-section-label">Account &amp; Registration</div>
    <div class="accordion mb-3" id="faqAccount">

      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
            How long does account approval take?
          </button>
        </h2>
        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccount">
          <div class="accordion-body">
            New resident accounts are typically reviewed within 1-2 working days. You'll be
            able to log in once a barangay official verifies your submitted ID and address.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
            I forgot my password. What do I do?
          </button>
        </h2>
        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccount">
          <div class="accordion-body">
            Use the "Forgot password?" link on the login page and enter your registered email.
            You'll receive a link to reset your password.
          </div>
        </div>
      </div>

    </div>

    <div class="faq-section-label">Submitting Concerns</div>
    <div class="accordion mb-3" id="faqConcerns">

      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
            If I submit anonymously, can barangay officials still see who I am?
          </button>
        </h2>
        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqConcerns">
          <div class="accordion-body">
            No. When you check "Submit anonymously," your name is not attached to the report
            that officials see. This is meant to protect residents reporting sensitive
            concerns, such as safety or harassment issues.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
            What's the difference between a Concern and a Blotter Report?
          </button>
        </h2>
        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqConcerns">
          <div class="accordion-body">
            A Concern is for general issues needing barangay attention (broken streetlights,
            illegal dumping, noise). A Blotter Report is a formal, official record used when
            there's a dispute or incident involving another specific person. It may lead to
            barangay mediation (Katarungang Pambarangay).
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
            This is an emergency. Should I use this site?
          </button>
        </h2>
        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqConcerns">
          <div class="accordion-body">
            No. If you are in immediate danger, call <strong>911</strong>
            first. Submitting a report here notifies barangay officials, but it is not a
            substitute for emergency response. See the Emergency Hotlines page for more numbers.
          </div>
        </div>
      </div>

    </div>

    <div class="faq-section-label">Document Requests</div>
    <div class="accordion mb-3" id="faqDocuments">

      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
            How long does a document request take to process?
          </button>
        </h2>
        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqDocuments">
          <div class="accordion-body">
            Most documents (Barangay Clearance, Certificate of Residency) are ready within
            1-3 working days. Certificate of Indigency may take longer since it can require
            a follow-up assessment.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
            Do I need to pick up the document in person?
          </button>
        </h2>
        <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqDocuments">
          <div class="accordion-body">
            Yes. Barangay documents require an original signature and official seal, so pickup
            at the Barangay Alejal hall is required. You'll be notified once it's ready.
          </div>
        </div>
      </div>

    </div>

    <div class="faq-section-label">Privacy &amp; Safety</div>
    <div class="accordion" id="faqPrivacy">

      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
            Can other residents see my submitted concerns or reports?
          </button>
        </h2>
        <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqPrivacy">
          <div class="accordion-body">
            No. Only you and barangay officials can see your own reports. The Transparency
            Dashboard only shows barangay-wide totals, never individual report
            details.
          </div>
        </div>
      </div>

    </div>

  </div>
  </div>

  <link href="{{ asset('css/pabarang.css') }}" rel="stylesheet">
<script src="{{ asset('js/pabarang.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>