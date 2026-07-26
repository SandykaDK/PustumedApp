# Test Plan Daftar User

## Application Overview

Test plan untuk menu Daftar User di aplikasi PustumedApp, meliputi tampilan halaman, pencarian, filter status, modal tambah/edit, validasi form, serta aksi hapus.

## Test Scenarios

### 1. Daftar User

**Seed:** `tests/e2e/seed.spec.ts`

#### 1.1. Halaman Daftar User tampil dengan data dan tombol utama

**File:** `tests/e2e/daftar-user.spec.js`

**Steps:**
  1. Buka halaman /users setelah login
    - expect: Halaman Daftar User tampil dengan judul 'Daftar User'
    - expect: Tabel user terlihat
    - expect: Tombol 'Tambah' terlihat

#### 1.2. User dapat mencari data berdasarkan nama, email, atau nomor telepon

**File:** `tests/e2e/daftar-user.spec.js`

**Steps:**
  1. Masukkan kata kunci pada kolom pencarian
    - expect: Hasil tabel memfilter data sesuai kata kunci
    - expect: Tabel tetap tampil dan tidak error

#### 1.3. User dapat memfilter data berdasarkan status aktif atau nonaktif

**File:** `tests/e2e/daftar-user.spec.js`

**Steps:**
  1. Pilih status Aktif atau Nonaktif pada filter status
    - expect: Tabel menampilkan hanya data dengan status yang dipilih

#### 1.4. User dapat mereset filter pencarian dan status

**File:** `tests/e2e/daftar-user.spec.js`

**Steps:**
  1. Klik tombol Reset setelah menerapkan pencarian/filter
    - expect: Filter dibersihkan
    - expect: Seluruh data tampil kembali

#### 1.5. User dapat membuka modal tambah dan membuat user baru

**File:** `tests/e2e/daftar-user.spec.js`

**Steps:**
  1. Klik tombol Tambah
    - expect: Modal Tambah User terbuka
    - expect: Form nama, email, role, password, konfirmasi password, dan status terlihat
  2. Isi form dengan data valid lalu klik Simpan
    - expect: User baru tersimpan
    - expect: Modal tertutup atau muncul notifikasi sukses
    - expect: Data user baru muncul di tabel

#### 1.6. Validasi form tambah user

**File:** `tests/e2e/daftar-user.spec.js`

**Steps:**
  1. Buka modal Tambah User lalu submit dengan data yang tidak valid atau kosong
    - expect: Validasi form berjalan
    - expect: Pesan error muncul dan user tidak tersimpan

#### 1.7. User dapat membuka modal edit dan mengubah data user

**File:** `tests/e2e/daftar-user.spec.js`

**Steps:**
  1. Klik tombol edit pada salah satu baris user
    - expect: Modal Edit User terbuka dengan data existing terisi
  2. Ubah salah satu field lalu klik Simpan
    - expect: Perubahan tersimpan
    - expect: Data yang diperbarui tampil di tabel

#### 1.8. User dapat menghapus user melalui konfirmasi hapus

**File:** `tests/e2e/daftar-user.spec.js`

**Steps:**
  1. Klik tombol hapus pada salah satu baris user
    - expect: Modal konfirmasi hapus tampil
  2. Konfirmasi penghapusan
    - expect: User hilang dari tabel
    - expect: Muncul notifikasi sukses atau state yang sesuai
