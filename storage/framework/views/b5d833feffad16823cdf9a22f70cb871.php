<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name' => 'dot', 'size' => 18, 'stroke' => 1.8]));

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

foreach (array_filter((['name' => 'dot', 'size' => 18, 'stroke' => 1.8]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $paths = [
        'dashboard' => '<rect x="3" y="3" width="7" height="8" rx="1.6"/><rect x="14" y="3" width="7" height="5" rx="1.6"/><rect x="14" y="12" width="7" height="9" rx="1.6"/><rect x="3" y="15" width="7" height="6" rx="1.6"/>',
        'locations' => '<path d="M12 21s7-5.1 7-11a7 7 0 1 0-14 0c0 5.9 7 11 7 11Z"/><circle cx="12" cy="10" r="2.6"/>',
        'ac' => '<rect x="3" y="5" width="18" height="10" rx="2.5"/><path d="M7 11h10M7 8.5h4"/><path d="M7 18c1.2-1.2 2.4-1.2 3.6 0M13.4 18c1.2-1.2 2.4-1.2 3.6 0"/>',
        'snowflake' => '<path d="M12 2v20M4.2 6.5l15.6 11M19.8 6.5l-15.6 11"/><path d="m9 4 3 2 3-2M9 20l3-2 3 2M4.5 10.5l1.2-3.4M18.3 16.9l1.2-3.4M19.5 10.5l-1.2-3.4M5.7 16.9l-1.2-3.4"/>',
        'analytics' => '<path d="M4 20V9M10 20V4M16 20v-7M22 20H2"/>',
        'activity' => '<path d="M3 12h4l2.2-6L13 18l2.2-6H21"/>',
        'reports' => '<path d="M7 3h7l4 4v14H7z"/><path d="M14 3v5h5M10 13h6M10 17h6"/>',
        'bell' => '<path d="M18 8a6 6 0 1 0-12 0c0 6-2 7-2 7h16s-2-1-2-7"/><path d="M10.3 20a2 2 0 0 0 3.4 0"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1 1.55V21a2 2 0 1 1-4 0v-.09A1.7 1.7 0 0 0 8.9 19.3a1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.7 15a1.7 1.7 0 0 0-1.55-1H3a2 2 0 1 1 0-4h.09A1.7 1.7 0 0 0 4.7 9a1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.7h.08A1.7 1.7 0 0 0 10.1 3.15V3a2 2 0 1 1 4 0v.09a1.7 1.7 0 0 0 1 1.55 1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.3 9v.08a1.7 1.7 0 0 0 1.55 1.02H21a2 2 0 1 1 0 4h-.09A1.7 1.7 0 0 0 19.4 15Z"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'minus' => '<path d="M5 12h14"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'chevron-right' => '<path d="m9 6 6 6-6 6"/>',
        'chevron-left' => '<path d="m15 6-6 6 6 6"/>',
        'arrow-left' => '<path d="M19 12H5M11 18l-6-6 6-6"/>',
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'power' => '<path d="M12 3v9"/><path d="M6.4 6.4a8 8 0 1 0 11.2 0"/>',
        'thermometer' => '<path d="M14 14.8V5a2 2 0 1 0-4 0v9.8a4 4 0 1 0 4 0Z"/><path d="M12 9v7"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'wifi' => '<path d="M5 12.5a10 10 0 0 1 14 0M8.5 16a5.5 5.5 0 0 1 7 0M12 19.5h.01"/>',
        'wifi-off' => '<path d="m3 3 18 18M8.5 16a5.5 5.5 0 0 1 7 0M5 12.5a10 10 0 0 1 4-2.4M15 10.1a10 10 0 0 1 4 2.4M12 19.5h.01"/>',
        'alert' => '<path d="M10.3 3.9 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4M12 17h.01"/>',
        'check' => '<path d="m20 6-11 11-5-5"/>',
        'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.5 2.5 5-5"/>',
        'close' => '<path d="m6 6 12 12M18 6 6 18"/>',
        'device' => '<rect x="6" y="2.5" width="12" height="19" rx="2.5"/><path d="M10 6h4M10 18h4"/><circle cx="12" cy="10" r="1.2"/>',
        'filter' => '<path d="M4 5h16l-6 7v6l-4 2v-8Z"/>',
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.4"/><rect x="14" y="3" width="7" height="7" rx="1.4"/><rect x="3" y="14" width="7" height="7" rx="1.4"/><rect x="14" y="14" width="7" height="7" rx="1.4"/>',
        'list' => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
        'download' => '<path d="M12 3v12M7 10l5 5 5-5M4 21h16"/>',
        'trash' => '<path d="M4 7h16M9 7V4h6v3M6 7l1 14h10l1-14M10 11v6M14 11v6"/>',
        'edit' => '<path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16Z"/><path d="m13.5 6.5 4 4"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'logout' => '<path d="M10 4H5v16h5M15 8l4 4-4 4M9 12h10"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'building' => '<path d="M4 21V5l7-2v18M11 21h9V9l-9-4"/><path d="M7.5 8h.01M7.5 12h.01M7.5 16h.01M15 11h.01M15 15h.01"/>',
        'floor' => '<path d="M3 20h18M5 20V8l7-5 7 5v12"/><path d="M9 20v-5h6v5"/>',
        'space' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 4v16M8 10h13"/>',
        'link' => '<path d="M10 13a5 5 0 0 0 7 0l2-2a5 5 0 0 0-7-7l-1 1"/><path d="M14 11a5 5 0 0 0-7 0l-2 2a5 5 0 0 0 7 7l1-1"/>',
        'refresh' => '<path d="M20 6v5h-5M4 18v-5h5"/><path d="M6.1 9A7 7 0 0 1 19 8M4 16a7 7 0 0 0 13 1"/>',
        'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/>',
        'export' => '<path d="M12 15V3M8 7l4-4 4 4M5 13v6h14v-6"/>',
        'sparkles' => '<path d="m12 3 1.6 4.4L18 9l-4.4 1.6L12 15l-1.6-4.4L6 9l4.4-1.6ZM19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8Z"/>',
        'more' => '<circle cx="5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/>',
        'copy' => '<rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/>',
        'shield' => '<path d="M12 3 4 6v6c0 5 8 9 8 9s8-4 8-9V6Z"/><path d="m9 12 2 2 4-4"/>',
        'flame' => '<path d="M12 22c4 0 7-3 7-7 0-5-5-6-5-12-2 2-3 4-3 6-1-1-2-2-2-4-2 2-4 5-4 10 0 4 3 7 7 7Z"/>',
        'command' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="m7 9 3 3-3 3M13 15h4"/>',
        'dot' => '<circle cx="12" cy="12" r="4"/>',
    ];
?>

<svg <?php echo e($attributes->merge(['class' => 'shrink-0'])); ?> width="<?php echo e($size); ?>" height="<?php echo e($size); ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="<?php echo e($stroke); ?>" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <?php echo $paths[$name] ?? $paths['dot']; ?>

</svg>
<?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/components/icon.blade.php ENDPATH**/ ?>