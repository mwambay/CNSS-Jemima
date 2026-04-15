<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CNSS')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-50: #ecf3ff;
            --brand-100: #dde9ff;
            --brand-300: #9cb9ff;
            --brand-500: #465fff;
            --brand-600: #3641f5;
            --gray-50: #f9fafb;
            --gray-100: #f2f4f7;
            --gray-200: #e4e7ec;
            --gray-300: #d0d5dd;
            --gray-400: #98a2b3;
            --gray-500: #667085;
            --gray-700: #344054;
            --gray-900: #101828;
            --error-500: #f04438;
            --shadow-xs: 0 1px 2px rgba(16, 24, 40, 0.05);
            --shadow-sm: 0 1px 3px rgba(16, 24, 40, 0.1), 0 1px 2px rgba(16, 24, 40, 0.06);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Outfit, sans-serif;
            color: var(--gray-900);
            background: var(--gray-50);
        }

        .app {
            display: grid;
            grid-template-columns: 270px minmax(0, 1fr);
            min-height: 100vh;
        }

        .sidebar {
            border-right: 1px solid var(--gray-200);
            background: #fff;
            padding: 1rem;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .55rem .5rem;
            margin-bottom: 1rem;
        }

        .brand-dot {
            width: 12px;
            height: 12px;
            border-radius: 99px;
            background: linear-gradient(130deg, var(--brand-500), #7a5af8);
        }

        .brand-name {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: .01em;
        }

        .nav {
            display: grid;
            gap: .3rem;
        }

        .nav-link {
            text-decoration: none;
            color: var(--gray-700);
            border-radius: 10px;
            padding: .62rem .7rem;
            font-size: .9rem;
            font-weight: 600;
            border: 1px solid transparent;
            transition: background .15s ease, color .15s ease, border-color .15s ease;
        }

        .nav-link:hover {
            background: var(--gray-100);
            color: var(--gray-900);
        }

        .nav-link.active {
            background: var(--brand-50);
            color: var(--brand-600);
            border-color: var(--brand-100);
        }

        .main {
            min-width: 0;
            display: grid;
            grid-template-rows: auto 1fr;
        }

        .header {
            background: #fff;
            border-bottom: 1px solid var(--gray-200);
            padding: .9rem 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .9rem;
            position: sticky;
            top: 0;
            z-index: 5;
        }

        .header-title {
            margin: 0;
            font-size: 1.04rem;
            font-weight: 700;
        }

        .header-subtitle {
            margin: .2rem 0 0;
            color: var(--gray-500);
            font-size: .86rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: .65rem;
        }

        .user-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border: 1px solid var(--gray-200);
            background: #fff;
            border-radius: 999px;
            padding: .35rem .65rem;
            font-size: .82rem;
            color: var(--gray-700);
            box-shadow: var(--shadow-xs);
        }

        .btn {
            border: 0;
            border-radius: 10px;
            padding: .56rem .86rem;
            font: inherit;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform .12s ease;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-outline {
            background: #fff;
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
        }

        .btn-danger {
            color: #fff;
            background: var(--error-500);
        }

        .content {
            padding: 1.1rem;
        }

        @media (max-width: 1000px) {
            .app { grid-template-columns: 1fr; }
            .sidebar {
                height: auto;
                position: static;
                border-right: 0;
                border-bottom: 1px solid var(--gray-200);
            }
            .main { grid-template-rows: auto auto 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-dot"></span>
            <span class="brand-name">CNSS Console</span>
        </div>

        <nav class="nav">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Tableau de bord</a>
            <a class="nav-link {{ request()->routeIs('employers.interface') ? 'active' : '' }}" href="{{ route('employers.interface') }}">Employeurs</a>
            <a class="nav-link {{ request()->routeIs('workers.interface') ? 'active' : '' }}" href="{{ route('workers.interface') }}">Travailleurs</a>
        </nav>
    </aside>

    <main class="main">
        <header class="header">
            <div>
                <h1 class="header-title">@yield('page_title', 'Espace de gestion')</h1>
                <p class="header-subtitle">@yield('page_subtitle', 'Administration CNSS')</p>
            </div>
            <div class="header-right">
                <span class="user-pill">Connecte: {{ auth()->user()->full_name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">Deconnexion</button>
                </form>
            </div>
        </header>

        <section class="content">
            @yield('content')
        </section>
    </main>
</div>

@stack('scripts')
</body>
</html>
