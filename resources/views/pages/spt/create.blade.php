@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Buat SPT" />

    <div class="space-y-6">
        <x-common.component-card title="Form Surat Perintah Tugas (SPT)">

            @if ($errors->any())
                <div class="mx-2 mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $notaData = ($notaDinasList ?? collect())->map(fn($n) => [
                    'id'              => $n->id,
                    'nomor_urut'      => $n->nomor_urut,
                    'perihal'         => $n->perihal,
                    'kegiatan'        => $n->kegiatan ?: $n->perihal,
                    'lokasi'          => $n->lokasi,
                    'tanggal_mulai'   => $n->tanggal_mulai ? \Carbon\Carbon::parse($n->tanggal_mulai)->format('Y-m-d') : '',
                    'tanggal_selesai' => $n->tanggal_selesai ? \Carbon\Carbon::parse($n->tanggal_selesai)->format('Y-m-d') : '',
                    'sub_kegiatan_id' => $n->sub_kegiatan_id,
                    'pegawai_ids'     => $n->pegawais->pluck('id')->toArray(),
                    'pegawais'        => $n->pegawais->map(fn($p) => $p->nama . ' (' . $p->jabatan . ')')->join(', '),
                ])->values()->toJson();
            @endphp

            <div x-data="sptForm({{ $notaData }}, '{{ old('nota_dinas_id', $selectedNotaId ?? '') }}')" class="space-y-6">

                <form method="POST" action="{{ route('spt.storeMandiri') }}" class="space-y-6">
                    @csrf

                    <input type="hidden" name="nota_dinas_id" :value="selectedNotaId">

                    {{-- PILIH NOTA DINAS RUJUKAN --}}
                    <div class="border border-blue-200 dark:border-blue-700 rounded-xl p-5 space-y-4 bg-blue-50/40 dark:bg-blue-900/10">
                        <h4 class="text-sm font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wider flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Pilih Nota Dinas Rujukan (Telah di-ACC Kaban)
                        </h4>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nota Dinas yang Disetujui
                            </label>
                            <select x-model="selectedNotaId" @change="fillFromNota()"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Buat SPT Tanpa Nota Dinas (Mandiri) --</option>
                                @foreach ($notaDinasList ?? [] as $nota)
                                    <option value="{{ $nota->id }}">
                                        No. {{ $nota->nomor_urut ?? '-' }} — {{ \Illuminate\Support\Str::limit($nota->perihal, 70) }} ({{ $nota->status === \App\Models\NotaDinas::DISETUJUI_KABAN ? 'ACC Kaban' : 'Disetujui' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Jika memilih Nota Dinas, data sub kegiatan, tugas, lokasi, tanggal, dan pegawai akan terisi otomatis dan dapat disesuaikan.
                            </p>
                        </div>

                        {{-- Preview data Nota Dinas terpilih --}}
                        <div x-show="selectedNota" x-cloak
                            class="bg-white dark:bg-gray-800 border border-blue-100 dark:border-blue-800 rounded-lg p-4 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            <p class="font-semibold text-blue-700 dark:text-blue-300 text-xs uppercase tracking-wide mb-2">Data dari Nota Dinas</p>
                            <p><span class="font-medium">Perihal / Kegiatan:</span> <span x-text="selectedNota?.perihal"></span></p>
                            <p><span class="font-medium">Lokasi:</span> <span x-text="selectedNota?.lokasi"></span></p>
                            <p><span class="font-medium">Tanggal:</span> <span x-text="selectedNota?.tanggal_mulai"></span>
                                <template x-if="selectedNota?.tanggal_selesai && selectedNota.tanggal_selesai !== selectedNota.tanggal_mulai">
                                    <span> s/d <span x-text="selectedNota?.tanggal_selesai"></span></span>
                                </template>
                            </p>
                            <p><span class="font-medium">Pegawai Diusulkan:</span> <span x-text="selectedNota?.pegawais"></span></p>
                        </div>
                    </div>

                    {{-- INFORMASI SPT --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Informasi SPT</h4>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nomor SPT <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nomor_spt" required
                                    value="{{ old('nomor_spt', '800.1.11.1/      /BPKAD/' . date('Y')) }}"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Sub Kegiatan
                                </label>
                                <select name="sub_kegiatan_id" x-model="form.sub_kegiatan_id"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Sub Kegiatan (opsional) --</option>
                                    @foreach ($subKegiatans as $sub)
                                        <option value="{{ $sub->id }}" {{ old('sub_kegiatan_id') == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->nomor_rekening }} - {{ $sub->nama_kegiatan }} 
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Sumber Anggaran <span class="text-rose-500">*</span>
                                </label>
                                <select name="jenis_anggaran" required
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="DPA" {{ old('jenis_anggaran') == 'DPA' ? 'selected' : '' }}>DPA (Murni)</option>
                                    <option value="DPPA" {{ old('jenis_anggaran') == 'DPPA' ? 'selected' : '' }}>DPPA (Perubahan)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tahun Anggaran <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" name="tahun_anggaran" required min="2020" max="2099"
                                    value="{{ old('tahun_anggaran', date('Y')) }}"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    {{-- DETAIL TUGAS --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                            Detail Tugas
                            <span x-show="selectedNota" x-cloak class="text-xs font-normal text-blue-500 normal-case tracking-normal">(dapat disesuaikan sesuai kebutuhan)</span>
                        </h4>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Uraian Kegiatan / Tugas <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="kegiatan" required rows="3"
                                x-model="form.kegiatan"
                                placeholder="Contoh: Menghadiri Rapat Koordinasi Penganggaran Daerah..."
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('kegiatan') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Lokasi / Kota Tujuan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="lokasi" required
                                x-model="form.lokasi"
                                value="{{ old('lokasi') }}"
                                placeholder="Contoh: Jakarta"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tanggal Mulai <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="tanggal_mulai" required
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

                    {{-- DAFTAR PEGAWAI --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Pegawai yang Diperintah <span class="text-rose-500">*</span>
                            </h4>
                            <span class="text-xs text-gray-500" x-text="form.selectedPegawais.length + ' pegawai terpilih'"></span>
                        </div>

                        <div x-data="{ search: '' }">
                            <input type="text" x-model="search" placeholder="Cari nama pegawai..."
                                class="w-full mb-3 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">

                            <div class="max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-100 dark:divide-gray-700/50">
                                @foreach ($pegawais as $pegawai)
                                    <label x-show="search === '' || '{{ strtolower($pegawai->nama) }}'.includes(search.toLowerCase())"
                                        class="flex items-center gap-3 px-4 py-2.5 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer transition">
                                        <input type="checkbox" name="pegawai_ids[]" value="{{ $pegawai->id }}"
                                            :checked="form.selectedPegawais.includes({{ $pegawai->id }})"
                                            @change="togglePegawai({{ $pegawai->id }})"
                                            class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $pegawai->nama }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $pegawai->jabatan }} — {{ $pegawai->pangkat }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @error('pegawai_ids')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- AKSI --}}
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('spt.index') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Batal
                        </a>
                        <x-ui.button type="submit" variant="primary">
                            Simpan SPT
                        </x-ui.button>
                    </div>
                </form>
            </div>

        </x-common.component-card>
    </div>

    <script>
        function sptForm(notaList, initialNotaId) {
            return {
                notaList: notaList || [],
                selectedNotaId: initialNotaId || '',
                selectedNota: null,
                form: {
                    sub_kegiatan_id: '{{ old('sub_kegiatan_id') }}',
                    kegiatan: '{{ old('kegiatan') }}',
                    lokasi: '{{ old('lokasi') }}',
                    tanggal_mulai: '{{ old('tanggal_mulai') }}',
                    tanggal_selesai: '{{ old('tanggal_selesai') }}',
                    selectedPegawais: @json(old('pegawai_ids', [])),
                },
                init() {
                    if (this.selectedNotaId) {
                        this.fillFromNota();
                    }
                },
                fillFromNota() {
                    const found = this.notaList.find(n => n.id == this.selectedNotaId);
                    this.selectedNota = found || null;
                    if (found) {
                        this.form.sub_kegiatan_id = found.sub_kegiatan_id || '';
                        this.form.kegiatan = found.kegiatan || found.perihal || '';
                        this.form.lokasi = found.lokasi || '';
                        this.form.tanggal_mulai = found.tanggal_mulai || '';
                        this.form.tanggal_selesai = found.tanggal_selesai || '';
                        if (found.pegawai_ids && found.pegawai_ids.length > 0) {
                            this.form.selectedPegawais = [...found.pegawai_ids];
                        }
                    }
                },
                togglePegawai(id) {
                    const idx = this.form.selectedPegawais.indexOf(id);
                    if (idx > -1) {
                        this.form.selectedPegawais.splice(idx, 1);
                    } else {
                        this.form.selectedPegawais.push(id);
                    }
                }
            }
        }
    </script>
@endsection
