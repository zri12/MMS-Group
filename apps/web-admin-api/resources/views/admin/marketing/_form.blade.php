@php
    $selectedWorkDays = old('work_days', isset($marketing)
        ? $marketing->workDays->pluck('day_name')->map(fn ($day) => is_string($day) ? $day : $day->value)->all()
        : []);
@endphp

<div class="grid gap-5 lg:grid-cols-2">
    <x-form.field label="Nama" for="name" required="true" :error="$errors->first('name')">
        <x-form.input id="name" name="name" :value="old('name', $marketing->user->name ?? '')" required autocomplete="name" :invalid="$errors->has('name')" />
    </x-form.field>

    <x-form.field label="Username" for="username" required="true" help="Gunakan huruf kecil, angka, titik, garis bawah, atau tanda hubung." :error="$errors->first('username')">
        <x-form.input id="username" name="username" :value="old('username', $marketing->user->username ?? '')" required autocomplete="username" :invalid="$errors->has('username')" />
    </x-form.field>

    <x-form.field label="Email" for="email" :error="$errors->first('email')">
        <x-form.input id="email" name="email" type="email" :value="old('email', $marketing->user->email ?? '')" autocomplete="email" :invalid="$errors->has('email')" />
    </x-form.field>

    <x-form.field label="Kode" for="code" required="true" help="Contoh format awal: M01." :error="$errors->first('code')">
        <x-form.input id="code" name="code" :value="old('code', $marketing->code ?? '')" required :invalid="$errors->has('code')" />
    </x-form.field>

    <x-form.field label="Nomor HP" for="phone" :error="$errors->first('phone')">
        <x-form.input id="phone" name="phone" :value="old('phone', $marketing->phone ?? '')" autocomplete="tel" :invalid="$errors->has('phone')" />
    </x-form.field>

    <x-form.field label="Area/Resort" for="area" required="true" :error="$errors->first('area')">
        <x-form.input id="area" name="area" :value="old('area', $marketing->area ?? '')" required :invalid="$errors->has('area')" />
    </x-form.field>

    <x-form.field label="Foto Profil" for="profile_photo" help="Format JPG, PNG, atau WebP. Maksimal {{ config('mms.uploads.max_image_kb', 2048) }} KB." :error="$errors->first('profile_photo')">
        <input
            id="profile_photo"
            name="profile_photo"
            type="file"
            accept="image/jpeg,image/png,image/webp"
            @class([
                'block w-full rounded-[var(--mms-radius-md)] border bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-sm text-[var(--mms-color-text)] file:mr-3 file:rounded-[var(--mms-radius-sm)] file:border-0 file:bg-[var(--mms-color-primary)] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-[var(--mms-color-primary-foreground)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]',
                $errors->has('profile_photo') ? 'border-[var(--mms-color-danger)]' : 'border-[var(--mms-color-border)]',
            ])
        />
        @if (($mode ?? 'create') === 'edit' && ! empty($marketing?->profile_photo_path))
            <p class="mt-2 text-xs text-[var(--mms-color-text-subtle)]">Foto saat ini akan diganti bila memilih file baru.</p>
        @endif
    </x-form.field>

    @if (($mode ?? 'create') === 'create')
        <x-form.field label="Password" for="password" required="true" :error="$errors->first('password')">
            <x-form.password-input id="password" name="password" autocomplete="new-password" required :invalid="$errors->has('password')" />
        </x-form.field>

        <x-form.field label="Konfirmasi Password" for="password_confirmation" required="true" :error="$errors->first('password_confirmation')">
            <x-form.password-input id="password_confirmation" name="password_confirmation" autocomplete="new-password" required :invalid="$errors->has('password_confirmation')" />
        </x-form.field>
    @endif

    <div class="lg:col-span-2">
        <x-form.field label="Hari Kerja" for="work_days" required="true" help="Pilih minimal satu hari kerja Senin sampai Sabtu." :error="$errors->first('work_days') ?: $errors->first('work_days.*')">
            <div id="work_days" class="grid gap-3 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] p-4 sm:grid-cols-3">
                @foreach ($days as $value => $label)
                    <x-form.checkbox
                        name="work_days[]"
                        :id="'work_day_'.Str::slug($value)"
                        :value="$value"
                        :label="$label"
                        :checked="in_array($value, $selectedWorkDays, true)"
                    />
                @endforeach
            </div>
        </x-form.field>
    </div>
</div>
