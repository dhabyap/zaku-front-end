@extends('layouts.app')

@section('content')
<div x-data="topupPage()" style="display:flex;flex-direction:column;height:100%;">
    <div class="inner-top">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
            <a href="/dashboard" class="back-btn" style="border-color:rgba(255,255,255,.2);color:var(--paper);text-decoration:none;">←</a>
            <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:2px;color:rgba(245,240,232,.4)">DOMPET / ISI SALDO</div>
        </div>
        <div class="inner-title">Isi Saldo.</div>
        <div class="inner-sub">TAMBAH SALDO DOMPET</div>
    </div>

    <div class="screen-body">
        <div class="balance-card" style="background:var(--mint);">
            <div class="bc-label">SALDO SAAT INI</div>
            <div class="bc-amount" x-text="'Rp ' + formatNumber(balance)">Rp 0</div>
        </div>

        <div style="margin:16px;background:#fff;border:var(--border);box-shadow:var(--bs-lg);padding:20px;">
            <div style="margin-bottom:16px;">
                <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:2px;color:rgba(17,16,16,.5);margin-bottom:10px;">PILIH NOMINAL</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <template x-for="val in [50000, 100000, 200000, 500000]">
                        <button @click="amount = val" class="filter-pill" :class="amount === val ? 'on' : ''" x-text="'Rp ' + formatNumber(val)" style="text-align:center;"></button>
                    </template>
                </div>
            </div>

            <div class="field">
                <label>ATAU MASUKKAN JUMLAH</label>
                <input type="number" x-model="amount" placeholder="0">
            </div>

            <button @click="submitTopup()" class="btn-main" :disabled="loading || amount < 10000" style="margin-top:10px;">
                <span x-show="!loading">KONFIRMASI TOP UP →</span>
                <span x-show="loading" x-cloak>MEMPROSES...</span>
            </button>
        </div>
    </div>
</div>

<script>
    function topupPage() {
        return {
            amount: '',
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
            async submitTopup() {
                this.loading = true;
                try {
                    const res = await window.apiClient.post('/wallet/topup', { amount: parseInt(this.amount) });
                    window.utils.showToast('success', 'Top up berhasil!');
                    setTimeout(() => { window.location.href = '/dashboard'; }, 1500);
                } catch (e) {
                    console.error('Topup error:', e);
                    window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal memproses top up'), true);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
