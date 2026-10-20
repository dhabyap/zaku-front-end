@extends('layouts.app')

@section('content')
<div x-data="transactionDetail('{{ $id }}')" style="display:flex;flex-direction:column;height:100%;">
    <div class="inner-top" style="display:flex;align-items:center;gap:12px;">
        <a href="/transactions" class="back-btn" style="border-color:rgba(255,255,255,.2);color:var(--paper);text-decoration:none;">←</a>
        <div>
            <div class="inner-title" style="font-size:24px;">Detail Transaksi</div>
            <div class="inner-sub">INFORMASI LENGKAP</div>
        </div>
    </div>

    <div class="screen-body">
        <template x-if="loading">
            <div style="padding:20px 16px;">
                <div class="balance-card" style="background:var(--cream);height:120px;"></div>
                <div class="tx" style="margin-top:16px;background:var(--cream);height:200px;"></div>
            </div>
        </template>

        <template x-if="!loading && transaction">
            <div>
                <div class="balance-card" :style="{background: transaction.type === 'expense' ? 'var(--punch)' : 'var(--mint)'}">
                    <div class="bc-label">JUMLAH TRANSAKSI</div>
                    <div class="bc-amount" :style="{color: transaction.type === 'expense' ? 'var(--paper)' : 'var(--ink)'}" x-text="(transaction.type === 'expense' ? '-' : '+') + 'Rp ' + formatNumber(transaction.amount)"></div>
                </div>

                <div style="margin:16px;background:#fff;border:var(--border);box-shadow:var(--bs-lg);padding:20px;">

                    {{-- VIEW MODE --}}
                    <div x-show="!editing">
                        <div style="margin-bottom:16px;">
                            <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:2.5px;color:rgba(17,16,16,.4);margin-bottom:4px;">DESKRIPSI</div>
                            <div style="font-size:20px;font-weight:800;color:var(--ink);" x-text="transaction.description"></div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                            <div>
                                <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:2.5px;color:rgba(17,16,16,.4);margin-bottom:4px;">TANGGAL</div>
                                <div style="font-family:var(--font-mono);font-size:13px;font-weight:500;color:var(--ink);" x-text="formatDate(transaction.created_at)"></div>
                            </div>
                            <div>
                                <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:2.5px;color:rgba(17,16,16,.4);margin-bottom:4px;">STATUS</div>
                                <div style="display:inline-block;background:var(--ink);color:var(--punch-2);padding:4px 10px;font-family:var(--font-mono);font-size:9px;font-weight:500;">SUCCESS</div>
                            </div>
                        </div>
                        <div style="margin-bottom:16px;">
                            <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:2.5px;color:rgba(17,16,16,.4);margin-bottom:4px;">KATEGORI</div>
                            <span style="font-family:var(--font-mono);font-size:13px;font-weight:500;color:var(--ink);" x-text="(transaction.category_icon || '') + ' ' + (transaction.category_name || transaction.category || 'UMUM')"></span>
                        </div>
                        <div style="margin-bottom:16px;">
                            <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:2.5px;color:rgba(17,16,16,.4);margin-bottom:4px;">TIPE</div>
                            <span :style="{color: transaction.type === 'income' ? 'var(--mint-2, #2d6a4f)' : 'var(--punch)'}" style="font-family:var(--font-mono);font-size:13px;font-weight:500;" x-text="transaction.type === 'income' ? '↑ PEMASUKAN' : '↓ PENGELUARAN'"></span>
                        </div>
                        <button @click="startEdit()" class="btn-main" style="background:var(--ink);color:var(--paper);margin-top:0;">✏️ EDIT TRANSAKSI</button>
                    </div>

                    {{-- EDIT MODE --}}
                    <div x-show="editing">
                        <div style="margin-bottom:16px;">
                            <label style="font-family:var(--font-mono);font-size:9px;letter-spacing:2.5px;color:rgba(17,16,16,.4);margin-bottom:4px;display:block;">DESKRIPSI</label>
                            <input x-model="editDescription" type="text" style="width:100%;padding:8px 12px;font-size:14px;border:var(--border);border-radius:4px;background:#fff;font-family:var(--font-mono);">
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                            <div>
                                <label style="font-family:var(--font-mono);font-size:9px;letter-spacing:2.5px;color:rgba(17,16,16,.4);margin-bottom:4px;display:block;">JUMLAH (Rp)</label>
                                <input x-model="editAmount" type="number" style="width:100%;padding:8px 12px;font-size:14px;border:var(--border);border-radius:4px;background:#fff;font-family:var(--font-mono);">
                            </div>
                            <div>
                                <label style="font-family:var(--font-mono);font-size:9px;letter-spacing:2.5px;color:rgba(17,16,16,.4);margin-bottom:4px;display:block;">TANGGAL</label>
                                <input x-model="editDate" type="date" style="width:100%;padding:8px 12px;font-size:14px;border:var(--border);border-radius:4px;background:#fff;font-family:var(--font-mono);">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                            <div>
                                <label style="font-family:var(--font-mono);font-size:9px;letter-spacing:2.5px;color:rgba(17,16,16,.4);margin-bottom:4px;display:block;">TIPE</label>
                                <select x-model="editType" style="width:100%;padding:8px 12px;font-size:13px;border:var(--border);border-radius:4px;background:#fff;font-family:var(--font-mono);">
                                    <option value="expense">↓ PENGELUARAN</option>
                                    <option value="income">↑ PEMASUKAN</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-family:var(--font-mono);font-size:9px;letter-spacing:2.5px;color:rgba(17,16,16,.4);margin-bottom:4px;display:block;">KATEGORI</label>
                                <select x-model="editCategory" style="width:100%;padding:8px 12px;font-size:13px;border:var(--border);border-radius:4px;background:#fff;font-family:var(--font-mono);">
                                    <template x-for="cat in categories" :key="cat.name">
                                        <option :value="cat.name" x-text="(cat.icon || '') + ' ' + cat.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <button @click="saveEdit()" class="btn-main" style="background:var(--ink);color:var(--paper);margin:0;flex:1;" :disabled="saving">
                                <span x-show="!saving">SIMPAN PERUBAHAN</span>
                                <span x-show="saving">Menyimpan...</span>
                            </button>
                            <button @click="editing = false" style="background:none;border:var(--border);padding:10px 16px;font-size:14px;cursor:pointer;border-radius:4px;">BATAL</button>
                        </div>
                    </div>
                </div>

                <div style="padding:0 16px; display:flex; flex-direction:column; gap:12px; margin-top:16px;">
                    <button @click="window.print()" class="btn-main" style="background:var(--paper);color:var(--ink);margin-top:0;">CETAK STRUK →</button>
                    <button @click="deleteTransaction()" class="btn-main" style="background:var(--punch);color:var(--paper);margin-top:0;border:var(--border);box-shadow:var(--bs);">HAPUS TRANSAKSI 🗑️</button>
                </div>

                {{-- PRINT RECEIPT --}}
                <div class="receipt-print">
                    <div class="receipt-header">
                        <div class="receipt-title">ZAKU</div>
                        <div class="receipt-subtitle">Dompet Digital</div>
                        <div class="receipt-subtitle" x-text="formatDate(transaction.created_at)"></div>
                    </div>
                    <div class="receipt-body">
                        <div class="receipt-row">
                            <span class="receipt-label">Deskripsi</span>
                            <span class="receipt-value" x-text="transaction.description"></span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Kategori</span>
                            <span class="receipt-value" x-text="(transaction.category_name || transaction.category || 'UMUM')"></span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Tipe</span>
                            <span class="receipt-value" x-text="transaction.type === 'income' ? 'PEMASUKAN' : 'PENGELUARAN'"></span>
                        </div>
                    </div>
                    <div class="receipt-total">
                        <div class="receipt-amount" x-text="(transaction.type === 'expense' ? '-' : '+') + 'Rp ' + formatNumber(transaction.amount)"></div>
                        <div class="receipt-type-label" x-text="transaction.type === 'income' ? '↑ PEMASUKAN' : '↓ PENGELUARAN'"></div>
                    </div>
                    <div class="receipt-footer">
                        Terima kasih telah menggunakan Zaku<br>
                        #<span x-text="transaction.id"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection