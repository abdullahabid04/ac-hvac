<?php

namespace App\Providers;

use App\Services\Contracts\DeviceMessagePublisher;
use App\Services\QueueDeviceMessagePublisher;
use App\Models\Notification as AcNotification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DeviceMessagePublisher::class, QueueDeviceMessagePublisher::class);
    }

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            $organization = $user?->currentOrganization();

            $view->with([
                'shellUser' => $user,
                'shellOrganization' => $organization,
                'shellUnreadCount' => $user
                    ? AcNotification::where('organization_id', $organization?->id)
                        ->where('user_id', $user->id)
                        ->whereNull('read_at')
                        ->count()
                    : 0,
                'shellSites' => $organization
                    ? $organization->sites()->orderBy('name')->get()
                    : collect(),
            ]);
        });
    }
}
