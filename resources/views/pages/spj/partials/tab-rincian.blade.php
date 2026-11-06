<!-- Rincian -->
<div x-show="activeTab === 'rincian'" x-cloak x-transition>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="p-2 text-left">No</th>
                    <th class="p-2 text-left">Uraian</th>
                    <th class="p-2 text-right">Jumlah</th>
                    <th class="p-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($spt->pegawais_efektif as $index => $pegawai)
                    @php
                        $rincian = $spt->spj_rincians_efektif->where('pegawai_id', $pegawai->id)->first();
                    @endphp
                    <tr class="border-b">
                        <td class="p-2">{{ $index + 1 }}</td>
                        <td class="p-2">
                            <p class="font-medium text-gray-900">{{ $pegawai->nama }}</p>
                            <p class="text-xs text-gray-500">{{ $pegawai->nip }}</p>
                        </td>
                        <td class="p-2 text-right font-semibold text-blue-600">
                            {{ $rincian ? 'Rp ' . number_format($rincian->total, 0, ',', '.') : 'Rp -' }}
                        </td>
                        <td class="p-2 text-center">
                            <button @click="openAddRincian({ 
                                                                    pegawai_id: {{ $pegawai->id }}, 
                                                                    uraian_id: '{{ $rincian->uraian_id ?? '' }}',
                                                                    uraian: '{{ $pegawai->nama }}', 
                                                                    jumlah_hari: '{{ $rincian->jumlah_hari ?? floor($spt->durasi_hari) }}',
                                                                    uang_harian: '{{ $rincian->uang_harian ?? '' }}',
                                                                    tiket_pesawat_pergi: '{{ $rincian->tiket_pesawat_pergi ?? 0 }}',
                                                                    tiket_pesawat_pulang: '{{ $rincian->tiket_pesawat_pulang ?? 0 }}',
                                                                    transport: '{{ $rincian->transport ?? 0 }}',
                                                                    penginapan: '{{ $rincian->penginapan ?? 0 }}',
                                                                    kode_rekening: @js($subKegiatan?->nomor_rekening),
                                                                })"
                                class="text-green-600 hover:text-green-800 p-1"
                                title="{{ $rincian ? 'Edit Rincian' : 'Tambah Rincian' }}">
                                @if($rincian)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                @endif
                            </button>
                        </td>
                    </tr>
                @endforeach
                @php
                    $grandTotal = $spt->spj_rincians_efektif->sum('total');
                @endphp
                <tr class="font-bold bg-gray-50 dark:bg-gray-700">
                    <td colspan="2" class="p-2 text-right">TOTAL</td>
                    <td class="p-2 text-right text-blue-600">Rp
                        {{ number_format($grandTotal, 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

