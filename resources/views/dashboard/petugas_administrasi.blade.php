<div class="stats-grid stats-grid-admin">
    @foreach($dashboardStats ?? [] as $stat)
        <div class="stat-card">
                        <div class="stat-icon {{ $stat['tone'] ?? 'blue' }}">
                                @php $label = $stat['label'] ?? '' @endphp
                                @if($label === 'Total Transaksi Pengeluaran Hari Ini')
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                        </svg>
                                @elseif(in_array($label, ['Total Transaksi Pengeluaran Bulan Ini', 'Total Transaksi Bulan ini', 'Total Transaksi Bulan Ini']))
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                                        </svg>
                                @elseif($label === 'Obat Stok Menipis')
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                        </svg>
                                @elseif(in_array($label, ['Obat Akan Kadaluwarsa','Obat Akan Kadaluarsa']))
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                        </svg>
                                @else
                                        {!! $stat['icon'] ?? '•' !!}
                                @endif
                        </div>
            <h3>{{ $stat['label'] ?? '-' }}</h3>
            <div class="value">{{ $stat['value'] ?? '0' }}</div>
        </div>
    @endforeach
</div>

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
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(2024, $m, 1)->translatedFormat('F') }}
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
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('month', month);
            currentUrl.searchParams.set('year', year);
            window.location.href = currentUrl.toString();
        }

        function applyChartFilter() {
            const chartYear = document.getElementById('filterChartYear').value;
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('chart_year', chartYear);
            window.location.href = currentUrl.toString();
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
    <div class="info-section">
        <h2>Obat Paling Banyak Dikeluarkan</h2>
        <div class="table-scrollable">
            <table class="priority-table">
                <thead>
                    <tr>
                        <th>Nama Obat</th>
                        <th>Jumlah Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topIssuedObat ?? [] as $item)
                        <tr>
                            <td>{{ $item['nama_obat'] }}</td>
                            <td>{{ number_format($item['jumlah_keluar']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="empty">Tidak ada data obat yang keluar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="info-section">
        <h2>Transaksi Pengeluaran Terbaru</h2>
        <div class="table-scrollable">
            <table class="priority-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Obat</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentIssues ?? [] as $issue)
                        <tr>
                            <td>{{ $issue['tanggal'] }}</td>
                            <td>{{ $issue['items'] }}</td>
                            <td>{{ number_format($issue['jumlah']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty">Tidak ada transaksi pengeluaran terbaru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="dashboard-layout">
    <div class="info-section">
        <h2>Notifikasi Stok</h2>
        <div class="table-scrollable">
            <table class="priority-table">
                <thead>
                    <tr>
                        <th>Nama Obat</th>
                        <th>Stok Saat Ini</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockAlerts ?? [] as $item)
                        @php
                            $statusClass = match($item['status'] ?? '') {
                                'Habis' => 'danger',
                                'Hampir Habis' => 'warning',
                                default => 'success',
                            };
                        @endphp
                        <tr>
                            <td>{{ $item['nama_obat'] }}</td>
                            <td>{{ number_format($item['stok']) }}</td>
                            <td><span class="priority-status {{ $statusClass }}">{{ $item['status'] }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty">Semua stok obat dalam kondisi aman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="info-section">
        <h2>Obat Mendekati Kadaluarsa</h2>
        <div class="table-scrollable">
            <table class="priority-table">
                <thead>
                    <tr>
                        <th>Nama Obat</th>
                        <th>Expired</th>
                        <th>Sisa Hari</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expiringSoonItems ?? [] as $item)
                        @php
                            $expiredDate = \Carbon\Carbon::parse($item['tanggal_kadaluwarsa']);
                            $sisaHari = (int) round(now()->diffInDays($expiredDate));
                        @endphp
                        <tr>
                            <td>{{ $item['nama_obat'] }}</td>
                            <td>{{ $expiredDate->translatedFormat('d M Y') }}</td>
                            <td>{{ $sisaHari }} hari</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty">Tidak ada obat yang akan kadaluarsa segera.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartMonths = {!! json_encode($chartMonths ?? []) !!};
    const chartUsageData = {!! json_encode($chartUsageData ?? []) !!};

    const usageCanvas = document.getElementById('chartAdminUsage');
    if (usageCanvas) {
        const usageContext = usageCanvas.getContext('2d');
        new Chart(usageContext, {
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
</script>
