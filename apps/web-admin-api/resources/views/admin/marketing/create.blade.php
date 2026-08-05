@extends('layouts.admin')

@section('title', 'Tambah Marketing - '.config('mms.name'))
@section('page_title', 'Tambah Marketing')

@section('content')
    <div class="space-y-6">
        <x-navigation.page-header
            title="Tambah Marketing"
            description="Buat akun marketing beserta profil area dan hari kerja."
            :breadcrumbs="[
                ['label' => 'Admin', 'href' => route('admin.home')],
                ['label' => 'Marketing', 'href' => route('admin.marketing.index')],
                ['label' => 'Tambah'],
            ]"
        />

        <x-ui.card>
            <form method="POST" action="{{ route('admin.marketing.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @include('admin.marketing._form', ['mode' => 'create'])
                <div class="flex flex-wrap justify-end gap-3">
                    <x-ui.link-button :href="route('admin.marketing.index')" variant="outline">Batal</x-ui.link-button>
                    <x-ui.button type="submit">Simpan</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
