@props(['state' => null, 'stale' => false, 'quality' => null, 'compact' => false])

@php
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
@endphp

<span {{ $attributes->merge(['class' => "badge {$class}"]) }} title="{{ $quality ? 'State quality: '.$quality : '' }}">
    <span class="badge-dot {{ $dot }}"></span>
    {{ $compact ? $label : $label }}
    @if ($quality === 'inferred')
        <span class="font-normal opacity-75">· inferred</span>
    @endif
</span>
