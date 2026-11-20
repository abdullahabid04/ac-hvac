<?php

use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ManagerController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('web')->group(function () {
        Route::post('/auth/login', [ApiAuthController::class, 'login'])
            ->middleware('throttle:10,1')
            ->name('api.auth.login');
        Route::post('/auth/logout', [ApiAuthController::class, 'logout'])
            ->middleware('auth')
            ->name('api.auth.logout');

        Route::middleware(['auth', 'organization'])->group(function () {
            Route::get('/me', [ManagerController::class, 'me'])->name('api.manager.me');
            Route::get('/overview', [ManagerController::class, 'overview'])->name('api.manager.overview');

            Route::get('/locations', [ManagerController::class, 'locations'])->name('api.manager.locations.index');

            Route::get('/acs', [ManagerController::class, 'acs'])->name('api.manager.acs.index');
            Route::get('/acs/{ac}', [ManagerController::class, 'ac'])->name('api.manager.acs.show');
            Route::post('/acs/{ac}/commands', [ManagerController::class, 'command'])
                ->middleware('throttle:30,1')
                ->name('api.manager.acs.commands.store');
            Route::get('/commands/{command}', [ManagerController::class, 'commandStatus'])->name('api.manager.commands.show');

            Route::get('/analytics', [ManagerController::class, 'analytics'])->name('api.manager.analytics');
            Route::get('/activity', [ManagerController::class, 'activity'])->name('api.manager.activity');

            Route::get('/notifications', [ManagerController::class, 'notifications'])->name('api.manager.notifications.index');
            Route::post('/notifications/read-all', [ManagerController::class, 'markAllNotificationsRead'])->name('api.manager.notifications.read-all');
            Route::post('/notifications/{notification}/read', [ManagerController::class, 'markNotificationRead'])->name('api.manager.notifications.read');

            Route::get('/reports', [ManagerController::class, 'reports'])->name('api.manager.reports.index');
            Route::post('/reports', [ManagerController::class, 'generateReport'])->name('api.manager.reports.store');
            Route::get('/reports/{report}/download', [ManagerController::class, 'downloadReport'])->name('api.manager.reports.download');

            Route::get('/settings', [ManagerController::class, 'settings'])->name('api.manager.settings');
            Route::put('/policies', [ManagerController::class, 'updatePolicy'])->name('api.manager.policies.update');
        });
    });

    Route::prefix('device')
        ->middleware(['device.auth', 'throttle:120,1'])
        ->group(function () {
            Route::post('/heartbeat', [DeviceController::class, 'heartbeat'])->name('api.device.heartbeat');
            Route::get('/config', [DeviceController::class, 'config'])->name('api.device.config');
            Route::get('/commands/pending', [DeviceController::class, 'pendingCommands'])->name('api.device.commands.pending');
            Route::post('/commands/{command}/ack', [DeviceController::class, 'acknowledge'])->name('api.device.commands.ack');
            Route::post('/commands/{command}/transmission-result', [DeviceController::class, 'transmissionResult'])->name('api.device.commands.transmission');
            Route::post('/state', [DeviceController::class, 'state'])->name('api.device.state');
            Route::post('/remote-events', [DeviceController::class, 'remoteEvent'])->name('api.device.remote-events');
            Route::post('/events', [DeviceController::class, 'event'])->name('api.device.events');
            Route::post('/policy/ack', [DeviceController::class, 'policyAck'])->name('api.device.policy-ack');
        });
});
