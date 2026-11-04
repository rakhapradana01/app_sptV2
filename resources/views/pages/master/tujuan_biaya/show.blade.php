@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detail SHS — {{ $shs->lokasi_tujuan }}" />
    <div class="space-y-6">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-medium text-red-800 dark:text-red-300">Terjadi kesalahan:</p>
                        <ul class="mt-1 space-y-1 text-sm text-red-600 dark:text-red-400">
                            @foreach($errors->all() as $error)
                                <li>&bull; {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Navigasi kembali --}}
        <a href="{{ route('master-shs.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Daftar SHS
        </a>

        {{-- ====================================== --}}
        {{-- CARD 1: DATA UTAMA SHS --}}
        {{-- ====================================== --}}
        <x-common.component-card title="Data Utama SHS (Sesuai Standar Pergub)">
            <form method="POST" action="{{ route('master-shs.update', $shs->id) }}"
                  x-data="{
                      bjbPergi: {{ old('transport_bjb_rumah_bandara', (float) $shs->transport_bjb_rumah_bandara) }},
                      bjbPulang: {{ old('transport_bjb_bandara_rumah', (float) $shs->transport_bjb_bandara_rumah) }},
                      tujPergi: {{ old('transport_tujuan_bandara_hotel', (float) $shs->transport_tujuan_bandara_hotel) }},
                      tujPulang: {{ old('transport_tujuan_hotel_bandara', (float) $shs->transport_tujuan_hotel_bandara) }},
                      totalBjb() {
                          return (parseFloat(this.bjbPergi) || 0) + (parseFloat(this.bjbPulang) || 0);
                      },
                      totalTujuan() {
                          return (parseFloat(this.tujPergi) || 0) + (parseFloat(this.tujPulang) || 0);
                      },
                      totalTransport() {
                          return this.totalBjb() + this.totalTujuan();
                      },
                      formatRupiah(val) {
                          return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
                      }
                  }"
                  class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Baris 1: Lokasi, Tahun, Status --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {{-- Lokasi Tujuan --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Lokasi Tujuan <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi_tujuan" value="{{ old('lokasi_tujuan', $shs->lokasi_tujuan) }}"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" required />
                    </div>

                    {{-- Tahun Anggaran --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Tahun Anggaran <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun_anggaran" value="{{ old('tahun_anggaran', $shs->tahun_anggaran) }}"
                               min="2020" max="2099"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" required />
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <div class="flex h-11 items-center">
                            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ $shs->is_active ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800" />
                                Status Aktif (Digunakan dalam sistem)
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Baris 2: Uang Harian & Tiket Pesawat --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {{-- Uang Harian --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Uang Harian (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="uang_harian" value="{{ old('uang_harian', (float) $shs->uang_harian) }}"
                               min="0" step="1000"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" required />
                    </div>

                    {{-- Tiket Pesawat --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Tiket Pesawat PP (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="tiket_pesawat" value="{{ old('tiket_pesawat', (float) $shs->tiket_pesawat) }}"
                               min="0" step="1000"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" required />
                    </div>
                </div>

                {{-- Baris 3: Penginapan (Pagu Maksimal) --}}
                <div class="rounded-xl border border-teal-200 bg-teal-50/40 p-4 dark:border-teal-800 dark:bg-teal-900/10">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex h-2 w-2 rounded-full bg-teal-500"></span>
                        <h5 class="text-xs font-semibold uppercase tracking-wider text-teal-800 dark:text-teal-300">
                            Penginapan (Pagu Maksimal)
                        </h5>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                Pagu Maksimal Penginapan per Malam (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">Rp</span>
                                <input type="number" name="penginapan" value="{{ old('penginapan', (float) $shs->penginapan) }}"
                                       min="0" step="1000" placeholder="0"
                                       class="h-11 w-full rounded-lg border border-teal-300 bg-white pl-10 pr-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 dark:border-teal-700 dark:bg-gray-900 dark:text-white" required />
                            </div>
                        </div>
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 leading-relaxed sm:pt-4">
                            Pagu acuan tertinggi untuk biaya penginapan di lokasi {{ $shs->lokasi_tujuan }}. Detail tarif per golongan/kelas jabatan dapat diatur di tabel bawah.
                        </div>
                    </div>
                </div>

                {{-- Baris 4: Transportasi (Kota Banjarbaru & Kota Tujuan) --}}
                <div class="rounded-xl border border-amber-200 bg-amber-50/40 p-4 dark:border-amber-800 dark:bg-amber-900/10">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-2 w-2 rounded-full bg-amber-500"></span>
                            <h5 class="text-xs font-semibold uppercase tracking-wider text-amber-800 dark:text-amber-300">
                                Tarif Transportasi Perjalanan Dinas
                            </h5>
                        </div>
                        <div class="rounded-lg bg-amber-100/80 px-3 py-1 font-mono text-xs font-bold text-amber-900 dark:bg-amber-900/40 dark:text-amber-200">
                            Total Pagu Transport: <span x-text="formatRupiah(totalTransport())"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        {{-- Transport Kota Banjarbaru (Kedudukan) --}}
                        <div class="rounded-lg border border-amber-200/80 bg-white/70 p-3.5 dark:border-amber-900/40 dark:bg-gray-900/50">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-xs font-bold text-amber-900 dark:text-amber-300">1. Kota Banjarbaru (Tempat Kedudukan)</span>
                                <span class="font-mono text-[11px] text-gray-500 dark:text-gray-400" x-text="'Subtotal: ' + formatRupiah(totalBjb())"></span>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                        Rumah &rarr; Bandara (Rp)
                                    </label>
                                    <input type="number" name="transport_bjb_rumah_bandara"
                                           x-model.number="bjbPergi"
                                           min="0" step="1000" placeholder="0"
                                           class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                        Bandara &rarr; Rumah (Rp)
                                    </label>
                                    <input type="number" name="transport_bjb_bandara_rumah"
                                           x-model.number="bjbPulang"
                                           min="0" step="1000" placeholder="0"
                                           class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                </div>
                            </div>
                        </div>

                        {{-- Transport Kota Tujuan --}}
                        <div class="rounded-lg border border-amber-200/80 bg-white/70 p-3.5 dark:border-amber-900/40 dark:bg-gray-900/50">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-xs font-bold text-amber-900 dark:text-amber-300">2. Kota Tujuan</span>
                                <span class="font-mono text-[11px] text-gray-500 dark:text-gray-400" x-text="'Subtotal: ' + formatRupiah(totalTujuan())"></span>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                        Bandara &rarr; Hotel &rarr; Acara (Rp)
                                    </label>
                                    <input type="number" name="transport_tujuan_bandara_hotel"
                                           x-model.number="tujPergi"
                                           min="0" step="1000" placeholder="0"
                                           class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                        Hotel &rarr; Bandara (Rp)
                                    </label>
                                    <input type="number" name="transport_tujuan_hotel_bandara"
                                           x-model.number="tujPulang"
                                           min="0" step="1000" placeholder="0"
                                           class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex justify-end">
                    <x-ui.button size="sm" type="submit">
                        <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </x-ui.button>
                </div>
            </form>
        </x-common.component-card>

        {{-- ====================================== --}}
        {{-- CARD 2: TARIF PENGINAPAN PER GOLONGAN --}}
        {{-- ====================================== --}}
        <x-common.component-card title="Tarif Penginapan per Golongan / Kelas Jabatan"
                                 desc="Isi tarif penginapan berdasarkan golongan sesuai Pergub. Pergub biasanya membedakan tarif penginapan berdasarkan golongan atau kelas jabatan.">

            {{-- Form Tambah Penginapan --}}
            <div class="rounded-xl border border-teal-100 bg-teal-50/50 p-4 dark:border-teal-900/50 dark:bg-teal-900/10">
                <h5 class="mb-3 text-xs font-semibold uppercase tracking-wider text-teal-700 dark:text-teal-400">Tambah Tarif Penginapan</h5>
                <form method="POST" action="{{ route('master-shs.penginapan.store', $shs->id) }}">
                    @csrf
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Golongan/Kelas <span class="text-red-500">*</span></label>
                            <input type="text" name="golongan_kelas" value="{{ old('golongan_kelas') }}"
                                   placeholder="Misal: Gol IV, Eselon II"
                                   class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Tarif (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="tarif" value="{{ old('tarif') }}" min="0" step="1000" placeholder="0"
                                   class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Keterangan</label>
                            <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="Opsional"
                                   class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>
                        <div class="flex items-end">
                            <x-ui.button size="sm" type="submit" className="w-full sm:w-auto bg-teal-600 hover:bg-teal-700">
                                <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah
                            </x-ui.button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tabel Penginapan --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-900/50">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-12">No</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Golongan / Kelas Jabatan</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tarif (Rp)</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keterangan</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody x-data="{ editingPenginapan: null }">
                            @forelse($shs->penginapan as $index => $p)
                                {{-- Baris tampilan --}}
                                <tr x-show="editingPenginapan !== {{ $p->id }}" class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition">
                                    <td class="px-5 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3 text-sm font-medium text-gray-800 dark:text-white">{{ $p->golongan_kelas }}</td>
                                    <td class="px-5 py-3 text-right font-mono text-sm text-gray-600 dark:text-gray-300">Rp {{ number_format($p->tarif, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $p->keterangan ?? '—' }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="editingPenginapan = {{ $p->id }}" type="button"
                                                    class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 ring-1 ring-inset ring-yellow-600/20 hover:bg-yellow-100 transition dark:bg-yellow-900/30 dark:text-yellow-300">
                                                Edit
                                            </button>
                                            <form action="{{ route('master-shs.penginapan.destroy', [$shs->id, $p->id]) }}" method="POST"
                                                  onsubmit="return confirm('Hapus tarif penginapan {{ addslashes($p->golongan_kelas) }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 hover:bg-red-100 transition dark:bg-red-900/30 dark:text-red-300">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                {{-- Baris edit inline --}}
                                <tr x-show="editingPenginapan === {{ $p->id }}" x-cloak class="border-b border-gray-100 dark:border-gray-800 bg-yellow-50/50 dark:bg-yellow-900/10">
                                    <td class="px-5 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td colspan="3" class="px-5 py-3">
                                        <form method="POST" action="{{ route('master-shs.penginapan.update', [$shs->id, $p->id]) }}" id="edit-penginapan-{{ $p->id }}" class="flex items-center gap-3">
                                            @csrf @method('PUT')
                                            <input type="text" name="golongan_kelas" value="{{ $p->golongan_kelas }}"
                                                   class="h-9 w-40 rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                            <input type="number" name="tarif" value="{{ $p->tarif }}" min="0" step="1000"
                                                   class="h-9 w-36 rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                            <input type="text" name="keterangan" value="{{ $p->keterangan }}" placeholder="Keterangan"
                                                   class="h-9 flex-1 rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                        </form>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="submit" form="edit-penginapan-{{ $p->id }}"
                                                    class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 hover:bg-emerald-100 transition dark:bg-emerald-900/30 dark:text-emerald-300">
                                                Simpan
                                            </button>
                                            <button @click="editingPenginapan = null" type="button"
                                                    class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-300/50 hover:bg-gray-200 transition dark:bg-gray-800 dark:text-gray-400">
                                                Batal
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                        Belum ada data tarif penginapan. Gunakan form di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </x-common.component-card>

        {{-- ====================================== --}}
        {{-- CARD 3: TARIF TRANSPORTASI PER RUTE --}}
        {{-- ====================================== --}}
        <x-common.component-card title="Tarif Transportasi per Rute"
                                 desc="Isi tarif transportasi berdasarkan rute/jenis kendaraan sesuai Pergub. Biasanya dibedakan per rute perjalanan.">

            {{-- Form Tambah Transportasi --}}
            <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-4 dark:border-amber-900/50 dark:bg-amber-900/10">
                <h5 class="mb-3 text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">Tambah Tarif Transportasi</h5>
                <form method="POST" action="{{ route('master-shs.transportasi.store', $shs->id) }}">
                    @csrf
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-5">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Rute <span class="text-red-500">*</span></label>
                            <input type="text" name="rute" value="{{ old('rute') }}"
                                   placeholder="Misal: Bandara - Hotel"
                                   class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Jenis Transportasi</label>
                            <input type="text" name="jenis_transportasi" value="{{ old('jenis_transportasi') }}"
                                   placeholder="Misal: Taksi, Bus"
                                   class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Tarif (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="tarif" value="{{ old('tarif') }}" min="0" step="1000" placeholder="0"
                                   class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Keterangan</label>
                            <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="Opsional"
                                   class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>
                        <div class="flex items-end">
                            <x-ui.button size="sm" type="submit" className="w-full sm:w-auto bg-amber-600 hover:bg-amber-700">
                                <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah
                            </x-ui.button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tabel Transportasi --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-900/50">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-12">No</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rute</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis Transportasi</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tarif (Rp)</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keterangan</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody x-data="{ editingTransport: null }">
                            @forelse($shs->transportasi as $index => $t)
                                {{-- Baris tampilan --}}
                                <tr x-show="editingTransport !== {{ $t->id }}" class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition">
                                    <td class="px-5 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3 text-sm font-medium text-gray-800 dark:text-white">{{ $t->rute }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $t->jenis_transportasi ?? '—' }}</td>
                                    <td class="px-5 py-3 text-right font-mono text-sm text-gray-600 dark:text-gray-300">Rp {{ number_format($t->tarif, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $t->keterangan ?? '—' }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="editingTransport = {{ $t->id }}" type="button"
                                                    class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 ring-1 ring-inset ring-yellow-600/20 hover:bg-yellow-100 transition dark:bg-yellow-900/30 dark:text-yellow-300">
                                                Edit
                                            </button>
                                            <form action="{{ route('master-shs.transportasi.destroy', [$shs->id, $t->id]) }}" method="POST"
                                                  onsubmit="return confirm('Hapus tarif transportasi rute {{ addslashes($t->rute) }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 hover:bg-red-100 transition dark:bg-red-900/30 dark:text-red-300">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                {{-- Baris edit inline --}}
                                <tr x-show="editingTransport === {{ $t->id }}" x-cloak class="border-b border-gray-100 dark:border-gray-800 bg-yellow-50/50 dark:bg-yellow-900/10">
                                    <td class="px-5 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td colspan="4" class="px-5 py-3">
                                        <form method="POST" action="{{ route('master-shs.transportasi.update', [$shs->id, $t->id]) }}" id="edit-transport-{{ $t->id }}" class="flex items-center gap-3 flex-wrap">
                                            @csrf @method('PUT')
                                            <input type="text" name="rute" value="{{ $t->rute }}"
                                                   class="h-9 w-44 rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                            <input type="text" name="jenis_transportasi" value="{{ $t->jenis_transportasi }}" placeholder="Jenis"
                                                   class="h-9 w-32 rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                            <input type="number" name="tarif" value="{{ $t->tarif }}" min="0" step="1000"
                                                   class="h-9 w-36 rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                            <input type="text" name="keterangan" value="{{ $t->keterangan }}" placeholder="Keterangan"
                                                   class="h-9 flex-1 rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                        </form>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="submit" form="edit-transport-{{ $t->id }}"
                                                    class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 hover:bg-emerald-100 transition dark:bg-emerald-900/30 dark:text-emerald-300">
                                                Simpan
                                            </button>
                                            <button @click="editingTransport = null" type="button"
                                                    class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-300/50 hover:bg-gray-200 transition dark:bg-gray-800 dark:text-gray-400">
                                                Batal
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                        Belum ada data tarif transportasi. Gunakan form di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </x-common.component-card>

    </div>
@endsection
