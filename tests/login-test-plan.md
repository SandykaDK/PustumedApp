# Test Plan Login

## Application Overview

Test plan untuk fitur login aplikasi PustumedApp, mencakup skenario berhasil login, gagal login, validasi form, serta perlindungan akses setelah login.

## Test Scenarios

### 1. Login

**Seed:** `tests/e2e/login.spec.js`

#### 1.1. Login berhasil dengan akun aktif

**File:** `tests/e2e/login.spec.js`

**Steps:**
  1. Buka halaman /login
    - expect: Halaman login ditampilkan dengan form email/username, password, dan tombol Login.
  2. Masukkan email aktif dan password yang benar
    - expect: Form terisi dengan benar.
  3. Klik tombol Login
    - expect: Pengguna diarahkan ke halaman dashboard dan login berhasil.

#### 1.2. Login gagal dengan kredensial salah

**File:** `tests/e2e/login.spec.js`

**Steps:**
  1. Buka halaman /login
    - expect: Halaman login tersedia.
  2. Masukkan email atau username yang tidak terdaftar dan password salah
    - expect: Form menerima input.
  3. Klik tombol Login
    - expect: Muncul pesan error 'Email/username atau password salah.' dan pengguna tetap di halaman login.

#### 1.3. Validasi field wajib kosong

**File:** `tests/e2e/login.spec.js`

**Steps:**
  1. Buka halaman /login
    - expect: Halaman login tersedia.
  2. Kosongkan identifier dan password, lalu klik Login
    - expect: Browser menolak submit karena field required.

#### 1.4. Login dengan username aktif

**File:** `tests/e2e/login.spec.js`

**Steps:**
  1. Buka halaman /login
    - expect: Halaman login tersedia.
  2. Masukkan username aktif beserta password yang benar
    - expect: Login berhasil dan pengguna diarahkan ke dashboard.

#### 1.5. Login dengan akun nonaktif ditolak

**File:** `tests/e2e/login.spec.js`

**Steps:**
  1. Buka halaman /login
    - expect: Halaman login tersedia.
  2. Masukkan akun dengan status nonaktif dan password yang benar
    - expect: Login ditolak dan muncul pesan error yang sama.

#### 1.6. Akses dashboard tanpa login ditolak

**File:** `tests/e2e/login.spec.js`

**Steps:**
  1. Buka URL /dashboard tanpa sesi login
    - expect: Aplikasi menolak akses dan mengembalikan error atau redirect ke halaman login.
