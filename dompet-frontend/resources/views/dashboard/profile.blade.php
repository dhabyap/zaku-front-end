@extends('layouts.app')

@section('content')
<div x-data="profilePage" style="display:flex;flex-direction:column;height:100%;">
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
            <a href="/changelogs" class="menu-row" style="text-decoration:none;display:flex;align-items:center;gap:12px;padding:14px 0;border-bottom:1px solid rgba(17,16,16,.06);color:var(--ink);cursor:pointer;">
                <div class="menu-ico">📋</div>
                <span>Update Zaku</span>
                <div class="menu-arr">→</div>
            </a>
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

@endsection
