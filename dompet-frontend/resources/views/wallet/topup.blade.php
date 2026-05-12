@extends('layouts.app')

@section('content')
<div x-data="topupPage()" class="space-y-8 p-6 pb-24 max-w-md mx-auto">
    <div class="flex items-center gap-4">
        <a href="/dashboard" class="bg-paper border-4 border-ink p-2 shadow-bs active:translate-x-1 active:translate-y-1 active:shadow-none transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-4xl font-display font-black text-ink uppercase tracking-tighter">Isi Saldo</h1>
    </div>

    <div class="bg-mint border-4 border-ink p-6 shadow-bs space-y-6">
        <div class="space-y-2">
            <label class="font-mono text-xs font-bold text-ink uppercase tracking-widest">Pilih Nominal Cepat</label>
            <div class="grid grid-cols-2 gap-3">
                <template x-for="val in [50000, 100000, 200000, 500000]">
                    <button @click="amount = val" 
                        class="bg-paper border-4 border-ink p-3 font-mono font-black text-sm text-ink hover:bg-ink hover:text-paper transition-colors"
                        :class="amount === val ? 'bg-ink text-paper' : ''"
                        x-text="window.utils.formatRupiah(val)">
                    </button>
                </template>
            </div>
        </div>

        <div class="space-y-2">
            <label for="amount" class="font-mono text-xs font-bold text-ink uppercase tracking-widest">Atau Masukkan Jumlah</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-display font-black text-2xl text-ink/30">Rp</span>
                <input type="number" id="amount" x-model="amount" 
                    class="w-full bg-paper border-4 border-ink p-4 pl-14 font-display font-black text-2xl text-ink focus:outline-none focus:ring-4 focus:ring-punch/20" 
                    placeholder="0">
            </div>
        </div>

        <button @click="submitTopup()" :disabled="loading || amount < 10000"
            class="w-full bg-ink border-4 border-ink p-5 text-paper font-display font-black text-xl uppercase tracking-widest shadow-bs-lg hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
            <span x-show="!loading">Konfirmasi Top Up</span>
            <span x-show="loading" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5 text-paper" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            </span>
        </button>
    </div>

    <div class="border-4 border-ink border-dashed p-6 bg-cream/10">
        <h4 class="font-display font-black text-ink uppercase mb-2">Informasi Penting</h4>
        <ul class="font-mono text-[10px] font-bold text-ink/60 space-y-2 uppercase tracking-tighter">
            <li>• Minimal top up adalah Rp 10.000</li>
            <li>• Saldo akan bertambah secara instan setelah konfirmasi</li>
            <li>• Pastikan koneksi internet stabil</li>
        </ul>
    </div>
</div>

<script>
    function topupPage() {
        return {
            amount: '',
            loading: false,
            async submitTopup() {
                this.loading = true;
                try {
                    const res = await window.apiClient.post('/wallet/topup', {
                        amount: parseInt(this.amount)
                    });
                    
                    window.utils.showToast('success', 'Top up berhasil!');
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 1500);
                } catch (e) {
                    console.error('Topup error:', e);
                    const detailedMsg = window.utils.parseApiError(e, 'Gagal memproses top up');
                    window.utils.showToast('error', detailedMsg, true);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
