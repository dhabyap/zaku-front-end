@extends('layouts.app')

@section('content')
<div x-data="recurringPage" style="display:flex;flex-direction:column;height:100%;">
    <header class="dash-header">
        <div class="dh-row">
            <div>
                <div class="dh-greet">RECURRING</div>
                <div class="dh-name">Transaksi Berulang</div>
            </div>
            <a href="/dashboard" class="dh-avatar" style="text-decoration:none;font-size:18px;">←</a>
        </div>
    </header>

    <div class="screen-body" style="padding-bottom:100px;">
        {{-- Loading skeleton --}}
        <template x-if="loading">
            <div style="padding:16px;">
                <template x-for="n in [1,2,3]" :key="n">
                    <div class="budget-card-skeleton" style="margin-bottom:12px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:10px;background:rgba(17,16,16,.08);"></div>
                                <div>
                                    <div style="width:120px;height:12px;background:rgba(17,16,16,.08);border-radius:4px;margin-bottom:6px;"></div>
                                    <div style="width:80px;height:10px;background:rgba(17,16,16,.06);border-radius:4px;"></div>
                                </div>
                            </div>
                            <div style="width:40px;height:14px;background:rgba(17,16,16,.08);border-radius:4px;"></div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        {{-- Empty state --}}
        <template x-if="!loading && items.length === 0">
            <div class="budget-empty-state">
                <div style="font-size:48px;margin-bottom:16px;">🔁</div>
                <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:var(--ink);margin-bottom:8px;">Belum ada transaksi berulang</div>
                <div style="font-family:'DM Mono',monospace;font-size:12px;color:rgba(17,16,16,.5);margin-bottom:20px;">Atur transaksi yang otomatis berulang tiap hari, minggu, atau bulan.</div>
                <button class="btn-budget-add" @click="openForm()">+ TAMBAH TRANSAKSI BERULANG</button>
            </div>
        </template>

        {{-- Recurring list --}}
        <template x-if="!loading && items.length > 0">
            <div style="padding:16px;">
                <button class="btn-budget-add" @click="openForm()" style="margin-bottom:16px;">+ TAMBAH TRANSAKSI BERULANG</button>

                <template x-for="r in items" :key="r.id">
                    <div class="budget-card">
                        <div class="budget-card-top">
                            <div class="budget-card-info">
                                <div class="budget-card-icon" x-text="getEmoji(r.category?.name)"></div>
                                <div>
                                    <div class="budget-card-name" x-text="r.description || 'Tanpa deskripsi'"></div>
                                    <div class="budget-card-period" x-text="frequencyLabel(r) + ' · ' + r.category?.name"></div>
                                </div>
                            </div>
                            <div class="budget-card-actions">
                                <button class="budget-card-btn" @click="toggleStatus(r)" title="Aktif/Nonaktif" x-text="r.status === 'active' ? '⏸' : '▶'"></button>
                                <button class="budget-card-btn" @click="openForm(r)" title="Edit">✎</button>
                                <button class="budget-card-btn danger" @click="confirmDelete(r)" title="Hapus">✕</button>
                            </div>
                        </div>
                        <div class="budget-card-amount"
                             :class="r.type === 'income' ? '' : ''"
                             style="color:var(--ink);"
                             x-text="(r.type === 'income' ? '+' : '−') + ' Rp ' + formatNumber(r.amount_cents)"></div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:rgba(17,16,16,.5);margin-top:6px;display:flex;gap:8px;align-items:center;">
                            <span style="letter-spacing:1px;"
                                  :class="r.type === 'income' ? '' : ''"
                                  x-text="r.type === 'income' ? '↑ PEMASUKAN' : '↓ PENGELUARAN'"></span>
                            <span>·</span>
                            <span x-text="'Berikutnya: ' + formatDate(r.next_execution_date)"></span>
                            <span x-show="r.status !== 'active'" style="color:var(--punch);font-weight:500;" x-text="'[' + r.status.toUpperCase() + ']'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>

    {{-- Form Modal --}}
    <div class="modal-bg" id="m-recurring-form" @click="bgClose($event, 'm-recurring-form')">
        <div class="modal-sheet">
            <div class="modal-head">
                <div class="modal-head-title" x-text="editMode ? '// EDIT TRANSAKSI BERULANG' : '// TAMBAH TRANSAKSI BERULANG'"></div>
                <button class="modal-close" @click="closeForm()">✕</button>
            </div>

            <div class="field">
                <label>TIPE</label>
                <div class="period-select">
                    <button class="period-btn" :class="{ active: form.type === 'income' }" @click="form.type = 'income'">↑ PEMASUKAN</button>
                    <button class="period-btn" :class="{ active: form.type === 'expense' }" @click="form.type = 'expense'">↓ PENGELUARAN</button>
                </div>
            </div>

            <div class="field">
                <label>DESKRIPSI</label>
                <input type="text" x-model="form.description" placeholder="Contoh: Tagihan Netflix" class="field-input">
            </div>

            <div class="field">
                <label>NOMINAL (Rp)</label>
                <input type="number" x-model.number="form.amount_cents" placeholder="159000" min="1" class="field-input">
            </div>

            <div class="field">
                <label>KATEGORI</label>
                <select x-model="form.category_id" class="field-select">
                    <option value="">Tanpa kategori</option>
                    <template x-for="cat in categories" :key="cat.id">
                        <option :value="cat.id" x-text="cat.icon + ' ' + cat.name"></option>
                    </template>
                </select>
            </div>

            <div class="field">
                <label>FREKUENSI</label>
                <div class="period-select">
                    <button class="period-btn" :class="{ active: form.frequency === 'daily' }" @click="form.frequency = 'daily'">HARIAN</button>
                    <button class="period-btn" :class="{ active: form.frequency === 'weekly' }" @click="form.frequency = 'weekly'">MINGGUAN</button>
                    <button class="period-btn" :class="{ active: form.frequency === 'monthly' }" @click="form.frequency = 'monthly'">BULANAN</button>
                    <button class="period-btn" :class="{ active: form.frequency === 'yearly' }" @click="form.frequency = 'yearly'">TAHUNAN</button>
                </div>
            </div>

            <div class="field">
                <label>TANGGAL MULAI</label>
                <input type="date" x-model="form.start_date" class="field-input">
            </div>

            <div class="field">
                <label>TANGGAL AKHIR <span style="font-size:9px;opacity:.5;">(opsional)</span></label>
                <input type="date" x-model="form.end_date" class="field-input">
            </div>

            <button class="btn-budget-submit" @click="saveRecurring()" :disabled="saving" x-text="saving ? 'MENYIMPAN...' : (editMode ? 'SIMPAN PERUBAHAN' : 'TAMBAH TRANSAKSI BERULANG')"></button>
        </div>
    </div>

    @include('components.confirm-modal')
</div>
@endsection
