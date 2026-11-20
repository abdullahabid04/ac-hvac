<?php

use App\Http\Controllers\AcController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SimulatorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('overview')
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'organization'])->group(function () {
    Route::get('/overview', [DashboardController::class, 'index'])->name('overview');

    Route::get('/locations', [LocationsController::class, 'index'])->name('locations.index');
    Route::post('/locations', [LocationsController::class, 'store'])->name('locations.store');
    Route::put('/locations/{locationType}/{id}', [LocationsController::class, 'update'])
        ->whereIn('locationType', ['site', 'building', 'floor', 'space'])
        ->name('locations.update');
    Route::delete('/locations/{locationType}/{id}', [LocationsController::class, 'destroy'])
        ->whereIn('locationType', ['site', 'building', 'floor', 'space'])
        ->name('locations.destroy');

    Route::get('/air-conditioners', [AcController::class, 'index'])->name('acs.index');
    Route::post('/air-conditioners', [AcController::class, 'store'])->name('acs.store');
    Route::get('/air-conditioners/{ac}', [AcController::class, 'show'])->name('acs.show');
    Route::put('/air-conditioners/{ac}', [AcController::class, 'update'])->name('acs.update');
    Route::post('/air-conditioners/{ac}/device', [AcController::class, 'assignDevice'])->name('acs.device.assign');
    Route::post('/air-conditioners/{ac}/remote-policy', [AcController::class, 'updateRemotePolicy'])->name('acs.policy.update');
    Route::get('/air-conditioners/{ac}/control', [AcController::class, 'control'])->name('acs.control');
    Route::post('/air-conditioners/{ac}/commands', [AcController::class, 'command'])->name('acs.commands.store');
    Route::get('/commands/{command}', [AcController::class, 'commandStatus'])->name('commands.show');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/organization', [SettingsController::class, 'updateOrganization'])->name('settings.organization.update');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotificationPreferences'])->name('settings.notifications.update');
    Route::put('/settings/policy', [SettingsController::class, 'updatePolicy'])->name('settings.policy.update');

    if (app()->environment('local')) {
        Route::get('/dev/simulator', [SimulatorController::class, 'index'])->name('dev.simulator');
        Route::post('/dev/simulator/action', [SimulatorController::class, 'action'])->name('dev.simulator.action');
    }
});
