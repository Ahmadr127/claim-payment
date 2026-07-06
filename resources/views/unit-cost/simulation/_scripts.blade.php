{{-- _scripts.blade.php --}}
<script>
document.addEventListener('alpine:init', () => {
    // ─────────────────────────────────────────────
    // unitCostSimulationForm — matrix & totals calculation
    // ─────────────────────────────────────────────
    Alpine.data('unitCostSimulationForm', () => ({
        activeTab: {{ $roomClasses->first()->id ?? 0 }},
        searchQuery: '',
        adminFeePercentage: {{ $diagnosis->admin_fee_percentage ?? 6.00 }},
        categoryTotals: {},
        matrix: [],

        init() {
            this.initializeMatrix();
            this.calculateTotals();

            // Listen for dynamic service additions
            window.addEventListener('service-added', (e) => {
                this.addService(e.detail);
            });
        },

        initializeMatrix() {
            const rawMatrix = {!! json_encode($matrix) !!};
            const roomClasses = {!! json_encode($roomClasses->pluck('id')->toArray()) !!};
            
            this.matrix = rawMatrix.map(row => {
                row.qty = row.qty || 0;
                row.deleted = row.deleted || false;
                row.is_new = row.is_new || false;

                roomClasses.forEach(rcId => {
                    if (!row.tariffs[rcId]) {
                        row.tariffs[rcId] = { amount: 0, amount_formatted: '0', total: 0, hna: 0, hna_formatted: '0', ppn: 0, hna_ppn: 0, percentage: 0, base_amount: 0 };
                    }
                    row.tariffs[rcId].amount = parseFloat(row.tariffs[rcId].amount) || 0;
                    row.tariffs[rcId].amount_formatted = row.tariffs[rcId].amount
                        ? this.formatCurrency(row.tariffs[rcId].amount)
                        : '0';
                    row.tariffs[rcId].hna = parseFloat(row.tariffs[rcId].hna) || 0;
                    row.tariffs[rcId].hna_formatted = row.tariffs[rcId].hna
                        ? this.formatCurrency(row.tariffs[rcId].hna)
                        : '0';
                    row.tariffs[rcId].ppn = parseFloat(row.tariffs[rcId].ppn) || 0;
                    row.tariffs[rcId].hna_ppn = parseFloat(row.tariffs[rcId].hna_ppn) || 0;
                    if (row.type === 'Medication' && !row.tariffs[rcId].hna_ppn) {
                        row.tariffs[rcId].hna_ppn = Math.round(row.tariffs[rcId].hna * (1 + (row.tariffs[rcId].ppn / 100)));
                    }
                    let defaultPct = row.type === 'MedicalService' ? 70 : (row.type === 'RoomTariffType' ? 100 : 0);
                    let rawPct = row.tariffs[rcId].percentage;
                    if (rawPct === null || rawPct === undefined || rawPct === '') {
                        row.tariffs[rcId].percentage = defaultPct;
                    } else {
                        row.tariffs[rcId].percentage = parseFloat(rawPct);
                    }
                    row.tariffs[rcId].base_amount = parseFloat(row.tariffs[rcId].base_amount) || 0;
                    row.tariffs[rcId].total = parseFloat(row.tariffs[rcId].total) || 0;
                });
                return row;
            });

            // Initialize category totals
            const roomClassIds = {!! json_encode($roomClasses->pluck('id')->toArray()) !!};
            roomClassIds.forEach(rcId => {
                this.categoryTotals[rcId] = {
                    'RoomTariffType': 0,
                    'MedicalService': 0,
                    'Medication': 0,
                    'total': 0
                };
            });
        },

        formatCurrency(value) {
            if (value === null || value === undefined || isNaN(value)) return '0';
            return new Intl.NumberFormat('id-ID').format(Math.round(value));
        },

        parseCurrency(value) {
            if (!value) return 0;
            return parseInt(value.toString().replace(/[^0-9]/g, ''), 10) || 0;
        },

        updateTotal(index) {
            const row = this.matrix[index];
            if (row.deleted) return;
            const qty = row.qty || 0;
            Object.keys(row.tariffs).forEach(rcId => {
                row.tariffs[rcId].total = row.tariffs[rcId].amount * qty;
            });
            this.calculateTotals();
        },

        updatePercentage(index, rcId) {
            const row = this.matrix[index];
            if ((row.type === 'MedicalService' || row.type === 'RoomTariffType') && !row.deleted) {
                const baseAmount = row.tariffs[rcId].base_amount || 0;
                let percentage = row.tariffs[rcId].percentage;
                if (percentage === undefined || percentage === null || percentage === '') {
                    percentage = row.type === 'MedicalService' ? 70 : 100;
                } else {
                    percentage = parseFloat(percentage);
                }
                const newAmount = Math.round(baseAmount * (percentage / 100));
                row.tariffs[rcId].amount = newAmount;
                row.tariffs[rcId].amount_formatted = this.formatCurrency(newAmount);
                this.updateTotal(index);
            }
        },

        onFocusHna(index, rcId) {
            const val = this.matrix[index].tariffs[rcId].hna_formatted;
            if (val === '0') {
                this.$nextTick(() => {
                    this.matrix[index].tariffs[rcId].hna_formatted = '';
                });
            }
        },

        onBlurHna(index, rcId) {
            let val = this.matrix[index].tariffs[rcId].hna_formatted;
            if (val === '') {
                this.matrix[index].tariffs[rcId].hna_formatted = '0';
                this.matrix[index].tariffs[rcId].hna = 0;
            } else {
                this.matrix[index].tariffs[rcId].hna = this.parseCurrency(val);
                this.matrix[index].tariffs[rcId].hna_formatted = this.formatCurrency(this.matrix[index].tariffs[rcId].hna);
            }
            
            const row = this.matrix[index];
            if (row.type === 'Medication') {
                this.updateHnaPpn(index, rcId);
            } else if (row.type === 'MedicalService' || row.type === 'RoomTariffType') {
                row.tariffs[rcId].base_amount = row.tariffs[rcId].hna;
                this.updatePercentage(index, rcId);
            } else {
                row.tariffs[rcId].amount = row.tariffs[rcId].hna;
                row.tariffs[rcId].amount_formatted = this.formatCurrency(row.tariffs[rcId].hna);
                this.updateTotal(index);
            }
        },

        onInputHna(index, rcId) {
            let val = this.matrix[index].tariffs[rcId].hna_formatted;
            let cleanVal = val.toString().replace(/\D/g, '');

            if (cleanVal !== '') {
                let numericVal = parseInt(cleanVal, 10);
                this.matrix[index].tariffs[rcId].hna = numericVal;
                this.matrix[index].tariffs[rcId].hna_formatted = this.formatCurrency(numericVal);
            } else {
                this.matrix[index].tariffs[rcId].hna = 0;
            }
            
            const row = this.matrix[index];
            if (row.type === 'Medication') {
                this.updateHnaPpn(index, rcId);
            } else if (row.type === 'MedicalService' || row.type === 'RoomTariffType') {
                row.tariffs[rcId].base_amount = row.tariffs[rcId].hna;
                this.updatePercentage(index, rcId);
            } else {
                row.tariffs[rcId].amount = row.tariffs[rcId].hna;
                row.tariffs[rcId].amount_formatted = this.formatCurrency(row.tariffs[rcId].hna);
                this.updateTotal(index);
            }
        },

        updateHnaPpn(index, rcId) {
            const row = this.matrix[index];
            if (row.type === 'Medication') {
                const hna = row.tariffs[rcId].hna || 0;
                const ppn = row.tariffs[rcId].ppn || 0;
                row.tariffs[rcId].hna_ppn = Math.round(hna * (1 + (ppn / 100)));
                row.tariffs[rcId].amount = row.tariffs[rcId].hna_ppn;
                row.tariffs[rcId].amount_formatted = this.formatCurrency(row.tariffs[rcId].hna_ppn);
                this.updateTotal(index);
            }
        },

        calculateTotals() {
            const roomClassIds = {!! json_encode($roomClasses->pluck('id')->toArray()) !!};
            
            roomClassIds.forEach(rcId => {
                this.categoryTotals[rcId] = {
                    'RoomTariffType': 0,
                    'MedicalService': 0,
                    'Medication': 0,
                    'total': 0
                };
            });

            this.matrix.forEach(row => {
                if (row.deleted) return;
                Object.keys(row.tariffs).forEach(rcId => {
                    const tariff = row.tariffs[rcId];
                    const type = row.type;
                    if (this.categoryTotals[rcId] && this.categoryTotals[rcId][type] !== undefined) {
                        this.categoryTotals[rcId][type] += tariff.total;
                    }
                });
            });

            roomClassIds.forEach(rcId => {
                if (this.categoryTotals[rcId]) {
                    this.categoryTotals[rcId].total = 
                        this.categoryTotals[rcId]['RoomTariffType'] +
                        this.categoryTotals[rcId]['MedicalService'] +
                        this.categoryTotals[rcId]['Medication'];
                }
            });
        },

        addService(service) {
            // Check if already exists and is active
            const exists = this.matrix.some(r => r.type === service.type && r.id === service.id && !r.deleted);
            if (exists) {
                window.Toast && Toast.error('Layanan sudah ada di dalam simulasi.');
                return;
            }

            let tariffs = {};
            let roomTariffRow = this.matrix.find(r => r.type === 'RoomTariffType' && !r.deleted);

            @foreach($roomClasses as $rc)
            {
                let hnaVal = 0;
                let ppnVal = 0;
                let hnaPpnVal = 0;
                let amountVal = 0;
                let percentageVal = 0;
                let baseAmountVal = 0;

                if (service.type === 'Medication') {
                    hnaVal = parseFloat(service.hna) || 0;
                    ppnVal = parseFloat(service.ppn_percentage) || 11;
                    hnaPpnVal = Math.round(hnaVal * (1 + ppnVal / 100));
                    amountVal = hnaPpnVal;
                } else if (service.type === 'MedicalService') {
                    percentageVal = parseFloat(service.percentage) || 70;
                    baseAmountVal = (service.tariffs && service.tariffs[{{ $rc->id }}])
                        ? parseFloat(service.tariffs[{{ $rc->id }}])
                        : 0;
                    hnaVal = baseAmountVal; 
                    amountVal = Math.round(baseAmountVal * (percentageVal / 100));
                } else if (service.type === 'RoomTariffType') {
                    percentageVal = parseFloat(service.percentage) || 100;
                    baseAmountVal = (service.tariffs && service.tariffs[{{ $rc->id }}])
                        ? parseFloat(service.tariffs[{{ $rc->id }}])
                        : 0;
                    hnaVal = baseAmountVal;
                    amountVal = Math.round(baseAmountVal * (percentageVal / 100));
                }

                tariffs[{{ $rc->id }}] = {
                    amount: amountVal,
                    amount_formatted: this.formatCurrency(amountVal),
                    hna: hnaVal,
                    hna_formatted: this.formatCurrency(hnaVal),
                    ppn: ppnVal,
                    hna_ppn: hnaPpnVal,
                    percentage: percentageVal,
                    base_amount: baseAmountVal,
                    total: amountVal * 1
                };
            }
            @endforeach

            this.matrix.push({
                id: service.id,
                type: service.type,
                name: service.name,
                code: service.code || '-',
                qty: 1,
                is_new: true,
                deleted: false,
                tariffs: tariffs
            });

            this.calculateTotals();
            window.Toast && Toast.success('Layanan "' + service.name + '" berhasil ditambahkan.');
        },

        removeService(index) {
            let row = this.matrix[index];
            if (row.type === 'RoomTariffType' && !row.is_new) {
                window.Toast && Toast.error('Tarif kamar bawaan tidak dapat dihapus.');
                return;
            }
            if (confirm('Hapus layanan "' + row.name + '" dari simulasi?')) {
                row.deleted = true;
                Object.keys(row.tariffs).forEach(rcId => {
                    row.tariffs[rcId].total = 0;
                });
                this.calculateTotals();
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
            const hasItems = this.matrix.some(row => row.type === type && !row.deleted);
            if (!hasItems) return false;
            if (!this.searchQuery) return true;
            const q = this.searchQuery.toLowerCase();
            return this.matrix.some(row =>
                row.type === type &&
                !row.deleted &&
                ((row.name && row.name.toLowerCase().includes(q)) ||
                 (row.code && row.code.toLowerCase().includes(q)))
            );
        },

        saveDraft() {
            const draftData = {
                admin_fee_percentage: this.adminFeePercentage,
                items: this.matrix.map((row, idx) => ({
                    index: idx,
                    id: row.id,
                    type: row.type,
                    name: row.name,
                    code: row.code,
                    qty: row.qty,
                    is_new: row.is_new || false,
                    deleted: row.deleted || false,
                    tariffs: Object.fromEntries(
                        Object.entries(row.tariffs).map(([rcId, tariff]) => [
                            rcId,
                            {
                                amount: tariff.amount,
                                base_amount: tariff.base_amount || 0,
                                hna: tariff.hna || 0,
                                ppn: tariff.ppn || 0,
                                hna_ppn: tariff.hna_ppn || 0,
                                percentage: tariff.percentage || 0,
                                total: tariff.total
                            }
                        ])
                    )
                }))
            };

            const url = '{{ route("unit-cost.simulation.save-draft", [$organizationUnit->id, $diagnosis->id]) }}';
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(draftData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.Toast && Toast.success(data.message || 'Simulasi berhasil disimpan');
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    window.Toast && Toast.error(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.Toast && Toast.error('Gagal menyimpan simulasi');
            });
        }
    }));

    // ─────────────────────────────────────────────
    // addServiceModal — search AJAX modal
    // ─────────────────────────────────────────────
    Alpine.data('addServiceModal', () => ({
        open: false,
        searchQuery: '',
        results: [],
        loading: false,
        focusedIndex: -1,

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
                
                let currentMatrix = [];
                try {
                    currentMatrix = Alpine.$data(document.querySelector('[x-data="unitCostSimulationForm()"]')).matrix || [];
                } catch (err) {}

                const items = await response.json();
                this.results = items.map(service => {
                    service.is_added = currentMatrix.some(r => r.type === service.type && r.id === service.id && !r.deleted);
                    return service;
                });

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
                    if (el) el.scrollIntoView({ block: 'nearest' });
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
            window.dispatchEvent(new CustomEvent('service-added', {
                detail: service,
            }));
            this.closeModal();
        },
    }));
});
</script>
