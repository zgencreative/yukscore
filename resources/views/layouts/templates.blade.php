@php
    $host = request()->getHost();
    $title = 'YUKSCORE - Ikuti live score dan jadwal pertandingan sepak bola dari seluruh dunia hanya di YukScore. Update real-time untuk semua liga top dan turnamen besar!';
    $logo = asset('img/logo.png');
    $ico  = asset('img/logo.ico');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Ikuti live score dan jadwal pertandingan sepak bola dari seluruh dunia hanya di Mansion Sports FC. Update real-time untuk semua liga top dan turnamen besar!">
    <meta name="title" content="Live Score - Mansion Sports">
    <meta content="Live Score - Mansion Sports" property="og:site_name" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    <link rel="icon" href="{{ $ico }}" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @yield('extra_head')
</head>
<body>
    <form method="POST" action="{{ route('logout') }}" class="d-none" id="global-logout-form">
        @csrf
    </form>
    @include('partials.navbar', ['logo' => $logo])

    <section>
        @yield('content')
    </section>

    <footer class="text-light py-4">
        <center>
            <a href="/" class="text-white" style="text-decoration: none">
                <p>YukScore</p>
            </a>
        </center>
    </footer>

    @yield('extra_js')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @include('partials.login-modal')

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Live Score - YukSports",
        "url": "https://score.yuksports.com/"
    }
    </script>
</body>
</html>