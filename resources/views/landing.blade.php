<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CNSS | Contrôle des déclarations</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #06346d;
            --navy-700: #052b5b;
            --teal: #008f83;
            --teal-soft: #e4f7f3;
            --bg: #f4f8f9;
            --surface: #ffffff;
            --ink: #101828;
            --muted: #667085;
            --border: #e4e7ec;
            --shadow-sm: 0 1px 3px rgba(6, 52, 109, 0.08);
            --shadow-md: 0 12px 32px rgba(6, 52, 109, 0.10);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        body {
            margin: 0;
            font-family: 'Outfit', system-ui, sans-serif;
            color: var(--ink);
            background: var(--bg);
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; }
        img { max-width: 100%; display: block; }

        /* ============ TOPBAR ============ */

        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar-inner {
            max-width: 1140px;
            margin: 0 auto;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            color: var(--ink);
            text-decoration: none;
        }

        .brand img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid var(--border);
            padding: .2rem;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-text strong {
            font-weight: 700;
            font-size: 1rem;
            color: var(--navy);
        }

        .brand-text span {
            font-size: .7rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .top-link {
            text-decoration: none;
            font-size: .88rem;
            font-weight: 600;
            color: var(--navy);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .55rem 1.15rem;
            background: var(--surface);
            transition: background .15s ease, border-color .15s ease, color .15s ease;
            white-space: nowrap;
        }

        .top-link:hover {
            background: var(--teal-soft);
            border-color: var(--teal);
            color: var(--teal);
        }

        /* ============ HERO ============ */

        .hero {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            overflow: hidden;
        }

        .hero-inner {
            max-width: 1140px;
            margin: 0 auto;
            padding: clamp(3rem, 7vw, 5.5rem) 1.5rem;
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(300px, .9fr);
            gap: clamp(2.5rem, 5vw, 4rem);
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            margin: 0 0 1rem;
            color: var(--teal);
            text-transform: uppercase;
            letter-spacing: .12em;
            font-size: .72rem;
            font-weight: 700;
        }

        .eyebrow::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--teal);
        }

        h1 {
            margin: 0;
            color: var(--ink);
            font-weight: 700;
            font-size: clamp(2.2rem, 4.8vw, 3.6rem);
            line-height: 1.1;
            letter-spacing: -.02em;
        }

        h1 em {
            font-style: normal;
            color: var(--navy);
        }

        .copy {
            margin: 1.2rem 0 0;
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.65;
            max-width: 50ch;
        }

        .actions {
            margin-top: 2rem;
            display: flex;
            gap: .85rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            min-height: 46px;
            border-radius: 8px;
            padding: 0 1.25rem;
            font-weight: 600;
            font-size: .92rem;
            text-decoration: none;
            border: 1px solid transparent;
            transition: transform .15s ease, background .15s ease, border-color .15s ease, color .15s ease;
        }

        .btn-primary {
            background: var(--navy);
            color: #fff;
        }

        .btn-primary:hover { background: var(--navy-700); transform: translateY(-1px); }

        .btn-secondary {
            background: var(--surface);
            color: var(--ink);
            border-color: var(--border);
        }

        .btn-secondary:hover { border-color: var(--teal); color: var(--teal); }

        /* ---- dashboard preview ---- */

        .preview {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .preview-card {
            width: 100%;
            max-width: 340px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow-md);
            padding: 1.25rem;
            animation: preview-in .6s ease-out both;
        }

        @keyframes preview-in {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .preview-title {
            font-size: .82rem;
            font-weight: 700;
            color: var(--ink);
        }

        .preview-badge {
            font-size: .68rem;
            font-weight: 700;
            color: #027a48;
            background: #ecfdf3;
            padding: .25rem .55rem;
            border-radius: 999px;
        }

        .preview-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .75rem 0;
            border-bottom: 1px solid var(--border);
        }

        .preview-row:last-child { border-bottom: 0; }

        .preview-label {
            font-size: .78rem;
            color: var(--muted);
        }

        .preview-value {
            font-size: .85rem;
            font-weight: 600;
            color: var(--ink);
        }

        .preview-bar {
            height: 6px;
            border-radius: 999px;
            background: var(--teal-soft);
            margin-top: .85rem;
            overflow: hidden;
        }

        .preview-bar span {
            display: block;
            height: 100%;
            width: 72%;
            background: var(--teal);
            border-radius: 999px;
        }

        /* ============ SECTIONS ============ */

        .sections {
            max-width: 1140px;
            width: 100%;
            margin: 0 auto;
            padding: clamp(3rem, 6vw, 5rem) 1.5rem;
        }

        .section-head {
            margin-bottom: 1.5rem;
        }

        .section-head .tag {
            display: inline-block;
            font-size: .7rem;
            font-weight: 700;
            color: var(--teal);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: .5rem;
        }

        .section-head h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--ink);
            letter-spacing: -.01em;
        }

        .block {
            margin-bottom: 4rem;
        }

        .block > p.lede {
            margin: 0;
            max-width: 60ch;
            color: var(--muted);
            font-size: 1.02rem;
            line-height: 1.65;
        }

        /* ---- cards ---- */

        .cards {
            margin-top: 1.8rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.25rem;
        }

        .mini-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.4rem;
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }

        .mini-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--teal);
        }

        .mini-card .icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--teal-soft);
            display: grid;
            place-items: center;
            margin-bottom: 1rem;
            color: var(--teal);
        }

        .mini-card strong {
            display: block;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: .4rem;
            font-size: 1rem;
        }

        .mini-card span {
            color: var(--muted);
            font-size: .9rem;
            line-height: 1.55;
        }

        /* ---- steps ---- */

        .steps {
            margin-top: 1.8rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.25rem;
        }

        .step {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            position: relative;
        }

        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--teal-soft);
            color: var(--teal);
            font-size: .85rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .step strong {
            display: block;
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--ink);
            margin-bottom: .4rem;
        }

        .step span {
            color: var(--muted);
            font-size: .89rem;
            line-height: 1.55;
        }

        /* ---- personas ---- */

        .split-section {
            margin-top: 1.8rem;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.25rem;
        }

        .persona-card {
            background: var(--surface);
            border-radius: 12px;
            padding: 1.6rem;
            border: 1px solid var(--border);
            transition: box-shadow .15s ease, border-color .15s ease;
        }

        .persona-card:hover {
            border-color: var(--teal);
            box-shadow: var(--shadow-md);
        }

        .persona-card h3 {
            margin: 0 0 .6rem;
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--ink);
        }

        .persona-card p {
            margin: 0;
            line-height: 1.6;
            font-size: .93rem;
            color: var(--muted);
        }

        .persona-link {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            margin-top: 1.1rem;
            font-size: .87rem;
            font-weight: 700;
            text-decoration: none;
            color: var(--navy);
        }

        .persona-link:hover { color: var(--teal); }

        /* ============ FOOTER ============ */

        .footer {
            border-top: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            font-size: .85rem;
            padding: 1.75rem 1.5rem;
            text-align: center;
        }

        /* ============ RESPONSIVE ============ */

        @media (max-width: 900px) {
            .hero-inner,
            .cards,
            .steps,
            .split-section {
                grid-template-columns: 1fr;
            }
        }

        :focus-visible {
            outline: 2px solid var(--teal);
            outline-offset: 2px;
        }
    </style>
</head>
<body>

    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand">
                <img src="{{ asset('images/logo-CNSS.png') }}" alt="Logo CNSS">
                <div class="brand-text">
                    <strong>CNSS</strong>
                    <span>Contrôle des déclarations</span>
                </div>
            </div>
            <a class="top-link" href="{{ route('login') }}">Connexion agent</a>
        </div>
    </header>

    <section class="hero">
        <div class="hero-inner">
            <div class="intro">
                <p class="eyebrow">Portail employeur</p>
                <h1>Affiliez votre entreprise à la <em>CNSS</em> en quelques étapes.</h1>
                <p class="copy">
                    Formulaire public simple, numéro de suivi immédiat et traitement transparent.
                    Aucun compte applicatif n'est nécessaire pour déposer une demande d'affiliation.
                </p>
                <div class="actions">
                    <a class="btn btn-secondary" href="#parcours">Voir comment ça marche</a>
                </div>
            </div>

            <div class="preview" aria-hidden="true">
                <div class="preview-card">
                    <div class="preview-header">
                        <span class="preview-title">Demande d'affiliation</span>
                        <span class="preview-badge">En cours</span>
                    </div>
                    <div class="preview-row">
                        <span class="preview-label">Numéro de suivi</span>
                        <span class="preview-value">CNSS-2026-04821</span>
                    </div>
                    <div class="preview-row">
                        <span class="preview-label">Étape actuelle</span>
                        <span class="preview-value">Vérification</span>
                    </div>
                    <div class="preview-row">
                        <span class="preview-label">Prochaine action</span>
                        <span class="preview-value">Avis SDT</span>
                    </div>
                    <div class="preview-bar"><span></span></div>
                </div>
            </div>
        </div>
    </section>

    <main class="sections">

        <div class="block">
            <div class="section-head">
                <span class="tag">Avantages</span>
                <h2>Une démarche simplifiée pour l'employeur</h2>
            </div>
            <p class="lede">
                La plateforme publique guide l'employeur depuis la demande initiale jusqu'à
                l'enregistrement définitif du dossier CNSS.
            </p>

            <div class="cards">
                <article class="mini-card">
                    <div class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
                    </div>
                    <strong>Sans compte préalable</strong>
                    <span>Le formulaire public est accessible directement. Aucune inscription n'est requise pour déposer une demande.</span>
                </article>
                <article class="mini-card">
                    <div class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                    </div>
                    <strong>Suivi par numéro</strong>
                    <span>Chaque demande reçoit un numéro de suivi unique pour consulter l'avancement du dossier.</span>
                </article>
                <article class="mini-card">
                    <div class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <strong>Canal officiel CNSS</strong>
                    <span>Les informations sont transmises directement à la Caisse Nationale de Sécurité Sociale.</span>
                </article>
            </div>
        </div>

        <div class="block" id="parcours">
            <div class="section-head">
                <span class="tag">Parcours</span>
                <h2>Comment ça marche ?</h2>
            </div>

            <div class="steps">
                <article class="step">
                    <span class="step-number">1</span>
                    <strong>Je remplis le formulaire</strong>
                    <span>L'employeur renseigne les informations de l'entreprise et de son activité.</span>
                </article>
                <article class="step">
                    <span class="step-number">2</span>
                    <strong>Je reçois mon numéro de suivi</strong>
                    <span>Un numéro unique est attribué pour suivre l'état de la demande.</span>
                </article>
                <article class="step">
                    <span class="step-number">3</span>
                    <strong>La CNSS instruit le dossier</strong>
                    <span>Les agents vérifient les informations et valident l'affiliation.</span>
                </article>
            </div>
        </div>

        <div class="block" style="margin-bottom: 1rem;">
            <div class="section-head">
                <span class="tag">Aperçu</span>
                <h2>Ce qui est traité en arrière-plan</h2>
            </div>
            <p class="lede">
                Derrière le formulaire public, la CNSS gère l'ensemble du cycle de vie du dossier :
                affiliation, déclarations, cotisations et contrôles.
            </p>

            <div class="cards">
                <article class="mini-card">
                    <div class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/></svg>
                    </div>
                    <strong>Déclarations et cotisations</strong>
                    <span>Les montants déclarés sont centralisés, recalculés et suivis pour chaque période.</span>
                </article>
                <article class="mini-card">
                    <div class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <strong>Employeurs et travailleurs</strong>
                    <span>Le registre relie les employeurs à leurs travailleurs et à leurs déclarations.</span>
                </article>
                <article class="mini-card">
                    <div class="icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <strong>Alertes et contrôles</strong>
                    <span>Les écarts de cotisation et les anomalies sont détectés automatiquement.</span>
                </article>
            </div>
        </div>

        <div class="block" style="margin-bottom: 1rem;">
            <div class="persona-card" style="text-align: center; padding: 2rem;">
                <h3 style="margin-bottom: .5rem;">Vous êtes agent CNSS ?</h3>
                <p style="max-width: 50ch; margin: 0 auto;">
                    L'accès sécurisé permet de gérer les affiliations, employeurs, déclarations et cotisations.
                </p>
                <a class="btn btn-secondary" href="{{ route('login') }}" style="margin-top: 1.25rem;">Se connecter à l'espace agent</a>
            </div>
        </div>

        <div class="block" style="margin-bottom: 1rem;">
            <div class="persona-card" style="text-align: center; padding: 2rem;">
                <h3 style="margin-bottom: .5rem;">Prêt à déposer une demande ?</h3>
                <p style="max-width: 52ch; margin: 0 auto;">
                    Accédez au formulaire public et recevez votre numéro de suivi par email après soumission.
                </p>
                <a class="btn btn-primary" href="{{ route('affiliation.create') }}" style="margin-top: 1.25rem;">Demander une affiliation</a>
            </div>
        </div>

    </main>

    <footer class="footer">
        Caisse Nationale de Sécurité Sociale
    </footer>

</body>
</html>
