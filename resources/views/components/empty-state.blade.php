@props(['icon' => 'search', 'title' => 'Nothing here yet', 'message' => null])

<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    <span class="empty-icon"><x-icon :name="$icon" :size="22" /></span>
    <h3 class="text-[14px] font-semibold text-ink">{{ $title }}</h3>
    @if ($message)
        <p class="max-w-sm text-[12px] leading-relaxed text-muted">{{ $message }}</p>
    @endif
    @if (isset($slot) && trim($slot) !== '')
        <div class="mt-1">{{ $slot }}</div>
    @endif
</div>
