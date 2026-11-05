@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Nota Dinas" />

    <div class="space-y-6">
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <x-common.component-card title="Form Nota Dinas">

            <form method="POST" action="{{ route('nota-dinas.store') }}">
                @csrf

                <div x-data="notaDinasForm()" class="space-y-6">

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        {{-- Yth --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Yth (Kepala Badan)</label>
                            <select name="kepada_id" class="w-full border rounded-lg p-2">
                                @foreach ($kepalaBadan as $pegawai)
                                    <option value="{{ $pegawai->id }}" {{ old('kepada_id') == $pegawai->id ? 'selected' : '' }}>
                                        {{ $pegawai->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Dari --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Dari</label>
                            @if ($userLogin->pegawai_id)
                                {{-- User login sudah terhubung ke data pegawai --}}
                                <input type="hidden" name="dari_id" value="{{ $userLogin->pegawai_id }}">
                                <input type="text" readonly
                                    value="{{ $userLogin->name }} ({{ $userLogin->pegawai->nama ?? '-' }})"
                                    class="w-full border rounded-lg p-2 bg-gray-100 text-gray-700 cursor-not-allowed">
                            @else
                                {{-- User belum terhubung ke data pegawai, tampilkan peringatan --}}
                                <div class="w-full border border-yellow-400 rounded-lg p-2 bg-yellow-50">
                                    <p class="text-yellow-700 text-sm font-medium">
                                        ⚠️ Akun <strong>{{ $userLogin->name }}</strong> belum terhubung ke data pegawai.
                                    </p>
                                    <p class="text-yellow-600 text-xs mt-1">
                                        Hubungi admin untuk menghubungkan akun Anda ke data pegawai agar bisa membuat Nota
                                        Dinas.
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Tembusan --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Tembusan (Kepala Bidang)</label>
                            <select name="melalui_id" class="w-full border rounded-lg p-2">
                                @forelse ($kepalaBidang as $pegawai)
                                    <option value="{{ $pegawai->id }}" {{ old('melalui_id', $kepalaBidang->first()->id ?? '') == $pegawai->id ? 'selected' : '' }}>
                                        {{ $pegawai->nama }} - {{ $pegawai->jabatan }}
                                    </option>
                                @empty
                                    <option value="">-- Belum ada data kepala bidang --</option>
                                @endforelse
                            </select>
                        </div>

                        {{-- Tanggal --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                                onclick="this.showPicker()"
                                class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        {{-- Nomor --}}
                        <div class="lg:col-span-2">
                            <label class="block mb-2 text-sm font-medium">
                                Nomor
                            </label>

                            <div class="flex items-center border rounded-lg overflow-hidden bg-gray-50">

                                <span class="px-3 py-2 bg-gray-100 text-gray-700 font-medium">
                                    900.1 /
                                </span>


                                <input type="text" disabled placeholder="Nomor akan dibuat otomatis"
                                    class="flex-1 px-3 py-2 bg-white text-center text-gray-400 cursor-not-allowed">

                                <span class="px-3 py-2 bg-gray-100 text-gray-700 font-medium">
                                    / BPKAD / {{ date('Y') }}
                                </span>

                            </div>

                            <p class="text-xs text-gray-500 mt-1">
                                Nomor urut akan diisi otomatis oleh sistem.
                            </p>
                        </div>

                        {{-- Sifat --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Sifat</label>
                            <select name="sifat"
                                class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                                @foreach (['Biasa', 'Penting', 'Segera', 'Sangat Segera'] as $sifat)
                                    <option value="{{ $sifat }}" {{ old('sifat') == $sifat ? 'selected' : '' }}>
                                        {{ $sifat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Lampiran --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Lampiran</label>
                            <input type="text" name="lampiran" value="{{ old('lampiran', '1 (satu) Berkas') }}"
                                class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                                placeholder="Contoh: 1 (satu) Berkas">
                        </div>

                        {{-- Hal --}}
                        <div class="lg:col-span-2">
                            <label class="block mb-2 text-sm font-medium">Hal</label>
                            <input type="text" name="perihal"
                                value="{{ old('perihal', 'Mohon persetujuan Perjalanan Dinas Dalam Rangka ') }}"
                                class="w-full border rounded-lg p-2">
                        </div>

                        {{-- Sub Kegiatan --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Sub Kegiatan</label>
                            <select name="sub_kegiatan_id" :disabled="!selectedKasubid"
                                class="w-full border rounded-lg p-2">
                                <option value="">-- Pilih --</option>

                                <template x-for="sub in subKegiatans" :key="sub.id">
                                    <option :value="sub.id" x-text="sub.nomor_rekening + ' - ' + sub.nama_kegiatan">
                                    </option>
                                </template>
                            </select>
                        </div>

                        {{-- Kegiatan --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Kegiatan</label>
                            <input type="text" name="kegiatan" value="{{ old('kegiatan', 'Dalam rangka melaksanakan ') }}"
                                class="w-full border rounded-lg p-2">
                        </div>

                        {{-- Undangan --}}
                        <div class="lg:col-span-2">
                            <label class="block mb-2 text-sm font-medium">
                                Konteks / Undangan
                            </label>

                            <div class="flex items-center gap-4 mb-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="pakai_undangan" value="1" x-model="pakaiUndangan"
                                        class="accent-blue-600">
                                    <span class="text-sm font-medium">Dengan Undangan</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="pakai_undangan" value="0" x-model="pakaiUndangan"
                                        class="accent-blue-600">
                                    <span class="text-sm font-medium">Tanpa Undangan</span>
                                </label>
                            </div>

                            <input type="text" name="asal_undangan" x-model="asalUndangan" :disabled="pakaiUndangan != '1'"
                                placeholder="Sehubungan dengan undangan dari ..."
                                class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">

                            <p class="text-xs text-gray-500 mt-1" x-show="pakaiUndangan == '1'">
                                Awali dengan kalimat "Sehubungan dengan".
                            </p>
                        </div>

                        {{-- Lokasi --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Tujuan / Lokasi</label>
                            <input type="text" name="lokasi" value="{{ old('lokasi') }}"
                                class="w-full border rounded-lg p-2" placeholder="D.K.I Jakarta">
                        </div>

                        {{-- Jenis Perjalanan --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Jenis Perjalanan</label>
                            <select name="jenis_perjalanan" class="w-full border rounded-lg p-2">
                                <option value="dalam_daerah" {{ old('jenis_perjalanan') == 'dalam_daerah' ? 'selected' : '' }}>
                                    Dalam Daerah / Dalam Provinsi
                                </option>
                                <option value="luar_daerah" {{ old('jenis_perjalanan') == 'luar_daerah' ? 'selected' : '' }}>
                                    Luar Daerah / Luar Provinsi
                                </option>
                            </select>
                        </div>

                        {{-- Pegawai yang Dilibatkan --}}
                        <div class="lg:col-span-2" x-data="{
                                            pegawais: [],
                                            addPegawai(select) {
                                                let id = select.value;
                                                let nama = select.options[select.selectedIndex].text;

                                                if (id && !this.pegawais.find(p => p.id === id)) {
                                                    this.pegawais.push({ id: id, nama: nama });
                                                    select.value = '';
                                                }
                                            }
                                        }">

                            <label class="block mb-3 text-sm font-medium">
                                Pegawai yang Dilibatkan
                            </label>

                            <div class="flex gap-3">
                                <select x-ref="pegawaiSelect" class="w-full border rounded-lg p-2">
                                    <option value="">-- Pilih Pegawai --</option>
                                    @foreach ($staff as $pegawai)
                                        <option value="{{ $pegawai->id }}">
                                            {{ $pegawai->nama }} - {{ $pegawai->jabatan }}
                                        </option>
                                    @endforeach
                                </select>

                                <button type="button" @click="addPegawai($refs.pegawaiSelect)"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg">
                                    Tambah
                                </button>
                            </div>

                            {{-- LIST --}}
                            <div class="mt-4 space-y-2">
                                <template x-for="(pegawai, index) in pegawais" :key="pegawai.id">
                                    <div class="flex items-center justify-between border p-3 rounded-lg bg-gray-50">

                                        <span x-text="pegawai.nama"></span>

                                        <div>
                                            <input type="hidden" name="pegawai_ids[]" :value="pegawai.id">

                                            <button type="button" @click="pegawais.splice(index, 1)"
                                                class="text-red-600 text-sm">
                                                Hapus
                                            </button>
                                        </div>

                                    </div>
                                </template>
                            </div>

                        </div>

                        {{-- Tanggal Mulai / Selesai --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                                onclick="this.showPicker()" class="w-full border rounded-lg p-2">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                                onclick="this.showPicker()" class="w-full border rounded-lg p-2">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('nota-dinas.index') }}" class="px-4 py-2 border rounded-lg">
                            Batal
                        </a>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>

        </x-common.component-card>
    </div>

    <script>
        function notaDinasForm() {
            return {
                selectedKasubid: '{{ $userLogin->id }}',
                subKegiatans: @json($subKegiatans),

                pakaiUndangan: '{{ old('asal_undangan') ? '1' : '0' }}',

                asalUndangan: @json(
                    old('asal_undangan', 'Sehubungan dengan ')
                ),

                pegawais: [],

                addPegawai(select) {
                    let id = select.value;
                    let nama = select.options[select.selectedIndex].text;

                    if (id && !this.pegawais.find(p => p.id === id)) {
                        this.pegawais.push({ id: id, nama: nama });
                        select.value = '';
                    }
                },

                removePegawai(index) {
                    this.pegawais.splice(index, 1);
                }
            }
        }
    </script>
@endsection