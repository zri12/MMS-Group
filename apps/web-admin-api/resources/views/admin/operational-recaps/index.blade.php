@extends('layouts.admin')

@section('title', config('mms.navigation.labels.operational_recaps').' - '.config('mms.name'))
@section('page_title', config('mms.navigation.labels.operational_recaps'))

@section('content')
    <div class="space-y-6">
        <x-navigation.page-header
            title="{{ config('mms.navigation.labels.operational_recaps') }}"
            description="Rekap seluruh PDL berdasarkan laporan operasional pada tanggal terpilih."
            :breadcrumbs="[['label' => 'Admin', 'href' => route('admin.home')], ['label' => config('mms.navigation.labels.operational_recaps')]]"
        />

        <x-ui.card title="Filter">
            <form method="GET" action="{{ route('admin.operational-recaps.index') }}" class="grid gap-4 md:grid-cols-3">
                <x-form.field label="Tanggal" for="date"><x-form.input id="date" name="date" type="date" :value="$filters['date']" /></x-form.field>
                <x-form.field label="Hari" for="day"><x-form.select id="day" name="day" placeholder="Semua hari">@foreach ($days as $value => $label)<option value="{{ $value }}" @selected($filters['day'] === $value)>{{ $label }}</option>@endforeach</x-form.select></x-form.field>
                <div class="flex items-end gap-2"><x-ui.button type="submit" full-width="true">Terapkan</x-ui.button><x-ui.link-button :href="route('admin.operational-recaps.index')" variant="outline">Reset</x-ui.link-button></div>
            </form>
        </x-ui.card>

        <form method="POST" action="{{ route('admin.operational-recaps.generate') }}" class="flex flex-wrap items-end gap-3">
            @csrf
            <x-form.field label="Tanggal rekap" for="recap_date" :error="$errors->first('recap_date')" class="w-full sm:w-56">
                <x-form.input id="recap_date" name="recap_date" type="date" :value="$filters['date'] ?: now(config('mms.timezone'))->toDateString()" required />
            </x-form.field>
            <x-ui.button type="submit">Perbarui Rekap</x-ui.button>
        </form>

        @if ($recaps->isEmpty())
            <x-ui.empty-state title="Rekap belum tersedia" description="Belum ada rekap sesuai filter." />
        @else
            <x-data.table caption="Daftar rekap operasional">
                <x-data.table-head><tr><x-data.table-cell heading="true">Nomor</x-data.table-cell><x-data.table-cell heading="true">Hari</x-data.table-cell><x-data.table-cell heading="true">Tanggal</x-data.table-cell><x-data.table-cell heading="true">Status</x-data.table-cell><x-data.table-cell heading="true">Baris</x-data.table-cell><x-data.table-cell heading="true">Aksi</x-data.table-cell></tr></x-data.table-head>
                <x-data.table-body>
                    @foreach ($recaps as $recap)
                        <x-data.table-row>
                            <x-data.table-cell>{{ $recap->report_number }}</x-data.table-cell>
                            <x-data.table-cell>{{ $recap->day_name->label() }}</x-data.table-cell>
                            <x-data.table-cell>{{ $recap->recap_date->format('d/m/Y') }}</x-data.table-cell>
                            <x-data.table-cell><x-ui.badge variant="neutral">{{ $recap->status }}</x-ui.badge></x-data.table-cell>
                            <x-data.table-cell>{{ $recap->rows_count }}</x-data.table-cell>
                            <x-data.table-cell><x-ui.link-button :href="route('admin.operational-recaps.show', $recap)" size="sm">Detail</x-ui.link-button></x-data.table-cell>
                        </x-data.table-row>
                    @endforeach
                </x-data.table-body>
            </x-data.table>
            <x-data.pagination :paginator="$recaps" class="mt-4" />
        @endif
    </div>
@endsection
