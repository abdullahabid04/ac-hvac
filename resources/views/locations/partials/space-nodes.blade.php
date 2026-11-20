@foreach ($spaces as $space)
    <div x-data="{ open: true }">
        <div class="flex items-center gap-0.5">
            @if ($space->childrenRecursive->isNotEmpty())
                <button type="button" class="flex h-6 w-5 items-center justify-center rounded text-muted hover:bg-blue-50 hover:text-blue-600" @click="open = ! open" :aria-expanded="open">
                    <x-icon name="chevron-down" :size="11" x-show="open" />
                    <x-icon name="chevron-right" :size="11" x-show="! open" />
                </button>
            @else
                <span class="w-5"></span>
            @endif
            <a href="{{ route('locations.index', ['type' => 'space', 'id' => $space->id]) }}"
               class="flex min-w-0 flex-1 items-center gap-1.5 rounded-md px-1.5 py-1 text-[11.5px] transition hover:bg-blue-50 {{ $selectedType === 'space' && $selectedId === $space->id ? 'bg-blue-100 font-semibold text-blue-700' : 'text-ink-soft' }}">
                <x-icon name="space" :size="12" class="{{ $selectedType === 'space' && $selectedId === $space->id ? 'text-blue-600' : 'text-muted' }}" />
                <span class="truncate">{{ $space->name }}</span>
            </a>
        </div>
        @if ($space->childrenRecursive->isNotEmpty())
            <div class="ml-5 border-l border-divider pl-1" x-show="open" x-cloak>
                @include('locations.partials.space-nodes', ['spaces' => $space->childrenRecursive, 'selectedType' => $selectedType, 'selectedId' => $selectedId])
            </div>
        @endif
    </div>
@endforeach
