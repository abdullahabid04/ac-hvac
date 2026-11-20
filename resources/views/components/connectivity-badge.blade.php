@props(['status' => null, 'lastSeen' => null, 'suffix' => false])

@php
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
@endphp

<span {{ $attributes->merge(['class' => "badge {$class}"]) }}>
    <span class="badge-dot {{ $dot }}"></span>
    {{ $connectivity->label() }}
    @if ($suffix && $lastSeen)
        <span class="font-normal opacity-75">· {{ $lastSeen }}</span>
    @endif
</span>
