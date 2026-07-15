@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Sub Kegiatan" />
    <div class="space-y-6">
        <x-common.component-card title="Daftar Sub Kegiatan">
            <x-ui.button size="sm" @click="$dispatch('open-profile-create-modal')">Tambah</x-ui.button>

            <div class="overflow-x-auto mt-4">
                <table class="w-full min-w-[600px] border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left sm:px-6">No</th>
                            <th class="px-5 py-3 text-left sm:px-6">PPTK</th>
                            <th class="px-5 py-3 text-left sm:px-6">Nama Program</th>
                            <th class="px-5 py-3 text-left sm:px-6">Koefisien (OK)</th>
                            <th class="px-5 py-3 text-left sm:px-6">Pagu</th>
                            <th class="px-5 py-3 text-left sm:px-6">Realisasi</th>
                            <th class="px-5 py-3 text-left sm:px-6">Sisa</th>
                            <th class="px-5 py-3 text-left sm:px-6">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($subKegiatan as $item)
                            <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white">
                                <td class="px-5 py-4 sm:px-6">{{ $subKegiatan->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $item->owner?->name ?? $item->pegawai?->nama ?? '-' }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $item->nama_kegiatan }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $item->koefisien }}</td>
                                <td class="px-5 py-4 sm:px-6">Rp{{ number_format($item->pagu, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 sm:px-6 text-green-600 dark:text-green-400 font-semibold">Rp{{ number_format($item->realisasi ?? 0, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 sm:px-6 text-red-600 dark:text-red-400 font-semibold">Rp{{ number_format($item->pagu - ($item->realisasi ?? 0), 0, ',', '.') }}</td>
                                <td class="px-5 py-4 sm:px-6 flex gap-2">
                                    <x-ui.button size="sm" type="button" onclick="editData({{ $item->id }})">
                                        Edit
                                    </x-ui.button>
                                    <form action="#" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button variant="red" size="sm">Hapus</x-ui.button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Modal Tambah Sub Kegiatan (plain div, bukan x-ui.modal supaya x-data tidak bentrok) --}}
            <div
                x-data="{
                    open: false,
                    dinas_id: '', bidang_id: '', sub_bidang_id: '',
                    bidangs: [], subBidangs: [],
                    async fetchBidangs() {
                        this.bidang_id = '';
                        this.sub_bidang_id = '';
                        this.bidangs = [];
                        this.subBidangs = [];
                        if (!this.dinas_id) return;
                        try {
                            const res = await fetch(`${window.location.origin}/api/bidangs/${this.dinas_id}`);
                            this.bidangs = await res.json();
                        } catch(e) { console.error('fetch bidang gagal', e); }
                    },
                    async fetchSubBidangs() {
                        this.sub_bidang_id = '';
                        this.subBidangs = [];
                        if (!this.bidang_id) return;
                        try {
                            const res = await fetch(`${window.location.origin}/api/sub-bidangs/${this.bidang_id}`);
                            this.subBidangs = await res.json();
                        } catch(e) { console.error('fetch sub bidang gagal', e); }
                    }
                }"
                @open-profile-create-modal.window="open = true; dinas_id=''; bidang_id=''; sub_bidang_id=''; bidangs=[]; subBidangs=[];"
                x-show="open" x-cloak
                @keydown.escape.window="open = false"
                class="modal fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5"
            >
                {{-- Backdrop --}}
                <div @click="open = false"
                    class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                ></div>

                {{-- Modal Content --}}
                <div @click.stop
                    class="relative w-full max-w-[500px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                >
                    {{-- Tombol Close --}}
                    <button @click="open = false"
                        class="absolute right-3 top-3 z-999 flex h-9.5 w-9.5 items-center justify-center rounded-full bg-gray-100 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 sm:h-11 sm:w-11">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
                                fill="currentColor" />
                        </svg>
                    </button>

                    <h4 class="mb-4 text-2xl font-semibold text-gray-800 dark:text-white/90">Tambah Sub Kegiatan</h4>
                    <form method="POST" action="{{ route('sub-kegiatan.store') }}" class="space-y-4">
                        @csrf

                        @if(in_array(auth()->user()->role?->name, ['admin', 'super_admin']))
                        {{-- Admin: pilih Dinas → Bidang → Sub Bidang --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Dinas <span class="text-red-500">*</span></label>
                            <select name="dinas_id" x-model="dinas_id" @change="fetchBidangs"
                                class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" required>
                                <option value="">-- Pilih Dinas --</option>
                                @foreach($dinas as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_dinas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="bidangs.length > 0">
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Bidang <span class="text-red-500">*</span></label>
                            <select name="bidang_id" x-model="bidang_id" @change="fetchSubBidangs"
                                class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" :required="bidangs.length > 0">
                                <option value="">-- Pilih Bidang --</option>
                                <template x-for="b in bidangs" :key="b.id">
                                    <option :value="b.id" x-text="b.nama_bidang"></option>
                                </template>
                            </select>
                        </div>
                        <div x-show="subBidangs.length > 0">
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Sub Bidang <span class="text-red-500">*</span></label>
                            <select name="sub_bidang_id" x-model="sub_bidang_id"
                                class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" :required="subBidangs.length > 0">
                                <option value="">-- Pilih Sub Bidang --</option>
                                <template x-for="s in subBidangs" :key="s.id">
                                    <option :value="s.id" x-text="s.nama_sub_bidang"></option>
                                </template>
                            </select>
                        </div>
                        @endif

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Nomor Rekening <span class="text-red-500">*</span></label>
                            <input type="text" name="nomor_rekening"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white"
                                placeholder="Masukkan nomor rekening" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Nama Program <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_kegiatan"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white"
                                placeholder="Masukkan nama program" required>
                        </div>

                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" @click="open = false" class="px-4 py-2 border rounded-lg dark:text-white dark:border-gray-700">Batal</button>
                            <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>


            <x-ui.modal x-data="{ open: false }" @open-edit-modal.window="open = true" :isOpen="false"
                class="max-w-[500px]">
                <div class="relative w-full max-w-[500px] rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">

                    <h4 class="mb-4 text-2xl font-semibold text-gray-800 dark:text-white/90">
                        Edit Sub Kegiatan
                    </h4>

                    <form id="formEdit" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <input type="hidden" id="edit_id">


                        <div>
                            <label class="block mb-1 text-sm font-medium dark:text-gray-400">Nomor Rekening</label>
                            <input type="text" id="edit_nomor_rekening"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white"
                                placeholder="Masukkan nomor rekening">
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium dark:text-gray-400">Nama Program</label>
                            <input type="text" id="edit_nama_kegiatan"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white">
                        </div>

                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" @click="open = false" class="px-4 py-2 border rounded-lg dark:text-white dark:border-gray-700">
                                Batal
                            </button>

                            <x-ui.button type="submit" variant="primary">
                                Update
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>

            <div class="mt-4">
                <x-ui.pagination :paginator="$subKegiatan" />
            </div>
        </x-common.component-card>
    </div>
    <script>
        function editData(id) {
            fetch(`/sub-kegiatan/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('edit_id').value = data.id;
                    document.getElementById('edit_nomor_rekening').value = data.nomor_rekening ?? '';
                    document.getElementById('edit_nama_kegiatan').value = data.nama_kegiatan;

                    window.dispatchEvent(new CustomEvent('open-edit-modal'));
                });
        }

        document.getElementById('formEdit').addEventListener('submit', function(e) {
            e.preventDefault();

            let id = document.getElementById('edit_id').value;

            fetch(`/sub-kegiatan/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nomor_rekening: document.getElementById('edit_nomor_rekening').value,
                        nama_kegiatan:  document.getElementById('edit_nama_kegiatan').value,
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.success);
                    location.reload();
                });
        });
    </script>
@endsection
