@extends('layouts.admin')

@section('title', 'Edit Jadwal - '.config('mms.name'))
@section('page_title', 'Edit Jadwal')

@section('content')
    <x-navigation.page-header title="Edit Jadwal" :breadcrumbs="[['label' => 'Admin', 'href' => route('admin.home')], ['label' => config('mms.navigation.labels.schedules'), 'href' => route('admin.schedules.index')], ['label' => 'Edit']]" />
    @include('admin.schedules._form', ['action' => route('admin.schedules.update', $schedule), 'method' => 'PUT'])
@endsection
