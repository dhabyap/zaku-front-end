// resources/js/alpine-components/dashboard.js
// Alpine components: dashboardHome, profilePage, settingsPage, notificationInbox, insightsPage

export function registerDashboard(Alpine) {

    Alpine.data('dashboardHome', () => ({
        balance: 0, income: 0, expense: 0,
        transactions: [], categories: [],
        maxCategory: null, maxCategoryAmount: 0, maxCategoryPct: 0,
        insightText: '', insightDetail: '', insightType: 'info',
        showAmount: (() => {
            try {
                return localStorage.getItem('zaku_hide_amount') !== 'false';
            } catch {
                return true;
            }
        })(),
        isDark: (() => {
            try {
                return localStorage.getItem('zaku_theme') === 'dark';
            } catch {
                return false;
            }
        })(),
        toggleTheme() {
            this.isDark = !this.isDark;
            const nextTheme = this.isDark ? 'dark' : 'light';
            localStorage.setItem('zaku_theme', nextTheme);
            if (this.isDark) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.removeAttribute('data-theme');
            }
            if (window.utils && window.utils.showToast) {
                window.utils.showToast('Tema diubah ke ' + (this.isDark ? 'Cyber Dark 🌙' : 'Light ☀️'), 'success');
            }
        },
        budget: { limit: 0, used: 0, left: 0, usedPct: 0, score: 0, status: 'Budget belum diatur', statusClass: 'risk', insight: '' },
        catBudgets: [],
        budgetHealthLoading: true,
        budgetHealthLoaded: false,
        budgetHealthError: '',
        budgetHealthInFlight: false,
        loading: { balance: true, transactions: true, categories: true, budget: true },
        async init() {
            this.fetchDashboard();
            this.fetchCategoryBudgets();
        },
        formatNumber(n) { if (!n) return '0'; return Number(n).toLocaleString('id-ID'); },
        formatDate(d) { if (!d) return ''; return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }); },
        toNumber(v) { const n = Number(v); return Number.isFinite(n) ? n : 0; },
        clamp(v, min, max) { return Math.min(Math.max(v, min), max); },
        readBudgetLimit(data) {
            const user = window.auth.getUser() || {};
            return this.toNumber(data.monthly_budget || data.budget_limit || data.budget?.limit
                || data.budget?.monthly_budget || user.monthly_budget || user.budget?.limit);
        },
        updateBudget(data = {}) {
            const limit = this.readBudgetLimit(data);
            const used = this.toNumber(data.budget_used || data.used_budget || data.budget?.used
                || data.budget?.spent || data.total_expense || this.expense);
            const left = Math.max(0, this.toNumber(data.remaining_budget || data.budget_left
                || data.budget?.left || data.budget?.remaining || (limit - used)));
            if (limit <= 0) {
                this.budget = { limit: 0, used, left: 0, usedPct: 0, score: 0, status: 'Budget bulanan umum belum diatur', statusClass: 'risk', insight: 'Atur budget bulanan agar Zaku bisa membaca kondisi pengeluaranmu.' };
                return;
            }
            const usedPct = this.clamp(Math.round((used / limit) * 100), 0, 100);
            const score = this.clamp(100 - usedPct, 0, 100);
            let status = 'RISIKO BOROS', statusClass = 'risk';
            if (score >= 80) { status = 'AMAN'; statusClass = 'safe'; }
            else if (score >= 50) { status = 'PERLU DIJAGA', statusClass = 'watch'; }
            const insight = score >= 80
                ? 'Budget masih aman. Pertahankan ritme pengeluaran bulan ini.'
                : score >= 50 ? 'Pengeluaran mulai mendekati batas. Jaga transaksi besar berikutnya.'
                : 'Budget berisiko habis. Prioritaskan kebutuhan utama dulu.';
            this.budget = { limit, used, left, usedPct, score, status, statusClass, insight };
        },
        categoryBudgetStatusClass(pct) {
            if (pct >= 80) return 'risk';
            if (pct >= 60) return 'watch';
            return 'safe';
        },
        categoryBudgetStatusLabel(pct) {
            if (pct >= 100) return 'MELEBIHI';
            if (pct >= 80) return 'BOROS';
            if (pct >= 60) return 'WASPADA';
            return 'AMAN';
        },
        updateInsight() {
            this.insightType = 'info';
            this.insightText = 'Belum ada insight. Mulai catat transaksimu agar Zaku bisa memberikan insight.';
            this.insightDetail = '';

            if (this.catBudgets.length > 0) {
                const pct = this.catBudgetPct;
                if (pct >= 80) {
                    this.insightType = 'warning';
                    this.insightText = 'Total pengeluaran budget kategori ';
                    this.insightText += pct >= 100 ? 'sudah melebihi batas.' : 'hampir habis.';
                    this.insightDetail = 'Terpakai Rp ' + this.formatNumber(this.catTotalSpent)
                        + ' · Sisa Rp ' + this.formatNumber(this.catTotalRemaining);
                    return;
                }
            }

            if (this.budget?.limit > 0 && this.budget?.score <= 50) {
                this.insightType = 'warning';
                this.insightText = 'Budget bulanan umum mulai menipis.';
                this.insightDetail = 'Terpakai Rp ' + this.formatNumber(this.budget.used) + ' · Sisa Rp ' + this.formatNumber(this.budget.left);
                return;
            }
            if (this.maxCategory && this.maxCategoryAmount > 0) {
                this.insightType = 'info';
                this.insightText = this.maxCategory.name + ' mengambil ' + this.maxCategoryPct + '% dari total pengeluaran bulan ini.';
                this.insightDetail = 'Total Rp ' + this.formatNumber(this.maxCategoryAmount);
            }
        },
        getEmoji(cat) {
            const map = { 'MAKANAN': '🍜', 'FOOD': '🍜', 'FOOD & BEVERAGE': '🍜',
                'TRANSPORTASI': '🚗', 'TRANSPORT': '🚗', 'TAGIHAN': '⚡', 'BILLS': '⚡', 'UTILITY': '⚡',
                'BELANJA': '🛍️', 'SHOPPING': '🛍️', 'GAJI': '💰', 'SALARY': '💰', 'INCOME': '💰',
                'FREELANCE': '💻', 'KESEHATAN': '💊', 'HEALTH': '💊', 'MAKAN': '🍜' };
            return map[cat?.toUpperCase()] || '📄';
        },
        async fetchCategoryBudgets() {
            if (this.budgetHealthInFlight) return;
            this.budgetHealthInFlight = true;
            this.budgetHealthLoading = true;
            this.budgetHealthError = '';
            try {
                const res = await window.apiClient.get('/v1/budgets');
                const budgets = res.data.data;
                if (!Array.isArray(budgets)) {
                    throw new Error('Data budget tidak valid.');
                }

                const progress = await Promise.all(budgets.map(async (budget) => {
                    const progressRes = await window.apiClient.get('/v1/budgets/' + encodeURIComponent(budget.id) + '/progress');
                    const payload = progressRes.data.data || {};
                    const detail = payload.budget || {};
                    const categoryName = detail.category || budget.category?.name || budget.category_name || 'LAINNYA';
                    const spent = Number(payload.spent || 0);
                    const amount = Number(detail.amount || budget.amount || 0);
                    const remaining = Number(payload.remaining || Math.max(0, amount - spent));
                    const pct = Number(payload.percentage || (amount > 0 ? Math.round((spent / amount) * 100) : 0));
                    return {
                        id: budget.id,
                        name: categoryName,
                        amount,
                        spent,
                        remaining,
                        pct,
                        status: payload.status || this.categoryBudgetStatusLabel(pct),
                        period: detail.period || budget.period || '',
                    };
                }));

                progress.sort((a, b) => b.pct - a.pct);
                this.catBudgets = progress;
                this.budgetHealthLoaded = true;
                this.updateInsight();
            } catch (e) {
                this.budgetHealthError = 'Budget belum dapat dimuat. Coba lagi.';
                console.error('Budget health load error:', e);
            } finally {
                this.budgetHealthLoading = false;
                this.budgetHealthInFlight = false;
                this.loading.budget = false;
            }
        },
        retryBudgetHealth() {
            if (this.budgetHealthInFlight) return;
            return this.fetchCategoryBudgets();
        },
        get catTotalBudget() {
            return this.catBudgets.reduce((total, budget) => total + budget.amount, 0);
        },
        get catTotalSpent() {
            return this.catBudgets.reduce((total, budget) => total + budget.spent, 0);
        },
        get catTotalRemaining() {
            return this.catBudgets.reduce((total, budget) => total + budget.remaining, 0);
        },
        get catBudgetPct() {
            if (this.catTotalBudget <= 0) return 0;
            return Math.round((this.catTotalSpent / this.catTotalBudget) * 100);
        },
        get catBudgetClass() {
            return this.categoryBudgetStatusClass(this.catBudgetPct);
        },
        get catBudgetStatus() {
            return {
                text: this.categoryBudgetStatusLabel(this.catBudgetPct),
                cls: this.categoryBudgetStatusClass(this.catBudgetPct),
                score: this.clamp(100 - this.catBudgetPct, 0, 100),
            };
        },
        get catBudgetInsight() {
            const pct = this.catBudgetPct;
            if (this.catBudgets.length === 0) return '';
            if (pct >= 100) return 'Total pengeluaran budget kategori sudah melebihi batas.';
            if (pct >= 80) return 'Total pengeluaran budget kategori hampir habis.';
            if (pct >= 60) return 'Pengeluaran budget kategori mulai perlu dijaga.';
            return 'Total pengeluaran budget kategori masih aman bulan ini.';
        },
        get catTopBudgets() {
            return this.catBudgets.slice(0, 3);
        },
        async fetchDashboard() {
            try {
                const res = await window.apiClient.get('/v1/dashboard');
                const data = res.data.data;
                this.balance = data.current_month_balance || 0;
                this.income = data.total_income || 0;
                this.expense = data.total_expense || 0;
                this.updateBudget(data);
                if (data.recent_transactions) this.transactions = data.recent_transactions;
                if (data.expense_by_category) {
                    const total = data.expense_by_category.reduce((s, c) => s + (c.amount || 0), 0);
                    this.categories = data.expense_by_category.filter(c => (c.amount || 0) > 0).map(c => ({
                        ...c, name: c.category_name || c.name || 'LAINNYA',
                        icon: c.category_icon || c.icon || '📌',
                        pct: total > 0 ? Math.round((c.amount / total) * 100) : 0,
                        emoji: this.getEmoji(c.category_name || c.name)
                    }));
                    if (this.categories.length > 0) {
                        const maxCat = this.categories.reduce((max, cat) => cat.amount > max.amount ? cat : max);
                        this.maxCategory = maxCat; this.maxCategoryAmount = maxCat.amount; this.maxCategoryPct = maxCat.pct;
                    }
                    this.updateInsight();
                }
                if (!data.expense_by_category) this.updateInsight();
            } catch (e) {
                window.utils.handleApiError(e, 'Gagal memuat data dashboard');
            } finally {
                this.loading.balance = false; this.loading.transactions = false;
                this.loading.categories = false; this.loading.budget = false;
            }
        }
    }));

    // ── Chat Page ──

    Alpine.data('profilePage', () => ({
        user: window.auth.getUser(),
        stats: { total: 0, month: 0, biggest: 0, categories: 0 },
        budget: { limit: 0, used: 0, left: 0, pct: 0 },
        editForm: { name: '', email: '' },
        budgetInput: 0,
        loading: true,
        hasLoaded: false,
        error: '',
        requestInFlight: false,
        async init() { await this.fetchProfile(); },
        formatNumber(n) { if (!n) return '0'; return Number(n).toLocaleString('id-ID'); },
        async fetchProfile() {
            if (this.requestInFlight) return;
            this.requestInFlight = true;
            this.loading = true;
            this.error = '';
            try {
                const res = await window.apiClient.get('/v1/user/profile');
                const data = res.data.data;
                if (!data || typeof data !== 'object' || Array.isArray(data)) {
                    throw new Error('Invalid profile response');
                }
                this.user = { ...this.user, ...data };
                window.auth.setUser(this.user);
                this.editForm = { name: data.name || '', email: data.email || '' };
                const s = data.stats || {};
                this.stats = {
                    total: s.total_transactions || 0,
                    month: s.transactions_this_month || 0,
                    biggest: s.largest_transaction_amount || 0,
                    categories: s.unique_categories_used || 0,
                };
                const b = data.budget || {};
                this.budget = {
                    limit: b.monthly_budget || 0,
                    used: b.budget_used || 0,
                    left: b.budget_remaining || 0,
                    pct: b.budget_percentage || 0,
                };
                this.budgetInput = this.budget.limit;
                this.hasLoaded = true;
            } catch (e) {
                this.error = 'Profil belum dapat dimuat. Coba lagi.';
            } finally {
                this.loading = false;
                this.requestInFlight = false;
            }
        },
        retryProfile() {
            if (this.requestInFlight) return;
            return this.fetchProfile();
        },
        openModal(id) { document.getElementById(id)?.classList.add('open'); },
        closeModal(id) { document.getElementById(id)?.classList.remove('open'); },
        bgClose(e, id) { if (e.target === e.currentTarget) this.closeModal(id); },
        async saveProfile() {
            try {
                const res = await window.apiClient.put('/v1/user/profile', {
                    name: this.editForm.name,
                    email: this.editForm.email,
                });
                const data = res.data.data;
                this.user = { ...this.user, ...data };
                window.auth.setUser(this.user);
                this.closeModal('m-edit');
                window.utils.showToast('success', 'Profil berhasil diperbarui');
            } catch (e) {
                window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal menyimpan profil'));
            }
        },
        async saveBudget() {
            try {
                await window.apiClient.put('/v1/user/budget', { monthly_budget: this.budgetInput });
                this.budget.limit = Number(this.budgetInput) || 0;
                this.closeModal('m-budget');
                window.utils.showToast('success', 'Budget berhasil diperbarui');
            } catch (e) {
                window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal menyimpan budget'));
            }
        },
        async exportData() {
            try {
                const res = await window.apiClient.get('/v1/transactions', { params: { per_page: 1000 } });
                const txs = res.data.data || [];
                if (!txs.length) { window.utils.showToast('info', 'Belum ada data untuk di-export'); return; }
                const header = 'Tanggal,Deskripsi,Kategori,Tipe,Jumlah\n';
                const rows = txs.map(t => `${t.transaction_date},${(t.description||'').replace(/,/g,';')},${t.category||''},${t.type||''},${t.amount||0}`).join('\n');
                const blob = new Blob([header + rows], { type: 'text/csv' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a'); a.href = url; a.download = 'zaku-transaksi.csv'; a.click();
                URL.revokeObjectURL(url);
                window.utils.showToast('success', 'Export berhasil! 📥');
            } catch (e) {
                window.utils.showToast('error', 'Gagal export data');
            }
        },
        async logout() {
            try {
                await window.apiClient.post('/v1/auth/logout');
            } catch { /* ignore */ } finally {
                window.auth.clearToken();
            }
        },
        async deleteAccount() {
            const ok = await window.utils.confirmDialog({
                title: 'Hapus Akun?',
                message: 'Semua data kamu (transaksi, budget, kategori) akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.',
                okLabel: 'YA, HAPUS AKUN',
                danger: true,
            });
            if (!ok) return;
            try {
                await window.apiClient.delete('/v1/user/account');
                window.auth.clearToken();
            } catch (e) {
                window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal menghapus akun'));
            }
        }
    }));

    // ===== CHANGELOG PAGE =====

    Alpine.data('settingsPage', () => ({
        form: {
            monthly_budget: 0,
            currency: 'IDR',
        },
        theme: localStorage.getItem('zaku_theme') || 'light',
        loading: true,
        saving: false,
        setTheme(t) {
            this.theme = t;
            localStorage.setItem('zaku_theme', t);
            if (t === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.removeAttribute('data-theme');
            }
            if (window.utils && window.utils.showToast) {
                window.utils.showToast('Tema diubah ke ' + (t === 'dark' ? 'Cyber Dark 🌙' : 'Light ☀️'), 'success');
            }
        },
        async init() { await this.fetchSettings(); },
        async fetchSettings() {
            this.loading = true;
            try {
                const res = await window.apiClient.get('/v1/user/profile');
                const data = res.data.data || {};
                this.form.monthly_budget = Number(data.budget?.monthly_budget || data.monthly_budget || 0);
                // Currency is a device-local display preference; notification state is not user-configurable.
                try {
                    const prefs = JSON.parse(localStorage.getItem('zaku_settings') || '{}');
                    this.form.currency = prefs.currency || 'IDR';
                } catch { /* ignore */ }
            } catch (e) {
                window.utils.showToast('error', 'Gagal memuat pengaturan');
            } finally {
                this.loading = false;
            }
        },
        async save() {
            this.saving = true;
            try {
                // Simpan budget ke backend
                await window.apiClient.put('/v1/user/budget', { monthly_budget: this.form.monthly_budget });
                // Currency remains a device-local display preference.
                localStorage.setItem('zaku_settings', JSON.stringify({
                    currency: this.form.currency,
                }));
                window.utils.showToast('success', 'Budget tersimpan di akun dan mata uang di perangkat ini.');
            } catch (e) {
                const msg = window.utils.parseApiError(e, 'Gagal menyimpan pengaturan');
                window.utils.showToast('error', msg);
            } finally {
                this.saving = false;
            }
        },
    }));

    // ── Dashboard: Calendar View ──

    Alpine.data('notificationInbox', () => ({
        isOpen: false,
        loading: false,
        markingAll: false,
        markingId: null,
        error: '',
        items: [],
        unreadCount: 0,
        returnFocus: null,
        async init() {
            await this.fetchNotifications(true);
        },
        async fetchNotifications(silent = false) {
            this.loading = true;
            this.error = '';
            try {
                const response = await window.apiClient.get('/notifications', {
                    params: { per_page: 10 },
                });
                const data = response.data.data || {};
                this.items = Array.isArray(data.items) ? data.items : [];
                this.unreadCount = Math.max(0, Number(data.unread_count) || 0);
            } catch (error) {
                this.error = window.utils.parseApiError(error, 'Notifikasi gagal dimuat.');
                if (!silent) window.utils.showToast('error', this.error);
            } finally {
                this.loading = false;
            }
        },
        async togglePanel() {
            if (this.isOpen) {
                this.closePanel();
                return;
            }
            this.returnFocus = document.activeElement;
            this.isOpen = true;
            this.fetchNotifications();
            this.$nextTick(() => document.getElementById('notification-panel-close')?.focus());
        },
        closePanel() {
            if (!this.isOpen) return;
            this.isOpen = false;
            this.$nextTick(() => {
                if (this.returnFocus?.isConnected) this.returnFocus.focus();
                this.returnFocus = null;
            });
        },
        trapFocus(event) {
            const panel = document.getElementById('notification-panel');
            const focusable = panel
                ? [...panel.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')]
                    .filter(element => element.getClientRects().length > 0)
                : [];
            if (focusable.length === 0) {
                event.preventDefault();
                return;
            }

            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        },
        formatNotificationDate(value) {
            if (!value) return '';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return '';
            return date.toLocaleString('id-ID', {
                day: 'numeric',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit',
            });
        },
        async markRead(item) {
            if (item.is_read || this.markingId !== null) return;
            this.markingId = item.id;
            this.error = '';
            try {
                await window.apiClient.post('/notifications/' + encodeURIComponent(item.id) + '/read');
                item.is_read = true;
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            } catch (error) {
                this.error = window.utils.parseApiError(error, 'Notifikasi gagal ditandai sudah dibaca.');
            } finally {
                this.markingId = null;
            }
        },
        async markAllRead() {
            if (this.markingAll || this.unreadCount === 0) return;
            this.markingAll = true;
            this.error = '';
            try {
                await window.apiClient.post('/notifications/read-all');
                this.items = this.items.map(item => ({ ...item, is_read: true }));
                this.unreadCount = 0;
            } catch (error) {
                this.error = window.utils.parseApiError(error, 'Notifikasi gagal ditandai sudah dibaca.');
            } finally {
                this.markingAll = false;
            }
        },
    }));

    // ── Settings Page ──

    Alpine.data('insightsPage', () => ({
        insights: [],
        loading: true,
        hasLoaded: false,
        error: '',
        requestInFlight: false,
        async init() { await this.fetchInsights(); },
        async fetchInsights() {
            if (this.requestInFlight) return;
            this.requestInFlight = true;
            this.loading = true;
            this.error = '';
            try {
                const res = await window.apiClient.get('/v1/insights');
                const data = res.data.data;
                if (!Array.isArray(data)) throw new Error('Invalid insights response');
                this.insights = data;
                this.hasLoaded = true;
            } catch (e) {
                this.error = 'Insight belum dapat dimuat. Coba lagi.';
            } finally {
                this.loading = false;
                this.requestInFlight = false;
            }
        },
        refresh() {
            if (this.requestInFlight) return;
            return this.fetchInsights();
        },
        iconFor(ins) {
            const map = { budget_risk: '⚠️', largest_expense: '💸', largest_category: '📊', danger: '⚠️', warning: '⚡', info: '💡' };
            return map[ins.type] || map[ins.severity] || '💡';
        },
        badgeFor(sev) {
            const map = { danger: 'RISIKO', warning: 'WASPADA', info: 'INFO' };
            return map[sev] || 'INFO';
        },
    }));

}
