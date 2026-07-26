@php
    function sortLink($column, $label) {
        $direction = request('direction') === 'asc' ? 'desc' : 'asc';
        $params = array_merge(request()->all(), [
            'sort' => $column,
            'direction' => $direction
        ]);

        return '<a href="'.route('satuan-obat.index', $params).'">'.$label.'</a>';
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Satuan Obat - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/satuan_obat/satuan_obat.css') }}">
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
            <h1>Satuan Obat</h1>
        </div>

        <x-alert type="success" />
        <x-alert type="error" />

        <div class="card">
            <div class="table-actions">
                <form method="GET" action="{{ route('satuan-obat.index') }}">
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
                            placeholder="Cari kode atau nama satuan obat..."
                            value="{{ request('search') }}"
                            class="search-input"
                        >

                        <a href="{{ route('satuan-obat.index') }}" class="btn-reset" style="display:flex;align-items:center;gap:6px;background:#6b7280;color:white;padding:8px 14px;border-radius:6px;text-decoration:none;">
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
            <table class="satuan-obat-table">
                <thead>
                <tr>
                    <x-sortable-th column="id" label="No." />
                    <x-sortable-th column="kode_satuan" label="Kode Satuan" />
                    <x-sortable-th column="satuan_obat" label="Nama Satuan Obat" />
                    {{-- <x-sortable-th column="created_at" label="Tanggal Dibuat" /> --}}
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                    @forelse ($satuanobats as $satuanobat)
                        <tr>
                            <td>{{ $satuanobat->id }}</td>
                            <td>{{ $satuanobat->kode_satuan }}</td>
                            <td>{{ $satuanobat->satuan_obat }}</td>
                            {{-- <td>{{ $satuanobat->created_at->format('d M Y') }}</td> --}}
                            <td>
                                <div class="action-buttons">
                                    <!-- EDIT (open modal) -->
                                    <button type="button"
                                        class="action-btn edit openEditModal"
                                        title="Edit"
                                        data-id="{{ $satuanobat->id }}"
                                        data-kode_satuan="{{ $satuanobat->kode_satuan }}"
                                        data-satuan_obat="{{ $satuanobat->satuan_obat }}">
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

                                    @if ($satuanobat->nama_obat_count > 0)
                                        <button type="button" class="action-btn delete disabled" title="Tidak bisa dihapus karena sudah digunakan pada daftar obat" disabled>
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
                                        <!-- DELETE (confirm-delete component) -->
                                        <x-confirm-delete action="{{ route('satuan-obat.destroy', $satuanobat->id) }}" :id="'delete-satuan-'.$satuanobat->id" title="Hapus Satuan" message="Yakin ingin menghapus satuan obat {{ $satuanobat->satuan_obat }}?">
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
                            <td colspan="6" class="empty">Tidak ada data satuan obat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="pagination-section">
                <div class="pagination-controls">
                    <div class="per-page-selector">
                        <form method="GET" action="{{ route('satuan-obat.index') }}" class="per-page-form">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                            <input type="hidden" name="direction" value="{{ request('direction') }}">
                            <label for="per_page_satuan" class="per-page-label">Tampilkan:</label>
                            <select name="per_page" id="per_page_satuan" class="per-page-input" onchange="this.form.submit()">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="pagination-wrapper">
                    {{ $satuanobats->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div id="createSatuanModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createSatuanTitle">
                <div class="modal-header">
                    <h2 id="createSatuanTitle">Tambah Satuan Obat</h2>
                    <button class="modal-close" id="closeCreateSatuanModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && !session('edit_satuan_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('satuan-obat.store') }}" method="POST" class="form-component">
                        @csrf

                        <div class="form-group">
                            <label for="kode_satuan">Kode Satuan <span class="required-star">*</span></label>
                            <input id="kode_satuan" type="text" name="kode_satuan" value="{{ old('kode_satuan', App\Models\SatuanObat::generateKode()) }}" readonly tabindex="-1">
                        </div>

                        <div class="form-group">
                            <label for="satuan_obat">Nama Satuan Obat <span class="required-star">*</span></label>
                            <input id="satuan_obat" type="text" name="satuan_obat" value="{{ old('satuan_obat') }}">
                        </div>

                        <div class="form-actions modal-actions">
                            <button type="button" class="btn-secondary" id="cancelCreateSatuanModal">Batal</button>
                            <button type="submit" class="btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="editSatuanModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="editSatuanTitle">
                <div class="modal-header">
                    <h2 id="editSatuanTitle">Edit Satuan Obat</h2>
                    <button class="modal-close" id="closeEditSatuanModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && session('edit_satuan_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="editSatuanForm" action="" method="POST" class="form-component">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-group">
                            <label for="edit_kode_satuan">Kode Satuan <span class="required-star">*</span></label>
                            <input id="edit_kode_satuan" type="text" name="kode_satuan" value="{{ old('kode_satuan') }}" readonly tabindex="-1">
                        </div>

                        <div class="form-group">
                            <label for="edit_satuan_obat">Nama Satuan Obat <span class="required-star">*</span></label>
                            <input id="edit_satuan_obat" type="text" name="satuan_obat" value="{{ old('satuan_obat') }}">
                        </div>

                        <div class="form-actions modal-actions">
                            <button type="button" class="btn-secondary" id="cancelEditSatuanModal">Batal</button>
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
        const modal = document.getElementById('createSatuanModal');
        const closeBtn = document.getElementById('closeCreateSatuanModal');
        const cancelBtn = document.getElementById('cancelCreateSatuanModal');

        const createForm = document.querySelector('#createSatuanModal form');

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

        openBtn && openBtn.addEventListener('click', function() {
            openModal();
            initCodeField();
        });
        closeBtn && closeBtn.addEventListener('click', closeModal);
        cancelBtn && cancelBtn.addEventListener('click', closeModal);

        // Auto open create modal if validation errors belong to create
        @if ($errors->any() && !session('edit_satuan_id'))
            document.addEventListener('DOMContentLoaded', function() { openModal(); });
        @endif

        // Edit modal handlers
        const editModal = document.getElementById('editSatuanModal');
        const openEditButtons = document.querySelectorAll('.openEditModal');
        // correct ids for edit modal
        const closeEditBtn = document.getElementById('closeEditSatuanModal');
        const cancelEditBtn = document.getElementById('cancelEditSatuanModal');
        const editForm = document.getElementById('editSatuanForm');

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
            editForm.action = '/satuan-obat/' + data.id;
            document.getElementById('edit_kode_satuan').value = data.kode_satuan || '';
            document.getElementById('edit_satuan_obat').value = data.satuan_obat || '';
        }

        function initCodeField() {
            const createCodeInput = document.getElementById('kode_satuan');
            if (createCodeInput && !createCodeInput.value) {
                createCodeInput.value = @json(App\Models\SatuanObat::generateKode());
            }
        }

        openEditButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const data = {
                    id: this.dataset.id,
                    kode_satuan: this.dataset.kode_satuan,
                    satuan_obat: this.dataset.satuan_obat
                };

                populateEditForm(data);
                openEditModal();
            });
        });

        closeEditBtn && closeEditBtn.addEventListener('click', closeEditModal);
        cancelEditBtn && cancelEditBtn.addEventListener('click', closeEditModal);

        const editSatuanIdFromServer = @json(session('edit_satuan_id'));
        const oldInput = @json(session()->getOldInput());

        if (editSatuanIdFromServer) {
            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.querySelector('.openEditModal[data-id="' + editSatuanIdFromServer + '"]');
                if (btn) {
                    const data = {
                        id: btn.dataset.id,
                        kode_satuan: btn.dataset.kode_satuan,
                        satuan_obat: btn.dataset.satuan_obat
                    };
                    populateEditForm(data);
                }

                if (oldInput && Object.keys(oldInput).length) {
                    if (oldInput.kode_satuan) document.getElementById('edit_kode_satuan').value = oldInput.kode_satuan;
                    if (oldInput.satuan_obat) document.getElementById('edit_satuan_obat').value = oldInput.satuan_obat;
                }

                openEditModal();
            });
        }

        // Auto submit filter form with debounce.
        const filterForm = document.querySelector('.table-actions form[method="GET"]');
        if (filterForm) {
            const searchField = filterForm.querySelector('input[name="search"]');
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
        }

    })();
</script>

</body>
</html>
