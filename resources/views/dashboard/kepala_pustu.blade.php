<div class="top-summary-row {{ ($dashboardType ?? '') === 'kepala_pustu' ? 'top-summary-row-kepala' : '' }}">
    <!-- Statistic Cards -->
    <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <h2 style="margin-bottom: 1rem; margin-top: 0;">Status Pemusnahan Obat</h2>

        <div class="stats-grid {{ ($dashboardType ?? '') === 'kepala_pustu' ? 'stats-grid-kepala' : '' }}">
            <a href="{{ route('pemusnahan-obat.index', ['tab' => 'sudah_dikonfirmasi']) }}" class="stat-card" style="text-decoration:none; color:inherit;">
                <div class="stat-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3>Sudah Approve</h3>
                <div class="value">{{ number_format(data_get($dashboardHighlights[2], 'value', 0)) }}</div>
            </a>
            <a href="{{ route('pemusnahan-obat.index', ['tab' => 'belum_dikonfirmasi']) }}" class="stat-card" style="text-decoration:none; color:inherit;">
                <div class="stat-icon purple">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3>Belum Approve</h3>
                <div class="value">{{ number_format(data_get($dashboardHighlights[3], 'value', 0)) }}</div>
            </a>
        </div>
    </div>

    <div class="info-section notifications">
        <h2>Notifikasi</h2>
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
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:0;">
                    <label for="chart_year_receipts" style="font-size:0.95rem; color:#374151; margin-bottom:0;">Tahun:</label>
                    <select id="chart_year_receipts" style="padding:8px 10px; border:1px solid #d1d5db; border-radius:8px; min-width:120px; background:#fff; color:#111827; cursor:pointer;">
                        @foreach($chartYearOptions ?? [] as $year)
                            <option value="{{ $year }}" {{ (int)($chartReceiptsYear ?? now()->year) === $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <canvas id="chartReceipts" height="95"></canvas>
        </div>
        <div class="chart-card compact">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; gap:10px; flex-wrap:wrap;">
                <h4 style="margin:0;">Pengeluaran Obat (12 bulan)</h4>
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:0;">
                    <label for="chart_year_issues" style="font-size:0.95rem; color:#374151; margin-bottom:0;">Tahun:</label>
                    <select id="chart_year_issues" style="padding:8px 10px; border:1px solid #d1d5db; border-radius:8px; min-width:120px; background:#fff; color:#111827; cursor:pointer;">
                        @foreach($chartYearOptions ?? [] as $year)
                            <option value="{{ $year }}" {{ (int)($chartIssuesYear ?? now()->year) === $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <canvas id="chartIssues" height="95"></canvas>
        </div>
    </div>
</div>

<div class="dashboard-layout">
    <div class="chart-card top-used-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; gap:10px; flex-wrap:wrap;">
            <h3 style="margin:0;">Obat Paling Sering Digunakan</h3>
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:0;">
                <label for="chart_year_topused" style="font-size:0.95rem; color:#374151; margin-bottom:0;">Tahun:</label>
                <select id="chart_year_topused" style="padding:8px 10px; border:1px solid #d1d5db; border-radius:8px; min-width:120px; background:#fff; color:#111827; cursor:pointer;">
                    @foreach($chartYearOptions ?? [] as $year)
                        <option value="{{ $year }}" {{ (int)($chartTopUsedYear ?? now()->year) === $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <canvas id="chartTopUsed" height="170"></canvas>
    </div>

    <div class="info-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; gap:10px; flex-wrap:wrap;">
            <h2 style="margin:0;">Klasifikasi Fast / Slow Moving</h2>
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:0;">
                <label for="chart_year_fastslow" style="font-size:0.95rem; color:#374151; margin-bottom:0;">Tahun:</label>
                <select id="chart_year_fastslow" style="padding:8px 10px; border:1px solid #d1d5db; border-radius:8px; min-width:120px; background:#fff; color:#111827; cursor:pointer;">
                    @foreach($chartYearOptions ?? [] as $year)
                        <option value="{{ $year }}" {{ (int)($chartFastSlowYear ?? now()->year) === $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
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

    const fixedChartMax = Math.max(...receipts, ...issues, 10);
    const fixedChartStep = Math.ceil(fixedChartMax / 5) || 1;

    let chartReceipts, chartIssues, chartTopUsed;

    // Initialize charts
    function initCharts() {
        const ctxR = document.getElementById('chartReceipts')?.getContext('2d');
        if (ctxR) {
            if (chartReceipts) chartReceipts.destroy();
            chartReceipts = new Chart(ctxR, {
                type: 'line',
                data: {
                    labels: receiptsMonths,
                    datasets:[{
                        label:'Penerimaan',
                        data: receipts,
                        borderColor:'#22c55e',
                        backgroundColor:'rgba(34,197,94,0.08)',
                        tension:0.3
                    }]
                },
                options:{
                    responsive:true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: fixedChartMax,
                            ticks: {
                                stepSize: fixedChartStep
                            }
                        }
                    }
                }
            });
        }

        const ctxI = document.getElementById('chartIssues')?.getContext('2d');
        if (ctxI) {
            if (chartIssues) chartIssues.destroy();
            chartIssues = new Chart(ctxI, {
                type: 'line',
                data: {
                    labels: issuesMonths,
                    datasets:[{
                        label:'Pengeluaran',
                        data: issues,
                        borderColor:'#6366f1',
                        backgroundColor:'rgba(99,102,241,0.08)',
                        tension:0.3
                    }]
                },
                options:{
                    responsive:true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: fixedChartMax,
                            ticks: {
                                stepSize: fixedChartStep
                            }
                        }
                    }
                }
            });
        }

        const ctxT = document.getElementById('chartTopUsed')?.getContext('2d');
        if (ctxT) {
            if (chartTopUsed) chartTopUsed.destroy();
            chartTopUsed = new Chart(ctxT, {
                type: 'pie',
                data: {
                    labels: topLabels,
                    datasets:[{
                        label:'Jumlah Keluar',
                        data: topData,
                        backgroundColor: topLabels.map((_, idx) => [
                            '#fb923c', '#f97316', '#f59e0b', '#3b82f6', '#22c55e', '#8b5cf6', '#ec4899', '#14b8a6', '#f43f5e', '#6366f1'
                        ][idx % 10]),
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options:{
                    responsive:true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 12
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    return label + ': ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    initCharts();

    // Handle form submissions with AJAX
    function handleChartFilter(selectId, paramName) {
        const selectElement = document.getElementById(selectId);
        if (!selectElement) return;

        selectElement.addEventListener('change', function() {
            const year = this.value;
            console.log('Filter changed:', paramName, '=', year);

            // Build proper URL with query parameter
            const url = new URL(window.location.href);
            url.searchParams.set(paramName, year);
            const fetchUrl = url.toString();

            console.log('Fetching from:', fetchUrl);

            fetch(fetchUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);

                // Update chart data based on which filter changed
                if (paramName === 'chart_year_receipts' && data.chartReceiptsMonths) {
                    console.log('Updating receipts chart');
                    receiptsMonths.length = 0;
                    receiptsMonths.push(...data.chartReceiptsMonths);
                    receipts.length = 0;
                    receipts.push(...data.chartReceiptsData);
                    if (chartReceipts) chartReceipts.update();
                }

                if (paramName === 'chart_year_issues' && data.chartIssuesMonths) {
                    console.log('Updating issues chart');
                    issuesMonths.length = 0;
                    issuesMonths.push(...data.chartIssuesMonths);
                    issues.length = 0;
                    issues.push(...data.chartIssuesData);
                    if (chartIssues) chartIssues.update();
                }

                if (paramName === 'chart_year_topused' && data.topUsedLabels) {
                    console.log('Updating top used chart');
                    topLabels.length = 0;
                    topLabels.push(...data.topUsedLabels);
                    topData.length = 0;
                    topData.push(...data.topUsedData);
                    if (chartTopUsed) chartTopUsed.update();
                }

                if (paramName === 'chart_year_fastslow' && data.fastMoving) {
                    console.log('Updating fast/slow table');
                    updateFastSlowTable(data.fastMoving, data.slowMoving);
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                alert('Terjadi kesalahan saat memperbarui data');
            });
        });
    }

    function updateFastSlowTable(fastMoving, slowMoving) {
        const tbody = document.querySelector('.priority-table tbody');
        if (!tbody) return;

        const maxRows = Math.max(fastMoving.length, slowMoving.length);
        tbody.innerHTML = '';

        for (let i = 0; i < maxRows; i++) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${fastMoving[i]?.nama_obat?.nama_obat ?? '—'}</td>
                <td>${fastMoving[i] ? fastMoving[i].total.toLocaleString('id-ID') : '—'}</td>
                <td>${slowMoving[i]?.nama_obat?.nama_obat ?? '—'}</td>
                <td>${slowMoving[i] ? slowMoving[i].total.toLocaleString('id-ID') : '—'}</td>
            `;
            tbody.appendChild(tr);
        }
    }

    // Setup event listeners for all chart filters
    handleChartFilter('chart_year_receipts', 'chart_year_receipts');
    handleChartFilter('chart_year_issues', 'chart_year_issues');
    handleChartFilter('chart_year_topused', 'chart_year_topused');
    handleChartFilter('chart_year_fastslow', 'chart_year_fastslow');
</script>
