// resources/js/alpine-components.js
// All Alpine.js components registered via Alpine.data()
// Extracted from inline Blade <script> blocks

export default function (Alpine) {
    // ── Auth: Login ──
    Alpine.data("loginForm", () => ({
        formData: { email: "", password: "", remember: false },
        loading: false,
        errors: { email: "", password: "" },
        validate() {
            this.errors = { email: "", password: "" };
            let ok = true;
            if (!this.formData.email) {
                this.errors.email = "Email wajib diisi";
                ok = false;
            } else if (
                !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.formData.email)
            ) {
                this.errors.email = "Format email tidak valid";
                ok = false;
            }
            if (!this.formData.password) {
                this.errors.password = "Password wajib diisi";
                ok = false;
            }
            return ok;
        },
        init() {
            const params = new URLSearchParams(window.location.search);
            if (params.get("session") === "expired") {
                window.utils.showToast(
                    "error",
                    "Sesi Anda telah berakhir. Silakan login kembali.",
                    true
                );
            }
        },
        async submit() {
            if (this.loading || !this.validate()) return;
            this.loading = true;
            try {
                const response = await window.apiClient.post(
                    "/v1/auth/login",
                    this.formData
                );
                const { token, user } = response.data.data;
                window.auth.setToken(token);
                window.auth.setUser(user);
                window.utils.showToast(
                    "success",
                    "Login berhasil! Sedang mengalihkan..."
                );
                setTimeout(() => {
                    window.location.href = "/dashboard";
                }, 1500);
            } catch (error) {
                const msg = window.utils.parseApiError(
                    error,
                    "Email atau password salah. Silakan coba lagi."
                );
                window.utils.showToast("error", msg, true);
            } finally {
                this.loading = false;
            }
        },
    }));

    // ── Auth: Register ──
    Alpine.data("registerForm", () => ({
        formData: {
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
        },
        loading: false,
        async submit() {
            if (this.loading) return;
            if (
                this.formData.password !== this.formData.password_confirmation
            ) {
                window.utils.showToast(
                    "error",
                    "Konfirmasi password tidak sesuai."
                );
                return;
            }
            if (this.formData.password.length < 8) {
                window.utils.showToast(
                    "error",
                    "Password harus minimal 8 karakter."
                );
                return;
            }
            this.loading = true;
            try {
                await window.apiClient.post("/v1/auth/register", this.formData);
                window.utils.showToast(
                    "success",
                    "Akun berhasil dibuat! Silakan cek email Anda."
                );
                setTimeout(() => {
                    window.location.href = "/verify-email";
                }, 2000);
            } catch (error) {
                const msg = window.utils.parseApiError(
                    error,
                    "Gagal membuat akun. Silakan periksa kembali data Anda."
                );
                window.utils.showToast("error", msg, true);
            } finally {
                this.loading = false;
            }
        },
    }));

    // ── Auth: Forgot Password ──
    Alpine.data("forgotPasswordForm", () => ({
        email: "",
        loading: false,
        sent: false,
        async submit() {
            if (this.loading) return;
            this.loading = true;
            try {
                await window.apiClient.post("/v1/auth/forgot-password", {
                    email: this.email,
                });
                this.sent = true;
                window.utils.showToast(
                    "success",
                    "Link reset password telah dikirim ke email Anda."
                );
            } catch (error) {
                const msg = window.utils.parseApiError(
                    error,
                    "Gagal mengirim link reset."
                );
                window.utils.showToast("error", msg, true);
            } finally {
                this.loading = false;
            }
        },
    }));

    // ── Auth: Verify Email ──
    Alpine.data("verifyEmail", () => ({
        email: "",
        code: ["", "", "", "", "", ""],
        loading: false,
        get codeString() {
            return this.code.join("");
        },
        focusNext(idx, event) {
            if (event.inputType === "deleteContentBackward" && idx > 0) {
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
                await window.apiClient.post("/v1/auth/verify-email", {
                    email: this.email,
                    code: this.codeString,
                });
                window.utils.showToast(
                    "success",
                    "Email berhasil diverifikasi!"
                );
                setTimeout(() => {
                    window.location.href = "/login";
                }, 1500);
            } catch (error) {
                const msg = window.utils.parseApiError(
                    error,
                    "Kode verifikasi salah atau kedaluwarsa."
                );
                window.utils.showToast("error", msg, true);
            } finally {
                this.loading = false;
            }
        },
        async resend() {
            if (!this.email) {
                window.utils.showToast(
                    "error",
                    "Masukkan email terlebih dahulu."
                );
                return;
            }
            try {
                await window.apiClient.post("/v1/auth/resend-verification", {
                    email: this.email,
                });
                window.utils.showToast("success", "Kode baru telah dikirim!");
            } catch (error) {
                const msg = window.utils.parseApiError(
                    error,
                    "Gagal mengirim ulang kode."
                );
                window.utils.showToast("error", msg, true);
            }
        },
    }));

    // ── Auth: Manual Verify ──
    Alpine.data("manualVerifyForm", () => ({
        email: "",
        code: ["", "", "", "", "", ""],
        loading: false,
        get codeString() {
            return this.code.join("");
        },
        focusNext(idx, event) {
            if (event.inputType === "deleteContentBackward" && idx > 0) {
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
                await window.apiClient.post("/v1/auth/verify-email", {
                    email: this.email,
                    code: this.codeString,
                });
                window.utils.showToast(
                    "success",
                    "Email berhasil diverifikasi! Silakan login."
                );
                setTimeout(() => {
                    window.location.href = "/login";
                }, 2000);
            } catch (error) {
                const msg = window.utils.parseApiError(
                    error,
                    "Kode verifikasi salah atau kedaluwarsa."
                );
                window.utils.showToast("error", msg, true);
            } finally {
                this.loading = false;
            }
        },
    }));

    // ── Auth: Process Verify ──
    Alpine.data("processVerification", () => ({
        email: "",
        loading: false,
        async submit() {
            if (this.loading || !this.email) return;
            this.loading = true;
            try {
                await window.apiClient.post("/v1/auth/resend-verification", {
                    email: this.email,
                });
                window.utils.showToast(
                    "success",
                    "Kode verifikasi telah dikirim ulang!"
                );
            } catch (error) {
                const msg = window.utils.parseApiError(
                    error,
                    "Gagal mengirim ulang verifikasi."
                );
                window.utils.showToast("error", msg, true);
            } finally {
                this.loading = false;
            }
        },
    }));

    // ── Dashboard: Home ──
    Alpine.data("dashboardHome", () => ({
        balance: 0,
        income: 0,
        expense: 0,
        transactions: [],
        categories: [],
        maxCategory: null,
        maxCategoryAmount: 0,
        maxCategoryPct: 0,
        insightText: "",
        insightDetail: "",
        insightType: "info",
        budget: {
            limit: 0,
            used: 0,
            left: 0,
            usedPct: 0,
            score: 0,
            status: "Budget belum diatur",
            statusClass: "risk",
            insight: "",
        },
        loading: {
            balance: true,
            transactions: true,
            categories: true,
            budget: true,
        },
        async init() {
            this.fetchDashboard();
        },
        formatNumber(n) {
            if (!n) return "0";
            return Number(n).toLocaleString("id-ID");
        },
        formatDate(d) {
            if (!d) return "";
            return new Date(d).toLocaleDateString("id-ID", {
                day: "numeric",
                month: "short",
            });
        },
        toNumber(v) {
            const n = Number(v);
            return Number.isFinite(n) ? n : 0;
        },
        clamp(v, min, max) {
            return Math.min(Math.max(v, min), max);
        },
        readBudgetLimit(data) {
            const user = window.auth.getUser() || {};
            return this.toNumber(
                data.monthly_budget ||
                    data.budget_limit ||
                    data.budget?.limit ||
                    data.budget?.monthly_budget ||
                    user.monthly_budget ||
                    user.budget?.limit
            );
        },
        updateBudget(data = {}) {
            const limit = this.readBudgetLimit(data);
            const used = this.toNumber(
                data.budget_used ||
                    data.used_budget ||
                    data.budget?.used ||
                    data.budget?.spent ||
                    data.total_expense ||
                    this.expense
            );
            const left = Math.max(
                0,
                this.toNumber(
                    data.remaining_budget ||
                        data.budget_left ||
                        data.budget?.left ||
                        data.budget?.remaining ||
                        limit - used
                )
            );
            if (limit <= 0) {
                this.budget = {
                    limit: 0,
                    used,
                    left: 0,
                    usedPct: 0,
                    score: 0,
                    status: "Budget belum diatur",
                    statusClass: "risk",
                    insight:
                        "Atur budget bulanan agar Zaku bisa membaca kondisi pengeluaranmu.",
                };
                return;
            }
            const usedPct = this.clamp(
                Math.round((used / limit) * 100),
                0,
                100
            );
            const score = this.clamp(100 - usedPct, 0, 100);
            let status = "RISIKO BOROS",
                statusClass = "risk";
            if (score >= 80) {
                status = "AMAN";
                statusClass = "safe";
            } else if (score >= 50) {
                status = "PERLU DIJAGA";
                statusClass = "watch";
            }
            const insight =
                score >= 80
                    ? "Budget masih aman. Pertahankan ritme pengeluaran bulan ini."
                    : score >= 50
                    ? "Pengeluaran mulai mendekati batas. Jaga transaksi besar berikutnya."
                    : "Budget berisiko habis. Prioritaskan kebutuhan utama dulu.";
            this.budget = {
                limit,
                used,
                left,
                usedPct,
                score,
                status,
                statusClass,
                insight,
            };
        },
        getEmoji(cat) {
            const map = {
                MAKANAN: "🍜",
                FOOD: "🍜",
                "FOOD & BEVERAGE": "🍜",
                TRANSPORTASI: "🚗",
                TRANSPORT: "🚗",
                TAGIHAN: "⚡",
                BILLS: "⚡",
                UTILITY: "⚡",
                BELANJA: "🛍️",
                SHOPPING: "🛍️",
                GAJI: "💰",
                SALARY: "💰",
                INCOME: "💰",
                FREELANCE: "💻",
                KESEHATAN: "💊",
                HEALTH: "💊",
                MAKAN: "🍜",
            };
            return map[cat?.toUpperCase()] || "📄";
        },
        updateInsight() {
            this.insightType = "info";
            this.insightText =
                "Belum ada insight. Mulai catat transaksimu agar Zaku bisa memberikan insight.";
            this.insightDetail = "";
            if (this.budget?.limit > 0 && this.budget?.score <= 50) {
                this.insightType = "warning";
                this.insightText = "Pengeluaran mendekati batas budget.";
                this.insightDetail =
                    "Terpakai Rp " +
                    this.formatNumber(this.budget.used) +
                    " · Sisa Rp " +
                    this.formatNumber(this.budget.left);
                return;
            }
            if (this.maxCategory && this.maxCategoryAmount > 0) {
                this.insightType = "info";
                this.insightText =
                    this.maxCategory.name +
                    " mengambil " +
                    this.maxCategoryPct +
                    "% dari total pengeluaran bulan ini.";
                this.insightDetail =
                    "Total Rp " + this.formatNumber(this.maxCategoryAmount);
            }
        },
        async fetchDashboard() {
            try {
                const res = await window.apiClient.get("/v1/dashboard");
                const data = res.data.data;
                this.balance = data.current_month_balance || 0;
                this.income = data.total_income || 0;
                this.expense = data.total_expense || 0;
                this.updateBudget(data);
                if (data.recent_transactions)
                    this.transactions = data.recent_transactions;
                if (data.expense_by_category) {
                    const total = data.expense_by_category.reduce(
                        (s, c) => s + (c.amount || 0),
                        0
                    );
                    this.categories = data.expense_by_category
                        .filter((c) => (c.amount || 0) > 0)
                        .map((c) => ({
                            ...c,
                            name: c.category_name || c.name || "LAINNYA",
                            icon: c.category_icon || c.icon || "📌",
                            pct:
                                total > 0
                                    ? Math.round((c.amount / total) * 100)
                                    : 0,
                            emoji: this.getEmoji(c.category_name || c.name),
                        }));
                    if (this.categories.length > 0) {
                        const maxCat = this.categories.reduce((max, cat) =>
                            cat.amount > max.amount ? cat : max
                        );
                        this.maxCategory = maxCat;
                        this.maxCategoryAmount = maxCat.amount;
                        this.maxCategoryPct = maxCat.pct;
                    }
                    this.updateInsight();
                }
                if (!data.expense_by_category) this.updateInsight();
            } catch (e) {
                window.utils.handleApiError(e, "Gagal memuat data dashboard");
            } finally {
                this.loading.balance = false;
                this.loading.transactions = false;
                this.loading.categories = false;
                this.loading.budget = false;
            }
        },
    }));

    // ── Chat Page ──
    Alpine.data("chatPage", () => ({
        user: window.auth.getUser(),
        loading: false,
        message: "",
        charCount: 0,
        typing: false,
        STORAGE_KEY: "zaku_chat_history",
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
            } catch {
                /* ignore */
            }
            this.messages.push({
                role: "ai",
                html: true,
                content:
                    "Halo, <strong>" +
                    (window.auth.getUser()?.name?.split(" ")[0] || "Teman") +
                    '</strong>! 👋 Saya bisa bantu catat <strong>pemasukan</strong> dan <strong>pengeluaran</strong> kamu.<br><br>Ketik aja transaksinya, misalnya:<br><em>"Tadi beli makan siang 35rb"</em> <span style="color:rgba(17,16,16,.4)">← pengeluaran</span><br><em>"Gajian bulan ini 5 juta"</em> <span style="color:rgba(17,16,16,.4)">← pemasukan</span><br><em>"Bayar Grab ke kantor 28 ribu"</em> <span style="color:rgba(17,16,16,.4)">← pengeluaran</span><div class="chips"><div class="chip" @click="sendQuick(\'Beli makan siang 35rb\')">🍜 Makan 35rb</div><div class="chip" @click="sendQuick(\'Bayar Grab 28 ribu\')">🚗 Grab 28rb</div><div class="chip" @click="sendQuick(\'Terima gaji 7.5 juta\')">💰 Gaji</div></div>',
                time: "09:00",
            });
        },
        persist() {
            try {
                localStorage.setItem(
                    this.STORAGE_KEY,
                    JSON.stringify(this.messages)
                );
            } catch {
                /* quota exceeded etc */
            }
        },
        updateCharCount() {
            this.charCount = this.message.length;
        },
        handleKey(event) {
            if (event.key === "Enter" && !event.shiftKey) {
                event.preventDefault();
                this.sendMsg();
            }
        },
        now() {
            return new Date().toLocaleTimeString("id-ID", {
                hour: "2-digit",
                minute: "2-digit",
            });
        },
        scrollBottom() {
            this.$nextTick(() => {
                const el = this.$refs.msgs;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },
        async sendMsg() {
            const val = this.message.trim();
            if (!val || this.loading) return;
            this.message = "";
            this.charCount = 0;
            this.messages.push({
                role: "usr",
                html: false,
                content: val,
                time: this.now(),
            });
            this.persist();
            this.typing = true;
            this.scrollBottom();
            this.loading = true;
            try {
                const res = await window.apiClient.post("/v1/ai/chat", {
                    message: val,
                });
                const data = res.data;
                let bubbleHtml = "";
                if (data.data) {
                    const inner = data.data;
                    if (inner.response)
                        bubbleHtml = this.escapeHtml(inner.response);
                    if (inner.amount && inner.description) {
                        const sign = inner.type === "income" ? "inc" : "exp";
                        bubbleHtml +=
                            '<div class="confirm-card">' +
                            '<div class="confirm-row"><span class="confirm-key">DESKRIPSI</span><span class="confirm-val">' +
                            this.escapeHtml(inner.description) +
                            "</span></div>" +
                            '<div class="confirm-row"><span class="confirm-key">JUMLAH</span><span class="confirm-val ' +
                            sign +
                            '">' +
                            this.escapeHtml(
                                inner.amount_formatted || inner.amount
                            ) +
                            "</span></div>";
                        if (inner.category)
                            bubbleHtml +=
                                '<div class="confirm-row"><span class="confirm-key">KATEGORI</span><span class="confirm-val">' +
                                this.escapeHtml(inner.category) +
                                "</span></div>";
                        bubbleHtml +=
                            '<div class="confirm-row"><span class="confirm-key">TIPE</span><span class="confirm-val ' +
                            sign +
                            '">' +
                            (inner.type === "income"
                                ? "↑ PEMASUKAN"
                                : "↓ PENGELUARAN") +
                            "</span></div></div>";
                    } else if (inner.message)
                        bubbleHtml = this.escapeHtml(inner.message);
                } else if (data.response)
                    bubbleHtml = this.escapeHtml(data.response);
                else if (data.message)
                    bubbleHtml = this.escapeHtml(data.message);
                if (!bubbleHtml)
                    bubbleHtml =
                        '<em style="color: #999;">Maaf, tidak ada respons dari server. Coba lagi ya!</em>';
                this.messages.push({
                    role: "ai",
                    html: true,
                    content: bubbleHtml,
                    time: this.now(),
                });
            } catch (e) {
                const errorMsg = window.utils.parseApiError(
                    e,
                    "Maaf, lagi ada gangguan. Coba lagi ya!"
                );
                this.messages.push({
                    role: "ai",
                    html: false,
                    content: errorMsg,
                    time: this.now(),
                });
            } finally {
                this.persist();
                this.typing = false;
                this.loading = false;
                this.scrollBottom();
            }
        },
        sendQuick(text) {
            this.message = text;
            this.charCount = text.length;
            this.sendMsg();
        },
        async clearChat() {
            const ok = await window.utils.confirmDialog({
                title: "Hapus Pesan?",
                message: "Semua riwayat chat akan dibersihkan.",
                okLabel: "YA, HAPUS",
                danger: false,
            });
            if (!ok) return;
            localStorage.removeItem(this.STORAGE_KEY);
            this.messages = [];
            this.messages.push({
                role: "ai",
                html: true,
                content: "Chat dibersihkan. Ada transaksi yang mau dicatat? 😊",
                time: "Sekarang",
            });
        },
        escapeHtml(text) {
            const d = document.createElement("div");
            d.textContent = text;
            return d.innerHTML;
        },
    }));

    // ── Transaction Detail ──
    Alpine.data("transactionDetail", (id) => ({
        id: id,
        transaction: null,
        loading: true,
        editing: false,
        editCategory: "",
        categories: [],
        saving: false,
        async init() {
            this.fetchDetail();
            this.fetchCategories();
        },
        formatNumber(n) {
            if (!n) return "0";
            return Number(n).toLocaleString("id-ID");
        },
        formatDate(d) {
            if (!d) return "";
            const date = new Date(d);
            return date.toLocaleDateString("id-ID", {
                day: "numeric",
                month: "long",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            });
        },
        async fetchDetail() {
            try {
                const res = await window.apiClient.get(
                    "/v1/transactions/" + this.id
                );
                this.transaction = res.data.data;
            } catch (e) {
                window.utils.handleApiError(e, "Gagal memuat detail transaksi");
            } finally {
                this.loading = false;
            }
        },
        async fetchCategories() {
            try {
                const res = await window.apiClient.get("/v1/categories");
                const data = res.data;
                if (
                    data.data &&
                    Array.isArray(data.data) &&
                    data.data.length > 0
                ) {
                    this.categories = data.data;
                }
                if (this.categories.length === 0) {
                    this.categories = [
                        { name: "MAKANAN", icon: "🍜" },
                        { name: "TRANSPORTASI", icon: "🚗" },
                        { name: "TAGIHAN", icon: "⚡" },
                        { name: "BELANJA", icon: "🛍️" },
                        { name: "GAJI", icon: "💰" },
                        { name: "KESEHATAN", icon: "💊" },
                        { name: "FREELANCE", icon: "💻" },
                        { name: "LAINNYA", icon: "📌" },
                    ];
                }
            } catch {
                /* fallback categories below */
            }
        },
        startEdit() {
            this.editCategory =
                this.transaction?.category_name ||
                this.transaction?.category ||
                "LAINNYA";
            this.editing = true;
        },
        async saveEdit() {
            if (!this.editCategory || this.saving) return;
            this.saving = true;
            try {
                const res = await window.apiClient.put(
                    "/v1/transactions/" + this.id,
                    { category: this.editCategory }
                );
                const data = res.data.data;
                this.transaction.category_name = data.category_name;
                this.transaction.category = data.category_name;
                this.transaction.category_icon = data.category_icon;
                this.editing = false;
                window.utils.showToast(
                    "success",
                    "Kategori berhasil diperbarui!"
                );
            } catch (e) {
                window.utils.handleApiError(e, "Gagal memperbarui kategori");
            } finally {
                this.saving = false;
            }
        },
        async deleteTransaction() {
            const ok = await window.utils.confirmDialog({
                title: "Hapus Transaksi?",
                message:
                    "Semua data transaksi ini akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.",
                okLabel: "YA, HAPUS",
                danger: true,
            });
            if (!ok) return;
            try {
                await window.apiClient.delete("/v1/transactions/" + this.id);
                window.utils.showToast(
                    "success",
                    "Transaksi berhasil dihapus!"
                );
                window.location.href = "/transactions";
            } catch (e) {
                window.utils.handleApiError(
                    e,
                    "Gagal menghapus transaksi. Coba lagi!"
                );
            }
        },
    }));

    // ── Transactions List ──
    Alpine.data("transactionList", () => ({
        transactions: [],
        loading: true,
        page: 1,
        hasMore: false,
        total: 0,
        async init() {
            this.fetch();
        },
        async fetch() {
            this.loading = true;
            try {
                const res = await window.apiClient.get(
                    "/v1/transactions?limit=20&page=" + this.page
                );
                const data = res.data.data;
                this.transactions = data.groups || [];
                this.total = data.meta?.total || 0;
                this.hasMore = data.meta?.has_more || false;
            } catch (e) {
                window.utils.showToast("error", "Gagal memuat transaksi");
            } finally {
                this.loading = false;
            }
        },
        loadMore() {
            if (!this.hasMore || this.loading) return;
            this.page++;
            this.fetch();
        },
        formatNumber(n) {
            if (!n) return "0";
            return Number(n).toLocaleString("id-ID");
        },
        formatDate(d) {
            if (!d) return "";
            return new Date(d).toLocaleDateString("id-ID", {
                day: "numeric",
                month: "short",
            });
        },
        getEmoji(cat) {
            const map = {
                MAKANAN: "🍜",
                FOOD: "🍜",
                TRANSPORTASI: "🚗",
                TAGIHAN: "⚡",
                BELANJA: "🛍️",
                GAJI: "💰",
                SALARY: "💰",
                FREELANCE: "💻",
                KESEHATAN: "💊",
                MAKAN: "🍜",
            };
            return map[cat?.toUpperCase()] || "📄";
        },
    }));

    // ── Profile Page ──
    Alpine.data("profilePage", () => ({
        user: window.auth.getUser(),
        budget: { monthly_budget: 0, budget_used: 0 },
        loading: true,
        async init() {
            await this.fetchProfile();
        },
        async fetchProfile() {
            this.loading = true;
            try {
                const res = await window.apiClient.get("/v1/user/profile");
                const data = res.data.data;
                this.user = { ...this.user, ...data };
                window.auth.setUser(this.user);
                this.budget = data.budget || {
                    monthly_budget: 0,
                    budget_used: 0,
                };
            } catch (e) {
                window.utils.showToast("error", "Gagal memuat profil");
            } finally {
                this.loading = false;
            }
        },
        async logout() {
            try {
                await window.apiClient.post("/v1/auth/logout");
            } catch (e) {
                /* ignore */
            }
            window.auth.clearToken();
            window.location.href = "/login";
        },
        async saveBudget() {
            try {
                await window.apiClient.put("/v1/user/budget", {
                    monthly_budget: this.budget.monthly_budget,
                });
                window.utils.showToast("success", "Budget berhasil diperbarui");
            } catch (e) {
                window.utils.showToast(
                    "error",
                    window.utils.parseApiError(e, "Gagal menyimpan budget")
                );
            }
        },
    }));

    // ── Changelog Page ──
    Alpine.data("changelogPage", () => ({
        logs: [],
        loading: true,
        async init() {
            await this.fetch();
        },
        formatDate(d) {
            if (!d) return "";
            return new Date(d).toLocaleDateString("id-ID", {
                day: "numeric",
                month: "long",
                year: "numeric",
            });
        },
        async fetch() {
            try {
                const res = await window.apiClient.get("/v1/changelogs");
                this.logs = res.data.data || [];
            } catch (e) {
                window.utils.showToast("error", "Gagal memuat changelog");
            } finally {
                this.loading = false;
            }
        },
    }));
}
