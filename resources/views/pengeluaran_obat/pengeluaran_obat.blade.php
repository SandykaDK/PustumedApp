@php
    function sortLink($column, $label) {
        $direction = request('direction') === 'asc' ? 'desc' : 'asc';
        $params = array_merge(request()->all(), [
            'sort' => $column,
            'direction' => $direction
        ]);
        return '<a href="'.route('pengeluaran-obat.index', $params).'">'.$label.'</a>';
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengeluaran Obat - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pengeluaran_obat/pengeluaran_obat.css') }}">
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
                <h1>Pengeluaran Obat</h1>
            </div>

            <x-alert type="success" />
            <x-alert type="error" />

            <div class="card">
                <div class="table-actions">
                    <form method="GET" action="{{ route('pengeluaran-obat.index') }}">
                        <div class="filter-row">
                            <div class="search-wrapper">
                                <span class="search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                </span>
                                <input type="text" name="search" class="search-input" placeholder="Cari keterangan..." value="{{ request('search') }}">
                            </div>

                            <div class="date-filter-wrapper">
                                <div class="date-input-group">
                                    <label for="tanggal_awal" class="date-label">Tanggal Awal</label>
                                    <input type="date" name="tanggal_awal" id="tanggal_awal" value="{{ request('tanggal_awal') }}" class="date-input">
                                </div>

                                <div class="date-input-group">
                                    <label for="tanggal_akhir" class="date-label">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="date-input">
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

                    <button type="button" id="openCreateModal" class="btn-add">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah
                    </button>
                </div>

                <table class="pengeluaran-obat-table">
                    <thead>
                        <tr>
                            <th>{!! sortLink('tanggal_pengeluaran', 'Tanggal') !!}</th>
                            <th>Pasien</th>
                            <th>Dokter</th>
                            <th>Jumlah Item</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pengeluaranObats as $record)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($record->tanggal_pengeluaran)->format('d/m/Y') }}</td>
                                <td>{{ $record->Pasien->nama ?? '-' }}</td>
                                <td>{{ $record->Dokter->nama ?? '-' }}</td>
                                <td>{{ count($record->detailPengeluaranObat) }}</td>
                                <td>{{ Str::limit($record->keterangan, 30) ?? '-' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('pengeluaran-obat.print', $record->id) }}" class="action-btn print" target="_blank" title="Cetak PDF">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                        </a>

                                        <button type="button" class="action-btn edit openEditModal" data-id="{{ $record->id }}" data-record='@json($record->load(["detailPengeluaranObat", "user", "dokter"]))'>
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

                                        <x-confirm-delete action="{{ route('pengeluaran-obat.destroy', $record->id) }}" :id="'delete-pengeluaran-'.$record->id" title="Hapus pengeluaran Obat" message="Yakin ingin menghapus pengeluaran obat {{ $record->no_batch }}?">
                                            <button type="button" class="action-btn delete">
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
                                <td colspan="8" class="empty">Tidak ada data pengeluaran obat</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $pengeluaranObats->render('pagination.custom') }}
                </div>
            </div>

            <!-- Create Modal -->
            <div id="createPengeluaranModal" class="modal hidden" aria-hidden="true">
                <div class="modal-content" role="dialog" aria-modal="true">
                    <div class="modal-header">
                        <h2>Pengeluaran Obat</h2>
                        <button type="button" id="closeCreatePengeluaranModal" class="modal-close" aria-label="Tutup">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>

                    <div id="createAlertContainer" style="margin-bottom: 16px; display: none;"></div>
                    <div class="modal-body">
                        <form id="createPengeluaranForm" method="POST" action="{{ route('pengeluaran-obat.store') }}" class="form-component">
                            @csrf

                            <div class="form-grid">
                                <div>
                                    <div class="form-group">
                                        <label for="tanggal_pengeluaran">Tanggal Pengeluaran</label>
                                        <input type="date" id="tanggal_pengeluaran" name="tanggal_pengeluaran" value="{{ old('tanggal_pengeluaran', now()->format('Y-m-d')) }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="pasien_id">Pasien</label>
                                        <select id="pasien_id" name="pasien_id" class="pasien-select" required>
                                            <option value="">Pilih Pasien</option>
                                            @foreach($pasienList as $pasien)
                                                <option value="{{ $pasien->id }}" data-no_bpjs="{{ $pasien->no_bpjs }}">{{ $pasien->no_bpjs }} - {{ $pasien->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <div class="form-group">
                                        <label for="user_name">Nama Petugas</label>
                                        <input type="text" id="user_name" name="user_name" value="{{ Auth::user()->name }}" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label for="dokter_id">Dokter</label>
                                        <select id="dokter_id" name="dokter_id" required>
                                            <option value="">Pilih Dokter</option>
                                            @foreach($dokterList as $dokter)
                                                <option value="{{ $dokter->id }}">{{ $dokter->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea id="keterangan" name="keterangan" rows="2"></textarea>
                            </div>

                            <div style="margin-top: 16px; border-top: 1px solid #e5e7eb; padding-top: 12px;">
                                <h4 style="margin: 0 0 12px 0; font-size: 14px; color: #374151; font-weight: 600;">Detail Obat</h4>
                                <div class="table-wrapper">
                                    <table class="detail-table">
                                        <thead>
                                            <tr>
                                                <th>Nama Obat</th>
                                                <th>Jumlah Keluar</th>
                                                <th>Tanggal Kadaluwarsa</th>
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

                            <div style="margin-top: 20px; display: flex; gap: 12px; justify-content: flex-end;">
                                <button type="button" id="cancelCreatePengeluaranModal" class="btn-secondary">Batal</button>
                                <button type="submit" class="btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div id="editPengeluaranModal" class="modal hidden" aria-hidden="true">
                <div class="modal-content" role="dialog" aria-modal="true">
                    <div class="modal-header">
                        <h2>Pengeluaran Obat</h2>
                        <button type="button" id="closeEditPengeluaranModal" class="modal-close" aria-label="Tutup">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>

                    <div id="editAlertContainer" style="margin-bottom: 16px; display: none;"></div>
                    <div class="modal-body">
                        <form id="editPengeluaranForm" method="POST" class="form-component">
                            @csrf
                            @method('PUT')

                            <div class="form-grid">
                                <div>
                                    <div class="form-group">
                                        <label for="edit_tanggal_pengeluaran">Tanggal Pengeluaran</label>
                                        <input type="date" id="edit_tanggal_pengeluaran" name="tanggal_pengeluaran" value="{{ old('tanggal_pengeluaran') }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_pasien_id">Pasien</label>
                                        <select id="edit_pasien_id" name="pasien_id" class="pasien-select" required>
                                            <option value="">Pilih Pasien</option>
                                            @foreach($pasienList as $pasien)
                                                <option value="{{ $pasien->id }}" data-no_bpjs="{{ $pasien->no_bpjs }}">{{ $pasien->no_bpjs }} - {{ $pasien->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <div class="form-group">
                                        <label for="edit_user_name">Nama Petugas</label>
                                        <input type="text" id="edit_user_name" name="user_name" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_dokter_id">Dokter</label>
                                        <select id="edit_dokter_id" name="dokter_id" required>
                                            <option value="">Pilih Dokter</option>
                                            @foreach($dokterList as $dokter)
                                                <option value="{{ $dokter->id }}">{{ $dokter->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="edit_keterangan">Keterangan</label>
                                <textarea id="edit_keterangan" name="keterangan" rows="2"></textarea>
                            </div>

                            <div style="margin-top: 16px; border-top: 1px solid #e5e7eb; padding-top: 12px;">
                                <h4 style="margin: 0 0 12px 0; font-size: 14px; color: #374151; font-weight: 600;">Detail Obat</h4>
                                <div class="table-wrapper">
                                    <table class="detail-table">
                                        <thead>
                                            <tr>
                                                <th>Nama Obat</th>
                                                <th>Jumlah Keluar</th>
                                                <th>Tanggal Kadaluwarsa</th>
                                                <th>Satuan</th>
                                                <th>Lokasi Penyimpanan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detailItemsEdit"></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

<script>
    @php
        $__namaOptions = '';
        foreach($namaObats as $__o) {
            $__namaOptions .= '<option value="'. $__o->id .'">'. e($__o->nama_obat) .'</option>';
        }
    @endphp
    const namaOptionsHtml = @json($__namaOptions);
    const satuanobats = @json($satuanobats);

    window.oldDetails = @json(old('details', []));

    // Helper function to get today's date in YYYY-MM-DD format
    function getTodayDate() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Helper function to show styled alert in modal
    function showModalAlert(containerId, message, type = 'error') {
        const container = document.getElementById(containerId);
        if (!container) return;

        const alertType = type === 'error' ? 'error' : 'warning';
        const alertClass = type === 'error' ? 'alert-error' : 'alert-warning';
        const iconColor = type === 'error' ? '#dc2626' : '#f59e0b';
        const bgColor = type === 'error' ? '#fee2e2' : '#fefce8';
        const borderColor = type === 'error' ? '#fecaca' : '#fde68a';
        const textColor = type === 'error' ? '#7f1d1d' : '#92400e';

        container.innerHTML = `
            <div style="background-color: ${bgColor}; border: 1px solid ${borderColor}; border-radius: 8px; padding: 12px 16px; display: flex; gap: 12px; align-items: flex-start;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="${iconColor}" style="width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <div style="flex: 1; color: ${textColor}; font-size: 14px; font-weight: 500;">${message}</div>
                <button type="button" onclick="document.getElementById('${containerId}').style.display = 'none';" style="background: none; border: none; cursor: pointer; color: ${textColor}; padding: 0; font-size: 18px;">&times;</button>
            </div>
        `;
        container.style.display = 'block';
    }

    function createDetailHTML(index, data = {}) {
        const isExisting = data && data.id; // existing detail from DB

        // build nama obat options (server-rendered HTML)
        const selectedId = isExisting ? (data.nama_obat_id || (data.nama_obat && data.nama_obat.id) || '') : '';
        let optionsHtml = namaOptionsHtml;
        if (selectedId) {
            // mark selected option
            const needle = `value="${selectedId}"`;
            if (optionsHtml.indexOf(needle) !== -1) {
                optionsHtml = optionsHtml.replace(needle, needle + ' selected');
            } else {
                // if selected id not in list (edge), prepend selected option
                optionsHtml = `<option value="${selectedId}" selected>${data.nama_obat && data.nama_obat.nama_obat ? data.nama_obat.nama_obat : data.nama_obat_nama || ''}</option>` + optionsHtml;
            }
        }
        const initialOption = `<option value="">Pilih Obat</option>` + optionsHtml;

        // build satuan options
        const satuanOptions = satuanobats.map(satuan => `<option value="${satuan.id}" ${data.satuan_id == satuan.id ? 'selected' : ''}>${satuan.satuan_obat}</option>`).join('');

        return `
            <tr data-existing="${isExisting ? '1' : '0'}">
                <td style="position: relative;">
                    <span class="error-icon" data-detail-index="${index}" style="display:none;" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                        <div class="error-tooltip" role="tooltip"></div>
                    </span>
                    <select ${isExisting ? 'disabled' : ''} name="details[${index}][nama_obat_id]" class="table-input nama-obat-select" data-detail-index="${index}" ${isExisting ? '' : 'required'}>
                        ${initialOption}
                    </select>
                    ${isExisting ? `<input type="hidden" name="details[${index}][nama_obat_id]" value="${data.nama_obat_id}">` : ''}
                </td>
                <td>
                    <input type="number" ${isExisting ? 'readonly' : ''} name="details[${index}][jumlah_keluar]" class="table-input jumlah-keluar-input" data-detail-index="${index}" min="1" value="${data.jumlah_keluar || ''}" ${isExisting ? '' : 'required'}>
                </td>
                <td>
                    <select name="details[${index}][stok_obat_id]" class="table-input tanggal-kadaluwarsa-select" data-detail-index="${index}" data-stok-obat-id="${data.stok_obat_id || ''}" ${isExisting ? 'disabled' : 'disabled'}>
                        <option value="">Pilih Tanggal</option>
                    </select>
                    <input type="hidden" name="details[${index}][stok_obat_id]" class="stok-obat-hidden" data-detail-index="${index}" value="${data.stok_obat_id || ''}">
                </td>
                <td>
                    <!-- Satuan: display-only plus hidden input to submit satuan_id -->
                    ${(() => {
                        const satuanName = (() => {
                            if (data.satuan_id) {
                                const s = satuanobats.find(x => x.id == data.satuan_id);
                                return s ? s.satuan_obat : '';
                            }
                            return '';
                        })();
                        return `
                        <input type="text" readonly class="table-input satuan-display" data-detail-index="${index}" value="${satuanName}">
                        <input type="hidden" name="details[${index}][satuan_id]" value="${data.satuan_id || ''}">
                        `;
                    })()}
                </td>
                <td>
                    <!-- Lokasi: always readonly and auto-filled from obat -->
                    <input type="text" readonly name="details[${index}][lokasi_penyimpanan]" class="table-input lokasi-display" data-detail-index="${index}" value="${data.lokasi_penyimpanan || ''}">
                </td>
                <td>
                    ${isExisting ? '' : `<button type="button" class="btn-delete-row" onclick="this.closest('tr').remove()" title="Hapus">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>`}
                </td>
            </tr>
        `;
    }

    // No Choices.js — use regular select dropdowns

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Deklarasi semua modal elements di awal
        const currentUserName = "{{ addslashes(Auth::user()->name) }}";
        const openCreateBtn = document.getElementById('openCreateModal');
        const createModal = document.getElementById('createPengeluaranModal');
        const closeCreateBtn = document.getElementById('closeCreatePengeluaranModal');
        const cancelCreateBtn = document.getElementById('cancelCreatePengeluaranModal');
        const detailItemsCreateDiv = document.getElementById('detailItemsCreate');
        const addDetailCreateBtn = document.getElementById('addDetailCreate');
        const createPengeluaranForm = document.getElementById('createPengeluaranForm');

        const editModal = document.getElementById('editPengeluaranModal');
        const closeEditBtn = document.getElementById('closeEditPengeluaranModal');
        const cancelEditBtn = document.getElementById('cancelEditPengeluaranModal');
        const detailItemsEditDiv = document.getElementById('detailItemsEdit');
        const addDetailEditBtn = document.getElementById('addDetailEdit');
        const editPengeluaranForm = document.getElementById('editPengeluaranForm');

        let createDetailIndex = 0;
        let editDetailIndex = 0;
        let isEditMode = false;

        function openCreateModal() {
            if (createModal) {
                createModal.classList.remove('hidden');
                createModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeCreateModal() {
            if (createModal) {
                createModal.classList.add('hidden');
                createModal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = 'auto';
            }
        }

        if (openCreateBtn) {
            openCreateBtn.addEventListener('click', () => {
                createDetailIndex = 0;
                if (detailItemsCreateDiv) {
                    detailItemsCreateDiv.innerHTML = '';
                    detailItemsCreateDiv.insertAdjacentHTML('beforeend', createDetailHTML(createDetailIndex));
                }
                createDetailIndex++;
                attachNameObatListeners();
                attachPasienListeners();
                const tanggalInput = document.getElementById('tanggal_pengeluaran');
                if (tanggalInput) tanggalInput.value = getTodayDate();
                const userInput = document.getElementById('user_name');
                if (userInput) userInput.value = currentUserName;
                openCreateModal();
            });
        }

        if (addDetailCreateBtn) {
            addDetailCreateBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (detailItemsCreateDiv) {
                    detailItemsCreateDiv.insertAdjacentHTML('beforeend', createDetailHTML(createDetailIndex));
                }
                createDetailIndex++;
                attachNameObatListeners();
            });
        }



        function attachPasienListeners() {
            document.querySelectorAll('.pasien-select').forEach(select => {
                // destroy previous select2 instance if any
                try {
                    if (window.jQuery && jQuery(select).hasClass('select2-hidden-accessible')) {
                        jQuery(select).select2('destroy');
                    }
                } catch (err) {}

                // initialize select2 for better dropdown/search UI
                try {
                    if (window.jQuery) {
                        const $s = jQuery(select);
                        let parent = $s.closest('.modal');
                        if (!parent || parent.length === 0) parent = jQuery(document.body);
                        $s.select2({ width: '100%', dropdownParent: parent, placeholder: 'Pilih Pasien', allowClear: true });
                    }
                } catch (err) {}
            });
        }

        function attachNameObatListeners() {
            // Plain select behavior: attach change listeners to nama obat selects
            document.querySelectorAll('.nama-obat-select').forEach(select => {
                const detailIndex = select.dataset.detailIndex;
                const stokSelect = document.querySelector(`.tanggal-kadaluwarsa-select[data-detail-index="${detailIndex}"]`);
                const storedStokObatId = stokSelect ? stokSelect.dataset.stokObatId : null;
                console.log(`DEBUG attachNameObatListeners: detailIndex=${detailIndex}, nama_obat_id=${select.value}, data-stok-obat-id=${storedStokObatId}`);

                // destroy previous select2 instance if any
                try {
                    if (window.jQuery && jQuery(select).hasClass('select2-hidden-accessible')) {
                        jQuery(select).select2('destroy');
                    }
                } catch (err) {}

                // initialize select2 for better dropdown/search UI
                try {
                    if (window.jQuery) {
                        const $s = jQuery(select);
                        let parent = $s.closest('.modal');
                        if (!parent || parent.length === 0) parent = jQuery(document.body);
                        $s.select2({ width: '100%', dropdownParent: parent, placeholder: 'Pilih Obat', allowClear: true });
                    }
                } catch (err) {}

                select.removeEventListener('change', handleNameObatChange);
                select.addEventListener('change', handleNameObatChange);
                // Also bind via jQuery as a fallback for Select2-triggered events
                try {
                    if (window.jQuery) {
                        jQuery(select).off('change.pustumed').on('change.pustumed', function(e) {
                            // normalize event to match native handler
                            handleNameObatChange(e);
                        });
                    }
                } catch (err) {}
            });

            // Attach pasien select listeners
            attachPasienListeners();

            // Attach change listener directly to all .tanggal-kadaluwarsa-select elements
            document.querySelectorAll('.tanggal-kadaluwarsa-select').forEach(select => {
                select.removeEventListener('change', handleStokObatChange);
                select.addEventListener('change', handleStokObatChange);
            });

            // Attach input listener directly to all .jumlah-keluar-input elements
            document.querySelectorAll('.jumlah-keluar-input').forEach(input => {
                // remove previously attached handlers (stored on the element) to avoid duplicate handlers
                if (input._pustumed_oninput) {
                    input.removeEventListener('input', input._pustumed_oninput);
                    input._pustumed_oninput = null;
                }
                if (input._pustumed_onchange) {
                    input.removeEventListener('change', input._pustumed_onchange);
                    input._pustumed_onchange = null;
                }

                // debounce helper
                const debounce = (fn, ms = 250) => { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); }; };

                const onInput = debounce(function(e) {
                    const detailIndex = e.target.dataset.detailIndex;
                    // attempt auto-allocation across stok entries for this nama obat
                    try { autoAllocateForDetail(detailIndex); } catch (err) { console.error('allocation error', err); }

                    const stokSelect = document.querySelector(`.tanggal-kadaluwarsa-select[data-detail-index="${detailIndex}"]`);
                    if (stokSelect) {
                        const stokValue = stokSelect.dataset.stokValue || 0;
                        validateJumlahKeluar(e.target, stokValue);
                    }
                }, 300);

                const onChange = function(e) { try { autoAllocateForDetail(this.dataset.detailIndex); } catch (err) {} };

                // store handlers on element so they can be removed next time
                input._pustumed_oninput = onInput;
                input._pustumed_onchange = onChange;

                input.addEventListener('input', onInput);
                input.addEventListener('change', onChange);
            });
        }

        function handleNameObatChange(e) {
            console.log('DEBUG: handleNameObatChange event', e);
            const select = e.target || e.currentTarget || (e && e.srcElement) || null;
            const namaObatId = select.value;
            const detailIndex = select.dataset.detailIndex;
            console.log('DEBUG: selected namaObatId=', namaObatId, 'detailIndex=', detailIndex);
            const tanggalKadaluwarsaSelect = document.querySelector(`.tanggal-kadaluwarsa-select[data-detail-index="${detailIndex}"]`);
            const satuanDisplay = document.querySelector(`.satuan-display[data-detail-index="${detailIndex}"]`);
            const satuanHidden = document.querySelector(`input[name="details[${detailIndex}][satuan_id]"]`);
            const lokasiInput = document.querySelector(`input[name="details[${detailIndex}][lokasi_penyimpanan]"]`);
            const isEdit = (typeof isEditMode !== 'undefined' && isEditMode);
            const storedStokObatId = tanggalKadaluwarsaSelect ? tanggalKadaluwarsaSelect.dataset.stokObatId : null;

            console.log('DEBUG: tanggalKadaluwarsaSelect found?', !!tanggalKadaluwarsaSelect, 'storedStokObatId=', storedStokObatId);

            // clear previous stok options
            if (tanggalKadaluwarsaSelect) {
                tanggalKadaluwarsaSelect.innerHTML = '<option value="">Pilih Tanggal</option>';
                tanggalKadaluwarsaSelect.dataset.stokValue = 0;
            }

            if (!namaObatId) {
                if (satuanDisplay) satuanDisplay.value = '';
                if (satuanHidden) satuanHidden.value = '';
                if (lokasiInput) lokasiInput.value = '';
                return;
            }

            // fetch nama obat detail (satuan, lokasi)
            fetch(`/pengeluaran-obat/detail/${namaObatId}`)
                .then(res => res.json())
                .then(data => {
                    console.log('DEBUG: nama obat detail response:', data);
                    const satuanName = data.satuan_name || '';
                    const satuanId = data.satuan_obat_id || '';
                    if (satuanDisplay) satuanDisplay.value = satuanName;
                    if (satuanHidden) satuanHidden.value = satuanId;
                    if (lokasiInput) lokasiInput.value = data.lokasi_penyimpanan || '';
                })
                .catch(err => console.error('Error fetching nama obat detail:', err))
                .finally(() => {
                    // Fetch stok data
                    if (!tanggalKadaluwarsaSelect) {
                        console.log('DEBUG: tanggalKadaluwarsaSelect is null, cannot fetch stok');
                        return;
                    }
                    fetch(`/pengeluaran-obat/stok/${namaObatId}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('DEBUG: stok data for namaObatId=', namaObatId, 'data=', data);
                            if (!Array.isArray(data)) {
                                console.warn('DEBUG: stok data is not an array', data);
                                return;
                            }

                            if (data.length === 0) {
                                console.warn('DEBUG: no stok entries returned for', namaObatId);
                                return;
                            }

                            // Ensure safe stock is shown first in the dropdown when possible.
                            data.sort((a, b) => {
                                const aSafe = parseInt(a.days_until_expiry, 10) > 30 ? 0 : 1;
                                const bSafe = parseInt(b.days_until_expiry, 10) > 30 ? 0 : 1;
                                if (aSafe !== bSafe) return aSafe - bSafe;
                                return new Date(a.tanggal_kadaluwarsa_iso) - new Date(b.tanggal_kadaluwarsa_iso);
                            });

                            console.log('DEBUG: processing', data.length, 'stok entries');
                            data.forEach((stok, idx) => {
                                console.log(`DEBUG: stok[${idx}]:`, { id: stok.id, display: stok.display, stok_qty: stok.stok, days: stok.days_until_expiry });
                                const option = document.createElement('option');
                                option.value = stok.id;
                                if (isEdit && storedStokObatId && storedStokObatId == stok.id) {
                                    option.textContent = stok.tanggal_kadaluwarsa;
                                } else {
                                    option.textContent = stok.display || stok.tanggal_kadaluwarsa || '';
                                }
                                option.dataset.stok = stok.stok;
                                option.dataset.daysUntilExpiry = stok.days_until_expiry;
                                if (storedStokObatId && storedStokObatId == stok.id) {
                                    option.selected = true;
                                    tanggalKadaluwarsaSelect.dataset.stokValue = stok.stok;
                                    console.log('DEBUG: marked stok option as selected, id=', stok.id);
                                } else {
                                    console.log(`DEBUG: stok id ${stok.id} does not match storedStokObatId ${storedStokObatId}`);
                                }
                                tanggalKadaluwarsaSelect.appendChild(option);
                            });

                            console.log('DEBUG: finished populating', tanggalKadaluwarsaSelect.options.length, 'options');

                            // Check if all available stoks are near expiry
                            checkNearExpiryWarning(data, detailIndex);

                            const changeEvent = new Event('change', { bubbles: true });
                            tanggalKadaluwarsaSelect.dispatchEvent(changeEvent);
                        })
                        .catch(error => console.error('Error fetching stok:', error));
                });
        }

        function getDetailStockOptions(detailIndex) {
            const stokSelect = document.querySelector(`.tanggal-kadaluwarsa-select[data-detail-index="${detailIndex}"]`);
            if (!stokSelect) return [];
            return Array.from(stokSelect.options)
                .map(opt => ({
                    stok: parseInt(opt.dataset.stok, 10) || 0,
                    days_until_expiry: opt.dataset.daysUntilExpiry !== undefined ? parseInt(opt.dataset.daysUntilExpiry, 10) : NaN
                }))
                .filter(s => s.stok > 0 && !Number.isNaN(s.days_until_expiry));
        }

        function isDetailAllStockNearExpiry(detailIndex) {
            const entries = getDetailStockOptions(detailIndex);
            return entries.length > 0 && entries.every(s => s.days_until_expiry <= 30);
        }

        function checkNearExpiryWarning(stokData, detailIndex) {
            const row = document.querySelector(`.nama-obat-select[data-detail-index="${detailIndex}"]`).closest('tr');
            if (!row) return;

            const firstCell = row.querySelector('td');
            if (!firstCell) return;

            const warningIcon = firstCell.querySelector('.warning-near-expiry-icon');

            // Use server data to check if all available stoks are near expiry
            const availableStoks = stokData.filter(s => s.stok > 0);
            const shouldWarn = availableStoks.length > 0 && availableStoks.every(s => s.days_until_expiry <= 30);

            if (shouldWarn) {
                if (!warningIcon) {
                    const newWarningIcon = document.createElement('span');
                    newWarningIcon.className = 'warning-near-expiry-icon';
                    newWarningIcon.style.display = 'inline-flex';
                    newWarningIcon.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px; color: #f59e0b; margin-right: 8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                        <div class="error-tooltip" style="background-color: #fef3c7; border-color: #f59e0b; color: #92400e;">Semua stok obat ini mendekati atau sudah kadaluwarsa</div>
                    `;
                    firstCell.insertBefore(newWarningIcon, firstCell.firstChild);
                } else {
                    warningIcon.style.display = 'inline-flex';
                }
            } else if (warningIcon) {
                warningIcon.style.display = 'none';
            }
        }

        function handleStokObatChange(e) {
            const stokSelect = e.target;
            const selectedOption = stokSelect.options[stokSelect.selectedIndex];
            const stokValue = selectedOption.dataset.stok || 0;
            stokSelect.dataset.stokValue = stokValue;

            // Validate jumlah keluar if already filled
            const detailIndex = stokSelect.dataset.detailIndex;
            const jumlahInput = document.querySelector(`input[name="details[${detailIndex}][jumlah_keluar]"]`);
            if (jumlahInput && jumlahInput.value) {
                validateJumlahKeluar(jumlahInput, stokValue);
            }

            // Update warning based on current nama obat selection
            const namaSelect = document.querySelector(`.nama-obat-select[data-detail-index="${detailIndex}"]`);
            if (namaSelect && namaSelect.value) {
                fetch(`/pengeluaran-obat/stok/${namaSelect.value}`)
                    .then(response => response.json())
                    .then(data => {
                        checkNearExpiryWarning(data, detailIndex);
                    })
                    .catch(err => console.error('Error checking expiry warning:', err));
            }
        }

        function validateJumlahKeluar(input, stokValue) {
            // Skip validation UI when editing existing pengeluaran
            if (typeof isEditMode !== 'undefined' && isEditMode) return;

            const jumlah = parseInt(input.value) || 0;
            const stok = parseInt(stokValue) || 0;
            const row = input.closest('tr');

            // Find the error icon in the first cell
            const firstCell = row ? row.querySelector('td') : null;
            let errorIcon = firstCell ? firstCell.querySelector('.error-icon') : null;

            if (jumlah > stok) {
                const message = `Stok tidak mencukupi! Stok tersedia: ${stok}, diminta: ${jumlah}`;
                if (errorIcon) {
                    // show and update tooltip
                    errorIcon.style.display = 'inline-flex';
                    const tooltip = errorIcon.querySelector('.error-tooltip');
                    if (tooltip) tooltip.textContent = message;
                } else if (firstCell) {
                    // create icon element if missing
                    errorIcon = document.createElement('span');
                    errorIcon.className = 'error-icon';
                    errorIcon.style.display = 'inline-flex';
                    errorIcon.dataset.detailIndex = input.dataset.detailIndex || '';
                    errorIcon.innerHTML = `\n                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>\n                        <div class="error-tooltip">${message}</div>`;
                    firstCell.insertBefore(errorIcon, firstCell.firstChild);
                }

                input.style.borderColor = '#dc2626';
                input.style.backgroundColor = '#fee2e2';
            } else {
                if (errorIcon) {
                    errorIcon.style.display = 'none';
                    const tooltip = errorIcon.querySelector('.error-tooltip');
                    if (tooltip) tooltip.textContent = '';
                }
                input.style.borderColor = '';
                input.style.backgroundColor = '';
            }
        }

        async function autoAllocateForDetail(detailIndex) {
            const namaSelect = document.querySelector(`.nama-obat-select[data-detail-index="${detailIndex}"]`);
            const jumlahInput = document.querySelector(`input[name="details[${detailIndex}][jumlah_keluar]"]`);
            if (!namaSelect || !jumlahInput) return;

            const namaObatId = namaSelect.value;
            const requested = parseInt(jumlahInput.value) || 0;
            if (!namaObatId || requested <= 0) return;

            // remove previously auto-inserted sibling allocation rows after this row
            const currentRow = namaSelect.closest('tr');
            let next = currentRow.nextElementSibling;
            while (next && next.dataset && next.dataset.autoAlloc === '1') {
                const toRemove = next;
                next = next.nextElementSibling;
                toRemove.remove();
            }

            // fetch stok entries for this nama obat
            let stoks = [];
            try {
                const res = await fetch(`/pengeluaran-obat/stok/${namaObatId}`);
                stoks = await res.json();
            } catch (err) {
                console.error('Failed to fetch stok for allocation', err);
                return;
            }

            if (!Array.isArray(stoks) || stoks.length === 0) {
                // nothing to allocate
                return;
            }

            // Use only safe stocks (>30 days) for allocation.
            // If requested qty exceeds safe stock, do not fall back to near-expiry batches.
            const safeStocks = stoks.filter(s => parseInt(s.days_until_expiry, 10) > 30);
            const pool = safeStocks;

            let remaining = requested;
            const allocations = [];
            for (const s of pool) {
                if (remaining <= 0) break;
                const take = Math.min(remaining, parseInt(s.stok || 0, 10));
                if (take <= 0) continue;
                allocations.push({ stokId: s.id, qty: take, display: s.display, iso: s.tanggal_kadaluwarsa_iso, daysUntilExpiry: s.days_until_expiry });
                remaining -= take;
            }

            // if total available < requested, still allocate whatever possible and leave validation to user
            const totalAllocated = allocations.reduce((a,b) => a + b.qty, 0);

            // If no safe allocation is possible (e.g. all stok near expiry),
            // keep one tanggal kadaluwarsa visible in the table for user context.
            if (allocations.length === 0 && stoks.length > 0) {
                const firstAvailable = stoks[0];
                const stokHidden = document.querySelector(`.stok-obat-hidden[data-detail-index="${detailIndex}"]`);
                const stokSelect = document.querySelector(`.tanggal-kadaluwarsa-select[data-detail-index="${detailIndex}"]`);

                if (stokSelect) {
                    stokSelect.value = String(firstAvailable.id);
                    stokSelect.dataset.stokValue = parseInt(firstAvailable.stok || 0, 10);
                    stokSelect.dataset.stokObatId = firstAvailable.id;
                }

                if (stokHidden) {
                    stokHidden.value = firstAvailable.id;
                }

                validateJumlahKeluar(jumlahInput, firstAvailable.stok);
                return;
            }

            // Update current row with first allocation (if any)
            if (allocations.length > 0) {
                const first = allocations[0];
                // set hidden stok id and stok select display
                const stokHidden = document.querySelector(`.stok-obat-hidden[data-detail-index="${detailIndex}"]`);
                const stokSelect = document.querySelector(`.tanggal-kadaluwarsa-select[data-detail-index="${detailIndex}"]`);
                if (stokSelect) {
                    stokSelect.innerHTML = '';
                    const opt = document.createElement('option');
                    opt.value = first.stokId;
                    opt.textContent = first.display || first.iso || '';
                    opt.dataset.stok = first.qty;
                    opt.dataset.daysUntilExpiry = first.daysUntilExpiry;
                    stokSelect.appendChild(opt);
                    stokSelect.dataset.stokValue = first.qty;
                    stokSelect.dataset.stokObatId = first.stokId;
                    stokSelect.value = first.stokId;
                }
                if (stokHidden) stokHidden.value = first.stokId;

                // If total available is less than requested, DO NOT insert extra allocation rows.
                // Keep the user's entered `jumlahInput.value` so they see the validation message, and
                // mark the field invalid via validateJumlahKeluar using the available stok for this
                // stok entry. This avoids creating confusing extra rows when stock is insufficient.
                if (totalAllocated < requested) {
                    // leave jumlahInput as the user typed (requested)
                    validateJumlahKeluar(jumlahInput, first.qty);

                    // show aggregate-not-enough tooltip on the row
                    const msg = `Stok total (${totalAllocated}) tidak cukup untuk permintaan (${requested}).`;
                    jumlahInput.style.borderColor = '#dc2626';
                    jumlahInput.style.backgroundColor = '#fee2e2';
                    const firstCell = currentRow ? currentRow.querySelector('td') : null;
                    let errorIcon = firstCell ? firstCell.querySelector('.error-icon') : null;
                    if (!errorIcon && firstCell) {
                        errorIcon = document.createElement('span');
                        errorIcon.className = 'error-icon';
                        errorIcon.style.display = 'inline-flex';
                        errorIcon.innerHTML = `\n                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>\n                            <div class="error-tooltip">${msg}</div>`;
                        firstCell.insertBefore(errorIcon, firstCell.firstChild);
                    } else if (errorIcon) {
                        const tooltip = errorIcon.querySelector('.error-tooltip');
                        if (tooltip) tooltip.textContent = msg;
                        errorIcon.style.display = 'inline-flex';
                    }

                    // DO NOT create additional allocation rows when stock is insufficient
                    return;
                }

                // totalAllocated >= requested: apply allocations and create extra rows as before
                jumlahInput.value = allocations[0].qty;
                validateJumlahKeluar(jumlahInput, allocations[0].qty);

                // Insert extra rows for remaining allocations
                for (let i = 1; i < allocations.length; i++) {
                    const a = allocations[i];
                    const satuanHidden = document.querySelector(`input[name="details[${detailIndex}][satuan_id]"]`);
                    const lokasiDisplay = document.querySelector(`input[name="details[${detailIndex}][lokasi_penyimpanan]"]`);
                    const satuanId = satuanHidden ? satuanHidden.value : '';
                    const lokasiVal = lokasiDisplay ? lokasiDisplay.value : '';

                    const newIndex = createDetailIndex++;
                    const rowHtml = createDetailHTML(newIndex, {
                        nama_obat_id: namaObatId,
                        jumlah_keluar: a.qty,
                        stok_obat_id: a.stokId,
                        satuan_id: satuanId,
                        lokasi_penyimpanan: lokasiVal
                    });
                    // insert after current row (append in sequence)
                    currentRow.insertAdjacentHTML('afterend', rowHtml);
                    const insertedRow = currentRow.nextElementSibling;
                    if (insertedRow) insertedRow.dataset.autoAlloc = '1';

                    // initialize Select2 / event handlers for the newly inserted controls
                    attachNameObatListeners();

                    // populate the nama select & trigger change to populate satuan/hidden fields
                    const insertedNama = insertedRow.querySelector('.nama-obat-select');
                    if (insertedNama) {
                        try {
                            if (window.jQuery && jQuery(insertedNama).hasClass('select2-hidden-accessible')) {
                                jQuery(insertedNama).val(String(namaObatId)).trigger('change');
                            } else {
                                insertedNama.value = namaObatId;
                                const evt = new Event('change', { bubbles: true });
                                insertedNama.dispatchEvent(evt);
                            }
                        } catch (err) {
                            insertedNama.value = namaObatId;
                            const evt = new Event('change', { bubbles: true });
                            insertedNama.dispatchEvent(evt);
                        }
                    }

                    // set the hidden stok input (createDetailHTML already added stok-obat-hidden)
                    const insertedStokHidden = insertedRow.querySelector('.stok-obat-hidden');
                    const insertedStokSelect = insertedRow.querySelector('.tanggal-kadaluwarsa-select');
                    if (insertedStokSelect) {
                        insertedStokSelect.innerHTML = '';
                        const opt2 = document.createElement('option');
                        opt2.value = a.stokId;
                        opt2.textContent = a.display || a.iso || '';
                        opt2.dataset.stok = a.qty;
                        opt2.dataset.daysUntilExpiry = a.daysUntilExpiry;
                        insertedStokSelect.appendChild(opt2);
                        insertedStokSelect.dataset.stokValue = a.qty;
                        insertedStokSelect.dataset.stokObatId = a.stokId;
                        insertedStokSelect.value = a.stokId;
                    }
                    if (insertedStokHidden) insertedStokHidden.value = a.stokId;

                    // set jumlah value in the inserted row
                    const insertedJumlah = insertedRow.querySelector('.jumlah-keluar-input');
                    if (insertedJumlah) {
                        insertedJumlah.value = a.qty;
                        validateJumlahKeluar(insertedJumlah, a.qty);
                    }
                }
            }
        }


        if (closeCreateBtn) {
            closeCreateBtn.addEventListener('click', closeCreateModal);
        }
        if (cancelCreateBtn) {
            cancelCreateBtn.addEventListener('click', closeCreateModal);
        }
        if (createModal) {
            createModal.addEventListener('click', (e) => {
                if (e.target === createModal) closeCreateModal();
            });
        }

        // Edit Modal
        function openEditModal() {
            if (editModal) {
                isEditMode = true;
                editModal.classList.remove('hidden');
                editModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                if (addDetailEditBtn) addDetailEditBtn.style.display = 'none';
            }
        }

        function closeEditModal() {
            if (editModal) {
                isEditMode = false;
                editModal.classList.add('hidden');
                editModal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = 'auto';
                if (addDetailEditBtn) addDetailEditBtn.style.display = '';
            }
        }

        document.querySelectorAll('.openEditModal').forEach(btn => {
            btn.addEventListener('click', function() {
                const record = JSON.parse(this.getAttribute('data-record'));

                const editTanggalInput = document.getElementById('edit_tanggal_pengeluaran');
                if (editTanggalInput) editTanggalInput.value = record.tanggal_pengeluaran;

                const editPasienSelect = document.getElementById('edit_pasien_id');
                if (editPasienSelect) editPasienSelect.value = record.pasien_id;

                const editDokterSelect = document.getElementById('edit_dokter_id');
                if (editDokterSelect) {
                    const doctorId = String(record.dokter_id || '');
                    const currentOption = Array.from(editDokterSelect.options).find(option => option.value === doctorId);
                    if (!currentOption && record.dokter) {
                        const preservedOption = document.createElement('option');
                        preservedOption.value = doctorId;
                        preservedOption.textContent = `${record.dokter.nama || 'Dokter'} (Nonaktif)`;
                        preservedOption.selected = true;
                        editDokterSelect.appendChild(preservedOption);
                    } else {
                        editDokterSelect.value = doctorId;
                    }
                }

                const editKeteranganInput = document.getElementById('edit_keterangan');
                if (editKeteranganInput) editKeteranganInput.value = record.keterangan || '';

                // fill nama petugas
                const editUserInput = document.getElementById('edit_user_name');
                if (editUserInput) {
                    editUserInput.value = record.user ? record.user.name : '';
                }

                editDetailIndex = 0;
                if (detailItemsEditDiv) {
                    detailItemsEditDiv.innerHTML = '';
                    record.detail_pengeluaran_obat.forEach(detail => {
                        detailItemsEditDiv.insertAdjacentHTML('beforeend', createDetailHTML(editDetailIndex, detail));
                        editDetailIndex++;
                    });
                }

                if (editPengeluaranForm) {
                    editPengeluaranForm.action = `/pengeluaran-obat/${record.id}`;
                }

                attachNameObatListeners();
                attachPasienListeners();

                setTimeout(() => {
                    const selects = document.querySelectorAll('.nama-obat-select');
                    let delay = 0;
                    selects.forEach((select, idx) => {
                        if (select.value) {
                            setTimeout(() => {
                                const event = new Event('change', { bubbles: true });
                                select.dispatchEvent(event);
                            }, delay);
                            delay += 150;
                        }
                    });
                }, 50);

                openEditModal();
            });
        });
        if (addDetailEditBtn) {
            addDetailEditBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (detailItemsEditDiv) {
                    detailItemsEditDiv.insertAdjacentHTML('beforeend', createDetailHTML(editDetailIndex));
                }
                editDetailIndex++;
                attachNameObatListeners();
            });
        }

        if (closeEditBtn) {
            closeEditBtn.addEventListener('click', closeEditModal);
        }
        if (cancelEditBtn) {
            cancelEditBtn.addEventListener('click', closeEditModal);
        }
        if (editModal) {
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal) closeEditModal();
            });
        }

        function enableDisabledFields(form) {
            if (!form) return;
            form.querySelectorAll('.satuan-select:disabled').forEach(select => {
                select.disabled = false;
            });
        }

        if (createPengeluaranForm) {
            let isCreateSubmitting = false;
            createPengeluaranForm.addEventListener('submit', async (e) => {
                if (isCreateSubmitting) return;
                e.preventDefault();

                // Check for validation errors
                const errorMessages = createPengeluaranForm.querySelectorAll('.error-message-jumlah');
                const visibleErrorIcons = createPengeluaranForm.querySelectorAll('.error-icon[style*="inline-flex"]');
                const emptyJumlahInputs = Array.from(createPengeluaranForm.querySelectorAll('.jumlah-keluar-input')).filter(input => !input.value);
                const emptyStokSelects = Array.from(createPengeluaranForm.querySelectorAll('.tanggal-kadaluwarsa-select')).filter(select => !select.value);

                // Check near-expiry status and wait until all checks complete
                const namaSelects = Array.from(createPengeluaranForm.querySelectorAll('.nama-obat-select')).filter(select => select.value);
                const nearExpiryResults = await Promise.all(
                    namaSelects.map(async (select) => {
                        const detailIndex = select.dataset.detailIndex;
                        try {
                            const response = await fetch(`/pengeluaran-obat/stok/${select.value}`);
                            const data = await response.json();
                            const availableStoks = Array.isArray(data) ? data.filter(s => s.stok > 0) : [];
                            const allNearExpiry = availableStoks.length > 0 && availableStoks.every(s => s.days_until_expiry <= 30);
                            return allNearExpiry ? detailIndex : null;
                        } catch (err) {
                            console.error('Error checking expiry:', err);
                            return null;
                        }
                    })
                );
                const nearExpiryDetails = nearExpiryResults.filter(Boolean);

                if (errorMessages.length > 0 || visibleErrorIcons.length > 0) {
                    showModalAlert('createAlertContainer', 'Ada beberapa item dengan jumlah melebihi stok yang tersedia. Silakan perbaiki terlebih dahulu.', 'error');
                    return;
                }

                if (emptyJumlahInputs.length > 0 || emptyStokSelects.length > 0) {
                    showModalAlert('createAlertContainer', 'Mohon isi semua detail item obat yang diperlukan.', 'error');
                    return;
                }

                if (nearExpiryDetails.length > 0) {
                    showModalAlert('createAlertContainer', 'Ada obat yang semua stoknya sudah mendekati atau melewati masa berlaku. Obat tersebut tidak dapat dikeluarkan. Silakan pilih obat lain.', 'warning');
                    return;
                }

                enableDisabledFields(createPengeluaranForm);
                isCreateSubmitting = true;
                createPengeluaranForm.submit();
            });
        }

        if (editPengeluaranForm) {
            let isEditSubmitting = false;
            editPengeluaranForm.addEventListener('submit', async (e) => {
                if (isEditSubmitting) return;
                e.preventDefault();

                // Check for validation errors
                const errorMessages = editPengeluaranForm.querySelectorAll('.error-message-jumlah');
                const visibleErrorIcons = editPengeluaranForm.querySelectorAll('.error-icon[style*="inline-flex"]');
                const emptyJumlahInputs = Array.from(editPengeluaranForm.querySelectorAll('.jumlah-keluar-input')).filter(input => !input.value);
                const emptyStokSelects = Array.from(editPengeluaranForm.querySelectorAll('.tanggal-kadaluwarsa-select')).filter(select => !select.value);

                // Check near-expiry status and wait until all checks complete
                const namaSelects = Array.from(editPengeluaranForm.querySelectorAll('.nama-obat-select')).filter(select => select.value);
                const nearExpiryResults = await Promise.all(
                    namaSelects.map(async (select) => {
                        const detailIndex = select.dataset.detailIndex;
                        try {
                            const response = await fetch(`/pengeluaran-obat/stok/${select.value}`);
                            const data = await response.json();
                            const availableStoks = Array.isArray(data) ? data.filter(s => s.stok > 0) : [];
                            const allNearExpiry = availableStoks.length > 0 && availableStoks.every(s => s.days_until_expiry <= 30);
                            return allNearExpiry ? detailIndex : null;
                        } catch (err) {
                            console.error('Error checking expiry:', err);
                            return null;
                        }
                    })
                );
                const nearExpiryDetails = nearExpiryResults.filter(Boolean);

                if (errorMessages.length > 0 || visibleErrorIcons.length > 0) {
                    showModalAlert('editAlertContainer', 'Ada beberapa item dengan jumlah melebihi stok yang tersedia. Silakan perbaiki terlebih dahulu.', 'error');
                    return;
                }

                if (emptyJumlahInputs.length > 0 || emptyStokSelects.length > 0) {
                    showModalAlert('editAlertContainer', 'Mohon isi semua detail item obat yang diperlukan.', 'error');
                    return;
                }

                if (nearExpiryDetails.length > 0) {
                    showModalAlert('editAlertContainer', 'Ada obat yang semua stoknya sudah mendekati atau melewati masa berlaku. Obat tersebut tidak dapat dikeluarkan. Silakan pilih obat lain.', 'warning');
                    return;
                }

                enableDisabledFields(editPengeluaranForm);
                isEditSubmitting = true;
                editPengeluaranForm.submit();
            });
        }
    });
</script>
</body>
</html>
