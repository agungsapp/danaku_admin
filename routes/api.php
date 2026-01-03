<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Transaksi routes - protected by auth
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('transaksis', TransaksiController::class);
});
