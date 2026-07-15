@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Master Sub Bidang</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data Sub Bidang</p>
            </div>
            <button @click="$dispatch('open-sub-bidang-create-modal')"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Sub Bidang
            </button>
        </div>

        <x-common.component-card>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left align-middle">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-800/50">
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400 w-16">No</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">Nama Sub Bidang</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">Bidang</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">Dinas</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400 w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subBidangs as $sb)
                                <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white text-sm">
                                    <td class="px-5 py-4 sm:px-6">{{ $subBidangs->firstItem() + $loop->index }}</td>
                                    <td class="px-5 py-4 sm:px-6 font-medium text-gray-900 dark:text-white">{{ $sb->nama_sub_bidang }}</td>
                                    <td class="px-5 py-4 sm:px-6 text-gray-500 dark:text-gray-400">{{ $sb->bidang->nama_bidang ?? '-' }}</td>
                                    <td class="px-5 py-4 sm:px-6 text-gray-500 dark:text-gray-400">{{ $sb->bidang->dinas->nama_dinas ?? '-' }}</td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-2">
                                            <x-ui.button variant="yellow" size="xs"
                                                @click="$dispatch('open-sub-bidang-edit-modal', { id: '{{ $sb->id }}', dinas_id: '{{ $sb->bidang->dinas_id ?? '' }}', bidang_id: '{{ $sb->bidang_id }}', nama_sub_bidang: '{{ addslashes($sb->nama_sub_bidang) }}' })">
                                                Edit
                                            </x-ui.button>
                                            <form action="{{ route('sub-bidang.destroy', $sb->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sub bidang ini?');" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button variant="red" size="xs" type="submit">Hapus</x-ui.button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada data sub bidang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($subBidangs->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $subBidangs->links() }}
                </div>
            @endif
        </x-common.component-card>

        <!-- Modal Create -->
        <x-ui.modal x-data="{
                open: false,
                dinas_id: '', bidang_id: '',
                bidangs: [],
                async fetchBidangs() {
                    this.bidang_id = '';
                    this.bidangs = [];
                    if (this.dinas_id) {
                        let response = await fetch('/api/bidangs/'+this.dinas_id);
                        this.bidangs = await response.json();
                    }
                }
            }" 
            x-init="
                $watch('dinas_id', (value) => {
                    fetchBidangs();
                    $nextTick(() => {
                        let selectBidang = $el.querySelector('select[name=&quot;bidang_id&quot;]');
                        if (selectBidang && selectBidang.tomselect) {
                            if (value) {
                                selectBidang.tomselect.enable();
                            } else {
                                selectBidang.tomselect.disable();
                            }
                        }
                    });
                });
                $watch('bidangs', (newBidangs) => {
                    $nextTick(() => {
                        let selectBidang = $el.querySelector('select[name=&quot;bidang_id&quot;]');
                        if (selectBidang && selectBidang.tomselect) {
                            let ts = selectBidang.tomselect;
                            ts.clearOptions();
                            ts.clear();
                            ts.addOption({value: '', text: 'Pilih Bidang'});
                            newBidangs.forEach(bidang => {
                                ts.addOption({value: bidang.id.toString(), text: bidang.nama_bidang});
                            });
                            ts.sync();
                        }
                    });
                });
            "
            @open-sub-bidang-create-modal.window="open = true; dinas_id=''; bidang_id=''; bidangs=[];" 
            :isOpen="false" class="max-w-[500px]">
            <div class="no-scrollbar relative w-full overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-8">
                <div class="mb-6">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Sub Bidang</h4>
                </div>
                <form method="POST" action="{{ route('sub-bidang.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">Dinas <span class="text-rose-500">*</span></label>
                        <select name="dinas_id" x-model="dinas_id" required class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="" disabled selected>Pilih Dinas</option>
                            @foreach($dinas as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_dinas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">Bidang <span class="text-rose-500">*</span></label>
                        <select name="bidang_id" x-model="bidang_id" :disabled="!dinas_id" required class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="" disabled selected>Pilih Bidang</option>
                            <template x-for="bidang in bidangs" :key="bidang.id">
                                <option :value="bidang.id" x-text="bidang.nama_bidang"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">Nama Sub Bidang <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_sub_bidang" required placeholder="Masukkan nama sub bidang" class="dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button @click="open = false" type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">Batal</button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </x-ui.modal>

        <!-- Modal Edit -->
        <x-ui.modal x-data="{
                open: false, id: '', dinas_id: '', bidang_id: '', nama_sub_bidang: '',
                bidangs: [],
                async fetchBidangs() {
                    this.bidang_id = '';
                    this.bidangs = [];
                    if (this.dinas_id) {
                        let response = await fetch('/api/bidangs/'+this.dinas_id);
                        this.bidangs = await response.json();
                    }
                },
                async loadInitialData() {
                    if (this.dinas_id) {
                        let r1 = await fetch('/api/bidangs/'+this.dinas_id);
                        this.bidangs = await r1.json();
                    }
                }
            }" 
            x-init="
                $watch('dinas_id', (value, oldValue) => { 
                    if (oldValue !== undefined && oldValue !== '') {
                        fetchBidangs(); 
                    }
                    $nextTick(() => {
                        let selectBidang = $el.querySelector('select[name=&quot;bidang_id&quot;]');
                        if (selectBidang && selectBidang.tomselect) {
                            if (value) {
                                selectBidang.tomselect.enable();
                            } else {
                                selectBidang.tomselect.disable();
                            }
                        }
                    });
                });
                $watch('bidangs', (newBidangs) => {
                    $nextTick(() => {
                        let selectBidang = $el.querySelector('select[name=&quot;bidang_id&quot;]');
                        if (selectBidang && selectBidang.tomselect) {
                            let ts = selectBidang.tomselect;
                            let currentVal = this.bidang_id;
                            ts.clearOptions();
                            ts.clear();
                            ts.addOption({value: '', text: 'Pilih Bidang'});
                            newBidangs.forEach(bidang => {
                                ts.addOption({value: bidang.id.toString(), text: bidang.nama_bidang});
                            });
                            ts.sync();
                            if (currentVal) {
                                ts.setValue(currentVal.toString());
                            }
                        }
                    });
                });
            "
            @open-sub-bidang-edit-modal.window="
                open = true; id = $event.detail.id; dinas_id = $event.detail.dinas_id; bidang_id = $event.detail.bidang_id; nama_sub_bidang = $event.detail.nama_sub_bidang;
                loadInitialData();
            " 
            :isOpen="false" class="max-w-[500px]">
            <div class="no-scrollbar relative w-full overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-8">
                <div class="mb-6">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white">Edit Sub Bidang</h4>
                </div>
                <form method="POST" :action="`{{ url('sub-bidang') }}/${id}`" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">Dinas <span class="text-rose-500">*</span></label>
                        <select name="dinas_id" x-model="dinas_id" required class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="" disabled>Pilih Dinas</option>
                            @foreach($dinas as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_dinas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">Bidang <span class="text-rose-500">*</span></label>
                        <select name="bidang_id" x-model="bidang_id" :disabled="!dinas_id" required class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="" disabled selected>Pilih Bidang</option>
                            <template x-for="bidang in bidangs" :key="bidang.id">
                                <option :value="bidang.id" x-text="bidang.nama_bidang"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">Nama Sub Bidang <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_sub_bidang" x-model="nama_sub_bidang" required placeholder="Masukkan nama sub bidang" class="dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button @click="open = false" type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">Batal</button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Perbarui</button>
                    </div>
                </form>
            </div>
        </x-ui.modal>
    </div>
@endsection