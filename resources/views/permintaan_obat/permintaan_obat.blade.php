<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Obat - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/permintaan_obat/permintaan_obat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">
</head>
<body>
    <x-sidebar />
    <div class="main-wrapper">
        <x-navbar />

        <div class="container main-content">
            <div class="page-header">
                <h1>Permintaan Obat</h1>
                <p>Rekomendasi restock berdasarkan metode Min-Max (periode {{ $period }} hari)</p>
            </div>

            <x-alert type="success" />
            <x-alert type="error" />

            <div class="card">
                <div class="table-actions">
                    <form method="GET" action="{{ route('permintaan-obat.index') }}">
                        <div class="filter-row">
                            <div class="search-wrapper">
                                <span class="search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                </span>
                                <input type="text" name="search" class="search-input" placeholder="Cari nama obat..." value="{{ $search }}">
                            </div>

                            <div class="date-input-group">
                                <label for="status" class="date-label">Status</label>
                                <select id="status" name="status" class="date-input">
                                    @foreach($allowedStatuses as $key => $label)
                                        <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn-filter">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <span>Cari</span>
                            </button>

                            <button type="submit" name="print" value="1" class="btn-filter btn-print">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2.25A2.25 2.25 0 0 1 8.25 0h7.5A2.25 2.25 0 0 1 18 2.25V9M6 9h12M6 9L4.5 11.25M18 9l1.5 2.25M9 14.25h6M9 18h6" />
                                </svg>
                                <span>Cetak</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="table-wrapper">
                    <table class="permintaan-obat-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Obat</th>
                                <th>Stok</th>
                                <th>MIN</th>
                                <th>MAX</th>
                                <th>Rata-rata</th>
                                <th>Status</th>
                                <th>Rekomendasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $index => $item)
                                <tr>
                                    <td>{{ $items->firstItem() + $index }}</td>
                                    <td>{{ $item['nama_obat'] }}</td>
                                    <td>{{ $item['stok'] }}</td>
                                    <td>{{ $item['minimum_stock'] }}</td>
                                    <td>{{ $item['maximum_stock'] }}</td>
                                    <td>{{ number_format($item['period_average'], 2) }}</td>
                                    <td><span class="status-pill {{ $item['status'] }}">{{ $item['status_label'] }}</span></td>
                                    <td>{{ $item['recommendation'] }} unit</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty">Tidak ada data permintaan obat</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                <div class="pagination-section">
                    <div class="pagination-controls">
                        <div class="per-page-selector">
                            <form method="GET" action="{{ route('permintaan-obat.index') }}" class="per-page-form">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="period" value="{{ request('period') }}">
                                <input type="hidden" name="lead_time" value="{{ request('lead_time') }}">
                                <input type="hidden" name="buffer_days" value="{{ request('buffer_days') }}">
                                <label for="per_page_footer" class="per-page-label">Tampilkan:</label>
                                <select name="per_page" id="per_page_footer" class="per-page-input" onchange="this.form.submit()">
                                    <option value="10" {{ $perPageOption == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPageOption == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPageOption == 50 ? 'selected' : '' }}>50</option>
                                    <option value="all" {{ $perPageOption == 'all' ? 'selected' : '' }}>Semua</option>
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="pagination-wrapper">
                        {{ $items->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
