<div class="relative" x-data="{
    dropdownOpen: false,
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
    },
    closeDropdown() {
        this.dropdownOpen = false;
    }
}" @click.away="closeDropdown()">
    <!-- User Button -->
    <button class="flex items-center text-gray-700 dark:text-gray-400" @click.prevent="toggleDropdown()" type="button">
        <span class="mr-3 overflow-hidden rounded-full h-11 w-11 bg-brand-600 flex justify-center items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#fff" viewBox="0 0 256 256">
                <path
                    d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8c18.84-32.56,52.14-52,89.07-52s70.23,19.44,89.07,52a8,8,0,1,0,13.85-8ZM72,96a56,56,0,1,1,56,56A56.06,56.06,0,0,1,72,96Z">
                </path>
            </svg>
        </span>

        <span class="block mr-1 font-medium text-theme-sm">{{ auth()->user()->name ?? '-' }}</span>

        <!-- Chevron Icon -->
        <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark z-50"
        style="display: none;">
        <!-- User Info -->
        <div class="px-2 py-1">
            <span class="block font-semibold text-gray-800 text-theme-sm dark:text-white truncate">
                {{ auth()->user()->name ?? '-' }}
            </span>
            <span class="block text-theme-xs text-gray-500 dark:text-gray-400 truncate">
                {{ '@' . (auth()->user()->username ?? '-') }}
            </span>
            <div class="mt-1.5">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-brand-50 text-brand-700 dark:bg-brand-900/30 dark:text-brand-400">
                    {{ ucwords(str_replace('_', ' ', auth()->user()->role->name ?? 'User')) }}
                </span>
            </div>
        </div>

        <div class="my-2 border-t border-gray-100 dark:border-gray-800"></div>

        <!-- Menu Links -->
        <a href="{{ route('profile.edit') }}"
            class="flex items-center w-full gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white transition"
            @click="closeDropdown()">
            <span class="text-gray-500 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </span>
            <span>Edit Profil</span>
        </a>

        @if(auth()->user()?->role?->name === 'super_admin')
            <a href="{{ route('users.index') }}"
                class="flex items-center w-full gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white transition"
                @click="closeDropdown()">
                <span class="text-gray-500 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </span>
                <span>Kelola Akun (Master)</span>
            </a>
        @endif

        <div class="my-1.5 border-t border-gray-100 dark:border-gray-800"></div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="flex items-center w-full gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-rose-50 hover:text-rose-600 dark:text-gray-400 dark:hover:bg-rose-950/30 dark:hover:text-rose-400 transition"
                @click="closeDropdown()">
                <span class="text-gray-500 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                </span>
                Log Out
            </button>
        </form>

    </div>
</div>
