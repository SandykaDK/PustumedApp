<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Dashboard - PustumedApp</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
</head>
<body>
    <!-- Sidebar Component -->
    <x-sidebar />

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Navbar Component -->
        <x-navbar />

        <!-- Main Content -->
        <div class="container main-content">
            <div class="welcome-section dashboard-hero {{ $dashboardAccentClass ?? '' }}">
                <div>
                    <h1>{{ $dashboardTitle ?? 'Dashboard' }}</h1>
                    <p>{{ $dashboardDescription ?? '' }}</p>
                </div>
                <div class="hero-badge">{{ $dashboardBadge ?? '' }}</div>
            </div>

            @if(($dashboardType ?? '') === 'petugas_obat')
                @include('dashboard.petugas_obat')
            @elseif(($dashboardType ?? '') === 'kepala_pustu')
                @include('dashboard.kepala_pustu')
            @elseif(($dashboardType ?? '') === 'petugas_administrasi')
                @include('dashboard.petugas_administrasi')
            @else
                <div class="info-section">
                    <h2>Overview</h2>
                    <p>Selamat datang di dashboard. Pilih role yang sesuai untuk melihat ringkasan spesifik.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
