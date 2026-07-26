<div style="background:white; border:1px solid #e5e7eb; border-radius:0.75rem; padding:1rem; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
    <div class="stats-grid stats-grid-admin">
        @php
            $cardPalette = [
                'red' => ['bg' => '#fef2f2', 'border' => '#fecaca', 'iconBg' => '#fee2e2', 'iconColor' => '#b91c1c'],
                'orange' => ['bg' => '#fff7ed', 'border' => '#fdba74', 'iconBg' => '#ffedd5', 'iconColor' => '#c2410c'],
                'green' => ['bg' => '#f0fdf4', 'border' => '#86efac', 'iconBg' => '#dcfce7', 'iconColor' => '#15803d'],
                'blue' => ['bg' => '#eff6ff', 'border' => '#bfdbfe', 'iconBg' => '#dbeafe', 'iconColor' => '#1d4ed8'],
            ];
        @endphp
        @foreach($dashboardStats ?? [] as $stat)
        @php
            $statTone = $stat['tone'] ?? 'blue';
            $palette = $cardPalette[$statTone] ?? $cardPalette['blue'];
            $label = $stat['label'] ?? '';
        @endphp
        <div class="stat-card" style="background: {{ $palette['bg'] }}; border: 1px solid {{ $palette['border'] }}; border-radius: 0.75rem; padding: 1rem; display:flex; align-items:center; gap:0.9rem; min-height: 96px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
            <div class="stat-icon {{ $statTone }}" style="background: {{ $palette['iconBg'] }}; color: {{ $palette['iconColor'] }}; flex-shrink:0; border-radius: 9999px; width: 2.75rem; height: 2.75rem; display:flex; align-items:center; justify-content:center;">
                @if(in_array($label, ['Total Transaksi Pengeluaran Bulan Ini', 'Total Transaksi Bulan ini', 'Total Transaksi Bulan Ini']))
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                    </svg>
                @elseif($label === 'Obat Stok Menipis')
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                @elseif(in_array($label, ['Pasien Datang Bulan Ini', 'Pasien Bulan Ini']))
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                @elseif(in_array($label, ['Obat Akan Kadaluwarsa','Obat Akan Kadaluwarsa']))
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                @else
                    {!! $stat['icon'] ?? '•' !!}
                @endif
            </div>
            <div style="min-width:0;">
                <h3 style="margin:0 0 0.2rem 0; font-size: 1.05rem; color:#111827;">{{ $stat['label'] ?? '-' }}</h3>
                <div class="value" style="font-size: 1.5rem; font-weight: 600; color:#111827;">{{ $stat['value'] ?? '0' }}</div>
            </div>
        </div>
    @endforeach
    </div>
</div><br>


<div class="dashboard-charts charts-row two-col">
    <div class="chart-card">
        <h3>Grafik Pengeluaran Obat per Bulan</h3><br>
        <div style="margin-bottom: 15px; display: flex; gap: 10px; align-items: flex-end;">
            <div style="flex: 1; max-width: 150px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Tahun</label>
                <select id="filterChartYear" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @php
                        $chartYear = request('chart_year', now()->year);
                        $startYear = now()->year - 5;
                        $endYear = now()->year;
                    @endphp
                    @for($y = $startYear; $y <= $endYear; $y++)
                        <option value="{{ $y }}" {{ $y == $chartYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <canvas id="chartAdminUsage" height="140"></canvas>
    </div>

    <div class="info-section">
        <h2>Ringkasan Kedatangan Pasien</h2>
        <div style="margin-bottom: 15px; display: flex; gap: 10px; align-items: flex-end;">
            <div style="flex: 1; max-width: 200px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Cari Nama Pasien</label>
                <input type="text" id="searchPatient" placeholder="Ketik nama pasien..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div style="flex: 1; max-width: 150px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Bulan</label>
                <select id="filterMonth" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @php
                        $currentMonth = request('month', now()->month);
                        $currentYear = request('year', now()->year);
                    @endphp
                    @php
                        $monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    @endphp
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>
                            {{ $monthNames[$m - 1] }}
                        </option>
                    @endfor
                </select>
            </div>
            <div style="flex: 1; max-width: 150px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Tahun</label>
                <select id="filterYear" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @php
                        $startYear = now()->year - 5;
                        $endYear = now()->year;
                    @endphp
                    @for($y = $startYear; $y <= $endYear; $y++)
                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="table-scrollable">
            <table class="priority-table">
                <thead>
                    <tr>
                        <th>Nama Pasien</th>
                        <th>Jumlah Kedatangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patientVisitSummary ?? [] as $patient)
                        <tr>
                            <td>{{ $patient['nama_pasien'] ?? '-' }}</td>
                            <td>{{ $patient['jumlah_kedatangan'] ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="empty">Tidak ada data transaksi pengeluaran untuk bulan yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('filterMonth').addEventListener('change', applyFilter);
        document.getElementById('filterYear').addEventListener('change', applyFilter);
        document.getElementById('filterChartYear').addEventListener('change', applyChartFilter);
        document.getElementById('searchPatient').addEventListener('keyup', filterPatientTable);

        function applyFilter() {
            const month = document.getElementById('filterMonth').value;
            const year = document.getElementById('filterYear').value;
            updatePatientSummary(month, year);
        }

        function applyChartFilter() {
            const chartYear = document.getElementById('filterChartYear').value;
            updateUsageChart(chartYear);
        }

        function updatePatientSummary(month, year) {
            const url = new URL(window.location.href);
            url.searchParams.set('admin_month', month);
            url.searchParams.set('admin_year', year);

            fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (!Array.isArray(data.patientVisitSummary)) {
                        throw new Error('Invalid response data');
                    }
                    renderPatientTable(data.patientVisitSummary);
                })
                .catch(error => {
                    console.error('Error updating patient summary:', error);
                });
        }

        function renderPatientTable(patientVisitSummary) {
            const tbody = document.querySelector('.dashboard-charts.charts-row.two-col .info-section table tbody');
            if (!tbody) return;

            tbody.innerHTML = '';

            if (patientVisitSummary.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="2" class="empty">Tidak ada data transaksi pengeluaran untuk bulan yang dipilih.</td>';
                tbody.appendChild(tr);
                return;
            }

            patientVisitSummary.forEach(patient => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${patient.nama_pasien || '-'}</td>
                    <td>${patient.jumlah_kedatangan?.toString() ?? '0'}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function updateUsageChart(chartYear) {
            const url = new URL(window.location.href);
            url.searchParams.set('chart_year', chartYear);

            fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (!Array.isArray(data.chartMonths) || !Array.isArray(data.chartUsageData)) {
                        throw new Error('Invalid response data');
                    }
                    if (window.usageChart) {
                        window.usageChart.data.labels = data.chartMonths;
                        window.usageChart.data.datasets[0].data = data.chartUsageData;
                        window.usageChart.update();
                    }
                    window.history.replaceState(null, '', url.toString());
                })
                .catch(error => {
                    console.error('Error updating usage chart:', error);
                });
        }

        function filterPatientTable() {
            const searchValue = document.getElementById('searchPatient').value.toLowerCase();
            const table = document.querySelector('.dashboard-charts.charts-row.two-col .info-section table');
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const namaCell = row.querySelector('td:first-child');
                if (namaCell) {
                    const nama = namaCell.textContent.toLowerCase();
                    if (nama.includes(searchValue) || searchValue === '') {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
    </script>
</div>

<div class="dashboard-layout">
    <div class="info-section" style="max-width: 700px;">
        <h2>Obat Paling Banyak Dikeluarkan</h2>
        <div style="padding: 0.75rem 0;">
            <canvas id="topIssuedBarChart" height="160" style="max-height: 160px;"></canvas>
        </div>
    </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartMonths = {!! json_encode($chartMonths ?? []) !!};
    const chartUsageData = {!! json_encode($chartUsageData ?? []) !!};
    const topIssuedLabels = {!! json_encode($topIssuedObatLabels ?? []) !!};
    const topIssuedData = {!! json_encode($topIssuedObatData ?? []) !!};

    const usageCanvas = document.getElementById('chartAdminUsage');
    if (usageCanvas) {
        const usageContext = usageCanvas.getContext('2d');
            window.usageChart = new Chart(usageContext, {
                type: 'line',
                data: {
                    labels: chartMonths,
                    datasets: [{
                        label: 'Pengeluaran Obat',
                        data: chartUsageData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.12)',
                        tension: 0.3,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        }

        const topIssuedCanvas = document.getElementById('topIssuedBarChart');
        if (topIssuedCanvas) {
            const topIssuedContext = topIssuedCanvas.getContext('2d');
            new Chart(topIssuedContext, {
                type: 'bar',
                data: {
                    labels: topIssuedLabels,
                    datasets: [{
                        label: 'Jumlah Keluar',
                        data: topIssuedData,
                        backgroundColor: '#3b82f6',
                        borderColor: '#2563eb',
                        borderWidth: 1,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + Number(context.parsed.x).toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                callback: function(value) {
                                    return Number(value).toLocaleString('id-ID');
                                }
                            }
                        },
                        y: {
                            ticks: {
                                autoSkip: false,
                            }
                        }
                    }
                }
            });
        }
    </script>
