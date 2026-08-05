@extends('layouts.admin')

@section('title', 'Tambah Jadwal - '.config('mms.name'))
@section('page_title', 'Tambah Jadwal')

@section('content')
    <x-navigation.page-header title="Tambah Jadwal" :breadcrumbs="[['label' => 'Admin', 'href' => route('admin.home')], ['label' => config('mms.navigation.labels.schedules'), 'href' => route('admin.schedules.index')], ['label' => 'Tambah']]" />
    @include('admin.schedules._form', ['action' => route('admin.schedules.store'), 'method' => 'POST', 'schedule' => null])
@endsection
