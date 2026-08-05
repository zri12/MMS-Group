@php
    $labels = config('mms.navigation.labels');
    $bottomLabels = config('mms.navigation.bottom_labels');
@endphp

<nav {{ $attributes->class(['mms-mobile-bottom-nav fixed inset-x-0 bottom-0 z-50 grid min-h-[var(--mms-mobile-nav-height)] grid-cols-5 gap-1 border-t border-[var(--mms-color-border)] bg-[var(--mms-color-background)] px-2 pb-[max(0.5rem,env(safe-area-inset-bottom))] pt-2 shadow-[var(--mms-shadow-md)] lg:hidden']) }} aria-label="Navigasi bawah">
    <x-navigation.mobile-nav-item :href="route('admin.dashboard')" :label="$labels['dashboard']" :active="request()->routeIs('admin.home') || request()->routeIs('admin.dashboard')">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5Z"></path>
        </svg>
    </x-navigation.mobile-nav-item>
    <x-navigation.mobile-nav-item :href="route('admin.marketing.index')" :label="$bottomLabels['marketing']" :active="request()->routeIs('admin.marketing.*')">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0m8 0a4 4 0 1 1-8 0m8 0v1a6 6 0 0 1-12 0v-1m18 8a7 7 0 0 0-7-7h-1m-4 0H9a7 7 0 0 0-7 7"></path>
        </svg>
    </x-navigation.mobile-nav-item>
    <x-navigation.mobile-nav-item :href="route('admin.daily.index')" :label="$bottomLabels['daily']" :active="request()->routeIs('admin.daily.*') || request()->routeIs('admin.prospects.*') || request()->routeIs('admin.members.*') || request()->routeIs('admin.operational-reports.*') || request()->routeIs('admin.visit-reports.*') || request()->routeIs('admin.operational-recaps.*') || request()->routeIs('admin.schedules.*') || request()->routeIs('admin.journeys.*')">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3m10-3v3M4 9h16M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Zm3 8h2m2 0h2m-6 4h2m2 0h2"></path>
        </svg>
    </x-navigation.mobile-nav-item>
    <x-navigation.mobile-nav-item :href="route('admin.tracking.index')" :label="$bottomLabels['tracking']" :active="request()->routeIs('admin.tracking.*')">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16c3-4 5-4 8 0s5 4 8 0M12 4v9m0 0 3-3m-3 3-3-3"></path>
        </svg>
    </x-navigation.mobile-nav-item>
    <x-navigation.mobile-nav-item :href="route('admin.profile.edit')" :label="$bottomLabels['profile']" :active="request()->routeIs('admin.profile.*') || request()->routeIs('admin.password.*')">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 9a7 7 0 0 0-14 0"></path>
        </svg>
    </x-navigation.mobile-nav-item>
</nav>
