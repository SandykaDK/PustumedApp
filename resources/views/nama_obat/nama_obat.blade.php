@php
    function sortLink($column, $label) {
        $direction = request('direction') === 'asc' ? 'desc' : 'asc';
        $params = array_merge(request()->all(), [
            'sort' => $column,
            'direction' => $direction
        ]);

        return '<a href="'.route('nama-obat.index', $params).'">'.$label.'</a>';
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Daftar Obat - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nama_obat/nama_obat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            color: #fff;
            font-weight: 600;
            font-size: 13px;
        }
        .status-available {
            background-color: #16a34a; /* green */
        }
        .status-unavailable {
            background-color: #dc2626; /* red */
        }
        .status-unknown {
            background-color: #6b7280; /* gray */
        }
    </style>
</head>
<body>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <x-sidebar />

    <div class="main-wrapper">
        <x-navbar />

        <div class="container main-content">

        <div class="page-header">
            <h1>Daftar Obat</h1>
        </div>

        <x-alert type="success" />
        <x-alert type="error" />

        <div class="card">
            <div class="table-actions">
                <form method="GET" action="{{ route('nama-obat.index') }}" class="filter-form">
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
                                placeholder="Cari kode atau nama obat..."
                                value="{{ request('search') }}"
                                class="search-input"
                            >
                        </div>

                        <div class="date-input-group">
                            <label for="jenis_obat_id" class="date-label">Jenis Obat</label>
                            <select name="jenis_obat_id" id="jenis_obat_id" class="date-input">
                                <option value="">Semua Jenis Obat</option>
                                @foreach ($jenisobats as $jenis)
                                    <option value="{{ $jenis->id }}" {{ (request('jenis_obat_id') == $jenis->id ? 'selected' : '') }}>{{ $jenis->jenis_obat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="date-input-group">
                            <label for="satuan_obat_id" class="date-label">Satuan Obat</label>
                            <select name="satuan_obat_id" id="satuan_obat_id" class="date-input">
                                <option value="">Semua Satuan Obat</option>
                                @foreach ($satuanobats as $satuan)
                                    <option value="{{ $satuan->id }}" {{ (request('satuan_obat_id') == $satuan->id ? 'selected' : '') }}>{{ $satuan->satuan_obat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <a href="{{ route('nama-obat.index') }}" class="btn-reset" style="display:flex;align-items:center;gap:6px;background:#6b7280;color:white;padding:8px 14px;border-radius:6px;text-decoration:none;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.995-1.465" />
                            </svg>
                            <span>Reset</span>
                        </a>
                    </div>
                </form>

                <!-- BUTTON TAMBAH -->
                @if(!(auth()->check() && auth()->user()->role === 'petugas_administrasi'))
                    <button type="button" id="openCreateModal" class="btn-add">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Tambah</span>
                    </button>
                @endif
            </div>
            <table class="nama-obat-table">
                <thead>
                <tr>
                    <th>No</th>
                    <x-sortable-th column="kode_obat" label="Kode Obat" />
                    <x-sortable-th column="nama_obat" label="Nama Obat" />
                    <x-sortable-th column="jenis_obat_id" label="Jenis Obat" />
                    <x-sortable-th column="satuan_obat_id" label="Satuan Obat" />
                    <x-sortable-th column="lokasi_penyimpanan" label="Lokasi Penyimpanan" />
                    <x-sortable-th column="stok" label="Stok" />
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                    @forelse ($namaobats as $namaobat)
                        <tr>
                            <td>{{ $namaobat->id }}</td>
                            <td>{{ $namaobat->kode_obat }}</td>
                            <td>{{ $namaobat->nama_obat }}</td>
                            <td>{{ $namaobat->jenisObat->jenis_obat }}</td>
                            <td>{{ $namaobat->satuanObat->satuan_obat }}</td>
                            <td>{{ $namaobat->lokasi_penyimpanan }}</td>
                            <td>
                                @php
                                    $totalStok = $namaobat->total_stok ?? 0;
                                @endphp
                                {{ $totalStok }}
                            </td>
                            {{-- <td>{{ $namaobat->created_at->format('d M Y') }}</td> --}}
                            <td>
                                <div class="action-buttons">
                                    <!-- VIEW stock details (modal) -->
                                    <button type="button"
                                        class="action-btn view openStockModal"
                                        title="Lihat Stok"
                                        data-nama-id="{{ $namaobat->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.64 0 8.576 3.01 9.964 7.183a1.012 1.012 0 0 1 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.64 0-8.576-3.01-9.964-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                        </svg>
                                    </button>

                                    @unless(auth()->check() && auth()->user()->role === 'petugas_administrasi')
                                        <!-- EDIT (open modal) -->
                                        <button type="button"
                                            class="action-btn edit openEditModal"
                                            title="Edit"
                                            data-id="{{ $namaobat->id }}"
                                            data-kode_obat="{{ $namaobat->kode_obat }}"
                                            data-nama_obat="{{ $namaobat->nama_obat }}"
                                            data-jenis_id="{{ $namaobat->jenis_obat_id }}"
                                            data-satuan_id="{{ $namaobat->satuan_obat_id }}"
                                            data-lokasi="{{ $namaobat->lokasi_penyimpanan }}">
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

                                        @php
                                            $isNamaObatUsed =
                                                ($namaobat->detail_penerimaan_obat_count ?? 0) > 0 ||
                                                ($namaobat->detail_pengeluaran_obat_count ?? 0) > 0 ||
                                                ($namaobat->detail_pemusnahan_obat_count ?? 0) > 0 ||
                                                ($namaobat->stok_obat_count ?? 0) > 0 ||
                                                ($namaobat->min_max_count ?? 0) > 0;
                                        @endphp

                                        @if ($isNamaObatUsed)
                                            <button type="button" class="action-btn delete disabled" title="Tidak bisa dihapus karena sudah digunakan pada transaksi/data lain" disabled>
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
                                        @else
                                            <x-confirm-delete action="{{ route('nama-obat.destroy', $namaobat->id) }}" :id="'delete-nama-obat-'.$namaobat->id" title="Hapus Nama Obat" message="Yakin ingin menghapus nama obat {{ $namaobat->nama_obat }}?">
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
                                        @endif
                                    @endunless

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">Tidak ada data obat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="pagination-section">
                <div class="pagination-controls">
                    <div class="per-page-selector">
                        <form method="GET" action="{{ route('nama-obat.index') }}" class="per-page-form">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="jenis_obat_id" value="{{ request('jenis_obat_id') }}">
                            <input type="hidden" name="satuan_obat_id" value="{{ request('satuan_obat_id') }}">
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                            <input type="hidden" name="direction" value="{{ request('direction') }}">
                            <label for="per_page_footer" class="per-page-label">Tampilkan:</label>
                            <select name="per_page" id="per_page_footer" class="per-page-input" onchange="this.form.submit()">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="pagination-wrapper">
                    {{ $namaobats->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        <!-- Stock Details Modal -->
        <div id="stockModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="stockModalTitle">
                <div class="modal-header">
                    <h2 id="stockModalTitle">Rincian Stok</h2>
                    <button class="modal-close" id="closeStockModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    <div id="stockModalContent">
                        <p style="text-align: center; color: #6b7280;">Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div id="createNamaObatModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createNamaObatTitle">
                <div class="modal-header">
                    <h2 id="createNamaObatTitle">Tambah Obat</h2>
                    <button class="modal-close" id="closeCreateNamaObatModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && !session('edit_nama_obat_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('nama-obat.store') }}" method="POST" class="form-component">
                        @csrf

                        <div class="form-group">
                            <label for="nama_obat">Nama Obat</label>
                            <input id="nama_obat" type="text" name="nama_obat" value="{{ old('nama_obat') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="create_jenis_obat_id">Jenis Obat</label>
                            <select id="create_jenis_obat_id" name="jenis_obat_id" class="js-modal-select2" data-placeholder="Pilih Jenis Obat" required>
                                <option value="">Pilih Jenis Obat</option>
                                @foreach(($jenisobats ?? []) as $jenis)
                                    <option value="{{ $jenis->id }}" {{ old('jenis_obat_id') == $jenis->id ? 'selected' : '' }}>{{ $jenis->jenis_obat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="create_satuan_obat_id">Satuan Obat</label>
                            <select id="create_satuan_obat_id" name="satuan_obat_id" class="js-modal-select2" data-placeholder="Pilih Satuan Obat" required>
                                <option value="">Pilih Satuan Obat</option>
                                @foreach(($satuanobats ?? []) as $satuan)
                                    <option value="{{ $satuan->id }}" {{ old('satuan_obat_id') == $satuan->id ? 'selected' : '' }}>{{ $satuan->satuan_obat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="lokasi_penyimpanan">Lokasi Penyimpanan</label>
                            <input id="lokasi_penyimpanan" type="text" name="lokasi_penyimpanan" value="{{ old('lokasi_penyimpanan') }}" required>
                        </div>

                        <div class="form-actions modal-actions">
                            <button type="button" class="btn-secondary" id="cancelCreateNamaObatModal">Batal</button>
                            <button type="submit" class="btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="editNamaObatModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="editNamaObatTitle">
                <div class="modal-header">
                    <h2 id="editNamaObatTitle">Edit Nama Obat</h2>
                    <button class="modal-close" id="closeEditNamaObatModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && session('edit_nama_obat_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="editNamaObatForm" action="" method="POST" class="form-component">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-group">
                            <label for="edit_nama_obat">Nama Obat</label>
                            <input id="edit_nama_obat" type="text" name="nama_obat" value="{{ old('nama_obat') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_jenis_obat_id">Jenis Obat</label>
                            <select id="edit_jenis_obat_id" name="jenis_obat_id" class="js-modal-select2" data-placeholder="Pilih Jenis Obat" required>
                                <option value="">Pilih Jenis Obat</option>
                                @foreach(($jenisobats ?? []) as $jenis)
                                    <option value="{{ $jenis->id }}">{{ $jenis->jenis_obat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_satuan_obat_id">Satuan Obat</label>
                            <select id="edit_satuan_obat_id" name="satuan_obat_id" class="js-modal-select2" data-placeholder="Pilih Satuan Obat" required>
                                <option value="">Pilih Satuan Obat</option>
                                @foreach(($satuanobats ?? []) as $satuan)
                                    <option value="{{ $satuan->id }}">{{ $satuan->satuan_obat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_lokasi_penyimpanan">Lokasi Penyimpanan</label>
                            <input id="edit_lokasi_penyimpanan" type="text" name="lokasi_penyimpanan" value="{{ old('lokasi_penyimpanan') }}" required>
                        </div>

                        <div class="form-actions modal-actions">
                            <button type="button" class="btn-secondary" id="cancelEditNamaObatModal">Batal</button>
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
        function initModalSelect2(modalElement) {
            if (!modalElement || !window.jQuery || !jQuery.fn.select2) return;

            modalElement.querySelectorAll('.js-modal-select2').forEach(function(select) {
                const $select = jQuery(select);

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.select2({
                    width: '100%',
                    dropdownParent: jQuery(modalElement),
                    placeholder: select.dataset.placeholder || 'Pilih',
                    allowClear: false,
                });
            });
        }

        // Create modal
        const openBtn = document.getElementById('openCreateModal');
        const modal = document.getElementById('createNamaObatModal');
        const closeBtn = document.getElementById('closeCreateNamaObatModal');
        const cancelBtn = document.getElementById('cancelCreateNamaObatModal');

        const createForm = document.querySelector('#createNamaObatModal form');

        function openModal() {
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            window.requestAnimationFrame(function() {
                initModalSelect2(modal);
            });
        }

        function clearCreateForm() {
            if (!createForm) return;
            createForm.reset();

            if (window.jQuery) {
                createForm.querySelectorAll('select').forEach(select => {
                    if (jQuery(select).data('select2')) {
                        jQuery(select).trigger('change');
                    }
                });
            }
        }

        function removeErrorList(targetModal) {
            if (!targetModal) return;
            const errorList = targetModal.querySelector('.error-list');
            if (errorList) {
                errorList.remove();
            }
        }

        function closeModal() {
            if (!modal) return;
            removeErrorList(modal);
            clearCreateForm();
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
        }

        openBtn && openBtn.addEventListener('click', openModal);
        closeBtn && closeBtn.addEventListener('click', closeModal);
        cancelBtn && cancelBtn.addEventListener('click', closeModal);

        // Auto open create modal if validation errors belong to create
        @if ($errors->any() && !session('edit_nama_obat_id') && !(auth()->check() && auth()->user()->role === 'petugas_administrasi'))
            document.addEventListener('DOMContentLoaded', function() { openModal(); });
        @endif

        // Edit modal handlers
        const editModal = document.getElementById('editNamaObatModal');
        const openEditButtons = document.querySelectorAll('.openEditModal');
        // correct ids for edit modal
        const closeEditBtn = document.getElementById('closeEditNamaObatModal');
        const cancelEditBtn = document.getElementById('cancelEditNamaObatModal');
        const editForm = document.getElementById('editNamaObatForm');

        function openEditModal() {
            if (!editModal) return;
            editModal.classList.remove('hidden');
            editModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            window.requestAnimationFrame(function() {
                initModalSelect2(editModal);
            });
        }

        function removeErrorList(targetModal) {
            if (!targetModal) return;
            const errorList = targetModal.querySelector('.error-list');
            if (errorList) {
                errorList.remove();
            }
        }

        function closeEditModal() {
            if (!editModal) return;
            removeErrorList(editModal);
            editModal.classList.add('hidden');
            editModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
        }

        function populateEditForm(data) {
            if (!editForm) return;
            try {
                editForm.action = '/nama-obat/' + data.id;
                const kodeEl = document.getElementById('edit_kode_obat');
                const namaEl = document.getElementById('edit_nama_obat');
                if (kodeEl) kodeEl.value = data.kode_obat || '';
                if (namaEl) namaEl.value = data.nama_obat || '';

                const jenisSelect = document.getElementById('edit_jenis_obat_id');
                const satuanSelect = document.getElementById('edit_satuan_obat_id');
                if (jenisSelect) {
                    jenisSelect.value = data.jenis_id || '';
                    if (window.jQuery) {
                        jQuery(jenisSelect).trigger('change');
                    }
                }
                if (satuanSelect) {
                    satuanSelect.value = data.satuan_id || '';
                    if (window.jQuery) {
                        jQuery(satuanSelect).trigger('change');
                    }
                }
                const lokasiEl = document.getElementById('edit_lokasi_penyimpanan');
                if (lokasiEl) lokasiEl.value = data.lokasi || '';
            } catch (err) {
                console.error('populateEditForm error:', err);
            }
        }

        openEditButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                try {
                    const data = {
                        id: this.dataset.id,
                        kode_obat: this.dataset.kode_obat,
                        nama_obat: this.dataset.nama_obat,
                        jenis_id: this.dataset.jenis_id,
                        satuan_id: this.dataset.satuan_id,
                        lokasi: this.dataset.lokasi || ''
                    };

                    if (window.console && window.console.log) console.log('edit button clicked, id=', data.id);

                    populateEditForm(data);
                    openEditModal();
                } catch (err) {
                    console.error('openEditButtons click handler error:', err);
                }
            });
        });

        closeEditBtn && closeEditBtn.addEventListener('click', closeEditModal);
        cancelEditBtn && cancelEditBtn.addEventListener('click', closeEditModal);

        const editNamaObatIdFromServer = @json(session('edit_nama_obat_id'));
        const oldInput = @json(session()->getOldInput());

        if (editNamaObatIdFromServer) {
            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.querySelector('.openEditModal[data-id="' + editNamaObatIdFromServer + '"]');
                if (btn) {
                    const data = {
                        id: btn.dataset.id,
                        kode_obat: btn.dataset.kode_obat,
                        nama_obat: btn.dataset.nama_obat,
                        jenis_id: btn.dataset.jenis_id,
                        satuan_id: btn.dataset.satuan_id,
                        lokasi: btn.dataset.lokasi || ''
                    };
                    populateEditForm(data);
                }

                if (oldInput && Object.keys(oldInput).length) {
                    if (oldInput.kode_obat) document.getElementById('edit_kode_obat').value = oldInput.kode_obat;
                    if (oldInput.nama_obat) document.getElementById('edit_nama_obat').value = oldInput.nama_obat;
                    if (oldInput.jenis_obat_id) {
                        const oldJenis = document.getElementById('edit_jenis_obat_id');
                        if (oldJenis) oldJenis.value = oldInput.jenis_obat_id;
                    }
                    if (oldInput.satuan_obat_id) {
                        const oldSatuan = document.getElementById('edit_satuan_obat_id');
                        if (oldSatuan) oldSatuan.value = oldInput.satuan_obat_id;
                    }
                    if (oldInput.lokasi_penyimpanan) {
                        const oldLokasi = document.getElementById('edit_lokasi_penyimpanan');
                        if (oldLokasi) oldLokasi.value = oldInput.lokasi_penyimpanan;
                    }
                }

                initModalSelect2(editModal);

                @if(!(auth()->check() && auth()->user()->role === 'petugas_administrasi'))
                    openEditModal();
                @endif
            });
        }

        // Auto-generate kode_obat when jenis is selected (create & edit)
        (function() {
            const createJenis = document.getElementById('create_jenis_obat_id');
            const createKode = document.getElementById('kode_obat');
            const editJenis = document.getElementById('edit_jenis_obat_id');
            const editKode = document.getElementById('edit_kode_obat');

            function fetchKode(jenisId, cb) {
                if (!jenisId) { cb(''); return; }
                fetch('/nama-obat/generate-kode/' + jenisId)
                    .then(r => r.json())
                    .then(d => cb(d.kode || ''))
                    .catch(() => cb(''));
            }

            if (createJenis && createKode) {
                createJenis.addEventListener('change', function() {
                    fetchKode(this.value, function(k) { createKode.value = k; });
                });
                // if there's an initial value (old input), trigger to fill kode
                document.addEventListener('DOMContentLoaded', function() {
                    if (createJenis.value) createJenis.dispatchEvent(new Event('change'));
                });
            }

            if (editJenis && editKode) {
                editJenis.addEventListener('change', function() {
                    fetchKode(this.value, function(k) { editKode.value = k; });
                });
            }
        })();

        // Stock Modal handlers
        const stockModal = document.getElementById('stockModal');
        const closeStockModalBtn = document.getElementById('closeStockModal');
        const openStockButtons = document.querySelectorAll('.openStockModal');

        function openStockModal() {
            if (!stockModal) return;
            stockModal.classList.remove('hidden');
            stockModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeStockModal() {
            if (!stockModal) return;
            stockModal.classList.add('hidden');
            stockModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
        }

        function loadStockData(namaObatId) {
            const contentDiv = document.getElementById('stockModalContent');
            contentDiv.innerHTML = '<p style="text-align: center; color: #6b7280;">Memuat data...</p>';

            fetch(`/nama-obat/${namaObatId}/stok`)
                .then(response => response.json())
                .then(data => {
                    const titleEl = document.getElementById('stockModalTitle');
                    titleEl.textContent = data.nama_obat;

                    if (data.stokItems.length === 0) {
                        contentDiv.innerHTML = '<p style="text-align: center; color: #9ca3af;">Belum ada data stok untuk obat ini.</p>';
                        return;
                    }

                    let tableHtml = '<table class="nama-obat-table" style="margin-top: 0;"><thead><tr><th>No</th><th>No. Batch</th><th>Tanggal Kadaluwarsa</th><th>Stok</th><th>Status</th></tr></thead><tbody>';

                    data.stokItems.forEach((item, idx) => {
                        const statusClass = item.status_class ? item.status_class : '';
                        tableHtml += `<tr>
                            <td>${idx + 1}</td>
                            <td>${item.no_batch}</td>
                            <td>${item.tanggal_kadaluwarsa}</td>
                            <td>${item.stok}</td>
                            <td><span class="status-badge ${statusClass}">${item.status}</span></td>
                        </tr>`;
                    });

                    tableHtml += '</tbody></table>';
                    contentDiv.innerHTML = tableHtml;
                })
                .catch(err => {
                    console.error('Error loading stock data:', err);
                    contentDiv.innerHTML = '<p style="text-align: center; color: #dc2626;">Gagal memuat data. Silakan coba lagi.</p>';
                });
        }

        openStockButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const namaObatId = this.dataset.namaId;
                loadStockData(namaObatId);
                openStockModal();
            });
        });

        closeStockModalBtn && closeStockModalBtn.addEventListener('click', closeStockModal);
        stockModal && stockModal.addEventListener('click', function(e) {
            if (e.target === stockModal) closeStockModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !stockModal.classList.contains('hidden')) closeStockModal();
        });

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

    })();
</script>

</body>
</html>
