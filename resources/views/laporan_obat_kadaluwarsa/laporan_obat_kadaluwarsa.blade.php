<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Obat Kadaluwarsa - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/laporan_obat_kadaluwarsa/laporan_obat_kadaluwarsa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">
</head>
<body>
    <x-sidebar />
    <div class="main-wrapper">
        <x-navbar />

        <div class="container main-content">
            <div class="page-header">
                <h1>Laporan Obat Kadaluwarsa</h1>
                <p>Daftar obat dengan tanggal kadaluwarsa yang mendekati atau sudah lewat.</p>
            </div>

            <x-alert type="success" />
            <x-alert type="error" />

            <div class="card">
                <div class="table-actions">
                    <form method="GET" action="{{ route('laporan-obat-kadaluwarsa.index') }}">
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
                                <label for="status" class="date-label">Status Kadaluwarsa</label>
                                <select id="status" name="status" class="date-input">
                                    @foreach($allowedStatuses as $key => $label)
                                        <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="date-input-group">
                                <label for="status_pemusnahan" class="date-label">Status Pemusnahan</label>
                                <select id="status_pemusnahan" name="status_pemusnahan" class="date-input">
                                    @foreach($allowedPemusnahanStatuses as $key => $label)
                                        <option value="{{ $key }}" {{ $statusPemusnahan == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'nama_obat') }}">
                            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">

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
                                <th>
                                    <a href="{{ route('laporan-obat-kadaluwarsa.index', array_merge(request()->query(), ['sort_by' => 'nama_obat', 'direction' => $sort_by === 'nama_obat' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Nama Obat
                                        @if($sort_by === 'nama_obat')
                                            @if($direction === 'asc') ↑ @else ↓ @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('laporan-obat-kadaluwarsa.index', array_merge(request()->query(), ['sort_by' => 'tanggal_kadaluwarsa', 'direction' => $sort_by === 'tanggal_kadaluwarsa' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Kadaluwarsa
                                        @if($sort_by === 'tanggal_kadaluwarsa')
                                            @if($direction === 'asc') ↑ @else ↓ @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('laporan-obat-kadaluwarsa.index', array_merge(request()->query(), ['sort_by' => 'sisa_hari', 'direction' => $sort_by === 'sisa_hari' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Sisa Hari
                                        @if($sort_by === 'sisa_hari')
                                            @if($direction === 'asc') ↑ @else ↓ @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('laporan-obat-kadaluwarsa.index', array_merge(request()->query(), ['sort_by' => 'stok', 'direction' => $sort_by === 'stok' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Stok
                                        @if($sort_by === 'stok')
                                            @if($direction === 'asc') ↑ @else ↓ @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Status Kadaluwarsa</th>
                                <th>Status Pemusnahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $index => $item)
                                <tr>
                                    <td>{{ $items->firstItem() + $index }}</td>
                                    <td>{{ $item['nama_obat'] }}</td>
                                    <td>{{ optional($item['tanggal_kadaluwarsa'])->translatedFormat('d F Y') }}</td>
                                    <td>{{ $item['sisa_hari'] }}</td>
                                    <td>{{ $item['stok'] }}</td>
                                    <td><span class="status-pill {{ strtolower($item['status_exp']) }}">{{ $item['status_exp'] }}</span></td>
                                    <td><span class="status-pill {{ strtolower($item['status_pemusnahan']) }}">{{ $item['status_pemusnahan'] }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="empty">Tidak ada obat kadaluwarsa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                <div class="pagination-section">
                    <div class="pagination-controls">
                        <div class="per-page-selector">
                            <form method="GET" action="{{ route('laporan-obat-kadaluwarsa.index') }}" class="per-page-form">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="status_pemusnahan" value="{{ request('status_pemusnahan') }}">
                                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'nama_obat') }}">
                                <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
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
