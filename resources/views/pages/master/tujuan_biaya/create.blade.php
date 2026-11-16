@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Standar Harga Satuan (SHS)" />
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
        {{-- CARD: DATA UTAMA SHS --}}
        {{-- ====================================== --}}
        <x-common.component-card title="Data Utama SHS (Sesuai Standar Pergub)">
            <form method="POST" action="{{ route('master-shs.store') }}"
                  x-data="{
                      bjbPergi: {{ old('transport_bjb_rumah_bandara', 0) }},
                      bjbPulang: {{ old('transport_bjb_bandara_rumah', 0) }},
                      tujPergi: {{ old('transport_tujuan_bandara_hotel', 0) }},
                      tujPulang: {{ old('transport_tujuan_hotel_bandara', 0) }},
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

                {{-- Baris 1: Lokasi, Tahun, Status --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {{-- Lokasi Tujuan --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Lokasi Tujuan <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi_tujuan" value="{{ old('lokasi_tujuan') }}"
                               placeholder="Contoh: Jakarta, Surabaya, Luar Daerah..."
                               class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder-gray-500" required />
                    </div>

                    {{-- Tahun Anggaran --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Tahun Anggaran <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun_anggaran" value="{{ old('tahun_anggaran', date('Y')) }}"
                               min="2020" max="2099"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" required />
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <div class="flex h-11 items-center">
                            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
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
                        <input type="number" name="uang_harian" value="{{ old('uang_harian') }}"
                               min="0" step="1000"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" required />
                    </div>

                    {{-- Tiket Pesawat --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Tiket Pesawat PP (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="tiket_pesawat" value="{{ old('tiket_pesawat') }}"
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
                                <input type="number" name="penginapan" value="{{ old('penginapan', 0) }}"
                                       min="0" step="1000" placeholder="0"
                                       class="h-11 w-full rounded-lg border border-teal-300 bg-white pl-10 pr-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 dark:border-teal-700 dark:bg-gray-900 dark:text-white" required />
                            </div>
                        </div>
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 leading-relaxed sm:pt-4">
                            Pagu acuan tertinggi untuk biaya penginapan. Detail tarif per golongan/kelas jabatan dapat diatur di halaman detail setelah data disimpan.
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
                        Simpan Data SHS
                    </x-ui.button>
                </div>
            </form>
        </x-common.component-card>

    </div>
@endsection

