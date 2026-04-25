<!-- Navbar Component -->
<nav class="navbar">
    <div class="navbar-wrapper">
        <div class="navbar-content">
            <div class="navbar-title">
                <h2 id="pageTitle"></h2>
            </div>

            <div class="navbar-right">
                <div class="profile-dropdown">
                    <button class="profile-btn" id="profileBtn">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="profile-icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span class="profile-name">{{ Auth::user()->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="profile-chevron">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div class="dropdown-menu" id="profileMenu">
                        <div class="dropdown-header">
                            <div class="profile-info">
                                <div class="profile-name-text">{{ Auth::user()->name }}</div>
                                <div class="profile-role">
                                    @php
                                        $roleMap = [
                                            'kepala_pustu' => 'Kepala Pustu',
                                            'petugas_gudang' => 'Petugas Gudang',
                                            'petugas_administrasi' => 'Petugas Administrasi',
                                        ];
                                        $displayRole = $roleMap[Auth::user()->role] ?? ucfirst(Auth::user()->role ?? 'User');
                                    @endphp
                                    {{ $displayRole }}
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="dropdown-footer">
                            @csrf
                            <button type="submit" class="dropdown-item logout">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="logout-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    .navbar {
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        padding: 0;
        position: sticky;
        top: 0;
        z-index: 98;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .navbar-wrapper {
        max-width: 100%;
        margin: 0 auto;
        padding: 0 40px;
    }

    .navbar-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 70px;
    }

    .navbar-title h2 {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    /* Profile */
    .profile-dropdown {
        position: relative;
    }

    .profile-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        transition: all 0.3s ease;
        padding: 8px 12px;
        border-radius: 8px;
    }

    .profile-btn:hover {
        background: #f3f4f6;
    }

    .profile-icon {
        width: 24px;
        height: 24px;
        color: #6b7280;
    }

    .profile-chevron {
        width: 16px;
        height: 16px;
        color: #9ca3af;
        transition: transform 0.3s ease;
    }

    .profile-btn:hover .profile-chevron {
        color: #6b7280;
    }

    /* Dropdown */
    .dropdown-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 12px);
        background: #ffffff;
        border-radius: 12px;
        min-width: 200px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        display: none;
        z-index: 100;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-header {
        padding: 16px;
        background: #ffffff;
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .profile-name-text {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
    }

    .profile-role {
        font-size: 12px;
        color: #9ca3af;
        font-weight: 500;
    }

    .dropdown-divider {
        height: 1px;
        background: #e5e7eb;
    }

    .dropdown-footer {
        padding: 0;
    }

    .dropdown-item {
        width: 100%;
        padding: 12px 16px;
        background: none;
        border: none;
        text-align: left;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s ease;
        color: #374151;
    }

    .dropdown-item:hover {
        background: #f9fafb;
    }

    .dropdown-item.logout {
        color: #dc2626;
    }

    .dropdown-item.logout:hover {
        background: #fee2e2;
    }

    .logout-icon {
        width: 18px;
        height: 18px;
    }

    @media (max-width: 768px) {
        .navbar-wrapper {
            padding: 0 20px;
        }

        .navbar-title h2 {
            font-size: 18px;
        }

        .navbar-content {
            height: 60px;
        }

        .profile-name {
            display: none;
        }
    }
</style>

<script>
    const profileBtn = document.getElementById('profileBtn');
    const profileMenu = document.getElementById('profileMenu');

    profileBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        profileMenu.classList.toggle('show');
    });

    document.addEventListener('click', function () {
        profileMenu.classList.remove('show');
    });
</script>
