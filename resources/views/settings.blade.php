@extends('layouts.app')

@section('page-title', 'Organization Settings')
@section('page-subtitle', 'Manage organization information, alerts, policy and your account')

@section('content')
    @php
        $timezones = \Illuminate\Support\Collection::make(\DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC));
        $explicitPolicyValue = $organizationPolicy?->policy_value?->value ?? 'inherit';
    @endphp

    <div class="grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
        <div class="space-y-4">
            <section class="card overflow-hidden" id="organization-information">
                <div class="card-header">
                    <div class="flex items-start gap-2.5">
                        <span class="icon-tile bg-blue-50 text-blue-600"><x-icon name="building" :size="16" /></span>
                        <div>
                            <h2 class="card-title">Organization information</h2>
                            <p class="card-subtitle">Name, timezone and contact details</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('settings.organization.update') }}" class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 md:p-[18px]">
                    @csrf
                    @method('PUT')
                    <div class="sm:col-span-2">
                        <label class="label" for="org-name">Organization name</label>
                        <input id="org-name" name="name" value="{{ old('name', $organization->name) }}" class="input" required maxlength="160">
                    </div>
                    <div>
                        <label class="label" for="org-timezone">Timezone</label>
                        <select id="org-timezone" name="timezone" class="select" required>
                            @foreach ($timezones as $timezone)
                                <option value="{{ $timezone }}" @selected(old('timezone', $organization->timezone) === $timezone)>{{ $timezone }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[9.5px] text-muted">Used for day boundaries, reports and all displayed timestamps.</p>
                    </div>
                    <div>
                        <label class="label" for="org-email">Contact email</label>
                        <input id="org-email" type="email" name="contact_email" value="{{ old('contact_email', $organization->contact_email) }}" class="input" maxlength="160">
                    </div>
                    <div>
                        <label class="label" for="org-phone">Contact phone</label>
                        <input id="org-phone" name="contact_phone" value="{{ old('contact_phone', $organization->contact_phone) }}" class="input" maxlength="40">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="org-address">Address</label>
                        <input id="org-address" name="address" value="{{ old('address', $organization->address) }}" class="input" maxlength="255">
                    </div>
                    <div class="flex items-center justify-between gap-2 border-t border-divider pt-3 sm:col-span-2">
                        <p class="text-[9.5px] text-muted">Member since {{ \App\Support\Format::datetime($organization->created_at, $organization->timezone ?: 'UTC') }}</p>
                        <button type="submit" class="btn btn-primary">Save organization</button>
                    </div>
                </form>
            </section>

            <section class="card overflow-hidden" id="remote-policy">
                <div class="card-header">
                    <div class="flex items-start gap-2.5">
                        <span class="icon-tile bg-warning-soft text-[#b77405]"><x-icon name="shield" :size="16" /></span>
                        <div>
                            <h2 class="card-title">Physical remote policy</h2>
                            <p class="card-subtitle">Organization-wide baseline; nearest explicit scope wins</p>
                        </div>
                    </div>
                    <span class="badge {{ $effectiveOrganizationPolicy['value']->value === 'enabled' ? 'badge-online' : 'badge-off' }}">{{ $effectiveOrganizationPolicy['value']->label() }}</span>
                </div>
                <div class="space-y-3 p-4 md:p-[18px]">
                    <div class="grid gap-2.5 sm:grid-cols-2">
                        <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                            <p class="filter-label">Effective value</p>
                            <p class="mt-1 text-[17px] font-bold text-ink">{{ $effectiveOrganizationPolicy['value']->label() }}</p>
                            <p class="mt-0.5 text-[9.5px] text-muted">Source: {{ $effectiveOrganizationPolicy['source_label'] }} · v{{ $effectiveOrganizationPolicy['version'] }}</p>
                        </div>
                        <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                            <p class="filter-label">System default</p>
                            <p class="mt-1 text-[17px] font-bold text-ink">{{ $defaultPolicy->label() }}</p>
                            <p class="mt-0.5 text-[9.5px] text-muted">Provisional — subject to client approval</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('settings.policy.update') }}" class="flex flex-wrap items-end gap-2">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="scope_type" value="organization">
                        <input type="hidden" name="scope_id" value="{{ $organization->id }}">
                        <div class="min-w-[190px] flex-1">
                            <label class="label mb-1" for="org-policy">Organization baseline policy</label>
                            <select id="org-policy" name="policy_value" class="select h-[34px] py-0 text-[11.5px]">
                                <option value="inherit" @selected($explicitPolicyValue === 'inherit')>Inherit system default ({{ $defaultPolicy->label() }})</option>
                                <option value="enabled" @selected($explicitPolicyValue === 'enabled')>Enabled — synchronize remote events</option>
                                <option value="disabled" @selected($explicitPolicyValue === 'disabled')>Disabled — do not synchronize remote events</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary btn-sm h-[34px]">Apply policy</button>
                    </form>

                    <div class="rounded-lg border border-[#f6deac] bg-warning-soft/60 px-3 py-2.5 text-[10px] leading-relaxed text-[#8a5a05]">
                        <p class="font-semibold">Software cannot block a handheld remote’s direct infrared beam.</p>
                        <p class="mt-0.5">The policy controls synchronization, reporting and device behaviour only. A restricted remote action may still change the physical AC. Site, building, floor, space and AC overrides take precedence over this baseline.</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="space-y-4">
            <section class="card overflow-hidden" id="notification-preferences">
                <div class="card-header">
                    <div class="flex items-start gap-2.5">
                        <span class="icon-tile bg-blue-50 text-blue-600"><x-icon name="bell" :size="16" /></span>
                        <div>
                            <h2 class="card-title">Notification preferences</h2>
                            <p class="card-subtitle">Choose which events should create alerts for you</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('settings.notifications.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="table-scroll">
                        <table class="table">
                            <thead><tr><th>Event category</th><th class="w-16 text-center">In-app</th><th class="w-16 text-center">Email</th></tr></thead>
                            <tbody>
                                @foreach ($notificationCategories as $key => $label)
                                    <tr>
                                        <td>
                                            <span class="block font-medium text-ink">{{ $label }}</span>
                                            <span class="block font-mono text-[9px] text-muted">{{ $key }}</span>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="preferences[{{ $key }}][in_app]" value="1" class="h-4 w-4 rounded border-line text-blue-600 focus:ring-blue-300" @checked($preferences[$key]['in_app'] ?? true)>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="preferences[{{ $key }}][email]" value="1" class="h-4 w-4 rounded border-line text-blue-600 focus:ring-blue-300" @checked($preferences[$key]['email'] ?? false)>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center justify-between gap-2 border-t border-divider px-4 py-3">
                        <p class="text-[9.5px] text-muted">Email delivery is a preference only; this deployment may not have a mail transport configured.</p>
                        <button type="submit" class="btn btn-primary btn-sm">Save preferences</button>
                    </div>
                </form>
            </section>

            <section class="card overflow-hidden" id="account-profile">
                <div class="card-header">
                    <div class="flex items-start gap-2.5">
                        <span class="icon-tile bg-blue-50 text-blue-600"><x-icon name="user" :size="16" /></span>
                        <div>
                            <h2 class="card-title">Account & profile</h2>
                            <p class="card-subtitle">Your name, email and contact details</p>
                        </div>
                    </div>
                    <span class="badge badge-info">Administrator</span>
                </div>
                <form method="POST" action="{{ route('settings.profile.update') }}" class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 md:p-[18px]">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="label" for="profile-name">Full name</label>
                        <input id="profile-name" name="name" value="{{ old('name', $user->name) }}" class="input" required maxlength="120">
                    </div>
                    <div>
                        <label class="label" for="profile-email">Email</label>
                        <input id="profile-email" type="email" name="email" value="{{ old('email', $user->email) }}" class="input" required maxlength="160">
                    </div>
                    <div>
                        <label class="label" for="profile-title">Job title</label>
                        <input id="profile-title" name="job_title" value="{{ old('job_title', $user->job_title) }}" class="input" maxlength="120">
                    </div>
                    <div>
                        <label class="label" for="profile-phone">Phone</label>
                        <input id="profile-phone" name="phone" value="{{ old('phone', $user->phone) }}" class="input" maxlength="40">
                    </div>
                    <div class="flex justify-end border-t border-divider pt-3 sm:col-span-2">
                        <button type="submit" class="btn btn-primary">Save profile</button>
                    </div>
                </form>
            </section>

            <section class="card overflow-hidden" id="account-password">
                <div class="card-header">
                    <div class="flex items-start gap-2.5">
                        <span class="icon-tile bg-neutral-soft text-neutral"><x-icon name="shield" :size="16" /></span>
                        <div>
                            <h2 class="card-title">Change password</h2>
                            <p class="card-subtitle">Minimum 8 characters</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('settings.password.update') }}" class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-3 md:p-[18px]">
                    @csrf
                    @method('PUT')
                    <div class="sm:col-span-3">
                        <label class="label" for="current-password">Current password</label>
                        <input id="current-password" type="password" name="current_password" class="input" required autocomplete="current-password">
                    </div>
                    <div>
                        <label class="label" for="new-password">New password</label>
                        <input id="new-password" type="password" name="password" class="input" required autocomplete="new-password">
                    </div>
                    <div>
                        <label class="label" for="new-password-confirmation">Confirm new password</label>
                        <input id="new-password-confirmation" type="password" name="password_confirmation" class="input" required autocomplete="new-password">
                    </div>
                    <div class="flex items-end justify-end">
                        <button type="submit" class="btn btn-secondary btn-sm w-full">Update password</button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <p class="mt-4 text-[9.5px] leading-relaxed text-muted">
        This portal intentionally exposes no raw IR payloads, MQTT administration, firmware debugging or developer provisioning controls. Device credentials are managed outside the manager interface.
    </p>
@endsection
