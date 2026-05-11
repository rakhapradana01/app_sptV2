@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Sub Kegiatan" />
    <div class="space-y-6">
        <x-common.component-card title="Monev: {{ $pptk->nama }}">
            <div x-data="{ selectedSub: '' }" class="space-y-6">

                <!-- Dropdown Pilih Sub Kegiatan -->
                <div class="form-group">
                    <label class="block mb-2 font-bold text-gray-700">Pilih Sub Kegiatan:</label>
                    <select class="form-control" x-model="selectedSub">
                        <option value="">-- Pilih Sub Kegiatan --</option>
                        @foreach ($pptk->subKegiatans as $sub)
                            <option value="sub-{{ $sub->id }}">{{ $sub->nama_kegiatan }}</option>
                        @endforeach
                    </select>
                </div>

                <hr class="my-4">

                <!-- Container Tampilan -->
                <div>
                    @foreach ($pptk->subKegiatans as $sub)
                        <div x-show="selectedSub == 'sub-{{ $sub->id }}'" x-cloak x-transition>
                            <h4 class="mb-4 text-lg font-semibold text-blue-600">{{ $sub->nama_kegiatan }}</h4>

                            <div class="overflow-x-auto">
                                 <table class="w-full min-w-[600px] border-collapse">
                                    <thead class="bg-light">
                                        <tr class="border-b border-gray-100 dark:border-gray-800">
                                            <th>Uraian</th>
                                            <th class="px-5 py-3 text-left sm:px-6">Koefisien</th>
                                            <th class="px-5 py-3 text-left sm:px-6">Digunakan</th>
                                            <th class="px-5 py-3 text-left sm:px-6">Anggaran</th>
                                            <th class="px-5 py-3 text-left sm:px-6">Realisasi</th>
                                            <th class="px-5 py-3 text-left sm:px-6">Progres</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sub->uraians as $uraian)
                                            @php
                                                $persen =
                                                    $uraian->koefisien > 0
                                                        ? ($uraian->koef_digunakan / $uraian->koefisien) * 100
                                                        : 0;
                                            @endphp
                                            <tr>
                                                <td>{{ $uraian->nama_uraian }}</td>
                                                <td class="px-5 py-3 text-left sm:px-6">{{ $uraian->koefisien }}</td>
                                                <td class="px-5 py-3 text-left sm:px-6">{{ $uraian->koef_digunakan }}</td>
                                                <td class="px-5 py-3 text-left sm:px-6">Rp{{ number_format($uraian->anggaran, 0, ',', '.') }}
                                                </td>
                                                <td class="text-right text-success">
                                                    Rp{{ number_format($uraian->anggaran_digunakan, 0, ',', '.') }}</td>
                                                <td>
                                                    <div class="flex items-center gap-2">
                                                        <div class="progress flex-grow" style="height: 10px;">
                                                            <div class="progress-bar bg-info"
                                                                style="width: {{ $persen }}%"></div>
                                                        </div>
                                                        <small class="font-bold">{{ round($persen) }}%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <!-- State Kosong -->
                    <div x-show="selectedSub == ''" class="py-10 text-center border-2 border-dashed rounded-lg">
                        <p class="text-gray-500 italic">Pilih sub kegiatan di atas untuk memunculkan uraian dummy.</p>
                    </div>
                </div>
            </div>
        </x-common.component-card>
    </div>
@endsection
