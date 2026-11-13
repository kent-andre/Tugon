<?php
/*
 | STEP 6 ROUTES - loaded from routes/web.php by:  require __DIR__.'/step6.php';
 | (added automatically by apply-step6.ps1). Because this file is loaded LAST,
 | these routes replace any older placeholder routes with the same URL.
 | Uses the same middleware as routes/web.php: EnsureAdmin, EnsureResident
 | and the 'role' alias (bootstrap/app.php).
 */

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\CalendarController as AdminCalendar;
use App\Http\Controllers\Admin\DocumentRequestController as AdminDocuments;
use App\Http\Controllers\Admin\EvidencePhotoController;
use App\Http\Controllers\Admin\OfficialController;
use App\Http\Controllers\Admin\PhotoReviewController;
use App\Http\Controllers\AdminConcernController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Resident\AccountController;
use App\Http\Controllers\Resident\CalendarController as ResidentCalendar;
use App\Http\Controllers\Resident\DocumentRequestController as ResidentDocuments;
use App\Http\Controllers\Resident\PortalController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureResident;
use Illuminate\Support\Facades\Route;

// ===================== RESIDENT PASSWORD RESET (guests) =====================
// Replaces the old view-only /forgot-password route in web.php (this file loads last).
Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'send'])->middleware('throttle:3,1')->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->where('token', '[A-Za-z0-9]{64}')->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:10,1')->name('password.update');

// ===================== ADMIN =====================
Route::middleware(EnsureAdmin::class)->prefix('admin')->name('admin.')->group(function () {

    // Calendar: every role can view; captain and secretary add, edit and delete.
    Route::get('/calendar', [AdminCalendar::class, 'index'])->name('calendar');
    Route::get('/calendar/feed', [AdminCalendar::class, 'feed'])->name('calendar.feed');
    Route::middleware('role:captain,secretary')->group(function () {
        Route::post('/calendar/events', [AdminCalendar::class, 'store'])->name('calendar.store');
        Route::put('/calendar/events/{event}', [AdminCalendar::class, 'update'])->whereNumber('event')->name('calendar.update');
        Route::delete('/calendar/events/{event}', [AdminCalendar::class, 'destroy'])->whereNumber('event')->name('calendar.destroy');
    });

    // 3D Concern Map (MapLibre GL JS): every official, same data as /admin/map
    Route::get('/map/3d', [AdminConcernController::class, 'map3d'])->name('map.3d');

    // Concern photos: only logged-in officials can open them
    Route::get('/concerns/{code}/photo', [EvidencePhotoController::class, 'show'])
        ->where('code', 'PB-[0-9]{4}-[A-Z0-9]{4,6}')
        ->name('concerns.photo');

    // Photo check (Step 8): every official who handles concerns can mark a
    // flagged photo as OK or keep the flag.
    Route::post('/concerns/{code}/photo-review', [PhotoReviewController::class, 'update'])
        ->where('code', 'PB-[0-9]{4}-[A-Z0-9]{4,6}')
        ->name('concerns.photo-review');

    // Document requests: captain and secretary. Staff only handle concerns
    // and view blotters (Step 5 roles).
    Route::middleware('role:captain,secretary')->group(function () {
        Route::get('/documents', [AdminDocuments::class, 'index'])->name('documents');
        Route::post('/documents/{document}', [AdminDocuments::class, 'update'])->whereNumber('document')->name('documents.update');
        Route::get('/documents/{document}/files/{index}', [AdminDocuments::class, 'file'])
            ->whereNumber(['document', 'index'])->name('documents.file');
    });

    // Public-facing content: captain and secretary only.
    Route::middleware('role:captain,secretary')->group(function () {
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::post('/announcements/popup', [AnnouncementController::class, 'savePopup'])->name('announcements.popup');
        Route::post('/announcements/popup/toggle', [AnnouncementController::class, 'togglePopup'])->name('announcements.popup.toggle');
        Route::post('/announcements/{announcement}/popup', [AnnouncementController::class, 'useAsPopup'])->whereNumber('announcement')->name('announcements.use-as-popup');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->whereNumber('announcement')->name('announcements.update');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->whereNumber('announcement')->name('announcements.destroy');

        Route::get('/officials', [OfficialController::class, 'index'])->name('officials');
        Route::post('/officials', [OfficialController::class, 'store'])->name('officials.store');
        Route::put('/officials/{official}', [OfficialController::class, 'update'])->whereNumber('official')->name('officials.update');
        Route::delete('/officials/{official}', [OfficialController::class, 'destroy'])->whereNumber('official')->name('officials.destroy');
    });
});

// ===================== RESIDENT (logged in + approved) =====================
Route::middleware(EnsureResident::class)->group(function () {
    Route::get('/home', [PortalController::class, 'home'])->name('resident.home');
    Route::get('/announcements', [PortalController::class, 'announcements'])->name('resident.announcements');
    Route::get('/officials', [PortalController::class, 'officials'])->name('resident.officials');
    Route::get('/my-reports', [PortalController::class, 'myReports'])->name('resident.my-reports');
    Route::get('/transparency', [PortalController::class, 'transparency'])->name('resident.transparency');

    // Settings > Change Password
    Route::post('/settings/password', [AccountController::class, 'updatePassword'])
        ->middleware('throttle:5,1')->name('resident.password');

    Route::get('/calendar', [ResidentCalendar::class, 'index'])->name('resident.calendar');
    Route::get('/calendar/feed', [ResidentCalendar::class, 'feed'])->name('resident.calendar.feed');

    Route::get('/request-document', [ResidentDocuments::class, 'create'])->name('resident.request-document');
    Route::post('/request-document/{type}', [ResidentDocuments::class, 'store'])
        ->whereIn('type', ['clearance', 'residency', 'indigency', 'business', 'vaccination', 'id'])
        ->middleware('throttle:10,1')
        ->name('resident.request-document.store');
});

require_once __DIR__.'/emergency.php';
