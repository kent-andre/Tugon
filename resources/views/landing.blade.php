<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Tugon') }} | Barangay Concern &amp; Incident Reporting</title>
    <meta name="description" content="Tugon: Barangay Alejal's web-based concern and incident reporting system. Report concerns, request documents, and stay connected.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Host Grotesk for headlines and text, same as the rest of Tugon --}}
    <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Theme: dark (default) or sky. Set before the page is drawn so it never flashes. --}}
    <script>
        try { if (localStorage.getItem('pabarang.landingTheme') === 'sky') document.documentElement.setAttribute('data-theme', 'sky'); } catch (e) {}
    </script>
    <style>
        :root {
            /* ===== Tugon dark palette ===== */
            --ink:        #0B1530;   /* night navy - page background         */
            --ink-2:      #0F1C3B;   /* alternate sections                   */
            --ink-3:      #152447;   /* raised surfaces                      */
            --navy:       #1B2454;   /* borders / subtle fills               */
            --blue:       #3D4ED1;   /* primary action blue (deep)           */
            --blue-hi:    #4E60E8;   /* hover state                          */
            --blue-soft:  #8E9BFF;   /* light blue text on dark              */
            --gold:       #7FB2FF;
            --green:      #3FB37F;   /* third color: small highlights */   /* accent                               */
            --gold-hi:    #A9CCFF;
            --txt:        #E7EAF6;   /* main text on dark                    */
            --txt-dim:    #9AA3C7;   /* secondary text                       */
            --line:       rgba(255,255,255,.09);
            /* Horizon (hero): paddy fields at night */
            --field-1: #13283E; --field-2: #163049; --field-3: #1A3852; --field-4: #1E4058;
            --dike: rgba(232,184,78,.16); --tree: #091527; --lamp: #E8B84E; --road: #24384F;
        }

        html { scroll-behavior: smooth; }
        body { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif; background: var(--ink); color: var(--txt); }
        .font-display { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif; letter-spacing: -0.005em; }

        .fs-xs { font-size: .75rem; }
        .fs-2xs { font-size: .6875rem; }
        .fw-extrabold { font-weight: 800; }
        .rounded-xl { border-radius: .9rem !important; }
        .rounded-2xl { border-radius: 1.25rem !important; }
        .text-brand { color: var(--blue-soft) !important; }
        .text-gold { color: var(--gold) !important; }
        .bg-brand { background-color: var(--blue) !important; }
        .btn-brand { background: var(--blue); border-color: var(--blue); color: #fff; }
        .btn-brand:hover { background: var(--blue-hi); border-color: var(--blue-hi); color: #fff; }
        .btn-gold { background: var(--gold); border-color: var(--gold); color: #14183A; font-weight: 700; }
        .btn-gold:hover { background: var(--gold-hi); border-color: var(--gold-hi); color: #14183A; }
        .shadow-soft { box-shadow: 0 8px 30px rgba(0,0,0,.4); }
        .shadow-lift { box-shadow: 0 16px 40px rgba(0,0,0,.5); }
        .section-py { padding-top: 6rem; padding-bottom: 6rem; }

        .reveal { opacity: 0; transform: translateY(30px); transition: opacity .7s cubic-bezier(.22,.68,0,1), transform .7s cubic-bezier(.22,.68,0,1); }
        .reveal.in-view { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: .08s; }
        .reveal-delay-2 { transition-delay: .16s; }
        .reveal-delay-3 { transition-delay: .24s; }
        @media (prefers-reduced-motion: reduce) {
            .reveal { opacity: 1; transform: none; transition: none; }
            html { scroll-behavior: auto; }
        }

        /* ===== HERO ===== */
        .hero-bg {
            background:
                radial-gradient(900px 480px at 85% -10%, rgba(127,178,255,.14), transparent 60%),
                radial-gradient(760px 520px at -10% 110%, rgba(61,78,209,.45), transparent 55%),
                linear-gradient(160deg, #101740 0%, #0B1130 45%, #070B1D 100%),
                url('{{ asset('images/barangay.png') }}') center center / cover no-repeat;
            background-color: var(--ink);
        }
        .grid-overlay {
            background-image:
                linear-gradient(rgba(255,255,255,.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.045) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(ellipse 90% 80% at 50% 20%, black 40%, transparent 100%);
        }
        .text-gradient {
            background: linear-gradient(92deg, var(--gold) 10%, #DCEBFF 50%, var(--gold) 90%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(28px);} to { opacity: 1; transform: translateY(0);} }
        @keyframes floatY { 0%,100% { transform: translateY(0);} 50% { transform: translateY(-14px);} }
        @keyframes blob {
            0%,100% { transform: translate(0,0) scale(1); }
            33% { transform: translate(28px,-36px) scale(1.08); }
            66% { transform: translate(-20px,20px) scale(.94); }
        }
        @keyframes pulseRing {
            0% { box-shadow: 0 0 0 0 rgba(127,178,255,.4); }
            70% { box-shadow: 0 0 0 14px rgba(127,178,255,0); }
            100% { box-shadow: 0 0 0 0 rgba(127,178,255,0); }
        }
        .animate-float-slow { animation: floatY 7s ease-in-out infinite; }
        .animate-blob { animation: blob 14s ease-in-out infinite; }
        .animate-pulse-ring { animation: pulseRing 2.2s cubic-bezier(.4,0,.2,1) infinite; }

        /* ===== Navbar ===== */
        #navbar { transition: background-color .3s, box-shadow .3s; }
        /* Phone/tablet menu: the bar turns solid while the menu is open, so the links
           never sit on top of the hero text. The menu scrolls if the screen is short. */
        @media (max-width: 1199.98px) {
            #navbar.menu-open { background: rgba(7,11,29,.97); backdrop-filter: blur(14px); box-shadow: 0 12px 30px rgba(0,0,0,.45); border-bottom: 1px solid var(--line); }
            #navbar.menu-open .nav-link { color: var(--txt-dim) !important; }
            #navbar.menu-open .brand-text { color: #fff !important; }
            #navbar .navbar-collapse { max-height: calc(100vh - 76px); overflow-y: auto; }
            #navbar .navbar-collapse .btn { width: 100%; }
            #navbar .navbar-collapse { padding-bottom: .75rem; }

            /* Links line up with the buttons below them */
            #navbar .navbar-collapse .nav-link { padding-left: 0 !important; padding-right: 0 !important; padding-top: .6rem; padding-bottom: .6rem; }
            /* The underline sits under the word only, starting at its left edge */
            #navbar .navbar-collapse .nav-item { display: block; }
            #navbar .navbar-collapse .nav-link { display: inline-block; }
            #navbar .navbar-collapse .nav-link::after { left: 0; right: auto; }
            #navbar .navbar-collapse .nav-link:hover::after, #navbar .navbar-collapse .nav-link.active::after { width: 100%; }

            /* Buttons in a tidy block under a divider:
               [      Check Status      ]
               [ Register ] [  Login   ]
               Theme                (o)          */
            #navbar .nav-actions { display: grid !important; grid-template-columns: 1fr 1fr; gap: .6rem !important;
                border-top: 1px solid var(--line); padding-top: 1rem; margin-top: .75rem !important; }
            #navbar .nav-actions > .btn-nav-outline { grid-column: 1 / -1; order: 1; min-height: 46px; }
            #navbar .nav-actions > .register-link { order: 2; display: flex; align-items: center; justify-content: center;
                min-height: 46px; border-radius: .9rem; border: 1.5px solid rgba(255,255,255,.35); color: #fff !important; }
            #navbar .nav-actions > .btn-gold { order: 3; min-height: 46px; display: flex; align-items: center; justify-content: center; }
            #navbar .nav-actions > .theme-toggle { order: 4; grid-column: 1 / -1; justify-self: stretch; width: auto; border-radius: .9rem;
                display: flex; align-items: center; justify-content: space-between; padding: 0 .9rem; min-height: 46px; }
            #navbar .nav-actions > .theme-toggle::before { content: 'Theme: Dark'; font-size: .95rem; font-weight: 600; }
            [data-theme="sky"] #navbar .nav-actions > .theme-toggle::before { content: 'Theme: Light'; }
            #navbar .nav-actions > .theme-toggle i { position: static; }
            #navbar .nav-actions > .theme-toggle .icon-moon { display: none; }
            [data-theme="sky"] #navbar .nav-actions > .theme-toggle .icon-sun { display: none; }
            [data-theme="sky"] #navbar .nav-actions > .theme-toggle .icon-moon { display: inline; transform: none; opacity: 1; }
            [data-theme="sky"] #navbar .nav-actions > .register-link { border-color: rgba(14,42,71,.35); color: #0E2A47 !important; }
            [data-theme="sky"] #navbar.menu-open { background: rgba(214,234,248,.97); box-shadow: 0 12px 30px rgba(30,80,140,.18); }
            [data-theme="sky"] #navbar.menu-open .nav-link { color: #1E3F63 !important; }
            [data-theme="sky"] #navbar.menu-open .brand-text { color: #0E2A47 !important; }
        }
        #navbar.navbar-solid { background: rgba(7,11,29,.9); backdrop-filter: blur(14px); box-shadow: 0 8px 30px rgba(0,0,0,.4); border-bottom: 1px solid var(--line); }
        #navbar .nav-link { position: relative; color: rgba(231,234,246,.85) !important; font-weight: 600; }
        #navbar.navbar-solid .nav-link { color: var(--txt-dim) !important; }
        #navbar.navbar-solid .brand-text { color: #fff !important; }
        #navbar .brand-text { color: #fff; }
        .btn-nav-outline { border: 1.5px solid rgba(255,255,255,.35); color: #fff; font-weight: 600; font-size: .875rem; }
        .btn-nav-outline:hover { background: rgba(255,255,255,.1); border-color: #fff; color: #fff; }
        #navbar.navbar-solid .btn-nav-outline { border-color: rgba(142,155,255,.5); color: var(--blue-soft); }
        #navbar .register-link { color: rgba(231,234,246,.85); }
        #navbar.navbar-solid .register-link { color: var(--txt-dim); }
        #navbar .register-link:hover, #navbar.navbar-solid .register-link:hover { color: var(--gold); }
        .nav-link::after {
            content: ''; position: absolute; left: .5rem; right: .5rem; bottom: 0; height: 2px; width: 0;
            background: var(--gold); border-radius: 2px; transition: width .28s ease;
        }
        .nav-link:hover::after, .nav-link.active::after { width: calc(100% - 1rem); }

        /* ===== Form ===== */
        .field {
            border: 1.5px solid var(--line); border-radius: .75rem; padding: .65rem 1rem; font-size: .9rem;
            background: var(--ink-2); color: var(--txt);
        }
        .field::placeholder { color: #6B739A; }
        .field:focus { border-color: var(--blue); box-shadow: 0 0 0 .2rem rgba(61,78,209,.25); background: var(--ink-3); color: #fff; }

        .category-pill .btn-check:checked + .btn-pill {
            background: var(--blue); color: #fff; border-color: var(--blue);
            box-shadow: 0 6px 16px rgba(61,78,209,.4);
        }
        .btn-pill { border: 1px solid var(--line); border-radius: .9rem; font-size: .78rem; font-weight: 600; color: var(--txt-dim); padding: .6rem .5rem; width: 100%; background: var(--ink-2); }
        .btn-pill:hover { border-color: rgba(142,155,255,.5); color: var(--txt); }

        #progressBar { transform-origin: 0 50%; }
        ::selection { background: var(--gold); color: #14183A; }

        .icon-tile { width: 3rem; height: 3rem; border-radius: .9rem; display: flex; align-items: center; justify-content: center; }
        .hover-lift { transition: transform .3s, box-shadow .3s; }
        .hover-lift:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,0,0,.5); }
        .card-flat { border: 1px solid var(--line); background: var(--ink-2); border-radius: 1.25rem; }
        .card-flat:hover { border-color: rgba(142,155,255,.35); }

        .photo-tile { background: linear-gradient(135deg, #1B2454, #2A3575, var(--blue)); position: relative; overflow: hidden; color: #fff; }

        .carousel-track { transition: transform .5s cubic-bezier(.22,.68,0,1); }
        #officials-dots button { border: 0; height: .5rem; border-radius: 999px; background: #2A3368; transition: all .3s; }
        #officials-dots button.active { width: 1.75rem; background: var(--green); }
        .officials-nav-btn { width: 44px; height: 44px; border-radius: 50%; border: 1px solid var(--line); background: var(--ink-3); color: var(--blue-soft); }
        .officials-nav-btn:hover { background: var(--gold); color: #14183A; }

        .cta-banner { background: linear-gradient(135deg, #1B2454, var(--blue), #16204D); position: relative; overflow: hidden; border: 1px solid var(--line); }

        #scroll-top { width: 44px; height: 44px; border-radius: 50%; }

        .text-secondary-c { color: var(--txt-dim) !important; }

        /* ===================== THEME TOGGLE ===================== */
        .theme-toggle {
            width: 40px; height: 40px; border-radius: 50%; border: 1.5px solid rgba(255,255,255,.35);
            background: transparent; color: #fff; display: inline-flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden; font-size: 18px; flex-shrink: 0; transition: background-color .2s, border-color .2s;
        }
        .theme-toggle:hover { background: rgba(255,255,255,.1); }
        .theme-toggle i { position: absolute; transition: transform .5s cubic-bezier(.22,.68,0,1), opacity .35s; }
        .theme-toggle .icon-moon { transform: translateY(140%) rotate(-90deg); opacity: 0; }
        [data-theme="sky"] .theme-toggle .icon-sun { transform: translateY(-140%) rotate(90deg); opacity: 0; }
        [data-theme="sky"] .theme-toggle .icon-moon { transform: none; opacity: 1; }

        /* Smooth switch: the new theme spreads from the button (View Transitions),
           or fades on browsers without it. */
        ::view-transition-old(root), ::view-transition-new(root) { animation: none; mix-blend-mode: normal; }
        html.theme-fade, html.theme-fade * {
            transition: background-color .5s ease, color .5s ease, border-color .5s ease, fill .5s ease, box-shadow .5s ease !important;
        }

        /* Drifting clouds, shown only in the sky theme */
        .sky-clouds { display: none; position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
        .cloud {
            position: absolute; left: -260px; background: rgba(255,255,255,.78); border-radius: 999px;
            animation: cloudDrift linear infinite; filter: blur(.5px);
        }
        .cloud::before, .cloud::after { content: ''; position: absolute; background: inherit; border-radius: 50%; }
        .cloud::before { width: 46%; height: 150%; top: -72%; left: 16%; }
        .cloud::after { width: 34%; height: 115%; top: -52%; left: 52%; }
        .cloud.c1 { top: 16%; width: 190px; height: 56px; animation-duration: 75s; animation-delay: -10s; }
        .cloud.c2 { top: 34%; width: 130px; height: 40px; animation-duration: 95s; animation-delay: -55s; opacity: .7; }
        .cloud.c3 { top: 8%;  width: 240px; height: 70px; animation-duration: 120s; animation-delay: -80s; opacity: .85; }
        .cloud.c4 { top: 58%; width: 160px; height: 48px; animation-duration: 105s; animation-delay: -30s; opacity: .55; }
        @keyframes cloudDrift { from { transform: translateX(0); } to { transform: translateX(calc(100vw + 520px)); } }
        @media (prefers-reduced-motion: reduce) {
            .cloud { animation: none; }
            .cloud.c1 { left: 8%; } .cloud.c2 { left: 40%; } .cloud.c3 { left: 64%; } .cloud.c4 { left: 22%; }
        }

        /* ===================== SKY THEME =====================
           Soft sky blue, never pure white, so it stays easy on the eyes.
           Rounder cards and soft shadows; the dark theme stays flat and simple. */
        [data-theme="sky"] {
            --ink:       #C9E3F5;   /* page background (soft sky)   */
            --ink-2:     #D6EAF8;   /* alternate sections           */
            --ink-3:     #E3F1FB;   /* raised cards                 */
            --navy:      #B3D2EB;
            --blue:      #2563B8;
            --blue-hi:   #1E56A3;
            --blue-soft: #1F5FAF;   /* blue text on light           */
            --gold:      #2459C4;   /* deeper gold so text reads on sky */
            --gold-hi:   #3A70DD;
            --txt:       #0E2A47;   /* deep navy text               */
            --txt-dim:   #46607D;
            --line:      rgba(14,42,71,.13);
        }
        [data-theme="sky"] .text-white { color: var(--txt) !important; }
        [data-theme="sky"] .bg-brand.text-white,
        [data-theme="sky"] .photo-tile .text-white,
        [data-theme="sky"] [style*="linear-gradient(135deg,var(--blue)"].text-white { color: #fff !important; }
        [data-theme="sky"] .btn-gold { background: #2459C4; border-color: #2459C4; color: #fff; }
        [data-theme="sky"] .btn-gold:hover { background: #1D4BA8; border-color: #1D4BA8; color: #fff; }
        [data-theme="sky"] .shadow-soft { box-shadow: 0 10px 30px rgba(30,80,140,.14); }
        [data-theme="sky"] .shadow-lift, [data-theme="sky"] .hover-lift:hover { box-shadow: 0 18px 40px rgba(30,80,140,.18); }
        [data-theme="sky"] .text-gradient { background: linear-gradient(92deg, #1D4BA8 10%, #3D7BEA 50%, #1D4BA8 90%); -webkit-background-clip: text; background-clip: text; }
        [data-theme="sky"] ::selection { background: #2563B8; color: #fff; }

        /* Hero: morning sky with a soft sun and drifting clouds */
        [data-theme="sky"] .hero-bg {
            background:
                radial-gradient(520px 320px at 88% 6%, rgba(255,236,170,.8), transparent 62%),
                linear-gradient(180deg, #6FB8E8 0%, #9FD0F1 48%, #C9E3F5 100%);
        }
        [data-theme="sky"] #home > .grid-overlay, [data-theme="sky"] #home > .animate-blob { display: none; }
        [data-theme="sky"] .sky-clouds { display: block; }
        [data-theme="sky"] #home > svg { fill: var(--ink); }
        [data-theme="sky"] #home [style*="rgba(255,255,255"] { background-color: rgba(255,255,255,.5) !important; border-color: rgba(14,42,71,.15) !important; }

        /* Navbar */
        [data-theme="sky"] #navbar .nav-link, [data-theme="sky"] #navbar.navbar-solid .nav-link { color: #1E3F63 !important; }
        [data-theme="sky"] #navbar .brand-text, [data-theme="sky"] #navbar.navbar-solid .brand-text { color: #0E2A47 !important; }
        [data-theme="sky"] #navbar.navbar-solid { background: rgba(201,227,245,.88); box-shadow: 0 8px 30px rgba(30,80,140,.15); }
        [data-theme="sky"] .btn-nav-outline, [data-theme="sky"] #navbar.navbar-solid .btn-nav-outline { border-color: rgba(14,42,71,.35); color: #0E2A47; }
        [data-theme="sky"] .btn-nav-outline:hover { background: rgba(255,255,255,.45); border-color: #0E2A47; color: #0E2A47; }
        [data-theme="sky"] #navbar .register-link, [data-theme="sky"] #navbar.navbar-solid .register-link { color: #1E3F63; }
        [data-theme="sky"] .navbar-toggler-icon { filter: invert(1) brightness(.35); }
        [data-theme="sky"] .theme-toggle { border-color: rgba(14,42,71,.35); color: #0E2A47; }
        [data-theme="sky"] .theme-toggle:hover { background: rgba(255,255,255,.45); }

        /* Cards and form */
        [data-theme="sky"] .card-flat { background: rgba(227,241,251,.92); border-color: rgba(255,255,255,.75); border-radius: 1.5rem; box-shadow: 0 10px 30px rgba(30,80,140,.12); }
        [data-theme="sky"] .card-flat:hover { border-color: rgba(37,99,184,.35); }
        [data-theme="sky"] .field { background: #EDF6FD; color: var(--txt); border-color: rgba(14,42,71,.15); }
        [data-theme="sky"] .field::placeholder { color: #7590AC; }
        [data-theme="sky"] .field:focus { background: #F4FAFE; color: var(--txt); border-color: var(--blue); box-shadow: 0 0 0 .2rem rgba(37,99,184,.2); }
        [data-theme="sky"] select.field option { background: #EDF6FD !important; color: #0E2A47 !important; }
        [data-theme="sky"] .btn-pill { background: #E3F1FB; color: var(--txt-dim); }
        [data-theme="sky"] .btn-outline-light { color: var(--txt); border-color: rgba(14,42,71,.3); }
        [data-theme="sky"] .btn-outline-light:hover { background: rgba(255,255,255,.5); color: var(--txt); }
        [data-theme="sky"] #officials-dots button { background: #9CC3E3; }
        [data-theme="sky"] .photo-tile { background: linear-gradient(135deg, #2563B8, #3B82D6, #6FB8E8); }
        [data-theme="sky"] .cta-banner { background: linear-gradient(135deg, #1E56A3, #2563B8, #3B82D6); }

        /* Parts that stay deep blue in the sky theme (emergency box, banner, footer) */
        [data-theme="sky"] .keep-dark, [data-theme="sky"] .cta-banner, [data-theme="sky"] #contact-footer {
            --txt: #E7EAF6; --txt-dim: #BFD0E3; --line: rgba(255,255,255,.14); --gold: #A9CCFF;
        }
        [data-theme="sky"] .keep-dark { background: #0F3355 !important; }
        [data-theme="sky"] #contact-footer { background: #0F3355 !important; }

        /* ===================== REDESIGN (Nov 7) =====================
           One bold element: the paddy-field horizon under the hero. Everything
           else stays quiet: left-aligned headings, plain lists, no decoration. */
        body { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif; font-size: 1.0625rem; line-height: 1.6; }
        .font-display, h1, h2, h3 { font-family: 'Host Grotesk', 'Segoe UI', Arial, sans-serif; letter-spacing: -0.015em; }
        h2.font-display { font-size: clamp(1.75rem, 3vw, 2.25rem) !important; line-height: 1.15; }

        /* Sections appear as they are; only the hero has an entrance */
        .reveal { opacity: 1 !important; transform: none !important; transition: none !important; }
        .hover-lift:hover { transform: none; box-shadow: none; }
        [data-theme="sky"] .hover-lift:hover { box-shadow: 0 10px 30px rgba(30,80,140,.12); }

        /* Section headings: left-aligned, no eyebrow pill, no underline bar */
        .section-head { max-width: 40rem; text-align: left; }
        .section-head p { margin-left: 0 !important; }

        /* ---- Hero ---- */
        .hero-bg {
            background:
                radial-gradient(70% 38% at 50% 100%, rgba(232,184,78,.16), transparent 70%),
                linear-gradient(180deg, #0A1430 0%, #11214A 55%, #1F305A 82%, #26365F 100%);
            color: var(--txt);
        }
        .hero-inner { padding-top: 8.5rem; padding-bottom: 15rem; }
        .hero-place { font-weight: 700; color: var(--green); margin-bottom: 1rem; font-size: 1rem; }
        .hero-title {
            font-weight: 800; font-size: clamp(2.4rem, 5.2vw, 4.1rem); line-height: 1.04;
            letter-spacing: -0.03em; font-variation-settings: "opsz" 96; max-width: 14ch; margin-bottom: 1.25rem; color: var(--txt);
        }
        .hero-lede { color: var(--txt-dim); max-width: 34rem; font-size: 1.125rem; }
        .hero-note { color: var(--txt-dim); font-size: .95rem; margin-top: 1.25rem; }
        .hero-note a { color: var(--green); font-weight: 700; }
        .btn-hero { padding: .85rem 1.5rem; border-radius: .7rem; font-weight: 700; font-size: 1rem; }
        .btn-hero-ghost { padding: .85rem 1.5rem; border-radius: .7rem; font-weight: 700; font-size: 1rem; color: var(--txt); border: 1.5px solid var(--line); background: transparent; }
        .btn-hero-ghost:hover { border-color: var(--gold); color: var(--txt); }

        /* The horizon: tree line, coconut palms and paddy fields */
        .horizon { position: absolute; left: 0; right: 0; bottom: 0; width: 100%; height: 230px; display: block; }
        .horizon .f1 { fill: var(--field-1); } .horizon .f2 { fill: var(--field-2); }
        .horizon .f3 { fill: var(--field-3); } .horizon .f4 { fill: var(--field-4); }
        .horizon .hz-tree { fill: var(--tree); }
        .horizon .hz-palm { fill: none; stroke: var(--tree); stroke-width: 3; stroke-linecap: round; }
        .horizon .hz-dike { stroke: var(--dike); stroke-width: 1.5; vector-effect: non-scaling-stroke; }
        .horizon .hz-light { fill: var(--lamp); }
        .horizon .hz-road { fill: var(--road); }
        /* Clouds stay in the upper sky, away from the text */
        .sky-clouds { bottom: auto; height: 34%; }
        .cloud { opacity: .55 !important; }
        .cloud.c1 { top: 22%; } .cloud.c2 { top: 48%; } .cloud.c3 { top: 6%; } .cloud.c4 { display: none; }

        /* The barangay board: real notices from the office */
        .board { background: var(--ink-3); border: 1px solid var(--line); border-radius: 1rem; overflow: hidden; box-shadow: 0 24px 60px rgba(3,8,24,.45); }
        .board-head { display: flex; align-items: center; gap: .85rem; padding: 1rem 1.25rem; background: var(--gold); color: #14183A; }
        .board-head img { width: 2.75rem; height: 2.75rem; border-radius: 50%; object-fit: cover; border: 2px solid rgba(20,24,58,.25); background: #fff; }
        .board-title { font-family: 'Host Grotesk', sans-serif; font-weight: 800; font-size: 1.15rem; line-height: 1.1; }
        .board-sub { font-size: .85rem; opacity: .8; }
        .board-item { display: block; padding: 1rem 1.25rem; border-top: 1px dashed var(--line); color: var(--txt); text-decoration: none; }
        .board-item:first-of-type { border-top: 0; }
        .board-label { font-size: .85rem; color: var(--txt-dim); margin-bottom: .2rem; }
        .board-main { font-weight: 700; font-size: 1.05rem; line-height: 1.3; }
        .board-meta { font-size: .9rem; color: var(--txt-dim); margin-top: .15rem; }
        .board-event { display: flex; gap: 1rem; align-items: center; }
        .board-date { flex-shrink: 0; width: 3.5rem; text-align: center; border-radius: .6rem; border: 1.5px solid var(--green); padding: .3rem 0; line-height: 1.1; }
        .board-date b { display: block; font-family: 'Host Grotesk', sans-serif; font-size: 1.5rem; color: var(--txt); }
        .board-date span { font-size: .8rem; color: var(--green); font-weight: 700; }
        .board-sos { display: flex; justify-content: space-between; align-items: center; gap: 1rem; background: rgba(214,69,69,.14); }
        .board-sos:hover { background: rgba(214,69,69,.22); color: var(--txt); }
        .board-sos strong { font-family: 'Host Grotesk', sans-serif; font-size: 1.6rem; color: #F08A8A; }

        /* One entrance, on the hero only */
        @keyframes heroIn { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        @keyframes horizonIn { from { transform: translateY(26px); } to { transform: none; } }
        .hero-copy > * { animation: heroIn .7s cubic-bezier(.22,.68,0,1) both; }
        .hero-copy > :nth-child(2) { animation-delay: .06s; } .hero-copy > :nth-child(3) { animation-delay: .12s; }
        .hero-copy > :nth-child(4) { animation-delay: .18s; } .hero-copy > :nth-child(5) { animation-delay: .24s; }
        .board { animation: heroIn .8s .2s cubic-bezier(.22,.68,0,1) both; }
        .horizon { animation: horizonIn 1.1s cubic-bezier(.22,.68,0,1) both; }
        @media (prefers-reduced-motion: reduce) { .hero-copy > *, .board, .horizon { animation: none; } }
        @media (max-width: 991.98px) { .hero-inner { padding-top: 7rem; padding-bottom: 12rem; } .horizon { height: 170px; } }

        /* Services: a plain list in columns instead of a grid of cards */
        #services .card-flat { background: transparent; border: 0; border-top: 1px solid var(--line); border-radius: 0; box-shadow: none; padding-left: 0 !important; padding-right: 0 !important; }
        #services .card-flat.border-2 { border-top: 3px solid var(--green) !important; }
        #services .card-flat h3 { border-bottom: 0 !important; }
        [data-theme="sky"] #services .card-flat { background: transparent; border-top-color: rgba(14,42,71,.18); box-shadow: none; }

        /* Closing banner: solid, no gradient wash */
        .cta-banner { background: var(--ink-3); border: 1px solid var(--line); border-top: 4px solid var(--green); }
        .cta-banner > .animate-blob, .cta-banner .grid-overlay { display: none; }
        #contact-footer { background: #08112A !important; }

        /* ---- Sky theme: the same horizon in daylight ---- */
        [data-theme="sky"] {
            --field-1: #8EC47F; --field-2: #7DB86E; --field-3: #6DAA5F; --field-4: #609E53;
            --dike: #B6DA9E; --tree: #3F7B48; --lamp: transparent; --road: #CDB98A;
        }
        [data-theme="sky"] .hero-bg {
            background:
                radial-gradient(520px 320px at 86% 8%, rgba(255,236,170,.85), transparent 62%),
                linear-gradient(180deg, #6FB8E8 0%, #A3D2F2 55%, #CFE7F7 100%);
        }
        [data-theme="sky"] .hero-place { color: #1F4FB8; }
        [data-theme="sky"] .board { background: #E6F2FB; border-color: rgba(255,255,255,.8); box-shadow: 0 24px 50px rgba(30,80,140,.2); }
        [data-theme="sky"] { --green: #1F8A57; }
        [data-theme="sky"] .board-head { background: #2459C4; color: #fff; }
        [data-theme="sky"] .board-head img { border-color: rgba(255,255,255,.5); }
        [data-theme="sky"] .board-sos { background: rgba(214,69,69,.1); }
        [data-theme="sky"] .board-sos strong { color: #B83232; }
        [data-theme="sky"] .btn-hero-ghost { border-color: rgba(14,42,71,.3); }
        [data-theme="sky"] .cta-banner { background: #0F3355; }

        /* ===================== HERO PHOTO (Nov 7, semi-formal) =====================
           The real barangay in the background (public/images/barangay.png), under a
           tint that keeps the text readable. Replace that image to change the photo. */
        .hero-bg {
            background:
                linear-gradient(90deg, rgba(9,18,44,.94) 0%, rgba(9,18,44,.82) 42%, rgba(9,18,44,.5) 100%),
                url('{{ asset('images/barangay.png') }}') center 40% / cover no-repeat;
            background-color: var(--ink);
        }
        .hero-bg::after {   /* soft edge into the next section */
            content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 90px; pointer-events: none;
            background: linear-gradient(180deg, transparent, var(--ink));
        }
        [data-theme="sky"] .hero-bg {
            background:
                linear-gradient(90deg, rgba(214,234,248,.8) 0%, rgba(214,234,248,.55) 42%, rgba(214,234,248,.1) 100%),
                url('{{ asset('images/barangay.png') }}') center 40% / cover no-repeat;
            background-color: var(--ink);
        }
        .hero-inner { padding-top: 9rem; padding-bottom: 7.5rem; }
        /* Phones: the wide g-5 gutter pushes rows past the screen edge, so use a smaller one */
        @media (max-width: 575.98px) { .row.g-5, .row.gx-5 { --bs-gutter-x: 1.5rem; } }
        /* Long notice or event titles wrap inside the board instead of widening it */
        .board-main, .board-meta, .board-label { overflow-wrap: anywhere; }
        .board-event > div:last-child { min-width: 0; }
        /* A calmer, semi-formal headline */
        .hero-title { font-weight: 700; font-size: clamp(2.2rem, 4.4vw, 3.5rem); line-height: 1.1; letter-spacing: -0.02em; font-variation-settings: "opsz" 48; max-width: 16ch; }
        .hero-place { display: flex; align-items: center; gap: .6rem; }
        .hero-place img { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; background: #fff; }
        @media (max-width: 991.98px) { .hero-inner { padding-top: 7rem; padding-bottom: 5rem; } }

        /* ===================== COMPACT LANDING (Nov 8) =====================
           Feedback from mobile testing: shorter sections, less empty space,
           officials as a hierarchy chart, a simpler footer. */

        /* Steps strip under the hero */
        .steps-strip { padding: 1.5rem 0; border-bottom: 1px solid var(--line); }
        .steps-list { display: grid; gap: .85rem; }
        @media (min-width: 768px) { .steps-list { grid-template-columns: repeat(3, 1fr); gap: 1.5rem; } }
        .step-item { display: flex; gap: .75rem; align-items: flex-start; }
        .step-num { flex-shrink: 0; width: 1.9rem; height: 1.9rem; border-radius: .55rem; background: var(--blue); color: #fff;
            font-weight: 700; font-size: .9rem; display: flex; align-items: center; justify-content: center; }
        .step-title { font-weight: 700; color: var(--txt); line-height: 1.3; }
        .step-text { font-size: .9rem; color: var(--txt-dim); line-height: 1.4; }

        /* About */
        .about-photo { height: 14rem; object-fit: cover; border: 1px solid var(--line); }
        .reg-steps { display: grid; gap: .65rem; }
        .reg-steps li { display: flex; gap: .75rem; align-items: center; color: var(--txt-dim); }

        /* Services list */
        .svc-list { display: grid; grid-template-columns: 1fr; border-top: 1px solid var(--line); }
        @media (min-width: 768px) { .svc-list { grid-template-columns: 1fr 1fr; column-gap: 2.5rem; } }
        .svc-item { display: flex; gap: .85rem; align-items: center; padding: .85rem 0; border-bottom: 1px solid var(--line); color: var(--txt); text-decoration: none; }
        .svc-item:hover { color: var(--txt); }
        .svc-item:hover .svc-title { color: var(--gold); }
        .svc-icon { flex-shrink: 0; width: 2.5rem; height: 2.5rem; border-radius: .7rem; display: flex; align-items: center; justify-content: center;
            background: rgba(142,155,255,.13); color: var(--blue-soft); font-size: 1.2rem; }
        .svc-item.is-main .svc-icon { background: var(--green); color: #fff; }
        .svc-body { display: block; min-width: 0; }
        .svc-title { display: flex; align-items: center; gap: .4rem; font-weight: 700; line-height: 1.25; transition: color .2s; }
        .svc-desc { display: block; font-size: .875rem; color: var(--txt-dim); line-height: 1.35; margin-top: .1rem; }
        .svc-lock { font-size: .85rem; color: var(--txt-dim); }

        /* Officials hierarchy: captain on top, a line down to each group */
        .org { --org-line: rgba(142,155,255,.35); }
        [data-theme="sky"] .org { --org-line: rgba(37,99,184,.35); }
        .org-top { display: flex; justify-content: center; position: relative; }
        .org-top.has-below { padding-bottom: 1.5rem; }
        .org-top.has-below::after, .org-group + .org-group::before {
            content: ''; position: absolute; left: 50%; width: 2px; height: 1.5rem; margin-left: -1px; background: var(--org-line); }
        .org-top.has-below::after { bottom: 0; }
        .org-group + .org-group { margin-top: 1.5rem; }
        .org-group + .org-group::before { top: calc(-1.5rem - 1px); }
        .org-group { position: relative; border: 1px solid var(--line); border-radius: 1rem; padding: .9rem; background: var(--ink-2); }
        .org-label { font-weight: 700; font-size: .9rem; color: var(--txt-dim); text-align: center; margin-bottom: .75rem; }
        .org-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .6rem; }
        @media (min-width: 768px) { .org-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        .org-node { display: flex; flex-direction: column; align-items: center; text-align: center; gap: .35rem;
            padding: .8rem .5rem; border-radius: .8rem; background: var(--ink-3); min-width: 0; }
        .org-node-lead { flex-direction: row; text-align: left; gap: 1rem; padding: 1rem 1.25rem; width: 100%; max-width: 22rem;
            border: 1px solid var(--line); border-top: 3px solid var(--gold); }
        .org-photo { position: relative; width: 3.5rem; height: 3.5rem; border-radius: 50%; overflow: hidden; flex-shrink: 0;
            background: var(--navy); display: flex; align-items: center; justify-content: center; }
        .org-node-lead .org-photo { width: 4.5rem; height: 4.5rem; }
        .org-photo img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: top; }
        .org-initials { font-weight: 700; color: var(--blue-soft); }
        .org-name { font-weight: 700; font-size: .9rem; line-height: 1.25; color: var(--txt); overflow-wrap: anywhere; }
        .org-node-lead .org-name { font-size: 1.05rem; }
        .org-role { font-size: .8rem; color: var(--gold); line-height: 1.3; }
        .min-w-0 { min-width: 0; }

        /* Footer */
        .footer-main { padding-top: 2.5rem; padding-bottom: 1.75rem; }
        .footer-head { color: var(--txt); font-weight: 700; margin-bottom: .6rem; }
        .footer-links, .footer-contact { list-style: none; padding: 0; margin: 0; display: grid; gap: .5rem; font-size: .9rem; }
        .footer-links a, .footer-contact a { color: var(--txt-dim); text-decoration: none; }
        .footer-links a:hover, .footer-contact a:hover { color: var(--gold); }
        .footer-contact li { display: flex; gap: .5rem; align-items: flex-start; overflow-wrap: anywhere; min-width: 0; }
        .footer-contact i { color: var(--gold); margin-top: .2rem; }
        .footer-contact .sos i, .footer-contact .sos a { color: #F08A8A; font-weight: 700; }
        .footer-bottom { border-top: 1px solid var(--line); padding: .9rem 0; font-size: .78rem; color: var(--txt-dim); }

        /* Phones: calm spacing everywhere (Nov 8 feedback: "too stuffed") */
        @media (max-width: 575.98px) {
            body { line-height: 1.65; }
            .container { padding-left: 1.25rem; padding-right: 1.25rem; }
            .section-py { padding-top: 3.75rem; padding-bottom: 3.75rem; }
            h2.font-display { font-size: 1.65rem !important; line-height: 1.2; }
            .section-head { margin-bottom: 2rem !important; }
            .section-head h2 { margin-bottom: .6rem !important; }

            /* Hero: an even, darker tint so text reads over the photo */
            .hero-bg {
                background:
                    linear-gradient(180deg, rgba(9,18,44,.9) 0%, rgba(9,18,44,.84) 60%, rgba(9,18,44,.95) 100%),
                    url('{{ asset('images/barangay.png') }}') center 40% / cover no-repeat;
            }
            [data-theme="sky"] .hero-bg {
                background:
                    linear-gradient(180deg, rgba(214,234,248,.88) 0%, rgba(214,234,248,.8) 60%, rgba(201,227,245,.95) 100%),
                    url('{{ asset('images/barangay.png') }}') center 40% / cover no-repeat;
            }
            .hero-inner { padding-top: 7rem; padding-bottom: 3.5rem; }
            .hero-inner > .row { --bs-gutter-y: 3rem; }
            .hero-place { font-size: .85rem; margin-bottom: 1.1rem; line-height: 1.35; }
            .hero-place img { width: 26px; height: 26px; }
            .hero-title { font-size: 1.85rem; line-height: 1.18; margin-bottom: 1.1rem; }
            .hero-lede { font-size: 1rem; line-height: 1.65; }
            /* Buttons stack full width, so nothing runs off the screen */
            .hero-actions { display: grid !important; grid-template-columns: 1fr; gap: .75rem !important; margin-top: 1.75rem !important; }
            .btn-hero, .btn-hero-ghost { width: 100%; padding: .85rem 1rem; font-size: 1rem; }
            .hero-note { font-size: .875rem; margin-top: 1.25rem; line-height: 1.5; }

            /* Board */
            .board-head { padding: .9rem 1.15rem; }
            .board-head img { width: 2.4rem; height: 2.4rem; }
            .board-sub { display: none; }
            .board-item { padding: 1.1rem 1.15rem; }
            .board-label { margin-bottom: .35rem; }
            .board-main { font-size: 1rem; line-height: 1.4; }
            .board-meta { margin-top: .3rem; }
            .board-event { gap: 1rem; }
            .board-sos strong { font-size: 1.4rem; }

            /* Steps strip */
            .steps-strip { padding: 2.25rem 0; }
            .steps-list { gap: 1.35rem; }
            .step-text { margin-top: .15rem; }

            /* Report form */
            #report .row { --bs-gutter-y: 2rem; }
            #report form > * + * { margin-top: .25rem; }
            #report .form-label { margin-bottom: .5rem; }
            #report aside { position: static !important; gap: 1.25rem !important; }

            /* About */
            .about-photo { height: 11rem; margin-top: .5rem; }
            #about .row { --bs-gutter-y: 2.75rem; }
            .reg-steps { gap: .9rem; }

            /* Services */
            .svc-item { padding: 1.05rem 0; gap: 1rem; }
            .svc-desc { margin-top: .2rem; }

            /* Officials */
            .org-top.has-below { padding-bottom: 1.75rem; }
            .org-top.has-below::after { height: 1.75rem; }
            .org-group { padding: 1.1rem; }
            .org-label { margin-bottom: 1rem; }
            .org-grid { gap: .8rem; }
            .org-node { padding: 1rem .6rem; gap: .45rem; }
            .org-node-lead { padding: 1rem 1.15rem; }
            .org-node-lead .org-photo { width: 3.75rem; height: 3.75rem; }

            /* News */
            #news .row { --bs-gutter-y: 1.5rem; }

            /* Closing banner */
            .cta-banner { padding: 2.5rem 1.35rem !important; }
            .cta-banner h2 { margin-bottom: 1rem !important; }
            .cta-banner .btn { width: 100%; }

            /* Footer */
            .footer-main { padding-top: 2.75rem; padding-bottom: 2rem; }
            .footer-main .row { --bs-gutter-y: 2rem; }
            .footer-links, .footer-contact { gap: .7rem; }
            .footer-bottom { padding: 1.1rem 0; }
            .footer-bottom .container { flex-direction: column; gap: .35rem !important; }
        }

        /* ===================== Nov 8 (night): team status, transparency, location map ===================== */
        /* Barangay emergency team on the board */
        .board-sos .team-call { display: flex; flex-direction: column; align-items: flex-end; flex-shrink: 0; color: #F08A8A; }
        .board-sos .team-call i { font-size: 1.1rem; }
        .board-sos .team-call strong { font-size: 1.05rem; white-space: nowrap; }
        [data-theme="sky"] .board-sos .team-call { color: #B83232; }
        .team-dot { width: .6rem; height: .6rem; border-radius: 50%; flex-shrink: 0; background: var(--green); }
        .team-dot[data-status="responding"] { background: #5B8CFF; }
        .team-dot[data-status="off"] { background: #8A93B5; }

        /* Transparency */
        .tp-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0; margin: 0 0 1.5rem; border-top: 1px solid var(--line); }
        @media (min-width: 768px) { .tp-stats { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        .tp-stats > div { padding: 1rem 1rem 1rem 0; border-bottom: 1px solid var(--line); }
        .tp-stats dt { font-weight: 500; font-size: .9rem; color: var(--txt-dim); }
        .tp-stats dd { margin: .15rem 0 0; font-size: 1.75rem; font-weight: 700; color: var(--txt); line-height: 1.2; }
        .tp-note { display: block; font-size: .75rem; color: var(--txt-dim); margin-top: .15rem; }
        .tp-charts { display: grid; gap: 1rem; grid-template-columns: 1fr; }
        @media (min-width: 992px) { .tp-charts { grid-template-columns: 1fr 1.3fr 1.3fr; } }
        .tp-chart { margin: 0; padding: 1rem; border: 1px solid var(--line); border-radius: 1rem; background: var(--ink-2); }
        .tp-chart figcaption { font-weight: 700; color: var(--txt); margin-bottom: .75rem; }
        .tp-canvas { position: relative; height: 220px; }

        /* Location map in the report form */
        .location-map { height: 240px; border-radius: .75rem; overflow: hidden; border: 1px solid var(--line); }
        .location-map .leaflet-control-attribution { font-size: 9px; }

        @media (max-width: 575.98px) {
            .tp-stats > div { padding: .9rem .75rem .9rem 0; }
            .tp-stats dd { font-size: 1.45rem; }
            .tp-canvas { height: 200px; }
            .board-sos .team-call strong { font-size: .95rem; }
        }

        /* Officials chart: cards centered under the Captain, boxes only as wide as their cards */
        .org { display: flex; flex-direction: column; align-items: center; }
        .org-top { width: 100%; }
        .org-group { width: fit-content; max-width: 100%; margin-left: auto; margin-right: auto; padding: 1rem 1.25rem 1.25rem; }
        .org-grid { display: flex !important; flex-wrap: wrap; justify-content: center; gap: .75rem; }
        .org-grid > .org-node { flex: 0 0 13.5rem; }
        @media (max-width: 575.98px) {
            .org-group { width: 100%; padding: 1rem .75rem; }
            .org-grid > .org-node { flex: 0 0 calc(50% - .4rem); }
        }
    </style>
</head>
<body>

    @include('partials.page-loader')

    {{-- scroll progress --}}
    <div id="progressBar" class="position-fixed top-0 start-0 end-0" style="height:3px;background:linear-gradient(90deg,var(--gold),var(--gold-hi),var(--gold));z-index:1060;transform:scaleX(0);"></div>

    {{-- ============ NAVBAR ============ --}}
    <nav id="navbar" class="navbar navbar-expand-xl fixed-top py-3">
        <div class="container">
            <a href="#home" class="navbar-brand d-flex align-items-center gap-2 font-display fw-extrabold brand-text">
                <span class="d-inline-flex align-items-center justify-content-center rounded-3 overflow-hidden" style="width:2.25rem;height:2.25rem;transform:rotate(3deg);">
                    @include('partials.tugon-logo')
                </span>
                Tugon
            </a>

            <button class="navbar-toggler navbar-dark border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav mx-auto gap-xl-3 mt-3 mt-xl-0" id="navLinks">
                    <li class="nav-item"><a class="nav-link px-2" data-section="home" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link px-2" data-section="report" href="#report">Report a Concern</a></li>
                    <li class="nav-item"><a class="nav-link px-2" data-section="about" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link px-2" data-section="services" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link px-2" data-section="officials" href="#officials">Officials</a></li>
                    <li class="nav-item"><a class="nav-link px-2" data-section="news" href="#news">News</a></li>
                    <li class="nav-item"><a class="nav-link px-2" data-section="contact-footer" href="#contact-footer">Contact</a></li>
                </ul>

                <div class="nav-actions d-flex flex-column flex-xl-row align-items-xl-center gap-3 mt-3 mt-xl-0">
                    <button type="button" id="themeToggle" class="theme-toggle" aria-label="Switch to sky theme" title="Switch theme">
                        <i class="ti ti-sun icon-sun" aria-hidden="true"></i>
                        <i class="ti ti-moon icon-moon" aria-hidden="true"></i>
                    </button>
                    <a href="{{ url('/check-status') }}" class="btn btn-nav-outline rounded-xl px-3 d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="ti ti-file-search"></i> Check Status
                    </a>
                    <a href="{{ url('/register') }}" class="register-link text-decoration-none fw-semibold">Register</a>
                    <a href="{{ url('/login') }}" class="btn btn-gold rounded-xl px-4">Login</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- ============ HERO ============ --}}
    {{-- ============ HERO: Alejal's horizon + the barangay board ============ --}}
    @php
        // Real notices for the board; the page still loads if a table is missing.
        $boardNotice = null; $boardEvent = null;
        try { $boardNotice = \App\Models\Announcement::forDisplay()->first(); } catch (\Throwable $e) {}
        // Barangay emergency team: hotline plus the status officials set on the admin dashboard
        $team = \App\Support\EmergencyTeam::current();
        try {
            $boardEvent = \App\Models\Event::public()
                ->where(fn ($q) => $q->where('starts_at', '>=', now()->startOfDay())->orWhere('ends_at', '>=', now()->startOfDay()))
                ->orderBy('starts_at')->first();
        } catch (\Throwable $e) {}
    @endphp
    <section id="home" class="hero-bg position-relative overflow-hidden">
        <div class="container position-relative hero-inner">
            <div class="row align-items-center g-5">
                <div class="col-lg-7 hero-copy">
                    <p class="hero-place"><img src="{{ asset('images/logo.jpg') }}" alt="">Barangay Alejal, Carmen, Davao del Norte</p>
                    <h1 class="hero-title">Report it to the barangay. Follow it until it's fixed.</h1>
                    <p class="hero-lede">
                        Tugon is how Alejal residents send concerns, request documents and
                        hear barangay news, from any phone, any time.
                    </p>
                    <div class="hero-actions d-flex flex-wrap gap-3 mt-4">
                        <a href="#report" class="btn btn-gold btn-hero">Report a concern</a>
                        <a href="{{ url('/check-status') }}" class="btn btn-hero-ghost">Check a report</a>
                    </div>
                    <p class="hero-note">
                        No account needed to report. <a href="{{ url('/register') }}">Register</a> for documents.
                    </p>
                </div>

                <div class="col-lg-5">
                    <aside class="board" aria-label="Barangay board">
                        <div class="board-head">
                            <img src="{{ asset('images/logo.jpg') }}" alt="Barangay Alejal seal">
                            <div>
                                <div class="board-title">Barangay board</div>
                                <div class="board-sub">Posted by the barangay office</div>
                            </div>
                        </div>

                        <a href="#news" class="board-item">
                            <div class="board-label">Latest notice</div>
                            @if ($boardNotice)
                                <div class="board-main">{{ $boardNotice->title }}</div>
                                @if ($boardNotice->created_at)
                                    <div class="board-meta">Posted {{ $boardNotice->created_at->format('M j, Y') }}</div>
                                @endif
                            @else
                                <div class="board-main">No notices posted yet</div>
                                <div class="board-meta">New announcements from the barangay show up here.</div>
                            @endif
                        </a>

                        <div class="board-item board-event">
                            @if ($boardEvent)
                                <div class="board-date" aria-hidden="true">
                                    <span>{{ $boardEvent->starts_at->format('M') }}</span>
                                    <b>{{ $boardEvent->starts_at->format('j') }}</b>
                                </div>
                                <div>
                                    <div class="board-label">Next event</div>
                                    <div class="board-main">{{ $boardEvent->title }}</div>
                                    <div class="board-meta">
                                        {{ $boardEvent->all_day ? 'Whole day' : $boardEvent->starts_at->format('g:i A') }}@if ($boardEvent->location ?? null), {{ $boardEvent->location }}@endif
                                    </div>
                                </div>
                            @else
                                <div>
                                    <div class="board-label">Next event</div>
                                    <div class="board-main">Nothing scheduled yet</div>
                                    <div class="board-meta">Barangay activities appear here once they are set.</div>
                                </div>
                            @endif
                        </div>

                        <a href="tel:{{ \App\Support\EmergencyTeam::HOTLINE_TEL }}" class="board-item board-sos" aria-label="Call the barangay emergency team">
                            <span class="min-w-0">
                                <span class="board-main d-block">Barangay emergency team</span>
                                <span class="board-meta d-flex align-items-center gap-2 team-line">
                                    <span class="team-dot" data-team-dot data-status="{{ $team['status'] }}"></span>
                                    <span data-team-text>{{ $team['public'] }}</span>
                                </span>
                            </span>
                            <span class="team-call">
                                <i class="ti ti-phone-call" aria-hidden="true"></i>
                                <strong>{{ \App\Support\EmergencyTeam::HOTLINE }}</strong>
                            </span>
                        </a>
                    </aside>
                </div>
            </div>
        </div>


    </section>

    {{-- ============ HOW IT WORKS: one slim numbered strip ============ --}}
    <section class="steps-strip" style="background:var(--ink);">
        <div class="container">
            <ol class="steps-list list-unstyled mb-0">
                @foreach ([
                    ['t' => 'Send a report', 'd' => 'Pick a category, describe it and add a photo.'],
                    ['t' => 'Officials review it', 'd' => 'The barangay checks it and takes action.'],
                    ['t' => 'Track it', 'd' => 'Use your tracking code to see each update.'],
                ] as $i => $step)
                    <li class="step-item">
                        <span class="step-num">{{ $i + 1 }}</span>
                        <div>
                            <div class="step-title">{{ $step['t'] }}</div>
                            <div class="step-text">{{ $step['d'] }}</div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ============ REPORT A CONCERN (public, no login required) ============ --}}
    <section id="report" class="section-py position-relative overflow-hidden" style="background:var(--ink-2);">
        <div class="container position-relative">
            <div class="section-head mb-5">
                <h2 class="font-display fw-extrabold mb-2 text-white" style="font-size:2rem;">Report a Concern or Incident</h2>
                <p class="mx-auto" style="color:var(--txt-dim);max-width:36rem;">Fill out the form below and barangay officials will be notified. You'll get a tracking code to follow up.</p>
            </div>

            <div class="row g-4">
                {{-- form --}}
                <div class="col-lg-8">
                    <form action="{{ route('concerns.store') }}" method="POST" enctype="multipart/form-data" id="concernForm"
                          class="reveal card-flat shadow-soft p-4 p-sm-5 position-relative">
                        @csrf

                        {{-- Honeypot spam trap --}}
                        <input type="text" name="website" id="website" autocomplete="off" tabindex="-1"
                               class="position-absolute" style="left:-9999px;width:1px;height:1px;opacity:0;" aria-hidden="true">

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-white">Category</label>
                            <div class="row g-2 category-pill">
                                @foreach (['Crime/Safety' => '🛡', 'Environment' => '🌿', 'Infrastructure' => '🏗', 'Water Supply' => '💧', 'Streetlights' => '💡', 'Noise' => '🔇', 'Other' => '📌'] as $cat => $emoji)
                                    <div class="col-6 col-sm-3">
                                        <input type="radio" class="btn-check" name="category" id="cat-{{ Str::slug($cat) }}" value="{{ $cat }}" required autocomplete="off">
                                        <label class="btn btn-pill d-flex align-items-center justify-content-center gap-1 mb-0" for="cat-{{ Str::slug($cat) }}">
                                            {{ $emoji }} {{ $cat }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Shown only when Crime/Safety is selected --}}
                        <div id="hotlineWarning" class="d-none mb-4 alert alert-danger d-flex gap-2 rounded-3 mb-0">
                            <span class="fs-5">⚠️</span>
                            <span>If this is happening right now or you're in danger, call the barangay emergency team at <a href="tel:{{ \App\Support\EmergencyTeam::HOTLINE_TEL }}" class="alert-link">{{ \App\Support\EmergencyTeam::HOTLINE }}</a>, or <strong>911</strong>. This form notifies barangay officials but is not a substitute for emergency response.</span>
                        </div>

                        <div class="mb-4 mt-4">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-semibold mb-0 text-white" for="description">Description</label>
                                <span class="fs-xs" style="color:var(--txt-dim);"><span id="charCount">0</span>/1000</span>
                            </div>
                            <textarea id="description" name="description" rows="4" required maxlength="1000" placeholder="Describe what happened: what, where, when, who is involved…"
                                      class="form-control field"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-white" for="purok">Zone / Purok</label>
                            <select id="purok" name="purok" required class="form-select field">
                                <option value="" selected disabled style="background:var(--ink-2);">Select your zone/purok</option>
                                @foreach (['Purok 1','Purok 2','Purok 3','Purok 4','Purok 5'] as $p)
                                    <option style="background:var(--ink-2);color:var(--txt);">{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold text-white" for="reporterName">Full Name <span style="color:var(--txt-dim);font-weight:400;">(optional)</span></label>
                                <input type="text" id="reporterName" name="reporter_name" placeholder="Leave blank to hide your name" class="form-control field">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold text-white" for="contact">Email or Phone Number <span id="contactStar" style="color:#E57373;">*</span><span id="contactOptional" class="d-none" style="color:var(--txt-dim);font-weight:400;">(optional)</span></label>
                                <input type="text" id="contact" name="contact" autocomplete="off" placeholder="e.g. juan@gmail.com or 09171234567" class="form-control field">
                                <p id="contactHint" class="fs-xs mt-2 mb-0" style="color:var(--txt-dim);">Only barangay officials see this.</p>
                                <p id="contactError" class="d-none fs-xs mt-2 mb-0" style="color:#E57373;">Enter a valid email address or a Philippine mobile number (like 09171234567).</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-white">Incident Location</label>
                            <div class="border rounded-xl p-3 fs-sm" style="border-style: dashed !important; background:var(--ink-3);border-color:var(--line) !important;color:var(--txt-dim);">
                                <button type="button" id="useLocationBtn"
                                        class="btn btn-outline-light btn-sm rounded-3 fw-semibold mb-2">
                                    <i class="ti ti-map-pin"></i> Use my current location
                                </button>
                                <div id="locationText" class="fs-xs" aria-live="polite">No location captured yet.</div>
                                {{-- Shown after the location is found: drag the pin to the exact spot --}}
                                <div id="locationMap" class="d-none mt-3 location-map"></div>
                            </div>
                            <input type="hidden" name="latitude" id="latitudeInput">
                            <input type="hidden" name="longitude" id="longitudeInput">
                            <input type="hidden" name="location_accuracy" id="accuracyInput">
                            <input type="hidden" name="location_adjusted" id="adjustedInput" value="0">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-white" for="evidenceDrop">Photo Evidence <span style="color:#E57373;">*</span> <span style="color:var(--txt-dim);font-weight:400;">(taken with your camera, max 5MB)</span></label>
                            <button type="button" id="evidenceDrop" class="d-flex align-items-center gap-3 rounded-xl px-3 py-3 w-100 text-start" style="border:2px dashed var(--line);background:var(--ink-3);cursor:pointer;">
                                <i class="ti ti-camera fs-4" style="color:var(--txt-dim);"></i>
                                <span id="fileLabel" class="fs-sm" style="color:var(--txt-dim);">Tap to capture a photo</span>
                            </button>
                            <input type="file" id="evidenceUpload" name="evidence" accept="image/jpeg,image/png,image/webp" capture="environment"
                                   class="position-absolute opacity-0" style="width:1px;height:1px;pointer-events:none;" tabindex="-1">
                            <div id="cameraPanel" class="d-none rounded-xl overflow-hidden mt-3" style="background:#000;border:1px solid var(--line);">
                                <video id="cameraVideo" autoplay playsinline muted class="w-100 d-block" style="max-height:22rem;object-fit:cover;"></video>
                                <div class="d-flex gap-2 justify-content-center p-2" style="background:var(--ink-3);">
                                    <button type="button" id="shutterBtn" class="btn btn-gold rounded-xl px-4 d-inline-flex align-items-center gap-2">
                                        <i class="ti ti-camera"></i> Capture photo
                                    </button>
                                    <button type="button" id="cameraSwitchBtn" class="btn btn-outline-light rounded-xl px-3 d-none d-inline-flex align-items-center gap-1" title="Switch between the front and back camera">
                                        <i class="ti ti-camera-rotate"></i> Switch
                                    </button>
                                    <button type="button" id="cameraFlipBtn" class="btn btn-outline-light rounded-xl px-3 d-inline-flex align-items-center gap-1" title="Flip the image left to right">
                                        <i class="ti ti-flip-horizontal"></i> Flip
                                    </button>
                                    <button type="button" id="cameraCancelBtn" class="btn btn-outline-light rounded-xl px-3">Cancel</button>
                                </div>
                            </div>
                            <canvas id="cameraCanvas" class="d-none"></canvas>
                            <p id="cameraNote" class="d-none fs-xs mt-2 mb-0" style="color:#E57373;"></p>
                            <p id="fileError" class="d-none fs-xs mt-2 mb-0" style="color:#E57373;">That photo is too large (over 5MB). Please retake it.</p>
                            <p id="fileTypeError" class="d-none fs-xs mt-2 mb-0" style="color:#E57373;">The photo must be a JPG, PNG or WebP image. Please retake it.</p>
                            <p id="fileMissing" class="d-none fs-xs mt-2 mb-0" style="color:#E57373;">A photo is required. Please capture a photo of the concern.</p>
                            <img id="idPreviewImg" alt="Evidence preview" class="d-none rounded-xl mt-3 shadow-soft" style="max-height:11rem;">
                        </div>

                        <div class="mb-4">
                            <label class="d-flex align-items-start gap-2 rounded-xl px-3 py-3 mb-0" style="cursor:pointer;background:var(--ink-3);border:1px solid var(--line);">
                                <input type="checkbox" id="dataConsent" name="data_consent" required class="form-check-input mt-1">
                                <span class="fs-xs" style="color:var(--txt-dim);">
                                    I agree to the processing of this information by Barangay Alejal under the
                                    <a href="https://privacy.gov.ph/data-privacy-act/" target="_blank" class="text-gold fw-semibold">Data Privacy Act (RA 10173)</a>.
                                    @include('partials.photo-check-consent')
                                </span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-gold w-100 rounded-xl py-3 font-display fw-bold">
                            Submit Concern
                        </button>
                        <p class="fs-xs mt-3 text-center mb-0" style="color:var(--txt-dim);">
                            Want to track your report's status? <a href="{{ url('/register') }}" class="text-gold fw-semibold">Create a free account</a> (optional).
                        </p>
                    </form>

                    {{-- Shown after a successful submit --}}
                    <div id="trackingConfirm" class="d-none mx-auto mt-4 card-flat shadow-soft border-top border-4 p-4 p-sm-5 text-center" style="max-width:32rem;border-top-color:var(--gold) !important;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:3.5rem;height:3.5rem;background:rgba(127,178,255,.15);color:var(--gold);">
                            <i class="ti ti-check fs-3"></i>
                        </div>
                        <div class="font-display fw-bold fs-4 mb-1 text-white">Concern Submitted</div>
                        <p class="fs-sm mb-3" style="color:var(--txt-dim);">Save this tracking code. You'll need it to check your report's status later.</p>
                        <div id="trackingCodeText" class="d-inline-block rounded-xl px-4 py-2 font-monospace fw-bold text-gold" style="background:var(--ink-3);border:2px dashed var(--gold);letter-spacing:.1em;"></div>
                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                            <button type="button" id="copyCodeBtn" class="btn btn-sm btn-outline-light rounded-xl">
                                <i class="ti ti-copy"></i> <span>Copy code</span>
                            </button>
                            <button type="button" id="copyLinkBtn" class="btn btn-sm btn-outline-light rounded-xl">
                                <i class="ti ti-link"></i> <span>Copy link</span>
                            </button>
                        </div>
                        <p class="fs-xs mt-3 mb-3" style="color:var(--txt-dim);">
                            Copy the link to bookmark it, or send it to yourself on Messenger or Viber.
                        </p>
                        <a id="trackLink" href="{{ url('/check-status') }}" class="btn btn-brand btn-sm rounded-xl px-4">Check status now</a>
                        <p class="fs-xs mt-3 mb-0" style="color:var(--txt-dim);">
                            This report is also saved on this device. Open <a href="{{ url('/check-status') }}" class="text-gold fw-semibold">Check Status</a> any time to see it.
                        </p>
                    </div>
                </div>

                {{-- side panel --}}
                <div class="col-lg-4">
                    <aside class="reveal reveal-delay-1 d-flex flex-column gap-3" style="position:sticky;top:6rem;">
                        <div class="card-flat text-white rounded-2xl p-4 shadow-soft keep-dark" style="background:#0A0F28;">
                            <div class="text-gold font-display fw-bold mb-2">Emergency?</div>
                            <p class="fs-sm mb-3" style="color:var(--txt-dim);">For crimes in progress or life-threatening situations, don't wait for the form.</p>
                            <div class="d-flex flex-column gap-2">
                                <a href="tel:{{ \App\Support\EmergencyTeam::HOTLINE_TEL }}" class="d-flex align-items-center justify-content-between gap-2 rounded-xl px-3 py-2 text-decoration-none text-white" style="background:rgba(255,255,255,.12);">
                                    <span class="fw-bold">{{ \App\Support\EmergencyTeam::HOTLINE }}</span>
                                    <span class="fs-xs text-end" style="color:var(--txt-dim);">Barangay team<br><span data-team-text>{{ $team['public'] }}</span></span>
                                </a>
                                <a href="tel:911" class="d-flex align-items-center justify-content-between rounded-xl px-3 py-2 text-decoration-none text-white" style="background:rgba(255,255,255,.07);">
                                    <span class="fw-bold">911</span><span class="fs-xs" style="color:var(--txt-dim);">National Emergency</span>
                                </a>
                                <a href="tel:117" class="d-flex align-items-center justify-content-between rounded-xl px-3 py-2 text-decoration-none text-white" style="background:rgba(255,255,255,.07);">
                                    <span class="fw-bold">117</span><span class="fs-xs" style="color:var(--txt-dim);">PNP Hotline</span>
                                </a>
                            </div>
                        </div>

                        <div class="card-flat rounded-2xl p-4 shadow-soft">
                            <div class="font-display fw-bold mb-2 text-white">What happens next?</div>
                            <ol class="list-unstyled fs-sm d-flex flex-column gap-2 mb-0" style="color:var(--txt-dim);">
                                <li class="d-flex gap-2"><span class="flex-shrink-0 rounded-circle fw-bold d-flex align-items-center justify-content-center fs-2xs" style="width:1.25rem;height:1.25rem;background:rgba(142,155,255,.15);color:var(--blue-soft);">1</span>Your concern is queued for review by barangay officials.</li>
                                <li class="d-flex gap-2"><span class="flex-shrink-0 rounded-circle fw-bold d-flex align-items-center justify-content-center fs-2xs" style="width:1.25rem;height:1.25rem;background:rgba(142,155,255,.15);color:var(--blue-soft);">2</span>You receive a tracking code (shown after submit).</li>
                                <li class="d-flex gap-2"><span class="flex-shrink-0 rounded-circle fw-bold d-flex align-items-center justify-content-center fs-2xs" style="width:1.25rem;height:1.25rem;background:rgba(142,155,255,.15);color:var(--blue-soft);">3</span>Check status anytime. No login needed for the tracking code.</li>
                            </ol>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ ABOUT: short intro + 3 account steps + video ============ --}}
    <section id="about" class="section-py" style="background:var(--ink);">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-6">
                    <h2 class="font-display fw-bold mb-3 text-white">About Tugon</h2>
                    <p class="mb-2" style="color:var(--txt-dim);">
                        <strong class="text-white">Tugon</strong> means "response" in Filipino. It gives Alejal
                        residents a direct way to report concerns to the barangay and follow them until they
                        are resolved. You can also request documents and read barangay news without lining up at the hall.
                    </p>
                    <p class="fs-sm mb-3" style="color:var(--txt-dim);">Built in support of SDG 16: Peace, Justice and Strong Institutions.</p>
                    <img src="{{ asset('images/community1.jpg') }}" alt="Barangay Alejal community"
                         class="about-photo rounded-2xl w-100" loading="lazy">
                </div>

                <div class="col-lg-6">
                    <h3 class="font-display fw-bold fs-5 mb-1 text-white">How to get an account</h3>
                    <p class="fs-sm mb-3" style="color:var(--txt-dim);">Only needed for document requests and following all your reports.</p>

                    <ol class="reg-steps list-unstyled mb-4">
                        <li><span class="step-num">1</span><span>Tap <a href="{{ url('/register') }}" class="text-gold fw-semibold">Register</a> and fill out the form.</span></li>
                        <li><span class="step-num">2</span><span>The barangay checks and approves your account.</span></li>
                        <li><span class="step-num">3</span><span>You get an email, then you can log in.</span></li>
                    </ol>

                    {{-- Walkthrough video --}}
                    <div class="ratio ratio-16x9 rounded-xl overflow-hidden" style="border:1px solid var(--line);">
                        <iframe src="https://www.youtube.com/embed/__T5GuTENL4" title="Tugon walkthrough" loading="lazy" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <a href="https://www.youtube.com/watch?v=__T5GuTENL4" target="_blank" rel="noopener" class="fs-sm mt-2 d-inline-block" style="color:var(--txt-dim);text-decoration:underline;">
                        Watch the walkthrough on YouTube
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ SERVICES: a short tap list, one line each ============ --}}
    <section id="services" class="section-py" style="background:var(--ink-2);">
        <div class="container">
            <div class="section-head mb-4">
                <h2 class="font-display fw-bold mb-2 text-white">Services</h2>
                <p class="mb-0" style="color:var(--txt-dim);">Reporting is open to everyone. Documents and blotter need an account.</p>
            </div>

            @php
                $services = [
                    ['title' => 'Report a concern', 'desc' => 'Crimes and community issues. No account needed.', 'link' => '#report', 'icon' => 'ti-alert-triangle', 'login' => false],
                    ['title' => 'Barangay Clearance', 'desc' => 'Often needed for jobs and permits.', 'link' => '/request-document', 'icon' => 'ti-certificate', 'login' => true],
                    ['title' => 'Certificate of Residency', 'desc' => 'Proof that you live in Alejal.', 'link' => '/request-document', 'icon' => 'ti-home', 'login' => true],
                    ['title' => 'Certificate of Indigency', 'desc' => 'For aid, scholarships or medical help.', 'link' => '/request-document', 'icon' => 'ti-heart-handshake', 'login' => true],
                    ['title' => 'Business Clearance', 'desc' => 'To run a business in the barangay.', 'link' => '/request-document', 'icon' => 'ti-building-store', 'login' => true],
                    ['title' => 'Blotter report', 'desc' => 'Put a complaint or incident on record.', 'link' => '/blotter-report', 'icon' => 'ti-clipboard-text', 'login' => true],
                ];
            @endphp

            <div class="svc-list">
                @foreach ($services as $i => $service)
                    <a href="{{ str_starts_with($service['link'], '#') ? $service['link'] : url($service['link']) }}" class="svc-item {{ $i === 0 ? 'is-main' : '' }}">
                        <span class="svc-icon"><i class="ti {{ $service['icon'] }}" aria-hidden="true"></i></span>
                        <span class="svc-body">
                            <span class="svc-title">
                                {{ $service['title'] }}
                                @if ($service['login'])
                                    <i class="ti ti-lock svc-lock" title="Account needed" aria-label="Account needed"></i>
                                @endif
                            </span>
                            <span class="svc-desc">{{ $service['desc'] }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ TRANSPARENCY: barangay-wide totals (moved here from the resident pages) ============ --}}
    @php
        // Totals only, never single reports. The page still loads if the table is missing.
        $tp = null;
        try { $tp = \App\Support\TransparencyStats::get(); } catch (\Throwable $e) {}
    @endphp
    <section id="transparency" class="section-py" style="background:var(--ink);">
        <div class="container">
            <div class="section-head mb-4">
                <h2 class="font-display fw-bold mb-2 text-white">Transparency</h2>
                <p class="mb-0" style="color:var(--txt-dim);">How the barangay is handling reports sent through Tugon. Totals only, with no names or details.</p>
            </div>

            @if (! $tp || $tp['total'] === 0)
                <p class="mb-0" style="color:var(--txt-dim);">No reports yet. Totals will appear here once residents start sending concerns.</p>
            @else
                <dl class="tp-stats">
                    <div><dt>Reports received</dt><dd>{{ number_format($tp['total']) }}</dd></div>
                    <div><dt>Resolved</dt><dd>{{ number_format($tp['resolved']) }}</dd></div>
                    <div><dt>Resolution rate</dt><dd>{{ $tp['rate'] }}%</dd></div>
                    <div>
                        <dt>Average time to resolve</dt>
                        <dd>{{ $tp['showAvg'] ? $tp['avgDays'] . ' days' : 'Not yet' }}</dd>
                        @unless ($tp['showAvg'])<span class="tp-note">Shown after {{ \App\Support\TransparencyStats::MIN_FOR_AVERAGE }} resolved reports</span>@endunless
                    </div>
                </dl>

                <div class="tp-charts">
                    <figure class="tp-chart">
                        <figcaption>By status</figcaption>
                        <div class="tp-canvas tp-canvas-round"><canvas id="tpStatus" aria-label="Reports by status"></canvas></div>
                    </figure>
                    <figure class="tp-chart">
                        <figcaption>By category</figcaption>
                        <div class="tp-canvas"><canvas id="tpCategory" aria-label="Reports by category"></canvas></div>
                    </figure>
                    <figure class="tp-chart">
                        <figcaption>By purok</figcaption>
                        <div class="tp-canvas"><canvas id="tpPurok" aria-label="Reports by purok"></canvas></div>
                    </figure>
                </div>
                <p class="fs-xs mt-3 mb-0" style="color:var(--txt-dim);">Figures as of {{ $tp['asOf'] }}</p>
            @endif
        </div>
    </section>

    {{-- ============ OFFICIALS: hierarchy chart ============ --}}
    @php
        // From the Officials page (admin). Barangay Council only, in the roster order.
        $officials = collect();
        try { $officials = \App\Models\Official::where('group', 'council')->ordered()->get(); } catch (\Throwable $e) {}

        // Captain on top. If nobody is marked Punong Barangay, the first in the roster leads.
        $captain = $officials->first(fn ($o) => $o->position === 'Punong Barangay') ?? $officials->first();
        $rest = $captain ? $officials->reject(fn ($o) => $o->is($captain))->values() : collect();

        // Kagawads in one group, everyone else (SK Chair, Secretary, Treasurer) in the next
        $isKagawad = fn ($o) => str_contains(strtolower($o->position ?? ''), 'kagawad') || str_contains(strtolower($o->position ?? ''), 'councilor');
        $kagawads = $rest->filter($isKagawad)->values();
        $others = $rest->reject($isKagawad)->values();

        $orgGroups = [];
        if ($kagawads->isNotEmpty()) $orgGroups[] = ['label' => 'Barangay Kagawad', 'people' => $kagawads];
        if ($others->isNotEmpty()) $orgGroups[] = ['label' => $kagawads->isNotEmpty() ? 'Other officials' : 'Council members', 'people' => $others];

        // Two letters shown when an official has no photo
        $initials = fn ($name) => collect(preg_split('/\s+/', trim(preg_replace('/^(Hon\.?|Kgd\.?)\s+/i', '', (string) $name))))
            ->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
        $roleName = fn ($o) => $o->position === 'Punong Barangay' ? 'Barangay Captain' : $o->position;
    @endphp
    <section id="officials" class="section-py" style="background:var(--ink);">
        <div class="container">
            <div class="section-head mb-4">
                <h2 class="font-display fw-bold mb-2 text-white">Barangay Officials</h2>
                <p class="mb-0" style="color:var(--txt-dim);">The Barangay Council of Alejal.</p>
            </div>

            @if ($officials->isEmpty())
                <p class="mb-0" style="color:var(--txt-dim);">The list of officials will appear here soon.</p>
            @else
                <div class="org">
                    <div class="org-top {{ count($orgGroups) ? 'has-below' : '' }}">
                        <div class="org-node org-node-lead">
                            <span class="org-photo">
                                <span class="org-initials">{{ $initials($captain->name) }}</span>
                                @if ($captain->photoUrl())
                                    <img src="{{ $captain->photoUrl() }}" alt="{{ $captain->name }}" loading="lazy" onerror="this.remove()">
                                @endif
                            </span>
                            <div class="min-w-0">
                                <div class="org-name">{{ $captain->name }}</div>
                                <div class="org-role">{{ $roleName($captain) }}</div>
                            </div>
                        </div>
                    </div>

                    @foreach ($orgGroups as $group)
                        <div class="org-group">
                            <div class="org-label">{{ $group['label'] }}</div>
                            <div class="org-grid">
                                @foreach ($group['people'] as $person)
                                    <div class="org-node">
                                        <span class="org-photo">
                                            <span class="org-initials">{{ $initials($person->name) }}</span>
                                            @if ($person->photoUrl())
                                                <img src="{{ $person->photoUrl() }}" alt="{{ $person->name }}" loading="lazy" onerror="this.remove()">
                                            @endif
                                        </span>
                                        <div class="org-name">{{ $person->name }}</div>
                                        <div class="org-role">{{ $roleName($person) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ============ NEWS & ANNOUNCEMENTS ============ --}}
    <section id="news" class="section-py" style="background:var(--ink-2);">
        <div class="container">
            <div class="section-head mb-5">
                <h2 class="font-display fw-extrabold mb-2 text-white" style="font-size:2rem;">Recent News &amp; Announcements</h2>
                <p class="mb-0" style="color:var(--txt-dim);">Keep up to date with the latest news and announcements from Barangay Alejal.</p>
            </div>

            @php
                $news = [
                    ['tag' => 'Safety', 'title' => 'Report Incidents Faster With Tugon', 'date' => 'Sep 12, 2026', 'image' => 'fast.png'],
                    ['tag' => 'Community', 'title' => 'Meeting of Honor', 'date' => 'Sep 05, 2026', 'image' => 'meeting of honor.jpg'],
                    ['tag' => 'Notice', 'title' => 'Barangay Assembly Schedule', 'date' => 'Aug 28, 2026', 'image' => 'assembly.jpg'],
                ];
            @endphp

            <div class="row g-4">
                @foreach ($news as $i => $item)
                    <div class="col-sm-6 col-lg-4">
                        <a href="{{ url('/announcements') }}" class="reveal {{ $i === 1 ? 'reveal-delay-1' : ($i === 2 ? 'reveal-delay-2' : '') }} d-block text-decoration-none card-flat shadow-soft hover-lift overflow-hidden h-100">
                            @if (!empty($item['image']))
                                <img src="{{ asset('images/' . rawurlencode($item['image'])) }}" alt="{{ $item['title'] }}"
                                     class="w-100 d-block" style="height:11rem;object-fit:cover;">
                            @else
                                <div class="photo-tile position-relative" style="height:11rem;">
                                    <div class="position-absolute top-0 start-0 w-100 h-100 grid-overlay" style="opacity:.5;"></div>
                                    <div class="position-absolute top-50 start-50 translate-middle rounded-circle d-flex align-items-center justify-content-center" style="width:3rem;height:3rem;background:rgba(255,255,255,.12);backdrop-filter:blur(4px);border:1px solid var(--line);">
                                        <i class="ti ti-news fs-5 text-white"></i>
                                    </div>
                                </div>
                            @endif
                            <div class="p-4" style="background:var(--ink-3);">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="rounded-pill fw-bold fs-xs px-2 py-1" style="background:rgba(127,178,255,.15);color:var(--gold);letter-spacing:.05em;">{{ $item['tag'] }}</span>
                                    <span class="fs-xs" style="color:var(--txt-dim);">{{ $item['date'] }}</span>
                                </div>
                                <div class="font-display fw-bold text-white" style="line-height:1.3;">{{ $item['title'] }}</div>
                                <span class="d-inline-flex align-items-center gap-1 text-brand fs-sm fw-semibold mt-3">Read more</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ REGISTER CTA ============ --}}
    <section class="container section-py" style="background:var(--ink);">
        <div class="reveal cta-banner text-white rounded-2xl px-4 px-sm-5 py-5 text-center shadow-lift">
            <div class="position-absolute top-0 start-0 w-100 h-100 grid-overlay" style="opacity:.7;"></div>
            <div class="position-absolute rounded-circle animate-blob" style="top:-4rem;right:-4rem;width:16rem;height:16rem;background:rgba(127,178,255,.2);filter:blur(40px);"></div>
            <div class="position-relative">
                <h2 class="font-display fw-extrabold mb-3 text-white" style="font-size:2rem;">Ready to get started?</h2>
                <p class="mx-auto mb-4" style="color:rgba(255,255,255,.75);max-width:36rem;">
                    Create your resident account on Tugon to report concerns and
                    incidents, file blotter reports, request documents, and receive
                    barangay announcements online.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ url('/register') }}" class="btn btn-gold rounded-xl px-4 py-3 d-inline-flex align-items-center gap-2">
                        Create Resident Account
                    </a>
                    <a href="#report" class="btn rounded-xl px-4 py-3 fw-semibold text-white" style="border:1px solid rgba(255,255,255,.3);">
                        Just report anonymously
                    </a>
                </div>
                <p class="fs-xs mt-3 mb-0" style="color:rgba(255,255,255,.5);">Your account will require admin approval before you can log in.</p>
            </div>
        </div>
    </section>

    {{-- ============ FOOTER ============ --}}
    <footer id="contact-footer" style="color:var(--txt-dim);">
        <div class="container footer-main">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="d-flex align-items-center gap-2 text-white font-display fw-bold fs-5 mb-2">
                        <span class="rounded-3 d-flex align-items-center justify-content-center overflow-hidden" style="width:2rem;height:2rem;">
                            @include('partials.tugon-logo')
                        </span>
                        Tugon
                    </div>
                    <p class="fs-sm mb-0" style="max-width:24rem;">
                        Barangay Alejal's portal for reporting concerns, requesting documents and reading barangay news.
                    </p>
                </div>

                <div class="col-5 col-lg-3">
                    <div class="footer-head">Explore</div>
                    <ul class="footer-links">
                        <li><a href="#report">Report a concern</a></li>
                        <li><a href="{{ url('/check-status') }}">Check status</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#transparency">Transparency</a></li>
                        <li><a href="#officials">Officials</a></li>
                        <li><a href="#news">News</a></li>
                    </ul>
                </div>

                <div class="col-7 col-lg-4">
                    <div class="footer-head">Contact</div>
                    <ul class="footer-contact">
                        <li><i class="ti ti-map-pin" aria-hidden="true"></i><span>Barangay Alejal Hall, Carmen, Davao del Norte</span></li>
                        <li><i class="ti ti-phone" aria-hidden="true"></i><a href="tel:+639000000000">+63 900 000 0000</a></li>
                        <li><i class="ti ti-mail" aria-hidden="true"></i><a href="mailto:barangayalejal@example.gov.ph">barangayalejal@example.gov.ph</a></li>
                        <li class="sos"><i class="ti ti-phone-call" aria-hidden="true"></i><a href="tel:{{ \App\Support\EmergencyTeam::HOTLINE_TEL }}">Emergency team: {{ \App\Support\EmergencyTeam::HOTLINE }}</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container d-flex flex-wrap justify-content-between gap-1">
                <span>© {{ date('Y') }} Tugon, Barangay Alejal</span>
                <span>SDG 16: Peace, Justice and Strong Institutions</span>
            </div>
        </div>
    </footer>

    <button id="scroll-top" class="btn btn-brand position-fixed d-none align-items-center justify-content-center" style="bottom:1.5rem;right:1.5rem;z-index:1040;">
        <i class="ti ti-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @include('partials.location-picker-script')

    <script>
        // Emergency team status: checked every minute, so residents see when the team heads out
        (function () {
            function refresh() {
                fetch("{{ route('emergency-team.status') }}", { headers: { 'Accept': 'application/json' }, cache: 'no-store' })
                    .then(function (r) { return r.ok ? r.json() : null; })
                    .then(function (t) {
                        if (!t) return;
                        document.querySelectorAll('[data-team-text]').forEach(function (n) { n.textContent = t.text; });
                        document.querySelectorAll('[data-team-dot]').forEach(function (n) { n.dataset.status = t.status; });
                    })
                    .catch(function () {});
            }
            setInterval(function () { if (!document.hidden) refresh(); }, 60000);
            document.addEventListener('visibilitychange', function () { if (!document.hidden) refresh(); });
        })();
    </script>

    @if (! empty($tp) && $tp['total'] > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
            // Transparency charts. Colors follow the page theme (dark or sky).
            (function () {
                if (!window.Chart) return;
                var tp = @json($tp);
                var charts = [];

                function css(name) { return getComputedStyle(document.documentElement).getPropertyValue(name).trim(); }
                function draw() {
                    charts.forEach(function (c) { c.destroy(); });
                    charts = [];
                    var text = css('--txt-dim'), line = css('--line'), bar = css('--gold');
                    Chart.defaults.font.family = "'Host Grotesk', 'Segoe UI', Arial, sans-serif";
                    Chart.defaults.color = text;
                    var axes = function (horizontal) {
                        var count = { ticks: { precision: 0 }, grid: { color: line }, beginAtZero: true };
                        var names = { grid: { display: false } };
                        return horizontal ? { x: count, y: names } : { x: names, y: count };
                    };
                    var base = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } };

                    charts.push(new Chart(document.getElementById('tpStatus'), {
                        type: 'doughnut',
                        data: { labels: ['Pending', 'In progress', 'Resolved'],
                                datasets: [{ data: tp.byStatus, backgroundColor: ['#E8A93B', '#5B8CFF', '#3FB37F'], borderWidth: 0 }] },
                        options: Object.assign({}, base, { cutout: '62%',
                            plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 10, padding: 12 } } } })
                    }));
                    charts.push(new Chart(document.getElementById('tpCategory'), {
                        type: 'bar',
                        data: { labels: tp.catLabels, datasets: [{ data: tp.catData, backgroundColor: bar, borderRadius: 4, maxBarThickness: 18 }] },
                        options: Object.assign({}, base, { indexAxis: 'y', scales: axes(true) })
                    }));
                    charts.push(new Chart(document.getElementById('tpPurok'), {
                        type: 'bar',
                        data: { labels: tp.zoneLabels, datasets: [{ data: tp.zoneData, backgroundColor: bar, borderRadius: 4, maxBarThickness: 28 }] },
                        options: Object.assign({}, base, { scales: axes(false) })
                    }));
                }
                draw();
                // Redraw with the new colors when the theme switch is used
                new MutationObserver(function () { setTimeout(draw, 550); })
                    .observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
            })();
        </script>
    @endif
    <script>
        // ---------- mobile menu: auto-close after a link click ----------
        var navCollapseEl = document.getElementById('navMain');
        var navCollapse = navCollapseEl ? new bootstrap.Collapse(navCollapseEl, { toggle: false }) : null;
        document.querySelectorAll('#navMain a').forEach(a =>
            a.addEventListener('click', () => { if (navCollapse) navCollapse.hide(); })
        );
        // Solid bar while the phone menu is open (see #navbar.menu-open)
        if (navCollapseEl) {
            navCollapseEl.addEventListener('show.bs.collapse', () => document.getElementById('navbar').classList.add('menu-open'));
            navCollapseEl.addEventListener('hidden.bs.collapse', () => document.getElementById('navbar').classList.remove('menu-open'));
        }

        // ---------- navbar: solid on scroll + progress bar ----------
        const navbar = document.getElementById('navbar');
        const progressBar = document.getElementById('progressBar');
        window.addEventListener('scroll', () => {
            const y = window.scrollY;
            navbar.classList.toggle('navbar-solid', y > 40);
            const h = document.documentElement.scrollHeight - window.innerHeight;
            progressBar.style.transform = `scaleX(${h > 0 ? y / h : 0})`;
        }, { passive: true });

        // ---------- active nav link on scroll ----------
        const sections = ['home', 'report', 'about', 'services', 'officials', 'news', 'contact-footer'];
        const navObserver = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    document.querySelectorAll('#navLinks a').forEach(a =>
                        a.classList.toggle('active', a.dataset.section === e.target.id)
                    );
                }
            });
        }, { rootMargin: '-45% 0px -50% 0px' });
        sections.forEach(id => { const el = document.getElementById(id); if (el) navObserver.observe(el); });

        // ---------- scroll reveal ----------
        const revealObserver = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in-view'); revealObserver.unobserve(e.target); } });
        }, { threshold: 0.12 });
        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

        // ---------- animated counters ----------
        const counterObserver = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                counterObserver.unobserve(e.target);
                const target = +e.target.dataset.target;
                const dur = 1600, start = performance.now();
                (function tick(now) {
                    const p = Math.min((now - start) / dur, 1);
                    e.target.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString();
                    if (p < 1) requestAnimationFrame(tick);
                })(start);
            });
        }, { threshold: 0.6 });
        document.querySelectorAll('.counter').forEach(el => counterObserver.observe(el));

        // ---------- scroll-to-top ----------
        const scrollBtn = document.getElementById('scroll-top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) { scrollBtn.classList.remove('d-none'); scrollBtn.classList.add('d-flex'); }
            else { scrollBtn.classList.add('d-none'); scrollBtn.classList.remove('d-flex'); }
        }, { passive: true });
        scrollBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        // ---- Report a Concern form (public, no login) ----
        function isValidContact(value) {
            const v = value.trim();
            if (/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v)) return true;
            return /^(09\d{9}|\+639\d{9}|639\d{9})$/.test(v.replace(/[\s()-]/g, ''));
        }
        document.getElementById('contact').addEventListener('input', function () {
            document.getElementById('contactError').classList.add('d-none');
            this.classList.remove('is-invalid');
        });

        // A blank name means an anonymous report: contact becomes optional (Step 8).
        function isAnonymousReport() {
            return document.getElementById('reporterName').value.trim() === '';
        }
        function updateContactHint() {
            const anon = isAnonymousReport();
            document.getElementById('contactStar').classList.toggle('d-none', anon);
            document.getElementById('contactOptional').classList.toggle('d-none', !anon);
            document.getElementById('contactHint').textContent = anon
                ? 'Optional for anonymous reports. Only barangay officials see it, and it is the only way they can reach you. Without it, use your tracking code to follow up.'
                : 'Only barangay officials see this.';
        }
        document.getElementById('reporterName').addEventListener('input', updateContactHint);
        updateContactHint();

        document.querySelectorAll('input[name="category"]').forEach(radio =>
            radio.addEventListener('change', function () {
                document.getElementById('hotlineWarning').classList.toggle('d-none', this.value !== 'Crime/Safety');
            })
        );

        document.getElementById('description').addEventListener('input', function () {
            document.getElementById('charCount').textContent = this.value.length;
        });

        // Location: see partials/location-picker-script (several GPS readings, draggable pin)

        // ---- Photo evidence: taken live with the camera ----
        const evidenceInput = document.getElementById('evidenceUpload');
        const evidenceDrop  = document.getElementById('evidenceDrop');
        const cameraPanel   = document.getElementById('cameraPanel');
        const cameraVideo   = document.getElementById('cameraVideo');
        const cameraCanvas  = document.getElementById('cameraCanvas');
        const cameraNote    = document.getElementById('cameraNote');
        let cameraStream = null;
        let cameraFlipped = false; // never flipped unless the person taps Flip
        let cameraFacing = 'environment'; // back camera first

        function stopTracks() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(t => t.stop());
                cameraStream = null;
            }
            cameraVideo.srcObject = null;
        }

        function stopCamera() {
            stopTracks();
            cameraPanel.classList.add('d-none');
        }

        function resetEvidence() {
            stopCamera();
            evidenceInput.value = '';
            document.getElementById('idPreviewImg').classList.add('d-none');
            document.getElementById('fileLabel').textContent = 'Tap to capture a photo';
            cameraNote.classList.add('d-none');
        }

        // Shows the captured photo and clears the error messages.
        function applyEvidence(file) {
            const img = document.getElementById('idPreviewImg');
            const fileError = document.getElementById('fileError');
            const fileLabel = document.getElementById('fileLabel');
            const typeError = document.getElementById('fileTypeError');

            // Same types the server accepts (no SVG, no HEIC)
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                typeError.classList.remove('d-none');
                fileError.classList.add('d-none');
                img.classList.add('d-none');
                evidenceInput.value = '';
                fileLabel.textContent = 'Tap to capture a photo';
                return;
            }
            typeError.classList.add('d-none');

            if (file.size > 5 * 1024 * 1024) {
                fileError.classList.remove('d-none');
                img.classList.add('d-none');
                evidenceInput.value = '';
                fileLabel.textContent = 'Tap to capture a photo';
                return;
            }
            fileError.classList.add('d-none');
            document.getElementById('fileMissing').classList.add('d-none');
            evidenceDrop.style.borderColor = '';
            fileLabel.textContent = 'Photo captured. Tap to retake.';

            const reader = new FileReader();
            reader.onload = e => { img.src = e.target.result; img.classList.remove('d-none'); };
            reader.readAsDataURL(file);
        }

        // If the live camera cannot start, phones fall back to their own camera app.
        // Computers only get a message, so a photo is never picked from the files.
        function cameraFallback(message) {
            if (/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                evidenceInput.click();
                return;
            }
            cameraNote.textContent = message;
            cameraNote.classList.remove('d-none');
        }

        // The Switch button only shows on touch devices that report two or more cameras,
        // so a normal laptop never sees it.
        async function updateSwitchButton() {
            const switchBtn = document.getElementById('cameraSwitchBtn');
            let show = false;
            try {
                const isTouch = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent) || navigator.maxTouchPoints > 1;
                if (isTouch) {
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    show = devices.filter(d => d.kind === 'videoinput').length > 1;
                }
            } catch (err) {}
            switchBtn.classList.toggle('d-none', !show);
        }

        async function startCamera() {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: cameraFacing }, width: { ideal: 1280 } },
                audio: false
            });
            cameraVideo.srcObject = cameraStream;
            cameraVideo.style.transform = cameraFlipped ? 'scaleX(-1)' : 'none';
            cameraPanel.classList.remove('d-none');
            updateSwitchButton();
        }

        evidenceDrop.addEventListener('click', async function () {
            stopCamera();
            cameraNote.classList.add('d-none');

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                cameraFallback('The camera is not available in this browser. Open this page over https on a device with a camera.');
                return;
            }
            try {
                await startCamera();
                cameraPanel.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } catch (err) {
                cameraFallback('Could not open the camera. Please allow camera access in your browser and try again.');
            }
        });

        document.getElementById('cameraSwitchBtn').addEventListener('click', async function () {
            const previous = cameraFacing;
            cameraFacing = cameraFacing === 'environment' ? 'user' : 'environment';
            cameraFlipped = false; // the other camera starts unflipped
            stopTracks();
            try {
                await startCamera();
            } catch (err) {
                // Could not open the other camera, go back to the one that worked
                cameraFacing = previous;
                try { await startCamera(); } catch (e2) { stopCamera(); }
                cameraNote.textContent = 'Could not switch the camera.';
                cameraNote.classList.remove('d-none');
            }
        });

        document.getElementById('shutterBtn').addEventListener('click', function () {
            const w = cameraVideo.videoWidth;
            const h = cameraVideo.videoHeight;
            if (!w || !h) return;

            // Keep the longest side at 1600px so the file stays well under 5MB
            const scale = Math.min(1, 1600 / Math.max(w, h));
            cameraCanvas.width = Math.round(w * scale);
            cameraCanvas.height = Math.round(h * scale);
            const ctx = cameraCanvas.getContext('2d');
            if (cameraFlipped) {
                ctx.translate(cameraCanvas.width, 0);
                ctx.scale(-1, 1);
            }
            ctx.drawImage(cameraVideo, 0, 0, cameraCanvas.width, cameraCanvas.height);

            cameraCanvas.toBlob(function (blob) {
                if (!blob) return;
                const file = new File([blob], 'evidence-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                const transfer = new DataTransfer();
                transfer.items.add(file);
                evidenceInput.files = transfer.files;
                stopCamera();
                applyEvidence(file);
            }, 'image/jpeg', 0.85);
        });

        document.getElementById('cameraFlipBtn').addEventListener('click', function () {
            cameraFlipped = !cameraFlipped;
            cameraVideo.style.transform = cameraFlipped ? 'scaleX(-1)' : 'none';
        });

        document.getElementById('cameraCancelBtn').addEventListener('click', stopCamera);
        window.addEventListener('pagehide', stopCamera);

        // Used only when a phone falls back to its own camera app
        evidenceInput.addEventListener('change', function () {
            if (this.files[0]) applyEvidence(this.files[0]);
        });

        document.getElementById('concernForm').addEventListener('submit', function (e) {
            e.preventDefault();

            if (document.getElementById('website').value !== '') return;

            const contactField = document.getElementById('contact');
            const photoInput = document.getElementById('evidenceUpload');
            let firstBad = null;

            const anonymous = isAnonymousReport();
            const contactGiven = contactField.value.trim() !== '';
            // Named reports need a valid contact; anonymous ones only if they typed one
            if ((!anonymous || contactGiven) && !isValidContact(contactField.value)) {
                document.getElementById('contactError').classList.remove('d-none');
                contactField.classList.add('is-invalid');
                firstBad = firstBad || contactField;
            }
            if (!photoInput.files || photoInput.files.length === 0) {
                document.getElementById('fileMissing').classList.remove('d-none');
                firstBad = firstBad || document.getElementById('evidenceDrop');
            }
            if (firstBad) {
                firstBad.scrollIntoView({ behavior: 'smooth', block: 'center' });
                if (firstBad === contactField) contactField.focus({ preventScroll: true });
                return;
            }

            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const btnLabel = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting…';

            const payload = new FormData(form);
            if (anonymous) payload.set('anonymous', '1');

            fetch(form.action, {
                method: 'POST',
                body: payload,
                headers: { 'Accept': 'application/json' }
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    const msg = data.errors
                        ? Object.values(data.errors).flat().join('\n')
                        : (data.message || 'Something went wrong. Please try again.');
                    throw new Error(msg);
                }
                return data;
            })
            .then(data => {
                const code = data.tracking_code;
                document.getElementById('trackingCodeText').textContent = code;
                document.getElementById('trackLink').href = "{{ url('/check-status') }}?code=" + encodeURIComponent(code);
                document.getElementById('copyCodeBtn').querySelector('span').textContent = 'Copy code';
                document.getElementById('copyLinkBtn').querySelector('span').textContent = 'Copy link';

                try {
                    const mine = JSON.parse(localStorage.getItem('pb_my_codes') || '[]');
                    localStorage.setItem('pb_my_codes', JSON.stringify([code, ...mine.filter(c => c !== code)].slice(0, 10)));
                } catch (err) {}

                const confirmBox = document.getElementById('trackingConfirm');
                confirmBox.classList.remove('d-none');
                confirmBox.scrollIntoView({ behavior: 'smooth', block: 'center' });

                form.reset();
                document.getElementById('hotlineWarning').classList.add('d-none');
                document.getElementById('idPreviewImg').classList.add('d-none');
                if (window.tugonLocationReset) window.tugonLocationReset();
                document.getElementById('charCount').textContent = '0';
                resetEvidence();
                document.getElementById('fileMissing').classList.add('d-none');
                document.getElementById('contactError').classList.add('d-none');
                updateContactHint();
            })
            .catch(err => alert(err.message))
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = btnLabel;
            });
        });

        function copyText(text, btn) {
            const label = btn.querySelector('span');
            const done = () => { label.textContent = 'Copied'; };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(done, done);
            } else {
                const t = document.createElement('textarea');
                t.value = text; document.body.appendChild(t); t.select();
                try { document.execCommand('copy'); } catch (err) {}
                t.remove(); done();
            }
        }
        document.getElementById('copyCodeBtn').addEventListener('click', function () {
            copyText(document.getElementById('trackingCodeText').textContent, this);
        });
        document.getElementById('copyLinkBtn').addEventListener('click', function () {
            copyText(document.getElementById('trackLink').href, this);
        });
    </script>
    <script>
        // ---- Theme switch: dark (default) <-> sky, remembered in this browser ----
        (function () {
            var html = document.documentElement;
            var btn = document.getElementById('themeToggle');
            if (!btn) return;

            function current() { return html.getAttribute('data-theme') === 'sky' ? 'sky' : 'dark'; }
            function label() {
                btn.setAttribute('aria-label', current() === 'sky' ? 'Switch to dark theme' : 'Switch to sky theme');
            }
            function setTheme(next) {
                if (next === 'sky') html.setAttribute('data-theme', 'sky'); else html.removeAttribute('data-theme');
                try { localStorage.setItem('pabarang.landingTheme', next); } catch (e) {}
                label();
            }
            function reduced() {
                var r = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                try { r = r || localStorage.getItem('pabarang.reduceMotion') === '1'; } catch (e) {}
                return r;
            }
            label();

            btn.addEventListener('click', function () {
                var next = current() === 'sky' ? 'dark' : 'sky';
                if (reduced()) { setTheme(next); return; }

                if (document.startViewTransition) {
                    // The new theme grows in a circle from the button
                    var r = btn.getBoundingClientRect();
                    var x = r.left + r.width / 2, y = r.top + r.height / 2;
                    var end = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));
                    var t = document.startViewTransition(function () { setTheme(next); });
                    t.ready.then(function () {
                        html.animate(
                            { clipPath: ['circle(0px at ' + x + 'px ' + y + 'px)', 'circle(' + end + 'px at ' + x + 'px ' + y + 'px)'] },
                            { duration: 650, easing: 'cubic-bezier(.22,.68,0,1)', pseudoElement: '::view-transition-new(root)' }
                        );
                    }).catch(function () {});
                } else {
                    // Older browsers: a soft color fade
                    html.classList.add('theme-fade');
                    setTheme(next);
                    setTimeout(function () { html.classList.remove('theme-fade'); }, 600);
                }
            });
        })();
    </script>
</body>
</html>