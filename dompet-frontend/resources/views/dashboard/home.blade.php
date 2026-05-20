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

            <div class="insight-strip" style="cursor:pointer;">
                <div class="insight-icon">💡</div>
                <div class="insight-text">
                    Pengeluaran makanan minggu ini +23%
                    <span>Dibanding minggu lalu · Rp 385.000</span>
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
                    <template x-if="!loading.categories">
                        <template x-for="cat in categories" :key="cat.name">
                            <div class="cat-bar">
                                <div class="cat-bar-top">
                                    <div class="cat-bar-name"><span class="emo" x-text="cat.emoji">🍜</span> <span
                                            x-text="cat.name">MAKANAN</span></div>
                                    <div class="cat-bar-amount" x-text="'Rp ' + formatNumber(cat.amount)">Rp 0</div>
                                </div>
                                <div class="cat-bar-track">
                                    <div class="cat-bar-fill" :style="'width:' + cat.pct + '%'"></div>
                                </div>
                            </div>
                        </template>
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
                loading: {
                    balance: true,
                    transactions: true,
                    categories: true
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
                async fetchDashboard() {
                    try {
                        const res = await window.apiClient.get('/dashboard');
                        const data = res.data.data;
                        this.balance = data.current_month_balance || 0;
                        this.income = data.total_income || 0;
                        this.expense = data.total_expense || 0;
                        if (data.recent_transactions) {
                            this.transactions = data.recent_transactions;
                        }
                        if (data.expense_by_category) {
                            const total = data.expense_by_category.reduce((s, c) => s + (c.amount || 0), 0);
                            this.categories = data.expense_by_category.map(c => ({
                                ...c,
                                pct: total > 0 ? Math.round((c.amount / total) * 100) : 0,
                                emoji: this.getEmoji(c.name)
                            }));
                        }
                    } catch (e) {
                        console.error('Fetch dashboard error:', e);
                    } finally {
                        this.loading.balance = false;
                        this.loading.transactions = false;
                        this.loading.categories = false;
                    }
                },
            }
        }
    </script>
@endsection