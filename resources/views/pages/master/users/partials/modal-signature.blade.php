{{-- Modal Upload Signature --}}
<div x-data="{
    openSignatureModal: false,
    userId: null,
    userName: '',
    currentSignature: null,
    init() {
        window.addEventListener('open-signature-modal', (e) => {
            this.userId        = e.detail.id;
            this.userName      = e.detail.name;
            this.currentSignature = e.detail.signature;
            this.openSignatureModal = true;
        });
    }
}"
    x-init="init()"
    @keydown.escape.window="openSignatureModal = false">

    <div x-show="openSignatureModal"
         class="fixed inset-0 z-50 flex items-center justify-center"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50" @click="openSignatureModal = false"></div>

        {{-- Modal Panel --}}
        <div class="relative z-10 w-full max-w-sm mx-4 bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">

            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Upload Tanda Tangan</h3>
                <button @click="openSignatureModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Preview tanda tangan saat ini --}}
            <div x-show="currentSignature" class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Tanda tangan saat ini:</p>
                <img :src="'/' + currentSignature" alt="Tanda tangan" class="h-12 mx-auto object-contain">
            </div>
            <div x-show="!currentSignature" class="mb-4 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-700 text-center">
                <p class="text-xs text-amber-600 dark:text-amber-400">Belum ada tanda tangan yang diupload.</p>
            </div>

            <form :action="'/users/' + userId + '/signature'" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Nama Pejabat
                    </label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="userName"></p>
                </div>

                <div class="mb-5">
                    <label class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        File Tanda Tangan <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" name="signature" accept="image/jpeg,image/jpg,image/png"
                           required
                           class="block w-full text-sm text-gray-700 dark:text-gray-300
                                  file:mr-3 file:py-1.5 file:px-3
                                  file:rounded-lg file:border-0
                                  file:text-xs file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100
                                  dark:file:bg-blue-900/30 dark:file:text-blue-400
                                  border border-gray-300 dark:border-gray-600 rounded-lg p-1.5
                                  bg-white dark:bg-gray-800">
                    <p class="mt-1 text-xs text-gray-400">Format: JPEG/JPG/PNG, maks 1 MB. Gunakan latar transparan/putih.</p>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="openSignatureModal = false"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
