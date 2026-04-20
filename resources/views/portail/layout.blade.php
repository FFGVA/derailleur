<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Mon espace') — Fast and Female Geneva</title>
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <style>
        :root {
            --color-primary: {{ config('association.colors.primary') }};
            --color-primary-hover: {{ config('association.colors.primary_hover') }};
            --color-bg: {{ config('association.colors.background') }};
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.5;
            color: #333;
            background-color: var(--color-bg);
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
        }
        .portal-header {
            background-color: var(--color-primary);
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .portal-brand {
            color: white;
            font-weight: 700;
            font-size: 1.125rem;
            text-decoration: none;
        }
        .portal-brand img {
            height: 1.75rem;
            vertical-align: middle;
        }
        .portal-header-action {
            background: none;
            border: 1px solid rgba(255,255,255,0.4);
            color: white;
            font-size: 0.8125rem;
            padding: 0.375rem 0.875rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.2s;
            text-decoration: none;
        }
        .portal-header-action:hover {
            background-color: rgba(255,255,255,0.15);
        }
        .portal-main {
            flex: 1;
            padding: 1.25rem 1rem;
            max-width: 480px;
            margin: 0 auto;
            width: 100%;
        }
        .portal-footer {
            text-align: center;
            padding: 1.25rem 1rem;
            color: #999;
            font-size: 0.75rem;
        }
        @yield('styles')
    </style>
</head>
<body>
    @yield('header')

    <main class="portal-main">
        @yield('content')
    </main>

    <footer class="portal-footer">
        <img src="/images/logo-ffgva.png" alt="Fast and Female Geneva" style="height: 2rem; margin-bottom: 0.5rem;">
        <div>&copy; {{ date('Y') }} Smart Gecko SA</div>
    </footer>

    <script>
    function openMaps(q) {
        var ua = navigator.userAgent || '';
        if (/iPhone|iPad|iPod/i.test(ua)) {
            window.location = 'maps:?q=' + q;
        } else if (/Android/i.test(ua)) {
            window.location = 'geo:0,0?q=' + q;
        } else {
            window.open('https://www.google.com/maps/search/?api=1&query=' + q, '_blank');
        }
    }
    </script>
    @yield('scripts')
</body>
</html>
