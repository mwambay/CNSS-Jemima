<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | CNSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-100: #dde9ff;
            --brand-500: #465fff;
            --brand-600: #3641f5;
            --gray-50: #f9fafb;
            --gray-200: #e4e7ec;
            --gray-300: #d0d5dd;
            --gray-500: #667085;
            --gray-700: #344054;
            --gray-900: #101828;
            --error: #d92d20;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Outfit, sans-serif;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at 15% 0%, rgba(70,95,255,.20), transparent 35%),
                radial-gradient(circle at 85% 20%, rgba(122,90,248,.16), transparent 30%),
                var(--gray-50);
            color: var(--gray-900);
            padding: 1rem;
        }

        .card {
            width: min(460px, 100%);
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 1.2rem;
            box-shadow: 0 16px 34px rgba(16,24,40,.08);
        }

        h1 {
            margin: 0;
            font-size: 1.28rem;
            font-weight: 700;
        }

        .sub {
            margin: .35rem 0 1rem;
            color: var(--gray-500);
            font-size: .9rem;
        }

        .field { margin-bottom: .78rem; }

        .field label {
            display: block;
            font-size: .84rem;
            margin-bottom: .35rem;
            color: var(--gray-700);
            font-weight: 600;
        }

        .control {
            width: 100%;
            height: 44px;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            padding: .55rem .72rem;
            font: inherit;
            font-size: .92rem;
        }

        .control:focus {
            outline: 0;
            border-color: var(--brand-100);
            box-shadow: 0 0 0 4px rgba(70, 95, 255, .12);
        }

        .btn {
            width: 100%;
            border: 0;
            border-radius: 10px;
            padding: .66rem .92rem;
            color: #fff;
            background: var(--brand-500);
            font: inherit;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
        }

        .btn:hover { background: var(--brand-600); }

        .error {
            margin-bottom: .7rem;
            border: 1px solid rgba(217,45,32,.24);
            background: rgba(217,45,32,.06);
            color: var(--error);
            border-radius: 10px;
            padding: .6rem .7rem;
            font-size: .86rem;
            font-weight: 600;
        }

        .hint {
            margin-top: .85rem;
            color: var(--gray-500);
            font-size: .82rem;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>Connexion CNSS</h1>
    <p class="sub">Accedez a la console de gestion.</p>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <div class="field">
            <label for="login">Username ou email</label>
            <input id="login" name="login" class="control" value="{{ old('login') }}" required autofocus>
        </div>

        <div class="field">
            <label for="password">Mot de passe</label>
            <input id="password" type="password" name="password" class="control" required>
        </div>

        <button class="btn" type="submit">Se connecter</button>
    </form>

    <p class="hint">Compte seed par defaut: admin / admin1234</p>
</div>
</body>
</html>
