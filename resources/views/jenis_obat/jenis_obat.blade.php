@php
    function sortLink($column, $label) {
        $direction = request('direction') === 'asc' ? 'desc' : 'asc';
        $params = array_merge(request()->all(), [
            'sort' => $column,
            'direction' => $direction
        ]);

        return '<a href="'.route('jenis-obat.index', $params).'">'.$label.'</a>';
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jenis Obat - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jenis_obat/jenis_obat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/form.css') }}">
</head>
<body>

    <x-sidebar />

    <div class="main-wrapper">
        <x-navbar />

        <div class="container main-content">

        <div class="page-header">
            <h1>Jenis Obat</h1>
        </div>

        <x-alert type="success" />
        <x-alert type="error" />

        <div class="card">
            <div class="table-actions">
                <form method="GET" action="{{ route('jenis-obat.index') }}">
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
                            placeholder="Cari kode atau nama jenis obat..."
                            value="{{ request('search') }}"
                            class="search-input"
                        >
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
            <table class="jenis-obat-table">
                <thead>
                <tr>
                    <th>No</th>
                    <x-sortable-th column="kode_jenis" label="Kode Jenis" />
                    <x-sortable-th column="jenis_obat" label="Nama Jenis Obat" />
                    {{-- <x-sortable-th column="created_at" label="Tanggal Dibuat" /> --}}
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                    @forelse ($jenisobats as $jenisobat)
                        <tr>
                            <td>{{ $jenisobat->id }}</td>
                            <td>{{ $jenisobat->kode_jenis }}</td>
                            <td>{{ $jenisobat->jenis_obat }}</td>
                            {{-- <td>{{ $jenisobat->created_at->format('d M Y') }}</td> --}}
                            <td>
                                <div class="action-buttons">
                                    <!-- EDIT (open modal) -->
                                    <button type="button"
                                        class="action-btn edit openEditModal"
                                        title="Edit"
                                        data-id="{{ $jenisobat->id }}"
                                        data-kode_jenis="{{ $jenisobat->kode_jenis }}"
                                        data-jenis_obat="{{ $jenisobat->jenis_obat }}">
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
                                    <x-confirm-delete action="{{ route('jenis-obat.destroy', $jenisobat->id) }}" :id="'delete-jenis-'.$jenisobat->id" title="Hapus Jenis" message="Yakin ingin menghapus jenis obat {{ $jenisobat->jenis_obat }}?">
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
                            <td colspan="6" class="empty">Tidak ada data jenis obat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="pagination-section">
                <div class="pagination-controls">
                    <div class="per-page-selector">
                        <form method="GET" action="{{ route('jenis-obat.index') }}" class="per-page-form">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                            <input type="hidden" name="direction" value="{{ request('direction') }}">
                            <label for="per_page_jenis" class="per-page-label">Tampilkan:</label>
                            <select name="per_page" id="per_page_jenis" class="per-page-input" onchange="this.form.submit()">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="pagination-wrapper">
                    {{ $jenisobats->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div id="createJenisModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createJenisTitle">
                <div class="modal-header">
                    <h2 id="createJenisTitle">Tambah Jenis Obat</h2>
                    <button class="modal-close" id="closeCreateModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && !session('edit_jenis_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('jenis-obat.store') }}" method="POST" class="form-component">
                        @csrf

                        <div class="form-group">
                            <label for="kode_jenis">Kode Jenis</label>
                            <input id="kode_jenis" type="text" name="kode_jenis" value="{{ old('kode_jenis') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="jenis_obat">Nama Jenis Obat</label>
                            <input id="jenis_obat" type="text" name="jenis_obat" value="{{ old('jenis_obat') }}" required>
                        </div>

                        <div class="form-actions modal-actions">
                            <button type="button" class="btn-secondary" id="cancelCreateModal">Batal</button>
                            <button type="submit" class="btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="editJenisModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="editJenisTitle">
                <div class="modal-header">
                    <h2 id="editJenisTitle">Edit Jenis Obat</h2>
                    <button class="modal-close" id="closeEditModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && session('edit_jenis_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="editJenisForm" action="" method="POST" class="form-component">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-group">
                            <label for="edit_kode_jenis">Kode Jenis</label>
                            <input id="edit_kode_jenis" type="text" name="kode_jenis" value="{{ old('kode_jenis') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_jenis_obat">Nama Jenis Obat</label>
                            <input id="edit_jenis_obat" type="text" name="jenis_obat" value="{{ old('jenis_obat') }}" required>
                        </div>

                        <div class="form-actions modal-actions">
                            <button type="button" class="btn-secondary" id="cancelEditModal">Batal</button>
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
        const modal = document.getElementById('createJenisModal');
        const closeBtn = document.getElementById('closeCreateModal');
        const cancelBtn = document.getElementById('cancelCreateModal');

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
        @if ($errors->any() && !session('edit_jenis_id'))
            document.addEventListener('DOMContentLoaded', function() { openModal(); });
        @endif

        // Edit modal handlers
        const editModal = document.getElementById('editJenisModal');
        const openEditButtons = document.querySelectorAll('.openEditModal');
        const closeEditBtn = document.getElementById('closeEditModal');
        const cancelEditBtn = document.getElementById('cancelEditModal');
        const editForm = document.getElementById('editJenisForm');

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
            editForm.action = '/jenis-obat/' + data.id;
            document.getElementById('edit_kode_jenis').value = data.kode_jenis || '';
            document.getElementById('edit_jenis_obat').value = data.jenis_obat || '';
        }

        openEditButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const data = {
                    id: this.dataset.id,
                    kode_jenis: this.dataset.kode_jenis,
                    jenis_obat: this.dataset.jenis_obat
                };

                populateEditForm(data);
                openEditModal();
            });
        });

        closeEditBtn && closeEditBtn.addEventListener('click', closeEditModal);
        cancelEditBtn && cancelEditBtn.addEventListener('click', closeEditModal);

        editModal && editModal.addEventListener('click', function(e) {
            if (e.target === editModal) closeEditModal();
        });

        // Auto open edit modal if controller indicates edit_jenis_id in session
        const editJenisIdFromServer = @json(session('edit_jenis_id'));
        const oldInput = @json(session()->getOldInput());

        if (editJenisIdFromServer) {
            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.querySelector('.openEditModal[data-id="' + editJenisIdFromServer + '"]');
                if (btn) {
                    const data = {
                        id: btn.dataset.id,
                        kode_jenis: btn.dataset.kode_jenis,
                        jenis_obat: btn.dataset.jenis_obat
                    };
                    populateEditForm(data);
                }

                if (oldInput && Object.keys(oldInput).length) {
                    if (oldInput.kode_jenis) document.getElementById('edit_kode_jenis').value = oldInput.kode_jenis;
                    if (oldInput.jenis_obat) document.getElementById('edit_jenis_obat').value = oldInput.jenis_obat;
                }

                openEditModal();
            });
        }

    })();
</script>

</body>
</html>
