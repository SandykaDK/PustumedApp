    <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Laporan Pemusnahan Obat - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/laporan_pemusnahan_obat/laporan_pemusnahan_obat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">
</head>
<body>
    <x-sidebar />
    <div class="main-wrapper">
        <x-navbar />

        <div class="container main-content">
            <div class="page-header">
                <h1>Laporan Pemusnahan Obat</h1>
                <p>History pemusnahan obat yang belum dan sudah dilakukan.</p>
            </div>

            <x-alert type="success" />
            <x-alert type="error" />

            <div class="card">
                <div class="table-actions">
                    <form method="GET" action="{{ route('laporan-pemusnahan-obat.index') }}">
                        <div class="filter-row">
                            <div class="search-wrapper">
                                <span class="search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                </span>
                                <input type="text" name="search" class="search-input" placeholder="Cari nama obat atau keterangan..." value="{{ $search }}">
                            </div>

                            <div class="date-input-group">
                                <label for="status" class="date-label">Status Pemusnahan</label>
                                <select id="status" name="status" class="date-input">
                                    @foreach($allowedStatuses as $key => $label)
                                        <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'tanggal_pemusnahan') }}">
                            <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">

                            <a href="{{ route('laporan-pemusnahan-obat.index') }}" class="btn-reset" style="display:flex;align-items:center;gap:6px;background:#6b7280;color:white;padding:8px 14px;border-radius:6px;text-decoration:none;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.995-1.465" />
                                </svg>
                                <span>Reset</span>
                            </a>
                        </div>
                    </form>
                </div>

                <div class="table-wrapper">
                    <table class="permintaan-obat-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>
                                    <a href="{{ route('laporan-pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'nama_obat', 'direction' => $sort_by === 'nama_obat' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Nama Obat
                                        @if($sort_by === 'nama_obat')
                                            @if($direction === 'asc') ↑ @else ↓ @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('laporan-pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'tanggal_kadaluwarsa', 'direction' => $sort_by === 'tanggal_kadaluwarsa' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Kadaluwarsa
                                        @if($sort_by === 'tanggal_kadaluwarsa')
                                            @if($direction === 'asc') ↑ @else ↓ @endif
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('laporan-pemusnahan-obat.index', array_merge(request()->query(), ['sort_by' => 'tanggal_pemusnahan', 'direction' => $sort_by === 'tanggal_pemusnahan' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                                        Tanggal Pemusnahan
                                        @if($sort_by === 'tanggal_pemusnahan')
                                            @if($direction === 'asc') ↑ @else ↓ @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Pengaju</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Bukti Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pemusnahanObat as $index => $pemusnahan)
                                <tr>
                                    <td>{{ $pemusnahanObat->firstItem() + $index }}</td>
                                    <td>{{ $pemusnahan['nama_obat'] }}</td>
                                    <td>
                                        @if($pemusnahan['tanggal_kadaluwarsa'])
                                            {{ \Carbon\Carbon::parse($pemusnahan['tanggal_kadaluwarsa'])->locale('id')->translatedFormat('d F Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($pemusnahan['tanggal_pemusnahan']))
                                            {{ \Carbon\Carbon::parse($pemusnahan['tanggal_pemusnahan'])->locale('id')->translatedFormat('d F Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $pemusnahan['pengaju'] ?? 'N/A' }}</td>
                                    <td><span class="status-pill {{ strtolower($pemusnahan['status']) }}">{{ ($pemusnahan['status'] ?? 'pending') === 'approved' ? 'Sudah Dimusnahkan' : 'Belum Dimusnahkan' }}</span></td>
                                    <td>{{ $pemusnahan['keterangan'] ?? '-' }}</td>
                                    <td>
                                        @if(!empty($pemusnahan['bukti_foto']))
                                            <a href="{{ asset('storage/' . $pemusnahan['bukti_foto']) }}" target="_blank" class="btn-view-photo">Lihat Foto</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty">Tidak ada data pemusnahan obat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                <div class="pagination-section">
                    <div class="pagination-controls">
                        <div class="per-page-selector">
                            <form method="GET" action="{{ route('laporan-pemusnahan-obat.index') }}" class="per-page-form">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'tanggal_pemusnahan') }}">
                                <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">
                                <label for="per_page_footer" class="per-page-label">Tampilkan:</label>
                                <select name="per_page" id="per_page_footer" class="per-page-input" onchange="this.form.submit()">
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>Semua</option>
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="pagination-wrapper">
                        {{ $pemusnahanObat->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>


<script>
    (function() {
        const filterForm = document.querySelector('.table-actions form[method="GET"]');
        if (!filterForm) return;

        const searchField = filterForm.querySelector('input[name="search"]');
        const filterFields = filterForm.querySelectorAll('select, input[type="date"], input[type="month"]');
        let filterDebounceTimer = null;

        const submitFilter = () => filterForm.submit();
        const submitFilterDebounced = (delay = 450) => {
            if (filterDebounceTimer) clearTimeout(filterDebounceTimer);
            filterDebounceTimer = setTimeout(submitFilter, delay);
        };

        if (searchField) {
            searchField.addEventListener('input', function() {
                submitFilterDebounced(500);
            });

            searchField.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitFilterDebounced(0);
                }
            });
        }

        filterFields.forEach(function(field) {
            field.addEventListener('change', function() {
                submitFilterDebounced(250);
            });
        });
    })();
</script>
</body>
</html>
