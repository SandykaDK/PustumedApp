@php
    function sortLink($column, $label) {
        $direction = request('direction') === 'asc' ? 'desc' : 'asc';
        $params = array_merge(request()->all(), [
            'sort' => $column,
            'direction' => $direction
        ]);

        return '<a href="'.route('users.index', $params).'">'.$label.'</a>';
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar User - PustumedApp</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/daftar_user/daftar_user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/modal.css') }}">
</head>
<body>

    <x-sidebar />

    <div class="main-wrapper">
        <x-navbar />

        <div class="container main-content">

        <div class="page-header">
            <h1>Daftar User</h1>
        </div>

        <x-alert type="success" />
        <x-alert type="error" />

        <div class="card">
            <div class="table-actions">
                <form method="GET" action="{{ route('users.index') }}" class="filter-form">
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
                                placeholder="Cari nama, email, atau no telepon..."
                                value="{{ request('search') }}"
                                class="search-input"
                            >
                        </div>

                        <div class="date-input-group">
                            <label for="status" class="date-label">Status</label>
                            <select id="status" name="status" class="date-input">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-filter">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            Cari
                        </button>
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
            <table class="user-table">
                <thead>
                <tr>
                    <th>No</th>
                    <x-sortable-th column="name" label="Nama" />
                    <x-sortable-th column="email" label="Email" />
                    <x-sortable-th column="no_telepon" label="No Telepon" />
                    <x-sortable-th column="role" label="Role" />
                    <th>Status</th>
                    <x-sortable-th column="created_at" label="Tanggal Bergabung" />
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                    @forelse ($users as $user)
                        <tr data-status="{{ $user->status }}">
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->no_telepon }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                <span class="status-badge {{ $user->status == 'aktif' ? 'status-aktif' : 'status-nonaktif' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="action-buttons">

                                    <!-- EDIT (open modal) -->
                                    <button type="button"
                                        class="action-btn edit openEditModal"
                                        title="Edit"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-no_telepon="{{ $user->no_telepon }}"
                                        data-role="{{ $user->role }}"
                                        data-status="{{ $user->status }}">
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

                                    <!-- DELETE -->
                                    <x-confirm-delete action="{{ route('users.destroy', $user->id) }}" :id="'delete-user-'.$user->id" title="Hapus User" message="Yakin ingin menghapus user {{ $user->name }}?">
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
                            <td colspan="8" class="empty">Tidak ada data user</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="pagination-section">
                <div class="pagination-controls">
                    <div class="per-page-selector">
                        <form method="GET" action="{{ route('users.index') }}" class="per-page-form">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                            <input type="hidden" name="direction" value="{{ request('direction') }}">
                            <label for="per_page_user" class="per-page-label">Tampilkan:</label>
                            <select name="per_page" id="per_page_user" class="per-page-input" onchange="this.form.submit()">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="pagination-wrapper">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        <!-- Create User Modal -->
        <div id="createUserModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createUserTitle">

                <div class="modal-header">
                    <h2 id="createUserTitle">Tambah User</h2>
                    <button class="modal-close" id="closeCreateModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && !session('edit_user_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('users.store') }}" method="POST" class="form-component">
                        @csrf

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="no_telepon">No Telepon</label>
                                <input id="no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon') }}">
                            </div>

                            <div class="form-group">
                                <label for="role">Role</label>
                                <select id="role" name="role" required>
                                    <option value="petugas_administrasi" {{ old('role') == 'petugas_administrasi' ? 'selected' : '' }}>Petugas Administrasi</option>
                                    <option value="petugas_obat" {{ old('role') == 'petugas_obat' ? 'selected' : '' }}>Petugas Obat</option>
                                    <option value="kepala_pustu" {{ old('role') == 'kepala_pustu' ? 'selected' : '' }}>Kepala Pustu</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required>
                        </div>

                        <div class="form-group">
                            <label for="status_toggle">Status Akun</label>
                            <div class="toggle-switch">
                                <input type="hidden" id="status_hidden" name="status" value="{{ old('status', 'aktif') }}">
                                <input type="checkbox" id="status_toggle" value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'checked' : '' }}>
                                <label for="status_toggle" class="toggle-slider"></label>
                                <span class="toggle-text">{{ old('status', 'aktif') == 'aktif' ? 'Aktif' : 'Nonaktif' }}</span>
                            </div>
                        </div>

                        <div class="form-actions modal-actions">
                            <button type="button" class="btn-secondary" id="cancelCreateModal">Batal</button>
                            <button type="submit" class="btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>

        <!-- Edit User Modal -->
        <div id="editUserModal" class="modal hidden" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="editUserTitle">

                <div class="modal-header">
                    <h2 id="editUserTitle">Edit User</h2>
                    <button class="modal-close" id="closeEditModal" aria-label="Tutup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && session('edit_user_id'))
                        <div class="error-list modern">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="editUserForm" action="" method="POST" class="form-component">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="edit_name">Nama</label>
                                <input id="edit_name" type="text" name="name" value="{{ old('name') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="edit_email">Email</label>
                                <input id="edit_email" type="email" name="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="edit_no_telepon">No Telepon</label>
                                <input id="edit_no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon') }}">
                            </div>

                            <div class="form-group">
                                <label for="edit_role">Role</label>
                                <select id="edit_role" name="role" required>
                                    <option value="petugas_administrasi">Petugas Administrasi</option>
                                    <option value="petugas_obat">Petugas Obat</option>
                                    <option value="kepala_pustu">Kepala Pustu</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_password">Password Baru</label>
                            <input id="edit_password" type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                        </div>

                        <div class="form-group">
                            <label for="edit_password_confirmation">Konfirmasi Password Baru</label>
                            <input id="edit_password_confirmation" type="password" name="password_confirmation" placeholder="Kosongkan jika tidak ingin mengubah">
                        </div>

                        <div class="form-group">
                            <label for="edit_status_toggle">Status Akun</label>
                            <div class="toggle-switch">
                                <input type="hidden" id="edit_status_hidden" name="status" value="aktif">
                                <input type="checkbox" id="edit_status_toggle" value="aktif">
                                <label for="edit_status_toggle" class="toggle-slider"></label>
                                <span class="toggle-text">Aktif</span>
                            </div>
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
        function initToggleSwitches() {
            const toggles = document.querySelectorAll('.toggle-switch input[type="checkbox"]');
            toggles.forEach(toggle => {
                const hiddenInput = toggle.previousElementSibling;
                const labelText = toggle.nextElementSibling.nextElementSibling;

                const updateStatusValue = () => {
                    const value = toggle.checked ? 'aktif' : 'nonaktif';
                    if (hiddenInput && hiddenInput.type === 'hidden') {
                        hiddenInput.value = value;
                    }
                    if (labelText) {
                        labelText.textContent = toggle.checked ? 'Aktif' : 'Nonaktif';
                    }
                };

                updateStatusValue();

                toggle.addEventListener('change', function() {
                    updateStatusValue();
                });
            });
        }

        function setupCreateModal() {
            const openBtn = document.getElementById('openCreateModal');
            const modal = document.getElementById('createUserModal');
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

            @if ($errors->any() && !session('edit_user_id'))
                openModal();
            @endif
        }

        function setupEditModal() {
            const editModal = document.getElementById('editUserModal');
            const openEditButtons = document.querySelectorAll('.openEditModal');
            const closeEditBtn = document.getElementById('closeEditModal');
            const cancelEditBtn = document.getElementById('cancelEditModal');
            const editForm = document.getElementById('editUserForm');

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
                editForm.action = '/users/' + data.id;
                document.getElementById('edit_name').value = data.name || '';
                document.getElementById('edit_email').value = data.email || '';
                document.getElementById('edit_no_telepon').value = data.no_telepon || '';
                document.getElementById('edit_role').value = data.role || 'user';

                const statusToggle = document.getElementById('edit_status_toggle');
                const statusHidden = document.getElementById('edit_status_hidden');
                const statusText = statusToggle && statusToggle.nextElementSibling.nextElementSibling;
                if (statusToggle && statusHidden && data.status) {
                    statusToggle.checked = data.status === 'aktif';
                    statusHidden.value = data.status === 'aktif' ? 'aktif' : 'nonaktif';
                    if (statusText) {
                        statusText.textContent = data.status === 'aktif' ? 'Aktif' : 'Nonaktif';
                    }
                }
            }

            openEditButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const data = {
                        id: this.dataset.id,
                        name: this.dataset.name,
                        email: this.dataset.email,
                        no_telepon: this.dataset.no_telepon,
                        role: this.dataset.role,
                        status: this.dataset.status
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

            const editUserIdFromServer = @json(session('edit_user_id'));
            const oldInput = @json(session()->getOldInput());

            if (editUserIdFromServer) {
                const btn = document.querySelector('.openEditModal[data-id="' + editUserIdFromServer + '"]');
                if (btn) {
                    const data = {
                        id: btn.dataset.id,
                        name: btn.dataset.name,
                        email: btn.dataset.email,
                        no_telepon: btn.dataset.no_telepon,
                        role: btn.dataset.role,
                        status: btn.dataset.status
                    };
                    populateEditForm(data);
                }

                if (oldInput && Object.keys(oldInput).length) {
                    if (oldInput.name) document.getElementById('edit_name').value = oldInput.name;
                    if (oldInput.email) document.getElementById('edit_email').value = oldInput.email;
                    if (oldInput.no_telepon) document.getElementById('edit_no_telepon').value = oldInput.no_telepon;
                    if (oldInput.role) document.getElementById('edit_role').value = oldInput.role;
                    if (oldInput.status) {
                        const statusToggle = document.getElementById('edit_status_toggle');
                        const statusHidden = document.getElementById('edit_status_hidden');
                        const statusText = statusToggle && statusToggle.nextElementSibling.nextElementSibling;
                        if (statusToggle && statusHidden) {
                            statusToggle.checked = oldInput.status === 'aktif';
                            statusHidden.value = oldInput.status === 'aktif' ? 'aktif' : 'nonaktif';
                            if (statusText) {
                                statusText.textContent = oldInput.status === 'aktif' ? 'Aktif' : 'Nonaktif';
                            }
                        }
                    }
                }

                openEditModal();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initToggleSwitches();
            setupCreateModal();
            setupEditModal();
        });
    })();
</script>

</body>
</html>
