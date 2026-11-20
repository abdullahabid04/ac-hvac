<?php $__env->startSection('page-title', 'Overview'); ?>
<?php $__env->startSection('page-subtitle', 'Real-time operational snapshot across your air conditioning estate'); ?>

<?php $__env->startSection('content'); ?>
    <form method="GET" action="<?php echo e(route('overview')); ?>" class="mb-4 flex flex-wrap items-center gap-2">
        <div class="flex items-center gap-2 rounded-xl border border-line bg-white px-2.5 py-1.5 shadow-sm">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'locations','size' => 14,'class' => 'text-muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'locations','size' => 14,'class' => 'text-muted']); ?>
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
            <select name="site_id" class="border-0 bg-transparent p-0 pr-6 text-[11.5px] font-semibold text-ink-soft outline-none focus:ring-0">
                <option value="">All sites</option>
                <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($site->id); ?>" <?php if($siteId === $site->id): echo 'selected'; endif; ?>><?php echo e($site->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex items-center gap-2 rounded-xl border border-line bg-white px-2.5 py-1.5 shadow-sm">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'calendar','size' => 14,'class' => 'text-muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar','size' => 14,'class' => 'text-muted']); ?>
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
            <select name="range" class="border-0 bg-transparent p-0 pr-6 text-[11.5px] font-semibold text-ink-soft outline-none focus:ring-0">
                <option value="1" <?php if($range === 1): echo 'selected'; endif; ?>>Today</option>
                <option value="7" <?php if($range === 7): echo 'selected'; endif; ?>>Last 7 days</option>
                <option value="30" <?php if($range === 30): echo 'selected'; endif; ?>>Last 30 days</option>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary btn-sm">Apply filters</button>
        <a href="<?php echo e(route('overview')); ?>" class="btn btn-ghost btn-sm">Reset</a>
        <span class="ml-auto hidden items-center gap-1.5 text-[10.5px] text-muted sm:flex">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Updated <?php echo e(now($organization->timezone ?: 'UTC')->format('H:i')); ?> · <?php echo e($organization->timezone ?: 'UTC'); ?>

        </span>
    </form>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
        <?php if (isset($component)) { $__componentOriginal6d74059c34730cb2c742dae13948a701 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d74059c34730cb2c742dae13948a701 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'Total ACs','value' => number_format($stats['total']),'icon' => 'ac','tone' => 'blue','hint' => $sites->count().' sites in scope','href' => route('acs.index', array_filter(['site_id' => $siteId]))]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Total ACs','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($stats['total'])),'icon' => 'ac','tone' => 'blue','hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sites->count().' sites in scope'),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('acs.index', array_filter(['site_id' => $siteId])))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'Currently ON','value' => number_format($stats['on']),'icon' => 'power','tone' => 'green','hint' => \App\Support\Format::percent($stats['on'], max(1, $stats['total'])).' of ACs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Currently ON','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($stats['on'])),'icon' => 'power','tone' => 'green','hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\Format::percent($stats['on'], max(1, $stats['total'])).' of ACs')]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'Known OFF','value' => number_format($stats['off']),'icon' => 'power','tone' => 'slate','hint' => \App\Support\Format::percent($stats['off'], max(1, $stats['total'])).' of ACs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Known OFF','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($stats['off'])),'icon' => 'power','tone' => 'slate','hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\Format::percent($stats['off'], max(1, $stats['total'])).' of ACs')]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'Offline devices','value' => number_format($stats['offline_devices']),'icon' => 'wifi-off','tone' => 'amber','hint' => ($stats['unknown_devices'] + $stats['stale']).' unknown / stale']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Offline devices','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($stats['offline_devices'])),'icon' => 'wifi-off','tone' => 'amber','hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($stats['unknown_devices'] + $stats['stale']).' unknown / stale')]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.metric-card','data' => ['label' => 'Today\'s ON Duration','value' => \App\Support\Format::duration($todayOnSeconds, true),'icon' => 'clock','tone' => 'cyan','hint' => 'Operating time, not kWh']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('metric-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Today\'s ON Duration','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\Format::duration($todayOnSeconds, true)),'icon' => 'clock','tone' => 'cyan','hint' => 'Operating time, not kWh']); ?>
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

    <?php if($stats['unknown'] > 0 || $stats['stale'] > 0): ?>
        <div class="mt-3 flex flex-wrap items-center gap-2 rounded-xl border border-[#f6deac] bg-warning-soft/70 px-3.5 py-2 text-[11px] text-[#8a5a05]">
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
            <span class="font-semibold"><?php echo e($stats['unknown'] + $stats['stale']); ?> AC<?php echo e(($stats['unknown'] + $stats['stale']) === 1 ? '' : 's'); ?></span>
            have unknown or stale state. These are <strong>not</strong> counted as OFF.
            <a href="<?php echo e(route('acs.index', ['state' => 'unknown'])); ?>" class="ml-auto font-semibold underline underline-offset-2">Review unknown states →</a>
        </div>
    <?php endif; ?>

    <div class="mt-4 grid gap-4 xl:grid-cols-[0.78fr_1.22fr]">
        <div class="card card-pad">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="card-title">AC state distribution</h2>
                    <p class="card-subtitle">Last reported state; stale is shown separately</p>
                </div>
                <a href="<?php echo e(route('acs.index')); ?>" class="btn btn-ghost btn-sm">Inventory <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
            <div class="mt-2 grid items-center gap-3 sm:grid-cols-[1.02fr_0.98fr]">
                <div class="chart-box-sm" style="height:170px">
                    <?php
                        $stateChart = [
                            "type" => "doughnut",
                            "labels" => collect($chart["state"])->pluck("label")->all(),
                            "values" => collect($chart["state"])->pluck("value")->all(),
                            "colors" => collect($chart["state"])->pluck("color")->all(),
                            "cutout" => "72%",
                        ];
                    ?>
                    <canvas data-chart='<?php echo json_encode($stateChart, 15, 512) ?>'></canvas>
                </div>
                <ul class="space-y-2.5">
                    <?php $__currentLoopData = $chart['state']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center gap-2 text-[11.5px]">
                            <span class="h-2 w-2 rounded-full" style="background: <?php echo e($slice['color']); ?>"></span>
                            <span class="flex-1 text-ink-soft"><?php echo e($slice['label']); ?></span>
                            <span class="font-semibold text-ink"><?php echo e($slice['value']); ?></span>
                            <span class="w-9 text-right text-[10px] text-muted"><?php echo e(\App\Support\Format::percent($slice['value'], max(1, $stats['total']))); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>

        <div class="card card-pad">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="card-title">Operating hours trend</h2>
                    <p class="card-subtitle">Known ON duration · last <?php echo e($range); ?> day<?php echo e($range === 1 ? '' : 's'); ?></p>
                </div>
                <a href="<?php echo e(route('analytics.index')); ?>" class="btn btn-ghost btn-sm">Analytics <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
            <div class="chart-box mt-3">
                <?php
                    $trendChart = [
                        "type" => "line",
                        "labels" => collect($series)->pluck("label")->all(),
                        "datasets" => [[
                            "label" => "Known ON hours",
                            "data" => collect($series)->map(fn ($point) => round($point["seconds"] / 3600, 1))->all(),
                            "color" => "#1689e8",
                            "fillColor" => "rgba(41,159,242,0.10)",
                            "fill" => true,
                        ]],
                        "showLegend" => false,
                        "tooltipSuffix" => "h",
                    ];
                ?>
                <canvas data-chart='<?php echo json_encode($trendChart, 15, 512) ?>'></canvas>
            </div>
        </div>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-[1.25fr_0.75fr]">
        <div class="card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">ACs by building</h2>
                    <p class="card-subtitle">Reported state and today’s known operating time</p>
                </div>
                <a href="<?php echo e(route('locations.index')); ?>" class="btn btn-ghost btn-sm">Locations <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
            <?php if($byBuilding->isEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'building','title' => 'No buildings in scope','message' => 'Create a site and building to start grouping air conditioners.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'building','title' => 'No buildings in scope','message' => 'Create a site and building to start grouping air conditioners.']); ?>
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
                            <tr><th>Building</th><th>Total</th><th>ON</th><th>OFF</th><th>Unknown</th><th>Today</th><th class="w-28">State mix</th></tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $byBuilding; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="font-semibold text-ink"><?php echo e($row['name']); ?></td>
                                    <td><?php echo e($row['total']); ?></td>
                                    <td class="text-[#0d8c4e]"><?php echo e($row['on']); ?></td>
                                    <td><?php echo e($row['off']); ?></td>
                                    <td class="text-muted"><?php echo e($row['unknown']); ?></td>
                                    <td class="font-semibold text-ink"><?php echo e(\App\Support\Format::duration($row['today_seconds'], true)); ?></td>
                                    <td>
                                        <div class="flex h-1.5 overflow-hidden rounded-full bg-[#edf3f8]">
                                            <?php if($row['on'] > 0): ?><span class="bg-success" style="width: <?php echo e(($row['on'] / max(1, $row['total'])) * 100); ?>%"></span><?php endif; ?>
                                            <?php if($row['off'] > 0): ?><span class="bg-neutral" style="width: <?php echo e(($row['off'] / max(1, $row['total'])) * 100); ?>%"></span><?php endif; ?>
                                            <?php if($row['unknown'] > 0): ?><span class="bg-[#c6d6e4]" style="width: <?php echo e(($row['unknown'] / max(1, $row['total'])) * 100); ?>%"></span><?php endif; ?>
                                        </div>
                                    </td>
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
                    <h2 class="card-title">Active alerts</h2>
                    <p class="card-subtitle">Unread warnings and alerts for you</p>
                </div>
                <a href="<?php echo e(route('notifications.index', ['tab' => 'alert'])); ?>" class="btn btn-ghost btn-sm">View all</a>
            </div>
            <?php $__empty_1 = true; $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e($alert->action_url ?: route('notifications.index')); ?>" class="flex gap-3 border-b border-divider px-4 py-3 transition last:border-0 hover:bg-blue-50/60">
                    <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full <?php echo e($alert->severity->value === 'alert' ? 'bg-danger-soft text-danger' : 'bg-warning-soft text-[#b77405]'); ?>">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $alert->severity->value === 'alert' ? 'alert' : 'info','size' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($alert->severity->value === 'alert' ? 'alert' : 'info'),'size' => 14]); ?>
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
                    <span class="min-w-0">
                        <span class="block truncate text-[12px] font-semibold text-ink"><?php echo e($alert->title); ?></span>
                        <span class="mt-0.5 block truncate text-[10.5px] text-ink-soft"><?php echo e($alert->acUnit?->locationLabel() ?? $alert->message); ?></span>
                        <span class="mt-1 block text-[9.5px] font-medium uppercase tracking-[0.05em] text-muted"><?php echo e($alert->occurred_at?->diffForHumans(short: true)); ?></span>
                    </span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'check-circle','title' => 'No active alerts','message' => 'Device and command alerts will appear here.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'check-circle','title' => 'No active alerts','message' => 'Device and command alerts will appear here.']); ?>
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
            <div class="border-t border-divider bg-[#fbfdff] px-4 py-2.5">
                <p class="text-[10px] leading-relaxed text-muted">Device online status does not prove an AC is running. IR transmission success does not prove physical execution.</p>
            </div>
        </div>
    </div>

    <div class="mt-4 card overflow-hidden">
        <div class="card-header">
            <div>
                <h2 class="card-title">Recent activity</h2>
                <p class="card-subtitle">Portal, physical remote, device and system events</p>
            </div>
            <a href="<?php echo e(route('activity.index')); ?>" class="btn btn-ghost btn-sm">Activity history <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
        <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex flex-wrap items-center gap-3 border-b border-divider px-4 py-2.5 last:border-0 sm:flex-nowrap">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                    <?php echo e($log->result === 'failure' ? 'bg-danger-soft text-danger' : ($log->source->value === 'physical_remote' ? 'bg-warning-soft text-[#b77405]' : ($log->result === 'success' ? 'bg-success-soft text-[#0d8c4e]' : 'bg-blue-50 text-blue-600'))); ?>">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $log->source->value === 'physical_remote' ? 'wifi' : ($log->result === 'failure' ? 'alert' : 'check'),'size' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($log->source->value === 'physical_remote' ? 'wifi' : ($log->result === 'failure' ? 'alert' : 'check')),'size' => 13]); ?>
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
                    <p class="truncate text-[12px] font-medium text-ink"><?php echo e($log->description); ?></p>
                    <p class="text-[10px] text-muted">
                        <?php echo e($log->acUnit?->asset_code ?? 'Organization'); ?>

                        <?php if($log->acUnit): ?> · <?php echo e($log->acUnit->locationLabel()); ?> <?php endif; ?>
                        · <?php echo e($log->source->label()); ?>

                    </p>
                </div>
                <span class="badge <?php echo e($log->result === 'failure' ? 'badge-danger' : ($log->result === 'pending' ? 'badge-warning' : 'badge-info')); ?>"><?php echo e(ucfirst($log->result ?? 'info')); ?></span>
                <span class="w-20 text-right text-[10px] text-muted"><?php echo e($log->occurred_at?->diffForHumans(short: true)); ?></span>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/dashboard.blade.php ENDPATH**/ ?>