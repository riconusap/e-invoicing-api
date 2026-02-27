<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\PlacementController;
use App\Http\Controllers\API\ContractClientController;
use App\Http\Controllers\API\ContractEmployeeController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\PicExternalController;

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:3,1');
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/is-logged-in', [AuthController::class, 'isLoggedIn']);
    Route::get('/login-info', [AuthController::class, 'loginInfo']);
    Route::post('/logout-all-devices', [AuthController::class, 'logoutFromAllDevices']);
    Route::post('/logout-all-user', [AuthController::class, 'logoutAllExceptCurrent']);
    Route::get('/active-sessions', [AuthController::class, 'getActiveSessions']);
});

// Protected API Resources
Route::middleware('auth:api')->group(function () {
    // Employee routes with custom endpoint for suggesting NIP
    Route::get('/employees/suggest-nip', [EmployeeController::class, 'suggestNip']);
    Route::apiResource('employees', EmployeeController::class);

    Route::apiResource('clients', ClientController::class);
    Route::apiResource('placements', PlacementController::class);
    Route::apiResource('contract-clients', ContractClientController::class);
    Route::apiResource('contract-employees', ContractEmployeeController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('pic-externals', PicExternalController::class);
});
