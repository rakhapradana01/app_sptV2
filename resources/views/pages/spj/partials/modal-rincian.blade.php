<!-- Modal Rincian -->
<div x-show="showRincianModal"
    x-effect="document.body.style.overflow = showRincianModal ? 'hidden' : ''"
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
    x-cloak x-transition>
    
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden max-h-[92vh] flex flex-col border border-gray-100 dark:border-gray-800"
        @click.away="showRincianModal = false">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/40">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white"
                    x-text="isEditRincian ? 'Edit Rincian Biaya' : 'Tambah Rincian Biaya'"></h3>
                <p class="text-sm text-gray-500 mt-0.5" x-show="rincianForm.uraian">
                    Pegawai: <span class="font-semibold text-gray-800 dark:text-gray-200" x-text="rincianForm.uraian"></span>
                </p>
            </div>
            <button @click="showRincianModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form Body (Scrollable) -->
        <form action="{{ route('spj.rincian.store', $spt->id) }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            <input type="hidden" name="pegawai_id" x-bind:value="rincianForm.pegawai_id">

            <div class="p-6 space-y-5 overflow-y-auto flex-1">
                
                <!-- PANDUAN PAGU DINAMIS (DIPINDAHKAN KE PALING ATAS) -->
                <div class="p-4 bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900 rounded-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-sm text-blue-900 dark:text-blue-300">💡 Panduan Pagu Standar (SHS):</span>
                        <span class="text-xs sm:text-sm text-blue-700 dark:text-blue-400">
                            Subtotal Harian: <b>Rp <span x-text="formatRupiah(subtotalUangHarian)"></span></b>
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400 pt-2 border-t border-blue-100 dark:border-blue-900/50">
                        <div>Tiket: <span class="font-semibold text-gray-900 dark:text-white">Rp <span x-text="formatRupiah(paguPanduan.tiket)"></span></span></div>
                        <div>Transport: <span class="font-semibold text-gray-900 dark:text-white">Rp <span x-text="formatRupiah(paguPanduan.transport)"></span></span></div>
                        <div>Penginapan: <span class="font-semibold text-gray-900 dark:text-white">Rp <span x-text="formatRupiah(paguPanduan.penginapan)"></span></span></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Kode Rekening -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Kode Rekening</label>
                        <input type="text" name="kode_rekening" x-model="rincianForm.kode_rekening"
                            class="form-control w-full bg-gray-50 text-sm text-gray-700 dark:text-gray-300 py-2 px-3" readonly>
                    </div>

                    <!-- Pagu Uraian -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Pagu Uraian (Mengurangi OK)
                        </label>
                        <select name="uraian_id" x-model="rincianForm.uraian_id" class="form-control w-full text-sm py-2 px-3">
                            <option value="">-- Tanpa Pagu Uraian --</option>
                            @foreach(($subKegiatan->uraians ?? []) as $uraian)
                                <option value="{{ $uraian->id }}">
                                    {{ $uraian->uraian }} (Sisa: {{ $uraian->ok_total - $uraian->ok_terpakai }} OK)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jumlah Hari -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Jumlah Hari</label>
                        <input type="number" name="jumlah_hari" x-model.number="rincianForm.jumlah_hari"
                            class="form-control w-full text-sm py-2 px-3" placeholder="0">
                    </div>

                    <!-- Uang Harian & Lokasi -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Uang Harian (SHS)</label>
                        <select name="uang_harian" 
                            x-model="selectedUangHarian"
                            @change="onUangHarianChange($event)"
                            data-max-tiket="{{ $maxTiketPesawat }}"
                            data-max-transport="{{ $maxTransport }}"
                            data-max-penginapan="{{ $maxPenginapan }}"
                            class="form-control w-full text-sm py-2 px-3">
                            <option value="">-- Pilih Standar / Lokasi --</option>
                            @foreach ($uangHarianOptions as $lokasi => $harga)
                                @php
                                    $shsItem = $shsByLokasi[$lokasi] ?? null;
                                    $optTiket = $shsItem['tiket_pesawat'] ?? $maxTiketPesawat;
                                    $optTransport = $shsItem['transport'] ?? $maxTransport;
                                    $optPenginapan = $shsItem['penginapan'] ?? $maxPenginapan;
                                    $optBjbPergi = $shsItem['bjb_pergi'] ?? 0;
                                    $optBjbPulang = $shsItem['bjb_pulang'] ?? 0;
                                    $optTujPergi = $shsItem['tuj_pergi'] ?? 0;
                                    $optTujPulang = $shsItem['tuj_pulang'] ?? 0;
                                @endphp
                                <option value="{{ $harga }}" 
                                    data-lokasi="{{ $lokasi }}"
                                    data-harga="{{ $harga }}"
                                    data-tiket="{{ $optTiket }}"
                                    data-transport="{{ $optTransport }}"
                                    data-penginapan="{{ $optPenginapan }}"
                                    data-bjb-pergi="{{ $optBjbPergi }}"
                                    data-bjb-pulang="{{ $optBjbPulang }}"
                                    data-tuj-pergi="{{ $optTujPergi }}"
                                    data-tuj-pulang="{{ $optTujPulang }}">
                                    {{ $lokasi }} (Rp {{ number_format($harga, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tiket Pesawat Pergi -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Tiket Pergi</label>
                        </div>
                        <input type="number" name="tiket_pesawat_pergi" x-model.number="rincianForm.tiket_pesawat_pergi" 
                            class="form-control w-full text-sm py-2 px-3" placeholder="0">
                    </div>

                    <!-- Tiket Pesawat Pulang -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Tiket Pulang</label>
    
                        </div>
                        <input type="number" name="tiket_pesawat_pulang" x-model.number="rincianForm.tiket_pesawat_pulang" 
                            class="form-control w-full text-sm py-2 px-3" placeholder="0">
                    </div>

                    <!-- Transport dengan Rincian Rute -->
                    <div class="sm:col-span-2 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Transportasi (Rp)
                            </label>
                            <button type="button" @click="showTransportBreakdown = !showTransportBreakdown"
                                    class="text-xs sm:text-sm font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400 inline-flex items-center gap-1">
                                <span x-text="showTransportBreakdown ? 'Tutup Rincian Rute' : '+ Rincikan Rute (Banjarbaru & Tujuan)'"></span>
                            </button>
                        </div>
                        <input type="number" name="transport" x-model.number="rincianForm.transport"
                            class="form-control w-full font-semibold text-sm py-2 px-3" placeholder="0">

                        <!-- Sub-form Rincian Transport -->
                        <div x-show="showTransportBreakdown" x-cloak class="p-4 bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 rounded-xl space-y-3">
                            <div>
                                <span class="font-bold text-sm text-amber-900 dark:text-amber-300">1. Kota Banjarbaru (Tempat Kedudukan)</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                                    <div>
                                        <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Rumah &rarr; Bandara</label>
                                        <input type="number" x-model.number="transportBreakdown.bjb_pergi" @input="syncTransportFromBreakdown()" placeholder="0" class="form-control w-full text-sm py-1.5 px-3">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Bandara &rarr; Rumah</label>
                                        <input type="number" x-model.number="transportBreakdown.bjb_pulang" @input="syncTransportFromBreakdown()" placeholder="0" class="form-control w-full text-sm py-1.5 px-3">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span class="font-bold text-sm text-amber-900 dark:text-amber-300">2. Kota Tujuan</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                                    <div>
                                        <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Bandara &rarr; Hotel &rarr; Acara</label>
                                        <input type="number" x-model.number="transportBreakdown.tuj_pergi" @input="syncTransportFromBreakdown()" placeholder="0" class="form-control w-full text-sm py-1.5 px-3">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Hotel &rarr; Bandara</label>
                                        <input type="number" x-model.number="transportBreakdown.tuj_pulang" @input="syncTransportFromBreakdown()" placeholder="0" class="form-control w-full text-sm py-1.5 px-3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Penginapan -->
                    <div class="sm:col-span-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Penginapan (Pagu Maksimal)</label>
                        
                        </div>
                        <input type="number" name="penginapan" x-model.number="rincianForm.penginapan"
                            class="form-control w-full font-semibold text-sm py-2 px-3" placeholder="0">
                    </div>
                </div>
            </div>

            <!-- Footer with Live Total Calculation & Buttons -->
            <div class="px-6 py-4 bg-gray-50/70 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="text-sm text-gray-600 dark:text-gray-400 w-full sm:w-auto">
                    <span>Total Estimasi:</span>
                    <span class="font-bold text-base text-blue-600 dark:text-blue-400 ml-1.5">
                        Rp <span x-text="formatRupiah(totalRincianLive)"></span>
                    </span>
                </div>
                <div class="flex justify-end gap-3 w-full sm:w-auto">
                    <button type="button" @click="showRincianModal = false"
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-xl hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition shadow-sm">
                        Simpan Rincian
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>