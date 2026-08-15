@extends('layouts.auth')

@section('title', 'Login Admin - '.config('mms.name'))

@section('content')
    <div class="grid min-h-[calc(100dvh-1.5rem)] w-full overflow-hidden rounded-[var(--mms-radius-xl)] border border-[var(--mms-color-border-strong)] bg-[var(--mms-color-surface)] shadow-[var(--mms-shadow-lg)] sm:min-h-[600px] sm:rounded-[1.5rem] lg:min-h-dvh lg:grid-cols-[1.05fr_.95fr] lg:rounded-none lg:border-0">

        {{-- Panel Kiri: Branding --}}
        <section class="relative hidden flex-col justify-between overflow-hidden border-r border-[var(--mms-color-border)] bg-[var(--mms-color-background-subtle)] p-[clamp(2.5rem,6vw,5rem)] lg:flex">
            {{-- Lingkaran dekoratif minimal --}}
            <div class="pointer-events-none absolute -bottom-32 -left-32 size-[480px] rounded-full border border-[var(--mms-color-border-gold)] opacity-20" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-16 -left-10 size-[280px] rounded-full border border-[var(--mms-color-border-gold)] opacity-15" aria-hidden="true"></div>

            <div class="relative z-10">
                <x-ui.logo />
                <p class="mt-4 text-[11px] font-semibold tracking-[.12em] text-[var(--mms-color-primary)]">KSP MANUNGGAL MAKMUR SEJAHTERA</p>
            </div>

            <div class="relative z-10 max-w-[480px]">
                <h1 class="text-[42px] font-semibold leading-[1.1] tracking-[-0.025em] text-[var(--mms-color-text)]">Marketing<br>Monitoring</h1>
                <p class="mt-4 text-[15px] leading-[1.7] text-[var(--mms-color-text-muted)]">
                    Pantau aktivitas, lokasi, dan laporan marketing dalam satu ruang kerja yang terintegrasi.
                </p>

                {{-- Feature chips (scope final) --}}
                <div class="mt-8 flex flex-wrap gap-2.5" aria-label="Fitur tersedia">
                    @foreach ([
                        ['label' => 'Tracking PDL',          'icon' => 'M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Zm0-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'],
                        ['label' => 'Rencana Kerja',         'icon' => 'M7 3v3m10-3v3M4 9h16M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z'],
                        ['label' => 'Monitoring Marketing',  'icon' => 'M16 11a4 4 0 1 0-8 0m8 0a4 4 0 1 1-8 0m8 0v1a6 6 0 0 1-12 0v-1'],
                    ] as $chip)
                        <span class="inline-flex items-center gap-2 rounded-full border border-[var(--mms-color-border-gold)] bg-[var(--mms-color-primary-soft)] px-3.5 py-2 text-[11px] font-semibold text-[var(--mms-color-primary)]">
                            <svg class="size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $chip['icon'] }}" />
                            </svg>
                            {{ $chip['label'] }}
                        </span>
                    @endforeach
                </div>
            </div>

            <p class="relative z-10 text-[11px] text-[var(--mms-color-text-subtle)]">© {{ date('Y') }} KSP MMS Marketing Monitoring</p>
        </section>

        {{-- Panel Kanan: Form Login --}}
        <section class="flex items-center justify-center bg-[var(--mms-color-surface)] px-5 py-8 sm:p-10 lg:bg-[#070b0f] lg:p-14">
            <div class="w-full max-w-[400px]">

                {{-- Logo mobile only --}}
                <div class="mb-9 lg:hidden">
                    <x-ui.logo />
                    <p class="mt-3 text-[11px] font-semibold tracking-[.12em] text-[var(--mms-color-primary)]">KSP MANUNGGAL MAKMUR SEJAHTERA</p>
                </div>

                <div class="mb-8">
                    <p class="text-[11px] font-semibold tracking-[.18em] text-[var(--mms-color-primary)]">Akses Admin</p>
                    <h2 class="mt-3 text-[28px] font-semibold leading-tight tracking-[-0.02em] text-[var(--mms-color-text)]">Selamat Datang</h2>
                    <p class="mt-2 text-[13px] leading-relaxed text-[var(--mms-color-text-subtle)]">Masukkan username dan password untuk melanjutkan.</p>
                </div>

                <x-feedback.flash-messages class="mb-6" />
                <x-feedback.validation-summary class="mb-6" />

                <form
                    id="login-form"
                    method="POST"
                    action="{{ route('login.store') }}"
                    x-data="{ submitting: false }"
                    x-on:submit="submitting = true"
                    x-bind:aria-busy="submitting"
                    class="space-y-5"
                >
                    @csrf

                    <x-form.field label="Username" for="username" required="true" :error="$errors->first('username')">
                        <x-form.input
                            id="username"
                            name="username"
                            :value="old('username')"
                            autocomplete="username"
                            autofocus
                            required
                            placeholder="Masukkan username Anda"
                            :invalid="$errors->has('username')"
                            class="min-h-[52px] rounded-[var(--mms-radius-lg)] px-5 text-[14px]"
                        />
                    </x-form.field>

                    <x-form.field label="Password" for="password" required="true" :error="$errors->first('password')">
                        <x-form.password-input
                            id="password"
                            name="password"
                            autocomplete="current-password"
                            required
                            placeholder="Masukkan password"
                            :invalid="$errors->has('password')"
                            class="min-h-[52px] rounded-[var(--mms-radius-lg)] px-5 pr-14 text-[14px]"
                        />
                    </x-form.field>

                    <div class="pt-1">
                        <x-ui.button
                            type="submit"
                            full-width="true"
                            x-bind:disabled="submitting"
                            class="min-h-[52px] rounded-[var(--mms-radius-lg)] text-[14px] font-semibold"
                        >
                            <span x-show="! submitting">Masuk</span>
                            <span x-cloak x-show="submitting">
                                <svg class="mr-2 inline size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25" />
                                    <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                                </svg>
                                Memproses...
                            </span>
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
