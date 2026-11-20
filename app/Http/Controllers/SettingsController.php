<?php

namespace App\Http\Controllers;

use App\Enums\PolicyScope;
use App\Enums\PolicyValue;
use App\Http\Requests\UpdateNotificationPreferencesRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateRemotePolicyRequest;
use App\Models\NotificationPreference;
use App\Models\RemotePolicy;
use App\Services\ActivityService;
use App\Services\AuditService;
use App\Services\RemotePolicyResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function __construct(
        private RemotePolicyResolver $policies,
        private AuditService $audit,
        private ActivityService $activity,
    ) {}

    public function index(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $user = $request->user();

        $preferences = [];
        foreach (array_keys(config('hvac.notification_categories', [])) as $key) {
            $preference = NotificationPreference::where('user_id', $user->id)->where('key', $key)->first();
            $preferences[$key] = [
                'in_app' => $preference?->in_app ?? true,
                'email' => $preference?->email ?? false,
            ];
        }

        $organizationPolicy = RemotePolicy::where('organization_id', $organization->id)
            ->where('scope_type', PolicyScope::Organization->value)
            ->where('scope_id', $organization->id)
            ->first();

        return view('settings', [
            'organization' => $organization,
            'user' => $user,
            'preferences' => $preferences,
            'notificationCategories' => config('hvac.notification_categories'),
            'organizationPolicy' => $organizationPolicy,
            'effectiveOrganizationPolicy' => $this->policies->effectiveForScope($organization, PolicyScope::Organization, $organization->id),
            'defaultPolicy' => PolicyValue::from(config('hvac.default_remote_policy', 'disabled')),
            'policyScopes' => [
                'organization' => 'Organization',
            ],
        ]);
    }

    public function updateOrganization(UpdateOrganizationRequest $request)
    {
        $organization = $request->attributes->get('organization');
        $before = $organization->only(['name', 'timezone', 'contact_email', 'contact_phone', 'address']);

        $organization->update($request->validated());

        $this->audit->record($organization, $request->user(), 'organization.updated', 'organization', $organization->id, $before, $organization->fresh()->only(array_keys($before)), $request);
        $this->activity->record($organization, null, 'system', 'settings_updated', 'Organization information updated.', 'success', $request->user());

        return back()->with('success', 'Organization information saved.');
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user->update($request->validated());

        $this->activity->record($request->attributes->get('organization'), null, 'system', 'profile_updated', 'Your account profile was updated.', 'success', $user);

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $request->user()->update(['password' => Hash::make($request->input('password'))]);

        $this->activity->record($request->attributes->get('organization'), null, 'system', 'password_updated', 'Your password was changed.', 'success', $request->user());

        return back()->with('success', 'Password changed.');
    }

    public function updateNotificationPreferences(UpdateNotificationPreferencesRequest $request)
    {
        $user = $request->user();

        foreach (array_keys(config('hvac.notification_categories', [])) as $key) {
            $values = $request->input("preferences.{$key}", []);
            NotificationPreference::updateOrCreate(
                ['user_id' => $user->id, 'key' => $key],
                ['in_app' => (bool) ($values['in_app'] ?? false), 'email' => (bool) ($values['email'] ?? false)],
            );
        }

        return back()->with('success', 'Notification preferences saved.');
    }

    public function updatePolicy(UpdateRemotePolicyRequest $request)
    {
        $organization = $request->attributes->get('organization');
        $scope = PolicyScope::from($request->input('scope_type', PolicyScope::Organization->value));
        $scopeId = $request->integer('scope_id') ?: $organization->id;

        $this->policies->setPolicy($organization, $scope, $scopeId, PolicyValue::from($request->input('policy_value')), $request->user());

        return back()->with('success', 'Physical remote policy updated.');
    }
}
