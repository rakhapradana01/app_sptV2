@if(!$spt->has_real_nomor)
    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 rounded-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-amber-100 dark:bg-amber-900 rounded-lg text-amber-700 dark:text-amber-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-amber-800 dark:text-amber-200 text-sm">Nomor SPT Belum Diisi Resmi</h4>
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">SPT ini masih menggunakan nomor template/placeholder. Silakan input nomor SPT resmi.</p>
            </div>
        </div>
        <button @click="showSptModal = true" class="px-4 py-2 text-xs font-semibold text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition">
            Input Nomor SPT Resmi
        </button>
    </div>
@endif

