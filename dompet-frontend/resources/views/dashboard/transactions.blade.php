@extends('layouts.app')

@section('content')
<div x-data="transactionList()" class="space-y-8 p-6 pb-24 max-w-md mx-auto">
    <div class="flex items-center gap-4">
        <a href="/dashboard" class="bg-paper border-4 border-ink p-2 shadow-bs active:translate-x-1 active:translate-y-1 active:shadow-none transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-3xl font-display font-black text-ink uppercase tracking-tighter">Riwayat Transaksi</h1>
    </div>

    <!-- Filters (Optional but nice) -->
    <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar">
        <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-ink text-paper' : 'bg-paper text-ink'" class="border-4 border-ink px-4 py-1 font-mono text-xs font-bold uppercase tracking-widest transition-colors">Semua</button>
        <button @click="filter = 'income'" :class="filter === 'income' ? 'bg-mint text-ink' : 'bg-paper text-ink'" class="border-4 border-ink px-4 py-1 font-mono text-xs font-bold uppercase tracking-widest transition-colors">Masuk</button>
        <button @click="filter = 'expense'" :class="filter === 'expense' ? 'bg-punch text-paper' : 'bg-paper text-ink'" class="border-4 border-ink px-4 py-1 font-mono text-xs font-bold uppercase tracking-widest transition-colors">Keluar</button>
    </div>

    <!-- Activity List -->
    <div class="space-y-4">
        <!-- Loading State -->
        <template x-if="loading">
            <div>
                <x-loading-skeleton count="6" />
            </div>
        </template>

        <!-- Data List -->
        <template x-if="!loading">
            <div class="space-y-4">
                <template x-for="trx in filteredTransactions()" :key="trx.id">
                    <a :href="'/transactions/' + trx.id" class="bg-paper border-4 border-ink p-4 flex items-center gap-4 shadow-bs hover:bg-cream transition-colors block active:translate-x-1 active:translate-y-1 active:shadow-none">
                        <div :class="trx.type === 'expense' ? 'bg-punch' : 'bg-mint'" 
                            class="h-14 w-14 border-4 border-ink flex items-center justify-center flex-shrink-0">
                            <template x-if="trx.type === 'expense'">
                                <svg class="h-6 w-6 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                    <path d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                            </template>
                            <template x-if="trx.type === 'income'">
                                <svg class="h-6 w-6 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                    <path d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </template>
                        </div>
                        
                        <div class="flex-grow overflow-hidden">
                            <p class="font-display font-black text-ink uppercase text-sm leading-tight truncate" x-text="trx.description"></p>
                            <p class="font-mono text-[10px] text-ink/50 font-bold uppercase tracking-widest" x-text="window.utils.formatDate(trx.created_at)"></p>
                        </div>
                        
                        <div class="text-right flex-shrink-0">
                            <p :class="trx.type === 'expense' ? 'text-punch' : 'text-mint'" 
                                class="font-mono font-black text-base leading-none" 
                                x-text="(trx.type === 'expense' ? '-' : '+') + window.utils.formatRupiah(trx.amount)">
                            </p>
                        </div>
                    </a>
                </template>

                <!-- Empty State -->
                <template x-if="filteredTransactions().length === 0">
                    <div class="border-4 border-ink border-dashed p-10 text-center bg-cream/20">
                        <p class="font-mono text-sm text-ink/30 uppercase font-bold tracking-widest">Tidak ada transaksi ditemukan</p>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>

<script>
    function transactionList() {
        return {
            transactions: [],
            loading: true,
            filter: 'all',
            async init() {
                this.fetchTransactions();
            },
            async fetchTransactions() {
                try {
                    const res = await window.apiClient.get('/transactions');
                    this.transactions = res.data.data || [];
                } catch (e) {
                    console.error('Fetch transactions error:', e);
                    window.utils.showToast('error', 'Gagal memuat riwayat transaksi');
                } finally {
                    this.loading = false;
                }
            },
            filteredTransactions() {
                if (this.filter === 'all') return this.transactions;
                return this.transactions.filter(t => t.type === this.filter);
            }
        }
    }
</script>
@endsection
