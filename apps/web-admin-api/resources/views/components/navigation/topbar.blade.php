@props([
    'user' => null,
    'title' => 'Admin',
])

<header {{ $attributes->class(['sticky top-0 z-30 border-b border-[var(--mms-color-border)] bg-[var(--mms-color-background-subtle)]/95 backdrop-blur']) }}>
    <div class="flex min-h-14 items-center justify-between gap-2 px-3 sm:px-4 lg:min-h-[var(--mms-header-height)] lg:px-8">
        <div class="flex min-w-0 flex-1 items-center gap-2 lg:hidden">
            <button
                type="button"
                class="inline-grid size-10 shrink-0 place-items-center rounded-full border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] text-[var(--mms-color-text)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
                aria-label="Buka navigasi"
                x-on:click="mobileSidebarOpen = true"
            >
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"></path>
                </svg>
            </button>
            <div class="min-w-0">
                <p class="truncate text-[13px] font-semibold leading-tight text-[var(--mms-color-text)]">{{ $title }}</p>
                <p class="truncate text-[10px] leading-tight text-[var(--mms-color-text-muted)] sm:text-[11px]">{{ now(config('app.timezone'))->translatedFormat('l, d M Y') }}</p>
            </div>
        </div>
        <div class="hidden min-w-0 lg:block">
            <p class="truncate text-sm font-semibold text-[var(--mms-color-text)]">{{ $title }}</p>
            <p class="truncate text-xs text-[var(--mms-color-text-muted)]">Operasional hari {{ now(config('app.timezone'))->translatedFormat('l, d M Y') }}</p>
        </div>
        <x-ui.dropdown label="{{ $user?->name ?? 'Admin' }}" compact-mobile="true">
            <a href="{{ route('admin.profile.edit') }}" class="block rounded-[var(--mms-radius-sm)] px-3 py-2 text-sm text-[var(--mms-color-text)] hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">Profil</a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="block w-full rounded-[var(--mms-radius-sm)] px-3 py-2 text-left text-sm text-red-100 hover:bg-red-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-300">
                    Keluar
                </button>
            </form>
        </x-ui.dropdown>
    </div>
</header>
