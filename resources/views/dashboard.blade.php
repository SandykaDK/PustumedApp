<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

            <div class="stats-grid">
                @foreach($dashboardStats ?? [] as $stat)
                    <div class="stat-card">
                        <div class="stat-icon {{ $stat['tone'] ?? 'blue' }}">{{ $stat['icon'] ?? '•' }}</div>
                        <h3>{{ $stat['label'] ?? '' }}</h3>
                        <div class="value">{{ $stat['value'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>

            @if(!empty($dashboardHighlights) || !empty($quickActions))
                <div class="dashboard-layout">
                    @if(!empty($dashboardHighlights))
                        <div class="info-section">
                            <h2>{{ $dashboardHighlightTitle ?? 'Fokus Hari Ini' }}</h2>
                            <div class="alert-list">
                                @foreach($dashboardHighlights as $highlight)
                                    <div class="alert-item">
                                        <div class="alert-item-header">
                                            <span class="alert-title">{{ $highlight['label'] ?? '' }}</span>
                                            <span class="alert-count">{{ $highlight['value'] ?? '' }}</span>
                                        </div>
                                        <p>{{ $highlight['description'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!empty($quickActions))
                        <div class="info-section">
                            <h2>Aksi Cepat</h2>
                            <div class="quick-action-grid">
                                @foreach($quickActions as $action)
                                    <a href="{{ $action['url'] ?? '#' }}" class="quick-action-card">
                                        <span class="quick-action-icon">{{ $action['icon'] ?? '•' }}</span>
                                        <span class="quick-action-text">{{ $action['label'] ?? '' }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="info-section">
                <h2>Informasi Akun</h2>
                <div class="info-item">
                    <span class="info-label">Nama</span>
                    <span class="info-value">{{ Auth::user()->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ Auth::user()->email }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Bergabung Sejak</span>
                    <span class="info-value">{{ Auth::user()->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
