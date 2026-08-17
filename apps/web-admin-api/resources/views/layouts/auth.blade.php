@extends('layouts.base')

@section('body')
    <main class="flex min-h-dvh items-center justify-center bg-[var(--mms-color-background)] px-3 py-3 text-[var(--mms-color-text)] sm:px-6 sm:py-6 lg:items-stretch lg:p-0">
        <section class="w-full lg:flex lg:items-stretch">
            @yield('content')
        </section>
    </main>
@endsection
