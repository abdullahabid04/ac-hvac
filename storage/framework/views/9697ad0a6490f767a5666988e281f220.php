<?php $__env->startSection('page-title', 'Activity History'); ?>
<?php $__env->startSection('page-subtitle', 'Chronological record of portal, physical remote, device and system events'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $sourceIcon = fn ($source) => match ($source?->value) {
            'physical_remote' => 'wifi',
            'device' => 'device',
            'system' => 'settings',
            default => 'user',
        };
        $resultClass = fn ($result) => match ($result) {
            'failure' => 'badge-danger',
            'pending' => 'badge-warning',
            'warning' => 'badge-warning',
            'success' => 'badge-online',
            default => 'badge-info',
        };
    ?>

    <form method="GET" action="<?php echo e(route('activity.index')); ?>" class="card mb-4 p-3.5">
        <div class="flex flex-wrap items-end gap-2">
            <div class="relative min-w-[220px] flex-1">
                <label class="label" for="activity-search">Search</label>
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'search','size' => 14,'class' => 'pointer-events-none absolute bottom-[10px] left-3 text-muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','size' => 14,'class' => 'pointer-events-none absolute bottom-[10px] left-3 text-muted']); ?>
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
                <input id="activity-search" type="search" name="q" value="<?php echo e($filters['q']); ?>" class="input h-[34px] pl-8 text-[12px]" placeholder="Search action, description, AC name or asset ID…">
            </div>
            <div>
                <label class="label" for="activity-source">Source</label>
                <select id="activity-source" name="source" class="select h-[34px] w-[145px] py-0 text-[11.5px]">
                    <option value="">All sources</option>
                    <?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($source->value); ?>" <?php if($filters['source'] === $source->value): echo 'selected'; endif; ?>><?php echo e($source->label()); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label" for="activity-result">Result</label>
                <select id="activity-result" name="result" class="select h-[34px] w-[120px] py-0 text-[11.5px]">
                    <option value="">All results</option>
                    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($result); ?>" <?php if($filters['result'] === $result): echo 'selected'; endif; ?>><?php echo e(ucfirst($result)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label" for="activity-site">Site</label>
                <select id="activity-site" name="site_id" class="select h-[34px] w-[130px] py-0 text-[11.5px]">
                    <option value="">All sites</option>
                    <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($site->id); ?>" <?php if($filters['site_id'] === $site->id): echo 'selected'; endif; ?>><?php echo e($site->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label" for="activity-ac">AC</label>
                <select id="activity-ac" name="ac_id" class="select h-[34px] w-[140px] py-0 text-[11.5px]">
                    <option value="">All ACs</option>
                    <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ac->id); ?>" <?php if($filters['ac_id'] === $ac->id): echo 'selected'; endif; ?>><?php echo e($ac->asset_code); ?> · <?php echo e($ac->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label" for="activity-from">From</label>
                <input id="activity-from" type="date" name="from" value="<?php echo e($filters['from']); ?>" class="input h-[34px] w-[135px] py-0 text-[11.5px]">
            </div>
            <div>
                <label class="label" for="activity-to">To</label>
                <input id="activity-to" type="date" name="to" value="<?php echo e($filters['to']); ?>" class="input h-[34px] w-[135px] py-0 text-[11.5px]">
            </div>
            <button type="submit" class="btn btn-primary btn-sm h-[34px]"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'filter','size' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'filter','size' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Apply</button>
            <a href="<?php echo e(route('activity.index')); ?>" class="btn btn-ghost btn-sm h-[34px]">Reset</a>
        </div>
    </form>

    <div class="mb-3 flex flex-wrap items-center justify-between gap-2 text-[11px] text-muted">
        <p>
            <span class="font-semibold text-ink"><?php echo e(number_format($activity->total())); ?></span> event<?php echo e($activity->total() === 1 ? '' : 's'); ?>

            <?php if($filters['q'] !== ''): ?> · matching “<?php echo e($filters['q']); ?>” <?php endif; ?>
            <?php if($filters['source'] !== ''): ?> · <?php echo e(ucfirst(str_replace('_', ' ', $filters['source']))); ?> <?php endif; ?>
            <?php if($filters['result'] !== ''): ?> · <?php echo e($filters['result']); ?> <?php endif; ?>
        </p>
        <p class="inline-flex items-center gap-1"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'clock','size' => 12]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock','size' => 12]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Times shown in <?php echo e($timezone); ?></p>
    </div>

    <div class="card overflow-hidden">
        <?php if($activity->isEmpty()): ?>
            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'activity','title' => 'No activity matches','message' => 'Adjust the filters or date range to find events. Commands, remote actions and device reports are recorded automatically.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'activity','title' => 'No activity matches','message' => 'Adjust the filters or date range to find events. Commands, remote actions and device reports are recorded automatically.']); ?>
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
                    <thead>
                        <tr>
                            <th class="w-[145px]">Timestamp</th>
                            <th>AC</th>
                            <th>Location</th>
                            <th>Action</th>
                            <th>Source</th>
                            <th>Result</th>
                            <th>Actor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $activity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <span class="block font-medium text-ink"><?php echo e(\App\Support\Format::datetime($log->occurred_at, $timezone)); ?></span>
                                    <span class="block text-[9.5px] text-muted"><?php echo e($log->occurred_at?->diffForHumans(short: true)); ?></span>
                                </td>
                                <td>
                                    <?php if($log->acUnit): ?>
                                        <a href="<?php echo e(route('acs.show', $log->acUnit)); ?>" class="font-semibold text-ink hover:text-blue-700"><?php echo e($log->acUnit->asset_code); ?></a>
                                        <span class="block text-[9.5px] text-muted"><?php echo e($log->acUnit->name); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Organization</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="block max-w-[210px] truncate"><?php echo e($log->acUnit?->locationLabel() ?? '—'); ?></span>
                                </td>
                                <td>
                                    <span class="block max-w-[340px] font-medium text-ink"><?php echo e($log->description); ?></span>
                                    <span class="block font-mono text-[9px] text-muted"><?php echo e($log->action); ?></span>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-1.5 text-[11px]">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full <?php echo e($log->source === \App\Enums\EventSource::PhysicalRemote ? 'bg-warning-soft text-[#b77405]' : 'bg-blue-50 text-blue-600'); ?>">
                                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $sourceIcon($log->source),'size' => 12]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sourceIcon($log->source)),'size' => 12]); ?>
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
                                        <?php echo e($log->source->label()); ?>

                                    </span>
                                </td>
                                <td><span class="badge <?php echo e($resultClass($log->result)); ?>"><?php echo e(ucfirst($log->result ?? 'info')); ?></span></td>
                                <td>
                                    <span class="block text-[11px]"><?php echo e($log->user?->name ?? '—'); ?></span>
                                    <?php if($log->device): ?>
                                        <span class="block text-[9.5px] text-muted"><?php echo e($log->device->name); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php if($activity->hasPages()): ?>
        <div class="mt-4"><?php echo e($activity->links()); ?></div>
    <?php endif; ?>

    <p class="mt-3 text-[9.5px] leading-relaxed text-muted">
        Physical remote events are observed and reported by the ESP32. A restricted remote action means software did not synchronize it — the direct IR beam may still have reached the AC. IR transmission success is not physical confirmation.
    </p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/activity.blade.php ENDPATH**/ ?>