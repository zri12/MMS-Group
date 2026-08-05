@extends('layouts.admin')

@section('title', 'Edit Marketing - '.config('mms.name'))
@section('page_title', 'Edit Marketing')

@section('content')
    <div class="space-y-6">
        <x-navigation.page-header
            title="Edit {{ $marketing->user->name }}"
            description="Perbarui profil marketing tanpa menghapus histori."
            :breadcrumbs="[
                ['label' => 'Admin', 'href' => route('admin.home')],
                ['label' => 'Marketing', 'href' => route('admin.marketing.index')],
                ['label' => $marketing->code, 'href' => route('admin.marketing.show', $marketing)],
                ['label' => 'Edit'],
            ]"
        />

        <x-ui.card>
            <form method="POST" action="{{ route('admin.marketing.update', $marketing) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                @include('admin.marketing._form', ['mode' => 'edit'])
                <div class="flex flex-wrap justify-end gap-3">
                    <x-ui.link-button :href="route('admin.marketing.show', $marketing)" variant="outline">Batal</x-ui.link-button>
                    <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
