@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Master Akun" />
    <div class="space-y-6">
        <x-common.component-card title="Daftar Akun Pengguna">
            <div class="mb-4">
                <x-ui.button size="sm" @click="$dispatch('open-user-create-modal')">Tambah Akun</x-ui.button>
            </div>

            @if ($errors->any())
                <div class="mx-2 mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mx-2 mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mx-2 mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter & Search Bar -->
            <div class="mb-6 rounded-2xl border border-gray-200 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-900/50"
                 x-data="{
                    filter_dinas_id: '{{ request('dinas_id') }}',
                    filter_bidang_id: '{{ request('bidang_id') }}',
                    filter_sub_bidang_id: '{{ request('sub_bidang_id') }}',
                    bidangs: [],
                    subBidangs: [],
                    async fetchBidangs() {
                        this.filter_bidang_id = '';
                        this.filter_sub_bidang_id = '';
                        this.bidangs = [];
                        this.subBidangs = [];
                        if (this.filter_dinas_id) {
                            let res = await fetch('/api/bidangs/' + this.filter_dinas_id);
                            this.bidangs = await res.json();
                        }
                    },
                    async fetchSubBidangs() {
                        this.filter_sub_bidang_id = '';
                        this.subBidangs = [];
                        if (this.filter_bidang_id) {
                            let res = await fetch('/api/sub-bidangs/' + this.filter_bidang_id);
                            this.subBidangs = await res.json();
                        }
                    },
                    async initFilter() {
                        if (this.filter_dinas_id) {
                            let res1 = await fetch('/api/bidangs/' + this.filter_dinas_id);
                            this.bidangs = await res1.json();
                            this.filter_bidang_id = '{{ request('bidang_id') }}';
                        }
                        if (this.filter_bidang_id) {
                            let res2 = await fetch('/api/sub-bidangs/' + this.filter_bidang_id);
                            this.subBidangs = await res2.json();
                            this.filter_sub_bidang_id = '{{ request('sub_bidang_id') }}';
                        }
                    }
                 }"
                 x-init="initFilter()">
                <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                    <!-- Search -->
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-400">Pencarian Akun</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username..."
                               class="no-search dark:bg-dark-900 h-9 w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                    </div>

                    <!-- Dinas -->
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-400">Filter Dinas</label>
                        <select name="dinas_id" x-model="filter_dinas_id" @change="fetchBidangs"
                                class="no-search dark:bg-dark-900 h-9 w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">Semua Dinas</option>
                            @foreach($dinas as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_dinas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bidang -->
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-400">Filter Bidang</label>
                        <select name="bidang_id" x-model="filter_bidang_id" @change="fetchSubBidangs" :disabled="!filter_dinas_id || bidangs.length === 0"
                                class="no-search dark:bg-dark-900 h-9 w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white disabled:bg-gray-100 disabled:opacity-60 dark:disabled:bg-gray-800">
                            <option value="" x-text="!filter_dinas_id ? '-- Pilih Dinas --' : (bidangs.length === 0 ? '-- Tidak Ada Bidang --' : 'Semua Bidang')"></option>
                            <template x-for="b in bidangs" :key="b.id">
                                <option :value="b.id" x-text="b.nama_bidang" :selected="b.id == filter_bidang_id"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Sub Bidang & Action Buttons -->
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-400">Filter Sub Bidang</label>
                            <select name="sub_bidang_id" x-model="filter_sub_bidang_id" :disabled="!filter_bidang_id || subBidangs.length === 0"
                                    class="no-search dark:bg-dark-900 h-9 w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white disabled:bg-gray-100 disabled:opacity-60 dark:disabled:bg-gray-800">
                                <option value="" x-text="!filter_bidang_id ? '-- Pilih Bidang --' : (subBidangs.length === 0 ? '-- Tidak Ada Sub Bidang --' : 'Semua Sub Bidang')"></option>
                                <template x-for="sb in subBidangs" :key="sb.id">
                                    <option :value="sb.id" x-text="sb.nama_sub_bidang" :selected="sb.id == filter_sub_bidang_id"></option>
                                </template>
                            </select>
                        </div>
                        <button type="submit" class="h-9 px-4 rounded-lg bg-blue-600 text-xs font-semibold text-white hover:bg-blue-700 transition flex items-center justify-center">
                            Cari
                        </button>
                        @if(request()->anyFilled(['search', 'dinas_id', 'bidang_id', 'sub_bidang_id']))
                            <a href="{{ route('users.index') }}" class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[800px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800 text-left">
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">No</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">Nama Lengkap</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">Username</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">Unit Kerja</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">Role</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white text-sm">
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $users->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 font-medium text-gray-900 dark:text-white">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-gray-500 dark:text-gray-400">
                                        {{ $user->username }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-gray-500 dark:text-gray-400 text-xs">
                                        @if($user->dinas)
                                            <div><strong>Dinas:</strong> {{ $user->dinas->nama_dinas ?? '-' }}</div>
                                            <div><strong>Bidang:</strong> {{ $user->bidang->nama_bidang ?? '-' }}</div>
                                            <div><strong>Sub:</strong> {{ $user->subBidang->nama_sub_bidang ?? '-' }}</div>
                                        @else
                                            -
                                        @endif
                                        @if($user->pegawai)
                                            <div class="mt-1 text-emerald-600 dark:text-emerald-400 font-semibold">
                                                <strong>Pegawai:</strong> {{ $user->pegawai->nama }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 border border-blue-200/50">
                                            {{ ucwords(str_replace('_', ' ', $user->role->name ?? '')) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-2">
                                            <x-ui.button variant="yellow" size="xs"
                                                @click="$dispatch('open-user-edit-modal', { id: '{{ $user->id }}', name: '{{ addslashes($user->name) }}', username: '{{ addslashes($user->username) }}', role_id: '{{ $user->role_id }}', dinas_id: '{{ $user->dinas_id }}', bidang_id: '{{ $user->bidang_id }}', sub_bidang_id: '{{ $user->sub_bidang_id }}', pegawai_id: '{{ $user->pegawai_id }}' })">
                                                Edit
                                            </x-ui.button>

                                            @if(auth()->id() !== $user->id)
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-ui.button variant="red" size="xs" type="submit">
                                                        Hapus
                                                    </x-ui.button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500 italic">
                                        Belum ada data user.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <x-ui.pagination :paginator="$users" />
            </div>

            {{-- Modal Tambah User --}}
            @include('pages.master.users.partials.modal-create')

            {{-- Modal Edit User --}}
            @include('pages.master.users.partials.modal-edit')
        </x-common.component-card>
    </div>
@endsection

