{{-- Modal Edit User --}}
<x-ui.modal
    @open-user-edit-modal.window="open = true"
    :isOpen="false" class="max-w-[600px]">
    <div x-data="{
            id: '', 
            name: '', 
            username: '', 
            role_id: '',
            dinas_id: '', 
            bidang_id: '', 
            sub_bidang_id: '', 
            pegawai_id: '',
            bidangs: [], 
            subBidangs: [],
            loadingBidang: false,
            loadingSubBidang: false,

            getTomSelect(name) {
                let el = this.$el.querySelector('select[name=\'' + name + '\']');
                if (!el) return null;
                if (el.tomselect) return el.tomselect;
                if (typeof TomSelect !== 'undefined' && !el.classList.contains('no-search')) {
                    try {
                        return new TomSelect(el, {
                            create: false,
                            plugins: ['dropdown_input'],
                            onChange: function(val) {
                                el.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        });
                    } catch(e) {
                        return null;
                    }
                }
                return null;
            },

            bindTomSelect(name, callback) {
                let ts = this.getTomSelect(name);
                let el = this.$el.querySelector('select[name=\'' + name + '\']');
                if (ts && el && !el._ts_bound) {
                    ts.on('change', (val) => {
                        callback(val);
                    });
                    el._ts_bound = true;
                }
            },

            updateTomSelectOptions(name, list, valueKey = 'id', textKey = 'nama_bidang', selectedVal = '') {
                let ts = this.getTomSelect(name);
                if (!ts) return;

                ts.clearOptions();
                ts.clear(true);
                if (list && list.length > 0) {
                    list.forEach(item => {
                        ts.addOption({ value: item[valueKey].toString(), text: item[textKey] });
                    });
                    ts.enable();
                } else {
                    ts.disable();
                }
                if (selectedVal) {
                    ts.setValue(selectedVal.toString(), true);
                }
                ts.refreshOptions(false);
            },

            // Fetch Bidang berdasarkan ID Dinas
            async fetchBidangs(dinasId = null) {
                if (dinasId && typeof dinasId !== 'object') {
                    this.dinas_id = dinasId;
                } else {
                    let el = this.$el.querySelector('select[name=\'dinas_id\']');
                    if (el && el.value) this.dinas_id = el.value;
                }

                this.bidang_id = '';
                this.sub_bidang_id = '';
                this.bidangs = [];
                this.subBidangs = [];

                this.updateTomSelectOptions('bidang_id', []);
                this.updateTomSelectOptions('sub_bidang_id', []);

                if (!this.dinas_id) return;

                this.loadingBidang = true;
                try {
                    let response = await fetch('/api/bidangs/' + this.dinas_id);
                    this.bidangs = await response.json();
                    this.updateTomSelectOptions('bidang_id', this.bidangs, 'id', 'nama_bidang', this.bidang_id);
                } catch(e) {
                    console.error('Gagal memuat data bidang:', e);
                } finally {
                    this.loadingBidang = false;
                }
            },

            // Fetch Sub Bidang berdasarkan ID Bidang
            async fetchSubBidangs(bidangId = null) {
                if (bidangId && typeof bidangId !== 'object') {
                    this.bidang_id = bidangId;
                } else {
                    let el = this.$el.querySelector('select[name=\'bidang_id\']');
                    if (el && el.value) this.bidang_id = el.value;
                }

                this.sub_bidang_id = '';
                this.subBidangs = [];

                this.updateTomSelectOptions('sub_bidang_id', []);

                if (!this.bidang_id) return;

                this.loadingSubBidang = true;
                try {
                    let response = await fetch('/api/sub-bidangs/' + this.bidang_id);
                    this.subBidangs = await response.json();
                    this.updateTomSelectOptions('sub_bidang_id', this.subBidangs, 'id', 'nama_sub_bidang', this.sub_bidang_id);
                } catch(e) {
                    console.error('Gagal memuat data sub bidang:', e);
                } finally {
                    this.loadingSubBidang = false;
                }
            },

            async loadInitialData() {
                this.bidangs = [];
                this.subBidangs = [];

                this.$nextTick(async () => {
                    this.bindTomSelect('dinas_id', (val) => {
                        this.fetchBidangs(val);
                    });
                    this.bindTomSelect('bidang_id', (val) => {
                        this.fetchSubBidangs(val);
                    });
                    this.bindTomSelect('sub_bidang_id', (val) => {
                        this.sub_bidang_id = val || '';
                    });

                    let tsDinas = this.getTomSelect('dinas_id');
                    if (tsDinas) {
                        tsDinas.setValue(this.dinas_id || '', true);
                    }

                    if (this.dinas_id) {
                        try {
                            let r1 = await fetch('/api/bidangs/' + this.dinas_id);
                            this.bidangs = await r1.json();
                            this.updateTomSelectOptions('bidang_id', this.bidangs, 'id', 'nama_bidang', this.bidang_id);
                        } catch(e) {}
                    } else {
                        this.updateTomSelectOptions('bidang_id', []);
                    }

                    if (this.bidang_id) {
                        try {
                            let r2 = await fetch('/api/sub-bidangs/' + this.bidang_id);
                            this.subBidangs = await r2.json();
                            this.updateTomSelectOptions('sub_bidang_id', this.subBidangs, 'id', 'nama_sub_bidang', this.sub_bidang_id);
                        } catch(e) {}
                    } else {
                        this.updateTomSelectOptions('sub_bidang_id', []);
                    }
                });
            },

            async onPegawaiChange(e) {
                let selectedOption = e.target.options[e.target.selectedIndex];
                let pDinas = selectedOption ? selectedOption.getAttribute('data-dinas') : null;
                let pBidang = selectedOption ? selectedOption.getAttribute('data-bidang') : null;

                if (pDinas) {
                    this.dinas_id = pDinas;
                    let tsDinas = this.getTomSelect('dinas_id');
                    if (tsDinas) tsDinas.setValue(pDinas, true);
                    await this.fetchBidangs(pDinas);

                    if (pBidang) {
                        this.bidang_id = pBidang;
                        let tsBidang = this.getTomSelect('bidang_id');
                        if (tsBidang) tsBidang.setValue(pBidang, true);
                        await this.fetchSubBidangs(pBidang);
                    }
                }
            }
        }"
        @open-user-edit-modal.window="
            id = $event.detail.id; 
            name = $event.detail.name; 
            username = $event.detail.username; 
            role_id = $event.detail.role_id;
            dinas_id = $event.detail.dinas_id || '{{ auth()->user()->dinas_id ?? ($dinas->count() == 1 ? $dinas->first()->id : '') }}';
            bidang_id = $event.detail.bidang_id; 
            sub_bidang_id = $event.detail.sub_bidang_id;
            pegawai_id = $event.detail.pegawai_id;
            loadInitialData();
        "
        class="no-scrollbar relative w-full max-w-[600px] overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">
        
        <div class="mb-6">
            <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                Edit Akun Pengguna
            </h4>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Perbarui nama, username, peran, atau sandi baru untuk akun ini.
            </p>
        </div>

        <form method="POST" :action="'/users/' + id" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" required x-model="name" placeholder="Masukkan nama lengkap"
                    class="h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                    Username <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="username" required x-model="username" placeholder="Masukkan username"
                    class="h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                    Password Baru <span class="text-gray-400">(Opsional)</span>
                </label>
                <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin diubah"
                    class="h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                    Role / Peran <span class="text-rose-500">*</span>
                </label>
                <select name="role_id" required x-model="role_id"
                    class="no-search h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    <option value="" disabled>Pilih Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                    Hubungkan ke Pegawai <span class="text-gray-400">(Opsional)</span>
                </label>
                <select name="pegawai_id" x-model="pegawai_id" @change="onPegawaiChange($event)"
                    class="no-search h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    <option value="">-- Pilih Pegawai (Tidak terhubung) --</option>
                    @foreach($pegawais as $pegawai)
                        <option value="{{ $pegawai->id }}" data-dinas="{{ $pegawai->dinas_id }}" data-bidang="{{ $pegawai->bidang_id }}">
                            {{ $pegawai->nama }} ({{ $pegawai->nip ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-4">
                {{-- 1. Dinas --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                        Dinas <span class="text-gray-400">(Opsional)</span>
                    </label>
                    <select name="dinas_id" x-model="dinas_id" @change="fetchBidangs($event.target.value)"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="">-- Pilih Dinas --</option>
                        @foreach($dinas as $d)
                            <option value="{{ $d->id }}">{{ $d->nama_dinas }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 2. Bidang --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                        Bidang <span class="text-gray-400">(Opsional)</span>
                    </label>
                    <select name="bidang_id" x-model="bidang_id" @change="fetchSubBidangs($event.target.value)"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="">-- Pilih Bidang --</option>
                        <template x-for="b in bidangs" :key="b.id">
                            <option :value="b.id" x-text="b.nama_bidang" :selected="b.id == bidang_id"></option>
                        </template>
                    </select>
                </div>

                {{-- 3. Sub Bidang --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-400">
                        Sub Bidang <span class="text-gray-400">(Opsional)</span>
                    </label>
                    <select name="sub_bidang_id" x-model="sub_bidang_id"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="">-- Pilih Sub Bidang --</option>
                        <template x-for="sb in subBidangs" :key="sb.id">
                            <option :value="sb.id" x-text="sb.nama_sub_bidang" :selected="sb.id == sub_bidang_id"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <button @click="$dispatch('close-modal') || (open = false)" type="button"
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
