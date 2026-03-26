@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Nota Dinas" />
    <div class="space-y-6">
        <x-common.component-card title="Daftar Nota Dinas">

            <div class="px-2">
                @if (in_array(auth()->user()->role->name, ['super_admin', 'kepala_sub_bidang']))
                    <a href="{{ route('nota-dinas.create') }}">
                        <x-ui.button size="sm">Tambah</x-ui.button>
                    </a>
                @endif
            </div>
            <div
                class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1102px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left sm:px-6">No</th>
                                <th class="px-5 py-3 text-left sm:px-6">Nomor</th>
                                <th class="px-5 py-3 text-left sm:px-6">Perihal</th>
                                <th class="px-5 py-3 text-left sm:px-6">Tujuan</th>
                                <th class="px-5 py-3 text-left sm:px-6">Status</th>
                                <th class="px-5 py-3 text-left sm:px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notaDinas as $nota)
                                <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white">
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $notaDinas->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $nota->nomor_urut }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $nota->perihal }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $nota->lokasi }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $nota->status }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{-- @if (auth()->user()->role->name == 'user' && $nota->isFinal())
                                            Tombol Cetak
                                        @endif --}}

                                        {{-- @if ($nota->status == 'disetujui_kabid')
                                            <a href="{{ route('nota.cetak', $nota->id) }}">Cetak Nota</a>
                                            <a href="{{ route('spt.cetak', $nota->id) }}">Cetak SPT</a>
                                        @endif --}}

                                        @if (optional(auth()->user()->role)->name === 'kepala_sub_bidang' &&
                                                $nota->status === \App\Models\NotaDinas::DISETUJUI_KABID)
                                            <div class="mt-4">
                                                <a href="{{ route('nota.cetakNotaDinas', $nota->id) }}" target="_blank"
                                                    class="inline-block px-4 py-2 bg-blue-600 text-white rounded whitespace-nowrap">
                                                    Nota Dinas
                                                </a>
                                            </div>
                                        @endif

                                        @if (auth()->user()->role->name == 'kepala_sub_bidang' && $nota->status == 'draft')
                                            <form action="{{ route('nota-dinas.approve-kasubid', $nota->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.button size="sm" variant="success">Kirim
                                                    Kabid</x-ui.button>
                                            </form>
                                        @endif


                                        @if (auth()->user()->role->name == 'kepala_bidang' && $nota->status == 'diajukan_kabid')
                                            <a href="{{ route('nota-dinas.preview', $nota->id) }}">
                                                <x-ui.button size="sm" variant="primary">
                                                    Lihat & Approve
                                                </x-ui.button>
                                            </a>
                                        @endif

                                        @if (auth()->user()->role->name == 'super_admin')
                                            <div class="flex gap-2">

                                                @if ($nota->status == 'draft')
                                                    <form action="{{ route('nota-dinas.kirim-kasubid', $nota->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <x-ui.button size="sm">Kirim</x-ui.button>
                                                    </form>
                                                @endif

                                                @if ($nota->status == 'diajukan_kasubid')
                                                    <form action="{{ route('nota-dinas.approve-kasubid', $nota->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <x-ui.button size="sm" variant="success">Approve
                                                            Kasubid</x-ui.button>
                                                    </form>
                                                @endif

                                                @if ($nota->status == 'disetujui_kasubid')
                                                    <form action="{{ route('nota-dinas.approve-kabid', $nota->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <x-ui.button size="sm" variant="primary">Approve
                                                            Kabid</x-ui.button>
                                                    </form>
                                                @endif

                                            </div>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <x-ui.pagination :paginator="$notaDinas" />
        </x-common.component-card>
    </div>
@endsection
