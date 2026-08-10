// resources/js/alpine-components.js
// All Alpine.js components registered via Alpine.data()
// Extracted from inline Blade <script> blocks

export default function (Alpine) {

    // ── Auth: Login ──
    Alpine.data('loginForm', () => ({
        formData: { email: '', password: '', remember: false },
        loading: false,
        errors: { email: '', password: '' },
        validate() {
            this.errors = { email: '', password: '' };
            let ok = true;
            if (!this.formData.email) { this.errors.email = 'Email wajib diisi'; ok = false; }
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.formData.email)) { this.errors.email = 'Format email tidak valid'; ok = false; }
            if (!this.formData.password) { this.errors.password = 'Password wajib diisi'; ok = false; }
            return ok;
        },
        init() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('session') === 'expired') {
                window.utils.showToast('error', 'Sesi Anda telah berakhir. Silakan login kembali.', true);
            }
        },
        async submit() {
            if (this.loading || !this.validate()) return;
            this.loading = true;
            try {
                const response = await window.apiClient.post('/v1/auth/login', this.formData);
                const { token, user } = response.data.data;
                window.auth.setToken(token);
                window.auth.setUser(user);
                document.cookie = 'zaku_token=' + token + '; path=/; max-age=86400; SameSite=Lax';
                window.utils.showToast('success', 'Login berhasil! Sedang mengalihkan...');
                setTimeout(() => { window.location.href = '/dashboard'; }, 1500);
            } catch (error) {
                const msg = window.utils.parseApiError(error, 'Email atau password salah. Silakan coba lagi.');
                window.utils.showToast('error', msg, true);
            } finally { this.loading = false; }
        }
    }));

    // ── Auth: Register ──
    Alpine.data('registerForm', () => ({
        formData: { name: '', email: '', password: '', password_confirmation: '' },
        loading: false,
        async submit() {
            if (this.loading) return;
            if (this.formData.password !== this.formData.password_confirmation) {
                window.utils.showToast('error', 'Konfirmasi password tidak sesuai.'); return;
            }
            if (this.formData.password.length < 8) {
                window.utils.showToast('error', 'Password harus minimal 8 karakter.'); return;
            }
            this.loading = true;
            try {
                await window.apiClient.post('/v1/auth/register', this.formData);
                window.utils.showToast('success', 'Akun berhasil dibuat! Silakan masuk.');
                setTimeout(() => { window.location.href = '/login'; }, 1500);
            } catch (error) {
                const msg = window.utils.parseApiError(error, 'Gagal membuat akun. Silakan periksa kembali data Anda.');
                window.utils.showToast('error', msg, true);
            } finally { this.loading = false; }
        }
    }));

    // ── Auth: Forgot Password ──
    Alpine.data('forgotPasswordForm', () => ({
        email: '',
        loading: false,
        sent: false,
        async submit() {
            if (this.loading) return;
            this.loading = true;
            try {
                await window.apiClient.post('/v1/auth/forgot-password', { email: this.email });
                this.sent = true;
                window.utils.showToast('success', 'Link reset password telah dikirim ke email Anda.');
            } catch (error) {
                const msg = window.utils.parseApiError(error, 'Gagal mengirim link reset.');
                window.utils.showToast('error', msg, true);
            } finally { this.loading = false; }
        }
    }));

    // ── Auth: Verify Email ──
    Alpine.data('verifyEmail', () => ({
        email: '',
        code: ['', '', '', '', '', ''],
        loading: false,
        get codeString() { return this.code.join(''); },
        focusNext(idx, event) {
            if (event.inputType === 'deleteContentBackward' && idx > 0) {
                this.$nextTick(() => this.$refs[`code-${idx - 1}`]?.focus());
                return;
            }
            if (this.code[idx] && idx < 5) {
                this.$nextTick(() => this.$refs[`code-${idx + 1}`]?.focus());
            }
        },
        async submit() {
            if (this.loading || this.codeString.length !== 6) return;
            this.loading = true;
            try {
                await window.apiClient.post('/v1/auth/verify-email', {
                    email: this.email,
                    code: this.codeString,
                });
                window.utils.showToast('success', 'Email berhasil diverifikasi!');
                setTimeout(() => { window.location.href = '/login'; }, 1500);
            } catch (error) {
                const msg = window.utils.parseApiError(error, 'Kode verifikasi salah atau kedaluwarsa.');
                window.utils.showToast('error', msg, true);
            } finally { this.loading = false; }
        },
        async resend() {
            if (!this.email) { window.utils.showToast('error', 'Masukkan email terlebih dahulu.'); return; }
            try {
                await window.apiClient.post('/v1/auth/resend-verification', { email: this.email });
                window.utils.showToast('success', 'Kode baru telah dikirim!');
            } catch (error) {
                const msg = window.utils.parseApiError(error, 'Gagal mengirim ulang kode.');
                window.utils.showToast('error', msg, true);
            }
        }
    }));

    // ── Auth: Manual Verify ──
    Alpine.data('manualVerifyForm', () => ({
        email: '',
        code: ['', '', '', '', '', ''],
        loading: false,
        get codeString() { return this.code.join(''); },
        focusNext(idx, event) {
            if (event.inputType === 'deleteContentBackward' && idx > 0) {
                this.$nextTick(() => this.$refs[`code-${idx - 1}`]?.focus()); return;
            }
            if (this.code[idx] && idx < 5) {
                this.$nextTick(() => this.$refs[`code-${idx + 1}`]?.focus());
            }
        },
        async submit() {
            if (this.loading || this.codeString.length !== 6) return;
            this.loading = true;
            try {
                await window.apiClient.post('/v1/auth/verify-email', {
                    email: this.email,
                    code: this.codeString,
                });
                window.utils.showToast('success', 'Email berhasil diverifikasi! Silakan login.');
                setTimeout(() => { window.location.href = '/login'; }, 2000);
            } catch (error) {
                const msg = window.utils.parseApiError(error, 'Kode verifikasi salah atau kedaluwarsa.');
                window.utils.showToast('error', msg, true);
            } finally { this.loading = false; }
        }
    }));

    // ── Auth: Process Verify ──
    Alpine.data('processVerification', () => ({
        email: '',
        loading: false,
        async submit() {
            if (this.loading || !this.email) return;
            this.loading = true;
            try {
                await window.apiClient.post('/v1/auth/resend-verification', { email: this.email });
                window.utils.showToast('success', 'Kode verifikasi telah dikirim ulang!');
            } catch (error) {
                const msg = window.utils.parseApiError(error, 'Gagal mengirim ulang verifikasi.');
                window.utils.showToast('error', msg, true);
            } finally { this.loading = false; }
        }
    }));

    // ── Dashboard: Home ──
    Alpine.data('dashboardHome', () => ({
        balance: 0, income: 0, expense: 0,
        transactions: [], categories: [],
        maxCategory: null, maxCategoryAmount: 0, maxCategoryPct: 0,
        insightText: '', insightDetail: '', insightType: 'info',
        budget: { limit: 0, used: 0, left: 0, usedPct: 0, score: 0, status: 'Budget belum diatur', statusClass: 'risk', insight: '' },
        loading: { balance: true, transactions: true, categories: true, budget: true },
        async init() { this.fetchDashboard(); },
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
                this.budget = { limit: 0, used, left: 0, usedPct: 0, score: 0, status: 'Budget belum diatur', statusClass: 'risk', insight: 'Atur budget bulanan agar Zaku bisa membaca kondisi pengeluaranmu.' };
                return;
            }
            const usedPct = this.clamp(Math.round((used / limit) * 100), 0, 100);
            const score = this.clamp(100 - usedPct, 0, 100);
            let status = 'RISIKO BOROS', statusClass = 'risk';
            if (score >= 80) { status = 'AMAN'; statusClass = 'safe'; }
            else if (score >= 50) { status = 'PERLU DIJAGA'; statusClass = 'watch'; }
            const insight = score >= 80
                ? 'Budget masih aman. Pertahankan ritme pengeluaran bulan ini.'
                : score >= 50 ? 'Pengeluaran mulai mendekati batas. Jaga transaksi besar berikutnya.'
                : 'Budget berisiko habis. Prioritaskan kebutuhan utama dulu.';
            this.budget = { limit, used, left, usedPct, score, status, statusClass, insight };
        },
        getEmoji(cat) {
            const map = { 'MAKANAN': '🍜', 'FOOD': '🍜', 'FOOD & BEVERAGE': '🍜',
                'TRANSPORTASI': '🚗', 'TRANSPORT': '🚗', 'TAGIHAN': '⚡', 'BILLS': '⚡', 'UTILITY': '⚡',
                'BELANJA': '🛍️', 'SHOPPING': '🛍️', 'GAJI': '💰', 'SALARY': '💰', 'INCOME': '💰',
                'FREELANCE': '💻', 'KESEHATAN': '💊', 'HEALTH': '💊', 'MAKAN': '🍜' };
            return map[cat?.toUpperCase()] || '📄';
        },
        updateInsight() {
            this.insightType = 'info';
            this.insightText = 'Belum ada insight. Mulai catat transaksimu agar Zaku bisa memberikan insight.';
            this.insightDetail = '';
            if (this.budget?.limit > 0 && this.budget?.score <= 50) {
                this.insightType = 'warning';
                this.insightText = 'Pengeluaran mendekati batas budget.';
                this.insightDetail = 'Terpakai Rp ' + this.formatNumber(this.budget.used) + ' · Sisa Rp ' + this.formatNumber(this.budget.left);
                return;
            }
            if (this.maxCategory && this.maxCategoryAmount > 0) {
                this.insightType = 'info';
                this.insightText = this.maxCategory.name + ' mengambil ' + this.maxCategoryPct + '% dari total pengeluaran bulan ini.';
                this.insightDetail = 'Total Rp ' + this.formatNumber(this.maxCategoryAmount);
            }
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
    Alpine.data('chatPage', () => ({
        user: window.auth.getUser(),
        loading: false, message: '', charCount: 0, typing: false,
        STORAGE_KEY: 'zaku_chat_history',
        messages: [],
        init() {
            try {
                const saved = localStorage.getItem(this.STORAGE_KEY);
                if (saved) {
                    const parsed = JSON.parse(saved);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        this.messages = parsed;
                        return;
                    }
                }
            } catch { /* ignore */ }
            this.messages.push({
                role: 'ai', html: true,
                content: 'Halo, <strong>' + (window.auth.getUser()?.name?.split(' ')[0] || 'Teman') + '</strong>! 👋 Saya bisa bantu catat <strong>pemasukan</strong> dan <strong>pengeluaran</strong> kamu.<br><br>Ketik aja transaksinya, misalnya:<br><em>"Tadi beli makan siang 35rb"</em> <span style=\"color:rgba(17,16,16,.4)\">← pengeluaran</span><br><em>"Gajian bulan ini 5 juta"</em> <span style=\"color:rgba(17,16,16,.4)\">← pemasukan</span><br><em>"Bayar Grab ke kantor 28 ribu"</em> <span style=\"color:rgba(17,16,16,.4)\">← pengeluaran</span><div class="chips"><div class="chip" @click="sendQuick(\'Beli makan siang 35rb\')">🍜 Makan 35rb</div><div class="chip" @click="sendQuick(\'Bayar Grab 28 ribu\')">🚗 Grab 28rb</div><div class="chip" @click="sendQuick(\'Terima gaji 7.5 juta\')">💰 Gaji</div></div>',
                time: '09:00'
            });
        },
        persist() {
            try {
                localStorage.setItem(this.STORAGE_KEY, JSON.stringify(this.messages));
            } catch { /* quota exceeded etc */ }
        },
        updateCharCount() { this.charCount = this.message.length; },
        handleKey(event) { if (event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); this.sendMsg(); } },
        now() { return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }); },
        scrollBottom() { this.$nextTick(() => { const el = this.$refs.msgs; if (el) el.scrollTop = el.scrollHeight; }); },
        async sendMsg() {
            const val = this.message.trim();
            if (!val || this.loading) return;
            this.message = ''; this.charCount = 0;
            this.messages.push({ role: 'usr', html: false, content: val, time: this.now() });
            this.persist();
            this.typing = true;
            this.scrollBottom();
            this.loading = true;
            try {
                const res = await window.apiClient.post('/v1/ai/chat', { message: val });
                const data = res.data;
                let bubbleHtml = '';
                if (data.data) {
                    const inner = data.data;
                    const parsed = inner.parsed_data || inner;
                    if (inner.reply_message) bubbleHtml = this.escapeHtml(inner.reply_message);
                    else if (inner.response) bubbleHtml = this.escapeHtml(inner.response);
                    if (parsed.amount && parsed.description) {
                        const sign = parsed.type === 'income' ? 'inc' : 'exp';
                        bubbleHtml += '<div class="confirm-card">'
                            + '<div class="confirm-row"><span class="confirm-key">DESKRIPSI</span><span class="confirm-val">' + this.escapeHtml(parsed.description) + '</span></div>'
                            + '<div class="confirm-row"><span class="confirm-key">JUMLAH</span><span class="confirm-val ' + sign + '">' + this.formatAmount(parsed.amount) + '</span></div>';
                        if (parsed.category) bubbleHtml += '<div class="confirm-row"><span class="confirm-key">KATEGORI</span><span class="confirm-val">' + this.escapeHtml(parsed.category) + '</span></div>';
                        bubbleHtml += '<div class="confirm-row"><span class="confirm-key">TIPE</span><span class="confirm-val ' + sign + '">' + (parsed.type === 'income' ? '↑ PEMASUKAN' : '↓ PENGELUARAN') + '</span></div></div>';
                    } else if (parsed.message) bubbleHtml = this.escapeHtml(parsed.message);
                } else if (data.response) bubbleHtml = this.escapeHtml(data.response);
                else if (data.message) bubbleHtml = this.escapeHtml(data.message);
                if (!bubbleHtml) bubbleHtml = '<em style="color: #999;">Maaf, tidak ada respons dari server. Coba lagi ya!</em>';
                this.messages.push({ role: 'ai', html: true, content: bubbleHtml, time: this.now() });
            } catch (e) {
                const errorMsg = window.utils.parseApiError(e, 'Maaf, lagi ada gangguan. Coba lagi ya!');
                this.messages.push({ role: 'ai', html: false, content: errorMsg, time: this.now() });
            } finally {
                this.persist();
                this.typing = false;
                this.loading = false;
                this.scrollBottom();
            }
        },
        sendQuick(text) { this.message = text; this.charCount = text.length; this.sendMsg(); },
        async clearChat() {
            const ok = await window.utils.confirmDialog({ title: 'Hapus Pesan?', message: 'Semua riwayat chat akan dibersihkan.', okLabel: 'YA, HAPUS', danger: false });
            if (!ok) return;
            localStorage.removeItem(this.STORAGE_KEY);
            this.messages = [];
            this.messages.push({ role: 'ai', html: true, content: 'Chat dibersihkan. Ada transaksi yang mau dicatat? 😊', time: 'Sekarang' });
        },
        escapeHtml(text) { const d = document.createElement('div'); d.textContent = text; return d.innerHTML; },
        formatAmount(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }
    }));

    // ── Transaction Detail ──
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
    Alpine.data('transactionList', () => ({
        transactions: [],
        loading: true,
        filter: 'all',
        categories: [],
        currentPage: 1,
        lastPage: 1,
        hasMore: false,
        total: 0,
        searchQuery: '',
        sortKey: 'date',
        sortAsc: false,
        activeTrx: null,
        async init() {
            await this.fetchTransactions();
            this.extractCategories();
        },
        formatNumber(n) { if (!n) return '0'; return Number(n).toLocaleString('id-ID'); },
        rp(n) { return 'Rp ' + this.formatNumber(n); },
        formatDay(d) { if (!d) return ''; const date = new Date(d); if (isNaN(date.getTime())) return ''; return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }); },
        getTransactionDate(trx) { return trx.transaction_date || trx.date || trx.created_at || trx.updated_at || null; },
        transactionDay(trx) { return this.formatDay(this.getTransactionDate(trx)); },
        getEmoji(cat) { const map = { 'MAKANAN': '🍜', 'FOOD': '🍜', 'TRANSPORTASI': '🚗', 'TRANSPORT': '🚗', 'TAGIHAN': '⚡', 'BILLS': '⚡', 'BELANJA': '🛍️', 'SHOPPING': '🛍️', 'GAJI': '💰', 'SALARY': '💰', 'INCOME': '💰', 'FREELANCE': '💻', 'KESEHATAN': '💊', 'HEALTH': '💊', 'HIBURAN': '🎮', 'ENTERTAINMENT': '🎮' }; return map[cat?.toUpperCase()] || '📄'; },
        getAiHint(cat) {
            const hints = {
                'MAKANAN': 'Pos makanan kamu bulan ini cukup tinggi. Coba masak sendiri untuk hemat lebih banyak.',
                'TRANSPORTASI': 'Pengeluaran transportasi masih wajar bulan ini.',
                'BELANJA': 'Hati-hati! Belanja bulan ini naik. Coba terapkan "48-hour rule" sebelum beli.',
                'TAGIHAN': 'Ini tagihan rutin — ZAKU akan ingatkan kamu otomatis bulan depan.',
                'GAJI': 'Pemasukan gaji diterima tepat waktu. Mantap!'
            };
            return hints[cat?.toUpperCase()] || 'Transaksi ini dicatat otomatis oleh ZAKU AI.';
        },
        extractCategories() { const set = new Set(); this.transactions.forEach(t => { if (t.category_name) set.add(t.category_name.toUpperCase()); }); this.categories = Array.from(set); },
        totalIncome: 0,
        totalExpense: 0,
        async fetchTransactions(page = 1) {
            this.loading = true;
            try {
                const res = await window.apiClient.get(`/v1/transactions?limit=20&page=${page}`);
                const payload = res.data.data || {};
                const groups = Array.isArray(payload) ? payload : (payload.groups || []);
                const meta = payload.meta || {};
                
                // Fetch totals from meta if available, else fallback
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
                
                this.transactions = flatTx;
                this.currentPage = meta.page || 1;
                this.lastPage = Math.ceil((meta.total || 0) / (meta.limit || 20));
                this.hasMore = meta.has_more || false;
                this.total = meta.total || 0;
            } catch (e) {
                console.error('Fetch transactions error:', e);
            } finally {
                this.loading = false;
            }
        },
        async loadPage(p) {
            if (p < 1 || (this.lastPage && p > this.lastPage)) return;
            await this.fetchTransactions(p);
            document.getElementById('tx-body').scrollTop = 0;
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
        },
        setSort(key, btn) {
            if (this.sortKey === key) this.sortAsc = !this.sortAsc;
            else { this.sortKey = key; this.sortAsc = false; }
            document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            btn.textContent = key === 'date' ? (this.sortAsc ? '↑ TERLAMA' : '↓ TERBARU') : (this.sortAsc ? '↑ TERKECIL' : '↓ TERBESAR');
        },
        doSearch(v) { this.searchQuery = v; },
        filtered() {
            let d = [...this.transactions];
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                d = d.filter(t => (t.description || '').toLowerCase().includes(q) || (t.category_name || '').toLowerCase().includes(q));
            }
            if (this.filter !== 'all') {
                if (this.filter === 'income' || this.filter === 'expense') d = d.filter(t => t.type === this.filter);
                else d = d.filter(t => t.category_name?.toUpperCase() === this.filter);
            }
            d.sort((a, b) => {
                const v = this.sortKey === 'date' ? new Date(this.getTransactionDate(a)) - new Date(this.getTransactionDate(b)) : a.amount - b.amount;
                return this.sortAsc ? v : -v;
            });
            return d;
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
        },
        openDrw(trx) { this.activeTrx = trx; document.getElementById('drawer-bg').classList.add('open'); },
        closeDrw() { document.getElementById('drawer-bg').classList.remove('open'); },
        showToast(msg) { window.utils.showToast('info', msg); }
    }));

    // ── Profile Page ──
    Alpine.data('profilePage', () => ({
        user: window.auth.getUser(),
        stats: { total: 0, month: 0, biggest: 0, categories: 0 },
        budget: { limit: 0, used: 0, left: 0, pct: 0 },
        editForm: { name: '', email: '' },
        budgetInput: 0,
        loading: true,
        async init() { await this.fetchProfile(); },
        formatNumber(n) { if (!n) return '0'; return Number(n).toLocaleString('id-ID'); },
        async fetchProfile() { this.loading = true; try {
            const res = await window.apiClient.get('/v1/user/profile');
            const data = res.data.data;
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
        } catch (e) { window.utils.showToast('error', 'Gagal memuat profil');
        } finally { this.loading = false; }},
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
                window.auth.clearUser();
                window.location.href = '/login';
            }
        }
    }));

    // ===== CHANGELOG PAGE =====
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

// changelogPage registered via Alpine.data() above
