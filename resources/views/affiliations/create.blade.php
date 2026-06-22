<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'affiliation employeur | CNSS</title>
    <style>
        :root {
            --ink: #111827;
            --muted: #5b6472;
            --line: #d6e0e7;
            --line-soft: #e7eef2;
            --paper: #ffffff;
            --surface: #f4f8f9;
            --blue: #06346d;
            --blue-700: #052b5b;
            --blue-soft: #e7f0fa;
            --teal: #008f83;
            --teal-soft: #e4f7f3;
            --green: #0c8f4d;
            --green-soft: #e6f7ed;
            --yellow: #f5d90a;
            --red: #b42318;
            --shadow: 0 14px 36px rgba(6, 52, 109, .08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--ink);
            background:
                linear-gradient(180deg, #e8f3f5 0, rgba(232, 243, 245, 0) 330px),
                var(--surface);
        }

        .topbar {
            border-bottom: 1px solid var(--line-soft);
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(10px);
        }

        .topbar-inner {
            max-width: 1180px;
            margin: 0 auto;
            padding: .9rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: .7rem;
            font-weight: 800;
            letter-spacing: .02em;
            color: var(--blue);
        }

        .brand-mark {
            width: 54px;
            height: 54px;
            border-radius: 8px;
            display: block;
            object-fit: contain;
            background: #fff;
            border: 1px solid var(--line-soft);
            padding: .22rem;
        }

        .topbar-meta {
            color: var(--muted);
            font-size: .9rem;
            text-align: right;
        }

        .page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 1.5rem 1rem 2.5rem;
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 1.1rem;
            align-items: stretch;
            margin-bottom: 1.1rem;
        }

        .hero-main,
        .summary-card,
        .form-card {
            background: var(--paper);
            border: 1px solid var(--line-soft);
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .hero-main {
            padding: 1.35rem;
            display: grid;
            align-content: center;
            min-height: 190px;
        }

        .eyebrow {
            margin: 0 0 .6rem;
            color: var(--teal);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        h1 {
            max-width: 760px;
            margin: 0;
            font-size: 2rem;
            line-height: 1.14;
            color: var(--blue);
        }

        .hero-copy {
            max-width: 740px;
            margin: .65rem 0 0;
            color: var(--muted);
            line-height: 1.55;
        }

        .summary-card {
            padding: 1rem;
            display: grid;
            gap: .7rem;
            align-content: start;
        }

        .summary-title {
            margin: 0;
            font-size: 1rem;
            color: var(--blue);
        }

        .summary-row {
            display: flex;
            gap: .6rem;
            align-items: flex-start;
            color: var(--muted);
            font-size: .9rem;
            line-height: 1.35;
        }

        .summary-dot {
            flex: 0 0 auto;
            width: 22px;
            height: 22px;
            border-radius: 999px;
            background: var(--teal-soft);
            color: var(--teal);
            display: grid;
            place-items: center;
            font-weight: 900;
            font-size: .76rem;
        }

        .form-shell {
            display: grid;
            grid-template-columns: 250px minmax(0, 1fr);
            gap: 1.1rem;
            align-items: start;
        }

        .rail {
            position: sticky;
            top: 1rem;
            display: grid;
            gap: .45rem;
        }

        .rail a {
            text-decoration: none;
            color: #344054;
            background: #fff;
            border: 1px solid var(--line-soft);
            border-radius: 8px;
            padding: .7rem .8rem;
            font-size: .88rem;
            font-weight: 700;
        }

        .rail a:hover {
            border-color: #9ed7ce;
            color: var(--blue);
            background: var(--teal-soft);
        }

        .form-card {
            padding: 1.15rem;
        }

        .notice {
            border: 1px solid #b7dfe5;
            background: var(--blue-soft);
            border-radius: 8px;
            padding: .85rem .95rem;
            color: var(--blue);
            font-size: .92rem;
            line-height: 1.45;
            margin-bottom: 1rem;
        }

        .form-section {
            padding: 1.15rem 0;
            border-top: 1px solid var(--line-soft);
        }

        .form-section:first-of-type {
            border-top: 0;
            padding-top: 0;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: .85rem;
        }

        .section-title {
            margin: 0;
            font-size: 1.08rem;
            color: var(--blue);
        }

        .section-note {
            max-width: 420px;
            margin: .2rem 0 0;
            color: var(--muted);
            font-size: .88rem;
            line-height: 1.4;
        }

        .section-pill {
            flex: 0 0 auto;
            border-radius: 999px;
            padding: .28rem .62rem;
            background: var(--teal-soft);
            color: var(--teal);
            font-size: .75rem;
            font-weight: 800;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .85rem;
        }

        .grid.three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .field {
            display: grid;
            gap: .38rem;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            color: #344054;
            font-size: .84rem;
            font-weight: 800;
        }

        .hint {
            color: var(--muted);
            font-size: .8rem;
            line-height: 1.35;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid #cfd6e2;
            border-radius: 8px;
            padding: .68rem .75rem;
            min-height: 44px;
            font: inherit;
            color: var(--ink);
            background: #fff;
        }

        textarea {
            min-height: 96px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: 0;
            border-color: var(--teal);
            box-shadow: 0 0 0 4px rgba(0, 143, 131, .13);
        }

        .required {
            color: var(--red);
        }

        .error {
            color: var(--red);
            font-size: .82rem;
            font-weight: 700;
        }

        .actions {
            border-top: 1px solid var(--line-soft);
            padding-top: 1rem;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .actions-copy {
            max-width: 520px;
            color: var(--muted);
            font-size: .86rem;
            line-height: 1.4;
        }

        .btn {
            border: 0;
            border-radius: 8px;
            padding: .78rem 1.05rem;
            background: var(--blue);
            color: #fff;
            font-weight: 800;
            cursor: pointer;
            min-height: 46px;
            box-shadow: 0 8px 18px rgba(6, 52, 109, .18);
        }

        .btn:hover {
            background: var(--blue-700);
        }

        @media (max-width: 980px) {
            .hero,
            .form-shell {
                grid-template-columns: 1fr;
            }

            .rail {
                position: static;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px) {
            .topbar-inner {
                align-items: flex-start;
                flex-direction: column;
            }

            .topbar-meta {
                text-align: left;
            }

            h1 {
                font-size: 1.55rem;
            }

            .grid,
            .grid.three,
            .rail {
                grid-template-columns: 1fr;
            }

            .section-head {
                display: grid;
            }
        }
    </style>
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <div class="brand">
            <img class="brand-mark" src="{{ asset('images/logo-CNSS.png') }}" alt="Logo CNSS">
            <span>Controle des declarations et cotisations</span>
        </div>
        <div class="topbar-meta">Votre demande d'affiliation</div>
    </div>
</header>

<main class="page">
    <section class="hero">
        <div class="hero-main">
            <p class="eyebrow">Demande publique</p>
            <h1>Affiliez votre entreprise a la CNSS</h1>
            <p class="hero-copy">
                Renseignez les informations de votre entreprise, son adresse, ses activites et son personnel.
                Apres envoi, la CNSS examinera votre demande et vous attribuera un numero d'affiliation si elle est validee.
            </p>
        </div>
        <aside class="summary-card" aria-label="Traitement de votre demande">
            <h2 class="summary-title">Ce qui se passe ensuite</h2>
            <div class="summary-row">
                <span class="summary-dot">1</span>
                <span>Vous envoyez votre demande sans creer de compte applicatif.</span>
            </div>
            <div class="summary-row">
                <span class="summary-dot">2</span>
                <span>La CNSS verifie les informations que vous avez fournies.</span>
            </div>
            <div class="summary-row">
                <span class="summary-dot">3</span>
                <span>Apres approbation, votre entreprise est enregistree avec son numero CNSS.</span>
            </div>
        </aside>
    </section>

    <div class="form-shell">
        <nav class="rail" aria-label="Sections du formulaire">
            <a href="#identification">Identification</a>
            <a href="#adresse">Adresse et contacts</a>
            <a href="#cadre">Cadre juridique</a>
            <a href="#activite">Activite</a>
            <a href="#personnel">Personnel</a>
            <a href="#signature">Signature</a>
        </nav>

        <form class="form-card" method="POST" action="{{ route('affiliation.store') }}">
            @csrf

            <div class="notice">
                Les champs marques d'un astisque sont requis. Si vous representez une entreprise, indiquez sa raison sociale.
                Si vous etes une personne physique employant du personnel, renseignez votre nom complet.
            </div>

            <section class="form-section" id="identification">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Votre identification</h2>
                        <p class="section-note">Ces informations permettent a la CNSS d'identifier votre entreprise ou votre activite.</p>
                    </div>
                    <span class="section-pill">Etape 1</span>
                </div>
                <div class="grid">
                    <div class="field">
                        <label for="management_center">Centre de gestion</label>
                        <input id="management_center" name="management_center" value="{{ old('management_center') }}" maxlength="150">
                        @error('management_center') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="registration_number">Numero d'immatriculation existant</label>
                        <input id="registration_number" name="registration_number" value="{{ old('registration_number') }}" maxlength="80">
                        @error('registration_number') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="legal_name">Raison sociale <span class="required">*</span></label>
                        <input id="legal_name" name="legal_name" value="{{ old('legal_name') }}" maxlength="200" placeholder="Ex. Jemima Services SARL">
                        <span class="hint">A remplir si vous representez une societe, une association ou un etablissement.</span>
                        @error('legal_name') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="physical_employer_name">Votre nom comme employeur personne physique <span class="required">*</span></label>
                        <input id="physical_employer_name" name="physical_employer_name" value="{{ old('physical_employer_name') }}" maxlength="200" placeholder="Nom complet">
                        <span class="hint">Requis si aucune raison sociale n'est indiquee.</span>
                        @error('physical_employer_name') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="abbreviation">Sigle</label>
                        <input id="abbreviation" name="abbreviation" value="{{ old('abbreviation') }}" maxlength="80" placeholder="Ex. JS SARL">
                        @error('abbreviation') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="legal_form">Forme juridique</label>
                        <select id="legal_form" name="legal_form">
                            <option value="">Selectionner</option>
                            @foreach(['SARL', 'SA', 'ASBL', 'ETS', 'AUTRE'] as $option)
                                <option value="{{ $option }}" @selected(old('legal_form') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('legal_form') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <section class="form-section" id="adresse">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Adresse et contacts</h2>
                        <p class="section-note">Indiquez ou votre activite est situee et comment la CNSS peut vous contacter.</p>
                    </div>
                    <span class="section-pill">Etape 2</span>
                </div>
                <div class="grid three">
                    <div class="field">
                        <label for="street">Avenue / rue</label>
                        <input id="street" name="street" value="{{ old('street') }}" maxlength="200">
                        @error('street') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="district">Quartier</label>
                        <input id="district" name="district" value="{{ old('district') }}" maxlength="120">
                        @error('district') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="municipality">Commune</label>
                        <input id="municipality" name="municipality" value="{{ old('municipality') }}" maxlength="120">
                        @error('municipality') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="city">Ville</label>
                        <input id="city" name="city" value="{{ old('city') }}" maxlength="120">
                        @error('city') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="province">Province</label>
                        <input id="province" name="province" value="{{ old('province') }}" maxlength="120">
                        @error('province') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="postal_box">Boite postale</label>
                        <input id="postal_box" name="postal_box" value="{{ old('postal_box') }}" maxlength="80">
                        @error('postal_box') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="phone">Telephone <span class="required">*</span></label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" maxlength="30" required placeholder="+243...">
                        @error('phone') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="150" placeholder="contact@exemple.cd">
                        @error('email') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="fax">Fax</label>
                        <input id="fax" name="fax" value="{{ old('fax') }}" maxlength="30">
                        @error('fax') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field full">
                        <label for="website">Site web</label>
                        <input id="website" name="website" value="{{ old('website') }}" maxlength="200" placeholder="https://...">
                        @error('website') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <section class="form-section" id="cadre">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Cadre juridique</h2>
                        <p class="section-note">Ajoutez vos references d'enregistrement et vos documents administratifs disponibles.</p>
                    </div>
                    <span class="section-pill">Etape 3</span>
                </div>
                <div class="grid">
                    <div class="field">
                        <label for="rccm_number">Numero RCCM</label>
                        <input id="rccm_number" name="rccm_number" value="{{ old('rccm_number') }}" maxlength="80">
                        @error('rccm_number') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="rccm_delivered_at">RCCM delivre a</label>
                        <input id="rccm_delivered_at" name="rccm_delivered_at" value="{{ old('rccm_delivered_at') }}" maxlength="120">
                        @error('rccm_delivered_at') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="rccm_delivered_on">RCCM delivre le</label>
                        <input id="rccm_delivered_on" name="rccm_delivered_on" type="date" value="{{ old('rccm_delivered_on') }}">
                        @error('rccm_delivered_on') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="approval_order_ref">Reference arrete agrement</label>
                        <input id="approval_order_ref" name="approval_order_ref" value="{{ old('approval_order_ref') }}" maxlength="150">
                        @error('approval_order_ref') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="creation_act_ref">Reference acte constitutif</label>
                        <input id="creation_act_ref" name="creation_act_ref" value="{{ old('creation_act_ref') }}" maxlength="150">
                        @error('creation_act_ref') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="head_office">Siege social</label>
                        <input id="head_office" name="head_office" value="{{ old('head_office') }}" maxlength="200">
                        @error('head_office') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="operating_sites_count">Nombre de sites d'exploitation</label>
                        <input id="operating_sites_count" name="operating_sites_count" type="number" min="0" value="{{ old('operating_sites_count') }}">
                        @error('operating_sites_count') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field full">
                        <label for="operating_sites_addresses">Adresses des sites d'exploitation</label>
                        <textarea id="operating_sites_addresses" name="operating_sites_addresses">{{ old('operating_sites_addresses') }}</textarea>
                        @error('operating_sites_addresses') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <section class="form-section" id="activite">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Activite economique</h2>
                        <p class="section-note">Activite principale et date de debut d'exploitation.</p>
                    </div>
                    <span class="section-pill">Etape 4</span>
                </div>
                <div class="grid">
                    <div class="field">
                        <label for="primary_activity">Activite principale</label>
                        <input id="primary_activity" name="primary_activity" value="{{ old('primary_activity') }}" maxlength="200">
                        @error('primary_activity') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="secondary_activity">Activite secondaire</label>
                        <input id="secondary_activity" name="secondary_activity" value="{{ old('secondary_activity') }}" maxlength="200">
                        @error('secondary_activity') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="activity_start_date">Date debut activite</label>
                        <input id="activity_start_date" name="activity_start_date" type="date" value="{{ old('activity_start_date') }}">
                        @error('activity_start_date') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <section class="form-section" id="personnel">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Personnel et masse salariale</h2>
                        <p class="section-note">Donnees initiales pour l'affiliation et la future declaration des cotisations.</p>
                    </div>
                    <span class="section-pill">Etape 5</span>
                </div>
                <div class="grid">
                    <div class="field">
                        <label for="personnel_employment_start_date">Date debut emploi du personnel</label>
                        <input id="personnel_employment_start_date" name="personnel_employment_start_date" type="date" value="{{ old('personnel_employment_start_date') }}">
                        @error('personnel_employment_start_date') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="workers_count">Nombre de travailleurs</label>
                        <input id="workers_count" name="workers_count" type="number" min="0" value="{{ old('workers_count') }}">
                        @error('workers_count') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="assimilated_workers_count">Nombre de travailleurs assimiles</label>
                        <input id="assimilated_workers_count" name="assimilated_workers_count" type="number" min="0" value="{{ old('assimilated_workers_count') }}">
                        @error('assimilated_workers_count') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="monthly_workers_gross_pay">Remuneration brute mensuelle</label>
                        <input id="monthly_workers_gross_pay" name="monthly_workers_gross_pay" type="number" min="0" step="0.01" value="{{ old('monthly_workers_gross_pay') }}">
                        @error('monthly_workers_gross_pay') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="monthly_assimilated_workers_gross_income">Revenu brut assimiles</label>
                        <input id="monthly_assimilated_workers_gross_income" name="monthly_assimilated_workers_gross_income" type="number" min="0" step="0.01" value="{{ old('monthly_assimilated_workers_gross_income') }}">
                        @error('monthly_assimilated_workers_gross_income') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="monthly_contribution_base_total">Total base mensuelle cotisable (CDF)</label>
                        <input id="monthly_contribution_base_total" name="monthly_contribution_base_total" type="number" min="0" step="0.01" value="{{ old('monthly_contribution_base_total') }}">
                        @error('monthly_contribution_base_total') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <section class="form-section" id="signature">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Reprise, signature et declaration</h2>
                        <p class="section-note">Completez cette partie si vous reprenez une activite existante, puis indiquez le lieu et la date de signature.</p>
                    </div>
                    <span class="section-pill">Etape 6</span>
                </div>
                <div class="grid">
                    <div class="field">
                        <label for="takeover_date">Date de reprise</label>
                        <input id="takeover_date" name="takeover_date" type="date" value="{{ old('takeover_date') }}">
                        @error('takeover_date') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="signature_place">Lieu de signature</label>
                        <input id="signature_place" name="signature_place" value="{{ old('signature_place') }}" maxlength="120">
                        @error('signature_place') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="signed_at">Date de signature</label>
                        <input id="signed_at" name="signed_at" type="date" value="{{ old('signed_at') }}">
                        @error('signed_at') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field full">
                        <label for="transferor_names">Noms des cedants</label>
                        <textarea id="transferor_names" name="transferor_names">{{ old('transferor_names') }}</textarea>
                        @error('transferor_names') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field full">
                        <label for="transferor_affiliation_numbers">Numeros d'affiliation des cedants</label>
                        <textarea id="transferor_affiliation_numbers" name="transferor_affiliation_numbers">{{ old('transferor_affiliation_numbers') }}</textarea>
                        @error('transferor_affiliation_numbers') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <div class="actions">
                <div class="actions-copy">
                    Apres soumission, vous recevrez un numero de suivi. Votre entreprise sera creee dans le systeme
                    uniquement apres approbation par la CNSS.
                </div>
                <button class="btn" type="submit">Soumettre la demande</button>
            </div>
        </form>
    </div>
</main>
</body>
</html>
