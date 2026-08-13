@extends('layouts.app')

@section('content')
    <div x-data="dashboardHome()" style="display:flex;flex-direction:column;height:100%;">
        <header class="dash-header" x-data="{ user: window.auth.getUser() }">
            <div class="dh-row">
                <div>
                    <div class="dh-greet" x-text="greeting()"></div>
                    <div class="dh-name" x-text="user?.name || 'Teman'"></div>
                </div>
                <div style="display:flex;align-items:center;gap:12px;">
                    <button @click="showAmount = !showAmount; localStorage.setItem('zaku_hide_amount', showAmount)" style="background:none;border:none;cursor:pointer;font-size:20px;">
                        <span x-text="showAmount ? '👁️' : '🙈'"></span>
                    </button>
                    <a href="/profile" class="dh-avatar" x-text="user?.name?.charAt(0).toUpperCase() || '?'"></a>
                </div>
            </div>
        </header>

        <div class="screen-body">
            <div class="balance-card">
                <div class="bc-label">SELISIH BULAN INI</div>
                <template x-if="loading.balance">
                    <div class="bc-amount" style="background:rgba(17,16,16,.1);height:44px;width:60%;"></div>
                </template>
                <template x-if="!loading.balance">
                    <div class="bc-amount" x-text="showAmount ? 'Rp ' + formatNumber(balance) : 'Rp ••••••'">Rp 3.250.000</div>
                </template>
                <div class="bc-stats">
                    <div class="bc-stat">
                        <div class="bc-stat-label">
                            <div class="dot" style="background:#00A36B;border-color:#00A36B"></div>PEMASUKAN
                        </div>
                        <div class="bc-stat-val" x-text="showAmount ? 'Rp ' + formatNumber(income) : 'Rp ••••••'">Rp 7.500.000</div>
                    </div>
                    <div class="bc-stat">
                        <div class="bc-stat-label">
                            <div class="dot" style="background:var(--punch);border-color:var(--punch)"></div>PENGELUARAN
                        </div>
                        <div class="bc-stat-val" x-text="showAmount ? 'Rp ' + formatNumber(expense) : 'Rp ••••••'">Rp 4.250.000</div>
                    </div>
                </div>
            </div>

            <div class="budget-box budget-health-box">
                {{-- Loading skeleton --}}
                <template x-if="loading.budget">
                    <div>
                        <div class="budget-top">
                            <div>
                                <div class="budget-label">BUDGET HEALTH</div>
                                <div class="budget-amount" style="background:rgba(17,16,16,.12);height:24px;width:150px;margin-top:6px;"></div>
                            </div>
                            <div class="budget-pct" style="background:rgba(17,16,16,.12);width:64px;height:44px;"></div>
                        </div>
                        <div class="budget-track">
                            <div class="budget-fill" style="width:40%;opacity:.35;"></div>
                        </div>
                    </div>
                </template>

                {{-- Per-category budgets exist --}}
                <template x-if="!loading.budget && typeof catBudgets !== 'undefined' && catBudgets && catBudgets.length > 0">
                    <div>
                        <div class="budget-top">
                            <div>
                                <div class="budget-label">BUDGET HEALTH</div>
                                <div class="budget-amount" x-text="catBudgetStatus.text">AMAN</div>
                            </div>
                            <div class="budget-pct" x-text="catBudgetScore">80</div>
                        </div>
                        <div class="budget-track">
                            <div class="budget-fill" :class="catBudgetStatus.cls" :style="'width:' + catBudgetPct + '%'"></div>
                        </div>
                        <div class="budget-foot">
                            <span x-text="'TERPAKAI Rp ' + formatNumber(catTotalSpent)">TERPAKAI Rp 0</span>
                            <span x-text="'SISA Rp ' + formatNumber(catTotalRemaining)">SISA Rp 0</span>
                        </div>
                        <div class="budget-health-note" x-text="catBudgetInsight"></div>

                        {{-- Per-category mini bars --}}
                        <div class="cat-budget-mini-list">
                            <template x-for="cb in catTopBudgets" :key="cb.name">
                                <div class="cat-budget-mini">
                                    <div class="cat-budget-mini-top">
                                        <span class="cat-budget-mini-name">
                                            <span x-text="catEmoji(cb.name)"></span>
                                            <span x-text="cb.name"></span>
                                        </span>
                                        <span class="cat-budget-mini-amt" x-text="'Rp ' + formatNumber(cb.spent) + ' / Rp ' + formatNumber(cb.amount)"></span>
                                    </div>
                                    <div class="budget-track" style="height:6px;">
                                        <div class="budget-fill" :class="{ 'safe': cb.status === 'aman', 'watch': cb.status === 'waspada', 'risk': cb.status === 'boros' || cb.status === 'melebihi' }" :style="'width:' + Math.min(100, cb.pct) + '%'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <a href="/budgets" class="btn-tiny budget-manage-btn">KELOLA BUDGET →</a>
                    </div>
                </template>

                {{-- No budgets yet --}}
                <template x-if="!loading.budget && (typeof catBudgets === 'undefined' || !catBudgets || catBudgets.length === 0)">
                    <div class="budget-empty">
                        <div>
                            <div class="budget-label">BUDGET HEALTH</div>
                            <div class="budget-amount">Belum ada budget</div>
                            <div class="budget-health-note">Atur budget per kategori agar bisa tracking pengeluaran.</div>
                        </div>
                        <a href="/budgets" class="btn-tiny budget-setup-btn">ATUR</a>
                    </div>
                </template>
            </div>

            <div class="insight-strip" style="cursor:pointer;">
                <div class="insight-icon" x-text="insightType === 'warning' ? '⚠️' : '💡'">💡</div>
                <div class="insight-text">
                    <template x-if="loading.categories || loading.budget">
                        <div style="opacity:.5">Memuat insight...</div>
                    </template>
                    <template x-if="!loading.categories && !loading.budget">
                        <div x-text="insightText">Belum ada insight. Mulai catat transaksimu agar Zaku bisa memberikan insight.</div>
                        <template x-if="insightDetail">
                            <span x-text="insightDetail" style="display:block;margin-top:6px;font-size:12px;color:rgba(17,16,16,.6)"></span>
                        </template>
                    </template>
                </div>
            </div>

            <div class="section mt16">
                <div class="section-row">
                    <div class="section-title">REKAPAN</div>
                    <button class="btn-tiny" onclick="window.location.href='/monthly-recap'">LIHAT REKAPAN BULANAN</button>
                </div>
            </div>

            <div class="section">
                <div class="section-row">
                    <div class="section-title">TRANSAKSI TERAKHIR</div>
                    <button class="btn-tiny" onclick="window.location.href='/transactions'">LIHAT SEMUA</button>
                </div>
                <div class="tx-list">
                    <template x-if="loading.transactions">
                        <div>
                            <x-loading-skeleton count="3" />
                        </div>
                    </template>
                    <template x-if="!loading.transactions && transactions.length === 0">
                        <div class="tx" style="justify-content:center;padding:24px;background:var(--cream);">
                            <span style="font-family:var(--font-mono);font-size:10px;color:rgba(17,16,16,.4);">BELUM ADA
                                TRANSAKSI</span>
                        </div>
                    </template>
                    <template x-for="trx in transactions" :key="trx.id">
                        <a :href="'/transactions/' + trx.id" class="tx"
                            :class="trx.type === 'income' ? 'income' : 'expense'"
                            style="text-decoration:none;cursor:pointer;">
                            <div class="tx-cat-icon" x-text="getEmoji(trx.category_name)">📄</div>
                            <div class="tx-info">
                                <div class="tx-desc" x-text="trx.description"></div>
                                <div class="tx-meta">
                                    <span x-text="trx.category_name || 'UMUM'"></span>
                                    <span class="tx-meta-sep">·</span>
                                    <span x-text="formatDate(trx.created_at)"></span>
                                </div>
                            </div>
                            <div class="tx-amt"
                                x-text="showAmount ? (trx.type === 'expense' ? '-' : '+') + 'Rp ' + formatNumber(trx.amount) : 'Rp ••••••'"></div>
                        </a>
                    </template>
                </div>
            </div>

            <div class="section mt16">
                <div class="section-row">
                    <div class="section-title">PENGELUARAN PER KATEGORI</div>
                </div>
                <div class="cat-bars">
                    <template x-if="!loading.categories && categories.length > 0">
                        <template x-for="cat in categories" :key="cat.name">
                            <div class="cat-bar" :class="cat.amount === maxCategoryAmount ? 'cat-bar-highlighted' : ''">
                                <div class="cat-bar-top">
                                    <div class="cat-bar-name"><span class="emo" x-text="cat.emoji">🍜</span> <span
                                            x-text="cat.name">MAKANAN</span></div>
                                    <div class="cat-bar-meta">
                                        <div class="cat-bar-amount" x-text="showAmount ? 'Rp ' + formatNumber(cat.amount) : 'Rp ••••••'">Rp 0</div>
                                        <div class="cat-bar-pct" x-text="cat.pct + '%'">42%</div>
                                    </div>
                                </div>
                                <div class="cat-bar-track">
                                    <div class="cat-bar-fill" :class="cat.amount === maxCategoryAmount ? 'highlighted' : ''" :style="'width:' + cat.pct + '%'"></div>
                                </div>
                            </div>
                        </template>
                    </template>
                    <template x-if="!loading.categories && categories.length > 0 && maxCategory">
                        <div class="cat-insight">
                            <div class="cat-insight-icon">💡</div>
                            <div class="cat-insight-text" x-text="maxCategory.name + ' mengambil ' + maxCategoryPct + '% dari total pengeluaran bulan ini.'">
                                Makanan mengambil 42% dari total pengeluaran bulan ini.
                            </div>
                        </div>
                    </template>
                    <template x-if="!categories || categories.length === 0">
                        <div class="cat-bar" style="justify-content:center;padding:24px;text-align:center;">
                            <span style="font-family:var(--font-mono);font-size:10px;color:rgba(17,16,16,.4);">BELUM ADA
                                DATA KATEGORI</span>
                        </div>
                    </template>
                </div>
            </div>



    <script>
        function greeting() {
            const h = new Date().getHours();
            let g = 'SELAMAT MALAM 🌙';
            if (h < 12) g = 'SELAMAT PAGI ☀️';
            else if (h < 15) g = 'SELAMAT SIANG 🌤️';
            else if (h < 18) g = 'SELAMAT SORE 🌅';
            return g;
        }

        function dashboardHome() {
            return {
                balance: 0,
                income: 0,
                expense: 0,
                transactions: [],
                categories: [],
                maxCategory: null,
                maxCategoryAmount: 0,
                maxCategoryPct: 0,
                insightText: '',
                insightDetail: '',
                showAmount: true,
                insightType: 'info',
                showAmount: localStorage.getItem('zaku_hide_amount') !== 'false', // Moved here
                budget: {
                    limit: 0,
                    used: 0,
                    left: 0,
                    usedPct: 0,
                    score: 0,
                    status: 'Budget belum diatur',
                    statusClass: 'risk',
                    insight: ''
                },
                catBudgets: [],
                catBudgetProgress: {},
                showAmount: localStorage.getItem('zaku_hide_amount') !== 'false',
                loading: {
                    balance: true,
                    transactions: true,
                    categories: true,
                    budget: true
                },
                async init() {
                    console.log('Init dashboard...');
                    await Promise.all([this.fetchDashboard(), this.fetchCatBudgets()]);
                },
                formatNumber(n) {
                    if (!n) return '0';
                    return Number(n).toLocaleString('id-ID');
                },
                formatDate(d) {
                    if (!d) return '';
                    const date = new Date(d);
                    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                },
                toNumber(value) {
                    const number = Number(value);
                    return Number.isFinite(number) ? number : 0;
                },
                clamp(value, min, max) {
                    return Math.min(Math.max(value, min), max);
                },
                readBudgetLimit(data) {
                    const user = window.auth.getUser() || {};
                    return this.toNumber(
                        data.monthly_budget
                        || data.budget_limit
                        || data.budget?.limit
                        || data.budget?.monthly_budget
                        || user.monthly_budget
                        || user.budget?.limit
                    );
                },
                updateBudget(data = {}) {
                    const limit = this.readBudgetLimit(data);
                    const used = this.toNumber(
                        data.budget_used
                        || data.used_budget
                        || data.budget?.used
                        || data.budget?.spent
                        || data.total_expense
                        || this.expense
                    );
                    const left = Math.max(0, this.toNumber(
                        data.remaining_budget
                        || data.budget_left
                        || data.budget?.left
                        || data.budget?.remaining
                        || (limit - used)
                    ));

                    if (limit <= 0) {
                        this.budget = {
                            limit: 0,
                            used,
                            left: 0,
                            usedPct: 0,
                            score: 0,
                            status: 'Budget belum diatur',
                            statusClass: 'risk',
                            insight: 'Atur budget bulanan agar Zaku bisa membaca kondisi pengeluaranmu.'
                        };
                        return;
                    }

                    const usedPct = this.clamp(Math.round((used / limit) * 100), 0, 100);
                    const score = this.clamp(100 - usedPct, 0, 100);
                    let status = 'RISIKO BOROS';
                    let statusClass = 'risk';

                    if (score >= 80) {
                        status = 'AMAN';
                        statusClass = 'safe';
                    } else if (score >= 50) {
                        status = 'PERLU DIJAGA';
                        statusClass = 'watch';
                    }

                    const insight = score >= 80
                        ? 'Budget masih aman. Pertahankan ritme pengeluaran bulan ini.'
                        : score >= 50
                            ? 'Pengeluaran mulai mendekati batas. Jaga transaksi besar berikutnya.'
                            : 'Budget berisiko habis. Prioritaskan kebutuhan utama dulu.';

                    this.budget = { limit, used, left, usedPct, score, status, statusClass, insight };
                },
                getEmoji(cat) {
                    const map = {
                        'MAKANAN': '🍜', 'FOOD': '🍜', 'FOOD & BEVERAGE': '🍜',
                        'TRANSPORTASI': '🚗', 'TRANSPORT': '🚗',
                        'TAGIHAN': '⚡', 'BILLS': '⚡', 'UTILITY': '⚡',
                        'BELANJA': '🛍️', 'SHOPPING': '🛍️',
                        'GAJI': '💰', 'SALARY': '💰', 'INCOME': '💰',
                        'FREELANCE': '💻',
                        'KESEHATAN': '💊', 'HEALTH': '💊',
                        'MAKAN': '🍜'
                    };
                    return map[cat?.toUpperCase()] || '📄';
                },
                updateInsight() {
                    // Default friendly message
                    this.insightType = 'info';
                    this.insightText = 'Belum ada insight. Mulai catat transaksimu agar Zaku bisa memberikan insight.';
                    this.insightDetail = '';

                    // If budget exists and is at risk, show budget warning
                    if (this.budget && this.budget.limit > 0 && this.budget.score <= 50) {
                        this.insightType = 'warning';
                        this.insightText = 'Pengeluaran mendekati batas budget.';
                        this.insightDetail = 'Terpakai Rp ' + this.formatNumber(this.budget.used) + ' · Sisa Rp ' + this.formatNumber(this.budget.left);
                        return;
                    }

                    // If there's a largest category, show category insight
                    if (this.maxCategory && this.maxCategoryAmount > 0) {
                        this.insightType = 'info';
                        this.insightText = this.maxCategory.name + ' mengambil ' + this.maxCategoryPct + '% dari total pengeluaran bulan ini.';
                        this.insightDetail = 'Total Rp ' + this.formatNumber(this.maxCategoryAmount);
                        return;
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
                        if (data.recent_transactions) {
                            this.transactions = data.recent_transactions;
                        }
                        if (data.expense_by_category) {
                            const total = data.expense_by_category.reduce((s, c) => s + (c.amount || 0), 0);
                            this.categories = data.expense_by_category
                                .filter(c => (c.amount || 0) > 0)
                                .map(c => ({
                                    ...c,
                                    name: c.category_name || c.name || 'LAINNYA',
                                    icon: c.category_icon || c.icon || '📌',
                                    pct: total > 0 ? Math.round((c.amount / total) * 100) : 0,
                                    emoji: this.getEmoji(c.category_name || c.name)
                                }));
                            
                            // Find max category
                            if (this.categories.length > 0) {
                                const maxCat = this.categories.reduce((max, cat) => 
                                    cat.amount > max.amount ? cat : max
                                );
                                this.maxCategory = maxCat;
                                this.maxCategoryAmount = maxCat.amount;
                                this.maxCategoryPct = maxCat.pct;
                            }
                            // Update insight after categories + budget processed
                            this.updateInsight();
                        }
                        // Ensure insight is updated even if there are no categories
                        if (!data.expense_by_category) this.updateInsight();
                    } catch (e) {
                        console.error('Fetch dashboard error:', e);
                        window.utils.showToast('error', 'Gagal memuat data dashboard');
                    } finally {
                        this.loading.balance = false;
                        this.loading.transactions = false;
                        this.loading.categories = false;
                    }
                },
                get catTotalBudget() { return this.catBudgets.reduce((s, b) => s + (b.amount || 0), 0); },
                get catTotalSpent() { return Object.values(this.catBudgetProgress).reduce((s, p) => s + (p.spent || 0), 0); },
                get catTotalRemaining() { return Math.max(0, this.catTotalBudget - this.catTotalSpent); },
                get catBudgetPct() { return this.catTotalBudget > 0 ? Math.min(100, Math.round((this.catTotalSpent / this.catTotalBudget) * 100)) : 0; },
                get catBudgetStatus() {
                    if (this.catBudgetPct >= 80) return { text: 'BOROS', cls: 'risk' };
                    if (this.catBudgetPct >= 60) return { text: 'WASPADA', cls: 'watch' };
                    return { text: 'AMAN', cls: 'safe' };
                },
                get catBudgetScore() { return this.catTotalBudget > 0 ? Math.max(0, 100 - this.catBudgetPct) : 0; },
                get catBudgetInsight() {
                    if (this.catTotalBudget <= 0) return 'Belum ada budget per kategori.';
                    if (this.catBudgetScore >= 80) return 'Budget masih aman. Pertahankan!';
                    if (this.catBudgetScore >= 50) return 'Pengeluaran mendekati batas. Jaga transaksi berikutnya.';
                    return 'Budget berisiko habis. Prioritaskan kebutuhan utama.';
                },
                get catTopBudgets() {
                    return this.catBudgets.slice(0, 4).map(b => {
                        const p = this.catBudgetProgress[b.id];
                        return {
                            name: b.category?.name || 'LAINNYA',
                            amount: b.amount || 0,
                            spent: p?.spent || 0,
                            pct: p?.percentage || 0,
                            status: p?.status || 'aman',
                        };
                    });
                },
                catEmoji(name) {
                    const map = { 'MAKANAN':'🍜','TRANSPORTASI':'🚗','TAGIHAN':'⚡','BELANJA':'🛍️','GAJI':'💰','FREELANCE':'💻','KESEHATAN':'💊' };
                    return map[(name||'').toUpperCase()] || '📄';
                },
                async fetchCatBudgets() {
                    console.log('Fetching budgets...');
                    try {
                        const res = await window.apiClient.get('/v1/budgets');
                        console.log('Budgets response:', res.data);
                        this.catBudgets = res.data.data || [];
                        console.log('catBudgets set:', this.catBudgets);
                        for (const b of this.catBudgets) {
                            try {
                                const pr = await window.apiClient.get('/v1/budgets/' + b.id + '/progress');
                                this.catBudgetProgress[b.id] = pr.data.data;
                            } catch { /* ignore */ }
                        }
                    } catch (e) {
                        console.error('Fetch budgets error:', e);
                    } finally {
                        this.loading.budget = false;
                    }
                },
            }
        }
    </script>
@endsection
