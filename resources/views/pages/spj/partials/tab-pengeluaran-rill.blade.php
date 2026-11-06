<!-- Pengeluaran Rill -->
<div x-show="activeTab === 'pengeluaran_rill'" x-cloak x-transition>
    <div class="flex justify-between items-center mb-6">
        <h5 class="font-bold text-gray-800 dark:text-white">Daftar Pengeluaran Riil (DPR)</h5>
        <span
            class="px-3 py-1 text-xs font-semibold {{ $isJakarta ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-200' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }} rounded-full">
            {{ $isJakarta ? 'Perjalanan Luar Daerah / Luar Provinsi' : 'Perjalanan Dalam Daerah / Dalam Provinsi' }}
        </span>
    </div>

    @if(!$isJakarta)
        <div
            class="bg-gray-100 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center max-w-2xl mx-auto space-y-3">
            <div
                class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto text-gray-400 text-xl">
                <svg class="w-6 h-6 mx-auto text-gray-400" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h6 class="font-bold text-gray-800 dark:text-white text-base">Tidak Memerlukan DPR</h6>
            <p class="text-sm text-gray-500 max-w-md mx-auto leading-relaxed">
                Daftar Pengeluaran Riil (DPR) taksi bandara hanya diterbitkan dan berlaku untuk perjalanan
                dinas luar provinsi/daerah. Perjalanan dalam daerah/provinsi ini tidak memerlukan pelaporan
                pengeluaran riil.
            </p>
        </div>
    @else
        <div class="space-y-6 max-w-4xl mx-auto">
            @php $hasDpr = false; @endphp
            @foreach($spt->pegawais_efektif as $pegawai)
                @php
                    $rincian = $spt->spj_rincians_efektif->where('pegawai_id', $pegawai->id)->first();
                @endphp
                @if($rincian && ($rincian->transport > 0))
                    @php $hasDpr = true; @endphp
                    <div
                        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-6 space-y-6 relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-amber-500/5 rounded-bl-full pointer-events-none">
                        </div>

                        <div
                            class="flex justify-between items-center border-b border-gray-100 dark:border-gray-800 pb-4">
                            <div>
                                <h6 class="font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                    SURAT PERNYATAAN DAFTAR PENGELUARAN RIIL</h6>
                                <p class="text-xs text-gray-400 mt-0.5">Pegawai: <span
                                        class="font-semibold text-gray-700 dark:text-gray-300">{{ $pegawai->nama }}</span>
                                </p>
                            </div>
                            <span
                                class="text-xs font-semibold text-amber-600 bg-amber-50 dark:bg-amber-950/40 dark:text-amber-300 px-2.5 py-1 rounded">Halaman
                                1 & 2 Aktif</span>
                        </div>

                        <p
                            class="text-xs text-gray-500 leading-relaxed bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-50 dark:border-gray-800">
                            "Berdasarkan Surat Perintah Perjalanan Dinas Nomor :
                            <strong>{{ $spt->sppd_efektif?->nomor_sppd ?? '800.1.11.1/    /BPKAD/' . Carbon\Carbon::parse($tanggalMulai)->format('Y') }}</strong>,
                            tanggal
                            <strong>{{ Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }}</strong>
                            dengan ini kami menyatakan dengan sesungguhnya bahwa biaya di bawah ini benar-benar
                            dikeluarkan untuk taksi bandara."
                        </p>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800 text-gray-400 uppercase tracking-wider">
                                        <th class="p-3 text-left">No</th>
                                        <th class="p-3 text-left">Uraian Pengeluaran</th>
                                        <th class="p-3 text-right">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="p-3 font-semibold text-gray-900 dark:text-white">1</td>
                                        <td class="p-3 text-gray-900 dark:text-white leading-relaxed">
                                            Biaya Taksi : Tempat Kedudukan (Banjarbaru) -
                                            ({{ $lokasi }}) PP
                                        </td>
                                        <td class="p-3 text-right font-bold text-amber-600 dark:text-amber-400">
                                            Rp {{ number_format($rincian->transport, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50/50 dark:bg-gray-800/20 font-bold">
                                        <td colspan="2" class="p-3 text-right text-gray-500 uppercase">Total
                                            Pengeluaran Riil</td>
                                        <td class="p-3 text-right text-amber-600 dark:text-amber-400">
                                            Rp {{ number_format($rincian->transport, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-800 text-xs">
                            <div
                                class="text-center p-3 border border-gray-50 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/10 rounded-lg">
                                <p class="font-semibold text-gray-400 uppercase tracking-wider mb-8">Mengetahui:
                                    Kuasa Pengguna Anggaran</p>
                                <p class="font-bold underline text-gray-900 dark:text-white">{{ $kpaNama }}</p>
                                <p class="text-gray-400 mt-0.5">NIP. {{ $kpaNip }}</p>
                            </div>
                            <div
                                class="text-center p-3 border border-gray-50 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/10 rounded-lg">
                                <p class="text-gray-400 mb-1">Banjarbaru,
                                    {{ Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }}
                                </p>
                                <p class="font-semibold text-gray-400 uppercase tracking-wider mb-7">Pelaksana SPPD
                                </p>
                                <p class="font-bold underline text-gray-900 dark:text-white">{{ $pegawai->nama }}
                                </p>
                                <p class="text-gray-400 mt-0.5">NIP. {{ $pegawai->nip ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
            @if(!$hasDpr)
                <div
                    class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center max-w-2xl mx-auto">
                    <p class="text-sm text-gray-500">Belum ada rincian biaya transport yang diisi untuk pegawai.
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>

