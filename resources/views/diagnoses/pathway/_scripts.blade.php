<script>
document.addEventListener('alpine:init', () => {

    // ─────────────────────────────────────────────
    // simulationForm — tabel tarif & matrix
    // ─────────────────────────────────────────────
    Alpine.data('simulationForm', () => ({
        activeTab: {{ $roomClasses->first()->id ?? 0 }},
        searchQuery: '',
        adminFeePercentage: {{ $diagnosis->admin_fee_percentage ?? 6.00 }},

        matrix: {!! json_encode(collect($matrix)->map(function($row) use ($roomClasses) {
            foreach ($roomClasses as $rc) {
                $row['tariffs'][$rc->id]['amount_formatted'] = $row['tariffs'][$rc->id]['amount']
                    ? number_format($row['tariffs'][$rc->id]['amount'], 0, ',', '.')
                    : '0';
            }
            $row['deleted'] = false;
            $row['is_new'] = false;
            return $row;
        })) !!},

        init() {
            // Listen for service-added event from addServiceModal via window
            window.addEventListener('service-added', (e) => {
                this.addService(e.detail);
            });
        },

        formatCurrency(value) {
            if (value === null || value === undefined || isNaN(value)) return '0';
            return new Intl.NumberFormat('id-ID').format(value);
        },

        parseCurrency(value) {
            if (!value) return 0;
            return parseInt(value.toString().replace(/[^0-9]/g, ''), 10) || 0;
        },

        onFocusAmount(index, rcId) {
            let val = this.matrix[index].tariffs[rcId].amount_formatted;
            if (val === '0') {
                this.matrix[index].tariffs[rcId].amount_formatted = '';
            }
        },

        onBlurAmount(index, rcId) {
            let val = this.matrix[index].tariffs[rcId].amount_formatted;
            if (val === '') {
                this.matrix[index].tariffs[rcId].amount_formatted = '0';
                this.matrix[index].tariffs[rcId].amount = 0;
            }
            this.updateTotal(index);
        },

        onInputAmount(index, rcId) {
            let val = this.matrix[index].tariffs[rcId].amount_formatted;
            let cleanVal = val.toString().replace(/\D/g, '');

            if (cleanVal !== '') {
                let numericVal = parseInt(cleanVal, 10);
                this.matrix[index].tariffs[rcId].amount = numericVal;
                this.matrix[index].tariffs[rcId].amount_formatted = numericVal.toLocaleString('id-ID');
            } else {
                this.matrix[index].tariffs[rcId].amount = 0;
                this.matrix[index].tariffs[rcId].amount_formatted = '';
            }
            this.updateTotal(index);
        },

        updateTotal(index) {
            let qty = parseInt(this.matrix[index].qty) || 0;
            Object.keys(this.matrix[index].tariffs).forEach(rcId => {
                let amount = this.matrix[index].tariffs[rcId].amount || 0;
                this.matrix[index].tariffs[rcId].total = qty * amount;
            });
        },

        getGrandTotal(rcId) {
            if (!rcId || this.matrix.length === 0) return 0;
            return this.matrix.reduce((sum, row) =>
                sum + (row.deleted ? 0 : (row.tariffs[rcId]?.total || 0)), 0);
        },

        get addedMatrixIndices() {
            return this.matrix
                .map((row, i) => (row.is_new && !row.deleted) ? i : -1)
                .filter(i => i !== -1);
        },

        addService(service) {
            let tariffs = {};
            @foreach($roomClasses as $rc)
            tariffs[{{ $rc->id }}] = { amount: 0, amount_formatted: '0', total: 0 };
            @endforeach

            this.matrix.push({
                is_new: true,
                deleted: false,
                id: service.id,
                code: service.code,
                name: service.name,
                description: service.description || '-',
                type: service.type || 'Lainnya',
                qty: 1,
                tariffs: tariffs,
            });

            this.updateTotal(this.matrix.length - 1);
            window.Toast && Toast.success('Layanan "' + service.name + '" berhasil ditambahkan.');
        },

        removeService(index) {
            if (confirm('Hapus layanan "' + this.matrix[index].name + '" dari simulasi?')) {
                this.matrix[index].deleted = true;
                Object.keys(this.matrix[index].tariffs).forEach(rcId => {
                    this.matrix[index].tariffs[rcId].amount = 0;
                    this.matrix[index].tariffs[rcId].total = 0;
                });
                window.Toast && Toast.success('Layanan dihapus dari simulasi.');
            }
        },

        matchesSearch(index) {
            const row = this.matrix[index];
            if (row.deleted) return false;
            if (!this.searchQuery) return true;
            const q = this.searchQuery.toLowerCase();
            return (row.name && row.name.toLowerCase().includes(q)) ||
                   (row.code && row.code.toLowerCase().includes(q));
        },

        hasAnyMatchInType(type) {
            const hasItems = this.matrix.some(row => row.type === type && !row.deleted && !row.is_new);
            if (!hasItems) return false;
            if (!this.searchQuery) return true;
            const q = this.searchQuery.toLowerCase();
            return this.matrix.some(row =>
                row.type === type &&
                !row.deleted &&
                !row.is_new &&
                ((row.name && row.name.toLowerCase().includes(q)) ||
                 (row.code && row.code.toLowerCase().includes(q)))
            );
        },

        getKamarQty() {
            let kamarRow = this.matrix.find(row =>
                !row.deleted &&
                (row.code === 'KAMAR' || (row.name && row.name.toLowerCase().includes('kamar rawat')))
            );
            return kamarRow ? (parseInt(kamarRow.qty) || 0) : {{ $pathway->length_of_stay }};
        },

        saveChanges() {
            const url = '{{ route("diagnoses.pathway.update", $diagnosis->id ?? 0) }}';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ 
                    matrix: this.matrix,
                    admin_fee_percentage: this.adminFeePercentage
                })
            })
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                window.Toast && Toast.success(data.message || 'Perubahan simulasi berhasil disimpan.');
                // Mark all new as no longer new
                this.matrix.forEach(row => { row.is_new = false; });
                
                // Reload halaman agar state sinkron dengan database
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            })
            .catch(err => {
                window.Toast && Toast.error('Gagal menyimpan perubahan.');
            });
        },
    }));

    // ─────────────────────────────────────────────
    // addServiceModal — modal pencarian layanan
    // ─────────────────────────────────────────────
    Alpine.data('addServiceModal', () => ({
        open: false,
        searchQuery: '',
        results: [],
        loading: false,
        focusedIndex: -1,
        _debounceTimer: null,

        openModal() {
            this.open = true;
            this.searchQuery = '';
            this.results = [];
            this.focusedIndex = -1;
            this.$nextTick(() => {
                this.$refs.searchInput?.focus();
            });
        },

        closeModal() {
            this.open = false;
            this.searchQuery = '';
            this.results = [];
            this.focusedIndex = -1;
        },

        async fetchServices() {
            const q = this.searchQuery.trim();
            if (q.length < 2) {
                this.results = [];
                this.focusedIndex = -1;
                return;
            }

            this.loading = true;
            try {
                const url = new URL('{{ route("diagnoses.services.search") }}', window.location.origin);
                url.searchParams.set('q', q);

                const response = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                });

                if (!response.ok) throw new Error('Network error');
                
                // Get matrix from parent component safely
                let currentMatrix = [];
                try {
                    currentMatrix = Alpine.$data(document.querySelector('[x-data="simulationForm()"]')).matrix || [];
                } catch (err) {}

                const items = await response.json();
                this.results = items.map(service => {
                    service.is_added = currentMatrix.some(r => r.type === service.type && r.id === service.id && !r.deleted);
                    return service;
                });

                // Find first non-added item for focus
                this.focusedIndex = this.results.findIndex(s => !s.is_added);
            } catch (e) {
                this.results = [];
                window.Toast && Toast.error('Gagal memuat daftar layanan.');
            } finally {
                this.loading = false;
            }
        },

        moveFocus(direction) {
            if (this.results.length === 0) return;
            const max = this.results.length - 1;
            let nextIndex = this.focusedIndex;
            let found = false;
            
            for (let i = 0; i < this.results.length; i++) {
                if (direction === 1) {
                    nextIndex = nextIndex >= max ? 0 : nextIndex + 1;
                } else {
                    nextIndex = nextIndex <= 0 ? max : nextIndex - 1;
                }
                if (!this.results[nextIndex].is_added) {
                    found = true;
                    break;
                }
            }
            
            if (found) {
                this.focusedIndex = nextIndex;
                this.$nextTick(() => {
                    const el = document.getElementById('result-item-' + nextIndex);
                    if (el) {
                        el.scrollIntoView({ block: 'nearest' });
                    }
                });
            }
        },

        selectFocused() {
            if (this.focusedIndex >= 0 && this.focusedIndex < this.results.length) {
                if (!this.results[this.focusedIndex].is_added) {
                    this.selectService(this.results[this.focusedIndex]);
                }
            }
        },

        selectService(service) {
            // Dispatch on window so simulationForm can receive it across Alpine scope boundaries
            window.dispatchEvent(new CustomEvent('service-added', {
                detail: service,
            }));
            this.closeModal();
        },
    }));
});
</script>
