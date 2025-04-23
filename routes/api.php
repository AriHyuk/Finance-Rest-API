<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;

Route::post('register', [AuthController::class, 'register']);
// routes/api.php
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    Route::post('top-up', [TransactionController::class, 'topUp']);
    Route::post('transfer', [TransactionController::class, 'transfer']);
    Route::get('report-transactions', [TransactionController::class, 'reportTransactions']);
    Route::post('update-profile', [TransactionController::class, 'updateProfile']);
});

