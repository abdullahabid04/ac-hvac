<?php $__env->startSection('page-title', 'AC Details & Status'); ?>
<?php $__env->startSection('page-subtitle', 'Complete view of configuration, state, commands and recent activity'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $snapshot = $ac->stateSnapshot;
        $stale = $snapshot?->isStale() ?? true;
        $device = $ac->currentAssignment?->device;
        $stateColor = $snapshot?->power_state === \App\Enums\PowerState::KnownOn && ! $stale
            ? 'text-[#0d8c4e]'
            : 'text-ink';

        $formatHistoryValue = function ($value, string $field) {
            if (is_array($value)) {
                $value = $value[$field] ?? $value['value'] ?? reset($value);
            }

            if ($field === 'power_state') {
                return match ((string) $value) {
                    'known_on' => 'ON',
                    'known_off' => 'OFF',
                    'unknown' => 'Unknown',
                    'stale' => 'Stale',
                    default => $value === null ? '—' : ucfirst((string) $value),
                };
            }

            if ($field === 'temperature_setpoint_c') {
                return is_numeric($value) ? $value.'°C' : '—';
            }

            return $value === null || $value === '' ? '—' : ucfirst((string) $value);
        };

        $tabs = [
            'overview' => ['label' => 'Overview', 'icon' => 'dashboard'],
            'history' => ['label' => 'History', 'icon' => 'activity'],
            'usage' => ['label' => 'Usage', 'icon' => 'analytics'],
            'settings' => ['label' => 'Settings', 'icon' => 'settings'],
        ];
    ?>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
        <a href="<?php echo e(route('acs.index')); ?>" class="btn btn-ghost btn-sm"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Back to inventory</a>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('acs.show', ['ac' => $ac, 'tab' => 'settings'])); ?>" class="btn btn-secondary btn-sm"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'edit','size' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit','size' => 14]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Edit</a>
            <a href="<?php echo e(route('acs.control', $ac)); ?>" class="btn btn-primary btn-sm <?php echo e($ac->supportsProfileControl() && $device ? '' : 'pointer-events-none opacity-50'); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'power','size' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'power','size' => 14]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Control panel</a>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-pad">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'ac','size' => 25,'stroke' => 1.6]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'ac','size' => 25,'stroke' => 1.6]); ?>
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
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="truncate text-[21px] font-bold tracking-[-0.025em] text-ink"><?php echo e($ac->asset_code); ?></h1>
                            <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['state' => $snapshot?->power_state,'stale' => $stale,'quality' => $snapshot?->quality?->value]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($snapshot?->power_state),'stale' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stale),'quality' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($snapshot?->quality?->value)]); ?>
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
                            <span class="badge <?php echo e($ac->status === 'active' ? 'badge-info' : 'badge-unknown'); ?>"><?php echo e(ucfirst($ac->status)); ?></span>
                        </div>
                        <p class="mt-0.5 truncate text-[12px] text-ink-soft"><?php echo e($ac->name); ?></p>
                        <p class="mt-1 flex flex-wrap items-center gap-1 text-[10.5px] text-muted">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'locations','size' => 11]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'locations','size' => 11]); ?>
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
                            <?php if($ac->space?->floor?->building?->site): ?>
                                <a href="<?php echo e(route('locations.index', ['type' => 'site', 'id' => $ac->space->floor->building->site->id])); ?>" class="hover:text-blue-700 hover:underline"><?php echo e($ac->space->floor->building->site->name); ?></a>
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-right','size' => 9]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','size' => 9]); ?>
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
                            <?php endif; ?>
                            <?php if($ac->space?->floor?->building): ?>
                                <a href="<?php echo e(route('locations.index', ['type' => 'building', 'id' => $ac->space->floor->building->id])); ?>" class="hover:text-blue-700 hover:underline"><?php echo e($ac->space->floor->building->name); ?></a>
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-right','size' => 9]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','size' => 9]); ?>
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
                            <?php endif; ?>
                            <?php if($ac->space?->floor): ?>
                                <a href="<?php echo e(route('locations.index', ['type' => 'floor', 'id' => $ac->space->floor->id])); ?>" class="hover:text-blue-700 hover:underline"><?php echo e($ac->space->floor->name); ?></a>
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-right','size' => 9]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','size' => 9]); ?>
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
                            <?php endif; ?>
                            <?php if($ac->space): ?>
                                <a href="<?php echo e(route('locations.index', ['type' => 'space', 'id' => $ac->space->id])); ?>" class="font-medium text-ink-soft hover:text-blue-700 hover:underline"><?php echo e($ac->space->name); ?></a>
                            <?php else: ?>
                                <span>Unassigned location</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1">
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
                    <span class="text-[10px] text-muted">Last update <?php echo e(\App\Support\Format::relative($snapshot?->observed_at)); ?></span>
                </div>
            </div>
        </div>

        <div class="border-t border-divider px-4 pt-3 md:px-[18px]">
            <nav class="tab-list w-fit" aria-label="AC detail tabs">
                <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('acs.show', ['ac' => $ac, 'tab' => $key])); ?>"
                       class="tab <?php echo e($tab === $key ? 'tab-active' : ''); ?>">
                        <?php echo e($meta['label']); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>
        </div>
    </div>

    <?php if($tab === 'overview'): ?>
        <div class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="stat-card">
                <p class="filter-label">Reported state</p>
                <p class="mt-1.5 flex items-center gap-2 text-[20px] font-bold leading-none <?php echo e($stateColor); ?>">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'power','size' => 18]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'power','size' => 18]); ?>
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
                    <?php echo e($snapshot?->power_state?->shortLabel() ?? 'Unknown'); ?>

                </p>
                <p class="mt-1 text-[10px] text-muted">
                    <?php echo e($stale ? 'Stale — not confirmed OFF' : 'Quality: '.($snapshot?->quality?->label() ?? 'Unknown')); ?>

                </p>
            </div>
            <div class="stat-card">
                <p class="filter-label">Temperature setpoint</p>
                <p class="mt-1.5 flex items-center gap-2 text-[20px] font-bold leading-none text-ink">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'thermometer','size' => 18,'class' => 'text-blue-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'thermometer','size' => 18,'class' => 'text-blue-500']); ?>
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
                    <?php echo e($snapshot?->temperature_setpoint_c !== null ? $snapshot->temperature_setpoint_c.'°C' : 'Unknown'); ?>

                </p>
                <p class="mt-1 text-[10px] text-muted">Target temperature, not room temperature</p>
            </div>
            <div class="stat-card">
                <p class="filter-label">Today’s ON duration</p>
                <p class="mt-1.5 flex items-center gap-2 text-[20px] font-bold leading-none text-ink">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'clock','size' => 18,'class' => 'text-[#3a9c78]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock','size' => 18,'class' => 'text-[#3a9c78]']); ?>
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
                    <?php echo e(\App\Support\Format::duration($usage['today']['on_seconds'], true)); ?>

                </p>
                <p class="mt-1 text-[10px] text-muted">Operating time, not kWh</p>
            </div>
            <div class="stat-card">
                <p class="filter-label">Device connection</p>
                <p class="mt-1.5 flex items-center gap-2 text-[20px] font-bold leading-none text-ink">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? 'wifi' : 'wifi-off','size' => 18,'class' => ''.e($ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? 'text-[#0d8c4e]' : 'text-muted').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? 'wifi' : 'wifi-off'),'size' => 18,'class' => ''.e($ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? 'text-[#0d8c4e]' : 'text-muted').'']); ?>
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
                    <?php echo e($ac->connectivity()->label()); ?>

                </p>
                <p class="mt-1 text-[10px] text-muted">Last seen <?php echo e($device?->last_seen_at?->diffForHumans(short: true) ?? 'never'); ?></p>
            </div>
        </div>

        <?php if($stale || $snapshot?->power_state === null || $snapshot?->power_state === \App\Enums\PowerState::Unknown): ?>
            <div class="mt-3 flex items-start gap-2 rounded-xl border border-[#f6deac] bg-warning-soft/70 px-3.5 py-2.5 text-[11px] text-[#8a5a05]">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert','size' => 14,'class' => 'mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','size' => 14,'class' => 'mt-0.5']); ?>
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
                <p>
                    <strong>This AC’s state is <?php echo e($stale ? 'stale' : 'unknown'); ?>.</strong>
                    It is not counted as OFF. Device connectivity does not prove the AC is running.
                    <?php if($device): ?> The last report was received <?php echo e(\App\Support\Format::relative($snapshot?->observed_at)); ?>. <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="mt-4 grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="space-y-4">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Last-known configuration</h2>
                            <p class="card-subtitle">Reported by the device; may lag behind a physical remote action</p>
                        </div>
                        <span class="badge <?php echo e($snapshot?->quality?->value === 'inferred' ? 'badge-warning' : 'badge-info'); ?>"><?php echo e($snapshot?->quality?->label() ?? 'Unknown'); ?></span>
                    </div>
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-3 p-4 text-[11.5px] md:p-[18px]">
                        <div><dt class="text-muted">Power</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e($snapshot?->power_state?->label() ?? 'Unknown'); ?></dd></div>
                        <div><dt class="text-muted">Setpoint</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e($snapshot?->temperature_setpoint_c !== null ? $snapshot->temperature_setpoint_c.'°C' : 'Unknown'); ?></dd></div>
                        <div><dt class="text-muted">Operating mode</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e($snapshot?->operating_mode ? ucfirst($snapshot->operating_mode) : 'Not reported'); ?></dd></div>
                        <div><dt class="text-muted">Fan setting</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e($snapshot?->fan_setting ? ucfirst($snapshot->fan_setting) : 'Not reported'); ?></dd></div>
                        <div><dt class="text-muted">Swing</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e($snapshot?->swing_setting ? ucfirst($snapshot->swing_setting) : 'Not reported'); ?></dd></div>
                        <div><dt class="text-muted">Observed at</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e(\App\Support\Format::datetime($snapshot?->observed_at, $timezone)); ?></dd></div>
                        <div><dt class="text-muted">Received at</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e(\App\Support\Format::datetime($snapshot?->received_at, $timezone)); ?></dd></div>
                        <div><dt class="text-muted">State source</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e($snapshot?->source ? ucfirst(str_replace('_', ' ', $snapshot->source)) : '—'); ?></dd></div>
                    </dl>
                    <div class="border-t border-divider bg-[#fbfdff] px-4 py-2.5 text-[10px] leading-relaxed text-muted">
                        This reading is the AC’s last-known state. A command transmission does not physically confirm the AC changed state.
                    </div>
                </div>

                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Assigned ESP32 device</h2>
                            <p class="card-subtitle">One device can control exactly one AC at a time</p>
                        </div>
                        <?php if($device): ?><?php if (isset($component)) { $__componentOriginalb254a181568b9eef85fcdb4bf6b8eccf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb254a181568b9eef85fcdb4bf6b8eccf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.connectivity-badge','data' => ['status' => $device->connectivity()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('connectivity-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($device->connectivity())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb254a181568b9eef85fcdb4bf6b8eccf)): ?>
<?php $attributes = $__attributesOriginalb254a181568b9eef85fcdb4bf6b8eccf; ?>
<?php unset($__attributesOriginalb254a181568b9eef85fcdb4bf6b8eccf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb254a181568b9eef85fcdb4bf6b8eccf)): ?>
<?php $component = $__componentOriginalb254a181568b9eef85fcdb4bf6b8eccf; ?>
<?php unset($__componentOriginalb254a181568b9eef85fcdb4bf6b8eccf); ?>
<?php endif; ?><?php endif; ?>
                    </div>
                    <?php if($device): ?>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 p-4 text-[11.5px] md:p-[18px]">
                            <div><dt class="text-muted">Device</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e($device->name); ?></dd></div>
                            <div><dt class="text-muted">Device ID</dt><dd class="mt-0.5 font-mono text-[10.5px] font-semibold text-ink"><?php echo e($device->device_uid); ?></dd></div>
                            <div><dt class="text-muted">Hardware</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e($device->hardware_model ?: '—'); ?></dd></div>
                            <div><dt class="text-muted">Firmware</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e($device->firmware_version ?: '—'); ?></dd></div>
                            <div><dt class="text-muted">Last seen</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e(\App\Support\Format::datetime($device->last_seen_at, $timezone)); ?></dd></div>
                            <div><dt class="text-muted">Remote policy</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e(ucfirst($device->effective_remote_policy ?? 'inherit')); ?> (v<?php echo e($device->policy_version); ?>)</dd></div>
                        </dl>
                        <div class="border-t border-divider px-4 py-2.5">
                            <form method="POST" action="<?php echo e(route('acs.device.assign', $ac)); ?>" class="flex flex-wrap items-end gap-2">
                                <?php echo csrf_field(); ?>
                                <div class="min-w-[180px] flex-1">
                                    <label class="label mb-1" for="detail-device">Reassign device</label>
                                    <select id="detail-device" name="device_id" class="select h-[34px] py-0 text-[11.5px]">
                                        <option value="">Unassign device</option>
                                        <?php $__currentLoopData = $availableDevices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($candidate->id); ?>" <?php if($candidate->id === $device->id): echo 'selected'; endif; ?>>
                                                <?php echo e($candidate->name); ?> · <?php echo e($candidate->device_uid); ?>

                                                <?php if($candidate->currentAssignment && $candidate->currentAssignment->ac_unit_id !== $ac->id): ?>
                                                    (currently <?php echo e($candidate->currentAssignment->acUnit?->asset_code); ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-secondary btn-sm h-[34px]">Save assignment</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="p-4 md:p-[18px]">
                            <p class="text-[11.5px] text-ink-soft">No ESP32 is assigned. Remote control is unavailable until a validated device is assigned.</p>
                            <form method="POST" action="<?php echo e(route('acs.device.assign', $ac)); ?>" class="mt-3 flex flex-wrap items-end gap-2">
                                <?php echo csrf_field(); ?>
                                <div class="min-w-[180px] flex-1">
                                    <label class="label mb-1" for="detail-device-empty">Assign device</label>
                                    <select id="detail-device-empty" name="device_id" class="select h-[34px] py-0 text-[11.5px]">
                                        <option value="">Select a device…</option>
                                        <?php $__currentLoopData = $availableDevices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($candidate->id); ?>"><?php echo e($candidate->name); ?> · <?php echo e($candidate->device_uid); ?><?php if($candidate->currentAssignment): ?> (assigned to <?php echo e($candidate->currentAssignment->acUnit?->asset_code); ?>)<?php endif; ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm h-[34px]">Assign</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="space-y-4">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Recent commands</h2>
                            <p class="card-subtitle">Queue and transmission facts, newest first</p>
                        </div>
                        <a href="<?php echo e(route('acs.show', ['ac' => $ac, 'tab' => 'history'])); ?>" class="btn btn-ghost btn-sm">History <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-right','size' => 12]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','size' => 12]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></a>
                    </div>
                    <?php $__empty_1 = true; $__currentLoopData = $ac->commands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $command): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-start gap-2.5 border-b border-divider px-4 py-2.5 last:border-0">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full
                                <?php echo e($command->status === \App\Enums\CommandStatus::Failed || $command->status === \App\Enums\CommandStatus::Expired ? 'bg-danger-soft text-danger' : ($command->status === \App\Enums\CommandStatus::Completed ? 'bg-success-soft text-[#0d8c4e]' : 'bg-blue-50 text-blue-600')); ?>">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $command->status === \App\Enums\CommandStatus::Completed ? 'check' : ($command->status->isPending() ? 'clock' : 'alert'),'size' => 11]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($command->status === \App\Enums\CommandStatus::Completed ? 'check' : ($command->status->isPending() ? 'clock' : 'alert')),'size' => 11]); ?>
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
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <p class="text-[11.5px] font-semibold text-ink"><?php echo e($command->status->label()); ?></p>
                                    <span class="badge badge-unknown"><?php echo e(ucfirst(str_replace('_', ' ', $command->command_type))); ?></span>
                                </div>
                                <p class="mt-0.5 text-[10px] text-muted">
                                    <?php if(! empty($command->desired_state['power'])): ?>
                                        Power <?php echo e(strtoupper($command->desired_state['power'])); ?>

                                    <?php endif; ?>
                                    <?php if(isset($command->desired_state['temperature_setpoint_c'])): ?>
                                        · Setpoint <?php echo e($command->desired_state['temperature_setpoint_c']); ?>°C
                                    <?php endif; ?>
                                    · <?php echo e($command->requestedBy?->name ?? 'System'); ?>

                                </p>
                                <?php if($command->failure_message): ?>
                                    <p class="mt-0.5 text-[10px] text-danger"><?php echo e($command->failure_message); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="shrink-0 text-right text-[9.5px] text-muted"><?php echo e(\App\Support\Format::relative($command->issued_at)); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'command','title' => 'No commands yet','message' => 'Commands issued from the control panel will appear here.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'command','title' => 'No commands yet','message' => 'Commands issued from the control panel will appear here.']); ?>
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
                    <?php endif; ?>
                </div>

                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Physical remote policy</h2>
                            <p class="card-subtitle">Nearest explicit scope wins</p>
                        </div>
                        <span class="badge <?php echo e($effectivePolicy['value']->value === 'enabled' ? 'badge-online' : 'badge-off'); ?>"><?php echo e($effectivePolicy['value']->label()); ?></span>
                    </div>
                    <div class="p-4 md:p-[18px]">
                        <p class="text-[11px] text-ink-soft">Effective source: <strong class="text-ink"><?php echo e($effectivePolicy['source_label']); ?></strong> · version <?php echo e($effectivePolicy['version']); ?></p>
                        <p class="mt-0.5 text-[10px] text-muted">AC override: <?php echo e($explicitPolicy ? $explicitPolicy->policy_value->label() : 'Inherit'); ?></p>
                        <form method="POST" action="<?php echo e(route('acs.policy.update', $ac)); ?>" class="mt-3 flex flex-wrap items-end gap-2">
                            <?php echo csrf_field(); ?>
                            <div class="min-w-[150px] flex-1">
                                <label class="label mb-1" for="ac-policy">AC-level policy</label>
                                <select id="ac-policy" name="policy_value" class="select h-[34px] py-0 text-[11.5px]">
                                    <option value="inherit" <?php if(! $explicitPolicy || $explicitPolicy->policy_value->value === 'inherit'): echo 'selected'; endif; ?>>Inherit from parent</option>
                                    <option value="enabled" <?php if($explicitPolicy?->policy_value->value === 'enabled'): echo 'selected'; endif; ?>>Enabled</option>
                                    <option value="disabled" <?php if($explicitPolicy?->policy_value->value === 'disabled'): echo 'selected'; endif; ?>>Disabled</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-secondary btn-sm h-[34px]">Apply</button>
                        </form>
                        <p class="mt-2 text-[9.5px] leading-relaxed text-muted">Software cannot block a handheld remote’s direct IR beam. Policy affects synchronization, reporting and device behaviour only.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Recent activity</h2>
                    <p class="card-subtitle">Portal, physical remote, device and system events</p>
                </div>
                <a href="<?php echo e(route('acs.show', ['ac' => $ac, 'tab' => 'history'])); ?>" class="btn btn-ghost btn-sm">Full history <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-right','size' => 12]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','size' => 12]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></a>
            </div>
            <?php $__empty_1 = true; $__currentLoopData = $ac->activityLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex flex-wrap items-center gap-3 border-b border-divider px-4 py-2.5 last:border-0">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                        <?php echo e($log->result === 'failure' ? 'bg-danger-soft text-danger' : ($log->source === \App\Enums\EventSource::PhysicalRemote ? 'bg-warning-soft text-[#b77405]' : ($log->result === 'success' ? 'bg-success-soft text-[#0d8c4e]' : 'bg-blue-50 text-blue-600'))); ?>">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $log->source === \App\Enums\EventSource::PhysicalRemote ? 'wifi' : ($log->result === 'failure' ? 'alert' : 'check'),'size' => 12]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($log->source === \App\Enums\EventSource::PhysicalRemote ? 'wifi' : ($log->result === 'failure' ? 'alert' : 'check')),'size' => 12]); ?>
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
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[11.5px] font-medium text-ink"><?php echo e($log->description); ?></p>
                        <p class="text-[10px] text-muted"><?php echo e($log->source->label()); ?> · <?php echo e($log->user?->name ?? ($log->device?->name ?? 'System')); ?></p>
                    </div>
                    <span class="badge <?php echo e($log->result === 'failure' ? 'badge-danger' : ($log->result === 'pending' ? 'badge-warning' : 'badge-info')); ?>"><?php echo e(ucfirst($log->result ?? 'info')); ?></span>
                    <span class="w-28 text-right text-[9.5px] text-muted"><?php echo e(\App\Support\Format::datetime($log->occurred_at, $timezone)); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'activity','title' => 'No activity yet','message' => 'Commands, device reports and remote events will appear here.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'activity','title' => 'No activity yet','message' => 'Commands, device reports and remote events will appear here.']); ?>
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
            <?php endif; ?>
        </div>
    <?php elseif($tab === 'history'): ?>
        <div class="mt-4 grid gap-4 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Activity timeline</h2>
                        <p class="card-subtitle">Latest 20 events for this AC</p>
                    </div>
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'activity','size' => 16,'class' => 'text-muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'activity','size' => 16,'class' => 'text-muted']); ?>
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
                <div class="p-4 md:p-[18px]">
                    <?php if($ac->activityLogs->isEmpty()): ?>
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'activity','title' => 'No history yet','message' => 'Events will appear as commands and reports are processed.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'activity','title' => 'No history yet','message' => 'Events will appear as commands and reports are processed.']); ?>
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
                    <?php else: ?>
                        <div class="timeline">
                            <?php $__currentLoopData = $ac->activityLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="timeline-item relative">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-[11.5px] font-semibold text-ink"><?php echo e($log->description); ?></p>
                                        <span class="badge <?php echo e($log->result === 'failure' ? 'badge-danger' : ($log->result === 'pending' ? 'badge-warning' : 'badge-info')); ?>"><?php echo e(ucfirst($log->result ?? 'info')); ?></span>
                                    </div>
                                    <p class="mt-0.5 text-[10px] text-muted">
                                        <?php echo e($log->source->label()); ?> · <?php echo e($log->user?->name ?? ($log->device?->name ?? 'System')); ?> · <?php echo e(\App\Support\Format::datetime($log->occurred_at, $timezone)); ?>

                                    </p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="space-y-4">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">State change history</h2>
                            <p class="card-subtitle">Append-only facts; unknown/stale is never converted to OFF</p>
                        </div>
                    </div>
                    <?php if($ac->stateHistory->isEmpty()): ?>
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'clock','title' => 'No state changes','message' => 'State changes reported by the device will be recorded here.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'clock','title' => 'No state changes','message' => 'State changes reported by the device will be recorded here.']); ?>
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
                    <?php else: ?>
                        <div class="table-scroll">
                            <table class="table">
                                <thead><tr><th>Observed</th><th>Field</th><th>Value</th><th>Source</th><th>Quality</th></tr></thead>
                                <tbody>
                                    <?php $__currentLoopData = $ac->stateHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-[10.5px] text-muted"><?php echo e(\App\Support\Format::datetime($change->observed_at, $timezone)); ?></td>
                                            <td class="font-medium text-ink"><?php echo e(ucfirst(str_replace('_', ' ', $change->state_field))); ?></td>
                                            <td class="font-semibold text-ink"><?php echo e($formatHistoryValue($change->new_value, $change->state_field)); ?></td>
                                            <td><?php echo e($change->source ? ucfirst(str_replace('_', ' ', $change->source)) : '—'); ?></td>
                                            <td><span class="badge <?php echo e($change->quality?->value === 'reported' ? 'badge-info' : 'badge-warning'); ?>"><?php echo e(ucfirst($change->quality?->value ?? 'unknown')); ?></span></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Device events</h2>
                            <p class="card-subtitle">Latest 15 messages received from the ESP32</p>
                        </div>
                    </div>
                    <?php if($ac->deviceEvents->isEmpty()): ?>
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'wifi','title' => 'No device events','message' => 'Device heartbeats and reports are stored here.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'wifi','title' => 'No device events','message' => 'Device heartbeats and reports are stored here.']); ?>
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
                    <?php else: ?>
                        <div class="table-scroll">
                            <table class="table">
                                <thead><tr><th>Occurred</th><th>Type</th><th>Source</th><th>Processing</th></tr></thead>
                                <tbody>
                                    <?php $__currentLoopData = $ac->deviceEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-[10.5px] text-muted"><?php echo e(\App\Support\Format::datetime($event->device_occurred_at, $timezone)); ?></td>
                                            <td class="font-medium text-ink"><?php echo e(str_replace(['.', '_'], [' ', ' '], $event->event_type)); ?></td>
                                            <td><?php echo e($event->source ? ucfirst(str_replace('_', ' ', $event->source)) : '—'); ?></td>
                                            <td><span class="badge <?php echo e($event->processing_status === 'processed' ? 'badge-online' : 'badge-unknown'); ?>"><?php echo e(ucfirst($event->processing_status ?? 'received')); ?></span></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php elseif($tab === 'usage'): ?>
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <?php if (isset($component)) { $__componentOriginal6d74059c34730cb2c742dae13948a701 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d74059c34730cb2c742dae13948a701 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'Today','value' => \App\Support\Format::duration($usage['today']['on_seconds'], true),'icon' => 'clock','tone' => 'blue','hint' => 'Known ON duration · operating time, not kWh']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Today','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\Format::duration($usage['today']['on_seconds'], true)),'icon' => 'clock','tone' => 'blue','hint' => 'Known ON duration · operating time, not kWh']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d74059c34730cb2c742dae13948a701)): ?>
<?php $attributes = $__attributesOriginal6d74059c34730cb2c742dae13948a701; ?>
<?php unset($__attributesOriginal6d74059c34730cb2c742dae13948a701); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d74059c34730cb2c742dae13948a701)): ?>
<?php $component = $__componentOriginal6d74059c34730cb2c742dae13948a701; ?>
<?php unset($__componentOriginal6d74059c34730cb2c742dae13948a701); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal6d74059c34730cb2c742dae13948a701 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d74059c34730cb2c742dae13948a701 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'Last 7 days','value' => \App\Support\Format::duration($usage['week']['on_seconds'], true),'icon' => 'calendar','tone' => 'cyan','hint' => 'Includes only known ON intervals']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Last 7 days','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\Format::duration($usage['week']['on_seconds'], true)),'icon' => 'calendar','tone' => 'cyan','hint' => 'Includes only known ON intervals']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d74059c34730cb2c742dae13948a701)): ?>
<?php $attributes = $__attributesOriginal6d74059c34730cb2c742dae13948a701; ?>
<?php unset($__attributesOriginal6d74059c34730cb2c742dae13948a701); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d74059c34730cb2c742dae13948a701)): ?>
<?php $component = $__componentOriginal6d74059c34730cb2c742dae13948a701; ?>
<?php unset($__componentOriginal6d74059c34730cb2c742dae13948a701); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal6d74059c34730cb2c742dae13948a701 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d74059c34730cb2c742dae13948a701 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'Last 30 days','value' => \App\Support\Format::duration($usage['month']['on_seconds'], true),'icon' => 'analytics','tone' => 'green','hint' => 'Unknown/stale gaps are excluded']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Last 30 days','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\Format::duration($usage['month']['on_seconds'], true)),'icon' => 'analytics','tone' => 'green','hint' => 'Unknown/stale gaps are excluded']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d74059c34730cb2c742dae13948a701)): ?>
<?php $attributes = $__attributesOriginal6d74059c34730cb2c742dae13948a701; ?>
<?php unset($__attributesOriginal6d74059c34730cb2c742dae13948a701); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d74059c34730cb2c742dae13948a701)): ?>
<?php $component = $__componentOriginal6d74059c34730cb2c742dae13948a701; ?>
<?php unset($__componentOriginal6d74059c34730cb2c742dae13948a701); ?>
<?php endif; ?>
        </div>

        <div class="mt-4 grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="card card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="card-title">ON duration — last 14 days</h2>
                        <p class="card-subtitle">Known operating time derived from state transitions</p>
                    </div>
                    <span class="badge badge-info">Hours</span>
                </div>
                <div class="chart-box mt-3">
                    <?php
                        $usageChart = [
                            "type" => "bar",
                            "labels" => collect($usage["daily"])->pluck("label")->all(),
                            "datasets" => [[
                                "label" => "Known ON hours",
                                "data" => collect($usage["daily"])->map(fn ($row) => round($row["seconds"] / 3600, 2))->all(),
                                "color" => "#299ff2",
                                "fill" => false,
                            ]],
                            "showLegend" => false,
                            "tooltipSuffix" => "h",
                        ];
                    ?>
                    <canvas data-chart='<?php echo json_encode($usageChart, 15, 512) ?>'></canvas>
                </div>
                <p class="mt-2 text-[9.5px] leading-relaxed text-muted">No electricity consumption is inferred. ON duration is not kWh.</p>
            </div>

            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Daily breakdown</h2>
                        <p class="card-subtitle">14-day window</p>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table">
                        <thead><tr><th>Date</th><th>Known ON</th><th class="text-right">Share</th></tr></thead>
                        <tbody>
                            <?php $dailyTotal = max(1, collect($usage['daily'])->sum('seconds')); ?>
                            <?php $__currentLoopData = array_reverse($usage['daily']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="font-medium text-ink"><?php echo e(\Illuminate\Support\Carbon::parse($day['date'])->format('d M Y')); ?></td>
                                    <td><?php echo e(\App\Support\Format::duration($day['seconds'])); ?></td>
                                    <td class="text-right text-muted"><?php echo e(\App\Support\Format::percent($day['seconds'], $dailyTotal)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 card card-pad">
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                    <p class="filter-label">Unknown / stale time (30 days)</p>
                    <p class="mt-1 text-[18px] font-bold text-ink"><?php echo e(\App\Support\Format::duration($usage['month']['unknown_seconds'], true)); ?></p>
                    <p class="mt-0.5 text-[9.5px] text-muted">Not counted as OFF</p>
                </div>
                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                    <p class="filter-label">Incomplete days (30 days)</p>
                    <p class="mt-1 text-[18px] font-bold text-ink"><?php echo e($usage['month']['incomplete_rows']); ?></p>
                    <p class="mt-0.5 text-[9.5px] text-muted">Days with provisional intervals</p>
                </div>
                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                    <p class="filter-label">Analysis note</p>
                    <p class="mt-1 text-[10.5px] leading-relaxed text-ink-soft">ON duration reflects known state transitions. Gaps while the device was offline or the state was unknown are excluded from OFF time.</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="mt-4 grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">AC profile & configuration</h2>
                        <p class="card-subtitle">Identity, location and IR profile assignment</p>
                    </div>
                </div>
                <form method="POST" action="<?php echo e(route('acs.update', $ac)); ?>" class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 md:p-[18px]">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div>
                        <label class="label" for="edit-ac-name">Display name</label>
                        <input id="edit-ac-name" name="name" value="<?php echo e($ac->name); ?>" class="input" required maxlength="120">
                    </div>
                    <div>
                        <label class="label" for="edit-ac-code">Asset ID</label>
                        <input id="edit-ac-code" name="asset_code" value="<?php echo e($ac->asset_code); ?>" class="input" required maxlength="60">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="edit-ac-space">Space</label>
                        <select id="edit-ac-space" name="space_id" class="select" required>
                            <?php $__currentLoopData = $spaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $space): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($space->id); ?>" <?php if($space->id === $ac->space_id): echo 'selected'; endif; ?>><?php echo e($space->floor?->building?->site?->name); ?> · <?php echo e($space->floor?->building?->name); ?> · <?php echo e($space->floor?->name); ?> · <?php echo e($space->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="edit-ac-model">Manufacturer / model</label>
                        <select id="edit-ac-model" name="ac_model_id" class="select" required>
                            <?php $__currentLoopData = $acModels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($model->id); ?>" <?php if($model->id === $ac->ac_model_id): echo 'selected'; endif; ?>><?php echo e($model->manufacturer); ?> <?php echo e($model->model_name); ?> (<?php echo e($model->remote_variant); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="edit-ac-profile">IR profile version</label>
                        <select id="edit-ac-profile" name="ir_profile_version_id" class="select">
                            <option value="">No profile assigned</option>
                            <?php $__currentLoopData = $profileVersions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $version): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($version->id); ?>" <?php if($version->id === $ac->ir_profile_version_id): echo 'selected'; endif; ?>><?php echo e($version->profile?->acModel?->manufacturer); ?> <?php echo e($version->profile?->acModel?->model_name); ?> · v<?php echo e($version->version_number); ?> · <?php echo e(ucfirst($version->validation_status)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="edit-ac-serial">Serial number</label>
                        <input id="edit-ac-serial" name="serial_number" value="<?php echo e($ac->serial_number); ?>" class="input" maxlength="80">
                    </div>
                    <div>
                        <label class="label" for="edit-ac-installed">Installation date</label>
                        <input id="edit-ac-installed" name="installation_date" type="date" value="<?php echo e($ac->installation_date?->format('Y-m-d')); ?>" class="input">
                    </div>
                    <div>
                        <label class="label" for="edit-ac-status">Status</label>
                        <select id="edit-ac-status" name="status" class="select">
                            <?php $__currentLoopData = ['active', 'inactive', 'maintenance', 'retired']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>" <?php if($ac->status === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst($status)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="edit-ac-notes">Notes</label>
                        <textarea id="edit-ac-notes" name="notes" rows="2" class="input" maxlength="1000"><?php echo e($ac->notes); ?></textarea>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-divider pt-3 sm:col-span-2">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">IR capability profile</h2>
                            <p class="card-subtitle">Limits come from the assigned profile version</p>
                        </div>
                        <span class="badge <?php echo e($ac->irProfileVersion?->isValidated() ? 'badge-online' : 'badge-warning'); ?>"><?php echo e($ac->irProfileVersion?->isValidated() ? 'Validated' : 'Not validated'); ?></span>
                    </div>
                    <?php if($ac->irProfileVersion): ?>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 p-4 text-[11.5px] md:p-[18px]">
                            <div><dt class="text-muted">Profile</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e($ac->irProfileVersion->profile?->name ?? 'IR profile'); ?></dd></div>
                            <div><dt class="text-muted">Version</dt><dd class="mt-0.5 font-semibold text-ink">v<?php echo e($ac->irProfileVersion->version_number); ?></dd></div>
                            <div><dt class="text-muted">Temperature range</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e($ac->irProfileVersion->temperature_min_c); ?>–<?php echo e($ac->irProfileVersion->temperature_max_c); ?>°C</dd></div>
                            <div><dt class="text-muted">Step</dt><dd class="mt-0.5 font-semibold text-ink"><?php echo e((float) $ac->irProfileVersion->temperature_step_c); ?>°C</dd></div>
                        </dl>
                        <div class="flex flex-wrap gap-1.5 border-t border-divider px-4 py-2.5">
                            <?php $__currentLoopData = ($ac->irProfileVersion->capabilities ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $capability => $enabled): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge <?php echo e($enabled ? 'badge-online' : 'badge-unknown'); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $capability))); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'command','title' => 'No IR profile assigned','message' => 'Control is disabled until a validated IR profile version is attached to this AC.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'command','title' => 'No IR profile assigned','message' => 'Control is disabled until a validated IR profile version is attached to this AC.']); ?>
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
                    <?php endif; ?>
                </div>

                <div class="card card-pad">
                    <h2 class="text-[12.5px] font-semibold text-ink">Record</h2>
                    <dl class="mt-2.5 space-y-2 text-[11px]">
                        <div class="flex justify-between gap-2"><dt class="text-muted">Manufacturer</dt><dd class="font-medium text-ink"><?php echo e($ac->acModel?->manufacturer ?? '—'); ?></dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-muted">Model</dt><dd class="font-medium text-ink"><?php echo e($ac->acModel?->model_name ?? '—'); ?></dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-muted">Remote variant</dt><dd class="font-medium text-ink"><?php echo e($ac->acModel?->remote_variant ?? '—'); ?></dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-muted">Installed</dt><dd class="font-medium text-ink"><?php echo e($ac->installation_date ? $ac->installation_date->format('d M Y') : '—'); ?></dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-muted">Registered</dt><dd class="font-medium text-ink"><?php echo e(\App\Support\Format::datetime($ac->created_at, $timezone)); ?></dd></div>
                    </dl>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/acs/show.blade.php ENDPATH**/ ?>