<?php $__env->startSection('page-title', 'Usage & Analytics'); ?>
<?php $__env->startSection('page-subtitle', 'Known ON duration, setpoint trends and usage breakdowns — never kWh'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $queryBase = request()->except(['period', 'from', 'to', 'tab', 'page']);
        $periodUrl = fn (string $value) => route('analytics.index', array_merge($queryBase, ['period' => $value, 'tab' => $tab]));
        $tabUrl = fn (string $value) => route('analytics.index', array_merge(request()->except(['tab', 'page']), ['tab' => $value]));
        $periodLabel = match ($period) {
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            '30d' => 'Last 30 days',
            'custom' => 'Custom range',
            default => 'Last 7 days',
        };
        $setpointLabels = collect($setpointSeries)->map(fn ($row) => \Illuminate\Support\Carbon::parse($row['at'])->setTimezone($timezone)->format('d M H:i'))->all();
        $totalOn = max(1, $totals['on_seconds']);
        $tabs = [
            'overview' => 'Usage Overview',
            'setpoint' => 'Temperature Trend',
            'location' => 'Location-wise',
            'ac' => 'AC-wise',
        ];
    ?>

    <form method="GET" action="<?php echo e(route('analytics.index')); ?>" class="mb-4 space-y-3">
        <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="tab-list">
                <?php $__currentLoopData = ['today' => 'Today', 'yesterday' => 'Yesterday', '7d' => '7 days', '30d' => '30 days', 'custom' => 'Custom']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($value === 'custom'): ?>
                        <button type="submit" name="period" value="custom" class="tab <?php echo e($period === 'custom' ? 'tab-active' : ''); ?>"><?php echo e($label); ?></button>
                    <?php else: ?>
                        <a href="<?php echo e($periodUrl($value)); ?>" class="tab <?php echo e($period === $value ? 'tab-active' : ''); ?>"><?php echo e($label); ?></a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="flex flex-wrap items-end gap-2">
                <div>
                    <label class="label mb-1" for="analytics-site">Site</label>
                    <select id="analytics-site" name="site_id" class="select h-[34px] w-[130px] py-0 text-[11.5px]">
                        <option value="">All sites</option>
                        <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($site->id); ?>" <?php if($filters['site_id'] === $site->id): echo 'selected'; endif; ?>><?php echo e($site->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="label mb-1" for="analytics-building">Building</label>
                    <select id="analytics-building" name="building_id" class="select h-[34px] w-[130px] py-0 text-[11.5px]">
                        <option value="">All buildings</option>
                        <?php $__currentLoopData = $buildings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $building): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($building->id); ?>" <?php if($filters['building_id'] === $building->id): echo 'selected'; endif; ?>><?php echo e($building->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="label mb-1" for="analytics-floor">Floor</label>
                    <select id="analytics-floor" name="floor_id" class="select h-[34px] w-[115px] py-0 text-[11.5px]">
                        <option value="">All floors</option>
                        <?php $__currentLoopData = $floors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $floor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($floor->id); ?>" <?php if($filters['floor_id'] === $floor->id): echo 'selected'; endif; ?>><?php echo e($floor->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="label mb-1" for="analytics-ac">AC</label>
                    <select id="analytics-ac" name="ac_id" class="select h-[34px] w-[135px] py-0 text-[11.5px]">
                        <option value="">All ACs</option>
                        <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($unit->id); ?>" <?php if($filters['ac_id'] === $unit->id): echo 'selected'; endif; ?>><?php echo e($unit->asset_code); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="submit" name="period" value="<?php echo e($period); ?>" class="btn btn-primary btn-sm h-[34px]"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
                <a href="<?php echo e(route('analytics.index')); ?>" class="btn btn-ghost btn-sm h-[34px]">Reset</a>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-line bg-white px-3 py-2 shadow-sm">
            <div class="flex flex-wrap items-center gap-3 text-[11px] text-ink-soft">
                <span class="inline-flex items-center gap-1.5 font-semibold text-ink"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'calendar','size' => 13,'class' => 'text-blue-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar','size' => 13,'class' => 'text-blue-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> <?php echo e($periodLabel); ?></span>
                <span><?php echo e($from->format('d M Y')); ?> – <?php echo e($to->format('d M Y')); ?></span>
                <span class="text-muted"><?php echo e($timezone); ?></span>
                <span class="inline-flex items-center gap-1.5"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'ac','size' => 12,'class' => 'text-muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'ac','size' => 12,'class' => 'text-muted']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> <?php echo e($scopedAcCount); ?> AC<?php echo e($scopedAcCount === 1 ? '' : 's'); ?> in scope</span>
            </div>
            <div class="flex flex-wrap items-end gap-2">
                <div>
                    <label class="label mb-0.5" for="analytics-from">From</label>
                    <input id="analytics-from" type="date" name="from" value="<?php echo e($from->format('Y-m-d')); ?>" class="input h-[30px] w-[130px] py-0 text-[11px]">
                </div>
                <div>
                    <label class="label mb-0.5" for="analytics-to">To</label>
                    <input id="analytics-to" type="date" name="to" value="<?php echo e($to->format('Y-m-d')); ?>" class="input h-[30px] w-[130px] py-0 text-[11px]">
                </div>
                <button type="submit" name="period" value="custom" class="btn btn-secondary btn-sm h-[30px]">Custom range</button>
            </div>
        </div>
    </form>

    <div class="mb-4 flex items-center gap-1 overflow-x-auto rounded-xl border border-line bg-white p-1 shadow-sm">
        <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($tabUrl($key)); ?>" class="whitespace-nowrap rounded-lg px-3.5 py-1.5 text-[11.5px] font-semibold transition <?php echo e($tab === $key ? 'bg-blue-600 text-white shadow-[0_6px_14px_-10px_rgba(22,137,232,0.9)]' : 'text-ink-soft hover:bg-blue-50 hover:text-blue-700'); ?>"><?php echo e($label); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($totals['incomplete_rows'] > 0 || $totals['unknown_seconds'] > 0): ?>
        <div class="mb-4 flex flex-wrap items-center gap-2 rounded-xl border border-[#f6deac] bg-warning-soft/70 px-3.5 py-2 text-[11px] text-[#8a5a05]">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert','size' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','size' => 14]); ?>
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
            <span><strong><?php echo e(\App\Support\Format::duration($totals['unknown_seconds'], true)); ?></strong> of unknown/interrupted time and <strong><?php echo e($totals['incomplete_rows']); ?></strong> incomplete aggregate row<?php echo e($totals['incomplete_rows'] === 1 ? '' : 's'); ?> are in this range.</span>
            <span class="text-[10px]">Unknown time is never presented as OFF or as zero consumption.</span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
        <?php if (isset($component)) { $__componentOriginal6d74059c34730cb2c742dae13948a701 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d74059c34730cb2c742dae13948a701 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'Total ON duration','value' => \App\Support\Format::duration($totals['on_seconds'], true),'icon' => 'clock','tone' => 'blue','hint' => 'Known operating time · not kWh']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Total ON duration','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\Format::duration($totals['on_seconds'], true)),'icon' => 'clock','tone' => 'blue','hint' => 'Known operating time · not kWh']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'Average per AC','value' => \App\Support\Format::duration($averagePerAc, true),'icon' => 'analytics','tone' => 'cyan','hint' => 'Across '.e(max(1, $totals['ac_count'])).' reporting ACs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Average per AC','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\Format::duration($averagePerAc, true)),'icon' => 'analytics','tone' => 'cyan','hint' => 'Across '.e(max(1, $totals['ac_count'])).' reporting ACs']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'ACs in scope','value' => number_format($scopedAcCount),'icon' => 'ac','tone' => 'green','hint' => ''.e($totals['ac_count']).' have usage data']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'ACs in scope','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($scopedAcCount)),'icon' => 'ac','tone' => 'green','hint' => ''.e($totals['ac_count']).' have usage data']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'Average setpoint','value' => $averageSetpoint !== null ? $averageSetpoint.'°C' : 'Unknown','icon' => 'thermometer','tone' => 'amber','hint' => 'Target temperature, not room temperature']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Average setpoint','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($averageSetpoint !== null ? $averageSetpoint.'°C' : 'Unknown'),'icon' => 'thermometer','tone' => 'amber','hint' => 'Target temperature, not room temperature']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'Unknown / stale time','value' => \App\Support\Format::duration($totals['unknown_seconds'], true),'icon' => 'alert','tone' => 'red','hint' => 'Excluded from ON and OFF']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Unknown / stale time','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\Format::duration($totals['unknown_seconds'], true)),'icon' => 'alert','tone' => 'red','hint' => 'Excluded from ON and OFF']); ?>
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

    <?php if($tab === 'overview'): ?>
        <div class="mt-4 grid gap-4 xl:grid-cols-[1.35fr_0.65fr]">
            <div class="card card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="card-title">Daily ON duration</h2>
                        <p class="card-subtitle">Known operating hours · <?php echo e($periodLabel); ?></p>
                    </div>
                    <span class="badge badge-info">Hours</span>
                </div>
                <div class="chart-box mt-3">
                    <?php
                        $dailyChart = [
                            "type" => "bar",
                            "labels" => collect($daily)->pluck("label")->all(),
                            "datasets" => [[
                                "label" => "Known ON hours",
                                "data" => collect($daily)->map(fn ($row) => round($row["seconds"] / 3600, 2))->all(),
                                "color" => "#299ff2",
                                "fill" => false,
                            ]],
                            "showLegend" => false,
                            "tooltipSuffix" => "h",
                        ];
                    ?>
                    <canvas data-chart='<?php echo json_encode($dailyChart, 15, 512) ?>'></canvas>
                </div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-divider pt-2.5 text-[10px] text-muted">
                    <span>Total <?php echo e(\App\Support\Format::duration($totals['on_seconds'])); ?> across <?php echo e(count($daily)); ?> day<?php echo e(count($daily) === 1 ? '' : 's'); ?>.</span>
                    <span class="inline-flex items-center gap-1"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'info','size' => 10]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info','size' => 10]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> ON duration is derived from state transitions.</span>
                </div>
            </div>

            <div class="card card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="card-title">Usage by building</h2>
                        <p class="card-subtitle">Share of known ON duration</p>
                    </div>
                </div>
                <?php if($byBuilding->isEmpty()): ?>
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'building','title' => 'No usage data','message' => 'No known ON intervals fall within this period.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'building','title' => 'No usage data','message' => 'No known ON intervals fall within this period.']); ?>
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
                    <div class="chart-box-sm mt-2" style="height:185px">
                        <?php
                            $buildingsChart = [
                                "type" => "doughnut",
                                "labels" => $byBuilding->pluck("building_name")->all(),
                                "values" => $byBuilding->pluck("on_seconds")->all(),
                                "colors" => ["#1689e8", "#20b96b", "#f2a52b", "#8798a9", "#299ff2"],
                                "cutout" => "70%",
                            ];
                        ?>
                        <canvas data-chart='<?php echo json_encode($buildingsChart, 15, 512) ?>'></canvas>
                    </div>
                    <ul class="mt-1 space-y-1.5">
                        <?php $__currentLoopData = $byBuilding; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center gap-2 text-[10.5px]">
                                <span class="h-2 w-2 rounded-full" style="background: <?php echo e(['#1689e8', '#20b96b', '#f2a52b', '#8798a9', '#299ff2'][$index % 5]); ?>"></span>
                                <span class="flex-1 truncate text-ink-soft"><?php echo e($row->building_name); ?></span>
                                <span class="font-semibold text-ink"><?php echo e(\App\Support\Format::duration((int) $row->on_seconds, true)); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4 card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Location breakdown</h2>
                    <p class="card-subtitle">Building · floor · space, sorted by known ON duration</p>
                </div>
                <span class="badge badge-info"><?php echo e($bySpace->count()); ?> spaces</span>
            </div>
            <?php if($bySpace->isEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'locations','title' => 'No location usage yet','message' => 'Aggregates appear once devices report known state changes.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'locations','title' => 'No location usage yet','message' => 'Aggregates appear once devices report known state changes.']); ?>
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
                        <thead><tr><th>Location</th><th>Type</th><th>ACs</th><th>Known ON</th><th class="w-36">Share</th></tr></thead>
                        <tbody>
                            <?php $__currentLoopData = $bySpace; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span class="block font-semibold text-ink"><?php echo e($row->space_name); ?></span>
                                        <span class="block text-[9.5px] text-muted"><?php echo e($row->building_name); ?> · <?php echo e($row->floor_name); ?></span>
                                    </td>
                                    <td class="capitalize"><?php echo e($row->space_type); ?></td>
                                    <td><?php echo e($row->ac_count); ?></td>
                                    <td class="font-semibold text-ink"><?php echo e(\App\Support\Format::duration((int) $row->on_seconds)); ?></td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-[#edf3f8]">
                                                <div class="h-full rounded-full bg-blue-500" style="width: <?php echo e(min(100, ((int) $row->on_seconds / $totalOn) * 100)); ?>%"></div>
                                            </div>
                                            <span class="w-8 text-right text-[9.5px] text-muted"><?php echo e(\App\Support\Format::percent((int) $row->on_seconds, $totalOn)); ?></span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php elseif($tab === 'setpoint'): ?>
        <div class="mt-4 grid gap-4 xl:grid-cols-[1.3fr_0.7fr]">
            <div class="card card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="card-title">Setpoint trend</h2>
                        <p class="card-subtitle">Reported target temperatures · <?php echo e($periodLabel); ?></p>
                    </div>
                    <span class="badge badge-info">°C</span>
                </div>
                <?php if(count($setpointSeries) === 0): ?>
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'thermometer','title' => 'No setpoint history','message' => 'Setpoint changes reported by devices will appear here.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'thermometer','title' => 'No setpoint history','message' => 'Setpoint changes reported by devices will appear here.']); ?>
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
                    <div class="chart-box mt-3">
                        <?php
                            $setpointChart = [
                                "type" => "line",
                                "labels" => $setpointLabels,
                                "datasets" => [[
                                    "label" => "Setpoint",
                                    "data" => collect($setpointSeries)->pluck("value")->all(),
                                    "color" => "#1689e8",
                                    "fillColor" => "rgba(41,159,242,0.10)",
                                    "fill" => true,
                                ]],
                                "showLegend" => false,
                                "tooltipSuffix" => "°C",
                                "options" => ["scales" => ["y" => ["beginAtZero" => false]]],
                            ];
                        ?>
                        <canvas data-chart='<?php echo json_encode($setpointChart, 15, 512) ?>'></canvas>
                    </div>
                    <p class="mt-2 text-[9.5px] leading-relaxed text-muted">Setpoint is the AC’s target temperature, not measured room temperature. Gaps indicate missing reports, not 0°C.</p>
                <?php endif; ?>
            </div>

            <div class="space-y-4">
                <div class="card card-pad">
                    <h2 class="card-title">Average setpoint</h2>
                    <p class="mt-2 text-[38px] font-bold leading-none tracking-[-0.04em] text-ink"><?php echo e($averageSetpoint !== null ? $averageSetpoint.'°C' : 'Unknown'); ?></p>
                    <p class="mt-1 text-[10.5px] text-muted">Mean of <?php echo e(count($setpointSeries)); ?> valid reported value<?php echo e(count($setpointSeries) === 1 ? '' : 's'); ?></p>
                    <div class="mt-3 rounded-lg border border-line bg-[#fbfdff] px-3 py-2 text-[10px] leading-relaxed text-ink-soft">
                        Values outside the plausible 10–35°C band are excluded from the average. Unknown values are not replaced with zero.
                    </div>
                </div>
                <div class="card card-pad">
                    <h2 class="card-title">Setpoint data quality</h2>
                    <dl class="mt-2 space-y-2 text-[11px]">
                        <div class="flex justify-between gap-2"><dt class="text-muted">Samples</dt><dd class="font-semibold text-ink"><?php echo e(count($setpointSeries)); ?></dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-muted">Average</dt><dd class="font-semibold text-ink"><?php echo e($averageSetpoint !== null ? $averageSetpoint.'°C' : 'Unknown'); ?></dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-muted">Range</dt><dd class="font-semibold text-ink"><?php echo e($periodLabel); ?></dd></div>
                    </dl>
                </div>
            </div>
        </div>
    <?php elseif($tab === 'location'): ?>
        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">By building</h2>
                        <p class="card-subtitle">Known ON duration and reporting ACs</p>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table">
                        <thead><tr><th>Building</th><th>Site</th><th>ACs</th><th>Known ON</th></tr></thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $byBuilding; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="font-semibold text-ink"><?php echo e($row->building_name); ?></td>
                                    <td><?php echo e($row->site_name); ?></td>
                                    <td><?php echo e($row->ac_count); ?></td>
                                    <td class="font-semibold text-ink"><?php echo e(\App\Support\Format::duration((int) $row->on_seconds)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4" class="text-center text-muted">No building data in this range.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">By floor</h2>
                        <p class="card-subtitle">Building and floor breakdown</p>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table">
                        <thead><tr><th>Floor</th><th>Building</th><th>ACs</th><th>Known ON</th></tr></thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $byFloor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="font-semibold text-ink"><?php echo e($row->floor_name); ?></td>
                                    <td><?php echo e($row->building_name); ?></td>
                                    <td><?php echo e($row->ac_count); ?></td>
                                    <td class="font-semibold text-ink"><?php echo e(\App\Support\Format::duration((int) $row->on_seconds)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4" class="text-center text-muted">No floor data in this range.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">By space</h2>
                    <p class="card-subtitle">Space type and AC count</p>
                </div>
            </div>
            <div class="table-scroll">
                <table class="table">
                    <thead><tr><th>Space</th><th>Floor / building</th><th>Type</th><th>ACs</th><th>Known ON</th><th class="w-36">Share</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $bySpace; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="font-semibold text-ink"><?php echo e($row->space_name); ?></td>
                                <td><?php echo e($row->floor_name); ?> · <?php echo e($row->building_name); ?></td>
                                <td class="capitalize"><?php echo e($row->space_type); ?></td>
                                <td><?php echo e($row->ac_count); ?></td>
                                <td><?php echo e(\App\Support\Format::duration((int) $row->on_seconds)); ?></td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-[#edf3f8]"><div class="h-full rounded-full bg-blue-500" style="width: <?php echo e(min(100, ((int) $row->on_seconds / $totalOn) * 100)); ?>%"></div></div>
                                        <span class="w-8 text-right text-[9.5px] text-muted"><?php echo e(\App\Support\Format::percent((int) $row->on_seconds, $totalOn)); ?></span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="text-center text-muted">No space data in this range.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="mt-4 grid gap-4 xl:grid-cols-[1.4fr_0.6fr]">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Usage by air conditioner</h2>
                        <p class="card-subtitle">Top <?php echo e($byAc->count()); ?> ACs by known ON duration</p>
                    </div>
                    <a href="<?php echo e(route('acs.index')); ?>" class="btn btn-ghost btn-sm">Inventory</a>
                </div>
                <div class="table-scroll">
                    <table class="table">
                        <thead><tr><th>AC</th><th>Location</th><th>Known ON</th><th>Unknown</th><th>Notes</th></tr></thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $byAc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('acs.show', $row->id)); ?>" class="font-semibold text-ink hover:text-blue-700"><?php echo e($row->asset_code); ?></a>
                                        <span class="block text-[9.5px] text-muted"><?php echo e($row->name); ?></span>
                                    </td>
                                    <td>
                                        <span class="block max-w-[190px] truncate"><?php echo e($row->space_name); ?></span>
                                        <span class="block text-[9.5px] text-muted"><?php echo e($row->building_name); ?> · <?php echo e($row->site_name); ?></span>
                                    </td>
                                    <td class="font-semibold text-ink"><?php echo e(\App\Support\Format::duration((int) $row->on_seconds)); ?></td>
                                    <td class="text-muted"><?php echo e(\App\Support\Format::duration((int) $row->unknown_seconds)); ?></td>
                                    <td>
                                        <?php if((int) $row->incomplete_rows > 0): ?>
                                            <span class="badge badge-warning"><?php echo e($row->incomplete_rows); ?> incomplete</span>
                                        <?php else: ?>
                                            <span class="badge badge-online">Reported</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="5" class="text-center text-muted">No AC usage data in this range.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-4">
                <div class="card card-pad">
                    <h2 class="card-title">Most used location</h2>
                    <?php if($mostUsed): ?>
                        <p class="mt-2 text-[17px] font-bold text-ink"><?php echo e($mostUsed->space_name); ?></p>
                        <p class="text-[10.5px] text-muted"><?php echo e($mostUsed->floor_name); ?> · <?php echo e($mostUsed->building_name); ?></p>
                        <p class="mt-2 text-[24px] font-bold tracking-[-0.03em] text-blue-600"><?php echo e(\App\Support\Format::duration((int) $mostUsed->on_seconds, true)); ?></p>
                        <p class="text-[10px] text-muted">known ON duration</p>
                    <?php else: ?>
                        <p class="mt-2 text-[11px] text-muted">No location usage in this range.</p>
                    <?php endif; ?>
                </div>

                <div class="card card-pad">
                    <h2 class="card-title">Data caveats</h2>
                    <ul class="mt-2 space-y-1.5 text-[10px] leading-relaxed text-ink-soft">
                        <li>• ON duration is not electricity consumption.</li>
                        <li>• Unknown/stale gaps are excluded, not counted as OFF.</li>
                        <li>• Incomplete rows indicate provisional intervals.</li>
                        <li>• Physical remote actions may leave brief reporting gaps.</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <p class="mt-3 text-[9.5px] leading-relaxed text-muted">
        Analytics use aggregated known ON intervals. The system does not convert operating time to energy (kWh) because no power measurement is collected.
    </p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/analytics.blade.php ENDPATH**/ ?>