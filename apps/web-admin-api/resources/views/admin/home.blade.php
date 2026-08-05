@extends('layouts.admin')

@section('title', 'Beranda Admin - '.config('mms.name'))
@section('page_title', 'Beranda Admin')

@section('content')
    <div class="space-y-6">
        <x-navigation.page-header
            title="Selamat bekerja, {{ $user->name }}"
            description="Ruang kerja admin sudah siap. Modul operasional akan mengikuti fase implementasi berikutnya."
            :breadcrumbs="[['label' => 'Admin'], ['label' => 'Beranda']]"
        >
            <x-slot:actions>
                <x-ui.link-button :href="route('admin.profile.edit')">Kelola Profil</x-ui.link-button>
            </x-slot:actions>
        </x-navigation.page-header>

        <section class="grid gap-4 md:grid-cols-4">
            <x-ui.stat-card label="Username" :value="$user->username" />
            <x-ui.stat-card label="Role" :value="$user->role->label()" />
            <x-ui.stat-card label="Status Akun" :value="$user->is_active ? 'Aktif' : 'Nonaktif'" />
            <x-ui.stat-card label="Login Terakhir" :value="$user->last_login_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '-'" />
        </section>

        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-ui.button type="submit" variant="outline">Keluar</x-ui.button>
            </form>
        </div>
    </div>
@endsection
