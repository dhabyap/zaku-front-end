// resources/js/alpine-components/budgets.js
// Alpine components: budgetPage, recurringPage, changelogPage

export function registerBudgets(Alpine) {

    Alpine.data('budgetPage', () => ({
        budgets: [],
        categories: [],
        progressMap: {},
        loading: true,
        hasLoaded: false,
        error: '',
        requestInFlight: false,
        saving: false,
        editMode: false,
        editId: null,
        form: { category: '', amount: 0, period: 'monthly', start_date: '', end_date: '' },
        get totalBudget() { return this.budgets.reduce((s, b) => s + (b.amount || 0), 0); },
        get totalSpent() { return this.budgets.reduce((s, b) => s + (this.progressMap[b.id]?.spent || 0), 0); },
        get summaryPct() { return this.totalBudget > 0 ? Math.min(100, Math.round((this.totalSpent / this.totalBudget) * 100)) : 0; },
        get summaryStatusClass() {
            if (this.summaryPct >= 80) return 'risk';
            if (this.summaryPct >= 60) return 'watch';
            return 'safe';
        },
        init() { this.fetchBudgets(); this.fetchCategories(); },
        formatNumber(n) { if (!n && n !== 0) return '0'; return Number(n).toLocaleString('id-ID'); },
        getEmoji(cat) {
            const map = { 'MAKANAN':'🍜','FOOD':'🍜','TRANSPORTASI':'🚗','TRANSPORT':'🚗','TAGIHAN':'⚡','BILLS':'⚡','BELANJA':'🛍️','SHOPPING':'🛍️','GAJI':'💰','SALARY':'💰','FREELANCE':'💻','KESEHATAN':'💊','HEALTH':'💊' };
            return map[(cat||'').toUpperCase()] || '📄';
        },
        periodLabel(p) {
            return { daily: 'HARIAN', weekly: 'MINGGUAN', monthly: 'BULANAN' }[p] || p;
        },
        statusClass(status) {
            return { aman: 'safe', waspada: 'watch', boros: 'risk', melebihi: 'risk' }[status] || '';
        },
        openForm(budget = null) {
            if (budget) {
                this.editMode = true;
                this.editId = budget.id;
                this.form = {
                    category: budget.category?.name || '',
                    amount: budget.amount || 0,
                    period: budget.period || 'monthly',
                    start_date: budget.start_date || new Date().toISOString().slice(0, 10),
                    end_date: budget.end_date || '',
                };
            } else {
                this.editMode = false;
                this.editId = null;
                this.form = { category: '', amount: 0, period: 'monthly', start_date: new Date().toISOString().slice(0, 10), end_date: '' };
            }
            document.getElementById('m-budget-form')?.classList.add('open');
        },
        closeForm() {
            document.getElementById('m-budget-form')?.classList.remove('open');
            this.editMode = false;
            this.editId = null;
        },
        bgClose(e, id) { if (e.target === e.currentTarget) { document.getElementById(id)?.classList.remove('open'); } },
        async fetchBudgets() {
            if (this.requestInFlight) return;
            this.requestInFlight = true;
            this.loading = true;
            this.error = '';
            try {
                const res = await window.apiClient.get('/v1/budgets');
                const data = res.data.data;
                if (!Array.isArray(data)) throw new Error('Invalid budgets response');
                this.budgets = data;
                this.hasLoaded = true;
                await this.fetchProgress();
            } catch (e) {
                this.error = 'Budget belum dapat dimuat. Coba lagi.';
            } finally {
                this.loading = false;
                this.requestInFlight = false;
            }
        },
        retryBudgets() {
            if (this.requestInFlight) return;
            return this.fetchBudgets();
        },
        async fetchCategories() {
            try {
                const res = await window.apiClient.get('/v1/categories');
                this.categories = res.data.data || [];
            } catch { /* ignore */ }
        },
        async fetchProgress() {
            let hasFailure = false;
            for (const b of this.budgets) {
                try {
                    const res = await window.apiClient.get('/v1/budgets/' + b.id + '/progress');
                    this.progressMap[b.id] = res.data.data;
                } catch {
                    hasFailure = true;
                }
            }
            if (hasFailure) throw new Error('Unable to load budget progress');
        },
        async saveBudget() {
            if (!this.form.category) { window.utils.showToast('error', 'Pilih kategori dulu'); return; }
            if (!this.form.amount || this.form.amount <= 0) { window.utils.showToast('error', 'Nominal harus lebih dari 0'); return; }
            this.saving = true;
            try {
                if (this.editMode) {
                    await window.apiClient.put('/v1/budgets/' + this.editId, { amount: this.form.amount });
                    window.utils.showToast('success', 'Budget berhasil diperbarui');
                } else {
                    await window.apiClient.post('/v1/budgets', this.form);
                    window.utils.showToast('success', 'Budget berhasil ditambahkan');
                }
                this.closeForm();
                await this.fetchBudgets();
            } catch (e) {
                const msg = window.utils.parseApiError(e, 'Gagal menyimpan budget');
                window.utils.showToast('error', msg);
            } finally { this.saving = false; }
        },
        async confirmDelete(b) {
            const confirmed = await window.utils.showConfirm(
                'HAPUS BUDGET',
                'Yakin hapus budget ' + (b.category?.name || '') + '?',
                'HAPUS'
            );
            if (!confirmed) return;
            try {
                await window.apiClient.delete('/v1/budgets/' + b.id);
                window.utils.showToast('success', 'Budget dihapus');
                await this.fetchBudgets();
            } catch (e) {
                window.utils.showToast('error', 'Gagal menghapus budget');
            }
        },
    }));

    // ── Manual Input Page ──

    Alpine.data('recurringPage', () => ({
        items: [],
        categories: [],
        loading: true,
        saving: false,
        editMode: false,
        editId: null,
        form: { type: 'expense', description: '', amount_cents: 0, category_id: '', frequency: 'monthly', interval_value: 1, start_date: '', end_date: '' },
        async init() {
            this.form.start_date = new Date().toISOString().slice(0, 10);
            await Promise.all([this.fetchItems(), this.fetchCategories()]);
        },
        formatNumber(n) { if (n === undefined || n === null) return '0'; return Number(n).toLocaleString('id-ID'); },
        formatDate(d) { if (!d) return '—'; const dt = new Date(d); return isNaN(dt.getTime()) ? d : dt.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }); },
        getEmoji(cat) {
            const map = { 'MAKANAN': '🍜', 'FOOD': '🍜', 'TRANSPORTASI': '🚗', 'TRANSPORT': '🚗', 'TAGIHAN': '⚡', 'BILLS': '⚡', 'BELANJA': '🛍️', 'SHOPPING': '🛍️', 'GAJI': '💰', 'SALARY': '💰', 'INCOME': '💰', 'FREELANCE': '💻', 'KESEHATAN': '💊', 'HEALTH': '💊', 'HIBURAN': '🎮', 'ENTERTAINMENT': '🎮' };
            return map[(cat || '').toUpperCase()] || '📄';
        },
        frequencyLabel(r) {
            const freq = { daily: 'Harian', weekly: 'Mingguan', monthly: 'Bulanan', yearly: 'Tahunan' };
            const base = freq[r.frequency] || r.frequency;
            return (r.interval_value > 1 ? 'Setiap ' + r.interval_value + ' ' + base.toLowerCase() : base);
        },
        async fetchItems() {
            this.loading = true;
            try {
                const res = await window.apiClient.get('/v1/recurring-transactions');
                const data = res.data.data;
                this.items = Array.isArray(data) ? data : (data.data || []);
            } catch (e) {
                window.utils.showToast('error', 'Gagal memuat transaksi berulang');
            } finally {
                this.loading = false;
            }
        },
        async fetchCategories() {
            try {
                const res = await window.apiClient.get('/v1/categories');
                this.categories = res.data.data || [];
            } catch { /* ignore */ }
        },
        openForm(r = null) {
            if (r) {
                this.editMode = true;
                this.editId = r.id;
                this.form = {
                    type: r.type || 'expense',
                    description: r.description || '',
                    amount_cents: r.amount_cents || 0,
                    category_id: r.category?.id || '',
                    frequency: r.frequency || 'monthly',
                    interval_value: r.interval_value || 1,
                    start_date: r.start_date || new Date().toISOString().slice(0, 10),
                    end_date: r.end_date || '',
                };
            } else {
                this.editMode = false;
                this.editId = null;
                this.form = { type: 'expense', description: '', amount_cents: 0, category_id: '', frequency: 'monthly', interval_value: 1, start_date: new Date().toISOString().slice(0, 10), end_date: '' };
            }
            document.getElementById('m-recurring-form')?.classList.add('open');
        },
        closeForm() {
            document.getElementById('m-recurring-form')?.classList.remove('open');
            this.editMode = false;
            this.editId = null;
        },
        bgClose(e, id) { if (e.target === e.currentTarget) { document.getElementById(id)?.classList.remove('open'); } },
        async saveRecurring() {
            if (!this.form.description) { window.utils.showToast('error', 'Deskripsi wajib diisi'); return; }
            if (!this.form.amount_cents || this.form.amount_cents <= 0) { window.utils.showToast('error', 'Nominal harus lebih dari 0'); return; }
            if (!this.form.start_date) { window.utils.showToast('error', 'Tanggal mulai wajib diisi'); return; }
            this.saving = true;
            try {
                const payload = { ...this.form };
                if (!payload.category_id) delete payload.category_id;
                if (!payload.end_date) delete payload.end_date;
                if (this.editMode) {
                    await window.apiClient.put('/v1/recurring-transactions/' + this.editId, payload);
                    window.utils.showToast('success', 'Transaksi berulang diperbarui');
                } else {
                    await window.apiClient.post('/v1/recurring-transactions', payload);
                    window.utils.showToast('success', 'Transaksi berulang ditambahkan');
                }
                this.closeForm();
                await this.fetchItems();
            } catch (e) {
                const msg = window.utils.parseApiError(e, 'Gagal menyimpan transaksi berulang');
                window.utils.showToast('error', msg, true);
            } finally {
                this.saving = false;
            }
        },
        async toggleStatus(r) {
            const newStatus = r.status === 'active' ? 'paused' : 'active';
            try {
                await window.apiClient.put('/v1/recurring-transactions/' + r.id, { status: newStatus });
                r.status = newStatus;
                window.utils.showToast('success', newStatus === 'active' ? 'Transaksi diaktifkan' : 'Transaksi dijeda');
            } catch (e) {
                window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal mengubah status'));
            }
        },
        async confirmDelete(r) {
            const ok = await window.utils.confirmDialog({
                title: 'Hapus Transaksi Berulang?',
                message: '"' + (r.description || 'Tanpa deskripsi') + '" akan dihapus permanen.',
                okLabel: 'YA, HAPUS',
                danger: true,
            });
            if (!ok) return;
            try {
                await window.apiClient.delete('/v1/recurring-transactions/' + r.id);
                this.items = this.items.filter(i => i.id !== r.id);
                window.utils.showToast('success', 'Transaksi berulang dihapus');
            } catch (e) {
                window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal menghapus'));
            }
        },
    }));

    // ── Insights Page ──

    Alpine.data('changelogPage', () => ({
        logs: [],
        loading: true,
        loadingMore: false,
        currentPage: 1,
        lastPage: 1,
        total: 0,
        async init() { await this.fetchLogs(); },
        formatDate(d) {
            if (!d) return '';
            return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        },
        async fetchLogs() {
            this.loading = true;
            try {
                const res = await window.apiClient.get('/v1/changelogs?per_page=10&page=1');
                const d = res.data.data;
                this.logs = d.items || [];
                this.currentPage = d.pagination.current_page;
                this.lastPage = d.pagination.last_page;
                this.total = d.pagination.total;
            } catch (e) {
                window.utils.showToast('error', 'Gagal memuat changelog');
            } finally {
                this.loading = false;
            }
        },
        async loadMore() {
            if (this.loadingMore || this.currentPage >= this.lastPage) return;
            this.loadingMore = true;
            try {
                const res = await window.apiClient.get(`/v1/changelogs?per_page=10&page=${this.currentPage + 1}`);
                const d = res.data.data;
                this.logs = [...this.logs, ...(d.items || [])];
                this.currentPage = d.pagination.current_page;
                this.lastPage = d.pagination.last_page;
            } catch (e) {
                window.utils.showToast('error', 'Gagal memuat data lainnya');
            } finally {
                this.loadingMore = false;
            }
        }
    }));

}
