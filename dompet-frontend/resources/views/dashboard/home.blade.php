@extends('layouts.app')

@section('content')
    <div x-data="dashboardHome()" style="display:flex;flex-direction:column;height:100%;">
        <header class="dash-header" x-data="{ user: window.auth.getUser() }">
            <div class="dh-row">
                <div>
                    <div class="dh-greet" x-text="greeting()"></div>
                    <div class="dh-name" x-text="user?.name || 'Teman'"></div>
                </div>
                <a href="/profile" class="dh-avatar" x-text="user?.name?.charAt(0).toUpperCase() || '?'"></a>
            </div>
        </header>

        <div class="screen-body">
            <div class="balance-card">
                <div class="bc-label">SELISIH BULAN INI</div>
                <template x-if="loading.balance">
                    <div class="bc-amount" style="background:rgba(17,16,16,.1);height:44px;width:60%;"></div>
                </template>
                <template x-if="!loading.balance">
                    <div class="bc-amount" x-text="'Rp ' + formatNumber(balance)">Rp 3.250.000</div>
                </template>
                <div class="bc-stats">
                    <div class="bc-stat">
                        <div class="bc-stat-label">
                            <div class="dot" style="background:#00A36B;border-color:#00A36B"></div>PEMASUKAN
                        </div>
                        <div class="bc-stat-val" x-text="'Rp ' + formatNumber(income)">Rp 7.500.000</div>
                    </div>
                    <div class="bc-stat">
                        <div class="bc-stat-label">
                            <div class="dot" style="background:var(--punch);border-color:var(--punch)"></div>PENGELUARAN
                        </div>
                        <div class="bc-stat-val" x-text="'Rp ' + formatNumber(expense)">Rp 4.250.000</div>
                    </div>
                </div>
            </div>

            <div class="budget-box budget-health-box">
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

                <template x-if="!loading.budget && budget.limit > 0">
                    <div>
                        <div class="budget-top">
                            <div>
                                <div class="budget-label">BUDGET HEALTH</div>
                                <div class="budget-amount" x-text="budget.status">AMAN</div>
                            </div>
                            <div class="budget-pct" x-text="budget.score">80</div>
                        </div>
                        <div class="budget-track">
                            <div class="budget-fill" :class="budget.statusClass" :style="'width:' + budget.usedPct + '%'"></div>
                        </div>
                        <div class="budget-foot">
                            <span x-text="'TERPAKAI Rp ' + formatNumber(budget.used)">TERPAKAI Rp 0</span>
                            <span x-text="'SISA Rp ' + formatNumber(budget.left)">SISA Rp 0</span>
                        </div>
                        <div class="budget-health-note" x-text="budget.insight"></div>
                    </div>
                </template>

                <template x-if="!loading.budget && budget.limit <= 0">
                    <div class="budget-empty">
                        <div>
                            <div class="budget-label">BUDGET HEALTH</div>
                            <div class="budget-amount">Budget belum diatur</div>
                            <div class="budget-health-note">Atur budget bulanan agar Zaku bisa membaca kondisi pengeluaranmu.</div>
                        </div>
                        <a href="/profile" class="btn-tiny budget-setup-btn">ATUR</a>
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
                                x-text="(trx.type === 'expense' ? '-' : '+') + 'Rp ' + formatNumber(trx.amount)"></div>
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
                                        <div class="cat-bar-amount" x-text="'Rp ' + formatNumber(cat.amount)">Rp 0</div>
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
                insightType: 'info',
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
                loading: {
                    balance: true,
                    transactions: true,
                    categories: true,
                    budget: true
                },
                async init() {
                    this.fetchDashboard();
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
                        const res = await window.apiClient.get('/dashboard');
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
                        this.loading.budget = false;
                    }
                },
            }
        }
    </script>
@endsection
