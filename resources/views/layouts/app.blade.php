<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <title>@yield('title', 'PustumedApp')</title>

    <script src="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/outline/index.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    @yield('css')
    <script>
        // If a per-tab token exists, append it to internal links and form actions
        (function(){
            try {
                const token = sessionStorage.getItem('pustumed_token');
                if (!token) return;

                // append token to anchors
                document.addEventListener('DOMContentLoaded', function(){
                    const anchors = document.querySelectorAll('a[href^="/"]');
                    anchors.forEach(a => {
                        try {
                            const url = new URL(a.href, location.origin);
                            if (!url.searchParams.has('token')) {
                                url.searchParams.set('token', token);
                                a.href = url.pathname + url.search + url.hash;
                            }
                        } catch(e) {}
                    });

                    // append token to form actions (for same-origin forms)
                    const forms = document.querySelectorAll('form[action^="/"]');
                    forms.forEach(f => {
                        try {
                            const url = new URL(f.action, location.origin);
                            if (!url.searchParams.has('token')) {
                                url.searchParams.set('token', token);
                                f.action = url.pathname + url.search + url.hash;
                            }
                        } catch(e) {}
                    });
                });
            } catch(e) {}
        })();
    </script>
</head>
<body>
    @yield('content')
</body>
</html>
