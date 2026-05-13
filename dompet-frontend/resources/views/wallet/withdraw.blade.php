@extends('layouts.app')

@section('content')
<div x-data="withdrawPage()" style="display:flex;flex-direction:column;height:100%;">
    <div class="inner-top">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
            <a href="/dashboard" class="back-btn" style="border-color:rgba(255,255,255,.2);color:var(--paper);text-decoration:none;">←</a>
            <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:2px;color:rgba(245,240,232,.4)">DOMPET / TARIK SALDO</div>
        </div>
        <div class="inner-title">Tarik Saldo.</div>
        <div class="inner-sub">CAIRKAN SALDO KE REKENING</div>
    </div>

    <div class="screen-body">
        <div class="balance-card" style="background:var(--sky);">
            <div class="bc-label">SALDO TERSEDIA</div>
            <div class="bc-amount" x-text="'Rp ' + formatNumber(balance)">Rp 0</div>
        </div>

        <div style="margin:16px;background:#fff;border:var(--border);box-shadow:var(--bs-lg);padding:20px;">
            <div class="field">
                <label>JUMLAH PENARIKAN</label>
                <input type="number" x-model="amount" placeholder="0">
            </div>

            <div class="field">
                <label>NOMOR REKENING TUJUAN</label>
                <input type="text" x-model="accountNumber" placeholder="CONTOH: 1234567890">
            </div>

            <button @click="submitWithdraw()" class="btn-main" :disabled="loading || !amount || amount > balance || !accountNumber" style="margin-top:10px;">
                <span x-show="!loading">TARIK SEKARANG →</span>
                <span x-show="loading" x-cloak>MEMPROSES...</span>
            </button>
        </div>
    </div>
</div>

<script>
    function withdrawPage() {
        return {
            amount: '',
            accountNumber: '',
            balance: 0,
            loading: false,
            async init() {
                try {
                    const res = await window.apiClient.get('/wallet/balance');
                    this.balance = res.data.data.balance || 0;
                } catch (e) { console.error(e); }
            },
            formatNumber(n) {
                if (!n) return '0';
                return Number(n).toLocaleString('id-ID');
            },
            async submitWithdraw() {
                this.loading = true;
                try {
                    const res = await window.apiClient.post('/wallet/withdraw', {
                        amount: parseInt(this.amount),
                        account_number: this.accountNumber
                    });
                    window.utils.showToast('success', 'Penarikan berhasil diproses!');
                    setTimeout(() => { window.location.href = '/dashboard'; }, 1500);
                } catch (e) {
                    console.error('Withdraw error:', e);
                    window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal memproses penarikan'), true);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
