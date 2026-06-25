<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\JenisObatController;
use App\Http\Controllers\LaporanObatKadaluwarsaController;
use App\Http\Controllers\LaporanPemusnahanObatController;
use App\Http\Controllers\MinMaxController;
use App\Http\Controllers\NamaObatController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PemusnahanObatController;
use App\Http\Controllers\PenerimaanObatController;
use App\Http\Controllers\PengeluaranObatController;
use App\Http\Controllers\PermintaanObatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SatuanObatController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');
// Per-tab token login (used by JS to create a tab-scoped token)
// Disabled: application uses session-based login now.
// Route::post('/tab-login', [\App\Http\Controllers\ApiAuthController::class, 'login'])
//     ->name('tab.login');

// Route::post('/tab-logout', [\App\Http\Controllers\ApiAuthController::class, 'logout'])
//     ->name('tab.logout');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// Dashboard Route (Protected)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Profile Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.updatePassword');
});

// Daftar User
Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index');

    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy');
});

// Jenis Obat
Route::middleware(['auth'])->group(function () {
    Route::get('/jenis-obat', [JenisObatController::class, 'index'])
        ->name('jenis-obat.index');

    Route::get('/jenis-obat/create', [JenisObatController::class, 'create'])
        ->name('jenis-obat.create');

    Route::post('/jenis-obat', [JenisObatController::class, 'store'])
        ->name('jenis-obat.store');

    Route::get('/jenis-obat/{jenis_obat}/edit', [JenisObatController::class, 'edit'])
        ->name('jenis-obat.edit');

    Route::put('/jenis-obat/{jenis_obat}', [JenisObatController::class, 'update'])
        ->name('jenis-obat.update');

    Route::delete('/jenis-obat/{jenis_obat}', [JenisObatController::class, 'destroy'])
        ->name('jenis-obat.destroy');
});

// Satuan Obat
Route::middleware(['auth'])->group(function () {
    Route::get('/satuan-obat', [SatuanObatController::class, 'index'])
        ->name('satuan-obat.index');

    Route::get('/satuan-obat/create', [SatuanObatController::class, 'create'])
        ->name('satuan-obat.create');

    Route::post('/satuan-obat', [SatuanObatController::class, 'store'])
        ->name('satuan-obat.store');

    Route::get('/satuan-obat/{satuan_obat}/edit', [SatuanObatController::class, 'edit'])
        ->name('satuan-obat.edit');

    Route::put('/satuan-obat/{satuan_obat}', [SatuanObatController::class, 'update'])
        ->name('satuan-obat.update');

    Route::delete('/satuan-obat/{satuan_obat}', [SatuanObatController::class, 'destroy'])
        ->name('satuan-obat.destroy');
});

// Nama Obat
Route::middleware(['auth'])->group(function () {
    Route::get('/nama-obat', [NamaObatController::class, 'index'])
        ->name('nama-obat.index');

    Route::get('/nama-obat/create', [NamaObatController::class, 'create'])
        ->name('nama-obat.create');

    Route::post('/nama-obat', [NamaObatController::class, 'store'])
        ->name('nama-obat.store');

    // AJAX: generate kode by jenis
    Route::get('/nama-obat/generate-kode/{jenisId}', [NamaObatController::class, 'generateKode'])
        ->name('nama-obat.generate-kode');

    Route::get('/nama-obat/{nama_obat}/edit', [NamaObatController::class, 'edit'])
        ->name('nama-obat.edit');

    // view stock details for a particular obat (JSON API)
    Route::get('/nama-obat/{nama_obat}/stok', [NamaObatController::class, 'show'])
        ->name('nama-obat.stok');

    Route::put('/nama-obat/{nama_obat}', [NamaObatController::class, 'update'])
        ->name('nama-obat.update');

    Route::delete('/nama-obat/{nama_obat}', [NamaObatController::class, 'destroy'])
        ->name('nama-obat.destroy');
});

// Pasien
Route::middleware(['auth'])->group(function () {

    Route::get('/pasien', [PasienController::class, 'index'])
        ->name('pasien.index');

    Route::get('/pasien/create', [PasienController::class, 'create'])
        ->name('pasien.create');

    Route::post('/pasien', [PasienController::class, 'store'])
        ->name('pasien.store');

    Route::get('/pasien/{pasien}/edit', [PasienController::class, 'edit'])
        ->name('pasien.edit');

    Route::put('/pasien/{pasien}', [PasienController::class, 'update'])
        ->name('pasien.update');

    Route::delete('/pasien/{pasien}', [PasienController::class, 'destroy'])
        ->name('pasien.destroy');
});

// Dokter
Route::middleware(['auth'])->group(function () {

    Route::get('/dokter', [DokterController::class, 'index'])
        ->name('dokter.index');

    Route::get('/dokter/create', [DokterController::class, 'create'])
        ->name('dokter.create');

    Route::post('/dokter', [DokterController::class, 'store'])
        ->name('dokter.store');

    Route::get('/dokter/{dokter}/edit', [DokterController::class, 'edit'])
        ->name('dokter.edit');

    Route::put('/dokter/{dokter}', [DokterController::class, 'update'])
        ->name('dokter.update');

    Route::delete('/dokter/{dokter}', [DokterController::class, 'destroy'])
        ->name('dokter.destroy');
});

// Penerimaan Obat
Route::middleware(['auth'])->group(function () {

    Route::get('/penerimaan-obat', [PenerimaanObatController::class, 'index'])
        ->name('penerimaan-obat.index');

    Route::get('/penerimaan-obat/create', [PenerimaanObatController::class, 'create'])
        ->name('penerimaan-obat.create');

    Route::post('/penerimaan-obat', [PenerimaanObatController::class, 'store'])
        ->name('penerimaan-obat.store');

    Route::get('/penerimaan-obat/{penerimaan_obat}/edit', [PenerimaanObatController::class, 'edit'])
        ->name('penerimaan-obat.edit');

    Route::put('/penerimaan-obat/{penerimaan_obat}', [PenerimaanObatController::class, 'update'])
        ->name('penerimaan-obat.update');

    Route::delete('/penerimaan-obat/{penerimaan_obat}', [PenerimaanObatController::class, 'destroy'])
        ->name('penerimaan-obat.destroy');
});

// Pengeluaran Obat
Route::middleware(['auth'])->group(function () {

    Route::get('/pengeluaran-obat', [PengeluaranObatController::class, 'index'])
        ->name('pengeluaran-obat.index');

    Route::get('/pengeluaran-obat/create', [PengeluaranObatController::class, 'create'])
        ->name('pengeluaran-obat.create');

    Route::post('/pengeluaran-obat', [PengeluaranObatController::class, 'store'])
        ->name('pengeluaran-obat.store');

    Route::get('/pengeluaran-obat/{pengeluaran_obat}/edit', [PengeluaranObatController::class, 'edit'])
        ->name('pengeluaran-obat.edit');

    Route::put('/pengeluaran-obat/{pengeluaran_obat}', [PengeluaranObatController::class, 'update'])
        ->name('pengeluaran-obat.update');

    Route::delete('/pengeluaran-obat/{pengeluaran_obat}', [PengeluaranObatController::class, 'destroy'])
        ->name('pengeluaran-obat.destroy');

    Route::get('/pengeluaran-obat/stok/{namaObatId}', [PengeluaranObatController::class, 'getStokByObat'])
        ->name('pengeluaran-obat.stok');

    Route::get('/pengeluaran-obat/satuan/{namaObatId}', [PengeluaranObatController::class, 'getSatuanByObat'])
        ->name('pengeluaran-obat.satuan');

    // AJAX search for nama obat
    Route::get('/pengeluaran-obat/search', [PengeluaranObatController::class, 'searchNamaObat'])
        ->name('pengeluaran-obat.search');
    Route::get('/pengeluaran-obat/detail/{namaObatId}', [PengeluaranObatController::class, 'getNamaObatDetail'])
        ->name('pengeluaran-obat.detail');

    // Print PDF
    Route::get('/pengeluaran-obat/{id}/print', [PengeluaranObatController::class, 'printPDF'])
        ->name('pengeluaran-obat.print');
});

// Pemusnahan Obat
Route::middleware(['auth'])->group(function () {
    Route::get('/pemusnahan-obat', [PemusnahanObatController::class, 'index'])
        ->name('pemusnahan-obat.index');

    Route::post('/pemusnahan-obat', [PemusnahanObatController::class, 'store'])
        ->name('pemusnahan-obat.store');

    Route::post('/pemusnahan-obat/{id}/approve', [PemusnahanObatController::class, 'approve'])
        ->name('pemusnahan-obat.approve');

    Route::post('/pemusnahan-obat/{id}/reject', [PemusnahanObatController::class, 'reject'])
        ->name('pemusnahan-obat.reject');

    Route::post('/pemusnahan-obat/{id}/dimusnahkan', [PemusnahanObatController::class, 'dimusnahkan'])
        ->name('pemusnahan-obat.dimusnahkan');

    Route::get('/pemusnahan-obat/{id}/unduh-foto', [PemusnahanObatController::class, 'downloadFoto'])
        ->name('pemusnahan-obat.download-foto');

    // Cancel a pending pemusnahan request (only request owner)
    Route::post('/pemusnahan-obat/{id}/cancel', [PemusnahanObatController::class, 'cancel'])
        ->name('pemusnahan-obat.cancel');
});

// Permintaan Obat
Route::middleware(['auth'])->group(function () {
    Route::get('/permintaan-obat', [PermintaanObatController::class, 'index'])
        ->name('permintaan-obat.index');

    Route::post('/permintaan-obat', [PermintaanObatController::class, 'store'])
        ->name('permintaan-obat.store');
});

// Min Max
Route::middleware(['auth'])->group(function () {
    Route::get('/min-max', [MinMaxController::class, 'index'])
        ->name('min-max.index');
});

// Laporan Obat Kadaluwarsa
Route::middleware(['auth'])->group(function () {
    Route::get('/laporan-obat-kadaluwarsa', [LaporanObatKadaluwarsaController::class, 'index'])
        ->name('laporan-obat-kadaluwarsa.index');
});

// Laporan Pemusnahan Obat
Route::middleware(['auth'])->group(function () {
    Route::get('/laporan-pemusnahan-obat', [LaporanPemusnahanObatController::class, 'index'])
        ->name('laporan-pemusnahan-obat.index');
});
