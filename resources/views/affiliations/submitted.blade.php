<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande soumise | CNSS</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; color: #101828; background: #f9fafb; }
        .page { max-width: 760px; margin: 0 auto; padding: 2rem 1rem; }
        .panel { background: #fff; border: 1px solid #e7eef2; border-radius: 12px; padding: 1.2rem; box-shadow: 0 1px 3px rgba(6, 52, 109, .08); }
        h1 { margin: 0 0 .5rem; font-size: 1.4rem; }
        p { color: #667085; line-height: 1.5; }
        .tracking { display: inline-flex; margin-top: .4rem; padding: .45rem .7rem; border-radius: 999px; background: #e7f0fa; color: #06346d; font-weight: 700; }
    </style>
</head>
<body>
<main class="page">
    <section class="panel">
        <h1>Demande d'affiliation soumise</h1>
        <p>Votre demande a ete enregistree. Conservez ce numero de suivi pour les echanges avec la CNSS.</p>
        <span class="tracking">{{ $affiliationRequest->tracking_number }}</span>
    </section>
</main>
</body>
</html>
