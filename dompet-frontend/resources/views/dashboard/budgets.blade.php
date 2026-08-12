@extends('layouts.app')

@section('content')
<div x-data="budgetPage" style="display:flex;flex-direction:column;height:100%;">
    <header class="dash-header">
        <div class="dh-row">
            <div>
                <div class="dh-greet">BUDGET</div>
                <div class="dh-name">Kelola Budget per Kategori</div>
            </div>
            <a href="/dashboard" class="dh-avatar" style="text-decoration:none;font-size:18px;">←</a>
        </div>
    </header>

    <div class="screen-body" style="padding-bottom:100px;">
        {{-- Loading skeleton --}}
        <template x-if="loading">
            <div>
                <div class="budget-card-skeleton" style="margin-bottom:12px;" x-repeat="3">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:10px;background:rgba(17,16,16,.08);"></div>
                            <div>
                                <div style="width:80px;height:12px;background:rgba(17,16,16,.08);border-radius:4px;margin-bottom:6px;"></div>
                                <div style="width:50px;height:10px;background:rgba(17,16,16,.06);border-radius:4px;"></div>
                            </div>
                        </div>
                        <div style="width:40px;height:14px;background:rgba(17,16,16,.08);border-radius:4px;"></div>
                    </div>
                    <div style="height:6px;background:rgba(17,16,16,.06);border-radius:3px;"></div>
                </div>
            </div>
        </template>

        {{-- Empty state --}}
        <template x-if="!loading && budgets.length === 0">
            <div class="budget-empty-state">
                <div style="font-size:48px;margin-bottom:16px;">💰</div>
                <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:var(--ink);margin-bottom:8px;">Belum ada budget</div>
                <div style="font-family:'DM Mono',monospace;font-size:12px;color:rgba(17,16,16,.5);margin-bottom:20px;">Atur budget per kategori agar bisa tracking pengeluaran.</div>
                <button class="btn-budget-add" @click="openForm()">+ TAMBAH BUDGET</button>
            </div>
        </template>

        {{-- Budget list --}}
        <template x-if="!loading && budgets.length > 0">
            <div>
                {{-- Summary card --}}
                <div class="budget-summary-card">
                    <div class="budget-summary-row">
                        <div>
                            <div class="budget-summary-label">TOTAL BUDGET</div>
                            <div class="budget-summary-val" x-text="'Rp ' + formatNumber(totalBudget)">Rp 0</div>
                        </div>
                        <div style="text-align:right;">
                            <div class="budget-summary-label">TOTAL TERPAKAI</div>
                            <div class="budget-summary-val" x-text="'Rp ' + formatNumber(totalSpent)">Rp 0</div>
                        </div>
                    </div>
                    <div class="budget-track" style="margin-top:12px;">
                        <div class="budget-fill" :class="summaryStatusClass" :style="'width:' + summaryPct + '%'"></div>
                    </div>
                    <div class="budget-foot">
                        <span x-text="summaryPct + '% terpakai'">0% terpakai</span>
                        <span x-text="'Sisa Rp ' + formatNumber(Math.max(0, totalBudget - totalSpent))">Sisa Rp 0</span>
                    </div>
                </div>

                {{-- Add button --}}
                <button class="btn-budget-add" @click="openForm()" style="margin-bottom:16px;">+ TAMBAH BUDGET</button>

                {{-- Budget cards --}}
                <template x-for="b in budgets" :key="b.id">
                    <div class="budget-card">
                        <div class="budget-card-top">
                            <div class="budget-card-info">
                                <div class="budget-card-icon" x-text="getEmoji(b.category?.name)"></div>
                                <div>
                                    <div class="budget-card-name" x-text="b.category?.name || 'TANPA KATEGORI'"></div>
                                    <div class="budget-card-period" x-text="periodLabel(b.period)"></div>
                                </div>
                            </div>
                            <div class="budget-card-actions">
                                <button class="budget-card-btn" @click="openForm(b)" title="Edit">✎</button>
                                <button class="budget-card-btn danger" @click="confirmDelete(b)" title="Hapus">✕</button>
                            </div>
                        </div>
                        <div class="budget-card-amount" x-text="'Rp ' + formatNumber(b.amount)"></div>
                        <template x-if="progressMap[b.id]">
                            <div>
                                <div class="budget-track" style="margin-top:8px;">
                                    <div class="budget-fill" :class="statusClass(progressMap[b.id].status)" :style="'width:' + progressMap[b.id].percentage + '%'"></div>
                                </div>
                                <div class="budget-foot" style="margin-top:6px;">
                                    <span x-text="'Terpakai Rp ' + formatNumber(progressMap[b.id].spent)">Terpakai Rp 0</span>
                                    <span class="budget-status-badge" :class="statusClass(progressMap[b.id].status)" x-text="progressMap[b.id].status?.toUpperCase()"></span>
                                </div>
                            </div>
                        </template>
                        <template x-if="!progressMap[b.id]">
                            <div class="budget-track" style="margin-top:8px;opacity:.3;">
                                <div class="budget-fill" style="width:0%"></div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>
    </div>

    {{-- Form Modal --}}
    <div class="modal-bg" id="m-budget-form" @click="bgClose($event, 'm-budget-form')">
        <div class="modal-sheet">
            <div class="modal-head">
                <div class="modal-head-title" x-text="editMode ? '// EDIT BUDGET' : '// TAMBAH BUDGET'"></div>
                <button class="modal-close" @click="closeForm()">✕</button>
            </div>

            <div class="field">
                <label>KATEGORI</label>
                <select x-model="form.category" :disabled="editMode" class="field-select">
                    <option value="">Pilih kategori...</option>
                    <template x-for="cat in categories" :key="cat.id">
                        <option :value="cat.name" x-text="cat.name"></option>
                    </template>
                </select>
            </div>

            <div class="field">
                <label>NOMINAL (Rp)</label>
                <input type="number" x-model.number="form.amount" placeholder="500000" min="1" class="field-input">
            </div>

            <div class="field">
                <label>PERIODE</label>
                <div class="period-select">
                    <button class="period-btn" :class="{ active: form.period === 'daily' }" @click="form.period = 'daily'" :disabled="editMode">HARIAN</button>
                    <button class="period-btn" :class="{ active: form.period === 'weekly' }" @click="form.period = 'weekly'" :disabled="editMode">MINGGUAN</button>
                    <button class="period-btn" :class="{ active: form.period === 'monthly' }" @click="form.period = 'monthly'" :disabled="editMode">BULANAN</button>
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

            <button class="btn-budget-submit" @click="saveBudget()" :disabled="saving" x-text="saving ? 'MENYIMPAN...' : (editMode ? 'SIMPAN PERUBAHAN' : 'TAMBAH BUDGET')"></button>
        </div>
    </div>

    @include('components.confirm-modal')
</div>
@endsection
