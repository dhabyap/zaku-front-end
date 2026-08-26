@extends('layouts.app')

@section('content')
<div x-data="categoriesPage()" style="display:flex;flex-direction:column;height:100%;background:var(--paper);overflow:hidden;">

    {{-- HEADER --}}
    <div class="mi-header">
        <div class="mi-header-row">
            <button class="mi-back-btn" onclick="window.history.back()">←</button>
            <div>
                <div class="mi-title"><strong>Kategori</strong> Saya<span style="color:var(--punch);font-style:normal">.</span></div>
                <div class="mi-sub">KELOLA KATEGORI TRANSAKSI</div>
            </div>
        </div>
    </div>

    {{-- BODY --}}
    <div class="mi-body" id="cat-body">

        {{-- Type Filter --}}
        <div class="mi-type-toggle" style="margin-bottom:16px;">
            <button class="mi-type-btn" :class="filter === 'all' ? 'active-inc' : ''" @click="filter = 'all'">
                <div class="mi-tb-dot"></div>SEMUA
            </button>
            <button class="mi-type-btn" :class="filter === 'income' ? 'active-inc' : 'inactive-inc'" @click="filter = 'income'">
                <div class="mi-tb-dot"></div>↑ PEMASUKAN
            </button>
            <button class="mi-type-btn" :class="filter === 'expense' ? 'active-exp' : 'inactive-exp'" @click="filter = 'expense'">
                <div class="mi-tb-dot"></div>↓ PENGELUARAN
            </button>
        </div>

        {{-- Add Button --}}
        <button class="mi-btn-add-cat" @click="openAddModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            TAMBAH KATEGORI BARU
        </button>

        {{-- Loading --}}
        <template x-if="loading && categories.length === 0">
            <div class="cat-loading">Memuat kategori...</div>
        </template>

        {{-- Category List --}}
        <div class="cat-list">
            <template x-for="cat in filteredCategories" :key="cat.id">
                <div class="cat-card">
                    <div class="cat-card-left">
                        <div class="cat-card-icon" x-text="cat.icon"></div>
                        <div class="cat-card-info">
                            <div class="cat-card-name" x-text="cat.name"></div>
                            <div class="cat-card-type" :class="'type-' + cat.type" x-text="cat.type === 'income' ? '↑ PEMASUKAN' : cat.type === 'expense' ? '↓ PENGELUARAN' : '↑↓ KEDUANYA'"></div>
                        </div>
                    </div>
                    <div class="cat-card-actions">
                        <button class="cat-action-btn edit" @click="openEditModal(cat)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button class="cat-action-btn delete" @click="confirmDelete(cat)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty State --}}
        <template x-if="!loading && filteredCategories.length === 0">
            <div class="cat-empty">
                <div class="cat-empty-icon">📂</div>
                <div class="cat-empty-text">Belum ada kategori</div>
                <div class="cat-empty-sub">Tekan tombol "TAMBAH KATEGORI BARU" untuk membuat kategori pertama.</div>
            </div>
        </template>
    </div>

    {{-- ADD / EDIT MODAL --}}
    <template x-if="showModal">
        <div class="mi-success-overlay" @click.self="closeModal()">
            <div class="cat-modal" @click.stop>
                <div class="cat-modal-header">
                    <span x-text="editingCat ? 'EDIT KATEGORI' : 'TAMBAH KATEGORI'"></span>
                    <button class="cat-modal-close" @click="closeModal()">✕</button>
                </div>

                <div class="cat-modal-body">
                    {{-- Icon Picker --}}
                    <div class="cat-form-label">ICON</div>
                    <div class="cat-icon-picker">
                        <template x-for="ico in iconOptions" :key="ico">
                            <button class="cat-icon-opt"
                                    :class="form.icon === ico ? 'selected' : ''"
                                    @click="form.icon = ico"
                                    x-text="ico"></button>
                        </template>
                    </div>

                    {{-- Name --}}
                    <div class="cat-form-label">NAMA KATEGORI</div>
                    <input class="cat-form-input" type="text" placeholder="Contoh: REJEKI" x-model="form.name" maxlength="50" @input="form.name = form.name.toUpperCase()">

                    {{-- Type --}}
                    <div class="cat-form-label">TIPE</div>
                    <div class="cat-type-radio-group">
                        <button class="cat-type-radio" :class="form.type === 'expense' ? 'selected exp' : ''" @click="form.type = 'expense'">
                            ↓ PENGELUARAN
                        </button>
                        <button class="cat-type-radio" :class="form.type === 'income' ? 'selected inc' : ''" @click="form.type = 'income'">
                            ↑ PEMASUKAN
                        </button>
                        <button class="cat-type-radio" :class="form.type === 'both' ? 'selected both' : ''" @click="form.type = 'both'">
                            ↑↓ KEDUANYA
                        </button>
                    </div>

                    {{-- Keywords --}}
                    <div class="cat-form-label" style="margin-top:12px;">KATA KUNCI <span style="opacity:.4;font-size:9px;">(AI chat)</span></div>
                    <input class="cat-form-input" type="text" placeholder="pisah koma: kopi, makan, resto" x-model="keywordsInput" @input="form.keywords = keywordsInput.split(',').map(s => s.trim()).filter(Boolean)">
                    <div style="font-family:var(--font-mono);font-size:9px;color:rgba(17,16,16,.4);margin-top:4px;">Kata kunci untuk auto-detect kategori oleh AI chat</div>
                </div>

                <div class="cat-modal-footer">
                    <button class="mi-btn-cancel" @click="closeModal()">BATAL</button>
                    <button class="mi-btn-submit exp" @click="saveCategory()" :disabled="saving || !form.name">
                        <span x-text="saving ? 'MENYIMPAN...' : (editingCat ? 'PERBARUI' : 'TAMBAH')"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- DELETE CONFIRM MODAL --}}
    <template x-if="showDeleteModal">
        <div class="mi-success-overlay" @click.self="showDeleteModal = false">
            <div class="cat-modal" @click.stop>
                <div class="cat-modal-header" style="border-bottom-color:var(--punch);">
                    <span>HAPUS KATEGORI?</span>
                    <button class="cat-modal-close" @click="showDeleteModal = false">✕</button>
                </div>
                <div class="cat-modal-body" style="text-align:center;">
                    <div style="font-size:48px;margin-bottom:8px;" x-text="deletingCat?.icon"></div>
                    <div style="font-family:var(--font-display);font-weight:800;font-size:18px;" x-text="deletingCat?.name"></div>
                    <div style="font-family:var(--font-mono);font-size:11px;color:rgba(17,16,16,.45);margin-top:8px;">Aksi ini tidak dapat dibatalkan.</div>
                </div>
                <div class="cat-modal-footer">
                    <button class="mi-btn-cancel" @click="showDeleteModal = false">BATAL</button>
                    <button class="mi-btn-submit" style="background:var(--punch);color:var(--paper);border-color:var(--punch);" @click="deleteCategory()" :disabled="deleting">
                        <span x-text="deleting ? 'MENGHAPUS...' : 'HAPUS'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
