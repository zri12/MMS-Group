@props([
    'user' => null,
    'mobile' => false,
])

@php
    $labels = config('mms.navigation.labels');
@endphp

<aside {{ $attributes->class([
    $mobile
        ? 'flex h-full flex-col bg-[var(--mms-color-sidebar)]'
        : 'hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-40 lg:flex lg:w-[var(--mms-sidebar-width)] lg:flex-col lg:border-r lg:border-[var(--mms-color-border)] lg:bg-[var(--mms-color-sidebar)]',
]) }} aria-label="Navigasi admin">
    <div class="flex min-h-[var(--mms-header-height)] items-center justify-between border-b border-[var(--mms-color-border)] px-5">
        <x-ui.logo />
        @if ($mobile)
            <button
                type="button"
                class="inline-grid size-10 place-items-center rounded-full border border-[var(--mms-color-border)] text-[var(--mms-color-text-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
                aria-label="Tutup navigasi"
                x-on:click="mobileSidebarOpen = false"
            >
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18"></path>
                </svg>
            </button>
        @endif
    </div>

    <nav class="mms-sidebar-scroll flex-1 space-y-6 overflow-y-auto px-3 py-5">
        <div>
            <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wide text-[var(--mms-color-text-subtle)]">Menu Utama</p>
            <div class="space-y-1">
                <x-navigation.sidebar-item :href="route('admin.dashboard')" :label="$labels['dashboard']" :active="request()->routeIs('admin.home') || request()->routeIs('admin.dashboard')">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5Z"></path>
                    </svg>
                </x-navigation.sidebar-item>
                <x-navigation.sidebar-item :href="route('admin.marketing.index')" :label="$labels['marketing']" :active="request()->routeIs('admin.marketing.*')">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0m8 0a4 4 0 1 1-8 0m8 0v1a6 6 0 0 1-12 0v-1m18 8a7 7 0 0 0-7-7h-1m-4 0H9a7 7 0 0 0-7 7"></path>
                    </svg>
                </x-navigation.sidebar-item>
                <x-navigation.sidebar-item :href="route('admin.daily.index')" :label="$labels['daily']" :active="request()->routeIs('admin.daily.*')">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3m10-3v3M4 9h16M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Zm3 8h2m2 0h2m-6 4h2m2 0h2"></path>
                    </svg>
                </x-navigation.sidebar-item>
                <x-navigation.sidebar-item :href="route('admin.tracking.index')" :label="$labels['tracking']" :active="request()->routeIs('admin.tracking.*')">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16c3-4 5-4 8 0s5 4 8 0M12 4v9m0 0 3-3m-3 3-3-3"></path>
                    </svg>
                </x-navigation.sidebar-item>
            </div>
        </div>

        <div>
            <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wide text-[var(--mms-color-text-subtle)]">Data Pemasaran</p>
            <div class="space-y-1">
                <x-navigation.sidebar-item :href="route('admin.prospects.index')" :label="$labels['prospects']" :active="request()->routeIs('admin.prospects.*')">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 5h14v14H5zM8 9h8M8 13h5M16 17l3 3"></path>
                    </svg>
                </x-navigation.sidebar-item>
                <x-navigation.sidebar-item :href="route('admin.members.index')" :label="$labels['members']" :active="request()->routeIs('admin.members.*')">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7 8a7 7 0 0 0-14 0M17 8h4M19 6v4"></path>
                    </svg>
                </x-navigation.sidebar-item>
            </div>
        </div>

        <div>
            <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wide text-[var(--mms-color-text-subtle)]">Laporan</p>
            <div class="space-y-1">
                <x-navigation.sidebar-item :href="route('admin.operational-reports.index')" :label="$labels['operational_reports']" :active="request()->routeIs('admin.operational-reports.*')">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 19V5m0 14h15M9 16V9m4 7V7m4 9v-5"></path>
                    </svg>
                </x-navigation.sidebar-item>
                <x-navigation.sidebar-item :href="route('admin.visit-reports.index')" :label="$labels['visit_reports']" :active="request()->routeIs('admin.visit-reports.*')">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Zm0-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"></path>
                    </svg>
                </x-navigation.sidebar-item>
                <x-navigation.sidebar-item :href="route('admin.operational-recaps.index')" :label="$labels['operational_recaps']" :active="request()->routeIs('admin.operational-recaps.*')">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16M4 11h16M4 17h16M8 5v14m8-14v14"></path>
                    </svg>
                </x-navigation.sidebar-item>
            </div>
        </div>

        <div>
            <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wide text-[var(--mms-color-text-subtle)]">Aktivitas</p>
            <div class="space-y-1">
                <x-navigation.sidebar-item :href="route('admin.schedules.index')" :label="$labels['schedules']" :active="request()->routeIs('admin.schedules.*')">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8M8 12h8M8 17h5M5 3h14a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"></path>
                    </svg>
                </x-navigation.sidebar-item>
                <x-navigation.sidebar-item :href="route('admin.journeys.index')" :label="$labels['journeys']" :active="request()->routeIs('admin.journeys.*')">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 18c4-6 7 0 11-6 2-3 3-5 5-6M5 6h.01M12 12h.01M19 18h.01"></path>
                    </svg>
                </x-navigation.sidebar-item>
            </div>
        </div>

        <div>
            <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wide text-[var(--mms-color-text-subtle)]">Akun</p>
            <div class="space-y-1">
                <x-navigation.sidebar-item :href="route('admin.profile.edit')" :label="$labels['profile']" :active="request()->routeIs('admin.profile.*') || request()->routeIs('admin.password.*')">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 9a7 7 0 0 0-14 0"></path>
                    </svg>
                </x-navigation.sidebar-item>
                @if (app()->environment(['local', 'testing']) && Route::has('admin.design-system'))
                    <x-navigation.sidebar-item :href="route('admin.design-system')" label="Design System" :active="request()->routeIs('admin.design-system')">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16M4 12h16M4 19h16M8 5v14"></path>
                        </svg>
                    </x-navigation.sidebar-item>
                @endif
            </div>
        </div>
    </nav>

    <div class="border-t border-[var(--mms-color-border)] p-3">
        <div class="mb-3 rounded-[var(--mms-radius-lg)] bg-[var(--mms-color-surface)] p-3">
            <div class="flex items-center gap-3">
                <x-ui.avatar :name="$user?->name ?? 'Admin'" size="sm" />
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-[var(--mms-color-text)]">{{ $user?->name ?? 'Admin' }}</p>
                    <p class="truncate text-xs text-[var(--mms-color-text-muted)]">{{ $user?->username ?? '-' }}</p>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-ui.button type="submit" variant="outline" full-width="true">Keluar</x-ui.button>
        </form>
    </div>
</aside>
