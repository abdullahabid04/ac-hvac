<?php $__env->startSection('page-title', 'AC Control Panel'); ?>
<?php $__env->startSection('page-subtitle', 'Control the selected air conditioner and track command delivery'); ?>

<?php
    $snapshot = $ac->stateSnapshot;
    $device = $ac->currentAssignment?->device;
    $controlData = array_merge($controlConfig, [
        'observedAt' => $snapshot?->observed_at?->toIso8601String(),
        'quality' => $snapshot?->quality?->value ?? 'unknown',
        'powerState' => $snapshot?->power_state?->value ?? 'unknown',
    ]);
    $min = $temperatureMin ?? 16;
    $max = $temperatureMax ?? 30;
    $step = (float) ($temperatureStep ?: 1);
?>

<?php $__env->startPush('head'); ?>
    <script>
        window.__AC_CONTROL__ = <?php echo json_encode($controlData, 15, 512) ?>;
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div x-data="acControl">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <a href="<?php echo e(route('acs.show', $ac)); ?>" class="btn btn-ghost btn-sm"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-left','size' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-left','size' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Back to AC details</a>
            <div class="flex items-center gap-2">
                <?php if (isset($component)) { $__componentOriginalb254a181568b9eef85fcdb4bf6b8eccf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb254a181568b9eef85fcdb4bf6b8eccf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.connectivity-badge','data' => ['status' => $ac->connectivity()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('connectivity-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ac->connectivity())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb254a181568b9eef85fcdb4bf6b8eccf)): ?>
<?php $attributes = $__attributesOriginalb254a181568b9eef85fcdb4bf6b8eccf; ?>
<?php unset($__attributesOriginalb254a181568b9eef85fcdb4bf6b8eccf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb254a181568b9eef85fcdb4bf6b8eccf)): ?>
<?php $component = $__componentOriginalb254a181568b9eef85fcdb4bf6b8eccf; ?>
<?php unset($__componentOriginalb254a181568b9eef85fcdb4bf6b8eccf); ?>
<?php endif; ?>
                <span class="text-[10.5px] text-muted">Last update <?php echo e(\App\Support\Format::datetime($snapshot?->observed_at, $timezone)); ?></span>
            </div>
        </div>

        <?php if(! $canControl): ?>
            <div class="alert alert-warning mb-4">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert','size' => 17,'class' => 'mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','size' => 17,'class' => 'mt-0.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                <div>
                    <p class="font-semibold">Remote control is unavailable for this AC.</p>
                    <p class="mt-0.5">
                        <?php if(! $ac->supportsProfileControl()): ?>
                            A validated IR profile version is required before commands can be issued.
                        <?php elseif(! $device): ?>
                            No ESP32 device is assigned. Assign a device from the AC details page.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php elseif($ac->connectivity() !== \App\Enums\ConnectivityStatus::Online): ?>
            <div class="alert alert-warning mb-4">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'wifi-off','size' => 17,'class' => 'mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'wifi-off','size' => 17,'class' => 'mt-0.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                <div>
                    <p class="font-semibold">The assigned device is <?php echo e($ac->connectivity()->value); ?>.</p>
                    <p class="mt-0.5">Commands will be queued until their expiry window. They will not execute after expiry. Device connectivity does not prove the AC state.</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="card overflow-hidden">
            <div class="flex flex-wrap items-center gap-3 border-b border-divider px-4 py-3 md:px-5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'ac','size' => 19]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'ac','size' => 19]); ?>
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
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-[15px] font-bold text-ink"><?php echo e($ac->asset_code); ?></h2>
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['state' => $snapshot?->power_state,'stale' => $snapshot?->isStale() ?? true,'quality' => $snapshot?->quality?->value]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($snapshot?->power_state),'stale' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($snapshot?->isStale() ?? true),'quality' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($snapshot?->quality?->value)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                    </div>
                    <p class="truncate text-[10.5px] text-muted">
                        <?php echo e(collect([$ac->space?->floor?->building?->site?->name, $ac->space?->floor?->building?->name, $ac->space?->floor?->name, $ac->space?->name])->filter()->implode(' › ') ?: 'Unassigned location'); ?>

                    </p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] uppercase tracking-[0.05em] text-muted">Mode / fan</p>
                    <p class="text-[11.5px] font-semibold text-ink"><?php echo e($snapshot?->operating_mode ? ucfirst($snapshot->operating_mode) : 'Not reported'); ?> · <?php echo e($snapshot?->fan_setting ? ucfirst($snapshot->fan_setting) : 'Not reported'); ?></p>
                </div>
            </div>

            <div class="grid items-center gap-6 px-5 py-7 lg:grid-cols-[1fr_auto_1.15fr] lg:px-8">
                
                <div class="order-3 flex flex-col items-center justify-center lg:order-1">
                    <div class="relative w-full max-w-[320px]">
                        <div class="mx-auto flex h-[118px] w-[280px] items-center gap-4 rounded-[22px] border border-[#d5e6f4] bg-gradient-to-b from-white to-[#eef7fe] px-6 shadow-[0_18px_40px_-28px_rgba(8,43,76,0.5)]">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full border border-[#cfe6f8] bg-white text-blue-500 shadow-inner">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'snowflake','size' => 28,'stroke' => 1.5]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'snowflake','size' => 28,'stroke' => 1.5]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <div class="h-[5px] rounded-full bg-[#cfe2f1]"></div>
                                <div class="mt-2.5 h-[5px] rounded-full bg-[#d9e9f6]"></div>
                                <div class="mt-2.5 h-[5px] rounded-full bg-[#e3eff8]"></div>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="h-1.5 w-1.5 rounded-full transition-colors" :class="isOn ? 'bg-success shadow-[0_0_8px_rgba(32,185,107,0.9)]' : 'bg-[#b9cad9]'"></span>
                                    <span class="text-[8.5px] font-semibold uppercase tracking-[0.14em] text-[#89a3b8]"><?php echo e($ac->acModel?->manufacturer ?? 'AC'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="mx-auto mt-2 h-3 w-[250px] rounded-b-[14px] bg-gradient-to-b from-[#e6f2fb] to-[#d8e9f6]"></div>
                    </div>
                    <p class="mt-3 text-[13px] font-bold tracking-[-0.01em] text-ink"><?php echo e($ac->asset_code); ?></p>
                    <p class="text-[10.5px] text-muted"><?php echo e($ac->name); ?></p>
                </div>

                
                <div class="order-1 flex flex-col items-center lg:order-2">
                    <button type="button"
                            class="rocker <?php echo e($canControl && $ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? '' : 'opacity-70'); ?>"
                            :data-on="isOn ? 'true' : 'false'"
                            :data-pending="pending ? 'true' : 'false'"
                            :disabled="! config.canControl || pending"
                            aria-label="AC power rocker. ON is at the top, OFF is at the bottom."
                            @click="setPower(isOn ? 'off' : 'on')">
                        <span class="rocker-thumb" :class="isOn ? 'rocker-thumb-on' : 'rocker-thumb-off'"></span>
                        <span class="rocker-position rocker-on">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'power','size' => 17,'stroke' => 2]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'power','size' => 17,'stroke' => 2]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                            ON
                        </span>
                        <span class="rocker-position rocker-off">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'power','size' => 17,'stroke' => 1.6]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'power','size' => 17,'stroke' => 1.6]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                            OFF
                        </span>
                    </button>
                    <p class="mt-2 text-[9.5px] font-medium uppercase tracking-[0.08em] text-muted">Power</p>
                    <p class="mt-0.5 text-[10px] text-ink-soft">
                        <span x-show="pending" x-cloak class="font-semibold text-blue-600"><?php echo e('Sending…'); ?></span>
                        <span x-show="! pending" x-cloak x-text="'Reported: ' + displayPower"></span>
                    </p>
                </div>

                
                <div class="order-2 flex flex-col items-center lg:order-3">
                    <div class="flex items-center gap-4 sm:gap-5">
                        <button type="button"
                                class="btn btn-secondary btn-icon shrink-0"
                                :disabled="! canDecrease || pending"
                                :class="(! canDecrease || pending) ? 'opacity-40' : ''"
                                @click="changeSetpoint(-1)"
                                aria-label="Decrease setpoint">
                            <span class="text-[20px] font-light leading-none">−</span>
                        </button>

                        <div class="temp-dial" :style="'--dial: ' + dialPercent + '%'">
                            <div class="temp-dial-inner">
                                <p class="text-[9px] font-semibold uppercase tracking-[0.12em] text-muted">Set temperature</p>
                                <p class="mt-1 text-[38px] font-bold leading-none tracking-[-0.05em] text-ink">
                                    <span x-text="(requestedSetpoint !== null && requestedSetpoint !== undefined) ? requestedSetpoint : '—'"></span><span class="text-[20px] align-top">°C</span>
                                </p>
                                <p class="mt-1 text-[9.5px] text-muted" x-show="! pending" x-cloak>
                                    Range <?php echo e($min); ?>–<?php echo e($max); ?>°C · step <?php echo e($step); ?>°C
                                </p>
                                <p class="mt-1 flex items-center gap-1 text-[9.5px] font-semibold text-blue-600" x-show="pending" x-cloak>
                                    <span class="h-2 w-2 animate-pulse rounded-full bg-blue-500"></span> Requested value
                                </p>
                            </div>
                        </div>

                        <button type="button"
                                class="btn btn-secondary btn-icon shrink-0"
                                :disabled="! canIncrease || pending"
                                :class="(! canIncrease || pending) ? 'opacity-40' : ''"
                                @click="changeSetpoint(1)"
                                aria-label="Increase setpoint">
                            <span class="text-[20px] font-light leading-none">+</span>
                        </button>
                    </div>

                    <div class="mt-3 grid w-full max-w-[330px] grid-cols-2 gap-2 text-center">
                        <div class="rounded-lg border border-line bg-[#fbfdff] px-2 py-1.5">
                            <p class="text-[8.5px] uppercase tracking-[0.06em] text-muted">Last reported</p>
                            <p class="text-[12px] font-semibold text-ink" x-text="reportedSetpoint !== null && reportedSetpoint !== undefined ? reportedSetpoint + '°C' : 'Unknown'"></p>
                        </div>
                        <div class="rounded-lg border border-line bg-[#fbfdff] px-2 py-1.5">
                            <p class="text-[8.5px] uppercase tracking-[0.06em] text-muted">Requested</p>
                            <p class="text-[12px] font-semibold" :class="requestedSetpoint !== reportedSetpoint ? 'text-blue-600' : 'text-ink'" x-text="requestedSetpoint !== null && requestedSetpoint !== undefined ? requestedSetpoint + '°C' : '—'"></p>
                        </div>
                    </div>

                    <p class="mt-2 text-center text-[9.5px] leading-relaxed text-muted">Setpoint is the AC target temperature, not measured room temperature.</p>
                </div>
            </div>

            
            <div class="border-t border-divider bg-[#fbfdff] px-4 py-3 md:px-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex min-w-0 items-start gap-2.5">
                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full"
                              :class="error ? 'bg-danger-soft text-danger' : (pending ? 'bg-blue-50 text-blue-600' : 'bg-success-soft text-[#0d8c4e]')">
                            <span x-show="pending" x-cloak class="h-3 w-3 animate-spin rounded-full border-2 border-blue-500 border-t-transparent"></span>
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check','size' => 12,'xShow' => '! pending && ! error','xCloak' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','size' => 12,'x-show' => '! pending && ! error','x-cloak' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert','size' => 12,'xShow' => 'error','xCloak' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','size' => 12,'x-show' => 'error','x-cloak' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11.5px] font-semibold text-ink" x-text="phaseLabel || 'Ready to send a command'"></p>
                            <p class="text-[10px] text-muted" x-show="message" x-cloak x-text="message"></p>
                            <p class="text-[10px] font-medium text-danger" x-show="error" x-cloak x-text="error"></p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5 text-[9.5px] text-muted">
                        <span class="badge bg-white text-ink-soft" x-show="command" x-cloak x-text="'Command ' + (command?.id ? command.id.substring(0, 8) : '')"></span>
                        <span x-show="phase === 'completed'" x-cloak class="badge badge-online">Transmission completed</span>
                        <span x-show="phase === 'failed' || phase === 'expired'" x-cloak class="badge badge-danger">Not completed</span>
                    </div>
                </div>

                <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4" x-show="command" x-cloak>
                    <?php $__currentLoopData = [
                        ['key' => 'issued_at', 'label' => 'Queued'],
                        ['key' => 'received_at', 'label' => 'Received'],
                        ['key' => 'transmission_at', 'label' => 'IR attempt'],
                        ['key' => 'completed_at', 'label' => 'Completed'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chartStep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-lg border border-line bg-white px-2.5 py-1.5">
                            <p class="text-[8.5px] uppercase tracking-[0.06em] text-muted"><?php echo e($chartStep['label']); ?></p>
                            <p class="mt-0.5 truncate text-[10px] font-semibold text-ink" x-text="command?.<?php echo e($chartStep['key']); ?> ? new Date(command.<?php echo e($chartStep['key']); ?>).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '—'"></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="mt-2.5 flex flex-wrap items-center gap-x-4 gap-y-1 border-t border-divider pt-2.5 text-[9.5px] text-muted">
                    <span class="inline-flex items-center gap-1"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'clock','size' => 10]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock','size' => 10]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Command expiry: <?php echo e(config('hvac.command_ttl_seconds', 300)); ?> seconds</span>
                    <span class="inline-flex items-center gap-1"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'shield','size' => 10]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield','size' => 10]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Remote policy: <?php echo e($effectivePolicy['value']->label()); ?> (<?php echo e($effectivePolicy['source_label']); ?>)</span>
                </div>
            </div>
        </div>

        <div class="mt-4 grid gap-4 lg:grid-cols-3">
            <div class="card card-pad lg:col-span-2">
                <div class="flex items-start gap-2">
                    <span class="icon-tile bg-blue-50 text-blue-600"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'info','size' => 16]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info','size' => 16]); ?>
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
                        <h2 class="text-[12.5px] font-semibold text-ink">What this control actually confirms</h2>
                        <ul class="mt-2 space-y-1.5 text-[10.5px] leading-relaxed text-ink-soft">
                            <li class="flex gap-1.5"><span class="mt-[5px] h-1 w-1 shrink-0 rounded-full bg-blue-400"></span> A queued command is a server fact. It is not proof the device received it.</li>
                            <li class="flex gap-1.5"><span class="mt-[5px] h-1 w-1 shrink-0 rounded-full bg-blue-400"></span> IR transmission success confirms the ESP32 sent a signal — not that the AC physically changed state.</li>
                            <li class="flex gap-1.5"><span class="mt-[5px] h-1 w-1 shrink-0 rounded-full bg-blue-400"></span> The last-known state remains the last state reported by the device. Unknown or stale is never treated as OFF.</li>
                            <li class="flex gap-1.5"><span class="mt-[5px] h-1 w-1 shrink-0 rounded-full bg-blue-400"></span> Software cannot block a handheld remote’s direct beam. Remote policy only governs synchronization and reporting.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card card-pad">
                <div class="flex items-start gap-2">
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
                        <h2 class="text-[12.5px] font-semibold text-ink">Protocol limits</h2>
                        <p class="mt-1 text-[10.5px] leading-relaxed text-ink-soft">Commands use the validated profile for <?php echo e($ac->acModel?->displayName() ?? 'this model'); ?>. Supported range <?php echo e($min); ?>–<?php echo e($max); ?>°C in <?php echo e($step); ?>°C steps.</p>
                        <p class="mt-2 text-[9.5px] leading-relaxed text-muted">Unsupported modes or out-of-range values are rejected before anything is queued.</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if($latestCommand): ?>
            <div class="mt-4 card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Last issued command</h2>
                        <p class="card-subtitle"><?php echo e($latestCommand->status->label()); ?> · <?php echo e(\App\Support\Format::datetime($latestCommand->issued_at, $timezone)); ?></p>
                    </div>
                    <span class="badge <?php echo e($latestCommand->status === \App\Enums\CommandStatus::Completed ? 'badge-online' : ($latestCommand->status->isPending() ? 'badge-warning' : 'badge-danger')); ?>"><?php echo e(ucfirst($latestCommand->status->value)); ?></span>
                </div>
                <div class="table-scroll">
                    <table class="table">
                        <thead><tr><th>Issued</th><th>Requested state</th><th>Status</th><th>Issued by</th><th>Expires</th></tr></thead>
                        <tbody>
                            <tr>
                                <td><?php echo e(\App\Support\Format::datetime($latestCommand->issued_at, $timezone)); ?></td>
                                <td class="font-medium text-ink">
                                    <?php if(! empty($latestCommand->desired_state['power'])): ?>
                                        Power <?php echo e(strtoupper($latestCommand->desired_state['power'])); ?>

                                    <?php endif; ?>
                                    <?php if(isset($latestCommand->desired_state['temperature_setpoint_c'])): ?>
                                        · <?php echo e($latestCommand->desired_state['temperature_setpoint_c']); ?>°C
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($latestCommand->status->label()); ?></td>
                                <td><?php echo e($latestCommand->requestedBy?->name ?? 'System'); ?></td>
                                <td><?php echo e(\App\Support\Format::datetime($latestCommand->expires_at, $timezone)); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/acs/control.blade.php ENDPATH**/ ?>