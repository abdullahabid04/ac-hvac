@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mb-4 flex flex-wrap items-start justify-between gap-3']) }}>
    <div class="min-w-0">
        <h1 class="page-title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if (isset($actions))
        <div class="flex flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
