@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Nota Dinas" />

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

        <x-common.component-card title="Edit Nota Dinas">

            <form method="POST" action="{{ route('nota-dinas.update', $nota->id) }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-sm font-medium">Kepada (kepala badan)</label>
                            <select name="kepada_id" class="w-full border rounded-lg p-2">
                                @foreach ($kepalaBadan as $pegawai)
                                    <option value="{{ $pegawai->id }}" {{ $nota->kepada_id == $pegawai->id ? 'selected' : '' }}>
                                        {{ $pegawai->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Melalui (kepala bidang)</label>
                            <select name="melalui_id" class="w-full border rounded-lg p-2">
                                <option value="">-- Tidak Ada --</option>
                                @foreach ($kepalaBidang as $pegawai)
                                    <option value="{{ $pegawai->id }}" {{ $nota->melalui_id == $pegawai->id ? 'selected' : '' }}>
                                        {{ $pegawai->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Dari (kepala sub bidang)</label>
                            @if ($userLogin->pegawai_id)
                                <input type="hidden" name="dari_id" value="{{ $userLogin->pegawai_id }}">
                                <input type="text" readonly
                                    value="{{ $userLogin->name }} ({{ $userLogin->pegawai->nama ?? '-' }})"
                                    class="w-full border rounded-lg p-2 bg-gray-100 text-gray-700 cursor-not-allowed">
                            @else
                                <div class="w-full border border-yellow-400 rounded-lg p-2 bg-yellow-50">
                                    <p class="text-yellow-700 text-sm font-medium">
                                        ⚠️ Akun <strong>{{ $userLogin->name }}</strong> belum terhubung ke data pegawai.
                                    </p>
                                    <p class="text-yellow-600 text-xs mt-1">
                                        Hubungi admin untuk menghubungkan akun Anda ke data pegawai.
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Sub Kegiatan</label>
                            <select name="sub_kegiatan_id" class="w-full border rounded-lg p-2">
                                <option value="">-- Pilih --</option>
                                @foreach ($subKegiatans as $sub)
                                    <option value="{{ $sub->id }}" {{ $nota->sub_kegiatan_id == $sub->id ? 'selected' : '' }}>
                                        {{ $sub->nomor_rekening }} - {{ $sub->nama_kegiatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block mb-2 text-sm font-medium">Nomor Nota Dinas</label>
                            <div class="flex items-center border rounded-lg overflow-hidden bg-gray-50">
                                <span class="px-3 py-2 bg-gray-100 text-gray-700 font-medium">900.1 /</span>
                                <input type="text" disabled
                                    value="{{ $nota->nomor_urut ?? '' }}"
                                    placeholder="Nomor akan dibuat otomatis"
                                    class="flex-1 px-3 py-2 bg-white text-center text-gray-400 cursor-not-allowed">
                                <span class="px-3 py-2 bg-gray-100 text-gray-700 font-medium">/ BPKAD / {{ date('Y') }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Nomor urut tidak dapat diubah.</p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Tanggal Nota</label>
                            <input type="date" name="tanggal" onclick="this.showPicker()"
                                value="{{ $nota->tanggal ? \Carbon\Carbon::parse($nota->tanggal)->format('Y-m-d') : '' }}"
                                class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:col-span-2">
                            <div>
                                <label class="block mb-2 text-sm font-medium">Sifat</label>
                                <select name="sifat" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                                    @foreach (['Biasa', 'Penting', 'Segera', 'Sangat Segera'] as $sifat)
                                        <option value="{{ $sifat }}" {{ $nota->sifat == $sifat ? 'selected' : '' }}>
                                            {{ $sifat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium">Lampiran</label>
                                <input type="text" name="lampiran" value="{{ old('lampiran', $nota->lampiran) }}"
                                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                                    placeholder="Contoh: 1 (satu) Berkas">
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block mb-2 text-sm font-medium">Perihal</label>
                            <input type="text" name="perihal" value="{{ old('perihal', $nota->perihal) }}"
                                class="w-full border rounded-lg p-2">
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block mb-2 text-sm font-medium">Kegiatan</label>
                            <input type="text" name="kegiatan" value="{{ old('kegiatan', $nota->kegiatan) }}"
                                class="w-full border rounded-lg p-2">
                        </div>

                        <div class="lg:col-span-2" x-data="undanganEdit()">
                            <label class="block mb-2 text-sm font-medium">Undangan</label>

                            {{-- Toggle dengan/tanpa undangan --}}
                            <div class="flex items-center gap-4 mb-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="pakai_undangan" value="1"
                                        x-model="pakaiUndangan"
                                        class="accent-blue-600">
                                    <span class="text-sm font-medium">Dengan Undangan</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="pakai_undangan" value="0"
                                        x-model="pakaiUndangan"
                                        class="accent-blue-600">
                                    <span class="text-sm font-medium">Tanpa Undangan</span>
                                </label>
                            </div>

                            <div x-show="pakaiUndangan == '1'" x-transition>
                                <input type="text" name="asal_undangan"
                                    value="{{ old('asal_undangan', $nota->asal_undangan) }}"
                                    class="w-full border rounded-lg p-2">
                            </div>
                            <input x-show="pakaiUndangan == '0'" type="hidden" name="asal_undangan" value="">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Tujuan / Lokasi</label>
                            <input type="text" name="lokasi" value="{{ old('lokasi', $nota->lokasi) }}"
                                class="w-full border rounded-lg p-2" placeholder="D.K.I Jakarta">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Jenis Perjalanan</label>
                            <select name="jenis_perjalanan" class="w-full border rounded-lg p-2">
                                <option value="dalam_daerah" {{ $nota->jenis_perjalanan == 'dalam_daerah' ? 'selected' : '' }}>
                                    Dalam Daerah / Dalam Provinsi
                                </option>
                                <option value="luar_daerah" {{ $nota->jenis_perjalanan == 'luar_daerah' ? 'selected' : '' }}>
                                    Luar Daerah / Luar Provinsi
                                </option>
                            </select>
                        </div>

                        {{-- Pegawai yang Dilibatkan --}}
                        <div class="lg:col-span-2" x-data="pegawaiEdit()">
                            <label class="block mb-3 text-sm font-medium">Pegawai yang Dilibatkan</label>

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
                            <input type="date" name="tanggal_mulai" onclick="this.showPicker()"
                                value="{{ $nota->tanggal_mulai ? \Carbon\Carbon::parse($nota->tanggal_mulai)->format('Y-m-d') : '' }}"
                                class="w-full border rounded-lg p-2">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" onclick="this.showPicker()"
                                value="{{ $nota->tanggal_selesai ? \Carbon\Carbon::parse($nota->tanggal_selesai)->format('Y-m-d') : '' }}"
                                class="w-full border rounded-lg p-2">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('nota-dinas.index') }}" class="px-4 py-2 border rounded-lg">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>

        </x-common.component-card>
    </div>

    <script>
        function pegawaiEdit() {
            return {
                pegawais: @json($nota->pegawais->map(fn($p) => ['id' => (string)$p->id, 'nama' => $p->nama . ' - ' . $p->jabatan])),
                addPegawai(select) {
                    let id = select.value;
                    let nama = select.options[select.selectedIndex].text;
                    if (id && !this.pegawais.find(p => p.id === id)) {
                        this.pegawais.push({ id: id, nama: nama });
                        select.value = '';
                    }
                }
            }
        }

        function undanganEdit() {
            return {
                pakaiUndangan: '{{ $nota->asal_undangan ? "1" : "0" }}',
            }
        }
    </script>
@endsection
