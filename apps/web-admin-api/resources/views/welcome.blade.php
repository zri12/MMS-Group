@extends('layouts.app')

@section('title', config('mms.name'))

@section('content')
    <main class="min-h-screen bg-[var(--bg-primary)] px-6 py-10 text-[var(--text-primary)]">
        <section class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-5xl flex-col justify-center gap-8">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-normal text-[var(--gold-primary)]">
                    KSP Manunggal Makmur Sejahtera
                </p>
                <h1 class="mt-4 text-3xl font-bold leading-tight sm:text-4xl">
                    MMS Marketing Monitoring System
                </h1>
                <p class="mt-4 max-w-2xl text-base leading-7 text-[var(--text-secondary)]">
                    Fondasi aplikasi sudah disiapkan untuk pembangunan web admin monitoring marketing berbasis Blade dan Livewire.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-[var(--border-subtle)] bg-[var(--surface-primary)] p-5">
                    <p class="text-sm font-semibold text-[var(--text-primary)]">Web Admin</p>
                    <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Blade, Livewire, Tailwind CSS, dan Alpine bawaan Livewire.</p>
                </div>
                <div class="rounded-2xl border border-[var(--border-subtle)] bg-[var(--surface-primary)] p-5">
                    <p class="text-sm font-semibold text-[var(--text-primary)]">Layanan Data</p>
                    <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Laravel 12, Sanctum, Form Request, Resource, Service, dan Action.</p>
                </div>
                <div class="rounded-2xl border border-[var(--border-subtle)] bg-[var(--surface-primary)] p-5">
                    <p class="text-sm font-semibold text-[var(--text-primary)]">Aplikasi Lapangan</p>
                    <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Flutter terpisah dengan sinkronisasi offline pada tahap berikutnya.</p>
                </div>
            </div>
        </section>
    </main>
@endsection
