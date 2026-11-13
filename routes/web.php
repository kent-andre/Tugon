<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminConcernController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BlotterController;
use App\Http\Controllers\ConcernController;
use App\Http\Controllers\ConcernStatusController;
use App\Http\Controllers\OfficialAccountController;
use App\Http\Controllers\ResidentAccountController;
use App\Http\Controllers\ResidentAuthController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureResident;

Route::view('/', 'landing')->name('landing');
Route::post('/concerns', [ConcernController::class, 'store'])->middleware('throttle:5,1')->name('concerns.store');
Route::get('/concerns/status/{code}', [ConcernStatusController::class, 'show'])
    ->where('code', '[A-Za-z0-9-]{1,30}')
    ->middleware('throttle:60,1');

Route::view('/login', 'auth.login', ['role' => 'resident'])->name('login');
Route::view('/admin-login', 'auth.login', ['role' => 'admin']);

// Resident login and registration (residents table)
Route::post('/login', [ResidentAuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('login.resident');
Route::post('/register', [ResidentAuthController::class, 'register'])->middleware('throttle:5,1');

// Official login (admins table)
Route::post('/admin-login', [AdminAuthController::class, 'login'])
    ->middleware('throttle:5,1') // 5 tries per minute
    ->name('login.admin');
Route::get('/admin/logout', [AdminAuthController::class, 'logout']);

Route::get('/register', fn () => view('auth.register'));
Route::get('/forgot-password', fn () => view('auth.forgot-password'));
Route::get('/placeholder', fn () => view('auth.placeholder'));
Route::get('/emergency-hotlines', fn () => view('resident.emergency-hotlines'));
Route::get('/transparency', fn () => view('resident.transparency'));
Route::get('/officials', fn () => view('resident.officials'));
Route::get('/faq', fn () => view('resident.faq'));
Route::get('/services', fn () => view('resident.services'));
Route::view('/check-status', 'check-status');

// Resident pages, need a signed-in resident
Route::middleware(EnsureResident::class)->group(function () {
    Route::get('/home', fn () => view('auth.home'));
    Route::get('/announcements', fn () => view('resident.announcements'));
    Route::get('/request-document', fn () => view('resident.request-document'));
    Route::get('/blotter-report', fn () => view('resident.blotter-report'));
    Route::get('/my-reports', fn () => view('resident.my-reports'));
    Route::get('/profile', fn () => view('resident.profile'));
    Route::get('/settings', fn () => view('resident.settings'));
    Route::get('/submit-concern', fn () => view('resident.submit-concern'));
});

Route::get('/logout', function () {
    if (session('admin_logged_in')) { \App\Support\AuditLog::record('Logged out', 'Accounts'); }
    session()->flush();
    return redirect('/');
});

// Admin pages, need an official login. Roles: captain, secretary, staff.
Route::middleware(EnsureAdmin::class)->prefix('admin')->group(function () {
    // Every official
    Route::get('/dashboard', [AdminConcernController::class, 'dashboard']);
    Route::get('/concerns', [AdminConcernController::class, 'index']);
    Route::post('/concerns/{code}', [AdminConcernController::class, 'update'])
        ->where('code', 'PB-[0-9]{4}-[A-Z0-9]{4,6}');
    Route::get('/map', [AdminConcernController::class, 'map']);
    Route::get('/blotters', [BlotterController::class, 'index']);
    Route::get('/password', [OfficialAccountController::class, 'passwordForm']);
    Route::post('/password', [OfficialAccountController::class, 'changePassword']);

    // Punong Barangay and Secretary
    Route::middleware('role:captain,secretary')->group(function () {
        Route::post('/concerns/{code}/blotter', [BlotterController::class, 'storeFromConcern'])
            ->where('code', 'PB-[0-9]{4}-[A-Z0-9]{4,6}');
        Route::post('/blotters/{blotter}', [BlotterController::class, 'update'])->whereNumber('blotter');
        Route::get('/blotters/{blotter}/summons', [BlotterController::class, 'printSummons'])->whereNumber('blotter');

        Route::get('/accounts', [ResidentAccountController::class, 'index']);
        Route::post('/accounts/{resident}/approve', [ResidentAccountController::class, 'approve'])->whereNumber('resident');
        Route::post('/accounts/{resident}/reject', [ResidentAccountController::class, 'reject'])->whereNumber('resident');
        Route::get('/accounts/{resident}/id', [ResidentAccountController::class, 'idPhoto'])->whereNumber('resident');

        Route::view('/announcements', 'admin.announcements');
        Route::view('/documents', 'admin.document', ['docTypes' => [], 'requests' => []]);
        Route::view('/officials', 'admin.officials');
        Route::get('/audit-log', [AuditLogController::class, 'index']);
    });

    // Punong Barangay only
    Route::middleware('role:captain')->group(function () {
        Route::get('/official-accounts', [OfficialAccountController::class, 'index']);
        Route::post('/official-accounts', [OfficialAccountController::class, 'store']);
        Route::post('/official-accounts/{admin}', [OfficialAccountController::class, 'update'])->whereNumber('admin');
    });
});

require __DIR__.'/step6.php';
