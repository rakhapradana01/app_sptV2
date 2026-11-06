<!-- Modal Edit SPT -->
<div x-show="showSptModal"
    x-effect="document.body.style.overflow = showSptModal ? 'hidden' : ''"
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
    x-cloak x-transition>
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl w-full max-w-md overflow-hidden"
        @click.away="showSptModal = false">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Input / Edit Nomor SPT</h3>
            <button @click="showSptModal = false" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="{{ route('spt.updateNomor', $spt->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor SPT</label>
                <input type="text" name="nomor_spt" required value="{{ $spt->nomor_spt }}"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white form-control">
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" @click="showSptModal = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

