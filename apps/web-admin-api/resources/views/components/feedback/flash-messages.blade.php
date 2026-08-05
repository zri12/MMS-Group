@php
    $messages = [
        'flash_message' => ['variant' => 'success', 'title' => null],
        'success' => ['variant' => 'success', 'title' => null],
        'error' => ['variant' => 'danger', 'title' => null],
        'warning' => ['variant' => 'warning', 'title' => null],
        'info' => ['variant' => 'info', 'title' => null],
    ];
@endphp

<div {{ $attributes->class(['space-y-3']) }}>
    @foreach ($messages as $key => $config)
        @if (session()->has($key))
            <x-ui.alert :variant="$config['variant']" :title="$config['title']">
                {{ session($key) }}
            </x-ui.alert>
        @endif
    @endforeach

    @if (session('status') === 'profile-updated')
        <x-ui.alert variant="success">Profil berhasil diperbarui.</x-ui.alert>
    @endif

    @if (session('status') === 'password-updated')
        <x-ui.alert variant="success">Password berhasil diperbarui.</x-ui.alert>
    @endif
</div>
