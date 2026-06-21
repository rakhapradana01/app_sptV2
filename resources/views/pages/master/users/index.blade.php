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

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[800px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800 text-left">
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">No</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">Nama Lengkap</th>
                                <th class="px-5 py-3 sm:px-6 font-semibold text-sm text-gray-500 dark:text-gray-400">Username</th>
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
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 border border-blue-200/50">
                                            {{ ucwords(str_replace('_', ' ', $user->role->name ?? '')) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-2">
                                            <x-ui.button variant="yellow" size="xs"
                                                @click="$dispatch('open-user-edit-modal', { id: '{{ $user->id }}', name: '{{ addslashes($user->name) }}', username: '{{ addslashes($user->username) }}', role_id: '{{ $user->role_id }}' })">
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

            <!-- Modal Tambah User -->
            <x-ui.modal x-data="{ open: false }" @open-user-create-modal.window="open = true" :isOpen="false" class="max-w-[600px]">
                <div class="no-scrollbar relative w-full max-w-[600px] overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                            Tambah Akun Baru
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Buat kredensial akun pengguna baru beserta perannya.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" required placeholder="Masukkan nama lengkap"
                                class="dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                                Username <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="username" required placeholder="Masukkan username"
                                class="dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" name="password" required placeholder="Masukkan password (min 6 karakter)"
                                class="dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                                Role / Peran <span class="text-rose-500">*</span>
                            </label>
                            <select name="role_id" required
                                class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="" disabled selected>Pilih Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <button @click="open = false" type="button"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                Batal
                            </button>
                            <button type="submit"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                Simpan Akun
                            </button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>

            <!-- Modal Edit User -->
            <x-ui.modal x-data="{ open: false, id: '', name: '', username: '', role_id: '' }" 
                @open-user-edit-modal.window="open = true; id = $event.detail.id; name = $event.detail.name; username = $event.detail.username; role_id = $event.detail.role_id" 
                :isOpen="false" class="max-w-[600px]">
                <div class="no-scrollbar relative w-full max-w-[600px] overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                            Edit Akun Pengguna
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Perbarui nama, username, peran, atau sandi baru untuk akun ini.
                        </p>
                    </div>
                    <form method="POST" :action="`{{ url('users') }}/${id}`" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" required x-model="name" placeholder="Masukkan nama lengkap"
                                class="dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                                Username <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="username" required x-model="username" placeholder="Masukkan username"
                                class="dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                                Password Baru <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin diubah"
                                class="dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                                Role / Peran <span class="text-rose-500">*</span>
                            </label>
                            <select name="role_id" required x-model="role_id"
                                class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="" disabled>Pilih Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <button @click="open = false" type="button"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                Batal
                            </button>
                            <button type="submit"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                Perbarui Akun
                            </button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>
        </x-common.component-card>
    </div>
@endsection
