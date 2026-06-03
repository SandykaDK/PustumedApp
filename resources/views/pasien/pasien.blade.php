@php
    function sortLink($column, $label) {
        $direction = request('direction') === 'asc' ? 'desc' : 'asc';
        $params = array_merge(request()->all(), [
            'sort' => $column,
            'direction' => $direction
        ]);

        return '<a href="'.route('pasien.index', $params).'">'.$label.'</a>';
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Daftar Pasien - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pasien/pasien.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">
</head>
<body>

    <x-sidebar />

    <div class="main-wrapper">
        <x-navbar />

        <div class="container main-content">

        <div class="page-header">
            <h1>Daftar Pasien</h1>
        </div>

        <x-alert type="success" />
        <x-alert type="error" />

        <div class="card">
            <div class="table-actions">
                <form method="GET" action="{{ route('pasien.index') }}" class="filter-form">
                    <div class="filter-row">
                        <div class="search-wrapper">
                            <span class="search-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0
                                        A7.5 7.5 0 1 0 5.196 5.196
                                        a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </span>

                            <input
                                type="text"
                                name="search"
                                placeholder="Cari nama pasien, NIK atau No.BPJS..."
                                value="{{ request('search') }}"
                                class="search-input"
                            >
                        </div>

                        <div class="date-input-group">
                            <label for="status" class="date-label">Status</label>
                            <select id="status" name="status" class="date-input">
                                <option value="semua" {{ request('status', 'semua') == 'semua' ? 'selected' : '' }}>Semua</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <a href="{{ route('pasien.index') }}" class="btn-reset" style="display:flex;align-items:center;gap:6px;background:#6b7280;color:white;padding:8px 14px;border-radius:6px;text-decoration:none;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.995-1.465" />
                            </svg>
                            <span>Reset</span>
                        </a>
                    </div>
                </form>

                <!-- BUTTON TAMBAH -->
                <button type="button" id="openCreateModal" class="btn-add">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah</span>
                </button>
            </div>
            <table class="pasien-table">
                <thead>
                    <tr>
                    <th>No</th>
                    <x-sortable-th column="nama" label="Nama Pasien" />
                    <x-sortable-th column="nik" label="NIK" />
                    <x-sortable-th column="alamat" label="Alamat" />
                    <x-sortable-th column="jenis_kelamin" label="Jenis Kelamin" />
                    <x-sortable-th column="golongan_darah" label="Gol. Darah" />
                    <x-sortable-th column="no_telepon" label="No. Telepon" />
                    <x-sortable-th column="no_bpjs" label="No. BPJS" />
                    <th>Status</th>
                    {{-- <x-sortable-th column="created_at" label="Tanggal Dibuat" /> --}}
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                    @forelse ($pasiens as $pasien)
                        <tr>
                            <td>{{ $pasien->id }}</td>
                            <td>{{ $pasien->nama }}</td>
                            <td>{{ $pasien->nik }}</td>
                            <td>{{ $pasien->alamat }}</td>
                            <td>{{ $pasien->jenis_kelamin }}</td>
                            <td>{{ $pasien->golongan_darah }}</td>
                            <td>{{ $pasien->no_telepon }}</td>
                            <td>{{ $pasien->no_bpjs }}</td>
                            <td>
                                <span class="status-badge {{ $pasien->status === 'aktif' ? 'status-aktif' : 'status-nonaktif' }}">{{ $pasien->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            {{-- <td>{{ $pasien->created_at->format('d M Y') }}</td> --}}
                            <td>
                                <div class="action-buttons">
                                    @php
                                        $historyPayload = $pasien->pengeluaranObat->map(function ($pengeluaran) {
                                            return [
                                                'id' => $pengeluaran->id,
                                                'tanggal' => $pengeluaran->tanggal_pengeluaran ? \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->locale('id')->translatedFormat('d F Y') : '-',
                                                'tanggal_raw' => $pengeluaran->tanggal_pengeluaran ? \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->format('Y-m-d') : null,
                                                'petugas' => $pengeluaran->user?->name ?? '-',
                                                'dokter' => $pengeluaran->dokter?->nama ?? '-',
                                                'keterangan' => $pengeluaran->keterangan ?? '-',
                                                'total_item' => $pengeluaran->detailPengeluaranObat->count(),
                                                'total_qty' => $pengeluaran->detailPengeluaranObat->sum('jumlah_keluar'),
                                                'details' => $pengeluaran->detailPengeluaranObat->map(function ($detail) {
                                                    return [
                                                        'nama_obat' => $detail->namaObat?->nama_obat ?? '-',
                                                        'jumlah_keluar' => $detail->jumlah_keluar,
                                                        'satuan' => $detail->satuan?->satuan_obat ?? '-',
                                                        'lokasi' => $detail->lokasi_penyimpanan ?? '-',
                                                    ];
                                                })->values(),
                                            ];
                                        })->values();
                                    @endphp

                                    <button type="button"
                                        class="action-btn view openHistoryModal"
                                        title="Lihat History"
                                        data-pasien-nama="{{ $pasien->nama }}"
                                        data-history='@json($historyPayload)'>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12c0-1.657 1.79-3.833 4.5-5.647C9.46 4.355 12.3 3.75 12 3.75c-.3 0 2.54.605 5.25 2.603 2.71 1.814 4.5 3.99 4.5 5.647s-1.79 3.833-4.5 5.647C14.54 19.645 11.7 20.25 12 20.25c.3 0-2.54-.605-5.25-2.603C4.04 15.833 2.25 13.657 2.25 12Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button>

                                    <!-- EDIT (open modal) -->
                                    <button type="button"
                                        class="action-btn edit openEditModal"
                                        title="Edit"
                                        data-id="{{ $pasien->id }}"
                                        data-nama="{{ $pasien->nama }}"
                                        data-nik="{{ $pasien->nik }}"
                                        data-alamat="{{ $pasien->alamat }}"
                                        data-jenis-kelamin="{{ $pasien->jenis_kelamin }}"
                                        data-golongan-darah="{{ $pasien->golongan_darah }}"
                                        data-no_telepon="{{ $pasien->no_telepon }}"
                                        data-no_bpjs="{{ $pasien->no_bpjs }}"
                                        data-status="{{ $pasien->status }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688
                                                a1.875 1.875 0 1 1 2.652 2.652
                                                L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13
                                                L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897
                                                l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75
                                                A2.25 2.25 0 0 1 15.75 21H5.25
                                                A2.25 2.25 0 0 1 3 18.75V8.25
                                                A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>

                                    <!-- DELETE (confirm-delete component) -->
                                    <x-confirm-delete action="{{ route('pasien.destroy', $pasien->id) }}" :id="'delete-pasien-'.$pasien->id" title="Hapus Pasien" message="Yakin ingin menghapus pasien {{ $pasien->nama }}?">
                                        <button type="button" class="action-btn delete" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0
                                                    L9.26 9m9.968-3.21c.342.052.682.107
                                                    1.022.166m-1.022-.165L18.16 19.673
                                                    a2.25 2.25 0 0 1-2.244 2.077H8.084
                                                    a2.25 2.25 0 0 1-2.244-2.077
                                                    L4.772 5.79m14.456 0
                                                    a48.108 48.108 0 0 0-3.478-.397
                                                    m-12 .562c.34-.059.68-.114
                                                    1.022-.165m0 0a48.11 48.11 0 0 1
                                                    3.478-.397m7.5 0v-.916
                                                    c0-1.18-.91-2.164-2.09-2.201
                                                    a51.964 51.964 0 0 0-3.32 0
                                                    c-1.18.037-2.09 1.022-2.09 2.201
                                                    v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </x-confirm-delete>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="empty">Tidak ada data pasien</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="pagination-section">
                <div class="pagination-controls">
                    <div class="per-page-selector">
                        <form method="GET" action="{{ route('pasien.index') }}" class="per-page-form">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="status" value="{{ request('status', 'semua') }}">
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                            <input type="hidden" name="direction" value="{{ request('direction') }}">
                            <label for="per_page_pasien" class="per-page-label">Tampilkan:</label>
                            <select name="per_page" id="per_page_pasien" class="per-page-input" onchange="this.form.submit()">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="pagination-wrapper">
                    {{ $pasiens->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        <!-- History Modal -->
        <div id="historyPasienModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content modal-large history-modal-content" role="dialog" aria-modal="true" aria-labelledby="historyPasienTitle">
                <div class="modal-header">
                    <div>
                        <h2 id="historyPasienTitle">History Pengeluaran Obat</h2>
                        <p id="historyPasienSubtitle" class="modal-subtitle">Riwayat pengeluaran obat pasien</p>
                    </div>
                    <button class="modal-close" id="closeHistoryPasienModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body history-modal-body">
                    <div class="history-filters">
                        <div class="history-filter-group">
                            <label for="historyDateFilter">Filter Tanggal</label>
                            <input type="date" id="historyDateFilter" class="history-filter-input">
                        </div>
                        <div class="history-filter-group">
                            <label for="historyDokterFilter">Filter Dokter</label>
                            <select id="historyDokterFilter" class="history-filter-input">
                                <option value="">Semua dokter</option>
                            </select>
                        </div>
                        <button type="button" id="historyFilterReset" class="history-filter-reset">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.995-1.465" />
                            </svg>
                            <span>Reset</span>
                        </button>
                    </div>

                    <div class="table-wrapper history-table-wrapper">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Petugas</th>
                                    <th>Dokter</th>
                                    <th>Jumlah Item</th>
                                    <th>Total Qty</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="historyPasienTableBody">
                                <tr>
                                    <td colspan="6" class="empty">Pilih pasien untuk melihat history</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="history-details" id="historyPasienDetails"></div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div id="createPasienModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createPasienTitle">
                <div class="modal-header">
                    <h2 id="createPasienTitle">Tambah Pasien</h2>
                    <button class="modal-close" id="closeCreatePasienModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && !session('edit_pasien_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                        <form action="{{ route('pasien.store') }}" method="POST" class="form-component">
                            @csrf

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="nama">Nama Pasien</label>
                                    <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="nik">NIK</label>
                                    <input id="nik" type="text" name="nik" value="{{ old('nik') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_kelamin">Jenis Kelamin</label>
                                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="golongan_darah">Golongan Darah</label>
                                    <select id="golongan_darah" name="golongan_darah" required>
                                        <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>AB</option>
                                        <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>O</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="no_bpjs">No. BPJS</label>
                                    <input id="no_bpjs" type="text" name="no_bpjs" value="{{ old('no_bpjs') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="no_telepon">No. Telepon</label>
                                    <input id="no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon') }}" required>
                                </div>

                                <div class="form-group form-group-full">
                                    <label for="status_toggle">Status Akun</label>
                                    <div class="toggle-switch">
                                        <input type="hidden" id="status_hidden" name="status" value="{{ old('status', 'aktif') }}">
                                        <input type="checkbox" id="status_toggle" value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'checked' : '' }}>
                                        <label for="status_toggle" class="toggle-slider"></label>
                                        <span class="toggle-text">{{ old('status', 'aktif') == 'aktif' ? 'Aktif' : 'Nonaktif' }}</span>
                                    </div>
                                </div>

                                <div class="form-group form-group-full">
                                    <label for="alamat">Alamat</label>
                                    <textarea id="alamat" name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                                </div>
                            </div>

                            <div class="form-actions modal-actions">
                                <button type="button" class="btn-secondary" id="cancelCreatePasienModal">Batal</button>
                                <button type="submit" class="btn-primary">Simpan</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="editPasienModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="editPasienTitle">
                <div class="modal-header">
                    <h2 id="editPasienTitle">Edit Pasien</h2>
                    <button class="modal-close" id="closeEditPasienModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && session('edit_pasien_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="editPasienForm" action="" method="POST" class="form-component">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="edit_nama">Nama Pasien</label>
                                <input id="edit_nama" type="text" name="nama" value="{{ old('nama') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="edit_nik">NIK</label>
                                <input id="edit_nik" type="text" name="nik" value="{{ old('nik') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="edit_jenis_kelamin">Jenis Kelamin</label>
                                <select id="edit_jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="edit_golongan_darah">Golongan Darah</label>
                                <select id="edit_golongan_darah" name="golongan_darah" required>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="edit_no_bpjs">No. BPJS</label>
                                <input id="edit_no_bpjs" type="text" name="no_bpjs" value="{{ old('no_bpjs') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="edit_no_telepon">No. Telepon</label>
                                <input id="edit_no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon') }}" required>
                            </div>

                            <div class="form-group form-group-full">
                                <label for="edit_status_toggle">Status Akun</label>
                                <div class="toggle-switch">
                                    <input type="hidden" id="edit_status_hidden" name="status" value="aktif">
                                    <input type="checkbox" id="edit_status_toggle" value="aktif">
                                    <label for="edit_status_toggle" class="toggle-slider"></label>
                                    <span class="toggle-text">Aktif</span>
                                </div>
                            </div>

                            <div class="form-group form-group-full">
                                <label for="edit_alamat">Alamat</label>
                                <textarea id="edit_alamat" name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                            </div>
                        </div>

                        <div class="form-actions modal-actions">
                            <button type="button" class="btn-secondary" id="cancelEditPasienModal">Batal</button>
                            <button type="submit" class="btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    </div>

<script>
    (function() {
        const historyModal = document.getElementById('historyPasienModal');
        const closeHistoryBtn = document.getElementById('closeHistoryPasienModal');
        const historyTableBody = document.getElementById('historyPasienTableBody');
        const historyDetails = document.getElementById('historyPasienDetails');
        const historySubtitle = document.getElementById('historyPasienSubtitle');
        const historyButtons = document.querySelectorAll('.openHistoryModal');
        const historyDateFilter = document.getElementById('historyDateFilter');
        const historyDokterFilter = document.getElementById('historyDokterFilter');
        const historyFilterReset = document.getElementById('historyFilterReset');
        let currentHistoryData = [];

        function openHistoryModal() {
            if (!historyModal) return;
            historyModal.classList.remove('hidden');
            historyModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeHistoryModal() {
            if (!historyModal) return;
            historyModal.classList.add('hidden');
            historyModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
        }

        function renderHistoryDetail(item) {
            if (!historyDetails) return;

            const detailRows = (item.details || []).map(detail => `
                <tr>
                    <td>${detail.nama_obat || '-'}</td>
                    <td>${detail.jumlah_keluar ?? 0}</td>
                    <td>${detail.satuan || '-'}</td>
                    <td>${detail.lokasi || '-'}</td>
                </tr>
            `).join('');

            historyDetails.innerHTML = `
                <div class="history-detail-card">
                    <div class="history-detail-card-header">
                        <h3>${item.tanggal || 'Tanggal tidak tersedia'}</h3>
                        <span>${item.total_item ?? 0} item</span>
                    </div>
                    <div class="table-wrapper">
                        <table class="history-detail-table">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Lokasi</th>
                                </tr>
                            </thead>
                            <tbody>${detailRows || '<tr><td colspan="4" class="empty">Tidak ada detail transaksi</td></tr>'}</tbody>
                        </table>
                    </div>
                </div>
            `;
        }

        function normalizeText(text) {
            return String(text || '').trim().toLowerCase();
        }

        function populateDokterFilterOptions(history) {
            if (!historyDokterFilter) return;

            const currentValue = historyDokterFilter.value;
            const dokterOptions = Array.from(new Set(
                history
                    .map(item => String(item.dokter || '').trim())
                    .filter(item => item && item !== '-')
            )).sort((a, b) => a.localeCompare(b, 'id', { sensitivity: 'base' }));

            historyDokterFilter.innerHTML = '<option value="">Semua dokter</option>';
            dokterOptions.forEach(dokter => {
                const option = document.createElement('option');
                option.value = dokter;
                option.textContent = dokter;
                historyDokterFilter.appendChild(option);
            });

            if (currentValue && dokterOptions.includes(currentValue)) {
                historyDokterFilter.value = currentValue;
            }
        }

        function renderHistoryRows(history) {
            if (!historyTableBody || !historyDetails) return;

            if (!history.length) {
                historyTableBody.innerHTML = '<tr><td colspan="6" class="empty">Tidak ada data history sesuai filter</td></tr>';
                historyDetails.innerHTML = '';
                return;
            }

            historyTableBody.innerHTML = history.map(item => `
                <tr class="history-row" tabindex="0" role="button" aria-label="Lihat detail history tanggal ${item.tanggal || '-'}">
                    <td>${item.tanggal || '-'}</td>
                    <td>${item.petugas || '-'}</td>
                    <td>${item.dokter || '-'}</td>
                    <td>${item.total_item ?? 0}</td>
                    <td>${item.total_qty ?? 0}</td>
                    <td>${item.keterangan || '-'}</td>
                </tr>
            `).join('');

            historyDetails.innerHTML = '<p class="empty">Klik salah satu history pada tabel untuk melihat detail item obat.</p>';

            const rows = historyTableBody.querySelectorAll('.history-row');
            rows.forEach((row, index) => {
                row.addEventListener('click', function() {
                    renderHistoryDetail(history[index]);
                });

                row.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        renderHistoryDetail(history[index]);
                    }
                });
            });
        }

        function applyHistoryFilters() {
            const selectedDate = historyDateFilter ? historyDateFilter.value : '';
            const selectedDokter = normalizeText(historyDokterFilter ? historyDokterFilter.value : '');

            const filteredHistory = currentHistoryData.filter(item => {
                const matchDate = !selectedDate || (item.tanggal_raw === selectedDate);
                const dokterName = normalizeText(item.dokter);
                const matchDokter = !selectedDokter || (dokterName === selectedDokter);

                return matchDate && matchDokter;
            });

            renderHistoryRows(filteredHistory);
        }

        function renderHistory(pasienName, history) {
            if (!historyTableBody || !historyDetails || !historySubtitle) return;

            historySubtitle.textContent = `Riwayat pengeluaran obat untuk pasien ${pasienName}`;
            currentHistoryData = Array.isArray(history) ? history : [];
            if (historyDateFilter) historyDateFilter.value = '';
            if (historyDokterFilter) historyDokterFilter.value = '';

            populateDokterFilterOptions(currentHistoryData);
            applyHistoryFilters();
        }

        historyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const pasienName = this.dataset.pasienNama || 'Pasien';
                let history = [];

                try {
                    history = JSON.parse(this.dataset.history || '[]');
                } catch (error) {
                    history = [];
                }

                renderHistory(pasienName, history);
                openHistoryModal();
            });
        });

        closeHistoryBtn && closeHistoryBtn.addEventListener('click', closeHistoryModal);

        historyDateFilter && historyDateFilter.addEventListener('change', applyHistoryFilters);
        historyDokterFilter && historyDokterFilter.addEventListener('change', applyHistoryFilters);
        historyFilterReset && historyFilterReset.addEventListener('click', function() {
            if (historyDateFilter) historyDateFilter.value = '';
            if (historyDokterFilter) historyDokterFilter.value = '';
            applyHistoryFilters();
        });

        historyModal && historyModal.addEventListener('click', function(e) {
            if (e.target === historyModal) closeHistoryModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && historyModal && !historyModal.classList.contains('hidden')) {
                closeHistoryModal();
            }
        });

        // Create modal
        const openBtn = document.getElementById('openCreateModal');
        const modal = document.getElementById('createPasienModal');
        const closeBtn = document.getElementById('closeCreatePasienModal');
        const cancelBtn = document.getElementById('cancelCreatePasienModal');

        function initStatusSwitches() {
            document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(toggle => {
                const hiddenInput = toggle.previousElementSibling;
                const labelText = toggle.nextElementSibling ? toggle.nextElementSibling.nextElementSibling : null;

                const updateStatusValue = () => {
                    const value = toggle.checked ? 'aktif' : 'non-aktif';
                    if (hiddenInput && hiddenInput.type === 'hidden') {
                        hiddenInput.value = value;
                    }
                    if (labelText) {
                        labelText.textContent = toggle.checked ? 'Aktif' : 'Nonaktif';
                    }
                };

                updateStatusValue();
                toggle.addEventListener('change', updateStatusValue);
            });
        }

        function openModal() {
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            if (!modal) return;
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
        }

        openBtn && openBtn.addEventListener('click', openModal);
        closeBtn && closeBtn.addEventListener('click', closeModal);
        cancelBtn && cancelBtn.addEventListener('click', closeModal);

        modal && modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        // Auto open create modal if validation errors belong to create
        @if ($errors->any() && !session('edit_pasien_id'))
            document.addEventListener('DOMContentLoaded', function() { openModal(); });
        @endif

        // Edit modal handlers
        const editModal = document.getElementById('editPasienModal');
        const openEditButtons = document.querySelectorAll('.openEditModal');
        // correct ids for edit modal
        const closeEditBtn = document.getElementById('closeEditPasienModal');
        const cancelEditBtn = document.getElementById('cancelEditPasienModal');
        const editForm = document.getElementById('editPasienForm');

        function openEditModal() {
            if (!editModal) return;
            editModal.classList.remove('hidden');
            editModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            if (!editModal) return;
            editModal.classList.add('hidden');
            editModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
        }

        function populateEditForm(data) {
            if (!editForm) return;
            try {
                editForm.action = '/pasien/' + data.id;
                const namaEl = document.getElementById('edit_nama');
                const nikEl = document.getElementById('edit_nik');
                const alamatEl = document.getElementById('edit_alamat');
                const jenisKelaminEl = document.getElementById('edit_jenis_kelamin');
                const golonganDarahEl = document.getElementById('edit_golongan_darah');
                if (namaEl) namaEl.value = data.nama || '';
                if (nikEl) nikEl.value = data.nik || '';
                if (alamatEl) alamatEl.value = data.alamat || '';
                if (jenisKelaminEl) jenisKelaminEl.value = data.jenis_kelamin || '';
                if (golonganDarahEl) golonganDarahEl.value = data.golongan_darah || '';

                const teleponEl = document.getElementById('edit_no_telepon');
                const bpjsEl = document.getElementById('edit_no_bpjs');
                const statusToggle = document.getElementById('edit_status_toggle');
                const statusHidden = document.getElementById('edit_status_hidden');
                const statusText = statusToggle && statusToggle.nextElementSibling ? statusToggle.nextElementSibling.nextElementSibling : null;
                if (teleponEl) teleponEl.value = data.no_telepon || '';
                if (bpjsEl) bpjsEl.value = data.no_bpjs || '';
                if (statusToggle && statusHidden) {
                    const isActive = String(data.status || 'aktif') === 'aktif';
                    statusToggle.checked = isActive;
                    statusHidden.value = isActive ? 'aktif' : 'non-aktif';
                    if (statusText) {
                        statusText.textContent = isActive ? 'Aktif' : 'Nonaktif';
                    }
                }
            } catch (err) {
                console.error('populateEditForm error:', err);
            }
        }

        openEditButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                try {
                        const data = {
                            id: this.dataset.id,
                            nama: this.dataset.nama,
                            nik: this.dataset.nik,
                            alamat: this.dataset.alamat,
                            jenis_kelamin: this.dataset.jenisKelamin || this.getAttribute('data-jenis-kelamin'),
                            golongan_darah: this.dataset.golonganDarah || this.getAttribute('data-golongan-darah'),
                            no_telepon: this.dataset.no_telepon || '',
                            no_bpjs: this.dataset.no_bpjs || '',
                            status: this.dataset.status || 'aktif'
                        };

                    if (window.console && window.console.log) console.log('edit pasien clicked, id=', data.id);

                    populateEditForm(data);
                    openEditModal();
                } catch (err) {
                    console.error('openEditButtons click handler error:', err);
                }
            });
        });

        closeEditBtn && closeEditBtn.addEventListener('click', closeEditModal);
        cancelEditBtn && cancelEditBtn.addEventListener('click', closeEditModal);

        editModal && editModal.addEventListener('click', function(e) {
            if (e.target === editModal) closeEditModal();
        });

        const editPasienIdFromServer = @json(session('edit_pasien_id'));
        const oldInput = @json(session()->getOldInput());

        if (editPasienIdFromServer) {
            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.querySelector('.openEditModal[data-id="' + editPasienIdFromServer + '"]');
                if (btn) {
                    const data = {
                        id: btn.dataset.id,
                        nama: btn.dataset.nama,
                        nik: btn.dataset.nik,
                        alamat: btn.dataset.alamat,
                        jenis_kelamin: btn.dataset.jenisKelamin || btn.getAttribute('data-jenis-kelamin'),
                        golongan_darah: btn.dataset.golonganDarah || btn.getAttribute('data-golongan-darah'),
                        no_telepon: btn.dataset.no_telepon || '',
                        no_bpjs: btn.dataset.no_bpjs || '',
                        status: btn.dataset.status || 'aktif'
                    };
                    populateEditForm(data);
                }

                if (oldInput && Object.keys(oldInput).length) {
                    if (oldInput.nama) document.getElementById('edit_nama').value = oldInput.nama;
                    if (oldInput.nik) document.getElementById('edit_nik').value = oldInput.nik;
                    if (oldInput.alamat) document.getElementById('edit_alamat').value = oldInput.alamat;
                    if (oldInput.jenis_kelamin) document.getElementById('edit_jenis_kelamin').value = oldInput.jenis_kelamin;
                    if (oldInput.golongan_darah) document.getElementById('edit_golongan_darah').value = oldInput.golongan_darah;
                    if (oldInput.no_telepon) document.getElementById('edit_no_telepon').value = oldInput.no_telepon;
                    if (oldInput.no_bpjs) document.getElementById('edit_no_bpjs').value = oldInput.no_bpjs;
                    if (oldInput.status) {
                        const statusToggle = document.getElementById('edit_status_toggle');
                        const statusHidden = document.getElementById('edit_status_hidden');
                        const statusText = statusToggle && statusToggle.nextElementSibling ? statusToggle.nextElementSibling.nextElementSibling : null;
                        if (statusToggle && statusHidden) {
                            const isActive = String(oldInput.status) === 'aktif';
                            statusToggle.checked = isActive;
                            statusHidden.value = isActive ? 'aktif' : 'non-aktif';
                            if (statusText) {
                                statusText.textContent = isActive ? 'Aktif' : 'Nonaktif';
                            }
                        }
                    }
                }

                openEditModal();
            });
        }

        // Auto submit filter form with debounce.
        const filterForm = document.querySelector('.table-actions .filter-form');
        if (filterForm) {
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
        }

        initStatusSwitches();

    })();
</script>

</body>
</html>
