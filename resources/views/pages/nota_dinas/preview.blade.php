@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-10 shadow">

        <div class="text-center mb-8">
            <h2 class="font-bold text-lg">PEMERINTAH PROVINSI KALIMANTAN SELATAN</h2>
            <h3 class="font-bold">BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH</h3>
            <p>Banjarbaru</p>
        </div>

        <style>
            body {
                font-family: "Times New Roman";
                font-size: 12pt;
            }

            .info-table {
                width: 100%;
                margin-bottom: 20px;
            }

            .info-table td {
                vertical-align: top;
                padding: 2px 0;
            }

            .label {
                width: 110px;
            }

            .colon {
                width: 10px;
            }
        </style>

        <div class="text-center font-bold mb-6">
            NOTA DINAS
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Yth</td>
                <td class="colon">:</td>
                <td>{{ $nota->kepada->jabatan }}</td>
            </tr>
            <tr>
                <td class="label">Dari</td>
                <td class="colon">:</td>
                <td>{{ $nota->dari->jabatan }}</td>
            </tr>
            <tr>
                <td class="label">Melalui</td>
                <td class="colon">:</td>
                <td>{{ $nota->melalui->jabatan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal</td>
                <td class="colon">:</td>
                <td>{{ \Carbon\Carbon::parse($nota->tanggal)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Nomor</td>
                <td class="colon">:</td>
                <td class="px-5 py-4 sm:px-6 whitespace-nowrap font-mono text-sm text-gray-600 dark:text-gray-400">
                    900.1 /
                    <span style="display:inline-block; min-width:60px; text-align:center;">
                        {{ $nota->nomor_urut ?: '     ' }}
                    </span>
                    / BPKAD / {{ date('Y') }}
                </td>
            </tr>
            <tr>
                <td class="label">Sifat</td>
                <td class="colon">:</td>
                <td>{{ $nota->sifat ?? 'Biasa' }}</td>
            </tr>
            <tr>
                <td class="label">Lampiran</td>
                <td class="colon">:</td>
                <td>{{ $nota->lampiran ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Hal</td>
                <td class="colon">:</td>
                <td>{{ $nota->perihal }}</td>
            </tr>
        </table>

        <div class="text-justify leading-relaxed mb-6 indent-8">
            Dengan hormat diusulkan
            <b>
                @foreach ($groupedPegawai as $jabatan => $jumlah)
                    {{ $jumlah }}
                    ({{ \Illuminate\Support\Str::ucfirst(terbilang($jumlah)) }})
                    orang {{ \Illuminate\Support\Str::title($jabatan) }}@if (!$loop->last)
                        ,
                    @endif
                @endforeach
            </b>
            {{ $nota->kegiatan }}
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left sm:px-6">No</th>
                        <th class="px-5 py-3 text-left sm:px-6">Nama</th>
                        <th class="px-5 py-3 text-left sm:px-6">Pangkat / Gol</th>
                        <th class="px-5 py-3 text-left sm:px-6">Jabatan</th>
                        <th class="px-5 py-3 text-left sm:px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nota->pegawais as $item)
                        <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white">
                            <td class="px-5 py-4 sm:px-6">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                {{ $item->nama }}
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                {{ $item->pangkat }}
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                {{ $item->jabatan }}
                            </td>
                            <td>
                                @if (auth()->user()->role->name == 'kepala_bidang')
                                    <form action="{{ route('nota-dinas.pegawai.destroy', [$nota->id, $item->id]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')

                                        <button class="text-red-600 text-sm" onclick="return confirm('Hapus pegawai ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if (auth()->user()->role->name == 'kepala_bidang')
                <div x-data="{ open: false }" x-effect="document.body.style.overflow = open ? 'hidden' : ''" class="mt-4">

                    <button @click="open=true" class="px-4 py-2 bg-green-600 text-white rounded">
                        Tambah Pegawai
                    </button>

                    <!-- MODAL -->
                    <div x-show="open" x-cloak class="fixed inset-0 flex items-center justify-center bg-black/40">

                        <div class="bg-white w-96 p-6 rounded-lg shadow">

                            <h2 class="font-bold text-lg mb-4">
                                Tambah Pegawai
                            </h2>

                            <form action="{{ route('nota-dinas.pegawai.store', $nota->id) }}" method="POST">
                                @csrf

                                <select name="pegawai_ids[]" class="w-full border rounded-lg p-2 mb-4">

                                    <option value="">-- Pilih Pegawai --</option>

                                    @foreach ($pegawais as $pegawai)
                                        <option value="{{ $pegawai->id }}">
                                            {{ $pegawai->nama }} - {{ $pegawai->jabatan }}
                                        </option>
                                    @endforeach

                                </select>

                                <div class="flex justify-end gap-2">

                                    <button type="button" @click="open=false" class="px-3 py-2 border rounded">
                                        Batal
                                    </button>

                                    <button class="px-3 py-2 bg-blue-600 text-white rounded">
                                        Simpan
                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>
            @endif
        </div>

        <div class="text-justify leading-relaxed mb-6 indent-8">
            Pembebanan biaya perjalanan dinas menggunakan Sub Kegiatan pada
            DPA Badan Pengelolaan Keuangan dan Aset Daerah Provinsi Kalimantan Selatan
            Tahun Anggaran {{ date('Y') }} yaitu
            <b>
                {{ $nota->subKegiatan->nomor_rekening }}
                {{ $nota->subKegiatan->nama_kegiatan }}
            </b>.
        </div>

        <div class="text-justify leading-relaxed mb-8 indent-8">
            Demikian disampaikan, apabila berkenan mohon persetujuan Bapak untuk
            penandatanganan SPT sebagaimana terlampir. Atas persetujuan dan
            perkenannya diucapkan terima kasih.
        </div>

        <div class="mt-12 text-right">
            <p class="mt-16 font-bold">{{ optional($nota->dari)->jabatan }}</p>
            <p class="mt-16 font-bold">{{ optional($nota->dari)->nama }}</p>
        </div>

        {{-- Riwayat / Catatan Disposisi --}}
        @if ($nota->disposisi_kabid || $nota->disposisi_sekban || $nota->disposisi_kaban)
            <div class="mt-12 p-6 rounded-2xl border border-gray-200 bg-gradient-to-br from-gray-50 to-slate-100 shadow-sm print:hidden">
                <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-200">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-base font-bold text-gray-800 tracking-wide">Lembar Catatan Disposisi &amp; Arahan Atasan</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Disposisi Kabid --}}
                    <div class="p-4 rounded-xl border {{ $nota->disposisi_kabid ? 'bg-white border-blue-200 shadow-xs' : 'bg-gray-100/60 border-dashed border-gray-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $nota->disposisi_kabid ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-600' }}">
                                Kepala Bidang
                            </span>
                            @if ($nota->tanggal_disposisi_kabid)
                                <span class="text-[11px] text-gray-500 font-mono">
                                    {{ \Carbon\Carbon::parse($nota->tanggal_disposisi_kabid)->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-700 italic">
                            {{ $nota->disposisi_kabid ?: 'Belum ada catatan disposisi Kabid.' }}
                        </p>
                    </div>

                    {{-- Disposisi Sekban --}}
                    <div class="p-4 rounded-xl border {{ $nota->disposisi_sekban ? 'bg-white border-purple-200 shadow-xs' : 'bg-gray-100/60 border-dashed border-gray-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $nota->disposisi_sekban ? 'bg-purple-100 text-purple-800' : 'bg-gray-200 text-gray-600' }}">
                                Sekretaris Badan
                            </span>
                            @if ($nota->tanggal_disposisi_sekban)
                                <span class="text-[11px] text-gray-500 font-mono">
                                    {{ \Carbon\Carbon::parse($nota->tanggal_disposisi_sekban)->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-700 italic">
                            {{ $nota->disposisi_sekban ?: 'Belum ada catatan disposisi Sekban.' }}
                        </p>
                    </div>

                    {{-- Disposisi Kaban --}}
                    <div class="p-4 rounded-xl border {{ $nota->disposisi_kaban ? 'bg-white border-emerald-200 shadow-xs' : 'bg-gray-100/60 border-dashed border-gray-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $nota->disposisi_kaban ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">
                                Kepala Badan (ACC)
                            </span>
                            @if ($nota->tanggal_disposisi_kaban)
                                <span class="text-[11px] text-gray-500 font-mono">
                                    {{ \Carbon\Carbon::parse($nota->tanggal_disposisi_kaban)->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-700 font-medium">
                            {{ $nota->disposisi_kaban ?: 'Belum ada catatan disposisi Kaban.' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @php
            $userRole = auth()->user()->role->name;
            $canApproveKabid = ($userRole === 'kepala_bidang' || $userRole === 'super_admin') && $nota->status === \App\Models\NotaDinas::DIAJUKAN_KABID;
            $canApproveSekban = ($userRole === 'sekretaris_badan' || $userRole === 'super_admin') && $nota->status === \App\Models\NotaDinas::DIAJUKAN_SEKBAN;
            $canApproveKaban = ($userRole === 'kepala_badan' || $userRole === 'super_admin') && $nota->status === \App\Models\NotaDinas::DIAJUKAN_KABAN;
        @endphp

        {{-- Tindakan Kepala Bidang --}}
        @if ($canApproveKabid)
            <div class="mt-10 p-5 border border-blue-200 bg-blue-50/50 rounded-2xl shadow-sm"
                x-data="{
                    showRevisiModal: false,
                    showDisposisiModalKabid: false,
                    disposisiKabidText: 'Mohon persetujuan Kaban'
                }">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-bold text-blue-900 flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        Tindakan Kepala Bidang:
                    </p>
                    <span class="text-xs text-blue-700 font-medium bg-blue-100/80 px-2.5 py-1 rounded-full">
                        Menunggu Verifikasi &amp; Disposisi Kabid
                    </span>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="showDisposisiModalKabid = true"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 shadow-sm transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Tulis Disposisi &amp; Teruskan ke Sekban
                    </button>

                    <x-ui.button variant="yellow" @click="showRevisiModal = true">Revisi</x-ui.button>

                    <form action="{{ route('nota-dinas.reject-kabid', $nota->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak nota dinas ini?')">
                        @csrf @method('PATCH')
                        <x-ui.button variant="red" type="submit">Tolak</x-ui.button>
                    </form>
                </div>

                {{-- MODAL DISPOSISI KABID --}}
                <div x-show="showDisposisiModalKabid" x-cloak
                    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
                    <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="showDisposisiModalKabid = false"></div>

                    <div class="relative bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden border border-gray-100 z-10">
                        {{-- Header Modal --}}
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/20 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-white">Disposisi Kepala Bidang</h3>
                                    <p class="text-xs text-blue-100">Diteruskan ke Sekretaris Badan</p>
                                </div>
                            </div>
                            <button type="button" @click="showDisposisiModalKabid = false" class="text-white/80 hover:text-white text-2xl font-semibold leading-none">&times;</button>
                        </div>

                        {{-- Body Modal --}}
                        <form action="{{ route('nota-dinas.approve-kabid', $nota->id) }}" method="POST" class="p-6">
                            @csrf
                            @method('PATCH')

                            <div class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-900 space-y-1">
                                <p><span class="font-semibold">Perihal:</span> {{ $nota->perihal }}</p>
                                <p><span class="font-semibold">Kegiatan:</span> {{ $nota->kegiatan }}</p>
                            </div>

                            <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1.5">
                                Tulisan Disposisi / Arahan Kabid:
                            </label>

                            {{-- Pilihan Cepat / Preset Templates --}}
                            <div class="mb-3">
                                <p class="text-xs text-gray-500 mb-1.5 font-medium">Pilihan template cepat:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" @click="disposisiKabidText = 'Mohon persetujuan Kaban'"
                                        class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-800 rounded-lg border border-gray-200 transition">
                                        + Mohon persetujuan Kaban
                                    </button>
                                    <button type="button" @click="disposisiKabidText = 'Diteruskan ke Sekretaris Badan untuk diproses lebih lanjut'"
                                        class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-800 rounded-lg border border-gray-200 transition">
                                        + Diteruskan ke Sekban
                                    </button>
                                    <button type="button" @click="disposisiKabidText = 'Setuju diusulkan, mohon arahan dan petunjuk lebih lanjut'"
                                        class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-800 rounded-lg border border-gray-200 transition">
                                        + Setuju diusulkan
                                    </button>
                                    <button type="button" @click="disposisiKabidText = 'Setuju, koordinasikan dengan bidang terkait'"
                                        class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-800 rounded-lg border border-gray-200 transition">
                                        + Koordinasikan bidang terkait
                                    </button>
                                </div>
                            </div>

                            <textarea name="disposisi_kabid" rows="4" x-model="disposisiKabidText" required
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition"
                                placeholder="Ketik tulisan disposisi atau arahan untuk Sekretaris Badan..."></textarea>
                            <p class="text-[11px] text-gray-500 mt-1">Tulisan disposisi ini akan tercetak pada Lembar Disposisi Nota Dinas resmi.</p>

                            <div class="flex justify-end gap-2.5 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <button type="button" @click="showDisposisiModalKabid = false"
                                    class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2 text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                    Kirim Disposisi ke Sekban
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- MODAL REVISI KABID --}}
                <div x-show="showRevisiModal" x-cloak
                    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4">
                    <div class="bg-white w-full max-w-md p-6 rounded-2xl shadow-xl">
                        <h2 class="font-bold text-lg mb-4 text-gray-800">Catatan Revisi Kabid</h2>

                        <form action="{{ route('nota-dinas.revisi-kabid', $nota->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <textarea name="revisi" rows="4" class="w-full border rounded-xl p-3 mb-4 focus:ring-2 focus:ring-yellow-500 text-sm"
                                placeholder="Tuliskan bagian yang perlu diperbaiki..." required></textarea>

                            <div class="flex justify-end gap-2">
                                <button type="button" @click="showRevisiModal = false"
                                    class="px-4 py-2 border rounded-xl text-sm font-medium">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-xl">Kirim Revisi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tindakan Sekretaris Badan --}}
        @if ($canApproveSekban)
            <div class="mt-10 p-5 border border-purple-200 bg-purple-50/50 rounded-2xl shadow-sm"
                x-data="{
                    showRevisiModalSekban: false,
                    showDisposisiModalSekban: false,
                    disposisiSekbanText: 'Diteruskan kepada Kepala Badan, mohon petunjuk dan persetujuan.'
                }">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-bold text-purple-900 flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                        Tindakan Sekretaris Badan:
                    </p>
                    <span class="text-xs text-purple-700 font-medium bg-purple-100/80 px-2.5 py-1 rounded-full">
                        Menunggu Verifikasi &amp; Disposisi Sekban
                    </span>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="showDisposisiModalSekban = true"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 active:bg-purple-800 shadow-sm transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Tulis Disposisi &amp; Teruskan ke Kaban
                    </button>

                    <x-ui.button variant="yellow" @click="showRevisiModalSekban = true">Revisi</x-ui.button>

                    <form action="{{ route('nota-dinas.reject-sekban', $nota->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak nota dinas ini?')">
                        @csrf @method('PATCH')
                        <x-ui.button variant="red" type="submit">Tolak</x-ui.button>
                    </form>
                </div>

                {{-- MODAL DISPOSISI SEKBAN --}}
                <div x-show="showDisposisiModalSekban" x-cloak
                    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
                    <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="showDisposisiModalSekban = false"></div>

                    <div class="relative bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden border border-gray-100 z-10">
                        {{-- Header Modal --}}
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-700 text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/20 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-white">Disposisi Sekretaris Badan</h3>
                                    <p class="text-xs text-purple-100">Diteruskan kepada Kepala Badan</p>
                                </div>
                            </div>
                            <button type="button" @click="showDisposisiModalSekban = false" class="text-white/80 hover:text-white text-2xl font-semibold leading-none">&times;</button>
                        </div>

                        {{-- Body Modal --}}
                        <form action="{{ route('nota-dinas.approve-sekban', $nota->id) }}" method="POST" class="p-6">
                            @csrf
                            @method('PATCH')

                            @if ($nota->disposisi_kabid)
                                <div class="mb-4 p-3 bg-blue-50/80 border border-blue-200 rounded-xl text-xs text-blue-950">
                                    <div class="flex items-center gap-1 font-semibold text-blue-800 mb-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path></svg>
                                        Catatan Disposisi Kabid Sebelumnya:
                                    </div>
                                    <p class="italic">"{{ $nota->disposisi_kabid }}"</p>
                                </div>
                            @endif

                            <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1.5">
                                Tulisan Disposisi / Arahan Sekban:
                            </label>

                            {{-- Pilihan Cepat / Preset Templates --}}
                            <div class="mb-3">
                                <p class="text-xs text-gray-500 mb-1.5 font-medium">Pilihan template cepat:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" @click="disposisiSekbanText = 'Diteruskan kepada Kepala Badan, mohon petunjuk dan persetujuan.'"
                                        class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-purple-100 text-gray-700 hover:text-purple-800 rounded-lg border border-gray-200 transition">
                                        + Mohon petunjuk Kaban
                                    </button>
                                    <button type="button" @click="disposisiSekbanText = 'Berdasarkan telaah, diusulkan untuk disetujui.'"
                                        class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-purple-100 text-gray-700 hover:text-purple-800 rounded-lg border border-gray-200 transition">
                                        + Diusulkan untuk disetujui
                                    </button>
                                    <button type="button" @click="disposisiSekbanText = 'Setuju diteruskan ke Kaban untuk ditindaklanjuti.'"
                                        class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-purple-100 text-gray-700 hover:text-purple-800 rounded-lg border border-gray-200 transition">
                                        + Setuju diteruskan
                                    </button>
                                    <button type="button" @click="disposisiSekbanText = 'Tindaklanjuti sesuai dengan ketentuan yang berlaku.'"
                                        class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-purple-100 text-gray-700 hover:text-purple-800 rounded-lg border border-gray-200 transition">
                                        + Tindaklanjuti sesuai ketentuan
                                    </button>
                                </div>
                            </div>

                            <textarea name="disposisi_sekban" rows="4" x-model="disposisiSekbanText" required
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-xl p-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white transition"
                                placeholder="Ketik tulisan disposisi atau arahan Sekban..."></textarea>
                            <p class="text-[11px] text-gray-500 mt-1">Catatan ini akan tersimpan dan dicetak pada lembar disposisi resmi.</p>

                            <div class="flex justify-end gap-2.5 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <button type="button" @click="showDisposisiModalSekban = false"
                                    class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2 text-sm font-semibold rounded-xl text-white bg-purple-600 hover:bg-purple-700 shadow-sm transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                    Kirim Disposisi ke Kaban
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- MODAL REVISI SEKBAN --}}
                <div x-show="showRevisiModalSekban" x-cloak
                    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4">
                    <div class="bg-white w-full max-w-md p-6 rounded-2xl shadow-xl">
                        <h2 class="font-bold text-lg mb-4 text-gray-800">Catatan Revisi Sekretaris Badan</h2>

                        <form action="{{ route('nota-dinas.revisi-sekban', $nota->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <textarea name="revisi" rows="4" class="w-full border rounded-xl p-3 mb-4 focus:ring-2 focus:ring-yellow-500 text-sm"
                                placeholder="Tuliskan bagian yang perlu diperbaiki..." required></textarea>

                            <div class="flex justify-end gap-2">
                                <button type="button" @click="showRevisiModalSekban = false"
                                    class="px-4 py-2 border rounded-xl text-sm font-medium">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-xl">Kirim Revisi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Persetujuan Kepala Badan (ACC Kaban) --}}
        @if ($canApproveKaban)
            <div class="mt-10 p-5 border border-emerald-200 bg-emerald-50/50 rounded-2xl shadow-sm"
                x-data="{
                    showRevisiModalKaban: false,
                    showDisposisiModalKaban: false,
                    disposisiKabanText: 'Setuju, agar diproses dan ditindaklanjuti sesuai dengan ketentuan yang berlaku.'
                }">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-bold text-emerald-900 flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        Persetujuan Kepala Badan (ACC Kaban):
                    </p>
                    <span class="text-xs text-emerald-700 font-medium bg-emerald-100/80 px-2.5 py-1 rounded-full">
                        Tahap Akhir Persetujuan Kaban
                    </span>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="showDisposisiModalKaban = true"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-sm transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tulis Disposisi &amp; ACC Kaban
                    </button>

                    <x-ui.button variant="yellow" @click="showRevisiModalKaban = true">Revisi Kaban</x-ui.button>

                    <form action="{{ route('nota-dinas.reject-kaban', $nota->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak nota dinas ini?')">
                        @csrf @method('PATCH')
                        <x-ui.button variant="red" type="submit">Tolak</x-ui.button>
                    </form>
                </div>

                {{-- MODAL DISPOSISI KABAN --}}
                <div x-show="showDisposisiModalKaban" x-cloak
                    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
                    <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="showDisposisiModalKaban = false"></div>

                    <div class="relative bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden border border-gray-100 z-10">
                        {{-- Header Modal --}}
                        <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-700 text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/20 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-white">Disposisi Kepala Badan</h3>
                                    <p class="text-xs text-emerald-100">Persetujuan Akhir (ACC Kaban)</p>
                                </div>
                            </div>
                            <button type="button" @click="showDisposisiModalKaban = false" class="text-white/80 hover:text-white text-2xl font-semibold leading-none">&times;</button>
                        </div>

                        {{-- Body Modal --}}
                        <form action="{{ route('nota-dinas.approve-kaban', $nota->id) }}" method="POST" class="p-6">
                            @csrf
                            @method('PATCH')

                            {{-- Info Disposisi Atasan Sebelumnya --}}
                            @if ($nota->disposisi_kabid || $nota->disposisi_sekban)
                                <div class="mb-4 space-y-2">
                                    @if ($nota->disposisi_kabid)
                                        <div class="p-2.5 bg-blue-50/70 border border-blue-200 rounded-xl text-xs text-blue-900">
                                            <span class="font-semibold text-blue-800">Catatan Kabid:</span>
                                            <span class="italic">"{{ $nota->disposisi_kabid }}"</span>
                                        </div>
                                    @endif
                                    @if ($nota->disposisi_sekban)
                                        <div class="p-2.5 bg-purple-50/70 border border-purple-200 rounded-xl text-xs text-purple-900">
                                            <span class="font-semibold text-purple-800">Catatan Sekban:</span>
                                            <span class="italic">"{{ $nota->disposisi_sekban }}"</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1.5">
                                Arahan / Tulisan Disposisi Kaban:
                            </label>

                            {{-- Pilihan Cepat / Preset Templates --}}
                            <div class="mb-3">
                                <p class="text-xs text-gray-500 mb-1.5 font-medium">Pilihan arahan cepat:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" @click="disposisiKabanText = 'Setuju, agar diproses dan ditindaklanjuti sesuai dengan ketentuan yang berlaku.'"
                                        class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-800 rounded-lg border border-gray-200 transition">
                                        + Setuju, tindaklanjuti sesuai ketentuan
                                    </button>
                                    <button type="button" @click="disposisiKabanText = 'Setuju, laksanakan dengan penuh tanggung jawab dan laporkan hasilnya.'"
                                        class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-800 rounded-lg border border-gray-200 transition">
                                        + Setuju &amp; laporkan hasil
                                    </button>
                                    <button type="button" @click="disposisiKabanText = 'Setuju, dengan tetap memperhatikan prinsip efisiensi anggaran.'"
                                        class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-800 rounded-lg border border-gray-200 transition">
                                        + Perhatikan efisiensi anggaran
                                    </button>
                                    <button type="button" @click="disposisiKabanText = 'Setuju.'"
                                        class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-emerald-100 text-gray-700 hover:text-emerald-800 rounded-lg border border-gray-200 transition">
                                        + Setuju
                                    </button>
                                </div>
                            </div>

                            <textarea name="disposisi_kaban" rows="4" x-model="disposisiKabanText" required
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-xl p-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:text-white transition"
                                placeholder="Ketik tulisan arahan disposisi Kepala Badan..."></textarea>
                            <p class="text-[11px] text-gray-500 mt-1">Disposisi ini menjadi dasar penerbitan SPT dan SPPD resmi perjalanan dinas.</p>

                            <div class="flex justify-end gap-2.5 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <button type="button" @click="showDisposisiModalKaban = false"
                                    class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2 text-sm font-semibold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    ACC &amp; Terbitkan Disposisi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- MODAL REVISI KABAN --}}
                <div x-show="showRevisiModalKaban" x-cloak
                    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4">
                    <div class="bg-white w-full max-w-md p-6 rounded-2xl shadow-xl">
                        <h2 class="font-bold text-lg mb-4 text-gray-800">Catatan Revisi Kepala Badan</h2>

                        <form action="{{ route('nota-dinas.revisi-kaban', $nota->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <textarea name="revisi" rows="4" class="w-full border rounded-xl p-3 mb-4 focus:ring-2 focus:ring-yellow-500 text-sm"
                                placeholder="Tuliskan catatan revisi Kaban..." required></textarea>

                            <div class="flex justify-end gap-2">
                                <button type="button" @click="showRevisiModalKaban = false"
                                    class="px-4 py-2 border rounded-xl text-sm font-medium">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-xl">Kirim Revisi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
