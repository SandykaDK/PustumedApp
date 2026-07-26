<aside class="sidebar" id="sidebar">
    {{-- SIDEBAR HEADER --}}
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('images/logo-pustumed.png') }}" alt="Pustumed Logo" class="sidebar-logo">
            <span class="sidebar-brand-text">Pustumed</span>
        </a>
        <div style="display: flex; gap: 10px; align-items: center;">
            <button class="toggle-sidebar-collapse" id="toggleSidebarCollapse" title="Collapse Sidebar">
                <svg class="heroicon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            <button class="toggle-sidebar-close" id="toggleSidebarClose" title="Close Sidebar">
                <svg class="heroicon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div class="sidebar-content">
        {{-- SIDEBAR MENU --}}
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                    <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            {{-- <li>
                <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Profil">
                    <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    <span>Profil</span>
                </a>
            </li> --}}
        </ul>
        {{-- MASTER --}}
        <div class="sidebar-section open" data-section="Master">
            <button class="sidebar-section-toggle">
                <div class="section-left">
                    <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7.5h18M3 12h18M3 16.5h18" />
                    </svg>
                    <span>Master</span>
                </div>

                <svg class="chevron" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <ul class="sidebar-submenu">
                @if(auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'kepala_pustu'))
                <li>
                    <a href="{{ route('users.index') }}" title="Daftar User">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        <span>Daftar User</span>
                    </a>
                </li>
                @endif

                @if(auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'petugas_administrasi'))
                <li>
                    <a href="{{ route('pasien.index') }}" title="Daftar Pasien">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span>Daftar Pasien</span>
                    </a>
                </li>
                @endif

                @if(auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'kepala_pustu'))
                <li>
                    <a href="{{ route('dokter.index') }}" title="Daftar Dokter">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span>Daftar Dokter</span>
                    </a>
                </li>
                @endif

                @if(auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'petugas_obat'))
                <li>
                    <a href="{{ route('jenis-obat.index') }}" title="Jenis Obat">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122" />
                        </svg>
                        <span>Jenis Obat</span>
                    </a>
                </li>
                @endif

                @if(auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'petugas_obat'))
                <li>
                    <a href="{{ route('satuan-obat.index') }}" title="Satuan Obat">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122" />
                        </svg>
                        <span>Satuan Obat</span>
                    </a>
                </li>
                @endif

                @if(auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'petugas_obat' || auth()->user()->role === 'petugas_administrasi' || auth()->user()->role === 'kepala_pustu'))
                <li>
                    <a href="{{ route('nama-obat.index') }}" title="Daftar Obat">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        <span>Daftar Obat</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <!-- TRANSAKSI -->
        <div class="sidebar-section open" data-section="Transaksi">
            <button class="sidebar-section-toggle">
                <div class="section-left">
                    <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7.5h18M3 12h18M3 16.5h18" />
                    </svg>
                    <span>Transaksi</span>
                </div>

                <svg class="chevron" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <ul class="sidebar-submenu">
                @if(auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'petugas_obat'))
                <li>
                    <a href="{{ route('penerimaan-obat.index') }}" title="Penerimaan Obat">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Penerimaan Obat</span>
                    </a>
                </li>
                @endif

                @if(auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'petugas_administrasi'))
                <li>
                    <a href="{{ route('pengeluaran-obat.index') }}" title="Pengeluaran Obat">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12-3-3m0 0-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Pengeluaran Obat</span>
                    </a>
                </li>
                @endif

                @if(auth()->check() && (auth()->user()->role === 'super_admin' || in_array(auth()->user()->role, ['petugas_obat', 'kepala_pustu'])))
                <li>
                    <a href="{{ route('pemusnahan-obat.index') }}" title="Pemusnahan Obat" class="sidebar-link-with-badge {{ request()->routeIs('pemusnahan-obat.*') ? 'active' : '' }}">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Pemusnahan Obat</span>
                        @if(auth()->check() && auth()->user()->role === 'petugas_obat' && ($sidebarApprovedPemusnahanCount ?? 0) > 0)
                            <span class="sidebar-badge" aria-label="{{ $sidebarApprovedPemusnahanCount }} pemusnahan sudah disetujui menunggu tindakan">
                                {{ $sidebarApprovedPemusnahanCount > 99 ? '99+' : $sidebarApprovedPemusnahanCount }}
                            </span>
                        @elseif(auth()->check() && auth()->user()->role === 'kepala_pustu' && ($sidebarPendingPemusnahanCount ?? 0) > 0)
                            <span class="sidebar-badge" aria-label="{{ $sidebarPendingPemusnahanCount }} pengajuan pemusnahan menunggu persetujuan">
                                {{ $sidebarPendingPemusnahanCount > 99 ? '99+' : $sidebarPendingPemusnahanCount }}
                            </span>
                        @endif
                    </a>
                </li>
                @endif

                <li>
                    @if(auth()->check() && auth()->user()->role === 'super_admin')
                    <a href="{{ route('min-max.index') }}" title="Min Max" class="{{ request()->routeIs('min-max.*') ? 'active' : '' }}">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V18Zm2.498-6.75h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V13.5Zm0 2.25h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V18Zm2.504-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V18Zm2.498-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5ZM8.25 6h7.5v2.25h-7.5V6ZM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0 0 12 2.25Z" />
                        </svg>
                        <span>Hitung Min Max</span>
                    </a>
                    @endif
                </li>
            </ul>
        </div>

        <!-- Laporan -->
        @if(auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'kepala_pustu'))
        <div class="sidebar-section open" data-section="Laporan">
            <button class="sidebar-section-toggle">
                <div class="section-left">
                    <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7.5h18M3 12h18M3 16.5h18" />
                    </svg>
                    <span>Laporan</span>
                </div>

                <svg class="chevron" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <ul class="sidebar-submenu">
                <li>
                    <a href="{{ route('permintaan-obat.index') }}" title="Permintaan Obat" class="{{ request()->routeIs('permintaan-obat.*') ? 'active' : '' }}">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Laporan Permintaan Obat</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('laporan-obat-kadaluwarsa.index') }}" title="Laporan Obat Kadaluwarsa" class="{{ request()->routeIs('laporan-obat-kadaluwarsa.*') ? 'active' : '' }}">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        <span>Laporan Obat Kadaluwarsa</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('laporan-pemusnahan-obat.index') }}" title="Laporan Pemusnahan Obat" class="{{ request()->routeIs('laporan-pemusnahan-obat.*') ? 'active' : '' }}">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        <span>Laporan Pemusnahan Obat</span>
                    </a>
                </li>
            </ul>
        </div>
        @endif

        {{-- <ul class="sidebar-menu">
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-logout" title="Logout">
                        <svg class="heroicon-sidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul> --}}
    </div>
</aside>

<script>
    const toggleCollapseBtn = document.getElementById('toggleSidebarCollapse');
    const toggleBtn = document.getElementById('toggleSidebar');
    const toggleCloseBtn = document.getElementById('toggleSidebarClose');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');

    // Load collapse state from localStorage
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    const bodyEl = document.body;

    if (isCollapsed && window.innerWidth > 768) {
        sidebar.classList.add('collapsed');
        bodyEl.classList.add('sidebar-collapsed');
        if (mainContent) mainContent.classList.add('collapsed-view');
    }

    // Toggle collapse on desktop
    if (toggleCollapseBtn) {
        toggleCollapseBtn.addEventListener('click', function() {
            if (window.innerWidth > 768 && sidebar) {
                const isNowCollapsed = !sidebar.classList.contains('collapsed');
                sidebar.classList.toggle('collapsed');

                if (isNowCollapsed) {
                    bodyEl.classList.add('sidebar-collapsed');
                    if (mainContent) mainContent.classList.add('collapsed-view');
                } else {
                    bodyEl.classList.remove('sidebar-collapsed');
                    if (mainContent) mainContent.classList.remove('collapsed-view');
                }

                localStorage.setItem('sidebarCollapsed', isNowCollapsed);
            }
        });
    }

    // Toggle mobile sidebar
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }

    // Close mobile sidebar
    if (toggleCloseBtn && sidebar) {
        toggleCloseBtn.addEventListener('click', function() {
            sidebar.classList.add('collapsed');
        });
    }

    // Close sidebar when clicking on a link (mobile)
    if (sidebar) {
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('collapsed');
                }
            });
        });

        // Close sidebar on logout (mobile)
        const logoutBtn = sidebar.querySelector('.sidebar-logout');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('collapsed');
                }
            });
        }
    }
</script>

<script>
    function setupSidebarIconTooltips() {
        const sidebarEl = document.getElementById('sidebar');
        if (!sidebarEl) return;

        // Ensure all clickable icons keep a readable tooltip when labels are hidden.
        sidebarEl.querySelectorAll('a').forEach((link) => {
            const textLabel = link.querySelector('span')?.textContent?.trim() || '';
            const tooltip = link.getAttribute('title') || textLabel;

            if (tooltip) {
                link.setAttribute('title', tooltip);
                link.setAttribute('aria-label', tooltip);
            }
        });

        sidebarEl.querySelectorAll('.sidebar-section-toggle').forEach((toggle) => {
            const sectionName = toggle.closest('.sidebar-section')?.getAttribute('data-section') || '';
            const tooltip = sectionName ? `Modul ${sectionName}` : '';

            if (tooltip) {
                toggle.setAttribute('title', tooltip);
                toggle.setAttribute('aria-label', tooltip);
            }
        });
    }

    // Function to save sidebar state to localStorage
    function saveSidebarState() {
        const sections = {};
        document.querySelectorAll('.sidebar-section').forEach((section) => {
            const sectionName = section.getAttribute('data-section');
            if (sectionName) {
                sections[sectionName] = section.classList.contains('open');
            }
        });
        console.log('Saved sidebar state:', sections);
        localStorage.setItem('sidebarSectionsState', JSON.stringify(sections));
    }

    // Function to restore sidebar state from localStorage
    function restoreSidebarState() {
        const saved = localStorage.getItem('sidebarSectionsState');
        if (saved) {
            const sections = JSON.parse(saved);
            console.log('Restoring sidebar state:', sections);
            document.querySelectorAll('.sidebar-section').forEach((section) => {
                const sectionName = section.getAttribute('data-section');
                if (sectionName && sections.hasOwnProperty(sectionName)) {
                    if (sections[sectionName]) {
                        section.classList.add('open');
                    } else {
                        section.classList.remove('open');
                    }
                }
            });
        }
    }

    // Restore state on page load
    document.addEventListener('DOMContentLoaded', function() {
        setupSidebarIconTooltips();
        restoreSidebarState();

        // Toggle and save state
        document.querySelectorAll('.sidebar-section-toggle').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const section = this.closest('.sidebar-section');
                if (!section) return;

                // Explicitly toggle instead of using classList.toggle
                if (section.classList.contains('open')) {
                    section.classList.remove('open');
                } else {
                    section.classList.add('open');
                }

                console.log('Section toggled:', section.getAttribute('data-section'), 'Is now open:', section.classList.contains('open'));
                saveSidebarState();
            });
        });
    });
</script>
