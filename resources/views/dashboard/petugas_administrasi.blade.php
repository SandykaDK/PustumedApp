<div class="top-summary-row {{ ($dashboardType ?? '') === 'petugas_administrasi' ? 'top-summary-row-kepala' : '' }}">
    <div class="stats-grid">
        @foreach($dashboardStats ?? [] as $stat)
            <div class="stat-card">
                <div class="stat-icon {{ $stat['tone'] ?? 'blue' }}">{{ $stat['icon'] ?? '•' }}</div>
                <h3>{{ $stat['label'] ?? '-' }}</h3>
                <div class="value">{{ $stat['value'] ?? '0' }}</div>
            </div>
        @endforeach
    </div>

    <div class="info-section">
        <h2>Ringkasan</h2>
        <div class="dashboard-highlights">
            @foreach($dashboardHighlights ?? [] as $h)
                <div class="alert-item">
                    <div class="alert-item-header">
                        <div class="alert-title">{{ $h['label'] ?? '-' }}</div>
                        <div class="alert-count">{{ $h['value'] ?? 0 }}</div>
                    </div>
                    <p>{{ $h['description'] ?? '' }}</p>
                </div>
            @endforeach
            @if(empty($dashboardHighlights))
                <div class="notification-empty">Tidak ada ringkasan tambahan.</div>
            @endif
        </div>
    </div>
</div>

<div class="dashboard-layout">
    <div class="info-section">
        <h2>Aksi Cepat</h2>
        <div class="quick-action-grid">
            @foreach($quickActions ?? [] as $action)
                <a href="{{ $action['url'] ?? '#' }}" class="quick-action-card">
                    <div class="quick-action-icon">{{ $action['icon'] ?? '⚡' }}</div>
                    <div class="quick-action-text">{{ $action['label'] ?? 'Aksi' }}</div>
                </a>
            @endforeach
            @if(empty($quickActions))
                <div class="notification-empty">Tidak ada aksi cepat tersedia.</div>
            @endif
        </div>
    </div>

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
    </div>
</div>
