<?php $__env->startSection('page-title', 'Locations'); ?>
<?php $__env->startSection('page-subtitle', 'Browse and manage your organization’s site hierarchy'); ?>

<?php $__env->startSection('content'); ?>
    <div x-data="{ addOpen: false, addType: 'site', editOpen: false }">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <form method="GET" action="<?php echo e(route('locations.index')); ?>" class="flex flex-wrap items-center gap-2">
                <?php if($selectedType && $selectedId): ?>
                    <input type="hidden" name="type" value="<?php echo e($selectedType); ?>">
                    <input type="hidden" name="id" value="<?php echo e($selectedId); ?>">
                <?php endif; ?>
                <div class="relative">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'search','size' => 14,'class' => 'pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','size' => 14,'class' => 'pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted']); ?>
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
                    <input type="search" name="q" value="<?php echo e($q); ?>" placeholder="Search locations…" class="input h-[34px] w-[230px] pl-8 text-[11.5px]">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
                <?php if($q !== ''): ?>
                    <a href="<?php echo e(route('locations.index')); ?>" class="btn btn-ghost btn-sm">Clear</a>
                <?php endif; ?>
            </form>

            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('acs.index')); ?>" class="btn btn-secondary btn-sm">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'ac','size' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'ac','size' => 14]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Browse ACs
                </a>
                <button type="button" class="btn btn-primary btn-sm" @click="addOpen = true">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','size' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','size' => 14]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Add location
                </button>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-[310px_minmax(0,1fr)]">
            <aside class="card self-start overflow-hidden">
                <div class="card-header py-3">
                    <div>
                        <h2 class="card-title">Hierarchy</h2>
                        <p class="card-subtitle"><?php echo e($sites->count()); ?> site<?php echo e($sites->count() === 1 ? '' : 's'); ?> in scope</p>
                    </div>
                    <span class="badge badge-info">Tree</span>
                </div>

                <div class="max-h-[calc(100vh-245px)] overflow-y-auto px-2.5 py-3">
                    <?php $__empty_1 = true; $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <details open class="group/site mb-1">
                            <summary class="flex cursor-pointer list-none items-center gap-1.5 rounded-lg px-2 py-1.5 text-[12px] font-semibold transition hover:bg-blue-50 [&::-webkit-details-marker]:hidden <?php echo e($selectedType === 'site' && $selectedId === $site->id ? 'bg-blue-100 text-blue-700' : 'text-ink'); ?>">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-right','size' => 11,'class' => 'text-muted transition group-open/site:rotate-90']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','size' => 11,'class' => 'text-muted transition group-open/site:rotate-90']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'locations','size' => 13,'class' => 'text-blue-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'locations','size' => 13,'class' => 'text-blue-600']); ?>
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
                                <a href="<?php echo e(route('locations.index', ['type' => 'site', 'id' => $site->id])); ?>" class="min-w-0 flex-1 truncate hover:underline"><?php echo e($site->name); ?></a>
                                <span class="text-[9.5px] font-medium text-muted"><?php echo e($site->buildings->count()); ?></span>
                            </summary>

                            <div class="ml-4 border-l border-divider pl-1.5">
                                <?php $__empty_2 = true; $__currentLoopData = $site->buildings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $building): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <details open class="group/building">
                                        <summary class="flex cursor-pointer list-none items-center gap-1.5 rounded-md px-1.5 py-1 text-[11.5px] transition hover:bg-blue-50 [&::-webkit-details-marker]:hidden <?php echo e($selectedType === 'building' && $selectedId === $building->id ? 'bg-blue-100 font-semibold text-blue-700' : 'text-ink-soft'); ?>">
                                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-right','size' => 10,'class' => 'text-muted transition group-open/building:rotate-90']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','size' => 10,'class' => 'text-muted transition group-open/building:rotate-90']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'building','size' => 12,'class' => 'text-[#5f8fb8]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building','size' => 12,'class' => 'text-[#5f8fb8]']); ?>
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
                                            <a href="<?php echo e(route('locations.index', ['type' => 'building', 'id' => $building->id])); ?>" class="min-w-0 flex-1 truncate hover:underline"><?php echo e($building->name); ?></a>
                                        </summary>

                                        <div class="ml-4 border-l border-divider pl-1.5">
                                            <?php $__empty_3 = true; $__currentLoopData = $building->floors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $floor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_3 = false; ?>
                                                <details open class="group/floor">
                                                    <summary class="flex cursor-pointer list-none items-center gap-1.5 rounded-md px-1.5 py-1 text-[11px] transition hover:bg-blue-50 [&::-webkit-details-marker]:hidden <?php echo e($selectedType === 'floor' && $selectedId === $floor->id ? 'bg-blue-100 font-semibold text-blue-700' : 'text-ink-soft'); ?>">
                                                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-right','size' => 9,'class' => 'text-muted transition group-open/floor:rotate-90']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','size' => 9,'class' => 'text-muted transition group-open/floor:rotate-90']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'floor','size' => 11,'class' => 'text-[#7ba3c4]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'floor','size' => 11,'class' => 'text-[#7ba3c4]']); ?>
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
                                                        <a href="<?php echo e(route('locations.index', ['type' => 'floor', 'id' => $floor->id])); ?>" class="min-w-0 flex-1 truncate hover:underline"><?php echo e($floor->name); ?></a>
                                                    </summary>
                                                    <div class="ml-4 border-l border-divider pl-1">
                                                        <?php echo $__env->make('locations.partials.space-nodes', ['spaces' => $floor->spaces, 'selectedType' => $selectedType, 'selectedId' => $selectedId], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                    </div>
                                                </details>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_3): ?>
                                                <p class="px-2 py-1 text-[10px] italic text-muted">No floors</p>
                                            <?php endif; ?>
                                        </div>
                                    </details>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <p class="px-2 py-1 text-[10px] italic text-muted">No buildings</p>
                                <?php endif; ?>
                            </div>
                        </details>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'locations','title' => 'No locations found','message' => ''.e($q ? 'No site, building, floor or space matches your search.' : 'Add your first site to begin.').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'locations','title' => 'No locations found','message' => ''.e($q ? 'No site, building, floor or space matches your search.' : 'Add your first site to begin.').'']); ?>
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
            </aside>

            <div class="min-w-0 space-y-4">
                <?php if(! $selected): ?>
                    <div class="card">
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'locations','title' => 'Select a location','message' => 'Choose a site, building, floor or space from the hierarchy.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'locations','title' => 'Select a location','message' => 'Choose a site, building, floor or space from the hierarchy.']); ?>
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
<?php endif; ?> Add location</button>
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
                <?php else: ?>
                    <div class="card overflow-hidden">
                        <div class="card-pad">
                            <nav class="flex flex-wrap items-center gap-1 text-[10.5px] text-muted" aria-label="Breadcrumb">
                                <?php $__currentLoopData = $breadcrumb; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('locations.index', ['type' => $crumb['type'], 'id' => $crumb['id']])); ?>" class="hover:text-blue-700 hover:underline"><?php echo e($crumb['label']); ?></a>
                                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-right','size' => 10]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','size' => 10]); ?>
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
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <span class="font-semibold text-ink-soft"><?php echo e($selected->name); ?></span>
                            </nav>

                            <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h1 class="truncate text-[20px] font-bold tracking-[-0.025em] text-ink"><?php echo e($selected->name); ?></h1>
                                        <span class="badge badge-info"><?php echo e(ucfirst($selectedType)); ?></span>
                                        <?php if(isset($selected->status)): ?>
                                            <span class="badge <?php echo e($selected->status === 'active' ? 'badge-online' : 'badge-unknown'); ?>"><?php echo e(ucfirst($selected->status)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mt-1 text-[11.5px] text-ink-soft">
                                        <?php echo e(collect($breadcrumb)->pluck('label')->implode(' · ')); ?>

                                        <?php if($selectedType === 'space'): ?> · <?php echo e($selected->typeLabel()); ?> <?php endif; ?>
                                        <?php if($selectedType === 'building' && $selected->site): ?> · <?php echo e($selected->site->name); ?> <?php endif; ?>
                                    </p>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn btn-secondary btn-sm" @click="editOpen = true"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'edit','size' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit','size' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Edit</button>
                                    <form method="POST" action="<?php echo e(route('locations.destroy', ['locationType' => $selectedType, 'id' => $selectedId])); ?>" onsubmit="return confirm('Delete this <?php echo e($selectedType); ?>? This cannot be undone.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-ghost btn-sm text-danger hover:bg-danger-soft"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'trash','size' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','size' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Delete</button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-2.5 lg:grid-cols-4">
                                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                                    <p class="text-[10px] font-medium uppercase tracking-[0.05em] text-muted">Total ACs</p>
                                    <p class="mt-1 text-[20px] font-bold leading-none text-ink"><?php echo e($summary['total']); ?></p>
                                </div>
                                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                                    <p class="text-[10px] font-medium uppercase tracking-[0.05em] text-muted">Known ON</p>
                                    <p class="mt-1 flex items-center gap-1.5 text-[20px] font-bold leading-none text-[#0d8c4e]"><span class="mini-dot bg-success"></span><?php echo e($summary['on']); ?></p>
                                </div>
                                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                                    <p class="text-[10px] font-medium uppercase tracking-[0.05em] text-muted">Known OFF</p>
                                    <p class="mt-1 flex items-center gap-1.5 text-[20px] font-bold leading-none text-ink-soft"><span class="mini-dot bg-neutral"></span><?php echo e($summary['off']); ?></p>
                                </div>
                                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                                    <p class="text-[10px] font-medium uppercase tracking-[0.05em] text-muted">Today’s ON duration</p>
                                    <p class="mt-1 text-[20px] font-bold leading-none text-ink"><?php echo e(\App\Support\Format::duration($summary['today_seconds'], true)); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 2xl:grid-cols-[minmax(0,1fr)_300px]">
                        <div class="card overflow-hidden">
                            <div class="card-header">
                                <div>
                                    <h2 class="card-title">Spaces</h2>
                                    <p class="card-subtitle"><?php echo e($spaces->count()); ?> space<?php echo e($spaces->count() === 1 ? '' : 's'); ?> directly in this location</p>
                                </div>
                                <a href="<?php echo e(route('acs.index', ['space_id' => $selectedType === 'space' ? $selectedId : null, 'floor_id' => $selectedType === 'floor' ? $selectedId : null, 'building_id' => $selectedType === 'building' ? $selectedId : null, 'site_id' => $selectedType === 'site' ? $selectedId : null])); ?>" class="btn btn-ghost btn-sm">View ACs <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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

                            <?php if($spaces->isNotEmpty()): ?>
                                <div class="grid grid-cols-2 gap-2.5 p-3 sm:grid-cols-3 2xl:grid-cols-4">
                                    <?php $__currentLoopData = $spaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $space): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $spaceModel = $space['model']; ?>
                                        <a href="<?php echo e(route('locations.index', ['type' => 'space', 'id' => $spaceModel->id])); ?>" class="space-card <?php echo e($selectedType === 'space' && $selectedId === $spaceModel->id ? 'space-card-active' : ''); ?>">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="min-w-0">
                                                    <p class="truncate text-[12px] font-semibold text-ink"><?php echo e($spaceModel->name); ?></p>
                                                    <p class="text-[9.5px] text-muted"><?php echo e($spaceModel->typeLabel()); ?><?php if($spaceModel->floor): ?> · <?php echo e($spaceModel->floor->name); ?><?php endif; ?></p>
                                                </div>
                                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'space','size' => 13,'class' => 'text-[#8fb6d8]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'space','size' => 13,'class' => 'text-[#8fb6d8]']); ?>
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
                                            <div class="mt-2 flex items-center justify-between">
                                                <span class="text-[10px] text-ink-soft"><strong class="text-[13px] font-bold text-ink"><?php echo e($space['acs_count']); ?></strong> ACs</span>
                                                <span class="text-[10px] text-muted"><?php echo e(\App\Support\Format::duration($space['today_seconds'], true)); ?></span>
                                            </div>
                                            <div class="mt-2 flex flex-wrap items-center gap-1">
                                                <?php if($space['on'] > 0): ?><span class="badge badge-on"><?php echo e($space['on']); ?> ON</span><?php endif; ?>
                                                <?php if($space['off'] > 0): ?><span class="badge badge-off"><?php echo e($space['off']); ?> OFF</span><?php endif; ?>
                                                <?php if($space['unknown'] > 0): ?><span class="badge badge-unknown"><?php echo e($space['unknown']); ?> unknown</span><?php endif; ?>
                                                <?php if($space['acs_count'] === 0): ?><span class="badge badge-unknown">No ACs</span><?php endif; ?>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php elseif($acs->isNotEmpty()): ?>
                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                    <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $snapshot = $ac->stateSnapshot;
                                            $stale = $snapshot?->isStale() ?? true;
                                            $setpoint = $snapshot?->temperature_setpoint_c;
                                        ?>
                                        <a href="<?php echo e(route('acs.control', $ac)); ?>" class="card overflow-hidden transition hover:border-blue-300 hover:shadow-[0_10px_24px_-18px_rgba(8,117,201,0.4)] block">
                                            <div class="p-3">
                                                <div class="flex items-start justify-between gap-2 mb-2">
                                                    <div class="min-w-0">
                                                        <span class="block truncate text-[12px] font-bold text-ink"><?php echo e($ac->asset_code); ?></span>
                                                        <p class="block text-[10px] text-muted truncate"><?php echo e($ac->name); ?></p>
                                                    </div>
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
                                                </div>

                                                <div class="mt-1.5 flex items-center gap-1.5 text-[9.5px] text-ink-soft">
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

                                                <div class="mt-2 flex items-center justify-between">
                                                    <div class="text-left">
                                                        <p class="text-[9px] uppercase tracking-[0.05em] text-muted">Setpoint</p>
                                                        <p class="mt-0.5 text-[14px] font-bold text-ink"><?php echo e($setpoint !== null ? $setpoint.'°C' : '—'); ?></p>
                                                    </div>
                                                    <div class="text-left">
                                                        <p class="text-[9px] uppercase tracking-[0.05em] text-muted">Connectivity</p>
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
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'ac','title' => 'No Air Conditioners Installed','message' => 'There are currently no air conditioners assigned to this location.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'ac','title' => 'No Air Conditioners Installed','message' => 'There are currently no air conditioners assigned to this location.']); ?>
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

                        <aside class="space-y-4">
                            <div class="card card-pad">
                                <div class="flex items-start gap-2">
                                    <span class="icon-tile bg-blue-50 text-blue-600"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
                                        <h2 class="text-[12.5px] font-semibold text-ink">Physical remote policy</h2>
                                        <p class="text-[10px] text-muted">Nearest explicit setting wins</p>
                                    </div>
                                </div>
                                <?php if($policy): ?>
                                    <div class="mt-3 rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] uppercase tracking-[0.05em] text-muted">Effective</span>
                                            <span class="badge <?php echo e($policy['value']->value === 'enabled' ? 'badge-online' : 'badge-off'); ?>"><?php echo e($policy['value']->label()); ?></span>
                                        </div>
                                        <p class="mt-1.5 text-[10.5px] text-ink-soft">Source: <span class="font-semibold text-ink"><?php echo e($policy['source_label']); ?></span></p>
                                        <p class="text-[9.5px] text-muted">Version <?php echo e($policy['version']); ?></p>
                                    </div>

                                    <form method="POST" action="<?php echo e(route('settings.policy.update')); ?>" class="mt-3 space-y-2">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <input type="hidden" name="scope_type" value="<?php echo e($policyScope); ?>">
                                        <input type="hidden" name="scope_id" value="<?php echo e($policyScopeId); ?>">
                                        <label class="label mb-0" for="location-policy">Set for this <?php echo e($selectedType); ?></label>
                                        <select id="location-policy" name="policy_value" class="select text-[11.5px]">
                                            <option value="inherit">Inherit from parent</option>
                                            <option value="enabled">Enabled</option>
                                            <option value="disabled">Disabled</option>
                                        </select>
                                        <button type="submit" class="btn btn-secondary btn-sm w-full">Apply policy</button>
                                    </form>
                                <?php endif; ?>
                                <p class="mt-3 text-[9.5px] leading-relaxed text-muted">Software cannot physically block a handheld remote’s direct IR beam. Policy controls synchronization and reporting only.</p>
                            </div>

                            <div class="card card-pad">
                                <h2 class="text-[12.5px] font-semibold text-ink">Location details</h2>
                                <dl class="mt-2.5 space-y-2 text-[11px]">
                                    <div class="flex justify-between gap-2"><dt class="text-muted">Type</dt><dd class="font-medium text-ink"><?php echo e(ucfirst($selectedType)); ?></dd></div>
                                    <?php if(isset($selected->code) && $selected->code): ?>
                                        <div class="flex justify-between gap-2"><dt class="text-muted">Code</dt><dd class="font-medium text-ink"><?php echo e($selected->code); ?></dd></div>
                                    <?php endif; ?>
                                    <?php if($selectedType === 'site'): ?>
                                        <div class="flex justify-between gap-2"><dt class="text-muted">Timezone</dt><dd class="font-medium text-ink"><?php echo e($selected->timezone ?: $organization->timezone); ?></dd></div>
                                        <div class="flex justify-between gap-2"><dt class="text-muted">Address</dt><dd class="max-w-[150px] text-right font-medium text-ink"><?php echo e($selected->address ?: '—'); ?></dd></div>
                                    <?php endif; ?>
                                    <?php if($selectedType === 'space'): ?>
                                        <div class="flex justify-between gap-2"><dt class="text-muted">Parent</dt><dd class="font-medium text-ink"><?php echo e($selected->parent?->name ?? 'Floor level'); ?></dd></div>
                                        <div class="flex justify-between gap-2"><dt class="text-muted">Area</dt><dd class="font-medium text-ink"><?php echo e($selected->area_sqm ? $selected->area_sqm.' m²' : '—'); ?></dd></div>
                                    <?php endif; ?>
                                    <div class="flex justify-between gap-2"><dt class="text-muted">ACs</dt><dd class="font-medium text-ink"><?php echo e($acs->count()); ?></dd></div>
                                </dl>
                            </div>
                        </aside>
                    </div>

                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <div>
                                <h2 class="card-title">Air conditioners in this location</h2>
                                <p class="card-subtitle">Includes nested spaces</p>
                            </div>
                            <a href="<?php echo e(route('acs.index')); ?>" class="btn btn-ghost btn-sm">Full inventory</a>
                        </div>
                        <?php if($acs->isEmpty()): ?>
                            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'ac','title' => 'No air conditioners yet','message' => 'Register an AC and assign it to a space in this location.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'ac','title' => 'No air conditioners yet','message' => 'Register an AC and assign it to a space in this location.']); ?>
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
                                    <thead><tr><th>AC</th><th>Space</th><th>Model</th><th>State</th><th>Setpoint</th><th>Device</th><th></th></tr></thead>
                                    <tbody>
                                        <?php $__currentLoopData = $acs->take(12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><a href="<?php echo e(route('acs.show', $ac)); ?>" class="font-semibold text-ink hover:text-blue-700"><?php echo e($ac->asset_code); ?></a><span class="block text-[10px] text-muted"><?php echo e($ac->name); ?></span></td>
                                                <td><?php echo e($ac->space?->name ?? '—'); ?></td>
                                                <td><?php echo e($ac->acModel?->displayName() ?? '—'); ?></td>
                                                <td><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['state' => $ac->stateSnapshot?->power_state,'stale' => $ac->stateSnapshot?->isStale() ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ac->stateSnapshot?->power_state),'stale' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ac->stateSnapshot?->isStale() ?? true)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?></td>
                                                <td><?php echo e($ac->stateSnapshot?->temperature_setpoint_c !== null ? $ac->stateSnapshot->temperature_setpoint_c.'°C' : 'Unknown'); ?></td>
                                                <td><?php if (isset($component)) { $__componentOriginalb254a181568b9eef85fcdb4bf6b8eccf = $component; } ?>
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
<?php endif; ?></td>
                                                <td class="text-right"><a href="<?php echo e(route('acs.control', $ac)); ?>" class="btn btn-soft btn-sm">Control</a></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-navy-950/45 p-4 backdrop-blur-sm" x-show="addOpen" x-cloak x-transition.opacity>
            <div class="mt-8 w-full max-w-lg rounded-2xl border border-line bg-white shadow-2xl" @click.outside="addOpen = false" x-transition>
                <div class="flex items-center justify-between border-b border-divider px-5 py-3.5">
                    <div>
                        <h2 class="text-[15px] font-semibold text-ink">Add location</h2>
                        <p class="text-[10.5px] text-muted">Spaces can be nested under other spaces.</p>
                    </div>
                    <button type="button" class="btn btn-icon btn-ghost" @click="addOpen = false"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'close','size' => 16]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'close','size' => 16]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></button>
                </div>
                <form method="POST" action="<?php echo e(route('locations.store')); ?>" class="space-y-3 px-5 py-4">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label" for="add-location-type">Location type</label>
                            <select id="add-location-type" name="location_type" class="select" x-model="addType" required>
                                <option value="site">Site</option>
                                <option value="building">Building</option>
                                <option value="floor">Floor</option>
                                <option value="space">Space</option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="add-location-name">Name</label>
                            <input id="add-location-name" name="name" class="input" required maxlength="120" placeholder="e.g. Floor 2">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label" for="add-location-code">Code <span class="font-normal normal-case tracking-normal text-muted">(optional)</span></label>
                            <input id="add-location-code" name="code" class="input" maxlength="40" placeholder="e.g. HO-F2">
                        </div>
                        <div x-show="addType === 'floor'">
                            <label class="label" for="add-floor-number">Floor number</label>
                            <input id="add-floor-number" name="floor_number" type="number" class="input" min="-10" max="200">
                        </div>
                    </div>

                    <div x-show="addType === 'building'">
                        <label class="label" for="add-site-id">Parent site</label>
                        <select id="add-site-id" name="site_id" class="select" x-bind:disabled="addType !== 'building'">
                            <?php $__currentLoopData = $allSites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($site->id); ?>"><?php echo e($site->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div x-show="addType === 'floor'">
                        <label class="label" for="add-building-id">Parent building</label>
                        <select id="add-building-id" name="building_id" class="select" x-bind:disabled="addType !== 'floor'">
                            <?php $__currentLoopData = $allBuildings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $building): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($building->id); ?>"><?php echo e($building->site?->name); ?> · <?php echo e($building->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3" x-show="addType === 'space'">
                        <div>
                            <label class="label" for="add-floor-id">Parent floor</label>
                            <select id="add-floor-id" name="floor_id" class="select" x-bind:disabled="addType !== 'space'">
                                <?php $__currentLoopData = $allFloors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $floor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($floor->id); ?>"><?php echo e($floor->building?->name); ?> · <?php echo e($floor->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="add-space-type">Space type</label>
                            <select id="add-space-type" name="space_type" class="select" x-bind:disabled="addType !== 'space'">
                                <?php $__currentLoopData = \App\Models\Space::TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div x-show="addType === 'space'">
                        <label class="label" for="add-parent-space">Parent space <span class="font-normal normal-case tracking-normal text-muted">(optional, must be on the same floor)</span></label>
                        <select id="add-parent-space" name="parent_space_id" class="select" x-bind:disabled="addType !== 'space'">
                            <option value="">None — top-level space on floor</option>
                            <?php $__currentLoopData = $allSpaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $space): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($space->id); ?>"><?php echo e($space->floor?->building?->name); ?> · <?php echo e($space->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3" x-show="addType === 'space'">
                        <div>
                            <label class="label" for="add-area">Area (m²)</label>
                            <input id="add-area" name="area_sqm" type="number" step="0.01" min="0" class="input" x-bind:disabled="addType !== 'space'">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3" x-show="addType === 'site'">
                        <div>
                            <label class="label" for="add-timezone">Site timezone</label>
                            <input id="add-timezone" name="timezone" class="input" :value="'<?php echo e($organization->timezone); ?>'" x-bind:disabled="addType !== 'site'" placeholder="e.g. Asia/Karachi">
                        </div>
                        <div>
                            <label class="label" for="add-address">Address</label>
                            <input id="add-address" name="address" class="input" x-bind:disabled="addType !== 'site'">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-divider pt-3">
                        <button type="button" class="btn btn-secondary" @click="addOpen = false">Cancel</button>
                        <button type="submit" class="btn btn-primary"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','size' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','size' => 14]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Create location</button>
                    </div>
                </form>
            </div>
        </div>

        
        <?php if($selected): ?>
            <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-navy-950/45 p-4 backdrop-blur-sm" x-show="editOpen" x-cloak x-transition.opacity>
                <div class="mt-8 w-full max-w-lg rounded-2xl border border-line bg-white shadow-2xl" @click.outside="editOpen = false" x-transition>
                    <div class="flex items-center justify-between border-b border-divider px-5 py-3.5">
                        <div>
                            <h2 class="text-[15px] font-semibold text-ink">Edit <?php echo e($selectedType); ?></h2>
                            <p class="text-[10.5px] text-muted"><?php echo e($selected->name); ?></p>
                        </div>
                        <button type="button" class="btn btn-icon btn-ghost" @click="editOpen = false"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'close','size' => 16]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'close','size' => 16]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></button>
                    </div>
                    <form method="POST" action="<?php echo e(route('locations.update', ['locationType' => $selectedType, 'id' => $selectedId])); ?>" class="space-y-3 px-5 py-4">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label" for="edit-name">Name</label>
                                <input id="edit-name" name="name" value="<?php echo e($selected->name); ?>" class="input" required maxlength="120">
                            </div>
                            <div>
                                <label class="label" for="edit-code">Code</label>
                                <input id="edit-code" name="code" value="<?php echo e($selected->code); ?>" class="input" maxlength="40">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label" for="edit-status">Status</label>
                                <select id="edit-status" name="status" class="select">
                                    <option value="active" <?php if($selected->status === 'active'): echo 'selected'; endif; ?>>Active</option>
                                    <option value="inactive" <?php if($selected->status === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
                                </select>
                            </div>
                            <?php if($selectedType === 'floor'): ?>
                                <div>
                                    <label class="label" for="edit-floor-number">Floor number</label>
                                    <input id="edit-floor-number" name="floor_number" type="number" value="<?php echo e($selected->floor_number); ?>" class="input" min="-10" max="200">
                                </div>
                            <?php endif; ?>
                            <?php if($selectedType === 'space'): ?>
                                <div>
                                    <label class="label" for="edit-space-type">Space type</label>
                                    <select id="edit-space-type" name="space_type" class="select">
                                        <?php $__currentLoopData = \App\Models\Space::TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value); ?>" <?php if($selected->space_type === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if($selectedType === 'space'): ?>
                            <div>
                                <label class="label" for="edit-area">Area (m²)</label>
                                <input id="edit-area" name="area_sqm" type="number" step="0.01" value="<?php echo e($selected->area_sqm); ?>" class="input">
                            </div>
                        <?php endif; ?>
                        <?php if($selectedType === 'site'): ?>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="label" for="edit-timezone">Timezone</label>
                                    <input id="edit-timezone" name="timezone" value="<?php echo e($selected->timezone); ?>" class="input">
                                </div>
                                <div>
                                    <label class="label" for="edit-address">Address</label>
                                    <input id="edit-address" name="address" value="<?php echo e($selected->address); ?>" class="input">
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-end gap-2 border-t border-divider pt-3">
                            <button type="button" class="btn btn-secondary" @click="editOpen = false">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/locations.blade.php ENDPATH**/ ?>