@extends('layouts.admin')

@section('title', 'Detail Anggota - '.config('mms.name'))
@section('page_title', 'Detail Anggota')

@php
    $rupiah = fn (int $value): string => 'Rp '.number_format($value, 0, ',', '.');
@endphp

@section('content')
    <div class="space-y-6">
        <x-navigation.page-header
            title="{{ $member->name }}"
            description="{{ $member->member_number }} - {{ $member->resort }}"
            :breadcrumbs="[
                ['label' => 'Admin', 'href' => route('admin.home')],
                ['label' => 'Anggota', 'href' => route('admin.members.index')],
                ['label' => $member->member_number],
            ]"
        />

        <section class="grid gap-4 lg:grid-cols-3">
            <x-ui.card title="Identitas" class="lg:col-span-2">
                <x-data.description-list class="grid-cols-1 sm:grid-cols-2">
                    <x-data.description-item label="Nama">{{ $member->name }}</x-data.description-item>
                    <x-data.description-item label="Nomor HP">{{ $member->phone }}</x-data.description-item>
                    <x-data.description-item label="Nomor Anggota">{{ $member->member_number }}</x-data.description-item>
                    <x-data.description-item label="Nomor Pinjaman">{{ $member->loan_number }}</x-data.description-item>
                    <x-data.description-item label="Usaha">{{ $member->business }}</x-data.description-item>
                    <x-data.description-item label="Jaminan">{{ $member->collateral }}</x-data.description-item>
                    <x-data.description-item label="Marketing">{{ $member->marketingProfile->code }} - {{ $member->marketingProfile->user->name }}</x-data.description-item>
                    <x-data.description-item label="Status">
                        <x-ui.badge :variant="$member->approval_status->value === 'Disetujui' ? 'success' : ($member->approval_status->value === 'Ditolak' ? 'danger' : 'warning')">{{ $member->approval_status->label() }}</x-ui.badge>
                    </x-data.description-item>
                </x-data.description-list>
            </x-ui.card>

            <x-ui.card title="Nominal">
                <x-data.description-list>
                    <x-data.description-item label="Pinjaman">{{ $rupiah($member->loan_amount) }}</x-data.description-item>
                    <x-data.description-item label="Angsuran">{{ $rupiah($member->installment_amount) }}</x-data.description-item>
                    <x-data.description-item label="Asuransi">{{ $rupiah($member->insurance_amount) }}</x-data.description-item>
                </x-data.description-list>
            </x-ui.card>
        </section>

        <section class="grid gap-4 lg:grid-cols-2">
            <x-ui.card title="Alamat dan Lokasi">
                <p class="text-sm leading-6 text-[var(--mms-color-text-muted)]">{{ $member->address }}</p>
                <x-ui.divider />
                <p class="text-xs text-[var(--mms-color-text-muted)]">Koordinat</p>
                <p class="mt-1 text-sm text-[var(--mms-color-text)]">{{ $member->latitude ?? '-' }}, {{ $member->longitude ?? '-' }}</p>
                <p class="mt-3 text-xs text-[var(--mms-color-text-muted)]">{{ $member->location_address ?? '-' }}</p>
            </x-ui.card>

            <x-ui.card title="Persetujuan">
                @if ($member->approval_status->value === 'Menunggu')
                    <div class="space-y-4">
                        <x-ui.confirmation-dialog name="approve-member" title="Setujui anggota" message="Status anggota akan menjadi Disetujui.">
                            <x-slot:trigger>
                                <x-ui.button type="button">Setujui</x-ui.button>
                            </x-slot:trigger>
                            <x-slot:confirm>
                                <form method="POST" action="{{ route('admin.members.approval', $member) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="approval_status" value="Disetujui">
                                    <x-ui.button type="submit">Setujui</x-ui.button>
                                </form>
                            </x-slot:confirm>
                        </x-ui.confirmation-dialog>

                        <form method="POST" action="{{ route('admin.members.approval', $member) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="approval_status" value="Ditolak">
                            <x-form.field label="Alasan Penolakan" for="rejection_reason" :error="$errors->first('rejection_reason')">
                                <x-form.textarea id="rejection_reason" name="rejection_reason" :invalid="$errors->has('rejection_reason')">{{ old('rejection_reason') }}</x-form.textarea>
                            </x-form.field>
                            <x-ui.button type="submit" variant="danger">Tolak</x-ui.button>
                        </form>
                    </div>
                @else
                    <x-data.description-list>
                        <x-data.description-item label="Diproses Oleh">{{ $member->approvedBy?->name ?? $member->rejectedBy?->name ?? '-' }}</x-data.description-item>
                        <x-data.description-item label="Waktu">{{ $member->approved_at?->format('d/m/Y H:i') ?? $member->rejected_at?->format('d/m/Y H:i') ?? '-' }}</x-data.description-item>
                        <x-data.description-item label="Alasan">{{ $member->rejection_reason ?? '-' }}</x-data.description-item>
                    </x-data.description-list>
                @endif
            </x-ui.card>
        </section>
    </div>
@endsection
