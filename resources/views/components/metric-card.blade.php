@props(['label', 'value', 'icon' => 'dashboard', 'tone' => 'blue', 'hint' => null, 'href' => null])

@php
    $tones = [
        'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
        'green' => ['bg' => 'bg-success-soft', 'text' => 'text-[#0d8c4e]'],
        'amber' => ['bg' => 'bg-warning-soft', 'text' => 'text-[#b77405]'],
        'red' => ['bg' => 'bg-danger-soft', 'text' => 'text-danger'],
        'slate' => ['bg' => 'bg-neutral-soft', 'text' => 'text-neutral'],
        'cyan' => ['bg' => 'bg-ice-100', 'text' => 'text-blue-500'],
    ];
    $palette = $tones[$tone] ?? $tones['blue'];
@endphp

@if ($href)
    <a href="{{ $href }}" class="stat-card group flex items-center gap-3.5 transition hover:-translate-y-0.5 hover:border-blue-300">
        <span class="stat-icon {{ $palette['bg'] }} {{ $palette['text'] }}">
            <x-icon :name="$icon" :size="19" />
        </span>
        <span class="min-w-0">
            <span class="block text-[11.5px] font-medium text-ink-soft">{{ $label }}</span>
            <span class="kpi-value mt-1 block text-[23px]">{{ $value }}</span>
            @if ($hint)<span class="mt-0.5 block text-[10.5px] text-muted">{{ $hint }}</span>@endif
        </span>
    </a>
@else
    <div class="stat-card flex items-center gap-3.5">
        <span class="stat-icon {{ $palette['bg'] }} {{ $palette['text'] }}">
            <x-icon :name="$icon" :size="19" />
        </span>
        <span class="min-w-0">
            <span class="block text-[11.5px] font-medium text-ink-soft">{{ $label }}</span>
            <span class="kpi-value mt-1 block text-[23px]">{{ $value }}</span>
            @if ($hint)<span class="mt-0.5 block text-[10.5px] text-muted">{{ $hint }}</span>@endif
        </span>
    </div>
@endif
