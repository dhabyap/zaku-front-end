@extends('layouts.app')

@section('content')
<div x-data="transactionDetail('{{ $id }}')" class="space-y-8 p-6 pb-24 max-w-md mx-auto">
    <div class="flex items-center gap-4">
        <a href="/transactions" class="bg-paper border-4 border-ink p-2 shadow-bs active:translate-x-1 active:translate-y-1 active:shadow-none transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-3xl font-display font-black text-ink uppercase tracking-tighter">Detail Transaksi</h1>
    </div>

    <!-- Loading State -->
    <template x-if="loading">
        <div class="space-y-6">
            <div class="h-40 bg-cream/20 border-4 border-ink animate-pulse"></div>
            <div class="space-y-2">
                <div class="h-4 w-1/2 bg-ink/10 animate-pulse"></div>
                <div class="h-8 w-full bg-ink/10 animate-pulse"></div>
            </div>
        </div>
    </template>

    <!-- Data Content -->
    <template x-if="!loading && transaction">
        <div class="space-y-8">
            <!-- Amount Card -->
            <div :class="transaction.type === 'expense' ? 'bg-punch' : 'bg-mint'" class="border-4 border-ink p-8 shadow-bs-lg text-center relative overflow-hidden">
                <p class="font-mono text-[10px] font-bold uppercase tracking-[0.3em] mb-2 opacity-70" :class="transaction.type === 'expense' ? 'text-paper' : 'text-ink'">
                    Jumlah Transaksi
                </p>
                <h2 class="text-4xl font-display font-black leading-none tracking-tighter" :class="transaction.type === 'expense' ? 'text-paper' : 'text-ink'"
                    x-text="(transaction.type === 'expense' ? '-' : '+') + window.utils.formatRupiah(transaction.amount)">
                </h2>
                
                <!-- Decorative elements -->
                <div class="absolute top-0 left-0 w-full h-1 opacity-20 bg-paper"></div>
                <div class="absolute bottom-0 left-0 w-full h-1 opacity-20 bg-ink"></div>
            </div>

            <!-- Detail List -->
            <div class="bg-paper border-4 border-ink p-6 shadow-bs space-y-6">
                <div class="space-y-1">
                    <p class="font-mono text-[10px] text-ink/40 font-bold uppercase tracking-widest">Deskripsi</p>
                    <p class="font-display font-black text-xl text-ink uppercase" x-text="transaction.description"></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <p class="font-mono text-[10px] text-ink/40 font-bold uppercase tracking-widest">Tanggal</p>
                        <p class="font-mono text-sm font-bold text-ink" x-text="window.utils.formatDate(transaction.created_at)"></p>
                    </div>
                    <div class="space-y-1">
                        <p class="font-mono text-[10px] text-ink/40 font-bold uppercase tracking-widest">Status</p>
                        <div class="inline-block bg-ink text-paper px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-tighter">SUCCESS</div>
                    </div>
                </div>

                <div class="space-y-1">
                    <p class="font-mono text-[10px] text-ink/40 font-bold uppercase tracking-widest">ID Transaksi</p>
                    <p class="font-mono text-[10px] font-bold text-ink/60 break-all" x-text="transaction.id"></p>
                </div>
            </div>

            <button @click="window.print()" class="w-full bg-paper border-4 border-ink p-4 font-display font-black text-ink uppercase tracking-widest shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all active:scale-95 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Struk
            </button>
        </div>
    </template>
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
            async fetchDetail() {
                try {
                    const res = await window.apiClient.get(`/transactions/${this.id}`);
                    this.transaction = res.data.data;
                } catch (e) {
                    console.error('Fetch transaction detail error:', e);
                    window.utils.showToast('error', 'Gagal memuat detail transaksi');
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
