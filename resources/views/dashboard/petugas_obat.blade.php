<div class="petugas-obat-dashboard">
    <div class="stats-grid petugas-stats-grid">
        @foreach($dashboardStats ?? [] as $stat)
            <div class="stat-card">
                <div class="stat-icon {{ $stat['tone'] ?? 'blue' }}">{{ $stat['icon'] ?? '•' }}</div>
                <h3>{{ $stat['label'] ?? '-' }}</h3>
                <div class="value">{{ $stat['value'] ?? '0' }}</div>
            </div>
        @endforeach
    </div>

    <div class="petugas-top-grid">
        <div class="chart-card priority-table-card">
            <h4>Prioritas Utama</h4>
            <div class="priority-table-toolbar">
                <div class="priority-filter-group">
                    <label for="priorityStatusFilter">Filter Status</label>
                    <select id="priorityStatusFilter" class="priority-status-filter">
                        <option value="all">Semua Status</option>
                        <option value="stok_habis">Stok Habis</option>
                        <option value="perlu_pengadaan">Perlu Pengadaan</option>
                        <option value="kadaluarsa">Sudah Kadaluarsa</option>
                        <option value="mendekati_kadaluarsa">Mendekati Kadaluarsa</option>
                        <option value="belum_minmax">Belum Ada Min-Max</option>
                    </select>
                </div>
                <div class="priority-table-meta">
                    <span>{{ count($priorityItems ?? []) }} data prioritas</span>
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
                            // Batasi jumlah item yang ditampilkan di card Prioritas Utama
                            if(isset($priorityItems) && $priorityItems instanceof \Illuminate\Support\Collection) {
                                $displayPriorityItems = $priorityItems->slice(0, 6);
                            } else {
                                $displayPriorityItems = array_slice($priorityItems ?? [], 0, 6);
                            }
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
                    @forelse($notifications ?? [] as $notification)
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
                <h4>Penerimaan Obat per Bulan</h4>
                <canvas id="chartReceipts" height="95"></canvas>
            </div>
            <div class="chart-card compact">
                <h4>Pengeluaran Obat per Bulan</h4>
                <canvas id="chartIssues" height="95"></canvas>
            </div>
        </div>
    </div>

    <div class="petugas-activity-grid">
        <div class="chart-card activity-card">
            <h4>5 Transaksi Penerimaan Terakhir</h4>
            <div class="activity-list">
                @forelse($recentReceipts ?? [] as $receipt)
                    <div class="activity-item">
                        <div class="activity-item-header">
                            <strong>{{ $receipt['title'] ?? '-' }}</strong>
                            <span>{{ $receipt['date_label'] ?? '-' }}</span>
                        </div>
                        <div class="activity-item-meta">
                            <span>{{ $receipt['user_name'] ?? '-' }}</span>
                            <span>{{ $receipt['detail_count'] ?? 0 }} item</span>
                        </div>
                        <p>{{ $receipt['items'] ?? '-' }}</p>
                    </div>
                @empty
                    <div class="notification-empty">Belum ada transaksi penerimaan terbaru.</div>
                @endforelse
            </div>
        </div>

        <div class="chart-card activity-card">
            <h4>5 Transaksi Pengeluaran Terakhir</h4>
            <div class="activity-list">
                @forelse($recentIssues ?? [] as $issue)
                    <div class="activity-item">
                        <div class="activity-item-header">
                            <strong>{{ $issue['title'] ?? '-' }}</strong>
                            <span>{{ $issue['date_label'] ?? '-' }}</span>
                        </div>
                        <div class="activity-item-meta">
                            <span>{{ $issue['user_name'] ?? '-' }}</span>
                            <span>{{ $issue['detail_count'] ?? 0 }} item</span>
                        </div>
                        <p>{{ $issue['items'] ?? '-' }}</p>
                    </div>
                @empty
                    <div class="notification-empty">Belum ada transaksi pengeluaran terbaru.</div>
                @endforelse
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
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

        const months = {!! json_encode($chartMonths ?? []) !!};
        const receipts = {!! json_encode($chartReceiptsData ?? []) !!};
        const issues = {!! json_encode($chartIssuesData ?? []) !!};

        const receiptsCanvas = document.getElementById('chartReceipts');
        if (receiptsCanvas) {
            const receiptsContext = receiptsCanvas.getContext('2d');
            new Chart(receiptsContext, {
                type: 'line',
                data: {
                    labels: months,
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

        const issuesCanvas = document.getElementById('chartIssues');
        if (issuesCanvas) {
            const issuesContext = issuesCanvas.getContext('2d');
            new Chart(issuesContext, {
                type: 'line',
                data: {
                    labels: months,
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
    </script>
</div>
