
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PeminjamanController;

// Halaman login dapat diakses tanpa login.
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

// Logout hanya dapat dilakukan oleh pengguna yang sudah login.
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Halaman dan proses aplikasi yang membutuhkan autentikasi.
Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return redirect()->route('buku.index');
    });

    Route::resource('kategori', KategoriController::class);

    Route::resource('buku', BukuController::class);

    // Riwayat peminjaman.
    Route::get('/peminjaman', [
        PeminjamanController::class,
        'index'
    ])->name('peminjaman.index');

    // Proses peminjaman.
    Route::post('/buku/{id}/pinjam', [
        PeminjamanController::class,
        'pinjam'
    ])->name('peminjaman.pinjam');

    // Proses pengembalian.
    Route::post('/peminjaman/{id}/kembalikan', [
        PeminjamanController::class,
        'kembalikan'
    ])->name('peminjaman.kembalikan');
});