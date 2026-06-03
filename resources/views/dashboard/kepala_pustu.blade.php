<div class="top-summary-row {{ ($dashboardType ?? '') === 'kepala_pustu' ? 'top-summary-row-kepala' : '' }}">
    <!-- Statistic Cards -->
    <div class="stats-grid {{ ($dashboardType ?? '') === 'kepala_pustu' ? 'stats-grid-kepala' : '' }}">
        <div class="stat-card">
            <div class="stat-icon orange">⏳</div>
            <h3>Akan Kadaluwarsa (&lt;6 bulan)</h3>
            <div class="value">{{ number_format($willExpireCount ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">⚠️</div>
            <h3>Sudah Kadaluwarsa</h3>
            <div class="value">{{ number_format($expiredCount ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">🧾</div>
            <h3>Transaksi Bulan Ini</h3>
            <div class="value">{{ number_format($transactionsThisMonth ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gray">🗑️</div>
            <h3>Pemusnahan Bulan Ini</h3>
            <div class="value">{{ number_format($destructionsThisMonth ?? 0) }}</div>
        </div>
    </div>

    <div class="info-section notifications">
        <h2>Notifikasi Penting</h2>
        <div class="notification-scroll">
            <div class="notification-stack">
                @foreach($notifications ?? [] as $notification)
                    <div class="notification-card {{ $notification['type'] ?? 'info' }}">
                        <span class="notification-chip">
                            {{ $notification['title'] ?? 'Notifikasi' }}
                        </span>
                        <strong>
                            {{ $notification['name'] ?? '-' }}
                        </strong>
                        <small>
                            @if(($notification['type'] ?? '') === 'danger' && !empty($notification['tanggal_kadaluwarsa']))
                                {{ \Carbon\Carbon::parse($notification['tanggal_kadaluwarsa'])->translatedFormat('d M Y') }}
                            @else
                                {{ $notification['description'] ?? '' }}
                            @endif
                        </small>
                    </div>
                @endforeach

                @if(($notifications ?? collect())->isEmpty())
                    <div class="notification-empty">
                        Tidak ada notifikasi penting saat ini.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="dashboard-charts">
    <div class="charts-row two-col">
        <div class="chart-card compact">
            <h4>Penerimaan Obat (12 bulan)</h4>
            <canvas id="chartReceipts" height="95"></canvas>
        </div>
        <div class="chart-card compact">
            <h4>Pengeluaran Obat (12 bulan)</h4>
            <canvas id="chartIssues" height="95"></canvas>
        </div>
    </div>
</div>

<div class="dashboard-layout">
    <div class="chart-card top-used-card">
        <h4>Obat Paling Sering Digunakan</h4>
        <canvas id="chartTopUsed" height="170"></canvas>
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
        <div class="info-item">
            <span class="info-label">Bergabung Sejak</span>
            <span class="info-value">{{ Auth::user()->created_at->locale('id')->translatedFormat('d F Y') }}</span>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const months = {!! json_encode($chartMonths ?? []) !!};
    const receipts = {!! json_encode($chartReceiptsData ?? []) !!};
    const issues = {!! json_encode($chartIssuesData ?? []) !!};
    const topLabels = {!! json_encode($topUsedLabels ?? []) !!};
    const topData = {!! json_encode($topUsedData ?? []) !!};

    const ctxR = document.getElementById('chartReceipts')?.getContext('2d');
    if (ctxR) new Chart(ctxR, {type: 'line', data: {labels: months, datasets:[{label:'Penerimaan', data: receipts, borderColor:'#22c55e', backgroundColor:'rgba(34,197,94,0.08)', tension:0.3}]}, options:{responsive:true}});

    const ctxI = document.getElementById('chartIssues')?.getContext('2d');
    if (ctxI) new Chart(ctxI, {type: 'line', data: {labels: months, datasets:[{label:'Pengeluaran', data: issues, borderColor:'#6366f1', backgroundColor:'rgba(99,102,241,0.08)', tension:0.3}]}, options:{responsive:true}});

    const ctxT = document.getElementById('chartTopUsed')?.getContext('2d');
    if (ctxT) new Chart(ctxT, {type: 'bar', data: {labels: topLabels, datasets:[{label:'Jumlah Keluar', data: topData, backgroundColor:'#fb923c'}]}, options:{indexAxis:'y', responsive:true}});
</script>
