<?php $__env->startSection('page-title', 'Organization Settings'); ?>
<?php $__env->startSection('page-subtitle', 'Manage organization information, alerts, policy and your account'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $timezones = \Illuminate\Support\Collection::make(\DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC));
        $explicitPolicyValue = $organizationPolicy?->policy_value?->value ?? 'inherit';
    ?>

    <div class="grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
        <div class="space-y-4">
            <section class="card overflow-hidden" id="organization-information">
                <div class="card-header">
                    <div class="flex items-start gap-2.5">
                        <span class="icon-tile bg-blue-50 text-blue-600"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'building','size' => 16]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building','size' => 16]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span>
                        <div>
                            <h2 class="card-title">Organization information</h2>
                            <p class="card-subtitle">Name, timezone and contact details</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="<?php echo e(route('settings.organization.update')); ?>" class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 md:p-[18px]">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="sm:col-span-2">
                        <label class="label" for="org-name">Organization name</label>
                        <input id="org-name" name="name" value="<?php echo e(old('name', $organization->name)); ?>" class="input" required maxlength="160">
                    </div>
                    <div>
                        <label class="label" for="org-timezone">Timezone</label>
                        <select id="org-timezone" name="timezone" class="select" required>
                            <?php $__currentLoopData = $timezones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timezone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($timezone); ?>" <?php if(old('timezone', $organization->timezone) === $timezone): echo 'selected'; endif; ?>><?php echo e($timezone); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <p class="mt-1 text-[9.5px] text-muted">Used for day boundaries, reports and all displayed timestamps.</p>
                    </div>
                    <div>
                        <label class="label" for="org-email">Contact email</label>
                        <input id="org-email" type="email" name="contact_email" value="<?php echo e(old('contact_email', $organization->contact_email)); ?>" class="input" maxlength="160">
                    </div>
                    <div>
                        <label class="label" for="org-phone">Contact phone</label>
                        <input id="org-phone" name="contact_phone" value="<?php echo e(old('contact_phone', $organization->contact_phone)); ?>" class="input" maxlength="40">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="org-address">Address</label>
                        <input id="org-address" name="address" value="<?php echo e(old('address', $organization->address)); ?>" class="input" maxlength="255">
                    </div>
                    <div class="flex items-center justify-between gap-2 border-t border-divider pt-3 sm:col-span-2">
                        <p class="text-[9.5px] text-muted">Member since <?php echo e(\App\Support\Format::datetime($organization->created_at, $organization->timezone ?: 'UTC')); ?></p>
                        <button type="submit" class="btn btn-primary">Save organization</button>
                    </div>
                </form>
            </section>

            <section class="card overflow-hidden" id="remote-policy">
                <div class="card-header">
                    <div class="flex items-start gap-2.5">
                        <span class="icon-tile bg-warning-soft text-[#b77405]"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'shield','size' => 16]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield','size' => 16]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span>
                        <div>
                            <h2 class="card-title">Physical remote policy</h2>
                            <p class="card-subtitle">Organization-wide baseline; nearest explicit scope wins</p>
                        </div>
                    </div>
                    <span class="badge <?php echo e($effectiveOrganizationPolicy['value']->value === 'enabled' ? 'badge-online' : 'badge-off'); ?>"><?php echo e($effectiveOrganizationPolicy['value']->label()); ?></span>
                </div>
                <div class="space-y-3 p-4 md:p-[18px]">
                    <div class="grid gap-2.5 sm:grid-cols-2">
                        <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                            <p class="filter-label">Effective value</p>
                            <p class="mt-1 text-[17px] font-bold text-ink"><?php echo e($effectiveOrganizationPolicy['value']->label()); ?></p>
                            <p class="mt-0.5 text-[9.5px] text-muted">Source: <?php echo e($effectiveOrganizationPolicy['source_label']); ?> · v<?php echo e($effectiveOrganizationPolicy['version']); ?></p>
                        </div>
                        <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                            <p class="filter-label">System default</p>
                            <p class="mt-1 text-[17px] font-bold text-ink"><?php echo e($defaultPolicy->label()); ?></p>
                            <p class="mt-0.5 text-[9.5px] text-muted">Provisional — subject to client approval</p>
                        </div>
                    </div>

                    <form method="POST" action="<?php echo e(route('settings.policy.update')); ?>" class="flex flex-wrap items-end gap-2">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <input type="hidden" name="scope_type" value="organization">
                        <input type="hidden" name="scope_id" value="<?php echo e($organization->id); ?>">
                        <div class="min-w-[190px] flex-1">
                            <label class="label mb-1" for="org-policy">Organization baseline policy</label>
                            <select id="org-policy" name="policy_value" class="select h-[34px] py-0 text-[11.5px]">
                                <option value="inherit" <?php if($explicitPolicyValue === 'inherit'): echo 'selected'; endif; ?>>Inherit system default (<?php echo e($defaultPolicy->label()); ?>)</option>
                                <option value="enabled" <?php if($explicitPolicyValue === 'enabled'): echo 'selected'; endif; ?>>Enabled — synchronize remote events</option>
                                <option value="disabled" <?php if($explicitPolicyValue === 'disabled'): echo 'selected'; endif; ?>>Disabled — do not synchronize remote events</option>
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
                        <span class="icon-tile bg-blue-50 text-blue-600"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'bell','size' => 16]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bell','size' => 16]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span>
                        <div>
                            <h2 class="card-title">Notification preferences</h2>
                            <p class="card-subtitle">Choose which events should create alerts for you</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="<?php echo e(route('settings.notifications.update')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="table-scroll">
                        <table class="table">
                            <thead><tr><th>Event category</th><th class="w-16 text-center">In-app</th><th class="w-16 text-center">Email</th></tr></thead>
                            <tbody>
                                <?php $__currentLoopData = $notificationCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <span class="block font-medium text-ink"><?php echo e($label); ?></span>
                                            <span class="block font-mono text-[9px] text-muted"><?php echo e($key); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="preferences[<?php echo e($key); ?>][in_app]" value="1" class="h-4 w-4 rounded border-line text-blue-600 focus:ring-blue-300" <?php if($preferences[$key]['in_app'] ?? true): echo 'checked'; endif; ?>>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="preferences[<?php echo e($key); ?>][email]" value="1" class="h-4 w-4 rounded border-line text-blue-600 focus:ring-blue-300" <?php if($preferences[$key]['email'] ?? false): echo 'checked'; endif; ?>>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        <span class="icon-tile bg-blue-50 text-blue-600"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'user','size' => 16]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user','size' => 16]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span>
                        <div>
                            <h2 class="card-title">Account & profile</h2>
                            <p class="card-subtitle">Your name, email and contact details</p>
                        </div>
                    </div>
                    <span class="badge badge-info">Administrator</span>
                </div>
                <form method="POST" action="<?php echo e(route('settings.profile.update')); ?>" class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 md:p-[18px]">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div>
                        <label class="label" for="profile-name">Full name</label>
                        <input id="profile-name" name="name" value="<?php echo e(old('name', $user->name)); ?>" class="input" required maxlength="120">
                    </div>
                    <div>
                        <label class="label" for="profile-email">Email</label>
                        <input id="profile-email" type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" class="input" required maxlength="160">
                    </div>
                    <div>
                        <label class="label" for="profile-title">Job title</label>
                        <input id="profile-title" name="job_title" value="<?php echo e(old('job_title', $user->job_title)); ?>" class="input" maxlength="120">
                    </div>
                    <div>
                        <label class="label" for="profile-phone">Phone</label>
                        <input id="profile-phone" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" class="input" maxlength="40">
                    </div>
                    <div class="flex justify-end border-t border-divider pt-3 sm:col-span-2">
                        <button type="submit" class="btn btn-primary">Save profile</button>
                    </div>
                </form>
            </section>

            <section class="card overflow-hidden" id="account-password">
                <div class="card-header">
                    <div class="flex items-start gap-2.5">
                        <span class="icon-tile bg-neutral-soft text-neutral"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'shield','size' => 16]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield','size' => 16]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span>
                        <div>
                            <h2 class="card-title">Change password</h2>
                            <p class="card-subtitle">Minimum 8 characters</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="<?php echo e(route('settings.password.update')); ?>" class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-3 md:p-[18px]">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/settings.blade.php ENDPATH**/ ?>