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
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Penerimaan Obat - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('css/penerimaan_obat/penerimaan_obat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">
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
                                placeholder="Cari no. batch atau nama obat..."
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

                            <a href="{{ route('penerimaan-obat.index') }}" class="btn-filter btn-reset">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.995-1.465" />
                                </svg>
                                <span>Reset</span>
                            </a>
                        </div>
                    </div>
                </form>

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
                    <x-sortable-th column="no_batch" label="No. Batch" />
                    <x-sortable-th column="tanggal_penerimaan" label="Tanggal Penerimaan" />
                    <th>Nama Petugas</th>
                    <th>Jumlah Item</th>
                    <th>Aksi</th>
                </tr>
                </thead>

                <tbody>
                    @forelse ($penerimaanObats as $penerimaan)
                        @php
                            $usageCheck = $penerimaan->checkUsage();
                        @endphp

                        <tr class="penerimaan-main-row" data-penerimaan-id="{{ $penerimaan->id }}" role="button" tabindex="0" aria-expanded="false" aria-controls="detail-row-{{ $penerimaan->id }}">
                            <td style="color: blue;">{{ $penerimaan->no_batch }}</td>
                            <td>{{ $penerimaan->tanggal_penerimaan ? \Carbon\Carbon::parse($penerimaan->tanggal_penerimaan)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                            <td>{{ $penerimaan->user?->name ?? '-' }}</td>
                            <td>{{ $penerimaan->detailPenerimaanObat->count() }} item(s)</td>
                            <td>
                                <div class="action-buttons">
                                    @if ($usageCheck['used'])
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

                        <tr class="penerimaan-detail-row hidden" id="detail-row-{{ $penerimaan->id }}">
                            <td colspan="5">
                                <div class="detail-panel">
                                    @if ($penerimaan->detailPenerimaanObat->isNotEmpty())
                                        <div class="detail-table-wrapper">
                                            <table class="expanded-detail-table">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Obat</th>
                                                        <th>Jenis Obat</th>
                                                        <th>Tanggal Kadaluwarsa</th>
                                                        <th>Jumlah Masuk</th>
                                                        <th>Satuan</th>
                                                        <th>Lokasi Penyimpanan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($penerimaan->detailPenerimaanObat as $detail)
                                                        <tr>
                                                            <td>{{ $detail->namaObat?->nama_obat ?? '-' }}</td>
                                                            <td>{{ $detail->jenisObat?->jenis_obat ?? '-' }}</td>
                                                            <td>{{ $detail->tanggal_kadaluwarsa ? \Carbon\Carbon::parse($detail->tanggal_kadaluwarsa)->format('d M Y') : '-' }}</td>
                                                            <td>{{ $detail->jumlah_masuk }}</td>
                                                            <td>{{ $detail->satuan?->satuan_obat ?? '-' }}</td>
                                                            <td>{{ $detail->lokasi_penyimpanan ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="detail-empty-state">Tidak ada detail obat pada penerimaan ini.</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                                <td colspan="5" class="empty">Tidak ada data penerimaan obat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

                <div class="pagination-section">
                    <div class="pagination-controls">
                        <div class="per-page-selector">
                            <form method="GET" action="{{ route('penerimaan-obat.index') }}" class="per-page-form">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
                                <input type="hidden" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
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
                        {{ $penerimaanObats->appends(request()->query())->links() }}
                    </div>
                </div>
        </div>

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

                    <form id="createPenerimaanForm" action="{{ route('penerimaan-obat.store') }}" method="POST" class="form-component">
                        @csrf

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="user_create">Nama Petugas</label>
                                <input id="user_create" type="text" value="{{ Auth::user()->name }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="tanggal_penerimaan">Tanggal Penerimaan</label>
                                <input id="tanggal_penerimaan" type="date" name="tanggal_penerimaan_disabled" value="{{ old('tanggal_penerimaan', now()->format('Y-m-d')) }}" required disabled>
                                <input type="hidden" name="tanggal_penerimaan" value="{{ old('tanggal_penerimaan', now()->format('Y-m-d')) }}">
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

                    </form>
                </div>

                <div class="modal-actions modal-footer-persistent">
                    <button type="button" class="btn-secondary" id="cancelCreatePenerimaanModal">Batal</button>
                    <button type="submit" class="btn-primary" form="createPenerimaanForm">Simpan</button>
                </div>
            </div>
        </div>



    </div>
    </div>

<script>
    const namaObats = @json($namaObats);
    const jenisobats = @json($jenisobats);
    const satuanobats = @json($satuanobats);

    window.oldDetails = @json(old('details', []));

    function getTodayDate() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function createDetailHTML(index, data = {}, options = {}) {
        const showDelete = options.showDelete !== false;

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
                ${showDelete ? `
                <td>
                    <button type="button" class="btn-delete-row" onclick="this.closest('tr').remove()" title="Hapus">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </td>` : ''}
            </tr>
        `;
    }

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

        const lokasiVal = selectedOption ? (selectedOption.dataset.lokasi || '') : '';
        if (lokasiInput && lokasiVal) {
            lokasiInput.value = lokasiVal;
        }
    }

    function attachDetailEventListeners() {
        document.querySelectorAll('.nama-obat-select').forEach(select => {
            try {
                if (window.jQuery && jQuery(select).hasClass('select2-hidden-accessible')) {
                    jQuery(select).select2('destroy');
                }
            } catch (err) {}

            try {
                if (window.jQuery) {
                    const $s = jQuery(select);
                    let parent = $s.closest('.modal');
                    if (!parent || parent.length === 0) parent = jQuery(document.body);
                    $s.select2({ width: '100%', dropdownParent: parent, placeholder: 'Pilih Obat', allowClear: true });
                }
            } catch (err) {}

            select.removeEventListener('change', namaObatChangeHandler);
            select.addEventListener('change', namaObatChangeHandler);

            try {
                if (window.jQuery) {
                    jQuery(select).off('change.pustumed').on('change.pustumed', function(e) { namaObatChangeHandler(e); });
                }
            } catch (err) {}
        });
    }

    function togglePenerimaanDetail(row) {
        if (!row) return;

        const penerimaanId = row.dataset.penerimaanId;
        const detailRow = document.getElementById(`detail-row-${penerimaanId}`);
        const icon = document.getElementById(`toggle-icon-${penerimaanId}`);
        const expanded = row.getAttribute('aria-expanded') === 'true';

        row.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        if (detailRow) {
            detailRow.classList.toggle('hidden', expanded);
        }
        if (icon) {
            icon.textContent = expanded ? '▸' : '▾';
        }
    }

    (function() {
        const penerimaanTable = document.querySelector('.penerimaan-obat-table tbody');

        if (penerimaanTable) {
            penerimaanTable.addEventListener('click', function(e) {
                const row = e.target.closest('.penerimaan-main-row');
                if (!row) return;

                if (e.target.closest('button, a, input, select, textarea, .select2-container, .action-buttons')) {
                    return;
                }

                togglePenerimaanDetail(row);
            });

            penerimaanTable.addEventListener('keydown', function(e) {
                const row = e.target.closest('.penerimaan-main-row');
                if (!row) return;

                if (e.target.closest('button, a, input, select, textarea, .select2-container, .action-buttons')) {
                    return;
                }

                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    togglePenerimaanDetail(row);
                }
            });
        }

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


        @if ($errors->any() && !session('edit_penerimaan_obat_id'))
            document.addEventListener('DOMContentLoaded', function() {
                openCreateModal();
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



        // Auto submit filter form with debounce.
        const filterForm = document.querySelector('.table-actions form[method="GET"]');
        if (filterForm) {
            const searchField = filterForm.querySelector('input[name="search"]');
            const filterFields = filterForm.querySelectorAll('input[type="date"], select');
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
