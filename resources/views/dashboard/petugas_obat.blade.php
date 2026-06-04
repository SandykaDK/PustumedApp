<div class="petugas-obat-dashboard">
    <div class="pemusnahan-container" style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <h2 style="margin-bottom: 1rem; margin-top: 0;">Status Pemusnahan Obat</h2>
        <div class="stats-grid petugas-stats-grid">
            <!-- Pemusnahan status cards: show separate cards for each status -->
            <a href="{{ route('pemusnahan-obat.index', ['tab' => 'belum_diajukan']) }}" class="stat-card" style="text-decoration:none; color:inherit; background: #f3f4f6;">
                <div class="stat-icon gray">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h6M7 11h6M7 15h6"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v4a1 1 0 0 0 1 1h4M5 21h14a2 2 0 0 0 2-2V7L14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/></svg>
                </div>
                <h3 style="font-size: 1rem;">Belum Diajukan</h3>
                <div class="value" style="font-size: 1,2rem;">{{ number_format($pemusnahanCounts['belum_diajukan'] ?? 0) }}</div>
            </a>
            <a href="{{ route('pemusnahan-obat.index', ['tab' => 'sudah_diajukan']) }}" class="stat-card" style="text-decoration:none; color:inherit; background: #fef3c7;">
                <div class="stat-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg>
                </div>
                <h3 style="font-size: 1rem;">Sudah Diajukan</h3>
                <div class="value" style="font-size: 1,2rem;">{{ number_format($pemusnahanCounts['sudah_diajukan'] ?? 0) }}</div>
            </a>
            <a href="{{ route('pemusnahan-obat.index', ['tab' => 'sudah_disetujui']) }}" class="stat-card" style="text-decoration:none; color:inherit; background: #dcfce7;">
                <div class="stat-icon green">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg>
                </div>
                <h3 style="font-size: 1rem;">Sudah Disetujui</h3>
                <div class="value" style="font-size: 1,2rem;">{{ number_format($pemusnahanCounts['sudah_disetujui'] ?? 0) }}</div>
            </a>
            <a href="{{ route('pemusnahan-obat.index', ['tab' => 'sudah_dimusnahkan']) }}" class="stat-card" style="text-decoration:none; color:inherit; background: #f3e8ff;">
                <div class="stat-icon purple">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                </div>
                <h3 style="font-size: 1rem;">Sudah Dimusnahkan</h3>
                <div class="value" style="font-size: 1,2rem;">{{ number_format($pemusnahanCounts['sudah_dimusnahkan'] ?? 0) }}</div>
            </a>
        </div>
    </div>

    <div class="petugas-top-grid">
        <div class="chart-card priority-table-card">
            <h3>Info Stok Obat</h3>
            <div class="priority-table-toolbar">
                <div class="priority-filter-group">
                    <br>
                    <label for="priorityStatusFilter">Filter Status</label>
                    <select id="priorityStatusFilter" class="priority-status-filter">
                        <option value="all">Semua Status</option>
                        <option value="stok_habis">Stok Habis</option>
                        <option value="perlu_pengadaan">Perlu Pengadaan</option>
                        <option value="kadaluarsa">Sudah Kadaluwarsa</option>
                        <option value="mendekati_kadaluarsa">Mendekati Kadaluarsa</option>
                        {{-- <option value="belum_minmax">Belum Ada Min-Max</option> --}}
                    </select>
                </div>
                <div class="priority-table-meta">
                    <span>{{ count($priorityItems ?? []) }} data </span>
                </div>
            </div>
            <div class="priority-table-wrap">
                <table class="priority-table" id="priorityTable">
                    <thead>
                        <tr>
                            <th>Nama Obat</th>
                            <th>Stok</th>
                            <th>Min Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Tampilkan semua item prioritas (biarkan container yang menggulir jika banyak data)
                            $displayPriorityItems = $priorityItems ?? [];
                        @endphp

                        @forelse($displayPriorityItems as $item)
                            <tr data-status="{{ $item['status_key'] ?? 'aman' }}">
                                <td>{{ $item['nama_obat'] ?? '-' }}</td>
                                <td>{{ number_format($item['stok'] ?? 0) }}</td>
                                <td>{{ isset($item['minimum_stock']) && $item['minimum_stock'] !== null ? number_format($item['minimum_stock']) : '-' }}</td>
                                <td>
                                    <span class="priority-status {{ $item['tone'] ?? 'success' }}">
                                        {{ $item['status_label'] ?? $item['status'] ?? 'Aman' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr class="priority-empty-row">
                                <td colspan="4" class="empty">Tidak ada obat yang masuk prioritas saat ini.</td>
                            </tr>
                        @endforelse
                        <tr class="priority-no-results" style="display:none;">
                            <td colspan="4" class="empty">Tidak ada data yang cocok dengan filter status.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="info-section notifications petugas-notifications">
            <h2>Notifikasi</h2>
            <div class="notification-scroll">
                <div class="notification-stack">
                    @php
                        // Robust sorting: compute numeric timestamp for each notification then sort desc
                        $sortedNotifications = collect($notifications ?? [])->map(function ($n) {
                            $ts = 0;
                            if (isset($n['sort_at'])) {
                                if (is_numeric($n['sort_at'])) {
                                    $ts = (int) $n['sort_at'];
                                } elseif ($n['sort_at'] instanceof \DateTimeInterface) {
                                    $ts = $n['sort_at']->getTimestamp();
                                } else {
                                    $ts = strtotime((string) $n['sort_at']) ?: 0;
                                }
                            }
                            $n['_ts'] = $ts;
                            return $n;
                        })->sortByDesc('_ts')->values();
                    @endphp

                    @forelse($sortedNotifications as $notification)
                        <div class="notification-card {{ $notification['type'] ?? 'info' }}">
                            <span class="notification-chip">
                                {{ $notification['title'] ?? 'Notifikasi' }}
                            </span>
                            <strong>{{ $notification['name'] ?? '-' }}</strong>
                            <small>
                                @if(($notification['type'] ?? '') === 'danger' && !empty($notification['tanggal_kadaluwarsa']))
                                    {{ \Carbon\Carbon::parse($notification['tanggal_kadaluwarsa'])->translatedFormat('d M Y') }}
                                @else
                                    {{ $notification['description'] ?? '' }}
                                @endif
                            </small>
                        </div>
                    @empty
                        <div class="notification-empty">
                            Tidak ada notifikasi penting saat ini.
                        </div>
                    @endforelse
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

        const priorityStatusFilter = document.getElementById('priorityStatusFilter');
        const priorityTable = document.getElementById('priorityTable');

        if (priorityStatusFilter && priorityTable) {
            const rows = Array.from(priorityTable.querySelectorAll('tbody tr[data-status]'));
            const emptyRow = priorityTable.querySelector('.priority-no-results');

            const applyPriorityFilter = () => {
                const selected = priorityStatusFilter.value;
                let visibleCount = 0;

                rows.forEach((row) => {
                    const rowStatus = row.getAttribute('data-status') || 'aman';
                    const matches = selected === 'all' || rowStatus === selected;
                    row.style.display = matches ? '' : 'none';
                    if (matches) visibleCount += 1;
                });

                if (emptyRow) {
                    emptyRow.style.display = visibleCount === 0 ? '' : 'none';
                }
            };

            priorityStatusFilter.addEventListener('change', applyPriorityFilter);
            applyPriorityFilter();
        }

        // Separate data for receipts and issues charts
        let receiptsMonths = convertToIndonesianMonths({!! json_encode($chartMonths ?? []) !!});
        let receipts = {!! json_encode($chartReceiptsData ?? []) !!};

        let issuesMonths = convertToIndonesianMonths({!! json_encode($chartMonths ?? []) !!});
        let issues = {!! json_encode($chartIssuesData ?? []) !!};

        let receiptsChart = null;
        let issuesChart = null;

        const initReceiptsChart = () => {
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
                            y: { beginAtZero: true, ticks: { precision: 0 } }
                        }
                    }
                });
            }
        };

        const initIssuesChart = () => {
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
                            y: { beginAtZero: true, ticks: { precision: 0 } }
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
