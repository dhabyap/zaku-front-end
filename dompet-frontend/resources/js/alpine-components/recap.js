// resources/js/alpine-components/recap.js
// Alpine component: monthlyRecapPage

export function registerRecap(Alpine) {

    Alpine.data('monthlyRecapPage', () => ({
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
            if (rate >= 30) return 'Kamu nabung dengan baik! 💚';
            if (rate >= 10) return 'Nabung lumayan, bisa lebih! 👍';
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
            return val > 0 ? '▲' : '▼';
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
    }));

}