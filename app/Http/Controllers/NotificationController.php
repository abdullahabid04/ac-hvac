<?php

namespace App\Http\Controllers;

use App\Enums\NotificationSeverity;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $user = $request->user();
        $tz = $organization->timezone ?: 'UTC';

        $tab = (string) $request->query('tab', 'all');
        if (! in_array($tab, ['all', 'unread', 'alert', 'warning', 'info'], true)) {
            $tab = 'all';
        }

        $query = Notification::where('organization_id', $organization->id)
            ->where('user_id', $user->id)
            ->with(['acUnit.space.floor.building.site', 'device'])
            ->orderByDesc('occurred_at');

        if ($tab === 'unread') {
            $query->whereNull('read_at');
        } elseif (in_array($tab, ['alert', 'warning', 'info'], true)) {
            $query->where('severity', $tab);
        }

        $notifications = $query->paginate(20)->withQueryString();

        $countsQuery = Notification::where('organization_id', $organization->id)->where('user_id', $user->id);

        return view('notifications', [
            'organization' => $organization,
            'timezone' => $tz,
            'notifications' => $notifications,
            'tab' => $tab,
            'counts' => [
                'all' => (clone $countsQuery)->count(),
                'unread' => (clone $countsQuery)->whereNull('read_at')->count(),
                'alert' => (clone $countsQuery)->where('severity', NotificationSeverity::Alert->value)->count(),
                'warning' => (clone $countsQuery)->where('severity', NotificationSeverity::Warning->value)->count(),
                'info' => (clone $countsQuery)->where('severity', NotificationSeverity::Info->value)->count(),
            ],
        ]);
    }

    public function markRead(Request $request, Notification $notification)
    {
        $organization = $request->attributes->get('organization');

        if ($notification->organization_id !== $organization->id || $notification->user_id !== $request->user()->id) {
            abort(404);
        }

        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        if ($request->boolean('redirect') && $notification->action_url) {
            return redirect($notification->action_url);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(Request $request)
    {
        $organization = $request->attributes->get('organization');

        $updated = Notification::where('organization_id', $organization->id)
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', $updated.' notification'.($updated === 1 ? '' : 's').' marked as read.');
    }
}
