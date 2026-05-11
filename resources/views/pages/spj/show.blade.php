@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detail SPJ" />

    <div class="space-y-6">
        <x-common.component-card title="SPJ: {{ $spj['nomor_spt'] }}">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Nama Pegawai</p>
                    <p class="font-semibold text-gray-900">{{ $spj['nama_pegawai'] }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Tujuan</p>
                    <p class="font-semibold text-gray-900">{{ $spj['tujuan'] }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Tanggal</p>
                    <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($spj['tanggal'])->format('d M Y') }}</p>
                </div>
            </div>

            <div x-data="{ 
                activeTab: 'kuitansi',
                showRincianModal: false,
                isEditRincian: false,
                rincianForm: { id: '', uraian: '', jumlah: '' },
                openAddRincian() {
                    this.isEditRincian = false;
                    this.rincianForm = { id: '', uraian: '', jumlah: '' };
                    this.showRincianModal = true;
                },
                openEditRincian(item) {
                    this.isEditRincian = true;
                    this.rincianForm = { ...item };
                    this.showRincianModal = true;
                }
            }" class="mt-8">
                <!-- Tab Headers -->
                <div class="flex flex-wrap border-b border-gray-200 dark:border-gray-700 gap-1">
                    <button @click="activeTab = 'kuitansi'" 
                        :class="activeTab === 'kuitansi' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 font-medium text-sm border-b-2 transition-all">
                        Kuitansi
                    </button>
                    <button @click="activeTab = 'tanda_tangan'" 
                        :class="activeTab === 'tanda_tangan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 font-medium text-sm border-b-2 transition-all">
                        Tanda Tangan
                    </button>
                    <button @click="activeTab = 'rincian'" 
                        :class="activeTab === 'rincian' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 font-medium text-sm border-b-2 transition-all">
                        Rincian
                    </button>
                    <button @click="activeTab = 'pengeluaran_rill'" 
                        :class="activeTab === 'pengeluaran_rill' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 font-medium text-sm border-b-2 transition-all">
                        Daftar pengeluaran rill
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 min-h-[300px]">
                    <!-- Kuitansi -->
                    <div x-show="activeTab === 'kuitansi'" x-cloak x-transition>
                        <h5 class="font-bold text-gray-800 dark:text-white mb-4">Preview Kuitansi</h5>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-10 text-center">
                            <p class="text-gray-500">Data Kuitansi Dummy (Frontend Only)</p>
                            <div class="mt-4 p-4 bg-white dark:bg-gray-900 shadow-sm rounded border max-w-lg mx-auto text-left">
                                <p class="text-xs uppercase text-gray-400">Nomor Bukti: K-001/V/2026</p>
                                <hr class="my-2">
                                <p class="mb-2">Telah terima dari: <strong>Bendahara Pengeluaran</strong></p>
                                <p class="mb-2">Uang sebesar: <strong>Rp 1.500.000,-</strong></p>
                                <p>Untuk pembayaran: <strong>Perjalanan Dinas ke Jakarta</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tanda Tangan -->
                    <div x-show="activeTab === 'tanda_tangan'" x-cloak x-transition>
                        <h5 class="font-bold text-gray-800 dark:text-white mb-6 text-center">Tanda Tangan Pengesahan</h5>
                        
                        <div class="space-y-12">
                            <!-- Baris Atas -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="text-center p-4">
                                    <p class="text-sm font-medium text-gray-500 mb-16 uppercase tracking-wider">Bendahara Pengeluaran Pembantu</p>
                                    <p class="font-bold underline text-gray-900 dark:text-white">NAMA BENDAHARA, S.E.</p>
                                    <p class="text-xs text-gray-400 mt-1">NIP. 19XXXXXXXXXXXXXXX</p>
                                </div>
                                <div class="text-center p-4">
                                    <p class="text-sm font-medium text-gray-500 mb-16 uppercase tracking-wider">Yang Menerima</p>
                                    <p class="font-bold underline text-gray-900 dark:text-white">{{ $spj['nama_pegawai'] }}</p>
                                    <p class="text-xs text-gray-400 mt-1">NIP. 19XXXXXXXXXXXXXXX</p>
                                </div>
                            </div>

                            <!-- Baris Bawah -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="text-center p-4">
                                    <p class="text-xs text-gray-400 mb-1">Mengetahui/Menyetujui,</p>
                                    <p class="text-sm font-medium text-gray-500 mb-16 uppercase tracking-wider">Kuasa Pengguna Anggaran</p>
                                    <p class="font-bold underline text-gray-900 dark:text-white">NAMA KPA, M.Si.</p>
                                    <p class="text-xs text-gray-400 mt-1">NIP. 19XXXXXXXXXXXXXXX</p>
                                </div>
                                <div class="text-center p-4">
                                    <p class="text-sm font-medium text-gray-500 mb-16 uppercase tracking-wider">Pejabat Pelaksana Teknis Kegiatan</p>
                                    <p class="font-bold underline text-gray-900 dark:text-white">NAMA PPTK, S.Sos.</p>
                                    <p class="text-xs text-gray-400 mt-1">NIP. 19XXXXXXXXXXXXXXX</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rincian -->
                    <div x-show="activeTab === 'rincian'" x-cloak x-transition>
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="font-bold text-gray-800 dark:text-white">Rincian Biaya Perjalanan</h5>
                            <button @click="openAddRincian()" class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Tambah Rincian
                            </button>
                        </div>
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
                                    <tr class="border-b">
                                        <td class="p-2">1</td>
                                        <td class="p-2">Uang Harian (3 Hari)</td>
                                        <td class="p-2 text-right">Rp 900.000</td>
                                        <td class="p-2 text-center">
                                            <button @click="openEditRincian({ id: 1, uraian: 'Uang Harian (3 Hari)', jumlah: 900000 })" class="text-blue-600 hover:text-blue-800 p-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-2">2</td>
                                        <td class="p-2">Uang Transport (PP)</td>
                                        <td class="p-2 text-right">Rp 400.000</td>
                                        <td class="p-2 text-center">
                                            <button @click="openEditRincian({ id: 2, uraian: 'Uang Transport (PP)', jumlah: 400000 })" class="text-blue-600 hover:text-blue-800 p-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="p-2">3</td>
                                        <td class="p-2">Biaya Penginapan</td>
                                        <td class="p-2 text-right">Rp 200.000</td>
                                        <td class="p-2 text-center">
                                            <button @click="openEditRincian({ id: 3, uraian: 'Biaya Penginapan', jumlah: 200000 })" class="text-blue-600 hover:text-blue-800 p-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="font-bold bg-gray-50 dark:bg-gray-700">
                                        <td colspan="2" class="p-2 text-right">TOTAL</td>
                                        <td class="p-2 text-right">Rp 1.500.000</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pengeluaran Rill -->
                    <div x-show="activeTab === 'pengeluaran_rill'" x-cloak x-transition>
                        <h5 class="font-bold text-gray-800 dark:text-white mb-4">Daftar Pengeluaran Rill</h5>
                        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg border">
                            <p class="text-sm text-gray-600 mb-4">Yang bertanda tangan di bawah ini menyatakan bahwa biaya di bawah ini benar-benar dikeluarkan:</p>
                            <table class="w-full text-sm mb-6">
                                <thead class="border-b">
                                    <tr>
                                        <th class="py-2 text-left">Uraian</th>
                                        <th class="py-2 text-right">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="py-2">Transport Lokal (Bandara - Hotel)</td>
                                        <td class="py-2 text-right">Rp 150.000</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Parkir & Tol</td>
                                        <td class="py-2 text-right">Rp 50.000</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="text-xs italic text-gray-400">* Digunakan untuk pengeluaran yang tidak memiliki kuitansi resmi.</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Rincian -->
                <div x-show="showRincianModal" 
                    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                    x-cloak x-transition>
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.away="showRincianModal = false">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="isEditRincian ? 'Edit Rincian Biaya' : 'Tambah Rincian Biaya'"></h3>
                            <button @click="showRincianModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form @submit.prevent="showRincianModal = false" class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Uraian Biaya</label>
                                <input type="text" x-model="rincianForm.uraian" class="form-control w-full" placeholder="Contoh: Uang Harian">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah (Rp)</label>
                                <input type="number" x-model="rincianForm.jumlah" class="form-control w-full" placeholder="0">
                            </div>
                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" @click="showRincianModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700" x-text="isEditRincian ? 'Simpan Perubahan' : 'Tambah'"></button>
                            </div>
                        </form>
                    </div>
                </div>
                    </div>
                </div>
            </div>
        </x-common.component-card>
    </div>
@endsection
