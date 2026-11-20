<?php
    $currentView = $filters['view'];
    $sortUrl = function (string $column) use ($filters) {
        return request()->fullUrlWithQuery([
            'sort' => $column,
            'direction' => $filters['sort'] === $column && $filters['direction'] === 'asc' ? 'desc' : 'asc',
        ]);
    };

    $onCount = $acs->filter(fn ($ac) => $ac->stateSnapshot?->power_state === \App\Enums\PowerState::KnownOn && ! $ac->stateSnapshot?->isStale())->count();
    $offCount = $acs->filter(fn ($ac) => $ac->stateSnapshot?->power_state === \App\Enums\PowerState::KnownOff && ! $ac->stateSnapshot?->isStale())->count();
    $unknownCount = max(0, $acs->count() - $onCount - $offCount);
    $staleCount = $acs->filter(fn ($ac) => $ac->stateSnapshot?->isStale() && $ac->stateSnapshot?->power_state !== \App\Enums\PowerState::Unknown)->count();
?>

<div class="mb-3 flex flex-wrap items-center justify-between gap-2 text-[11px] text-muted">
    <div class="flex flex-wrap items-center gap-3">
        <span class="text-ink-soft"><span class="font-semibold text-ink"><?php echo e(number_format($acs->total())); ?></span> air conditioner<?php echo e($acs->total() === 1 ? '' : 's'); ?></span>

        <span class="summary-chip <?php echo e($filters['state'] === 'on' ? 'summary-chip-active' : ''); ?>">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'power','size' => 11,'class' => 'text-[#0d8c4e]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'power','size' => 11,'class' => 'text-[#0d8c4e]']); ?>
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
            <span><?php echo e($onCount); ?> ON</span>
        </span>
        <span class="summary-chip <?php echo e($filters['state'] === 'off' ? 'summary-chip-active' : ''); ?>">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'power','size' => 11,'class' => 'text-neutral']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'power','size' => 11,'class' => 'text-neutral']); ?>
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
            <span><?php echo e($offCount); ?> OFF</span>
        </span>
        <span class="summary-chip <?php echo e($filters['state'] === 'unknown' || $filters['state'] === 'stale' ? 'summary-chip-active' : ''); ?>">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert','size' => 11,'class' => 'text-[#b77405]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','size' => 11,'class' => 'text-[#b77405]']); ?>
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
            <span><?php echo e($unknownCount + $staleCount); ?> Unknown / Stale</span>
        </span>
    </div>

    <div class="flex items-center gap-2">
        <label for="sort-select" class="text-[10px] uppercase tracking-[0.05em] text-muted">Sort</label>
        <form method="GET" action="<?php echo e(route('acs.index')); ?>" id="sort-form">
            <?php $__currentLoopData = request()->except(['sort', 'direction', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(is_scalar($value)): ?><input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>"><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <select id="sort-select" name="sort" class="border-0 bg-transparent py-0 pr-6 text-[11px] font-semibold text-ink-soft outline-none" onchange="this.form.submit()">
                <option value="asset_code" <?php if($filters['sort'] === 'asset_code'): echo 'selected'; endif; ?>>Asset code</option>
                <option value="name" <?php if($filters['sort'] === 'name'): echo 'selected'; endif; ?>>Name</option>
                <option value="duration" <?php if($filters['sort'] === 'duration'): echo 'selected'; endif; ?>>Today’s ON duration</option>
                <option value="setpoint" <?php if($filters['sort'] === 'setpoint'): echo 'selected'; endif; ?>>Setpoint</option>
                <option value="last_update" <?php if($filters['sort'] === 'last_update'): echo 'selected'; endif; ?>>Last update</option>
            </select>
            <input type="hidden" name="direction" value="<?php echo e($filters['direction']); ?>">
        </form>
        <a href="<?php echo e($sortUrl($filters['sort'])); ?>" class="btn btn-ghost btn-sm px-1.5" title="Toggle direction">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $filters['direction'] === 'asc' ? 'chevron-down' : 'chevron-right','size' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filters['direction'] === 'asc' ? 'chevron-down' : 'chevron-right'),'size' => 13]); ?>
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
        </a>
    </div>
</div>

<?php if($acs->isEmpty()): ?>
    <div class="card">
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'ac','title' => 'No air conditioners match','message' => 'Adjust your search or filters, or register a new AC and assign it to a space.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'ac','title' => 'No air conditioners match','message' => 'Adjust your search or filters, or register a new AC and assign it to a space.']); ?>
            <button type="button" class="btn btn-primary btn-sm" @click="addOpen = true"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','size' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','size' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Register AC</button>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
    </div>
<?php elseif($currentView === 'table'): ?>
    <div class="card overflow-hidden">
        <div class="space-y-2">
            <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $snapshot = $ac->stateSnapshot;
                            $stale = $snapshot?->isStale() ?? true;
                            $device = $ac->currentAssignment?->device;
                            $profile = $ac->irProfileVersion;
                            $canControl = $ac->supportsProfileControl() && $device;
                            $powerVal = $snapshot?->power_state?->value ?? 'unknown';
                            $qualityVal = $snapshot?->quality?->value ?? 'unknown';
                            $setpointVal = $snapshot?->temperature_setpoint_c ?? null;
                            $minVal = $profile?->temperature_min_c ?? 16;
                            $maxVal = $profile?->temperature_max_c ?? 30;
                            $stepVal = (float) ($profile?->temperature_step_c ?? 1);
                            $cmdUrl = route('acs.commands.store', $ac);
                            $pollUrl = route('commands.show', ['command' => '__COMMAND__']);
                            $obsAt = $snapshot?->observed_at?->toIso8601String() ?? '';
                            $deviceStatusClass = $device ? 'ac-list-chip-small-online' : 'ac-list-chip-small-unknown';
                            $deviceStatusText = $device ? $device->connectivity()->label() : 'Unknown';
                        ?>
                        <article x-data="acControl" x-init="config = {
                    acId: <?php echo e($ac->id); ?>,
                    commandUrl: '<?php echo e($cmdUrl); ?>',
                    commandPollUrl: '<?php echo e($pollUrl); ?>',
                    power: '<?php echo e($powerVal); ?>',
                    setpoint: <?php echo e($setpointVal); ?>,
                    min: <?php echo e($minVal); ?>,
                    max: <?php echo e($maxVal); ?>,
                    step: <?php echo e($stepVal); ?>,
                    canControl: <?php echo e($canControl ? 'true' : 'false'); ?>,
                    deviceOnline: <?php echo e($ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? 'true' : 'false'); ?>,
                    observedAt: '<?php echo e($obsAt); ?>',
                    quality: '<?php echo e($qualityVal); ?>'
                }; init()"
                    class="list-ac-card border border-line/85 rounded-xl bg-white p-3 flex flex-col md:flex-row items-center justify-between gap-3 transition hover:border-blue-300 hover:shadow-[0_4px_12px_rgba(8,117,201,0.15)]">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 min-w-[110px]">
                        <a href="<?php echo e(route('acs.show', $ac)); ?>" class="text-[12px] font-bold text-ink truncate hover:text-blue-700"><?php echo e($ac->asset_code); ?></a>
                        <span class="block text-[9px] text-muted truncate"><?php echo e($ac->name); ?></span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 min-w-[140px]">
                        <span class="block max-w-[150px] truncate text-[9.5px] text-ink-soft"><?php echo e($ac->space?->name ?? 'Unassigned'); ?></span>
                        <?php if($ac->space?->floor): ?>
                            <span class="block text-[9px] text-muted truncate"><?php echo e($ac->space->floor->name); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center sm:items-center gap-2 sm:gap-3 min-w-[120px]">
                        <span class="block text-[9px] text-ink-soft"><?php echo e($ac->acModel?->manufacturer ?? '—'); ?></span>
                        <span class="block text-[9px] text-muted truncate"><?php echo e($ac->acModel?->model_name ?? 'Model not recorded'); ?></span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center sm:justify-center gap-2 sm:gap-3 min-w-[70px]">
                        <button type="button"
                            class="rocker rocker-sm <?php echo e($canControl && $ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? '' : 'opacity-70'); ?>"
                            :data-on="isOn ? 'true' : 'false'"
                            :data-pending="pending ? 'true' : 'false'"
                            :disabled="! config.canControl || pending"
                            aria-label="AC power rocker. ON is at the top, OFF is at the bottom."
                            @click="setPower(isOn ? 'off' : 'on')">
                            <span class="rocker-thumb" :class="isOn ? 'rocker-thumb-on' : 'rocker-thumb-off'"></span>
                            <span class="rocker-position rocker-on">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'power','size' => 11,'stroke' => 2]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'power','size' => 11,'stroke' => 2]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'power','size' => 11,'stroke' => 1.6]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'power','size' => 11,'stroke' => 1.6]); ?>
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
                        <div class="flex items-center gap-1 mt-1 sm:mt-0">
                            <button type="button"
                                class="btn btn-secondary btn-icon shrink-0 h-6 w-6"
                                :disabled="! canDecrease || pending"
                                :class="(! canDecrease || pending) ? 'opacity-40' : ''"
                                @click="changeSetpoint(-1)"
                                aria-label="Decrease setpoint">
                                <span class="text-[13px] font-light leading-none">−</span>
                            </button>
                            <span class="font-semibold text-ink"><?php echo e($setpointVal !== null ? $setpointVal : '—'); ?></span><span class="text-[10px] text-muted">°C</span>
                            <button type="button"
                                class="btn btn-secondary btn-icon shrink-0 h-6 w-6"
                                :disabled="! canIncrease || pending"
                                :class="(! canIncrease || pending) ? 'opacity-40' : ''"
                                @click="changeSetpoint(1)"
                                aria-label="Increase setpoint">
                                <span class="text-[13px] font-light leading-none">+</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 min-w-[110px]">
                        <span class="ac-list-chip-small <?php echo e($deviceStatusClass); ?>"><?php echo e($deviceStatusText); ?></span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 min-w-[80px] text-right sm:text-right">
                        <span class="text-[9px] text-muted"><?php echo e(\App\Support\Format::relative($snapshot?->observed_at)); ?></span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 min-w-[70px]">
                        <a href="<?php echo e(route('acs.show', $ac)); ?>" class="flex items-center gap-1.5 btn btn-ghost btn-sm" title="View details">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'info','class' => 'text-[11px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info','class' => 'text-[11px]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> <span class="text-[9.5px]">Details</span>
                        </a>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php else: ?>
    <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
        <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $snapshot = $ac->stateSnapshot;
                $stale = $snapshot?->isStale() ?? true;
                $device = $ac->currentAssignment?->device;
                $profile = $ac->irProfileVersion;
                $canControl = $ac->supportsProfileControl() && $device;
                $onSeconds = (int) ($todayUsage[$ac->id] ?? 0);
                $cmdUrl = route('acs.commands.store', $ac);
                $pollUrl = route('commands.show', ['command' => '__COMMAND__']);
                $obsAt = $snapshot?->observed_at?->toIso8601String() ?? '';
                $powerVal = $snapshot?->power_state?->value ?? 'unknown';
                $qualityVal = $snapshot?->quality?->value ?? 'unknown';
                $setpointVal = $snapshot?->temperature_setpoint_c ?? 'null';
                $minVal = $profile?->temperature_min_c ?? 16;
                $maxVal = $profile?->temperature_max_c ?? 30;
                $stepVal = (float) ($profile?->temperature_step_c ?? 1);
                $deviceStatusClass = $device ? 'ac-list-chip-small-online' : 'ac-list-chip-small-unknown';
                $deviceStatusText = $device ? $device->connectivity()->label() : 'Unknown';
            ?>
            <article x-data="acControl" x-init="config = {
                acId: <?php echo e($ac->id); ?>,
                commandUrl: '<?php echo e($cmdUrl); ?>',
                commandPollUrl: '<?php echo e($pollUrl); ?>',
                power: '<?php echo e($powerVal); ?>',
                setpoint: <?php echo e($setpointVal); ?>,
                min: <?php echo e($minVal); ?>,
                max: <?php echo e($maxVal); ?>,
                step: <?php echo e($stepVal); ?>,
                canControl: <?php echo e($canControl ? 'true' : 'false'); ?>,
                deviceOnline: <?php echo e($ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? 'true' : 'false'); ?>,
                observedAt: '<?php echo e($obsAt); ?>',
                quality: '<?php echo e($qualityVal); ?>'
            }; init()"
                class="ac-card-grid group overflow-hidden transition hover:border-blue-300 hover:shadow-[0_10px_24px_-18px_rgba(8,117,201,0.4)]">
                <div class="p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <a href="<?php echo e(route('acs.show', $ac)); ?>" class="block truncate text-[12.5px] font-bold tracking-[-0.01em] text-ink hover:text-blue-700"><?php echo e($ac->asset_code); ?></a>
                            <span class="block text-[9px] text-muted text-xs uppercase tracking-[0.05em]"><?php echo e($ac->id); ?></span>
                        </div>
                        <a href="<?php echo e(route('acs.show', $ac)); ?>" class="text-[9px] text-muted hover:text-blue-700" title="View details">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'info','size' => 12]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info','size' => 12]); ?>
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
                        </a>
                    </div>

                    <div class="mt-2 flex items-center gap-1.5 text-[9.5px] text-ink-soft">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'space','size' => 10,'class' => 'text-[#8fb6d8]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'space','size' => 10,'class' => 'text-[#8fb6d8]']); ?>
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
                        <span class="truncate"><?php echo e($ac->space?->name ?? 'Unassigned'); ?></span>
                        <?php if($ac->space?->floor): ?>
                            <span class="text-muted">· <?php echo e($ac->space->floor->name); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="mt-2 flex flex-wrap items-center gap-1">
                        <span class="rounded-md bg-[#f3f8fc] px-1.5 py-0.5 text-[9px] font-medium text-ink-soft"><?php echo e($ac->acModel?->manufacturer ?? 'Unknown'); ?></span>
                        <span class="rounded-md bg-[#f3f8fc] px-1.5 py-0.5 text-[9px] font-medium text-ink-soft"><?php echo e($ac->acModel?->model_name ?? 'No model'); ?></span>
                    </div>

                    
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <button type="button"
                                class="rocker rocker-sm <?php echo e($canControl && $ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? '' : 'opacity-70'); ?>"
                                :data-on="isOn ? 'true' : 'false'"
                                :data-pending="pending ? 'true' : 'false'"
                                :disabled="! config.canControl || pending"
                                aria-label="AC power rocker. ON is at the top, OFF is at the bottom."
                                @click="setPower(isOn ? 'off' : 'on')">
                                <span class="rocker-thumb" :class="isOn ? 'rocker-thumb-on' : 'rocker-thumb-off'"></span>
                                <span class="rocker-position rocker-on">
                                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'power','size' => 13,'stroke' => 2]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'power','size' => 13,'stroke' => 2]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'power','size' => 13,'stroke' => 1.6]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'power','size' => 13,'stroke' => 1.6]); ?>
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
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="temp-dial-sm" :style="'--dial: ' + dialPercent + '%'">
                                <div class="temp-dial-inner">
                                    <p class="text-[8px] font-semibold uppercase tracking-[0.12em] text-muted">Setpoint</p>
                                    <p class="mt-0.5 temp-text-sm">
                                        <span x-text="(requestedSetpoint !== null && requestedSetpoint !== undefined) ? requestedSetpoint : '—'"></span><span class="text-[14px] align-top">°C</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 mt-1">
                                <button type="button"
                                    class="btn btn-secondary btn-icon shrink-0 h-6 w-6"
                                    :disabled="! canDecrease || pending"
                                    :class="(! canDecrease || pending) ? 'opacity-40' : ''"
                                    @click="changeSetpoint(-1)"
                                    aria-label="Decrease setpoint">
                                    <span class="text-[14px] font-light leading-none">−</span>
                                </button>
                                <button type="button"
                                    class="btn btn-secondary btn-icon shrink-0 h-6 w-6"
                                    :disabled="! canIncrease || pending"
                                    :class="(! canIncrease || pending) ? 'opacity-40' : ''"
                                    @click="changeSetpoint(1)"
                                    aria-label="Increase setpoint">
                                    <span class="text-[14px] font-light leading-none">+</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 flex items-center justify-between gap-2">
                        <span class="ac-list-chip-small <?php echo e($deviceStatusClass); ?>"><?php echo e($deviceStatusText); ?></span>
                        <span class="power-status-chip <?php echo e($powerVal === 'known_on' ? 'bg-success-soft text-[#0d8c4e]' : ($powerVal === 'known_off' ? 'bg-[#f0f4f8] text-neutral' : 'bg-warning-soft text-[#b77405]')); ?> text-[9px] font-semibold px-2 py-0.5 rounded-full">
                            <?php echo e($stale ? 'Stale ' : ''); ?><?php echo e($powerVal === 'known_on' ? 'ON' : ($powerVal === 'known_off' ? 'OFF' : 'Unknown')); ?>

                        </span>
                        <span class="text-[9px] text-muted"><?php echo e(\App\Support\Format::relative($snapshot?->observed_at)); ?></span>
                    </div>
                </div>

                <div class="flex items-center gap-1 border-t border-divider bg-[#fbfdff] px-3 py-1.5">
                    <a href="<?php echo e(route('acs.show', $ac)); ?>" class="flex-1 flex items-center justify-center gap-1.5 btn btn-ghost btn-sm" title="View details">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'info','class' => 'text-[12px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info','class' => 'text-[12px]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> <span class="text-[9.5px]">Details</span>
                    </a>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>

<?php if($acs->hasPages()): ?>
    <div class="mt-3"><?php echo e($acs->links()); ?></div>
<?php endif; ?>

<p class="mt-2 text-[9px] leading-relaxed text-muted">
    Setpoint is the AC target temperature, not measured room temperature. Device connectivity is separate from AC power state. Unknown setpoint is shown as “—”, never 0°C.
</p>
<?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/acs/partials/results.blade.php ENDPATH**/ ?>