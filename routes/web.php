<?php

use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\Soap\LibrarySoapController;
use Illuminate\Support\Facades\Route;

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
Route::get('/soap/sipus.wsdl', [LibrarySoapController::class, 'wsdl'])->name('soap.wsdl');
Route::post('/soap/sipus', [LibrarySoapController::class, 'checkClearance'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
    ->name('soap.clearance');
