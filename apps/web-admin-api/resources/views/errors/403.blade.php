@extends('layouts.auth')

@section('title', 'Akses Ditolak - '.config('mms.name'))

@section('content')
    <x-ui.error-state class="mx-auto max-w-lg" title="Akses tidak tersedia." description="Sesi saat ini tidak memiliki izin untuk membuka halaman ini.">
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-ui.button type="submit">Keluar</x-ui.button>
            </form>
        @else
            <x-ui.link-button :href="route('login')">Kembali ke Login</x-ui.link-button>
        @endauth
    </x-ui.error-state>
@endsection
