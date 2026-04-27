@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pegawai" />
    <div class="space-y-6">
        <x-common.component-card title="Dfoaftar Pegawai">
            <x-ui.button size="sm" @click="$dispatch('open-profile-create-modal')">Tambah</x-ui.button>
            <div
                class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1102px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left sm:px-6">No</th>
                                <th class="px-5 py-3 text-left sm:px-6">Nama</th>
                                <th class="px-5 py-3 text-left sm:px-6">NIP</th>
                                <th class="px-5 py-3 text-left sm:px-6">Pangkat</th>
                                <th class="px-5 py-3 text-left sm:px-6">Jabatan</th>
                                <th class="px-5 py-3 text-left sm:px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pegawais as $pegawai)
                                <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white">
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $pegawais->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $pegawai->nama }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $pegawai->nip }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $pegawai->pangkat }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $pegawai->jabatan }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-2">
                                            <x-ui.button variant="yellow">
                                                Edit
                                            </x-ui.button>

                                            <form action="{{ route('pegawai.destroy', $pegawai->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus?')">
                                                @csrf
                                                @method('DELETE')

                                                <x-ui.button variant="red" type="submit">
                                                    Hapus
                                                </x-ui.button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                </div>
            </div>
            <x-ui.modal x-data="{ open: false }" @open-profile-create-modal.window="open = true" :isOpen="false"
                class="max-w-[700px]">
                <div
                    class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
                    <div class="px-2 pr-14">
                        <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                            Tambah Pegawai
                        </h4>
                    </div>
                    <form method="POST" action="{{ route('pegawai.store') }}" class="flex flex-col">
                        @csrf
                        <div class="px-2 overflow-y-auto custom-scrollbar">
                            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nama
                                    </label>
                                    <input type="text" name="nama"
                                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nip
                                    </label>
                                    <input type="text" name="nip"
                                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Pangkat
                                    </label>
                                    <input type="text" name="pangkat"
                                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Jabatan
                                    </label>
                                    <input type="text" name="jabatan"
                                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mt-6 lg:justify-end">
                            <button @click="open = false" type="button"
                                class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                                Close
                            </button>
                            <button type="submit"
                                class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>

            <x-ui.pagination :paginator="$pegawais" />
        </x-common.component-card>
    </div>
@endsection
