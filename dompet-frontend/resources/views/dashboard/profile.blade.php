@extends('layouts.app')

@section('content')
<div x-data="profilePage()" style="display:flex;flex-direction:column;height:100%;">
    <div class="screen-body" style="padding-top:0;padding-bottom:90px">
        <div class="prof-hero">
            <div class="prof-av" x-text="(user?.name?.charAt(0) || '?').toUpperCase()"></div>
            <div class="prof-name" x-text="user?.name || 'Teman'"></div>
            <div class="prof-email" x-text="user?.email || ''"></div>
            <div class="prof-badge">MEMBER AKTIF</div>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-box-label">TOTAL TRANSAKSI</div>
                <div class="stat-box-val" x-text="stats.total || '0'">47</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-label">BULAN INI</div>
                <div class="stat-box-val" x-text="stats.month || '0'">12</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-label">TERBESAR</div>
                <div class="stat-box-val big" x-text="'Rp ' + formatNumber(stats.biggest)">Rp 7,5jt</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-label">KATEGORI</div>
                <div class="stat-box-val" x-text="stats.categories || '0'">8</div>
            </div>
        </div>

        <div class="budget-box">
            <div class="budget-top">
                <div>
                    <div class="budget-label">BUDGET BULANAN</div>
                    <div class="budget-amount" x-text="'Rp ' + formatNumber(budget.limit)">Rp 6.000.000</div>
                </div>
                <div class="budget-pct" x-text="budget.pct + '%'">67%</div>
            </div>
            <div class="budget-track">
                <div class="budget-fill" :style="'width:' + budget.pct + '%'"></div>
            </div>
            <div class="budget-foot">
                <span x-text="'TERPAKAI Rp ' + formatNumber(budget.used)">TERPAKAI Rp 4.025.000</span>
                <span x-text="'SISA Rp ' + formatNumber(budget.left)">SISA Rp 1.975.000</span>
            </div>
        </div>

        <div class="menu-block mt16">
            <div class="menu-row" @click="openModal('m-edit')">
                <div class="menu-ico">✎</div>
                <span>Edit Profil</span>
                <div class="menu-arr">→</div>
            </div>
            <div class="menu-row" @click="openModal('m-budget')">
                <div class="menu-ico">💰</div>
                <span>Atur Budget</span>
                <div class="menu-arr">→</div>
            </div>
            <div class="menu-row" @click="exportData()">
                <div class="menu-ico">↓</div>
                <span>Export Data CSV</span>
                <div class="menu-arr">→</div>
            </div>
            <div class="menu-row" @click="window.utils.showToast('info', 'Fitur segera hadir! 🔔')">
                <div class="menu-ico">🔔</div>
                <span>Notifikasi</span>
                <div class="menu-arr">→</div>
            </div>
            <div class="menu-row" @click="window.utils.showToast('info', 'Fitur segera hadir! 📊')">
                <div class="menu-ico">📊</div>
                <span>Laporan Bulanan</span>
                <div class="menu-arr">→</div>
            </div>
            <div class="menu-row danger" @click="logout()">
                <div class="menu-ico">↩</div>
                <span>Keluar</span>
                <div class="menu-arr">→</div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal-bg" id="m-edit" @click="bgClose($event,'m-edit')">
        <div class="modal-sheet">
            <div class="modal-head">
                <div class="modal-head-title">// EDIT PROFIL</div>
                <button class="modal-close" @click="closeModal('m-edit')">✕</button>
            </div>
            <div class="field">
                <label>NAMA LENGKAP</label>
                <input type="text" x-model="editForm.name">
            </div>
            <div class="field">
                <label>EMAIL</label>
                <input type="email" x-model="editForm.email">
            </div>
            <button class="btn-main" @click="saveProfile()">SIMPAN PERUBAHAN →</button>
        </div>
    </div>

    <!-- Budget Modal -->
    <div class="modal-bg" id="m-budget" @click="bgClose($event,'m-budget')">
        <div class="modal-sheet">
            <div class="modal-head">
                <div class="modal-head-title">// ATUR BUDGET</div>
                <button class="modal-close" @click="closeModal('m-budget')">✕</button>
            </div>
            <div class="field">
                <label>BUDGET BULANAN (RP)</label>
                <input type="number" x-model="budgetInput">
            </div>
            <button class="btn-main" @click="saveBudget()">SIMPAN BUDGET →</button>
        </div>
    </div>
</div>

<script>
    function profilePage() {
        return {
            user: window.auth.getUser(),
            stats: { total: 0, month: 0, biggest: 0, categories: 0 },
            budget: { limit: 0, used: 0, pct: 0, left: 0 },
            editForm: { name: '', email: '' },
            budgetInput: 0,
            async init() {
                try {
                    const res = await window.apiClient.get('/user/profile');
                    const data = res.data.data;
                    this.user = data;
                    window.auth.setUser(this.user);
                    if (data.budget) {
                        this.budget = {
                            limit: data.budget.monthly_budget || 0,
                            used: data.budget.budget_used || 0,
                            pct: data.budget.budget_percentage || 0,
                            left: Math.max(0, (data.budget.monthly_budget || 0) - (data.budget.budget_used || 0))
                        };
                        this.budgetInput = this.budget.limit;
                    }
                } catch (e) {
                    console.error('Fetch profile error:', e);
                    window.utils.showToast('error', 'Gagal memuat data profil');
                }
                this.editForm = { name: this.user?.name || '', email: this.user?.email || '' };
                this.fetchStats();
            },
            formatNumber(n) {
                if (!n) return '0';
                return Number(n).toLocaleString('id-ID');
            },
            async fetchStats() {
                try {
                    const res = await window.apiClient.get('/transactions/stats');
                    const data = res.data.data || {};
                    this.stats = {
                        total: data.total || 0,
                        month: data.this_month || 0,
                        biggest: data.biggest || 0,
                        categories: data.categories || 0
                    };
                } catch (e) {
                    console.error('Fetch stats error:', e);
                    window.utils.showToast('error', 'Gagal memuat statistik');
                }
            },
            async logout() {
                const ok = await window.utils.confirmDialog({
                    title: 'Keluar dari Akun?',
                    message: 'Semua sesi aktif akan diakhiri. Kamu perlu login kembali untuk melanjutkan.',
                    okLabel: 'YA, KELUAR',
                    danger: false
                });
                if (ok) {
                    window.auth.clearToken();
                    window.location.href = '/login';
                }
            },
            openModal(id) {
                document.getElementById(id).classList.add('open');
            },
            closeModal(id) {
                document.getElementById(id).classList.remove('open');
            },
            bgClose(e, id) {
                if (e.target === document.getElementById(id)) this.closeModal(id);
            },
            async saveProfile() {
                try {
                    await window.apiClient.put('/user/profile', this.editForm);
                    this.user = { ...this.user, ...this.editForm };
                    window.auth.setUser(this.user);
                    this.closeModal('m-edit');
                    window.utils.showToast('success', 'Profil disimpan! ✓');
                } catch (e) {
                    window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal menyimpan profil'), true);
                }
            },
            async saveBudget() {
                try {
                    const newLimit = parseInt(this.budgetInput);
                    await window.apiClient.put('/user/budget', { monthly_budget: newLimit });
                    this.budget.limit = newLimit;
                    this.budget.pct = newLimit > 0 ? Math.min(100, Math.round((this.budget.used / newLimit) * 100)) : 0;
                    this.budget.left = Math.max(0, newLimit - this.budget.used);
                    this.closeModal('m-budget');
                    window.utils.showToast('success', 'Budget disimpan! ✓');
                } catch (e) {
                    window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal menyimpan budget'), true);
                }
            },
            exportData() {
                window.utils.showToast('success', 'Data berhasil diexport! ↓');
            }
        }
    }
</script>
@endsection
