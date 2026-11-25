// resources/js/alpine-components/transactions.js
// Alpine components: transactionList, transactionDetail, manualInputPage, categoriesPage, calendarPage

export function registerTransactions(Alpine) {

    Alpine.data('transactionList', () => ({
        transactions: [],
        loading: true,
        loadingMore: false,
        hasLoaded: false,
        error: '',
        retryPage: 1,
        retryAppend: false,
        transactionRequestId: 0,
        filter: 'all',
        categories: [],
        currentPage: 1,
        lastPage: 1,
        hasMore: false,
        total: 0,
        searchQuery: '',
        dateFrom: '',
        dateTo: '',
        sortKey: 'date',
        sortAsc: false,
        activeTrx: null,
        detailTarget: null,
        detailOpen: false,
        detailLoading: false,
        detailError: '',
        detailRequestId: 0,
        detailOpener: null,
        previousBodyOverflow: '',
        totalIncome: 0,
        totalExpense: 0,
        _searchTimer: null,
        async init() {
            await this.fetchTransactions();
            this.extractCategories();
        },
        formatNumber(n) { if (n === undefined || n === null) return '0'; return Number(n).toLocaleString('id-ID'); },
        rp(n) { return 'Rp ' + this.formatNumber(n); },
        formatTransactionDate(trx) {
            const value = this.getTransactionDate(trx);
            if (!value) return 'Tidak tersedia';
            const date = new Date(value);
            if (isNaN(date.getTime())) return 'Tidak tersedia';
            return date.toLocaleString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        },
        getTransactionDate(trx) { return trx.transaction_date || trx.date || trx.created_at || trx.updated_at || null; },
        async openDetail(trx, opener) {
            this.detailRequestId += 1;
            const requestId = this.detailRequestId;
            this.detailTarget = trx;
            this.activeTrx = null;
            this.detailError = '';
            this.detailLoading = true;
            if (!this.detailOpen) {
                this.detailOpener = opener || document.activeElement;
                this.previousBodyOverflow = document.body.style.overflow;
                document.body.style.overflow = 'hidden';
            }
            this.detailOpen = true;
            this.$nextTick(() => document.getElementById('transaction-detail-close')?.focus());

            try {
                const res = await window.apiClient.get('/v1/transactions/' + encodeURIComponent(trx.id));
                if (requestId !== this.detailRequestId) return;
                const detail = res.data.data;
                this.activeTrx = {
                    ...trx,
                    ...detail,
                    category_name: detail.category_name || detail.category || trx.category_name,
                    category_icon: detail.category_icon || trx.category_icon,
                    transaction_date: detail.transaction_date || trx.transaction_date || trx.date,
                    date_formatted: detail.date_formatted || trx.date_formatted,
                };
            } catch (e) {
                if (requestId !== this.detailRequestId) return;
                this.detailError = window.utils.parseApiError(e, 'Gagal memuat detail transaksi. Coba lagi.');
            } finally {
                if (requestId === this.detailRequestId) this.detailLoading = false;
            }
        },
        closeDetail() {
            if (!this.detailOpen) return;
            this.detailRequestId += 1;
            this.detailOpen = false;
            this.activeTrx = null;
            this.detailTarget = null;
            this.detailLoading = false;
            this.detailError = '';
            document.body.style.overflow = this.previousBodyOverflow;
            this.$nextTick(() => {
                if (this.detailOpener?.isConnected) this.detailOpener.focus();
                this.detailOpener = null;
            });
        },
        retryDetail() {
            if (this.detailTarget) this.openDetail(this.detailTarget, this.detailOpener);
        },
        getEmoji(cat) { const map = { 'MAKANAN': '🍜', 'FOOD': '🍜', 'TRANSPORTASI': '🚗', 'TRANSPORT': '🚗', 'TAGIHAN': '⚡', 'BILLS': '⚡', 'BELANJA': '🛍️', 'SHOPPING': '🛍️', 'GAJI': '💰', 'SALARY': '💰', 'INCOME': '💰', 'FREELANCE': '💻', 'KESEHATAN': '💊', 'HEALTH': '💊', 'HIBURAN': '🎮', 'ENTERTAINMENT': '🎮' }; return map[cat?.toUpperCase()] || '📄'; },
        extractCategories() { const set = new Set(); this.transactions.forEach(t => { if (t.category_name) set.add(t.category_name.toUpperCase()); }); this.categories = Array.from(set); },
        async fetchTransactions(page = 1, append = false, isLoadMore = false) {
            if (isLoadMore && this.loadingMore) return;
            if (isLoadMore) this.loadingMore = true;
            else {
                this.loading = true;
                this.loadingMore = false;
            }
            this.error = '';
            this.retryPage = page;
            this.retryAppend = append;
            const requestId = ++this.transactionRequestId;
            try {
                const params = { limit: 20, page };
                if (this.searchQuery) params.q = this.searchQuery;
                if (this.filter !== 'all') {
                    if (this.filter === 'income') params.filter = 'PEMASUKAN';
                    else if (this.filter === 'expense') params.filter = 'PENGELUARAN';
                    else params.filter = this.filter;
                }
                if (this.dateFrom) params.date_from = this.dateFrom;
                if (this.dateTo) params.date_to = this.dateTo;
                const sortBy = this.sortKey === 'date' ? 'transaction_date' : this.sortKey;
                params.sort_by = sortBy;
                params.sort_order = this.sortAsc ? 'asc' : 'desc';

                const res = await window.apiClient.get('/v1/transactions', { params });
                if (requestId !== this.transactionRequestId) return;
                const payload = res.data.data || {};
                if (!Array.isArray(payload) && !Array.isArray(payload.groups)) {
                    throw new Error('Invalid transactions response');
                }
                const groups = Array.isArray(payload) ? payload : (payload.groups || []);
                const meta = payload.meta || {};

                this.totalIncome = meta.total_income || 0;
                this.totalExpense = meta.total_expense || 0;

                let flatTx = [];
                groups.forEach(group => {
                    if (Array.isArray(group.transactions)) {
                        const transactions = group.transactions.map(trx => ({
                            ...trx,
                            month_label: trx.month_label || group.month_label
                        }));
                        flatTx = flatTx.concat(transactions);
                    }
                });

                if (append) {
                    this.transactions = [...this.transactions, ...flatTx];
                } else {
                    this.transactions = flatTx;
                }

                this.currentPage = meta.page || 1;
                this.lastPage = Math.ceil((meta.total || 0) / (meta.limit || 20));
                this.hasMore = meta.has_more || false;
                this.total = meta.total || 0;
                this.hasLoaded = true;
            } catch (e) {
                if (requestId !== this.transactionRequestId) return;
                this.error = 'Transaksi belum dapat dimuat. Coba lagi.';
            } finally {
                if (requestId === this.transactionRequestId) {
                    if (isLoadMore) this.loadingMore = false;
                    else this.loading = false;
                }
            }
        },
        retryTransactions() {
            if (this.loading || this.loadingMore) return;
            return this.fetchTransactions(this.retryPage, this.retryAppend, this.retryAppend);
        },
        async loadPage(p) {
            if (p < 1 || (this.lastPage && p > this.lastPage)) return;
            await this.fetchTransactions(p, false, false);
            document.getElementById('tx-body').scrollTop = 0;
        },
        async loadMore() {
            if (!this.hasMore || this.loading || this.loadingMore) return;
            const body = document.getElementById('tx-body');
            const scrollPos = body.scrollTop; 
            // Tidak perlu loading spinner untuk loadMore, hanya append
            await this.fetchTransactions(this.currentPage + 1, true, true); 
            this.$nextTick(() => {
                body.scrollTop = scrollPos;
            });
        },
        paginationNumbers() {
            const total = this.lastPage;
            const current = this.currentPage;
            const delta = 1;
            const range = [];
            const rangeWithDots = [];
            let l;

            range.push(1);
            for (let i = current - delta; i <= current + delta; i++) {
                if (i < total && i > 1) range.push(i);
            }
            range.push(total);

            for (let i of range) {
                if (l) {
                    if (i - l === 2) rangeWithDots.push(l + 1);
                    else if (i - l !== 1) rangeWithDots.push('...');
                }
                rangeWithDots.push(i);
                l = i;
            }
            return rangeWithDots;
        },
        setFilter(f, el) {
            this.filter = f;
            document.querySelectorAll('.fpill').forEach(p => p.classList.remove('on'));
            if (el) el.classList.add('on');
            this.fetchTransactions(1);
        },
        setSort(key, btn) {
            if (this.sortKey === key) this.sortAsc = !this.sortAsc;
            else { this.sortKey = key; this.sortAsc = false; }
            document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            btn.textContent = key === 'date' ? (this.sortAsc ? '↑ TERLAMA' : '↓ TERBARU') : (this.sortAsc ? '↑ TERKECIL' : '↓ TERBESAR');
            this.fetchTransactions(1);
        },
        doSearch(v) {
            clearTimeout(this._searchTimer);
            this._searchTimer = setTimeout(() => {
                this.searchQuery = v;
                this.fetchTransactions(1);
            }, 400);
        },
        setDateRange(from, to) {
            this.dateFrom = from;
            this.dateTo = to;
            this.fetchTransactions(1);
        },
        async exportCsv() {
            try {
                const params = new URLSearchParams();
                if (this.dateFrom) params.append('start_date', this.dateFrom);
                if (this.dateTo) params.append('end_date', this.dateTo);
                if (this.filter === 'income' || this.filter === 'expense') {
                    params.append('type', this.filter);
                }

                const url = '/v1/export/transactions-csv?' + params.toString();
                const res = await window.apiClient.get(url, { responseType: 'blob' });
                const blob = new Blob([res.data], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', 'zaku-transactions-' + new Date().toISOString().slice(0, 10) + '.csv');
                document.body.appendChild(link);
                link.click();
                link.remove();
                if (window.utils && window.utils.showToast) {
                    window.utils.showToast('File CSV berhasil diunduh! 📄', 'success');
                }
            } catch (err) {
                console.error('Export CSV error:', err);
                if (window.utils && window.utils.showToast) {
                    window.utils.showToast('Gagal mengunduh CSV. Coba lagi.', 'error');
                }
            }
        },
        filtered() {
            return this.transactions;
        },
        grouped() {
            const groups = {};
            const data = this.filtered();
            data.forEach(t => {
                const dateValue = this.getTransactionDate(t);
                const date = dateValue ? new Date(dateValue) : null;
                const key = t.month_label || (date && !isNaN(date.getTime()) ? date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }).toUpperCase() : 'TANPA TANGGAL');
                if (!groups[key]) groups[key] = [];
                groups[key].push(t);
            });
            return groups;
        }
    }));

    // ── Profile Page ──

    Alpine.data('transactionDetail', (id) => ({
        id: id,
        transaction: null,
        loading: true,
        editing: false,
        editCategory: '',
        editDescription: '',
        editAmount: '',
        editType: '',
        editDate: '',
        categories: [],
        saving: false,
        async init() {
            this.fetchDetail();
            this.fetchCategories();
        },
        formatNumber(n) {
            if (!n) return '0';
            return Number(n).toLocaleString('id-ID');
        },
        formatDate(d) {
            if (!d) return '';
            const date = new Date(d);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        },
        formatDateInput(d) {
            if (!d) return '';
            const date = new Date(d);
            return date.toISOString().slice(0, 10);
        },
        async fetchDetail() {
            try {
                const res = await window.apiClient.get('/v1/transactions/' + this.id);
                this.transaction = res.data.data;
            } catch (e) {
                window.utils.handleApiError(e, 'Gagal memuat detail transaksi');
            } finally {
                this.loading = false;
            }
        },
        async fetchCategories() {
            try {
                const res = await window.apiClient.get('/v1/categories');
                const data = res.data;
                if (data.data && Array.isArray(data.data) && data.data.length > 0) {
                    this.categories = data.data;
                }
                if (this.categories.length === 0) {
                    this.categories = [
                        { name: 'MAKANAN', icon: '🍜' }, { name: 'TRANSPORTASI', icon: '🚗' },
                        { name: 'TAGIHAN', icon: '⚡' }, { name: 'BELANJA', icon: '🛍️' },
                        { name: 'GAJI', icon: '💰' }, { name: 'KESEHATAN', icon: '💊' },
                        { name: 'FREELANCE', icon: '💻' }, { name: 'LAINNYA', icon: '📌' }
                    ];
                }
            } catch { /* fallback categories below */ }
        },
        startEdit() {
            this.editCategory = this.transaction?.category_name || this.transaction?.category || 'LAINNYA';
            this.editDescription = this.transaction?.description || '';
            this.editAmount = this.transaction?.amount || '';
            this.editType = this.transaction?.type || 'expense';
            this.editDate = this.formatDateInput(this.transaction?.transaction_date || this.transaction?.created_at);
            this.editing = true;
        },
        async saveEdit() {
            if (!this.editCategory || this.saving) return;
            this.saving = true;
            try {
                const payload = {
                    category: this.editCategory,
                    description: this.editDescription,
                    amount: Number(this.editAmount),
                    type: this.editType,
                    transaction_date: this.editDate,
                };
                const res = await window.apiClient.put('/v1/transactions/' + this.id, payload);
                const data = res.data.data;
                this.transaction.category_name = data.category_name;
                this.transaction.category = data.category_name;
                this.transaction.category_icon = data.category_icon;
                this.transaction.description = this.editDescription;
                this.transaction.amount = Number(this.editAmount);
                this.transaction.type = this.editType;
                this.editing = false;
                window.utils.showToast('success', 'Transaksi berhasil diperbarui!');
            } catch (e) {
                window.utils.handleApiError(e, 'Gagal memperbarui transaksi');
            } finally {
                this.saving = false;
            }
        },
        async deleteTransaction() {
            const ok = await window.utils.confirmDialog({
                title: 'Hapus Transaksi?',
                message: 'Semua data transaksi ini akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.',
                okLabel: 'YA, HAPUS',
                danger: true
            });
            if (!ok) return;
            try {
                await window.apiClient.delete('/v1/transactions/' + this.id);
                window.utils.showToast('success', 'Transaksi berhasil dihapus!');
                window.location.href = '/transactions';
            } catch (e) {
                window.utils.handleApiError(e, 'Gagal menghapus transaksi. Coba lagi!');
            }
        }
    }));

    // ── Transactions List ──

    Alpine.data('manualInputPage', () => ({
        txType: 'income',
        selectedCat: '',
        amount: 0,
        description: '',
        dateRaw: '',
        dateDisplay: '',
        notes: '',
        loading: false,
        saved: false,
        allCategories: [],
        savedData: { icon: '💰', description: '', amount: 0, type: 'income' },
        showQuickAdd: false,
        quickAddSaving: false,
        quickAddForm: { name: '', icon: '📌', type: 'expense' },
        quickAddIcons: ['☕', '🚗', '🛒', '🧾', '🎮', '💰', '📌', '🏠', '💊', '🎓', '✈️', '🐱', '👶', '💼', '🎁', '🔧', '📱', '🎬', '🏋️', '🎵', '📚', '🍕', '🛍️'],
        async init() {
            const today = new Date();
            this.dateRaw = today.toISOString().split('T')[0];
            this.dateDisplay = this.formatDateID(this.dateRaw);
            await this.fetchCategories();
        },
        async fetchCategories() {
            try {
                const res = await window.apiClient.get('/v1/categories');
                this.allCategories = res.data.data || [];
            } catch {
                this.allCategories = [
                    { name: 'MAKANAN', icon: '☕', type: 'expense' },
                    { name: 'TRANSPORT', icon: '🚗', type: 'expense' },
                    { name: 'BELANJA', icon: '🛒', type: 'expense' },
                    { name: 'TAGIHAN', icon: '🧾', type: 'expense' },
                    { name: 'HIBURAN', icon: '🎮', type: 'expense' },
                    { name: 'GAJI', icon: '💰', type: 'income' },
                    { name: 'LAINNYA', icon: '📌', type: 'both' },
                ];
            }
        },
        get categories() {
            return this.allCategories.filter(c =>
                c.type === this.txType || c.type === 'both'
            );
        },
        formatNumber(n) { return Number(n || 0).toLocaleString('id-ID'); },
        setType(t) { this.txType = t; this.selectedCat = ''; },
        addAmount(n) { this.amount = (Number(this.amount) || 0) + n; },
        getCatLabel() {
            const cat = this.allCategories.find(c => c.name === this.selectedCat);
            return cat ? cat.icon + ' ' + cat.name : '—';
        },
        formatDateID(d) {
            if (!d) return '—';
            try {
                const dt = new Date(d + 'T00:00:00');
                if (isNaN(dt.getTime())) return d;
                return dt.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            } catch { return d; }
        },
        parseDateInput(val) {
            const cleaned = val.replace(/[/]/g, '-');
            const parts = cleaned.split('-');
            if (parts.length === 3) {
                let [d, m, y] = parts;
                if (y.length === 2) y = '20' + y;
                if (m.length === 1) m = '0' + m;
                if (d.length === 1) d = '0' + d;
                const iso = y + '-' + m + '-' + d;
                if (!isNaN(new Date(iso + 'T00:00:00').getTime())) {
                    this.dateRaw = iso;
                    this.dateDisplay = this.formatDateID(iso);
                    return;
                }
            }
            this.dateRaw = '';
            this.dateDisplay = val;
        },
        setToday() {
            const today = new Date();
            this.dateRaw = today.toISOString().split('T')[0];
            this.dateDisplay = this.formatDateID(this.dateRaw);
        },
        openQuickAdd() {
            this.quickAddForm = { name: '', icon: '📌', type: this.txType };
            this.showQuickAdd = true;
        },
        async quickAddCategory() {
            if (!this.quickAddForm.name) return;
            this.quickAddSaving = true;
            try {
                const res = await window.apiClient.post('/v1/categories', this.quickAddForm);
                const newCat = res.data.data;
                this.allCategories.push(newCat);
                this.selectedCat = newCat.name;
                this.showQuickAdd = false;
                window.utils.showToast('success', 'Kategori "' + newCat.name + '" berhasil ditambahkan');
            } catch (e) {
                const msg = window.utils.parseApiError(e, 'Gagal menambahkan kategori');
                window.utils.showToast('error', msg, true);
            } finally {
                this.quickAddSaving = false;
            }
        },
        resetForm() {
            this.amount = 0;
            this.description = '';
            this.notes = '';
            this.selectedCat = '';
            this.txType = 'income';
            const today = new Date();
            this.dateRaw = today.toISOString().split('T')[0];
            this.dateDisplay = this.formatDateID(this.dateRaw);
        },
        async submitForm() {
            if (this.amount <= 0) { window.utils.showToast('error', 'Masukkan nominal terlebih dahulu!'); return; }
            if (!this.description) { window.utils.showToast('error', 'Isi deskripsi transaksi!'); return; }
            if (!this.selectedCat) { window.utils.showToast('error', 'Pilih kategori transaksi!'); return; }
            this.loading = true;
            try {
                const txDate = this.dateRaw || new Date().toISOString().split('T')[0];
                await window.apiClient.post('/v1/transactions', {
                    type: this.txType,
                    amount: this.amount,
                    description: this.description,
                    category: this.selectedCat,
                    transaction_date: txDate,
                });
                const cat = this.allCategories.find(c => c.name === this.selectedCat);
                this.savedData = {
                    icon: cat ? cat.icon : '💰',
                    description: this.description,
                    amount: this.amount,
                    type: this.txType,
                };
                this.saved = true;
            } catch (e) {
                window.utils.handleApiError(e, 'Gagal menyimpan transaksi');
            } finally { this.loading = false; }
        }
    }));

    // ── Categories Management Page ──

    Alpine.data('categoriesPage', () => ({
        categories: [],
        loading: false,
        filter: 'all',
        showModal: false,
        showDeleteModal: false,
        editingCat: null,
        deletingCat: null,
        saving: false,
        deleting: false,
        keywordsInput: '',
        form: { name: '', icon: '📌', type: 'expense', keywords: [] },
        iconOptions: ['☕', '🚗', '🛒', '🧾', '🎮', '💰', '📌', '🏠', '💊', '🎓', '✈️', '🐱', '👶', '💼', '🎁', '🔧', '📱', '🎬', '🏋️', '🎵', '📚', '✈️', '🍕', '🛍️', '🐱'],
        get filteredCategories() {
            if (this.filter === 'all') return this.categories;
            return this.categories.filter(c => c.type === this.filter || c.type === 'both');
        },
        async init() {
            await this.fetchCategories();
        },
        async fetchCategories() {
            this.loading = true;
            try {
                const res = await window.apiClient.get('/v1/categories');
                this.categories = res.data.data || [];
            } catch {
                window.utils.showToast('error', 'Gagal memuat kategori');
            } finally {
                this.loading = false;
            }
        },
        openAddModal() {
            this.editingCat = null;
            this.keywordsInput = '';
            this.form = { name: '', icon: '📌', type: 'expense', keywords: [] };
            this.showModal = true;
        },
        openEditModal(cat) {
            this.editingCat = cat;
            this.keywordsInput = (cat.keywords || []).join(', ');
            this.form = { name: cat.name, icon: cat.icon, type: cat.type, keywords: cat.keywords || [] };
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.editingCat = null;
        },
        async saveCategory() {
            if (!this.form.name) return;
            this.saving = true;
            try {
                if (this.editingCat) {
                    const res = await window.apiClient.put('/v1/categories/' + this.editingCat.id, this.form);
                    const idx = this.categories.findIndex(c => c.id === this.editingCat.id);
                    if (idx !== -1) this.categories[idx] = res.data.data;
                    window.utils.showToast('success', 'Kategori berhasil diperbarui');
                } else {
                    const res = await window.apiClient.post('/v1/categories', this.form);
                    this.categories.push(res.data.data);
                    window.utils.showToast('success', 'Kategori berhasil ditambahkan');
                }
                this.closeModal();
            } catch (e) {
                const msg = window.utils.parseApiError(e, 'Gagal menyimpan kategori');
                window.utils.showToast('error', msg, true);
            } finally {
                this.saving = false;
            }
        },
        confirmDelete(cat) {
            this.deletingCat = cat;
            this.showDeleteModal = true;
        },
        async deleteCategory() {
            if (!this.deletingCat) return;
            this.deleting = true;
            try {
                await window.apiClient.delete('/v1/categories/' + this.deletingCat.id);
                this.categories = this.categories.filter(c => c.id !== this.deletingCat.id);
                window.utils.showToast('success', 'Kategori berhasil dihapus');
                this.showDeleteModal = false;
                this.deletingCat = null;
            } catch (e) {
                const msg = window.utils.parseApiError(e, 'Gagal menghapus kategori');
                window.utils.showToast('error', msg, true);
            } finally {
                this.deleting = false;
            }
        },
    }));

    // ── Recurring Transactions Page ──

    Alpine.data('calendarPage', () => ({
        currentYear: new Date().getFullYear(),
        currentMonth: new Date().getMonth(),
        transactions: [],
        loading: true,
        selectedDate: null,
        selectedTransactions: [],
        daySummary: {},

        init() {
            this.fetchTransactions();
        },

        formatNumber(n) {
            if (n === undefined || n === null) return '0';
            return Number(n).toLocaleString('id-ID');
        },

        getMonthName(month) {
            const months = [
                'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI',
                'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'
            ];
            return months[month];
        },

        getDayName(dayIndex) {
            const days = ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'];
            return days[dayIndex];
        },

        getDaysInMonth(year, month) {
            return new Date(year, month + 1, 0).getDate();
        },

        getFirstDayOfMonth(year, month) {
            return new Date(year, month, 1).getDay();
        },

        generateCalendarDays() {
            const daysInMonth = this.getDaysInMonth(this.currentYear, this.currentMonth);
            const firstDay = this.getFirstDayOfMonth(this.currentYear, this.currentMonth);
            const days = [];

            for (let i = 0; i < firstDay; i++) {
                days.push({ day: null, dateKey: null });
            }

            for (let d = 1; d <= daysInMonth; d++) {
                const dateKey = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                days.push({ day: d, dateKey: dateKey });
            }

            return days;
        },

        formatDate(dateKey) {
            if (!dateKey) return '';
            const [y, m, d] = dateKey.split('-');
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${d} ${monthNames[parseInt(m) - 1]} ${y}`;
        },

        async fetchTransactions() {
            this.loading = true;
            try {
                const startDate = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-01`;
                const lastDay = this.getDaysInMonth(this.currentYear, this.currentMonth);
                const endDate = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;

                const res = await window.apiClient.get('/v1/transactions', {
                    params: {
                        date_from: startDate,
                        date_to: endDate,
                        limit: 500,
                    }
                });

                const payload = res.data.data || {};
                const groups = Array.isArray(payload) ? payload : (payload.groups || []);

                let allTx = [];
                groups.forEach(group => {
                    if (Array.isArray(group.transactions)) {
                        allTx = allTx.concat(group.transactions);
                    }
                });

                this.transactions = allTx;
                this.calculateDaySummary();
            } catch (e) {
                console.error('Fetch transactions error:', e);
                window.utils.showToast('error', 'Gagal memuat data transaksi.');
            } finally {
                this.loading = false;
            }
        },

        calculateDaySummary() {
            const summary = {};
            this.transactions.forEach(tx => {
                const date = tx.transaction_date || tx.date || tx.created_at;
                if (!date) return;
                const dateKey = date.substring(0, 10);

                if (!summary[dateKey]) {
                    summary[dateKey] = { income: 0, expense: 0, count: 0 };
                }
                summary[dateKey].count++;

                if (tx.type === 'income') {
                    summary[dateKey].income += Number(tx.amount) || 0;
                } else if (tx.type === 'expense') {
                    summary[dateKey].expense += Number(tx.amount) || 0;
                }
            });
            this.daySummary = summary;
        },

        prevMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
            this.selectedDate = null;
            this.selectedTransactions = [];
            this.fetchTransactions();
        },

        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
            this.selectedDate = null;
            this.selectedTransactions = [];
            this.fetchTransactions();
        },

        goToToday() {
            this.currentYear = new Date().getFullYear();
            this.currentMonth = new Date().getMonth();
            this.selectedDate = null;
            this.selectedTransactions = [];
            this.fetchTransactions();
        },

        selectDate(dateKey) {
            if (!dateKey) return;
            this.selectedDate = dateKey;

            this.selectedTransactions = this.transactions.filter(tx => {
                const date = tx.transaction_date || tx.date || tx.created_at;
                if (!date) return false;
                return date.substring(0, 10) === dateKey;
            });
        },

        closeDetail() {
            this.selectedDate = null;
            this.selectedTransactions = [];
        },

        isToday(dateKey) {
            if (!dateKey) return false;
            const today = new Date();
            const todayKey = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            return dateKey === todayKey;
        },

        getEmoji(cat) {
            const map = {
                'MAKANAN': '🍜', 'FOOD': '🍜',
                'TRANSPORTASI': '🚗', 'TRANSPORT': '🚗',
                'TAGIHAN': '⚡', 'BILLS': '⚡',
                'BELANJA': '🛍️', 'SHOPPING': '🛍️',
                'GAJI': '💰', 'SALARY': '💰',
                'FREELANCE': '💻',
                'KESEHATAN': '💊', 'HEALTH': '💊',
                'HIBURAN': '🎮', 'ENTERTAINMENT': '🎮',
            };
            return map[cat?.toUpperCase()] || '📄';
        },

        txCount(dateKey) {
            const s = this.daySummary[dateKey];
            if (!s || s.count === 0) return '';
            return s.count + ' tx';
        },
    }));

}
