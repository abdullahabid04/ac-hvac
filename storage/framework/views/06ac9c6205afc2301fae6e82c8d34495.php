<?php $__env->startSection('page-title', 'Reports'); ?>
<?php $__env->startSection('page-subtitle', 'Generate and download operational reports — PDF or Excel-compatible CSV'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $tz = $organization->timezone ?: 'UTC';
        $defaultFrom = now($tz)->subDays(6)->toDateString();
        $defaultTo = now($tz)->toDateString();
        $typeDescriptions = [
            'daily_usage' => 'Day-by-day known ON duration across the selected scope.',
            'weekly_usage' => 'Week-by-week operating hours for the selected period.',
            'monthly_usage' => 'Month-by-month operating hours for the selected period.',
            'location_usage' => 'Operating hours grouped by building, floor and space.',
            'ac_history' => 'Chronological ON/OFF state changes reported by devices.',
            'setpoint_history' => 'Reported target-temperature changes over time.',
            'device_connectivity' => 'Device connectivity, last seen and remote policy state.',
        ];
        $typeIcons = [
            'daily_usage' => 'calendar',
            'weekly_usage' => 'analytics',
            'monthly_usage' => 'reports',
            'location_usage' => 'locations',
            'ac_history' => 'power',
            'setpoint_history' => 'thermometer',
            'device_connectivity' => 'device',
        ];
        $statusClass = fn ($status) => match ($status) {
            'completed' => 'badge-online',
            'failed' => 'badge-danger',
            default => 'badge-warning',
        };
    ?>

    <div x-data="{ type: 'daily_usage', format: 'pdf' }">
        <div class="mb-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <?php $__currentLoopData = $reportTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" @click="type = '<?php echo e($value); ?>'"
                        class="nav-card h-full flex-col !items-start gap-2 text-left"
                        :class="type === '<?php echo e($value); ?>' ? 'border-blue-500 ring-2 ring-blue-100' : ''">
                    <span class="flex w-full items-start justify-between gap-2">
                        <span class="icon-tile bg-blue-50 text-blue-600"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $typeIcons[$value] ?? 'reports','size' => 17]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($typeIcons[$value] ?? 'reports'),'size' => 17]); ?>
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
                        <span class="h-4 w-4 rounded-full border-2 transition" :class="type === '<?php echo e($value); ?>' ? 'border-blue-600 bg-blue-600 ring-2 ring-white ring-inset' : 'border-[#c9d8e6]'"></span>
                    </span>
                    <span class="block text-[12px] font-semibold text-ink"><?php echo e($label); ?></span>
                    <span class="block text-[10px] leading-relaxed text-muted"><?php echo e($typeDescriptions[$value] ?? 'Operational report.'); ?></span>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Generate report</h2>
                        <p class="card-subtitle">Reports are generated immediately and stored for 30 days</p>
                    </div>
                    <span class="badge badge-info" x-text="type.replaceAll('_', ' ')"></span>
                </div>

                <form method="POST" action="<?php echo e(route('reports.store')); ?>" class="space-y-3 p-4 md:p-[18px]">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="report_type" :value="type">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label" for="report-from">From</label>
                            <input id="report-from" type="date" name="from" value="<?php echo e(old('from', $defaultFrom)); ?>" class="input" required>
                        </div>
                        <div>
                            <label class="label" for="report-to">To</label>
                            <input id="report-to" type="date" name="to" value="<?php echo e(old('to', $defaultTo)); ?>" class="input" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label" for="report-site">Site <span class="font-normal normal-case tracking-normal text-muted">(optional)</span></label>
                            <select id="report-site" name="site_id" class="select">
                                <option value="">All sites in organization</option>
                                <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($site->id); ?>"><?php echo e($site->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="report-building">Building <span class="font-normal normal-case tracking-normal text-muted">(optional)</span></label>
                            <select id="report-building" name="building_id" class="select">
                                <option value="">All buildings</option>
                                <?php $__currentLoopData = $buildings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $building): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($building->id); ?>"><?php echo e($building->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="report-floor">Floor <span class="font-normal normal-case tracking-normal text-muted">(optional)</span></label>
                            <select id="report-floor" name="floor_id" class="select">
                                <option value="">All floors</option>
                                <?php $__currentLoopData = $floors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $floor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($floor->id); ?>"><?php echo e($floor->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="report-space">Space <span class="font-normal normal-case tracking-normal text-muted">(optional)</span></label>
                            <select id="report-space" name="space_id" class="select">
                                <option value="">All spaces</option>
                                <?php $__currentLoopData = $spaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $space): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($space->id); ?>"><?php echo e($space->floor?->building?->name); ?> · <?php echo e($space->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="label" for="report-ac">Air conditioner <span class="font-normal normal-case tracking-normal text-muted">(optional, narrows the report to one AC)</span></label>
                        <select id="report-ac" name="ac_id" class="select">
                            <option value="">All air conditioners in scope</option>
                            <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ac->id); ?>"><?php echo e($ac->asset_code); ?> · <?php echo e($ac->name); ?> — <?php echo e($ac->locationLabel()); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <span class="label">Format</span>
                        <div class="flex items-center gap-2">
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-line bg-white px-3 py-2 text-[11.5px] font-semibold text-ink-soft transition" :class="format === 'pdf' ? 'border-blue-500 bg-blue-50 text-blue-700' : ''">
                                <input type="radio" name="format" value="pdf" x-model="format" class="sr-only">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'reports','size' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'reports','size' => 14]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> PDF
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-line bg-white px-3 py-2 text-[11.5px] font-semibold text-ink-soft transition" :class="format === 'csv' ? 'border-blue-500 bg-blue-50 text-blue-700' : ''">
                                <input type="radio" name="format" value="csv" x-model="format" class="sr-only">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'export','size' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'export','size' => 14]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> CSV / Excel
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2 border-t border-divider pt-3">
                        <p class="max-w-[240px] text-[9.5px] leading-relaxed text-muted">Reports contain known ON duration, not energy consumption (kWh).</p>
                        <button type="submit" class="btn btn-primary"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'download','size' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'download','size' => 14]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Generate report</button>
                    </div>
                </form>
            </div>

            <div class="card card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="card-title">Report guidance</h2>
                        <p class="card-subtitle">What the numbers mean</p>
                    </div>
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'info','size' => 16,'class' => 'text-muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info','size' => 16,'class' => 'text-muted']); ?>
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
                <ul class="mt-3 space-y-2.5 text-[10.5px] leading-relaxed text-ink-soft">
                    <li class="flex gap-2"><span class="mt-[5px] h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400"></span><span><strong class="text-ink">ON duration</strong> is operating time derived from known ON/OFF transitions. It is not electricity consumption and is never converted to kWh.</span></li>
                    <li class="flex gap-2"><span class="mt-[5px] h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400"></span><span><strong class="text-ink">Unknown or stale</strong> intervals are excluded from totals and never treated as OFF time.</span></li>
                    <li class="flex gap-2"><span class="mt-[5px] h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400"></span><span><strong class="text-ink">Setpoint</strong> is the target temperature, not measured room temperature.</span></li>
                    <li class="flex gap-2"><span class="mt-[5px] h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400"></span><span><strong class="text-ink">IR transmission success</strong> is not physical confirmation that the AC changed state.</span></li>
                    <li class="flex gap-2"><span class="mt-[5px] h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400"></span><span><strong class="text-ink">Sample data</strong> in development is illustrative and is not production evidence.</span></li>
                </ul>
                <div class="mt-3 rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5 text-[10px] leading-relaxed text-muted">
                    Scope precedence for report filters is AC, then space/building/floor/site. Files are stored privately and access-checked against your organization.
                </div>
            </div>
        </div>

        <div class="mt-4 card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Generated reports</h2>
                    <p class="card-subtitle">Private downloads scoped to your organization</p>
                </div>
                <span class="badge badge-unknown"><?php echo e(number_format($reports->total())); ?> stored</span>
            </div>

            <?php if($reports->isEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'reports','title' => 'No reports generated yet','message' => 'Choose a report type above, set the period and scope, then generate a PDF or CSV.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'reports','title' => 'No reports generated yet','message' => 'Choose a report type above, set the period and scope, then generate a PDF or CSV.']); ?>
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
                            <tr><th>Report</th><th>Period</th><th>Scope</th><th>Format</th><th>Status</th><th>Requested by</th><th>Created</th><th class="text-right">Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $filters = $report->filters ?? [];
                                    if (isset($filters['ac_id'])) {
                                        $scopeLabel = 'AC selected';
                                    } elseif (isset($filters['space_id'])) {
                                        $scopeLabel = 'Space selected';
                                    } elseif (isset($filters['building_id'])) {
                                        $scopeLabel = 'Building selected';
                                    } elseif (isset($filters['floor_id'])) {
                                        $scopeLabel = 'Floor selected';
                                    } elseif (isset($filters['site_id'])) {
                                        $scopeLabel = 'Site selected';
                                    } else {
                                        $scopeLabel = 'Organization-wide';
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <span class="block font-semibold text-ink"><?php echo e($report->title); ?></span>
                                        <span class="block font-mono text-[9px] text-muted"><?php echo e(str($report->id)->substr(0, 8)); ?></span>
                                    </td>
                                    <td class="whitespace-nowrap text-[10.5px]">
                                        <?php if(! empty($filters['from']) && ! empty($filters['to'])): ?>
                                            <?php echo e(\Illuminate\Support\Carbon::parse($filters['from'])->format('d M Y')); ?> – <?php echo e(\Illuminate\Support\Carbon::parse($filters['to'])->format('d M Y')); ?>

                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($scopeLabel); ?></td>
                                    <td><span class="badge badge-info"><?php echo e(strtoupper($report->format)); ?></span></td>
                                    <td><span class="badge <?php echo e($statusClass($report->status)); ?>"><?php echo e(ucfirst($report->status)); ?></span></td>
                                    <td><?php echo e($report->requestedBy?->name ?? 'System'); ?></td>
                                    <td class="whitespace-nowrap text-[10.5px]"><?php echo e(\App\Support\Format::datetime($report->created_at, $tz)); ?></td>
                                    <td>
                                        <div class="flex items-center justify-end gap-1">
                                            <?php if($report->status === 'completed'): ?>
                                                <a href="<?php echo e(route('reports.download', $report)); ?>" class="btn btn-soft btn-sm"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'download','size' => 12]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'download','size' => 12]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Download</a>
                                            <?php endif; ?>
                                            <form method="POST" action="<?php echo e(route('reports.destroy', $report)); ?>" onsubmit="return confirm('Delete this report and its file?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-ghost btn-sm text-danger hover:bg-danger-soft" title="Delete report"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'trash','size' => 12]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','size' => 12]); ?>
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
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <?php if($reports->hasPages()): ?>
            <div class="mt-4"><?php echo e($reports->links()); ?></div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/reports.blade.php ENDPATH**/ ?>