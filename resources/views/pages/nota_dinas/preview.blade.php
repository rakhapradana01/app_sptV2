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
                <td>{{ $nota->kepada->nama }}</td>
            </tr>
            <tr>
                <td class="label">Dari</td>
                <td class="colon">:</td>
                <td>{{ $nota->dari->nama }}</td>
            </tr>
            <tr>
                <td class="label">Melalui</td>
                <td class="colon">:</td>
                <td>{{ $nota->melalui->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal</td>
                <td class="colon">:</td>
                <td>{{ \Carbon\Carbon::parse($nota->tanggal)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Nomor</td>
                <td class="colon">:</td>
                <td>{{ $nota->nomor_urut }}</td>
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
            untuk melaksanakan perjalanan dinas biasa yakni perjalanan dinas dalam rangka {{ $nota->perihal }}
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
                <div x-data="{ open: false }" class="mt-4">

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

        @if (auth()->user()->role->name == 'kepala_bidang')
            <div class="mt-10" x-data="{ showRevisiModal: false }">
                <div class="flex gap-2">
                    <form action="{{ route('nota-dinas.approve-kabid', $nota->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <x-ui.button variant="success" type="submit">Setujui</x-ui.button>
                    </form>

                    <x-ui.button variant="yellow" @click="showRevisiModal = true">Revisi</x-ui.button>

                    <form action="{{ route('nota-dinas.reject-kabid', $nota->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <x-ui.button variant="red" type="submit">Tolak</x-ui.button>
                    </form>
                </div>

                <div x-show="showRevisiModal" x-cloak
                    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                    <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                        <h2 class="font-bold text-lg mb-4">Catatan Revisi</h2>

                        <form action="{{ route('nota-dinas.revisi-kabid', $nota->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <textarea name="revisi" rows="4"
                                class="w-full border rounded-lg p-2 mb-4 focus:ring-2 focus:ring-yellow-500"
                                placeholder="Tuliskan bagian yang perlu diperbaiki..." required></textarea>

                            <div class="flex justify-end gap-2">
                                <button type="button" @click="showRevisiModal = false"
                                    class="px-4 py-2 border rounded">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded">Kirim
                                    Revisi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
