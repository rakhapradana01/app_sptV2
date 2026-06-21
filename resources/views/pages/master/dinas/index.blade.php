@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Master Dinas</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data Dinas / Instansi</p>
            </div>
            <button @click="$dispatch('open-dinas-create-modal')"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Dinas
            </button>
        </div>

        <x-common.component-card>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left align-middle">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-800/50">
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400 w-16">No</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">Nama Dinas</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400 w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dinas as $d)
                                <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white text-sm">
                                    <td class="px-5 py-4 sm:px-6">{{ $dinas->firstItem() + $loop->index }}</td>
                                    <td class="px-5 py-4 sm:px-6 font-medium text-gray-900 dark:text-white">{{ $d->nama_dinas }}</td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-2">
                                            <x-ui.button variant="yellow" size="xs"
                                                @click="$dispatch('open-dinas-edit-modal', { id: '{{ $d->id }}', nama_dinas: '{{ addslashes($d->nama_dinas) }}' })">
                                                Edit
                                            </x-ui.button>
                                            <form action="{{ route('dinas.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dinas ini?');" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button variant="danger" size="xs" type="submit">Hapus</x-ui.button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada data dinas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($dinas->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $dinas->links() }}
                </div>
            @endif

            <!-- Modal Create -->
            <x-ui.modal x-data="{ open: false }" @open-dinas-create-modal.window="open = true" :isOpen="false" class="max-w-[500px]">
                <div class="no-scrollbar relative w-full overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-8">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Dinas</h4>
                    </div>
                    <form method="POST" action="{{ route('dinas.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">Nama Dinas <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_dinas" required placeholder="Masukkan nama dinas" class="dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <button @click="open = false" type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">Batal</button>
                            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>

            <!-- Modal Edit -->
            <x-ui.modal x-data="{ open: false, id: '', nama_dinas: '' }" @open-dinas-edit-modal.window="open = true; id = $event.detail.id; nama_dinas = $event.detail.nama_dinas;" :isOpen="false" class="max-w-[500px]">
                <div class="no-scrollbar relative w-full overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-8">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white">Edit Dinas</h4>
                    </div>
                    <form method="POST" :action="`{{ url('dinas') }}/${id}`" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">Nama Dinas <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_dinas" x-model="nama_dinas" required placeholder="Masukkan nama dinas" class="dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <button @click="open = false" type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">Batal</button>
                            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Perbarui</button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>

        </x-common.component-card>
    </div>
@endsection
