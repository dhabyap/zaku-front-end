@extends('layouts.app')

@section('content')
<div x-data="withdrawPage()" class="space-y-8 p-6 pb-24 max-w-md mx-auto">
    <div class="flex items-center gap-4">
        <a href="/dashboard" class="bg-paper border-4 border-ink p-2 shadow-bs active:translate-x-1 active:translate-y-1 active:shadow-none transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-4xl font-display font-black text-ink uppercase tracking-tighter">Tarik Saldo</h1>
    </div>

    <!-- Available Balance Info -->
    <div class="bg-paper border-4 border-ink p-4 flex justify-between items-center shadow-bs">
        <span class="font-mono text-[10px] font-bold text-ink/40 uppercase tracking-widest">Saldo Tersedia</span>
        <span class="font-mono font-black text-ink" x-text="window.utils.formatRupiah(balance)"></span>
    </div>

    <div class="bg-sky border-4 border-ink p-6 shadow-bs space-y-6">
        <div class="space-y-2">
            <label for="amount" class="font-mono text-xs font-bold text-ink uppercase tracking-widest">Jumlah Penarikan</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-display font-black text-2xl text-ink/30">Rp</span>
                <input type="number" id="amount" x-model="amount" 
                    class="w-full bg-paper border-4 border-ink p-4 pl-14 font-display font-black text-2xl text-ink focus:outline-none focus:ring-4 focus:ring-ink/10" 
                    placeholder="0">
            </div>
            <p x-show="amount > balance" class="font-mono text-[10px] text-punch font-bold uppercase tracking-tighter">Saldo tidak mencukupi</p>
        </div>

        <div class="space-y-2">
            <label for="account" class="font-mono text-xs font-bold text-ink uppercase tracking-widest">Nomor Rekening Tujuan</label>
            <input type="text" id="account" x-model="accountNumber" 
                class="w-full bg-paper border-4 border-ink p-4 font-mono font-bold text-lg text-ink focus:outline-none focus:ring-4 focus:ring-ink/10" 
                placeholder="CONTOH: 1234567890">
        </div>

        <button @click="submitWithdraw()" :disabled="loading || !amount || amount > balance || !accountNumber"
            class="w-full bg-ink border-4 border-ink p-5 text-paper font-display font-black text-xl uppercase tracking-widest shadow-bs-lg hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
            <span x-show="!loading">Tarik Sekarang</span>
            <span x-show="loading" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5 text-paper" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            </span>
        </button>
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
                } catch (e) {
                    console.error('Fetch balance error:', e);
                }
            },
            async submitWithdraw() {
                this.loading = true;
                try {
                    const res = await window.apiClient.post('/wallet/withdraw', {
                        amount: parseInt(this.amount),
                        account_number: this.accountNumber
                    });
                    
                    window.utils.showToast('success', 'Penarikan berhasil diproses!');
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 1500);
                } catch (e) {
                    console.error('Withdraw error:', e);
                    window.utils.showToast('error', e.response?.data?.message || 'Gagal memproses penarikan');
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
