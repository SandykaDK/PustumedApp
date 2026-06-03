<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Min Max - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/min_max/min_max.css') }}">
    <link rel="stylesheet" href="{{ asset('css/permintaan_obat/permintaan_obat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">
</head>
<body>
    <x-sidebar />
    <div class="main-wrapper">
        <x-navbar />

        <div class="container main-content">
            <div class="page-header">
                <h1>Min Max</h1>
                <p>Perhitungan minimum stock, maximum stock, safety stock, dan reorder point (ROP) untuk setiap obat.</p>
            </div>

            <x-alert type="success" />
            <x-alert type="error" />

            <div class="card">
                <div class="table-actions">
                    <form method="GET" action="{{ route('min-max.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="search-wrapper">
                                <span class="search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                </span>
                                <input type="text" name="search" class="search-input" placeholder="Cari kode atau nama obat..." value="{{ $search }}">
                            </div>

                            <div class="date-input-group">
                                <label for="month_year" class="date-label">Bulan / Tahun</label>
                                <input id="month_year" type="month" name="month_year" class="date-input month-year-input" value="{{ $monthYearValue ?? '' }}" min="{{ min($yearOptions ?? []) }}-01" max="{{ max($yearOptions ?? []) }}-12">
                            </div>

                            <div class="date-input-group">
                                <label for="status" class="date-label">Status</label>
                                <select id="status" name="status" class="date-input">
                                    @foreach(($allowedStatuses ?? []) as $key => $label)
                                        <option value="{{ $key }}" {{ (isset($status) && $status == $key) ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn-filter">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <span>Cari</span>
                            </button>
                            <a href="{{ route('min-max.index') }}" class="btn-filter btn-reset">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.995-1.465" />
                                </svg>
                                <span>Reset</span>
                            </a>
                        </div>
                    </form>
                </div>

                <div class="table-wrapper">
                    <table class="min-max-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Obat</th>
                                <th>Nama Obat</th>
                                <th>Satuan</th>
                                <th>Stok</th>
                                <th>Avg</th>
                                <th>Max Daily</th>
                                <th>Safety Stock</th>
                                <th>Min</th>
                                <th>Max</th>
                                <th>ROP</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $index => $item)
                                <tr>
                                    <td>{{ $items->firstItem() + $index }}</td>
                                    <td>{{ $item['kode_obat'] }}</td>
                                    <td>{{ $item['nama_obat'] }}</td>
                                    <td>{{ $item['satuan'] }}</td>
                                    <td>{{ number_format($item['stok_saat_ini']) }}</td>
                                    <td>{{ number_format($item['average_daily_usage'], 2) }}</td>
                                    <td>{{ number_format($item['maximum_daily_usage']) }}</td>
                                    <td>{{ number_format($item['safety_stock']) }}</td>
                                    <td>{{ number_format($item['minimum_stock']) }}</td>
                                    <td>{{ number_format($item['maximum_stock']) }}</td>
                                    <td>{{ number_format($item['reorder_point']) }}</td>
                                    <td><span class="status-pill {{ $item['status_class'] }}">{{ $item['status_label'] }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="empty">Tidak ada data min max</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pagination-section">
                    <div class="pagination-controls">
                        <div class="per-page-selector">
                            <form method="GET" action="{{ route('min-max.index') }}" class="per-page-form">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="month_year" value="{{ request('month_year', $monthYearValue ?? '') }}">
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
