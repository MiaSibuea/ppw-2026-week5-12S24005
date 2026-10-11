<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;

/*
|--------------------------------------------------------------------------
| Web Routes - SIPUS-Del (Sistem Informasi Perpustakaan Kampus Del)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('buku.index');
});

// 7 RESTful Resource Actions untuk Kategori & Buku
Route::resource('kategori', KategoriController::class);
Route::resource('buku', BukuController::class);