<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('me')->group(function () {
            Route::get('/', [UserController::class, 'me']);
            Route::get('transactions', [UserController::class, 'transactions']);
            Route::get('income-summary', [UserController::class, 'incomeSummary']);
            Route::get('tax-reports', [UserController::class, 'taxReports']);
            Route::get('notifications', [UserController::class, 'notifications']);
            Route::patch('notifications/{id}/read', [UserController::class, 'markNotificationRead']);
        });

        Route::middleware(\App\Http\Middleware\AdminMiddleware::class)
            ->prefix('admin')
            ->group(function () {
                Route::get('dashboard-stats', [AdminController::class, 'dashboardStats']);
                Route::get('users', [AdminController::class, 'users']);
                Route::get('users/{id}/report', [AdminController::class, 'userReport']);
                Route::get('tax-reports', [AdminController::class, 'taxReports']);
                Route::post('tax-reports/{id}/submit', [AdminController::class, 'submitTaxReport']);
                Route::get('anomalies', [AdminController::class, 'anomalies']);
                Route::post('scan/trigger', [AdminController::class, 'triggerScan']);
            });
    });
});
