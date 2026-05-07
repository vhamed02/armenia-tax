<?php

use App\Http\Controllers\Web\CasinoAuthController;
use App\Http\Controllers\Web\CasinoKycController;
use App\Http\Controllers\Web\LoginController;
use App\Livewire\Admin\AdminAnomalies;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\AdminReports;
use App\Livewire\Admin\AdminTenants;
use App\Livewire\Admin\AdminTransactionLog;
use App\Livewire\Admin\AdminUserDetail;
use App\Livewire\Admin\AdminUsers;
use App\Livewire\Casino\CasinoAccount;
use App\Livewire\Casino\CasinoHome;
use App\Livewire\Casino\MockImidVerify;
use App\Livewire\Portal\UserDashboard;
use App\Livewire\Portal\UserNotifications;
use App\Livewire\Portal\UserTaxReports;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/reports', AdminReports::class)->name('reports');
    Route::get('/anomalies', AdminAnomalies::class)->name('anomalies');
    Route::get('/users', AdminUsers::class)->name('users');
    Route::get('/users/{id}', AdminUserDetail::class)->name('users.detail');
    Route::get('/tenants', AdminTenants::class)->name('tenants');
    Route::get('/transaction-log', AdminTransactionLog::class)->name('transaction-log');
});

Route::middleware('auth')->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    Route::get('/tax-reports', UserTaxReports::class)->name('tax-reports');
    Route::get('/notifications', UserNotifications::class)->name('notifications');
});

Route::prefix('casino')->name('casino.')->group(function () {
    Route::get('/login', [CasinoAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CasinoAuthController::class, 'login'])->name('login.post');
    Route::get('/register', [CasinoAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CasinoAuthController::class, 'register'])->name('register.post');
    Route::post('/logout', [CasinoAuthController::class, 'logout'])->name('logout');

    Route::get('/', CasinoHome::class)->name('home');

    Route::middleware('auth')->group(function () {
        Route::get('/account', CasinoAccount::class)->name('account');
        Route::post('/kyc/start', [CasinoKycController::class, 'start'])->name('kyc.start');
        Route::get('/kyc/callback', [CasinoKycController::class, 'callback'])->name('kyc.callback');
        Route::post('/kyc/callback', [CasinoKycController::class, 'callback']);
    });
});

Route::get('/mock-imid/verify', MockImidVerify::class)->name('mock-imid.verify');
