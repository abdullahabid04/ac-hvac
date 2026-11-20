<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['state' => null, 'stale' => false, 'quality' => null, 'compact' => false]));

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

foreach (array_filter((['state' => null, 'stale' => false, 'quality' => null, 'compact' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    use App\Enums\PowerState;

    $power = $state instanceof PowerState ? $state : (is_string($state) ? PowerState::tryFrom($state) : null);
    $isUnknown = ! $power || $power === PowerState::Unknown;

    if ($stale && ! $isUnknown) {
        $label = 'Stale ' . $power->shortLabel();
        $class = 'badge-stale';
        $dot = 'bg-warning';
    } elseif ($power === PowerState::KnownOn) {
        $label = 'ON';
        $class = 'badge-on';
        $dot = 'bg-success';
    } elseif ($power === PowerState::KnownOff) {
        $label = 'OFF';
        $class = 'badge-off';
        $dot = 'bg-neutral';
    } elseif ($power === PowerState::Stale) {
        $label = 'Stale';
        $class = 'badge-stale';
        $dot = 'bg-warning';
    } else {
        $label = 'Unknown';
        $class = 'badge-unknown';
        $dot = 'bg-neutral/60';
    }
?>

<span <?php echo e($attributes->merge(['class' => "badge {$class}"])); ?> title="<?php echo e($quality ? 'State quality: '.$quality : ''); ?>">
    <span class="badge-dot <?php echo e($dot); ?>"></span>
    <?php echo e($compact ? $label : $label); ?>

    <?php if($quality === 'inferred'): ?>
        <span class="font-normal opacity-75">· inferred</span>
    <?php endif; ?>
</span>
<?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/components/status-badge.blade.php ENDPATH**/ ?>