@extends('layouts.app')

@section('content')
    <div x-data="monthlyRecapPage()" style="display:flex;flex-direction:column;height:100%;">

        <!-- ===== HEADER (dark bg) ===== -->
        <div class="rekap-top">
            <div class="rekap-top-row">
                <a href="/dashboard" class="rekap-back">←</a>
                <div style="text-align:right">
                    <div class="rekap-title">Rekapan.</div>
                    <div class="rekap-sub">LAPORAN KEUANGAN BULANAN</div>
                </div>
            </div>
            <!-- Month Picker -->
            <div class="month-picker">
                <button class="mp-arrow" @click="changeMonth(-1)">‹</button>
                <div class="mp-label" x-text="monthLabel"></div>
                <button class="mp-arrow" @click="changeMonth(1)">›</button>
            </div>
        </div>

        <!-- ===== SCROLLABLE BODY ===== -->
        <div class="screen-body" style="padding-top:0;padding-bottom:20px">

            {{-- Loading State --}}
            <template x-if="loading">
                <div style="padding:20px 16px;">
                    <x-loading-skeleton count="5" />
                </div>
            </template>

            {{-- Main Content --}}
            <template x-if="!loading">
                <div>

                    <!-- 3-col summary strip -->
                    <div class="rekap-summary-strip">
                        <div class="rss-item">
                            <div class="rss-label">PEMASUKAN</div>
                            <div class="rss-val inc" x-text="'Rp ' + formatCompact(recap.total_income)"></div>
                            <div class="rss-delta" :class="deltaClass(recap.summary_delta.income)">
                                <span x-text="deltaArrow(recap.summary_delta.income)"></span>
                                <span x-text="formatDelta(recap.summary_delta.income)"></span>
                            </div>
                        </div>
                        <div class="rss-item rss-hl">
                            <div class="rss-label">SELISIH</div>
                            <div class="rss-val" x-text="'Rp ' + formatCompact(recap.net_cashflow)"></div>
                            <div class="rss-delta" :class="deltaClass(recap.summary_delta.savings)">
                                <span x-text="deltaArrow(recap.summary_delta.savings)"></span>
                                <span x-text="formatDelta(recap.summary_delta.savings)"></span>
                            </div>
                        </div>
                        <div class="rss-item">
                            <div class="rss-label">PENGELUARAN</div>
                            <div class="rss-val exp" x-text="'Rp ' + formatCompact(recap.total_expense)"></div>
                            <div class="rss-delta" :class="deltaClass(recap.summary_delta.expense)">
                                <span x-text="deltaArrow(recap.summary_delta.expense)"></span>
                                <span x-text="formatDelta(recap.summary_delta.expense)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Savings Rate -->
                    <div class="rekap-sec">
                        <div class="rekap-sec-title">SAVING RATE</div>
                        <div class="savings-box">
                            <div class="savings-pct" x-text="recap.savings_rate + '%'"></div>
                            <div>
                                <div class="savings-title" x-text="savingsTitle"></div>
                                <div class="savings-desc" x-text="savingsDesc"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly spending chart -->
                    <div class="rekap-sec">
                        <div class="rekap-sec-title">PENGELUARAN PER MINGGU</div>
                        <div class="week-chart-wrap">
                            <div class="week-chart" x-init="$nextTick(() => { $el.querySelectorAll('.wk-bar').forEach((b, i) => { b.style.setProperty('--h', b.style.height); b.classList.add('animated'); b.style.animationDelay = (i * 0.1) + 's'; }) })">
                                <template x-for="week in recap.week_expenses" :key="week.week">
                                    <div class="wk-col">
                                        <div class="wk-tooltip">
                                            <div x-text="week.label + ': Rp ' + formatNumber(week.amount)"></div>
                                            <div x-show="week.start" style="opacity:.6;font-size:8px" x-text="week.start + ' – ' + week.end"></div>
                                        </div>
                                        <div class="wk-bar-wrap">
                                            <div class="wk-bar"
                                                 :class="{ 'wk-active': week.amount === recap.week_max, 'wk-high': week.amount === recap.week_max }"
                                                 :style="'height:' + weekBarHeight(week.amount)">
                                            </div>
                                        </div>
                                        <div class="wk-lbl" :class="{ 'wk-active': week.amount === recap.week_max }" x-text="week.label"></div>
                                    </div>
                                </template>
                                <template x-if="recap.week_expenses.length === 0">
                                    <div style="flex:1;text-align:center;padding:20px 0;font-family:var(--font-mono);font-size:10px;color:rgba(17,16,16,.35);letter-spacing:1px;">TIDAK ADA DATA</div>
                                </template>
                            </div>
                            <div class="week-legend">
                                <div class="wl-item"><div class="wl-dot punch"></div>TERTINGGI</div>
                                <div class="wl-item"><div class="wl-dot ink"></div>NORMAL</div>
                            </div>
                        </div>
                    </div>

                    <!-- Category breakdown: Expense -->
                    <div class="rekap-sec">
                        <div class="rekap-sec-title">
                            <span>PENGELUARAN PER KATEGORI</span>
                            <span style="color:var(--punch)" x-text="'Rp ' + formatCompact(recap.total_expense)"></span>
                        </div>
                        <template x-if="recap.expense_by_category.length > 0">
                            <div class="hbar-list">
                                <template x-for="(cat, idx) in recap.expense_by_category" :key="cat.category_name">
                                    <div class="hbar-item">
                                        <div class="hbar-top">
                                            <div class="hbar-name">
                                                <span x-text="cat.category_icon"></span>
                                                <span x-text="cat.category_name"></span>
                                            </div>
                                            <div class="hbar-amount" x-text="'Rp ' + formatCompact(cat.amount) + ' · ' + cat.percentage + '%'"></div>
                                        </div>
                                        <div class="hbar-track">
                                            <div class="hbar-fill" :class="'f' + ((idx % 5) + 1)" :style="'width:' + cat.percentage + '%'"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="recap.expense_by_category.length === 0">
                            <div class="rekap-empty-state">
                                <div class="rekap-empty-state-icon">💸</div>
                                <div class="rekap-empty-state-text">BELUM ADA DATA PENGELUARAN</div>
                            </div>
                        </template>
                    </div>

                    <!-- VS Bulan Lalu (Comparison) -->
                    <div class="rekap-sec">
                        <div class="rekap-sec-title" x-text="'VS BULAN LALU (' + recap.prev_month_label.toUpperCase() + ')'"></div>
                        <template x-if="recap.comparison.length > 0">
                            <div class="compare-list">
                                <template x-for="cmp in recap.comparison" :key="cmp.category_name">
                                    <div class="cmp-row">
                                        <div class="cmp-ico" x-text="cmp.category_icon"></div>
                                        <div class="cmp-body">
                                            <div class="cmp-name" x-text="cmp.category_name"></div>
                                            <div class="cmp-bars">
                                                <div class="cmp-bar-row">
                                                    <div class="cmp-bar-label" x-text="recap.month_label.toUpperCase()"></div>
                                                    <div class="cmp-track">
                                                        <div class="cmp-fill this" :style="'width:' + cmpBarWidth(cmp.current_amount, cmp.prev_amount) + '%'"></div>
                                                    </div>
                                                    <div class="cmp-val" x-text="'Rp ' + formatCompact(cmp.current_amount)"></div>
                                                </div>
                                                <div class="cmp-bar-row">
                                                    <div class="cmp-bar-label" x-text="recap.prev_month_label.substring(0, 3).toUpperCase()"></div>
                                                    <div class="cmp-track">
                                                        <div class="cmp-fill prev" :style="'width:' + cmpPrevBarWidth(cmp.current_amount, cmp.prev_amount) + '%'"></div>
                                                    </div>
                                                    <div class="cmp-val" style="color:rgba(17,16,16,.4)" x-text="'Rp ' + formatCompact(cmp.prev_amount)"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cmp-delta" :class="cmp.delta >= 0 ? 'up' : 'down'" x-text="(cmp.delta >= 0 ? '+' : '') + cmp.delta + '%'"></div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="recap.comparison.length === 0">
                            <div class="rekap-empty-state">
                                <div class="rekap-empty-state-icon">📊</div>
                                <div class="rekap-empty-state-text">TIDAK ADA DATA PERBANDINGAN</div>
                            </div>
                        </template>
                    </div>

                    <!-- Top 5 Pengeluaran Terbesar -->
                    <div class="rekap-sec">
                        <div class="rekap-sec-title">TOP PENGELUARAN TERBESAR</div>
                        <template x-if="recap.top_expenses.length > 0">
                            <div class="top-list">
                                <template x-for="(tx, idx) in recap.top_expenses" :key="tx.id">
                                    <a :href="'/transactions/' + tx.id" class="top-item" style="text-decoration:none;color:inherit;">
                                        <div class="top-rank" x-text="idx + 1"></div>
                                        <div class="top-info">
                                            <div class="top-name" x-text="tx.description || 'Tanpa deskripsi'"></div>
                                            <div class="top-cat" x-text="tx.category_name + ' · ' + tx.date.toUpperCase()"></div>
                                        </div>
                                        <div class="top-amt" x-text="'Rp ' + formatNumber(tx.amount)"></div>
                                    </a>
                                </template>
                            </div>
                        </template>
                        <template x-if="recap.top_expenses.length === 0">
                            <div class="rekap-empty-state">
                                <div class="rekap-empty-state-icon">📝</div>
                                <div class="rekap-empty-state-text">BELUM ADA TRANSAKSI BESAR</div>
                            </div>
                        </template>
                    </div>

                    <!-- AI Insight Section -->
                    <div class="rekap-sec">
                        <div class="rekap-sec-title">
                            <span>ZAKU AI · INSIGHT BULAN INI</span>
                            <span style="color:var(--mint);font-size:8px">● LIVE</span>
                        </div>
                        <div class="ai-insight-card">
                            <div class="ai-ins-header">
                                <div class="ai-ins-avatar">AI</div>
                                <div>
                                    <div class="ai-ins-name">ZAKU AI</div>
                                    <div class="ai-ins-sub" x-text="'Analisis keuangan ' + monthLabel"></div>
                                </div>
                                <button class="ai-ins-refresh" @click="fetchMonthlyRecap()">↻ REFRESH</button>
                            </div>

                            <template x-if="recap.ai_insights.length > 0">
                                <div class="ai-ins-body">
                                    <div class="ai-ins-points">
                                        <template x-for="insight in recap.ai_insights" :key="insight.title">
                                            <div class="ai-ins-point" :class="insight.type">
                                                <div class="aip-icon" x-text="insight.icon"></div>
                                                <div>
                                                    <div class="aip-title" x-text="insight.title"></div>
                                                    <div class="aip-desc" x-text="insight.description"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- AI Summary -->
                                    <div class="ai-ins-summary">
                                        <div class="ais-label">// RINGKASAN AI</div>
                                        <div class="ais-text" x-text="aiSummaryText"></div>
                                        <div class="ais-score">
                                            <span class="ais-score-label">SKOR KEUANGAN</span>
                                            <span class="ais-score-val"><span x-text="recap.financial_score"></span><span>/100</span></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="recap.ai_insights.length === 0">
                                <div class="ai-ins-body">
                                    <div style="text-align:center;padding:20px 0;">
                                        <div style="font-size:24px;margin-bottom:8px;opacity:.4;">🤖</div>
                                        <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:1.5px;color:rgba(17,16,16,.35);">
                                            BELUM ADA INSIGHT UNTUK BULAN INI
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div style="height:20px"></div>
                </div>
            </template>
        </div>
    </div>

    <script>
        function monthlyRecapPage() {
            return {
                recap: {
                    month_year: 'Loading...',
                    month_label: '',
                    prev_month_label: '',
                    total_income: 0,
                    total_expense: 0,
                    net_cashflow: 0,
                    savings_rate: 0,
                    days_in_month: 30,
                    summary_delta: { income: 0, expense: 0, savings: 0 },
                    week_expenses: [],
                    week_max: 0,
                    top_expenses: [],
                    expense_by_category: [],
                    income_by_category: [],
                    comparison: [],
                    ai_insights: [],
                    financial_score: 0
                },
                currentMonth: new Date().getMonth() + 1,
                currentYear: new Date().getFullYear(),
                loading: true,
                monthNames: {
                    1: 'Januari', 2: 'Februari', 3: 'Maret', 4: 'April',
                    5: 'Mei', 6: 'Juni', 7: 'Juli', 8: 'Agustus',
                    9: 'September', 10: 'Oktober', 11: 'November', 12: 'Desember'
                },
                get monthLabel() {
                    return (this.monthNames[this.currentMonth] || '').toUpperCase() + ' ' + this.currentYear;
                },
                get savingsTitle() {
                    const rate = this.recap.savings_rate;
                    if (rate >= 30) return 'Kamu nabung dengan baik! 🎉';
                    if (rate >= 10) return 'Nabung lumayan, bisa lebih! 💪';
                    if (rate > 0) return 'Nabung sedikit, ayo naikkan! 📈';
                    return 'Pengeluaran melebihi pemasukan! ⚠️';
                },
                get savingsDesc() {
                    const rate = this.recap.savings_rate;
                    const income = this.formatCompact(this.recap.total_income);
                    const saved = this.formatCompact(this.recap.net_cashflow);
                    if (rate > 0) {
                        return 'Dari total pemasukan Rp ' + income + ', kamu berhasil simpan Rp ' + saved + ' bulan ini.';
                    }
                    return 'Pengeluaran melebihi pemasukan bulan ini. Coba cek kembali.';
                },
                get aiSummaryText() {
                    if (!this.recap.ai_insights.length) return 'Belum ada analisis.';
                    const warns = this.recap.ai_insights.filter(i => i.type === 'warn').length;
                    const goods = this.recap.ai_insights.filter(i => i.type === 'good').length;
                    const rate = this.recap.savings_rate;
                    let text = 'Bulan ini saving rate ' + rate + '%.';
                    if (goods > 0) text += ' Ada ' + goods + ' hal positif.';
                    if (warns > 0) text += ' Perlu perhatian di ' + warns + ' area.';
                    text += ' Pertahankan yang baik, perbaiki yang belum optimal.';
                    return text;
                },
                async init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const monthParam = parseInt(urlParams.get('month'));
                    const yearParam = parseInt(urlParams.get('year'));

                    if (!isNaN(monthParam) && monthParam >= 1 && monthParam <= 12) {
                        this.currentMonth = monthParam;
                    }
                    if (!isNaN(yearParam) && yearParam >= 2000 && yearParam <= new Date().getFullYear() + 10) {
                        this.currentYear = yearParam;
                    } else {
                        const prevMonth = new Date();
                        prevMonth.setMonth(prevMonth.getMonth() - 1);
                        this.currentMonth = prevMonth.getMonth() + 1;
                        this.currentYear = prevMonth.getFullYear();
                    }

                    await this.fetchMonthlyRecap();
                },
                formatNumber(n) {
                    if (!n && n !== 0) return '0';
                    return Number(n).toLocaleString('id-ID');
                },
                formatCompact(n) {
                    if (!n && n !== 0) return '0';
                    const num = Number(n);
                    if (num >= 1000000) {
                        const juta = num / 1000000;
                        return (juta % 1 === 0 ? juta.toFixed(0) : juta.toFixed(1).replace('.0', '')) + 'jt';
                    }
                    if (num >= 1000) {
                        const ribu = num / 1000;
                        return (ribu % 1 === 0 ? ribu.toFixed(0) : ribu.toFixed(1).replace('.0', '')) + 'rb';
                    }
                    return num.toLocaleString('id-ID');
                },
                formatDelta(val) {
                    if (val === 0 || val == null) return 'sama';
                    const sign = val > 0 ? '+' : '';
                    return sign + val + '%';
                },
                deltaArrow(val) {
                    if (val === 0 || val == null) return '';
                    return val > 0 ? '↑' : '↓';
                },
                deltaClass(val) {
                    if (val === 0 || val == null) return '';
                    if (val > 0) return 'up';
                    return 'down';
                },
                weekBarHeight(amount) {
                    if (!this.recap.week_max || !amount) return '4%';
                    return Math.max(4, (amount / this.recap.week_max) * 100) + '%';
                },
                cmpBarWidth(current, prev) {
                    const max = Math.max(current, prev);
                    if (!max) return '0';
                    return (current / max * 100).toFixed(0);
                },
                cmpPrevBarWidth(current, prev) {
                    const max = Math.max(current, prev);
                    if (!max) return '0';
                    return (prev / max * 100).toFixed(0);
                },
                async fetchMonthlyRecap() {
                    this.loading = true;
                    try {
                        const res = await window.apiClient.get(
                            `/v1/dashboard/monthly-recap?month=${this.currentMonth}&year=${this.currentYear}`
                        );
                        const d = res.data.data;
                        this.recap = {
                            month_year: d.month_year || 'Bulan Tidak Diketahui',
                            month_label: d.month_label || this.monthNames[this.currentMonth].substring(0, 3),
                            prev_month_label: d.prev_month_label || '',
                            total_income: d.total_income || 0,
                            total_expense: d.total_expense || 0,
                            net_cashflow: d.net_cashflow || 0,
                            savings_rate: d.savings_rate || 0,
                            days_in_month: d.days_in_month || 30,
                            summary_delta: d.summary_delta || { income: 0, expense: 0, savings: 0 },
                            week_expenses: d.week_expenses || [],
                            week_max: d.week_max || 0,
                            top_expenses: d.top_expenses || [],
                            expense_by_category: d.expense_by_category || [],
                            income_by_category: d.income_by_category || [],
                            comparison: d.comparison || [],
                            ai_insights: d.ai_insights || [],
                            financial_score: d.financial_score || 0
                        };
                    } catch (e) {
                        console.error('Fetch monthly recap error:', e);
                        if (window.utils && window.utils.showToast) {
                            window.utils.showToast('error', 'Gagal memuat rekap bulanan');
                        }
                    } finally {
                        this.loading = false;
                    }
                },
                changeMonth(delta) {
                    let newMonth = this.currentMonth + delta;
                    let newYear = this.currentYear;

                    if (newMonth > 12) {
                        newMonth = 1;
                        newYear++;
                    } else if (newMonth < 1) {
                        newMonth = 12;
                        newYear--;
                    }
                    this.currentMonth = newMonth;
                    this.currentYear = newYear;
                    this.fetchMonthlyRecap();
                }
            }
        }
    </script>
@endsection
