@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Sub Kegiatan" />
    <div class="space-y-6">
        <x-common.component-card title="Daftar Sub Kegiatan">
            <h1>Rekap Monev: {{ $pptk->nama }}</h1>
            <p>Daftar Sub Kegiatan yang dikelola:</p>
            <ul>
                @foreach ($pptk->subKegiatans as $item)
                    <li>{{ $item->nama_sub_kegiatan }}</li>
                @endforeach
            </ul>
        </x-common.component-card>
    </div>
@endsection
