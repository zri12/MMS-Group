@extends('layouts.admin')

@section('title', 'Profil Admin - '.config('mms.name'))
@section('page_title', 'Profil Admin')

@section('content')
    <div class="space-y-6">
        <x-navigation.page-header
            title="Kelola profil dan password"
            description="Perbarui identitas akun dan keamanan akses admin."
            :breadcrumbs="[['label' => 'Admin', 'href' => route('admin.home')], ['label' => 'Profil']]"
        />

        <section class="grid gap-6 lg:grid-cols-2">
            <x-ui.card title="Informasi Profil" description="Username dan role tidak dapat diubah dari halaman ini.">
                <form method="POST" action="{{ route('admin.profile.update') }}" x-data="{ submitting: false }" x-on:submit="submitting = true" x-bind:aria-busy="submitting">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <x-form.field label="Nama" for="name" required="true" :error="$errors->first('name')">
                        <x-form.input id="name" name="name" :value="old('name', $user->name)" required :invalid="$errors->has('name')" />
                    </x-form.field>

                    <x-form.field label="Email" for="email" :error="$errors->first('email')">
                        <x-form.input id="email" name="email" type="email" :value="old('email', $user->email)" :invalid="$errors->has('email')" />
                    </x-form.field>

                    <div class="rounded-[var(--mms-radius-lg)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] p-4">
                        <x-data.description-list class="grid-cols-1 sm:grid-cols-2">
                            <x-data.description-item label="Username">{{ $user->username }}</x-data.description-item>
                            <x-data.description-item label="Role">{{ $user->role->label() }}</x-data.description-item>
                            <x-data.description-item label="Status Akun">
                                <x-ui.status-indicator :variant="$user->is_active ? 'active' : 'inactive'" :label="$user->is_active ? 'Aktif' : 'Nonaktif'" />
                            </x-data.description-item>
                            <x-data.description-item label="Login Terakhir">{{ $user->last_login_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '-' }}</x-data.description-item>
                        </x-data.description-list>
                    </div>
                </div>

                <x-ui.button type="submit" class="mt-6" x-bind:disabled="submitting">
                    <span x-show="! submitting">Simpan Profil</span>
                    <span x-cloak x-show="submitting">Menyimpan...</span>
                </x-ui.button>
                </form>
            </x-ui.card>

            <form
                method="POST"
                action="{{ route('admin.password.update') }}"
                x-data="{ submitting: false }"
                x-on:submit="submitting = true"
                x-bind:aria-busy="submitting"
            >
                <x-ui.card title="Ubah Password" description="Gunakan password baru minimal delapan karakter.">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <x-form.field label="Password Saat Ini" for="current_password" required="true" :error="$errors->first('current_password')">
                            <x-form.password-input id="current_password" name="current_password" autocomplete="current-password" required :invalid="$errors->has('current_password')" show-label="Tampilkan password saat ini" hide-label="Sembunyikan password saat ini" />
                        </x-form.field>

                        <x-form.field label="Password Baru" for="password" required="true" :error="$errors->first('password')">
                            <x-form.password-input id="password" name="password" autocomplete="new-password" required :invalid="$errors->has('password')" show-label="Tampilkan password baru" hide-label="Sembunyikan password baru" />
                        </x-form.field>

                        <x-form.field label="Konfirmasi Password Baru" for="password_confirmation" required="true">
                            <x-form.password-input id="password_confirmation" name="password_confirmation" autocomplete="new-password" required show-label="Tampilkan konfirmasi password baru" hide-label="Sembunyikan konfirmasi password baru" />
                        </x-form.field>
                    </div>

                    <x-ui.button type="submit" class="mt-6" x-bind:disabled="submitting">
                        <x-ui.spinner size="sm" x-cloak x-show="submitting" />
                        <span x-show="! submitting">Simpan Password</span>
                        <span x-cloak x-show="submitting">Menyimpan...</span>
                    </x-ui.button>
                </x-ui.card>
            </form>
        </section>
    </div>
@endsection
