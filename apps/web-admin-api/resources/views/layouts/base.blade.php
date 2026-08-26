<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#080b10">
        <link rel="icon" type="image/jpeg" href="{{ asset('images/LOGO-KSP.jpeg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/LOGO-KSP.jpeg') }}">

        <title>@yield('title', config('mms.name'))</title>

        <style>
            html,
            body {
                min-height: 100%;
                background: #080b10;
                color-scheme: dark;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @stack('head')
    </head>
    <body class="min-h-screen antialiased">
        @yield('body')

        @livewireScripts
        @stack('scripts')
    </body>
</html>
