@php
    function sortLink($column, $label) {
        $direction = request('direction') === 'asc' ? 'desc' : 'asc';
        $params = array_merge(request()->all(), [
            'sort' => $column,
            'direction' => $direction
        ]);

        return '<a href="'.route('penerimaan-obat.index', $params).'">'.$label.'</a>';
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerimaan Obat - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/penerimaan_obat/penerimaan_obat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
</head>
<body>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <x-sidebar />

    <div class="main-wrapper">
        <x-navbar />

        <div class="container main-content">

        <div class="page-header">
            <h1>Penerimaan Obat</h1>
        </div>

        <x-alert type="success" />
        <x-alert type="error" />

        <div class="card">
            <div class="table-actions">
                <form method="GET" action="{{ route('penerimaan-obat.index') }}">
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
                                placeholder="Cari no. batch..."
                                value="{{ request('search') }}"
                                class="search-input"
                            >
                        </div>

                        <div class="date-filter-wrapper">
                            <div class="date-input-group">
                                <label for="tanggal_awal" class="date-label">Tanggal Awal</label>
                                <input
                                    type="date"
                                    name="tanggal_awal"
                                    id="tanggal_awal"
                                    value="{{ request('tanggal_awal') }}"
                                    class="date-input"
                                >
                            </div>

                            <div class="date-input-group">
                                <label for="tanggal_akhir" class="date-label">Tanggal Akhir</label>
                                <input
                                    type="date"
                                    name="tanggal_akhir"
                                    id="tanggal_akhir"
                                    value="{{ request('tanggal_akhir') }}"
                                    class="date-input"
                                >
                            </div>

                            <button type="submit" class="btn-filter">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <span>Cari</span>
                            </button>
                        </div>
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
            <table class="penerimaan-obat-table">
                <thead>
                <tr>
                    <th>No</th>
                    <x-sortable-th column="no_batch" label="No. Batch" />
                    <x-sortable-th column="tanggal_penerimaan" label="Tanggal Penerimaan" />
                    <th>Nama Petugas</th>
                    <th>Jumlah Item</th>
                    <th>Aksi</th>
                </tr>
                </thead>

                <tbody>
                    @forelse ($penerimaanObats as $penerimaan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $penerimaan->no_batch }}</td>
                            <td>{{ $penerimaan->tanggal_penerimaan ? \Carbon\Carbon::parse($penerimaan->tanggal_penerimaan)->format('d M Y') : '-' }}</td>
                            <td>{{ $penerimaan->user?->name ?? '-' }}</td>
                            <td>{{ $penerimaan->detailPenerimaanObat->count() }} item(s)</td>
                            <td>
                                <div class="action-buttons">
                                    @php
                                        $usageCheck = $penerimaan->checkUsage();
                                    @endphp

                                    <!-- EDIT (open modal) -->
                                    <button type="button"
                                        class="action-btn edit openEditModal"
                                        title="Edit"
                                        data-id="{{ $penerimaan->id }}"
                                        data-no_batch="{{ $penerimaan->no_batch }}"
                                        data-tanggal_penerimaan="{{ $penerimaan->tanggal_penerimaan }}"
                                        data-keterangan="{{ $penerimaan->keterangan }}"
                                        data-details="{{ json_encode($penerimaan->detailPenerimaanObat) }}">
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
                                    @if ($usageCheck['used'])
                                        <!-- Button disabled with tooltip -->
                                        <button type="button"
                                            class="action-btn delete"
                                            disabled
                                            title="{{ $usageCheck['message'] }}"
                                            style="opacity: 0.5; cursor: not-allowed;">
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
                                        <x-confirm-delete action="{{ route('penerimaan-obat.destroy', $penerimaan->id) }}" :id="'delete-penerimaan-'.$penerimaan->id" title="Hapus Penerimaan Obat" message="Yakin ingin menghapus penerimaan obat {{ $penerimaan->no_batch }}?">
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

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">Tidak ada data penerimaan obat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Create Modal -->
        <div id="createPenerimaanModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createPenerimaanTitle">
                <div class="modal-header">
                    <h2 id="createPenerimaanTitle">Penerimaan Obat</h2>
                    <button class="modal-close" id="closeCreatePenerimaanModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && !session('edit_penerimaan_obat_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('penerimaan-obat.store') }}" method="POST" class="form-component">
                        @csrf

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="user_create">Nama Petugas</label>
                                <input id="user_create" type="text" value="{{ Auth::user()->name }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="tanggal_penerimaan">Tanggal Penerimaan</label>
                                <input id="tanggal_penerimaan" type="date" name="tanggal_penerimaan" value="{{ old('tanggal_penerimaan', now()->format('Y-m-d')) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea id="keterangan" name="keterangan" rows="2">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>

                        <div style="margin-top: 16px; border-top: 1px solid #e5e7eb; padding-top: 12px;">
                            <h4 style="margin: 0 0 12px 0; font-size: 14px; color: #374151; font-weight: 600;">Detail Obat</h4>
                            <div class="table-wrapper">
                                <table class="detail-table">
                                    <thead>
                                        <tr>
                                            <th>Nama Obat</th>
                                            <th>Jenis Obat</th>
                                            <th>Tanggal Kadaluwarsa</th>
                                            <th>Jumlah Masuk</th>
                                            <th>Satuan</th>
                                            <th>Lokasi Penyimpanan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailItemsCreate"></tbody>
                                </table>
                            </div>
                            <button type="button" class="btn-add-detail" id="addDetailCreate">+ Tambah Item</button>
                        </div>

                        <div class="form-actions modal-actions" style="margin-top: 16px;">
                            <button type="button" class="btn-secondary" id="cancelCreatePenerimaanModal">Batal</button>
                            <button type="submit" class="btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="editPenerimaanModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="editPenerimaanTitle">
                <div class="modal-header">
                    <h2 id="editPenerimaanTitle">Penerimaan Obat</h2>
                    <button class="modal-close" id="closeEditPenerimaanModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && session('edit_penerimaan_obat_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="editPenerimaanForm" action="" method="POST" class="form-component">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="user_edit">Nama Petugas</label>
                                <input id="user_edit" type="text" value="{{ Auth::user()->name }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="edit_tanggal_penerimaan">Tanggal Penerimaan</label>
                                <input id="edit_tanggal_penerimaan" type="date" name="tanggal_penerimaan" value="{{ old('tanggal_penerimaan') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="edit_keterangan">Keterangan</label>
                                <textarea id="edit_keterangan" name="keterangan" rows="2">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>

                        <div style="margin-top: 16px; border-top: 1px solid #e5e7eb; padding-top: 12px;">
                            <h4 style="margin: 0 0 12px 0; font-size: 14px; color: #374151; font-weight: 600;">Detail Obat</h4>
                            <div class="table-wrapper">
                                <table class="detail-table">
                                    <thead>
                                        <tr>
                                            <th>Nama Obat</th>
                                            <th>Jenis Obat</th>
                                            <th>Tanggal Kadaluwarsa</th>
                                            <th>Jumlah Masuk</th>
                                            <th>Satuan</th>
                                            <th>Lokasi Penyimpanan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailItemsEdit"></tbody>
                                </table>
                            </div>
                            <button type="button" class="btn-add-detail" id="addDetailEdit">+ Tambah Item</button>
                        </div>

                        <div class="form-actions modal-actions" style="margin-top: 16px;">
                            <button type="button" class="btn-secondary" id="cancelEditPenerimaanModal">Batal</button>
                            <button type="submit" class="btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    </div>

<script>
    const namaObats = @json($namaObats);
    const jenisobats = @json($jenisobats);
    const satuanobats = @json($satuanobats);

    // Old input from previous request (for restoring form on validation errors)
    window.oldDetails = @json(old('details', []));
    window.oldEditPenerimaanId = @json(session('edit_penerimaan_obat_id'));
    window.oldInput = @json(session()->getOldInput());

    // Helper function to get today's date in YYYY-MM-DD format
    function getTodayDate() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function createDetailHTML(index, data = {}) {
        return `
            <tr class="detail-row" data-detail-index="${index}">
                <td>
                    <select name="details[${index}][nama_obat_id]" class="nama-obat-select table-input" data-detail-index="${index}" required>
                        <option value="">Pilih Obat</option>
                        ${namaObats.map(o => `<option value="${o.id}" data-jenis-id="${o.jenis_obat_id}" data-satuan-id="${o.satuan_obat_id}" data-lokasi="${o.lokasi_penyimpanan || ''}" ${data.nama_obat_id == o.id ? 'selected' : ''}>${o.nama_obat}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select class="jenis-obat-field table-input" data-detail-index="${index}" disabled>
                        <option value="">-</option>
                        ${jenisobats.map(j => `<option value="${j.id}" ${data.jenis_obat_id == j.id ? 'selected' : ''}>${j.jenis_obat}</option>`).join('')}
                    </select>
                    <input type="hidden" name="details[${index}][jenis_obat_id]" class="jenis-obat-hidden" data-detail-index="${index}" value="${data.jenis_obat_id || ''}">
                </td>
                <td>
                    <input type="date" class="table-input" name="details[${index}][tanggal_kadaluwarsa]" value="${data.tanggal_kadaluwarsa || ''}" required>
                </td>
                <td>
                    <input type="number" class="table-input" name="details[${index}][jumlah_masuk]" value="${data.jumlah_masuk || ''}" min="1" required style="width: 70px;">
                </td>
                <td>
                    <select class="satuan-obat-field table-input" data-detail-index="${index}" disabled>
                        <option value="">-</option>
                        ${satuanobats.map(s => `<option value="${s.id}" ${data.satuan_id == s.id ? 'selected' : ''}>${s.satuan_obat}</option>`).join('')}
                    </select>
                    <input type="hidden" name="details[${index}][satuan_id]" class="satuan-obat-hidden" data-detail-index="${index}" value="${data.satuan_id || ''}">
                </td>
                <td>
                    <input type="text" class="table-input" name="details[${index}][lokasi_penyimpanan]" value="${data.lokasi_penyimpanan || ''}" required readonly>
                </td>
                <td>
                    <button type="button" class="btn-delete-row" onclick="this.closest('tr').remove()" title="Hapus">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        `;
    }

    // handler extracted so it can be used by native and jQuery bindings
    function namaObatChangeHandler(e) {
        const select = e.target || e.currentTarget || (e && e.srcElement) || this;
        const detailIndex = select.dataset.detailIndex;
        const selectedOption = select.options[select.selectedIndex];
        const jenisId = selectedOption ? selectedOption.dataset.jenisId : null;
        const satuanId = selectedOption ? selectedOption.dataset.satuanId : null;

        const jenisSelect = document.querySelector(`.jenis-obat-field[data-detail-index="${detailIndex}"]`);
        const satuanSelect = document.querySelector(`.satuan-obat-field[data-detail-index="${detailIndex}"]`);
        const jenisHidden = document.querySelector(`.jenis-obat-hidden[data-detail-index="${detailIndex}"]`);
        const satuanHidden = document.querySelector(`.satuan-obat-hidden[data-detail-index="${detailIndex}"]`);
        const lokasiInput = document.querySelector(`input[name="details[${detailIndex}][lokasi_penyimpanan]"]`);

        if (jenisSelect && satuanSelect && jenisHidden && satuanHidden && jenisId && satuanId) {
            jenisSelect.value = jenisId;
            satuanSelect.value = satuanId;
            jenisHidden.value = jenisId;
            satuanHidden.value = satuanId;
        } else {
            if (jenisSelect) jenisSelect.value = '';
            if (satuanSelect) satuanSelect.value = '';
            if (jenisHidden) jenisHidden.value = '';
            if (satuanHidden) satuanHidden.value = '';
            if (lokasiInput) lokasiInput.value = '';
        }

        // set lokasi value if available in option
        const lokasiVal = selectedOption ? (selectedOption.dataset.lokasi || '') : '';
        if (lokasiInput && lokasiVal) {
            lokasiInput.value = lokasiVal;
        }
    }

    function attachDetailEventListeners() {
        document.querySelectorAll('.nama-obat-select').forEach(select => {
            // destroy select2 if present
            try {
                if (window.jQuery && jQuery(select).hasClass('select2-hidden-accessible')) {
                    jQuery(select).select2('destroy');
                }
            } catch (err) {}

            // init select2 for searchable dropdown
            try {
                if (window.jQuery) {
                    const $s = jQuery(select);
                    let parent = $s.closest('.modal');
                    if (!parent || parent.length === 0) parent = jQuery(document.body);
                    $s.select2({ width: '100%', dropdownParent: parent, placeholder: 'Pilih Obat', allowClear: true });
                }
            } catch (err) {}

            // native change handler
            select.removeEventListener('change', namaObatChangeHandler);
            select.addEventListener('change', namaObatChangeHandler);

            // jQuery fallback binding for select2
            try {
                if (window.jQuery) {
                    jQuery(select).off('change.pustumed').on('change.pustumed', function(e) { namaObatChangeHandler(e); });
                }
            } catch (err) {}
        });
    }

    (function() {
        // CREATE MODAL
        const openCreateBtn = document.getElementById('openCreateModal');
        const createModal = document.getElementById('createPenerimaanModal');
        const closeCreateBtn = document.getElementById('closeCreatePenerimaanModal');
        const cancelCreateBtn = document.getElementById('cancelCreatePenerimaanModal');
        const detailItemsCreateDiv = document.getElementById('detailItemsCreate');
        const addDetailCreateBtn = document.getElementById('addDetailCreate');
        let createDetailIndex = 0;

        function openCreateModal() {
            if (!createModal) return;
            createModal.classList.remove('hidden');
            createModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeCreateModal() {
            if (!createModal) return;
            createModal.classList.add('hidden');
            createModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
        }

        openCreateBtn && openCreateBtn.addEventListener('click', function() {
            createDetailIndex = 0;
            detailItemsCreateDiv.innerHTML = '';
            // Add one empty detail item
            detailItemsCreateDiv.insertAdjacentHTML('beforeend', createDetailHTML(createDetailIndex));
            createDetailIndex++;
            attachDetailEventListeners();
            const tanggalPenerimaanInput = document.getElementById('tanggal_penerimaan');
            if (tanggalPenerimaanInput) {
                tanggalPenerimaanInput.value = getTodayDate();
            }
            openCreateModal();
        });

        addDetailCreateBtn && addDetailCreateBtn.addEventListener('click', function(e) {
            e.preventDefault();
            detailItemsCreateDiv.insertAdjacentHTML('beforeend', createDetailHTML(createDetailIndex));
            createDetailIndex++;
            attachDetailEventListeners();
        });

        closeCreateBtn && closeCreateBtn.addEventListener('click', closeCreateModal);
        cancelCreateBtn && cancelCreateBtn.addEventListener('click', closeCreateModal);

        createModal && createModal.addEventListener('click', function(e) {
            if (e.target === createModal) closeCreateModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !createModal.classList.contains('hidden')) closeCreateModal();
        });

        @if ($errors->any() && !session('edit_penerimaan_obat_id'))
            document.addEventListener('DOMContentLoaded', function() {
                openCreateModal();
                // Restore detail items from old input if available
                if (window.oldDetails && window.oldDetails.length) {
                    detailItemsCreateDiv.innerHTML = '';
                    window.oldDetails.forEach((detail, idx) => {
                        detailItemsCreateDiv.insertAdjacentHTML('beforeend', createDetailHTML(idx, detail));
                        createDetailIndex = idx + 1;
                    });
                    attachDetailEventListeners();
                }
            });
        @endif

        // EDIT MODAL
        const editModal = document.getElementById('editPenerimaanModal');
        const openEditButtons = document.querySelectorAll('.openEditModal');
        const closeEditBtn = document.getElementById('closeEditPenerimaanModal');
        const cancelEditBtn = document.getElementById('cancelEditPenerimaanModal');
        const editPenerimaanForm = document.getElementById('editPenerimaanForm');
        const detailItemsEditDiv = document.getElementById('detailItemsEdit');
        const addDetailEditBtn = document.getElementById('addDetailEdit');
        let editDetailIndex = 0;

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

        openEditButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                try {
                    const id = this.dataset.id;
                    const tanggal = this.dataset.tanggal_penerimaan;
                    const keterangan = this.dataset.keterangan || '';
                    const details = JSON.parse(this.dataset.details || '[]');

                    editPenerimaanForm.action = '/penerimaan-obat/' + id;
                    document.getElementById('edit_tanggal_penerimaan').value = tanggal || '';
                    document.getElementById('edit_keterangan').value = keterangan;

                    detailItemsEditDiv.innerHTML = '';
                    editDetailIndex = 0;
                    details.forEach((detail, idx) => {
                        detailItemsEditDiv.insertAdjacentHTML('beforeend', createDetailHTML(idx, detail));
                        editDetailIndex = idx + 1;
                    });
                    attachDetailEventListeners();

                    openEditModal();
                } catch (err) {
                    console.error('edit button error:', err);
                }
            });
        });

            // If the server returned validation errors for an edit, restore the edit modal with old input
            if (window.oldEditPenerimaanId) {
                document.addEventListener('DOMContentLoaded', function() {
                    try {
                        // set form action
                        editPenerimaanForm.action = '/penerimaan-obat/' + window.oldEditPenerimaanId;

                        // restore tanggal_penerimaan from old input if present
                        if (window.oldInput && window.oldInput.tanggal_penerimaan) {
                            document.getElementById('edit_tanggal_penerimaan').value = window.oldInput.tanggal_penerimaan;
                        }

                        // restore keterangan from old input if present
                        if (window.oldInput && window.oldInput.keterangan) {
                            document.getElementById('edit_keterangan').value = window.oldInput.keterangan;
                        }

                        // restore details from old input
                        if (window.oldDetails && window.oldDetails.length) {
                            detailItemsEditDiv.innerHTML = '';
                            window.oldDetails.forEach((detail, idx) => {
                                detailItemsEditDiv.insertAdjacentHTML('beforeend', createDetailHTML(idx, detail));
                                editDetailIndex = idx + 1;
                            });
                            attachDetailEventListeners();
                        }

                        openEditModal();
                    } catch (err) {
                        console.error('restore edit modal error:', err);
                    }
                });
            }

        addDetailEditBtn && addDetailEditBtn.addEventListener('click', function(e) {
            e.preventDefault();
            detailItemsEditDiv.insertAdjacentHTML('beforeend', createDetailHTML(editDetailIndex));
            editDetailIndex++;
            attachDetailEventListeners();
        });

        closeEditBtn && closeEditBtn.addEventListener('click', closeEditModal);
        cancelEditBtn && cancelEditBtn.addEventListener('click', closeEditModal);

        editModal && editModal.addEventListener('click', function(e) {
            if (e.target === editModal) closeEditModal();
        });

    })();
</script>

</body>
</html>
