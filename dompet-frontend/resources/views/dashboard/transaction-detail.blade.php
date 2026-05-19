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
                    <div>
                        <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:2.5px;color:rgba(17,16,16,.4);margin-bottom:4px;">KATEGORI</div>
                        <div style="font-family:var(--font-mono);font-size:13px;font-weight:500;color:var(--ink);" x-text="transaction.category_name || transaction.category || 'UMUM'"></div>
                    </div>
                </div>

                <div style="padding:0 16px; display:flex; flex-direction:column; gap:12px; margin-top:16px;">
                    <button @click="window.print()" class="btn-main" style="background:var(--paper);color:var(--ink);margin-top:0;">CETAK STRUK →</button>
                    <button @click="deleteTransaction()" class="btn-main" style="background:var(--punch);color:var(--paper);margin-top:0;border:var(--border);box-shadow:var(--bs);">HAPUS TRANSAKSI 🗑️</button>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    function transactionDetail(id) {
        return {
            id: id,
            transaction: null,
            loading: true,
            async init() {
                this.fetchDetail();
            },
            formatNumber(n) {
                if (!n) return '0';
                return Number(n).toLocaleString('id-ID');
            },
            formatDate(d) {
                if (!d) return '';
                const date = new Date(d);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            },
            async fetchDetail() {
                try {
                    const res = await window.apiClient.get('/transactions/' + this.id);
                    this.transaction = res.data.data;
                } catch (e) {
                    console.error('Fetch transaction detail error:', e);
                    window.utils.showToast('error', 'Gagal memuat detail transaksi');
                } finally {
                    this.loading = false;
                }
            },
            async deleteTransaction() {
                const ok = await window.utils.confirmDialog({
                    title: 'Hapus Transaksi?',
                    message: 'Semua data transaksi ini akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.',
                    okLabel: 'YA, HAPUS',
                    danger: true
                });
                if (!ok) return;
                try {
                    await window.apiClient.delete('/transactions/' + this.id);
                    window.utils.showToast('success', 'Transaksi berhasil dihapus!');
                    window.location.href = '/transactions';
                } catch (e) {
                    console.error('Delete transaction error:', e);
                    window.utils.showToast('error', 'Gagal menghapus transaksi. Coba lagi!');
                }
            }
        }
    }
</script>
@endsection
