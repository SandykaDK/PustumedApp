@php
    function sortLink($column, $label) {
        $direction = request('direction') === 'asc' ? 'desc' : 'asc';
        $params = array_merge(request()->all(), [
            'sort' => $column,
            'direction' => $direction
        ]);

        return '<a href="'.route('dokter.index', $params).'">'.$label.'</a>';
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Daftar Dokter - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dokter/dokter.css') }}">
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
            <h1>Daftar Dokter</h1>
        </div>

        <x-alert type="success" />
        <x-alert type="error" />

        <div class="card">
            <div class="table-actions">
                <form method="GET" action="{{ route('dokter.index') }}">
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
                                placeholder="Cari nama dokter atau email..."
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

                        <a href="{{ route('dokter.index') }}" class="btn-reset" style="display:flex;align-items:center;gap:6px;background:#6b7280;color:white;padding:8px 14px;border-radius:6px;text-decoration:none;">
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
            <table class="dokter-table">
                <thead>
                <tr>
                    <th>No</th>
                    <x-sortable-th column="nama" label="Nama Dokter" />
                    <x-sortable-th column="alamat" label="Alamat" />
                    <x-sortable-th column="jenis_kelamin" label="Jenis Kelamin" />
                    <x-sortable-th column="no_telepon" label="No. Telepon" />
                    <x-sortable-th column="email" label="Email" />
                    <x-sortable-th column="status" label="Status" />
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                    @forelse ($dokters as $dokter)
                        <tr>
                            <td>{{ $dokter->id }}</td>
                            <td>{{ $dokter->nama }}</td>
                            <td>{{ $dokter->alamat }}</td>
                            <td>{{ $dokter->jenis_kelamin }}</td>
                            <td>{{ $dokter->no_telepon }}</td>
                            <td>{{ $dokter->email }}</td>
                            <td>
                                <span class="status-badge {{ ($dokter->status ?? 'aktif') === 'aktif' ? 'status-aktif' : 'status-nonaktif' }}">
                                    {{ ucfirst($dokter->status ?? 'aktif') }}
                                </span>
                            </td>
                            <td>
                                @php $isUsedInPengeluaran = ($dokter->pengeluaran_obat_count ?? 0) > 0; @endphp
                                <div class="action-buttons">
                                    <!-- EDIT (open modal) -->
                                    <button type="button"
                                        class="action-btn edit openEditDokterModal"
                                        title="Edit"
                                        data-id="{{ $dokter->id }}"
                                        data-nama="{{ $dokter->nama }}"
                                        data-alamat="{{ $dokter->alamat }}"
                                        data-jenis-kelamin="{{ $dokter->jenis_kelamin }}"
                                        data-no_telepon="{{ $dokter->no_telepon }}"
                                        data-email="{{ $dokter->email }}"
                                        data-status="{{ $dokter->status ?? 'aktif' }}">
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
                                    @if($isUsedInPengeluaran)
                                        <button type="button" class="action-btn delete" title="Tidak dapat dihapus karena sudah dipakai di transaksi pengeluaran obat" disabled aria-disabled="true" style="opacity: 0.45; cursor: not-allowed; pointer-events: none;">
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
                                        <x-confirm-delete action="{{ route('dokter.destroy', $dokter->id) }}" :id="'delete-dokter-'.$dokter->id" title="Hapus Dokter" message="Yakin ingin menghapus dokter {{ $dokter->nama }}?">
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
                            <td colspan="6" class="empty">Tidak ada data dokter</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="pagination-section">
                <div class="pagination-controls">
                    <div class="per-page-selector">
                        <form method="GET" action="{{ route('dokter.index') }}" class="per-page-form">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="status" value="{{ request('status', 'semua') }}">
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                            <input type="hidden" name="direction" value="{{ request('direction') }}">
                            <label for="per_page_dokter" class="per-page-label">Tampilkan:</label>
                            <select name="per_page" id="per_page_dokter" class="per-page-input" onchange="this.form.submit()">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="pagination-wrapper">
                    {{ $dokters->appends(request()->query())->links() }}
                </div>
            </div>
        {{-- </div> --}}

        <!-- Create Modal -->
        <div id="createDokterModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createDokterTitle">
                <div class="modal-header">
                    <h2 id="createDokterTitle">Tambah Dokter</h2>
                    <button class="modal-close" id="closeCreateDokterModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && !session('edit_dokter_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('dokter.store') }}" method="POST" class="form-component">
                        @csrf

                        <div class="form-group">
                            <label for="nama">Nama Dokter</label>
                            <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input id="alamat" type="text" name="alamat" value="{{ old('alamat') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="no_telepon">No. Telepon</label>
                            <input id="no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Status Dokter</label>
                            <input type="hidden" name="status" id="create_status" value="{{ old('status', 'aktif') }}">
                            <div class="toggle-switch">
                                <input type="checkbox" id="create_status_toggle" value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'checked' : '' }}>
                                <label for="create_status_toggle" class="toggle-slider"></label>
                                <span class="toggle-text" id="create_status_label">{{ old('status', 'aktif') === 'aktif' ? 'Aktif' : 'Nonaktif' }}</span>
                            </div>
                        </div>

                        <div class="form-actions modal-actions">
                            <button type="button" class="btn-secondary" id="cancelCreateDokterModal">Batal</button>
                            <button type="submit" class="btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="editDokterModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="editDokterTitle">
                <div class="modal-header">
                    <h2 id="editDokterTitle">Edit Dokter</h2>
                    <button class="modal-close" id="closeEditDokterModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && session('edit_dokter_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="editDokterForm" action="" method="POST" class="form-component">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-group">
                            <label for="edit_nama">Nama Dokter</label>
                            <input id="edit_nama" type="text" name="nama" value="{{ old('nama') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_alamat">Alamat</label>
                            <input id="edit_alamat" type="text" name="alamat" value="{{ old('alamat') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_jenis_kelamin">Jenis Kelamin</label>
                            <select id="edit_jenis_kelamin" name="jenis_kelamin" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_no_telepon">No. Telepon</label>
                            <input id="edit_no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_email">Email</label>
                            <input id="edit_email" type="email" name="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Status Dokter</label>
                            <input type="hidden" name="status" id="edit_status" value="{{ old('status', 'aktif') }}">
                            <div class="toggle-switch">
                                <input type="checkbox" id="edit_status_toggle" value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'checked' : '' }}>
                                <label for="edit_status_toggle" class="toggle-slider"></label>
                                <span class="toggle-text" id="edit_status_label">{{ old('status', 'aktif') === 'aktif' ? 'Aktif' : 'Nonaktif' }}</span>
                            </div>
                        </div>

                        <div class="form-actions modal-actions">
                            <button type="button" class="btn-secondary" id="cancelEditDokterModal">Batal</button>
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
        // Create modal
        const openBtn = document.getElementById('openCreateModal');
        const modal = document.getElementById('createDokterModal');
        const closeBtn = document.getElementById('closeCreateDokterModal');
        const cancelBtn = document.getElementById('cancelCreateDokterModal');
        const createStatusToggle = document.getElementById('create_status_toggle');
        const createStatusHidden = document.getElementById('create_status');
        const createStatusLabel = document.getElementById('create_status_label');

        const createForm = document.querySelector('#createDokterModal form');

        function openModal() {
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
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

        function syncStatusToggle(toggleEl, hiddenEl, labelEl) {
            if (!toggleEl || !hiddenEl || !labelEl) return;

            const update = () => {
                const isActive = toggleEl.checked;
                hiddenEl.value = isActive ? 'aktif' : 'nonaktif';
                labelEl.textContent = isActive ? 'Aktif' : 'Nonaktif';
            };

            toggleEl.addEventListener('change', update);
            update();
        }

        syncStatusToggle(createStatusToggle, createStatusHidden, createStatusLabel);

        openBtn && openBtn.addEventListener('click', openModal);
        closeBtn && closeBtn.addEventListener('click', closeModal);
        cancelBtn && cancelBtn.addEventListener('click', closeModal);

        // Auto open create modal if validation errors belong to create
        @if ($errors->any() && !session('edit_dokter_id'))
            document.addEventListener('DOMContentLoaded', function() { openModal(); });
        @endif

        // Edit modal handlers
        const editModal = document.getElementById('editDokterModal');
        const openEditButtons = document.querySelectorAll('.openEditDokterModal');
        // correct ids for edit modal
        const closeEditBtn = document.getElementById('closeEditDokterModal');
        const cancelEditBtn = document.getElementById('cancelEditDokterModal');
        const editForm = document.getElementById('editDokterForm');
        const editStatusToggle = document.getElementById('edit_status_toggle');
        const editStatusHidden = document.getElementById('edit_status');
        const editStatusLabel = document.getElementById('edit_status_label');

        syncStatusToggle(editStatusToggle, editStatusHidden, editStatusLabel);

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
                editForm.action = '/dokter/' + data.id;
                const namaEl = document.getElementById('edit_nama');
                const alamatEl = document.getElementById('edit_alamat');
                const jenisKelaminEl = document.getElementById('edit_jenis_kelamin');
                if (namaEl) namaEl.value = data.nama || '';
                if (alamatEl) alamatEl.value = data.alamat || '';
                if (jenisKelaminEl) jenisKelaminEl.value = data.jenis_kelamin || '';

                const teleponEl = document.getElementById('edit_no_telepon');
                const emailEl = document.getElementById('edit_email');
                if (teleponEl) teleponEl.value = data.no_telepon || '';
                if (emailEl) emailEl.value = data.email || '';

                if (editStatusHidden) editStatusHidden.value = data.status || 'aktif';
                if (editStatusToggle) editStatusToggle.checked = (data.status || 'aktif') === 'aktif';
                if (editStatusLabel) editStatusLabel.textContent = (data.status || 'aktif') === 'aktif' ? 'Aktif' : 'Nonaktif';
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
                        alamat: this.dataset.alamat,
                        jenis_kelamin: this.dataset.jenisKelamin || this.getAttribute('data-jenis-kelamin'),
                        no_telepon: this.dataset.no_telepon || '',
                        email: this.dataset.email,
                        status: this.dataset.status || 'aktif',
                    };

                    if (window.console && window.console.log) console.log('edit dokter clicked, id=', data.id);

                    populateEditForm(data);
                    openEditModal();
                } catch (err) {
                    console.error('openEditButtons click handler error:', err);
                }
            });
        });

        closeEditBtn && closeEditBtn.addEventListener('click', closeEditModal);
        cancelEditBtn && cancelEditBtn.addEventListener('click', closeEditModal);

        const editDokterIdFromServer = @json(session('edit_dokter_id'));
        const oldInput = @json(session()->getOldInput());

        if (editDokterIdFromServer) {
            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.querySelector('.openEditDokterModal[data-id="' + editDokterIdFromServer + '"]');
                if (btn) {
                    const data = {
                        id: btn.dataset.id,
                        nama: btn.dataset.nama,
                        alamat: btn.dataset.alamat,
                        jenis_kelamin: btn.dataset.jenisKelamin || btn.getAttribute('data-jenis-kelamin'),
                        no_telepon: btn.dataset.no_telepon || '',
                        email: btn.dataset.email,
                        status: btn.dataset.status || 'aktif',
                    };
                    populateEditForm(data);
                }

                if (oldInput && Object.keys(oldInput).length) {
                    if (oldInput.nama) document.getElementById('edit_nama').value = oldInput.nama;
                    if (oldInput.alamat) document.getElementById('edit_alamat').value = oldInput.alamat;
                    if (oldInput.jenis_kelamin) document.getElementById('edit_jenis_kelamin').value = oldInput.jenis_kelamin;
                    if (oldInput.no_telepon) document.getElementById('edit_no_telepon').value = oldInput.no_telepon;
                    if (oldInput.email) document.getElementById('edit_email').value = oldInput.email;
                    if (oldInput.status) {
                        if (editStatusHidden) editStatusHidden.value = oldInput.status;
                        if (editStatusToggle) editStatusToggle.checked = oldInput.status === 'aktif';
                        if (editStatusLabel) editStatusLabel.textContent = oldInput.status === 'aktif' ? 'Aktif' : 'Nonaktif';
                    }
                }

                openEditModal();
            });
        }

        // Auto submit filter form with debounce.
        const filterForm = document.querySelector('.table-actions form[method="GET"]');
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
