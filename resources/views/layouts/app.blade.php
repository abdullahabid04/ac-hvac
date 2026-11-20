<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light">
    <title>@yield('page-title', 'Overview') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="app-shell" x-data="{ sidebarOpen: false }">
    <div class="fixed inset-0 z-30 bg-navy-950/45 backdrop-blur-[1px] lg:hidden" x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false"></div>

    <aside class="sidebar" :class="sidebarOpen ? 'is-open' : ''" aria-label="Primary navigation">
        <div class="sidebar-brand">
            <span class="brand-mark text-white">
                <x-icon name="snowflake" :size="22" :stroke="1.7" />
            </span>
            <span class="min-w-0">
                <span class="block text-[13.5px] font-semibold leading-tight text-white">AC Management</span>
                <span class="block text-[10px] font-medium uppercase tracking-[0.14em] text-[#8fb6d8]">Manager Portal</span>
            </span>
        </div>

        <nav class="mt-1 flex-1 space-y-0.5 overflow-y-auto px-3 pb-4">
            @php
                $nav = [
                    ['route' => 'overview', 'label' => 'Overview', 'icon' => 'dashboard', 'active' => request()->routeIs('overview')],
                    ['route' => 'locations.index', 'label' => 'Locations', 'icon' => 'locations', 'active' => request()->routeIs('locations.*')],
                    ['route' => 'acs.index', 'label' => 'Air Conditioners', 'icon' => 'ac', 'active' => request()->routeIs('acs.*', 'acs.show', 'acs.control')],
                    ['route' => 'analytics.index', 'label' => 'Usage & Analytics', 'icon' => 'analytics', 'active' => request()->routeIs('analytics.*')],
                    ['route' => 'activity.index', 'label' => 'Activity History', 'icon' => 'activity', 'active' => request()->routeIs('activity.*')],
                    ['route' => 'reports.index', 'label' => 'Reports', 'icon' => 'reports', 'active' => request()->routeIs('reports.*')],
                    ['route' => 'notifications.index', 'label' => 'Notifications', 'icon' => 'bell', 'active' => request()->routeIs('notifications.*'), 'badge' => $shellUnreadCount ?? 0],
                    ['route' => 'settings.index', 'label' => 'Settings', 'icon' => 'settings', 'active' => request()->routeIs('settings.*')],
                ];
            @endphp

            @foreach ($nav as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link {{ $item['active'] ? 'sidebar-link-active' : '' }}">
                    <x-icon :name="$item['icon']" :size="17" :stroke="1.7" />
                    <span class="flex-1">{{ $item['label'] }}</span>
                    @if (! empty($item['badge']))
                        <span class="rounded-full bg-[#f2a52b] px-1.5 py-0.5 text-[9px] font-bold leading-none text-[#3d2a00]">{{ $item['badge'] > 99 ? '99+' : $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="border-t border-white/10 px-4 py-3">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/12 text-[11px] font-semibold text-white">
                    {{ strtoupper(substr($shellUser?->name ?? 'U', 0, 1)) }}
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-[11.5px] font-semibold text-white">{{ $shellUser?->name ?? 'Guest' }}</span>
                    <span class="block truncate text-[10px] text-[#8fb6d8]">{{ $shellOrganization?->name ?? 'No organization' }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md p-1.5 text-[#8fb6d8] transition hover:bg-white/10 hover:text-white" title="Sign out">
                        <x-icon name="logout" :size="15" />
                    </button>
                </form>
            </div>
            @if (app()->environment('local'))
                <a href="{{ route('dev.simulator') }}" class="mt-2.5 flex items-center justify-between rounded-lg border border-white/10 bg-white/5 px-2.5 py-1.5 text-[9.5px] font-semibold uppercase tracking-[0.08em] text-[#9cc3e2] hover:bg-white/10">
                    <span>Developer simulator</span>
                    <x-icon name="chevron-right" :size="11" />
                </a>
            @endif
        </div>
    </aside>

    <div class="main-area">
        <header class="topbar">
            <button type="button" class="btn btn-icon btn-secondary lg:hidden" @click="sidebarOpen = true" aria-label="Open navigation">
                <x-icon name="menu" :size="18" />
            </button>

            <div class="min-w-0 flex-1">
                <h1 class="truncate text-[21px] font-bold leading-tight tracking-[-0.025em] text-ink md:text-[23px]">@yield('page-title', 'Overview')</h1>
                <p class="mt-0.5 hidden truncate text-[11.5px] text-ink-soft sm:block">@yield('page-subtitle', 'Organization-wide air conditioning operations')</p>
            </div>

            <div class="flex items-center gap-2">
                @if (app()->environment('local'))
                    <span class="hidden items-center gap-1.5 rounded-full border border-[#f6deac] bg-warning-soft px-2.5 py-1 text-[10px] font-semibold text-[#96640b] md:inline-flex" title="Seed and simulator data are illustrative, not production evidence.">
                        <span class="mini-dot bg-warning"></span> Sample data
                    </span>
                @endif

                <a href="{{ route('notifications.index') }}" class="btn btn-icon btn-secondary relative" aria-label="Notifications">
                    <x-icon name="bell" :size="17" />
                    @if (($shellUnreadCount ?? 0) > 0)
                        <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger px-1 text-[9px] font-bold text-white">{{ $shellUnreadCount > 9 ? '9+' : $shellUnreadCount }}</span>
                    @endif
                </a>

                <div class="relative" x-data="{ profileOpen: false }" @click.outside="profileOpen = false">
                    <button type="button" class="flex h-9 items-center gap-2 rounded-full border border-line bg-white pl-1 pr-2.5 text-left transition hover:border-blue-300" @click="profileOpen = ! profileOpen">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-navy-900 text-[10.5px] font-bold text-white">{{ strtoupper(substr($shellUser?->name ?? 'U', 0, 1)) }}</span>
                        <span class="hidden text-[11.5px] font-semibold text-ink md:block">{{ str($shellUser?->name ?? 'User')->before(' ') }}</span>
                        <x-icon name="chevron-down" :size="12" class="text-muted" />
                    </button>

                    <div class="absolute right-0 z-40 mt-2 w-56 overflow-hidden rounded-xl border border-line bg-white p-1.5 shadow-xl" x-show="profileOpen" x-cloak x-transition.origin.top.right>
                        <div class="border-b border-divider px-2.5 py-2">
                            <p class="truncate text-[12px] font-semibold text-ink">{{ $shellUser?->name }}</p>
                            <p class="truncate text-[10.5px] text-muted">{{ $shellUser?->email }}</p>
                        </div>
                        <a href="{{ route('settings.index') }}" class="mt-1 flex items-center gap-2 rounded-lg px-2.5 py-2 text-[12px] text-ink-soft hover:bg-blue-50 hover:text-blue-700">
                            <x-icon name="user" :size="14" /> Account & settings
                        </a>
                        <a href="{{ route('settings.index') }}#remote-policy" class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-[12px] text-ink-soft hover:bg-blue-50 hover:text-blue-700">
                            <x-icon name="shield" :size="14" /> Remote policy
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-divider pt-1">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-2.5 py-2 text-left text-[12px] text-danger hover:bg-danger-soft">
                                <x-icon name="logout" :size="14" /> Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="page-wrap">
            @if ($errors->any())
                <div class="alert alert-error mb-4" role="alert">
                    <x-icon name="alert" :size="17" class="mt-0.5" />
                    <div>
                        <p class="font-semibold">Please correct the following:</p>
                        <ul class="mt-1 list-inside list-disc space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <div class="pointer-events-none fixed right-4 top-4 z-[70] flex w-[min(92vw,360px)] flex-col gap-2" aria-live="polite">
        <template x-for="toast in $store.toasts.items" :key="toast.id">
            <div class="pointer-events-auto flex items-start gap-2.5 rounded-xl border bg-white px-3.5 py-3 shadow-xl"
                 :class="toast.type === 'error' ? 'border-[#f7c9ce]' : 'border-[#bfeed4]'"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full"
                      :class="toast.type === 'error' ? 'bg-danger-soft text-danger' : 'bg-success-soft text-[#0d8c4e]'">
                    <x-icon name="check" :size="12" x-show="toast.type !== 'error'" />
                    <x-icon name="alert" :size="12" x-show="toast.type === 'error'" />
                </span>
                <p class="flex-1 text-[12px] leading-snug text-ink-soft" x-text="toast.message"></p>
                <button type="button" class="text-muted hover:text-ink" @click="$store.toasts.dismiss(toast.id)">
                    <x-icon name="close" :size="13" />
                </button>
            </div>
        </template>
    </div>

    <div x-data="flashToast(@js(session('success')), 'success')" class="hidden"></div>
    <div x-data="flashToast(@js(session('error')), 'error')" class="hidden"></div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
