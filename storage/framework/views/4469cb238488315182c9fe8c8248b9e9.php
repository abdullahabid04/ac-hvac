<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['status' => null, 'lastSeen' => null, 'suffix' => false]));

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

foreach (array_filter((['status' => null, 'lastSeen' => null, 'suffix' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    use App\Enums\ConnectivityStatus;

    $connectivity = $status instanceof ConnectivityStatus ? $status : ConnectivityStatus::tryFrom((string) $status);
    $connectivity ??= ConnectivityStatus::Unknown;

    $class = match ($connectivity) {
        ConnectivityStatus::Online => 'badge-online',
        ConnectivityStatus::Offline => 'badge-offline',
        default => 'badge-unknown',
    };
    $dot = match ($connectivity) {
        ConnectivityStatus::Online => 'bg-success',
        ConnectivityStatus::Offline => 'bg-danger',
        default => 'bg-neutral',
    };
?>

<span <?php echo e($attributes->merge(['class' => "badge {$class}"])); ?>>
    <span class="badge-dot <?php echo e($dot); ?>"></span>
    <?php echo e($connectivity->label()); ?>

    <?php if($suffix && $lastSeen): ?>
        <span class="font-normal opacity-75">· <?php echo e($lastSeen); ?></span>
    <?php endif; ?>
</span>
<?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/components/connectivity-badge.blade.php ENDPATH**/ ?>