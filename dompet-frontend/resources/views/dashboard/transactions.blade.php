@extends('layouts.app')

@section('content')
    <div x-data="transactionList()" style="display:flex;flex-direction:column;height:100%;">
        <div class="hist-top">
            <div class="hist-title">Riwayat.</div>
            <div class="hist-sub">SEMUA TRANSAKSI KAMU</div>
        </div>

        <div class="filter-scroll">
            <button class="filter-pill on" @click="setFilter('all', $el)">SEMUA</button>
            <button class="filter-pill" @click="setFilter('income', $el)">PEMASUKAN</button>
            <button class="filter-pill" @click="setFilter('expense', $el)">PENGELUARAN</button>
            <template x-for="cat in categories" :key="cat">
                <button class="filter-pill" @click="setFilter(cat, $el)" x-text="cat"></button>
            </template>
        </div>

        <div class="screen-body" style="padding-bottom:90px;padding-top:0">
            <template x-if="loading">
                <div style="padding:16px;">
                    <x-loading-skeleton count="5" />
                </div>
            </template>

            <template x-if="!loading && filtered().length === 0">
                <div style="padding:40px 16px;text-align:center;">
                    <div style="font-size:48px;margin-bottom:12px;">📭</div>
                    <span style="font-family:var(--font-mono);font-size:10px;color:rgba(17,16,16,.4);">TIDAK ADA
                        TRANSAKSI</span>
                    <div style="margin-top:8px;font-size:12px;color:rgba(17,16,16,.3);">Mulai catat pemasukan atau pengeluaran via AI Chat</div>
                </div>
            </template>

            <template x-for="(group, month) in grouped()" :key="month">
                <div class="month-group">
                    <div class="month-label" x-text="month"></div>
                    <div class="tx-list" style="padding:0 16px;gap:8px">
                        <template x-for="trx in group" :key="trx.id">
                            <a :href="'/transactions/' + trx.id" class="tx"
                                :class="trx.type === 'income' ? 'income' : 'expense'"
                                style="text-decoration:none;cursor:pointer;">
                                <div class="tx-cat-icon" x-text="getEmoji(trx.category_name)">📄</div>
                                <div class="tx-info">
                                    <div class="tx-desc" x-text="trx.description"></div>
                                    <div class="tx-meta">
                                        <span x-text="trx.category_name || 'UMUM'"></span>
                                        <span class="tx-meta-sep">·</span>
                                        <span x-text="formatDay(trx.created_at)"></span>
                                    </div>
                                </div>
                                <div class="tx-amt"
                                    x-text="(trx.type === 'expense' ? '-' : '+') + 'Rp ' + formatNumber(trx.amount)"></div>
                            </a>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
        function transactionList() {
            return {
                transactions: [],
                loading: true,
                filter: 'all',
                categories: [],
                async init() {
                    await this.fetchTransactions();
                    this.extractCategories();
                },
                formatNumber(n) {
                    if (!n) return '0';
                    return Number(n).toLocaleString('id-ID');
                },
                formatDay(d) {
                    if (!d) return '';
                    const date = new Date(d);
                    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
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
                extractCategories() {
                    const set = new Set();
                    this.transactions.forEach(t => {
                        if (t.category_name) set.add(t.category_name.toUpperCase());
                    });
                    this.categories = Array.from(set);
                },
                async fetchTransactions() {
                    try {
                        const res = await window.apiClient.get('/transactions');
                        const rawData = res.data.data || [];
                        let flatTx = [];
                        rawData.forEach(group => {
                            if (group.transactions) {
                                flatTx = flatTx.concat(group.transactions);
                            }
                        });
                        this.transactions = flatTx;
                    } catch (e) {
                        console.error('Fetch transactions error:', e);
                        window.utils.showToast('error', 'Gagal memuat riwayat transaksi');
                    } finally {
                        this.loading = false;
                    }
                },
                setFilter(f, el) {
                    this.filter = f;
                    document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('on'));
                    if (el) el.classList.add('on');
                },
                filtered() {
                    if (this.filter === 'all') return this.transactions;
                    return this.transactions.filter(t => {
                        if (this.filter === 'income' || this.filter === 'expense') return t.type === this.filter;
                        return t.category_name?.toUpperCase() === this.filter;
                    });
                },
                grouped() {
                    const groups = {};
                    const data = this.filtered();
                    data.forEach(t => {
                        const d = new Date(t.created_at);
                        const key = d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }).toUpperCase();
                        if (!groups[key]) groups[key] = [];
                        groups[key].push(t);
                    });
                    return groups;
                }
            }
        }
    </script>
@endsection