@extends('layouts.base')

@section('body')
    <main class="flex min-h-dvh bg-[var(--mms-color-background)] text-[var(--mms-color-text)] lg:items-stretch">
        <section class="w-full lg:flex lg:items-stretch">
            @yield('content')
        </section>
    </main>
@endsection
