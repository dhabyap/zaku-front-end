    // ── Manual Input Page ──
    Alpine.data('manualInputPage', () => ({
        txType: 'income',
        selectedCat: '',
        isRecurring: false,
        amount: 0,
        description: '',
        date: new Date().toISOString().split('T')[0],
        notes: '',
        loading: false,
        CATS: {
            income: [
                { key:'gaji',     icon:'💰', label:'GAJI' },
                { key:'freelance',icon:'💻', label:'FREELANCE' },
                { key:'investasi',icon:'📈', label:'INVESTASI' },
                { key:'bonus',    icon:'🎁', label:'BONUS' },
                { key:'transfer', icon:'🏦', label:'TRANSFER' },
                { key:'lainnya',  icon:'📦', label:'LAINNYA' },
            ],
            expense: [
                { key:'makanan',  icon:'🍜', label:'MAKANAN' },
                { key:'transport',icon:'🚗', label:'TRANSPORT' },
                { key:'belanja',  icon:'🛍️',label:'BELANJA' },
                { key:'tagihan',  icon:'⚡', label:'TAGIHAN' },
                { key:'hiburan',  icon:'🎮', label:'HIBURAN' },
                { key:'kesehatan',icon:'💊', label:'KESEHATAN' },
                { key:'pendidikan',icon:'📚',label:'PENDIDIKAN' },
                { key:'lainnya',  icon:'📦', label:'LAINNYA' },
            ]
        },
        formatNumber(n) { return Number(n).toLocaleString('id-ID'); },
        setType(t) { this.txType = t; this.selectedCat = ''; },
        addAmount(n) { this.amount = (Number(this.amount) || 0) + n; },
        async submitForm() {
            if (this.amount <= 0 || !this.description || !this.selectedCat) {
                window.utils.showToast('error', 'Lengkapi data transaksi!');
                return;
            }
            this.loading = true;
            try {
                await window.apiClient.post('/v1/transactions', {
                    type: this.txType,
                    amount: this.amount,
                    description: this.description,
                    category: this.selectedCat,
                    transaction_date: this.date,
                });
                window.utils.showToast('success', 'Transaksi berhasil disimpan!');
                window.location.href = '/transactions';
            } catch (e) {
                window.utils.handleApiError(e, 'Gagal menyimpan transaksi');
            } finally { this.loading = false; }
        }
    }));
