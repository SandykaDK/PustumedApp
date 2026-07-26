<div class="petugas-obat-dashboard">
    <div style="display:flex; gap:1rem; align-items:flex-start; flex-wrap:wrap; margin-bottom:1rem;">
        <div class="pemusnahan-container" style="flex:1; min-width:320px; background: white; padding: 1rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
            <h2 style="margin-bottom: 1rem; margin-top: 0;">Status Pemusnahan Obat</h2>
            <div class="stats-grid petugas-stats-grid">
                <a href="{{ route('pemusnahan-obat.index', ['tab' => 'belum_diajukan']) }}" class="stat-card" style="text-decoration:none; color:inherit; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 0.75rem; padding: 1rem; display:flex; align-items:center; gap:0.9rem; min-height: 96px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                <div class="stat-icon gray" style="background: #e5e7eb; color: #374151; flex-shrink:0; border-radius: 9999px; width: 2.75rem; height: 2.75rem; display:flex; align-items:center; justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h6M7 11h6M7 15h6"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v4a1 1 0 0 0 1 1h4M5 21h14a2 2 0 0 0 2-2V7L14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/></svg>
                </div>
                <div style="min-width:0;">
                    <h3 style="margin:0 0 0.2rem 0; font-size: 0.96rem; color:#111827;">Belum Diajukan</h3>
                    <div class="value" style="font-size: 1.3rem; font-weight: 600; color:#111827;">{{ number_format($pemusnahanCounts['belum_diajukan'] ?? 0) }}</div>
                </div>
            </a>
            <a href="{{ route('pemusnahan-obat.index', ['tab' => 'sudah_diajukan']) }}" class="stat-card" style="text-decoration:none; color:inherit; background: #fff7ed; border: 1px solid #fdba74; border-radius: 0.75rem; padding: 1rem; display:flex; align-items:center; gap:0.9rem; min-height: 96px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                <div class="stat-icon orange" style="background: #ffedd5; color: #c2410c; flex-shrink:0; border-radius: 9999px; width: 2.75rem; height: 2.75rem; display:flex; align-items:center; justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg>
                </div>
                <div style="min-width:0;">
                    <h3 style="margin:0 0 0.2rem 0; font-size: 0.96rem; color:#111827;">Sudah Diajukan</h3>
                    <div class="value" style="font-size: 1.3rem; font-weight: 600; color:#111827;">{{ number_format($pemusnahanCounts['sudah_diajukan'] ?? 0) }}</div>
                </div>
            </a>
            <a href="{{ route('pemusnahan-obat.index', ['tab' => 'sudah_disetujui']) }}" class="stat-card" style="text-decoration:none; color:inherit; background: #f0fdf4; border: 1px solid #86efac; border-radius: 0.75rem; padding: 1rem; display:flex; align-items:center; gap:0.9rem; min-height: 96px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                <div class="stat-icon green" style="background: #dcfce7; color: #15803d; flex-shrink:0; border-radius: 9999px; width: 2.75rem; height: 2.75rem; display:flex; align-items:center; justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg>
                </div>
                <div style="min-width:0;">
                    <h3 style="margin:0 0 0.2rem 0; font-size: 0.96rem; color:#111827;">Sudah Disetujui</h3>
                    <div class="value" style="font-size: 1.3rem; font-weight: 600; color:#111827;">{{ number_format($pemusnahanCounts['sudah_disetujui'] ?? 0) }}</div>
                </div>
            </a>
            <a href="{{ route('pemusnahan-obat.index', ['tab' => 'sudah_dimusnahkan']) }}" class="stat-card" style="text-decoration:none; color:inherit; background: #f5f3ff; border: 1px solid #c4b5fd; border-radius: 0.75rem; padding: 1rem; display:flex; align-items:center; gap:0.9rem; min-height: 96px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                <div class="stat-icon purple" style="background: #ede9fe; color: #6d28d9; flex-shrink:0; border-radius: 9999px; width: 2.75rem; height: 2.75rem; display:flex; align-items:center; justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6M14 11v6"/></svg>
                </div>
                <div style="min-width:0;">
                    <h3 style="margin:0 0 0.2rem 0; font-size: 0.96rem; color:#111827;">Pemusnahan Bulan Ini</h3>
                    <div class="value" style="font-size: 1.3rem; font-weight: 600; color:#111827;">{{ number_format($pemusnahanCounts['bulan_ini'] ?? 0) }}</div>
                </div>
            </a>
            </div>
        </div>

        <div style="flex:1; min-width:320px; background:white; padding:1rem; border-radius:0.75rem; box-shadow:0 1px 3px rgba(0,0,0,0.1); border:1px solid #e5e7eb;">
            <h2 style="margin-bottom: 1rem; margin-top: 0;">Ringkasan Stok</h2>
            <div class="stats-grid petugas-stats-grid">
                <div class="stat-card" style="text-decoration:none; color:inherit; background: #fff7ed; border: 1px solid #fdba74; border-radius: 0.75rem; padding: 1rem; display:flex; align-items:center; gap:0.9rem; min-height: 96px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    <div class="stat-icon orange" style="background: #ffedd5; color: #c2410c; flex-shrink:0; border-radius: 9999px; width: 2.75rem; height: 2.75rem; display:flex; align-items:center; justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                    </div>
                    <div style="min-width:0;">
                        <h3 style="margin:0 0 0.2rem 0; font-size: 0.96rem; color:#111827;">Obat Akan Kadaluwarsa</h3>
                        <div class="value" style="font-size: 1.3rem; font-weight: 600; color:#111827;">{{ number_format($willExpireCount ?? 0) }}</div>
                    </div>
                </div>
                <div class="stat-card" style="text-decoration:none; color:inherit; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1rem; display:flex; align-items:center; gap:0.9rem; min-height: 96px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    <div class="stat-icon red" style="background: #fee2e2; color: #b91c1c; flex-shrink:0; border-radius: 9999px; width: 2.75rem; height: 2.75rem; display:flex; align-items:center; justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6 9 12.75l4.286-4.286a11.948 11.948 0 0 1 4.306 6.43l.776 2.898m0 0 3.182-5.511m-3.182 5.51-5.511-3.181" /></svg>
                    </div>
                    <div style="min-width:0;">
                        <h3 style="margin:0 0 0.2rem 0; font-size: 0.96rem; color:#111827;">Obat Menipis</h3>
                        <div class="value" style="font-size: 1.3rem; font-weight: 600; color:#111827;">{{ number_format($lowStockCount ?? 0) }}</div>
                    </div>
                </div>
                <div class="stat-card" style="text-decoration:none; color:inherit; background: #fef2f2; border: 1px solid #fca5a5; border-radius: 0.75rem; padding: 1rem; display:flex; align-items:center; gap:0.9rem; min-height: 96px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    <div class="stat-icon red" style="background: #fee2e2; color: #dc2626; flex-shrink:0; border-radius: 9999px; width: 2.75rem; height: 2.75rem; display:flex; align-items:center; justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                    </div>
                    <div style="min-width:0;">
                        <h3 style="margin:0 0 0.2rem 0; font-size: 0.96rem; color:#111827;">Obat Habis</h3>
                        <div class="value" style="font-size: 1.3rem; font-weight: 600; color:#111827;">{{ number_format($outOfStockCount ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-charts">
        <div class="charts-row two-col">
            <div class="chart-card compact">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="margin: 0;">Penerimaan Obat per Bulan</h3>
                    <select id="yearFilterReceipts" style="padding: 0.5rem; border-radius: 0.25rem; border: 1px solid #d1d5db;">
                        @php
                            $currentYear = now()->year;
                            for ($year = $currentYear; $year >= $currentYear - 5; $year--) {
                                $selected = ($year === $currentYear) ? 'selected' : '';
                                echo "<option value=\"$year\" $selected>$year</option>";
                            }
                        @endphp
                    </select>
                </div>
                <canvas id="chartReceipts" height="95"></canvas>
            </div>
            <div class="chart-card compact">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h4 style="margin: 0;">Pengeluaran Obat per Bulan</h4>
                    <select id="yearFilterIssues" style="padding: 0.5rem; border-radius: 0.25rem; border: 1px solid #d1d5db;">
                        @php
                            $currentYear = now()->year;
                            for ($year = $currentYear; $year >= $currentYear - 5; $year--) {
                                $selected = ($year === $currentYear) ? 'selected' : '';
                                echo "<option value=\"$year\" $selected>$year</option>";
                            }
                        @endphp
                    </select>
                </div>
                <canvas id="chartIssues" height="95"></canvas>
            </div>
        </div>
    </div>

    <!-- Removed recent transactions cards: 5 recent receipts and 5 recent issues (not needed) -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const monthsIndonesian = {
            'January': 'Januari',
            'February': 'Februari',
            'March': 'Maret',
            'April': 'April',
            'May': 'Mei',
            'June': 'Juni',
            'July': 'Juli',
            'August': 'Agustus',
            'September': 'September',
            'October': 'Oktober',
            'November': 'November',
            'December': 'Desember'
        };

        const convertToIndonesianMonths = (monthsArray) => {
            return monthsArray.map(month => {
                // Handle format like "May 2026"
                const parts = month.split(' ');
                if (parts.length === 2 && monthsIndonesian[parts[0]]) {
                    return monthsIndonesian[parts[0]] + ' ' + parts[1];
                }
                return month;
            });
        };

        // Separate data for receipts and issues charts
        let receiptsMonths = convertToIndonesianMonths({!! json_encode($chartMonths ?? []) !!});
        let receipts = {!! json_encode($chartReceiptsData ?? []) !!};

        let issuesMonths = convertToIndonesianMonths({!! json_encode($chartMonths ?? []) !!});
        let issues = {!! json_encode($chartIssuesData ?? []) !!};

        let receiptsChart = null;
        let issuesChart = null;
        let sharedYMax = 1;

        const refreshSharedYMax = () => {
            const values = [...receipts, ...issues].filter(value => Number.isFinite(value));
            const maxValue = values.length > 0 ? Math.max(...values) : 0;
            sharedYMax = maxValue > 0 ? Math.ceil(maxValue * 1.1) : 1;
        };

        const initReceiptsChart = () => {
            refreshSharedYMax();
            const receiptsCanvas = document.getElementById('chartReceipts');
            if (receiptsCanvas) {
                if (receiptsChart) {
                    receiptsChart.destroy();
                }
                const receiptsContext = receiptsCanvas.getContext('2d');
                receiptsChart = new Chart(receiptsContext, {
                    type: 'line',
                    data: {
                        labels: receiptsMonths,
                        datasets: [{
                            label: 'Penerimaan',
                            data: receipts,
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22, 163, 74, 0.08)',
                            tension: 0.3,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, max: sharedYMax, ticks: { precision: 0 } }
                        }
                    }
                });
            }
        };

        const initIssuesChart = () => {
            refreshSharedYMax();
            const issuesCanvas = document.getElementById('chartIssues');
            if (issuesCanvas) {
                if (issuesChart) {
                    issuesChart.destroy();
                }
                const issuesContext = issuesCanvas.getContext('2d');
                issuesChart = new Chart(issuesContext, {
                    type: 'line',
                    data: {
                        labels: issuesMonths,
                        datasets: [{
                            label: 'Pengeluaran',
                            data: issues,
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.08)',
                            tension: 0.3,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, max: sharedYMax, ticks: { precision: 0 } }
                        }
                    }
                });
            }
        };

        initReceiptsChart();
        initIssuesChart();

        // Handle year filter for receipts chart
        const yearFilterReceipts = document.getElementById('yearFilterReceipts');
        if (yearFilterReceipts) {
            yearFilterReceipts.addEventListener('change', function() {
                const year = this.value;
                const url = new URL(window.location.href);
                url.searchParams.set('year_receipts', year);

                fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.chartMonths && data.chartReceiptsData) {
                        receiptsMonths = convertToIndonesianMonths(data.chartMonths);
                        receipts = data.chartReceiptsData;
                        initReceiptsChart();
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        }

        // Handle year filter for issues chart
        const yearFilterIssues = document.getElementById('yearFilterIssues');
        if (yearFilterIssues) {
            yearFilterIssues.addEventListener('change', function() {
                const year = this.value;
                const url = new URL(window.location.href);
                url.searchParams.set('year_issues', year);

                fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.chartMonths && data.chartIssuesData) {
                        issuesMonths = convertToIndonesianMonths(data.chartMonths);
                        issues = data.chartIssuesData;
                        initIssuesChart();
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        }
    </script>
</div>
