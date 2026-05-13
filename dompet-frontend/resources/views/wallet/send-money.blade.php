@extends('layouts.app')

@section('content')
<div x-data="sendMoneyPage()" style="display:flex;flex-direction:column;height:100%;">
    <div class="inner-top">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
            <a href="/dashboard" class="back-btn" style="border-color:rgba(255,255,255,.2);color:var(--paper);text-decoration:none;">←</a>
            <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:2px;color:rgba(245,240,232,.4)">DOMPET / KIRIM UANG</div>
        </div>
        <div class="inner-title">Kirim Uang.</div>
        <div class="inner-sub">KIRIM UANG KE TEMAN</div>
    </div>

    <div class="screen-body">
        <div class="balance-card" style="background:var(--punch-2);">
            <div class="bc-label">SALDO TERSEDIA</div>
            <div class="bc-amount" x-text="'Rp ' + formatNumber(balance)">Rp 0</div>
        </div>

        <div style="margin:16px;background:#fff;border:var(--border);box-shadow:var(--bs-lg);padding:20px;">
            <div class="field">
                <label>EMAIL PENERIMA</label>
                <input type="email" x-model="recipientEmail" placeholder="email@penerima.com">
            </div>

            <div class="field">
                <label>JUMLAH KIRIM</label>
                <input type="number" x-model="amount" placeholder="0">
            </div>

            <div class="field">
                <label>CATATAN (OPSIONAL)</label>
                <input type="text" x-model="note" placeholder="Tulis pesan...">
            </div>

            <button @click="submitSend()" class="btn-main" :disabled="loading || !amount || amount > balance || !recipientEmail" style="margin-top:10px;">
                <span x-show="!loading">KIRIM SEKARANG →</span>
                <span x-show="loading" x-cloak>MEMPROSES...</span>
            </button>
        </div>
    </div>
</div>

<script>
    function sendMoneyPage() {
        return {
            amount: '',
            recipientEmail: '',
            note: '',
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
            async submitSend() {
                this.loading = true;
                try {
                    const res = await window.apiClient.post('/wallet/send', {
                        amount: parseInt(this.amount),
                        to_email: this.recipientEmail,
                        description: this.note
                    });
                    window.utils.showToast('success', 'Uang berhasil dikirim!');
                    setTimeout(() => { window.location.href = '/dashboard'; }, 1500);
                } catch (e) {
                    console.error('Send money error:', e);
                    window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal mengirim uang'), true);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
