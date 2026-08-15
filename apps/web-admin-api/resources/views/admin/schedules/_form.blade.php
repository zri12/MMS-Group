@php($schedule = $schedule ?? null)

<div class="mt-6 grid gap-6 xl:grid-cols-[1fr_18rem]">
    <x-ui.card title="Data Jadwal">
        <form method="POST" action="{{ $action }}" class="space-y-5">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <x-form.field label="Marketing" for="marketing_profile_id" error="{{ $errors->first('marketing_profile_id') }}">
                    <x-form.select id="marketing_profile_id" name="marketing_profile_id" placeholder="Pilih PDL">
                        @foreach ($marketingOptions as $marketing)
                            <option value="{{ $marketing->id }}" @selected((int) old('marketing_profile_id', $schedule?->marketing_profile_id) === $marketing->id)>{{ $marketing->code }} - {{ $marketing->user->name }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field label="Prospek" for="prospect_id" error="{{ $errors->first('prospect_id') }}">
                    <x-form.select id="prospect_id" name="prospect_id" placeholder="Tanpa prospek">
                        @foreach ($prospects as $prospect)
                            <option value="{{ $prospect->id }}" @selected((int) old('prospect_id', $schedule?->prospect_id) === $prospect->id)>{{ $prospect->name }} - {{ $prospect->marketingProfile->code }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field label="Tanggal" for="schedule_date" error="{{ $errors->first('schedule_date') }}"><x-form.input id="schedule_date" name="schedule_date" type="date" :value="old('schedule_date', $schedule?->schedule_date?->format('Y-m-d'))" /></x-form.field>
                <x-form.field label="Hari" for="day_name" error="{{ $errors->first('day_name') }}"><x-form.select id="day_name" name="day_name" placeholder="Pilih hari">@foreach ($days as $value => $label)<option value="{{ $value }}" @selected(old('day_name', $schedule?->day_name?->value) === $value)>{{ $label }}</option>@endforeach</x-form.select></x-form.field>
                <x-form.field label="Jam Mulai" for="start_time" error="{{ $errors->first('start_time') }}"><x-form.input id="start_time" name="start_time" type="time" :value="old('start_time', $schedule?->start_time ? substr($schedule->start_time, 0, 5) : null)" /></x-form.field>
                <x-form.field label="Jam Selesai" for="end_time" error="{{ $errors->first('end_time') }}"><x-form.input id="end_time" name="end_time" type="time" :value="old('end_time', $schedule?->end_time ? substr($schedule->end_time, 0, 5) : null)" /></x-form.field>
                <x-form.field label="Nama Konsumen" for="consumer_name_snapshot" error="{{ $errors->first('consumer_name_snapshot') }}"><x-form.input id="consumer_name_snapshot" name="consumer_name_snapshot" :value="old('consumer_name_snapshot', $schedule?->consumer_name_snapshot)" /></x-form.field>
                <x-form.field label="Status" for="status" error="{{ $errors->first('status') }}"><x-form.select id="status" name="status">@foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected(old('status', $schedule?->status?->value ?? 'Belum Dikunjungi') === $value)>{{ $label }}</option>@endforeach</x-form.select></x-form.field>
                <x-form.field label="Agenda" for="agenda" error="{{ $errors->first('agenda') }}"><x-form.input id="agenda" name="agenda" :value="old('agenda', $schedule?->agenda)" /></x-form.field>
                <x-form.field label="Area" for="area" error="{{ $errors->first('area') }}"><x-form.input id="area" name="area" :value="old('area', $schedule?->area)" /></x-form.field>
                <x-form.field label="Resort" for="resort" error="{{ $errors->first('resort') }}"><x-form.input id="resort" name="resort" :value="old('resort', $schedule?->resort)" /></x-form.field>
                <x-form.field label="Tujuan" for="destination" error="{{ $errors->first('destination') }}"><x-form.input id="destination" name="destination" :value="old('destination', $schedule?->destination)" /></x-form.field>
            </div>
            <x-form.field label="Catatan" for="note" error="{{ $errors->first('note') }}"><x-form.textarea id="note" name="note">{{ old('note', $schedule?->note) }}</x-form.textarea></x-form.field>
            <div class="flex flex-wrap gap-2"><x-ui.button type="submit">Simpan</x-ui.button><x-ui.link-button :href="route('admin.schedules.index')" variant="outline">Batal</x-ui.link-button></div>
        </form>
    </x-ui.card>

    @if ($schedule)
        <x-ui.card title="Hapus Jadwal">
            <p class="text-sm leading-6 text-[var(--mms-color-text-muted)]">Menghapus jadwal akan menyembunyikannya dari daftar aktif.</p>
            <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}" class="mt-4">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="danger">Hapus</x-ui.button>
            </form>
        </x-ui.card>
    @endif
</div>
