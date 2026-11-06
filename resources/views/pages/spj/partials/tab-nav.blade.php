<!-- Tab Headers -->
<div class="flex flex-wrap border-b border-gray-200 dark:border-gray-700 gap-1">
    <button @click="activeTab = 'kuitansi'"
        :class="activeTab === 'kuitansi' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        class="px-4 py-2 font-medium text-sm border-b-2 transition-all">
        Kuitansi
    </button>
    <button @click="activeTab = 'rincian'"
        :class="activeTab === 'rincian' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        class="px-4 py-2 font-medium text-sm border-b-2 transition-all">
        Rincian
    </button>
    <button @click="activeTab = 'pengeluaran_rill'"
        :class="activeTab === 'pengeluaran_rill' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        class="px-4 py-2 font-medium text-sm border-b-2 transition-all">
        Daftar pengeluaran rill
    </button>
</div>

