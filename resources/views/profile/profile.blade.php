<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Profil - PustumedApp</title>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
</head>
<body>
    <!-- Sidebar Component -->
    <x-sidebar />

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Navbar Component -->
        <x-navbar />

        <!-- Main Content -->
        <div class="container main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Profil Saya</h1>
            <p>Kelola informasi akun dan keamanan Anda</p>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="alert alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        <!-- Edit Profile Section -->
        <div class="profile-card">
            <h2>Informasi Pribadi</h2>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            placeholder="Masukkan nama lengkap Anda"
                            required
                        >
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            placeholder="Masukkan email Anda"
                            required
                        >
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
            </form>
        </div>

        <!-- Change Password Section -->
        <div class="profile-card">
            <h2>Ubah Password</h2>
            <form action="{{ route('profile.updatePassword') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="current_password">Password Saat Ini</label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        placeholder="Masukkan password saat ini"
                        required
                    >
                    @error('current_password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password baru (minimal 8 karakter)"
                        required
                    >
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ulangi password baru Anda"
                        required
                    >
                    @error('password_confirmation')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <p class="info-text">⚠️ Password harus berbeda dari password sebelumnya</p>
                <button type="submit" class="btn-save">🔒 Ubah Password</button>
            </form>
        </div>

        <!-- Account Info -->
        <div class="profile-card">
            <h2>Informasi Akun</h2>
            <div style="padding: 20px 0;">
                <p style="margin-bottom: 15px;">
                    <strong>User ID:</strong> {{ $user->id }}
                </p>
                <p style="margin-bottom: 15px;">
                    <strong>Email Terverifikasi:</strong> {{ $user->email_verified_at ? '✓ Ya' : '✗ Belum' }}
                </p>
                <p style="margin-bottom: 15px;">
                    <strong>Bergabung Sejak:</strong> {{ $user->created_at->format('d M Y H:i') }}
                </p>
                <p>
                    <strong>Update Terakhir:</strong> {{ $user->updated_at->format('d M Y H:i') }}
                </p>
            </div>
        </div>
    </div>
    </div>
</body>
</html>
