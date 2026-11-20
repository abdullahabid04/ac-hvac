<?php $__env->startSection('page-title', 'Air Conditioners'); ?>
<?php $__env->startSection('page-subtitle', 'Manage all air conditioners in your organization'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $currentView = $filters['view'];
        $gridUrl = request()->fullUrlWithQuery(['view' => 'grid', 'page' => null]);
        $tableUrl = request()->fullUrlWithQuery(['view' => 'table', 'page' => null]);
    ?>

    <script>
        window.__acsFilters = <?php echo \Illuminate\Support\Js::from([
            'q' => $filters['q'],
            'site_id' => $filters['site_id'],
            'building_id' => $filters['building_id'],
            'floor_id' => $filters['floor_id'],
            'space_id' => $filters['space_id'],
            'state' => $filters['state'],
            'connectivity' => $filters['connectivity'],
            'sites' => $sites,
            'buildings' => $buildings,
            'floors' => $floors,
            'spaces' => $spaces,
            'currentView' => $currentView,
            'routeUrl' => route('acs.index'),
            'gridUrl' => $gridUrl,
            'tableUrl' => $tableUrl,
        ])->toHtml() ?>;
    </script>

    <div x-data="acFilters">
        <div class="mb-3 flex flex-wrap items-end justify-between gap-2">
            <form class="flex flex-1 flex-wrap items-end gap-2" @submit.prevent="fetchResults">
                <input type="hidden" name="view" :value="currentView">
                <div class="relative min-w-[220px] flex-1">
                    <label class="label" for="ac-search">Search</label>
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
                    <input id="ac-search" type="search" name="q" x-model="q" @input="debounceSearch" class="input h-[34px] pl-8 text-[12px]" placeholder="Search by AC name, ID, model or location…">
                </div>
                <div>
                    <label class="label" for="filter-site">Site</label>
                    <select id="filter-site" name="site_id" x-model="site_id" @change="building_id = null; floor_id = null; space_id = null; fetchResults()" class="select h-[34px] w-[135px] py-0 text-[11.5px]">
                        <option value="">All sites</option>
                        <template x-for="site in sites" :key="site.id">
                            <option x-bind:value="site.id" x-text="site.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="label" for="filter-building">Building</label>
                    <select id="filter-building" name="building_id" x-model="building_id" @change="floor_id = null; space_id = null; fetchResults()" class="select h-[34px] w-[135px] py-0 text-[11.5px]" :disabled="! site_id">
                        <option value="">All buildings</option>
                        <template x-for="building in filteredBuildings" :key="building.id">
                            <option x-bind:value="building.id" x-text="building.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="label" for="filter-floor">Floor</label>
                    <select id="filter-floor" name="floor_id" x-model="floor_id" @change="space_id = null; fetchResults()" class="select h-[34px] w-[120px] py-0 text-[11.5px]" :disabled="! building_id">
                        <option value="">All floors</option>
                        <template x-for="floor in filteredFloors" :key="floor.id">
                            <option x-bind:value="floor.id" x-text="floor.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="label" for="filter-space">Space</label>
                    <select id="filter-space" name="space_id" x-model="space_id" @change="fetchResults" class="select h-[34px] w-[130px] py-0 text-[11.5px]" :disabled="! floor_id">
                        <option value="">All spaces</option>
                        <template x-for="space in filteredSpaces" :key="space.id">
                            <option x-bind:value="space.id" x-text="space.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="label" for="filter-state">State</label>
                    <select id="filter-state" name="state" x-model="state" @change="fetchResults" class="select h-[34px] w-[112px] py-0 text-[11.5px]">
                        <option value="">All states</option>
                        <option value="on">Known ON</option>
                        <option value="off">Known OFF</option>
                        <option value="unknown">Unknown</option>
                        <option value="stale">Stale</option>
                    </select>
                </div>
                <div>
                    <label class="label" for="filter-connectivity">Device</label>
                    <select id="filter-connectivity" name="connectivity" x-model="connectivity" @change="fetchResults" class="select h-[34px] w-[112px] py-0 text-[11.5px]">
                        <option value="">All devices</option>
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                        <option value="unknown">Unknown</option>
                    </select>
                </div>
                <button type="button" class="btn btn-ghost btn-sm h-[34px]" @click="resetFilters">Reset Filters</button>
            </form>

            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1 rounded-lg border border-line bg-white p-0.5 sm:flex">
                    <button type="button" @click="currentView = 'grid'; fetchResults()" :class="currentView === 'grid' ? 'bg-blue-600 text-white' : 'text-muted hover:text-blue-700'" class="rounded-md p-1.5" title="Grid view"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'grid','size' => 15]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'grid','size' => 15]); ?>
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
                    <button type="button" @click="currentView = 'table'; fetchResults()" :class="currentView === 'table' ? 'bg-blue-600 text-white' : 'text-muted hover:text-blue-700'" class="rounded-md p-1.5" title="List view"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'list','size' => 15]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'list','size' => 15]); ?>
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
                <button type="button" class="btn btn-secondary btn-sm h-[34px]" onclick="window.print()"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'export','size' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'export','size' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Export</button>
                <button type="button" class="btn btn-primary btn-sm h-[34px]" @click="addOpen = true"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Add AC</button>
            </div>
        </div>

        <div id="results-container">
            <?php echo $__env->make('acs.partials.results', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-navy-950/45 p-4 backdrop-blur-sm" x-show="addOpen" x-cloak x-transition.opacity>
            <div class="mt-6 w-full max-w-2xl rounded-2xl border border-line bg-white shadow-2xl" @click.outside="addOpen = false" x-transition>
                <div class="flex items-center justify-between border-b border-divider px-5 py-3.5">
                    <div>
                        <h2 class="text-[15px] font-semibold text-ink">Register air conditioner</h2>
                        <p class="text-[10.5px] text-muted">A validated IR profile is required before control is enabled.</p>
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
                <form method="POST" action="<?php echo e(route('acs.store')); ?>" class="grid grid-cols-1 gap-3 px-5 py-4 sm:grid-cols-2">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="label" for="new-ac-name">Display name</label>
                        <input id="new-ac-name" name="name" class="input" required maxlength="120" placeholder="e.g. Room 204 AC">
                    </div>
                    <div>
                        <label class="label" for="new-ac-code">Asset ID</label>
                        <input id="new-ac-code" name="asset_code" class="input" required maxlength="60" placeholder="e.g. AC-204-01">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="new-ac-space">Space</label>
                        <select id="new-ac-space" name="space_id" class="select" required>
                            <option value="">Select a space…</option>
                            <?php $__currentLoopData = $spaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $space): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($space->id); ?>"><?php echo e($space->floor?->building?->site?->name); ?> · <?php echo e($space->floor?->building?->name); ?> · <?php echo e($space->floor?->name); ?> · <?php echo e($space->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="new-ac-model">Manufacturer / model</label>
                        <select id="new-ac-model" name="ac_model_id" class="select" required>
                            <option value="">Select a model…</option>
                            <?php $__currentLoopData = $acModels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($model->id); ?>"><?php echo e($model->manufacturer); ?> <?php echo e($model->model_name); ?> (<?php echo e($model->remote_variant); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="new-ac-profile">IR profile version</label>
                        <select id="new-ac-profile" name="ir_profile_version_id" class="select">
                            <option value="">No profile assigned</option>
                            <?php $__currentLoopData = $profileVersions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $version): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($version->id); ?>"><?php echo e($version->profile?->acModel?->manufacturer); ?> <?php echo e($version->profile?->acModel?->model_name); ?> · v<?php echo e($version->version_number); ?> · <?php echo e(ucfirst($version->validation_status)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="new-ac-serial">Serial number</label>
                        <input id="new-ac-serial" name="serial_number" class="input" maxlength="80">
                    </div>
                    <div>
                        <label class="label" for="new-ac-installed">Installation date</label>
                        <input id="new-ac-installed" name="installation_date" type="date" class="input">
                    </div>
                    <div>
                        <label class="label" for="new-ac-status">Status</label>
                        <select id="new-ac-status" name="status" class="select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="retired">Retired</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="new-ac-notes">Notes</label>
                        <textarea id="new-ac-notes" name="notes" rows="2" class="input" maxlength="1000"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-divider pt-3 sm:col-span-2">
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
<?php endif; ?> Register AC</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/acs/index.blade.php ENDPATH**/ ?>