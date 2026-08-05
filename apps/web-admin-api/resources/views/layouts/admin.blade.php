@extends('layouts.base')

@section('body')
    @php
        $pageTitle = trim($__env->yieldContent('page_title', 'Admin'));
        $pageShellClass = trim($__env->yieldContent('page_shell_class', 'max-w-[var(--mms-content-max-width)]'));
    @endphp

    <div
        class="min-h-screen bg-[var(--mms-color-background)] text-[var(--mms-color-text)]"
        x-data="{ mobileSidebarOpen: false }"
        x-effect="document.body.classList.toggle('overflow-hidden', mobileSidebarOpen)"
        x-on:keydown.escape.window="mobileSidebarOpen = false"
    >
        <x-navigation.sidebar :user="auth()->user()" />

        <div class="min-h-screen lg:pl-[var(--mms-sidebar-width)]">
            <x-navigation.topbar :user="auth()->user()" :title="$pageTitle" />

            <main class="mx-auto w-full {{ $pageShellClass }} px-4 pb-[calc(var(--mms-mobile-nav-height)+1.5rem)] pt-6 sm:px-6 lg:px-8 lg:pb-10">
                <x-feedback.flash-messages class="mb-5" />
                @yield('content')
            </main>
        </div>

        <div class="lg:hidden" x-cloak x-show="mobileSidebarOpen" aria-modal="true" role="dialog">
            <button
                type="button"
                class="fixed inset-0 z-50 bg-black/70"
                aria-label="Tutup navigasi"
                x-on:click="mobileSidebarOpen = false"
                x-transition.opacity
            ></button>
            <div
                class="fixed inset-y-0 left-0 z-50 w-[min(82vw,20rem)] border-r border-[var(--mms-color-border)] bg-[var(--mms-color-sidebar)] shadow-[var(--mms-shadow-lg)]"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-160"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
            >
                <x-navigation.sidebar :user="auth()->user()" mobile="true" />
            </div>
        </div>

        <x-navigation.mobile-nav />
    </div>
@endsection
