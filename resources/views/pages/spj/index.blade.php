@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Surat Pertanggungjawaban (SPJ)" />
    
    <div class="space-y-6">
        <x-common.component-card title="Daftar SPJ">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] border-collapse">
                    <thead class="bg-light">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700">No</th>
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700">Nomor SPT</th>
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700">Nama Pegawai</th>
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700">Tujuan</th>
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700">Tanggal</th>
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($spjs as $index => $item)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-left sm:px-6">{{ $index + 1 }}</td>
                                <td class="px-5 py-3 text-left sm:px-6 font-medium">{{ $item['nomor_spt'] }}</td>
                                <td class="px-5 py-3 text-left sm:px-6">{{ $item['nama_pegawai'] }}</td>
                                <td class="px-5 py-3 text-left sm:px-6">{{ $item['tujuan'] }}</td>
                                <td class="px-5 py-3 text-left sm:px-6 text-gray-500">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y') }}</td>
                                <td class="px-5 py-3 text-left sm:px-6">
                                    <a href="{{ route('spj.show', $item['id']) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-common.component-card>
    </div>
@endsection
