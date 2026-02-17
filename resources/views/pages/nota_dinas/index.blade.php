@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Nota Dinas" />
    <div class="space-y-6">
        <x-common.component-card title="Daftar Nota Dinas">
            <a href="{{ route('nota-dinas.create') }}">
                <x-ui.button size="sm">Tambah</x-ui.button>
            </a>
            <x-ui.pagination :paginator="$notaDinas" />
        </x-common.component-card>
    </div>
@endsection
