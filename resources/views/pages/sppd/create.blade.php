@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Buat SPPD" />

    <div class="space-y-6">
        <x-common.component-card title="Form SPPD">

            @if ($errors->any())
                <div class="mx-2 mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Data SPT untuk Alpine.js --}}
            @php
                $sptData = $spts->map(fn($s) => [
                    'id'              => $s->id,
                    'label'           => $s->nomor_spt . ' — ' . \Illuminate\Support\Str::limit($s->kegiatan, 60),
                    'nomor_spt'       => $s->nomor_spt,
                    'kegiatan'        => $s->kegiatan,
                    'lokasi'          => $s->lokasi,
                    'tanggal_mulai'   => $s->tanggal_mulai?->format('Y-m-d') ?? '',
                    'tanggal_selesai' => $s->tanggal_selesai?->format('Y-m-d') ?? '',
                    'pegawais'        => $s->pegawais->map(fn($p) => $p->nama . ' (' . $p->jabatan . ')')->join(', '),
                ])->values()->toJson();
            @endphp

            <div x-data="sppdForm({{ $sptData }})" class="space-y-6">

                <form method="POST" action="{{ route('sppd.storeMandiri') }}" class="space-y-6">
                    @csrf

                    {{-- PILIH SPT --}}
                    <div class="border border-blue-200 dark:border-blue-700 rounded-xl p-5 space-y-4 bg-blue-50/40 dark:bg-blue-900/10">
                        <h4 class="text-sm font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wider flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Pilih SPT Rujukan <span class="text-rose-500">*</span>
                        </h4>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                SPT yang sudah dibuat
                            </label>
                            <select name="spt_id" required x-model="selectedSptId" @change="fillFromSpt()"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih SPT --</option>
                                @foreach ($spts as $spt)
                                    <option value="{{ $spt->id }}" {{ old('spt_id') == $spt->id ? 'selected' : '' }}>
                                        {{ $spt->nomor_spt }} — {{ \Illuminate\Support\Str::limit($spt->kegiatan, 70) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('spt_id')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Preview data SPT terpilih --}}
                        <div x-show="selectedSpt" x-cloak
                            class="bg-white dark:bg-gray-800 border border-blue-100 dark:border-blue-800 rounded-lg p-4 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            <p class="font-semibold text-blue-700 dark:text-blue-300 text-xs uppercase tracking-wide mb-2">Data dari SPT</p>
                            <p><span class="font-medium">Kegiatan:</span> <span x-text="selectedSpt?.kegiatan"></span></p>
                            <p><span class="font-medium">Lokasi:</span> <span x-text="selectedSpt?.lokasi"></span></p>
                            <p><span class="font-medium">Tanggal:</span> <span x-text="selectedSpt?.tanggal_mulai"></span>
                                <template x-if="selectedSpt?.tanggal_selesai && selectedSpt.tanggal_selesai !== selectedSpt.tanggal_mulai">
                                    <span> s/d <span x-text="selectedSpt?.tanggal_selesai"></span></span>
                                </template>
                            </p>
                            <p><span class="font-medium">Pegawai:</span> <span x-text="selectedSpt?.pegawais"></span></p>
                        </div>
                    </div>

                    {{-- INFORMASI SPPD --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Informasi SPPD</h4>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nomor SPPD <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nomor_sppd" required
                                    value="{{ old('nomor_sppd', '800.1.11.1/      /BPKAD/' . date('Y')) }}"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Alat Angkutan <span class="text-rose-500">*</span>
                                </label>
                                <select name="alat_angkutan" required
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="mobil" {{ old('alat_angkutan') == 'mobil' ? 'selected' : '' }}>Mobil</option>
                                    <option value="pesawat dan mobil" {{ old('alat_angkutan') == 'pesawat dan mobil' ? 'selected' : '' }}>Pesawat dan Mobil</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tanggal Pembuatan SPPD <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="tanggal_sppd" required
                                    value="{{ old('tanggal_sppd', date('Y-m-d')) }}"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    {{-- RUTE & WAKTU (pre-fill dari SPT, bisa diedit) --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                            Rute &amp; Waktu Perjalanan
                            <span x-show="selectedSpt" x-cloak class="text-xs font-normal text-blue-500 normal-case tracking-normal">(terisi otomatis dari SPT, dapat diedit)</span>
                        </h4>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tempat Berangkat <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="tempat_berangkat" required
                                    value="{{ old('tempat_berangkat', 'Banjarbaru') }}"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tempat Tujuan Utama <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="tempat_tujuan" required
                                    x-model="form.tempat_tujuan"
                                    value="{{ old('tempat_tujuan') }}"
                                    placeholder="Contoh: Jakarta"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tempat Tujuan Tambahan <span class="text-gray-400 font-normal">(opsional)</span>
                                </label>
                                <input type="text" name="tempat_tujuan_2"
                                    value="{{ old('tempat_tujuan_2') }}"
                                    placeholder="Contoh: Bogor"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tanggal Mulai <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="tanggal_mulai"
                                    x-model="form.tanggal_mulai"
                                    value="{{ old('tanggal_mulai') }}"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tanggal Selesai <span class="text-gray-400 font-normal">(jika lebih dari 1 hari)</span>
                                </label>
                                <input type="date" name="tanggal_selesai"
                                    x-model="form.tanggal_selesai"
                                    value="{{ old('tanggal_selesai') }}"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    {{-- KEGIATAN (pre-fill dari SPT, bisa diedit) --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                            Maksud Perjalanan
                            <span x-show="selectedSpt" x-cloak class="text-xs font-normal text-blue-500 normal-case tracking-normal">(terisi dari SPT)</span>
                        </h4>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Kegiatan / Maksud Perjalanan Dinas
                            </label>
                            <textarea name="kegiatan" rows="3"
                                x-model="form.kegiatan"
                                placeholder="Otomatis terisi dari SPT, atau isi manual..."
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('kegiatan') }}</textarea>
                        </div>
                    </div>

                    {{-- INFO PEGAWAI (read-only, dari SPT) --}}
                    <div x-show="selectedSpt" x-cloak
                        class="border border-green-200 dark:border-green-700 rounded-xl p-5 space-y-3 bg-green-50/40 dark:bg-green-900/10">
                        <h4 class="text-sm font-semibold text-green-700 dark:text-green-300 uppercase tracking-wider flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            Pegawai yang Melaksanakan
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Pegawai diambil otomatis dari SPT yang dipilih:
                        </p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="selectedSpt?.pegawais"></p>
                        <p class="text-xs text-gray-400">Untuk mengubah pegawai, edit SPT terkait terlebih dahulu.</p>
                    </div>

                    {{-- AKSI --}}
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('sppd.index') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Batal
                        </a>
                        <button type="submit"
                            x-bind:disabled="!selectedSpt"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition">
                            Simpan SPPD
                        </button>
                    </div>
                </form>
            </div>

        </x-common.component-card>
    </div>

    <script>
        function sppdForm(sptList) {
            return {
                sptList: sptList,
                selectedSptId: '{{ old('spt_id') }}',
                selectedSpt: null,
                form: {
                    tempat_tujuan: '{{ old('tempat_tujuan') }}',
                    tanggal_mulai: '{{ old('tanggal_mulai') }}',
                    tanggal_selesai: '{{ old('tanggal_selesai') }}',
                    kegiatan: '{{ old('kegiatan') }}',
                },
                init() {
                    if (this.selectedSptId) {
                        this.fillFromSpt();
                    }
                },
                fillFromSpt() {
                    const found = this.sptList.find(s => s.id == this.selectedSptId);
                    this.selectedSpt = found || null;
                    if (found) {
                        this.form.tempat_tujuan   = found.lokasi;
                        this.form.tanggal_mulai   = found.tanggal_mulai;
                        this.form.tanggal_selesai = found.tanggal_selesai;
                        this.form.kegiatan        = found.kegiatan;
                    }
                },
            }
        }
    </script>
@endsection
