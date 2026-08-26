@extends('layouts.app')

@section('content')
<div x-data="manualInputPage()" style="display:flex;flex-direction:column;height:100%;background:var(--paper);overflow:hidden;">

    {{-- =====================================================
         HEADER
    ====================================================== --}}
    <div class="mi-header">
        <div class="mi-header-row">
            <button class="mi-back-btn" onclick="window.history.back()">←</button>
            <div>
                <div class="mi-title"><strong>Input</strong> Manual<span style="color:var(--punch);font-style:normal">.</span></div>
                <div class="mi-sub">ISI TRANSAKSI SECARA MANUAL</div>
            </div>
        </div>

        <div class="mi-type-toggle">
            <button class="mi-type-btn" :class="txType === 'income' ? 'active-inc' : ''" @click="setType('income')">
                <div class="mi-tb-dot"></div>↑ PEMASUKAN
            </button>
            <button class="mi-type-btn" :class="txType === 'expense' ? 'active-exp' : ''" @click="setType('expense')">
                <div class="mi-tb-dot"></div>↓ PENGELUARAN
            </button>
        </div>
    </div>

    {{-- =====================================================
         SCROLLABLE FORM BODY
    ====================================================== --}}
    <div class="mi-body" id="mi-body">

        {{-- Amount Block --}}
        <div class="mi-amount-block">
            <div class="mi-amount-label">
                NOMINAL TRANSAKSI
                <span x-text="'Rp ' + formatNumber(amount)">Rp 0</span>
            </div>
            <div class="mi-amount-input-row">
                <div class="mi-rp-prefix">Rp</div>
                <input class="mi-amount-input" type="number" inputmode="numeric" placeholder="0"
                    x-model.number="amount">
            </div>
            <div class="mi-quick-amounts">
                <button class="mi-qa-chip" @click="addAmount(5000)">+5rb</button>
                <button class="mi-qa-chip" @click="addAmount(10000)">+10rb</button>
                <button class="mi-qa-chip" @click="addAmount(25000)">+25rb</button>
                <button class="mi-qa-chip" @click="addAmount(50000)">+50rb</button>
                <button class="mi-qa-chip" @click="addAmount(100000)">+100rb</button>
                <button class="mi-qa-chip" @click="addAmount(500000)">+500rb</button>
                <button class="mi-qa-chip" @click="addAmount(1000000)">+1jt</button>
            </div>
        </div>

        {{-- Description & Date --}}
        <div class="mi-field-group">
            <div class="mi-field-row">
                <div class="mi-field-label">DESKRIPSI</div>
                <input class="mi-field-input" type="text" placeholder="Nama transaksi..." x-model="description">
            </div>
        </div>

        {{-- Date (text input with helper) --}}
        <div class="mi-date-block">
            <div class="mi-date-header">
                <div class="mi-date-label">TANGGAL</div>
                <button class="mi-date-today-btn" @click="setToday()">HARI INI</button>
            </div>
            <input class="mi-date-text-input" type="text"
                placeholder="DD/MM/YYYY"
                :value="dateDisplay"
                @change="parseDateInput($event.target.value)"
                @blur="parseDateInput($event.target.value)"
                inputmode="numeric">
            <div class="mi-date-hint">Format: DD/MM/YYYY — contoh: 21/08/2026</div>
        </div>

        {{-- Category Grid --}}
        <div class="mi-cat-section">
            <div class="mi-cat-section-label">PILIH KATEGORI</div>
            <template x-if="categories.length === 0">
                <div style="font-family:var(--font-mono);font-size:10px;color:rgba(17,16,16,.35);padding:12px 0;">Memuat kategori...</div>
            </template>
            <div class="mi-cat-grid">
                <template x-for="cat in categories" :key="cat.name">
                    <div class="mi-cat-card"
                         :class="selectedCat === cat.name ? (txType === 'income' ? 'selected inc-sel' : 'selected') : ''"
                         @click="selectedCat = cat.name">
                        <div class="mi-cat-ico" x-text="cat.icon"></div>
                        <div class="mi-cat-name" x-text="cat.name"></div>
                    </div>
                </template>
                <div class="mi-cat-card add-new" @click="openQuickAdd()">
                    <div class="mi-cat-ico">+</div>
                    <div class="mi-cat-name">TAMBAH</div>
                </div>
            </div>
        </div>

        {{-- Preview Card --}}
        <div class="mi-preview-card" :class="amount > 0 || description ? 'show' : ''">
            <div class="mi-preview-card-head">
                // PREVIEW TRANSAKSI
                <span>SEBELUM DISIMPAN</span>
            </div>
            <div class="mi-preview-body">
                <div class="mi-prev-row"><span class="mi-prev-k">DESKRIPSI</span><span class="mi-prev-v" x-text="description || '—'">—</span></div>
                <div class="mi-prev-row"><span class="mi-prev-k">KATEGORI</span><span class="mi-prev-v" x-text="getCatLabel()">—</span></div>
                <div class="mi-prev-row"><span class="mi-prev-k">TANGGAL</span><span class="mi-prev-v" x-text="formatDateID(dateRaw)">—</span></div>
                <div class="mi-prev-row"><span class="mi-prev-k">TIPE</span><span class="mi-prev-v" :class="txType === 'income' ? 'inc' : 'exp'" x-text="txType === 'income' ? '↑ PEMASUKAN' : '↓ PENGELUARAN'"></span></div>
                <div class="mi-prev-amt" :class="txType === 'income' ? 'inc' : 'exp'"
                     x-text="(txType === 'income' ? '+' : '−') + 'Rp ' + formatNumber(amount)"></div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="mi-submit-area">
            <button class="mi-btn-submit" :class="txType === 'income' ? 'inc' : 'exp'" @click="submitForm()" :disabled="loading || amount <= 0 || !description || !selectedCat">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span x-text="'SIMPAN ' + (txType === 'income' ? 'PEMASUKAN' : 'PENGELUARAN')">SIMPAN</span>
            </button>
            <button class="mi-btn-cancel" onclick="window.history.back()">BATAL, KEMBALI</button>
        </div>

    </div>

    {{-- =====================================================
         SUCCESS OVERLAY
    ====================================================== --}}
    <template x-if="saved">
        <div class="mi-success-overlay">
            <div class="mi-success-icon-wrap">
                <div class="mi-success-icon-bg" x-text="savedData.icon">💰</div>
                <div class="mi-success-badge">✓</div>
            </div>
            <div class="mi-success-title" x-text="savedData.type === 'income' ? 'Cuan masuk! 🎉' : 'Tercatat! 💪'"></div>
            <div class="mi-success-sub">TRANSAKSI BERHASIL DISIMPAN</div>

            <div class="mi-success-card">
                <div class="mi-success-card-head">
                    <div class="mi-scard-ico" x-text="savedData.icon"></div>
                    <div class="mi-scard-name" x-text="savedData.description"></div>
                    <div class="mi-scard-amt" :class="savedData.type === 'income' ? 'inc' : 'exp'"
                         x-text="(savedData.type === 'income' ? '+' : '−') + 'Rp ' + formatNumber(savedData.amount)"></div>
                </div>
                <div class="mi-success-card-body">
                    <div class="mi-sc-row"><span class="mi-sc-k">NOMINAL</span><span class="mi-sc-v" x-text="(savedData.type === 'income' ? '+' : '−') + 'Rp ' + formatNumber(savedData.amount)"></span></div>
                    <div class="mi-sc-row"><span class="mi-sc-k">KATEGORI</span><span class="mi-sc-v" x-text="getCatLabel()"></span></div>
                    <div class="mi-sc-row"><span class="mi-sc-k">TANGGAL</span><span class="mi-sc-v" x-text="formatDateID(dateRaw)"></span></div>
                    <div class="mi-sc-row"><span class="mi-sc-k">TIPE</span><span class="mi-sc-v" x-text="savedData.type === 'income' ? '↑ PEMASUKAN' : '↓ PENGELUARAN'"></span></div>
                </div>
            </div>

            <div class="mi-success-actions">
                <button class="mi-btn-success-primary" @click="saved = false; resetForm();">← KEMBALI KE CHAT</button>
                <button class="mi-btn-success-sec" @click="saved = false; resetForm();">+ TAMBAH TRANSAKSI LAGI</button>
            </div>
        </div>
    </template>

    {{-- QUICK ADD CATEGORY MODAL --}}
    <template x-if="showQuickAdd">
        <div class="mi-success-overlay" @click.self="showQuickAdd = false">
            <div class="cat-modal" @click.stop>
                <div class="cat-modal-header">
                    <span>TAMBAH KATEGORI</span>
                    <button class="cat-modal-close" @click="showQuickAdd = false">✕</button>
                </div>
                <div class="cat-modal-body">
                    <div class="cat-form-label">ICON</div>
                    <div class="cat-icon-picker">
                        <template x-for="ico in quickAddIcons" :key="ico">
                            <button class="cat-icon-opt"
                                    :class="quickAddForm.icon === ico ? 'selected' : ''"
                                    @click="quickAddForm.icon = ico"
                                    x-text="ico"></button>
                        </template>
                    </div>
                    <div class="cat-form-label">NAMA KATEGORI</div>
                    <input class="cat-form-input" type="text" placeholder="Contoh: REJEKI" x-model="quickAddForm.name" maxlength="50" @input="quickAddForm.name = quickAddForm.name.toUpperCase()">
                    <div class="cat-form-label">TIPE</div>
                    <div class="cat-type-radio-group">
                        <button class="cat-type-radio" :class="quickAddForm.type === 'expense' ? 'selected exp' : ''" @click="quickAddForm.type = 'expense'">↓ PENGELUARAN</button>
                        <button class="cat-type-radio" :class="quickAddForm.type === 'income' ? 'selected inc' : ''" @click="quickAddForm.type = 'income'">↑ PEMASUKAN</button>
                        <button class="cat-type-radio" :class="quickAddForm.type === 'both' ? 'selected both' : ''" @click="quickAddForm.type = 'both'">↑↓ KEDUANYA</button>
                    </div>
                </div>
                <div class="cat-modal-footer">
                    <button class="mi-btn-cancel" @click="showQuickAdd = false">BATAL</button>
                    <button class="mi-btn-submit exp" @click="quickAddCategory()" :disabled="quickAddSaving || !quickAddForm.name">
                        <span x-text="quickAddSaving ? 'MENYIMPAN...' : 'TAMBAH'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
