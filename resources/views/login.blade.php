<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Login - PustumedApp</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="login-icon">
                <img src="{{ asset('images/logo-pustumed.png') }}" alt="Logo PustumedApp">
            </div>

            <div class="header">
                <h1>PUSTUMED APP</h1>
                <p>Kelola stok obat dengan mudah</p>
            </div>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="identifier">Email atau Username</label>
                    <input
                        type="text"
                        id="identifier"
                        name="identifier"
                        placeholder="Masukkan email atau username Anda"
                        value="{{ old('identifier') }}"
                        required
                    >
                    @error('identifier')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password Anda"
                        required
                    >
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
