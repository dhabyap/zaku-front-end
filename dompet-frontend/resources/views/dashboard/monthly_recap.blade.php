@extends('layouts.app')

@section('content')
    <div x-data="monthlyRecapPage()" style="display:flex;flex-direction:column;height:100%;">
        <header class="dash-header">
            <div class="dh-row">
                <a href="/dashboard" class="back-btn">←</a>
                <div>
                    <div class="dh-greet">REKAPAN BULANAN</div>
                    <div class="dh-name" x-text="recap.month_year"></div>
                </div>
            </div>
        </header>

        <div class="screen-body">
            <div class="month-selector">
                <button @click="changeMonth(-1)">← Prev</button>
                <select x-model="currentMonth" @change="fetchRecapForSelectedMonth()">
                    <template x-for="(monthName, monthNum) in monthNames" :key="monthNum">
                        <option :value="monthNum" x-text="monthName"></option>
                    </template>
                </select>
                <input type="number" x-model.debounce.500ms="currentYear" @change="fetchRecapForSelectedMonth()">
                <button @click="changeMonth(1)">Next →</button>
            </div>

            <template x-if="loading.recap">
                <div class="p-4">
                    <x-loading-skeleton count="5" />
                </div>
            </template>
            <template x-if="!loading.recap">
                <div>
                    <div class="balance-card">
                        <div class="bc-label" x-text="recap.month_year"></div>
                        <div class="bc-stats">
                            <div class="bc-stat">
                                <div class="bc-stat-label">
                                    <div class="dot" style="background:#00A36B;border-color:#00A36B"></div>PEMASUKAN
                                </div>
                                <div class="bc-stat-val" x-text="'Rp ' + formatNumber(recap.total_income)"></div>
                            </div>
                            <div class="bc-stat">
                                <div class="dot" style="background:var(--punch);border-color:var(--punch)"></div>PENGELUARAN
                            </div>
                            <div class="bc-stat-val" x-text="'Rp ' + formatNumber(recap.total_expense)"></div>
                            </div>
                            <div class="budget-foot" style="margin-top:10px;">
                                <span>NET CASHFLOW</span>
                                <span x-text="'Rp ' + formatNumber(recap.net_cashflow)"></span>
                            </div>
                        </div>

                    <div class="section mt16">
                        <div class="section-row">
                            <div class="section-title">INSIGHT BULANAN</div>
                        </div>
                        <div class="insight-strip" x-show="recap.insight.text">
                            <div class="insight-icon" x-text="recap.insight.icon"></div>
                            <div class="insight-text">
                                <div x-text="recap.insight.text"></div>
                                <template x-if="recap.insight.subtext">
                                    <span x-text="recap.insight.subtext" style="display:block;margin-top:6px;font-size:12px;color:rgba(17,16,16,.6)"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="section mt16">
                        <div class="section-row">
                            <div class="section-title">PEMASUKAN PER KATEGORI</div>
                        </div>
                        <div class="cat-bars">
                            <template x-if="recap.income_by_category && recap.income_by_category.length > 0">
                                <template x-for="cat in recap.income_by_category" :key="cat.category_name">
                                    <div class="cat-bar">
                                        <div class="cat-bar-top">
                                            <div class="cat-bar-name"><span class="emo" x-text="cat.category_icon"></span> <span
                                                    x-text="cat.category_name"></span></div>
                                            <div class="cat-bar-meta">
                                                <div class="cat-bar-amount" x-text="'Rp ' + formatNumber(cat.amount)"></div>
                                                <div class="cat-bar-pct" x-text="cat.percentage + '%'"></div>
                                            </div>
                                        </div>
                                        <div class="cat-bar-track">
                                            <div class="cat-bar-fill green" :style="'width:' + cat.percentage + '%'"></div>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <template x-if="!recap.income_by_category || recap.income_by_category.length === 0">
                                <div class="cat-bar" style="justify-content:center;padding:24px;text-align:center;">
                                    <span style="font-family:var(--font-mono);font-size:10px;color:rgba(17,16,16,.4);">BELUM ADA DATA PEMASUKAN</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="section mt16">
                        <div class="section-row">
                            <div class="section-title">PENGELUARAN PER KATEGORI</div>
                        </div>
                        <div class="cat-bars">
                            <template x-if="recap.expense_by_category && recap.expense_by_category.length > 0">
                                <template x-for="cat in recap.expense_by_category" :key="cat.category_name">
                                    <div class="cat-bar">
                                        <div class="cat-bar-top">
                                            <div class="cat-bar-name"><span class="emo" x-text="cat.category_icon"></span> <span
                                                    x-text="cat.category_name"></span></div>
                                            <div class="cat-bar-meta">
                                                <div class="cat-bar-amount" x-text="'Rp ' + formatNumber(cat.amount)"></div>
                                                <div class="cat-bar-pct" x-text="cat.percentage + '%'"></div>
                                            </div>
                                        </div>
                                        <div class="cat-bar-track">
                                            <div class="cat-bar-fill red" :style="'width:' + cat.percentage + '%'"></div>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <template x-if="!recap.expense_by_category || recap.expense_by_category.length === 0">
                                <div class="cat-bar" style="justify-content:center;padding:24px;text-align:center;">
                                    <span style="font-family:var(--font-mono);font-size:10px;color:rgba(17,16,16,.4);">BELUM ADA DATA PENGELUARAN</span>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </div>

    <script>
        function monthlyRecapPage() {
            return {
                recap: {
                    month_year: 'Loading...',
                    total_income: 0,
                    total_expense: 0,
                    net_cashflow: 0,
                    expense_by_category: [],
                    income_by_category: [],
                    insight: { text: '', subtext: '', icon: '💡' }
                },
                currentMonth: new Date().getMonth() + 1, // 1-indexed
                currentYear: new Date().getFullYear(),
                monthNames: {
                    1: 'Januari', 2: 'Februari', 3: 'Maret', 4: 'April', 5: 'Mei', 6: 'Juni',
                    7: 'Juli', 8: 'Agustus', 9: 'September', 10: 'Oktober', 11: 'November', 12: 'Desember'
                },
                loading: {
                    recap: true
                },
                async init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const monthParam = parseInt(urlParams.get('month'));
                    const yearParam = parseInt(urlParams.get('year'));

                    if (!isNaN(monthParam) && monthParam >= 1 && monthParam <= 12) {
                        this.currentMonth = monthParam;
                    }
                    if (!isNaN(yearParam) && yearParam >= 2000 && yearParam <= new Date().getFullYear() + 10) { // arbitrary year limit
                        this.currentYear = yearParam;
                    } else {
                        // Default to previous month if no valid params
                        const prevMonth = new Date();
                        prevMonth.setMonth(prevMonth.getMonth() - 1);
                        this.currentMonth = prevMonth.getMonth() + 1;
                        this.currentYear = prevMonth.getFullYear();
                    }

                    this.fetchMonthlyRecap();
                },
                formatNumber(n) {
                    if (!n) return '0';
                    return Number(n).toLocaleString('id-ID');
                },
                async fetchMonthlyRecap() {
                    this.loading.recap = true;
                    try {
                        const res = await window.apiClient.get(`/v1/dashboard/monthly-recap?month=${this.currentMonth}&year=${this.currentYear}`);
                        const data = res.data.data;
                        this.recap = {
                            month_year: data.month_year || 'Bulan Tidak Diketahui',
                            total_income: data.total_income || 0,
                            total_expense: data.total_expense || 0,
                            net_cashflow: data.net_cashflow || 0,
                            expense_by_category: data.expense_by_category || [],
                            income_by_category: data.income_by_category || [],
                            insight: data.insight || { text: '', subtext: '', icon: '💡' }
                        };
                    } catch (e) {
                        console.error('Fetch monthly recap error:', e);
                        window.utils.showToast('error', 'Gagal memuat rekap bulanan');
                    } finally {
                        this.loading.recap = false;
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
                },
                fetchRecapForSelectedMonth() {
                    this.fetchMonthlyRecap();
                }
            }
        }
    </script>
@endsection