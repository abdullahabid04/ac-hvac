@extends('layouts.app')

@section('page-title', 'Notifications')
@section('page-subtitle', 'Device, command and remote activity alerts for your account')

@section('content')
    @php
        $tabs = [
            'all' => 'All',
            'unread' => 'Unread',
            'alert' => 'Alerts',
            'warning' => 'Warnings',
            'info' => 'Information',
        ];
        $severityStyles = [
            'alert' => ['bg' => 'bg-danger-soft', 'text' => 'text-danger', 'badge' => 'badge-danger', 'icon' => 'alert'],
            'warning' => ['bg' => 'bg-warning-soft', 'text' => 'text-[#b77405]', 'badge' => 'badge-warning', 'icon' => 'alert'],
            'info' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'badge' => 'badge-info', 'icon' => 'info'],
        ];
    @endphp

    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
        <div class="tab-list">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('notifications.index', ['tab' => $key]) }}" class="tab {{ $tab === $key ? 'tab-active' : '' }}">
                    {{ $label }}
                    <span class="ml-1 text-[9.5px] {{ $tab === $key ? 'text-white/80' : 'text-muted' }}">{{ $counts[$key] }}</span>
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            @if ($counts['unread'] > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm"><x-icon name="check-circle" :size="13" /> Mark all read</button>
                </form>
            @endif
            <span class="badge {{ $counts['unread'] > 0 ? 'badge-warning' : 'badge-online' }}">{{ $counts['unread'] }} unread</span>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <div>
                <h2 class="card-title">{{ $tabs[$tab] }} notifications</h2>
                <p class="card-subtitle">Newest first · times shown in {{ $timezone }}</p>
            </div>
            <span class="badge badge-unknown">{{ number_format($notifications->total()) }} total</span>
        </div>

        @forelse ($notifications as $notification)
            @php $style = $severityStyles[$notification->severity->value] ?? $severityStyles['info']; @endphp
            <article class="flex items-start gap-3 border-b border-divider px-4 py-3.5 transition last:border-0 {{ $notification->read_at ? 'bg-white' : 'bg-[#fbfdff]' }}">
                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $style['bg'] }} {{ $style['text'] }}">
                    <x-icon :name="$style['icon']" :size="15" />
                </span>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-[12.5px] font-semibold {{ $notification->read_at ? 'text-ink-soft' : 'text-ink' }}">{{ $notification->title }}</h3>
                        <span class="badge {{ $style['badge'] }}">{{ $notification->severity->label() }}</span>
                        @if (! $notification->read_at)
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-[0.06em] text-blue-600"><span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span> Unread</span>
                        @endif
                    </div>
                    <p class="mt-1 text-[11.5px] leading-relaxed text-ink-soft">{{ $notification->message }}</p>
                    <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[9.5px] text-muted">
                        <span class="inline-flex items-center gap-1"><x-icon name="clock" :size="10" /> {{ \App\Support\Format::datetime($notification->occurred_at, $timezone) }} · {{ $notification->occurred_at?->diffForHumans(short: true) }}</span>
                        @if ($notification->acUnit)
                            <span class="inline-flex items-center gap-1"><x-icon name="ac" :size="10" /> {{ $notification->acUnit->asset_code }} · {{ $notification->acUnit->locationLabel() }}</span>
                        @endif
                        @if ($notification->device)
                            <span class="inline-flex items-center gap-1"><x-icon name="device" :size="10" /> {{ $notification->device->name }}</span>
                        @endif
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-1.5">
                    @if ($notification->action_url)
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                            @csrf
                            <input type="hidden" name="redirect" value="1">
                            <button type="submit" class="btn btn-ghost btn-sm">Open <x-icon name="arrow-right" :size="11" /></button>
                        </form>
                    @endif
                    @if (! $notification->read_at)
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm" title="Mark as read"><x-icon name="check" :size="12" /> Mark read</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <x-empty-state icon="bell" title="No notifications here" message="Device offline/reconnected events, command failures, stale state and remote activity will appear in this list." />
        @endforelse
    </div>

    @if ($notifications->hasPages())
        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif

    <p class="mt-3 text-[9.5px] leading-relaxed text-muted">
        Notifications describe detected events; they do not confirm physical AC state. Device online status and IR transmission success are separate facts.
    </p>
@endsection
