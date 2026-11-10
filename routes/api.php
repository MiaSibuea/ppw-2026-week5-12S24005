<?php

use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\BukuApiController;
use App\Http\Controllers\Api\V1\GithubConsumerController;
use App\Http\Controllers\Api\V1\PeminjamanApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/bukus', [BukuApiController::class, 'index']);
    Route::get('/bukus/{buku}/referensi-eksternal', [BukuApiController::class, 'externalReference']);
    Route::get('/bukus/{buku}', [BukuApiController::class, 'show']);
    Route::get('/github/repos/{owner}/{repository}', [GithubConsumerController::class, 'showRepository'])
        ->where([
            'owner' => '[A-Za-z0-9-]+',
            'repository' => '[A-Za-z0-9_.-]+',
        ]);

    Route::post('/auth/login', [AuthApiController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthApiController::class, 'me']);
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);

        Route::post('/bukus', [BukuApiController::class, 'store']);
        Route::put('/bukus/{buku}', [BukuApiController::class, 'update']);
        Route::patch('/bukus/{buku}', [BukuApiController::class, 'update']);
        Route::delete('/bukus/{buku}', [BukuApiController::class, 'destroy']);

        Route::get('/peminjamans', [PeminjamanApiController::class, 'index']);
        Route::get('/peminjamans/{peminjaman}', [PeminjamanApiController::class, 'show']);
        Route::post('/peminjamans', [PeminjamanApiController::class, 'store']);
        Route::patch('/peminjamans/{peminjaman}/kembalikan', [PeminjamanApiController::class, 'returnBook']);
    });
});
