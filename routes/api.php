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
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Protected API Resources
Route::middleware('auth:api')->group(function () {
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('placements', PlacementController::class);
    Route::apiResource('contract-clients', ContractClientController::class);
    Route::apiResource('contract-employees', ContractEmployeeController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('pic-externals', PicExternalController::class);
});
