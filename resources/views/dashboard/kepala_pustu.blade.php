<div class="top-summary-row {{ ($dashboardType ?? '') === 'kepala_pustu' ? 'top-summary-row-kepala' : '' }}">
    <!-- Statistic Cards -->
    <div>
        <div class="stats-grid {{ ($dashboardType ?? '') === 'kepala_pustu' ? 'stats-grid-kepala' : '' }}">
            <a href="{{ route('pemusnahan-obat.index', ['tab' => 'sudah_disetujui']) }}" class="stat-card" style="text-decoration:none; color:inherit;">
                <div class="stat-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3>Pemusnahan Sudah Dikonfirmasi</h3>
                <div class="value">{{ number_format(data_get($dashboardHighlights[2], 'value', 0)) }}</div>
            </a>
            <a href="{{ route('pemusnahan-obat.index', ['tab' => 'sudah_dimusnahkan']) }}" class="stat-card" style="text-decoration:none; color:inherit;">
                <div class="stat-icon red">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </div>
                <h3>Pemusnahan Sudah Diproses</h3>
                <div class="value">{{ number_format($destructionsThisMonth ?? 0) }}</div>
            </a>
        </div>

        <div class="stats-grid {{ ($dashboardType ?? '') === 'kepala_pustu' ? 'stats-grid-kepala' : '' }}" style="margin-top: 12px;">
            <a href="{{ route('pemusnahan-obat.index', ['tab' => 'sudah_diajukan']) }}" class="stat-card" style="grid-column: 1 / -1; text-decoration:none; color:inherit;">
                <div class="stat-icon purple">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3>Pemusnahan Belum Dikonfirmasi</h3>
                <div class="value">{{ number_format(data_get($dashboardHighlights[3], 'value', 0)) }}</div>
            </a>
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
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; gap:10px; flex-wrap:wrap;">
                <h4 style="margin:0;">Penerimaan Obat (12 bulan)</h4>
                <form method="GET" action="" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:0;">
                    <label for="chart_year_receipts" style="font-size:0.95rem; color:#374151; margin-bottom:0;">Tahun:</label>
                    <select id="chart_year_receipts" name="chart_year_receipts" onchange="this.form.submit()" style="padding:8px 10px; border:1px solid #d1d5db; border-radius:8px; min-width:120px; background:#fff; color:#111827;">
                        @foreach($chartYearOptions ?? [] as $year)
                            <option value="{{ $year }}" {{ (int)($chartReceiptsYear ?? now()->year) === $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="chart_year_issues" value="{{ $chartIssuesYear ?? now()->year }}" />
                    <input type="hidden" name="chart_year_topused" value="{{ $chartTopUsedYear ?? now()->year }}" />
                    <input type="hidden" name="chart_year_fastslow" value="{{ $chartFastSlowYear ?? now()->year }}" />
                </form>
            </div>
            <canvas id="chartReceipts" height="95"></canvas>
        </div>
        <div class="chart-card compact">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; gap:10px; flex-wrap:wrap;">
                <h4 style="margin:0;">Pengeluaran Obat (12 bulan)</h4>
                <form method="GET" action="" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:0;">
                    <label for="chart_year_issues" style="font-size:0.95rem; color:#374151; margin-bottom:0;">Tahun:</label>
                    <select id="chart_year_issues" name="chart_year_issues" onchange="this.form.submit()" style="padding:8px 10px; border:1px solid #d1d5db; border-radius:8px; min-width:120px; background:#fff; color:#111827;">
                        @foreach($chartYearOptions ?? [] as $year)
                            <option value="{{ $year }}" {{ (int)($chartIssuesYear ?? now()->year) === $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="chart_year_receipts" value="{{ $chartReceiptsYear ?? now()->year }}" />
                    <input type="hidden" name="chart_year_topused" value="{{ $chartTopUsedYear ?? now()->year }}" />
                    <input type="hidden" name="chart_year_fastslow" value="{{ $chartFastSlowYear ?? now()->year }}" />
                </form>
            </div>
            <canvas id="chartIssues" height="95"></canvas>
        </div>
    </div>
</div>

<div class="dashboard-layout">
    <div class="chart-card top-used-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; gap:10px; flex-wrap:wrap;">
            <h3 style="margin:0;">Obat Paling Sering Digunakan</h3>
            <form method="GET" action="" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:0;">
                <label for="chart_year_topused" style="font-size:0.95rem; color:#374151; margin-bottom:0;">Tahun:</label>
                <select id="chart_year_topused" name="chart_year_topused" onchange="this.form.submit()" style="padding:8px 10px; border:1px solid #d1d5db; border-radius:8px; min-width:120px; background:#fff; color:#111827;">
                    @foreach($chartYearOptions ?? [] as $year)
                        <option value="{{ $year }}" {{ (int)($chartTopUsedYear ?? now()->year) === $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="chart_year_receipts" value="{{ $chartReceiptsYear ?? now()->year }}" />
                <input type="hidden" name="chart_year_issues" value="{{ $chartIssuesYear ?? now()->year }}" />
                <input type="hidden" name="chart_year_fastslow" value="{{ $chartFastSlowYear ?? now()->year }}" />
            </form>
        </div>
        <canvas id="chartTopUsed" height="170"></canvas>
    </div>

    <div class="info-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; gap:10px; flex-wrap:wrap;">
            <h2 style="margin:0;">Klasifikasi Fast / Slow Moving</h2>
            <form method="GET" action="" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:0;">
                <label for="chart_year_fastslow" style="font-size:0.95rem; color:#374151; margin-bottom:0;">Tahun:</label>
                <select id="chart_year_fastslow" name="chart_year_fastslow" onchange="this.form.submit()" style="padding:8px 10px; border:1px solid #d1d5db; border-radius:8px; min-width:120px; background:#fff; color:#111827;">
                    @foreach($chartYearOptions ?? [] as $year)
                        <option value="{{ $year }}" {{ (int)($chartFastSlowYear ?? now()->year) === $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="chart_year_receipts" value="{{ $chartReceiptsYear ?? now()->year }}" />
                <input type="hidden" name="chart_year_issues" value="{{ $chartIssuesYear ?? now()->year }}" />
                <input type="hidden" name="chart_year_topused" value="{{ $chartTopUsedYear ?? now()->year }}" />
            </form>
        </div>
        <p style="margin-bottom: 12px; color: #6b7280;">Berdasarkan jumlah obat yang keluar selama 12 bulan.</p>
        <div class="table-scrollable">
            <table class="priority-table">
                <thead>
                    <tr>
                        <th>Fast Moving</th>
                        <th>Keluar</th>
                        <th>Slow Moving</th>
                        <th>Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rows = max(count($fastMoving ?? []), count($slowMoving ?? [])); @endphp
                    @for($i = 0; $i < $rows; $i++)
                        <tr>
                            <td>{{ $fastMoving[$i]->namaObat?->nama_obat ?? '-' }}</td>
                            <td>{{ isset($fastMoving[$i]) ? number_format($fastMoving[$i]->total) : '-' }}</td>
                            <td>{{ $slowMoving[$i]->namaObat?->nama_obat ?? '-' }}</td>
                            <td>{{ isset($slowMoving[$i]) ? number_format($slowMoving[$i]->total) : '-' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    {{-- <div class="info-section">
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
    </div> --}}
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const receiptsMonths = {!! json_encode($chartReceiptsMonths ?? []) !!};
    const issuesMonths = {!! json_encode($chartIssuesMonths ?? []) !!};
    const receipts = {!! json_encode($chartReceiptsData ?? []) !!};
    const issues = {!! json_encode($chartIssuesData ?? []) !!};
    const topLabels = {!! json_encode($topUsedLabels ?? []) !!};
    const topData = {!! json_encode($topUsedData ?? []) !!};

    const ctxR = document.getElementById('chartReceipts')?.getContext('2d');
    if (ctxR) new Chart(ctxR, {type: 'line', data: {labels: receiptsMonths, datasets:[{label:'Penerimaan', data: receipts, borderColor:'#22c55e', backgroundColor:'rgba(34,197,94,0.08)', tension:0.3}]}, options:{responsive:true}});

    const ctxI = document.getElementById('chartIssues')?.getContext('2d');
    if (ctxI) new Chart(ctxI, {type: 'line', data: {labels: issuesMonths, datasets:[{label:'Pengeluaran', data: issues, borderColor:'#6366f1', backgroundColor:'rgba(99,102,241,0.08)', tension:0.3}]}, options:{responsive:true}});

    const ctxT = document.getElementById('chartTopUsed')?.getContext('2d');
    if (ctxT) new Chart(ctxT, {type: 'bar', data: {labels: topLabels, datasets:[{label:'Jumlah Keluar', data: topData, backgroundColor:'#fb923c'}]}, options:{indexAxis:'y', responsive:true}});
</script>
