<?php

use App\Http\Controllers\Admin\EmergencyTeamController;
use Illuminate\Support\Facades\Route;

// Emergency team status (loaded from routes/step6.php)
Route::post('/admin/emergency-team', [EmergencyTeamController::class, 'update'])->name('admin.emergency-team');
Route::get('/emergency-team/status', [EmergencyTeamController::class, 'status'])->name('emergency-team.status');
