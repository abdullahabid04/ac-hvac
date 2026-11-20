<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label', 'value', 'icon' => 'dashboard', 'tone' => 'blue', 'hint' => null, 'href' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['label', 'value', 'icon' => 'dashboard', 'tone' => 'blue', 'hint' => null, 'href' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $tones = [
        'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
        'green' => ['bg' => 'bg-success-soft', 'text' => 'text-[#0d8c4e]'],
        'amber' => ['bg' => 'bg-warning-soft', 'text' => 'text-[#b77405]'],
        'red' => ['bg' => 'bg-danger-soft', 'text' => 'text-danger'],
        'slate' => ['bg' => 'bg-neutral-soft', 'text' => 'text-neutral'],
        'cyan' => ['bg' => 'bg-ice-100', 'text' => 'text-blue-500'],
    ];
    $palette = $tones[$tone] ?? $tones['blue'];
?>

<?php if($href): ?>
    <a href="<?php echo e($href); ?>" class="stat-card group flex items-center gap-3.5 transition hover:-translate-y-0.5 hover:border-blue-300">
        <span class="stat-icon <?php echo e($palette['bg']); ?> <?php echo e($palette['text']); ?>">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $icon,'size' => 19]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'size' => 19]); ?>
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
            <span class="block text-[11.5px] font-medium text-ink-soft"><?php echo e($label); ?></span>
            <span class="kpi-value mt-1 block text-[23px]"><?php echo e($value); ?></span>
            <?php if($hint): ?><span class="mt-0.5 block text-[10.5px] text-muted"><?php echo e($hint); ?></span><?php endif; ?>
        </span>
    </a>
<?php else: ?>
    <div class="stat-card flex items-center gap-3.5">
        <span class="stat-icon <?php echo e($palette['bg']); ?> <?php echo e($palette['text']); ?>">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $icon,'size' => 19]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'size' => 19]); ?>
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
            <span class="block text-[11.5px] font-medium text-ink-soft"><?php echo e($label); ?></span>
            <span class="kpi-value mt-1 block text-[23px]"><?php echo e($value); ?></span>
            <?php if($hint): ?><span class="mt-0.5 block text-[10.5px] text-muted"><?php echo e($hint); ?></span><?php endif; ?>
        </span>
    </div>
<?php endif; ?>
<?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/components/metric-card.blade.php ENDPATH**/ ?>