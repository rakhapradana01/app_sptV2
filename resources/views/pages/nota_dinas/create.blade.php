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
                        <div>
                            <label class="block mb-2 text-sm font-medium">Kepada (kepala badan) </label>
                            <select name="kepada_id" class="w-full border rounded-lg p-2">
                                @foreach ($kepalaBadan as $pegawai)
                                    <option value="{{ $pegawai->id }}">
                                        {{ $pegawai->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Melalui (kepala bidang)</label>
                            <select name="melalui_id" class="w-full border rounded-lg p-2">
                                @foreach ($kepalaBidang as $pegawai)
                                    <option value="{{ $pegawai->id }}">
                                        {{ $pegawai->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Dari (kepala sub bidang)</label>
                            <select name="dari_id" x-model="selectedKasubid" class="w-full border rounded-lg p-2">
                                <option value="">-- Pilih --</option>
                                @foreach ($kasubid as $pegawai)
                                    <option value="{{ $pegawai->id }}">
                                        {{ $pegawai->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Sub Kegiatan</label>
                            <select name="sub_kegiatan_id" :disabled="!selectedKasubid"
                                class="w-full border rounded-lg p-2">
                                <option value="">-- Pilih --</option>

                                <template x-for="sub in subKegiatans.filter(s => s.pegawai_kasubid_id == selectedKasubid)"
                                    :key="sub.id">
                                    <option :value="sub.id" x-text="sub.nomor_rekening + ' - ' + sub.nama_kegiatan">
                                    </option>
                                </template>
                            </select>
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block mb-2 text-sm font-medium">
                                Nomor Nota Dinas
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
                        <div>
                            <label class="block mb-2 text-sm font-medium">Tanggal Nota</label>
                            <input type="date" name="tanggal" onclick="this.showPicker()"
                                class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block mb-2 text-sm font-medium">Perihal</label>
                            <input type="text" name="perihal" value="Mohon persetujuan Pejalanan Dinas Dalam Rangka "
                                class="w-full border rounded-lg p-2">
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block mb-2 text-sm font-medium">Kegiatan</label>
                            <input type="text" name="kegiatan" value="Menghadiri " class="w-full border rounded-lg p-2">
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block mb-2 text-sm font-medium">Undangan</label>
                            <input type="text" name="asal_undangan" value="Sehubungan dengan undangan dari "
                                class="w-full border rounded-lg p-2">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Tujuan</label>
                            <input type="text" name="lokasi" class="w-full border rounded-lg p-2"
                                placeholder="D.K.I Jakarta">
                        </div>

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
                                            {{ $pegawai->nama }}
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

                        <div>
                            <label class="block mb-2 text-sm font-medium">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="w-full border rounded-lg p-2">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="w-full border rounded-lg p-2">
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
            </form>

        </x-common.component-card>
    </div>

    <script>
        function notaDinasForm() {
            return {
                selectedKasubid: '',
                subKegiatans: @json($subKegiatans),
                pegawaisDilibatkan: [],
                addPegawai(select) {
                    let id = select.value;
                    let nama = select.options[select.selectedIndex].text;

                    if (id && !this.pegawaisDilibatkan.find(p => p.id === id)) {
                        this.pegawaisDilibatkan.push({
                            id: id,
                            nama: nama
                        });
                        select.value = ''; // Reset select
                    }
                },

                // Fungsi Hapus Pegawai
                removePegawai(index) {
                    this.pegawaisDilibatkan.splice(index, 1);
                }
            }
        }
    </script>
@endsection
